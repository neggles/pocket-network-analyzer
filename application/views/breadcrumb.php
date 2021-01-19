<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-9">
	<h2 class="jobName">
		<?php if (isset($breadcrumbTitle) && $breadcrumbTitle !== "") : ?>
            <?php echo $breadcrumbTitle; ?>
        <?php elseif ($currentJob) :?>
            <?php echo $currentJob->name; ?>                
        <?php else : ?>
            You must select a job before continuing.           
        <?php endif;?>
        </h2>
        <ol class="breadcrumb">
            <li>
                <a><?php echo $this->session->userdata('uid'); ?></a>
            </li>
        </ol>
    </div>
</div>