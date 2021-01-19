<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tesseract_model extends CI_Model
{
    public $key;
    public $value;

    public function __construct()
    {
        parent::__construct();
    }

    public function readImage($imagePath)
    {
        if (is_file($imagePath)) {
            return (new TesseractOCR($imagePath))
            ->executable($this->config->item('tesseract_bin'))
            ->userWords($this->config->item('tesseract_words'))
            ->userPatterns($this->config->item('tesseract_patterns'))
            ->lang('eng')
            ->run();
        }
    }

    public function get($key)
    {
        $query = $this->db->select()
                ->where('key', $key)
                ->get('settings');

        foreach ($query->result() as $row) {
            return $row->value;
        }
    }
}
