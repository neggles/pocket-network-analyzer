<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Issue_model extends CI_Model
{
    private $client;
    private $project;
    private $repository;
    private $force;
    private $initialized = false;
    public $projectDetails;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Network_model', 'network');
        $this->load->model('Notifications_model', 'notification');
        //$this->load->model('Update_model', 'update');
    }

    public function initialize()
    {
        log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        
        if ($this->network->checkInternetConnection()) {
            if (null === $this->client) {
                $this->client = new \Gitlab\Client($this->config->item('git_repo_api'));
                $this->client->authenticate($this->config->item('git_repo_token'), \Gitlab\Client::AUTH_URL_TOKEN);
            }
            $this->initialized = true;
        } else {
            log_message('debug', 'No internet connection for feedback submission.');
            $this->initialized = false;
        }
    }

    private function getProject()
    {
        log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        if (null !== $this->client) {
            $this->project = new \Gitlab\Model\Project($this->config->item('git_project_id'), $this->client);
            return true;
        } else {
            return false;
        }
    }

    public function createIssue($issue)
    {
        if (!$this->initialized) {
            $this->initialize();
        }
        if ($this->getProject()) {
            try {
                return $this->createNewIssue($issue);
            } catch (Exception $e) {
                log_message('error', $e->getMessage());
                $this->storeIssueUntilNetwork($issue);
            }
        } else {
            $this->storeIssueUntilNetwork($issue);
        }
    }

    private function storeIssueUntilNetwork($issue)
    {
        $issue['sent'] = 0;
        try {
            $this->db->insert('issues', $issue);
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }

    private function createNewIssue($issue)
    {
        $issue['description'] = $issue['description'] . '<br />' . $issue['name'] . ' <' . $issue['email'] . '>';
        $issueStatus = $this->project->createIssue(
            $issue['title'],
            array(
                'description' => $issue['description'],
                'assignee_id' => 16
            )
        );
        
        log_message('debug', "New issue: " . json_encode($issueStatus));
        
        if ($issueStatus) {
            $msg = array('status' => true, 'msg' => 'Successfully submitted the issue.');
        } else {
            $msg = array('status' => false, 'msg' => 'Error Submitting new issue.');
        }
        return json_encode($msg);
    }

    private function checkForUnsentFeedback()
    {
        $this->db->select('*');
        $this->db->where('sent', 0);
        $query = $this->db->get('issues');

        foreach ($query->result() as $result) {
            $data[] =  $result;
        }
        return json_encode($data);
    }
}
