<?php
 defined('BASEPATH') or exit('No direct script access allowed'); ?>
    <div id="page-wrapper" class="gray-bg">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Submit an Issue</h5>
                        </div>
                        <div class="ibox-content">
                            <form class="form-horizontal" id="createIssueForm">
                                <p>Please be sure that you are connected to the internet before attempting submission.</p>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Email</label>
                                    <div class="col-lg-10">
                                        <input type="email" placeholder="Email" class="form-control" name="email"> <span class="help-block m-b-none">This will help the development team contact you for further information if needed.</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Name</label>
                                    <div class="col-lg-10">
                                        <input type="text" placeholder="John Doe" class="form-control" name="name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Title</label>
                                    <div class="col-lg-10">
                                        <input type="text" placeholder="Something is broken" class="form-control" name="title"> <span class="help-block m-b-none" required>A descriptive title.</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Issue</label>
                                    <div class="col-lg-10">
                                        <textarea placeholder="Details about issue" class="form-control" name="description" required></textarea>
                                        <span class="help-block m-b-none">Be as thorough as possible.</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-10 col-lg-offset-2">
                                        <button class="btn btn-primary" name="submitIssue" id="submitIssue">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    $(document).on("click", "#submitIssue", function(e) {

        e.preventDefault();

        alertify.log("Issue is being submitted.");

        $.ajax({
            method: "post",
            url: "/issue/createIssue",
            dataType: "json",
            data: $('form#createIssueForm').serialize(),
            success: function(data) {
                console.log(data);
                if (data.status == true) {
                    alertify.success(data.msg);
                    $('form#createIssueForm')[0].reset();
                } else if (data.status == false) {
                    alertify.error(data.msg);
                }
            }
        });

    });
    </script>
    </body>

    </html>
