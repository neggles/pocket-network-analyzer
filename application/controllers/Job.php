<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Job extends CI_Controller
{
    public $jobId;
    public $currentJob;
    public $jobName;
    public $banNumber;
    public $phoneNumber;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Job_model', 'job');
        $this->load->model('Network_model', 'network');
        $this->load->model('Wireless_model', 'wireless');
        $this->load->model('Ethernet_model', 'ethernet');
        
        if ($this->session->userdata('jobId') == null && $this->config->item('development') == true) {
            $this->jobId = $this->config->item('development_job');
        } else {
            $this->jobId = $this->session->has_userdata('jobId') ? $this->session->userdata('jobId') : 0;
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
        $this->session->set_userdata('jobId', $this->jobId);
    }
     
    public function index()
    {
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
        $this->load->view('job', $data);
        $this->load->view('footer', $data);
    }

    public function saveSessionData()
    {
        $key = $this->input->get_post('key');
        $value = $this->input->get_post('value');

        if ($key && $value) {
            // $ar = array($key => $value);
            $this->session->unset_userdata($key);
            $this->session->set_userdata($key, $value);
            //$_SESSION[$key] = $value;
            //session_write_close();
            $msg = array('status' => true, 'msg' => 'Sucessfully set new value for :' . $key . ' value = ' . $this->session->$key);
            echo json_encode($msg);
        } else {
            $msg = array('status' => false, 'msg' => 'Incorrect value set :' . $key . ' value = ' . $value);
            echo json_encode($msg);
        }
    }

    public function getSessionData()
    {
        $key = $this->input->get('key');
        if ($key) {
            $value = $this->session->$key;
            //session_write_close();
            if ($value !== null) {
                $msg = array('status'=>true,'msg'=> $value);
            } else {
                $msg = array('status'=>false,'msg'=> 'No value set for that parameter');
            }
        } else {
            $msg = array('status'=>false,'msg'=> 'Invalid api call.');
        }
        echo json_encode($msg);
    }
    
    
    public function getJob()
    {
        //session_write_close();
        $this->job->getMostRecentJob();
    }

    public function getJobDetails()
    {
        //session_write_close();
        $job = $this->input->post('job');

        $jobObj = new Job_model($job);

        $jobData = array(
            'jobName'=>$jobObj->getName(),
            'jobBan'=>$jobObj->getBanNumber(),
            'jobPhone'=>$jobObj->getPhoneNumber()

            );

        echo json_encode($jobData);
    }
    
    public function createNewJob()
    {
        //session_write_close();
        if ($this->input->post('jobName')) {
            $jobName = $this->input->post('jobName');

            $data = array(
                'id'=>null,
                'name'=>$jobName,
                'comments'=>$this->input->post('jobComments'),
                'ban'=> $this->input->post('banNumber'),
                'phone' => $this->input->post('phone')
            );
            echo $this->job->createNewJob($data);
        } else {
            return false;
        }
    }

    public function saveJobLocationFloorplan()
    {
        //session_write_close();
        $floorplan = $this->input->post();
        if ($this->job->saveFloorplan($floorplan)) {
            echo json_encode(array('msg' => 'Updated Job Floorplan','status' => true));
        } else {
            echo json_encode(array('msg' => 'Could not update floorplan','status' => false));
        }
    }

    public function updateJobLocationSettings()
    {
        //session_write_close();
        $return = $this->job->saveJobLocationSettings($this->input->post());
        echo json_encode($return);
    }

    public function updateJobLocationDefaultNetwork()
    {
        //session_write_close();
        $return = $this->job->saveJobLocationDefaultNetwork($this->input->post());
        echo json_encode($return);
    }

    public function jobExist()
    {
    }
}
