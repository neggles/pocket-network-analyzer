<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Speedtest extends CI_Controller
{
    public $jobId;
    public $jobName;
    public $currentJob;
    public $banNumber;
    
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Speed_test', 'speedtest');

        $this->load->model('Job_model', 'job');

        if ($this->session->userdata('jobId') == null && $this->config->item('development') == true) {
            $this->jobId = $this->config->item('development_job');
            $this->session->set_userdata('jobId', $this->jobId);
            //session_write_close();
        } else {
            $this->jobId = $this->session->userdata('jobId') ? $this->session->userdata('jobId') : 0;
            //session_write_close();
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
        $this->load->model('Wireless_model', 'wireless');
        $this->load->model('Ethernet_model', 'ethernet');
        if ($this->config->item('development')) {
            $this->output->enable_profiler(true);
        }
        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=> $this->currentJob,
            'jobName'=>$this->jobName,
            'banNumber'=>$this->banNumber,
            'dynamicSsid' => $this->settings->getDynamicSsid(),
        );

        $this->load->view('head');
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('speedtest', $data);
        $this->load->view('footer', $data);
    }

    public function test()
    {
        $this->load->model('Wireless_model', 'wireless');
        $this->load->model('Ethernet_model', 'ethernet');
        if ($this->config->item('development')) {
            $this->output->enable_profiler(true);
        }
        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=> $this->currentJob,
            'jobName'=>$this->jobName,
            'banNumber'=>$this->banNumber,
            'dynamicSsid' => $this->settings->getDynamicSsid(),
        );

        $this->load->view('head');
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('speedtestnew', $data);
        $this->load->view('footer', $data);
    }

    public function listServers()
    {
        echo "<pre>";
        print_r($this->speedtest->getBestSpeedtestHost());
        echo "</pre>";
    }


    public function runSpeedTest()
    {
        session_write_close();
        echo $this->speedtest->runSpeedTest($this->input->post());
    }
        
    public function setSpeedTestComments()
    {
        $c=$this->input->post('comments');
        $id = $this->input->post('id');
        $updateJobId = $this->input->post('jobId');
        session_write_close();
        echo $this->speedtest->setComments($c, $id, $updateJobId);
    }

    public function parseSpeedTestResults()
    {
        session_write_close();
        $this->speedtest->parseSpeedTestResults();
    }
    
    public function getCommentsById()
    {
        $id = $this->input->post('id');
        session_write_close();
        echo $this->speedtest->getCommentsById($id);
    }
    
    public function deleteSpeedTest()
    {
        $id = $this->input->post('id');
        $j = $this->input->post('jobId');
        session_write_close();
        $this->db->where('id', $id)
                ->where('job', $j)
            ->delete('speedtest');
    }

    /*
    *   This function is called by a javascript ajax function
    *   It will receive both an ethernet and wireless speedtest
    *   which will be inserted to the database.
     */
    public function saveInitialSpeedTestResults()
    {
        $data = $this->input->post();
        session_write_close();
        if ($data) {
            echo $this->speedtest->parseSpeedTestResults($data);
        }
    }

    public function wirelessNetworkConfigurationExists()
    {
        $ssid = $this->input->post('ssid');
        $job = $this->input->post('jobId');
        $this->load->model('Wireless_model', 'wireless');
        $cleanSsid = Wireless_model::cleanSsid($ssid);
        session_write_close();
        if (file_exists('/etc/wpa_supplicant/networks/'.$cleanSsid.'.conf')) {
            echo json_encode(array('status' => true));
        } else {
            echo json_encode(array('status' => false));
        }
    }

    public function getSpeedTestByJob($job = null)
    {
        if ($job === null) {
            $job = $this->jobId;
        }
        session_write_close();
        echo json_encode(array('data' => $this->speedtest->getSpeedTestsJSON($job)->result_object()));
    }

    public function runWirelessSpotCheck()
    {
        $post = array(
            'ssid'=>$this->input->post('ssid'),
            'connection'=>$this->input->post('connection'),
            'server'=>$this->input->post('server'),
            'jobId'=>$this->input->post('jobId'),
            'location'=>$this->input->post('location'),
            'type'=>$this->input->post('type')
        );
        session_write_close();
        echo $this->speedtest->runSpeedTest($post);
    }
}
