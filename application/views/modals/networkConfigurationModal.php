<script src="<?php echo $this->config->item('plugins_directory');?>chosen/chosen.jquery.js"></script>
<link rel="stylesheet" href="<?php echo $this->config->item('plugins_directory');?>chosen/chosen.css">

<div class="modal fade" tabindex="-1" id="networkConfigurationModal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modify Wireless Networks</h4>
      </div>
      <div class="modal-body">      
      <div class="form-group">
      <label for="configuredNetwork">Select Network</label>
      <select style="width:350px;" id="configuredNetwork" name="configuredNetwork" class="chosen-select" data-placeholder="Choose a network..." tabindex="-1">
        <?php
        $configuredNetworks = $this->wireless->getConfiguredNetworkList();
        if (!empty($configuredNetworks)):
        foreach ($configuredNetworks as $network) :
            echo '<option value="'.$network.'">'. $network .'</option>';
        endforeach;
      endif;
        ?>
        </select>
        </div>
  <div class="form-group" id="textField" style="display:none;">
    <div class="input-group">
      <input type="text" class="form-control" id="wirelessNetworkPassword">
      <span class="input-group-btn">
      <button type="button" class="btn btn-primary" id="saveNewPassword">
      <i class="fa fa-floppy-o"></i>
      </button>
      </span>
    </div>
  </div>


      <div class="form-group">
        <button type="button" class="btn btn-primary" id="editConfiguredNetwork">Edit</button>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
/*$(".chosen-select").chosen({
    no_results_text: "Oops, nothing found!",
    width: "100%"
  });*/

 $(document).on('click','button#editConfiguredNetwork', function(){
    var ssidConf = $('select#configuredNetwork').val();


  if(ssidConf){
      $('div#textField').toggle();
      $('input#wirelessNetworkPassword').prop('placeholder','Password for '+ ssidConf.replace(".conf", ""));
    }

 });  

 $(document).on('click', 'button#saveNewPassword', function() {

  var ssid = $('select#configuredNetwork').val().replace(".conf", "");
  var passphrase = $('input#wirelessNetworkPassword').val();
  console.log($('input#wirelessNetworkPassword').val());

  if(passphrase) {

            $.ajax({
                method: 'post',
                url: "/wireless/getPreSharedKey",
                data: {
                    ssid: ssid,
                    passphrase: passphrase
                },
                success: function(data) {
                        $.ajax({
                            method: 'post',
                            url: "/wireless/savePreSharedKey",
                            data: {
                                conf: data,
                                ssid: ssid,
                                encryption:''

                            },
                            success: function(data) {
                              console.log(data);
                            }
                        });
                        //nested ajax function endpoint 
                        //console.log(data);

                    } //end of ajax success function
            }); //end of ajax  
  } else {
    console.log($(this));
  }   
 });
</script>
