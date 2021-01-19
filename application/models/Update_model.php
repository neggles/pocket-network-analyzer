<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Update_model extends CI_Model
{
    /**
     * Will store the file names and commands to
     * keep dependencies upto date
     * @var array()
     */
    private $updateFiles;

    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
        $this->updateFiles = array(
        'bower' => array(
            'file' => 'bower.json',
            'command' => 'bower install'
        ),
        'composer' => array(
            'file' => 'composer.lock',
            'command' => 'composer install --no-dev'
            ),
        'python' => array(
            'file' => 'requirements.txt',
            'command' => 'pip install -r requirements.txt'
            )
        );
        $this->load->model('Notifications_model', 'notification');
    }

    protected function sendNotification($status, $msg)
    {
        if (is_cli()) {
            echo $status . ' ' . json_encode($msg) . PHP_EOL;
        } else {
            $this->notification->sendNotification(
                $this->job,
                'update',
                $status,
                json_encode($msg)
            );
        }
    }

    /**
     * public interface to run the updates for the pfi
     * @return none
     */
    public function runUpdates()
    {
        $this->updateCommands();
    }

    /**
     * The main router interface to run the update commands.
     * @return [type] [description]
     */
    private function updateCommands()
    {
        if (!self::isWindows()) {
            try {
                try {
                    $this->runMigrations();
                } catch (Exception $e) {
                }
                $this->confirmLatestUpdates();
                return true;
            } catch (Exception $e) {
                if (is_cli()) {
                    echo "Unable to run updates: " . $e->getMessage() . PHP_EOL;
                }
                return true;
            }
        } else {
            if (is_cli()) {
                echo "Cannot run all update commands on windows machines." . PHP_EOL;
            }
            try {
                $this->runMigrations();
            } catch (Exception $e) {
            }
            $this->confirmLatestUpdates();
            return true;
        }
    }

    //JSON Validator function
    private function jsonValidator($data = null)
    {
        if (!empty($data)) {
            @json_decode($data);

            return (json_last_error() === JSON_ERROR_NONE);
        }

        return false;
    }
    
    private function checkChangedFiles()
    {
        // Check the diff-tree for the files that have been changed
        exec("git diff-tree -r  --name-only --no-commit-id ORIG_HEAD HEAD", $output);
        return $output;
    }

    private function runMigrations()
    {
        // load migration library
        $this->load->library('migration');

        if (! $this->migration->current()) {
            log_message('error', $this->migration->error_string());
            if (is_cli()) {
                echo 'Error' . $this->migration->error_string() . PHP_EOL;
            } else {
                $this->sendNotification('progress', array('status' => false, 'msg'=> $this->migration->error_string()));
            }
        } else {
            log_message('debug', 'Migrations ran successfully');
            if (is_cli()) {
                echo 'Migrations ran successfully!' . PHP_EOL;
            } else {
                $msg = array('status' => true,'msg' => 'Migrations ran successfully.');
                echo json_encode($msg);
            }
           
            $this->sendNotification('progress', array('msg'=>'Successfully ran update migrations'));
        }
    }

    private function confirmLatestUpdates()
    {
        $search = $this->checkChangedFiles();
        try {
            foreach ($this->updateFiles as $file) {
                try {
                    if (array_search($file['file'], $search)) {
                        if (is_cli()) {
                            echo 'Found ' . $file['file'] . ' was changed.' . PHP_EOL;
                            echo 'Will run command: ' . $file['command'] . PHP_EOL;
                        }
                        $this->runCommand($file['command']);
                    }
                } catch (Exception $e) {
                    $msg = array('status' => false,'msg' => $e->getMessage());
                    $this->sendNotification('progress', array('msg'=>$e->getMessage()));
                    log_message('error', $e->getMessage());
                    echo json_encode($msg);
                }
            }
        } catch (Exception $e) {
            $msg = array('status' => false,'msg' => $e->getMessage());
            $this->sendNotification('progress', array('msg'=>$e->getMessage()));
            
            echo json_encode($msg);
        }
    }

    private function runCommand($command)
    {
        if (self::isWindows()) {
            exec($command);
        } else {
            if (!is_cli()) {
                exec("sudo " . $command);
            } else {
                passthru("sudo " . $command);
            }
        }
    }

    /**
     * @return bool Whether the host machine is running a Windows OS
     */
    private static function isWindows()
    {
        return defined('PHP_WINDOWS_VERSION_BUILD');
    }
}
