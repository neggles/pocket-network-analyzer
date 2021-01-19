<?php defined('BASEPATH') or exit('No direct script access allowed');

class Manufacturer extends CI_Controller
{
   
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Manufacturer_model', 'manuf');
    }
    
    public function searchByMac()
    {
        $mac = $this->input->post('mac');
        return $this->manuf->searchByMac($mac);
    }
}
