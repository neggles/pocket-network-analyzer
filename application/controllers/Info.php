<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Info extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->input->is_cli_request()) {
            phpinfo();
        }
    }

    public function index()
    {
        if (!$this->input->is_cli_request()) {
            show_error("You cannot directly access this file.", 403);
        }
    }

    public function debug()
    {
        phpinfo();
    }
}
