<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vpn_model extends Network_model
{
    private $interface;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->interface = "tun0";
    }

    public function turnOnInterface()
    {
        exec("sudo ifconfig {$this->interface} up", $output, $status);
        if ($status) {
            return false;
        }
        return true;
    }

    public function turnOffInterface()
    {
        exec("sudo ifconfig {$this->interface} down", $output, $status);
        if ($status) {
            return false;
        }
        return true;
    }

    public function renewIpAddress($return = false)
    {
        log_message('debug', __FUNCTION__);
        if ($this->checkInterfaceIpAddress() === false) {
            exec("sudo service openvpn restart", $output, $status);
            if (0 !== $status) {
                log_message('error', 'There was a problem with openvpn retrieving an IP address : ' . json_encode($output));
                return false;
            }
            if ($return) {
                return $this->checkInterfaceIpAddress(true);
            }
            return true;
        }
    }

    public function checkInterfaceIpAddress($return = false)
    {
        log_message('debug', __FUNCTION__);
        $ipAddr = exec("sudo ifconfig {$this->interface} | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'");

        if ($ipAddr != "" or $ipAddr != null) {
            log_message('debug', 'class: '. __CLASS__ .  ' function: ' . __FUNCTION__ . ' IP Address is: ' . $ipAddr);
            if ($return) {
                return $ipAddr;
            } else {
                return true;
            }
        } else {
            log_message('error', 'The IP address is not set.');
        }
        return false;
    }

    public function releaseIpAddress()
    {
        return true;
    }

    public function getInterfaceStatus()
    {
        $status = exec("sudo cat /sys/class/net/{$this->interface}/carrier");

        if ($status) {
            $output = exec("sudo cat /sys/class/net/{$this->interface}/operstate");
            if ($output == "up") {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getLinkRate()
    {
        if ($this->getInterfaceStatus()) {
            if ($this->checkInterfaceIpAddress()) {
                $linkRate = exec("sudo ethtool {$this->interface} | grep 'Speed: ' | cut -d':' -f2 | awk '{print $1 $2}'");
                //$this->writeLinkRate($linkRate);
                return $linkRate;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function writeLinkRate($linkRate)
    {
        $interfaceLinkFile = "assets/link-rate/";

        if (!is_dir($interfaceLinkFile)) {
            mkdir($interfaceLinkFile);
        }
        if (is_file($interfaceLinkFile. "{$this->interface}")) {
            $fileContent = file_get_contents($interfaceLinkFile. "{$this->interface}");
            
            if ($this->isJSON($fileContent)) {
                $jsonContent = json_decode($fileContent);
            } else {
                $linkArray = array(
                'interface'=>"{$this->interface}",
                'rate'=>$linkRate,
                'lastModified'=>time()
                            );
                write_file($interfaceLinkFile. "{$this->interface}", json_encode($linkArray, JSON_PRETTY_PRINT));
            }
        } else {
            $linkArray = array(
                'interface'=>"{$this->interface}",
                'rate'=>$linkRate,
                'lastModified'=>time()
                );
            write_file($interfaceLinkFile. "{$this->interface}", json_encode($linkArray, JSON_PRETTY_PRINT));
        }
    }

    public function readLinkRate()
    {
    }

    public function isJSON($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}
