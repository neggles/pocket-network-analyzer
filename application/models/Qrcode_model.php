<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\HttpFoundation\Response;

class Qrcode_model extends CI_Model
{
    public $qrCode;

    public function __construct($id = null)
    {
        parent::__construct();
        $this->load->model('Ethernet_model', 'ethernet');
        $mac = $this->ethernet->getMacAddress();
        $this->qrCode = new QrCode($mac);
        $this->qrCode->setSize(200);
        $this->qrCode
        ->setMargin(10)
        ->setEncoding('UTF-8')
        ->setErrorCorrectionLevel(ErrorCorrectionLevel::LOW)
        ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0])
        ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255])
        ->setLogoPath(FCPATH . 'assets/images/pfi-logo.png')
        ->setLogoWidth(100)
        ->setValidateResult(false)
        ;
    }

    public function display()
    {
        header('Content-Type: ' . $this->qrCode->getContentType(PngWriter::class));
        echo $this->qrCode->writeString(PngWriter::class);
    }

    public function returnImageStringBase64()
    {
        $writer = new PngWriter();
        $pngData = $writer->writeString($this->qrCode);
        return base64_encode($pngData);
    }
}
