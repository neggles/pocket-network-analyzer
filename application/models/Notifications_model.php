<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notifications_model extends CI_Model
{
    private $key;
    private $app_id;
    private $secret;
    private $pusher;
    private $pusher_host;
    private $options;

    public function __construct()
    {
        parent::__construct();
        $this->key = $this->config->item('pusher_app_key');
        $this->secret = $this->config->item('pusher_secret');
        $this->app_id = $this->config->item('pusher_appid');
        $this->options = array(
            'debug'=>false,
            'host'=>$this->config->item('pusher_host'),
            'port'=> $this->config->item('pusher_port')
            );

        try {
            $this->pusher = new Pusher($this->key, $this->secret, $this->app_id, $this->options);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function sendNotification($job = null, $channel = null, $notificationKey = null, $message = null)
    {
        //$message = str_replace("-", " ", $message);
        //log_message('debug', $message);
        $this->pusher->trigger($channel, $notificationKey, array('message' => $message));
    }
}
