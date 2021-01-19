var debug = true;

var customerData = function (jobId)
{
    this.jobId = jobId;
    this.floorplan = {};
    this.locationSettings = ((store.get("job_location_settings_" + this.jobId) !== undefined && !$.isEmptyObject(store.get("job_location_settings_" + this.jobId))) ? JSON.parse(store.get("job_location_settings_" + this.jobId)) : null);
    this.rooms = [];
    //this.id = ((!$.isEmptyObject(this.locationSettings)) ? this.locationSettings.id : JSON.parse(store.get("job_location_settings_" + this.jobId)).id);
    this.id = ((!$.isEmptyObject(this.locationSettings)) ? this.locationSettings.id : this.jobId);
    this.getFloorplan = function (force)
    {
        var plan = (store.get("layoutdata_" + this.jobId) !== undefined && !$.isEmptyObject(store.get("layoutdata_" + this.jobId)) ? JSON.parse(store.get("layoutdata_" + this.jobId)) : null);

        if (plan !== null)
        {
            if (this.isFloorplanEqual(plan))
            {
                return this.floorplan;
            }
            else
            {
                this.updateFloorplan(plan);
                return this.floorplan;
            }
        }
        else
        {
            return null;
        }
    };
    this.updateScanTime = function () {

    };

    this.updateFloorplan = function (floorplan)
    {
        this.floorplan = floorplan;
        store.set("layoutdata_" + this.jobId, JSON.stringify(floorplan));
    };

    this.defaultNetwork = function ()
    {
        //return JSON.parse(store.get("job_location_settings_" + this.jobId))[default_network];
        return ((!$.isEmptyObject(this.locationSettings)) ? this.locationSettings.default_network : ((store.get("job_location_settings_" + this.jobId) !== undefined && !$.isEmptyObject(store.get("job_location_settings_" + this.jobId))) ? JSON.parse(store.get("job_location_settings_" + this.jobId)).default_network : ""));
    };

    this.defaultNetworkMac = function ()
    {
        //return JSON.parse(store.get("job_location_settings_" + this.jobId))[default_network];
        return ((!$.isEmptyObject(this.locationSettings)) ? this.locationSettings.mac : ((store.get("job_location_settings_" + this.jobId) !== undefined && !$.isEmptyObject(store.get("job_location_settings_" + this.jobId))) ? JSON.parse(store.get("job_location_settings_" + this.jobId)).mac : ""));
    };

    this.wirelessThresholds = function ()
    {
        return JSON.parse(store.get("wireless_thresholds"));
    };
    this.setLocationSettings = function (locationSettings)
    {
        this.locationSettings = locationSettings;
        store.set("job_location_settings_" + this.jobId, JSON.stringify(locationSettings));
    };
    this.getLocationSettings = function (key)
    {
        //return JSON.parse(store.get("job_location_settings_" + this.jobId))[key];
        return ((!$.isEmptyObject(this.locationSettings)) ? this.locationSettings[key] : (store.get("job_location_settings_" + this.jobId) !== undefined ? JSON.parse(store.get("job_location_settings_" + this.jobId))[key] : ""));
    };
    this.isFloorplanEqual = function (newFloorplan)
    {
        return JSON.stringify(newFloorplan) === JSON.stringify(this.floorplan.floors);
    };
    this.setRoomName = function (id, name) {

    };
    this.setDefaultNetworkSelect = function (id)
    {
        var network = JSON.parse(this.defaultNetwork());
        //$(id).append("<option>" + this.defaultNetwork() + "</option>");
        $(id).append("<option data-frequency='" + network.frequency + "' data-mac='" + network.mac + "' value='" + network.ssid + "' selected='selected'>" + network.ssid + "</option>");
        var usedNames = {};
        console.log(this.defaultNetwork());
        $(id + " > option").each(function ()
        {
            if (usedNames[this.text])
            {
                $(this).remove();
            }
            else
            {
                usedNames[this.text] = this.value;
            }
        });
    }
};

var jobId = getJobId();
var customer = new customerData(jobId);

customer.setDefaultNetworkSelect("[name=front_default_network]");

var floorplan = customer.floorplan;

handleLog(customer);

function saveFloorPlan(jobId, floorplan)
{
    customer.updateFloorplan(floorplan);
}


/**
 * saveLayout function to update the localstorage
 * and the current customer object.
 * This function will get the current floors object from the dom,
 * and compare it to the current customer object.
 * If these two values are different, it will update the current customer object
 * and send the layout to the database.
 * @return none.
 */
function saveLayout()
{
    var data = customer.floorplan;
    var floors = getFloors();
    if ($.isEmptyObject(data))
    {
        data = {}
        data.timestamp = new Date().getTime();
        data.floors = [];
    }
    else
    {
        data.updated = new Date().getTime();
    }
    if (!customer.isFloorplanEqual(floors))
    {
        data.floors = floors;
        handleLog('Floorplan has changed, updating now.');
        customer.updateFloorplan(data);
        sendLayoutToDatabase(JSON.stringify(data));
    }
    handleLog("saveLayout() function");
}

function getSessionData(dataKey)
{
    var data = store.get(dataKey);
    console.log(data);
    if (data !== null && data !== undefined)
    {
        handleLog("getSessionData() localstorage callback");
        return data;
    }
    $.ajax(
    {
        method: "get",
        url: "/job/getSessionData",
        dataType: "json",
        data:
        {
            key: dataKey
        },
        success: function (data)
        {
            handleLog("getSessionData() ajax callback");
            if (data.status === true)
            {
                setSessionData("jobId", data.msg);
                return data.msg;
            }
            else
            {
                handleLog(data.msg);
                return 0;
            }
        }
    });
    return false;
}

function setJobId(id)
{
    setSessionData("jobId", id);
}

function getJobId()
{
    return getSessionData("jobId");
}

function getDistance(frequency, level)
{
    return Math.pow(10.0, (27.55 - (20 * Math.log10(frequency)) + Math.abs(level)) / 20.0);
}

/**/
function runWirelessScan(room)
{
    if (room !== null)
    {
        var scanResults = runRoomScan(room, customer.defaultNetwork());
    }
    else
    {
        alertify.error("There was an issue getting the rooms unique id.");
    }
}

$(document).on("click", ".refreshNetworkList", function (e)
{
    e.preventDefault();
    var target = $(this).data('target');
    if (target)
    {
        wirelessNetworks(target);
    }
});


$(document).on('click', "#saveDefaultNetwork", function (e)
{
    e.preventDefault();
    var target = $(this).data("target");
    var defaultNetwork = $(target).val();
    var macAddr = $('option:selected', target).data('mac');
    var freq = $('option:selected', target).data('frequency');
    //console.log(defaultNetwork);

    $.ajax(
    {
        method: "post",
        url: "/job/updateJobLocationDefaultNetwork",
        dataType: 'json',
        data:
        {
            default_network: defaultNetwork,
            mac: macAddr,
            frequency: freq,
            job: customer.jobId,
            id: customer.id
        },
        success: function (data)
        {
            customer.setLocationSettings(data);
        }
    });
});

wirelessNetworks("[name=default_network]");
wirelessNetworks("[name=front_default_network]");

$(document).on("click", "#saveJobLocationSettings", function (e)
{
    e.preventDefault();
    $.ajax(
    {
        method: "post",
        url: "/job/updateJobLocationSettings",
        dataType: "json",
        data: $("form#jobLocationSettingsForm").serialize(),
        success: function (data)
        {
            handleLog(data);
            customer.setLocationSettings(data);
            $("#editHomeSettingsModal").modal("hide");
        }
    });
});

function handleLog(message, level)
{
    if (debug)
    {
        console.log(message);
    }
}

function handleRoomTimeStamp(room)
{
    var wirelessData = $("#" + room).data("wirelessResults");
    if (wirelessData.timestamp)
    {
        var timestamp = wirelessData.timestamp;
        var timeScanRan = new Date(timestamp);
        var currentTime = new Date();

        console.log(moment.utc(moment().diff(moment(timestamp))).format("HH:mm:ss"));

        var timeDiff = moment.utc(moment().diff(moment(timestamp))).format("HH:mm:ss");
        var scanTimeElement = $("#" + room + " span.scanTime");

        if (scanTimeElement.length !== 0)
        {
            $("#" + room + " span.scanTime").text("Time Since Last Scan: " + timeDiff);
        }
        else
        {
            $("#" + room).append("<span class='text-center font-bold scanTime'>Time Since Last Scan: " + timeDiff + "</span>");
        }

    }
}

/*
    Will parse the RSSI of a given network for a given room
    and highlight the room the appropriate color.
 */
function handleRoomSignalStrength(room)
{
    handleLog("handleRoomSignalStrength() function");
    var wirelessThresholds = customer.wirelessThresholds();

    var wirelessData = $("#" + room).data("wirelessResults");
    console.log(wirelessData);

    if (wirelessData)
    {

        var rssi = wirelessData.signal_strength;
        var cleanRssi = rssi.replace(" dBm", "");
        var strengthColor;

        switch (true)
        {
        case cleanRssi > Number(wirelessThresholds.high.value):
            strengthColor = wirelessThresholds.high.color;
            break;
        case cleanRssi > Number(wirelessThresholds.medium.value):
            strengthColor = wirelessThresholds.medium.color;
            break;
        case cleanRssi < Number(wirelessThresholds.low.value):
            strengthColor = wirelessThresholds.low.color;
            break;
        }

        var signalStrengthElement = $("#" + room + " span.signalStrength");

        if (signalStrengthElement.length !== 0)
        {
            $("#" + room + " span.signalStrength").text("RSSI: " + rssi);
        }
        else
        {
            $("#" + room).append("<span class='text-center font-bold signalStrength no-margins'>RSSI: " + rssi + "</span>");
        }

        $("#" + room).css("background-color", strengthColor);
    }
}


/**
 * run a wireless network scan for a given room.
 * @param  string room       The id for the room div currently selected
 * @param  string targetSsid The ssid being scanned for.
 * @return {[type]}            [description]
 */
function runRoomScan(room, targetSsid)
{
    console.log(customer.defaultNetwork());
    handleLog("runRoomScan() function");

    $("#" + room).append("<div class='text-center scanSpinner'><i class='fa fa-spinner fa-spin'></i></div>");
    var d = new Date();
    var n = d.getTime();

    $.ajax(
    {
        method: "post",
        url: "/network/runnetworkscanforroom",
        dataType: 'json',
        success: function (data)
        {
            var keySlot = false;
            Object.keys(data).forEach(function (key)
            {
                if (data[key].ssid.indexOf(customer.defaultNetwork()) !== -1)
                {
                    keySlot = key;
                }
            });
            if (keySlot)
            {
                //add timestamp to wireless results      
                data[keySlot]["timestamp"] = n;

                handleWirelessNetworkScanResults(room, data[keySlot]);
            }
            else
            {
                $("#" + room + " div.scanSpinner").remove();
                alertify.error("Your target network is not visible.", 0);
            }
        }
    });
}

/*
    This function give us the ability to abstract the
    room scan results from the ajax call.
 */
function handleWirelessNetworkScanResults(room, data)
{
    handleLog("In handleWirelessNetworkScanResults()");
    $("#" + room).data("wirelessResults", data);
    $("#" + room + " div.scanSpinner").remove();
    handleRoomSignalStrength(room);
    saveLayout();
}

/*
    Called from the form submission of the wireless threshold form.
    This form allows for the editing of the attributes
    for a given wireless threshold.
 */
function editWirelessThresholds(values)
{
    $.ajax(
    {
        url: "/wireless/saveWirelessThresholds",
        type: "POST",
        dataType: "json",
        data: values,
        success: function (data)
        {
            if (data.status === true)
            {
                alertify.success(data.msg);
            }
            else
            {
                alertify.error(data.msg);
            }
        }
    });
}


$(document).on("click", "#saveFloorplanLayout", function (e)
{
    e.preventDefault();
    saveLayout();
});

/*
Save the layout to the database.
*/
function sendLayoutToDatabase(layout)
{
    var location = customer.getLocationSettings("id");
    console.log(customer.jobId)
    $.ajax(
    {
        url: "/job/saveJobLocationFloorplan",
        type: "POST",
        dataType: "json",
        data:
        {
            layout: layout,
            location: location
        },
        success: function (data)
        {
            if (data.status === false)
            {
                alertify.error(data.msg);
            }
        }
    });
}

function handleWirelessResults(json)
{
    $("table#wirelessResultsModal").html("");
    $("table#wirelessResultsModal").append("<tr><td>SSID</td>" + "<td>" + json.ssid + "</td></tr>");
    $("table#wirelessResultsModal").append("<tr><td>Manufacturer</td>" + "<td>" + json.manufacturer + "</td></tr>");
    $("table#wirelessResultsModal").append("<tr><td>Signal Strength</td>" + "<td>" + json.signal_strength + "</td></tr>");
}

var maxLayoutHistory = 5;

var wifiScanHistory = 2;

function getFloors()
{
    var floors = [],
        floorId,
        quadrantId;

    $("div.house [data-type=floor]").each(function ()
    {

        var floor = {
            quadrants: []
        };
        floorId = $(this).data("floor");
        floor.floor = floorId;
        floor.name = $(this).find(".floor-title").text();
        var quadrantCounter = 0;
        $("[data-floor=" + floorId + "] [data-type=quadrant]").each(function ()
        {
            var rooms = [];
            var quadrant = {};
            quadrantId = $(this).data("quadrant");
            if (quadrantCounter < 4)
            {
                $("[data-floor=" + floorId + "] [data-quadrant=" + quadrantId + "] [data-type=room]").each(function ()
                {
                    quadrant.quadrant = quadrantId;
                    var id = $(this).attr("id");

                    if (id !== undefined)
                    {
                        var name = (($(this).data("name") !== undefined) ? $(this).data("name") : (($(this).find(".room-title").text() !== undefined) ? $(this).find(".room-title").text() : "Room"));
                        var room = {
                            id: id,
                            name: name,
                            wirelessResults: $(this).data("wirelessResults"),
                            wirelessExtender: $(this).data("wirelessExtender"),
                            gateway: $(this).data("gateway"),
                            style: $(this).attr("style"),
                            x: $(this).data("x"),
                            y: $(this).data("y")
                        };
                        rooms.push(room);
                    }
                });
            }
            quadrantCounter++;
            quadrant.rooms = rooms;
            floor.quadrants.push(quadrant);
        });
        floors.push(floor);

    });

    return floors;
}


function handleRoomIds()
{
    handleLog("handleRoomIds() function");
    $("div.house [data-room=newRoom]").each(function ()
    {
        var t = randomNumber();
        var n = "room-" + t;
        $(this).attr("id", n);
        $(this).data("name", "Room");
        $(this).removeData("room");
        $(this).removeAttr("data-room");
    });
}

function randomNumber()
{
    return Math.floor(Math.random() * (1e6 - 1 + 1) + 1);
}

function clearHouse()
{
    $(".house").empty();
    layouthistory = null;
    store.remove("layoutdata_" + customer.jobId);
    handleDynamicLayout();
}

var currentDocument = null;
var timerSave = 1000;
var stopsave = 0;
var startdrag = 0;

function verifyRooms()
{
    var jobDetail = customer.getFloorplan();
    handleLog("verifyRooms() function");

    if (jobDetail !== null && jobDetail.floors !== undefined)
    {
        $.each(jobDetail.floors, function (index, floor)
        {
            $.each(floor.quadrants, function (index, quadrant)
            {
                $.each(quadrant.rooms, function (index, room)
                {
                    if (room.wirelessResults !== undefined)
                    {
                        $("#" + room.id).data("wirelessResults", room.wirelessResults);
                        $("#" + room.id).append("<span class='room-extender'><i class='fa fa-wifi'></i></span>");
                        handleRoomSignalStrength(room.id);
                        handleRoomTimeStamp(room.id);
                    }
                    if (room.gateway !== undefined)
                    {
                        $("#" + room.id).data("gateway", true);
                        $("#" + room.id).append("<span class='room-gateway'><i class='fa fa-sitemap'></i></span>");
                    }
                });
            });
        });
    }
}

function initContainer()
{
    $(".house, .house .column").sortable(
    {
        connectWith: ".column",
        opacity: 0.35,
        handle: ".drag",
        start: function (e, t)
        {
            if (!startdrag)
            {
                stopsave++;
            }
            startdrag = 1;
        },
        stop: function (e, t)
        {
            if (stopsave > 0)
            {
                stopsave--;
            }
            startdrag = 0;
            handleRoomIds();
        },
        receive: function (event, ui)
        {
            ui.helper.first().removeAttr("style"); // undo styling set by jqueryUI
        }
    });
}

function loadFloorPlan()
{
    handleDynamicLayout();

    $("[type=color]").spectrum();

    //initContainer();
    handleContextMenu();

    $(document).on("click", "#clear", function (e)
    {
        e.preventDefault();
        clearHouse();
    });

    /* setInterval(function() {
         handleSaveLayout()
     }, timerSave)*/

    $("[data-toggle=popover]").popover(
    {
        trigger: "hover",
        container: "body",
        html: "true"
    });

    $(document).on("click", "[data-toggle=floor]", function (e)
    {
        e.preventDefault();
        var targetFloor = $(this).data("target-floor");

        $(".house [data-type=floor]").each(function ()
        {
            if ($(this).data("floor") !== targetFloor)
            {
                $(this).hide();
            }
            else
            {
                $(this).show();
            }
        });
    });
    $(document).on("click", "[data-action=remove-floor]", function (e)
    {
        e.preventDefault();
        var targetFloor = $(this).data("target");
        if (targetFloor > 1)
        {
            console.log(targetFloor);
            $(".house [data-type=floor][data-floor=" + targetFloor + "]").remove();
            saveLayout();
        }
    });
    //handleRoomDrag(); 
}

//Editor for html code inline
$(document).on("ready", function ()
{
    console.log(floorPlanLoaded);
    if (floorPlanLoaded)
    {
        loadFloorPlan();
    }

});

function writeNewLabel(id, name)
{
    handleLog("writeNewLabel() function");
    $("#" + id).data("name", name);
    $("#" + id + " .room-title").text(name);
    saveLayout();
}

/*
 Handles the creation of the context menus for the rooms
 */
function handleContextMenu()
{


    if ($("[data-type=room]").length !== 0)
    {
        //$("[data-type=room]").contextMenu();

        interact('[data-type=room]').on('tap', function (e)
        {
            e.preventDefault();
            var target = e.target;
            console.log(e);
            console.log($(target).offset())
            $(target).contextMenu(
            {
                x: e.pageX,
                y: e.pageY,
            });
        })

        $.contextMenu(
        {
            selector: "[data-type=room]",
            className: 'data-title',
            trigger: "none",
            zIndex: 100,
            callback: function (key, options)
            {
                var m = "clicked: " + key;
                handleLog(m);
            },
            items:
            {
                edit:
                {
                    name: "Rename",
                    icon: "fa-edit",
                    callback: function (key, options)
                    {
                        var id = $(this).attr("id");
                        var title = $(this).find(".room-title").text();
                        if (id !== undefined)
                        {
                            $("#editRoomModal [name=targetRoomId]").val(id);
                            $("#editRoomModal [name=roomName]").val(title);
                            $("#editRoomModal").modal("show");
                        }
                    }
                },
                scan:
                {
                    name: "Scan",
                    icon: "fa-wifi",
                    callback: function (key, options)
                    {
                        var room = $(this).attr("id");
                        runWirelessScan(room);
                    }
                },
                results:
                {
                    name: "Results",
                    icon: "fa-history",
                    callback: function (key, options)
                    {
                        var results = $(this).data("wirelessResults");
                        if (results !== undefined)
                        {
                            var formattedScanResults = handleWirelessResults(results);
                            //$("#roomWirelessResultsModal .modal-body").text(JSON.stringify(results));
                            $("#roomWirelessResultsModal").modal("show");
                        }
                        else
                        {
                            alertify.error("No results for this room currently.");
                        }
                    }
                },
                insertGateway:
                {
                    name: "Gateway",
                    icon: "fa-sitemap",
                    callback: function (key, options)
                    {
                        console.log($(this).data("gateway"));
                        if ($(this).data("gateway") == true)
                        {
                            $(this).removeData("gateway");
                            $(this).find(".room-gateway").remove();

                        }
                        else
                        {
                            $(this).append("<span class='room-gateway'><i class='fa fa-sitemap'></i></span>");
                            $(this).data("gateway", true);
                        }
                        saveLayout();
                        handleLog("Insert Gateway context option.");
                    }
                },
                insertExtender:
                {
                    name: "Wireless Extender",
                    icon: "fa-wifi",
                    callback: function (key, options)
                    {

                        var extender = $(this).data("wirelessExtender");

                        if ($(this).data("wirelessExtender") == true)
                        {
                            $(this).removeData("wirelessExtender");
                            $(this).find(".room-extender").remove();

                        }
                        else
                        {
                            $(this).data("wirelessExtender", true);
                            $(this).append("<span class='room-extender'><i class='fa fa-wifi'></i></span>");
                        }
                        saveLayout();
                        handleLog("Insert Extender context option.");
                    }
                },
                delete:
                {
                    name: "Delete",
                    icon: "fa-trash",
                    callback: function (key, options)
                    {
                        $(this).remove();
                    }
                }
            }
        });
    }
}

function dragMoveListener(event)
{
    var target = event.target,
        // keep the dragged position in the data-x/data-y attributes
        x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx,
        y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

    // translate the element
    target.style.webkitTransform =
        target.style.transform =
        'translate(' + x + 'px, ' + y + 'px)';

    // update the posiion attributes
    target.setAttribute('data-x', x);
    target.setAttribute('data-y', y);
}

function handleRoomDrag()
{

    // target elements with the "draggable" class
    interact('.room')
        .draggable(
        {
            allowFrom: "span.room-title",
            // enable inertial throwing
            inertia: false,
            // keep the element within the area of it's parent
            restrict:
            {
                restriction: "parent",
                endOnly: true,
                elementRect:
                {
                    top: 0,
                    left: 0,
                    bottom: 1,
                    right: 1
                }
            },
            // enable autoScroll
            autoScroll: true,

            // call this function on every dragmove event
            onmove: dragMoveListener,
            // call this function on every dragend event
            onend: function (event)
            {
                saveLayout();
            }
        }).resizable(
        {
            preserveAspectRatio: false,
            edges:
            {
                left: true,
                right: true,
                bottom: true,
                top: true
            }
        }).on('resizemove', function (event)
        {
            var target = event.target,
                x = (parseFloat(target.getAttribute('data-x')) || 0),
                y = (parseFloat(target.getAttribute('data-y')) || 0);

            // update the element's style
            target.style.width = event.rect.width + 'px';
            target.style.height = event.rect.height + 'px';

            // translate when resizing from top or left edges
            x += event.deltaRect.left;
            y += event.deltaRect.top;

            target.style.webkitTransform = target.style.transform =
                'translate(' + x + 'px,' + y + 'px)';

            target.setAttribute('data-x', x);
            target.setAttribute('data-y', y);
            //target.textContent = Math.round(event.rect.width) + '×' + Math.round(event.rect.height);
        });



    // this is used later in the resizing and gesture demos
    window.dragMoveListener = dragMoveListener;

}

function handleDynamicLayout()
{

    handleLog("handleDynamicLayout()");
    var floorplan = customer.getFloorplan();

    var defaultFloors = [];

    var numFloors = customer.getLocationSettings("floors");
    var i = 0;
    for (i; i < numFloors; i += 1)
    {
        var floorNumber = i + 1;
        var tempFloor = {
            floor: floorNumber,
            name: "Floor " + floorNumber
        };
        defaultFloors.push(tempFloor);
    }

    handleLog(floorplan);

    if (floorplan !== null)
    {
        if (floorplan.floors !== undefined)
        {
            handleLog("layout floors are set");
            $(".house").json2html(floorplan.floors, floorLayout.floor);

            if (floorplan.floors.length < numFloors)
            {
                var floorDifference = numFloors - floorplan.floors.length;
                var i = 0;
                var defaultFloors = [];
                for (i; i < floorDifference; i += 1)
                {
                    var floorNumber = floorplan.floors.length + i + 1;
                    var tempFloor = {
                        floor: floorNumber,
                        name: "Floor " + floorNumber
                    };
                    defaultFloors.push(tempFloor);
                }
                $(".house").json2html(defaultFloors, defaultFloorLayout);
                handleRoomIds();
                saveLayout();
            }
            verifyRooms();
        }
        else
        {
            handleLog("layout floors are not set");
            $(".house").json2html(defaultFloors, defaultFloorLayout);
            // Assign all of the new rooms unique ids
            handleRoomIds();
            verifyRooms();
        }
    }
    else
    {
        handleLog("layout floors are not set");
        $(".house").json2html(defaultFloors, defaultFloorLayout);
        // Assign all of the new rooms unique ids
        handleRoomIds();
        verifyRooms();
    }
}

var roomLayout = {
    tag: "div",
    class: "col-xs-6 column room",
    "data-type": "room",
    "data-x": "${x}",
    "data-y": "${y}",
    id: "${id}",
    style: "${style}",
    html: function ()
    {
        var innerHtmlCode = "<span class='room-title' title='" + this.name + "'>" + this.name + "</span>";
        if (this.wirelessExtender == true)
        {
            innerHtmlCode += "<span class='room-extender'><i class='fa fa-wifi'></i></span>";
        }
        if (this.gateway == true)
        {
            innerHtmlCode += "<span class='room-gateway'><i class='fa fa-sitemap'></i></span>";
        }
        return innerHtmlCode;
    }
};

var quadrantInnerLayout = {
    tag: "div",
    class: "col-xs-6 lyrow",
    children: [
    {
        tag: "div",
        class: "view",
        children: [
        {
            tag: "div",
            class: "row clearfix",
            children: [
            {
                tag: "div",
                class: "col-xs-12",
                "data-type": "quadrant",
                "data-quadrant": "${quadrant}",
                children: function ()
                {
                    return $.json2html(this.rooms, roomLayout);
                }
            }]
        }]
    }]
};

var floorLayout = {
    floor:
    {
        tag: "div",
        class: "floor",
        "data-type": "floor",
        "data-floor": "${floor}",
        style: function ()
        {
            return ((this.floor > 1) ? "display:none;" : "");
        },
        html: function ()
        {
            var label = "<span class=\"floor-title\" title='" + this.name + "''>" + this.name + "</span>";
            if (this.floor > 1)
            {
                label += "<span class='pull-right remove-floor'><a href='#' title='Delete Floor' data-action='remove-floor' data-target='" + this.floor + "' class='btn btn-danger btn-xs'><i class='fa fa-trash'></i></a></span>";
            }
            var wrap = "";
            var i;
            for (i = 0; i < this.quadrants.length; i += 1)
            {
                if (i % 2)
                {
                    wrap += $.json2html(this.quadrants[i], quadrantInnerLayout,
                    {
                        "output": "html"
                    });
                    wrap += "</div>";
                }
                else
                {
                    wrap += "<div class=\"row quadrantWrapper\">";
                    wrap += $.json2html(this.quadrants[i], quadrantInnerLayout,
                    {
                        "output": "html"
                    });
                }
            }
            return label + wrap;
        }
    }
};
/*
var defaultFloorLayout = {
    tag: "div",
    class: "floor",
    "data-floor": "${floor}",
    "data-type": "floor",
    style: function () {
        return ((this.floor > 1) ? 'display:none;' : '');
    },
    html: '<span class="floor-title" contenteditable="true">${name}</span>' +
        '<div class="row quadrantWrapper">' +
        '<div class="col-xs-6 lyrow">' +
        '<div class="view">' +
        '<div class="row clearfix">' +
        '<div class="col-xs-12" data-type="quadrant" data-quadrant="2">' +
        '<div class="col-xs-12 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="col-xs-6 lyrow">' +
        '<div class="view">' +
        '<div class="row clearfix">' +
        '<div class="col-xs-12" data-type="quadrant" data-quadrant="1">' +
        '<div class="col-xs-12 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="row quadrantWrapper">' +
        '<div class="col-xs-6 lyrow">' +
        '<div class="view">' +
        '<div class="row clearfix">' +
        '<div class="col-xs-12" data-type="quadrant" data-quadrant="3">' +
        '<div class="col-xs-12 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="col-xs-6 lyrow">' +
        '<div class="view">' +
        '<div class="row clearfix">' +
        '<div class="col-xs-12" data-type="quadrant" data-quadrant="4">' +
        '<div class="col-xs-12 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>'

};
*/

var defaultFloorLayout = {
    tag: "div",
    class: "floor",
    "data-floor": "${floor}",
    "data-type": "floor",
    style: function ()
    {
        return ((this.floor > 1) ? 'display:none;' : '');
    },
    html: '<span class="floor-title" contenteditable="true">${name}</span>' +
        '<div class="row quadrantWrapper">' +
        '<div class="col-xs-6 lyrow">' +
        '<div class="view">' +
        '<div class="row clearfix">' +
        '<div class="col-xs-12" data-type="quadrant" data-quadrant="2">' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="col-xs-6 lyrow">' +
        '<div class="view">' +
        '<div class="row clearfix">' +
        '<div class="col-xs-12" data-type="quadrant" data-quadrant="1">' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="row quadrantWrapper">' +
        '<div class="col-xs-6 lyrow">' +
        '<div class="view">' +
        '<div class="row clearfix">' +
        '<div class="col-xs-12" data-type="quadrant" data-quadrant="3">' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="col-xs-6 lyrow">' +
        '<div class="view">' +
        '<div class="row clearfix">' +
        '<div class="col-xs-12" data-type="quadrant" data-quadrant="4">' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '<div class="col-xs-6 column room" data-type="room" data-room="newRoom"><span class="room-title">Room</span></div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>'

};
