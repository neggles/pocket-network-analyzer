<?php
 defined('BASEPATH') or exit('No direct script access allowed');
?>
<div id="page-wrapper" class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 m-b-md">
                <div class="ibox float-e-margins" style="min-height:200px">
                    <div class="ibox-title">
                        <h5>Image Recognition</h5>
                    </div>
                    <div class="ibox-content">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-warning">
                        <?php echo $error;?>
                        </div>
                    <?php endif; ?>
                        <?php echo form_open_multipart('ocr/do_upload');?>

                        <div class="form-group">
                        <input type="file" name="imageUpload" accept="image/*" capture="camera" class="form-control"/>
                        </div>
                        <div class="form-group">
                        <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
         <div class="col-lg-12 m-b-md">
        <div class="ibox float-e-margins">
        <div class="ibox-title">
        <h5>Results</h5>
        </div>
        <div class="ibox-content">
        <?php if (!empty($password) || !empty($ssid)): ?>
            <input class="form-control" name="ssid" id="ssid" value="<?php echo $ssid; ?>">
            <input class="form-control" name="password" id="password" value="<?php echo $password; ?>">
        <?php endif;?>
        <?php if (!empty($imageText)): ?>
            <pre>
                <?php print_r($imageText); ?>
            </pre>
        <?php endif; ?>
        </div>
        </div>
        </div>
        </div>
    </div>

<?php $this->load->view('additional/footer');?>
</div>
</div>
