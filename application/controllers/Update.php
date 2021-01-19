<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Update extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Update_model', 'update');
        $this->load->dbforge();
    }

    public function index()
    {
    }
    
    public function runUpdates()
    {
        session_write_close();
        if (is_cli()) {
            $this->update->runUpdates();
        } elseif ($this->input->post('manually_update')) {
            $this->update->runUpdates();
        } else {
            echo "You cannot directly access this file.". PHP_EOL;
        }
    }
}
