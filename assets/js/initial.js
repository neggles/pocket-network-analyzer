//==========================================================
    // Functions for initial page testings
    // TODO: Move all functions related to front page below this line
    //      this will ensure maintainability.
    //==========================================================

    $(document).on('ready', function() {

        $('ul#navigation').append('<li><a href="javascript:void(0);" class="gotoMainView">Main View</a></li>');
        //$('ul#navigation').append('<li><a href="#" onclick="javascript:createAPdfReport();">PDF Report</a></li>');
        $('ul#navigation').append('<li><a href="javascript:void(0);" onclick="javascript:resetNetworks();"><i class="fa fa-refresh"></i> Refresh Networks</a></li>');
        //$('ul#navigation').append('<li><a href="javascript:void(0);" onclick="javascript:resetNetworks();"><i class="fa fa-refresh"></i> Refresh Networks</a></li>');


        $(document).on('click', '.gotoMainView', function(e) {

            e.preventDefault();

            var jobId = (typeof $('input#intialTestJobId') == 'undefined') ? $('input#intialTestJobId') : 0;

            setSessionData("jobId", jobId);

            window.location = window.location.href + 'home';
        })


        $(document).on('click', '.toggle', function(e) {
            e.preventDefault();
            var toggleObject = $(this).closest('button').attr('data-toggle');

            $(toggleObject).toggle();
        })

        // When the ethernet is not plugged in initially
        // the user will be prompted to plug in the ethernet and 
        // then press a button.
        // That button's actions are handled here.
        $(document).on('click', 'button#reconfirmEthernet', function() {

            $('div#ethernetWarning').remove();

            $('div#workFlowTest ul#testsComplete').html('');
            $('i#networkTestSpinner').show();
            $('div#workFlowTest span#testResults p').html('Testing for ethernet link...');
            console.log('Testing for ethernet connection again.');
            performNetworkFunctionsCheck();
        });

        // Action that happens when the user selects the appropriate
        // job from the dropdown list
        $(document).on('click', '#selectJob', function(e) {

            

       var target = $(this).data('target');
       var jobId = $('select' + target).val();
            //window.location = '<?php echo site_url() ?>'+id;
            console.log("Selected Job Id: " + jobId);
            if (jobId != 0) {
                $('#initialView').hide();
                selectedJobDetails(jobId);
                $('#secondView').show();
            } else {
                $('select#newJobSelection').parent('div.form-group').addClass('has-error');
                $('select#newJobSelection').parent('div.form-group').append('<span class="help-block">You must select a job before proceeding.</span>');
            }

        });

        $(document).on('click', 'button#skipWirelessSpeedTest', function() {

            $('button#skipWirelessSpeedTest').hide();
            clearAlerts();
            console.log("User opted to skip wireless test");
            runEthernetSpeedTest();
        });


        $(document).on('click', 'button#proceedToTest', function(e) {
            e.preventDefault();
            $('#secondView').hide();
            $('#thirdView').show();
            checkIfWirelessNetworkConfigurationExists(processWirelessNetworkConfiguration);
            performNetworkFunctionsCheck();
        });

        // When the user selects the wireless network from
        // the list, this code will be triggered
        $(document).on('change', 'form#wirelessNetworkForSpeedTestForm select#networkName', function() {
            var passField = $('form#wirelessNetworkForSpeedTestForm input#passphrase');
            var passExist = passField.length;

            if (passExist == 1) {
                $("form#wirelessNetworkForSpeedTestForm div#passwordGroup").html('');
            }


            var enc = $('form#wirelessNetworkForSpeedTestForm select#networkName').find('option:selected').attr('class');

            if (enc == "none" || typeof enc === "undefined") {
                $('form#wirelessNetworkForSpeedTestForm div#passwordGroupOuter').hide();
                $('form#wirelessNetworkForSpeedTestForm div#saveButtonDiv').hide();
                $('form#wirelessNetworkForSpeedTestForm span#helpBlock').html("This is an open network, no configuration needed.");
                $('div#confirmWirelessNetwork').show();
            } else {
                checkIfWirelessNetworkConfigurationExists(processWirelessNetworkConfiguration);
            }
        });




        // When the user clicks on the confirm button, 
        // we will call the beginRunningTests function.
        $(document).on('click', 'button#confirmWirelessNetworkButton', function(e) {
            e.preventDefault();
            var ssid = $('form#wirelessNetworkForSpeedTestForm select#networkName').val();
            $('input#wirelessSpeedTestNetworkName').val(ssid);
            setSessionData("wirelessSpeedTestNetworkName", ssid);
            beginRunningTests();
            return false;
        });


        // Make sure that when the user is inputting a password, it must be atleast 8 characters long
        // 
        $(document).on('keyup', 'form#wirelessNetworkForSpeedTestForm input#passphrase', function() {
            var status = $('button#submitWirelessConfiguration').attr('disabled');
            var val = $(this).val();

            if (status = "disabled" && val.length >= 8) {
                $('button#submitWirelessConfiguration').removeAttr('disabled');
            } else if (status != "disabled" && val.length < 8) {
                $('button#submitWirelessConfiguration').attr('disabled', true);
            }
        });



        $(document).on('click', 'button#retrySpeedTest', function() {
            clearAlerts();
            $('button#skipWirelessSpeedTest').hide();

            console.warn("Retrying speedtest now.");

            $.ajax({
                method: 'post',
                url: "speedtest/runSpeedTest",
                data: {
                    ssid: $('input#wirelessSpeedTestNetworkName').val(),
                    id: $('input#intialTestJobId').val(),
                    connection: $('input#speedtestInterface').val(),
                    server: $('input#speedtest-server').val()
                },
                success: function(data) {

                        console.log(data);

                    } //end of ajax success function
            }); //end of ajax 

        });


        $(document).on('click', 'button#wirelessSpotCheckButton', function(e) {
            e.preventDefault();
            $('div#spotCheckFormWrapper').toggle();
        });

    });
    //Closing document.ready tag

    function createAPdfReport() {

        var htmlCode = escape("<html><head>" + $('head').html() +
            $('div#javscriptContent').html() + $('#tailScripts').html() + "</head><body>" +
            "<div class='container'> " +
            $(".content").html() +
            "</div></body></html>");

        $.ajax({
            dataType: "json",
            method: "post",
            url: "initial/createHtmlPage",
            data: {
                "html": htmlCode
            },
            success: function(data) {
                console.log(data);
                if (data.status == true) {
                    $.ajax({
                        method: "post",
                        url: "initial/createAPdf",
                        data: {
                            "job": $('input#intialTestJobId').val(),
                            "url": data.msg,
                            "type": "url"
                        },
                        success: function(data) {
                            console.log(data);

                        }
                    });

                }

            }
        });
    }

    function submitSpeedTest(type) {

        var jobId = $('input#intialTestJobId').val();
        var upload = $('p#' + type + 'Upload').text();
        var download = $('p#' + type + 'Download').text();
        var ssid = null;

        if (type === "wireless") {
            ssid = $('input#wirelessSpeedTestNetworkName').val();
        }

        $.ajax({
            dataType: "json",
            method: "post",
            url: "speedtest/saveInitialSpeedTestResults",
            data: {
                "jobId": jobId,
                "connection": type,
                "ssid": ssid,
                "upload": upload,
                "download": download
            },
            success: function(data) {
                console.log(data);
            }
        });

    }


    function runBandwidthTest() {

        $('div#channelChartInfo').hide();

        $('button#backToStepTwo').hide();
        $('canvas#channelUtilizationChart').hide();
        var time = $('form#wirelessChannelScan input#scanTime').val();
        var content = '<div id="runningBandwidthTest" class="text-center">' +
            '<i class="fa fa-cog fa-spin fa-5x"></i>' +
            '<p>Currently running the channel utilization program. </br> This could take upto 60 seconds.</p>' +
            '</div>';

        $('#channelUtilizationElement div#contentLoading').append(content);

        console.log("Running a bandwidth test on the wireless channels.");
        $.ajax({
            dataType: "json",
            method: "post",
            data: {
                "time":time
            },
            url: "wireless/runBandwidthTest",
            success: function(data) {
                console.log(data);
                displayChannelChart(data);
            }
        });

        initialTestsComplete();
    }


    function initialTestsComplete() {

        $('div#thirdView').hide();
        $('div#completeView').show();
    }

    // This function serves the purpose of checking the status of 
    // the ethernet interface. It will first check if the port is active 
    // or plugged in, it will then check if it has an ip address
    // assigned.

    function performNetworkFunctionsCheck() {
        //Test for ethernet cable plugged in.
        $.ajax({
            method: 'post',
            url: "diagnostics/checkEthernetStatus",
            data: {},
            success: function(data) {
                console.log("Ethernet Network Status: "+ data);
                if (data == "up" || data == 1) {
                    addCommentToLog('ethernetInterfaceStatus', 'eth0 is active...', 'success');

                    $('div#workFlowTest span#testResults p').html('Testing for ethernet ip address...');

                    addCommentToLog('ethernetInterfaceIpAddressTest', 'testing for ethernet ip address... ');
                    //Test for ethernet IP address.
                    $.ajax({
                        method: 'post',
                        url: "diagnostics/checkEthernetAddress",
                        data: {},
                        success: function(data) {

                            if (data) {

                                addCommentToLog('ethernetInterfaceIpAddress', 'eth0 ip address: ' + data, 'success');

                                addCommentToLog('testingWirelessNetworks', 'testing for wireless networks... ');

                                $('div#workFlowTest span#testResults p').html('Testing for wireless networks...');
                                performWirelessFunctionsCheck();
                            } else {
                                addCommentToLog('ethernetInterfaceIpAddress', 'eth0 is not assigned an ip address... ', 'danger');
                                $('div#workFlowTest span#testResults p').html('Testing for wireless networks...');
                                performWirelessFunctionsCheck();
                            }
                        }
                    }); //end of ajax
                } else {

                    addCommentToLog('ethernetInterfaceStatus', 'eth0 is not active...');

                    addCommentToLog('ethernetTest', 'Without the ethernet cable plugged in we will not be able to run a speedtest on that interface.', 'danger');

                    $('i#networkTestSpinner').hide();
                    $('div#workFlowTest span#testResults p').html('');
                    performWirelessFunctionsCheck();
                }
            }
        }); //end of ajax

    }

    function checkIfWirelessNetworkConfigurationExists(callback) {
        var jobId = $('input#intialTestJobId').val();
        var ssid = $('form#wirelessNetworkForSpeedTestForm select#networkName').val();
        if (ssid != "") {
            $.ajax({
                method: 'post',
                dataType: 'json',
                cache: 'false',
                url: "/speedtest/wirelessNetworkConfigurationExists",
                data: {
                    "ssid": ssid,
                    "job": jobId
                },
                success: function(data) {

                    callback(data);
                }

            });
        }
    }


    // Function that handles the running of each test
    // this function is called once the user confirms
    // the wireless network that they want to use 
    // for the speed test.
    function beginRunningTests() {
        $('ul#testsComplete').html('');
        $('div#chooseWirelessNetwork').hide();

        runEthernetSpeedTest();
    }


    // Function to run a wireless speed test.
    // This will be called by default, and if the network
    // does in fact have encryption, it will be passed to the
    // wirelessSpeedTestConfiguration() function will will then 
    // process the ssid and passphrase in order to be able to connect to the network.
    function runWirelessSpeedTest() {

        $('input#speedtestInterface').val('wireless');

        console.log("Starting the wireless speedtest, function runWirelessSpeedTest()");

        storeItemInLocalStorage('speedtestInterface', 'wireless');

        setSessionData("speedTestInterface", "wireless");

        var pleaseWait = '<div class="waitSpeedTest"><i class="fa fa-cog fa-spin fa-5x"></i>' +
            ' <p>Please wait while we attempt to retrieve the speedtest configuration.</p></div>';

        $('div#testResultsContent').append(pleaseWait);

        $.ajax({
            method: 'post',
            url: "speedtest/runSpeedTest",
            data: {
                ssid: $('input#wirelessSpeedTestNetworkName').val(),
                id: $('input#intialTestJobId').val(),
                connection: "wireless",
                server: $('input#speedtest-server').val()
            },
            success: function(data) {
                console.log(data);

                } //end of ajax success function
        }); //end of ajax                               
    }

    function runEthernetSpeedTest() {

        $('div#retryButtonWrapper').hide();

        console.log("Starting ethernet speed test, function runEthernetSpeedTest()");

        storeItemInLocalStorage('speedtestInterface', 'ethernet');

        $('input#speedtestInterface').val('ethernet');

        setSessionData("speedTestInterface", "ethernet");

        var pleaseWait = '<div class="waitSpeedTest"><i class="fa fa-cog fa-spin fa-5x"></i>' +
            ' <p>Please wait while we attempt to retrieve the speedtest configuration.</p></div>';

        $('div#testResultsContent').append(pleaseWait);

        $.ajax({
            method: 'post',
            url: "speedtest/runSpeedTest",
            data: {
                id: $('input#intialTestJobId').val(),
                connection: "ethernet",
                server: $('input#speedtest-server').val()
            },
            success: function(data) {
                console.log(data);

                } //end of ajax success function
        }); //end of ajax                               
    }


    function goBack(current, location) {
        $(current).hide();
        $(location).show();
        $("ul#testsComplete").html('');
    }

    function displayChannelChart(data) {

        var channelChart = document.getElementById("channelUtilizationChart").getContext("2d");
        var canvas = document.getElementById("channelUtilizationChart");
        channelChart.clearRect(0, 0, canvas.width, canvas.height);

        var channelList = data;

        var channelLabels = new Array();

        var channelPackets = new Array();

        for (var i = 0; i < channelList.length; i++) {
            channelLabels.push(channelList[i].channel);
            channelPackets.push(channelList[i].packets);
        }
        $('div#runningBandwidthTest').remove();
        $('canvas#channelUtilizationChart').show();
        $('#channelUtilizationElement button').show();

        var channelChartData = {
            labels: channelLabels,
            datasets: [{
                label: "Channel Utilization",
                fillColor: "rgba(3,178,20,0.2)",
                strokeColor: "rgba(3,178,20,1)",
                pointColor: "rgba(3,178,20,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(151,187,205,1)",
                data: channelPackets
            }]
        };



        window.myBar = new Chart(channelChart).Bar(channelChartData, {
            responsive: true,
            tooltipTemplate: "<%if (label){%>Channel <%=label%>: <%}%><%= value %> packets",
        });
    }

    function setSessionData(dataKey, dataValue) {

        $.ajax({
            method: 'post',
            url: "initial/saveSessionData",
            data: {
                "key": dataKey,
                "value": dataValue
            },
            success: function(data) {


                } //end of ajax success function
        }); //end of ajax 

    }

    function getSessionData(dataKey) {
        $.ajax({
            method: 'post',
            url: "initial/getSessionData",
            data: {
                "key": dataKey
            },
            success: function(data) {
                    console.log(data);
                    return data;
                } //end of ajax success function
        }); //end of ajax     
    }

    function runAnotherSpotCheck() {
        $('#runAnotherSpotCheck').toggle();
        $('#spotCheckFormWrapper').toggle();
    }

    function endTest() {
        $('#runAnotherSpotCheck').toggle();
        return false;
    }

    function uploadSpotCheckSpeedTest() {

        var ssid = $('table#wirelessSpotCheckTable tbody tr:last td.spotCheckNetworkName').text();
        var location = $('table#wirelessSpotCheckTable tbody tr:last td.spotCheckRoom').text();
        var download = $('table#wirelessSpotCheckTable tbody tr:last td.spotCheckDownload').text();
        var upload = $('table#wirelessSpotCheckTable tbody tr:last td.spotCheckUpload').text();
        var job = $('input#initialTestJobId').val();

        $.ajax({
            method: 'post',
            url: "speedtest/saveInitialSpeedTestResults",
            data: {
                job: job,
                connection: "wireless",
                server: "",
                ssid: ssid,
                location: location,
                download: download,
                upload: upload
            },
            success: function(data) {

                    console.log(data);
                } //end of ajax success function
        }); //end of ajax      

    }

    function submitSpotCheck() {

        $('#spotCheckFormWrapper').hide();

        $('input#speedtestInterface').val('spotcheck');

        storeItemInLocalStorage('speedtestInterface', 'spotcheck');

        storeItemInLocalStorage("spotCheckRoom", $('#spotCheckForm input#roomName').val());

        setSessionData("speedTestInterface", "spotcheck");

        setSessionData("spotCheckRoom", $('#spotCheckForm input#roomName').val());

        $.ajax({
            method: "post",
            url: "speedtest/runWirelessSpotCheck",
            data: {
                "jobId": $('input#intialTestJobId').val(),
                "location": $('#spotCheckForm input#roomName').val(),
                "ssid": $('input#wirelessSpeedTestNetworkName').val(),
                "connection": "wireless",
                "type": "spotcheck"
            },
            success: function(data) {
                console.log(data);
            }
        });

    }
