<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_Gfast_results extends CI_Migration
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
        $this->dbforge->add_field(
            array(
              'id' => array(
                 'type' => 'INTEGER'
              ),
              'date' => array(
                 'type' => 'timestamp',
                 'null' => false,
                 'default' => 'CURRENT_TIMESTAMP'
              ),
              'unit' => array(
                 'type' => 'INTEGER'
              ),
              'results' => array(
                 'type' => 'text',
                 'null' => true
              )
            )
        );
        try {
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('gfast_mdu_unit_results');
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
            $this->dbforge->drop_table('gfast_mdu_unit_results');
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }
}
