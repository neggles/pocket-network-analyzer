 <?php
 defined('BASEPATH') or exit('No direct script access allowed');
 if (!isset($jobId)) : ?>
    <script>
        swal({
            title: "You cannot access this page directly",
            text: "You will be redirected.",
            type: "warning",
            timer: 5000
        },
        function(){
		window.location = 	"<?php echo site_url(); ?>";
        });     
    </script>
<?php                                    else : ?>
    <div id="page-wrapper" class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
<div role="main">
<div class="row">
<!--Comments Panel-->
            <!--<div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-comments fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">                                    
                                    <div>Comments!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#" id="commentsBox">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>--><!--End Comments Panel-->
                
                <div class="col-lg-3 col-md-6"><!--Speed Tests Panel-->
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-tasks fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $currentJob->numberOfSpeedTests();?></div>
                                    <div>Speed Tests</div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo site_url()?>speedtest">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div><!--End Speed Tests Panel-->
                
</div>

    <div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <center>  <h4 class="panel-title"><?php echo $currentJob->getName();?></h4></center>
            </div>
            <div class="panel-body">    
            <div class="list-group">
                <a href="#" class="list-group-item"> <i class="fa fa-calendar"></i> Date Created: <?php

            $t = strtotime($currentJob->getDate());
                echo date('jS  F, Y', $t) . PHP_EOL;
            
            ?><span class="pull-right"><em><?php echo $this->job->humanTiming($t).' ago';?></em></span></a>
                </div>
            </div>   
    </div>  
    </div>
    
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-center"> <h4 class="panel-title">Job Info</h4></div>
            </div>
            <div class="panel-body">
            <table class="table">
            <tbody>
            <tr>
                <td>Job Name</td>
                <td>
                <?php if (isset($jobName)) : ?>
                    <?php echo $jobName; ?>
                <?php else : ?>
                    <?php echo 'No Job Name set'; ?>
                <?php endif; ?>          
                </td>
            </tr>
            <tr>
                <td>BAN Number</td>
                <td><?php if (isset($banNumber)) :
                    echo $banNumber;

else :
    echo 'No BAN Number set';

endif; ?></td>
            </tr>
            <tr>
                <td>Phone Number</td>
                <td><?php if (isset($phoneNumber)) :
                    echo '<a href="tel:' . $phoneNumber . '>' . $phoneNumber . '</a>' ;

else :
    echo 'No Phone Number set';

endif; ?></td>
            </tr>
            </tbody>
            </table>
            </div>  
    </div>  
    </div>  
    
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-center">  <h4 class="panel-title">Job History</h4></div>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-condensed">
                    <tbody>
                    <tr>
                    <td># Speed Tests</td>
                    <td><?php echo $currentJob->numberOfSpeedTests();?></td>
                    </tr>
                    </tbody>
                </table>            
            </div>  
        </div>  
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-center">  <h4 class="panel-title">User Information</h4></div>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-condensed">
                    <tbody>
                    <tr>
                    <td>User ID</td>
                    <td><?php echo $this->session->userdata('uid');?></td>
                    </tr>
                    </tbody>
                </table>            
            </div>  
        </div>  
    </div>

    </div>
</div>
</div>
</div>
</body>
</html>
<?php
endif;
?>
