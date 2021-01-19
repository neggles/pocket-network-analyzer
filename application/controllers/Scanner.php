<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Scanner extends CI_Controller
{
    public $jobId;
    public $currentJob;


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
        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=> $this->currentJob,
            'thresholds' => $this->settings->get('wireless_thresholds'),
            'dynamicSsid' => $this->settings->getDynamicSsid(),
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

    public function floorplan()
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
        
        
        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=> $this->currentJob
        );
        $this->load->view('head');
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('floorplan_view', $data);
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
        $this->wireless->getRecords();
    }

    public function getPreSharedKey()
    {
        $this->load->model('Wireless_model', 'wireless');
        $pass=$this->input->post('passphrase');
        $ssid=$this->input->post('ssid');
        echo $this->wireless->getPreSharedKey($ssid, $pass);
    }
    
    public function savePreSharedKey()
    {
        $ssid = $this->input->post('ssid');
        $conf = $this->input->post('conf');
        $encryption = $this->input->post('encryption');
        $this->load->model('Wireless_model', 'wireless');
        $this->wireless->savePreSharedKey($ssid, $conf, $encryption);
    }
    
    public function getJob($id)
    {
        $thisJob = $this->job->currentJob($id);
    }
    
        
    public function getJsonData()
    {
        $this->load->model('Wireless_model', 'wireless');
        echo $this->wireless->getRecentResults();
    }
    
    public function ethernetAvailable()
    {
        $this->load->model('Ethernet_model', 'ethernet');
        $this->load->model('Network_model', 'network');
        $ethernetConnection = $this->network->isEthernetConnected();
        $stringMatch = 'Link detected: ';
        $ethc = str_replace($stringMatch, '', $ethernetConnection);
        echo $ethc;
    }
    
    public function connectToWirelessNetwork()
    {
        $ssid = $this->input->post('ssid');
        $job = $this->input->post('job');
        $cleanSsid = preg_replace("/[^a-zA-Z0-9]/", "", $ssid);
        if (file_exists('/etc/wpa_supplicant/networks/'.$cleanSsid.'.conf')) {
            $this->load->model('Wireless_model', 'wireless');
            $this->load->model('Notifications_model', 'notification');

            $this->notification->sendNotification($job, 'wireless', 'success', 'Network-configured,-attempting-to-connect');

            $this->wireless->connectToWirelessNetwork($job, $ssid);
        }
    }

    public function wirelessNetworkConfigurationExists()
    {
        $ssid = $this->input->post('ssid');
        $job = $this->input->post('job');
        $cleanSsid = preg_replace("/[^a-zA-Z0-9]/", "", $ssid);
        if (file_exists('/etc/wpa_supplicant/networks/'.$cleanSsid.'.conf')) {
            echo json_encode(array('status' => true));
        } else {
            log_message('debug', 'Need to configure wireless network for ssid['.$ssid.']');

            $this->load->model('Notifications_model', 'notification');

            $this->notification->sendNotification($job, 'wireless', 'error', 'Need-to-configure');
            
            echo json_encode(array('status' => false));
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
        $type = $this->input->post('type');
        $this->load->model('Wireless_model', 'wireless');
        if ($type == 'ajax') {
            echo json_encode($this->wireless->globalWirelessNetworks());
        } else {
            return $this->wireless->globalWirelessNetworks();
        }
    }

    public function runBandwidthTest()
    {
        $scanTime = $this->input->post('time');
        $this->load->model('Wireless_model', 'wireless');
        $this->wireless->runBandwidthTest($scanTime);
    }

    public function getConfiguredNetworks()
    {
        $this->load->model('Wireless_model', 'wireless');
        $this->wireless->getConfiguredNetworkList();
    }

    public function getCurrentNetworkPassword()
    {
        $this->load->model('Wireless_model', 'wireless');
        if ($this->input->post('file')) {
            $this->wireless->getCurrentNetworkPassword($this->input->post('file'));
        }
    }
}
