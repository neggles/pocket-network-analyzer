<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Modify_Gfast_cloud extends CI_Migration
{
    /**
     * [up description]
     *
     * @return [type] [description]
     */
    public function up()
    {
        $this->results();
    }

    /**
     * [complex description]
     *
     * @return [type] [description]
     */
    private function results()
    {
        $fields = array(
                'cloud' => array(
                    'type' => 'INT',
                    'default' => 0
                )
        );
        try {
            $this->dbforge->add_column('gfast_mdu_unit_results', $fields);
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }

    /**
     * [down description]
     *
     * @return [type] [description]
     */
    public function down()
    {
        try {
            $this->dbforge->drop_column('gfast_mdu_unit_results', 'cloud');
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
}
