<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Job_model extends CI_Model
{
    private static $db;
    public $id;
    public $name;
    public $date;
    public $comments;
    public $ban;
    public $phone;
    public $location;

    public function __construct($id = null)
    {
        parent::__construct();
        self::$db = &get_instance()->db;
        $this->setJob($id);
    }
    
    public function setJob($id)
    {
        $this->id = $id;
        
        if ($id !== null) {
            $this->getJobDetails();
        }
    }

    private function jobObject()
    {
        $jobObj = new StdClass();
        $jobObj->id = $this->id;
        $jobObj->name = $this->name;
        $jobObj->date = $this->date;
        $jobObj->comments = $this->comments;
        $jobObj->ban = $this->ban;
        $jobObj->phone = $this->phone;
        $jobObj->location = $this->getLocationDetails();
        return $jobObj;
    }

    public function getJobDispatchTypes()
    {
        $dispatchTypes = array(
            "install" => "Install",
            "repair" => "Repair"
            );
        return $dispatchTypes;
    }

    public function getJobHomeTypes()
    {
        $homeTypes = array(
            "single_story_home" => "Single Story Home",
            "multi_story_home" => "Multi Story Home",
            "townhome"=>"Townhome",
            "apartment_condo" => "Apartment/Condo"
            );
        return $homeTypes;
    }

    
    private function getJobDetails()
    {
        $query = $this->db->select()
                ->where('id', $this->id)
                ->get('job');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $this->name = $row->name;
                $this->id = $row->id;
                $this->date = $row->date;
                $this->comments = $row->comments;
                $this->ban = $row->ban;
                $this->phone = $row->phone;
            }
        }
    }

    public function getFloorPlan($id = null)
    {
        $query = $this->db->select('floorplan')
                ->where('job', $id ? $id : $this->id)
                ->get('location');
        if ($query->num_rows() === 0) {
            return false;
        } else {
            return $query->row()->floorplan;
        }
    }

    public function getDetails()
    {
        return $this->jobObject();
    }

    private function getLocationDetails($id = null)
    {
        $thisId = ($id !== null) ? $id : $this->id;
        $query = $this->db->select('id, address, home_type, floors, dispatch_type, job, default_network, last_update')
                ->where('job', $thisId)
                ->get('location');
        if ($query->num_rows() == 0) {
            $msg = array('status' => false, 'msg' =>'No Location Results Found for id: ' . $thisId);
            return $msg;
        }
        return $query->row();
    }
        
    public function getName($id = null)
    {
        if (null === $id) {
            return $this->name;
        } else {
            $jobName = $this->db->select('name')
            ->where('id', $id)
            ->get('job');

            if ($jobName->num_rows() > 0) {
                foreach ($jobName->result() as $row) {
                    return $row->name;
                }
            } else {
                return false;
            }
        }
    }

    public function saveFloorplan($floorplan)
    {
        $dataArray = array(
            'floorplan' => $floorplan['layout']
        );
        $this->db->set($dataArray);
        $this->db->where('id', $floorplan['location']);
        $this->db->update('location');
        return true;
    }

    public function getBanNumber()
    {
        return $this->ban;
    }
    
    public function getComments()
    {
        return $this->comments;
    }

    public function getPhoneNumber()
    {
        return $this->phone;
    }
    
    public function getDate()
    {
        return $this->date;
    }
    
    public function humanTiming($time)
    {
        $time = time() - $time; // to get the time since that moment

        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) {
                continue;
            }
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }
    }
    
    public function numberOfSpeedTests()
    {
        $this->db->select();
        $this->db->where('job', $this->id);
        $query = $this->db->get('speedtest');
        return $query->num_rows();
    }
    
    public function numberOfNetworkScans($id = null)
    {
        $this->id = $id;
        $this->db->select();
        $this->db->where('job', $this->id);
        $query = $this->db->get('networkscan');
        return $query->num_rows();
    }
        
    public static function getCurrentJobStatic()
    {
        self::$db->order_by('date', 'desc');
        self::$db->select();
        self::$db->limit(1);
        $job=self::$db->get('job');

        foreach ($job->result() as $row) {
            $data[]=array(
            'id'=>$row->id,
            'name'=>$row->name,
            'comments'=>$row->comments,
            'ban'=>$row->ban
            );
        }
        return $data;
    }
    
    public static function getAllJobs()
    {
        $query = self::$db->select()
        ->get('job');

        if ($query->num_rows() !== 0) {
            return $query->result_object();
        } else {
            return null;
        }
    }
        
    public function getId()
    {
        return $this->id;
    }

    public function saveJobLocationSettings($data)
    {
        $dataArray = array(
            'home_type' => $data['home_type'],
            'dispatch_type' => $data['dispatch_type'],
            'address' => $data['address'],
            'floors' => $data['floors'],
            'floorplan' => '',
            'default_network' => json_encode(array('ssid' => $data['default_network'], 'mac' => $data['mac'], 'frequency' => $data['frequency'])),
            'last_update' => date("Y-m-d H:i:s"),
            'job' => $data['job']
            );

        if ($data['id'] !== '' || !empty($data['id'])) {
            $this->db->set($dataArray);
            $this->db->where('id', $data['id']);
            $this->db->update('location');
        } else {
            $this->db->set($dataArray);
            $this->db->where('job', $data['job']);

            if (!$this->db->insert('location')) {
                $error = $this->db->error();
                return $error;
            }
        }
        return $this->getLocationDetails($data['job']);
    }
    
    public function saveJobLocationDefaultNetwork($data)
    {
        $dataArray = array(
            'default_network' => json_encode(array('ssid' => $data['default_network'], 'mac' => $data['mac'], 'frequency' => $data['frequency']))
            );

        if (!empty($data['id'])) {
            $this->db->set($dataArray);
            $this->db->where('id', $data['id']);
            if (!$this->db->update('location')) {
                $array = array(
                        'status' => false,
                        'msg' => 'Error updating existing location record',
                        'error' => $this->db->error()
                        );
                return $array;
            }
        } else {
            if ($this->doesJobHaveLocation($data['job'])) {
                $this->db->set($dataArray);
                $this->db->where('job', $data['job']);
                if (!$this->db->update('location')) {
                    $array = array(
                        'status' => false,
                        'msg' => 'Error updating existing location record',
                        'error' => $this->db->error()
                        );
                    return $array;
                }
            } else {
                $this->db->set($dataArray);
                if (!$this->db->insert('location')) {
                    $array = array(
                        'status' => false,
                        'msg' => 'Error creating location record',
                        'error' => $this->db->error()
                        );
                    return $array;
                }
            }
        }
        return $this->getLocationDetails($data['job']);
    }

    public function updateName($data)
    {
    }
    
    public function updateComments($data)
    {
    }

    public function deleteJob($jobId = null)
    {
        if ($jobId !== null) {
            $this->db->where('id', $jobId);
            $this->db->delete('job');
            if ($this->db->affected_rows() !== 1) {
                return false;
            } else {
                $this->db->where('job', $jobId);
                $this->db->delete('location');
                return true;
            }
        } else {
            return false;
        }
    }
    
    private function insertNewJob($data)
    {
        $this->db->insert('job', $data);
        return $this->db->insert_id();
    }

    private function createInitialLocation($job)
    {
        $location = array(
            'job' => $job,
            'address' => '',
            'home_type' => 'single_story_home',
            'floors' => '2',
            'dispatch_type' => 'install',
            'default_network' => '{}',
            'floorplan' => '',
            'last_update' => date("Y-m-d H:i:s")
            );
        $this->db->insert('location', $location);

        if ($this->db->affected_rows() !== 1) {
            return false;
        } else {
            return true;
        }
    }
    
    public function createNewJob($data)
    {
        $exists = $this->doesJobExist($data['ban']);
        // Job doesnt exist, keep going
        if (!$exists) {
            $this->db->insert('job', $data);
            // Unable to create a new job
            if ($this->db->affected_rows() !== 1) {
                $msg = array(
                'status' => false,
                'msg' => 'Unable to create new job',
                'error' => $this->db->error()
                );
                return json_encode($msg);
            } else {
                $jobId = $this->db->insert_id();
                // try setting up the default location settings
                if ($this->createInitialLocation($jobId)) {
                    $msg = array(
                    'status' => true,
                    'msg' => 'Added New Job',
                    'job' => array(
                    'id' => $jobId,
                    'name' => $data['name']
                    )
                    );
                    return json_encode($msg);
                } else {
                    $msg = array(
                        'status' => false,
                        'msg' => 'Unable to create default location',
                        'error' => $this->db->error()
                    );
                    return json_encode($msg);
                }
            }
        } else {
            $error = array(
                'status' => false,
                'msg' => 'Job with that BAN already exists.'
            );
            return json_encode($error);
        }
    }

    private function doesJobExist($jobBan)
    {
        $query = $this->db->select('*')
                ->where('ban', $jobBan)
                ->get('job');

        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }

    private function doesJobHaveLocation($job)
    {
        $query = $this->db->select('*')
                ->where('job', $job)
                ->get('location');
        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }
    
    public function getMostRecentJob()
    {
        $this->db->order_by('date', 'desc');
        $this->db->select();
        $this->db->limit(1);
        $job=$this->db->get('job');
        foreach ($job->result() as $row) {
            $data[] =array(
            'id' => $row->id,
            'name' => $row->name
            );
        }
        return json_encode($data);
    }
}
