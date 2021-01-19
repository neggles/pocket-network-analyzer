<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_settings extends CI_Migration
{
    public function up()
    {
        $this->settings();
        $this->job();
        $this->location();
        $this->speedtest();
        $this->service();
        $this->updates();
        $this->networkscan();
        $this->user();
        $this->versions();
    }

    private function settings()
    {
        $this->dbforge->add_field(
            array(
              'key' => array(
                 'type' => 'varchar',
                 'constraint' => '60',
                 'null' => false,
                 'unique' => true
              ),
              'value' => array(
                 'type' => 'text',
              ),
            )
        );

        $this->dbforge->create_table('settings');

        $this->db->query("INSERT INTO `settings` (key,value) VALUES ('wireless_minimum_extender','65');");
        $this->db->query('INSERT INTO `settings` (key,value) VALUES (\'wireless_thresholds\',\'{"high":{"value":"-64","color":"#1ab394","name":"Great Quality"},"medium":{"value":"-75","color":"#f8ac59","name":"Medium Quality"},"low":{"value":"-80","color":"#ed5565","name":"Low Quality"}}\');');
    }

    private function job()
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
                 'null' => false
              ),
              'comments' => array(
                 'type' => 'varchar',
                 'constraint' => '1000',
                 'null' => false,
                 'default' => 'No Comments'
              ),
              'phone' => array(
                 'type' => 'varchar',
                 'constraint' => '20',
                 'null' => true,
                 'default' => 'install'
              ),
              'ban' => array(
                 'type' => 'int',
                 'constraint' => '15',
                 'null' => true
              )
            )
        );

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('job');
    }
    private function speedtest()
    {
        $this->dbforge->add_field(
            array(
              'id' => array(
                 'type' => 'INTEGER'
              ),
              'name' => array(
                 'type' => 'VARCHAR',
                 'constraint' => '255',
                 'null' => true
              ),
              'date' => array(
                 'type' => 'datetime',
                 'null' => false
              ),
              'upload' => array(
                 'type' => 'text',
                 'null' => false
              ),
              'download' => array(
                 'type' => 'text',
                 'null' => false
              ),
              'location' => array(
                 'type' => 'varchar',
                 'constraint' => '255',
                 'default' => null
              ),
              'connection' => array(
                 'type' => 'varchar',
                 'constraint' => '255',
                 'null' => false
              ),
              'ssid' => array(
                 'type' => 'varchar',
                 'constraint' => '255',
                 'default' => null
              ),
              'comments' => array(
                 'type' => 'varchar',
                 'constraint' => '1000',
                 'null' => false,
                 'default' => 'No Comments'
              ),
              'job' => array(
                 'type' => 'INT',
                 'constraint' => '11',
                 'null' => false
              ),
            )
        );

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('speedtest');
    }

    private function updates()
    {
        $this->dbforge->add_field(
            array(
              'id' => array(
                 'type' => 'INTEGER'
              ),
              'content' => array(
                 'type' => 'TEXT',
                 'null' => false
              ),
              'tag' => array(
                 'type' => 'VARCHAR',
                 'constraint' => '150',
                 'null' => false,
                 'unique' => true
              )
            )
        );

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('updates');
    }

    private function location()
    {
        $this->dbforge->add_field(
            array(
              'id' => array(
                 'type' => 'INTEGER'
              ),
              'address' => array(
                 'type' => 'text',
                 'null' => false
              ),
              'home_type' => array(
                 'type' => 'varchar',
                 'constraint' => '255',
                 'null' => false,
                 'default' => 'single_story_home'
              ),
              'floors' => array(
                 'type' => 'int',
                 'constraint' => '11',
                 'null' => false,
                 'default' => '1'
              ),
              'dispatch_type' => array(
                 'type' => 'varchar',
                 'constraint' => '45',
                 'null' => false,
                 'default' => 'install'
              ),
              'floorplan' => array(
                 'type' => 'text'
              ),
              'job' => array(
                 'type' => 'int',
                 'constraint' => '11',
                 'null' => false
              ),
              'default_network' => array(
                 'type' => 'mediumtext',
              ),
              'last_update' => array(
                 'type' => 'datetime',
                 'null' => false
              ),
            )
        );

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('location');
    }

    private function service()
    {
        $this->dbforge->add_field(
            array(
              'id' => array(
                 'type' => 'INTEGER'
              ),
              'location' => array(
                 'type' => 'int',
                 'constraint' => '11',
                 'null' => false
              ),
              'dispatch_type' => array(
                 'type' => 'tinytext',
                 'null' => false
              ),
              'notes' => array(
                 'type' => 'text',
              ),
              'date' => array(
                 'type' => 'datetime',
                 'null' => false
              ),
            )
        );

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('service');
    }

    private function networkscan()
    {
        $this->dbforge->add_field(
            array(
              'id' => array(
                 'type' => 'INTEGER'
              ),
              'job' => array(
                 'type' => 'int',
                 'constraint' => '11',
                 'null' => false
              ),
              'data' => array(
                 'type' => 'mediumtext',
                 'null' => false
              ),
              'date' => array(
                 'type' => 'datetime',
                 'null' => false
              ),
            )
        );

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('networkscan');
    }
    private function versions()
    {
        $this->dbforge->add_field(
            array(
              'id' => array(
                 'type' => 'INTEGER'
              ),
              'tag' => array(
                 'type' => 'VARCHAR',
                 'constraint' => '45',
              ),
              'content' => array(
                 'type' => 'TEXT',
                 'null' => false,
              ),
              'current' => array(
                 'type' => 'INT(2)',
                 'null' => false,
                 'default'=> '0'
              ),
            )
        );

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('versions');
    }
    private function user()
    {
        $this->dbforge->add_field(
            array(
              'id' => array(
                 'type' => 'INTEGER'
              ),
              'first_name' => array(
                 'type' => 'VARCHAR',
                 'constraint' => '255',
                 'null' => false
              ),
              'last_name' => array(
                 'type' => 'VARCHAR',
                 'constraint' => '255',
                 'null' => false
              ),
              'email' => array(
                 'type' => 'VARCHAR',
                 'constraint' => '255',
                 'null' => false
              ),
            )
        );

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('user');
    }
    public function down()
    {
        //$this->dbforge->drop_table('settings');
        //$this->dbforge->drop_table('job');
    }
}
