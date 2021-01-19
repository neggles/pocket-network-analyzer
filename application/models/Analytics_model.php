<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Analytics_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Network_model', 'network');
        $this->load->model('Ethernet_model', 'ethernet');
        // Load the config from the config file
        $this->config->load('analytics', true);
            
        // And assign it to $config
        $config = $this->config->item('analytics');
        $this->load->library('PiwikTracker', $config, 'tracker');
    }

    public function trackVisit($url, $title = 'Default Page Title')
    {
        $this->tracker->setUrl($url);
        if ($this->session->userdata('uid') !== null) {
            $this->tracker->setUserId($this->session->userdata('uid'));
        }
        //$this->tracker->setCustomTrackingParameter('macAddress', $this->ethernet->getMacAddress());
        $this->tracker->setCustomVariable(1, 'macAddress', $this->ethernet->getMacAddress());
        $this->load->model('Version_model', 'version');
        $this->tracker->setCustomVariable(2, 'pfiVersion', $this->version->getCurrentTag());
        
        // If there is a network connection track the page view and then send it.
        if ($this->network->checkInternetConnection()) {
            return $this->tracker->doTrackPageView($title);
            // There is no network connection, so enable bulk tracking
        } else {
            log_message('debug', 'Enabling bulk tracking');
            $this->tracker->enableBulkTracking();
            $this->tracker->doTrackPageView($title);
            $this->saveVisitOffline($this->tracker->storedTrackingActions);
            log_message('debug', 'No internet connection for feedback submission.');
        }
    }

    private function saveVisitOffline($tracker)
    {
        try {
            if (($existingData = $this->getStoredActions()) !== false) {
                $data1 = json_decode($existingData);
                $trackingData = array_merge($data1, $tracker);
                log_message('debug', 'Updated Tracking Data: ' . json_encode($trackingData));
            } else {
                $trackingData = $tracker;
            }

            $data = array(
            'id' => 1,
            'data' => json_encode($trackingData),
            'date' => date('Y-m-d H:i:s')
            );
            $this->db->update('analytics', $data);
        } catch (Exception $e) {
        }
    }

    private function getStoredActions()
    {
        $this->db->select('data');
        $return = $this->db->get('analytics');
        if (!empty($return->result_object()[0]->data)) {
            return $return->result_object()[0]->data;
        } else {
            return false;
        }
    }

    public function clearAnalyticsFromCache()
    {
        if ($this->network->checkInternetConnection()) {
            return $this->sendResultsToCloud();
        } else {
            return array('status' => false, 'msg'=> 'Unable to send analytics data to the cloud');
        }
    }

    private function sendResultsToCloud()
    {
        $this->tracker->enableBulkTracking();
        if (($actions = $this->getStoredActions()) !== false) {
            $this->tracker->storedTrackingActions = json_decode($actions);
            if ($this->tracker->doBulkTrack()) {
                $this->flushResults();
            } else {
                return false;
            }
        }
    }

    public function flushResults()
    {
        $data = array(
            'id' => 1,
            'data' => "",
            'date' => date('Y-m-d H:i:s')
            );
        $this->db->update('analytics', $data);
    }
}
