<?php $this->load->view('modals/setupWizard');?>

<?php $this->load->view('modals/connectToWirelessNetworkModal');?>
<?php $this->load->view('modals/networkConfigurationModal');?>
</div>

<link rel="stylesheet" href="/assets/css/jquery.steps.css">
<script src="<?php echo $this->config->item('plugins_directory');?>datatables/media/js/jquery.dataTables.min.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>datatables/media/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>jquery.steps/build/jquery.steps.min.js"></script>

<script src="<?php echo $this->config->item('plugins_directory');?>alertify/alertify.min.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>sweetalert/dist/sweetalert.min.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>jquery-slimscroll/jquery.slimscroll.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>chosen/chosen.jquery.js"></script>

<script>
        var config = {
                '.chosen-select'           : {width:"95%"},
                '.chosen-select-deselect'  : {allow_single_deselect:true},
                '.chosen-select-no-single' : {disable_search_threshold:10},
                '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
                '.chosen-select-width'     : {width:"95%"}
                }
$(document).ready(function () {

    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }
});

var timeoutID;
 
function setup() {
    this.addEventListener("mousemove", resetTimer, false);
    this.addEventListener("mousedown", resetTimer, false);
    this.addEventListener("keypress", resetTimer, false);
    this.addEventListener("DOMMouseScroll", resetTimer, false);
    this.addEventListener("mousewheel", resetTimer, false);
    this.addEventListener("touchmove", resetTimer, false);
    this.addEventListener("MSPointerMove", resetTimer, false);
 
    startTimer();
}

setup();
 
function startTimer() {
    // wait 5 minutes before calling goInactive
    timeoutID = window.setTimeout(goInactive, 300000);
}
 
function resetTimer(e) {
    window.clearTimeout(timeoutID);
 
    goActive();
}
 
function goInactive() {
    // do something
    console.log("You are inactive.");
    idleAction();
    resetTimer(false);
}
 
function goActive() {
    // do something
    startTimer();
}

function idleAction(action)
{
    var d = new Date();
    var n = d.getTime();

    if(window.globalTimeout) {
        $.ajax({
            method: "get",
            url: "/home/timeoutAction",
            data: {
                time: n
            },
            success: function (data) {
                console.log("played timeout sound.");
            }
        });    
    }
}
<?php if ($this->config->item('analytics_enabled')) : ?>
/* AJAX call to backend analytics page. */
$(document).ready(function () {
        $.ajax({
            method: "post",
            url: "/analytics",
            data: {
                url: 'http://pfi.test.com' + window.location.pathname,
                title: window.location.pathname
            },
            success: function (data) {
                console.log(data);
            }
        });

});
<?php endif; ?>
    /* Setup wizard */
    var wizard = $("#wizard");

            wizard.steps({
                headerTag: "h1",
                bodyTag: "section",
                onStepChanging: function (event, currentIndex, newIndex)
                {
                    // Always allow step back to the previous step even if the current step is not valid!
                    if (currentIndex > newIndex)
                    {
                        return true;
                    }
                    if(currentIndex === 0) {
                        var uid = $('input#uid').val();
                        if(uid !== "" && typeof uid !== 'undefined') {
                             //console.log(uid);                            
                            submitUserId(uid);
                             
                             return true;
                        } else {
                            return false;
                        }                       
                    }
                },
                onInit: function (event, currentIndex) { 
                    if(currentIndex === 0) {
                        var uid = $('input#uid').val();
                        if(uid !== "" && typeof uid !== 'undefined') {

                             return true;
                        } else {
                            return false;
                        }                       
                    }
                },
                onFinished: function (event, currentIndex) { 
                    var job = $('select#newJobSelection').val();
                    console.log(job);
                    if(job === 0 || (typeof job === 'undefined')) {
                        $('#selectAJobForm .selectJobWrapper.form-group').toggleClass('has-error');
                        $('#selectAJobForm .selectJobWrapper .help-block').text('You must select a job before proceeding.');
                        return false;
                    } else {
                        setNewJobValue(job);
                        //return true;
                    }
                },
            });

</script>
</body>

</html>
