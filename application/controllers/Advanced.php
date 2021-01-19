<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Advanced extends CI_Controller
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
        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=> new Job_model($this->jobId),
            'jobName'=>$this->jobName,
            'banNumber'=>$this->banNumber,
            'phoneNumber'=>$this->phoneNumber,
            'breadcrumbTitle' => 'Network Analysis'
        );
        $this->load->view('head', $data);
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('advanced', $data);
        $this->load->view('footer', $data);
    }

    public function traceroute()
    {
        session_write_close();
        $this->load->model('Advanced_model', 'advanced');
        $this->advanced->traceroute($this->input->post());
    }

    public function ping()
    {
        session_write_close();
        $this->load->model('Advanced_model', 'advanced');
        $this->advanced->ping($this->input->post());
    }

    public function nslookup()
    {
        session_write_close();
    }
}
