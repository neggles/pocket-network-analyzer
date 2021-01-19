    <!--Edit comments modal-->
    <div id="editCommentModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Comments</h4>
                </div>
                <div class="modal-body">
                    <form class="form" id="commentsForm">
                        <div class="btn-toolbar" role="toolbar" aria-label="...">
                            <div class="btn-group">
                                <button type="button" id="editCommentsBtn" class="btn btn-default"><i class="fa fa-pencil-square-o"></i></button>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="id" id="id" value="">
                            <label for="oldComments">Comments</label>
                            <textarea id="comments" name="comments" class="form-control" style="overflow:auto;resize:none" disabled></textarea>
                        </div>
                        <div class="form-group">
                            <button id="saveComments" type="button" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="closeCommentModal" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--Edit comments modal-->