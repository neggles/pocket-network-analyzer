<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="text-center">
                    <h3>Network Table</h3>
                </div>
            </div>
            <div class="ibox-content">
                <div class="well">
                    <div class="row">
                    <?php if (isset($currentJob->location)) : ?>
                        <?php //print_r($currentJob->location);?>
                    <?php endif; ?>
                        <div class="col-xs-3"> </div>
                        <div class="col-xs-3">
                            <i class="fa fa-wifi text-success fa-fw">Good</i>
                        </div>
                        <div class="col-xs-3">
                            <i class="fa fa-wifi text-warning fa-fw">Fair</i>
                        </div>
                        <div class="col-xs-3">
                            <i class="fa fa-wifi text-danger fa-fw">Weak</i>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                <?php if (isset($networkList) && $networkList !== null) : ?>
                        <table class="table table-hover" id="fullTable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>SSID</th>
                                    <th>MAC</th>
                                    <th>Channel</th>
                                    <!--<th>Manufacturer</th>-->
                                    <th>Encryption Type</th>
                                    <th>Signal Strength</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($networkList as $network) : ?>
                                <?php if (isset($network->ssid) && $network->ssid !== $dynamicSsid) : ?>
                                <?php
                                if (isset($network->mac)) :
                                        $mac = str_replace(":", "", $network->mac);
                                endif; ?>
                                    <tr>
                                        <td><i class="fa fa-wifi" style="color:<?php echo $this->wireless->getIcon($thresholds, $network->signal_strength); ?>"></i></td>
                                        <td>
                                            <a href="#" data-toggle="modal" data-target="#<?php echo $mac; ?>Modal">
                                                <?php
                                                if (isset($network->ssid)) :
                                                    echo $network->ssid;
                                                endif; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="#" data-toggle="modal" data-target="#<?php echo $mac; ?>Modal">
                                                <?php
                                                if (isset($network->mac)) :
                                                    echo $network->mac;
                                                endif; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="#" data-toggle="modal" data-target="#<?php echo $mac; ?>Modal">
                                                <?php
                                                if (isset($network->dist_system->channel)) :
                                                    echo $network->dist_system->channel;
                                                elseif (isset($network->ht_operation->primary_channel)) :
                                                        echo $network->ht_operation->primary_channel;
                                                endif; ?>
                                            </a>
                                        </td>
                                        <!--<td>
                                            <a href="#" data-toggle="modal" data-target="#<?php echo $mac; ?>Modal">
                                                <?php /*
                                                if (isset($network->mac)) :
                                                    echo $this->manuf->searchByMac($network->mac);
                                                endif; */ ?>
                                            </a>
                                        </td>-->
                                        <td>
                                            <a href="#" data-toggle="modal" data-target="#<?php echo $mac; ?>Modal">
                                                <?php
                                                if (!empty($network->wps) && $network->wps->wps_state == 2) :
                                                    echo 'WPS ';
                                                endif;
                                                if (!empty($network->wpa)) :
                                                    echo ' WPA';
                                                elseif (isset($network->robust_secure_network) && !empty($network->robust_secure_network)) :
                                                    echo ' WPA2';
                                                endif;
                                                if (isset($network->robust_secure_network->authentication_suites->auth)) :
                                                    echo '-' . $network->robust_secure_network->authentication_suites->auth;
                                                endif;
                                                if (isset($network->robust_secure_network->pairwise_cipher->encryption)) :
                                                    if (is_array($network->robust_secure_network->pairwise_cipher->encryption)) :
                                                        foreach ($network->robust_secure_network->pairwise_cipher->encryption as $encryptionType) :
                                                            echo '-' . $encryptionType;
                                                        endforeach;
                                                    else :
                                                        echo '-' . $network->robust_secure_network->pairwise_cipher->encryption;
                                                    endif;
                                                endif;
                                                ?>
                                            </a>
                                        </td>
                                        <td data-class-name="priority">
                                            <a href="#" data-toggle="modal" data-target="#<?php echo $mac; ?>Modal">
                                                <?php
                                                if (isset($network->signal_strength)) :
                                                    echo $network->signal_strength;
                                                endif; ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                            <div class="text-center">
                                <h3 class="text-danger">No Scans for this job yet.</h3>
                            </div>
                    <?php endif; //endif $networkList isset?>
                </div>
            </div>
            <!--Table Chart of all wireless networks-->
            
        </div>
    </div>
</div>
