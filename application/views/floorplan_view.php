<?php
defined('BASEPATH') or exit('No direct script access allowed');
if (isset($currentJob->id) && $currentJob->id !== 0) :
    $settings = $currentJob->getDetails();
    $floorplan = $currentJob->getFloorPlan();
    ?>
    <link href="/assets/css/builder.css" rel="stylesheet" type="text/css" media="all">
    <link href="<?php echo $this->config->item('plugins_directory');?>jQuery-contextMenu/dist/jquery.contextMenu.css" rel="stylesheet" type="text/css" media="all">
    <link href="<?php echo $this->config->item('plugins_directory');?>alertify/themes/alertify.core.css" rel="stylesheet" type="text/css" media="all">
    <link href="<?php echo $this->config->item('plugins_directory');?>alertify/themes/alertify.bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <script src="<?php echo $this->config->item('plugins_directory');?>moment/min/moment.min.js"></script>

    <div id="page-wrapper" class="gray-bg" style="padding:0;">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div role="main">
            <div class="container-fluid">
            <div class="float-e-margins">
                <div class="row">   
                    <div class="col-sm-6">               
                            <a href="#" class="btn btn-danger" id="saveFloorplanLayout"><i class="fa fa-floppy-o fa-fw"></i>Save Layout</a>
                            <a href="#clear" class="btn btn-danger" id="clear"><i class="fa fa-trash fa-fw"></i>Clear</a>  
                            <!--<a class="btn btn-primary" id="addRow" data-toggle="modal" data-target="#editHomeModal">Add Row</a> --> 
                            <a href="#" class="btn btn-primary" id="editHomeSettings" data-toggle="modal" data-target="#editHomeSettingsModal">Modify Home Settings</a>
                            <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#editWirelessThresholdSettingsModal">Wireless Thresholds</a>
                    </div>
                    <div class="col-sm-6">
                    <div class="form-group">
                              <label>Default Network</label>
                              <div class="input-group">
                              <span class="input-group-btn">
                                  <a href="#" class="btn btn-primary" title="Save Default Network" id="saveDefaultNetwork" data-target="[name=front_default_network]">
                                      <i class="fa fa-floppy-o"></i>
                                  </a>
                              </span>
                              <select class="form-control" name="front_default_network" required></select>
                                <span class="input-group-btn">
                                  <a href="#" class="btn btn-primary refreshNetworkList" data-target="[name=front_default_network]" title="Refresh list of available networks">
                                  <i class="fa fa-refresh"></i>
                                  </a>
                                </span>
                                </div>
                                </div>

                    </div>
                </div>
                </div>
                    <br/>
                    <div class="row" id="toggleFloorWrapper">
                        <div class="col-sm-12">
                            <div class="text-center">                     
                    <?php if (isset($settings->location->floors)) : ?>
                                <div class="btn-toolbar" role="toolbar">
                                    <div class="btn-group" role="group">
                                        <?php $numFloors = $settings->location->floors; ?>                      
                                        <?php for ($i = 0; $i < $numFloors; $i++) : ?>
                                            <button data-toggle="floor" data-target="[data-floor=<?php echo $i +1; ?>]" data-target-floor="<?php echo $i +1; ?>" class="btn btn-primary"><?php echo $i+1; ?></button>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                    <?php endif; ?>
                            </div>
                        </div>                    
                    </div>
                    <div class="row-fluid">
                    <div class="text-center">
                        <span class="label label-primary">Front</span>
                        </div>
                    </div>                  
                    <div class="row-fluid">                
                        <div class="house ui-sortable" style="min-height: 304px;" id="houseSurround" data-type="house">
                        </div> <!-- data-type=house -->
                <script>
                        store.set("jobId", <?php echo $currentJob->id; ?>);

                        <?php if (is_object($settings->location) and isset($settings->location)) : ?>
                            store.set("job_location_settings_" + <?php echo $currentJob->id; ?> , JSON.stringify(<?php echo json_encode($settings->location);?>));
                        <?php endif; ?>
                        <?php if (!empty($floorplan)) : ?>        
                            store.set("layoutdata_" + <?php echo $currentJob->id; ?>, JSON.stringify(<?php echo $floorplan; ?>));
                        <?php else : ?>
                            var floorPlanLoaded = false; 
                        <?php endif; ?>
                        store.set("wireless_thresholds", JSON.stringify(<?php echo $this->settings->get('wireless_thresholds');?>));                        
                </script> 
            </div>
        </div>           
    </div>
</div> <!-- .wrapper-content -->

<?php $this->load->view('additional/footer');?>
</div> <!-- #page-wrapper -->

<div class="modal fade" role="dialog" id="editRoomModal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" class="close" data-dismiss="modal"><i class="fa fa-times"></i></a>
                <h3>Edit Room</h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="hidden" value="" name="targetRoomId">
                    <label>Room Name</label>
                    <input type="text" class="form-control" name="roomName" id="roomName" autofocus="autofocus">
                </div>
                <button id="saveRoom" class="btn btn-primary" type="button">Save</button>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal">Close</a>
            </div>
        </div>
    </div>
    <script>
    $("#saveRoom").click(function(e) {
        e.preventDefault();
        var roomId = $("#editRoomModal [name=targetRoomId]").val();
        $("#editRoomModal [name=targetRoomId]").val('')
        var roomName = $("#editRoomModal [name=roomName]").val();
        $("#editRoomModal [name=roomName]").val('');
        writeNewLabel(roomId, roomName);
        $('#editRoomModal').modal('hide');
    });
    </script>
</div> <!-- edit room modal window -->

<div class="modal fade" role="dialog" id="editHomeModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" class="close" data-dismiss="modal"><i class="fa fa-times"></i></a>
                <h3>Add Row</h3>
            </div>
            <div class="modal-body">
                <form class="form">
                    <div class="form-group">
                        <label>Number of Rooms</label>
                        <input type="number" class="form-control" name="rowOfRooms" value="3" max="4" min="1">
                    </div>
                    <button id="saveRoomRow" class="btn btn-primary" type="button">Save</button>
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal">Close</a>
            </div>
        </div>
    </div>
    <script>
    $("#saveRoomRow").click(function() {
        e.preventDefault();
        var roomValue = $("#editHomeModal [name=rowOfRooms]").val();
        addRooms(roomValue);
        $('#editHomeModal').modal('hide');
    })
    </script>
</div> <!-- edit home modal window -->


<div class="modal fade" role="dialog" id="roomWirelessResultsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" class="close" data-dismiss="modal"><i class="fa fa-times"></i></a>
                <h3>Scan Results</h3>
            </div>
            <div class="modal-body">
                <table class="table table-striped" id="wirelessResultsModal"></table>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal">Close</a>
            </div>
        </div>
    </div>
</div> <!-- room wireless results modal window -->
<?php if (is_object($settings->location) and isset($settings->location)) : ?>
<div class="modal fade" role="dialog" id="editHomeSettingsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" class="close" data-dismiss="modal"><i class="fa fa-times"></i></a>
                <h3>Home Settings</h3>
            </div>
            <div class="modal-body">
            <pre>
            <?php print_r($settings->location) ?>
            </pre>
                <form class="form" id="jobLocationSettingsForm">
                    <input type="hidden" name="id" value="<?php echo $settings->location->id; ?>">
                    <input type="hidden" name="job" value="<?php echo $settings->location->job; ?>">
                    <?php foreach ($settings->location as $k => $v) : ?>
                    <?php $key = str_replace("_", " ", $k); /* replace underscores with spaces */?>
                    <div class="form-group">
                        <?php if ($k == "address") : ?>
                        <label>Address</label>
                        <input type="text" name="<?php echo $k;?>" value="<?php echo $v;?>" class="form-control">
                        <?php elseif ($k == "dispatch_type") : ?>
                        <?php $dispatchTypes = $this->job->getJobDispatchTypes();?>
                        <label>Dispatch Type</label>
                        <select class="form-control" name="dispatch_type">
                            <?php foreach ($dispatchTypes as $k => $v) : ?>
                            <option value="<?php echo $k; ?>">
                                <?php echo $v;?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php elseif ($k == "home_type") : ?>
                        <?php $homeTypes = $this->job->getJobHomeTypes();?>
                        <label>Home Type</label>
                        <select class="form-control" name="home_type">
                            <?php foreach ($homeTypes as $k => $v) : ?>
                            <option value="<?php echo $k; ?>">
                                <?php echo $v;?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php elseif ($k == "default_network") :?>
                            <?php $network = json_decode($v); ?>
                        <label>Default Network</label>
                        <div class="input-group">
                            <select class="form-control" id="default_network" name="default_network">
                                <option data-frequency="<?php echo $network->frequency ? $network->frequency : '' ?>" data-mac="<?php echo $network->mac ? $network->mac : '' ?>" value="<?php echo $network->ssid ? $network->ssid : '' ?>">
                                    <?php echo $network->ssid ? $network->ssid : 'None';?>
                                </option>
                            </select>
                            <span class="input-group-btn">
                                  <a href="#" class="btn btn-primary refreshNetworkList" data-target="[name=default_network]">
                                  <i class="fa fa-refresh"></i>
                                  </a>
                                </span>
                        </div>
                        <input type="hidden" name="mac" id="default_mac" value="<?php echo $network->mac; ?>"/>
                        <input type="hidden" name="frequency" id="default_frequency" value="<?php echo $network->mac; ?>"/>
                        <script>
                        $(document).on('change', '#default_network', function(){
                            var mac = $('option:selected', this).data('mac');
                            var freq = $('option:selected', this).data('frequency');
                            $('#default_mac').val(mac);
                            $('#default_frequency').val(freq);
                        })
                        </script>
                        <?php elseif ($k == "floors") : ?>
                        <label>Number of Floors</label>
                        <input type="number" class="form-control" name="floors" value="<?php echo $v; ?>">
                        <?php elseif ($k == "mac") : ?>
                            <input type="hidden" name="mac" id="default_mac" value="<?php echo $v; ?>"/>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary" id="saveJobLocationSettings">Save</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal">Close</a>
            </div>
        </div>
    </div>
</div>
<!-- edit home settings modal. -->
<?php endif; ?>

<div class="modal fade" role="dialog" id="editWirelessThresholdSettingsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" class="close" data-dismiss="modal"><i class="fa fa-times"></i></a>
                <h3>Wireless Thresholds</h3>
            </div>
            <div class="modal-body">
                <?php $wirelessThresholds = json_decode($this->settings->get('wireless_thresholds'));?>
                <?php foreach ($wirelessThresholds as $key => $value) : ?>
                <div class="row">
                    <form class="form" id="<?php echo $key; ?>WirelessThresholdSettings">
                        <input type="hidden" name="index" value="<?php echo $key; ?>">
                        <label>
                            <?php echo $value->name; ?>
                        </label>
                        <?php foreach ($value as $k => $v) : ?>
                        <?php if ($k == "color" || $k == "value") : ?>
                        <div class="form-group ">
                            <?php if ($k == "color") : ?>
                            <label>Color</label>
                            <input type="color" name="<?php echo $k; ?>" class="form-control" value="<?php echo $v;?>">
                            <?php elseif ($k == "value") : ?>
                            <label>Threshold</label>
                            <input type="number" name="<?php echo $k; ?>" class="form-control" value="<?php echo $v; ?>">
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <div class="form-group">
                            <button type="button" name="save" data-form="<?php echo $key?>WirelessThresholdSettings" class="btn btn-primary saveWirelessThresholds">Save</button>
                        </div>
                    </form>
                    <hr>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal">Close</a>
            </div>
        </div>
    </div>
    <script>
    $(".saveWirelessThresholds").click(function(e) {
        e.preventDefault();
        var form = $(this).data('form');
        var values = $("#" + form).serialize();
        editWirelessThresholds(values);
        $('#editWirelessThresholdSettingsModal').modal('hide');
    });
    </script>
</div> <!-- Wireless threshold modal settings. -->
              

        <script>
        $(document).ready(function()
        {
          $('body').addClass('edit');
        });

        </script>
<script src="<?php echo $this->config->item('plugins_directory');?>interactjs/dist/interact.min.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>spectrum/spectrum.js"></script>
<link rel="stylesheet" href="<?php echo $this->config->item('plugins_directory');?>spectrum/spectrum.css" />
<script src="<?php echo $this->config->item('plugins_directory');?>jquery-ui/jquery-ui.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>nouislider/distribute/nouislider.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>jquery-htmlclean/jquery.htmlClean.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>jQuery-contextMenu/dist/jquery.contextMenu.min.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>jquery-json2html/json2html.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>jquery-json2html/jquery.json2html.js"></script>
<script src="/assets/js/scripts.js?v=<?php echo time();?>"></script>
<?php  if (!isset($settings->location) || !is_object($settings->location)) : ?>
                <div class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static" id="createNewHomeSettingsModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">                          
                                <h3>Home Settings</h3>
                            </div>
                            <div class="modal-body">
                            <form class="form" id="newJobLocationSettingsForm">                            
                            <input type="hidden" name="id" value="">
                            <input type="hidden" name="job" value="<?php echo $this->currentJob->id; ?>">
                              <div class="form-group" >                              
                              <label>Address</label>
                               <input type="text" name="address" value="" class="form-control" placeholder="123 Easy Street" required>                            
                                <?php $dispatchTypes = $this->job->getJobDispatchTypes();?>
                              <label>Dispatch Type</label>
                              <select class="form-control" name="dispatch_type" required>
                                <?php foreach ($dispatchTypes as $k => $v) : ?>
                                  <option value="<?php echo $k; ?>"><?php echo $v;?></option>
                                <?php endforeach; ?>
                                </select>
                                <?php $homeTypes = $this->job->getJobHomeTypes();?>
                              <label>Home Type</label>
                              <select class="form-control" name="home_type" required>
                                <?php foreach ($homeTypes as $k => $v) : ?>
                                  <option value="<?php echo $k; ?>"><?php echo $v;?></option>
                                <?php endforeach; ?>
                                </select>
                              <label>Default Network</label>
                              <div class="input-group">
                              <select class="form-control" name="default_network" id="new_default_network" required>
                                <option value="">None</option>
                                </select>
                                <span class="input-group-btn">
                                  <a href="#" class="btn btn-primary refreshNetworkList" data-target="[name=default_network]">
                                  <i class="fa fa-refresh"></i>
                                  </a>
                                </span>
                                 <input type="hidden" name="mac" id="new_mac" />   
                                  <input type="hidden" name="frequency" id="new_frequency" />   
                                </div>
                              <label>Number of Floors</label>
                              <input type="number" class="form-control" name="floors" value="2" max="4" min="1" required>    
                                           
                            </div>
                            <div class="form-group">
                              <button type="button" class="btn btn-primary" id="saveNewJobLocationSettings">Save</button>
                            </div>
                            </form>
                            <div class="alertWrapper">
                            </div>
                            </div>
                        </div>
                    </div>
                    <script> 
                    $(document).on('change', '#new_default_network', function(){
                        var mac = $('option:selected', this).data('mac');
                        var freq = $('option:selected', this).data('frequency');
                        $('#new_mac').val(mac);
                        $('#new_frequency').val(freq);
                    })
                    var floorPlanLoaded = false;                   
                    $('#createNewHomeSettingsModal').modal('show');
                    $('.alertWrapper').html("<div class='alert alert-danger'>This job does not have a location setup yet, please enter the information above before proceeding.</div>")
                    $(document).on('click',"button#saveNewJobLocationSettings", function(e) {
                        e.preventDefault();
                         $("form#newJobLocationSettingsForm").validate();
                          $.ajax({
                            method: "post",
                            url: "/job/updateJobLocationSettings",
                            dataType:'json',
                            data: $("form#newJobLocationSettingsForm").serialize(),
                            success: function(data) {                    
                                console.log(data);                                      
                                store.set("job_location_settings_<?php echo $this->currentJob->id; ?>", JSON.stringify(data));                            
                                $('#createNewHomeSettingsModal').modal('hide');
                                loadFloorPlan();
                            }
                        });
                    });
                    </script>
                </div>

<?php else : ?>
    <script>
    var floorPlanLoaded = true; 
    </script> 
<?php endif; ?>
<?php else : ?>
  <script>
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
<?php endif; //if job isset?>
