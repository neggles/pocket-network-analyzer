<?php
defined('BASEPATH') or exit('No direct script access allowed');
if (isset($currentJob->id) && $currentJob->id !== 0) :
    $speedTests = $this->speedtest->getSpeedTests($currentJob->id);
    $settings = $currentJob->getDetails();
?>
<link rel="stylesheet" href="<?php echo $this->config->item('plugins_directory');?>switchery/dist/switchery.min.css"/>
<?php if ($this->config->item('speedtest_enabled')) : ?>
    <script src="/assets/js/jquery.peity.js"></script>
    <?php endif;?>
<div id="page-wrapper" class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div role="main">
        <div class="row">
            <!--Attributes to run speed test on-->
            <div class="col-md-4 col-xs-12">
                <div class="ibox float-e-margins" id="upload">
                    <div class="ibox-title">
                        <h5>Speed Test Parameters</h5>
                    </div>
                    <div class="ibox-content">
                        <form class="form" id="speedTestForm" autocomplete="off">
                            <a href="#" id="advancedToggle" class="btn btn-xs btn-primary" data-target="#advanced-options">
                                <i class="fa fa-cog"></i>
                            </a>
                            <div style="display:none;" id="advanced-options">
                            <p>Advanced Options</p>
                             <div class="form-group">
                                <label for="pspeed-speedtest-server">Speedtest Server: </label>
                                <input type="text" class="form-control" name="pspeed-speedtest-server" id="pspeed-speedtest-server" placeholder="atlanta.speedtest.test.com" autocomplete="off">
                                <p class="help-block">Use this text field to identify a custom speed test server (Not Mandatory)</p>
                            </div>
                             <div class="form-group">
                                <label for="pspeed-speedtest-server">Speedtest Server Port: </label>
                                <input type="text" class="form-control" name="pspeed-speedtest-port" id="pspeed-speedtest-port" placeholder="8444" autocomplete="off">
                                <p class="help-block">Use this text field to identify a custom speed test port (Not Mandatory)</p>
                            </div>
                            <div class="row">
                            <div class="col-xs-6">
                             <div class="form-group">
                                <label for="download-connections">Download Connections: </label>
                                <input type="number" class="form-control" name="download-connections" id="download-connections" placeholder="<?php echo $this->config->item('speedtest_down_connections');?>" data-min="2" data-max="99" autocomplete="off" value="<?php echo $this->config->item('speedtest_down_connections');?>" data-default="<?php echo $this->config->item('speedtest_down_connections');?>">
                            </div> 
                            </div>
                            <div class="col-xs-6">   
                             <div class="form-group">
                                <label for="upload-connections">Upload Connections: </label>
                                <input type="number" class="form-control" name="upload-connections" id="upload-connections" placeholder="<?php echo $this->config->item('speedtest_up_connections');?>" data-min="2" data-max="99" autocomplete="off" value="<?php echo $this->config->item('speedtest_up_connections');?>" data-default="<?php echo $this->config->item('speedtest_up_connections');?>">
                            </div>
                            </div>
                            </div>
                            </div>
                        <?php if ($this->config->item('speedtest_server')) : ?>
                            <div class="form-group">
                                <label for="speedtest-server">Speedtest Server: </label>
                                <input type="text" class="form-control" id="speedtest-server" placeholder="http://att.com/speedtest" autocomplete="off">
                                <p class="help-block">Use this text field to identify a custom speed test server (Not Mandatory)</p>
                            </div>
                        <?php endif; ?>

                            <div class="form-group">
                                <label for="interface">Interface: </label>
                                <select id="interface" name="interface" class="form-control">
                                    <?php  if ($this->ethernet->getInterfaceStatus() === true) : ?>
                                    <option id="ethernet" value="ethernet">Ethernet</option>
                                    <?php endif; ?>
                                    <option id="wireless" value="wireless">Wireless</option>
                                </select>
                            </div>
                            <div id="networkNameDiv" style="display:none;">
                                <div class="form-group">
                                    <label for="networkName">Wireless Network: </label>
                                    <select id="networkName" name="networkName" class="form-control" disabled="disabled">
                                    <option value="empty">Select a Network</option>
                                    <?php if (isset($settings->location)) : ?>
                                        <?php if ($settings->location->default_network) :?>
                                            <option class="on" value="<?php echo $settings->location->default_network; ?>"><?php echo $settings->location->default_network; ?></option>
                                        <?php endif; ?>
                                    <?php endif; ?>                                       
                                    </select>
                                </div>
                            </div>
                            <div id="passwordGroupOuter" style="display:none;">
                                <div class="form-group">
                                    <label for="passphrase">Password: </label>
                                    <div class="input-group" id="passwordGroup">
                                    </div>
                                    <span id="helpBlock" class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" id="networkConfiguration" name="networkConfiguration" value="">
                            </div>

                            <div class="form-group">
                            <div class="text-center"> 
                                <button class="btn btn-danger" id="runSpeedTest">Run Speed Test</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--Attributes to run speed test on-->
            <div class="col-md-8 col-sm-5 col-xs-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">                       
                            <h5>Speedtest Updates</h5>
                            <div class="ibox-tools">
                            <p id="host"></p>
                            </div>
                    </div>
                    <div class="ibox-content">
                        <div id="speedTestAlerts">
                            <div class="alert alert-danger" id="dangerAlerts" style="display:none;">
                            </div>
                            <div class="alert alert-warning" id="warningAlerts" style="display:none;">
                            </div>
                            <div class="alert alert-success" id="successAlerts" style="display:none;">
                            </div>
                            <div class="alert alert-info" id="infoAlerts" style="display:none;">
                            </div>
                        </div>
                        <div id="speedTestIntructionsWrapper">
                            <div class="text-center" id="speedTestConfirmWrapper" style="display:none;">
                                <p class="lead">Are you sure you want to run a speedtest?</p>
                                <button type="button" class="btn btn-primary" id="speedTestSubmit">Yes</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                            </div>
                            <div id="speedTestActions" class="text-center" style="display:none;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-sm-7 col-xs-12">
            <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="ibox float-e-margins" id="download">
                    <div class="ibox-title">                     
                            <h5>Download Speed</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="text-center">
                        <span class="updating-chart" style="display:none;">0</span>
                            <h2></h2>
                            <h5></h5>
                        </div>
                    </div>
                </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="ibox float-e-margins" id="upload">
                    <div class="ibox-title">
                            <h5>Upload Speed</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="text-center">
                        <span class="updating-chart" style="display:none;">0</span>
                            <h2></h2>
                            <h5></h5>
                        </div>
                    </div>
                </div>
                </div>
                </div>
                <div class="row">
                <div class="ibox" id="fastResults" style="display:none;">
                <div class="ibox-title">
                <h5>Fast.com Results</h5>
                </div>
                <div id="fastSpeedTestActions" class="ibox-content">
                <div class="text-center">
                <h2></h2>
                </div>
                </div>
                </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="ibox float-e-margins" id="download">
                    <div class="ibox-title">
                            <h5>History</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="speedTestHistory">
                              <thead>
                                    <tr>                           
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Interface</th>
                                        <th>SSID</th>
                                        <th>Download</th>
                                        <th>Upload</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php $this->load->view('additional/footer');?>
    </div> <!-- #page-wrapper -->
    </div>
    </div>
    <?php $this->load->view('modals/editCommentsModal');?>

<script src="<?php echo $this->config->item('plugins_directory');?>switchery/dist/switchery.min.js"></script>

<script>

$(document).on('click', '#advancedToggle', function(e){
    e.preventDefault();
    var target = $(this).data('target');
    console.log(target);
    $(target).toggle();
});

/*
    Call the function to update the wireless networks dropdown.
 */
wirelessNetworks("#networkName", "<?php echo $dynamicSsid; ?>");

    //Handle dynamic modal to edit comments for speed test

    $('#closeCommentModal').on('click', function() {
        $('#editCommentModal textarea#comments').val('');
        $('#editCommentModal input#id').val('');
    });

    //get comments and appends to modal textarea

    $('button#eTC').on('click', function() {
        var id = $(this).val();
        $('#editCommentModal input#id').val(id);
        //console.log(id);  
        $.ajax({
            method: 'post',
            url: "speedtest/getCommentsById",
            dataType: 'json',
            data: {
                id: id
            },
            success: function(data) {
                for (var i = 0; i < data.length; i++) {
                    $('#editCommentModal textarea#comments').val(data[i].comments);
                }
            }
        });
        $('#editCommentModal').modal('show');
    });

var speedtestTable;
$(document).on('ready', function() {
    
store.set("runningSpeedtest", false);

        speedtestTable = $('#speedTestHistory').DataTable({
          "ajax": '/speedtest/getSpeedTestByJob',
            "columns":[
            { "data": 'speedtestid'},
            { "data": 'date'},
            { "data": 'connection'},
            { "data": 'ssid'},
            { "data": 'download'},
            { "data": 'upload'}
            ],
            "order": [
                [0, "desc"]
            ]
        });

<?php if ($this->config->item('pusher_enabled')) : ?>
    var speedtestNotificationsChannel = pusher.subscribe('speedtest');
        // Subscribe to notifications that are being fed from the speed test.     

/* Begin rmr speedtest channels */
  
    /*
    Example:

     */
    speedtestNotificationsChannel.bind('receiver', function(notification) {
        var message = notification;
        //console.log(message);
    });
    /*
    Example:
{
  "socket": 5,
  "start": 0,
  "end": 10.003275156021118,
  "seconds": 10.003275156021118,
  "bytes": 1086212272,
  "bits_per_second": 868685309.60777807,
  "retransmits": 0,
  "max_snd_cwnd": 2832288,
  "max_rtt": 23750,
  "min_rtt": 21875,
  "mean_rtt": 23212
}
     */
    speedtestNotificationsChannel.bind('sender', function(notification) {
        var message = notification;
        //console.log(message);
    });
    /*
    Example:
{
  "socket": 5,
  "start": 0,
  "end": 1.0032730102539062,
  "seconds": 1.0032730102539062,
  "bytes": 31082672,
  "bits_per_second": 247850159.88526323,
  "retransmits": 0,
  "snd_cwnd": 1823032,
  "rtt": 21875,
  "rttvar": 750,
  "pmtu": 1500,
  "omitted": false
}
     */
    speedtestNotificationsChannel.bind('interval', function(notification) {
        var message = notification;
        //console.log('Interval');
        //console.log(message);
        //console.log(message.bits_per_second);
        //var speed = message.bits_per_second / 1024 / 1024; /* Mbps | bytes not bits */
        //var updatingChart = $(".updating-chart").peity("line", { width: 64 });
        //var currentValues = $(".updating-chart");
        //var values = updatingChart.text().split(",");
        //values.push(speed);

        //$('div#download h2').text(speed.toFixed(2));

        //currentValues.text(values.join(","));

        //updatingChart.change();
    });

    speedtestNotificationsChannel.bind('download_sum', function(notification) {
        var message = notification;
        var speed = message.bits_per_second / 1000 / 1000; /* Mbps | bytes not bits */
        var updatingChart = $("#download .updating-chart").peity("line", { 
            width: 128, 
            fill: '#1ab394',
            stroke:'#169c81'
        });
        var currentValues = $("#download .updating-chart");
        var values = updatingChart.text().split(",");
        values.push(speed);

        $('div#download .ibox-content h2').html("<span id='actual-speed'>" + speed.toFixed(2) + "</span> <span id='units'>Mbps</span>");

        currentValues.text(values.join(","));

        updatingChart.change();

        var speedTestProgress = $('div#speedTestProgressBar').attr('aria-valuenow');
        var newSpeedTestProgress = Number(speedTestProgress) + 5;
        addToProgressBar($('div#speedTestProgressBar'), newSpeedTestProgress);
        $('div#progressWrapper p#speedTestUpdates').text("Running download speedtest");

    });

    speedtestNotificationsChannel.bind('upload_sum', function(notification) {
        var message = notification;
        var speed = message.bits_per_second / 1000 / 1000; /* Mbps | bytes not bits */
        var updatingChart = $("#upload .updating-chart").peity("line", { 
            width: 128,
            fill: '#1ab394',
            stroke:'#169c81' 
        });
        var currentValues = $("#upload .updating-chart");
        var values = updatingChart.text().split(",");
        values.push(speed);

        $('div#upload .ibox-content h2').html("<span id='actual-speed'>" + speed.toFixed(2) + "</span> <span id='units'>Mbps</span>");

        currentValues.text(values.join(","));
        updatingChart.change();
        var speedTestProgress = $('div#speedTestProgressBar').attr('aria-valuenow');
        var newSpeedTestProgress = Number(speedTestProgress) + 5;
        addToProgressBar($('div#speedTestProgressBar'), newSpeedTestProgress);
        $('div#progressWrapper p#speedTestUpdates').text("Running upload speedtest");

    });

    speedtestNotificationsChannel.bind('complete', function(notification) {
        var message = notification;
        //console.log(message.message);
        //console.log(message);
        if(message.direction == "download") {
            
        } else if (message.direction == "upload") {
            store.set("runningSpeedtest", false);
            addToProgressBar($('div#speedTestProgressBar'), 100);
            $('div#progressWrapper p#speedTestUpdates').text("Speedtest Complete");
            submitSpeedTest($('form#speedTestForm select#interface').val());
        }
    });

    /*
    Example:
{
  "protocol": "TCP",
  "num_streams": 1,
  "blksize": 131072,
  "omit": 0,
  "duration": 10,
  "bytes": 0,
  "blocks": 0,
  "reverse": 0,
  "tos": 140488380252160
}
     */
    speedtestNotificationsChannel.bind('test_start', function(notification) {
        var message = notification;
        console.log('Test Start');
        //console.log(message);
    });

    /*
    Example:
{
  "host": "atlanta.speedtest.test.com",
  "port": 8444
}
     */
    speedtestNotificationsChannel.bind('connecting_to', function(notification) {
        var message = notification;
        //console.log(message);
        $("p#host").text("Speedtest Host: " + message.host);
    });
    /* End rmr speedtest channels */

        /* Send from model when connection is established */
        speedtestNotificationsChannel.bind('success', function(notification) {
            var message = notification.message;
            //console.log('Success: ' + message);
            if (message == "running") {
                $('#speedTestConfirmWrapper').hide();
                if($('#speedTestActions #progressWrapper').length > 0) {
                   $('#speedTestActions #progressWrapper').remove(); 
                }

                $('#speedTestActions').show();
                var progressBar = '<div id="progressWrapper"><p>Speed Test Progress</p>' +
                    '<div class="progress" >' +
                    '<div id="speedTestProgressBar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100" style="width: 5%">' +
                    '<span class="sr-only">5% Complete</span></div></div><p class="small" id="speedTestUpdates"></p></div>';

                $('#speedTestActions').append(progressBar);
                $('div#progressWrapper p#speedTestUpdates').text('Starting speedtest...');
                $('div.waitSpeedTest').remove();
                $('button#retrySpeedTest').remove();
                clearAlerts();
            } else {
                processAlerts($('div#speedTestAlerts div#successAlerts'), message, 'show');
            }
        });

        speedtestNotificationsChannel.bind('error', function(notification) {

            var message = notification.message;
            var date = new Date();
            console.log(date);          

            for(var i = 0; i < message.length; i++) {
                console.log(message[i]);
                var jsonString = JSON.stringify(message[i]);
                console.log(jsonString);
            }

            if ($('#retryButtonWrapper').length > 0) {
                $('#retryButtonWrapper').show();
            } else {
                $('#speedTestConfirmWrapper').hide();
                var buttonContent = '<div id="retryButtonWrapper"><button type="button" class="btn btn-danger" onclick="javascript:retrySpeedTest();">Retry</button></div>';
                $('#speedTestIntructionsWrapper').append(buttonContent);
            }
            processAlerts($('div#speedTestAlerts div#dangerAlerts'), message, 'show');
            $('#speedTestConfirmWrapper').hide();
        });

        speedtestNotificationsChannel.bind('no-network', function(notification) {
            var message = notification.message;
            //console.log(message);
            if ($('#retryButtonWrapper').length > 0) {
                $('#retryButtonWrapper').show();
            } else {
                $('#speedTestConfirmWrapper').hide();
                var buttonContent = '<div id="retryButtonWrapper"><button type="button" class="btn btn-danger" onclick="javascript:retrySpeedTest();">Retry</button></div>';
                $('#speedTestActions').append(buttonContent);
            }
            processAlerts($('div#speedTestAlerts div#dangerAlerts'), message, 'show');
            store.set("runningSpeedtest", false);
        });


        speedtestNotificationsChannel.bind('status', function(notification) {

            var message = notification.message;
            //console.log('Status: ' + message);
            if(message == "download_start") {
                $('div#download .ibox-content h2').html("<i class='fa fa-cog fa-spin'></i>");
                return;
            } else if(message == "upload_start") {
                $('div#upload .ibox-content h2').html("<i class='fa fa-cog fa-spin'></i>");
                return;
            }

            processAlerts($('div#speedTestAlerts div#infoAlerts'), message, 'show');
        });

        speedtestNotificationsChannel.bind('update', function(notification) {

            var message = notification.message;
            //console.log('Update: ' + message);
            var speedTestProgress = $('div#speedTestProgressBar').attr('aria-valuenow');
            var newSpeedTestProgress = Number(speedTestProgress) + 15;
            addToProgressBar($('div#speedTestProgressBar'), newSpeedTestProgress);
            $('div#progressWrapper p#speedTestUpdates').text(message);
        });

        speedtestNotificationsChannel.bind('download-speed', function(notification) {
            addToProgressBar($('div#speedTestProgressBar'), 75);
            var message = notification.message;
            console.log('Download Speed: ' + message);
            $("#download .ibox-content h2").html("<span id='actual-speed'>" + message + "/s</span>");
        });



        speedtestNotificationsChannel.bind('upload-speed', function(notification) {
            addToProgressBar($('div#speedTestProgressBar'), 100);
            var message = notification.message;
            console.log(message);
            $("#upload .ibox-content h2").html("<span id='actual-speed'>" + message + "/s</span>");
            $('div#speedTestActions').html('');
            submitSpeedTest($('form#speedTestForm select#interface').val());
            store.set("runningSpeedtest", false);
        });

        speedtestNotificationsChannel.bind('download', function(notification) {
            var message = notification.message;
            $('div#download .ibox-content h2').html('');
            console.log('Download');


            var cleanedMessage = message.replace('e ', '');
            
            console.log(cleanedMessage);          

            var json = JSON.parse(cleanedMessage);
            if(json.error !== undefined) {
                var cleanJson = JSON.parse(message);
                $('div#download .ibox-content h5').text(cleanJson.error);
                return;
            }

            console.log(json);

            var updatingChart = $("#download .updating-chart").peity("line", { width: 128, fill: '#1ab394',
            stroke:'#169c81' });

            var biggest = 0;

            for(var i = 0; i < json.intervals.length; i++) {
                var data = json.intervals[i];
                //console.log(json.intervals[i]);

                var speed = data.sum.bits_per_second / 1000 / 1000; /* Mbps | bytes not bits */
                if(speed > biggest) {
                    biggest = speed;
                }
                var currentValues = $("#download .updating-chart");
                var values = updatingChart.text().split(",");
                values.push(speed);
                currentValues.text(values.join(","));
                updatingChart.change();

            }

            var speed = json.end.sum_sent.bits_per_second / 1000 / 1000;
            $('div#download .ibox-content h5').html("Avg: <span id='avg-speed'>" + speed.toFixed(2) + " </span> <span id='units'>Mbps</span>");
            $('div#download .ibox-content h2').html("Max Attained: <span id='actual-speed'>" + biggest.toFixed(2) + "</span> <span id='units'>Mbps</span>");
               addToProgressBar($('div#speedTestProgressBar'), 50);
        });

        speedtestNotificationsChannel.bind('upload', function(notification) {
            var message = notification.message;
            $('div#upload .ibox-content h2').html('');
            console.log('Upload');
            var cleanedMessage = message.replace('e ', '');
            var json = JSON.parse(cleanedMessage);

            if(json.error !== undefined) {
                var cleanJson = JSON.parse(message);
                $('div#upload .ibox-content h5').text(cleanJson.error);
                store.set("runningSpeedtest", false);
                addToProgressBar($('div#speedTestProgressBar'), 0);
                $('#speedTestUpdates').text('Error running speedtest');
                return;
            }

            var updatingChart = $("#upload .updating-chart").peity("line", { width: 128, fill: '#1ab394',
            stroke:'#169c81' });
            var biggest = 0;

            for(var i = 0; i < json.intervals.length; i++) {
                var data = json.intervals[i];
                var speed = data.sum.bits_per_second / 1000 / 1000; /* Mbps | bytes not bits */

                if(speed > biggest) {
                    biggest = speed;
                }

                var currentValues = $("#upload .updating-chart");
                var values = updatingChart.text().split(",");
                values.push(speed);
                currentValues.text(values.join(","));
                updatingChart.change();
            }

            var speed = json.end.sum_sent.bits_per_second / 1000 / 1000;
            $('div#upload .ibox-content h5').html("Avg: <span id='avg-speed'>" + speed.toFixed(2) + " </span> <span id='units'>Mbps</span>");
            $('div#upload .ibox-content h2').html("Max Attained: <span id='actual-speed'>" + biggest.toFixed(2) + " </span> <span id='units'>Mbps</span>");
            store.set("runningSpeedtest", false);
            addToProgressBar($('div#speedTestProgressBar'), 100);
            submitSpeedTest($('form#speedTestForm select#interface').val());
        });



<?php endif; ?>

        $(document).on('click','button#deleteSpeedTest', function(e) {
            e.preventDefault();
            var id = $(this).val();
            swal({
                    title: "Are you sure you want to delete this speed test? <br> <i class='fa fa-trash-o text-danger fa-4x'></i>",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    html: true,
                    confirmButtonText: "Delete",
                    animation: "slide-from-top"
                },
                function() {
                    $.ajax({
                        method: 'post',
                        url: "/speedtest/deleteSpeedTest",
                        data: {
                            id: id,
                            jobId: <?php echo $jobId; ?>
                        },
                        success: function() {
                            $('table#speedTestHistory tr#' + id).remove();
                            alertify.success("Speedtest successfully deleted.", null);
                        }
                    });
                })
        });

        $('button#editCommentsBtn').on('click', function() {
            var status = $('textarea#comments').attr('disabled');
            if (status == 'disabled') {
                $('textarea#comments').removeAttr('disabled');
            } else {
                $('textarea#comments').attr('disabled', true);
            }
        });

        $('button#saveComments').on('click', function(e) {
            $('#editCommentModal').modal('hide');
            $('textarea#comments').attr('disabled', true);
            e.preventDefault();
            var comments = $('form#commentsForm textarea#comments').val();
            var id = $('form#commentsForm input#id').val();
            $.ajax({
                method: 'post',
                url: "/speedtest/setSpeedTestComments",
                data: {
                    comments: comments,
                    id: id,
                    jobId: <?php echo $jobId;?>
                },
                success: function(data) {
                    if (data == 1) {
                        alertify.success("Comments Successfully Updated", null);
                    } else if (data == 0) {
                        alertify.error("Comments were not updated", null);
                    }
                }
                //end of ajax success function
            });
        });

            var interface = $('select#interface').val();
            console.log(interface);
            if(interface == "wireless"){
                $('div#networkNameDiv').show();
                $('select#networkName').removeAttr('disabled');
                var wirelessNetworkValue = $('select#networkName').val();

                if(wirelessNetworkValue !== "empty"){
                    $('button#runSpeedTest').attr('disabled', false);
                } else {
                    $('button#runSpeedTest').attr('disabled', true);
                }
            }
       
        $('select#interface').on('change', function() {
            var val = $(this).val();
            if (val == "wireless") {
                $('div#networkNameDiv').show();
                $('select#networkName').removeAttr('disabled');
                var wirelessNetworkValue = $('select#networkName').val();

                if(wirelessNetworkValue !== "empty"){
                    $('button#runSpeedTest').attr('disabled', false);
                } else {
                    $('button#runSpeedTest').attr('disabled', true);
                }
            } else if (val == "ethernet") {
                $('div#networkNameDiv').hide();
                $('select#networkName').attr('disabled', true);
                $('button#runSpeedTest').removeAttr('disabled');
            }
        });

        $(document).on('keyup', 'form#speedTestForm input#passphrase', function() {
            var status = $('button#runSpeedTest').attr('disabled');
            var val = $(this).val();
            if (status = "disabled" && val.length >= 8) {
                $('button#runSpeedTest').removeAttr('disabled');
            } else if (status != "disabled" && val.length < 8) {
                $('button#runSpeedTest').attr('disabled', true);
            }
        });

        // When the user selects the wireless network from
        // the list, this code will be triggered
        $(document).on('change', 'select#networkName', function() {

                var wirelessNetworkValue = $('select#networkName').val();

                if(wirelessNetworkValue !== "empty"){
                    $('button#runSpeedTest').attr('disabled', false);
                } else {
                    $('button#runSpeedTest').attr('disabled', true);
                }

            var passField = $('input#passphrase');
            var passExist = passField.length;
            if (passExist == 1) {
                $("div#passwordGroup").html('');
            }
            var val = $(this).val();
            var enc = $(this).find('option:selected').attr('class');
            if (enc == "none" || typeof enc === "undefined" || enc == "off") {
                $('div#passwordGroupOuter').hide();
                $('button#runSpeedTest').removeAttr('disabled');

            } else {

                $.ajax({
                    method: 'post',
                    dataType: 'json',
                    cache: 'false',
                    url: "/wireless/wirelessNetworkConfigurationExists",
                    data: {
                        ssid: val,
                        jobId: <?php echo $jobId ?>
                    },
                    success: function(data) {
                        if (data.status === false) {
                            console.log("In error section of ajax response.");
                            $('input#networkConfiguration').val('false');
                            $('div#passwordGroupOuter').show();
                            var passElem = '<span id="togglePassword" class="input-group-addon" title="Show Password">' +
                                '<i id="passphraseIcon" class="fa fa-eye" ></i></span>' +
                                '<input type="password" id="passphrase" name="passphrase" ' +
                                'class="form-control required"  placeholder="Wireless Password" autocomplete="off" />';
                            $('button#runSpeedTest').attr('disabled', true);
                            $('div#passwordGroup').append(passElem);
                            $('span#helpBlock').html("Encryption Type: " + enc);
                        } else if (data.status === true) {
                            console.log("In success section of ajax response.");
                            $('input#networkConfiguration').val('true');
                            $('div#passwordGroupOuter').hide();
                            $('button#runSpeedTest').removeAttr('disabled');
                            $('span#helpBlock').html("Configuration for this network already exists.");
                        }
                    }
                });
            }
        });

        $(document).on('click', 'span#togglePassword', function() {
            var type = $('#passphrase').attr('type');
            var show = "Show Password";
            var hide = "Hide Password";
            if (type == "text") {
                $('#passphrase').attr('type', 'password');
                $(this).attr('title', show);
                $('#passphraseIcon').attr('class', 'fa fa-eye');
            } else if (type == "password") {
                $('#passphrase').attr('type', 'text');
                $(this).attr('title', hide);
                $('#passphraseIcon').attr('class', 'fa fa-eye-slash');
            }
        });
        // Handle when the users click on the yes run a speed test
        // button.
        
        $(document).on('click', '#runSpeedTest', function(e) {
            e.preventDefault();
            var connection = $('form#speedTestForm select#interface').val();
            $('#speedTestConfirmWrapper').hide();
            if (connection == "wireless") {
                wirelessSpeedTestConfiguration();
            } else {
                runEthernetSpeedTest();
            }
        });
        
    }); //End of document.ready

    function runEthernetSpeedTest() {
        var runningTest = store.get("runningSpeedtest");
        console.log("runEthernetSpeedTest");
        if(runningTest !== "true") {
        var upConnections, downConnections;
        if($('input#upload-connections').val() <= $('input#upload-connections').data('max') && $('input#upload-connections').val() >= $('input#upload-connections').data('min'))
        {
            upConnections = $('input#upload-connections').val();
        } else {
            if($('input#upload-connections').val() == 1) {
                upConnections = 2;
            } else {
                upConnections = $('input#upload-connections').data('default');
            }
        }

        if($('input#download-connections').val() <= $('input#download-connections').data('max') && $('input#download-connections').val() >= $('input#download-connections').data('min'))
        {
            downConnections = $('input#download-connections').val();
        } else {
            if($('input#download-connections').val() == 1)
            {
                downConnections = 2
            } else {
                downConnections = $('input#download-connections').data('default');
            }
        }

        store.set("runningSpeedtest", true);
        $.ajax({
            method: 'post',
            url: "/speedtest/runSpeedTest",
            dataType: 'json',
            data: {
                connection: "ethernet",
                jobId: <?php echo $jobId;?>,
                server: $('input#pspeed-speedtest-server').val(),
                port:$('input#pspeed-speedtest-port').val(),
                "downConnections": downConnections,
                "upConnections": upConnections,
                type: "speedtest",
            },
            success: function(data) {
                store.set("runningSpeedtest", false);
                if(data !== null) {
                    console.log(data);
                    $("#download .ibox-content h2").html(data.DownSpeed);
                    $("#upload .ibox-content h2").html(data.UpSpeed);
                    submitSpeedTest("ethernet");
                } else {
                 
                }
            }
        }); //end of ajax
    } else {
        alertify.error("Speedtest request has already been submitted");
    }
    }

    // Function that handles the neccessary information to run a wireless speed test.

    function wirelessSpeedTestConfiguration() {
        var ssid = $('form select#networkName').val();
        var server = $('form input#speedtest-server').val();
        var pass = $('form input#passphrase').val();
        var enc = $('form select#networkName').find('option:selected').attr('class');
        if (enc === "None" || enc === "none" || enc == "off") {
            runWirelessSpeedTest(ssid);
            return;
        }
        if ($('input#networkConfiguration').val() === "true") {
            runWirelessSpeedTest(ssid);
        } else {
            if(pass !== "" || pass !== null) {
            $.ajax({
                method: 'post',
                url: "/wireless/getPreSharedKey",
                data: {
                    passphrase: pass,
                    ssid: ssid
                },
                success: function(data) {
                    $.ajax({
                        method: 'post',
                        url: "/wireless/savePreSharedKey",
                        data: {
                            conf: data,
                            ssid: ssid
                        },
                        success: function() {
                            runWirelessSpeedTest(ssid);
                        }
                    });
                    //nested ajax function endpoint      
                }
                //end of ajax success function
            });
            //end of ajax
            } else {
                alertify.error("Cannot pass an empty string to passphrase for encrypted network.")
            }
            
        }
    }

    // Function to run a wireless speed test.
    // This will be called by default, and if the network
    // does in fact have encryption, it will be passed to the
    // wirelessSpeedTestConfiguration() function will will then 
    // process the ssid and passphrase in order to be able to connect to the network.
    function runWirelessSpeedTest(ssid) {
        var runningTest = store.get("runningSpeedtest");
        console.log(runningTest);
        console.log("runWirelessSpeedTest");
        if(runningTest !== "true") {
        var upConnections, downConnections;

        if($('input#upload-connections').val() <= $('input#upload-connections').data('max') && $('input#upload-connections').val() >= $('input#upload-connections').data('min'))
        {
            upConnections = $('input#upload-connections').val();
        } else {
            if($('input#upload-connections').val() == 1) {
                upConnections = 2;
            } else {
                upConnections = $('input#upload-connections').data('default');
            }
        }

        if($('input#download-connections').val() <= $('input#download-connections').data('max') && $('input#download-connections').val() >= $('input#download-connections').data('min'))
        {
            downConnections = $('input#download-connections').val();
        } else {
            if($('input#download-connections').val() == 1)
            {
                downConnections = 2
            } else {
                downConnections = $('input#download-connections').data('default');
            }
        }

        store.set("runningSpeedtest", true);
        $.ajax({
            method: 'post',
            url: "/speedtest/runSpeedTest",
            dataType: 'json',
            data: {
                ssid: ssid,
                jobId: <?php echo $jobId; ?>,
                connection: "wireless",
                server: $('input#pspeed-speedtest-server').val(),
                port:$('input#pspeed-speedtest-port').val(),
                "downConnections": downConnections,
                "upConnections": upConnections,                
            },
            success: function(data) {
                store.set("runningSpeedtest", false);
                if(data !== null) {
                    console.log(data);
                    $("#download .ibox-content h2").html(data.DownSpeed);
                    $("#upload .ibox-content h2").html(data.UpSpeed);
                     submitSpeedTest("wireless");
                } else {
                  
                }
            } //end of ajax success function

        }); //end of ajax      
        } else {
            alertify.error("Speedtest request has already been submitted.");
        }                         
    }

    /**
     * This javascript function submits the speedtest results to the database
     * and then returns the information to the history table on the page.
     */
    function submitSpeedTest(type) {
        var upload = $('div#upload h2 #actual-speed').text();
        var uploadUnits = $('div#upload h2 #units').text();
        var download = $('div#download h2 #actual-speed').text();
        var downloadUnits = $('div#download h2 #units').text();
        var ssid = null;
        if (type === "wireless") {
            ssid = $('form select#networkName').val();
        }
        $.ajax({
            dataType: "json",
            method: "post",
            url: "/speedtest/saveInitialSpeedTestResults",
            data: {
                "jobId": <?php echo $jobId; ?>,
                "connection": type,
                "ssid": ssid,
                "upload": upload,
                "uploadUnits": uploadUnits,
                "download": download,
                "downloadUnits": downloadUnits
            },
            success: function(data) {
                console.log(data);
                speedtestTable.row.add({
                    "speedtestid": data.id,
                    "date": data.date,
                    "connection" : data.connection,
                    "ssid":data.ssid,
                    "download": data.download + ' ' + downloadUnits,
                    "upload": data.upload + ' ' + uploadUnits
                }).draw();
                addToProgressBar($('div#speedTestProgressBar'), 0);
                $('div#progressWrapper p#speedTestUpdates').text("Speedtest Complete");
            }
        });
    }

    function retrySpeedTest() {
        clearAlerts();
        var connection = $('form#speedTestForm select#interface').val();
        if (connection == "wireless") {
            wirelessSpeedTestConfiguration();
        } else {
            runEthernetSpeedTest();
        }
    }
    </script>
    <?php else : ?>
<script>
    <?php if ($this->session->userdata('uid') === null) : ?>
            $('#enterAttUid').modal('show');
    <?php endif; ?>
        
 $(document).on('ready',function() {

    $(document).on('click', '#selectJob', function(e) {
        e.preventDefault();
        var target = $(this).data('target');
        var jobId = $('select' + target).val();

        if((typeof jobId == 'undefined') || jobId == 0) {
            $('#selectAJobForm .selectJobWrapper.form-group').toggleClass('has-error');
            $('#selectAJobForm .selectJobWrapper .help-block').text('You must select a job before proceeding.');
            return;
        } else {
             setSessionData("jobId", jobId);          
        }

    });

    $('#selectJobModal').modal('show');
});
        </script>
    <?php endif; ?>
