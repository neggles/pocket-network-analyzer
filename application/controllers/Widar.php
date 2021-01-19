<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Widar extends CI_Controller
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
            'mac' => $this->wireless->getWirelessAccessPointMac()
            );

        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=> $this->currentJob,
            'wirelessConn' => $wirelessArray,
            'jobName'=>$this->jobName,
            'banNumber'=>$this->banNumber,
            'phoneNumber'=>$this->phoneNumber,
            'dynamicSsid' => $this->settings->getDynamicSsid(),
            'breadcrumbTitle' => 'Wireless Radar'
        );
        $this->load->view('head', $data);
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('widar', $data);
        $this->load->view('footer', $data);
    }

    public function startWidar()
    {
        session_write_close();
        $start = $this->input->post('start');
        $mode = $this->input->post('mode');
        if ((bool) $start === true) {
            $this->load->model('Wireless_model', 'wireless');
            echo $this->wireless->startWidar($mode);
        } else {
            $array = array(
                'status' => false,
                'msg' => 'Incorrect parameters passed'
                );
            echo json_encode($array);
        }
    }

    public function stopWidar()
    {
        session_write_close();
        $stop = $this->input->post('stop');
        $mode = $this->input->post('mode');
        if ((bool) $stop === true) {
            $this->load->model('Wireless_model', 'wireless');
            echo $this->wireless->stopWidar($mode);
        } else {
            $array = array(
                'status' => false,
                'msg' => 'Incorrect parameters passed'
                );
            echo json_encode($array);
        }
    }
}
