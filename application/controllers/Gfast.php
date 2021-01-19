<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gfast extends CI_Controller
{
    public $jobId;
    public $currentJob;

    private static $_commandOptions = array(
        'getGfastNumDevices',
        'getGfastNumLines',
        'getGfastDeviceInfo',
        'getGfastLineState',
        'getGfastLineStatus',
        'getGfastLineInventory',
        'getGfastLineEtrs',
        'getGfastLineConfig',
        'getGfastLinePerformanceCounters',
        'getGfastCounters',
        'getGfastFramerIntiCounters',
        'getGfastFramerRawCounters',
        'getGfastFduInitCounters',
        'getGfastFduRawCounters',
        'getGfastDerivedCounters'
    );

    /**
     * [__construct description]
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Job_model', 'job');
        $this->load->model('Gfast_model', 'gfast');
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
            'breadcrumbTitle' => 'G.Fast Site Survey',
            'mac' => $this->ethernet->getMacAddress()
        );
        $this->load->view('head', $data);
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('gfast/gfast', $data);
        $this->load->view('footer', $data);
    }


    public function technician()
    {
        $this->load->model('Wireless_model', 'wireless');
        $this->load->model('Ethernet_model', 'ethernet');

        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=> new Job_model($this->jobId),
            'jobName'=>$this->jobName,
            'banNumber'=>$this->banNumber,
            'phoneNumber'=>$this->phoneNumber,
            'breadcrumbTitle' => 'G.Fast Technician',
            'mac' => $this->ethernet->getMacAddress()
        );
        $this->load->view('head', $data);
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('gfast/technician', $data);
        $this->load->view('footer', $data);
    }

    public function history()
    {
        $this->load->model('Wireless_model', 'wireless');
        $this->load->model('Ethernet_model', 'ethernet');

        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=> new Job_model($this->jobId),
            'jobName'=>$this->jobName,
            'banNumber'=>$this->banNumber,
            'phoneNumber'=>$this->phoneNumber,
            'breadcrumbTitle' => 'G.Fast History',
            'mac' => $this->ethernet->getMacAddress()
        );
        $this->load->view('head', $data);
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('gfast/history', $data);
        $this->load->view('footer', $data);
    }

    public function saveComplex()
    {
        session_write_close();
        $form = $this->input->get();
        $output = $this->gfast->saveComplexToDatabase($form);
        echo json_encode($output);
    }

    public function saveUnit()
    {
        session_write_close();
        $form = $this->input->get();
        $output = $this->gfast->saveUnitToDatabase($form);
        echo json_encode($output);
    }

    public function getUnitList()
    {
        session_write_close();
        $form = $this->input->get('mdu');
        $output = $this->gfast->getUnitsForMduFromDatabase($form);
        echo json_encode($output);
    }


    public function runCommand()
    {
        $command = $this->input->post('command');
        if (in_array($command, $this->_commandOptions)) {
            $json = $this->gfast->$command();
        }
    }

    public function getEtrValues()
    {
        session_write_close();
        $this->gfast->loadGfast();
        $output = $this->gfast->getGfastLineEtrs();
        echo json_encode($output);
    }


    public function getGfastCounters()
    {
        session_write_close();
        $this->gfast->loadGfast();
        $output = $this->gfast->getGfastCounters();
        echo json_encode($output);
    }


    public function getGfastDeviceInfo()
    {
        session_write_close();
        $this->gfast->loadGfast();
        echo "<pre>";
        echo "Gfast Line State: <br>" . $this->gfast->getGfastLineState();
        echo "</pre>";
    }

    public function saveEtrData()
    {
        session_write_close();
        $unit = $this->input->get('unit');
        $form = $this->input->get('data');
        $output = $this->gfast->saveUnitEtrValues($unit, $form);
        echo json_encode($output);
    }

    public function updateSystemTime()
    {
        session_write_close();
        $time = $this->input->get('time');
        $output = $this->gfast->setSystemTime($time);
        echo json_encode($output);
    }

    public function getEtrHistory()
    {
        session_write_close();
        $unit = $this->input->get('unit');
        $output = $this->gfast->getEtrValuesFromDatabase($unit, 'json');
        echo $output;
    }

    public function getAllEtrHistory()
    {
        session_write_close();
        echo json_encode(array('data' => $this->gfast->getSpeedTestsJSON()->result_object()));
    }

    public function forceClockReset()
    {
        $return = $this->gfast->forceClockReset();
        echo $return;
    }

    public function loadGfast()
    {
        session_write_close();
        log_message('debug', 'loadGfast called');
        if (is_cli()) {
            $this->load->model('Notification_model', 'notification');
            log_message('debug', 'Status has changed');
            $this->notification->sendNotification(0, 'gfast', 'status', 'G.Fast state has changed');
        }
        $return = $this->gfast->loadGfast();

        echo json_encode($return);
    }

    public function cableUnplugged()
    {
        //session_write_close();
        $this->load->model('Notification_model', 'notification');
        log_message('debug', 'G.Fast Unplugged');
        $this->notification->sendNotification(0, 'gfast', 'unplug', 'G.Fast cable has been unplugged');
        return true;
    }

    public function checkStatus()
    {
        session_write_close();
        $status = $this->gfast->isLoaded();
    }

    public function install()
    {
        session_write_close();
        $this->gfast->installGfastPackage();
    }

    public function checkForCloud()
    {
        session_write_close();
        try {
            $this->gfast->checkResultsForCloud();
        } catch (Exception $e) {
        }
    }
}
