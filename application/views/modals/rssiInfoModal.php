<div class="modal fade" tabindex="-1" id="rssiInfoModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Features</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Signal Strength</th>
                            <th></th>
                            <th> Required for</th>
                        </tr>
                    </thead>
                    <tr>
                        <td>-30 dBm</td>
                        <td>Max achievable signal strength. The client can only be a few feet from the AP to achieve this. Not typical or desirable in the real world.</td>
                        <td>N/A</td>
                    </tr>
                    <tr>
                        <td>-67 dBm</td>
                        <td>Minimum signal strength for applications that require very reliable, timely packet delivery.</td>
                        <td> VoIP/VoWiFi, streaming video</td>
                    </tr>
                    <tr>
                        <td>-70 dBm</td>
                        <td>Minimum signal strength for reliable packet delivery.</td>
                        <td> Email, web</td>
                    </tr>
                    <tr>
                        <td>-80 dBm</td>
                        <td>Minimum signal strength for basic connectivity. Packet delivery may be unreliable.</td>
                        <td> N/A</td>
                    </tr>
                    <tr>
                        <td>-90 dBm</td>
                        <td>Approaching or drowning in the noise floor. Any functionality is highly unlikely.</td>
                        <td> N/A</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script>
function toggleModal(e) {
    $(e).modal('hide');
}
</script>
