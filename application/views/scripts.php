<div id="javscriptContent">
    <script src="<?php echo $this->config->item('plugins_directory');?>jquery/dist/jquery.min.js"></script>
    <script src="/assets/js/customJavascript.min.js?v=<?php echo time();?>"></script>
    <script src="<?php echo $this->config->item('plugins_directory');?>jquery-validation/dist/jquery.validate.min.js"></script>
</div>
<?php if (isset($status) && $status == 'initial-test') : ?>
<script src="/assets/js/initial.js?v=<?php echo time();?>"></script>
<?php endif; ?>

<script>
url = window.location.href;

var pfiObject = {
    expiration: new Date().getTime() + <?php echo $this->config->item('global_javascript_expiration'); ?>,
    jobId: <?php echo isset($currentJob->id) ? $currentJob->id : 0; ?>

};

function setSessionData(dataKey, dataValue) {
    store.set(dataKey, dataValue);
    $.ajax({
        method: 'post',
        url: "/job/saveSessionData",
        data: {
            "key": dataKey,
            "value": dataValue
        },
        success: function(data) {
            console.log(data);
            window.location.reload();
        }
    });
}

$(document).on('ready', function() {

    <?php if ($this->config->item('pusher_enabled')) : ?>

    var updateChannel = pusher.subscribe('update');

    updateChannel.bind('needs_restart', function(notification) {
        var message = notification.message;
     
                            swal({
                                title: "Restart Required",
                                text: "Latest update requires a system restart to function properly, we will restart now <i class='fa fa-cog fa-spin fa-4x'></i>",
                                html: true,
                                showConfirmButton: false
                            });


    });


    updateChannel.bind('needsupdate', function(notification) {
        var message = notification.message;

        var obj = jQuery.parseJSON(message);

        lastCheck = store.get("updateCheck");
        

    var now = new Date();

    if(lastCheck !== null){
        var then = new Date(lastCheck);
    } else {
        var then = now;
    }
    var timeGap = diff_minutes(now, then);

    if(lastCheck === null || timeGap > 360) {

        alertify.error(obj.msg);
    }
        console.log(obj);
        $('a#dropdown-menu-toggle').addClass('count-info');
        $('a#dropdown-menu-toggle').append('<span class="label label-danger">*</span>');
        $('li#version-wrapper').append('<a class="btn btn-xs btn-danger" id="updateToNewest" href="#">' + obj.version + '</a>');
    });


    updateChannel.bind('needsupdate_force', function(notification) {
        var message = notification.message;

        var obj = jQuery.parseJSON(message);
        alertify.error(obj.msg);
        console.log(obj);
        $('a#dropdown-menu-toggle').addClass('count-info');
        $('a#dropdown-menu-toggle').append('<span class="label label-danger">*</span>');
        var update = $('a#updateToNewest');
        if(update.length !== 0) {
            $('a#updateToNewest').text(obj.version);
        } else {
            $('li#version-wrapper').append('<a class="btn btn-xs btn-danger" id="updateToNewest" href="#">' + obj.version + '</a>');
        }
    });

    updateChannel.bind('uptodate', function(notification) {
        var message = notification.message;
        var obj = jQuery.parseJSON(message);
        console.log(obj);
    });

    updateChannel.bind('success', function(notification) {
        var message = notification.message;
        var obj = jQuery.parseJSON(message);
        alertify.success(obj.msg);
        console.log(obj);
    });

    updateChannel.bind('progress', function(notification) {
        var message = notification.message;
        var obj = jQuery.parseJSON(message);
        alertify.success(obj.msg);
        console.log(obj);
    });

    updateChannel.bind('complete', function(notification) {
        var message = notification.message;
        var obj = jQuery.parseJSON(message);
        alertify.success(obj.msg);
        console.log(obj);
        window.location.reload();        
    });



    //The base url for the current page, which
    //will be needed by multiple scripts throughout the page.

    // Subscribe to the default note channel for notifications
    var notificationsChannel = pusher.subscribe('note');

    notificationsChannel.bind('connecting', function(notification) {
        var message = notification.message;

        var obj = jQuery.parseJSON(message);

        $('footer div#connectionIssues ul.list-group').append('<li class="list-group-item">' + obj + '</li>');

        console.log(message);
    });

    notificationsChannel.bind('new_note', function(notification) {
        var message = notification.message;
        console.log(message);
        alertify.log(message);
    });

    // Subscribe to the wireless channel
    var wirelessNotificationsChannel = pusher.subscribe('wireless');
    // receive messages for success messages on wireless

    wirelessNotificationsChannel.bind('success', function(notification) {
        var message = notification.message;
        console.log(message);
        if (typeof message !== 'undefined') {
            alertify.success(message);
        }
    });

    wirelessNotificationsChannel.bind('complete_connection', function(notification) {
        var message = notification.message;
        var obj = jQuery.parseJSON(message);
        console.log(obj);

        if (typeof widarNetwork === 'function') {
            widarNetwork(obj);
        }
        if (typeof message !== 'undefined') {
            //alertify.log(message);
        }

    });

    wirelessNotificationsChannel.bind('status', function(notification) {
        var message = notification.message;
        console.log(message);
        if (typeof message !== 'undefined') {
            alertify.log(message);
        }
    });

    wirelessNotificationsChannel.bind('error', function(notification) {
        var message = notification.message;
        console.error(message);
        if (typeof message !== 'undefined') {
            alertify.error(message);
        }
    });

    <?php endif;?>

    $(document).on('click', '#updateToNewest', function() {
        $.ajax({
            method: "post",
            dataType: "json",
            url: "/version/runUpdate",
            success: function(data) {
                alertify.log(data);
            }
        }); //end of ajax   
    })

    // When a user clicks the shutdown button from the navigation dropdown
    // this ajax call will shutdown the giga-fi machine
    $(document).on('click', '#shutdown', function() {
        swal({
                title: "Shutdown",
                text: "Are you Sure?",
                showCancelButton: true,
                closeOnConfirm: true,
                confirmButtonText: "Shutdown",
                animation: "slide-from-top",
            },
            function() {
                if (window.navigator && window.navigator.vibrate) {
                    navigator.vibrate(1000);
                }
                $.ajax({
                    method: "post",
                    dataType: "json",
                    url: "/home/shutdown",
                    success: function(data) {
                        if (data.status === 'error') {
                            alertify.error(data.msg);
                        } else {
                            $('body').html('<div class="alert alert-danger">POCKET-FI is shutting down.</div>');
                        }
                    }
                }); //end of ajax           
            })
    });

    // When a user clicks the restart button from the navigation dropdown
    // this ajax call will restart the giga-fi machine
    $(document).on('click', '#restart', function() {
        swal({
                title: "Restart",
                text: "Are you Sure?",
                showCancelButton: true,
                closeOnConfirm: true,
                confirmButtonText: "Restart",
                animation: "slide-from-top",
            },
            function() {
                if (window.navigator && window.navigator.vibrate) {
                    navigator.vibrate(1000);
                }
                $.ajax({
                    method: "post",
                    url: "/home/restart",
                    success: function(data) {
                        if (data.status == 'error') {
                            alertify.error(data.msg);
                        } else {
                            $('body').html('<div class="alert alert-danger">POCKET-FI is restarting.</div>');
                        }
                    }
                }); //end of ajax           
            })

    });



    //End of the document.ready wrapper



    $(document).on('click', '.clearNetworkCache', function(e) {
        e.preventDefault();

        $.ajax({
            method: 'post',
            url: "/wireless/clearCache",
            data: {
                "item": "current_wifi_networks_available"
            },
            success: function(data) {
                console.log("Cleared network cache.");
                resetNetworkList();
            }
        }); //end of ajax

    })
});

function addToProgressBar(element, progress) {
    element.find('span.sr-only').text(progress + "% Complete");
    element.attr('aria-valuenow', progress);
    element.width(progress + "%");
}

function clearAlerts() {
    $('div#dangerAlerts').hide().html('');
    $('div#warningAlerts').hide().html('');
    $('div#successAlerts').hide().html('');
}

function processAlerts(element, message, action) {
    if (action == "show") {
        element.append('<p>' + message + '</p>').show();
    } else if (action == "hide") {
        element.html('').hide();
    }
}

/*
 * ====================================================
 * 
 *     Wireless related javascript functions
 *
 * ====================================================
 */

function resetNetworks() {
    clearFromStorage("wirelessNetworks");
    runNewNetworkScan();
}

function runNewNetworkScan() {

    $.ajax({
        dataType: "json",
        method: "post",
        url: "/network/runNewNetworkScanAnonymous",
        success: function(data) {
            processWirelessNetworksTable(data);
        }
    });
}

function disconnectFromWirelessNetwork() {
    $.ajax({
        dataType: "json",
        method: "post",
        url: "/wireless/disconnectFromWirelessNetwork",
        success: function(data) {
           console.log(data);
           window.location.reload();
        }
    });
}

function processWirelessNetworkConfiguration(data) {

    if (data.status === false) {

        $('div#confirmWirelessNetwork').hide();
        $('input#networkConfiguration').val('false');
        var enc = $('form#wirelessNetworkForSpeedTestForm select#networkName').find('option:selected').attr('class');

        if (typeof enc !== 'undefined' && enc != "") {
            $('form#wirelessNetworkForSpeedTestForm span#helpBlock').html("Encryption Type: " + enc);
            $('form#wirelessNetworkForSpeedTestForm div#passwordGroupOuter').show();
            var passElem = '<input type="text" id="passphrase" name="passphrase" ' +
                'class="form-control required"  placeholder="Wireless Password" autocomplete="false" />';
            $('form#wirelessNetworkForSpeedTestForm div#passwordGroup').append(passElem);
            $('form#wirelessNetworkForSpeedTestForm div#saveButtonDiv').show();
        } else {
            $('form#wirelessNetworkForSpeedTestForm span#helpBlock').html("");
            $('form#wirelessNetworkForSpeedTestForm div#passwordGroupOuter').hide();
            $('form#wirelessNetworkForSpeedTestForm div#saveButtonDiv').hide();
            $('div#confirmWirelessNetwork').show();
        }

    } else if (data.status === true) {

        $('input#networkConfiguration').val('true');
        $('form#wirelessNetworkForSpeedTestForm div#passwordGroupOuter').hide();
        $('form#wirelessNetworkForSpeedTestForm div#saveButtonDiv').hide();
        $('form#wirelessNetworkForSpeedTestForm span#helpBlock').html("Configuration for this network already exists.");
        $('div#confirmWirelessNetwork').show();
    }

}

// This function serves the purpose of checking all of the 
// wireless interface options. These options include whether or not it is currently 
// powered up and connected to any networks. It will then check to see
// if the interface has been assigned an IP address.

function performWirelessFunctionsCheck() {

    //Test for wireless card available.
    $.ajax({
        method: 'post',
        url: "/diagnostics/checkWirelessStatus",
        data: {},
        success: function(data) {

            if (data == "up") {

                addCommentToLog('wirelessInterfaceStatus', '<i class="fa fa-check"></i> wlan0 is active... ', 'success');

                $('div#workFlowTest span#testResults p').html('Testing for wireless ip address...');

                addCommentToLog('wirelessInterfaceTestingIpAddress', 'Testing for wireless ip address... ', 'info');

                //Test for wireless IP address.
                $.ajax({
                    method: 'post',
                    url: "/diagnostics/checkWirelessAddress",
                    data: {},
                    success: function(data) {

                        if (data) {
                            addCommentToLog('wirelessInterfaceIpAddress', 'wlan0 ip address is: ' + data, 'success');

                            $('i#networkTestSpinner').hide();
                            $('div#workFlowTest span#testResults p').html('');
                            $('div#chooseWirelessNetwork').show();

                        } else {

                            addCommentToLog('wirelessInterfaceIpAddress', '<i class="fa fa-times"></i> wlan0 is not assigned an ip address...', 'info');
                            $('i#networkTestSpinner').hide();
                            $('div#workFlowTest span#testResults p').html('');
                            $('div#chooseWirelessNetwork').show();
                        }
                    }
                }); //end of ajax
            } else {
                $('div#workFlowTest span#testResults p').html('');
                $('i#networkTestSpinner').hide();
                $('div#chooseWirelessNetwork').show();
                addCommentToLog('wirelessInterfaceStatus', '<i class="fa fa-times"></i> wlan0 is not active', 'info');
            }
        }
    }); //end of ajax
}

// Begin connect to wireless network globally function
$(document).on('click', 'a.wirelessNetwork', function(e) {

    if(typeof widarHoldStartButton === 'function'){
        widarHoldStartButton(true);
    }
    
    e.preventDefault();
    var encryptionStatus = $(this).attr('data-encryption-status');
    var networkSsid = $(this).attr('id');
    var groupCipher = $(this).attr('data-group-cipher');
    var pairwise = $(this).attr('data-pairwise-cipher');
    var authenticationSuite = $(this).attr('data-encryption-type');
    var encryptionType = $(this).attr('data-encryption-type');

    $.ajax({
        method: 'post',
        dataType: 'json',
        url: "/wireless/wirelessNetworkConfigurationExists",
        data: {
            "ssid": networkSsid
        },
        success: function(data) {
            console.log(data);
            if (data.status == false) {
                if (encryptionStatus == "on") {
                    $('#connectToWirelessNetworkModal').modal('toggle');
                    $('#connectToWirelessNetworkModal form#connectToWirelessNetworkForm input#networkSsid').attr('value', networkSsid);
                    $('#connectToWirelessNetworkModal input#encryptionType').attr('value', encryptionType);
                    $('#connectToWirelessNetworkModal div#encryptionKeyParent').attr('hidden', false);
                    $('#connectToWirelessNetworkModal div#encryptionTypeParent').attr('hidden', false);
                    $('#connectToWirelessNetworkModal input#group').attr('value', groupCipher);
                    $('#connectToWirelessNetworkModal input#pairwise').attr('value', pairwise);
                    $('#connectToWirelessNetworkModal input#authentication').attr('value', authenticationSuite);
                } else {
                    $('#connectToWirelessNetworkModal input#networkSsid').attr('value', '');
                    $('#connectToWirelessNetworkModal input#encryptionType').attr('value', '');
                    $('#connectToWirelessNetworkModal div#encryptionKeyParent').attr('hidden', true);
                    $('#connectToWirelessNetworkModal div#encryptionTypeParent').attr('hidden', true);
                    connectToWirelessNetwork(networkSsid, false);
                }

            } else if (data.status == true) {
                if (typeof widarNetwork === 'function') {
                    console.log('widarNetwork function is defined');
                    widarNetwork(networkSsid)
                }
                connectToWirelessNetwork(networkSsid, true);
            }
        }
    }); //end of ajax
});

/**
 * Given a network ssid that is already configured, this function
 * will use an ajax request to send the command to the pfi to 
 * begin the connection sequence for the wireless network.
 * 
 * @param  {[string]} networkSsid Wireless Network ssid
 * @return null
 */
function connectToWirelessNetwork(networkSsid, encryption)
{
                $.ajax({
                    method: 'post',
                    dataType: 'json',
                    url: "/wireless/connectToWirelessNetwork",
                    data: {
                        "ssid": networkSsid,
                        'encryption': encryption
                    },
                    success: function(data) {
                        console.log(data);
                    }
                });
}

$(document).on('click', 'button#connectToNetworkFormSubmit', function(e) {

    e.preventDefault();

    var networkData = $('form#connectToWirelessNetworkForm').serialize();
    var pass = $('form#connectToWirelessNetworkForm input#encryptionKey').val();
    var ssid = $('form#connectToWirelessNetworkForm input#networkSsid').val();
    var enc = $('form#wirelessNetworkForSpeedTestForm select#networkName').find('option:selected').attr('class');
    console.log(pass + ' ' + ssid);
    $.ajax({
        method: 'post',
        url: "/wireless/getPreSharedKey",
        data: {
            passphrase: pass,
            ssid: ssid
        },
        success: function(data) {
                console.log(data);
                $.ajax({
                    method: 'post',
                    url: "/wireless/savePreSharedKey",
                    data: {
                        conf: data,
                        ssid: ssid,
                        encryption: enc
                    },
                    success: function(data) {
                        console.log(data);
                        $('#connectToWirelessNetworkModal').modal('toggle');
                        connectToWirelessNetwork(ssid);
                    }
                });
                //nested ajax function endpoint       
            }
            //end of ajax success function
    });
});

function addCommentToLog(id, comment, css) {

    var cssClass = (typeof css == 'undefined') ? "info" : css;

    var commentExists = 0;
    if (id != "") {
        commentExists = $('#networkTestLog div.list-group a#' + id);
    }

    if (commentExists.length == 1) {
        commentExists.html(comment);
    } else {
        $('#networkTestLog div.list-group').append('<a href="#" class="list-group-item list-group-item-' + cssClass + '"" id="' + id + '">' + comment + '</a>');
    }
}

function resetNetworkList() {

    $.ajax({
        dataType: 'json',
        method: 'post',
        url: "/wireless/getWirelessNetworkScanResults",
        data: {
            'type': 'ajax'
        },
        success: function(data) {

            $('ul#wirelessNetworkList').html('');
            $('select#networkName').html('');

            $('select#networkName').append('<option selected="selected">Select SSID</option>');

            if (data.networks.length > 0) {
                for (var i = 0; i < data.networks.length; i++) {

                    var $encryptionIcon = '';
                    var $ssid = data.networks[i].ssid;
                    var $encryption = 'data-encryption-status="' + data.networks[i].encryption + '"';
                    var $pairwiseCipher = 'data-pairwise-cipher="' + data.networks[i].pairwiseCipher + '"';
                    var $groupCipher = 'data-group-cipher="' + data.networks[i].groupCipher + '"';
                    var $authentication = 'data-authentication="' + data.networks[i].authenticationSuite + '"';
                    var $encryptionType = 'data-encryption-type="' + data.networks[i].encryptionType + '"';

                    var $encryptionOption = data.networks[i].encryptionType;

                    if ($encryptionOption == "") {
                        $encryptionOption = "none";
                    }

                    if (data.networks[i].encryption = "on") {
                        $encryptionIcon = "fa fa-lock";
                    } else {
                        $encryptionIcon = "fa fa-unlock";
                    }

                    var $network = '<option id="' + $ssid + '" class="' + $encryptionOption + '" value="' + $ssid + '">' + $ssid + '</option>';

                    $('select#networkName').append($network);

                    var $link = '<a href="javascript:void(0);" class="wirelessNetwork" id="' + $ssid + '" ' + $encryption + ' ' + $pairwiseCipher + '' +
                        '' + $groupCipher + ' ' + $authentication + ' ' + $encryptionType + ' >' + $ssid + ' <i class="' + $encryptionIcon + '"></i></a>';

                    $('ul#wirelessNetworkList').append('<li>' + $link + '</li>');
                }
            } else {
                $('ul#wirelessNetworkList').append('<li><a href="javascript:void(0);" title="This is most likely because you have not ran a network scan yet.">No Networks Available </a></li>');
            }

            $('select#networkName').append('<option value="none">Select SSID</option>');


        }
    }); //end of ajax
}

function isEmpty(object) {
    for (var key in object) {
        if (object.hasOwnProperty(key)) {
            return false;
        }
    }
    return true;
}

function processWirelessNetworksTable(data) {
    console.log(data);
    $('tbody#networkScanTableBody').html('');
    $('select#networkName').html('');
    for (var i = 0; i < data.length; i++) {

        if (data[i].ssid != "<?php echo $dynamicSsid; ?>") {

            var channel = (typeof data[i].dist_system.channel === 'undefined') ? data[i].ht_operation.primary_channel : data[i].dist_system.channel;

            var manufacturer = data[i].manufacturer;

            var encryption = "";

            var macModal = data[i].mac.replace(/\:/g, '');


            if (!isEmpty(data[i].wps)) {

                encryption = encryption + " WPS";
            }

            if (!isEmpty(data[i].wpa)) {

                encryption = encryption + " WPA-" + data[i].wpa.authentication_suites.auth;
            }

            var ssid = data[i].ssid;

            var optionItem = '<option class="' + encryption + '" id="' + ssid + '" value="' + ssid + '">' + ssid + '</option>';

            var tableContent = '<tr id="' + ssid + 'Row">' +
                '<td><i class="fa fa-wifi"></i></td>' +
                '<td>' +
                '<a href="#" data-toggle="modal" id="networkSsid" class="toggleNetworkModal" data-target="#' + macModal + 'Modal" >' +
                ssid +
                '</a>' +
                '</td>' +
                '<td>' +
                '<a href="#" data-toggle="modal" id="networkMac" class="toggleNetworkModal" data-target="#' + macModal + 'Modal">' +
                data[i].mac +
                '</a>' +
                '</td>' +
                '<td>' +
                '<a href="#" data-toggle="modal" id="networkChannel" class="toggleNetworkModal" data-target="#' + macModal + 'Modal">' +
                channel +
                '</a>' +
                '</td>' +
                '<td>' +
                '<a href="#" data-toggle="modal" id="networkManufacturer" class="toggleNetworkModal" data-target="#' + macModal + 'Modal">' +
                manufacturer +
                '</a>' +
                '</td>' +
                '<td>' +
                '<a href="#" data-toggle="modal" id="networkEncryption" class="toggleNetworkModal" data-target="#' + macModal + 'Modal">' +
                encryption +
                '</a>' +
                '</td>' +
                '<td data-class-name="priority">' +
                '<a href="#" data-toggle="modal" id="networkSignalStrength" class="toggleNetworkModal" data-target="#' + macModal + 'Modal">' +
                data[i].signal_strength +
                '</a>' +
                '</td>' +
                '</tr>';

            $('tbody#networkScanTableBody').append(tableContent);


            var networkModal = '<div class="modal fade" id="' + macModal + 'Modal">' +
                '<div class="modal-dialog">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                '<span >&times;</span></button>' +
                '<h4 class="modal-title text-center">' + ssid + '</h4>' +
                '</div>' +
                '<div class="modal-body">' +
                '<div class="row">' +
                '<div class="col-md-12" id="basicNetworkInfo">' +
                '<p class="lead">Channel: ' + channel + '</p>' +
                '<p class="lead">Frequency: ' + data[i].frequency + '</p>' +
                '<p class="lead">Encryption: ' + encryption + '</p>' +
                '<p class="lead">Signal Strength: ' + data[i].signal_strength + '</p>' +
                '</div></div>' +
                '<div class="row">' +

                '<div class="col-md-12" id="htCapabilities">' +
                '<div class="panel panel-primary">' +
                '<div class="panel-heading">' +
                '<div class="text-center">High Throughput Capabilities</div>' +
                '</div>' +
                '<div class="panel-body">' +
                '<div class="list-group" id="listOfHtCapabilities">' +

                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +

                '<div class="col-md-12" id="vhtCapabilities">' +
                '<div class="panel panel-danger">' +
                '<div class="panel-heading">' +
                '<div class="text-center">Very High Throughput Capabilities</div>' +
                '</div>' +
                '<div class="panel-body">' +
                '<div class="list-group" id="listOfVhtCapabilities">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +

                '<div class="modal-footer">' +
                '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';

            $('select#networkName').append(optionItem);
            $('body').append(networkModal);


            if (typeof data[i].ht_capabilities.capabilities != 'undefined') {
                $.each(data[i].ht_capabilities.capabilities.capability, function(index, value) {
                    $("#" + macModal + "Modal div#listOfHtCapabilities").append('<a class="list-group-item">' + value + '</a>');
                });
            } else {
                $("#" + macModal + "Modal div#htCapabilities").html('<div class="alert alert-danger">' +
                    '<p>This device does not support High Throughput or we were unable to obtain the neccessary information.</p></div>');
            }

            if (typeof data[i].vht_capabilities.capabilities != 'undefined') {
                $.each(data[i].vht_capabilities.capabilities.capability, function(index, value) {
                    $("#" + macModal + "Modal div#listOfVhtCapabilities").append('<a class="list-group-item">' + value + '</a>');
                });
            } else {
                $("#" + macModal + "Modal div#vhtCapabilities").html('<div class="alert alert-danger">' +
                    '<p>This device does not support Very High Throughput or we were unable to obtain the neccessary information.</p></div>');
            }

        }
    }

    displayWirelessChart(data);

}


function displayWirelessChart(data) {


    var networkList = data;

    var wirelessNetworkSignalChart = document.getElementById("myNetworkChart").getContext("2d");

    var networkLabels = new Array();

    var networkSignals = new Array();

    for (var i = 0; i < networkList.length; i++) {
        if (data[i].ssid != "<?php echo $dynamicSsid; ?>") {
            networkLabels.push(networkList[i].ssid);
            networkSignals.push(networkList[i].signal_strength.replace('dBm', ''));
        }

    }

    var wirelessNetworksData = {
        labels: networkLabels,
        datasets: [{
            label: "Signal Power",
            fillColor: "rgba(3,178,20,0.2)",
            strokeColor: "rgba(3,178,20,1)",
            pointColor: "rgba(3,178,20,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: networkSignals
        }]
    };


    window.myLine = new Chart(wirelessNetworkSignalChart).Line(wirelessNetworksData, {
        responsive: true,
        tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>dBm",
        bezierCurve: true,
        bezierCurveTension: 0.4,
    });

}
/*
 * ====================================================
 * 
 *    End Wireless related javascript functions
 *
 * ====================================================
 */


/*
 * ====================================================
 * 
 *     Job related javascript function
 *
 * ====================================================
 */

function diff_minutes(dt2, dt1) 
 {

  var diff =(dt2.getTime() - dt1.getTime()) / 1000;
  diff /= 60;
  return Math.abs(Math.round(diff));
  
 }

function checkForUpdate() {

    var lastCheck = null;

    
    lastCheck = store.get("updateCheck");
    
    var now = new Date();

    if(lastCheck !== null){
        var then = new Date(lastCheck);
    } else {
        var then = now;
    }
    
    var timeGap = diff_minutes(now, then);
    console.log(timeGap);
    if(lastCheck === null || timeGap > 180) {
        $.ajax({
            method: "post",
            url: "/version/checkForUpdate",
            success: function(data) {
                var date = new Date();
                console.log("date: " + date);
                store.set("updateCheck", date.toISOString());                
            }
        }); //end of ajax 
    }
}


function updateAvailableJobs(job)
{
    $('select#jobSelection').append("<option value='"+job.id+"'>"+job.name+"</option>");
    $('select#jobSelection').trigger("chosen:updated");
    $('select#newJobSelection').append("<option value='"+job.id+"'>"+job.name+"</option>");
    $('select#newJobSelection').trigger("chosen:updated");
}

$(document).on('ready', function() {

    //checkForUpdate();

    $('form#createNewJobForm .createJob').on('click', function(e) {

        e.preventDefault();

        $.ajax({
            method: 'post',
            url: "/job/createnewjob",
            data: $('form#createNewJobForm').serialize(),
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if(data.status !== "error") {
                    // If this is the first job created go ahead and set it as
                    // the current job which will also redirect to the home page and log the user in.
                    if(data.job.id == 1) {
                        setSessionData("jobId", data.job.id);
                    } else {
                        updateAvailableJobs(data.job);
                    }
                }
                if (data.status == true) {
                    alertify.success(data.msg);
                } else if (data.status == false) {
                    alertify.error(data.msg);
                }
                if(data.job.id !== 1) {
                    $('#createJobModal').modal('toggle');
                }

                <?php if ($jobId === 0) : ?>
                $('#selectJobModal').modal('toggle');
                <?php endif; ?>
            }
        }); //end of ajax   

    });

    $('form#createNewJobFormRaw .createJob').on('click', function(e) {

        e.preventDefault();

        $.ajax({
            method: 'post',
            url: "/job/createnewjob",
            data: $('form#createNewJobFormRaw').serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status == true) {
                    alertify.success(data.msg);
                } else {
                    alertify.error("Unable to create new job.");
                }
                $('#createJobModal').modal('toggle');
            }
        }); //end of ajax   
    });

    $(document).on('click', 'button#toggleCreateJob', function() {
        $('div#createANewJobSection').toggle();
    });

    $(document).on('click', 'form#createAJobForm .createJob', function(e) {
        e.preventDefault();
        $.ajax({
            method: 'post',
            url: "/job/createnewjob",
            data: $('form#createAJobForm').serialize(),
            dataType: 'json',
            success: function(data) {

                $('#initialView').hide();
                selectedJobDetails(data);
                $('#secondView').show();

            }
        }); //end of ajax   

    });
});

function selectedJobDetails(jobId) {

    $.ajax({
        dataType: 'json',
        method: 'post',
        url: "/job/getJobDetails",
        data: {
            job: jobId
        },
        success: function(data) {

            $('#updateUserStatus span#jobName p').html("Job Name: " + data.jobName);

            $('#updateUserStatus span#jobBan p').html("Job BAN #: " + data.jobBan);

            addCommentToLog('currentlySelectedJobName', 'Job Name: ' + data.jobName);

            $('input#intialTestJobId').val(jobId);

            setSessionData("jobId", jobId);

        }

    }); //end of ajax
}
/*
 * ====================================================
 * 
 *    End Job related javascript function
 *
 * ====================================================
 */

$(document).ready(function () {

    // Full height of sidebar
    function fix_height() {
        var heightWithoutNavbar = $("body > #wrapper").height() - 61;
        $(".sidebard-panel").css("min-height", heightWithoutNavbar + "px");

        var navbarHeigh = $('nav.navbar-default').height();
        var wrapperHeigh = $('#page-wrapper').height();

        if (navbarHeigh > wrapperHeigh) {
            $('#page-wrapper').css("min-height", navbarHeigh + "px");
        }

        if (navbarHeigh < wrapperHeigh) {
            $('#page-wrapper').css("min-height", $(window).height() + "px");
        }

        if ($('body').hasClass('fixed-nav')) {
            if (navbarHeigh > wrapperHeigh) {
                $('#page-wrapper').css("min-height", navbarHeigh - 60 + "px");
            } else {
                $('#page-wrapper').css("min-height", $(window).height() - 60 + "px");
            }
        }

    }

    fix_height();
});

/*
 * ====================================================
 * 
 *    Storage related javascript function
 *
 * ====================================================
 */

function storageAvailable(type) {
    try {
        var storage = window[type],
            x = '__storage_test__';
        storage.setItem(x, x);
        storage.removeItem(x);
        return true;
    } catch (e) {
        return false;
    }
}

function storeItemInLocalStorage(key, data) {
    store.set(key, data)
}

function clearFromStorage(item) {
    store.remove(item);
}

function getFromLocalStorage(item) {
    store.get(item);
}

function setNewJobValue(jobId)
{
        if((typeof jobId == 'undefined') || jobId == 0) {
            $('#selectAJobForm .selectJobWrapper.form-group').toggleClass('has-error');
            $('#selectAJobForm .selectJobWrapper .help-block').text('You must select a job before proceeding.');
            return;
        } else {
             setSessionData("jobId", jobId);    
        }
}

function showSetupWizard(event, target) {
    console.log(wizard);
    if(target === 'job') {
        wizard.steps("next");
    } else if(target === 'createJob') {
        wizard.steps("next");
        $('modal#setupWizard button#showCreateForm').hide();
        $('div#createJobForm').show();
    }
    $('#setupWizard').modal('show');
}


$(document).ready(function() {
    $(document).on('click', '#selectJob', function(e) {
        e.preventDefault();
       var target = $(this).data('target');
       var jobId = $('select' + target).val();
       console.log('Job ID: ' + jobId);
        if((typeof jobId == 'undefined') || jobId == 0) {
            $('#selectAJobForm .selectJobWrapper.form-group').toggleClass('has-error');
            $('#selectAJobForm .selectJobWrapper .help-block').text('You must select a job before proceeding.');
            return;
        } else {
             setSessionData("jobId", jobId);    
        }

    });
});

/*
 * ====================================================
 * 
 *    End storage related javascript function
 *
 * ====================================================
 */
</script>
