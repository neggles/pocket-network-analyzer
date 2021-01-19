<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home_model extends CI_Model
{
    public function timeoutAction($time)
    {
        $this->load->helper('file');
        $array = array('time'=>$time);
        $json = json_encode($array);
        $timeoutFile = $this->config->item('timeout_file');

        exec("/usr/bin/mpg123 -q ".FCPATH."assets/sounds/still_on.mp3 &");
        
        if (! write_file($timeoutFile, $json)) {
            return false;
        } else {
            return true;
        }
    }

    public function getFileSystemUsage()
    {
        return passthru("df -h /tmp | tail -1 | awk '{print $5}'");
    }
    
    public function createPdfOfPage($data, $jobId = null, $type = "html")
    {
        $fileName = $this->job->getName($jobId) ? $this->job->getName($jobId) : "new-file";

        $pdfObject = new \Knp\Snappy\Pdf($this->config->item('wkhtmltopdf'));

        $css = array(
                    FCPATH . 'assets/css/customStyles.css',
                    FCPATH . 'assets/css/font-awesome.min.css'
                );

        $options = array(
                    'title'=> $fileName . ' Initial Test Specs',
                    'javascript-delay'=> 1000,
                    'viewport-size'=> '1250',
                    'load-error-handling' => 'skip',
                    'user-style-sheet' => $css
                    );

        if ($type == "html") {
            $pdfObject->generateFromHtml(urldecode($data), "assets/pdf/".$fileName.".pdf", $options, true);
            $data = array(
                        'status'=> true,
                        'msg'=>'In the url pdf generation function.'
                        );
            echo json_encode($data);
        } elseif ($type == "url") {
            $pdfObject->generate("http://localhost/assets/pdf/test.html", FCPATH. "assets/pdf/test.pdf", $options, true);

            $data = array(
                        'status'=> true,
                        'msg'=>'In the url pdf generation function.'
                        );
            echo json_encode($data);
        }
    }


    public function createPdfOfUrl($url)
    {
        $pdfObject = new \Knp\Snappy\Pdf($this->config->item('wkhtmltopdf'));

        $options = array(
                    'viewport-size'=>'1250',
                    'load-error-handling' =>'skip',
                    'no-background'=> false
                    );

        $pdfObject->setOptions($options);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="file.pdf"');
        echo $pdfObject->getOutput($url);
    }
}
