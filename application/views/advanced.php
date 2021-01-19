<?php
 defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="page-wrapper" class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 m-b-md">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                <div class="panel-body">
                        <div class="row">
                        <form class="form" name="networkTestForm" id="networkTestForm">
                        <div class="col-sm-3">

                        <div class="form-group">
                        <label>Tool</label>
                            <select class="form-control" name="testMethod" id="testMethod">
                                <option value="traceroute">Traceroute</option>
                                <option value="ping">Ping</option>
                            </select>
                        </div>
                        </div>
                        <div class="col-sm-6">
                        <div class="form-group">
                        <label>Target</label>
                        <div class="input-group">
                            <input type="text" name="host" class="input form-control">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary" id="submitNetworkTest" data-target="#testMethod"> Go!</button>
                            </span>
                        </div>
                        </div>
                        </div>   
                        <div class="col-sm-3">
                        <div class="form-group" style="display:none;" id="countWrapper">
                        <label>Count</label>
                            <input type="text" name="count" class="input form-control">
                        </div>
                        </div> 

                        </form>
                        </div>
                    
                    <div class="ibox-content">
                        <div id="tracerouteWrapper">
                        <div class="alert alert-danger" id="traceRouteError" style="display:none;">
                        </div>
                        <table class="table table-striped" id="traceroute">
                            <thead>
                                <tr>
                                    <th>Hop</th>
                                    <th>IP</th>
                                    <th>Host <small>(IP if no PTR)</small></th>
                                    <th></th>
                                    <th colspan="3">Round trip</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        </div>
                        <div id="pingWrapper" style="display:none;">
                        <div class="col-lg-12">
                        <div class="row">
                        <span id="statusWrapper"></span>
                        <table class="table table-striped" id="ping">
                            <thead>
                                <tr>
                                    <th>Hop</th>
                                    <th>Data</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        </div>
                        <div class="row">
                        <dl class="dl-horizontal" id="pingDetails">
                        </dl>
                        </div>
                        </div>
                        </div>
                        <div id="nslookupWrapper" style="display:none;">
                        <div class="alert alert-danger">
                        <p>Not implemented yet.</p>
                        </div>
                        </div>

                    </div>
                    </div>
                </div>
                </div>
        </div>
</div>
</div>
<?php $this->load->view('additional/footer');?>
</div>
    <script src="/assets/js/jquery.peity.js"></script>
    <script>
$(document).on('click','button#submitNetworkTest', function(e){
    var target = $(this).data('target');
    var value = $('select'+target).val();
        $('table#'+value+ ' tbody').html('');
        $.ajax({
            method: "post",
            url: "/advanced/"+value,
            dataType: "json",
            data: $('form#networkTestForm').serialize(),
            success: function(data) {
                if (data.status == true) {
                    alertify.success(data.msg);
                    //$('form#createIssueForm')[0].reset();
                } else if (data.status == false) {
                    alertify.error(data.msg);
                }
            }
        });
});

$(document).on('change', 'select#testMethod', function(e) {
    var value = $(this).val();

    if(value == "ping") {
        $('#countWrapper').show();
    } else {
        $('#countWrapper').hide();
    }

    $("select#testMethod > option").each(function() {
        if(this.value !== value){
        $('#'+this.value+'Wrapper').hide();
    }
    });

    $('#'+value+'Wrapper').show();

});


<?php if ($this->config->item('pusher_enabled')) : ?>
    var traceRouteChannel = pusher.subscribe('traceroute');
    var pingChannel = pusher.subscribe('ping');
    
    traceRouteChannel.bind('error', function(notification) {
        var message = notification;
        $('#traceRouteError').show();
        $('#traceRouteError').append(message.error + '\n');
    });

    traceRouteChannel.bind('probe', function(notification) {
        var message = notification;
        addTableRowToTraceRoute("traceroute", message);
    });
    traceRouteChannel.bind('details', function(notification) {
        var message = notification;

    });

    pingChannel.bind('stats', function(notification) {
        var message = notification;
        addTableRowToPing("ping", message);

    });

    pingChannel.bind('end_stats', function(notification) {
        var message = notification;

    });

    pingChannel.bind('start', function(notification) {
        var message = notification;
        if(message.status !== false) {
            $('#statusWrapper').html("<label class='label label-primary'>Connected</label>");
        } else {
            $('#statusWrapper').html("<label class='label label-danger'>Error</label>");
        }
    });

    pingChannel.bind('stop', function(notification) {
        var message = notification;
        $("#pingDetails").append(
            '<dt>Alive</dt><dd>'+message.alive+'</dd>'
            +'<dt>ICMP Echos Sent</dt><dd>'+ message.icmp_echos_sent +'</dd>'
            +'<dt>ICMP Echo Replies Received</dt><dd>'+ message.icmp_echo_replies_received +'</dd>'
            +'<dt>Average Roundtrip time</dt><dd>'+ message.avg_rtt +' (ms)</dd>'
            +'<dt>Max Roundtrip time</dt><dd>'+ message.max_rtt +' (ms)</dd>'
            +'<dt>Min Roundtrip time</dt><dd>'+ message.min_rtt +' (ms)</dd>'
            );
    });

    pingChannel.bind('error', function(notification) {

    });

    <?php endif; ?>

function addTableRowToTraceRoute(id, data)
{
    var data = data.probe;
    var hop = data.id;
    if(data.valid == true) {
        if($('#'+id+'> tbody tr#' + hop).length === 0)
        {

        $('#'+id+'> tbody:last-child').append(
          '<tr id="'+data.id+'" >'
          +'<td class="hop">'+data.id+'</td>'
          +'<td class="ip">'+data.ip+'</td>'
          +'<td class="host">'+data.host+'</td>'
          +'<td class="t1">' + data.time.toFixed(3) + '</td>'
          +'<td class="t2"></td>'
          +'<td class="t3"></td>'
          +'</tr>');

        } else {

            if($('#' + id + '> tbody tr#' + hop + ' td.t2').text() == "") {
                $('#' + id + '> tbody tr#' + hop + ' td.t2').text(data.time.toFixed(3));
            } else if($('#' + id + '> tbody tr#' + hop + ' td.t3').text() == "") {
                $('#' + id + '> tbody tr#' + hop + ' td.t3').text(data.time.toFixed(3));
            }
        }
    } else {
        if($('#'+id+'> tbody tr#' + hop).length === 0)
        {
        $('#'+id+'> tbody:last-child').append(
          '<tr id="'+data.id+'" >'
          +'<td class="hop">'+data.id+'</td>'
          +'<td class="ip">*</td>'
          +'<td class="host">*</td>'
          +'<td class="t1"></td>'
          +'<td class="t2"></td>'
          +'<td class="t3"></td>'
          +'</tr>');
        }
    }
}

function addTableRowToPing(id, data)
{
    
        $('#'+id+'> tbody:last-child').append(
          '<tr id="'+data.count+'" >'
          +'<td class="hop">'+data.count+'</td>'
          +'<td class="data">' + data.result.size + ' '+ data.result.unit + '</td>'
          +'<td class="time">'+data.time.length + ' ' + data.time.unit + '</td>'
          +'</tr>');       
    
}

function updateChart(chartId, time)
{
        var updatingChart = $(chartId).peity("line", { fill:'#1ab394', stroke:'#169c81' });
        var currentValues = $(chartId);
        var values = updatingChart.text().split(",");
        values.push(time);

        currentValues.text(values.join(","));
        updatingChart.change();
}

    </script>
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