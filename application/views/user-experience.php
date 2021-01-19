<?php
 defined('BASEPATH') or exit('No direct script access allowed');
if (isset($currentJob->id) && $currentJob->id !== 0) :
    $settings = $currentJob->getDetails();
    ?>
 <style>
.st-block {
    text-align: center;
}

.st-value>span:empty::before {
    content: "0.00";
    color: #636c72;
}

 .display-4 {
    font-size: 3.5rem;
    font-weight: 300;
    line-height: 1.1;
}
</style>
<div id="page-wrapper" class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
      <div class="row">
               <span id="error">
        </span>
          <div class="col-lg-12 m-b-md">
          <div class="ibox float-e-margins">
              <div class="ibox-title">
                  <h5>User Experience</h5>

                        <?php if ($wirelessConn['authenticated']) : ?>
                      <div id="widar-auth" class="row">
                      <div class="col-lg-4 col-offset-lg-4">
                      <span class="auth-intro">Authenticated to:</span> <span id="auth-network" class="font-bold"><?php echo $wirelessConn['ssid'] ?></span>
                      </div>
                      <div class="col-lg-4">
                        <span class="auth-bitrate">Bit Rate:</span> <span id="auth-bitrate" class="font-bold"><?php echo $wirelessConn['bitRate'] ?></span>
                      </div>
                      </div>
                    <?php else : ?>
                    <div id="widar-auth" class="row hidden">
                      <div class="col-lg-4 col-offset-lg-4">
                        <span class="auth-intro">Authenticated to:</span> <span id="auth-network" class="font-bold"></span>
                      </div>
                      <div class="col-lg-4">
                        <span class="auth-bitrate">Bit Rate:</span> <span id="auth-bitrate" class="font-bold"></span>
                      </div>
                    </div>
                      
                    <?php endif; ?>

              </div>
              <div class="ibox-content">
                <div class="row">
                   <a class="btn btn-primary" id="st-start" onclick="startTest()">Start</a>

                  <div class="col-lg-4 col-sm-3">
                  
                  <div class="btn-group">
                      <a href="#" id="wirelessNetworkDropdown" class="btn btn-primary dropdown-toggle disabled" data-toggle="dropdown" data-dismiss="tooltip">
                          <i id="wifiLogo" class="fa fa-spin fa-spinner"></i> <span id="wifiText">Populating Networks - please wait</span>
                          <span class="caret"></span>
                      </a>
                  <ul id="wirelessNetworkListWidar" class="dropdown-menu">
                  </ul>
                  </div>
                  </div>

                 </div>

                  <div class="row">
                      <div class="col-lg-3 col-sm-6 mb-3 st-block">
                          <h3>Ping</h3>
                          <p class="display-4 st-value"><span id="st-ping"></span></p>
                          <p class="lead">ms</p>
                      </div>
                      <!--<div class="col-lg-3 col-sm-6 mb-3 st-block">
                          <h3>Jitter</h3>
                          <p class="display-4 st-value"><span id="st-jitter"></span></p>
                          <p class="lead">ms</p>
                      </div>-->
                      <div class="col-lg-3 col-sm-6 mb-3 st-block">
                          <h3>Download</h3>
                          <p class="display-4 st-value"><span id="st-download"></span></p>
                          <p class="lead">MB/s</p>
                      </div> 

                  </div>
                  <div class="row">
                      <div class="col-lg-12" id="st-results">
                            
                              <div class="row">
                      <div class="col-lg-2 col-md-6 col-sm-4 mb-3" id="amazon"> 
                          <div class="st-block"><h3>Amazon</h3><img class="img" src="/assets/images/speedtest/amazon.png"></div>
                          <p class="display-4 st-result">
                              <div class="row"><span class="col-lg-6">480p</span><span class="col-lg-6 st-480p"></span></div>
                              <div class="row"><span class="col-lg-6">720p</span><span class="col-lg-6 st-720p"></span></div>
                              <div class="row"><span class="col-lg-6">1080p</span><span class="col-lg-6 st-1080p"></span></div>
                              <div class="row"><span class="col-lg-6">4k</span><span class="col-lg-6 st-4k"></span></div>
                          </p>
                      </div>
                      <div class="col-lg-2 col-md-6 col-sm-4 mb-3" id="netflix">
                          <div class="st-block"><h3>Netflix</h3><img class="img" src="/assets/images/speedtest/netflix.png"></div>
                           <p class="display-4 st-result">
                              <div class="row"><span class="col-lg-6">480p</span><span class="col-lg-6 st-480p"></span></div>
                              <div class="row"><span class="col-lg-6">720p</span><span class="col-lg-6 st-720p"></span></div>
                              <div class="row"><span class="col-lg-6">1080p</span><span class="col-lg-6 st-1080p"></span></div>
                              <div class="row"><span class="col-lg-6">4k</span><span class="col-lg-6 st-4k"></span></div>
                          </p>
                      </div>
                      <div class="col-lg-2 col-md-6 col-sm-4 mb-3" id="youtube">
                          <div class="st-block"><h3>Youtube</h3><img class="img" src="/assets/images/speedtest/youtube.png"></div>
                               <p class="display-4 st-result">
                              <div class="row"><span class="col-lg-6">480p</span><span class="col-lg-6 st-480p"></span></div>
                              <div class="row"><span class="col-lg-6">720p</span><span class="col-lg-6 st-720p"></span></div>
                              <div class="row"><span class="col-lg-6">1080p</span><span class="col-lg-6 st-1080p"></span></div>
                              <div class="row"><span class="col-lg-6">4k</span><span class="col-lg-6 st-4k"></span></div>
                          </p>
                      </div>
                      <div class="col-lg-2 col-md-6 col-sm-4 mb-3" id="hulu">
                          <div class="st-block"><h3>Hulu</h3><img class="img" src="/assets/images/speedtest/hulu.png"></div>
                          <p class="display-4 st-result">
                              <div class="row"><span class="col-lg-6">480p</span><span class="col-lg-6 st-480p"></span></div>
                              <div class="row"><span class="col-lg-6">720p</span><span class="col-lg-6 st-720p"></span></div>
                              <div class="row"><span class="col-lg-6">1080p</span><span class="col-lg-6 st-1080p"></span></div>
                              <div class="row"><span class="col-lg-6">4k</span><span class="col-lg-6 st-4k"></span></div>
                          </p>
                      </div>
                      <div class="col-lg-2 col-md-6 col-sm-4 mb-3" id="vudu">
                          <div class="st-block"><h3>Vudu</h3><img class="img" src="/assets/images/speedtest/vudu.png"></div>
                          <p class="display-4 st-result">
                              <div class="row"><span class="col-lg-6">480p</span><span class="col-lg-6 st-480p"></span></div>
                              <div class="row"><span class="col-lg-6">720p</span><span class="col-lg-6 st-720p"></span></div>
                              <div class="row"><span class="col-lg-6">1080p</span><span class="col-lg-6 st-1080p"></span></div>
                              <div class="row"><span class="col-lg-6">4k</span><span class="col-lg-6 st-4k"></span></div>
                          </p>
                      </div>

                  </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-6">
                         <!--<video controls="" name="media">
                              <source src="<?php site_url() ?>home/videos?video=att-4k.mp4" type="video/mp4">
                          </video>-->
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

    <script type="text/javascript">

      var quality = {
          netflix: {
              standard : {
                  speed: 3.0,
                  quality: '480p'
              },
              sevenTwenty: {
                  speed: 4.0,
                  quality: '720p'
              },
              tenEighty: {
                  speed: 5.0,
                  quality: '1080p'
              },
              fourK: {
                  speed: 25,
                  quality: '4k'
              }
          },
          vudu: {
              standard : {
                  speed: 2.3,
                  quality: '480p'
              },
              sevenTwenty: {
                  speed: 4.5,
                  quality: '720p'
              },
              tenEighty: {
                  speed: 5.0,
                  quality: '1080p'
              },
              fourK :{
                  speed: 11,
                  quality: '4k'
              }
          },
          hulu: {
              standard: {
                  speed: 1.5,
                  quality: '480p'
              },
              sevenTwenty: {
                  speed: 3,
                  quality: '720p'
              },
              tenEighty: {
                  speed: 6,
                  quality: '1080p'
              },
              fourK: {
                  speed: 13,
                  quality: '4k'
              }
          },
          amazon: {
              standard: {
                  speed: 1,
                  quality: '480p'
              },
              sevenTwenty: {
                  speed: 3.5,
                  quality: '720p'
              },
              tenEighty: {
                  speed: 3.5,
                  quality: '1080p'
              },
              fourK: {
                  speed: 15,
                  quality: '4k'
              }
          },
          youtube: {
              standard: {
                  speed: 3,
                  quality: '480p'
              },
              sevenTwenty: {
                  speed: 5,
                  quality: '720p'
              },
              tenEighty: {
                  speed: 5,
                  quality: '1080p'
              },
              fourK: {
                  speed: 25,
                  quality: '4k'
              }
          }
      }

      function startTest() {
          $('a#st-start').addClass('hidden');
          $('#st-ping').html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
      $.ajax({
          method: "post",
          url: "/userexperience/startUxTest",
          dataType: "json",
          success: function(data) {
              console.log(data);
          }
      });            
      }

function analyzeSpeedTestNumbers(speed)
{
                          for (var key in quality) {
                              if(!quality.hasOwnProperty(key)) continue;
                              for (var innerKey in quality[key])
                              {
                                  if(!quality[key].hasOwnProperty(innerKey)) continue;

                                  if (speed > quality[key][innerKey].speed) {

                                  $('#' + key + ' .st-' + quality[key][innerKey].quality).html('<i class="fa fa-check-square text-navy fa-fw"></i>');
                                  } else {
                                      $('#' + key + ' .st-' + quality[key][innerKey].quality).html('<i class="fa fa-exclamation text-danger fa-fw"></i>');
                                  }
                                  //console.log(key + ' Speed: ' + quality[key][innerKey].speed)
                              }
                             // console.log(quality[key])
                          }

}



      function stopTest() {
          if (worker) {
              worker.postMessage('abort')
          }
      }

$(document).on('click','button#submitNetworkTest', function(e) {
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
                  $("a#wirelessNetworkDropdown i#wifiLogo").removeClass("fa fa-spin fa-spinner");
                  $("a#wirelessNetworkDropdown i#wifiLogo").addClass("fa fa-wifi");
                  $("a#wirelessNetworkDropdown span#wifiText").text("Select Network");
                  $("a#wirelessNetworkDropdown").removeClass('disabled');
                  //$("p#loadingText").addClass('hidden');
}



// This function is called when a network is being connected to on behalf of
// widar and it returns a successful connection
function widarNetwork(obj)
{
  console.log(obj)
  if(typeof obj.ssid !== 'undefined') {
    $('span#auth-network').text(obj.ssid);
    $('div#widar-auth').removeClass('hidden');

    /*if(!elem.checked) {
      $('.js-switch').trigger('click');
    }*/
    // Re enable the start button
    //widarHoldStartButton(false);
  }
}

/*
 This function accepts the selector attribute for 
 which to append the results back to the dom element.
 */
function uxWirelessNetworks()
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
              }
          }
        }
    });
}
uxWirelessNetworks();

<?php if ($this->config->item('pusher_enabled')) : ?>

    var uxChannel = pusher.subscribe('ux');

    uxChannel.bind('message', function(notification) {
        var message = notification.message;
        
        if(typeof message !== 'undefined') {
            console.log(message);
            if(typeof message.jitter !== 'undefined') {
                $('#st-jitter').text(message.jitter);
            }
            if(typeof message.ping !== 'undefined') {
                $('#st-ping').text(message.ping);
            }
            if(typeof message.speed !== 'undefined') {
                $('#st-download').text(message.speed);
                analyzeSpeedTestNumbers(message.speed);
            }
        }
    });

    uxChannel.bind('ping', function(notification) {
        var message = notification.message;
        
        if(typeof message !== 'undefined') {
            console.log(message);
            if(typeof message.jitter !== 'undefined') {
                $('#st-jitter').text(message.jitter);
            }
            if(typeof message.ping !== 'undefined') {
                $('#st-ping').text(message.ping);
            }
        }
    });

    uxChannel.bind('complete', function(notification) {
        var message = notification.message;
        console.log('Complete');
        console.log(message);
        $('#st-ping').text(message.data.server.ping);
        $('#st-download').text((message.data.speeds.download).toFixed(2));
        $('a#st-start').removeClass('hidden');
        //console.log(message);
    });

    uxChannel.bind('error', function(notification) {
        var message = notification.message;
        $('span#error').append('<div class="alert alert-danger">' + message + '</div>')
        console.log(message);
    });


    uxChannel.bind('probe', function(notification) {
        var message = notification;
        console.log(message);
    });

    uxChannel.bind('details', function(notification) {
        var message = notification;
        console.log(message);
    });

    <?php endif; ?>

    </script>
<?php  endif; ?>

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