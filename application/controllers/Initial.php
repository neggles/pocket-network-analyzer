<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Initial extends CI_Controller
{
    public $jobId;
    public $currentJob;
    public $jobName;
    public $banNumber;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Home_model', 'home');
        $this->load->model('Network_model', 'network');
        $this->load->model('Job_model', 'job');
        $this->load->model('Wireless_model', 'wireless');
        $this->load->model('Ethernet_model', 'ethernet');
        
        if ($this->session->userdata('jobId') == null && $this->config->item('development') == true) {
            $this->jobId = $this->config->item('development_job');
        } else {
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
        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=>$this->currentJob,
            'jobName'=>$this->jobName,
            'banNumber'=>$this->banNumber,
            'status'=>'initial-test'
        );
        $this->load->view('head');
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('initial-test', $data);
        $this->load->view('footer', $data);
    }
    
    public function getJob($id)
    {
        $thisJob = $this->job->currentJob($id);
    }
    
        
    public function getJsonData()
    {
        echo $this->wireless->getRecentResults();
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function createNewJob()
    {
        $jobName = $_POST['jobName'];
        
        if (isset($_POST['comments'])) {
            $comments = $_POST['comments'];
        } else {
            $comments = 'No Comments.';
        }
        $data=array(
            'name'=>$jobName,
            'comments'=>$comments
            );
        $this->home->createNewJob($data);
    }
    
    public function ethernetAvailable()
    {
        if ($this->ethernet->getInterfaceStatus()) {
            echo "up";
        }
        return;
    }

    public function createAPdf()
    {
        $data = $this->input->post('url');
        $jobId = $this->input->post('job');
        $type = $this->input->post('type');

        $this->home->createPdfOfPage($data, $jobId, $type);
    }

    public function testCreatePdf()
    {
        $pdfObject = new \Knp\Snappy\Pdf($this->config->item('wkhtmltopdf'));


        $options = array(
                    'title'=> ' Initial Test Specs',
                    'javascript-delay'=> 1000,
                    'viewport-size'=> 800,
                    'load-error-handling' => 'skip'
                    );

        $pdfObject->generate("http://localhost/assets/pdf/test.html", "/var/www/html/assets/pdf/test.pdf", $options, true);
    }

    public function createHtmlPage()
    {
        $html = $this->input->post('html');

        $location ='assets/pdf/test.html';

        if (! write_file($location, urldecode($html))) {
            $msg = array(
                'status' => false,
                'msg' => 'Could not write the page contents to file.'
                );
            echo json_encode($msg);
            exit;
        }

        $msg = array(
            'status' => true,
            'msg' => $location
            );
        echo json_encode($msg);
    }

    public function createPdfFromUrl()
    {
        $url = $this->input->get('url');

        $this->home->createPdfOfUrl($url);
    }


    public function saveSessionData()
    {
        $key = $this->input->post('key');
        $value = $this->input->post('value');

        if ($key && $value) {
            $this->session->set_userdata($key, $value);
            return;
        }
    }

    public function destroySessionData()
    {
        $key = $this->input->post('key');

        if ($key) {
            $this->session->unset_userdata($key);
        }
        return;
    }


    public function getSessionData()
    {
        echo $this->session->userdata($this->input->post('key'));
        return;
    }
}
