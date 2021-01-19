<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Navigation extends CI_Controller
{
    public function index()
    {
        $file = $this->input->get("file");
        if ($file !== null and $file !== "") {
            $json = file_get_contents(FCPATH."application/views/navigation/".$file);
            if ($this->isJson($json)) {
                echo $json;
            } else {
                echo json_encode(array('status' => false, 'msg' => 'Invalid JSON returned'));
            }
        }
    }

    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
