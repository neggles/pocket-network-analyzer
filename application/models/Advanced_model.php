<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Advanced_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function traceroute($input)
    {
        $host = (isset($input['host']) && $input['host'] !== "") ? $input['host'] : $this->config->item('speedtest_default_host');
        
        if (substr(sprintf("%o", fileperms($this->config->item('traceroute'))), -4) !== "0755") {
            chmod($this->config->item('traceroute'), 0755);
        }

        exec("{$this->config->item('traceroute')} {$host} --http &");
    }

    public function ping($input, $type = 'http')
    {
        log_message('debug', 'In advanced_model ping()');
        $host = (isset($input['host']) && $input['host'] !== "") ? $input['host'] : $this->config->item('speedtest_default_host');
        $count = (isset($input['count']) && $input['count'] !== "") ? $input['count'] : 5;
        
        log_message('debug', 'host: ' . $host);

        if (substr(sprintf("%o", fileperms($this->config->item('ping'))), -4) !== "0755") {
            chmod($this->config->item('ping'), 0755);
        }

        if ($type === 'http') {
            $option = '-K';
        } elseif ($type === 'json') {
            $option = '-j';
        }

        $output = shell_exec("sudo {$this->config->item('ping')} -s {$option} {$host} --count={$count} &");
        if ($type === 'json') {
            return $output;
        }
    }
}
