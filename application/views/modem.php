<div id="page-wrapper" class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
    	<div class="col-lg-4">
    	<div class="ibox float-e-margins">
    	<div class="ibox-title">
    	<h5>Refresh Rate</h5>
    	</div>
    	<div class="ibox-content">
    	  	<div id="timeSlider"></div>
    	  </div>
    	  </div>
   		</div>
    </div>
        <div class="row">
            <div class="col-lg-12 m-b-md">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#broadband_content" data-toggle="tab">Broadband</a></li>
                        <li><a href="#log_content" data-toggle="tab">Logs</a></li>
                        <li><a href="#ethernet_content" data-toggle="tab">Network Statistics</a></li>
                        <li><a href="#dhcp_server_content" data-toggle="tab">DHCP</a></li>
                        <li><a href="#system_information_content" data-toggle="tab">System Information</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="broadband_content" class="tab-pane active">
                            <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6" id="broadband_connection_widget">

                                </div> 
                                <div class="col-sm-6" id="broadband_connection_stats">
                                    <table class="table table-striped" id="connection_stats_table">
                                    <thead>                                    
                                    <tr><th>Name</th><th>Downstream</th><th>Upstream</th></tr>                                  
                                    </thead>
                                    <tbody id="connection_table"></tbody>
                                    </table>
                                </div>   
                                </div>                            
                            </div>
                        </div>
                        <div id="log_content" class="tab-pane">
                            <div class="panel-body">
                                <div id="log_widget">
                                </div>
                            </div>
                        </div>
                        <div id="ethernet_content" class="tab-pane">
                            <div class="panel-body">
                                <div id="ethernet_widget" class="col-lg-12">
                                </div>
                            </div>
                        </div>
                        <div id="dhcp_server_content" class="tab-pane">
                            <div class="panel-body">
                                <div id="dhcp_server_widget">
                                </div>
                            </div>
                        </div>
                        <div id="system_information_content" class="tab-pane">
                            <div class="panel-body">
                                <div id="system_information_widget">

                                </div>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      
    </div>
</div>
<script src="<?php echo $this->config->item('plugins_directory');?>nouislider/distribute/nouislider.min.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>table-to-json/lib/jquery.tabletojson.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>jquery-json2html/json2html.js"></script>
<script src="<?php echo $this->config->item('plugins_directory');?>jquery-json2html/jquery.json2html.js"></script>
<script src="/assets/js/bbttModem.js"></script>
