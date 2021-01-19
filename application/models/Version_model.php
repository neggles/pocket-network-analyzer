<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Composer\Semver\Comparator;

class Version_model extends CI_Model
{
    private $client;
    private $tags;
    private $project;
    private $repository;
    private $highestVersion;
    private $force;
    private $initialized = false;
    public $currentTag;
    public $projectDetails;


    public function __construct()
    {
        parent::__construct();
        $this->load->model('Network_model', 'network');
        $this->getCurrentTag();
        $this->load->model('Notifications_model', 'notification');
        $this->load->model('Update_model', 'update');
    }

    /**
     * Initialize the update model
     * @return boolean just return true
     */
    public function initialize()
    {
        if ($this->network->checkInternetConnection()) {
            if (null === $this->client) {
                $this->client = new \Gitlab\Client($this->config->item('git_repo_api'));
                $this->client->authenticate($this->config->item('git_repo_token'), \Gitlab\Client::AUTH_URL_TOKEN);
            }
        } else {
            log_message('debug', 'No internet connection for version check.');
        }
        $this->initialized = true;
        return true;
    }

    /**
     * Check if updates are available
     * @param  boolean $force Should we force the update check
     * @return boolean
     */
    public function areUpdatesAvailable($force = false)
    {
        $this->force = $force;
        if ($this->network->checkInternetConnection()) {
            if (is_array($tags = $this->getTags())) {
                foreach ($tags as $tag) {
                    if (! is_null($tag['release'])) {
                        if (Comparator::greaterThan($tag['name'], $this->currentTag)) {
                            if (null !== $this->highestVersion) {
                                if (Comparator::greaterThan($tag['name'], $this->highestVersion)) {
                                    $this->highestVersion = $tag['name'];
                                    $this->needsUpdated();
                                    $this->fetchAllTags();
                                }
                            } else {
                                $this->highestVersion = $tag['name'];
                                $this->needsUpdated();
                                $this->fetchAllTags();
                                return;
                            }
                        }
                    }
                }
            } else {
                log_message('debug', 'tags is not an array');
            }
            $this->fetchAllTags();
        } else {
            return false;
        }
    }

    private function needsUpdated()
    {
        if ($this->force) {
            $this->sendNotification('needsupdate_force', array('msg'=>'Update is available','version'=> $this->highestVersion));
        } else {
            $this->sendNotification('needsupdate', array('msg'=>'Update is available','version'=> $this->highestVersion));
        }
        if ($this->config->item('auto_update')) {
            $this->getCurrentTagUpdate();
        }
    }

    private function upToDate()
    {
        if ($this->force) {
            $this->sendNotification('uptodate_force', array('msg'=>'POCKET-FI is up to date','version'=> $this->highestVersion));
        } else {
            $this->sendNotification('uptodate', array('msg'=>'POCKET-FI is up to date','version'=> $this->highestVersion));
        }
    }


    public function getLatestVersion()
    {
        // if the highest version has already been set
        // save time and return it immediately
        if (null !== $this->highestVersion) {
            return $this->highestVersion;
        } else {
            // check for latest updates
            $this->areUpdatesAvailable();
            return isset($this->highestVersion) ? $this->highestVersion : $this->currentTag;
        }
    }

    public function currentTagDetails()
    {
        if (null !== $tagInfo = $this->getTagInformation()) {
            return json_encode($tagInfo, JSON_PRETTY_PRINT);
        }

        if (is_array($tags = $this->getTags())) {
            foreach ($tags as $tag) {
                if ($tag["name"] = $this->currentTag) {
                    return json_encode($tag, JSON_PRETTY_PRINT);
                }
            }
        } else {
            if ($this->tags->name = $this->currentTag) {
                return json_encode($this->tags, JSON_PRETTY_PRINT);
            }
        }
    }

    private function getUpdatedTagReference()
    {
        $this->sendNotification('progress', array('msg'=>'Checking for the latest version.','version'=> $this->highestVersion));
        if (!is_cli()) {
            exec("git --git-dir=" . FCPATH . "/.git fetch --tags");
        } else {
            passthru("git --git-dir=" . FCPATH . "/.git fetch --tags");
        }
    }

    private function sendUpdateInformationToCloud($oldVersion, $newVersion)
    {
        $curl = new \Curl\Curl();
        $this->load->model('Ethernet_model', 'ethernet');
        $curl->post($this->config->item('pfi_cloud_api_endpoint') . 'update/results', array(
            'version' => $newVersion,
            'old_version' => $oldVersion,
            'uid' => $this->session->userdata('uid') ? $this->session->userdata('uid') : 'system',
            'mac' => $this->ethernet->getMacAddress()
        ));
        
        if ($curl->error) {
            log_message('error', $curl->errorCode . ': ' . $curl->errorMessage);
        }
        $curl->close();
    }

    public function getCurrentTagUpdate()
    {
        $this->getLatestVersion();
        $currentTag = $this->currentTag;
        if ($this->network->checkInternetConnection()) {
            if (Comparator::greaterThan($this->highestVersion, $this->currentTag)) {
                $this->discardChanges();
                $this->getUpdatedTagReference();
                $this->updateFileSystemPermissions();
                $this->sendNotification('progress', array('msg'=>'Processing new update','version'=> $this->highestVersion));

                // Force checkout the latest tags
                if ($this->checkoutVersion($this->highestVersion)) {
                    log_message('debug', '!!!!!!!!!!!!!!!!!!CHECKOUT SHOULD BE COMPLETE NOW!!!!!!!!!!!!!!!!!');
                    $this->sendNotification('complete', array('msg'=>'POCKET-FI update is complete', 'version'=> $this->highestVersion));
                }
                // Send old tag and latest tag for tracking
                $this->sendUpdateInformationToCloud($currentTag, $this->highestVersion);
                //log_message('debug', 'Running updates from the [' . __FUNCTION__ . '] function the latest updates should already be pulled in.');
                // Run the update model
                $this->update->runUpdates();
            } else {
                //log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__ . 'POCKET-FI is already the newest version.');
                if (is_cli()) {
                    echo "POCKET-FI is already the newest version." . PHP_EOL;
                }
                $this->sendNotification('progress', array('msg'=>'POCKET-FI is already the newest version', 'version'=> $this->highestVersion));
            }
        } else {
            return false;
        }
    }

    private function writeLatestVersionToFile()
    {
        $version = $this->getCurrentTag();
        $array = array('version' => $version, 'date' => "{date()}");
        write_file($this->config->item('old_tag_version'), json_encode($array));
    }

    private function writeNewestVersionToFile($version)
    {
        $array = array('version' => $version, 'date' => "{date()}");
        write_file($this->config->item('new_tag_version'), json_encode($array));
    }

    private function checkoutVersion($version)
    {
        $this->writeLatestVersionToFile();
        exec("git --git-dir=" . FCPATH . "/.git -C " . FCPATH . " checkout -f {$version}", $output, $status);
        
        if (0 !== $status) {
            log_message('error', "Issue completing checkout of new tag [{$status}]: " . json_encode($output));
            return false;
        } else {
            log_message('debug', 'Completed checkout of new tag');
            return true;
        }
        $this->writeNewestVersionToFile($version);
    }

    public function checkoutLatestDevelopment()
    {
        if ($this->config->item('branch') === 'development') {
            $this->discardChanges();
            $this->updateFileSystemPermissions();
            $this->sendNotification('progress', array('msg' => 'Processing new update'  ,'version'=> $this->highestVersion));
            exec("git --git-dir=" . FCPATH . "/.git -C " . FCPATH . " checkout development", $output, $status);
            exec("git --git-dir=" . FCPATH . "/.git -C " . FCPATH . " pull", $output, $status);
            // Run the update model
            $this->update->runUpdates();
        }
    }

    private function updateFileSystemPermissions()
    {
        //log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        exec("sudo chown -R pfi:pfi /home/pfi/public_html");
    }

    public function getCurrentTag()
    {
        $this->currentTag = exec("git --git-dir=" . FCPATH . "/.git describe --abbrev=0 --tags");
        return $this->currentTag;
    }

    /**
     * Get the revision number / commit number for the current code
     * @return [type] [description]
     */
    public function getCurrentRevision()
    {
        $revision = exec("git --git-dir=" . FCPATH . "/.git rev-parse HEAD");
        return $revision;
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

    public function getClient()
    {
        return $this->client;
    }

    /**
     * Call the Gitlab api and return the project
     * @return [type] [description]
     */
    private function getProject()
    {
        //log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        if (null !== $this->client) {
            $this->project = new \Gitlab\Model\Project($this->config->item('git_project_id'), $this->client);
        }
    }

    public function getTags()
    {
        $useableTags = array();
        if (!$this->initialized) {
            $this->initialize();
        }
        //log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        if (! $this->tags = $this->cache->get('version_tags')) {
            if (null == $this->tags) {
                if ($this->network->checkInternetConnection()) {
                    try {
                        $this->tags = $this->client->api('repo')->tags($this->config->item('git_project_id'));
                    } catch (Exception $e) {
                        return json_encode(array('status'=> false,'msg'=>'Unable to fetch tags'));
                    }
                    
                    foreach ($this->tags as $tag) {
                        // We are only looking for tags that are releases.
                        // so check to make sure the release object is not null
                        if (! is_null($tag['release'])) {
                            if (Comparator::lessThan($tag['release']['tag_name'], $this->currentTag)) {
                                unset($tag);
                            } else {
                                array_push($useableTags, $tag);
                            }
                        } else {
                            unset($tag);
                        }
                    }

                    $this->tags = $useableTags;
                    $this->saveTagInformation();

                    return $useableTags;
                    //return $this->tags;
                } else {
                    $this->tags = $this->getTagInformation();
                    return $this->tags;
                }
            } else {
                return $this->tags;
            }
        }
        return $this->tags;
    }

    public function getRepo()
    {
        //log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        if (!$this->initialized) {
            $this->initialize();
        }
        try {
            return $this->client->api('projects')->show($this->config->item('git_project_id'));
        } catch (Exception $e) {
        }
    }

    private function saveTagInformation()
    {
        //log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        $tag_name = '';
        if ($this->network->checkInternetConnection()) {
            $this->cache->save('version_tags', $this->tags);
        }
        // Loop through each tag that is retrieved
        foreach ($this->tags as $tag) {
            // We are only looking for tags that are releases.
            // so check to make sure the release object is not null
            if (! is_null($tag['release'])) {
                $tag_name = $tag['release']['tag_name'];
                
                if (!$this->doesTagExist($tag_name)) {
                    if (Comparator::greaterThanOrEqualTo($tag_name, $this->currentTag)) {
                        $data = json_encode($tag);
                        $content = array(
                            'tag' => $tag_name,
                            'content' => $data
                        );
                        $this->db->insert('updates', $content);
                    }
                }
            }
        }
        $this->cleanTagInformation();
    }


    private function cleanTagInformation()
    {
        //log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        $query = $this->db->select('*')->get('updates');

        foreach ($query->result() as $result) {
            if (Comparator::lessThan($result->tag, $this->currentTag)) {
                $this->db->where('tag', $result->tag);
                $query = $this->db->delete('updates');
            }
        }
    }

    private function getTagInformation()
    {
        //log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        $query = $this->db->select('*')
                ->where('tag', $this->currentTag)
                ->get('updates');

        foreach ($query->result() as $result) {
            return json_decode($result->content);
        }
    }

    private function doesTagExist($tag)
    {
        //log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        $this->db->where('tag', $tag);
        $query = $this->db->get('updates');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getProjectDetails()
    {
        //log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        $this->getProject();
        return $this->project;
    }

    protected function discardChanges()
    {
        //log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        $output = array();
        if (!is_cli()) {
            exec("git --git-dir=" . FCPATH . "/.git reset --hard", $output, $status);
        } else {
            passthru("git --git-dir=" . FCPATH . "/.git reset --hard", $status);
        }

        if (0 !== $status) {
            log_message('error', 'Error trying to reset --hard repo: ' . json_encode($output));
            return;
        }

        $this->hasDiscardedChanges = true;
    }

    private function fetchAllTags()
    {
        //log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        if (!is_cli()) {
            exec("git --git-dir=" . FCPATH . "/.git fetch --all --tags --prune");
        } else {
            passthru("git --git-dir=" . FCPATH . "/.git fetch --all --tags --prune");
        }
    }

    protected function normalizePath($path)
    {
        //log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        if (self::isWindows() && strlen($path) > 0) {
            $basePath = $path;
            $removed = array();

            while (!is_dir($basePath) && $basePath !== '\\') {
                array_unshift($removed, basename($basePath));
                $basePath = dirname($basePath);
            }

            if ($basePath === '\\') {
                return $path;
            }

            $path = rtrim(realpath($basePath) . '/' . implode('/', $removed), '/');
        }

        return $path;
    }

    /**
     * @return bool Whether the host machine is running a Windows OS
     */
    private static function isWindows()
    {
        return defined('PHP_WINDOWS_VERSION_BUILD');
    }

    /**
     * {@inheritDoc}
     */
    protected function hasMetadataRepository($path)
    {
        //log_message('debug', "function: " . __FUNCTION__ . " line: ". __LINE__);
        $path = $this->normalizePath($path);

        return is_dir($path.'/.git');
    }
}
