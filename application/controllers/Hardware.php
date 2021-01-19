<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hardware extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $query = $this->db->select('*')
        ->limit(1)
        ->get('registration');
        print_r($query->result()[0]);
    }

    public function register()
    {
        $this->load->model('Hardware_model', 'hardware');
        $this->checkWirelessStatus();
        return $this->hardware->registerDevice();
    }

    private function checkWirelessStatus()
    {
        $this->load->model('Wireless_model', 'wireless');
        $this->wireless->checkWirelessCardStatus();
    }
}
