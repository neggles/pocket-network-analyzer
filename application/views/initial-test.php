<div id="page-wrapper" class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div role="main">
            <div class="main-content">
                <div class="content">
                    <div class="row">
                        <!--/.row-->
                        <div class="col-md-4" id="leftSideContent">
                            <div id="networkTestLog">
                                <div class="list-group">
                                </div>
                            </div> 
                        </div>
                         <!--#mainPageInitialization-->
                        <div id="mainPageInitialization" class="col-lg-4">
                            <div class="ibox float-e-margins">
                                        <div class="ibox-title">
                <h5>Initial Testing Workflow 
                    <a href="#" data-toggle="modal" data-target="#helpModal">
                        <i class="fa fa-question"></i>
                    </a>
                </h5>
            </div>
                            <div id="alerts">
                                <div class="alert alert-danger" id="dangerAlerts" style="display:none;">
                                </div>
                                <div class="alert alert-warning" id="warningAlerts" style="display:none;">
                                </div>
                                <div class="alert alert-success" id="successAlerts" style="display:none;">
                                </div>
                            </div>
                            <div class="ibox-content">                            
            <!--#initialView -->
                <?php $this->load->view('initial/initial_view');?>
            <!--/#initialView -->

            <!--#secondView-->
                <?php $this->load->view('initial/second_view');?>
            <!--/#secondView-->

            <!--#thirdView-->
                <?php $this->load->view('initial/third_view');?>
            <!--/#thirdView-->

            <!--#completeView-->
            <?php $this->load->view('initial/complete_view');?>
            <!--/#completeView-->
                            <div class="initialTestConfigurationSettings">
                                <input type="hidden" id="networkConfiguration" name="networkConfiguration" value="">
                                <input type="hidden" id="wirelessSpeedTestNetworkName" name="wirelessSpeedTestNetworkName" value="">
                                <input type="hidden" id="intialTestJobId" name="intialTestJobId" value="5">
                                <input type="hidden" id="speedtestInterface" name="speedtestInterface" value="">
                            </div>
                        </div>
             <!--/#mainPageInitialization-->

             <!--speedtest results-->
            <?php $this->load->view('initial/speedtest_view');?>
            <!--/speedtest results-->
                        </div>
                        </div>
                    </div>

            <!--network table-->
                <?php $this->load->view('initial/networktable_view');?>
            <!--/network table-->


            <!--network signal chart-->
                <?php $this->load->view('initial/networksignalchart_view');?>
            <!--/network signal chart-->
                </div>
            </div>
        </div>
    </div>
</div>
<div id="tailScripts">
    <script>
    $(document).on('ready', function() {
        // ensure that a network scan has been conducted and display the results
        // in the table, and populate the neccessary information.
        runNewNetworkScan();
        // Subscribe to notifications that are being fed from the speed test.
        var speedtestNotificationsChannel = pusher.subscribe('speedtest');
        var spotCheckNotificationsChannel = pusher.subscribe('spotcheck');
        speedtestNotificationsChannel.bind('no-network', function(notification) {
            var message = notification.message;
            //alertify.log(message);
            var retryButtonExists = $('div#retryButtonWrapper').length;
            if ($('input#speedtestInterface').val() == "wireless") {
                var wirelessMessage = 'It is possible that this wireless network is not connected to the internet.';
                processAlerts($('div#alerts div#dangerAlerts'), wirelessMessage, 'show');
            }
            if ($('input#speedtestInterface').val() == "ethernet") {
                var ethernetMessage = '<strong><p>It appears that there is not network '+
                'connection on your network.</p>' +
                '<p class="small"> In the mean time you could run a site survey.</p></strong>';                 
                processAlerts($('div#alerts div#dangerAlerts'), ethernetMessage, 'show');
            }

            $('div.waitSpeedTest').remove();

            //Check if the button already exists, if so do not add it again.
            if (!retryButtonExists > 0) {
                var buttonContent = '<div id="retryButtonWrapper"><button type="button" class="btn btn-danger" id="retrySpeedTest">Retry</button></div>';
                $('div#testResultsContent').append(buttonContent);

                if ($('input#speedtestInterface').val() == "wireless") {
                    var wirelessButtonContent = '<button type="button" class="btn btn-primary" id="skipWirelessSpeedTest">Skip Wireless Speedtest</button>';
                    $('div#testResultsContent div#retryButtonWrapper').append(wirelessButtonContent);
                }
                else if( $('input#speedtestInterface').val() == "ethernet" ) {
                    var skipEthernet = '<button type="button" class="btn btn-danger" onclick="javascript:runWirelessSpeedTest();">Skip Ethernet Test</button>';
                    $('div#testResultsContent div#retryButtonWrapper').append(skipEthernet);
                }
            } else {
                $('div#retryButtonWrapper').show();

                if ($('input#speedtestInterface').val() == "ethernet") {
                    var ethernetButtonContent = '<button type="button" class="btn btn-primary" id="runSiteSurvey">Site Survey</button>';
                    $('div#testResultsContent div#retryButtonWrapper').append(ethernetButtonContent);
                    var skipEthernet = '<button type="button" class="btn btn-danger" onclick="javascript:runWirelessSpeedTest();">Skip Ethernet Test</button>';
                    $('div#testResultsContent div#retryButtonWrapper').append(skipEthernet);
                }
            }
            console.log(message);
            addCommentToLog('', message, 'danger');
        });

        speedtestNotificationsChannel.bind('bad-network-config', function(notification) {
            var message = notification.message;
            addToProgressBar($('div#speedTestProgressBar'), 0);
            $('div#progressWrapper').hide();
            $('div.waitSpeedTest').remove();
            console.log(message);
        });

        speedtestNotificationsChannel.bind('update', function(notification) {
            var message = notification.message;
            // alertify.log(message);
            var $speedTestProgress = $('div#speedTestProgressBar').attr('aria-valuenow');
            var $newSpeedTestProgress = Number($speedTestProgress) + 15;
            addToProgressBar($('div#speedTestProgressBar'), $newSpeedTestProgress);
            $('div#testResultsContent div#retryButtonWrapper').hide();          
            $('div#progressWrapper p#speedTestUpdates').text(message);
        console.log(message);
        });

        speedtestNotificationsChannel.bind('error', function(notification) {
            var message = notification.message;
            console.error(message);
            processAlerts($('div#alerts div#dangerAlerts'), message, 'show');
        });

        speedtestNotificationsChannel.bind('success', function(notification) {
            var message = notification.message;
            console.log(message);
            //alertify.log(message);
            if (message == "running") {
                $('div#testResultsContent div#retryButtonWrapper').hide();                
                $('div#progressWrapper p#speedTestUpdates').text('Starting speedtest...');
                $('div#speedTestResults').show();
                $('div.waitSpeedTest').remove();
                $('button#retrySpeedTest').remove();
                clearAlerts();
                var progressBar = '<div id="progressWrapper"><p>Speed Test Progress</p>' +
                    '<div class="progress" >' +
                    '<div id="speedTestProgressBar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100" style="width: 5%">' +
                    '<span class="sr-only">5% Complete</span></div></div><p class="small" id="speedTestUpdates"></p></div>';
                $('div#testResultsContent').append(progressBar);
            } else {
                processAlerts($('div#alerts div#successAlerts'), message, 'show');
            }
        });

        speedtestNotificationsChannel.bind('download-speed', function(notification) {
            addToProgressBar($('div#speedTestProgressBar'), 75);
            var message = notification.message;
            console.log('Download Speed: ' + message);
            if ($('input#speedtestInterface').val() !== "spotcheck") {
                $("#" + $('input#speedtestInterface').val() + " .panel-body p#" + $('input#speedtestInterface').val() + "Download").html("Download: " + message + "/s");
            } else if ($('input#speedtestInterface').val() === "spotcheck") {
                $('table#wirelessSpotCheckTable tbody tr:last td.spotCheckDownload').text(message + "/s");
            }
        });

        speedtestNotificationsChannel.bind('upload-speed', function(notification) {
            addToProgressBar($('div#speedTestProgressBar'), 100);
            var message = notification.message;
            console.log('Upload Speed: ' + message);
            if ($('input#speedtestInterface').val() !== "spotcheck") {
                $("#" + $('input#speedtestInterface').val() + " .panel-body p#" + $('input#speedtestInterface').val() + "Upload").html("Upload: " + message + "/s");        
            $('div#progressWrapper').remove();        
                //Call the javascript function to submit the speed test results
                submitSpeedTest($('input#speedtestInterface').val());
            }

            if ($('input#speedtestInterface').val() === "wireless") {
                 runBandwidthTest();                
            } else if ($('input#speedtestInterface').val() === "ethernet") {
               runWirelessSpeedTest();
            } else if ($('input#speedtestInterface').val() === "spotcheck") {
                $('table#wirelessSpotCheckTable tbody tr:last td.spotCheckUpload').text(message + "/s");
                $('div#completeView div#progressWrapper').remove();
                $('#runAnotherSpotCheck').toggle();
                uploadSpotCheckSpeedTest();
            }
        });

        /*
         * Beginning Spot Check section
         */

        spotCheckNotificationsChannel.bind('error', function(notification) {
            var message = notification.message;
            console.error(message);
            var retryButtonExists = $('#retrySpotCheck').length;
            processAlerts($('div#alerts div#dangerAlerts'), message, 'show');
            if (!retryButtonExists > 0) {
                var buttonContent = '<button type="button" class="btn btn-danger" id="retrySpotCheck" onclick="javascript:submitSpotCheck();">Retry</button>';
                $('div#spotCheckButtonWrapper').append(buttonContent).toggleClass('btn-group');
            } else {
                $('#retryButtonWrapper').show();
            }
        });
        spotCheckNotificationsChannel.bind('success', function(notification) {
            var message = notification.message;
            console.log(message);
            if (message == "running") {
                $('div#progressWrapper p#speedTestUpdates').text('Starting speedtest...');
                $('div.waitSpeedTest').remove();
                $('button#retrySpeedTest').remove();
                clearAlerts();

                if (!$('div#leftSideContent table#wirelessSpotCheckTable').length > 0) {
                    var tableContent = '<table class="table table-striped" id="wirelessSpotCheckTable"><thead>' +
                        '<tr>' +
                        '<th>Network</th>' +
                        '<th>Room</th>' +
                        '<th>Download</th>' +
                        '<th>Upload</th>' +
                        '</tr></thead><tbody></tbody></table>';

                    $('div#leftSideContent').html(tableContent);
                }

                var tableRow = '<tr>' +
                    '<td class="spotCheckNetworkName">' + $('input#wirelessSpeedTestNetworkName').val() + '</td>' +
                    '<td class="spotCheckRoom">' + $('#spotCheckForm input#roomName').val() + '</td>' +
                    '<td class="spotCheckDownload"></td>' +
                    '<td class="spotCheckUpload"></td>' +
                    '</tr>';

                $('div#leftSideContent table tbody').append(tableRow);

                var progressBar = '<div id="progressWrapper" class="text-center"><p>Speed Test Progress</p>' +
                    '<div class="progress" >' +
                    '<div id="speedTestProgressBar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100" style="width: 5%">' +
                    '<span class="sr-only">5% Complete</span></div></div><p class="small" id="speedTestUpdates"></p></div>';

                $('div#completeView').append(progressBar);

            } else {
                processAlerts($('div#alerts div#successAlerts'), message, 'show');
            }

        });

        spotCheckNotificationsChannel.bind('update', function(notification) {
            var $speedTestProgress = $('div#speedTestProgressBar').attr('aria-valuenow');
            var $newSpeedTestProgress = Number($speedTestProgress) + 15;
            addToProgressBar($('div#speedTestProgressBar'), $newSpeedTestProgress);
            var message = notification.message;
            console.log(message);
            $('div#progressWrapper p#speedTestUpdates').text(message);
        });
    });
    </script>
</div>
<?php $this->load->view('modals/networkConfigurationModal');?>
<?php $this->load->view('modals/helpModal');?>
