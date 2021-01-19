<div class="modal fade" tabindex="-1" id="helpModal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">New Features</h4>
      </div>
      <div class="modal-body">      
      <div class="row-fluid">
      <h2>Modify Wireless Networks</h2>
      <p>In order to modify a previously saved wireless network configuration there are two ways to access.</p>
      <ol>
        <li>If you are in the mobile view, click in the top right corner for the dropdown.</li>
        <li>Select the red wireless icon.</li>
         <li>Click on the dropdown and select your network</li>
          <li>The files are named by the network ssid concatenated with '.conf'</li>
          <li>Click the edit button.</li>
          <li>Type the new password.</li>
          <li>Click the Save icon to the right.</li>
          <li>You should be prompted with a green success popup.</li>
          <li>Close the popup and continue.</li>
      </ol>
      <button type="button" class="btn btn-primary" 
      data-toggle="modal" 
      data-target="#networkConfigurationModal" onclick="javascript:toggleModal('#helpModal')">Launch</button>
      </div>
        </div>


      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
function toggleModal(e)
{
  $(e).modal('hide');
}
</script>