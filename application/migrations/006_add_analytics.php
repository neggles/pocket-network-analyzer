<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_analytics extends CI_Migration
{
    public function up()
    {
        $this->analytics();
    }

    private function analytics()
    {
        $this->dbforge->add_field(
            array(
              'id' => array(
                 'type' => 'INTEGER'
              ),
              'data' => array(
                 'type' => 'text',
                 'null' => true
              ),
             'date' => array(
                 'type' => 'timestamp',
                 'null' => false,
                 'default' => 'CURRENT_TIMESTAMP'
              )
            )
        );

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('analytics');
        $this->createInitialRecord();
        $this->handleCreationOfAnalyticsNetworkScript();
    }

    private function handleCreationOfAnalyticsNetworkScript()
    {
        exec("sudo cp " . FCPATH . "/assets/updates/analytics.sh /etc/network/if-up.d/analytics");
        exec("sudo sh -c 'chmod +x /etc/network/if-up.d/analytics'");
    }

    private function removeAnalyticsNetworkScript()
    {
        if (file_exists("/etc/network/if-up.d/analytics")) {
            exec("sudo rm -f /etc/network/if-up.d/analytics");
        }
    }

    private function createInitialRecord()
    {
        $data = array(
            'id' => 1,
            'data' => "",
            'date' => date('Y-m-d H:i:s')
        );
        $this->db->insert('analytics', $data);
    }

    public function down()
    {
        try {
            $this->dbforge->drop_table('analytics');
            $this->removeAnalyticsNetworkScript();
        } catch (Exception $e) {
        }
    }
}
