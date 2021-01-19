<!--Wireless Scan Panel-->
<div class="col-lg-4">
    <div class="ibox float-e-margins">
        <a href="<?php echo site_url() ?>scanner">
            <div class="ibox-title">
                <div class="row">
                    <div class="col-lg-6">
                        <i class="fa fa-wifi fa-3x"></i>  
                    </div>
                    <div class="col-lg-6 text-right">             
                        <p class="lead">Wireless Scans</p>
                    </div>                
                </div>
            </div>
        </a>
        <div class="ibox-content">
            <?php if ($this->wireless->checkForScans()) :?>
                <p>Last Scan:
                    <?php echo $this->wireless->humanTiming(strtotime($this->wireless->getLastScanDate())); ?> ago
                </p>
            <?php else : ?>
                <div class="alert alert-danger"> No scans have been performed yet.</div>
            <?php endif; ?>
        </div>
        <a href="<?php echo site_url() ?>scanner">
            <div class="ibox-footer">
                <span class="pull-left">Run New Scan</span>
                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                <div class="clearfix"></div>
            </div>
        </a>
    </div>
</div>
<!--End Wireless Scan Panel-->
