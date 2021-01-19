$(function() {

var slider = document.getElementById('timeSlider');
if(slider) {
    noUiSlider.create(slider, {
       start: 15,
       connect: true,
       range: {
           'min': 0,
           'max': 60
       }
    });
  
    slider.noUiSlider.on('change', function() {
            store.set('refresh_interval', slider.noUiSlider.get() * 1000);
        
    });
}
        var refresh_interval = store.get('refresh_interval');
       if(refresh_interval !== null)
       {
            slider.noUiSlider.set(refresh_interval / 1000)
       }

    $(document).on("submit", "form", function(e) {
        e.preventDefault();
    });


//dslModemParser.loadPages();
Pace3800DslModemParser.loadPages();
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

var Pace3800DslModemParser = {
    url:"http://192.168.1.254",
    loadPages: function () {
        //clearInterval(window.refreshTimer); 
        dslModemParser.getPage(dslModemParser.url + "/xslt?PAGE=C_1_0", "broadband_connection");
        //dslModemParser.getPage(dslModemParser.url + "/logs.ha", "logs");
        dslModemParser.getPage(dslModemParser.url + "/xslt?PAGE=C_0_0", "system_information");
        dslModemParser.getPage(dslModemParser.url + "/dhcpinfo.html", "dhcp_server");
        //dslModemParser.getPage(dslModemParser.url + "/lancfg2.html", "ethernet");
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
        var parsedHtml = $.parseHTML(data);
        $("table#connection_stats_table tbody").html("");
        $("#broadband_connection_widget").html("");
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
            // name of the attribute, first value is for the majority of the options
            // more complex tables have third column in which case
            // the value is the downstream and value2 is the upstream
            var json = $(this).tableToJSON({
                    ignoreHiddenRows: false,
                    headings:['name','value', 'value2']
                });

        for (var prop in json) {
          if( json.hasOwnProperty( prop ) ) {    
             
            if(json[prop].name == "Line State:") {                
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
                if(json[prop].name !== "" && (json[prop].value !== "" || json[prop].value !== null)) {
                    if(json[prop].value !== json[prop].name && json[prop].value2 !== undefined && !json[prop].name.startsWith("DSL Modulation")) {
                        //console.log(json[prop]);

                        $("table#connection_stats_table tbody").json2html(json[prop], broadband_connection_table.body);
                    }
            }
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
        var parsedHtml = $.parseHTML(data);

        $("div#" + value).html($(data).find("div.section-content table"))
        
        $.each( parsedHtml, function( i, el ) {
            if(el.nodeName == "BLOCKQUOTE") {
                //console.log(parsedHtml[i]); 
                $("div#" + value).html(parsedHtml[i]);
            }
        });

        var json = $("div#" + value + " blockquote table").tableToJSON({
                    ignoreHiddenRows: false,
                    headings:['name','value']
                });
        var htmlProp = json2html.transform(json, system_information); 
        dslModemParser.writeContent(value+"_widget", "<table class='table table-striped'>"+ htmlProp + "</table>");
    }, 
    dhcpServer: function (data,value) {

        var parsedHtml = $.parseHTML(data);

        $.each( parsedHtml, function( i, el ) {
            if(el.nodeName == "BLOCKQUOTE") {
                //console.log(parsedHtml[i]); 
                $("div#" + value).html(parsedHtml[i]);
            }
        });
        var json = $("div#" + value + " blockquote table").tableToJSON({
                    ignoreHiddenRows: false,
                    headings:['name','value']
                });

        
        dslModemParser.writeContent(value+"_widget", $("div#" + value).html());
    },
    lanStatistics: function (data,value) {        
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

var dslModemParser = {

    url:"http://192.168.1.254",
    loadPages: function () {
        //clearInterval(window.refreshTimer); 
        dslModemParser.getPage(dslModemParser.url + "/truediag.html", "broadband_connection");
        //dslModemParser.getPage(dslModemParser.url + "/logs.ha", "logs");
        dslModemParser.getPage(dslModemParser.url + "/info.html", "system_information");
        dslModemParser.getPage(dslModemParser.url + "/dhcpinfo.html", "dhcp_server");
        //dslModemParser.getPage(dslModemParser.url + "/lancfg2.html", "ethernet");
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
        var parsedHtml = $.parseHTML(data);
        $("table#connection_stats_table tbody").html("");
        $("#broadband_connection_widget").html("");
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
            // name of the attribute, first value is for the majority of the options
            // more complex tables have third column in which case
            // the value is the downstream and value2 is the upstream
            var json = $(this).tableToJSON({
                    ignoreHiddenRows: false,
                    headings:['name','value', 'value2']
                });

        for (var prop in json) {
          if( json.hasOwnProperty( prop ) ) {    
             
            if(json[prop].name == "Line State:") {                
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
                if(json[prop].name !== "" && (json[prop].value !== "" || json[prop].value !== null)) {
                    if(json[prop].value !== json[prop].name && json[prop].value2 !== undefined && !json[prop].name.startsWith("DSL Modulation")) {
                        //console.log(json[prop]);

                        $("table#connection_stats_table tbody").json2html(json[prop], broadband_connection_table.body);
                    }
            }
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
        var parsedHtml = $.parseHTML(data);

        $.each( parsedHtml, function( i, el ) {
            if(el.nodeName == "BLOCKQUOTE") {
                //console.log(parsedHtml[i]); 
                $("div#" + value).html(parsedHtml[i]);
            }
        });

        var json = $("div#" + value + " blockquote table").tableToJSON({
                    ignoreHiddenRows: false,
                    headings:['name','value']
                });
        var htmlProp = json2html.transform(json, system_information); 
        dslModemParser.writeContent(value+"_widget", "<table class='table table-striped'>"+ htmlProp + "</table>");
    }, 
    dhcpServer: function (data,value) {

        var parsedHtml = $.parseHTML(data);

        $.each( parsedHtml, function( i, el ) {
            if(el.nodeName == "BLOCKQUOTE") {
                //console.log(parsedHtml[i]); 
                $("div#" + value).html(parsedHtml[i]);
            }
        });
        var json = $("div#" + value + " blockquote table").tableToJSON({
                    ignoreHiddenRows: false,
                    headings:['name','value']
                });

        
        dslModemParser.writeContent(value+"_widget", $("div#" + value).html());
    },
    lanStatistics: function (data,value) {        
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
            "class":"col-sm-6",
            "children":[{
                "tag":"div",
                "children":[{
                    "tag":"div",              
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
            }]
    };

    var broadband_connection_table = {
        body : {
            "tag":"tr",
            "children":[{
                "tag":"td",
                "html":"${name}"
            },{
                "tag":"td",
                "html":"${value}"
            },{
                "tag":"td",
                "html":"${value2}"
            }]
        }
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