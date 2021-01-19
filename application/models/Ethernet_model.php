<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ethernet_model extends Network_model
{
    private $interface;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->interface = "eth0";
    }

    public function turnOnInterface()
    {
        log_message('debug', __FUNCTION__);
        exec("sudo ifconfig {$this->interface} up", $ouput, $status);
        if (0 !== $status) {
            return false;
        }
        return true;
    }

    public function turnOffInterface()
    {
        log_message('debug', __FUNCTION__);
        exec("sudo ifconfig {$this->interface} down", $output, $status);
        if (0 !== $status) {
            return false;
        }
        return true;
    }

    public function renewIpAddress($return = false, $repair = false)
    {
        log_message('debug', __FUNCTION__);
        if ($this->checkInterfaceIpAddress() === false && $this->checkIpRoute()) {
            exec("sudo dhclient {$this->interface}", $output, $status);
            if (0 !== $status) {
                log_message('error', 'There was a problem with dhclient retrieving an IP address : ' . json_encode($output));
                return false;
            }
            if ($return) {
                return $this->checkInterfaceIpAddress(true);
            }
            return true;
        } elseif ($repair === true) {
            exec("sudo dhclient {$this->interface}", $output, $status);
            if (0 !== $status) {
                log_message('error', 'There was a problem with dhclient retrieving an IP address : ' . json_encode($output));
                return false;
            }
            if ($return) {
                return $this->checkInterfaceIpAddress(true);
            }
            return true;
        }
    }

    public function releaseIpAddress()
    {
        log_message('debug', __FUNCTION__);
        exec("sudo dhclient -r {$this->interface}", $output, $status);
        if (0 !== $status) {
            log_message('error', 'There was a problem with dhclient releasing an IP address: ' . json_encode($output));
            return false;
        }
        return true;
    }

    /**
     * This function should check to see what the default route is set to
     * If it is not properly set, it should check request the interface ip address again
     * which will reset the default gateway to this interface
     * @param  boolean $return [description]
     * @return [type]          [description]
     */
    public function checkIpRoute($renewIfFalse = false, $return = false)
    {
        log_message('debug', __FUNCTION__);
        exec("ip route show | grep default ", $output, $status);
        $exploded = explode(" ", $output[0]);
        if ($return) {
            //print_r($output);
            //print_r($exploded);
            //echo 'The default route is not set to '.$this->interface.' it is set to: ' . $exploded[4];
            if ($exploded[4] !== $this->interface) {
                return false;
            }
        }
        if ($exploded[4] !== $this->interface && $renewIfFalse == true) {
            log_message('error', 'In ethernet model, the default route is not set to '.$this->interface.' it is set to: [' . $exploded[4] . '] but the system will attempt to fix that automatically.');
            $this->renewIpAddress(false, true);
        } elseif ($exploded[4] !== $this->interface && $renewIfFalse == false) {
            log_message('error', 'In ethernet model, the default route is not set to '.$this->interface.' it is set to: [' . $exploded[4] . ']');
            return false;
        }
        return true;
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

    public function getInterfaceStatus($return = false)
    {
        exec("sudo cat /sys/class/net/{$this->interface}/carrier", $output, $status);

        if ($return) {
            print_r($output);
        }
        // Link is down
        if ($output[0] == 0) {
            return false;
        } elseif ($output[0] == 1) {
            return true;
        }
    }

    public function getLinkRate()
    {
        if ($this->getInterfaceStatus()) {
            if ($this->checkInterfaceIpAddress()) {
                $linkRate = exec("sudo ethtool {$this->interface} | grep 'Speed: ' | cut -d':' -f2 | awk '{print $1 $2}'");
                //$this->writeLinkRate($linkRate);
                return $linkRate;
            }
        }
        return false;
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
                    'interface' => "{$this->interface}",
                    'rate' => $linkRate,
                    'lastModified' => time()
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

    public function getClientList()
    {
        log_message('debug', __FUNCTION__);
        exec("sudo arp-scan --interface={$this->interface} -l | tail -n +3 | head -n -3", $output);
            
        $count = 0;
        foreach ($output as $out) {
            if ($out !== "") {
                $count ++;
            }
        }
        $data =array(
                'networks'=>$output,
                'count'=>$count
                );
        return json_encode($data);
    }

    public function readLinkRate()
    {
    }

    public function getMacAddress()
    {
        log_message('debug', __FUNCTION__);
        exec("cat /sys/class/net/{$this->interface}/address", $output, $status);
        return $output[0];
    }
    
    public function isJSON($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}
