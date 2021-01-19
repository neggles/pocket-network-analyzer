<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wpasupplicant_model extends Network_model
{
    public $interface;
    public $wirelessChannels;
    private $driver;
    private $contents;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->interface = $this->config->item('wireless_interface');
        $this->driver = $this->config->item('wireless_driver');
    }

    public function readConfigurationFile($configFile)
    {
        if (empty($configFile)) {
            return false;
        }
        $this->contents = file_get_contents($configFile);
        //echo $this->contents;
    }

    private function findNetworkBlock()
    {
    }

    public function findNetworkValues()
    {
        $config = array();
        foreach ($this->contents as $line) {
            if ($line !== "network={" || $line !== "}") {
                //echo $line;
                list($keys, $value) = explode('=', $line);
                $config[$keys] = $value;
            }
        }
        return $this->contents;
        //return $config;
    }

    private function readNetworkBlock()
    {
    }
}
