<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bandwidth_model extends Wireless_model
{
    //private $interface;
    
    public function __construct()
    {
        parent::__construct();
    }

    public function checkInterfaceMode()
    {
        $mode = exec("sudo iwconfig {$this->interface} | grep 'Mode:' | cut -d: -f2 | awk '{print $1}'");

        if ($mode !== "Monitor") {
            exec("sudo iwconfig {$this->interface} mode monitor");
        }
    }

    public function setUpEnvironment($scanTime)
    {
        exec("sudo rm -rf " . FCPATH . "assets/tcpdump/channel*");
        $this->wireless->shutDownInterface();
        $this->checkInterfaceMode();
        $this->wireless->turnOnInterface();
        $this->runTcpDump($scanTime);
    }

    public function shutDownEnvironment()
    {
        $this->wireless->shutDownInterface();

        exec("sudo iwconfig {$this->interface} mode managed");
        exec("sudo chown -R www-data:root " . FCPATH . "assets/tcpdump");
        $this->wireless->turnOnInterface();
    }

    public function runTcpDump($scanTime = 5)
    {

        foreach ($this->wirelessChannels as $channel) {
            
            $this->wireless->shutDownInterface();

            exec("sudo iwconfig {$this->interface} channel {$channel}");

            $this->wireless->turnOnInterface();

            exec("sudo tcpdump -G {$scanTime} -W 1 -i {$this->interface} -B 256 -n -s 256 -w " . FCPATH . "assets/tcpdump/channel-{$channel}-file.txt");
        }

        $this->shutDownEnvironment();
    }
}
