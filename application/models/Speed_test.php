<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Speed_test extends CI_Model
{
    public $connection;
    public $job;
    public $speedtest;
    public $server;
    public $ssid;
    private $port;
    private $host;
    private $customSpeedtest;
    private $returnChannel;
    private $speedtestHost;
    private $speedtestPort;
    private $downConnections;
    private $upConnections;
    private $maxRetry = 5;
    private $retry = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Wireless_model', 'wireless');
        $this->load->model('Ethernet_model', 'ethernet');
    }
    
    public function getSpeedTests($id)
    {
        log_message('debug', __FUNCTION__);
        $this->db->select();
        $this->db->where('job', $id);
        return $this->db->get('speedtest');
    }

    public function getSpeedTestsJSON($id)
    {
        log_message('debug', __FUNCTION__);
        $this->db->select('id as speedtestid, date, upload, download, location, connection, ssid, job');
        $this->db->where('job', $id);
        return $this->db->get('speedtest');
    }

    public function runSpeedTest($post)
    {
        log_message('debug', __FUNCTION__);
        $this->returnChannel = isset($post['type']) ? $post['type'] : "speedtest";
        $this->connection = $post['connection'];
        $this->job = $post['jobId'];
        $this->ssid = isset($post['ssid']) ? $post['ssid'] : null;
        $this->server = (isset($post['server']) && $post['server'] !== "") ? $post['server'] : null;
        $this->port = (isset($post['port']) && $post['port'] !== "") ? $post['port'] : null;


        $this->upConnections = (isset($post['upConnections']) && $post['upConnections'] !== "") ? $post['upConnections'] : $this->config->item('speedtest_up_connections');
        $this->downConnections = (isset($post['downConnections']) && $post['downConnections'] !== "") ? $post['downConnections'] : $this->config->item('speedtest_down_connections');
        // is this speedtest wireless?
        // if so, we need to ensure that the wireless network is up and is valid
        // so that we can retrieve the speedtest configuration on the wireless
        // network not the ethernet network.

        if ($this->connection === "wireless" && $this->ssid !== "") {
            if ($this->ethernet->getInterfaceStatus()) {
                log_message('debug', 'Running wireless speedtest so releasing ethernet interface ip address.');
                //$this->ethernet->turnOffInterface();
                $this->ethernet->releaseIpAddress();
            }

            if ($this->wireless->getInterfaceStatus()) {
                log_message('debug', 'Running wireless speedtest and status is up. function: ' . __FUNCTION__ . ' line: ' . __LINE__);
                if ($this->wireless->getWirelessInterfaceEssid() !== $this->ssid) {
                    log_message('error', 'Current wireless ssid does not match the desired one.');
                    if ($this->wireless->connectToWirelessNetwork($this->job, $this->ssid) === false) {
                        log_message('error', 'Unable to connect to the wireless network.');
                        $this->ethernet->renewIpAddress(false, true);
                    }
                }

                if (!$this->wireless->checkInterfaceIpAddress()) {
                    log_message('error', 'Wireless interface is not assigned an ip address.');
                    if ($this->wireless->connectToWirelessNetwork($this->job, $this->ssid) === false) {
                        $this->sendNotification(
                            'no-network',
                            'Unable to obtain an ip address for the wireless interface.'
                        );
                        $this->ethernet->renewIpAddress(false, true);
                        return json_encode(array('status'=>false,'msg'=>'Error obtaining ip address.'));
                    }
                }
            } else {
                log_message('error', 'Running wireless speedtest and status is down.');
                $this->wireless->turnOnInterface();
                if ($this->wireless->connectToWirelessNetwork($this->job, $this->ssid) === false) {
                    log_message('error', 'Failed to connect to wireless network.');
                    $this->ethernet->renewIpAddress(false, true);
                }

                if (! $this->wireless->checkInterfaceIpAddress()) {
                    log_message('error', 'Wireless interface is not assigned an ip address.');
                    $this->sendNotification(
                        'no-network',
                        'Unable to obtain an ip address for the wireless interface.'
                    );
                    return;
                }
            }
        } elseif ($this->connection === "ethernet" && !$this->ethernet->getInterfaceStatus()) {
            if ($this->wireless->getInterfaceStatus()) {
                $this->wireless->turnOffInterface();
            }
            $this->ethernet->turnOnInterface();
            $this->ethernet->renewIpAddress(false, true);
            if (!$this->ethernet->getInterfaceStatus()) {
                $this->sendNotification(
                    'no-network',
                    'Network Connection Issues'
                );
                exit;
            } else {
                $this->ethernet->renewIpAddress(false, true);
            }
        } elseif ($this->connection === "ethernet" && $this->ethernet->getInterfaceStatus()) {
            $this->wireless->turnOffInterface();
            
            $this->ethernet->renewIpAddress(false, true);
        }
        // Check that the ssid is set and that the connection is set to wireless
        if ($this->ssid && $this->connection === "wireless") {
            // Run wireless speedtest
            $this->executeWirelessSpeedTest();
        } else {
            // Run ethernet speedtest
            $this->executeEthernetSpeedTest();
        }
    }

    private function executeEthernetSpeedTest()
    {
        log_message('debug', 'Running an ethernet speed test: ' . __FUNCTION__);

        if ($this->wireless->checkWirelessNetworkStatus()) {
            log_message('debug', 'Wireless interface was active.');
            $this->wireless->turnOffInterface();
        }

        if (! $this->ethernet->getInterfaceStatus()) {
            log_message('debug', 'Ethernet interface was down: '. __FUNCTION__);
            $this->ethernet->turnOnInterface();
            $this->ethernet->renewIpAddress(false, true);
        }
        if ($this->ethernet->checkIpRoute(true)) {
            $this->executeSpeedTest();
        }

        $this->cleanUpAfterSpeedtest();
    }

    private function cleanUpAfterSpeedtest()
    {
        log_message('debug', __FUNCTION__);
        if ($this->connection === "wireless") {
            $this->wireless->killWpaSupplicant();
            $this->ethernet->renewIpAddress(false, true);
        } else {
            $this->wireless->turnOnInterface();
        }
    }

    /**
     * [executeWirelessSpeedTest description]
     * @return [type] [description]
     */
    private function executeWirelessSpeedTest()
    {
        log_message('debug', 'Running a wireless speed test: '. __FUNCTION__);
        if ($this->ethernet->getInterfaceStatus()) {
            log_message('debug', 'Ethernet interface was active.');
            //$this->ethernet->turnOffInterface();
            $this->ethernet->releaseIpAddress();
        }

        if (!$this->wireless->getInterfaceStatus() || !$this->wireless->checkInterfaceIpAddress()) {
            log_message('debug', 'Inside wireless speedtest execution and the wireless interface was not up or not assigned an ip address');
            if ($this->wireless->connectToWirelessNetwork($this->job, $this->ssid) === false) {
                log_message('error', 'Error connecting to the wireless network.');
                $this->ethernet->renewIpAddress(false, true);
            }
        }

        if ($this->wireless->checkIpRoute(true)) {
            $this->executeSpeedTest();
        }

        $this->cleanUpAfterSpeedtest();
    }

    private function handleSpeedtestHostAndPortValues($host = null)
    {
        // If no value was explicitly passed, then we will
        // go through the process of selecting the best server.
        log_message('debug', 'Host: ' . json_encode($host));
        if ($this->server === null) {
            if ($host === null) {
                $this->host = $this->getBestSpeedtestHost();
            } else {
                $this->host = $this->getSpeedtestInstanceFromServer($host, false);
            }
            
            if (is_object($this->host)) {
                if (!empty($this->host->host)) {
                    $this->customSpeedtest = true;
                    $this->speedtestHost = $this->host->host;
                } else {
                    $this->speedtestHost = $this->config->item('speedtest_default_host');
                }
                if (!empty($this->host->port)) {
                    $this->speedtestPort = $this->host->port;
                } else {
                    $this->speedtestPort = $this->config->item('speedtest_default_port');
                }
            } else {
                return false;
            }
        } else {
            $this->speedtestHost = $this->server;
            if ($this->port === null || $upload === true) {
                $server = new StdClass();
                $server->host = $this->speedtestHost;
                $this->host = $this->getSpeedtestInstanceFromServer($server, false);
                if (is_object($this->host)) {
                    if (!empty($this->host->port)) {
                        $this->customSpeedtest = true;
                        $this->speedtestPort = $this->host->port;
                    } else {
                        $this->speedtestPort = $this->config->item('speedtest_default_port');
                    }
                } else {
                    $this->speedtestPort = $this->config->item('speedtest_default_port');
                }
            } else {
                $this->speedtestPort = $this->port;
            }
        }
    }

    /**
     * [executeSpeedTest description]
     * @return [type] [description]
     */
    private function executeSpeedTest()
    {
        log_message('debug', __FUNCTION__);
        $success = false;
        $intervals = $this->config->item('speedtest_interval');
        $this->customSpeedtest = false;

        if ($this->handleSpeedtestHostAndPortValues() === false) {
            log_message('error', 'Host and port were not able to be properly set.');
            $this->cleanUpAfterSpeedtest();
            return false;
        }
        $this->sendNotification('host', $this->speedtestHost);
        log_message('debug', 'Speedtest Host: ' . $this->speedtestHost);
        log_message('debug', 'Speedtest Port: ' . $this->speedtestPort);
        // Download test
        // -c is the host to connect to
        // -p is the port number to connect to
        // -R || --download flag makes it a download test
        // --http makes it send the data to local websocket API
        // -t is for time interval (default is 10 seconds)
        // -P is for the number of parallel threads to use to maximize bandwidth
        // -O is for omit, this option helps to get a more accurate result of the average
        // bandwidth by not counting the beginning seconds of a TCP slow start period
        // -f is for the format of the return units m is for Mbit/s
        $speedtestCommand = "{$this->config->item('alternate_speedtest_script')} -c {$this->speedtestHost} -p {$this->speedtestPort} --download -t {$intervals} -P {$this->downConnections} -O 3 -f m --json -i 1 --logfile {$this->config->item('speedtest_log_file')}";

        
        if (substr(sprintf("%o", fileperms($this->config->item('alternate_speedtest_script'))), -4) !== "0755") {
            chmod($this->config->item('alternate_speedtest_script'), 0755);
        }
        
        // Send the notification that the speedtest is running
        $this->sendNotification(
            'success',
            'running'
        );
        $this->removeSpeedtestLogFile();

        log_message('debug', 'Starting download rmr speedtest.');
        $this->sendNotification('status', 'download_start');
        exec($speedtestCommand, $output, $status);
        if (0 !== $status) {
            log_message('error', 'Error running download test: ' . json_encode($output) . ' server['.$this->speedtestHost.'] port[' . $this->speedtestPort. ']');
            $success = false;
                //return false;
        } else {
            $sucess = true;
        }
        $json = file_get_contents($this->config->item('speedtest_log_file'));
        $this->sendNotification('download', $json);
        $this->removeSpeedtestLogFile();

        if ($this->customSpeedtest === true) {
            if ($this->handleSpeedtestHostAndPortValues($this->host, true) === false) {
                log_message('error', 'Host and port were not able to be properly set. LINE: ' . __LINE__);
                return false;
            }
        }

        log_message('debug', 'Speedtest Host: ' . $this->speedtestHost);
        log_message('debug', 'Speedtest Port: ' . $this->speedtestPort);

        $uploadSpeedtestCommand = "{$this->config->item('alternate_speedtest_script')} -c {$this->speedtestHost} -p {$this->speedtestPort} -t {$intervals} -P {$this->upConnections} -f m --json --logfile {$this->config->item('speedtest_log_file')}";
        
        $this->sendNotification('status', 'upload_start');
        log_message('debug', 'Starting upload rmr test.');
        exec($uploadSpeedtestCommand, $output, $status);
        if (0 !== $status) {
            log_message('error', 'Error running upload test: ' . json_encode($output) . ' server[' . $this->speedtestHost . '] port[' . $this->speedtestPort . ']');
            $success = false;
                //return false;
        } else {
            $success = true;
        }

        $json = file_get_contents($this->config->item('speedtest_log_file'));
        $this->sendNotification('upload', $json);
        log_message('debug', 'Upload Complete');
        $this->removeSpeedtestLogFile();
        
                
        if (!$success) {
            log_message('error', 'Error running speedtest');
            return json_encode(array('status' => false,'msg'=>'Error running speedtest'));
        }
    }

    private function removeSpeedtestLogFile()
    {
        if (file_exists($this->config->item('speedtest_log_file'))) {
            unlink($this->config->item('speedtest_log_file'));
            return true;
        } else {
            return false;
        }
    }

    private function sendNotification($context, $message)
    {
        $this->load->model('Notifications_model', 'notification');
        $this->notification->sendNotification(
            $this->job,
            $this->returnChannel,
            $context,
            $message
        );
    }

    public function getSpeedTestId()
    {
        return $this->id;
    }
    
    public function parseSpeedTestResults(array $speedTestData = array())
    {
        $data = array(
            'date' => date('Y-m-d H:i:s'),
            'upload' => $speedTestData['upload'] . ' ' . $speedTestData['uploadUnits'],
            'download' => $speedTestData['download'] . ' ' . $speedTestData['downloadUnits'],
            'connection' => $speedTestData['connection'],
            'location' => isset($speedTestData['location']) ? $speedTestData['location'] : null,
            'ssid' => $speedTestData['ssid'],
            'job' => isset($speedTestData['jobId']) ? $speedTestData['jobId'] : $this->session->userdata('jobId'),
            );
        if (!empty($speedTestData['download']) && !empty($speedTestData['upload'])) {
            $newTestId = $this->insertNewRecords($data);
            $data['download'] = $speedTestData['download'];
            $data['upload'] = $speedTestData['upload'];
            $data['download_units'] = $speedTestData['downloadUnits'];
            $data['upload_units'] = $speedTestData['uploadUnits'];
            $data['host'] = $speedTestData['host'];
            $this->saveSpeedTestToCloud($data);

            $data['id'] = $newTestId;
        }
        $this->wireless->killWpaSupplicant();

        return json_encode($data);
    }

    /*
     * Send the speedtest data to the pfi-cloud
     */
    private function saveSpeedTestToCloud(array $data)
    {
        $curl = new \Curl\Curl();
        $curl->setUserAgent('PocketFi/0.0.1 (speedtest)');
        $this->load->model('Ethernet_model', 'ethernet');
        $data['mac'] = $this->ethernet->getMacAddress();
        $data['uid'] = $this->session->userdata('uid') ? $this->session->userdata('uid') : 'system';
        $data['version'] = $this->version->currentTag;
        $data['action'] = 'speedtest';


        $curl->post($this->config->item('pfi_cloud_api_endpoint') . 'speedtest/results', $data);
        
        if ($curl->error) {
            log_message('error', $curl->errorCode . ': ' . $curl->errorMessage);
        } else {
            log_message('debug', 'Sent new speedtest results to cloud');
        }
        $curl->close();
    }
    
    public function setComments($comment, $id, $updateJobId)
    {
        $testResults = array(
            'comments'=>$comment
        );
        
        $this->db->where('id', $id);
        $this->db->where('job', $updateJobId);
        $this->db->update('speedtest', $testResults);
        
        if ($this->db->affected_rows()==0) {
            return 0;
        } elseif ($this->db->affected_rows()>0) {
            return 1;
        }
    }
    
    public function getComments()
    {
        foreach ($this->speedtest->result() as $row) {
            return $row->comments;
        }
    }
    
    /**
     * @param  int $speedtestId The speedtest id to retrieve the comments for
     * @return json Json encoded array for the speedtest comments
     */
    public function getCommentsById($speedtestId)
    {
        log_message('debug', __FUNCTION__);
        $this->db->select('comments');
        $this->db->where('id', $speedtestId);
        $comms = $this->db->get('speedtest');
        foreach ($comms->result() as $comm) {
            $data[]=array(
            'comments'=>$comm->comments
            );
        }
        return json_encode($data);
    }
    
    private function insertNewRecords($data)
    {
        log_message('debug', __FUNCTION__);
        $this->db->insert('speedtest', $data);
        return $this->db->insert_id();
    }
    
    public function getRecords()
    {
        log_message('debug', __FUNCTION__);
        $this->db->order_by('date', 'desc');
        $this->db->select();
        $this->db->limit(1);
        $query=$this->db->get('speedtest');
        return $query;
    }

    public function getBestSpeedtestHost()
    {
        $this->sendNotification('status', 'Selecting the fastest speedtest host available');
        $hosts = $this->getListOfSpeedtestServers();
        $results = array();
            
        if (is_array($hosts)) {
            foreach ($hosts as $host) {
                $options = array('host' => $host->host, 'count' => 1);
                $results[] = json_decode($this->ping($options, 'json'));
            }

            $fastest = null;
            foreach ($results as $result) {
                if ($result->status == true) {
                    if ($fastest !== null) {
                        if ($result->elapsed < $fastest->elapsed) {
                            $fastest = $result;
                        }
                    } else {
                        $fastest = $result;
                    }
                }
            }
            // The second parameter tells whether the call for server is a test or not.
            return $this->getSpeedtestInstanceFromServer($fastest, false);
        } else {
            return false;
        }
    }

    private function getListOfSpeedtestServers()
    {
        $curl = new \Curl\Curl();
        $curl->setUserAgent('PocketFi/0.0.1 (speedtest)');
        $curl->setHeader($this->config->item('speedtest_api_key_name'), $this->config->item('speedtest_api_key_value'));
        $curl->get($this->config->item('speedtest_api') . 'servers/closest');
        
        if ($curl->error) {
            $this->sendNotification('error', 'There was an error trying to get the list of available speedtest servers: ' . $curl->errorCode . ': ' . $curl->errorMessage);
            log_message('error', 'There was an error trying to get the list of available speedtest servers: ' .  $curl->errorCode . ': ' . $curl->errorMessage);
            return $curl->errorCode . ': ' . $curl->errorMessage;
        } else {
            log_message('debug', 'Host List: ' . json_encode($curl->response));
            return $curl->response->message;
        }
    }

    private function getSpeedtestInstanceFromServer(StdClass $server, $test = false)
    {
        $curl = new \Curl\Curl();
        $curl->setUserAgent('PocketFi/0.0.1 (speedtest)');
        $curl->setHeader($this->config->item('speedtest_api_key_name'), $this->config->item('speedtest_api_key_value'));
        $curl->get('https://' . $server->host . '/api/tcp/server?test=' . $test);
        if ($curl->error) {
            if ($curl->errorCode === 404 && ($this->retry < $this->maxRetry)) {
                log_message('error', 'Got 404, going to sleep for 2 seconds and then try again');
                sleep(2);
                $this->retry++;
                $this->getSpeedtestInstanceFromServer($server, $test);
            } else {
                $this->retry = 0;
                $this->sendNotification('error', 'There was an error trying to get the speedtest server: ' . $curl->errorCode . ': ' . $curl->errorMessage);
                log_message('error', 'Speedtest Host Error: ' . $server->host . ' ' .  $curl->errorCode . ': ' . $curl->errorMessage);
                return $curl->errorCode . ': ' . $curl->errorMessage;
            }
        } else {
            $this->retry = 0;
            log_message('debug', __FUNCTION__ . ': ' . json_encode($curl->response));
            return $curl->response;
        }
    }

    private function ping($input)
    {
        $host = (isset($input['host']) && $input['host'] !== "") ? $input['host'] : $this->config->item('speedtest_default_host');
        $count = (isset($input['count']) && $input['count'] !== "") ? $input['count'] : 5;
        
        if (substr(sprintf("%o", fileperms($this->config->item('ping'))), -4) !== "0755") {
            chmod($this->config->item('ping'), 0755);
        }

        $output = shell_exec("sudo {$this->config->item('ping')} -s -j {$host} &");
        return $output;
    }
}
