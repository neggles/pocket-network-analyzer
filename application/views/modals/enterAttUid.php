<div class="modal fade" role="dialog" id="enterAttUid"> 
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="text-center modal-title">Enter Your AT&amp;T UID</h4>
            </div>
            <div class="modal-body">
                <form id="loginAttUid" class="form-vertical">
                    <div class="form-group">
                        <label for="uid">User id</label>
                        <input class="form-control" name="uid" maxlength="6" id="uid" type="text" placeholder="ATT UID">
                    </div>
                    <button type="button" class="btn btn-primary" id="submitUid">Submit</button>
                    <div class="alert alert-info">
                    Once you create the new job, this modal will close. The newly created job will then be added to the dropdown in the Select a Job box.
                    </div>
                </form>
            </div>   
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#enterAttUid" >Not you?</button>
      </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <script>
        $(document).on('ready',function() {
    $(document).on('click', '#submitUid', function(e) {
        e.preventDefault();
        var uid = $('input#uid').val();

        $.ajax({
            dataType: "json",
            method: "post",
            url: "/user/createUser",
            data: {
                "uid": uid,
            },
            success: function(data) {
                console.log(data);
            }
        });

    });

        });
    </script>
    <!-- /.modal-dialog -->
</div>

<!-- /.modal -->