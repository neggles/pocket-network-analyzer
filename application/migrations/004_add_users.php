<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_users extends CI_Migration
{
    public function up()
    {
        $this->users();
    }

    private function users()
    {
        $this->dbforge->add_field(
            array(
              'id' => array(
                 'type' => 'INTEGER'
              ),
              'uid' => array(
                 'type' => 'varchar',
                 'constraint' => '24',
                 'null' => true
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
        $this->dbforge->create_table('users');
    }

    public function down()
    {
        try {
            $this->dbforge->drop_table('users');
        } catch (Exception $e) {
        }
    }
}
