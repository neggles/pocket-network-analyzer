<?php defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
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
        $this->load->model('Manufacturer_model', 'manuf');
        
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
        $data = array(
            'jobId'=>$this->jobId,
            'currentJob'=>$this->currentJob,
            'jobName'=>$this->jobName,
            'banNumber'=>$this->banNumber
        );

        if ($this->config->item('development')) {
            $this->output->enable_profiler(true);
        }

        $this->load->view('head', $data);
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('home', $data);
        $this->load->view('footer', $data);
    }
    
    public function getJob($id)
    {
        $thisJob = $this->job->currentJob($id);
    }
    
    public function getJsonData()
    {
        echo $this->network->getRecentResults();
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function createNewJob()
    {
        $jobName = $this->input->post('jobName');
        
        if (!is_null($this->input->post('comments'))) {
            $comments = $this->input->post('comments');
        } else {
            $comments = 'No Comments.';
        }
        $data = array(
            'name' => $jobName,
            'comments' => $comments
            );
        $this->home->createNewJob($data);
    }
    
    public function ethernetAvailable()
    {
        $eth = $this->network->isEthernetConnected();
        $string = 'Link detected: ';
        $ethc = str_replace($string, '', $eth);
        echo $ethc;
    }
    
    public function shutDown()
    {
        log_message('debug', 'In the shutdown function');
        exec("sudo shutdown now", $output, $status);
        if ($status) {
            return json_encode(array('status' => false, 'msg' =>$output[0]));
        }
    }

    public function restart()
    {
        log_message('debug', 'In the restart function');
        exec("sudo reboot", $output, $status);
        if ($status) {
            return json_encode(array('status' => false, 'msg' => $output[0]));
        }
    }

    public function createAPdf()
    {
        $data = $this->input->post('url');
        $jobId = $this->input->post('job');
        $type = $this->input->post('type');
        $this->home->createPdfOfPage($data, $jobId, $type);
    }

    public function timeoutAction()
    {
        $time = $this->input->get('time');
        $this->home->timeoutAction($time);
    }

    public function createPdfFromUrl()
    {
        $url = $this->input->get('url');

        $this->home->createPdfOfUrl($url);
    }

    public function phpinfo()
    {
        phpinfo();
    }

    public function cleanUnit()
    {
        if ($this->session->sess_destroy()) {
            echo 'Session destroyed!' . PHP_EOL;
        }
        $this->load->dbforge();

        if ($this->dbforge->drop_database('pfi')) {
            echo 'Database deleted!' . PHP_EOL;
        }

        exec("sudo rm -f {$this->config->item('persistent_user_file')}");

        $this->load->library('migration');

        exec("sudo rm -rf /etc/wpa_supplicant/networks/");

        if (! $this->migration->current()) {
            echo 'Error' . $this->migration->error_string() . PHP_EOL;
        } else {
            echo 'Migrations ran successfully!' . PHP_EOL;
        }
    }

    public function videos()
    {
        $this->load->model('VideoStream_model');
        $video = $this->input->get('video');
        $videoUrl = 'https://pfi-cloud.test.com/assets/videos/' . $video;
        $stream = new VideoStream_model();
        $stream->setPath($videoUrl);
        return $stream->start();
    }
}
