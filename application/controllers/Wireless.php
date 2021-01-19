<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wireless extends CI_Controller
{
    public $jobId;
    public $jobName;
    public $currentJob;
    public $banNumber;
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $this->load->model('Wireless_model', 'wireless');
        $this->load->model('Ethernet_model', 'ethernet');
        $this->load->model('Manufacturer_model', 'manuf');
        $this->load->model('Job_model', 'job');
        

        if ($this->session->userdata('jobId') == null && $this->config->item('development') == true) {
            $this->jobId = $this->config->item('development_job');
        } else {
            $this->jobId = $this->session->userdata('jobId') ? $this->session->userdata('jobId') : 0;
        }
        
        $this->currentJob = false;

        if ($this->jobId === null) {
            $this->jobId = 0;
            $this->jobName = '';
        }
        if ($this->jobId !== 0) {
            $this->currentJob = new Job_model($this->jobId);
        }
        $this->load->model('Settings_model', 'settings');
        
        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=> $this->currentJob,
            'thresholds' => $this->settings->get('wireless_thresholds')
        );

        if ($this->config->item('development')) {
            $this->output->enable_profiler(true);
        }

        $this->load->view('head');
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('wireless_view', $data);
        $this->load->view('footer', $data);
    }


    public function updateDataBase()
    {
        $this->load->model('Wireless_model', 'wireless');
        $this->wireless->parseWirelessScanResults();
    }
    
    public function getLastNetworkScan()
    {
        $this->load->model('Wireless_model', 'wireless');
        echo $this->wireless->getRecords();
    }

    public function getPreSharedKey()
    {
        session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        $pass = $this->input->get_post('passphrase');
        $ssid = $this->input->get_post('ssid');
        echo $this->wireless->getPreSharedKey($ssid, $pass);
    }
    
    public function savePreSharedKey()
    {
        session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        $ssid = $this->input->post('ssid');
        $conf = $this->input->post('conf');
        $encryption = $this->input->post('encryption');
        $this->wireless->savePreSharedKey($ssid, $conf, $encryption);
    }
    
        
    public function getJsonData()
    {
        session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        echo $this->wireless->getRecentResults();
    }
    
    public function ethernetAvailable()
    {
        session_write_close();
        $this->load->model('Network_model', 'network');
        $this->load->model('Ethernet_model', 'ethernet');
        $ethernetConnection = $this->network->isEthernetConnected();
        $stringMatch = 'Link detected: ';
        $ethc = str_replace($stringMatch, '', $ethernetConnection);
        echo $ethc;
    }
    
    private function processWirelessConnectionRequest($ssid, $job)
    {
        /* More robust wireless connection process */
        if ($this->wireless->getInterfaceStatus()) {
            log_message('debug', 'Connecting to wireless network and interface status is up.');
            if ($this->wireless->getWirelessInterfaceEssid() !== $ssid) {
                log_message('debug', 'Current wireless ssid does not match the desired one.');
                $this->wireless->connectToWirelessNetwork($job, $ssid);
            }

            if (!$this->wireless->checkInterfaceIpAddress()) {
                log_message('debug', 'Wireless interface is not assigned an ip address.');
                if (!$this->wireless->connectToWirelessNetwork($job, $ssid)) {
                    return json_encode(array('status'=>false,'msg'=>'Error obtaining ip address.'));
                } else {
                    return json_encode(array('status'=>true,'msg'=>'Successfully connected to wireless network', 'ssid' => $ssid));
                }
            }
        } else {
            log_message('debug', 'Connecting to wireless network and interface status is down.');
            $this->wireless->turnOnInterface();
            $this->wireless->connectToWirelessNetwork($job, $ssid);

            if (! $this->wireless->checkInterfaceIpAddress()) {
                log_message('error', 'Wireless interface is not assigned an ip address.');
                return json_encode(array('status'=>false,'msg'=>'Error obtaining ip address.'));
            } else {
                return json_encode(array('status'=>true,'msg'=>'Successfully connected to wireless network', 'ssid' => $ssid));
            }
        }
    }

    public function connectToWirelessNetwork()
    {
        session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        $this->load->model('Notification_model', 'notification');
        $ssid = $this->input->post('ssid');
        $encryption = $this->input->post('encryption');
        $job = $this->input->post('job');
        $cleanSsid = Wireless_model::cleanSsid($ssid);
        if (file_exists('/etc/wpa_supplicant/networks/'.$cleanSsid.'.conf')) {
            $this->load->model('Notifications_model', 'notification');
            $this->notification->sendNotification($job, 'wireless', 'success', 'Network configured, attempting to connect');
            $msg = $this->processWirelessConnectionRequest($ssid, $job);
            echo $msg;
            //$this->wireless->connectToWirelessNetwork($job, $ssid);
        } elseif ($encryption !== true) {
            if ($this->wireless->createOpenNetworkConfigurationFile($ssid) === true) {
                $msg = $this->processWirelessConnectionRequest($ssid, $job);
                echo $msg;
            } else {
                echo json_encode(array('status' => false, 'msg' =>'error creating configuration file'));
            }
        }
    }

    public function wirelessNetworkConfigurationExists()
    {
        session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        $ssid = $this->input->post('ssid');
        $job = $this->input->post('job');
        //$cleanSsid = preg_replace("/[^a-zA-Z0-9]/", "", $ssid);
        $cleanSsid = Wireless_model::cleanSsid($ssid);
        if (file_exists('/etc/wpa_supplicant/networks/'.$cleanSsid.'.conf') && filesize('/etc/wpa_supplicant/networks/'.$cleanSsid.'.conf') > 36) {
            echo json_encode(array('status' => true));
        } else {
            exec("sudo rm -rf /etc/wpa_supplicant/networks/".$cleanSsid.".conf");
            $this->load->model('Notifications_model', 'notification');
            $this->notification->sendNotification($job, 'wireless', 'error', 'Need to configure');

            log_message('debug', 'Need to configure wireless network.');

            echo json_encode(array('status' => false, 'msg' => 'Need to configure wireless network'));
        }
    }

    public function clearCache()
    {
        $this->load->model('Wireless_model', 'wireless');
        if ($cacheItem = $this->input->post('item')) {
            $this->wireless->deleteCacheItem($cacheItem);
        }
    }

    public function getWirelessNetworkScanResults()
    {
        session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        $type = $this->input->post('type');
        
        if ($type == 'ajax') {
            header('Content-Type: application/json');
            echo json_encode($this->wireless->globalWirelessNetworks());
        } else {
            return $this->wireless->globalWirelessNetworks();
        }
    }

    public function runBandwidthTest()
    {
        //session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        $scanTime = $this->input->post('time');
        echo $this->wireless->runBandwidthTest($scanTime);
    }

    public function getConfiguredNetworks()
    {
        //session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        $this->wireless->getConfiguredNetworkList();
    }

    public function getCurrentNetworkPassword()
    {
        //session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        if ($this->input->post('file')) {
            $this->wireless->getCurrentNetworkPassword($this->input->post('file'));
        }
    }

    public function checkRoute()
    {
        //session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        $this->wireless->checkIpRoute(false, true);
    }

    public function checkEthernetStatus()
    {
        //session_write_close();
        $this->load->model('Ethernet_model', 'ethernet');
        $this->ethernet->getInterfaceStatus(true);
    }

    public function disconnectFromWirelessNetwork()
    {
        session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        $this->wireless->killWpaSupplicant();
        return true;
    }


    public function saveWirelessThresholds()
    {
        //session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        $form = $this->input->post();
        if ($this->wireless->saveThresholds($form)) {
            log_message('info', 'Updated wireless thresholds.');
            echo json_encode(array('msg' => 'Updated Wireless Thresholds', 'status' => true));
        } else {
            log_message('error', 'Error updating wireless thresholds.');
            echo json_encode(array('msg' => 'Error Updating Wireless Thresholds', 'status' => false));
        }
    }
}
