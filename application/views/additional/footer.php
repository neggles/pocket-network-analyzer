<!-- .footer-->
<div class="footer">
    <div class="pull-right">
        <?php if ($this->config->item('branch') === 'development') : ?>
        <span class="system-branch">
            Branch: <span class="current-branch"><strong><?php echo $this->config->item('branch');?></strong></span>
        </span>
        <span class="system-revision">
            Revision: <span class="current-revision"><strong><?php echo $this->version->getCurrentRevision();?></strong></span>
        </span>

    <?php elseif ($this->config->item('branch') === 'master') : ?>
        <span class="system-version">
        Version: <strong><a href="<?php echo site_url();?>version/details"><span class="current-version"><?php echo $this->version->getCurrentTag();?></span></a></strong>
    </span>
<?php endif; ?>
    </div>
    <div>
        <strong>Copyright</strong>
        <?php echo $this->config->item('copyright'); ?> &copy;
        <?php echo date('Y');?>
    </div>
</div>