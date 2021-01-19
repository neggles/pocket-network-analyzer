<div class="row border-bottom white-bg">
<nav class="navbar navbar-static-top" >
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <i class="fa fa-bars"></i>
            </button> 
            <a class="navbar-brand" href="<?php echo site_url();?>">           
            POCKET-FI
            </a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav" id="navigation">
            <?php /* load the navigation from the json file. */ $navigation = json_decode(file_get_contents(FCPATH . "application/views/navigation/navigation.json")); ?>
            <?php foreach ($navigation as $key => $value) : ?>
                <?php if ($key =="top_nav") : ?>
                    <?php foreach ($value as $link) : ?>
                        <?php $icon = ''; ?>
                        <?php if ($link->icon !== "") : ?>
                            <?php $icon = '<i class="' . $link->icon . ' fa-fw"></i>'; ?>
                        <?php endif; ?>
                        <?php if ($link->url === 'gfast') : ?>
                <!-- The dropdown options available on all pages. -->
                <li class="dropdown" title="View Additional Settings">
                    <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown" id="dropdown-menu-toggle">
                        G.Fast
                        <!--<span class="caret"></span>-->
                    </a>
                    <ul role="menu" class="dropdown-menu">
                        <li>
                        <a href="<?php echo site_url() . $link->url ?>">
                            Site Survey</a>
                        </li>
                        <li>
                        <a href="<?php echo site_url() ?>gfast/technician">
                            Technician</a>
                        </li>
                        <li>
                        <a href="<?php echo site_url() ?>gfast/history">
                            History</a>
                        </li>
                    </ul>
                </li>
                        <?php else : ?>
                            <li><a href="<?php echo site_url() . $link->url ?>"><?php echo $icon . $link->text; ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif;?>
            <?php endforeach; ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
            <li>
            <a href="#" title="Edit Wireless Networks" data-toggle="modal" data-target="#networkConfigurationModal">
            <i class="fa fa-wifi fa-1x text-navy"></i>
            </a>
            </li>
                <li>
                    <a href="#"  title="If this number is lower than expected, do not be alarmed. In order to conserve power on both the router and this device, the wireless cards will usually default to a low link rate. When there is actual traffic moving between the two, the actual link rate will be used.">                      
                    <?php if ($this->wireless->getLinkRate()) : ?>
                        <i class="fa fa-wifi fa-fw"></i><?php echo $this->wireless->getLinkRate(); ?>
                    <?php endif; ?>
                        <?php if ($this->ethernet->getLinkRate()) : ?>
                            <i class="fa fa-sitemap fa-fw"></i><?php echo $this->ethernet->getLinkRate(); ?>
                        <?php endif; ?>                  
                    </a>
                </li>
                <?php if (isset($currentJob->ban)) : ?>
                <li>
                    <a href="#">BAN: <?php echo $currentJob->ban;?></a>
                </li>
                <?php endif;?>
                <!-- The dropdown options available on all pages. -->
                <li class="dropdown" title="View Additional Settings">
                    <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown" id="dropdown-menu-toggle">
                        <i class="fa fa-bars"></i>
                        <!--<span class="caret"></span>-->
                    </a>
                    <ul role="menu" class="dropdown-menu">
                        <li>
                        <a href="<?php echo site_url() ?>diagnostics">
                            <i class="fa fa-stethoscope fa-fw"></i>Diagnostics</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="#" id="shutdown"><i class="fa fa-power-off fa-fw"></i>Shutdown</a></li>
                        <li class="divider"></li>
                        <li><a href="#" id="restart"><i class="fa fa-history fa-fw"></i>Restart</a></li>
                        <li class="divider"></li>
                        <li><a href="#" onclick="showSetupWizard('click', 'createJob');">
                        <i class="fa fa-plus-square fa-fw"></i>Create New Job</a></li>
                        <li class="divider"></li>
                        <li><a href="#" onclick="showSetupWizard('click', 'job');">
                        <i class="fa fa-home fa-fw"></i>Switch Jobs</a></li>
                        <li class="divider"></li>
                        <li><a href="#" onclick="showSetupWizard('click', 'uid');">
                        <i class="fa fa-user fa-fw"></i>Switch User</a></li>                        
                        <li class="divider"><a href="#"></a></li>
                        <li><a href="#" onclick="disconnectFromWirelessNetwork();">
                        <i class="fa fa-wifi fa-fw"></i>Disconnect Wireless Network</a></li>                        
                        <li class="divider"><a href="#"></a></li>
                        <li id="version-wrapper">
                        <a href="<?php echo base_url();?>version/details">Version: <span class="current-version"><?php echo $this->version->currentTag;?></span></a>
                        </li>
                    </ul>
                </li>
        <?php if (isset($status) && $status !== 'initial-test') : ?>
                <li class="dropdown" data-toggle="tooltip" data-placement="auto" 
                title="View Available Wireless Networks">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-dismiss="tooltip">
                        <i class="fa fa-wifi"></i>
                        <span class="caret"></span>
                    </a>
                    <ul id="wirelessNetworkList" class="dropdown-menu">
                    </ul>
                </li>
            <?php endif; ?>
            </ul>
        </div>
        <!--/.nav-collapse -->
</nav>
</div>
<?php $this->load->view('breadcrumb');?>
