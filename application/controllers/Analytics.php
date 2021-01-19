<?php defined('BASEPATH') or exit('No direct script access allowed');

class Analytics extends CI_Controller
{
    public function index()
    {
        session_write_close();

        $url = $this->input->post('url');
        $title = str_replace("/", " ", $this->input->post('title'));
        $returnContent = $this->analytics->trackVisit($url, $title);
        echo json_encode($returnContent);
    }

    public function storedActions()
    {
        $this->db->select('data');
        $return = $this->db->get('analytics');
        print_r(json_decode($return->result_object()[0]->data));
    }

    public function flushResults()
    {
        $return = json_encode($this->analytics->clearAnalyticsFromCache());
        if (is_cli()) {
            log_message('debug', $return);
        } else {
            echo $return;
        }
    }
}
