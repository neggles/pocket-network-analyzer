<div id="thirdView" style="display:none;">
    <div id="workFlowTest">
        <div class="text-center" id="testResultsContent">
            <ul class="list-group" id="testsComplete">
            </ul>
            <span id="testResults">
                        <i id="networkTestSpinner" class="fa fa-cog fa-spin fa-4x"></i>               
                        <p>Testing for ethernet link...</p>
                    </span>
            <div id="chooseWirelessNetwork" style="display:none;">
                <div class="alert alert-info">
                    Please select the wireless network you would like to perform the speed test on.
                </div>
                <form class="form" id="wirelessNetworkForSpeedTestForm" name="wirelessNetworkForSpeedTestForm">
                    <div class="form-group pull-left">
                        <button type="button" data-toggle="#speedTestServerWrapper" class="btn btn-primary toggle" title="Click to add a custom speedtest server."><i class="fa fa-server"></i></button>
                    </div>
                    <div id="customServerWrapper">
                        <div class="form-group" id="speedTestServerWrapper" style="display:none;">
                            <label for="speedtest-server">Custom Server: </label>
                            <input type="text" class="form-control" id="speedtest-server" placeholder="http://att.com/speedtest">
                            <p class="help-block">Use this text field to identify a custom speed test server (Not Mandatory)</p>
                        </div>
                    </div>
                    <div id="networkNameDiv">
                        <div class="form-group">
                            <label for="networkName">Wireless Network: </label>
                            <select id="networkName" name="networkName" class="form-control" value="">
                                <option selected="selected" value="">Select SSID</option>
                            </select>
                        </div>
                    </div>
                    <div id="passwordGroupOuter" style="display:none;">
                        <div class="form-group">
                            <label for="passphrase">Password: </label>
                            <div id="passwordGroup">
                            </div>
                        </div>
                        <div id="saveButtonDiv" style="display:none;">
                            <button type="button" class="btn btn-danger" id="submitWirelessConfiguration" onclick="javascript:wirelessSpeedTestConfiguration();" disabled>Save Configuration</button>
                        </div>
                    </div>
                    <span id="helpBlock" class="help-block"></span>
                    <div id="confirmWirelessNetwork" class="form-group" style="display:none;">
                        <button type="button" class="btn btn-primary" id="confirmWirelessNetworkButton">Confirm</button>
                    </div>
                </form>
            </div>
            <!--/#chooseWirelessNetwork-->
        </div>
        <!--/#testResultsContent-->
    </div>
    <!--/#workFlowTest-->
    <div class="form-group">
        <button type="button" class="btn btn-danger back" onclick="javascript:goBack('#thirdView','#secondView')" id="backToStepTwo" title="Go Back">
            <i class="fa fa-arrow-left"></i></button>
    </div>
</div>
