
<div class="modal fade" role="dialog" id="selectJobModal"> 
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="text-center modal-title">Select a Job</h4>
            </div>
            <div class="modal-body">
            <?php $jobs = Job_model::getAllJobs(); ?>
            <?php if ($jobs !== null) : ?>
                <div class="row">
                <div class="col-lg-12">
                        <form class="" id="selectAJobForm" name="selectAJobForm">
                            <div class="form-group selectJobWrapper text-center" >                               
                                <select id="newJobSelection" class="form-control chosen-select-width">
                                    <option value="0">Select a Job</option>                    
                                    <?php foreach ($jobs as $job) :?>
                                        <?php $selected = ($job->id == $this->session->userdata('jobId')) ? 'selected="selected"': ''; ?>
                                        <option value="<?php echo $job->id;?>" <?php echo $selected;?>><?php echo $job->name;?></option>
                                    <?php endforeach; ?>                               
                                </select>
                            <span class="help-block"></span>
                            </div>                            
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-primary switchJob" id="selectJob" data-target="#newJobSelection">Proceed</button>
                            </div>
                        </form>
                        </div>
                        </div>
                    <?php else : ?>
                <form id="createNewJobForm" class="form-vertical">
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
                            <button type="button" class="btn btn-primary btn-lg createJob">Create</button>
                        </div>
                    </div>
                    <div class="alert alert-info">
                    Once you create the new job, this modal will close. The newly created job will then be added to the dropdown in the Select a Job box.
                    </div>
                </form>
                    <?php endif; ?>
            </div>   
            <?php if ($jobs !== null) : ?> 
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createJobModal" data-dismiss="modal">Create New Job</button>
      </div>
    <?php endif; ?>

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- /.modal -->