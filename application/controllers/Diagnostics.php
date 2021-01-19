<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Diagnostics extends CI_Controller
{
    public $jobId;
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
        $this->load->model('Vpn_model', 'vpn');
        $this->load->model('Diagnostics_model', 'diagnostics');
        $this->load->model('Qrcode_model', 'qrcode');
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
            $this->jobName = $this->currentJob->getName();
            $this->banNumber = $this->currentJob->getBanNumber();
            $this->phoneNumber = $this->currentJob->getPhoneNumber();
        }

        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=>$this->currentJob,
            'banNumber'=>$this->banNumber,
            'dnsmasq' => $this->diagnostics->checkDnsmasq(),
            'redis' => $this->diagnostics->checkRedis(),
            'slanger' => $this->diagnostics->checkSlanger(),
            'hostapd' => $this->diagnostics->checkHostapd(),
            'dynamicSsid' => $this->settings->getDynamicSsid(),
            'mac' => $this->ethernet->getMacAddress()
        );

        $this->load->view('head');
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('diagnostics', $data);
        $this->load->view('footer', $data);
    }

    public function qrcode()
    {
        session_write_close();
        $this->load->model('Qrcode_model', 'qrcode');
        $this->qrcode->display();
    }
    
    public function getJob($id)
    {
        session_write_close();
        $this->load->model('Job_model', 'job');
        $thisJob = $this->job->currentJob($id);
    }
    
    public function getName()
    {
        session_write_close();
        return $this->name;
    }
    
    public function ethernetAvailable()
    {
        session_write_close();
        $this->load->model('Network_model', 'network');
        $eth = $this->network->isEthernetConnected();
        $string = 'Link detected: ';
        $ethc = str_replace($string, '', $eth);
        echo $ethc;
    }


    public function checkEthernetAddress()
    {
        session_write_close();
        $this->load->model('Ethernet_model', 'ethernet');
        echo $this->ethernet->checkInterfaceIpAddress();
    }

    public function checkWirelessAddress()
    {
        session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        echo $this->wireless->checkInterfaceIpAddress();
    }

    public function checkWirelessStatus()
    {
        session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        echo $this->wireless->getInterfaceStatus();
    }

    public function restartDnsmasq()
    {
        session_write_close();
        exec("sudo systemctl stop dnsmasq", $output, $status);
        exec("sudo systemctl start dnsmasq", $output, $status);
    }

    public function restartHostapd()
    {
        session_write_close();
        exec("sudo systemctl stop hostapd", $output, $status);
        exec("sudo systemctl start hostapd", $output, $status);
    }
}
