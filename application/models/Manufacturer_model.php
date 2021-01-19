<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manufacturer_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        // Loading second db and running query.
        $CI = &get_instance();
        //setting the second parameter to TRUE (Boolean) the function will return the database object.
        $this->db2 = $CI->load->database('macvendors', true);
    }

    public function searchByMac($mac)
    {
        if (strlen($mac) > 8) {
            $mac = substr($mac, 0, 8);
        }
        $mac = str_replace(":", "", $mac);

        $manufacturerMatch = $this->db2->select()
            ->where('mac', strtoupper($mac))
            ->get('macvendors');

        foreach ($manufacturerMatch->result() as $manuf) {
            return $manuf->vendor;
            //return isset($manuf->long_name) ? $manuf->long_name : $manuf->short_name;
        }
    }
}
