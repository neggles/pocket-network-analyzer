<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migrate extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // load migration library
        $this->load->library('migration');
    }

    public function index()
    {
        if (! $this->migration->current()) {
            echo 'Error' . $this->migration->error_string() . PHP_EOL;
        } else {
            echo 'Migrations ran successfully!' . PHP_EOL;
        }
    }
}
