<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Userexperience extends CI_Controller
{
    public $jobId;
    public $currentJob;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Job_model', 'job');

        if ($this->session->userdata('jobId') == null && $this->config->item('development') == true) {
            $this->jobId = $this->config->item('development_job');
            log_message('debug', 'No job id is set, but the environment is in development mode defaulting to test user.');
        } else {
            log_message('debug', 'Setting the jobid to [' . $this->session->userdata('jobId') . ']');
            $this->jobId = $this->session->userdata('jobId') ? $this->session->userdata('jobId') : 0;
        }

        $this->currentJob = false;
        
        if ($this->jobId === null) {
            $this->jobId = 0;
        }

        if ($this->jobId !== 0) {
            $this->currentJob = new Job_model($this->jobId);
            $this->jobName = $this->currentJob->getName();
            $this->banNumber = $this->currentJob->getBanNumber();
        }
    }
     
    public function index()
    {
        $this->load->model('Wireless_model', 'wireless');
        $this->load->model('Ethernet_model', 'ethernet');

        $wirelessArray = array(
            'authenticated' => $this->wireless->checkIfAuthenticated(),
            'frequency' => $this->wireless->getWirelessInterfaceFrequency(),
            'ssid' => $this->wireless->getWirelessInterfaceEssid(),
            'mac' => $this->wireless->getWirelessAccessPointMac(),
            'bitRate' => $this->wireless->getWirelessBitRate(),
            );

        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=> new Job_model($this->jobId),
            'jobName'=>$this->jobName,
            'banNumber'=>$this->banNumber,
            'wirelessConn' => $wirelessArray,
            'phoneNumber'=>$this->phoneNumber,
            'breadcrumbTitle' => 'User Experience',
            'mac' => $this->ethernet->getMacAddress()
        );
        $this->load->view('head', $data);
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('user-experience', $data);
        $this->load->view('footer', $data);
    }

    public function startUxTest()
    {
        session_write_close();
        $this->runUxTest();
    }

    private function install()
    {
        if (is_file(FCPATH . '/assets/ux/package.json')) {
            if (is_cli()) {
                passthru("npm install " . FCPATH . "/assets/ux --only=production --prefix=" . FCPATH . "/assets/ux");
            } else {
                log_message('debug', 'Installing ux program via npm');
                exec("npm install " . FCPATH . "/assets/ux --only=production --prefix=".FCPATH . "/assets/ux");
            }
        }
    }

    private function isInstalled()
    {
        if (is_dir(FCPATH . '/assets/ux/node_modules')) {
            return true;
        }
        return false;
    }

    public function runUxTest()
    {
        log_message('debug', __FUNCTION__);
        $output = array();
        if ($this->isInstalled()) {
            exec("/usr/bin/node " . FCPATH . "/assets/ux/test.js > /dev/null &");
            log_message('debug', 'Running ux node app in background');
        } else {
            $this->load->model('Network_model', 'network');
            if ($this->network->checkInternetConnection()) {
                $this->install();
                exec("/usr/bin/node " . FCPATH . "/assets/ux/test.js > /dev/null &");
                log_message('debug', 'Running ux node app in background: ' . __LINE__);
            } else {
                return json_encode(array('status' => false, 'msg' => 'UX is not installed and there is no network to install it.'));
            }
        }
    }
}
