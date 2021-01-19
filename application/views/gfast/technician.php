<?php
 defined('BASEPATH') or exit('No direct script access allowed');
if (isset($currentJob->id) && $currentJob->id !== 0) {
    $settings = $currentJob->getDetails();
}
    ?>
    <style>
.p-3 {
    padding: 1rem!important;
}

  </style>
<div id="page-wrapper" class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
<div class="container-fluid">
<div class="row">
<a class="btn btn-primary" onclick="javascript:cancelGfastCounterRefresh()">Stop Refresh</a>

        <?php if ($this->gfast->isLoaded()  && $this->gfast->isGfastCliRunning() !== 1) {
            $gfastCounters = $this->gfast->getGfastCounters();

            if (!empty($gfastCounters)) {
                $gfastCounters = json_decode($gfastCounters);
            }
} ?>
<div class="tabs-container">
<ul class="nav nav-tabs">
<li class="active">
<a data-toggle="tab" href="#summary" ><i class="fa fa-list"></i>Summary</a>
</li>
<li>
<a data-toggle="tab" href="#errors"><i class="fa fa-"></i>Errors</a>
</li>
<li>
<a data-toggle="tab" href="#events"><i class="fa fa-"></i>Events</a>
</li>
<li>
<a data-toggle="tab" href="#graphs"><i class="fa fa-line-chart"></i>Graphs</a>
</li>
</ul>
<div class="tab-content">

<div class="tab-pane active" id="summary" >
    <div class="row">
        <div class="col-sm-12">
          <table class="table table-striped" id="summary">
            <thead>
              <tr>
                <th></th>
            <th>Upstream</th>
            <th>Downstream</th>
          </tr>
          </thead>
          <tbody>
            <tr id="actual_rate">
              <td><b>Actual Rate</b></td>

              <td class="up">
                <?php if (is_object($gfastCounters->derivedCounters)) : ?>
                    <?php echo $gfastCounters->derivedCounters->EocTxRateInKbps;?>
                    <?php else : ?>
              N/A
                <?php endif; ?>
             </td>
              <td class="down">
                <?php if (is_object($gfastCounters->derivedCounters)) : ?>
                    <?php echo $gfastCounters->derivedCounters->EocRxRateInKbps;?>
                    <?php else : ?>
              N/A
                <?php endif; ?>
              </td>
            </tr>
            <tr id="capacity">
              <td><b>Capacity</b></td>
              <td class="up">N/A</td>
              <td class="down">N/A</td>
            </tr>
            <tr id="margin">
              <td><b>Margin</b></td>
              <td class="up">
                <?php if (is_object($gfastCounters->fduInitCounters)) : ?>
                    <?php echo $gfastCounters->fduInitCounters->usMargin;?> dB
                    <?php else : ?>
              N/A
                <?php endif; ?>
              </td>
              <td class="down">
                <?php if (is_object($gfastCounters->fduInitCounters)) : ?>
                    <?php echo $gfastCounters->fduInitCounters->dsMargin;?> dB
                    <?php else : ?>
              N/A
                <?php endif; ?>
              </td>
            </tr>
            <tr id="crc">
              <td><b>CRC</b></td>
              <td class="up">N/A</td>
              <td class="down">
                <?php if (is_object($gfastCounters->derivedCounters)) : ?>
                    <?php echo $gfastCounters->derivedCounters->crcAnomaly;?>
                    <?php else : ?>
              N/A
                <?php endif; ?>
              </td>
            </tr>
            <tr id="rtx_uc">
              <td><b>RTX-UC</b></td>
              <td class="up">
                <?php if (is_object($gfastCounters->framerRawCounters)) : ?>
                    <?php echo $gfastCounters->framerRawCounters->rtxuc;?>
                    <?php else : ?>
              N/A
                <?php endif; ?>
              </td>
              <td class="down">
                <?php if (is_object($gfastCounters->framerRawCounters)) : ?>
                    <?php echo $gfastCounters->framerRawCounters->rtxuc;?>
                    <?php else : ?>
              N/A
                <?php endif; ?>
              </td>
            </tr>
          </tbody>
          </table>
        </div>
    </div>
</div>
<div class="tab-pane" id="errors">
    <div class="row">
        <div class="col-sm-12">
          <table class="table table-striped" id="errors">
            <thead>
              <tr>
                <th></th>
            <th>Upstream</th>
            <th>Downstream</th>
          </tr>
          </thead>
          <tbody>
            <tr id="crc">
              <td><b>CRC</b></td>
              <td class="up">N/A</td>
              <td class="down">
                <?php if (is_object($gfastCounters->derivedCounters)) : ?>
                    <?php echo $gfastCounters->derivedCounters->crcAnomaly;?>
                    <?php else : ?>
              N/A
                <?php endif; ?>
              </td>
            </tr>
            <tr id="es">
              <td><b>ES</b></td>
              <td class="up">N/A</td>
              <td class="down">
                <?php if (is_object($gfastCounters->derivedCounters)) : ?>
                    <?php echo $gfastCounters->derivedCounters->erroredSeconds;?>
                    <?php else : ?>
              0
                <?php endif; ?>
              </td>
            </tr>
            <tr id="ses">
              <td><b>SES</b></td>
              <td class="up">N/A</td>
              <td class="down">
                <?php if (is_object($gfastCounters->derivedCounters)) : ?>
                    <?php echo $gfastCounters->derivedCounters->severlyErroredSeconds;?>
                    <?php else : ?>
              0
                <?php endif; ?>
              </td>
            </tr>
            <tr id="uas">
              <td><b>UAS</b></td>
              <td class="up">N/A</td>
              <td class="down">
                <?php if (is_object($gfastCounters->derivedCounters)) : ?>
                    <?php echo $gfastCounters->derivedCounters->unavailableSeconds;?>
                    <?php else : ?>
              0
                <?php endif; ?>

              </td>
            </tr>
            <tr ><td colspan="3"><p class="text-center"><strong>Alarm Seconds</strong></p></td></tr>
            <tr id="los">
              <td><b>LOS</b></td>
              <td class="up">0</td>
              <td class="down">
                <?php if (is_object($gfastCounters->derivedCounters)) : ?>
                    <?php echo $gfastCounters->derivedCounters->lossOfSignalSeconds;?>
                    <?php else : ?>
              0
                <?php endif; ?>

              </td>
            </tr>
            <tr id="lom">
              <td><b>LOM</b></td>
              <td class="up">0</td>
              <td class="down">
                <?php if (is_object($gfastCounters->derivedCounters)) : ?>
                    <?php echo $gfastCounters->derivedCounters->lossOfMarginFailure;?>
                    <?php else : ?>
              0
                <?php endif; ?>
              </td>
            </tr>
            <tr id="lor">
              <td><b>LOR</b></td>
              <td class="up">0</td>
              <td class="down">
                <?php if (is_object($gfastCounters->derivedCounters)) : ?>
                    <?php echo $gfastCounters->derivedCounters->lossOfRMCSeconds;?>
                    <?php else : ?>
              0
                <?php endif; ?>
              </td>
            </tr>
          </tbody> 
          </table>
        </div>
    </div>
</div>
<div class="tab-pane" id="events">
    <div class="row">
        <div class="col-sm-12">
          <table class="table table-striped" id="events">
          <tbody>
            <tr id="full_inits">
              <td><b>Full Inits</b></td>
              <td colspan="2">
                <?php if (is_object($gfastCounters->derivedCounters)) : ?>
                    <?php echo $gfastCounters->derivedCounters->fullInits;?>
                    <?php else : ?>
              0
                <?php endif; ?>
              </td>
            </tr>
            <tr id="failed_full_inits">
              <td><b>Failed Full Inits</b></td>
              <td colspan="2">
                <?php if (is_object($gfastCounters->derivedCounters)) : ?>
                    <?php echo $gfastCounters->derivedCounters->failedFullInits;?>
                    <?php else : ?>
              0
                <?php endif; ?>
              </td>
            </tr>
            <tr id="fast_inits">
              <td><b>Fast Inits</b></td>
              <td colspan="2">
                <?php if (is_object($gfastCounters->derivedCounters)) : ?>
                    <?php echo $gfastCounters->derivedCounters->fastInits;?>
                    <?php else : ?>
              0
                <?php endif; ?>
              </td>
            </tr>
            <tr id="failed_fast_inits">
              <td><b>Failed Fast Inits</b></td>
              <td colspan="2">
                <?php if (is_object($gfastCounters->derivedCounters)) : ?>
                    <?php echo $gfastCounters->derivedCounters->failedFastInits;?>
                    <?php else : ?>
              0
                <?php endif; ?>
              </td>
            </tr>
            <tr>
              <td></td>
              <td><b>Upstream</b></td>
              <td><b>Downstream</b></td>
            </tr>
            <tr id="tiga">
              <td><b>TIGA</b></td>
              <td class="up">
                <?php if (is_object($gfastCounters->framerRawCounters)) : ?>
                    <?php echo $gfastCounters->framerRawCounters->tigaExngCnt;?>
                    <?php else : ?>
              0
                <?php endif; ?>
              </td>
              <td class="down">
                <?php if (is_object($gfastCounters->framerRawCounters)) : ?>
                    <?php echo $gfastCounters->framerRawCounters->tigaExngCnt;?>
                    <?php else : ?>
              0
                <?php endif; ?>

              </td>
            </tr>
            <tr id="bitswaps">
              <td><b>Bitswaps</b></td>
              <td class="up">
                <?php if (is_object($gfastCounters->framerRawCounters)) : ?>
                    <?php echo $gfastCounters->framerRawCounters->bitswapTxExngCnt;?>
                    <?php else : ?>
              0
                <?php endif; ?>

              </td>
              <td class="down">
                <?php if (is_object($gfastCounters->framerRawCounters)) : ?>
                    <?php echo $gfastCounters->framerRawCounters->bitswapRxExngCnt;?>
                    <?php else : ?>
              0
                <?php endif; ?>
              </td>
            </tr>
            <tr id="sra">
              <td><b>SRA</b></td>
              <td class="up">
                <?php if (is_object($gfastCounters->framerRawCounters)) : ?>
                    <?php echo $gfastCounters->framerRawCounters->sraTxTo;?>
                    <?php else : ?>
              0
                <?php endif; ?>
              </td>
              <td class="down">
                <?php if (is_object($gfastCounters->framerRawCounters)) : ?>
                    <?php echo $gfastCounters->framerRawCounters->sraRxTo;?>
                    <?php else : ?>
              0
                <?php endif; ?>
              </td>
            </tr>
            <tr id="fra">
              <td><b>FRA</b></td>
              
              <td class="up">
                <?php if (is_object($gfastCounters->framerRawCounters)) : ?>
                    <?php echo $gfastCounters->framerRawCounters->fraTxExngCnt;?>
                    <?php else : ?>
              0
                <?php endif; ?>
              </td>
              <td class="down">
                <?php if (is_object($gfastCounters->framerRawCounters)) : ?>
                    <?php echo $gfastCounters->framerRawCounters->fraRxExngCnt;?>
                    <?php else : ?>
              0
                <?php endif; ?>
              </td>
            </tr>
          </tbody> 
          </table>
        </div>
    </div>
</div>
<div class="tab-pane" id="graphs">
    <div class="row">
        <div class="col-sm-12">
          <canvas id="dslCanvas"></canvas>
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
<script src="<?php echo $this->config->item('plugins_directory');?>chart.js\dist\Chart.min.js"></script>
    <script type="text/javascript">


var gfastCounterFunctionLoop;

gfastCounterFunctionLoop = window.setInterval(getGfastCounters, 15000);


function populate(frm, data) {   
    $.each(data, function(key, value) {  
        var ctrl = $('[name='+key+']', frm);  
        switch(ctrl.prop("type")) { 
            case "radio": case "checkbox":   
                ctrl.each(function() {
                    if($(this).attr('value') == value) $(this).attr("checked",value);
                });   
                break;  
            default:
                ctrl.val(value); 
        }  
    });  
}


function saveValuesToDatabase(json) {
      var unit = store.get('mdu_unit')
      $.ajax({
          url: "/gfast/saveEtrData",
          dataType: "json",
          data: {unit: unit, data: json},
          success: function(data) {
            console.log(data);
          }
      });
}

function getGfastCounters() {

      $.ajax({
          url: "/gfast/getGfastCounters",
          dataType: "json",
          success: function(data) {
            if(data !== "") {
            var json = JSON.parse(data);
            console.log(json);
            if(json.hasOwnProperty('derivedCounters')){
              $('#es .down').text(json.derivedCounters.erroredSeconds);
              $('#ses .down').text(json.derivedCounters.severlyErroredSeconds);              
              $('#uas .down').text(json.derivedCounters.unavailableSeconds);

              //console.log('derivedCounters exists!')
            }
            //$("#total_etr").val(json.ETR);
            //$("#down_etr").val(json.RxETR);
            //$("#up_etr").val(json.TxETR);
          }
          }
      });
}


function cancelGfastCounterRefresh() {
  window.clearInterval(gfastCounterFunctionLoop) 
}


$(document).on('click', 'button#attemptLoad', function(e) {
  e.preventDefault();

      $.ajax({
          url: "/gfast/loadGfast",
          dataType: "json",
          success: function(data) {
            console.log(data);
          }
      });

});


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
</script>

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