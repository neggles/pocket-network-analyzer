<div id="page-wrapper" class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div role="main">
        <div class="row">
<div class="ibox float-e-margins">
<div class="ibox-title">
Version Details <label class="label label-primary pull-right"><?php echo $this->version->currentTag;?></label>
</div>
<div class="ibox-content">
<?php $releaseDetails = json_decode($this->version->currentTagDetails()); ?>

<?php if (is_array($releaseDetails)) :
    $releaseDetails = $releaseDetails[0];
endif; ?>
<div class="col-lg-4">
<button class="btn btn-primary btn-sm disabled" id="manuallyUpdate" disabled>
Check For Update</button>
</div>

<div class="col-lg-8">
<dl class="dl-horizontal">
<dt>Release Version:</dt>
<dd><?php echo $releaseDetails->name;?></dd>
<dt>Release Date:</dt>
<dd id="formattedDate"></dd>
<dt>Message:</dt>
<dd><?php echo $releaseDetails->message;?></dd>
</dl>
</div>
<script>
var d = new Date("<?php echo $releaseDetails->commit->authored_date;?>");
console.log(d);
jQuery('dd#formattedDate').html("<time>" + d + "</time>");

</script>
            <div class="release-notes">

            </div>

</div>

</div>
</div>
</div>
</div>
<?php $this->load->view('additional/footer');?>
</div> <!-- #page-wrapper -->
<div id="rawMarkdownNotes" style="display:none;">
<?php if ((isset($releaseDetails->release) && null !== $releaseDetails->release) && isset($releaseDetails->release->description)) :?>
    <?php echo $releaseDetails->release->description; ?>
<?php endif;?>
</div>
<script src="<?php echo $this->config->item('plugins_directory');?>showdown/dist/showdown.min.js"></script>
<script>
var converter = new showdown.Converter({
    'github_flavouring': true,
    'tables': true
});

$('.release-notes').html(converter.makeHtml($('#rawMarkdownNotes').html()));

$('button#manuallyUpdate').on('click',function(){
        $.ajax({
        method: 'post',
        url: "/version/checkForUpdate",
        data: {
            "force_check": true
        }
    });
});

/*
$('button#manuallyUpdate').on('click',function(){
        $.ajax({
        method: 'post',
        url: "/update/runUpdates",
        data: {
            "manually_update": true
        }
    });
})*/
</script>