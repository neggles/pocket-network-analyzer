<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Modify_analytics extends CI_Migration
{
    public function up()
    {
        $this->analytics();
    }

    private function analytics()
    {
        $this->handleModificationOfAnalyticsNetworkScript();
        $this->handleNewHotspotScript();
    }

    private function handleModificationOfAnalyticsNetworkScript()
    {
        exec("sudo cp " . FCPATH . "/assets/updates/analytics.sh /etc/network/if-up.d/analytics");
        exec("sudo sh -c 'chmod +x /etc/network/if-up.d/analytics'");
    }

    private function handleNewHotspotScript()
    {
        exec("sudo rm -f /etc/hostapd/ifupdown.sh");
        exec("sudo cp " . FCPATH . "/assets/updates/hostapd.sh /etc/hostapd/ifupdown.sh");
        exec("sudo sh -c 'chmod +x /etc/hostapd/ifupdown.sh'");
    }

    /*
    Since this is a patch of migration 6 where the script sets the wrong permissions, we do not want to revert it.
     */
    public function down()
    {
        return true;
    }
}
