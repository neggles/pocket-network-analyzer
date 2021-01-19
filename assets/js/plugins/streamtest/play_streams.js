// ArrayBuffer slice polyfill
// for browsers without it like Firefox 10/11 and old Android
/*
if (!ArrayBuffer.prototype.slice)
    ArrayBuffer.prototype.slice = function(start, end) {
        var that = new Uint8Array(this);
        if (end == undefined) end = that.length;
        var result = new ArrayBuffer(end - start);
        var resultArray = new Uint8Array(result);
        for (var i = 0; i < resultArray.length; i++)
            resultArray[i] = that[i + start];
        return result;
    };
*/

//window['performance'] = window.performance || {};
//
//performance.now = (function() {
//                    return performance.now       ||
//                    performance.mozNow    ||
//                    performance.msNow     ||
//                    performance.oNow      ||
//                    performance.webkitNow ||            
//                    Date.now  /*none found - fallback to browser default */
//})();

/*
window['swizzledSetTimeout'] = function (fn, ms) {
    if (arguments.length === 2) {
        //console.log("Bypassing swizzledSetTimeout");
        return window.originalSetTimeout(fn, ms);
    } else {
        var args = [];
        for (i = 2; i < arguments.length; i++) {
            args.push(arguments[i])
        };
        var retval = window.originalSetTimeout(function () {
            fn.apply(null, args);
        }, ms);
        return retval;
    }
}

function installSwizzledTimeout(cb) {
    var args = [];
    for (i = 1; i < arguments.length; i++) {
        args.push(arguments[i])
    };
    setTimeout(function (arg) {
        if (arguments.length == 0) {

            function doInstall() {
                window['originalSetTimeout'] = window.setTimeout;
                window['setTimeout'] = function setTimeout() {
                    return window.swizzledSetTimeout.apply(null, arguments);
                };
                if (cb) {
                    cb.apply(null, args);
                };
            }

            if (window.setTimeout.toString().indexOf("swizzledSetTimeout") < 0) {
                doInstall();
            }
        } else {
            if (cb) {
                cb.apply(null, args);
            };
        }
    }, 0, 1, 2, 3, 4);
}
installSwizzledTimeout();
*/

var objs = new Array();
var req2o = new Array();
var xmlreqs = new Array();
var memory_limit = 15000000;

var glob = new Object();

function eth_bc(doing) {
    return glob.byteCount[doing];
}

function eth_reset(doing) {
    if (typeof glob.eth == 'undefined') {
        glob.eth = new Array();
        glob.eth_list = new Array();
        glob.eth_trail = new Array();
        glob.eth_smooth = new Array();
        glob.byteCount = new Array();
        glob.old_byteCount = new Array();
        glob.old_byteCount_stamp = new Array();
        glob.byteCount_tmp = new Array();
    }

    glob.eth_list[doing] = new Array();
    glob.byteCount[doing] = 0;
    glob.old_byteCount[doing] = 0;
    glob.old_byteCount_stamp[doing] = new Date();; 
    glob.byteCount_tmp[doing] = 0;
    if (doing == 'uploading') {
        glob.upload_instant_mbits_smooth = 0;
    } 
}

function eth_delta(doing, delta, tentative) {
    glob.total_bytes += delta;

    if (doing == 'in') {
        glob.total_bytes_down += delta;
    } else {
        glob.total_bytes_up += delta;
    }
    if (!glob.byteCount[doing]) {
        glob.testms_up_data = new Date();
    }
    if (tentative) {
        glob.byteCount_tmp[doing] += tentative;
    } else {
        glob.byteCount[doing] += delta;
        glob.byteCount_tmp[doing] = 0;
    }
}

eth_reset('in');
eth_reset('out');

// this function could be called more often than the part that smooths the series
function eth_mark(doing, t) {
    var b   = glob.byteCount[doing] + glob.byteCount_tmp[doing];
    var delta = b - glob.old_byteCount[doing];
    var ms  = (t - glob.old_byteCount_stamp[doing]);

    if (ms > 500) {
        var mbits = ((ms > 0 && delta > 0) ? ((1000.0 / ms) * delta * 8.0) / 1000000.0 : 0);
        if (isNaN(mbits)) {
            mbits = 0;
        }
        {
            glob.eth[doing] = mbits;
            glob.eth_list[doing].push(mbits);
            //glob.eth_smooth[doing] = glob.eth_smooth[doing]*0.7 + glob.eth[doing]*0.30;
            var a = glob.eth_list[doing].slice(-5);
            var str = "";
            for(var j=0; j<5; j++) {
                str += a[j]+" ";
            }
            glob.eth_trail[doing] = avg(a);
            log("total mbits = "+glob.eth_trail[doing]);

        }

        if (doing == 'uploading') {
            glob.upload_instant_mbits = glob.eth[doing];
            glob.upload_instant_mbits_smooth =
                     glob.upload_instant_mbits_smooth*0.70 + glob.upload_instant_mbits*0.30;
        } 
    } 
    glob.old_byteCount[doing] = glob.byteCount[doing] + glob.byteCount_tmp[doing];
    glob.old_byteCount_stamp[doing] = t; 
}

function avg(values) {
    var tot=0.0;
    for(x=0; x<values.length; x++) {
        tot += values[x];
    }
    return tot/values.length;
}
function median(values) {
    values.sort( function(a,b) {return a - b;} );
    var half = Math.floor(values.length/2);
    if(values.length % 2)
        return values[half];
    else
        return (values[half-1] + values[half]) / 2.0;
}


function grsh(req, proc) {
    return function() {
        if (req.readyState == 3) {
            eth_delta("in", 0, 1450);
            var o = req2o[req];
            o.guesswork += 1450;
            if (quota_mb > 1000) {
                if (o.getpost == 'get') {
                    myAbort(o.req);
                } else {
                    o.req.kill();
                }
            }
            return;
        } else if (req.readyState == 1) {
            // state OPENED
            proc(1, req);
        } else if (req.readyState == 2) {
            // state HEADERS RECD
            proc(2, req);
        } else if (req.readyState == 4) {

            // state DONE
            if (req.status == 200 || req.status == 206 || req.status == 204 || req.status == 1223) {
                log("FINISHED");
                proc(4, req);
            } else {
                // in the case where a file is not found, a CORS error may not give a proper status code
                if (req.status == 404 || req.status == 401) {
                    disaster("Error: tool temporarily disabled (" + req.status + ") error:10");
                } else {
                    if (req.status !== 0) {
                        log("- Error: a payload file did not completely transfer code=" + (req.status) + "");
                    } else {
                        try {
                            log("- got a status 0 on readystate 4 "+req.statusText);
                        } catch(e) {
                            log("- got a status 0 on readystate 4 no statusText");
                        }
                    }
                }
                proc(99, req);
            }
        }
    }
}
function disaster(s) {
    //self.console.log(s);
    self.postMessage(["log", s]);
}
function log(s) {
    //self.console.log(s);
    self.postMessage(["log", s]);
}

function XHR() {
    var xmlreq = false;

    if (self.XMLHttpRequest) {
        xmlreq = new XMLHttpRequest();

        if ("withCredentials" in xmlreq) {
            // all ok
        } else if (typeof XDomainRequest != "undefined") {
            xmlreq = new XDomainRequest();
        }

        //if (typ && xmlreq.overrideMimeType) {
        //    xmlreq.overrideMimeType(typ);
        //}
    } else if (self.ActiveXObject) {
        try {
            xmlreq = new ActiveXObject("MSXML2.XMLHTTP.3.0");
        } catch (e1) {
            try {
                xmlreq = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e2) {
                log("This browser does not appear to support async javascript (Ajax)");
                return null;
            }
        }
    } else {
        return null;
    }
    xmlreqs.push(xmlreq);
    return xmlreq;
}

function myAbort(req) {
    if (req) {
        try {
            req.onreadystatechange = function() {};
        } catch (e) {}
        try {
            req.abort();
        } catch(e) {}
    }
}

function rs_getpost() {
    log("rs_getpost()");
}

function nice_mbits(ms, size) {
    var mbits = ((ms > 0 && size > 0) ? ((1000.0 / ms) * size * 8.0) / 1000000.0 : 0);
    if (mbits && (size / mbits < 1000)) {
        // for example 1m/1000 is 10000 which is still ok
        // log("-impossible megabits size=" + size + " mbits=" + mbits);
        log('- nice_mbits error ' + ms + ' size=' + size);
        return 0;
    }
    return mbits;
}

function hz1(o) {
    if (o.getpost == 'get') {
        var ts = (new Date()).getTime();
        var ms = ts - o.hz1_ms;
        var mbits = nice_mbits(ms, o.progress_loaded - o.hz1_progress_loaded);
        var lobuffer = Math.round(o.speed_mbits*1000000/8 * 3);
        var hibuffer = Math.round(o.speed_mbits*1000000/8 * 10);

        if (!o.stopped && !o.errored) {
            o.mbits = mbits;
            o.hz1_ms = ts;
            o.hz1_progress_loaded = o.progress_loaded;
            log("mbits "+o.handle+"="+mbits+ " ms="+ms+" "+o.buffer);    

            if (o.buffer > 0) {
                self.postMessage(["status", o.handle, 'good']);
                var excess = Math.round((mbits - o.speed_mbits*0.95)*1000000/8);
                o.buffer += excess;
                if (o.buffer > hibuffer) {
                    o.buffer = hibuffer;
                }
            } else if (o.buffer < lobuffer) {
                var excess = Math.round((mbits)*1000000/8);
                o.buffer += excess;
                self.postMessage(["status", o.handle, 'buffering']);
                if (o.buffering_count++ > 10) {
                    log("buffering too long");
                    myAbort(o.req);
                    o.errored = true;
                    self.postMessage(["status", o.handle, 'error', 'stream too slow - would be too much buffering']);
                }
            }
        }
    } else {
         if (o.req.lagging) {
            self.postMessage(["status", o.handle, 'buffering']);
         } else {
            self.postMessage(["status", o.handle, 'good']);
         }
    }
}

var timer1 = setInterval(all_hz1, 1000);
var quota_rolling = 0;
var quota_mb = 0;

function all_hz1() {
    eth_mark('in', (new Date()).getTime());
    eth_mark('out', (new Date()).getTime());
    var flip_out = glob.eth_trail['in'] * 0.025;
    var flip_in = glob.eth_trail['out'] * 0.025;

    self.postMessage(["mbits", glob.eth_trail['in']+flip_in, glob.eth_trail['out']+flip_out]);

    for (var key in objs) {
        var value = objs[key];
        if (value) {
            hz1(value);
        }
    }
    if ((quota_rolling++ % 10) == 0) {
        var more = eth_bc('in') + eth_bc('out') - quota_mb;
        log("more = "+more);
        if (more > (50 * 1024 * 1024)) {
            quota(Math.round(more/(1024*1024)));
            quota_mb += more;
        }
    }
}

function quota(mb) {
    var req = XHR(undefined);

    req.open("GET", "/speedtest?typ=q&mb=" + mb +"&t=" + (new Date()).getTime());
    req.timeout = 1000
    req.onloadend = function() {
        log("quota call "+req.status);
    };
    setTimeout(function() {
        req.send(null);
    }, 10);
}

function progress(e, o) {
    var delta = (e.loaded - o.progress_loaded);
    if (delta > 0) {
        eth_delta("in", delta, 0);
    } else {
        return;
    }
    o.progress_loaded = e.loaded;
}

function cachebuster() {
    return "?"+Math.random();
}

function start(o) {
    if (o.stopped || o.killed || o.errored)
        return;

    var s;

    if (o.getpost == 'get') {
        o.req.open(o.getpost, s=o.url+cachebuster(), true);
        log("open "+s);
        o.req.onreadystatechange = grsh(o.req, rs_getpost);
        o.req.onerror = function(e) {
            o.errored = true;
            self.postMessage(["status", o.handle, 'errored']);
            log("onerror! -"+e);
        };
        o.req.onload = function() {
            // restart; 
            if (!o.errored && !o.stopped) {
                log("onloadend - restart");
                setTimeout(start, 100, o);
            }
        };

        var offset = Math.round(Math.random()*10000);
        var offset_to = offset+memory_limit;;
        o.req.setRequestHeader("Range", "bytes="+offset+"-"+offset_to);

        var pfn = function(o) {
            return function(e) {
                progress(e, o);
            }
        }
        if (typeof o.req.addEventListener == 'function') {
            o.req.addEventListener("progress", pfn(o), false);
        } else {
            o.req.onprogress = pfn(o);
        }
        o.progress_loaded = 0;
        if (typeof o.buffering_count == 'undefined') {
            o.buffering_count = 0;
        }
        o.hz1_ms = (new Date()).getTime();
        o.hz1_progress_loaded = 0;
        o.guesswork = 0;
        if (typeof o.buffer == 'undefined') {
            o.buffer = 0;
        }
        o.req.responseType = 'blob';
        o.req.send();
        log("start "+o.url);
    } else {
        o.req.start();
        log("start ws");
    }
    o.ts = (new Date()).getTime();
}

var closest_ms = 9999;
var closest_server = "72.52.179.213";

self.addEventListener('message', 

    function(e) {
        switch(e.data[0]) {


            case 'new':
                // Create a new stream to unique url e.data[1]
                o = new Object();
                o.closest = closest_server;
                o.stopped = true;
                o.killed = false;
                o.errored = false;
                o.getpost = e.data[1];
                o.speed_mbits = Number(e.data[2]);
                log("speed is "+o.speed_mbits+" "+(typeof o.speed_mbits));

                if (o.getpost == 'get') {
                    o.req = XHR(undefined);
                    req2o[o.req] = o;
                    o.speed = Math.round(e.data[2] * 1000000 / 8 / 1024 * 1.05);    // 5% more
                    o.url = "https://"+o.closest+"/front"+o.speed+"k/k"
                } else {
                    o.req = new Ws(o.speed_mbits);
                }
                o.handle = e.data[3];
                objs[o.handle] = o;
                self.postMessage(["status", o.handle, 'buffering']);
                break;

            case 'unpause':
            case 'start':
                o = objs[e.data[1]];
                o.stopped = false;
                start(o);
                self.postMessage(["status", o.handle, 'buffering']);
                break;

            case 'pause':
            case 'stop':
                // Stop streaming from url e.data[1]
                o = objs[e.data[1]];
                o.stopped = true;
                if (o.getpost == 'get') {
                    myAbort(o.req);
                } else {
                    o.stop();
                }
                log("pause "+o.url);
                self.postMessage(["status", o.handle, 'paused']);
                break;

            case 'kill':
                // Forget about a URL e.data[1]
                o = objs[e.data[1]];
                o.killed = true;
                if (o.getpost == 'get') {
                    myAbort(o.req);
                } else {
                    o.req.kill();
                }
                objs[e.data[1]] = null;
                log("kill "+o.url+" req="+o.req);
                break;
        };
    }, false);


function blob_slice(blob, start, end) {
    try {
        if (blob.webkitSlice) {
            return blob.webkitSlice(start, end);
        } else if (blob.mozSlice) {
            return blob.mozSlice(start, end);
        } else {
            return blob.slice(start, end);
        }
    } catch (e) {
        // no blob.slice method eg NX Front
        return undefined;
    }
}

find_closest();
// Request a list of servers, it is a json array of ip,addr
function find_closest() {
    var req = XHR(undefined);
    req.open("GET", "/speedtest?typ=sl&t="+(new Date()).getTime());
    req.onload = function() {
        var q = JSON.parse(req.responseText);
        for(var x=0; x<q.length; x++) {
            setTimeout(launch_ping, 100+x*300, q[x]);
            setTimeout(launch_ping, 400+x*300, q[x]);
        }
    };
    setTimeout(function() {
        req.send(null);
    }, 10);
}

function launch_ping(o) {
    var req = XHR(undefined);

    o.ts = (new Date()).getTime();
    req.open("GET", "https://"+o.dns+"/front/0k?"+(new Date()).getTime());
    req.onload = function() {
        o.ms = (new Date()).getTime() - o.ts;
        if (o.ms < closest_ms) {
            closest_ms = o.ms;
            closest_server = o.dns;
            self.postMessage(["closest", o.addr]);
        }
    };
    req.send(null);
}

function Ws(megabits) {
    this.megabits = megabits;
    log("new websocket "+megabits);
    this.ws = new WebSocket("ws://72.52.179.213:8888/upload");
    this.on = false;
    this.ws.onerror = function() { log("ws error"); };
    var that = this;
    this.ws.onmessage = function(m) {
        return function(that, m) {
            var res = m.data.split(" ");
            var remote_mbits = Number(res[0]);
            that.mbits_smooth = remote_mbits * 0.4 + that.mbits_smooth * 0.6;
            that.mbits_instant = remote_mbits;
            log("mbits = "+remote_mbits+" smooth="+that.mbits_smooth);
        }(that,m);
    }
    this.ws.onopen = function() {
         return function(that) {
            log("web socket open ok "+that.megabits);
            var rolling = 0;
            var sz = Math.round(that.megabits * 1000000 / 8 / 20);
            log("sz = "+sz);
            var hz = 20;

            that.b = createBlob(sz);
            that.bytes = 0;
            that.lagging = 0;
            that.start = new Date();
            that.mbits_smooth = that.megabits;
            log("pump "+that.megabits+" sz="+sz+" hz="+hz);

            that.t = 
                setInterval( function() {
                    if (that.on) {
                        that.ws.send(that.b);
                        that.bytes += that.b.size;
                        eth_delta("out", that.b.size, 0);

                        if ((rolling++ % 20) == 0) {
                            if (that.mbits_smooth < that.megabits*0.90) {
                                log("lagging incr");
                                that.lagging++;
                            } else {
                                that.lagging=0;
                            }
                        }
                    }
                }, 1000/hz);
        }(that);
    }
}

Ws.prototype.stop = function() {
    this.on = false;
}
Ws.prototype.start = function() {
    this.mbits_smooth = this.mbits_instant = 0;
    this.on = true;
    log("start pumping "+this.on);
}
Ws.prototype.kill = function() {
    this.ws.close();
    clearTimeout(this.t);
}

// 20hz 
// Should check remote if it falls too far behind, halt..
Ws.prototype.pump = function(megabits) {
}

function createBlob(sz, b) {
    var ar = ['gSnebMOCUtEOdqTk1 EOC6zZtEYczD2Pq3wQyT A6uur64cE2vFYH4N47bb 65V1AIeMsgqZ6gt8HMEh WIgO6jSHgTj9gDuo9kFs wDFsZzoHfIwopfd2UAPi jECiffJ56ewYbR5kMfIt bbJyroVDBIwszY04NNCo Rr3lqIlT6UgiYVpKvY8B GEP8orBigqo7trcKHOwC fowG5edVxUQf0LIjBXDW LslI9H7K6mKAk2YK6zUr od6F1JUo7Y1mql1UAb3y jBJvHRsRozN1GGAcRHAK oZdmSAa33kyMIjywpJcm rW0Ades9NNpEJksyFe6T O7Yt8Dt9DQATzhTcEwAx zGPwBa9woHKxoWUIx8fx brmTnLshe7EdCVJ9BU68 Tgo3hmEh6dCK1vywOBvD KhVtGaaUak0EJnss3dEW ji3yo3vlidefhJrNIe3V QgMvVvpy6yCvp6KA8Dq5 jp5GBDOxcIY7oBoEwTab QvF8lGvfwZIEjHGtKwz0 icJJ1TRwj93JnTUXBTVC dQ79Y0ASJ9x7JhYOLl6n 2f9HID4VuSC3lxRhGsWV 3vOura59XWunNjYkBDH4 b32rxuXcZQTVhXGad6XN H8RYACcD2qatB8l7EseJ zcy58Fj6WWOvqt7qfbHg vY7Nts7iP4sIRQeIkAcx x47qubFwh9RPKNCL2CgS VWXxk1CCxS4OXOGNlWZs kFSUGm8duYX7YYMwwwpP G51iCcfjRwjCr96Vk1KN KfvaApWF2k7BHxmrldpm uwk54j1qBkJKdv1EYJQm L7kdPndnBG9Ui0grzFp1 eDKLiNLcbiwJdj2IVl4J OihLDKzKo9ZL1jrPdIgH wG9CHzJHjAi1ySgjFl2P frj9ze4bRDiFQBbRCsxu hXDOsTPwdLuhGKIAlipv sLWeozNMbhUuLaXU2q2U ZzxG3fDDSP3nuJY8YnJm RDR2y2dLaPPPfDPJmkRA yXrPmClUjD77ecIjZl9j Zz5nuOFgcOKJI9LwaRe8 QIZNxCJDxS89nBhViLRF MswfMhP9F8hAEISsh0Ix 1tSTE6RdPGNQzyhlgYyd m2qoxRnrOvDUDOrDHNZJ HG9r8uYjPfYKPRJlAV7T mhEQhbs94GOxDHDVpBIS LJm5o5x6kY1vekpyZCHc l2m5Ekk9dNLzrOsyTlLm dHlcBieVbVYQiA1vXu8X ZoudtEMQwbNuVWDgWAP3 UYoPqAllFxnoc3WYXNgB 8SRfTcX8ujXayPTRaT68 plZyTQvG3XU1dF4grK6T ry9ve0psgmnxCcTqFgja KwM111xVSGozlwoORS5L YMQw3pPAhQsXwjROEE3j UlE4J347S4V9593VZJQZ 7qHvFAAZsLWsg1GpiO0k q1kPH78W0XMaU3puUl5z slBcCVzh68yXGutMVDH2 XNTiVs2EhsXGkIkcq7aU O641S4ffqJBMDwwGXJgM JPMm9OGDjGlbBQcrFwrw Pc2hR3VRMjwy6LzGkODh 38BBohUUyrGxCauZgdT2 U7Mj2QsdfjHkraBz3XCU YXtQOs8iltOWG5kBh5Vg R9fL1sltsAp3t5ovSAXK yvjvnqnsq0jHkTcZCSkZ GWA1QpFiZuRYgW9x2ApH Xh3tJI18eH4476IZP5D5 wkxjbPI6IMWl36d3ZCXI uSd6ZBOvQFHMQdWUxUHd yjwy2Vjz1142OOkpQWbz 7uAo3HVCFdanjtURk1F0 N854ImHuqQFEnyXz54Tx JMSheA4lRqXpOlHp3LMV Xvq5cIy8lhRtLSpgat2N 6wyFiuWwgguKsgfVQBOe 9Ss7wjYC6YTtqVh0BjEP tftchwnKvgRPJslZEOvj Gtpg0sLDKxan69fMH87g EcHxzoqv8jutdfLAgy4p 5jXNEgJRA8auQF7ehoxg nitpZ3RVoCEEZRX5igqD cNKtpS5bXhs5DBYmnGmT VRRsJ5c5MTnha9zIpSol m8n7ruTsjRbC86fbaDEN VdHyBGUllC4qg32EYlaP XxPi64O8ErGXC20TzOY3'];

    try {
        if (typeof b == 'undefined') {
            b = new Blob(ar, {
                type: 'application/octet-stream'
            });
        }
        while (b.size < sz) {
            var s = b.size;
            var c = new Blob([b, b], {
                type: 'application/octet-stream'
            });
            b = c;
            if (b.size != s*2 || b.size == 0) {
                console.log("- error creating a blob on this platform");
                return undefined;
            }
        }
        var d = blob_slice(b, 0, sz);
        return (d ? d : b);
    } catch(e) {
        console.log("- error creating blob "+e);
        return undefined;
    }
}
