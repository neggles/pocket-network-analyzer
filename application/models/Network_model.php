<?php
class Network_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function checkInternetConnection($sCheckHost = 'www.google.com')
    {
        if (!$sock = @fsockopen($sCheckHost, 80, $errorNumber, $errorMessage)) {
            //log_message('debug', 'Error Number: ' . $errorNumber . ' Error String: ' . $errorMessage);
            return false;
        } else {
            return true;
        }
    }
}
