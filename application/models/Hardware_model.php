<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Hardware_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Network_model', 'network');
        $this->load->model('Ethernet_model', 'ethernet');
    }

    public function registerDevice()
    {
        log_message('debug', __FUNCTION__);
        if (!$this->network->checkInternetConnection()) {
            log_message('error', 'There is no internet connection to send the registration.');
            return false;
        }
        return $this->confirmRegistration();
    }

    private function confirmRegistration()
    {
        log_message('debug', __FUNCTION__);
        // Attempt to get the registration details
        if (($details = $this->getRegistrationDetails()) !== false) {
            $mac = $this->ethernet->getMacAddress();
            // Compare the stored serial number with a hash
            // of the mac address
            if (sha1($mac) === $details->serial) {
                log_message('debug', 'The serial number matches the mac hash, this device is registered');
                return true;
            }
            return $this->sendNewRegistration();
        }
        return $this->sendNewRegistration();
    }

    private function checkRegistrationExists()
    {
        log_message('debug', __FUNCTION__);
        $query = $this->db->select('*')
        ->limit(1)
        ->get('registration');
        if ($query->num_rows() > 0) {
            return $query->result()[0];
        }
        return false;
    }

    private function getRegistrationDetails()
    {
        log_message('debug', __FUNCTION__);
        // Check if the registration already exists
        if (($details = $this->checkRegistrationExists()) !== false) {
            return json_decode($details->data);
        }
        return false;
    }

    private function getDeviceDetails()
    {
        $mac = trim($this->ethernet->getMacAddress());

        // Model ID is the model for the pfi
        $data = [
            'status_id' => 1,
            'model_id' => 2,
            '_snipeit_mac_address_1' => $mac,
            'location_id' => 1,
            'rtd_location_id' => 1,
            'company_id' => 1,
            'serial' => $this->getSerial($mac),
            'notes' => 'Auto registering on update'
        ];

        return $data;
    }

    private function getSerial($mac = null)
    {
        if ($mac === null) {
            $mac = trim($this->ethernet->getMacAddress());
        }
        return sha1($mac);
    }

    private function sendNewRegistration()
    {
        log_message('debug', __FUNCTION__);
        // Confirm before attempting that there is a network connection.
        if ($this->network->checkInternetConnection()) {
            $data = $this->getDeviceDetails();
            $curl = new \Curl\Curl();
            $curl->setOpt(CURLOPT_RETURNTRANSFER, true);
            $curl->setUserAgent('PocketFi/0.1');

            $curl->post($this->config->item('pfi_cloud_api_endpoint') . 'hardware/register', $data);

            if ($curl->error) {
                $msg = array('status' => false, 'code' => $curl->errorCode, 'msg' => $curl->errorMessage,  'data' => false);
                log_message('error', 'There was an error registering device: ' . json_encode($msg));
            } else {
                if (isset($curl->response->status) && $curl->response->status === true) {
                    $payload = isset($curl->response->data) ? $curl->response->data : '';

                    if (is_object($payload)) {
                        $data = array(
                        'id' => $payload->id,
                        'asset_tag' => $payload->asset_tag,
                        'serial' => isset($payload->serial) ? $payload->serial : $this->getSerial()
                        );
                        $json = json_encode($data);
                        $this->writeFile($json);
                        return $this->createInitialRecord($json);
                    } else {
                        log_message('error', 'Payload returned was not an object: ' . json_encode($payload));
                        return false;
                    }
                }
                $msg = array('status' => true, 'msg' => 'Successfully called the endpoint.', 'data' => $curl->response);
                log_message('error', json_encode($msg));
            }
            $curl->close();
            return true;
        }
        return false;
    }

    private function writeFile($json)
    {
        $this->load->helper('file');

        if (!write_file($this->config->item('persistent_asset_tag_file'), $json)) {
            log_message('error', 'There was a problem writing the persistent asset file');
            return false;
        }
    }

    private function createInitialRecord($json)
    {
        $data = array(
            'id' => 1,
            'data' => $json,
            'date' => date('Y-m-d H:i:s')
        );
        if ($this->checkRegistrationExists() !== false) {
            log_message('debug', 'Registration exists so we are updating the values');
            $this->db->update('registration', $data);
            return true;
        }
        log_message('debug', 'Registration did not exists so we are inserting the values');
        $this->db->insert('registration', $data);
        return true;
    }
}
