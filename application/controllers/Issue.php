<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Issue extends CI_Controller
{
    public $jobId;
    public $currentJob;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Job_model', 'job');
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
            'phoneNumber'=>$this->phoneNumber
        );
        $this->load->view('head');
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('issue', $data);
        $this->load->view('footer', $data);
    }

    public function createIssue()
    {
        $issue = $this->input->post();
        $this->load->model('Issue_model', 'issue');
        
        echo $this->issue->createIssue($issue);
    }
}
