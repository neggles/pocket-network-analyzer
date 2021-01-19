<?php defined('BASEPATH') or exit('No direct script access allowed');
use Curl\Curl;

class Modem extends CI_Controller
{
    public $jobId;
    public $currentJob;
    public $jobName;
    public $banNumber;
    public $attribute;
    private $file = 'assets/modem/nonce.json';


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

        $this->session->set_userdata('jobId', $this->jobId);
    }
     
    public function index()
    {
        $data = array(
        'jobId'=>$this->jobId,
        'currentJob'=>$this->currentJob
        );

        $this->load->view('head', $data);
        $this->load->view('navigation', $data);
        $this->load->view('scripts', $data);
        $this->load->view('modem', $data);
        $this->load->view('footer', $data);
    }

    private function checkNonce()
    {
        if (file_exists($this->file)) {
            return;
        } else {
            file_put_contents($this->file, '');
        }
    }

    public function getCredentials()
    {
        $nonceValue = @file_get_contents($this->file);
        $attributes = json_decode($nonceValue, true);
    }

    public function captureModem()
    {
        $htmlOptions = array(
            LIBXML_NOWARNING =>1
            );

        $url = $this->input->get('url');

        $curl = new Curl();

        $curl->get($url);

        // A string containing everything in the response except for the headers

        if ($curl->error) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => false, 'msg' => $curl->errorMessage));
            //echo 'Error:' . $curl->errorCode . ': ' . $curl->errorMessage;
        } else {
            $body = str_replace('<?xml version="1.0"?>', '', $curl->response);
            libxml_use_internal_errors(true);
            $DOM = new DOMDocument;
            $DOM->loadHTML($body, 1);

            $domElemsToRemove = array();
            
            //get all inputs
            $items = $DOM->getElementsByTagName('input');

            $img = $DOM->getElementsByTagName('img');

            $link = $DOM->getElementsByTagName('link');

            $meta = $DOM->getElementsByTagName('meta');

            // Cycle through each img and add them to the array of elements to remove
            foreach ($img as $domElement) {
                $domElemsToRemove[] = $domElement;
            }

            // Cycle through each link and add them to the array of elements to remove
            foreach ($link as $domElement) {
                $domElemsToRemove[] = $domElement;
            }

            // Cycle through each meta tag and add them to the array of elements to remove
            foreach ($meta as $domElement) {
                $domElemsToRemove[] = $domElement;
            }


            // cycle through the array of elements to remove and remove them.
            // makes sure there are no 404 errors when loading the html.
            foreach ($domElemsToRemove as $i) {
                $i->parentNode->removeChild($i);
            }

            //display all H1 text
            for ($i = 0; $i < $items->length; $i++) {
                if ($items->item($i)->getAttribute('name') == "NONCE" || $items->item($i)->getAttribute('name') == "nonce") {
                    $nonce = $items->item($i)->getAttribute('value');
                    if (isset($nonce) and $nonce !== '') {
                        $array = array(
                            'key'=> 'nonce',
                            'value'=>$nonce
                            );
                        $this->writeFile($array);
                    }
                }
            }
            echo $DOM->saveHTML();
        }
    }

    private function writeFile(array $data, $file = null)
    {
        if ($file == null) {
            $file = $this->file;
        }
        if (file_exists($file)) {
            $nonce = file_get_contents($file);
            $array = json_decode($nonce, true);
            
            if (!isset($array[$data['key']])) {
                $array[$data['key']] = '';
            }
            if ($array[$data['key']] !== $data['value'] and $data['value'] !== '') {
                $array[$data['key']] = $data['value'];
                $json = json_encode($array);
                file_put_contents($file, $json);
            }
        } else {
            $array = array(
                $data['key'] => $data['value']
            );
            $json = json_encode($array);
            file_put_contents($file, $json);
        }
    }
}
