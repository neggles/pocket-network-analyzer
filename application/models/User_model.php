<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    private static $db;
    protected $user;

    public function __construct($id = null)
    {
        parent::__construct();
        self::$db = &get_instance()->db;
        if ($this->session->userdata('uid') === null) {
            $this->checkForPersistentUser();
        }
    }

    public function createNewUser($uid)
    {
        $uid = trim(strtolower($uid));
        $exists = $this->doesUserExist($uid);
        // Job doesnt exist, keep going
        if (!$exists) {
            $data = array(
                'uid' => $uid,
                'date' => date("Y-m-d H:i:s")
            );
            $this->db->insert('users', $data);
            // Unable to create a new job
            if ($this->db->affected_rows() !== 1) {
                $msg = array(
                'status' => false,
                'msg' => 'Unable to create new user',
                'error' => $this->db->error()
                );
                return json_encode($msg);
            } else {
                $this->session->set_userdata('uid', $uid);
                $this->storeUserPersistent($uid);
                $msg = array(
                    'status' => true,
                    'msg' => 'Added New User',
                    'uid' => $uid
                    );
                return json_encode($msg);
            }
        } else {
            $this->session->set_userdata('uid', $uid);
            $this->storeUserPersistent($uid);
            $error = array(
                'status' => true,
                'msg' => 'User with that ID already exists, so that user id has been set.',
                'uid' => $uid
            );
            return json_encode($error);
        }
    }

    private function storeUserPersistent($uid)
    {
        if (file_exists($this->config->item('persistent_user_file'))) {
            $content = file_get_contents($this->config->item('persistent_user_file'));
            if ($this->jsonValidator($content)) {
                $json = json_decode($content);
                if ($json->user === $uid) {
                    log_message('debug', 'User value is the same, so not changing the file contents');
                } else {
                    $json->user = $uid;
                    $content = json_encode($json);
                    log_message('debug', 'New user id is not the same as the previous value, changing persistent user.');
                    return $this->writeFile($content);
                }
            } else {
                return false;
            }
        } else {
            $data = array('user' => $uid);
            $content = json_encode($data);
            return $this->writeFile($content);
        }
    }

    private function writeFile($data)
    {
        $this->load->helper('file');
        if (! write_file($this->config->item('persistent_user_file'), $data)) {
            log_message('error', 'Unable to write the persistent user file.');
            return false;
        } else {
            log_message('debug', 'Wrote the persistent user file.');
            return true;
        }
    }

    /**
     * Checking to see if the user has been written to the file system for persistence
     * @return bool true|false
     */
    private function checkForPersistentUser()
    {
        if (file_exists($this->config->item('persistent_user_file'))) {
            $content = file_get_contents($this->config->item('persistent_user_file'));
            if ($this->jsonValidator($content)) {
                $json = json_decode($content);
                //log_message('debug', json_encode($json));
                if (is_object($json) && !empty($json->user)) {
                    log_message('debug', 'Persistent User file exists and it checks out clean');
                    $this->session->set_userdata('uid', $json->user);
                }
            } else {
                log_message('error', 'Data in the persistent file is not valid json');
                return false;
            }
        } else {
            log_message('debug', 'No persistent user file');
            return false;
        }
    }

    // JSON Validator function
    private function jsonValidator($data = null)
    {
        if (!empty($data)) {
            @json_decode($data);

            return (json_last_error() === JSON_ERROR_NONE);
        }
        return false;
    }

    private function doesUserExist($uid)
    {
        $query = $this->db->select('*')
                ->where('uid', $uid)
                ->get('users');

        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }
}
