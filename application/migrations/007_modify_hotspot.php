<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Modify_hotspot extends CI_Migration
{
    public function up()
    {
        $this->hotspot();
    }

    private function hotspot()
    {
        $this->handleUpdateOfHotspotScript();
    }

    private function handleUpdateOfHotspotScript()
    {
        exec("sudo mv /etc/hostapd/ifupdown.sh /etc/hostapd/ifupdown.sh.bak");
        exec("sudo sh -c 'chmod -x /etc/hostapd/ifupdown.sh.bak'");
        exec("sudo cp " . FCPATH . "/assets/updates/hostapd.sh /etc/hostapd/ifupdown.sh");
        exec("sudo sh -c 'chmod +x /etc/hostapd/ifupdown.sh'");
    }

    private function revertHotspotScript()
    {
        if (file_exists("/etc/hostapd/ifupdown.sh.bak")) {
            exec("sudo rm -f /etc/hostapd/ifupdown.sh");
            exec("sudo mv /etc/hostapd/ifupdown.sh.bak /etc/hostapd/ifupdown.sh");
            exec("sudo sh -c 'chmod +x /etc/hostapd/ifupdown.sh'");
        }
    }

    public function down()
    {
        try {
            $this->revertHotspotScript();
        } catch (Exception $e) {
        }
    }
}
