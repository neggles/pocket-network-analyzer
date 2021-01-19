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
    <?php if (!$this->gfast->isGfastModuleInstalled()) : ?>
      <div id="installWrapper">
      <a class="btn btn-primary" id="installGfast" href="#">Install G.Fast</a>
      <p class="help-text"><small>Make sure the unit has an internet connection.<br/>After the "Install Complete" message, make sure cable is plugged in and then you will be able to refresh the connection for G.Fast</small></p>
    </div>
    <?php endif; ?>
<div class="row">
  <div class="col-sm-12">
    <div class="row">
    <div class="col-sm-6">
  <div class="time">
    <h3>System Time</h3>
    <div class="form-group">
      <div class="input-group">
        <p id="clock"></p>
   <input class="form-control readonly" type="text"  id="dateString">
   <div class="input-group-btn">
    <button class="btn btn-primary" id="editTime" type="button">
      Edit
    </button>
   </div>
 </div>
 </div>
 </div>
</div>
<div class="col-sm-6">
  <div id="outerDateTimeLocal" style="display:none;">  
    <p><small>If this date looks right, click save.</small></p>
    <div class="form-group">
      <div class="input-group">
    <input class="form-control" type="text" id="dateTimeLocal">
    <div class="input-group-btn">
      <button class="btn btn-primary" id="saveNewTime">
        Save
      </button>
    </div>
  </div>
  </div>
  </div>
  </div>
</div>
</div>
<div class="col-sm-6">
    <div style="background:#FFF;">
        <p class="system_status"><strong>System Status</strong></p>        
        <?php if (!$gfastLoaded = $this->gfast->isConnected()) : ?>
          <img id="connectionStatus" src="/assets/images/gfast/connection-bad.png" class="img-responsive"/>
          <div class="text-center" id="loadGfastWrapper">
          <button class="btn btn-danger" id="attemptLoad">Refresh</button>
          <p class="help-text"><small>After plugging in the equipment, press the button above.</small></p>
        </div>
        <?php else : ?>
        <img id="connectionStatus" src="/assets/images/gfast/e_connected.png" class="img-responsive"/>
          <div class="text-center" id="loadGfastWrapper">
          <button class="btn btn-danger" id="attemptLoad">Refresh</button>
          <p class="help-text"><small>After plugging in the equipment, press the button above.</small></p>
        </div>

        <?php endif; ?>
        <form action="" class="p-3">
          <div class="row">
            <div class="form-group">
                <div class="col-lg-6">
                    <input type="hidden" value="10" name="refreshRate" id="refreshRate" class="form-control form-control-sm">
                </div>
            </div>
          </div>
            <?php if (!$gfastLoaded) : ?>
            <button class="btn btn-primary" role="button" id="runEtrTest" disabled>Begin Test</button>
            <?php else : ?>
              <button class="btn btn-primary" role="button" id="runEtrTest" >Begin Test</button>
            <?php endif; ?>
            <a class="btn btn-primary" id="saveResults" disabled>Save Results</a>
        </form>
    </div>
    <div style="background:#FC6;" class="container-fluid">
     
        <?php if ($this->gfast->isLoaded()  && $this->gfast->isGfastCliRunning() !== 1) {
            $etrValues = $this->gfast->getGfastLineEtrs();
         
            if (!empty($etrValues)) {
                $etrValues = json_decode($etrValues);
            }
} ?>
    <form id="etr_values">
        <div class="text-md-center">
              <p>ETR Measurements in Mbp/s</p>
             <div class="form-group">      
                    <label for="total_etr">TOTAL ETR:</label>       
                    <div class="input-group">    
                      <span class="input-group-addon text-success" id="total_etr_status"></span>
                      <input type="text" class="form-control form-control-sm" id="total_etr" value="<?php echo (int) $etrValues->ETR; ?>">   
                      </div>  
                      <p class="help-text"><small>Value greater than 550 is PASS</small></p>               
                  </div>           
              <div class="form-group">
                    <label for="down_etr">DOWN ETR#:</label>
                      <input type="text" class="form-control form-control-sm" id="down_etr" value="<?php echo (int) $etrValues->RxETR; ?>">
                   </div>                    
                    <div class="form-group">        
                    <label for="up_etr">UP ETR:</label>                   
                      <input type="text" class="form-control form-control-sm" id="up_etr" value="<?php echo (int) $etrValues->TxETR; ?>">
                   </div>                    
                    
              <a href="#" class="btn btn-primary" onclick="javascript:clearResults()">Clear Results</a>  
              </div>
              <input type="hidden" name="json_values" id="json_values" value="">
            </form>
    </div>
    <div style="background:#fff;">
        <p class="system_status"><strong>History</strong></p>

          <table class="table" id="etrHistory">
            <thead>
              <tr>
                <th>Date</th>
                <th>Total ETR</th>
                <th>Down ETR</th>
                <th>Up ETR</th>
              </tr>
            </thead>
            <tbody>
              
          </tbody>
          </table>
    </div>
</div>
<div class="col-sm-6" id="mdu_complex_outer">
    <div style="background:#CCC;" >
        <h2><strong>MDU Complex</strong></h2>
        <?php $complexes = $this->gfast->getComplexesFromDatabase(); ?>
        <div class="col-lg-12">
        <?php if (!empty($complexes)) :?>
        
          <div class="col-lg-6">
        <div class="form-group">
          <label>Previous MDU</label>
          <select class="form-control chosen-select-width" id="mdu_select">
            <option>Default</option>
            <?php foreach ($complexes as $complex) : ?>
              <option value="<?php echo $complex->id ?>" data-info='<?php echo json_encode($complex); ?>'><?php echo $complex->name ?></option>
            <?php endforeach;?>
          </select>
        </div>
      </div>

        <?php endif; ?>
      <div class="col-lg-6">
        <button class="btn btn-primary" id="showComplex" disabled>Show</button>
        <button class="btn btn-primary" id="newComplex">New</button>
      </div>
      </div>
        
        <form id="complex" class="p-3" style="display:none;">
          <input name="id" id="id" type="hidden">
                            <div class="form-group">
                                <label for="name">MDU Name:</label>                               
                                    <input type="text" name="name" id="name" class="form-control">                             
                            </div>
                            <div class="form-group" >
                                <label for="street">Street Address:</label>                               
                                    <input type="text" name="street" id="street" class="form-control">                               
                            </div>
                            <div class="form-group" >
                                <label for="num_floors">Number of Floors:</label>                            
                                    <input type="number" name="num_floors" id="num_floors" class="form-control">                              
                            </div>                 
                            <div class="form-group">
                                <label for="city">City:</label>                           
                                    <input type="text" name="city" id="city" class="form-control">                             
                            </div>
                            <div class="row">
                              <div class="col-sm-6">
                            <div class="form-group">
                                <label for="state">State:</label>
                                <select name="state" id="state" class="form-control chosen-select-width">
                                  <option value="AL">Alabama</option>
                                  <option value="AK">Alaska</option>
                                  <option value="AZ">Arizona</option>
                                  <option value="AR">Arkansas</option>
                                  <option value="CA">California</option>
                                  <option value="CO">Colorado</option>
                                  <option value="CT">Connecticut</option>
                                  <option value="DE">Delaware</option>
                                  <option value="DC">District Of Columbia</option>
                                  <option value="FL">Florida</option>
                                  <option value="GA">Georgia</option>
                                  <option value="HI">Hawaii</option>
                                  <option value="ID">Idaho</option>
                                  <option value="IL">Illinois</option>
                                  <option value="IN">Indiana</option>
                                  <option value="IA">Iowa</option>
                                  <option value="KS">Kansas</option>
                                  <option value="KY">Kentucky</option>
                                  <option value="LA">Louisiana</option>
                                  <option value="ME">Maine</option>
                                  <option value="MD">Maryland</option>
                                  <option value="MA">Massachusetts</option>
                                  <option value="MI">Michigan</option>
                                  <option value="MN">Minnesota</option>
                                  <option value="MS">Mississippi</option>
                                  <option value="MO">Missouri</option>
                                  <option value="MT">Montana</option>
                                  <option value="NE">Nebraska</option>
                                  <option value="NV">Nevada</option>
                                  <option value="NH">New Hampshire</option>
                                  <option value="NJ">New Jersey</option>
                                  <option value="NM">New Mexico</option>
                                  <option value="NY">New York</option>
                                  <option value="NC">North Carolina</option>
                                  <option value="ND">North Dakota</option>
                                  <option value="OH">Ohio</option>
                                  <option value="OK">Oklahoma</option>
                                  <option value="OR">Oregon</option>
                                  <option value="PA">Pennsylvania</option>
                                  <option value="RI">Rhode Island</option>
                                  <option value="SC">South Carolina</option>
                                  <option value="SD">South Dakota</option>
                                  <option value="TN">Tennessee</option>
                                  <option value="TX">Texas</option>
                                  <option value="UT">Utah</option>
                                  <option value="VT">Vermont</option>
                                  <option value="VA">Virginia</option>
                                  <option value="WA">Washington</option>
                                  <option value="WV">West Virginia</option>
                                  <option value="WI">Wisconsin</option>
                                  <option value="WY">Wyoming</option>
                                </select>      
                              
                            </div>    
                          </div>
                          <div class="col-sm-6">

                            <div class="form-group">
                                <label for="zip">Zip:</label>
                                    <input type="text" name="zip" id="zip" class="form-control">                             
                            </div> 
                          </div>
                            </div>  

                            <div class="form-group">
            <label for="con_date">Building Construction Date</label>            
      
                    <select id="con_date" name="con_date" class="form-control">
                        <option value="1960">
                          1960 or Earlier
                        </option>
                        <option value="1970">
                          1970's
                        </option>
                        <option value="1980">
                          1980's
                        </option>
                        <option value="1990">
                          1990's
                        </option>
                        <option value="2000">
                          2000 or later
                        </option>
                    </select>            
            </div>
            <div class="form-group">
              <button class="btn btn-primary" id="complex_button" type="button">
                Save Complex
              </button>
            </div>
        </form>
        <div style="background:#f0f0f0;" id="unit_wrapper">
            <h2>Living Unit Information</h2>

        <div class="col-lg-12">
          <div class="col-lg-6">
        <div class="form-group">
          <label>Previous Unit</label>
          <select class="form-control chosen-select-width" id="unit_select">
            <option>Default</option>
          </select>
        </div>
      </div>
      <div class="col-lg-6">
        <button class="btn btn-primary" id="showUnit" type="button" disabled>Show</button>
        <button class="btn btn-primary" id="newUnit" type="button">New</button>
      </div>
      </div>
 

            <form id="unit_form" class="p-3" style="display:none;">
              <input type="hidden" class="form-control form-control-sm" name="mdu" id="mdu" value="0">
              <input type="hidden" class="form-control form-control-sm" name="id" value="0">
                <div class="form-group">                  
                    <label for="bldg">MDU Bldg#:</label>                   
                        <input type="text" class="form-control form-control-sm" name="bldg" id="bldg">         
                </div>
                <div class="form-group">
                    <label for="unit" >MDU living unit#:</label>                   
                        <input type="text" class="form-control form-control-sm" name="unit" id="unit">                  
                </div>
                <div class="form-group">
                    <label for="ccu">CCU #: </label>             
                        <input type="text" class="form-control form-control-sm" name="ccu" id="ccu">
              
                </div>
                <div class="form-group">
                    <label for="distance_from_idf" >Distance From IDF(ft):</label>                
                        <input type="text" class="form-control form-control-sm" name="distance_from_idf" id="distance_from_idf">              
                </div>
                <strong>Cable Type</strong>
                <div class="form-group">                                       
                                        <select class="form-control" name="utp" id="utp">
                                            <option value="non_cat">
                                              Non-CAT
                                            </option>
                                            <option value="cat_3">
                                              CAT 3
                                            </option>
                                            <option value="cat_5">
                                              CAT 5
                                            </option>
                                            <option value="cat_5+">
                                              CAT 6
                                            </option>
                                            <option value="coax_rg59">
                                              Coax - RG59
                                            </option>
                                            <option value="coax_other">
                                              Coax - Other
                                            </option>
                                            <option value="coax_rg6_plus">
                                              Coax - RG6+
                                            </option>
                                            <option value="unknown">
                                              UNKNOWN
                                            </option>
                                        </select>                                
                                      <button type="submit" class="btn btn-primary" id="save_unit">Save Address</button> 
               </div>
            </form>
        </div>
    </div>
</div>
</div>

</div>
</div>
<?php $this->load->view('additional/footer');?>
</div>

    <script type="text/javascript">



<?php if ($this->config->item('pusher_enabled')) : ?>
    var gfastNotificationsChannel = pusher.subscribe('gfast');
        // Subscribe to notifications that are being fed from the speed test.  
    gfastNotificationsChannel.bind('status', function(notification) {
        var message = notification;
        console.log(message);
        $('img#connectionStatus').attr('src','/assets/images/gfast/connection-good.png');

        $('button#runEtrTest').attr('disabled', false);
    });     
    gfastNotificationsChannel.bind('unplug', function(notification) {
        var message = notification;
        console.log(message);
        $('img#connectionStatus').attr('src', '/assets/images/gfast/connection-bad.png');
        $('button#runEtrTest').attr('disabled', true);
        alertify.error(message.message);
    }); 

    gfastNotificationsChannel.bind('install', function(notification) {
        var message = notification;
        console.log(message);
        if(message.message == "Install Complete") {
          $('div#installWrapper').hide();
        }
        alertify.log(message.message);
    }); 

<?php endif; ?>


var etrFunctionLoop, etrHistoryTable;



var myVar = setInterval(function() {
  myTimer();
}, 1000);


var month = new Array();
month[0] = "January";
month[1] = "February";
month[2] = "March";
month[3] = "April";
month[4] = "May";
month[5] = "June";
month[6] = "July";
month[7] = "August";
month[8] = "September";
month[9] = "October";
month[10] = "November";
month[11] = "December";

function myTimer() {
  var d = new Date();
  //document.getElementById("clock").innerHTML = d.toLocaleTimeString();
  //document.getElementById("clock").innerHTML = d.toString();
  $('#dateTimeLocal').val(toDatetimeLocal(d))
}



modifyTimeAndZoneInfo();

function toDatetimeLocal(self) {

      var month = new Array();
      month[0] = "Jan";
      month[1] = "Feb";
      month[2] = "Mar";
      month[3] = "Apr";
      month[4] = "May";
      month[5] = "Jun";
      month[6] = "Jul";
      month[7] = "Aug";
      month[8] = "Sep";
      month[9] = "Oct";
      month[10] = "Nov";
      month[11] = "Dec";

      var date = self,
      ten = function(i) {
        return (i < 10 ? '0' : '') + i;
      },
      YYYY = date.getFullYear(),
      MM = month[date.getMonth()],
      DD = ten(date.getDate()),
      HH = ten(date.getHours()),
      II = ten(date.getMinutes()),
      SS = ten(date.getSeconds())
      ;
      return DD + ' ' + MM + ' ' + YYYY + ' ' + HH + ':' + II + ':' + SS + ' ' + convertOffsetToZone(date.getTimezoneOffset())
    };

  function convertOffsetToZone(offset)
  {
    if(offset = 300) {
      return "EST";
    } else if(offset = 360) {
      return "CST";
    } else if(offset = 420) {
      return "MST";
    } else if(offset = 480) {
      return "PST";
    } else {
      return "UTC";
    }
  }


  function modifyTimeAndZoneInfo()
  {

    var timeUtc = '<?php echo $this->gfast->getSystemTime();?>';
    var date = new Date(timeUtc);
    var browserDate = new Date();
    $('.time #dateString').val(date.toString());
  }

    $(document).ready(function() {
       etrHistoryTable = $('#etrHistory').DataTable();
    });

function clearResults(){
              $("#total_etr").val(0);
              $("#total_etr_status").text('').removeClass('text-danger text-success');
            $("#down_etr").val(0);
            $("#up_etr").val(0);
            return false;
}

function populate(frm, data) {   
    $.each(data, function(key, value) {  
        var ctrl = $('[name='+key+']', frm);  
        switch(ctrl.prop("type")) { 
            case "radio": case "checkbox":   
                ctrl.each(function() {
                    if($(this).attr('value') == value) $(this).attr("checked",value);
                });   
                break;  
            case "select-one":
                ctrl.val(value);
                $(ctrl).trigger("chosen:updated");
            default:              
                ctrl.val(value); 
        }  
    });  
}

function generateMduUnitList(mdu) { 
  if(mdu > 0) {
          $.ajax({
          url: "/gfast/getUnitList",
          dataType: "json",
          data: {mdu: mdu},
          success: function(data) {
            console.log(data);
            for (i = 0; i < data.length; i++) { 
              $('select#unit_select').append($("<option></option>").attr("value", data[i].id).text('Bldg #: ' + data[i].bldg + ' Unit #:' + data[i].unit).attr('data-info', JSON.stringify(data[i])))
              $('select#unit_select').trigger("chosen:updated");
                console.log(data[i].bldg);
            }
          }
      });
  }
}


function getDateFormattedForTable() {
  var date = new Date(),
  formattedDate,
  ten = function(i) {
    return (i < 10 ? '0' : '') + i;
  };

  formattedDate = date.getFullYear() + '-' + 
  (date.getMonth() + 1) + 
  '-' + date.getDate() + ' ' + 
  ten(date.getHours()) + ':' + ten(date.getMinutes()) + ':' + ten(date.getSeconds());

  return formattedDate;

}

$(document).ready(function() {

$(document).on('click', '#saveResults', function(e) {
  e.preventDefault();
  var attr = $(this).attr('disabled');
  if(typeof attr === typeof undefined || attr === false) {
      var unit = store.get('mdu_unit');
      var json = $("#json_values").val();
      if(json !== "") {
        json = JSON.parse(json);
      $.ajax({
          url: "/gfast/saveEtrData",
          dataType: "json",
          data: {unit: unit, data: json},
          success: function(data) {
            console.log(data);
            if(data.status = true) {
              alertify.success(data.msg);                  
                  etrHistoryTable.row.add( [
                      getDateFormattedForTable(),
                      Math.trunc(json.ETR),
                      Math.trunc(json.RxETR),
                      Math.trunc(json.TxETR)
                  ] ).draw( false );
            } else{
              alertify.error('There was an error saving the results.')
              console.error(data.msg);
            }            
          }
      });
    } else {
      alertify.error('G.Fast unit is not responding with valid values, check connections. Nothing to save.');
    }    
    }
});




$(document).on('click', '#installGfast', function(e) {
  e.preventDefault();
      $.ajax({
          url: "/gfast/install",
          dataType: "json",
          success: function(data) {
            console.log(data)
          }
      });
});


$(document).on('click', '#editTime', function(e) {
  e.preventDefault();
 $('#outerDateTimeLocal').show();
});


$(document).on('click', '#resetTime', function(e) {
  e.preventDefault();

      $.ajax({
          url: "/gfast/forceClockReset",
          dataType: "json",
          success: function(data) {
            if(data.status == true) {
              alertify.success(data.msg)
            } else{
              alertify.error('Unable to update system clock')
            }
          }
      });

});

$(document).on('click', '#saveNewTime', function(e) {
  e.preventDefault();
  var localDate = $('#dateTimeLocal').val();
      $.ajax({
          url: "/gfast/updateSystemTime",
          dataType: "json",
          data: {time: localDate },
          success: function(data) {
            console.log(data);
            if(data.status = true) {
              alertify.success('System time has successfully been updated.');
              $('#outerDateTimeLocal').hide();
            }
          }
      });
});


$(document).on('click', '#attemptLoad', function(e) {
  e.preventDefault();

      $.ajax({
          url: "/gfast/loadGfast",
          dataType: "json",
          success: function(data) {
            console.log(data);
            if(data.status == 0) {
              $('img#connectionStatus').attr('src','/assets/images/gfast/e_connected.png');
              $('button#runEtrTest').attr('disabled', false);
            }
          }
      });

});

$(document).on('click', '#newComplex', function(e) {
  e.preventDefault();
  $('form#complex').show();
  $('form#complex')[0].reset();

})


$(document).on('click', '#showComplex', function(e) {
  e.preventDefault();
  var txt = $(this).text();
  if(txt == 'Show') {
    $('form#complex').show();
    $(this).text('Hide');
  } else if(txt == 'Hide') {
    $('form#complex').hide();
    $(this).text('Show');
  }
})


$(document).on('click', '#newUnit', function(e) {
  e.preventDefault();
  $('form#unit_form').show();
  $('form#unit_form')[0].reset();

})


$(document).on('click', '#showUnit', function(e) {
  e.preventDefault();
  var txt = $(this).text();
  if(txt == 'Show') {
    $('form#unit_form').show();
    $(this).text('Hide');
  } else if(txt == 'Hide') {
    $('form#unit_form').hide();
    $(this).text('Show');
  }
})

$(document).on('change', 'select#mdu_select', function(e) {
  var value = $(this).val()
  var data = $("#mdu_select option:selected").data('info');
  if (value !== "Default") {
    populate('form#complex', data);
    $('button#showComplex').attr('disabled', false);
    $('form#unit_form input#mdu').val(data.id);
    generateMduUnitList(data.id);
  }
});



$(document).on('change', 'select#unit_select', function(e) {
  
  var value = $(this).val()
  var data = $("#unit_select option:selected").data('info');
  if(value !== "Default"){
    populate('form#unit_form', data); 
    $('button#showUnit').attr('disabled', false);
    getHistoryOfUnit(data.id)
    store.set("mdu_unit", data.id);
    $('a#saveResults').attr('disabled', false);
  }
});



$(document).on('click', 'button#runEtrTest', function(e) {
  e.preventDefault();
  $(this).text('Test Running');
  getEtrValues();
});


$(document).on('click', 'button#complex_button', function(e) {
  e.preventDefault();
      $.ajax({
          url: "/gfast/saveComplex",
          dataType: "json",
          data: $('form#complex').serialize(),
          success: function(data) {
            if(data.status == true) {
              alertify.success("Successfully saved complex.");
              $('form#unit_form input#mdu').val(data.id);
            }
          }
      });
});

$(document).on('click', 'button#save_unit', function(e) {
  e.preventDefault();
      $.ajax({
          url: "/gfast/saveUnit",
          dataType: "json",
          data: $('form#unit_form').serialize(),
          success: function(data) {
            if(data.status == true) {
              alertify.success("Successfully saved unit.");
              $('form#unit_form input[name="id"]').val(data.id);
              store.set("mdu_unit", data.id);
              $('a#saveResults').attr('disabled', false);
            }
          }
      });
});

$(document).on('click','button#loadGfast', function(e) {
  console.log('Load G.Fast button clicked')
      $.ajax({
          url: "/gfast/loadGfast",
          dataType: "json",
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

});
function getEtrValues() {

      $.ajax({
          url: "/gfast/getEtrValues",
          dataType: "json",
          success: function(data) {
            if(data !== "") {
            $('img#connectionStatus').attr('src','/assets/images/gfast/connection-good.png');
            var json = JSON.parse(data);            
            $("#json_values").val(data);
            $("#total_etr").val(Math.trunc(json.ETR));

            if(Math.trunc(json.ETR) > 550) {
              $("#total_etr_status").text('PASS').removeClass('text-danger').addClass('text-success');
            } else {
              $("#total_etr_status").text('FAIL').removeClass('text-success').addClass('text-danger');
            }
            $("#down_etr").val(Math.trunc(json.RxETR));
            $("#up_etr").val(Math.trunc(json.TxETR));
            alertify.log('New test results are available', 1);
          } else {
            $('img#connectionStatus').attr('src','/assets/images/gfast/e_connected.png');
            alertify.error('Empty test results returned. Check connection and devices.');
          }
          $('button#runEtrTest').text('Begin Test');
          }
      });
      
}

function getHistoryOfUnit(id) {

      $.ajax({
          url: "/gfast/getEtrHistory",
          dataType: "json",
          data: {unit: id},
          success: function(data) {
            if(data !== "") {
              //console.log(data);
              var i;
              for (i = 0; i < data.length; i++) { 
                  results = JSON.parse(data[i].results)
                  data[i].date
                  console.log(results);                  
                  etrHistoryTable.row.add( [
                      data[i].date,
                      Math.trunc(results.ETR),
                      Math.trunc(results.RxETR),
                      Math.trunc(results.TxETR)
                  ] ).draw( false );
              }
            }
          }
      });
}

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