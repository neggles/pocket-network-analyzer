<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Update_sanitycheck_script extends CI_Migration
{
    public function up()
    {
        $this->removeScript();
        $this->addSanityCheckScript();
    }

    private function removeScript()
    {
        if (file_exists(FCPATH . "/sanitycheck.sh")) {
            exec("sudo rm -f " . FCPATH . "/sanitycheck.sh");
        }
    }

    private function addSanityCheckScript()
    {
        exec("cp " . FCPATH . "/assets/updates/sanitycheck.sh " . FCPATH . "/sanitycheck.sh");
        exec("sh -c 'chmod +x " . FCPATH . "/sanitycheck.sh'");
    }

    private function returnToOriginalSanityCheck()
    {
        exec("cp " . FCPATH . "/assets/updates/sanitycheck_og.sh " . FCPATH . "/sanitycheck.sh");
        exec("sh -c 'chmod +x " . FCPATH . "/sanitycheck.sh'");
    }

    public function down()
    {
        try {
            $this->removeScript();
            $this->returnToOriginalSanityCheck();
            return true;
        } catch (Exception $e) {
        }
    }
}
