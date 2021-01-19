<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_ux extends CI_Migration
{
    public function up()
    {
        $this->ux();
    }

    private function ux()
    {
        if (is_file(FCPATH . '/assets/ux/package.json')) {
            if (is_cli()) {
                passthru("npm install " . FCPATH . "/assets/ux --only=production --prefix=" . FCPATH . "/assets/ux");
            } else {
                log_message('debug', 'Installing ux program via npm');
                exec("npm install " . FCPATH . "/assets/ux --only=production --prefix=" . FCPATH . "/assets/ux");
            }
        }
    }

    public function down()
    {
        try {
            log_message('debug', 'Removing node_modules folder from ux program');
            if (is_dir(FCPATH . '/assets/ux/node_modules')) {
                exec("rm -rf " . FCPATH . "/assets/ux/node_modules");
            }
        } catch (Exception $e) {
        }
    }
}
