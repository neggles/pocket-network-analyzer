<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The plan for this migration is to make it so that each pfi is forced to register.
 * It will check everytime the network is connected to see if it has registered.
 * If it has registered then it will just return true.
 * On the next update we will ensure that this script is reversed so that
 * there are not the annoying system checks constantly running when a unit is plugged in and out.
 */

class Migration_Change_apachephpversion extends CI_Migration
{
    public function up()
    {
        if (!$this->checkVersion("php7.1")) {
            exec("sudo a2enmod php7.1");
            exec("sudo a2dismod php7.0");
            exec("sudo service apache2 restart");
        }
    }

    private function checkVersion($version)
    {
        if (file_exists("/etc/apache2/mods-enabled/{$version}")) {
            return true;
        }
        return false;
    }
    /*
        Remove the register script from the system.
     */
    public function down()
    {
        if (!$this->checkVersion("php7.0")) {
            exec("sudo a2enmod php7.0");
            exec("sudo a2dismod php7.1");
            exec("sudo service apache2 restart");
        }
    }
}
