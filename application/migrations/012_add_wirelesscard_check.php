<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The plan for this migration is to make it so that each pfi is forced to register.
 * It will check everytime the network is connected to see if it has registered.
 * If it has registered then it will just return true.
 * On the next update we will ensure that this script is reversed so that
 * there are not the annoying system checks constantly running when a unit is plugged in and out.
 */

class Migration_Add_wirelesscard_check extends CI_Migration
{
    // Add check on initial update, it will be completed on later checks through the register
    // hardware functionality
    public function up()
    {
        $this->load->model('Wireless_model', 'wireless');
        try {
            if (method_exists($this->wireless, 'checkWirelessCardStatus')) {
                $this->wireless->checkWirelessCardStatus();
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
    /*
        Remove the register script from the system.
     */
    public function down()
    {
        return true;
    }
}
