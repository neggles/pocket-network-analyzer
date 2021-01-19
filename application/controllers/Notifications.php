<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Notifications extends CI_Controller
{
    public $message;
    public $channel;
    public $notificationKey;
    private $key;
    private $app_id;
    private $secret;
    private $pusher;
    private $pusher_host;

    public function __construct()
    {
        parent::__construct();
        $this->key = $this->config->item('pusher_app_key');
        $this->secret = $this->config->item('pusher_secret');
        $this->app_id = $this->config->item('pusher_appid');
        $options = array(
            'debug'=>false,
            'host'=>$this->config->item('pusher_host'),
            'port'=> $this->config->item('pusher_port')
            );
        try {
            $this->pusher = new Pusher($this->key, $this->secret, $this->app_id, $options);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function index()
    {
        $this->channel = $this->uri->segment(2);
        $this->notificationKey = $this->uri->segment(3);
        $this->message = $this->uri->segment(4);
        $this->message = str_replace("-", " ", $this->message);
        log_message('debug', $this->message);
        $this->pusher->trigger($this->channel, $this->notificationKey, array('message' => $this->message));
    }

    public function test()
    {
        $info = $this->input->post_get();
        
        $this->pusher->trigger('wireless', 'error', array('message' => $info));
    }
}
