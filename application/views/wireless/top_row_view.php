<!--Current Wireless Network-->
<div class="row">
     <!--<div class="col-md-4">
       <a class="btn btn-block btn-lg btn-primary" href="<?php echo base_url();?>scanner/floorplan">Create Floorplan</a> 
    </div>-->
    <!--End Network Block-->
    <div class="col-md-offset-4 col-md-4">
        <!--Begin Job Information Block-->
        <div class="text-center">
            <h3>Current Job</h3>
        </div>
        <div class="alert alert-success">
            <div class="text-center">
                <h4 id="currentJobTitle"><?php echo $currentJob->getName(); ?></h4>
                <?php if (isset($networkList) && $networkList !== null) : ?>
                <p class="lead">
                    <?php $timeSince = $this->wireless->humanTiming(strtotime($this->wireless->getLastScanDate())); ?>
                    <?php if ($timeSince == null || $timeSince == "") :?>
                        The last scan was just performed
                    <?php else : ?>
                        The last scan was performed
                        <?php echo $timeSince; ?> ago 
                    <?php endif; ?>
                </p>
                <?php else : ?>
                <div class="alert alert-danger">
                    No scans have been performed yet.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!--End Job Information Block-->
    <div class="col-md-4">
        <div class="text-center">
            <button id="runNewNetworkScan" class="btn btn-danger btn-lg">
                Run New Scan</button>
        </div>
    </div>
</div>
