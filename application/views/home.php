<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (isset($jobId) && $jobId !== 0) :
    ?>
<div id="page-wrapper" class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div role="main">
        <div class="row">
            <?php $this->load->view('additional/wireless_scans_view.php'); /* Load wireless scans panel */?>
            <?php $this->load->view('additional/speedtest_view.php'); /* Load speedtest panel */?>
            <?php $this->load->view('additional/client_list_view.php'); /* Load client list panel */ ?>
        </div>
        <div class="row">
        <?php $this->load->view('additional/select_job_view.php'); /* Load select job panel */ ?>
        <?php $this->load->view('additional/create_job_view.php'); /* Load create job panel */ ?>
        </div>
    <script>
    $(document).ready(function() {
        $('#wirelessTable').DataTable();
        $('#ethernetTable').DataTable();
    });
    </script>
    </div>

</div> <!-- .wrapper-content -->

<?php $this->load->view('additional/footer');?>
</div> <!-- #page-wrapper -->
<?php endif; ?>
<script src="<?php echo $this->config->item('plugins_directory');?>nouislider/distribute/nouislider.min.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>table-to-json/lib/jquery.tabletojson.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>jquery-json2html/json2html.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>jquery-json2html/jquery.json2html.js"></script>
<?php if ($this->config->item('dsl_enabled')) : ?>
<script src="/assets/js/mainPageModem.js"></script>
<?php endif; ?>
<?php if ($this->session->userdata('uid') === null) : /* Load the setup wizard and default to the uid page */ ?>
    <script> 
        $(document).on('ready',function() {
            showSetupWizard('load', 'uid');
        });
    </script>
<?php elseif ($this->session->userdata('jobId') === null) : /* Load the setup wizard and default to the jobId page */ ?>
    <script> 
        $(document).on('ready',function() {
            showSetupWizard('load', 'job');
        });
    </script>
<?php endif; ?>
