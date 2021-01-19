<div id="completeView" style="display:none;">
    <!--/#completeView-->
    <div id="spotCheckWrapper" class="text-center">
        <div id="spotCheckButtonWrapper" class="btn-group">
            <button type="button" class="btn btn-primary" id="wirelessSpotCheckButton">Spot Check</button>
            <button type="button" class="btn btn-danger gotoMainView">Continue</button>
        </div>
        </br>
        </br>
        <div id="spotCheckFormWrapper" style="display:none;">
            <div id="spotCheckForm">
                <div class="form-group">
                    <input type="text" name="roomName" id="roomName" placeholder="Living Room" value="" autocomplete="false">
                    <span class="help-block">The name of the room that the test will be conducted from.</span>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary" id="submitSpotCheck" onclick="javascript:submitSpotCheck();">Run Spot Check</button>
                </div>
            </div>
        </div>
        </br>
        <div id="runAnotherSpotCheck" style="display:none;">
            <p>Would you like to run another spot check?</p>
            <button type="button" class="btn btn-primary" onclick="javascript:runAnotherSpotCheck();">Yes</button>
            <button type="button" class="btn btn-danger gotoMainView">No</button>
        </div>
    </div>
</div>
