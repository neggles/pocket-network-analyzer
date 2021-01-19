<?php
 defined('BASEPATH') or exit('No direct script access allowed');

class Cacher
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance(); //grab an instance of CI
        $this->initiate_cache();
    }

    public function initiate_cache()
    {
        $this->CI->load->driver('cache', array('adapter' => $this->CI->config->item('primary_cache'), 'backup' => $this->CI->config->item('backup_cache'),'key_prefix' => 'gfi_'));
    }
}
