<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Modify_gfast_cloud_script extends CI_Migration
{
    public function up()
    {
        $this->removeGfastScript();
        $this->removeGfastDownScript();
        $this->addNewGfastDownScript();
        $this->addGfastShellScript();
    }

    private function addGfastShellScript()
    {
        exec("sudo cp " . FCPATH . "/assets/updates/gfast_2.sh /etc/network/if-up.d/gfast");
        exec("sudo sh -c 'chmod +x /etc/network/if-up.d/gfast'");
    }

    private function removeGfastScript()
    {
        if (file_exists("/etc/network/if-up.d/gfast")) {
            exec("sudo rm -f /etc/network/if-up.d/gfast");
        }
    }

    private function addNewGfastDownScript()
    {
        exec("sudo cp " . FCPATH . "/assets/updates/gfast_down.sh /etc/network/if-down.d/gfast");
        exec("sudo sh -c 'chmod +x /etc/network/if-down.d/gfast'");
    }

    private function removeGfastDownScript()
    {
        if (file_exists("/etc/network/if-down.d/gfast")) {
            exec("sudo rm -f /etc/network/if-down.d/gfast");
        }
    }

    private function returnGfastDownScript()
    {
        $this->removeGfastDownScript();
        exec("sudo cp " . FCPATH . "/assets/updates/gfast_down_og.sh /etc/network/if-down.d/gfast");
        exec("sudo sh -c 'chmod +x /etc/network/if-down.d/gfast'");
    }

    public function down()
    {
        try {
            $this->removeGfastScript();
            $this->returnGfastDownScript();
            return true;
        } catch (Exception $e) {
        }
    }
}
