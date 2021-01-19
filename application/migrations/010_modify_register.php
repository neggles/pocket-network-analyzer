<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The plan for this migration is to make it so that each pfi is forced to register.
 * It will check everytime the network is connected to see if it has registered.
 * If it has registered then it will just return true.
 * On the next update we will ensure that this script is reversed so that
 * there are not the annoying system checks constantly running when a unit is plugged in and out.
 */

class Migration_Modify_register extends CI_Migration
{
    public function up()
    {
        $this->handleRegisterScript();
    }

    private function handleRegisterScript()
    {
        exec("sudo cp " . FCPATH . "/assets/updates/register.sh /etc/network/if-up.d/register");
        exec("sudo sh -c 'chmod +x /etc/network/if-up.d/register'");
    }

    /*
        Remove the register script from the system.
     */
    public function down()
    {
        if (file_exists("/etc/network/if-up.d/register")) {
            exec("sudo rm -f /etc/network/if-up.d/register");
        }
    }
}
