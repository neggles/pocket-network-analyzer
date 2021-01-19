<?php
 defined('BASEPATH') or exit('No direct script access allowed');
if (isset($currentJob->id) && $currentJob->id !== 0) {
    $settings = $currentJob->getDetails();
}
    ?>
    <style>
.p-3 {
    padding: 1rem!important;
}

  </style>
<div id="page-wrapper" class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
<div class="container-fluid">
<div class="row">
    <?php $gfastHistory = $this->gfast->getAllEtrValuesFromDatabase()->result_object(); ?>

<table class="table" id="etrHistory">
    <thead>
        <tr>
            <th>Date</th>
            <th>MDU Name</th>
            <th>Bldg #</th>
            <th>Unit #</th>
            <th>CCU</th>
            <th>Total ETR</th>
            <th>Up ETR</th>
            <th>Down ETR</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($gfastHistory as $object): ?>
            <?php $results = json_decode($object->results); ?>
            <tr>
                <td><?php echo $object->result_date; ?></td>
                <td><?php echo $object->name; ?></td>
                 <td><?php echo $object->bldg; ?></td>
                  <td><?php echo $object->unit; ?></td>
                <td><?php echo $object->ccu; ?></td>
                <td><?php echo (int) $results->ETR; ?></td>
                <td><?php echo (int) $results->TxETR; ?></td>
                <td><?php echo (int) $results->RxETR; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
        <?php //print_r($gfastHistory);?>
    
</div>
</div>
<?php $this->load->view('additional/footer');?>
</div>
<script type="text/javascript">
                $(document).ready(function() {
                    $('#etrHistory').DataTable();
                });


</script>

<?php if ($this->session->userdata('uid') === null) : /* Load the setup wizard and default to the uid page */ ?>
    <script> 
        $(document).on('ready',function() {
            showSetupWizard('load', 'uid');
            //$('#setupWizard').modal('show');
        });
    </script>
<?php elseif ($this->session->userdata('jobId') === null) : /* Load the setup wizard and default to the jobId page */ ?>
    <script> 
        $(document).on('ready',function() {
            showSetupWizard('load', 'job');
            //$('#setupWizard').modal('show');
        });
    </script>
<?php endif; ?>