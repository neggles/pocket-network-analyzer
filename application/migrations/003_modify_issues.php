<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Modify_issues extends CI_Migration
{
    public function up()
    {
        $this->issues();
    }

    private function issues()
    {
        $this->dbforge->add_column('issues', array('sent' => array('type'=>'INTEGER')));
    }

    public function down()
    {
        try {
            $this->dbforge->drop_column('issues', 'sent');
        } catch (Exception $e) {
        }
    }
}
