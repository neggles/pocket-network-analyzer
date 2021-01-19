
var Y_OFFSET = 100
var wifiBands5G = {
    lower: [36,40,44,48,42,56,60,64],
    middle: [100,104,108,112,116,120,124,128,132,136,140,144],
    upper: [149,153,157,161,165]
};

var listed5GNetworks = {};
/**
 * element {DOM Element} - element container for chart
 * dataArr {Array} - data series [{name, centerFreq, bandwidth, peak}]
 * options {Object} - options of echart, include: chartTitle
 */
function drawWifiDbmChart(chart, dataArr, options, band) {
  var chartOpts;
  if(band == 2) {
    if (dataArr && dataArr.length) {
      var dataArr = batchOffsetPeak(dataArr);
      var xAxisData = getXAxisData(dataArr);
      var seriesData = getInterpolatedData(dataArr, xAxisData); // Data interpolation
      chartOpts = getEchartOptions2G(seriesData, options);
    } else {
      chartOpts = getEchartOptions2G([], options);
    }
  } else if(band == 149) {
    if (dataArr && dataArr.length) {
      var dataArr = batchOffsetPeak(dataArr);
      var xAxisData = getXAxisData(dataArr);
      var seriesData = getInterpolatedData(dataArr, xAxisData); // Data interpolation
      chartOpts = getEchartOptions5Gupper(seriesData, options);
    } else {
      chartOpts = getEchartOptions5Gupper([], options);
    }    
  } else if(band == 100) {
    if (dataArr && dataArr.length) {
      var dataArr = batchOffsetPeak(dataArr);
      var xAxisData = getXAxisData(dataArr);
      var seriesData = getInterpolatedData(dataArr, xAxisData); // Data interpolation
      chartOpts = getEchartOptions5Gmiddle(seriesData, options);
    } else {
      chartOpts = getEchartOptions5Gmiddle([], options);
    }    
  } else if(band == 36) {
    if (dataArr && dataArr.length) {
      var dataArr = batchOffsetPeak(dataArr);
      var xAxisData = getXAxisData(dataArr);
      var seriesData = getInterpolatedData(dataArr, xAxisData); // Data interpolation
      chartOpts = getEchartOptions5Glower(seriesData, options);
    } else {
      chartOpts = getEchartOptions5Glower([], options);
    }    
  } else if(band == 5) {
    if (dataArr && dataArr.length) {
      var dataArr = batchOffsetPeak(dataArr);
      var xAxisData = getXAxisData(dataArr);
      var seriesData = getInterpolatedData(dataArr, xAxisData); // Data interpolation
      chartOpts = getEchartOptions5Gall(seriesData, options);
    } else {
      chartOpts = getEchartOptions5Gall([], options);
    }    
  }
  chart.setOption(chartOpts, true);
}


function batchOffsetPeak(dataArr) {
  return dataArr.map(function(data) {
    data.peak = Y_OFFSET + data.peak
    return data
  })
}

function getFreqRange(dataArr) {
  if(dataArr[0]['centerChannel'] !== dataArr[0]['primaryChannel']) {
    var maxFreq = dataArr[0]['centerChannel'] + dataArr[0]['bandwidth']
    var minFreq = dataArr[0]['centerChannel'] - dataArr[0]['bandwidth']
  } else {
    var maxFreq = dataArr[0]['primaryChannel'] + dataArr[0]['bandwidth']
    var minFreq = dataArr[0]['primaryChannel'] - dataArr[0]['bandwidth']
  }
  dataArr.forEach(function(data) {
    var f = data.primaryChannel
    var b = data.bandwidth
    if (maxFreq < f + b) {
      maxFreq = f + b
    }
    if (minFreq > f - b) {
      minFreq = f - b
    }
  })
  return [minFreq, maxFreq]
}

function getXAxisData(dataArr) {
  var freqRange = getFreqRange(dataArr)
  var step = 0.1
  var x = freqRange[0]
  var xAxisData = []
  while(x <= freqRange[1]) {
    xAxisData.push(x)
    x += step
  }
  return xAxisData
}

function getInterpolatedData(dataArr, xAxisData) {
  var colorMap = {}
  var colorList = COLOR_LIST.slice(0)

  var series = dataArr.map(function(data) {
    var channel = ((data.primaryChannel == data.centerChannel) ? data.primaryChannel : data.centerChannel);
    var yData = getYAxisData(xAxisData, data.bandwidth, channel, data.peak)
    if (colorList.length === 0) {
      colorList = COLOR_LIST.slice(0)
    }
    if (!colorMap[data.name]) {
      colorMap[data.name] = colorList.pop()
    }

    return {
      name: data.name,
      color: colorMap[data.name],
      centerChannel: data.centerChannel,
      primaryChannel: data.primaryChannel,
      peak: data.peak,
      bandwidth: data.bandwidth,
      data: yData
    }
  })

  return series
}

function getYAxisData(xAxisData, B, f0, p) {
  var a = -1 * p / (B * B)
  var b = 2 * p * f0 / (B * B)
  var c = p * (1 - ((f0 * f0) / (B * B)))
  return xAxisData.map(function(x) {
    var y = (a * x * x + b * x + c).toFixed(2)
    y = y >= 0 ? y : null
    return [x, y]
  })
}

function getSeriesOptions(seriesData) {
  return seriesData.map(function(item, index) {
    return {
      name: item.name + '#' + index, // series of name the same，markLine only one is displayed
      type: 'line',
      data: item.data,
      symbol: 'none',
      markLine: {
        silent: true,
        animation: false,
        label: {
          normal: {
            formatter: item.name
          }
        },
        data: [[{
          coord: [((item.primaryChannel == item.centerChannel) ? item.primaryChannel : item.centerChannel), item.peak + 3],
          symbol: 'none'
        }, {
          coord: [((item.primaryChannel == item.centerChannel) ? item.primaryChannel : item.centerChannel), item.peak + 3],
          symbol: 'none'
        }]]
      },
      itemStyle: {
        normal: {
          color: item.color
        }
      },
      markPoint: {
        symbol: 'circle',
        symbolSize: [8, 8],
        label: {
          normal: {
            show: true,
            formatter: ' ',
          }
        },
        data: [
          {type: 'max', value: ''}
        ]
      },
      areaStyle: {
        normal: {
          color: item.color,
          opacity: 0.3
        }
      },
      smooth: true
    }
  })
}

function getEchartOptions5Gmiddle(seriesData, options) {
  return {
      title: {
        textStyle: {
          color: '#000'
        },
        text: options.chartTitle
      },
      backgroundColor: '#fff',
      toolbox: {
        show: true,
        feature: {
          dataZoom: {
            yAxisIndex: 'none'
          }
        },
        iconStyle: {
          normal: {
            borderColor: '#000'
          }
        }
      },
      tooltip: {
        show: true,
        formatter: getTip
      },
      xAxis: {
        name: 'Wireless Channel',
        nameLocation: 'middle',
        nameGap: 25,
        interval: 4,
        axisLine: {
          onZero: false,
          lineStyle: {
            color: '#000'
          }
        },
        axisLabel: {
          formatter: xLabelFormatter5Gmiddle
        },
        min: 145,
        max: 169,
        splitLine: {
          show: false
        }
      },
      yAxis: {
        name: 'Power Level [dBm]',
        nameLocation: 'middle',
        nameGap: 40,
        axisLabel: {
          formatter: yLabelFormatter
        },
        axisLine: {
          onZero: false,
          lineStyle: {
            color: '#000'
          }
        },
        boundaryGap: [0, '5%'],
      },
      series: getSeriesOptions(seriesData)
  }
}

function getEchartOptions5Gupper(seriesData, options) {
  return {
      title: {
        textStyle: {
          color: '#000'
        },
        text: options.chartTitle
      },
      backgroundColor: '#fff',
      toolbox: {
        show: true,
        feature: {
          dataZoom: {
            yAxisIndex: 'none'
          }
        },
        iconStyle: {
          normal: {
            borderColor: '#000'
          }
        }
      },
      tooltip: {
        show: true,
        formatter: getTip
      },
      xAxis: {
        name: 'Wireless Channel',
        nameLocation: 'middle',
        nameGap: 25,
        interval: 4,
        axisLine: {
          onZero: false,
          lineStyle: {
            color: '#000'
          }
        },
        axisLabel: {
          formatter: xLabelFormatter5Gupper
        },
        min: 145,
        max: 169,
        splitLine: {
          show: false
        }
      },
      yAxis: {
        name: 'Power Level [dBm]',
        nameLocation: 'middle',
        nameGap: 40,
        axisLabel: {
          formatter: yLabelFormatter
        },
        axisLine: {
          onZero: false,
          lineStyle: {
            color: '#000'
          }
        },
        boundaryGap: [0, '5%'],
      },
      series: getSeriesOptions(seriesData)
  }
}


function getEchartOptions5Gall(seriesData, options) {
  return {
      title: {
        textStyle: {
          color: '#000'
        },
        text: options.chartTitle
      },
      backgroundColor: '#fff',
      toolbox: {
        show: true,
        feature: {
          dataZoom: {
            yAxisIndex: 'none'
          }
        },
        iconStyle: {
          normal: {
            borderColor: '#000'
          }
        }
      },
      tooltip: {
        show: true,
        formatter: getTip
      },
      xAxis: {
        name: 'Wireless Channel',
        nameLocation: 'middle',
        nameGap: 25,
        interval: 4,
        axisLine: {
          onZero: false,
          lineStyle: {
            color: '#000'
          }
        },
        axisLabel: {
          formatter: xLabelFormatter5Gall
        },
        min: 32,
        max: 169,
        splitLine: {
          show: false
        }
      },
      yAxis: {
        name: 'Power Level [dBm]',
        nameLocation: 'middle',
        nameGap: 40,
        axisLabel: {
          formatter: yLabelFormatter
        },
        axisLine: {
          onZero: false,
          lineStyle: {
            color: '#000'
          }
        },
        boundaryGap: [0, '5%'],
      },
      series: getSeriesOptions(seriesData)
  }
}

function getEchartOptions2G(seriesData, options) {
  return {
      title: {
        textStyle: {
          color: '#000'
        },
        text: options.chartTitle
      },
      backgroundColor: '#fff',
      toolbox: {
        show: true,
        feature: {
          dataZoom: {
            yAxisIndex: 'none'
          }
        },
        iconStyle: {
          normal: {
            borderColor: '#000'
          }
        }
      },
      tooltip: {
        show: true,
        formatter: getTip
      },
      xAxis: {
        name: 'Wireless Channel',
        nameLocation: 'middle',
        nameGap: 25,
        interval: 1,
        axisLine: {
          onZero: false,
          lineStyle: {
            color: '#000'
          }
        },
        axisLabel: {
          formatter: xLabelFormatter2G
        },
        min: -1,
        max: 16,
        splitLine: {
          show: false
        }
      },
      yAxis: {
        name: 'Power Level [dBm]',
        nameLocation: 'middle',
        nameGap: 40,
        axisLabel: {
          formatter: yLabelFormatter
        },
        axisLine: {
          onZero: false,
          lineStyle: {
            color: '#000'
          }
        },
        boundaryGap: [0, '5%'],
      },
      series: getSeriesOptions(seriesData)
  }
}

function xLabelFormatter2G(value) {
  return (value >= 1 && value <= 11) ? value : ''
}


function xLabelFormatter5Gall(value) {
  return (value >= 36 && value <= 165) ? value : ''
}

function xLabelFormatter5Gupper(value) {
  return (value >= 149 && value <= 165) ? value : ''
}

function xLabelFormatter5Gmiddle(value) {

  return (value >= 100 && value <= 144) ? value : ''
}

function xLabelFormatter5Glower(value) {
  return (value >= 36 && value <= 64) ? value : ''
}

function yLabelFormatter(value) {
  return value - Y_OFFSET
}

function getTip(params) {
  /*
   @TODO - pass the channel information to 
   draw the tool tip with that information
   */
  var name = params.seriesName;
  name = name.replace(/#\d+$/g, '');

  return 'SSID: ' + name + '<br\>' + 'Power： <span style="color:">' + (params.value - Y_OFFSET) + 'dBm</span><br\>' +
    'Channel: ' + Math.round(params.data.coord[0]);
}

var COLOR_LIST = [
  "#c4ccd3", "#546570", "#6e7074", "#bda29a", "#ca8622",
  "#749f83", "#91c7ae", "#d48265", "#61a0a8", "#2f4554",
  "#c23531", "#30e0e0", "#b8860b", "#3cb371", "#ff00ff",
  "#6b8e23", "#ffd700", "#00fa9a", "#7b68ee", "#ff6347",
  "#1e90ff", "#40e0d0", "#ffa500", "#cd5c5c", "#ba55d3",
  "#ff69b4", "#6495ed", "#32cd32", "#da70d6", "#87cefa",
  "#ff7f50"
]
