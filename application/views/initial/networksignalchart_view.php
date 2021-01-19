<div class="row">
    <div id="networkListElement" class="col-md-6 well">
        <div class="text-center">
            <h3>Signal Strength Chart</h3>
        </div>
        <canvas id="myNetworkChart"></canvas>
    </div>
    <div id="channelUtilizationElement" class="col-md-6 well">
        <div class="text-center">
            <h3>Channel Utilization</h3>
        </div>
        <div id="channelChartInfo" class="text-center alert alert-info">
            <p>This chart will be populated after the speed tests are conducted.</p>
        </div>
        <div id="contentLoading"></div>
        <canvas id="channelUtilizationChart" style="display:none;"></canvas>
        <div class="col-lg-6">
            <form id="wirelessChannelScan">
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" id="scanWirelessChannels" class="btn btn-default" onclick="javascript:runBandwidthTest();">Rescan</button>
                    </div>
                    <input type="number" id="scanTime" name="scanTime" class="form-control" min="5" max="30" value="5">
                    <span class="input-group-addon">secs</span>
                </div>
            </form>
        </div>
    </div>
</div>
