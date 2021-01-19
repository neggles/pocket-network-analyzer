<div class="modal fade" role="dialog" id="connectToWirelessNetworkModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="text-center modal-title">Connect to Wireless Network</h4>
            </div>
            <div class="modal-body">
                <form id="connectToWirelessNetworkForm">
                    <div class="form-group">
                        <label for="networkSsid">Network SSID</label>
                        <input name="networkSsid" id="networkSsid" class="form-control" type="text">
                    </div>
                    <div class="form-group" id="encryptionTypeParent" hidden>
                        <label for="encryptionType">Encryption Type</label>
                        <input name="encryptionType" id="encryptionType" class="form-control" type="text" disabled>
                    </div>
                    <div class="form-group" id="encryptionKeyParent" hidden>
                        <label for="encryptionKey">Network Passphrase</label>
                        <input name="encryptionKey" id="encryptionKey" class="form-control" type="text" placeholder="Passphrase">
                    </div>
                    <input type="hidden" name="group" id="group">
                    <input type="hidden" name="pairwise" id="pairwise">
                    <input type="hidden" name="authentication" id="authentication">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="connectToNetworkFormSubmit">Connect</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- /.modal -->