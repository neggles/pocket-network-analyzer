<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
     
    public function createUser()
    {
        if ($this->input->post('uid')) {
            $uid = $this->input->post('uid');
            echo $this->user->createNewUser($uid);
        } else {
            return false;
        }
    }
}
