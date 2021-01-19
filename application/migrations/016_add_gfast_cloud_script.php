<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_gfast_cloud_script extends CI_Migration
{
    public function up()
    {
        $this->addGfastShellScript();
    }

    private function addGfastShellScript()
    {
        exec("sudo cp " . FCPATH . "/assets/updates/gfast.sh /etc/network/if-up.d/gfast");
        exec("sudo sh -c 'chmod +x /etc/network/if-up.d/gfast'");
    }

    private function removeGfastScript()
    {
        if (file_exists("/etc/network/if-up.d/gfast")) {
            exec("sudo rm -f /etc/network/if-up.d/gfast");
        }
    }

    public function down()
    {
        try {
            $this->removeGfastScript();
            return true;
        } catch (Exception $e) {
        }
    }
}
