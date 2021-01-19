<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settings_model extends CI_Model
{
    private static $db;
    public $key;
    public $value;

    public function __construct($id = null)
    {
        parent::__construct();
        self::$db = &get_instance()->db;
    }

    public function get($key)
    {
        $query = $this->db->select()
                ->where('key', $key)
                ->get('settings');

        foreach ($query->result() as $row) {
            return $row->value;
        }
    }

    public function set($key, $value)
    {
        $data = array(
                'key' => $key,
                'value' => $value
        );
        $this->db->set('value', $value);
        $this->db->where('key', $key);
        $this->db->update('settings');
    }
    
    public function updateSetting($key, $value)
    {
    }

    public function getDynamicSsid()
    {
        if (file_exists($this->config->item('ssid_conf_file'))) {
            $dynamicSsid = json_decode(file_get_contents($this->config->item('ssid_conf_file')));
            if (is_object($dynamicSsid)) {
                $ssid = $dynamicSsid->ssid;
            } else {
                $ssid= "POCKET-FI";
            }
        } else {
            $ssid = "POCKET-FI";
        }
        return $ssid;
    }
}
