<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_issues extends CI_Migration
{
    public function up()
    {
        $this->issues();
    }

    private function issues()
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
              'email' => array(
                 'type' => 'varchar',
                 'constraint' => '255',
                 'null' => true
              ),
              'title' => array(
                 'type' => 'varchar',
                 'constraint' => '1000',
                 'null' => false,
                 'default' => 'No Comments'
              ),
              'description' => array(
                 'type' => 'text',
                 'null' => false
              )
            )
        );

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('issues');
    }

    public function down()
    {
        try {
            $this->dbforge->drop_table('issues');
        } catch (Exception $e) {
        }
    }
}
