<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Network extends CI_Controller
{
    public $jobId;
    public $currentJob;
    public $jobName;
    public $banNumber;
    
    public function __construct()
    {
        parent::__construct();
       
        $this->load->model('Network_model', 'network');
        $this->load->model('Job_model', 'job');
        $this->load->model('Notifications_model', 'notification');

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
        }
    }

    public function index()
    {
        $data = array(
            'jobId'=>$this->jobId,
            'jobName'=>$this->jobName,
            'banNumber'=>$this->banNumber
        );
        $this->load->model('Wireless_model', 'wirelessNetwork');
        $this->load->view('head');
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('home', $data);
        $this->load->view('footer', $data);
    }

    public function runNetworkScan()
    {
        $job = $this->input->post('job');
        //session_write_close();
        log_message('info', 'Running network scan for job ['.$job.']');
        $this->load->model('Wireless_model', 'wireless');
        echo $this->wireless->runNewNetworkScan($job);
    }

    public function runNetworkScanForRoom()
    {
        //session_write_close();
        $this->load->model('Wireless_model', 'wireless');
        echo $this->wireless->runNewNetworkScanForRoom();
    }

    public function interfaceStatus()
    {
        $this->load->model('Wireless_model', 'wireless');
        $this->load->model('Ethernet_model', 'ethernet');
        $interface = $this->input->post_get('interface');
        if ($this->input->post_get('value') == "up") {
            if ($this->$interface->turnOnInterface()) {
                $msg = array('status' => true,'msg' =>'Sucessfully turned on ' . $interface. ' interface.');
                log_message('debug', json_encode($msg));
                echo json_encode($msg);
            } else {
                $msg = array('status' => false,'msg' =>'Could not turn on ' . $interface. ' interface.');
                log_message('debug', json_encode($msg));
                echo json_encode($msg);
            }
        } elseif ($this->input->post_get('value') == "down") {
            if ($this->$interface->turnOffInterface()) {
                $msg = array('status' => true,'msg' =>'Sucessfully turned off ' . $interface. ' interface.');
                log_message('debug', json_encode($msg));
                echo json_encode($msg);
            } else {
                $msg = array('status' => false,'msg' =>'Could not turn off ' . $interface. ' interface.');
                log_message('debug', json_encode($msg));
                echo json_encode($msg);
            }
        }
    }

    public function interfaceIpAddress()
    {
        $this->load->model('Wireless_model', 'wireless');
        $this->load->model('Ethernet_model', 'ethernet');
        $this->load->model('Vpn_model', 'vpn');
        $interface = $this->input->post('interface');
        if ($this->input->post('value') == "request") {
            if ($address = $this->$interface->renewIpAddress(true, true)) {
                $msg = array('status' => true,'msg' =>'Sucessfully requested ip address on ' . $interface . ' interface.', 'address' =>$address);
                log_message('debug', json_encode($msg));
                echo json_encode($msg);
            } else {
                $msg = array('status' => false,'msg' =>'Could not get an ip address on ' . $interface . ' interface.', 'address' => 'No Ip Address Assigned');
                log_message('debug', json_encode($msg));
                echo json_encode($msg);
            }
        } elseif ($this->input->post('value') == "release") {
            if ($this->$interface->releaseIpAddress()) {
                $msg = array('status' => true,'msg' =>'Sucessfully released ip address on ' . $interface . ' interface.');
                log_message('debug', json_encode($msg));
                echo json_encode($msg);
            } else {
                $msg = array('status' => false,'msg' =>'Error releasing ip address on ' . $interface . ' interface.');
                log_message('debug', json_encode($msg));
                echo json_encode($msg);
            }
        }
    }

    public function runNewNetworkScanAnonymous()
    {
        $this->load->model('Wireless_model', 'wireless');
        echo $this->wireless->runNewNetworkScanAnonymous();
    }
}
