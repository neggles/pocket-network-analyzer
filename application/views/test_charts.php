<style>
      #myNewNetworkChart {
        width: 100%;
        height: 500px;
      }
</style>

    <script src="<?php echo $this->config->item('plugins_directory');?>randomcolor/randomColor.js"></script>
    <script src="<?php echo base_url();?>assets/js/plugins/wifi/dbmChart.js"></script>
    <script src="<?php echo base_url();?>assets/js/plugins/wifi/charts.js"></script>
    <div id="page-wrapper" class="gray-bg">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="ibox-content">                
                <?php $networkList = json_decode($this->wireless->getRecentResults($jobId));
                //echo json_encode($networkList, JSON_PRETTY_PRINT);?>
                <div class="row">
                    <div class="col-sm-12">
                        <div id="myNewNetworkChart"></div>
                       
                    </div>
                </div>
            </div>
        </div>
        </div>

<script>
var wifiChart = undefined
$(document).ready(function(){
 entry(); 
})

function entry() {
  var data = {
    title: 'Wireless Networks',
    wifiList: [
       {
         name: 'Home-Bailey',
         centerFreq: 4,
         bandwidth: 2,
         peak: -50
       },
       {
         name: 'Home-Bailey2',
         centerFreq: 2,
         bandwidth: 2,
         peak: -80
       },
       {
         name: 'Home-Bailey1',
         centerFreq: 1,
         bandwidth: 2,
         peak: -60
       },
    ]
  }
  console.log(data);
  setChartData(data)
}

function setChartData(data) {
  if (!wifiChart) {
    wifiChart = echarts.init(document.getElementById('myNewNetworkChart'))
  }

  var options = {
    chartTitle: data.title
  }

  drawWifiDbmChart(wifiChart, data.wifiList, options)
}

                
</script>
                