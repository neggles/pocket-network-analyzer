<div class="modal fade" role="dialog" id="setupWizard"> 
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="text-center modal-title">Setup Wizard</h4>
            </div>
            <div class="modal-body">
                <div id="wizard">
                            <h1>User ID</h1>
                            <section>
                                    <div class="form-group">
                                        <label for="uid">User id</label>
                                        <input class="form-control" name="uid" id="uid" type="text" maxlength="6" placeholder="ATT UID" value="<?php echo $this->session->userdata('uid'); ?>" style="text-transform: lowercase;">
                                    </div>
                            </section>
                            <h1>Job</h1>
                            <section>
                                <?php $jobs = Job_model::getAllJobs(); ?>
                                <?php if ($jobs !== null) : ?>
                         
                                        <div class="form-group">                               
                                            <select id="newJobSelection" class="form-control">
                                                <option value="0">Select a Job</option>                    
                                                <?php foreach ($jobs as $job) :?>
                                                    <?php $selected = ($job->id == $this->session->userdata('jobId')) ? 'selected="selected"': ''; ?>
                                                    <option value="<?php echo $job->id;?>" <?php echo $selected;?>><?php echo $job->name;?></option>
                                                <?php endforeach; ?>                               
                                            </select>
                                        </div> 
                                    
                                <?php endif; ?>
                                <div style="display:none;" id="createJobForm">
                                    <form id="createNewJobForm">
                                    <div class="form-group">
                                        <label for="jobName">Name</label>
                                        <input class="form-control" name="jobName" id="jobName" type="text" placeholder="Client Name">
                                    </div>
                                    <div class="form-group">
                                        <label for="banNumber">BAN Number</label>
                                        <input class="form-control" name="banNumber" id="banNumber" type="text" placeholder="BAN number" maxlength="15">
                                    </div>
                                    <div class='form-group'>
                                        <label for="jobComments">Comments</label>
                                        <textarea class="form-control" name="jobComments" id="jobComments" placeholder="Comments"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <div class="text-center">
                                            <button type="button" class="btn btn-primary" id="createNewJobWizardBtn">Create</button>
                                        </div>
                                    </div>  
                                    </form>                            
                                </div>
                            </section>
                        </div>
              
            </div>   
              <div class="modal-footer">
                <button type="button" class="btn btn-primary switchJob" id="showCreateForm" data-target="#createJobForm">Create Job</button>
              </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <script>
    $(document).on('click', 'button#createNewJobWizardBtn', function(e) {
        e.preventDefault();
        console.log('Button has been pressed');
        $.ajax({
            method: 'post',
            url: "/job/createnewjob",
            data: $('form#createNewJobForm').serialize(),
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if(data.status !== "error") {
                    // If this is the first job created go ahead and set it as
                    // the current job which will also redirect to the home page and log the user in.
                    if(data.job.id == 1) {
                        setSessionData("jobId", data.job.id);
                    } else {
                        updateAvailableJobs(data.job);
                        $('div#createJobForm').hide();
                    }
                }
                if (data.status == true) {
                    alertify.success(data.msg);
                } else if (data.status == false) {
                    alertify.error(data.msg);
                }
            }
        }); //end of ajax   

    });
            $(document).on('click', '#showCreateForm', function(e){

                var target = $(this).data('target');
                $(this).hide();
                $(target).toggle();
            });

function submitUserId(uid) {
    if(typeof uid !== 'undefined') {
        $.ajax({
            dataType: "json",
            method: "post",
            url: "/user/createUser",
            data: {
                "uid": uid,
            },
            success: function(data) {
                console.log(data);
                if(data.uid) {
                    store.set("uid", data.uid);
                }
            }
        });
    }
}

    </script>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->