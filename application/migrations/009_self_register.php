<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Self_register extends CI_Migration
{
    public function up()
    {
        $this->load->dbforge();
        $this->dbforge->add_field(
            array(
              'id' => array(
                 'type' => 'INTEGER'
              ),
              'data' => array(
                 'type' => 'text',
                 'null' => true
              ),
             'date' => array(
                 'type' => 'timestamp',
                 'null' => false,
                 'default' => 'CURRENT_TIMESTAMP'
              )
            )
        );

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('registration');

        $this->registerDevice();
    }

    private function registerDevice()
    {
        $mac = trim($this->getMacAddress());

        // Model ID is the model for the pfi
        $data = [
            'status_id' => 1,
            'model_id' => 2,
            '_snipeit_mac_address_1' => $mac,
            'location_id' => 1,
            'company_id' => 1,
            'serial' => sha1($mac),
            'notes' => 'Auto registering on update'
        ];

        $curl = new \Curl\Curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, true);
        $curl->setUserAgent('PocketFi/0.1');

        $curl->post($this->config->item('pfi_cloud_api_endpoint') . 'hardware/register', $data);

        if ($curl->error) {
            $msg = array('status' => false, 'code' => $curl->errorCode, 'msg' => $curl->errorMessage,  'data' => false);
        } else {
            if (isset($curl->response->status) && $curl->response->status === true) {
                $payload = isset($curl->response->data->payload) ? $curl->response->data->payload : '';
                $this->writeFile($payload);
            }
            $msg = array('status' => true, 'msg' => 'Successfully called the endpoint.', 'data' => $curl->response);
        }
        $curl->close();
    }

    private function writeFile($data)
    {
        $json = array(
            'id' => $data->id,
            'asset_tag' => $data->asset_tag,
            'serial' => $data->serial
        );
        $data = json_encode($json);

        $this->load->helper('file');

        $this->createInitialRecord($data);
        
        if (!write_file($this->config->item('persistent_asset_tag_file'), $data)) {
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
        $this->db->insert('registration', $data);
    }

    private function getMacAddress()
    {
        $this->load->model('Ethernet_model', 'ethernet');
        return $this->ethernet->getMacAddress();
    }

    public function down()
    {
        if (is_file($this->config->item('persistent_asset_tag_file'))) {
            unlink($this->config->item('persistent_asset_tag_file'));
        }
        $this->dbforge->drop_table('registration');

        return true;
    }
}
