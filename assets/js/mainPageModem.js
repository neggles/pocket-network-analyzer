$(function() {

var slider = document.getElementById('timeSlider');
if(slider) {
      noUiSlider.create(slider,{
        start: 15,
        connect: true,
        range: {
            'min': 0,
            'max': 60
        }
      });
  
    slider.noUiSlider.on('change', function(){
       
            store.set('refresh_interval', slider.noUiSlider.get() * 1000);
        
    });
}
        var refresh_interval = store.get('refresh_interval');
        
       if(refresh_interval !== null)
       {
            slider.noUiSlider.set(refresh_interval / 1000)
       } else {
            store.set('refresh_interval', 15000);
       }
    


    $(document).on("submit", "form", function(e) {
        e.preventDefault();
    });

dslModemParser.loadPages();

var refreshTimer = setInterval(dslModemParser.loadPages, getRefreshRate());

});


function getRefreshRate()
{
    var refresh_interval = store.get('refresh_interval');
       if(refresh_interval !== null)
       {
            return refresh_interval;
       } else {
            return 15000;
       }
  
}

var dslModemParser = {

    url:"http://bbtt.test.com",

    loadPages: function () {
        dslModemParser.getPage(dslModemParser.url + "/truediag.html", "broadband_connection");
        //dslModemParser.getPage(dslModemParser.url + "/logs.ha", "logs");
        //dslModemParser.getPage(dslModemParser.url + "/info.html", "system_information");
        //dslModemParser.getPage(dslModemParser.url + "/dhcpserver.ha", "dhcp_server");
        //dslModemParser.getPage(dslModemParser.url + "/lanstatistics.ha", "ethernet");
    },
    getPage: function(urlValue, callbackValue) {
        try {
        $.ajax({
            url: "http://"+window.location.hostname+"/modem/captureModem",
            type: "GET",
            cache: false,
            data: {
                url: urlValue
            },
            success: function(data) {
                //console.log(data);
                if(data.status !== false) {
                    dslModemParser.handleReturnParameters(data, callbackValue);
                }
            }
        });
        } catch (err) {

        }
    },
    handleReturnParameters: function(data, value) {


        if($("div#"+value).length === 0) {
            var elem = document.createElement("div");  // Create with DOM
            elem.setAttribute("id", value);
            elem.setAttribute("style", "display:none;");
            $("body").append(elem);
        }

        switch(value) {
            case "broadband_connection":
                dslModemParser.broadbandConnection(data, value);
                break;
            case "logs":
                dslModemParser.logContent(data, value);
                break;
            case "system_information":
                dslModemParser.systemInformation(data, value);
                break;
            case "dhcp_server":
                dslModemParser.dhcpServer(data, value);
                break;
            case "ethernet":
                dslModemParser.lanStatistics(data, value);
                break;
            default:
                console.log('no function yet');
                break;
        }
    },
    broadbandConnection: function (data, value) {

        $("table#connection_stats_table tbody").html("");
        $("#broadband_connection_widget").html("");

        var parsedHtml = $.parseHTML(data);
        var newHtml = $(data).find("body"),
        nodeNames = [];
        // find the inner blockquote
        $.each( parsedHtml, function( i, el ) {
            if(el.nodeName == "BLOCKQUOTE") {
                //console.log(parsedHtml[i]); 
                $("div#" + value).html(parsedHtml[i]);
            }
        });
        var html1, html2 = '';
        $('div#'+ value +' blockquote form table').each(function(){
            var json = $(this).tableToJSON({
                    ignoreHiddenRows: false,
                    headings:['name','value']
                });

        for (var prop in json) {
          if( json.hasOwnProperty( prop ) ) {    
          //console.log(json[prop]);       
            if(json[prop].name == "Line State:"){                
                var htmlProp = json2html.transform(json[prop], broadband_connection);                
                dslModemParser.writeContent("broadband_connection_widget", htmlProp);
            } else if(json[prop].name == "Broadband Connection:"){  
                var htmlProp = json2html.transform(json[prop], broadband_connection);                
                dslModemParser.writeContent("broadband_connection_widget", htmlProp);  
            } else if(json[prop].name == "Upstream Sync Rate:(kbps)"){  
                var htmlProp = json2html.transform(json[prop], broadband_connection);                
                dslModemParser.writeContent("broadband_connection_widget", htmlProp);     
            } else if(json[prop].name == "Downstream Sync Rate:(kbps)"){  
                var htmlProp = json2html.transform(json[prop], broadband_connection);                
                dslModemParser.writeContent("broadband_connection_widget", htmlProp);

            } else {
                if(json[prop].name !== "" && (json[prop].value !== "" || json[prop].value !== "&nbsp;")) {
                //console.log(json[prop]);
                //var htmlProp = json2html.transform(json[prop], broadband_connection_table);
                //dslModemParser.writeContent("broadband_connection_widget_table tbody", htmlProp);
            }
            }
          } 
        }
        }) 
    },
    writeContent: function(elem_id, content) {
        $("#" + elem_id).append(content); 
    }
};

    var broadband_connection = {
        "tag": "div",
        "children": [{
            "tag": "div",
            "class":"col-xs-6 col-sm-3",
            "children":[{
                "tag":"div",
                "class":"ibox",
                "children":[{
                    "tag":"div",    
                    "id":"line_1_content",  
                    "class":"ibox-content",             
                    "children":[{
                        "tag":"h5",
                        "class":"m-b-md",
                        "html":"${name}"
                    }, {
                    "tag":"h2",
                    "class": function(){
                        if(this.value == "Down" || this.value == "down") {
                            return "text-danger";
                        } else {
                            return "text-navy";
                        }
                    },
                    "html":function() {
                        if(this.value == "Down" || this.value == "down"){
                            return "<i class='fa fa-play fa-rotate-90 fa-fw'></i>" + "<span class='text-capitalize'>" + this.value + "</span>";
                        } else {
                            return "<i class='fa fa-play fa-rotate-270 fa-fw'></i>" + "<span class='text-capitalize'>" + this.value + "</span>";
                        }
                    }
                    }
                    ],
                }],
            }],
        }]
    };
