<?php
 defined('BASEPATH') or exit('No direct script access allowed');
if (isset($currentJob->id) && $currentJob->id !== 0) :
    $settings = $currentJob->getDetails();
    ?>
<style>
.container { margin-top: 10px; }

.progress-bar-vertical {
  width: 40px;
  min-height: 100px;
  display: flex;
  align-items: flex-end;
  margin-right: 20px;
  float: left;
}

.progress-bar-vertical .progress-bar {
  width: 100%;
  height: 0;
  -webkit-transition: height 0.6s ease;
  -o-transition: height 0.6s ease;
  transition: height 0.6s ease;
}

</style>
<link rel="stylesheet" href="<?php echo $this->config->item('plugins_directory');?>switchery/dist/switchery.min.css"/>
<div id="page-wrapper" class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
      <div class="row">
          <div class="col-lg-12 m-b-md">
              <div class="ibox float-e-margins" style="min-height:200px">
                  <div class="ibox-title">
                      <h5>Wifi Radar</h5>&nbsp;

                      <div class="ibox-tools">
                        <a data-toggle="modal" data-target="#rssiInfoModal"><i class="fa fa-info"></i></a>
                      </div>
                        <?php if ($wirelessConn['authenticated']) : ?>
                      <div id="widar-auth" class="row">
                      <div class="col-lg-4 col-offset-lg-4">
                      <span class="auth-intro">Authenticated to:</span> <span id="auth-network" class="font-bold"><?php echo $wirelessConn['ssid'] ?></span>
                      </div>
                      </div>
                    <?php else : ?>
                    <div id="widar-auth" class="row hidden">
                      <div class="col-lg-4 col-offset-lg-4">
                        <span class="auth-intro">Authenticated to:</span> <span id="auth-network" class="font-bold"></span>
                      </div>
                    </div>
                      
                    <?php endif; ?>

                  </div>
                  <div class="ibox-content">
                  <div class="row">
                  <div class="col-lg-4 col-sm-3">
                  <div class="form-group" title="Turning this on will force you to authenticate to a wireless network. Once connected you will be able to monitor the signal strength much faster.">
                  <input type="checkbox" name="monitor" class="js-switch" data-switchery="true" id="monitor" <?php if ($wirelessConn['authenticated']) :
                        echo 'checked';
endif;?> />
                  <p class="help-text">Run in monitor mode</p>
                  </div>
                  </div>

                  <div class="col-lg-4 col-sm-3">
                  <div class="btn-group">
                      <a href="#" id="wirelessNetworkDropdown" class="btn btn-primary dropdown-toggle disabled" data-toggle="dropdown" data-dismiss="tooltip">
                          <i class="fa fa-wifi"></i> Select Network
                          <span class="caret"></span>
                      </a>
                  <ul id="wirelessNetworkListWidar" class="dropdown-menu">
                  </ul>
                  </div>
                  </div>
                  <div class="col-lg-4 col-sm-3">
                      <a class="btn btn-primary" id="startWidarTest" href="#">Start</a>
                      <a class="btn btn-danger" id="stopWidarTest" href="#">Stop</a>
                  </div>
                  </div>
                      <div class="row">
<?php if (!empty($wirelessConn['frequency']) && substr($wirelessConn['frequency'], 0, 1) === '2') :
    $twoG = true;
    $fiveG = false;
elseif (!empty($wirelessConn['frequency']) && substr($wirelessConn['frequency'], 0, 1) === '5') :
    $twoG = false;
    $fiveG = true;
else :
    $twoG = true;
    $fiveG = false;
endif; ?>
                        
                        <div class="tabs-container">
                          <ul class="nav nav-tabs">
                              <li class="<?php if ($twoG === true) :
                                    echo 'active';
endif; ?>">
                                  <a data-toggle="tab" href="#24ghztab" data-chart="myNewNetworkChart2G"><i class="fa fa-wifi"></i>2.4 GHz Networks</a>
                              </li>
                              <li class="<?php if ($fiveG === true) :
                                    echo 'active';
endif; ?>">
                                  <a data-toggle="tab" href="#5ghztab" data-chart="myNewNetworkChart5Gupper"><i class="fa fa-wifi"></i>5 GHz Networks</a>
                              </li>
                          </ul>
                          <div class="tab-content">

<div class="tab-pane <?php if ($twoG === true) :
    echo 'active';
endif; ?>" id="24ghztab">
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-lg-offset-3 col-lg-6">
                    <!-- Injecting radial gauge -->
                    <canvas id="radialGauge2g"></canvas>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-offset-3 col-lg-6">
                <div class="text-center">
                    <div id="2gInnerValue">
                        <p>Instant Power</p>
                        <div id="2gRate"><span>Link Rate:</span>
                            <span class="font-bold" id="linkRate"></span>
                        </div>
                        <div id="2gMac"><span>BSSID:</span>
                            <span class="font-bold" id="macValue"></span>
                        </div>
                        <div class="row">
                            <h5></h5>
                        </div>
                    </div>
                </div>                  
                </div>
            </div>
            <div class="row" id="2gNetworksWrapper">
              <div class="col-lg-12">
              <div class="row">
                <div class="col-lg-offset-3 col-lg-6 col-xs-12">
                <div class="form-group networkSelectWrapper" <?php if ($wirelessConn[ 'authenticated']) :
                    echo 'style="display:none;"';
endif;?> >
                    <p class="help-text">2.4 GHz Network to monitor</p>
                    <select class="form-control chosen-select" id="2gNetworks">
                        <?php if ($wirelessConn['authenticated']) : ?>
                        <?php if (substr($wirelessConn['frequency'], 0, 1) === '2') :?>
                        <?php if (!empty($wirelessConn['ssid'])) : ?>
                        <option data-mac="<?php echo $wirelessConn['mac'];?>" value="<?php echo $wirelessConn['ssid']; ?>">
                            <?php echo $wirelessConn['ssid']; ?> &lt;
                            <?php echo $wirelessConn['mac'];?>&gt;</option>
                        <?php endif; ?>
                        <?php endif;?>
                        <?php elseif (isset($settings->location) and is_object($settings->location)) : ?>
                        <!-- Fall back to default network -->
                        <?php if ($settings->location->default_network) :?>
                        <?php $network = json_decode($settings->location->default_network); ?>
                        <?php if (!empty($network->frequency) && substr($wirelessConn['frequency'], 0, 1) === '2') : ?>
                        <option data-mac="<?php echo $network->mac; ?>" value="<?php echo $network->ssid; ?>">
                            <?php echo $network->ssid; ?> &lt;
                            <?php echo $network->mac;?>&gt;</option>
                        <?php elseif (!empty($wirelessConn['ssid'])) : ?>
                        <?php if (substr($wirelessConn['frequency'], 0, 1) === '2') :?>
                        <option data-mac="<?php echo $wirelessConn['mac'];?>" value="<?php echo $wirelessConn['ssid']; ?>">
                            <?php echo $wirelessConn['ssid']; ?> &lt;
                            <?php echo $wirelessConn['mac'];?>&gt;</option>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php elseif (!empty($wirelessConn['ssid'])) : ?>
                        <option data-mac="<?php echo $wirelessConn['mac'];?>" value="<?php echo $wirelessConn['ssid']; ?>">
                            <?php echo $wirelessConn['ssid']; ?> &lt;
                            <?php echo $wirelessConn['mac'];?>&gt;</option>
                        <?php endif; ?>
                        <?php endif; ?>
                    </select>
                </div>
              </div>
              </div>
            </div>              
                <div class="col-xs-12">
                    <div class="row" id="2gNetworkTimeChart">
                        <!-- 2.4 Graph Over time -->
                        <span id="2gTimeGraph" style="display:none;">0</span>
                        <canvas id="2gCanvas"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="tab-pane <?php if ($fiveG === true) :
    echo 'active';
endif; ?>" id="5ghztab">
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-lg-offset-3 col-lg-6">
                    <!-- Injecting radial gauge -->
                    <canvas id="radialGauge5g"></canvas>
                </div>
            </div>
            <div class="row">
              <div class="col-lg-offset-3 col-lg-6">
                <div class="text-center">
                    <div id="5gInnerValue">
                        <p>Instant Power</p>
                        <div id="5gRate"><span>Link Rate:</span>
                            <span class="font-bold" id="linkRate"></span>
                        </div>

                        <div id="5gMac"><span>BSSID:</span>
                            <span class="font-bold" id="macValue"></span>
                        </div>
                        <div class="row">
                            <h5></h5>
                        </div>
                    </div>
                </div>
              </div>
            </div>
            <div class="row" id="5gNetworksWrapper">
              <div class="col-lg-12">
              <div class="row">
                <div class="col-lg-offset-3 col-lg-6 col-xs-12">
                <div class="form-group networkSelectWrapper" <?php if ($wirelessConn[ 'authenticated']) :
                    echo 'style="display:none;"';
endif;?> >
                    <p class="help-text">5 GHz Network to monitor</p>
                    <select class="form-control chosen-select" id="5gNetworks">
                        <?php if ($wirelessConn['authenticated']) : ?>
                        <?php if (substr($wirelessConn['frequency'], 0, 1) === '5') :?>
                        <?php if (!empty($wirelessConn['ssid'])) : ?>
                        <option data-mac="<?php echo $wirelessConn['mac'];?>" value="<?php echo $wirelessConn['ssid']; ?>">
                            <?php echo $wirelessConn['ssid']; ?> &lt;
                            <?php echo $wirelessConn['mac'];?>&gt;</option>
                        <?php endif; ?>
                        <?php endif;?>
                        <?php elseif (isset($settings->location) and is_object($settings->location)) : ?>
                        <!-- Fall back to default network -->
                        <?php if ($settings->location->default_network) :?>
                        <?php $network = json_decode($settings->location->default_network); ?>
                        <?php if (!empty($network->frequency) && substr($wirelessConn['frequency'], 0, 1) === '5') : ?>
                        <option data-mac="<?php echo $network->mac; ?>" value="<?php echo $network->ssid; ?>">
                            <?php echo $network->ssid; ?> &lt;
                            <?php echo $network->mac;?>&gt;</option>
                        <?php elseif (!empty($wirelessConn['ssid'])) : ?>
                        <?php if (substr($wirelessConn['frequency'], 0, 1) === '5') :?>
                        <option data-mac="<?php echo $wirelessConn['mac'];?>" value="<?php echo $wirelessConn['ssid']; ?>">
                            <?php echo $wirelessConn['ssid']; ?> &lt;
                            <?php echo $wirelessConn['mac'];?>&gt;</option>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php elseif (!empty($wirelessConn['ssid'])) : ?>
                        <option data-mac="<?php echo $wirelessConn['mac'];?>" value="<?php echo $wirelessConn['ssid']; ?>">
                            <?php echo $wirelessConn['ssid']; ?> &lt;
                            <?php echo $wirelessConn['mac'];?>&gt;</option>
                        <?php endif; ?>
                        <?php endif; ?>
                    </select>
                </div>
              </div>
              </div>
            </div>
                <div class="col-xs-12">
                    <div class="row" id="5gNetworkTimeChart">
                        <!--5 Graph Over time -->
                        <span id="5gTimeGraph" style="display:none;">0</span>
                        <canvas id="5gCanvas"></canvas>
                    </div>
                </div>
         
            </div>
        </div>
    </div>
</div>
                    </div>
                        </div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
    </div>

<?php $this->load->view('modals/rssiInfoModal');?>
<?php $this->load->view('additional/footer');?>
</div>
</div>
<script src="<?php echo $this->config->item('plugins_directory');?>chart.js\dist\Chart.min.js"></script>
<script src="/assets/js/gauges.min.js"></script>

<script src="<?php echo $this->config->item('plugins_directory');?>switchery/dist/switchery.min.js"></script>

<script>
$(window).bind('beforeunload', function() {
  stopWidar(false);
});

var elem = document.querySelector('.js-switch');
var switchery = new Switchery(elem, { color: '#1AB394', secondaryColor: '#ED5565' });
 
var gauge2g = new RadialGauge({
    renderTo: 'radialGauge2g',
    width: 400,
    height: 400,
    units: "RSSI",
    minValue: 0,
    startAngle: 90,
    ticksAngle: 180,
    valueBox: false,
    maxValue: 100,
    majorTicks: [
        "0",
        "10",
        "20",
        "30",
        "40",
        "50",
        "60",
        "70",
        "80",
        "90",
        "100"
    ],
    minorTicks: 2,
    strokeTicks: true,
    highlights: [
        {
            "from": 0,
            "to": 25,
            "color": "rgb(237, 85, 101)"
        },
        {
            "from": 25,
            "to": 45,
            "color": "rgb(248, 172, 89)"
        },    
        {
            "from": 45,
            "to": 100,
            "color": "rgb(26, 179, 148)"
        }
    ],
    colorPlate: "#fff",
    borderShadowWidth: 0,
    borders: false,
    needleType: "arrow",
    needleWidth: 2,
    needleCircleSize: 7,
    needleCircleOuter: true,
    needleCircleInner: false,
    animationDuration: 100,
    animationRule: "linear"
}).draw();

var gauge5g = new RadialGauge({
    renderTo: 'radialGauge5g',
    width: 400,
    height: 400,
    units: "RSSI",
    minValue: 0,
    startAngle: 90,
    ticksAngle: 180,
    valueBox: false,
    maxValue: 100,
    majorTicks: [
        "0",
        "10",
        "20",
        "30",
        "40",
        "50",
        "60",
        "70",
        "80",
        "90",
        "100"
    ],
    minorTicks: 2,
    strokeTicks: true,
    highlights: [    
        {
            "from": 0,
            "to": 25,
            "color": "rgb(237, 85, 101)"
        },
        {
            "from": 25,
            "to": 45,
            "color": "rgb(248, 172, 89)"
        },
        {
            "from": 45,
            "to": 100,
            "color": "rgb(26, 179, 148)"
        }
    ],
    colorPlate: "#fff",
    borderShadowWidth: 0,
    borders: false,
    needleType: "arrow",
    needleWidth: 2,
    needleCircleSize: 7,
    needleCircleOuter: true,
    needleCircleInner: false,
    animationDuration: 100,
    animationRule: "linear"
}).draw();

$(document).on('change', '.js-switch', function(e){
  console.log('changed switch');
  if(elem.checked) {
    $(".networkSelectWrapper").hide();
  } else {
    $(".networkSelectWrapper").show();
  }
})

$(document).on('click', '#startWidarTest', function(e){

        e.preventDefault();
        console.log("Starting widar test");
        if(elem.checked) {
          var widarMode = 'monitor';
        } else {
          var widarMode = 'standard';
        }
        store.set("widar_mode", widarMode);

        $.ajax({
          method: "post",
          url: "/widar/startWidar",
          dataType: "json",
          data:{ 
              start: true,
              mode: widarMode
          },
          success: function(data) {
              if (data.status == true) {
                store.set("widar_running", true);
                alertify.success(data.msg);
              } else if (data.status == false) {
                store.set("widar_running", false);
                alertify.error(data.msg);
              }
          }
        });
});

$(document).on('click','#stopWidarTest', function(e){
        e.preventDefault();
        stopWidar(true);
});

function stopWidar(display)
{
    var widarMode = store.get("widar_mode");
        $.ajax({
          method: "post",
          url: "/widar/stopWidar",
          dataType: "json",
          data:{
              stop:true,
              mode: widarMode
          },
          success: function(data) {
            console.log(data);
            store.set("widar_running", false);
              if (data.status == true) {
                gauge5g.value = 0;
                gauge2g.value = 0;
                  if(display) {
                      alertify.success(data.msg);
                  }
              } else if (data.status == false) {
                  if(display) {
                      alertify.error(data.msg);
                  }
              }
          }
        });
}


function widarHoldStartButton(status)
{

  if(status === true) {
    $('#startWidarTest').prop('disabled', true);
    $('#startWidarTest').addClass('disabled');

    // Check to see if widar is currently running
    // most likely it is not in monitor mode, which means the results we are getting
    // do not match what we truly want, so we stop the currently running process
    var widarStatus = store.get("widar_running");
    if(widarStatus) {
      stopWidar(false);
    }
  } else if(status === false) {
    $('#startWidarTest').prop('disabled', false);
    $('#startWidarTest').removeClass('disabled');
  }

}

// This function is called when a network is being connected to on behalf of
// widar and it returns a successful connection
function widarNetwork(obj)
{
  if(typeof obj.ssid !== 'undefined') {
    $('span#auth-network').text(obj.ssid);
    $('div#widar-auth').removeClass('hidden');
    if(obj.freq.startsWith("2")) {
      $("select#2gNetworks").val(obj.ssid).trigger("chosen:updated");
    } else if(obj.freq.startsWith("5")) {
      $("select#5gNetworks").val(obj.ssid).trigger("chosen:updated");
    }
    if(!elem.checked) {
      $('.js-switch').trigger('click');
    }
    // Re enable the start button
    widarHoldStartButton(false);

  }
}


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
              } else if (data.status == false) {
                  alertify.error(data.msg);
              }
          }
        });
});

<?php if ($this->config->item('pusher_enabled')) : ?>
    
    var widar = pusher.subscribe('widar');

        widar.bind('scan', function(notification) {

            var message = notification.message;            
            for(var i = 0; i < message.length; i++) {
                updateWirelessRadarChart(message[i]);
            }
        });

        widar.bind('monitor', function(notification) {

            var message = notification.message;    
            for(var i = 0; i < message.length; i++) {
                updateWirelessMonitorChart(message[i]);
            }
        });


<?php endif; ?>

var twoGctx = document.getElementById("2gCanvas").getContext("2d");
var fiveGctx = document.getElementById("5gCanvas").getContext("2d");



$('select#2gNetworks').on('change', function(){
    var network = $(this).val();
    $("select#5gNetworks > option").each(function () {
     if (this.value == network)
     {                   
        $('select#5gNetworks option[value='+this.value+']').attr('selected','selected');  
        $('select#5gNetworks').trigger("chosen:updated");                   
        console.log("Five g network match: " + this.text);
    }
    });
})

var twoGLineChart = new Chart(twoGctx, {
    type: 'line',
    data: {
          datasets: [{
                  label: 'RSSI',
                  backgroundColor: "rgba(26,179,148,0.5)",
                  borderColor: "rgba(26,179,148,0.7)",
                  pointBackgroundColor: "rgba(26,179,148,1)",
                  pointBorderColor: "#fff",
                  data: [],
                  fill: false,
              }]
    },
    options: {
              responsive: true,
              
              lineTension: 0,
              title:{
                  display:true,
                  text:'2.4Ghz Network RSSI'
              },
              tooltips: {
                  mode: 'index',
                  intersect: false,
              },
              hover: {
                  mode: 'nearest',
                  intersect: true
              },
              scales: {
                  xAxes: [{
                      display: false,
                      scaleLabel: {
                          display: true,
                          labelString: 'Time'
                      }
                  }],                    
                  yAxes: [{
                      display: true,
                      scaleLabel: {
                          display: true,
                          labelString: 'RSSI'
                      },
                      ticks: {
                          /*suggestedMin: -90,
                          suggestedMax: -30,*/
                          suggestedMin: 0,
                          suggestedMax: 100,
                          stepSize: 5
                      }
                  }]
              }
          }
});

var fiveGLineChart = new Chart(fiveGctx, {
    type: 'line',
    data: {
          datasets: [{
                  label: 'RSSI',
                  data: [],
                  backgroundColor: "rgba(26,179,148,0.5)",
                  borderColor: "rgba(26,179,148,0.7)",
                  pointBackgroundColor: "rgba(26,179,148,1)",
                  pointBorderColor: "#fff",
                  fill: false,
              }]
    },
    options: {
              responsive: true,             
              title:{
                  display:true,
                  text:'5Ghz Network RSSI'
              },
              tooltips: {
                  mode: 'index',
                  intersect: false,
              },
              hover: {
                  mode: 'nearest',
                  intersect: true
              },
              scales: {
                  xAxes: [{
                      display: false,
                      scaleLabel: {
                          display: true,
                          labelString: 'Time'
                      }
                  }],
                  yAxes: [{
                      display: true,
                      scaleLabel: {
                          display: true,
                          labelString: 'RSSI'
                      },
                      ticks: {
                          /*suggestedMin: -90,
                          suggestedMax: -30,*/
                          suggestedMin: 0,
                          suggestedMax: 100,
                          stepSize: 5
                      }
                  }]
              }
          }
});

function addData(chart, label, data) {
    chart.data.labels.push(label);
    chart.data.datasets.forEach(function(dataset, datasetIndex){      


        if(dataset.data.length > 30) {
          chart.data.labels.shift();
          dataset.data.shift();
        }
        dataset.data.push(data);

    });
    chart.update();
}

function calculateRssiValueFromDbm(dbm)
{
    var rssi = 100 * ( 1 - (-30 - dbm)/(-30 + 100));
    return rssi;
}

function writeToDisplayBar(element, value)
{
    $(element + ' .progress-bar').css('height', value + '%');
    $(element).show();
}

function updateWirelessRadarChart(network)
{
    var twoGNetwork = $("select#2gNetworks").val();
    var fiveGNetwork = $("select#5gNetworks").val();
    var twoGMac = $('option:selected', "select#2gNetworks").data('mac');
    var fiveGMac = $('option:selected', "select#5gNetworks").data('mac');

    if(network.ssid == twoGNetwork  && network.mac == twoGMac) {
        $("#2gInnerValue h5").text(network.signal_level);

        $('#2gInnerValue #2gMac span#macValue').text(network.mac);
        var rssi = network.signal_level;
        addData(twoGLineChart, twoGNetwork, network.signal_level)
        gauge2g.value = network.signal_level
    }
    if (network.ssid == fiveGNetwork  && network.mac == fiveGMac) {
        $("#5gInnerValue h5").text(network.signal_level);
        $('#5gInnerValue #5gMac span#macValue').text(network.mac);
        var rssi = network.signal_level;
        addData(fiveGLineChart, fiveGNetwork, network.signal_level)
        gauge5g.value = network.signal_level
    }
}


function updateWirelessMonitorChart(network)
{
    var twoGNetwork = $("select#2gNetworks").val();
    var fiveGNetwork = $("select#5gNetworks").val();

    if(network.ssid == twoGNetwork /*&& network.mac == twoGMac*/) {
        $("#2gInnerValue h5").text(network.signal_level);
        $('#2gInnerValue #2gRate span#linkRate').text(network.link_rate);
        $('#2gInnerValue #2gMac span#macValue').text(network.mac);
        var rssi = network.signal_level;
        //writeToDisplayBar("#2gNetworkChart", rssi);
        addData(twoGLineChart, twoGNetwork, network.signal_level)
        gauge2g.value = network.signal_level
    }

    if (network.ssid == fiveGNetwork /*&& network.mac == fiveGMac*/) {

        $("#5gInnerValue h5").text(network.signal_level);
        $('#5gInnerValue #5gRate span#linkRate').text(network.link_rate);
        $('#5gInnerValue #5gMac span#macValue').text(network.mac);
        var rssi = network.signal_level;
        //writeToDisplayBar("div#5gNetworkChart", rssi);
        addData(fiveGLineChart, fiveGNetwork, network.signal_level)
        gauge5g.value = network.signal_level
    }
}

function handleDropdownMenu(networks)
{

                  for (var i = 0; i < networks.length; i++)
                  {
                      
                      if (networks[i].ssid !== "<?php echo $dynamicSsid; ?>" && networks[i].ssid !== "")
                      {
                          if(networks[i].encryption == "on") {
                              var encryptionIcon = 'fa fa-lock fa-fw';
                          } else {
                              var encryptionIcon = 'fa fa-unlock fa-fw';
                          }

                          if(networks[i].frequency.startsWith("2")) {
                            var frequency = "2.4 GHz";
                            var frequencyLabel = "label label-primary";
                          } else {
                            var frequency = "5 GHz";
                            var frequencyLabel = "label label-warning";
                          }
                        
                          var html = "<li><a class='wirelessNetwork' id='" + networks[i].ssid +
                          "' href='javascript:void(0);' data-encryption-status='" + networks[i].encryption + "'" +
                          "data-encryption-type='" + networks[i].encryptionType + "'" + "data-group-cipher='" + networks[i].groupCipher + "'" +
                          "data-pairwise-cipher='" + networks[i].pairwiseCipher + "'" + "data-authentication='" + networks[i].authenticationSuite + "'" +
                          ">" + networks[i].ssid + " &lt;" + networks[i].mac + "&gt; <span class='"+frequencyLabel+"'>"+frequency+"</span>" + "<i class='" + encryptionIcon + "'></i></a></li>";
                          $('ul#wirelessNetworkListWidar').append(html);
                      }
                  }
                  $("a#wirelessNetworkDropdown").removeClass('disabled');
}

/*
 This function accepts the selector attribute for 
 which to append the results back to the dom element.
 */
function widarWirelessNetworks()
{
    $.ajax(
    {
        method: "post",
        url: "/wireless/getWirelessNetworkScanResults",
        data:
        {
          type: "ajax"
        },
        success: function (data)
        {
          if (data !== null)
          {
              if (data.networks)
              {
                  var networks = data.networks;
                  handleDropdownMenu(networks);
                  for (var i = 0; i < networks.length; i++)
                  {
                      if (networks[i].ssid !== "<?php echo $dynamicSsid; ?>" && networks[i].ssid !== "")
                      {
                          if(networks[i].frequency.startsWith("2")) {
                              $("select#2gNetworks").append("<option data-mac='"+networks[i].mac+"' value='" + networks[i].ssid + "'>" + networks[i].ssid + " &lt;" + networks[i].mac + "&gt;</option>");
                              $("select#2gNetworks").trigger("chosen:updated");
                              var usedNames = {};
                              $("select#2gNetworks > option").each(function ()
                              {
                                  //if (usedNames[this.text])
                                  if(usedNames[$(this).data('mac')])
                                  {
                                      $(this).remove();
                                      $("select#2gNetworks").trigger("chosen:updated");
                                  }
                                  else
                                  {
                                      //usedNames[this.text] = this.value;
                                      usedNames[$(this).data('mac')] = this.text;
                                  }
                              });
                          } else if(networks[i].frequency.startsWith("5")) {
                              $("select#5gNetworks").append("<option data-mac='"+networks[i].mac+"' value='" + networks[i].ssid + "'>" + networks[i].ssid + " &lt;" + networks[i].mac + "&gt;</option>");
                              $("select#5gNetworks").trigger("chosen:updated");
                              var usedNames = {};
                              $("select#5gNetworks > option").each(function ()
                              {
                                  if (usedNames[$(this).data('mac')])
                                  {
                                      $(this).remove();
                                      $("select#5gNetworks").trigger("chosen:updated");
                                  }
                                  else
                                  {
                                      usedNames[$(this).data('mac')] = this.text;
                                      //usedNames['mac'] = $(this).data('mac');
                                  }
                              });
                          }
                      }
                  }
              }
          }
        }
    });
}
widarWirelessNetworks();

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
<?php endif; //if job isset?>
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
