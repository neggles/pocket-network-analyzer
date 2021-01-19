<?php if (isset($networkList) && $networkList !== null) : ?>
            <?php foreach ($networkList as $network) : ?>
            <?php if (isset($network->ssid) && $network->ssid !== $dynamicSsid) : ?>
            <div class="modal fade" tabindex="-1" role="dialog" id="<?php echo (isset($network->mac)) ? str_replace(":", "", $network->mac) : "";?>Modal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title text-center">
                                    <?php
                                    if (isset($network->ssid)) :
                                        echo $network->ssid;
                                    endif; ?> 
                                    details</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="lead">Channel:<?php if (isset($network->dist_system->channel)) :
                                            echo $network->dist_system->channel;
elseif (isset($network->ht_operation->primary_channel)) :
                                                echo $network->ht_operation->primary_channel;
else :
                                                echo 'Could not reliably determine this AP\'s channel settings.';
endif;?></p>
                                    <p class="lead">
                                        <?php if (isset($network->frequency)) :?>
                                            Frequency: <?php echo $network->frequency .' MHz' ; ?>
                                        <?php else : ?>
                                            <p class="lead">Could not reliably determine this AP's frequency
                                        <?php endif;?>
                                        </p>
                                        <!--<p class="lead"><?php if (isset($network->mac)) :
?>Manufacturer:<?php /*echo $this->manuf->searchByMac($network->mac); */ ?><?php
endif; ?></p>-->
                                        <p class="lead">Encryption Type: <?php if (!empty($network->wps) && $network->wps->wps_state == 2) :
                                                echo '<span class="encryption-method">WPS</span> ';
endif;
if (!empty($network->wpa)) :
    echo '<span class="encryption-method"> WPA</span>';
elseif (isset($network->robust_secure_network) && !empty($network->robust_secure_network)) :
                                                    echo ' <span class="encryption-method">WPA2</span>';
endif;
if (isset($network->robust_secure_network->authentication_suites->auth)) :
     echo '-<span class="encryption-cipher">' . $network->robust_secure_network->authentication_suites->auth . '</span>';
endif;
if (isset($network->robust_secure_network->pairwise_cipher->encryption)) :
    if (is_array($network->robust_secure_network->pairwise_cipher->encryption)) :
        foreach ($network->robust_secure_network->pairwise_cipher->encryption as $encryptionType) :
            echo '-<span class="encryption-cipher">' . $encryptionType . '</span>';
        endforeach;
    else :
        echo '-<span class="encryption-cipher">' . $network->robust_secure_network->pairwise_cipher->encryption . '</span>';
    endif;
endif; ?></p>
                                        <p class="lead" style="color:<?php echo $this->wireless->getIcon($thresholds, $network->signal_strength);?>">Signal Strength: <?php echo $network->signal_strength; ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <?php if (isset($network->ht_capabilities->capabilities) and is_object($network->ht_capabilities->capabilities)) : ?>
                                <!--High Throughput Capes-->
                                <div class="col-md-6">
                                    <div class="panel panel-default ">
                                        <div class="panel-heading">
                                            <div class="text-center">High Throughput Capabilities</div>
                                        </div>
                                        <div class="panel-body">
                                            <div class="list-group">

                                                <?php foreach ($network->ht_capabilities->capabilities->capability as $capes) :?>
                                                <a href="#" class="list-group-item">
                                                    <?php echo $capes ?>
                                                </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php else : ?>
                                <div class="col-md-6">
                                    <div class="alert alert-danger">
                                        <p>This device does not support High Throughput or we were unable to 
                                        obtain the neccessary information.</p>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (isset($network->vht_capabilities->capabilities)) : ?>
                                <!--Very High Throughput Capes-->
                                <div class="col-md-6">
                                    <div class="panel panel-default ">
                                        <div class="panel-heading">
                                            <div class="text-center">Very High Throughput Capabilities</div>
                                        </div>
                                        <div class="panel-body">
                                            <div class="list-group">
                                                <?php foreach ($network->vht_capabilities->capabilities->capability as $capes) :?>
                                                <a href="#" class="list-group-item">
                                                    <?php echo $capes; ?>
                                                </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php else : ?>
                                <div class="col-md-6">
                                    <div class="alert alert-danger">
                                        <p>This device does not support Very High Throughput or we were unable to obtain the neccessary information.</p>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- /.modal-body -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
            <?php endif;?>
            <?php endforeach; ?>
            <?php endif; ?>
            <!--endif isset-->