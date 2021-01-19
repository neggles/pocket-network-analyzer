<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Version extends CI_Controller
{
    public $jobId;
    public $currentJob;


    public function __construct()
    {
        parent::__construct();
        $this->load->model('Job_model', 'job');
        $this->load->model('Network_model', 'network');
        $this->load->model('Wireless_model', 'wireless');
        $this->load->model('Ethernet_model', 'ethernet');


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
        $json = array(
            'version' => $this->version->currentTag
            );
        echo json_encode($json);
    }

    public function details()
    {
        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=>$this->currentJob
        );

        $this->load->view('head');
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('additional/version_view');
        $this->load->view('footer');
    }

    public function checkForUpdate()
    {
        $force_check = $this->input->post('force_check', false);
        //session_write_close();
        $this->version->areUpdatesAvailable($force_check);
    }

    public function runUpdate()
    {
        //session_write_close();
        if ($this->config->item('branch') !== 'development') {
            echo $this->version->getCurrentTagUpdate();
        } else {
            echo $this->version->checkoutLatestDevelopment();
        }
    }
}
