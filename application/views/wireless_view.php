<?php
defined('BASEPATH') or exit('No direct script access allowed');
if (isset($currentJob->id) && $currentJob->id !== 0) :
    /*
        Get the most recent wireless network scan results
    */
    $networkList = json_decode($this->wireless->getRecentResults($currentJob->id));
    ?>
    <?php if (isset($networkList)) : ?>
    <style>
    #myNewNetworkChart {
        width: 100%;
        height: 500px;
    }
    
    #myNewNetworkChart5Glower {
        width: 100%;
        height: 500px;
    }
    
    #myNewNetworkChart5Gmiddle {
        width: 100%;
        height: 500px;
    }
    
    #myNewNetworkChart5Gupper {
        width: 100%;
        height: 500px;
    }
    </style>
    <?php endif; ?>
    <script src="/assets/js/plugins/wifi/dbmChart.js?v=<?php echo time();?>"></script>
    <script src="/assets/js/plugins/wifi/charts.js?v=<?php echo time();?>"></script>
    <div id="page-wrapper" class="gray-bg">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div role="main">
                <?php $this->load->view('wireless/top_row_view', array('networkList' => $networkList));?>
                <?php $this->load->view('wireless/wireless_utilization_view', array('networkList' =>$networkList));?>
                <!--Network Scan Chart Signal to Noise-->
                <!--Table Chart of all wireless networks-->
                <?php $this->load->view('wireless/network_table_view', array('networkList' => $networkList, 'thresholds' => $thresholds, 'dynamicSsid' => $dynamicSsid));?>
                <script>
                $(document).ready(function() {
                    $('#fullTable').DataTable({
                        "order": [
                            [5, "asc"]
                        ],
                        "aoColumns": [{
                                "bSortable": false
                            },
                            null,
                            null,
                            null,
                            null,
                            null
                        ]
                    });
                });

                $(document).on('click', '#runNewNetworkScan', function() {
                    swal({
                            title: "Are you sure you want to run a new scan?",
                            text: "If you are currently connected to a wireless network you will be disconnected.",
                            type: "warning",
                            showCancelButton: true,
                            closeOnConfirm: false,
                            confirmButtonText: "I am sure",
                            animation: "slide-from-top",
                        },
                        function() {
                            swal({
                                title: "Running a new network scan<br> <i class='fa fa-cog fa-spin fa-4x'></i>",
                                text: "This will only take a few seconds.",
                                html: true,
                                showConfirmButton: false
                            });
                            $.ajax({
                                method: "post",
                                url: "/network/runnetworkscan",
                                data: {
                                    job: "<?php echo $jobId ;?>"
                                },
                                success: function(data) {
                                    console.log(data);
                                    swal.close();
                                    window.location.reload();
                                }
                            });
                        })
                });


                <?php if (isset($networkList)) : ?>


                // Chart Data
                var data;
                /*
                    Draw the 5G chart when the data is clicked
                 */
                $(document).on('click', '[data-chart=myNewNetworkChart5Gupper]', function() {
                    if (wifiChart5Gupper != null && wifiChart5Gupper != undefined) {
                        wifiChart5Gupper.resize();
                    } else {
                       //setChartData(data, "149") 
                       setChartData(data, "5") 
                    }
                });

                $(document).on('click', '[data-chart=myNewNetworkChart5Gmiddle]', function() {
                    if (wifiChart5Gmiddle != null && wifiChart5Gmiddle != undefined) {
                        wifiChart5Gmiddle.resize();
                    } else {
                       setChartData(data, "100") 
                    }
                });

                $(document).on('click', '[data-chart=myNewNetworkChart5Glower]', function() {
                    if (wifiChart5Glower != null && wifiChart5Glower != undefined) {
                        wifiChart5Glower.resize();
                    } else {
                       setChartData(data, "36") 
                    }
                });
                

                $(document).on('click', '[data-chart=myNewNetworkChart2G]', function() {
                    //setChartData(data, "2")                
                    if (wifiChart2G != null && wifiChart2G != undefined) {
                        wifiChart2G.resize();
                    } 
                });

                var wifiChart2G = undefined;
                var wifiChart5Glower = undefined;
                var wifiChart5Gmiddle = undefined;
                var wifiChart5Gupper = undefined;

                // calls the entry function on page load.
                entry();

                function entry() {
                    
                    <?php if ($networkList !== null && !isset($networkList->status)) : ?>
                    data = {
                            title: 'Wireless Networks',
                            wifiList2G: [
                                <?php foreach ($networkList as $network) : ?>
                                <?php $channel = isset($network->dist_system->channel) ? $network->dist_system->channel : (isset($network->ht_operation->primary_channel) ? $network->ht_operation->primary_channel : "") ?>
                                <?php if ($network->ssid !== $dynamicSsid && $channel <= 11) : ?>                                
                                <?php $powerLevel = str_replace(" dBm", "", $network->signal_strength); ?>                               
                                {
                                    name: "<?php echo addslashes($network->ssid); ?>",
                                    primaryChannel: "<?php echo $channel; ?>",
                                    centerChannel: "<?php echo $channel; ?>",
                                    bandwidth: 2,
                                    peak: <?php echo (int) $powerLevel ?>,
                                    distance: <?php echo Wireless_model::calculateDistance($network->frequency, $powerLevel);?>
                                },                                
                                <?php endif; ?>
                                <?php endforeach; ?>
                            ],
                            wifiList5Gupper: [
                                <?php foreach ($networkList as $network) : ?>
                                <?php $primaryChannel = isset($network->dist_system->channel) ? $network->dist_system->channel : (isset($network->ht_operation->primary_channel) ? $network->ht_operation->primary_channel : "") ?>

                                <?php if ($network->ssid !== $dynamicSsid && ($primaryChannel >= 36 && $primaryChannel <= 165)) : ?>                                
                                <?php $powerLevel = str_replace(" dBm", "", $network->signal_strength); ?>
                                <?php /* The wireless scan tool return values 0-3 which correspond with the channel width, if it is not set we will fallback to settings the value to 4 which corresponds to a channel width of 20 MHz */ ?>
                                <?php $channelWidth = (isset($network->vht_operation->channel_width) ? $network->vht_operation->channel_width : 4);
                                $channelWidth = Wireless_model::cleanChannelWidth($channelWidth);
                                if ($channelWidth > 2) :
                                    $channelWidth = $channelWidth / 10;
                                endif;
                                $centerChannel = (isset($network->vht_operation->center_freq_segment_1) ? $network->vht_operation->center_freq_segment_1 : null);
                                ?>                                
                                {
                                    name: "<?php echo addslashes($network->ssid); ?>",                                    
                                    <?php if ($centerChannel) : ?>
                                    primaryChannel: <?php echo $primaryChannel; ?>,
                                    centerChannel: <?php echo $centerChannel; ?>,
                                    <?php else : ?>
                                    primaryChannel: <?php echo $primaryChannel; ?>,
                                    centerChannel: <?php echo $primaryChannel; ?>,
                                    <?php endif; ?>
                                    bandwidth: <?php echo $channelWidth; ?>,
                                    peak: <?php echo (int) $powerLevel ?>,
                                    distance: <?php echo Wireless_model::calculateDistance($network->frequency, $powerLevel);?>
                                },                                
                                <?php endif; ?>
                                <?php endforeach; ?>                               
                            ]
                        }
                        
                    setChartData(data, "2");
                    <?php endif; ?>
                }

                function setChartData(data, chart) {

                    if (!wifiChart2G) {
                        wifiChart2G = echarts.init(document.getElementById('myNewNetworkChart'))
                    }
                    if (!wifiChart5Gupper && chart == "149") {
                        wifiChart5Gupper = echarts.init(document.getElementById('myNewNetworkChart5Gupper'))
                    }
                    if (!wifiChart5Gupper && chart == "5") {
                        wifiChart5Gupper = echarts.init(document.getElementById('myNewNetworkChart5Gupper'))
                    }
                    if (!wifiChart5Gmiddle && chart == "100") {
                        wifiChart5Gmiddle = echarts.init(document.getElementById('myNewNetworkChart5Gmiddle'))
                    }
                    if (!wifiChart5Glower && chart == "36") {
                        wifiChart5Glower = echarts.init(document.getElementById('myNewNetworkChart5Glower'))
                    }

                    var options = {
                        chartTitle: data.title
                    }

                    if(chart == "2") {
                        drawWifiDbmChart(wifiChart2G, data.wifiList2G, options, chart)
                    } else if (chart == "149") {
                        drawWifiDbmChart(wifiChart5Gupper, data.wifiList5Gupper, options, chart) 
                    } else if (chart == "100") {
                        drawWifiDbmChart(wifiChart5Gmiddle, data.wifiList5Gmiddle, options, chart) 
                    } else if (chart == "36") {
                        drawWifiDbmChart(wifiChart5Glower, data.wifiList5Glower, options, chart) 
                    }  else if (chart == "5") {
                        drawWifiDbmChart(wifiChart5Gupper, data.wifiList5Gupper, options, chart) 
                    }                  
                    
                }

                $(window).on('resize', function() {
                    if (wifiChart2G != null && wifiChart2G != undefined) {
                        wifiChart2G.resize();
                    }
                });

                <?php endif; ?>
                </script>
            </div>
        </div>
        <?php $this->load->view('additional/footer');?>
        </div> <!-- #page-wrapper -->
        <?php $this->load->view('wireless/network_modal_view', array('networkList' =>$networkList, 'thresholds' => $thresholds, 'dynamicSsid' => $dynamicSsid));?>
        <?php endif; //if job isset?>
<?php if ($this->session->userdata('uid') === null) : /* Load the setup wizard and default to the uid page */ ?>
    <script> 
        $(document).on('ready',function() {
            showSetupWizard('load', 'uid');
            //$('#setupWizard').modal('show');
        });
    </script>
<?php elseif ($this->session->userdata('jobId') === null) : /* Load the setup wizard and default to the jobId page */ ?>
    <script> 
        $(document).on('ready',function() {
            showSetupWizard('load', 'job');
            //$('#setupWizard').modal('show');
        });
    </script>
<?php endif; ?>
