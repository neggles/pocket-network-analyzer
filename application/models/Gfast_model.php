<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gfast_model extends Network_model
{
    protected $interface;

    /**
     * [__construct description]
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Network_model', 'network');
        $this->load->helper('file');
    }

    /**
     * [isLoaded description]
     *
     * @return boolean [description]
     */
    public function isLoaded()
    {
        return $this->_isGfastModuleLoaded();
    }

    public function isConnected()
    {
        return $this->isGfastCliRunning();
    }

    /**
     * [isGfastModuleLoaded description]
     *
     * @return boolean [description]
     */
    private function _isGfastModuleLoaded()
    {
        return is_dir('/proc/gfast');
    }

    /**
     * [isGfastModuleInstalled description]
     *
     * @return boolean [description]
     */
    public function isGfastModuleInstalled()
    {
        return is_file('/lib/modules/4.9.24+/kernel/net/gfast/gfast.ko');
    }

    /**
     * [getSystemTime description]
     *
     * @return [type] [description]
     */
    public function getSystemTime()
    {
        exec("date '+%Y-%m-%dT%H:%M:%SZ'", $output);
        $outputClean = trim($output[0]);
        return $outputClean;
    }

    /**
     * [forceClockReset description]
     *
     * @return [type] [description]
     */
    public function forceClockReset()
    {
        exec("sudo service ntp stop");
        exec("sudo ntpd -gq");
        exec("sudo service ntp start");
        return json_encode(array('status' => true, 'msg' => 'Time updated'));
    }

    /**
     * [setSystemTime description]
     *
     * @param [type] $time [description]
     *
     * @return [type] [description]
     */
    public function setSystemTime($time)
    {
        if (!empty($time)) {
            //log_message('debug', 'Time string: ' . $time);
            //$exec = "sudo date --set \"{$time}\"";
            //log_message('debug', 'Exec: ' . $exec);
            exec("sudo date --set \"{$time}\"", $output);
            log_message('debug', json_encode($output));
            return array(
                'status' => true,
                'msg' => $output[0]
            );
        }
        return false;
    }

    /**
     * [isGfastCliRunning description]
     *
     * @return boolean [description]
     */
    public function isGfastCliRunning()
    {
        exec('sudo cat /proc/gfast/driver_enable', $output);
        return $output[0];
    }
    
    /**
     * [_loadGfastModule description]
     *
     * @return [type] [description]
     */
    private function _loadGfastModule()
    {
        if (!$this->_isGfastModuleLoaded()) {
            exec("sudo modprobe gfast");
        }
        return true;
    }

    /**
     * [loadGfast description]
     *
     * @return [type] [description]
     */
    public function loadGfast()
    {
        $this->_loadGfastModule();

        if (substr(sprintf("%o", fileperms($this->config->item('configure_gfast'))), -4) !== "0755") {
            chmod($this->config->item('configure_gfast'), 0755);
        }

        if ($this->isGfastCliRunning() !== 1) {
            log_message('debug', 'gfast-cli is not running, executing again');
            exec("sudo " . $this->config->item('configure_gfast'), $output, $status);
            if (0 !== $status) {
                log_message('debug', json_encode($output));
                return array('status' => $status, 'output'=> $output, 'msg' => 'Error starting gfast-cli');
            }
            return array('status' => $status, 'output'=> $output, 'msg' => 'Successfully started gfast-cli');
        }
        return array('status' => true, 'output' => 'gfast cli is already running');
    }

    private function convertToJson($string)
    {
        return str_replace("'", "\"", $string);
    }

    /**
     * [getGfastNumDevices description]
     *
     * @return [type] [description]
     */
    public function getGfastNumDevices()
    {
        $output = shell_exec("python3 -c 'from sckgfast import *; print(get_number_of_devices())'");
        return $this->convertToJson($output);
    }

    /**
     * [getGfastNumLines description]
     *
     * @return [type] [description]
     */
    public function getGfastNumLines()
    {
        $output = shell_exec("python3 -c 'from sckgfast import *; print(get_number_of_lines())'");
        return $this->convertToJson($output);
    }

    /**
     * [getGfastDeviceInfo description]
     *
     * @return [type] [description]
     */
    public function getGfastDeviceInfo()
    {
        $output = shell_exec("python3 -c 'from sckgfast import *; print(get_device_info())'");
        return $this->convertToJson($output);
    }

    /**
     * [getGfastLineState description]
     *
     * @return [type] [description]
     *
     * show time
     */
    public function getGfastLineState()
    {
        if ($this->_isGfastModuleLoaded() && $this->isGfastCliRunning() !== 1) {
            $output = shell_exec("python3 -c 'from sckgfast import *; print(get_line_state())'");
            return $this->convertToJson($output);
        }
        return false;
    }

    /**
     * [getGfastLineStatus description]
     *
     * @return [type] [description]
     */
    public function getGfastLineStatus()
    {
        if ($this->_isGfastModuleLoaded()) {
            $output = shell_exec("python3 -c 'from sckgfast import *; print(get_line_status())'");
            return $this->convertToJson($output);
        }
        return false;
    }

    /**
     * [getGfastLineInventory description]
     *
     * @return [type] [description]
     */
    public function getGfastLineInventory()
    {
        $output = shell_exec("python3 -c 'from sckgfast import *; print(get_line_inventory())'");
        return $this->convertToJson($output);
    }

    /**
     * [getGfastEtrs description]
     *
     * @return [type] [description]
     */
    public function getGfastLineEtrs()
    {
        $output = shell_exec("python3 -c 'from sckgfast import *; print(get_line_etrs())'");
        return $this->convertToJson($output);
    }

    /**
     * [getGfastLineConfig description]
     *
     * @return [type] [description]
     */
    public function getGfastLineConfig()
    {
        $output = shell_exec("python3 -c 'from sckgfast import *; print(get_line_config())'");
        return $this->convertToJson($output);
    }

    /**
     * [getGfastLinePerformanceCounters description]
     *
     *
     *
     *
     * @return [type] [description]
     */
    public function getGfastLinePerformanceCounters()
    {
        $output = shell_exec("python3 -c 'from sckgfast import *; print(get_line_performance_counters())'");
        return $this->convertToJson($output);
    }

    /**
     * [getGfastCounters description]
     *
     * @return [type] [description]
     */
    public function getGfastCounters()
    {
        $output = shell_exec("python3 -c 'from sckgfast import *; print(get_gfast_counters())'");
        return $this->convertToJson($output);
    }

    /**
     * [getGfastFramerInitCounters description]
     *
     * @return [type] [description]
     */
    public function getGfastFramerIntiCounters()
    {
        $output = shell_exec("python3 -c 'from sckgfast import *; print(get_framer_init_counters())'");
        return $this->convertToJson($output);
    }

    /**
     * [getGfastFramerRawCounters description]
     *
     * @return [type] [description]
     */
    public function getGfastFramerRawCounters()
    {
        $output = shell_exec("python3 -c 'from sckgfast import *; print(get_framer_raw_counters())'");
        return $this->convertToJson($output);
    }

    /**
     * [getGfastFduInitCounters description]
     *
     * @return [type] [description]
     */
    public function getGfastFduInitCounters()
    {
        $output = shell_exec("python3 -c 'from sckgfast import *; print(get_fdu_init_counters())'");
        return $this->convertToJson($output);
    }

    /**
     * [getGfastFduRawCounters description]
     *
     * @return [type] [description]
     */
    public function getGfastFduRawCounters()
    {
        $output = shell_exec("python3 -c 'from sckgfast import *; print(get_fdu_raw_counters())'");
        return $this->convertToJson($output);
    }

    /**
     * [getGfastDerivedCounters description]
     *
     * @return [type] [description]
     */
    public function getGfastDerivedCounters()
    {
        $output = shell_exec("python3 -c 'from sckgfast import *; print(get_derived_counters())'");
        return $this->convertToJson($output);
    }



    /**
     * [getInterfaceStatus description]
     *
     * @param boolean $return [description]
     *
     * @return [type]          [description]
     */
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
    /**
     * [getMacAddress description]
     *
     * @return [type] [description]
     */
    public function getMacAddress()
    {
        log_message('debug', __FUNCTION__);
        exec("cat /sys/class/net/{$this->interface}/address", $output, $status);
        return $output[0];
    }

    public function saveComplexToDatabase($input)
    {
        if ($input['id'] == '') {
            unset($input['id']);
        }
        if (!$this->db->insert('gfast_mdu', $input)) {
            $error = $this->db->error();
            return $error;
        }
        return array('status' => true, 'id' => $this->db->insert_id());
    }

    private function doesUnitExist($unit)
    {
        $query = $this->db->select('*')
        ->where('unit', $unit['unit'])
        ->where('bldg', $unit['bldg'])
        ->get('gfast_mdu_unit');

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function saveUnitToDatabase($input)
    {
        if ($input['id'] == 0) {
            unset($input['id']);
        }

        if (!empty($input['id'])) {
            if ($this->doesUnitExist($input)) {
                log_message('debug', 'Unit id was set, and it was unchanged');
                $this->db->where('id', $input['id']);
                if (!$this->db->update('gfast_mdu_unit', $input)) {
                    $error = $this->db->error();
                    return $error;
                }
            } else {
                // Change made to unit or bldg number
                log_message('debug', 'Unit id was set, but it was changed');
                unset($input['id']);
                if (!$this->db->insert('gfast_mdu_unit', $input)) {
                    $error = $this->db->error();
                    return $error;
                }
            }
        } else {
            if (!$this->db->insert('gfast_mdu_unit', $input)) {
                $error = $this->db->error();
                return $error;
            }
        }
        return array('status' => true, 'id' => $this->db->insert_id());
    }

    public function getComplexesFromDatabase()
    {
        $this->db->select('*');
        $query = $this->db->get('gfast_mdu');
        return $query->result();
    }

    public function getUnitsForMduFromDatabase($mdu)
    {
        $this->db->select('*')
        ->where('mdu', $mdu);
        $query = $this->db->get('gfast_mdu_unit');
        return $query->result();
    }

    public function getEtrValuesFromDatabase($id, $return = 'default')
    {
        $this->db->select('*')
            ->where('unit', $id);
        $query = $this->db->get('gfast_mdu_unit_results');
        if ($return === 'json') {
            return json_encode($query->result());
        }
        return $query->result();
    }



    public function getAllEtrValuesFromDatabase()
    {
        $this->db->select('gfast_mdu_unit_results.id as result_id, gfast_mdu_unit_results.date as result_date, gfast_mdu_unit_results.unit as result_unit, gfast_mdu_unit_results.results, gfast_mdu_unit.id as unit_id, gfast_mdu_unit.mdu as mdu, gfast_mdu_unit.bldg as bldg, gfast_mdu_unit.unit as unit, gfast_mdu_unit.ccu as ccu, gfast_mdu.*');
        $this->db->from('gfast_mdu_unit_results');
        $this->db->join('gfast_mdu_unit', 'gfast_mdu_unit.id = gfast_mdu_unit_results.unit');
        $this->db->join('gfast_mdu', 'gfast_mdu.id = gfast_mdu_unit.mdu');
        return $this->db->get();
    }




    public function saveUnitEtrValues($unit, $data)
    {
        $this->db->set('results', json_encode($data));
        $this->db->set('unit', $unit);
        if (!$this->db->insert('gfast_mdu_unit_results')) {
            $error = $this->db->error();
            return array('status' => false, 'msg' => $error);
        } else {
            if ($this->network->checkInternetConnection()) {
                $this->checkResultsForCloud();
            }
            return array('status' => true, 'msg' => 'Successfully saved results');
        }
    }

    public function getUnitsFromDatabase()
    {
        $this->db->select('*');
        $query = $this->db->get('gfast_mdu_unit');
        return $query->result();
    }

    public function checkResultsForCloud()
    {
        $results = $this->db->select('id')
            ->where('cloud', 0)
            ->get('gfast_mdu_unit_results');

        foreach ($results->result() as $result) {
            log_message('debug', json_encode($result));
            $full = $this->getEtrValuesFromDatabaseById($result->id);
            log_message('debug', json_encode($full->result_array()));
            if ($this->network->checkInternetConnection()) {
                foreach ($full->result() as $result) {
                    $this->saveGfastResultsToCloud($result);
                }
            }
        }
    }

    public function getEtrValuesFromDatabaseById($id)
    {
        $this->db->select('gfast_mdu_unit_results.date as result_date, gfast_mdu_unit_results.id as result_id, gfast_mdu_unit_results.unit as result_unit, gfast_mdu_unit_results.results, gfast_mdu_unit.id as unit_id, gfast_mdu_unit.mdu as mdu, gfast_mdu_unit.bldg as bldg, gfast_mdu_unit.unit as unit, gfast_mdu_unit.ccu as ccu, gfast_mdu_unit.distance_from_idf as distance, gfast_mdu_unit.utp as utp, gfast_mdu.*');
        $this->db->from('gfast_mdu_unit_results');
        $this->db->where('gfast_mdu_unit_results.id', $id);
        $this->db->join('gfast_mdu_unit', 'gfast_mdu_unit.id = gfast_mdu_unit_results.unit');
        $this->db->join('gfast_mdu', 'gfast_mdu.id = gfast_mdu_unit.mdu');
        return $this->db->get();
    }

    private function setResultsToCloud($id)
    {
        $this->db->set('cloud', 1)
            ->where('id', $id)
            ->update('gfast_mdu_unit_results');
    }

    /*
     * Send the gfast data to the pfi-cloud
     */
    private function saveGfastResultsToCloud(StdClass $data)
    {
        $curl = new \Curl\Curl();
        $curl->setUserAgent('PocketFi/0.0.1 (gfast)');
        $this->load->model('Ethernet_model', 'ethernet');
        $data->mac = $this->ethernet->getMacAddress();
        $data->uid = $this->session->userdata('uid') ? $this->session->userdata('uid') : 'system';
        $data->version = $this->version->currentTag;


        $curl->post($this->config->item('pfi_cloud_api_endpoint') . 'gfast/results', $data);
        
        if ($curl->error) {
            log_message('error', $curl->errorCode . ': ' . $curl->errorMessage);
        } else {
            $this->setResultsToCloud($data->result_id);
            log_message('debug', 'Sent new speedtest results to cloud');
        }
        $curl->close();
    }

    public function installGfastPackage()
    {
        exec("sudo python3 {$this->config->item('gfast_install_python')}");
    }

    /**
     * [isJSON description]
     *
     * @param string $string [description]
     *
     * @return boolean         [description]
     */
    public function isJSON($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}
