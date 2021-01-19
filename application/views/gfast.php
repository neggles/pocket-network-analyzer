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
                  <h5>G.Fast</h5>
              </div>
              <div class="ibox-content">
                <?php if (!$this->gfast->isLoaded()): ?>
                  <div class="alert alert-danger">
                    G.Fast is not loaded
                    <button class="btn btn-danger" id="loadGfast">Load G.Fast</button>
                  </div>
                  <?php else: ?>
                    <div class="row">
                      <div class="col-lg-12">
                        <div class="col-lg-4">
                    <?php echo $this->gfast->getGfastCounters();?>
                  </div>
                  <div class="col-lg-4">
                    <?php echo $this->gfast->getGfastNumberOfDevices();?>
                  </div>
                  </div>

                <?php endif; ?>

              </div>
              </div>
      </div>
</div>
</div>
<?php $this->load->view('additional/footer');?>
</div>

    <script type="text/javascript">

$(document).on('click','button#loadGfast', function(e) {
  console.log('Load G.Fast button clicked')
      $.ajax({
          url: "/gfast/loadGfast",
          dataType: "json",
          success: function(data) {
            console.log(data);
              if (data.status == true) {
                  alertify.success(data.msg);
                  //$('form#createIssueForm')[0].reset();
              } else if (data.status == false) {
                  alertify.error(data.msg);
              }
          }
      });
});


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