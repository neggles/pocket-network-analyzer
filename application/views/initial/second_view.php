<div id="secondView" style="display:none;">
    <div id="updateUserStatus">
        <div class="text-center">
            <span id="jobName">
                        <p></p>
                        </span>
            <span id="jobBan">
                         <p></p>
                        </span>
            <p class="lead">
                About to perform the first battery of tests
                <br/> this will take approximately 5 minutes.
            </p>
        </div>
        <div class="text-center">
            <button type="button" class="btn btn-primary" id="proceedToTest">Proceed</button>
            <button type="button" class="btn btn-primary cancel">Cancel</button>
        </div>
    </div>
    <div class="form-group">
        <button class="btn btn-danger back" onclick="javascript:goBack('#secondView','#initialView')" id="backToStepOne" title="Go Back">
            <i class="fa fa-arrow-left"></i></button>
    </div>
</div>
