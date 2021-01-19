<div id="initialView">
    <div class="text-center">
        <h2>Select a Job</h2>
    </div>
    <form class="form" id="selectAJobForm" name="selectAJobForm">
        <div class="form-group">
            <label for="newJobSelection">Select from current jobs</label>
            <select id="newJobSelection" class="form-control" autofocus>
                <option selected="selected" value="0">Select a Job</option>
                <?php $jobs=Job_model::getAllJobs(); ?>
                <?php
                foreach ($jobs->result() as $job) : ?>
                    <option value="<?php echo $job->id;?>">
                        <?php echo $job->name;?>
                    </option>
                <?php
                endforeach; ?>
            </select>
        </div>
        <div class="form-group text-center">
            <button type="button" id="selectJob" class="btn btn-primary">Proceed</button>
            <button type="button" class="btn btn-primary cancel">Cancel</button>
        </div>
    </form>
    <div>
        <button id="toggleCreateJob" class="btn btn-danger" title="Create a New Job">
            <i class="fa fa-user-plus"></i>
        </button>
    </div>
    <div id="createANewJobSection" style="display:none;">
        <div class="text-center">
            <h2>Create a New Job</h2>
        </div>
        <form id="createAJobForm" name="createAJobForm" class="form-vertical">
            <div class="form-group">
                <label for="jobName">Name</label>
                <input class="form-control" name="jobName" id="jobName" type="text" placeholder="Client Name">
            </div>
            <div class="form-group">
                <label for="banNumber">BAN Number</label>
                <input class="form-control" name="banNumber" id="banNumber" type="text" 
                placeholder="BAN number" maxlength="10">
            </div>
            <div class='form-group'>
                <label for="jobComments">Comments</label>
                <textarea class="form-control" name="jobComments" id="jobComments" placeholder="Comments"></textarea>
            </div>
            <div class="form-group">
                <div class="text-center">
                    <button type="button" class="btn btn-primary createJob">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>
