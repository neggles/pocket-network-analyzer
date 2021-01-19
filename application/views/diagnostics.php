<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div id="page-wrapper" class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
                <div class="text-center">
            <h1>Diagnostics</h1>
        </div>

            <div class="col-lg-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h3 class="panel-title text-center"><i class="fa fa-wifi"></i> Network Information</h3>
                    </div>
                    <div class="ibox-content">
                        <!--Private IP-->
                        <div class="row-fluid">
                            <p>Private IP Address
                            
                                <?php if ($privateIp = $this->vpn->checkInterfaceIpAddress(true)) : ?>
                                    <span class="label label-primary">
                                    <?php echo $privateIp; ?>  
                                    </span>                                 
                                <?php else : ?>
                                    <a class="ipToggle" title="Click to request private IP" data-interface="vpn" data-value="request" onClick="">
                                    <span class="label label-danger">
                                    <?php echo "No IP Address Assigned.";?>
                                    </span>
                                    </a>
                                <?php endif; ?>                    
                         
                            </p>
                        </div>

                        <!--Ethernet IP-->
                        <div class="row-fluid">
                            <p>Ethernet IP Address:
                            
                            <?php if ($ethernetIp = $this->ethernet->checkInterfaceIpAddress(true)) : ?>
                                <a class="ipToggle" title="Click to release ethernet IP" data-interface="ethernet" data-value="release" onClick="">
                                <span class="label label-primary">
                                <?php echo $ethernetIp; ?>
                                </span>
                                </a>
                            <?php else : ?>
                                <a class="ipToggle" title="Click to request ethernet IP" data-interface="ethernet" data-value="request" onClick="">
                                <span class="label label-danger">
                                <?php echo "No IP Address Assigned."; ?>
                                </span>
                                </a>
                            <?php endif; ?>                 
                            </p>
                        </div>
                        <!--Wireless IP-->
                        <div class="row-fluid">
                            <p>Wireless IP Address: 
                            
                                <?php if ($wirelessIp = $this->wireless->checkInterfaceIpAddress(true)) : ?>

                                    <a class="ipToggle" title="Click to release wireless IP" data-interface="wireless" data-value="release">
                                    <span class="label label-primary">
                                    <?php echo $wirelessIp; ?>
                                    </span>
                                    </a>
                                <?php else : ?>
                                    <a class="ipToggle" title="Click to request wireless IP" data-interface="wireless" data-value="request" onClick="">
                                    <span class="label label-danger" >
                                    <?php echo "No IP Address assigned."; ?>
                                    </span>
                                    </a>
                                <?php endif; ?>                 
                         
                            </p>
                        </div>
                        <!--Ethernet Status-->
                        <div class="row-fluid">
                           <p> Ethernet Status
                                <?php if ($this->ethernet->getInterfaceStatus()) : ?>
                                    <a class="interfaceToggle" title="Click to turn off ethernet interface" data-interface="ethernet" data-value="down" onClick="" style="cursor: pointer;">
                                    <span class="label label-primary">up</span>
                                    </a>
                                <?php else : ?>
                                    <a class="interfaceToggle" title="Click to turn on ethernet interface" data-interface="ethernet" data-value="up" onClick="" style="cursor: pointer;">
                                    <span class="label label-danger">down</span>
                                    </a>
                                <?php endif; ?>
                           </p>
                        </div>
                        <!--Wireless Status-->
                        <div class="row-fluid">
                            <p> Wireless Status
                                <?php if ($this->wireless->getInterfaceStatus()) : ?>
                                    <a class="interfaceToggle" title="Click to turn off wireless interface" data-interface="wireless" data-value="down" onClick="" style="cursor: pointer;">
                                    <span class="label label-primary">up</span>
                                    </a>
                                <?php else : ?>
                                    <a class="interfaceToggle" title="Click to turn on wireless interface" data-interface="wireless" data-value="up" onClick="" style="cursor: pointer;">
                                    <span class="label label-danger">down</span>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>         
            </div>
                    <div class="col-lg-3">
            <div class="ibox float-e-margins">
            <div class="ibox-title">
            <h5>MAC Address</h5>
            <span class="ibox-tools">
            <small><?php echo $mac; ?></small>
        </span>
            </div>
            <div class="ibox-content">
                <img class="img" src="data:image/jpeg;base64,<?php echo $this->qrcode->returnImageStringBase64(); ?>" alt="QR Code"/>
            </div>
            </div>
            </div>

            <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <?php $releaseDetails = json_decode($this->version->currentTagDetails()); ?>
<?php if (is_array($releaseDetails)) :
    $releaseDetails = $releaseDetails[0];
endif; ?>
<dl class="dl-horizontal">
<dt>Release Version:</dt>
<dd><?php echo $releaseDetails->name;?></dd>
<dt>Release Date:</dt>
<dd id="formattedDate"></dd>
<dt>Message:</dt>
<dd><?php echo $releaseDetails->message;?></dd>
</dl>

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
        <div class="row">
            <div class="col-md-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h3 class="panel-title text-center"><i class="fa fa-wifi"></i> System Packages Status</h3>
                    </div>
                    <div class="ibox-content">
                        <!--DNS Service-->
                        <div class="row-fluid">
                            <p>DNS Service:
                            <?php if ($dnsmasq == "active(running)") : ?>
                            <span class="label label-primary">
                        <?php else : ?>
                            <span class="label label-danger">
                        <?php endif; ?>
                                    <?php echo $dnsmasq; ?>                  
                            </span>
                            </p>
                        </div>
                        <div class="row-fluid">
                            <p>HotSpot Service:
                            <?php if ($hostapd == "active(running)") : ?>
                            <span class="label label-primary">
                        <?php else : ?>
                            <span class="label label-danger">
                        <?php endif; ?>
                                    <?php echo $hostapd; ?>                  
                            </span>
                            </p>
                        </div>

                        <!-- Redis Server -->
                        <div class="row-fluid">
                            <p>Redis Server:
                            <?php if ($redis == "active(running)") : ?>
                            <span class="label label-primary">
                        <?php else : ?>
                            <span class="label label-danger">
                        <?php endif; ?>
                                <?php echo $redis; ?>
                            </span>
                            </p>
                        </div>
                        <!--WebSocket Server-->
                        <div class="row-fluid">
                            <p>WebSocket Server: 
                            <?php if ($slanger == "active(running)") : ?>
                            <span class="label label-primary">
                        <?php else : ?>
                            <span class="label label-danger">
                        <?php endif; ?>
                                    <?php echo $slanger; ?>                  
                            </span>
                            </p>
                        </div>
                    </div>
                </div>         
            </div> 
            <div class="col-md-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h3 class="panel-title text-center"><i class="fa fa-wifi"></i> Wireless Details</h3>
                    </div>
                    <div class="ibox-content">
                        <p class="lead">SSID: <b><?php echo $dynamicSsid;?></b></p>
                    </div>
                </div>
            </div>

        </div>

</div> <!-- .wrapper-content -->
<?php $this->load->view('additional/footer');?>
</div> <!-- #page-wrapper -->
<div id="rawMarkdownNotes" style="display:none;">
<?php if ((isset($releaseDetails->release) && null !== $releaseDetails->release) && isset($releaseDetails->release->description)) :?>
<?php echo $releaseDetails->release->description;?>
<?php endif;?>
</div>
<script src="<?php echo $this->config->item('plugins_directory');?>showdown/dist/showdown.min.js"></script>
<script>
var converter = new showdown.Converter({
    'github_flavouring': true,
    'tables': true
});

$('.release-notes').html(converter.makeHtml($('#rawMarkdownNotes').html()));
$(function() {
$(document).on('click', 'a.interfaceToggle', function(){
    var interface = $(this).data('interface');
    var value = $(this).data('value');
        $.ajax({
        method: 'post',
        dataType: 'json',
        url: "/network/interfaceStatus",
        data: {
            "interface": interface,
            "value": value
        },
        success: function(data) {
            console.log(data);
            if(data.status == true) {
                console.log(data.msg);
                console.log(interface);
                var label, newLabel, newText, newValue;
                if(value == "up") {
                    label = "danger";
                    newLabel = "primary";
                    newText = "up";
                    newValue = "down";
                } else {
                    label ="primary";
                    newLabel = "danger";
                    newText = "down";
                    newValue = "up";
                }

                $('a.interfaceToggle[data-interface='+interface+']').data('value', newValue);
                $('a.interfaceToggle[data-interface='+interface+'] span').removeClass('label-'+label).addClass("label-"+newLabel).text(newText);
            }
        }
    });
});

$(document).on('click', 'a.ipToggle', function(){
    var interface = $(this).data('interface');
    var value = $(this).data('value');
        $.ajax({
        method: 'post',
        dataType: 'json',
        url: "/network/interfaceIpAddress",
        data: {
            "interface": interface,
            "value": value
        },
        success: function(data) {
            console.log(data);
            if(data.status == true) {
                console.log(data.msg);
                console.log(interface);
                console.log(data.address);
                var label, newLabel, newText, newValue;
                if(value == "release") {
                    label = "danger";
                    newLabel = "primary";
                    newText = "release";
                    newValue = "request";
                } else {
                    label ="primary";
                    newLabel = "danger";
                    newText = "request";
                    newValue = "release";
                }

                $('a.ipToggle[data-interface='+interface+']').data('value', newValue);
                $('a.ipToggle[data-interface='+interface+'] span').removeClass('label-'+label).addClass("label-"+newLabel).text(address);
            } else {
                alertify.error(data.msg);
            }
        }
    });
});
});
</script>