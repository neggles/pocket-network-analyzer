<div class="col-lg-4">
    <div class="ibox float-e-margins" id="jobSelection">
        <div class="ibox-title">
                <h5>Select a Job</h5>         
        </div>
        <div class="ibox-content">
            <div class="form-group selectJobWrapper">
                <select class="form-control chosen-select-width" id="jobSelection">
                    <option value="0">Select a Job</option>
                    <?php $jobs = Job_model::getAllJobs(); ?>
                        <?php if ($jobs !== null) : ?>
                            <?php foreach ($jobs as $job) : ?>
                                <?php $selected = ($job->id == $currentJob->id) ? 'selected="selected"': ''; ?>
                                <option value="<?php echo $job->id;?>" <?php echo $selected;?>>
                                    <?php echo $job->name;?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                </select>
                <span class="help-block"></span>
            </div>
            <div class="text-center form-group">
                <button type="button" class="btn btn-primary switchJob" id="selectJob" data-target="#jobSelection">Switch Jobs</button>
            </div>
        </div>
    </div>
</div>
