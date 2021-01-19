<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_gfast extends CI_Migration
{
    /**
     * [up description]
     *
     * @return [type] [description]
     */
    public function up()
    {
        $this->complex();
        $this->unit();
    }

    /**
     * [complex description]
     *
     * @return [type] [description]
     */
    private function complex()
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
              'name' => array(
                 'type' => 'varchar',
                 'constraint' => '255',
                 'null' => true
              ),
              'num_floors' => array(
                 'type' => 'integer',
                 'default' => 1
              ),
              'street' => array(
                 'type' => 'varchar',
                 'constraint' => '255',
                 'null' => true
              ),
              'city' => array(
                'type' => 'varchar',
                'constraint' => '255',
                'null' => false
              ),
              'state' => array(
                 'type' => 'varchar',
                 'constraint' => '20',
                 'null' => true
              ),
              'zip' => array(
                 'type' => 'varchar',
                 'constraint' => '10',
                 'null' => true
              ),
              'con_date' => array(
                 'type' => 'varchar',
                 'constraint' => '50',
                 'null' => true
              ),
            )
        );

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('gfast_mdu');
    }

    /**
     * [unit description]
     *
     * @return [type] [description]
     */
    private function unit()
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
              'mdu' => array(
                'type' => 'INTEGER'
              ),
              'bldg' => array(
                 'type' => 'varchar',
                 'constraint' => '255',
                 'null' => true
              ),
              'unit' => array(
                 'type' => 'varchar',
                 'constraint' => '255',
                 'null' => true
              ),
              'ccu' => array(
                 'type' => 'varchar',
                 'constraint' => '20',
                 'null' => true
              ),
              'distance_from_idf' => array(
                 'type' => 'varchar',
                 'constraint' => '10',
                 'null' => true
              ),
              'utp' => array(
                 'type' => 'varchar',
                 'constraint' => '50',
                 'null' => true
              ),
              'data' => array(
                 'type' => 'text',
                 'null' => true
              ),

            )
        );

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('gfast_mdu_unit');
    }

    /**
     * [down description]
     *
     * @return [type] [description]
     */
    public function down()
    {
        try {
            $this->dbforge->drop_table('gfast_mdu');
            $this->dbforge->drop_table('gfast_mdu_unit');
        } catch (Exception $e) {
        }
    }
}
