$(function() {

var slider = document.getElementById('timeSlider');
if(slider) {
  noUiSlider.create(slider,{
    start: 30,
    connect: true,
    range: {
        'min': 0,
        'max': 60
    }
  });
  
slider.noUiSlider.on('change', function(){

        store.set('refresh_interval', slider.noUiSlider.get());
    
});
}
        var refresh_interval = store.get('refresh_interval');
       if(refresh_interval !== null)
       {
            slider.noUiSlider.set(refresh_interval)
       }
    


    $(document).on("submit", "form", function(e) {
        e.preventDefault();
    });
    dslModemParser.loadPages();
});

var dslModemParser = {

    url:"http://192.168.7.254/cgi-bin",

    loadPages: function () {
        dslModemParser.getPage(dslModemParser.url + "/dslstatistics.ha", "broadband_connection");
        dslModemParser.getPage(dslModemParser.url + "/logs.ha", "logs");
        dslModemParser.getPage(dslModemParser.url + "/sysinfo.ha", "system_information");
        dslModemParser.getPage(dslModemParser.url + "/dhcpserver.ha", "dhcp_server");
        dslModemParser.getPage(dslModemParser.url + "/lanstatistics.ha", "ethernet");
    },
    getPage: function(urlValue, callbackValue) {
        $.ajax({
            url: "http://"+window.location.hostname+"/modem/captureModem",
            type: "GET",
            cache: false,
            data: {
                url: urlValue
            },
            success: function(data) {
                //console.log(data);
                dslModemParser.handleReturnParameters(data, callbackValue);
            }
        });
    },
    handleReturnParameters: function(data, value) {

        var elem = document.createElement("div");  // Create with DOM
        elem.setAttribute("id", value);
        elem.setAttribute("style", "display:none;");
        $("body").append(elem);

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
    broadbandConnection: function (data,value) {
        $("div#" + value).html($(data).find("div#content-sub"));

        var html1, html2 = '';
        $('div#'+ value +' form table.grid').each(function(){
            var json = $(this).tableToJSON({
                    ignoreHiddenRows: false,
                    headings:['name','line_1','line_2']
                });

        for (var prop in json) {
          if( json.hasOwnProperty( prop ) ) {           
            if(json[prop].name == "Line State"){                
                var htmlProp = json2html.transform(json[prop], broadband_connection);                
                dslModemParser.writeContent("broadband_connection_widget", htmlProp);
                
            } else {
                console.log(json[prop]);
                dslModemParser.writeContent("broadband_connection_widget_json pre", JSON.stringify(json[prop]) + "\n");
            }
          } 
        }
        }) 
    },
    logContent: function (data,value) {
        $("div#" + value).html($(data).find("pre").text());
        var logContentInner = "<pre>" + $("div#"+value).html() + "</pre>";
        dslModemParser.writeContent("log_widget", logContentInner)
    },
    systemInformation: function (data,value) {

        $("div#" + value).html($(data).find("div.section-content table"));

        var json = $("div#" + value + " table").tableToJSON({
                    ignoreHiddenRows: false,
                    headings:['name','value']
                });
        var htmlProp = json2html.transform(json, system_information); 
        dslModemParser.writeContent(value+"_widget", "<table class='table table-striped'>"+ htmlProp + "</table>");
    },dhcpServer: function (data,value) {

        $("div#" + value).html($(data).find("div#content-sub"));
 
        dslModemParser.writeContent(value+"_widget", $("div#" + value).html());
    },lanStatistics: function (data,value) {        
        $("div#" + value).html($(data).find("div#content-sub form"));
        dslModemParser.writeContent(value+"_widget", $("div#" + value).html());

        $('#'+ value +'_widget table:not(:first)').each(function() {
            
            if($(this).parent("div").not(".section-content").length == 1) {
                $(this).parent("div").not(".section-content").addClass("col-lg-6");
            } else {
                $(this).wrap("<div class='col-lg-6'></div>");
            }

            var heading = $(this).parent("div").prev("h2").text();
            $(this).parent("div").prev("h2").remove();
            $(this).parent().prepend("<h3>"+ heading +"</h3>");
            $(this).prop("width", "");

            $(this).prop("cellpadding", "");
            $(this).addClass("table table-striped table-bordered");
        });
        $('#'+ value +'_widget table:first').addClass("table table-hover").wrap("<div class='col-lg-12'></div>");

    },
    writeContent: function(elem_id, content) {
        $("#" + elem_id).append(content); 
    }
};

    var broadband_connection = {
        "tag": "div",
        "children": [{
            "tag": "div",
            "class":"col-md-3",
            "children":[{
                "tag":"div",
                "children":[{
                    "tag":"div",    
                    "id":"line_1_content",               
                    "children":[{
                        "tag":"h5",
                        "class":"m-b-md",
                        "html":"Line 1"
                    }, {
                    "tag":"h2",
                    "class": function(){
                        if(this.line_1 == "Down") {
                            return "text-danger";
                        } else {
                            return "text-navy";
                        }
                    },
                    "html":function() {
                        if(this.line_1 == "Down"){
                            return "<i class='fa fa-play fa-rotate-90 fa-fw'></i>" + this.line_1;
                        } else {
                            return "<i class='fa fa-play fa-rotate-270 fa-fw'></i>" + this.line_1;;
                        }
                    }
                    }
                    ],
                }],
            }],
        },{
            "tag": "div",
            "class":"col-md-3",
            "children":[{
                "tag":"div",
                "children":[{
                    "tag":"div",
                    "id":"line_2_content",
                    "children":[{
                        "tag":"h5",
                        "class":"m-b-md",
                        "html":"Line 2"
                    },
                    {
                    "tag":"h2",
                    "class": function() {
                        if(this.line_1 == "Down"){
                            return "text-danger";
                        } else {
                            return "text-navy";
                        }
                    },
                    "html": function() {
                        if(this.line_2 == "Down"){
                            return "<i class='fa fa-play fa-rotate-90 fa-fw'></i>" + this.line_2;
                        } else {
                            return "<i class='fa fa-play fa-rotate-270 fa-fw'></i>" + this.line_2;;
                        }
                    }
                    }
                    ],
                }],
            }],
        }]
    };


    var broadband_connection_statistics_1 = {
        "tag": "tr",
        "children": [{
            "tag": "td",
            "html":"${name}"
        },{
            "tag": "td",
            "html":"${line_1}"
        }
        ]
    };

    var broadband_connection_statistics_2 = {
        "tag": "tr",
        "children": [{
            "tag": "td",
            "html":"${name}"
        },{
            "tag": "td",
            "html":"${line_2}"
        }]
    };

    var system_information = {
        "tag": "tr",
        "children": [{
            "tag": "td",
            "html":"${name}"
        },{
            "tag": "td",
            "html":"${value}"
        }]
    };