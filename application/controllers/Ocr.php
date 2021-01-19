<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ocr extends CI_Controller
{
    public $jobId;
    public $currentJob;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tesseract_model', 'tesseract');
        $this->load->model('Job_model', 'job');
        $this->load->helper('form');

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
            'breadcrumbTitle' => 'Character Recognition'
        );
/*
        $text =         "Wi—F i Network Name: ATTwE8sGI2
Wi—Fi Password: dic#3e2d+wji




For help, att.com/support";
        $text = trim($text);
        $password = $this->handlePassword($text);
        $ssid = $this->handleSsid($text);

        $data['password'] = $password;
        $data['ssid'] = $ssid;*/

        $this->load->view('head', $data);
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('ocr', $data);
        $this->load->view('footer', $data);
    }

    private function handlePassword($text)
    {
        preg_match('/(?<=Password: )([^\s]+)/', $text, $password);
        return $password[0];
    }

    private function handleSsid($text)
    {
        preg_match('/(?<=Network Name: )([^\s]+)/', $text, $ssid);
        return $ssid[0];
    }
    
    public function do_upload()
    {
        $config['upload_path']          = FCPATH . 'assets/uploads/';
        $config['allowed_types']        = 'gif|jpg|png|tiff';
        $config['max_size']             = 0;
        $config['max_width']            = 0;
        $config['max_height']           = 0;

        $this->load->library('upload', $config);
        $this->load->model('Wireless_model', 'wireless');
        $this->load->model('Ethernet_model', 'ethernet');
        
        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=> new Job_model($this->jobId),
            'jobName'=>$this->jobName,
            'banNumber'=>$this->banNumber,
            'phoneNumber'=>$this->phoneNumber,
            'breadcrumbTitle' => 'Character Recognition'
        );
        if (! $this->upload->do_upload('imageUpload')) {
            $data['error'] = $this->upload->display_errors();
            $this->load->view('head', $data);
            $this->load->view('navigation', $data);
            $this->load->view('scripts', $data);
            $this->load->view('ocr', $data);
            $this->load->view('footer', $data);
        } else {
            $fullPath = $this->upload->data('full_path');

            $text = $this->tesseract->readImage($fullPath);
            $text = trim($text);

            $password = $this->handlePassword($text);
            $ssid = $this->handleSsid($text);
            unlink($fullPath);
            
            $data['password'] = $password;
            $data['ssid'] = $ssid;

            $data['imageText'] = $text;

            $this->load->view('head', $data);
            $this->load->view('navigation', $data);
            $this->load->view('scripts', $data);
            $this->load->view('ocr', $data);
            $this->load->view('footer', $data);
        }
    }
}
