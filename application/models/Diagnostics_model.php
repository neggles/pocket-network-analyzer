<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Diagnostics_model extends CI_Model
{
    private $services;

    public function __construct()
    {
        parent::__construct();
        $this->services = array('redis-server', 'slanger','dnsmasq');
    }

    public function getFileSystemUsage()
    {
        return passthru("df -h /tmp | tail -1 | awk '{print $5}'");
    }
       
    public function checkPowerSource()
    {
        return passthru("sudo acpi -a");
    }

    public function checkWirelessIpAddress()
    {
        return $this->wireless->checkWirelessIpAddress();
    }

    public function checkEthernetIpAddress()
    {
        $ipAddr = exec("sudo ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'");

        if ($ipAddr != "" or $ipAddr != null) {
            return $ipAddr;
        } else {
            return false;
        }
    }

    public function ethernetStats()
    {
        exec("sudo cat /sys/class/net/eth0/carrier", $stats);

        if ($stats) {
            return exec("sudo cat /sys/class/net/eth0/operstate");
        } else {
            return false;
        }
    }

    public function wirelessStats()
    {
        $stats = exec("sudo cat /sys/class/net/wlan0/carrier");

        if ($stats) {
            return exec("sudo cat /sys/class/net/wlan0/operstate");
        } else {
            return false;
        }
    }

    public function checkDnsmasq()
    {
        exec("sudo service dnsmasq status | grep 'Active' | awk '{ print $2 $3}'", $dnsmasq);
        return $dnsmasq[0];
    }

    public function checkOpenVpn()
    {
        exec("sudo service openvpn status | grep 'Active' | awk '{ print $2 $3}'", $openvpn);
        return $openvpn[0];
    }

    public function checkRedis()
    {
        exec("sudo service redis-server status | grep 'Active' | awk '{ print $2 $3}'", $redis);
        return $redis[0];
    }

    public function checkSlanger()
    {
        exec("sudo service slanger status | grep 'Active' | awk '{ print $2 $3}'", $slanger);
        return $slanger[0];
    }

    public function checkHostapd()
    {
        if (file_exists("/run/hostapd.wlan1.pid")) {
            $pid = file_get_contents("/run/hostapd.wlan1.pid");
            exec("ps -p " . $pid . " > /dev/null", $output, $status);
            if (0 === $status) {
                return 'active(running)';
            } else {
                return 'inactive';
            }
        } else {
            return 'inactive';
        }
    }

    public function checkMemory()
    {
        $totalMemory= exec("free mem -h | grep 'Mem:' | awk '{print $2}'");
        $usedMemory = exec("free mem -h | grep 'Mem:' | awk '{print $3}'");
        $sizeTypes = array("G", "M", "B");
        
        $usedMem = null;

        if (strpos($usedMemory, "M")) {
            $usedMem = str_replace($sizeTypes, "", $usedMemory);
            $usedMem = $usedMem / 1000;
        } elseif (strpos($usedMemory, "B")) {
            $usedMem = str_replace($sizeTypes, "", $usedMemory);
            $usedMem = $usedMem / 10000;
        } elseif (strpos($usedMemory, "G")) {
            $usedMem = str_replace($sizeTypes, "", $usedMemory);
            $usedMem = $usedMem;
        }

        $totalMem = str_replace($sizeTypes, "", $totalMemory);
        $availPercent = round($usedMem / $totalMem * 100, 1);
        $memory = array(
            'total'=>$totalMemory,
            'used'=>$usedMemory,
            'percent'=>$availPercent
            );
        return json_encode($memory);
    }
}
