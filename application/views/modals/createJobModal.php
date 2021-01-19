<div class="modal fade" role="dialog" id="createJobModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="text-center modal-title">Create a New Job</h4>
            </div>
            <div class="modal-body">
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
            </div>            
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->