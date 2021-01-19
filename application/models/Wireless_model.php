<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wireless_model extends Network_model
{
    public $interface;
    public $wirelessChannels;
    private $driver;
    /**
     *
     *   vhtChannelWidths[] = {
        [0] = "20 or 40 MHz",
        [1] = "80 MHz",
        [3] = "80+80 MHz",
        [2] = "160 MHz",
        };
     */
    private static $vhtChannelWidths = array(
        0 => '40',
        1 => '80',
        2 => '160',
        3 => '160',
        4 => '20'
    );
    //private static $channelPatternMatch = '/1(\W+)(\d+) (MHz)(\W)/i';
    private static $channelPatternMatch = '/(\d)/i';
    private static $distanceMhzM = 27.55;
    protected $retry;
    private $maxRetry;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->interface = $this->config->item('wireless_interface');
        $this->driver = $this->config->item('wireless_driver');
        $this->wirelessChannels = array(1,2,3,4,5,6,7,8,9,10,11);
        $this->retry = 0;
        $this->maxRetry = 5;
    }

    public static function metersToFeet($meters)
    {
        return floatval($meters) * 3.2808399;
    }

    public static function calculateDistance($frequency, $level)
    {
        return pow(10.0, (self::$distanceMhzM - (20 * log10($frequency)) + abs($level)) / 20.0);
    }

    public static function cleanChannelWidth($channelWidthValue)
    {
        preg_match(self::$channelPatternMatch, $channelWidthValue, $matches);
        
        return self::$vhtChannelWidths[$matches[0]];
    }

    public function saveThresholds($input)
    {
        $currentSettings = json_decode($this->settings->get('wireless_thresholds'));
        // Have to assign to variable or you get array to string conversion error.
        $index = $input['index'];
        if (is_object($currentSettings->$index)) {
            $currentSettings->$index->value = $input['value'];
            $currentSettings->$index->color = $input['color'];
                //echo json_encode($currentSettings);
                $this->settings->set('wireless_thresholds', json_encode($currentSettings));
            return true;
        }
        
        return false;
    }

    /*
    Run the customized wireless network scan program, the
    output from the program will be written to the file

    */
    public function runNewNetworkScan($job = null)
    {
        if ($this->getWirelessInterfaceMode() !== "Managed") {
            log_message('error', 'Changing interface mode to managed.');
            $this->setWirelessInterfaceMode("managed");
        }

        $this->runWirelessNetworkScan();
        log_message('error', 'Running new network scan.');
        $this->addNetworkScanToDb($job);
    }

    public function runNewNetworkScanForRoom($ssid = null)
    {
        if ($this->getWirelessInterfaceMode() !== "Managed") {
            $this->setWirelessInterfaceMode("managed");
            log_message('error', 'Switching wireless interface to managed mode.');
        }
        $this->runWirelessNetworkScan();
        $json = $this->parseScanResults();
        log_message('error', 'wireless_results [' . $json . ']');
        header('Content-Type: application/json');
        return $json;
    }

    public function runNewNetworkScanAnonymous()
    {
        $this->runWirelessNetworkScan();
        return $this->parseScanResults();
    }


    /*
    Get the most recent network scan results and
    return them, if there are not any results return
    NULL
    */
    public function getRecentResults($job = null)
    {
        $query= $this->db->order_by('date', 'desc')
        ->limit(1)
        ->select('data')
        ->where('job', $job)
        ->get('networkscan');
        
        if ($query->num_rows() !== 0) {
            foreach ($query->result() as $row) {
                return $row->data;
            }
        } else {
            return null;
        }
    }

    /*
    Function to add the recent network scan results to the
    database.
    */
    public function addNetworkScanToDb($job)
    {
        $json = $this->parseScanResults();
        log_message('error', 'Adding wireless network scan results to the db.');
        $data= array(
            'date'=>date('Y-m-d H:i:s'),
            'data'=> $json,
            'job'=>$job
        );

        $this->insertNetworkScanResults($data);
    }

    /*
    Function to determine which icon color to show depending on the
    signal quality.
 */
    public function getIcon($thresholds, $signalStrength)
    {
        $wirelessThresholds = json_decode($thresholds);

        switch (true) {
            case ($signalStrength > (int)$wirelessThresholds->high->value):
                return $wirelessThresholds->high->color;
            break;
            case ($signalStrength > (int)$wirelessThresholds->medium->value):
                return $wirelessThresholds->medium->color;
            break;
            case ($signalStrength < (int)$wirelessThresholds->medium->value):
            case ($signalStrength < (int)$wirelessThresholds->low->value):
                return $wirelessThresholds->low->color;
            break;
            default:
                return '#000';
            break;
        }
    }

    public function deleteCacheItem($item = null)
    {
        if ($item !== null) {
            $this->cache->delete($item);
        }
    }

    public function checkForScans()
    {
        $query = $this->db->select()
            ->where('job', $this->jobId)
            ->limit(1)
            ->get('networkscan');

        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }
    
    public function globalWirelessNetworks()
    {
        if (!$this->getInterfaceStatus()) {
            $this->turnOnInterface();
        }
        // Added perl to the command for consistency
        $currentAvailableNetworks = shell_exec('sudo perl ' . FCPATH . 'assets/networks/wireless-networks');
        log_message('debug', 'Capturing basic information on available wireless networks.');
        return json_decode($currentAvailableNetworks);
    }
   

    /*
    Private function to insert the new network scan results
 */
    private function insertNewRecords($data)
    {
        $this->db->insert('networkscan', $data);
    }
    
    /*
    Public function to insert the new network scan results
 */
    public function insertNetworkScanResults($data)
    {
        $this->insertNewRecords($data);
    }


    /*
    Open the json file and parse the results from the network scan.

 */
    public function parseWirelessScanResults()
    {
        $json = file_get_contents('logs/wireless-networks/data.json');
        log_message('error', 'Parsing the json wireless scan results.');
        $data= array(
            'date'=>date('Y-m-d H:i:s'),
            'data'=> $json,
            'job'=>$this->jobId
        );
        $this->insertNewRecords($data);
    }
    
    /*
    Get the network scan results from the database.

 */
    public function getRecords()
    {
        $query = $this->db->select('data')
                    ->get('networkscan');

        foreach ($query->result() as $row) {
            return $row->data;
        }
    }

    /*
    Use the wpa_passphrase utility to get the hash key from the given
    passphrase.
 */
    public function getPreSharedKey($ssid, $pass)
    {
        if ($pass !== "" && $pass !== null) {
            $output = shell_exec('sudo wpa_passphrase "' . $ssid . '" '. $pass);
            if (substr($output, 0, 8) !== "network") {
                return $output;
            } else {
                return false;
            }
        } else {
            return "network={
                ssid=\"" . $ssid . "\"
            }";
        }
        return false;
    }


    public function createOpenNetworkConfigurationFile($ssid, $encryption = false)
    {
        $cleanSsid = self::cleanSsid($ssid);
        if (!file_exists('/etc/wpa_supplicant/networks/'.$cleanSsid.'.conf')) {
            $conf = "network={\nssid=\"$ssid\"\nkey_mgmt=NONE\n}";

            if (! write_file('/etc/wpa_supplicant/networks/'.$cleanSsid.'.conf', $conf, 'w')) {
                return json_encode(array('status' => false, 'msg' =>'error creating configuration file'));
            } else {
                return true;
            }
        }
    }

    /*
    Write the pre shared key / network configuration content to a file.
    The file name will be the ssid concatenated with ".conf"
 */
    
    public function savePreSharedKey($ssid, $conf, $encryption = 'WPA')
    {
        if (isset($conf)) {
            switch ($encryption) {
                case 'NONE':
                    $conf = str_replace(
                        "}",
                        "key_mgmt=NONE
            }",
                        $conf
                    );
                    break;
                case 'WPA':
                    $conf = str_replace(
                        "}",
                        "key_mgmt=WPA-PSK WPA-EAP NONE
                proto=RSN WPA
                pairwise=CCMP TKIP
                group=CCMP TKIP WEP104 WEP40
                eap=TTLS PEAP TLS
            }",
                        $conf
                    );
                    break;

                case 'WPS':
                    $conf = str_replace(
                        "}",
                        "key_mgmt=WPA-PSK WPA-EAP NONE
                proto=RSN WPA
                pairwise=CCMP TKIP
                group=CCMP TKIP WEP104 WEP40
                eap=TTLS PEAP TLS
            }",
                        $conf
                    );
                    break;
                case 'WEP':
                    $conf = str_replace(
                        "}",
                        "key_mgmt=NONE
                    }",
                        $conf
                    );
                    $conf = str_replace("#psk", "wep_key0", $conf);
                    break;
                default:
                    $conf = str_replace(
                        "}",
                        "key_mgmt=WPA-PSK WPA-EAP NONE
                proto=RSN WPA
                pairwise=CCMP TKIP
                group=CCMP TKIP WEP104 WEP40
                eap=TTLS PEAP TLS
            }",
                        $conf
                    );
                    break;
            }
            $cleanSsid = self::cleanSsid($ssid);
            if (! write_file('/etc/wpa_supplicant/networks/'.$cleanSsid.'.conf', $conf, 'w')) {
                exec("sudo chown -R pfi:root /etc/wpa_supplicant/networks");
                if (! write_file('/etc/wpa_supplicant/networks/'.$cleanSsid.'.conf', $conf, 'w')) {
                    log_message('error', 'Could not create network configuration file.');
                    $this->notification->sendNotification(null, 'wireless', 'error', 'Could not create a configuration file for '. $ssid);
                }
                exit;
            } else {
                log_message('error', 'Successfull created configuration file.');
                $this->notification->sendNotification(null, 'wireless', 'success', 'Sucessfully created configuration file for '. $ssid);
                return true;
            }
        }
        return false;
    }

    /*
    Gives the ssid of the current network if connected
 */
    public function getNetworkSSID()
    {
        $ssid = shell_exec('sudo iwgetid --raw');
        return $ssid;
    }

    
    public function getLastScanDate()
    {
        $query = $this->db->select('date')
            ->where('job', $this->jobId)
            ->limit(1)
            ->order_by('date', 'desc')
            ->get('networkscan');
        
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $result) {
                return $result->date;
            }
        }
        return false;
    }
    
    public function humanTiming($time)
    {
        $time = time() - $time; // to get the time since that moment

        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) {
                continue;
            }
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }
    }


    public function getWirelessChannelStats($job = null)
    {
        $query = $this->db->order_by('date', 'desc')
        ->limit(1)
        ->select('data')
        ->where('job', $job)
        ->get('networkscan');
        
        if ($query->num_rows() !== 0) {
            foreach ($query->result() as $row) {
                return $this->processWirelessChannels(json_decode($row->data));
            }
        } else {
            return null;
        }
    }

    public function processWirelessChannels($networkList)
    {
        $wirelessChannels = array();

        foreach ($networkList as $network) {
            if (isset($network->dist_system->channel)) {
                $channel = $network->dist_system->channel;
                isset($wirelessChannels[$channel]) ? $wirelessChannels[$channel]++ : $wirelessChannels[$channel] = 1;
            }
        }
        return $wirelessChannels;
    }

    public function runBandwidthTest($scanTime)
    {
        $this->load->model('Bandwidth_model', 'bandwidth');
        //exec("sudo /var/www/html/assets/networks/bandwidth-test", $ouput, $status);
        
        $this->bandwidth->setUpEnvironment($scanTime);
        
        $channelPackets = array();

        foreach ($this->wirelessChannels as $channel) {
            $count = 0;
            $output = '';
            exec("sudo tcpdump -r ".FCPATH."assets/tcpdump/channel-{$channel}-file.txt", $output);
            log_message('error', 'Running tcpdump for bandwidth calculation.');
            foreach ($output as $line) {
                $count++;
            }
            
            $channelPackets[] = array(
                'channel'=>$channel,
                'packets'=>$count
            );
        }
        return json_encode($channelPackets);
    }



    /**
     *  Parse the xml file from the wireless scan and convert them to json.
     *  @todo  write a method to write the scan results to a specific filename.
     */
    public function parseScanResults()
    {
        /**
         * for now we must append the closing network_scan tag
         */
        if (!$this->checkWirelessResultsFile()) {
            return json_encode(array('status' => false,'msg'=>'The wireless results file has not been created, there is an error somewhere.'));
        }

        $myXMLData = file_get_contents($this->config->item('wireless_networks_results_xml')) . '</network_scan>';
        libxml_use_internal_errors(true);
        try {
            $xml = simplexml_load_string($myXMLData);
        } catch (Exception $e) {
            echo json_encode(array('status' => false, 'msg' => $e->getMessage()));
        }
        
        $data = array();

        foreach ($xml->wireless_network as $x) {
            if ((string)$x->probe_response_frame->ssid !== null && (string)$x->probe_response_frame->ssid !== '') {
                $mac = (string)$x->mac;
                $freq = (string)$x->frequency;
                $capabilities = array();
                // This call slows everything down quite a bit and serves no real purpose or benefit
                //$manufacturer = $this->manuf->searchByMac($mac);

                foreach ($x->bss_capabilities as $capability) {
                    $capabilities = (array)$capability->capability;
                }
                
                $signal_strength = (string)$x->signal_strength;

                $ssid = (string)$x->probe_response_frame->ssid;
                $supported_rates = (array)$x->probe_response_frame->supported_rates->rate ?: 'No Information';
                $ds_parameter = (array)$x->probe_response_frame->ds_parameter;
                $tim = (array)$x->probe_response_frame->tim;
                $ht_capes = (array)$x->probe_response_frame->ht_capabilities;
                $ht_operation = (array)$x->probe_response_frame->ht_operation;
                $wmm = (array)$x->probe_response_frame->wireless_multimedia;
                $rsn = (array)$x->probe_response_frame->rsn;
                $wps = (array)$x->probe_response_frame->wps;
                $esp = (array)$x->probe_response_frame->extended_supported_rates;
                $erp = (string)$x->probe_response_frame->erp;
                $ext_capes = (array)$x->probe_response_frame->extended_capabilities;
                $bss_load = (array)$x->probe_response_frame->bss_load;
                $wpa = (array)$x->probe_response_frame->wpa;
                $avail_channels = (array)$x->probe_response_frame->available_channels;
                $vht_capes = (array)$x->probe_response_frame->vht_capabilities;
                $vht_operation = (array)$x->probe_response_frame->vht_operation;
                
                if (!substr_compare($ssid, '\x00', 0, 4)) {
                    $ssid = "Hidden SSID";
                }

                $ssid = addslashes($ssid);

                $data[] = array(
                    'ssid' =>$ssid ,
                    'mac'=>$mac,
                    'manufacturer'=>$manufacturer,
                    'frequency'=>$freq,
                    'capabilities'=>$capabilities,
                    'signal_strength'=>$signal_strength,
                    'supported_rates'=>$supported_rates,
                    'dist_system'=>$ds_parameter,
                    'traffic_indic_map'=>$tim,
                    'ht_capabilities'=>$ht_capes,
                    'ht_operation'=>$ht_operation,
                    'vht_capabilities'=>$vht_capes,
                    'vht_operation'=>$vht_operation,
                    'wireless_multimedia'=>$wmm,
                    'robust_secure_network'=>$rsn,
                    'wps'=>$wps,
                    'wpa'=>$wpa,
                    'extended_supported_rates'=>$esp,
                    'extended_rate_physical_layer'=>$erp,
                    'extended_capabilities'=>$ext_capes,
                    'bss_load'=>$bss_load,
                    'available_channels'=>$avail_channels
                );
            }
        }
        return json_encode($data, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT);
    }

    public function getIpAddress()
    {
        log_message('debug', __FUNCTION__);
        $ipAddress = exec("sudo ifconfig {$this->interface} | grep 'inet addr:' | awk '{ print $2 }'");
        return $ipAddress;
    }

    public function getWirelessInterfaceMode()
    {
        log_message('debug', __FUNCTION__);
        $output = exec("sudo iwconfig {$this->interface} | grep 'Mode:' | cut -d: -f2 | awk '{ print $1}'");

        return $output;
    }


    public function getWirelessInterfaceEssid()
    {
        log_message('debug', __FUNCTION__);
        $output = exec("sudo iwconfig {$this->interface} | grep 'ESSID:' | cut -d: -f2 | awk '{ print $1}'");
        $output = str_replace('"', "", $output);
        $output = str_replace("'", "", $output);
        return $output;
    }

    public function getWirelessInterfaceFrequency()
    {
        log_message('debug', __FUNCTION__);
        $output = exec("sudo iwconfig {$this->interface} | grep 'Frequency:' | cut -d: -f3 | awk '{print $1}'");
        return $output;
    }

    public function getWirelessAccessPointMac()
    {
        log_message('debug', __FUNCTION__);
        $output = exec("sudo iwconfig {$this->interface} | grep 'Access Point: ' | cut -f4 | awk '{print $6}'");
        return $output;
    }

    public function getWirelessBitRate()
    {
        log_message('debug', __FUNCTION__);
        $output = exec("sudo iwconfig {$this->interface} | grep 'Bit Rate:' | cut -d: -f2 | awk '{print $1 $2}'");
        return $output;
    }

    public function setWirelessInterfaceMode($mode = "managed")
    {
        log_message('debug', __FUNCTION__);
        $this->turnOffInterface();
        exec("sudo iwconfig {$this->interface} mode $mode", $output, $status);
        if ($status) {
            log_message('error', 'There was an issue changing the mode of the wireless card: ' . json_encode($output));
        }
        $this->turnOnInterface();
    }


    private function checkWirelessResultsFile()
    {
        log_message('debug', __FUNCTION__);
        if (file_exists($this->config->item('wireless_networks_results_xml'))) {
            return true;
        }
        return false;
    }

    private function deleteWirelessResultsFile()
    {
        log_message('debug', __FUNCTION__);
        if (file_exists($this->config->item('wireless_networks_results_xml'))) {
            if (unlink($this->config->item('wireless_networks_results_xml'))) {
                return true;
            }
        }
    }

    /**
     * [runWirelessNetworkScan description]
     * @todo change the custom-iw program to enable
     * passing a filename to the program
     */
    public function runWirelessNetworkScan()
    {
        log_message('debug', __CLASS__ . '::' . __FUNCTION__);
        if ($this->checkWirelessResultsFile()) {
            log_message('debug', 'Deleting the existing wireless results file.');
            $this->deleteWirelessResultsFile();
        }
        if (!$this->getInterfaceStatus()) {
            $this->turnOnInterface();
        }
        if (substr(sprintf("%o", fileperms($this->config->item('custom_iw'))), -4) !== "0755") {
            chmod($this->config->item('custom_iw'), 0755);
        }

        exec("sudo {$this->config->item('custom_iw')} {$this->interface} scan", $output, $status);
        if ($status) {
            log_message('error', 'Error running wireless network scan: ' . json_encode($output));
        }
    }

    public function turnOnInterface()
    {
        log_message('debug', __CLASS__ . '::' . __FUNCTION__);
        exec("sudo ifconfig {$this->interface} up", $output, $status);
        if ($status) {
            log_message('error', 'There was an issue turning on wireless interface: '. json_encode($output));
            return false;
        }
        return true;
    }

    public function turnOffInterface()
    {
        log_message('debug', __CLASS__ . '::' . __FUNCTION__);
        exec("sudo ifconfig {$this->interface} down", $output, $status);
        if ($status) {
            log_message('error', 'There was a problem shutting down the wireless interface.');
            return false;
        }
        return true;
    }

    public function renewIpAddress($return = false, $repair = false)
    {
        log_message('debug', __CLASS__ . '::' . __FUNCTION__);
        if ($this->checkInterfaceIpAddress() === false && $this->checkIpRoute()) {
            exec("sudo dhclient {$this->interface}", $output, $status);
        
            if (0 !== $status) {
                log_message('error', 'class: '. __CLASS__ .  'function: ' . __FUNCTION__ . 'There was a problem with dhclient retrieving an IP address : ' . json_encode($output));
                return false;
            }
            if ($return) {
                return $this->checkInterfaceIpAddress(true);
            } else {
                return true;
            }
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
        log_message('debug', __CLASS__ . '::' . __FUNCTION__);
        exec("sudo dhclient -r {$this->interface}", $output, $status);
        if (0 !== $status) {
            log_message('error', 'There was a problem with dhclient releasing an IP address.');
            return false;
        }
        return true;
    }

    public function getInterfaceStatus()
    {
        log_message('debug', __CLASS__ . '::' . __FUNCTION__);

        exec("sudo cat /sys/class/net/{$this->interface}/carrier", $output, $status);
        // Link is down
        if (0 !== $status) {
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
        log_message('debug', __CLASS__ . '::' . __FUNCTION__);
        exec("ip route show | grep default ", $output, $status);
        $exploded = explode(" ", $output[0]);
        if ($return) {
            print_r($output);
            print_r($exploded);
            echo 'The default route is not set to '.$this->interface.' it is set to: ' . $exploded[4];
            if ($exploded[4] !== $this->interface) {
                return false;
            }
        }
        if ($exploded[4] !== $this->interface && $renewIfFalse == true) {
            log_message('error', 'In wireless model, the default route is not set to '.$this->interface.' it is set to: [' . $exploded[4] . '] but the system will attempt to fix that automatically.');
            $this->renewIpAddress(false, true);
            return true;
        } elseif ($exploded[4] !== $this->interface && $renewIfFalse == false) {
            log_message('error', 'In wireless model, the default route is not set to '.$this->interface.' it is set to: [' . $exploded[4] . ']');
            return false;
        }
        return true;
    }

    public function checkInterfaceIpAddress($return = false)
    {
        log_message('debug', __CLASS__ . '::' . __FUNCTION__);
        $ipAddr = exec("sudo ifconfig {$this->interface} | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'");
        
        if (($ipAddr !== "" || $ipAddr !== null) && !empty($ipAddr)) {
            log_message('debug', 'IP Address is set to: ' . $ipAddr);
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

    public function getWpaAuthenticationStatus()
    {
        log_message('debug', __CLASS__ . '::' . __FUNCTION__);
        $authStatus = exec("sudo wpa_cli -i{$this->interface} status | grep 'wpa_state=' | cut -d'=' -f2 | awk '{print $1}' ");

        if ($authStatus == "COMPLETED") {
            exec("sudo wpa_cli -i{$this->interface} status", $output);
            log_message('debug', 'WPA Status Complete:' . json_encode($output));

            // Create an easily parsible array for the browser to be able to work with the information
            $array = array();
            foreach ($output as $string) {
                $newString = explode('=', $string);
                $array[$newString[0]] = $newString[1];
            }
            $this->notification->sendNotification(null, 'wireless', 'complete_connection', json_encode($array));
            return true;
        } elseif ($authStatus == "SCANNING") {
            exec("sudo wpa_cli -i{$this->interface} status", $output);
            log_message('debug', 'WPA Status Scanning :' . json_encode($output));
            if ($this->retry < $this->maxRetry) {
                log_message('debug', 'WPA Status scanning, looping back through to test attempt #:' . $this->retry);
                $this->retry++;
                sleep(2);
                $this->getWpaAuthenticationStatus();
            } else {
                $this->notification->sendNotification(null, 'wireless', 'error', 'Unable to connect to wireless AP, error could be from incorrect password or AP is too far away.');
                $this->killWpaSupplicant();
                return false;
            }
        } elseif ($authStatus == "ASSOCIATING") {
            exec("sudo wpa_cli -i{$this->interface} status", $output);
            log_message('debug', 'WPA Status Associating :' . json_encode($output));
            if ($this->retry < $this->maxRetry) {
                log_message('debug', 'WPA Status associating, looping back through to test attempt #:' . $this->retry);
                $this->retry++;
                sleep(2);
                $this->getWpaAuthenticationStatus();
            } else {
                $this->notification->sendNotification(null, 'wireless', 'error', 'Unable to connect to wireless AP, error could be from incorrect password or AP is too far away.');
                $this->killWpaSupplicant();
                return false;
            }
        } else {
            exec("sudo wpa_cli -i{$this->interface} status", $output);
            log_message('debug', 'WPA Status: ' . json_encode($output));
            if ($this->retry < $this->maxRetry) {
                log_message('debug', 'WPA Status '.$authStatus.', looping back through to test attempt #:' . $this->retry);
                $this->retry++;
                sleep(2);
                $this->getWpaAuthenticationStatus();
            } else {
                $this->notification->sendNotification(null, 'wireless', 'error', 'Unable to connect to wireless AP, error could be from incorrect password or AP is too far away.');
                $this->killWpaSupplicant();
                return false;
            }
        }
    }

    public function killWpaSupplicant()
    {
        log_message('debug', 'Killing current wpa_supplicant process');
        exec("sudo wpa_cli -p /var/run/wpa_supplicant -i{$this->interface} terminate", $output, $status);
        if (0 !== $status) {
            log_message('error', 'Error killing current wpa_supplicant: ' . json_encode($output));
        }
        $this->releaseIpAddress();
        return true;
    }

    public function checkWirelessNetworkStatus()
    {
        log_message('debug', 'Checking wireless network status');

        if (is_file('/var/run/wpa_supplicant/{$this->interface}')) {
            return false;
        }
        return true;
    }

    public function getLinkRate()
    {
        if ($this->getInterfaceStatus()) {
            if ($this->checkInterfaceIpAddress()) {
                $linkRate = exec("sudo iwconfig {$this->interface} | grep 'Bit Rate=' | cut -d'=' -f2 | awk '{print $1 $2}'");
                return $linkRate;
            } else {
                return false;
            }
        }
        return false;
    }

    public function getConfiguredNetworkList()
    {
        $this->load->helper('directory');
        return directory_map('/etc/wpa_supplicant/networks/', 1);
    }

    public function getCurrentNetworkPassword($file)
    {
        if (isset($file) and is_file('/etc/wpa_supplicant/networks/'.$file)) {
            $fileContent = $this->load->file('/etc/wpa_supplicant/networks/'.$file);
        }
    }

    public function checkWirelessConfiguration($ssid)
    {
        if (! $this->getInterfaceStatus()) {
            $this->turnOnInterface();
        }

        if ($this->checkWirelessNetworkStatus()) {
        }
    }

    public function getClientList()
    {
        exec("sudo arp-scan --interface={$this->interface} -l | tail -n +3 | head -n -3", $output, $status);


        $count = 0;
        foreach ($output as $out) {
            if ($out !== "") {
                $count ++;
            }
        }
        $data = array(
                'networks' => $output,
                'count' => $count
                );
        return json_encode($data);
    }

    public static function cleanSsid($ssid)
    {
        return preg_replace("/[^a-zA-Z0-9]/", "", $ssid);
    }

    private static function configFileExists($file)
    {
    }

    private function executeWpaSupplicant($ssid)
    {
        $cleanSsid = self::cleanSsid($ssid);
        log_message('debug', 'Attempting wpa_supplicant wireless connection');
        exec("sudo wpa_supplicant -D{$this->driver} -i{$this->interface} -C/var/run/wpa_supplicant  -c/etc/wpa_supplicant/networks/{$cleanSsid}.conf -B", $output, $status);
        if ($status) {
            log_message('error', 'Error running wpa_supplicant: ' . json_encode($output));
        }
    }

    private function executeIwconfig($ssid)
    {
        log_message('debug', 'No configuration exists, attempting simple wireless connection');
        exec("sudo iwconfig wlan0 essid {$ssid}", $output, $status);
        if ($status) {
            log_message('error', 'There was an error setting the ssid using iwconfig: ' . json_encode($output));
        }
    }

    /*
    Connect to a wireless network for the ability to surf
    the web and other functionalities.
 */

    public function connectToWirelessNetwork($job = null, $ssid = null)
    {
        log_message('debug', 'In connectToWirelessNetwork function in Wireless_model connecting to: ' . $ssid);
        if ($ssid !== null and $ssid !== "") {
            /*
            Check if wpa_supplicant is currently running
            If it is we need to kill it before attempting to connect to
            a network.
            */
           
            if ($this->checkWirelessNetworkStatus()) {
                $this->killWpaSupplicant();
            }

            /*
            The following list of commands are just to make
            sure that nothing is in control of the wlan0 interface.
             */

            //$this->turnOffInterface();

            $cleanSsid = self::cleanSsid($ssid);
            //Release the current ip for the interface
            //$this->releaseIpAddress();

            log_message('debug', 'Releasing the ip address for interface: '. $this->interface);
            
            //Turn on the wireless interface
            if (!$this->getInterfaceStatus()) {
                $this->turnOnInterface();
            }

            // This command attempts to connect to the given network.
            if (! is_file("/etc/wpa_supplicant/networks/{$cleanSsid}.conf")) {
                $this->executeIwconfig($ssid);
            } else {
                $this->executeWpaSupplicant($ssid);
            }

            if ($this->getWpaAuthenticationStatus() === false) {
                log_message('error', 'WPA Authentication status returned false: ' . __FUNCTION__);
                return false;
            }

            //Get new ip address for wireless interface.
            $this->renewIpAddress(false, true);

            // If no ip address assigned, run the dhclient again
            if ($this->checkInterfaceIpAddress() === false) {
                $this->renewIpAddress(false, true);
            }

            $this->checkIpRoute(true);
            return true;
        } else {
            log_message('error', 'SSID is not set in call to: ' . __FUNCTION__);
            return false;
        }
    }

    public function startWidar($mode = 'standard')
    {
        if (substr(sprintf("%o", fileperms($this->config->item('widar_bin'))), -4) !== "0755") {
            chmod($this->config->item('widar_bin'), 0755);
        }
        if ($mode == 'standard') {
            exec("sudo {$this->config->item('python_command')} {$this->config->item('widar_python')} > /dev/null &", $output, $status);
        } elseif ($mode == 'monitor') {
            exec("sudo {$this->config->item('python_command')} {$this->config->item('wifimon_python')} > /dev/null &", $output, $status);
        }

        if (0 !== $status) {
            log_message('error', 'There was an error running Widar in mode:' . $mode);
            $array = array(
                'status' => true,
                'msg' => 'Error starting widar: ' . json_encode($output)
                );
            return json_encode($array);
        } else {
            $array = array(
                'status' => true,
                'msg' => 'Successfully started widar'
                );
            return json_encode($array);
        }
    }

    public function checkIfAuthenticated()
    {
        $ssid = $this->getWirelessInterfaceEssid();
        if (empty($ssid)) {
            return false;
        } else {
            return true;
        }
    }

    private function getWidarPid($mode)
    {
        if ($mode == "standard") {
            exec("sudo ps aux | grep '[w]idar' | awk '{print $2}'", $output, $status);
        } elseif ($mode == "monitor") {
            exec("sudo ps aux | grep '[w]ifimon' | awk '{print $2}'", $output, $status);
        }
        if (0 !== $status) {
            return false;
        } else {
            if (empty($output)) {
                return false;
            } else {
                return $output[0];
            }
        }
    }

    public function stopWidar($mode)
    {
        $pid = $this->getWidarPid($mode);
        if ($pid !== false || $pid !== null) {
            exec("sudo kill -TERM {$pid}", $output, $status);
            if (0 !== $status) {
                $array = array(
                'status' => false,
                'msg' => 'Failed stopping widar'
                );
                return json_encode($array);
            } else {
                $array = array(
                'status' => true,
                'msg' => 'Successfully stopped widar'
                );
                return json_encode($array);
            }
        } else {
            $array = array(
                'status' => true,
                'msg' => 'Successfully stopped widar'
            );
            return json_encode($array);
        }
    }


    public function checkWirelessCardStatus()
    {
        $cards = array('wlan0','wlan1');
        $cardStatus = array();
        foreach ($cards as $card) {
            if (is_dir("/sys/class/net/{$card}")) {
                $status = array('card' => $card, 'status' => 'up');
                array_push($cardStatus, $status);
            } else {
                $status = array('card' => $card, 'status' => 'down');
                array_push($cardStatus, $status);
            }
        }

        if ($this->config->item('report_wireless_cards') === true) {
            $this->reportWirelessCardStatus($cardStatus);
        }
    }

    private function reportWirelessCardStatus($cardStatus)
    {
        // Confirm before attempting that there is a network connection.
        if ($this->network->checkInternetConnection()) {
            $curl = new \Curl\Curl();
            $curl->setOpt(CURLOPT_RETURNTRANSFER, true);
            $curl->setUserAgent('PocketFi/0.1');
            $this->load->model('Ethernet_model', 'ethernet');
            $mac = $this->ethernet->getMacAddress();
            $data = array('mac' => $mac, 'data' => $cardStatus);
            $curl->post($this->config->item('pfi_cloud_api_endpoint') . 'status/wireless', $data);

            if ($curl->error) {
                $msg = array('status' => false, 'code' => $curl->errorCode, 'msg' => $curl->errorMessage,  'data' => false);
                log_message('error', 'There was an error reporting card status: ' . json_encode($msg));
            } else {
                $msg = array('status' => true, 'msg' => 'Successfully called the endpoint.', 'data' => $curl->response);
                log_message('error', json_encode($msg));
            }
            $curl->close();
            return true;
        }
        return false;
    }
}
