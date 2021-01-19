<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="theme-color" content="#0093d1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<title>POCKET-FI</title>
<link rel="stylesheet" href="/assets/css/customStyles.min.css">
<link rel="stylesheet" href="<?php echo $this->config->item('plugins_directory');?>font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="/assets/css/dataTables.fontAwesome.css">
<link rel="stylesheet" href="<?php echo $this->config->item('plugins_directory');?>datatables/media/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="<?php echo $this->config->item('plugins_directory');?>nouislider/distribute/nouislider.css">
<link rel="stylesheet" href="<?php echo $this->config->item('plugins_directory');?>animate.css/animate.min.css">
<link rel="stylesheet" href="<?php echo $this->config->item('plugins_directory');?>chosen/chosen.css">
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="icon" type="image/png" href="/assets/images/att-small.png" />
<script>
window.globalTimeout = true;
</script>
<script src="<?php echo $this->config->item('plugins_directory');?>store-js/dist/store.everything.min.js"></script>
<?php if ($this->config->item('pusher_enabled')) : ?>
<script src="/assets/js/pusher.min.js"></script>
<script>
    var pusher = new Pusher("<?php echo $this->config->item('pusher_app_key'); ?>");
</script>
<?php endif; ?>
</head>
<body class="top-navigation pace-done">
<div id="wrapper">