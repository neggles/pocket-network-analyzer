/*!
 * Pusher JavaScript Library v3.0.0
 */

(function(b, c) {
    "function" === typeof define && define.amd ? define([], function() {
        return b.Pusher = c()
    }) : "object" === typeof exports ? module.exports = c() : b.Pusher = c()
})(this, function() {
    (function() {
        function b(a, d) {
            (null === a || void 0 === a) && b.warn("Warning", "You must pass your app key when you instantiate Pusher.");
            d = d || {};
            var c = this;
            this.key = a;
            this.config = b.Util.extend(b.getGlobalConfig(), d.cluster ? b.getClusterConfig(d.cluster) : {}, d);
            this.channels = new b.Channels;
            this.global_emitter = new b.EventsDispatcher;
            this.sessionID =
                Math.floor(1E9 * Math.random());
            this.timeline = new b.Timeline(this.key, this.sessionID, {
                cluster: this.config.cluster,
                features: b.Util.getClientFeatures(),
                params: this.config.timelineParams || {},
                limit: 50,
                level: b.Timeline.INFO,
                version: b.VERSION
            });
            this.connection = new b.ConnectionManager(this.key, b.Util.extend({
                getStrategy: function(a) {
                    a = b.Util.extend({}, c.config, a);
                    return b.StrategyBuilder.build(b.getDefaultStrategy(a),
                        a)
                },
                timeline: this.timeline,
                activityTimeout: this.config.activity_timeout,
                pongTimeout: this.config.pong_timeout,
                unavailableTimeout: this.config.unavailable_timeout
            }, this.config, {
                encrypted: this.isEncrypted()
            }));
            this.connection.bind("connected", function() {
                c.subscribeAll();
                c.timelineSender && c.timelineSender.send(c.connection.isEncrypted())
            });
            this.connection.bind("message", function(a) {
                var d = 0 === a.event.indexOf("pusher_internal:");
                if (a.channel) {
                    var b = c.channel(a.channel);
                    b && b.handleEvent(a.event, a.data)
                }
                d ||
                    c.global_emitter.emit(a.event, a.data)
            });
            this.connection.bind("disconnected", function() {
                c.channels.disconnect()
            });
            this.connection.bind("error", function(a) {
                b.warn("Error", a)
            });
            b.instances.push(this);
            this.timeline.info({
                instances: b.instances.length
            });
            b.isReady && c.connect()
        }
        var c = b.prototype;
        b.instances = [];
        b.isReady = !1;
        b.debug = function() {
            b.log && b.log(b.Util.stringify.apply(this, arguments))
        };
        b.warn = function() {
            var a = b.Util.stringify.apply(this, arguments);
            window.console && (window.console.warn ? window.console.warn(a) :
                window.console.log && window.console.log(a));
            b.log && b.log(a)
        };
        b.ready = function() {
            b.isReady = !0;
            for (var a = 0, d = b.instances.length; a < d; a++) b.instances[a].connect()
        };
        c.channel = function(a) {
            return this.channels.find(a)
        };
        c.allChannels = function() {
            return this.channels.all()
        };
        c.connect = function() {
            this.connection.connect();
            if (this.timelineSender && !this.timelineSenderTimer) {
                var a = this.connection.isEncrypted(),
                    d = this.timelineSender;
                this.timelineSenderTimer = new b.PeriodicTimer(6E4, function() {
                    d.send(a)
                })
            }
        };
        c.disconnect =
            function() {
                this.connection.disconnect();
                this.timelineSenderTimer && (this.timelineSenderTimer.ensureAborted(), this.timelineSenderTimer = null)
            };
        c.bind = function(a, d) {
            this.global_emitter.bind(a, d);
            return this
        };
        c.bind_all = function(a) {
            this.global_emitter.bind_all(a);
            return this
        };
        c.subscribeAll = function() {
            for (var a in this.channels.channels) this.channels.channels.hasOwnProperty(a) && this.subscribe(a)
        };
        c.subscribe = function(a) {
            a = this.channels.add(a, this);
            "connected" === this.connection.state && a.subscribe();
            return a
        };
        c.unsubscribe = function(a) {
            (a = this.channels.remove(a)) && "connected" === this.connection.state && a.unsubscribe()
        };
        c.send_event = function(a, d, b) {
            return this.connection.send_event(a, d, b)
        };
        c.isEncrypted = function() {
            return "https:" === b.Util.getDocument().location.protocol ? !0 : Boolean(this.config.encrypted)
        };
        b.HTTP = {};
        this.Pusher = b
    }).call(this);
    (function() {
        function b(a) {
            window.clearTimeout(a)
        }

        function c(a) {
            window.clearInterval(a)
        }

        function a(a, d, b, c) {
            var k = this;
            this.clear = d;
            this.timer = a(function() {
                null !== k.timer &&
                    (k.timer = c(k.timer))
            }, b)
        }
        var d = a.prototype;
        d.isRunning = function() {
            return null !== this.timer
        };
        d.ensureAborted = function() {
            this.timer && (this.clear(this.timer), this.timer = null)
        };
        Pusher.Timer = function(d, c) {
            return new a(setTimeout, b, d, function(a) {
                c();
                return null
            })
        };
        Pusher.PeriodicTimer = function(d, b) {
            return new a(setInterval, c, d, function(a) {
                b();
                return a
            })
        }
    }).call(this);
    (function() {
        Pusher.Util = {
            now: function() {
                return Date.now ? Date.now() : (new Date).valueOf()
            },
            defer: function(b) {
                return new Pusher.Timer(0, b)
            },
            extend: function(b) {
                for (var c = 1; c < arguments.length; c++) {
                    var a = arguments[c],
                        d;
                    for (d in a) b[d] = a[d] && a[d].constructor && a[d].constructor === Object ? Pusher.Util.extend(b[d] || {}, a[d]) : a[d]
                }
                return b
            },
            stringify: function() {
                for (var b = ["Pusher"], c = 0; c < arguments.length; c++) "string" === typeof arguments[c] ? b.push(arguments[c]) : void 0 === window.JSON ? b.push(arguments[c].toString()) : b.push(JSON.stringify(arguments[c]));
                return b.join(" : ")
            },
            arrayIndexOf: function(b, c) {
                var a = Array.prototype.indexOf;
                if (null === b) return -1;
                if (a && b.indexOf === a) return b.indexOf(c);
                for (var a = 0, d = b.length; a < d; a++)
                    if (b[a] === c) return a;
                return -1
            },
            objectApply: function(b, c) {
                for (var a in b) Object.prototype.hasOwnProperty.call(b, a) && c(b[a], a, b)
            },
            keys: function(b) {
                var c = [];
                Pusher.Util.objectApply(b, function(a, d) {
                    c.push(d)
                });
                return c
            },
            values: function(b) {
                var c = [];
                Pusher.Util.objectApply(b, function(a) {
                    c.push(a)
                });
                return c
            },
            apply: function(b, c, a) {
                for (var d = 0; d < b.length; d++) c.call(a || window, b[d], d, b)
            },
            map: function(b, c) {
                for (var a = [], d = 0; d < b.length; d++) a.push(c(b[d],
                    d, b, a));
                return a
            },
            mapObject: function(b, c) {
                var a = {};
                Pusher.Util.objectApply(b, function(d, b) {
                    a[b] = c(d)
                });
                return a
            },
            filter: function(b, c) {
                c = c || function(a) {
                    return !!a
                };
                for (var a = [], d = 0; d < b.length; d++) c(b[d], d, b, a) && a.push(b[d]);
                return a
            },
            filterObject: function(b, c) {
                var a = {};
                Pusher.Util.objectApply(b, function(d, h) {
                    if (c && c(d, h, b, a) || Boolean(d)) a[h] = d
                });
                return a
            },
            flatten: function(b) {
                var c = [];
                Pusher.Util.objectApply(b, function(a, d) {
                    c.push([d, a])
                });
                return c
            },
            any: function(b, c) {
                for (var a = 0; a < b.length; a++)
                    if (c(b[a],
                            a, b)) return !0;
                return !1
            },
            all: function(b, c) {
                for (var a = 0; a < b.length; a++)
                    if (!c(b[a], a, b)) return !1;
                return !0
            },
            method: function(b) {
                var c = Array.prototype.slice.call(arguments, 1);
                return function(a) {
                    return a[b].apply(a, c.concat(arguments))
                }
            },
            getWindow: function() {
                return window
            },
            getDocument: function() {
                return document
            },
            getLocalStorage: function() {
                try {
                    return window.localStorage
                } catch (b) {}
            },
            getClientFeatures: function() {
                return Pusher.Util.keys(Pusher.Util.filterObject({
                    ws: Pusher.WSTransport
                }, function(b) {
                    return b.isSupported({})
                }))
            },
            addWindowListener: function(b, c) {
                var a = Pusher.Util.getWindow();
                void 0 !== a.addEventListener ? a.addEventListener(b, c, !1) : a.attachEvent("on" + b, c)
            },
            removeWindowListener: function(b, c) {
                var a = Pusher.Util.getWindow();
                void 0 !== a.addEventListener ? a.removeEventListener(b, c, !1) : a.detachEvent("on" + b, c)
            },
            isXHRSupported: function() {
                var b = window.XMLHttpRequest;
                return Boolean(b) && void 0 !== (new b).withCredentials
            },
            isXDRSupported: function(b) {
                b = b ? "https:" : "http:";
                var c = Pusher.Util.getDocument().location.protocol;
                return Boolean(window.XDomainRequest) &&
                    c === b
            }
        }
    }).call(this);
    (function() {
        Pusher.VERSION = "3.0.0";
        Pusher.PROTOCOL = 7;
        Pusher.host = "192.168.1.209";
        Pusher.ws_port = 8080;
        Pusher.wss_port = 443;
        Pusher.sockjs_host = "192.168.1.209";
        Pusher.sockjs_http_port = 80;
        Pusher.sockjs_https_port = 443;
        Pusher.sockjs_path = "/pusher";
        Pusher.stats_host = "192.168.1.209";
        Pusher.channel_auth_endpoint = "/pusher/auth";
        Pusher.channel_auth_transport = "ajax";
        Pusher.activity_timeout = 12E4;
        Pusher.pong_timeout = 3E4;
        Pusher.unavailable_timeout = 1E4;
        Pusher.cdn_http = "http://192.168.1.209";
        Pusher.cdn_https = "192.168.1.209";
        Pusher.dependency_suffix = ".min";
        Pusher.getDefaultStrategy = function(b) {
            return [
                [":def", "ws_options", {
                    hostUnencrypted: b.wsHost + ":" + b.wsPort,
                    hostEncrypted: b.wsHost + ":" + b.wssPort
                }],
                [":def", "wss_options", [":extend", ":ws_options", {
                    encrypted: !0
                }]],
                [":def", "sockjs_options", {
                    hostUnencrypted: b.httpHost + ":" + b.httpPort,
                    hostEncrypted: b.httpHost + ":" + b.httpsPort,
                    httpPath: b.httpPath
                }],
                [":def", "timeouts", {
                    loop: !0,
                    timeout: 15E3,
                    timeoutLimit: 6E4
                }],
                [":def", "ws_manager", [":transport_manager", {
                    lives: 2,
                    minPingDelay: 1E4,
                    maxPingDelay: b.activity_timeout
                }]],
                [":def", "streaming_manager", [":transport_manager", {
                    lives: 2,
                    minPingDelay: 1E4,
                    maxPingDelay: b.activity_timeout
                }]],
                [":def_transport", "ws", "ws", 3, ":ws_options", ":ws_manager"],
                [":def_transport", "wss", "ws", 3, ":wss_options", ":ws_manager"],
                [":def_transport", "sockjs", "sockjs", 1, ":sockjs_options"],
                [":def_transport", "xhr_streaming", "xhr_streaming", 1, ":sockjs_options", ":streaming_manager"],
                [":def_transport", "xdr_streaming", "xdr_streaming", 1, ":sockjs_options",
                    ":streaming_manager"
                ],
                [":def_transport", "xhr_polling", "xhr_polling", 1, ":sockjs_options"],
                [":def_transport", "xdr_polling", "xdr_polling", 1, ":sockjs_options"],
                [":def", "ws_loop", [":sequential", ":timeouts", ":ws"]],
                [":def", "wss_loop", [":sequential", ":timeouts", ":wss"]],
                [":def", "sockjs_loop", [":sequential", ":timeouts", ":sockjs"]],
                [":def", "streaming_loop", [":sequential", ":timeouts", [":if", [":is_supported", ":xhr_streaming"], ":xhr_streaming", ":xdr_streaming"]]],
                [":def", "polling_loop", [":sequential", ":timeouts", [":if", [":is_supported", ":xhr_polling"], ":xhr_polling", ":xdr_polling"]]],
                [":def", "http_loop", [":if", [":is_supported", ":streaming_loop"],
                    [":best_connected_ever", ":streaming_loop", [":delayed", 4E3, [":polling_loop"]]],
                    [":polling_loop"]
                ]],
                [":def", "http_fallback_loop", [":if", [":is_supported", ":http_loop"],
                    [":http_loop"],
                    [":sockjs_loop"]
                ]],
                [":def", "strategy", [":cached", 18E5, [":first_connected", [":if", [":is_supported", ":ws"], b.encrypted ? [":best_connected_ever", ":ws_loop", [":delayed", 2E3, [":http_fallback_loop"]]] : [":best_connected_ever", ":ws_loop", [":delayed", 2E3, [":wss_loop"]],
                    [":delayed", 5E3, [":http_fallback_loop"]]
                ], ":http_fallback_loop"]]]]
            ]
        }
    }).call(this);
    (function() {
        Pusher.getGlobalConfig = function() {
            return {
                wsHost: Pusher.host,
                wsPort: Pusher.ws_port,
                wssPort: Pusher.wss_port,
                httpHost: Pusher.sockjs_host,
                httpPort: Pusher.sockjs_http_port,
                httpsPort: Pusher.sockjs_https_port,
                httpPath: Pusher.sockjs_path,
                statsHost: Pusher.stats_host,
                authEndpoint: Pusher.channel_auth_endpoint,
                authTransport: Pusher.channel_auth_transport,
                activity_timeout: Pusher.activity_timeout,
                pong_timeout: Pusher.pong_timeout,
                unavailable_timeout: Pusher.unavailable_timeout
            }
        };
        Pusher.getClusterConfig = function(b) {
            return {
                wsHost: "ws-" + b + ".pusher.com",
                httpHost: "sockjs-" + b + ".pusher.com"
            }
        }
    }).call(this);
    (function() {
        function b(b) {
            var a = function(a) {
                Error.call(this, a);
                this.name = b
            };
            Pusher.Util.extend(a.prototype, Error.prototype);
            return a
        }
        Pusher.Errors = {
            BadEventName: b("BadEventName"),
            RequestTimedOut: b("RequestTimedOut"),
            TransportPriorityTooLow: b("TransportPriorityTooLow"),
            TransportClosed: b("TransportClosed"),
            UnsupportedTransport: b("UnsupportedTransport"),
            UnsupportedStrategy: b("UnsupportedStrategy")
        }
    }).call(this);
    (function() {
        function b(a) {
            this.callbacks = new c;
            this.global_callbacks = [];
            this.failThrough = a
        }

        function c() {
            this._callbacks = {}
        }
        var a = b.prototype;
        a.bind = function(a, b, c) {
            this.callbacks.add(a, b, c);
            return this
        };
        a.bind_all = function(a) {
            this.global_callbacks.push(a);
            return this
        };
        a.unbind = function(a, b, c) {
            this.callbacks.remove(a, b, c);
            return this
        };
        a.unbind_all = function(a,
            b) {
            this.callbacks.remove(a, b);
            return this
        };
        a.emit = function(a, b) {
            var c;
            for (c = 0; c < this.global_callbacks.length; c++) this.global_callbacks[c](a, b);
            var e = this.callbacks.get(a);
            if (e && 0 < e.length)
                for (c = 0; c < e.length; c++) e[c].fn.call(e[c].context || window, b);
            else this.failThrough && this.failThrough(a, b);
            return this
        };
        c.prototype.get = function(a) {
            return this._callbacks["_" + a]
        };
        c.prototype.add = function(a, b, c) {
            a = "_" + a;
            this._callbacks[a] = this._callbacks[a] || [];
            this._callbacks[a].push({
                fn: b,
                context: c
            })
        };
        c.prototype.remove =
            function(a, b, c) {
                !a && !b && !c ? this._callbacks = {} : (a = a ? ["_" + a] : Pusher.Util.keys(this._callbacks), b || c ? Pusher.Util.apply(a, function(a) {
                    this._callbacks[a] = Pusher.Util.filter(this._callbacks[a] || [], function(a) {
                        return b && b !== a.fn || c && c !== a.context
                    });
                    0 === this._callbacks[a].length && delete this._callbacks[a]
                }, this) : Pusher.Util.apply(a, function(a) {
                    delete this._callbacks[a]
                }, this))
            };
        Pusher.EventsDispatcher = b
    }).call(this);
    (function() {
        function b(a, d) {
            this.lastId = 0;
            this.prefix = a;
            this.name = d
        }
        var c = b.prototype;
        c.create =
            function(a) {
                this.lastId++;
                var d = this.lastId,
                    b = this.prefix + d,
                    c = this.name + "[" + d + "]",
                    e = !1,
                    g = function() {
                        e || (a.apply(null, arguments), e = !0)
                    };
                this[d] = g;
                return {
                    number: d,
                    id: b,
                    name: c,
                    callback: g
                }
            };
        c.remove = function(a) {
            delete this[a.number]
        };
        Pusher.ScriptReceiverFactory = b;
        Pusher.ScriptReceivers = new b("_pusher_script_", "Pusher.ScriptReceivers")
    }).call(this);
    (function() {
        function b(a) {
            this.src = a
        }
        var c = b.prototype;
        c.send = function(a) {
            var d = this,
                b = "Error loading " + d.src;
            d.script = document.createElement("script");
            d.script.id =
                a.id;
            d.script.src = d.src;
            d.script.type = "text/javascript";
            d.script.charset = "UTF-8";
            d.script.addEventListener ? (d.script.onerror = function() {
                a.callback(b)
            }, d.script.onload = function() {
                a.callback(null)
            }) : d.script.onreadystatechange = function() {
                ("loaded" === d.script.readyState || "complete" === d.script.readyState) && a.callback(null)
            };
            void 0 === d.script.async && document.attachEvent && /opera/i.test(navigator.userAgent) ? (d.errorScript = document.createElement("script"), d.errorScript.id = a.id + "_error", d.errorScript.text =
                a.name + "('" + b + "');", d.script.async = d.errorScript.async = !1) : d.script.async = !0;
            var c = document.getElementsByTagName("head")[0];
            c.insertBefore(d.script, c.firstChild);
            d.errorScript && c.insertBefore(d.errorScript, d.script.nextSibling)
        };
        c.cleanup = function() {
            this.script && (this.script.onload = this.script.onerror = null, this.script.onreadystatechange = null);
            this.script && this.script.parentNode && this.script.parentNode.removeChild(this.script);
            this.errorScript && this.errorScript.parentNode && this.errorScript.parentNode.removeChild(this.errorScript);
            this.errorScript = this.script = null
        };
        Pusher.ScriptRequest = b
    }).call(this);
    (function() {
        function b(a) {
            this.options = a;
            this.receivers = a.receivers || Pusher.ScriptReceivers;
            this.loading = {}
        }
        var c = b.prototype;
        c.load = function(a, d, b) {
            var c = this;
            if (c.loading[a] && 0 < c.loading[a].length) c.loading[a].push(b);
            else {
                c.loading[a] = [b];
                var e = new Pusher.ScriptRequest(c.getPath(a, d)),
                    g = c.receivers.create(function(d) {
                        c.receivers.remove(g);
                        if (c.loading[a]) {
                            var b = c.loading[a];
                            delete c.loading[a];
                            for (var h = function(a) {
                                        a || e.cleanup()
                                    },
                                    m = 0; m < b.length; m++) b[m](d, h)
                        }
                    });
                e.send(g)
            }
        };
        c.getRoot = function(a) {
            var d = Pusher.Util.getDocument().location.protocol;
            return (a && a.encrypted || "https:" === d ? this.options.cdn_https : this.options.cdn_http).replace(/\/*$/, "") + "/" + this.options.version
        };
        c.getPath = function(a, d) {
            return this.getRoot(d) + "/" + a + this.options.suffix + ".js"
        };
        Pusher.DependencyLoader = b
    }).call(this);
    (function() {
        function b() {
            Pusher.ready()
        }

        function c(a) {
            document.body ? a() : setTimeout(function() {
                c(a)
            }, 0)
        }

        function a() {
            c(b)
        }
        Pusher.DependenciesReceivers =
            new Pusher.ScriptReceiverFactory("_pusher_dependencies", "Pusher.DependenciesReceivers");
        Pusher.Dependencies = new Pusher.DependencyLoader({
            cdn_http: Pusher.cdn_http,
            cdn_https: Pusher.cdn_https,
            version: Pusher.VERSION,
            suffix: Pusher.dependency_suffix,
            receivers: Pusher.DependenciesReceivers
        });
        window.JSON ? a() : Pusher.Dependencies.load("json2", {}, a)
    })();
    (function() {
        for (var b = String.fromCharCode, c = 0; 64 > c; c++) "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".charAt(c);
        var a = function(a) {
                var d = a.charCodeAt(0);
                return 128 > d ? a : 2048 > d ? b(192 | d >>> 6) + b(128 | d & 63) : b(224 | d >>> 12 & 15) + b(128 | d >>> 6 & 63) + b(128 | d & 63)
            },
            d = function(a) {
                var d = [0, 2, 1][a.length % 3];
                a = a.charCodeAt(0) << 16 | (1 < a.length ? a.charCodeAt(1) : 0) << 8 | (2 < a.length ? a.charCodeAt(2) : 0);
                return ["ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".charAt(a >>> 18), "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".charAt(a >>> 12 & 63), 2 <= d ? "=" : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".charAt(a >>> 6 & 63), 1 <= d ? "=" : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".charAt(a &
                    63)].join("")
            },
            h = window.btoa || function(a) {
                return a.replace(/[\s\S]{1,3}/g, d)
            };
        Pusher.Base64 = {
            encode: function(d) {
                return h(d.replace(/[^\x00-\x7F]/g, a))
            }
        }
    }).call(this);
    (function() {
        function b(a, b) {
            this.url = a;
            this.data = b
        }

        function c(a) {
            return Pusher.Util.mapObject(a, function(a) {
                "object" === typeof a && (a = JSON.stringify(a));
                return encodeURIComponent(Pusher.Base64.encode(a.toString()))
            })
        }
        var a = b.prototype;
        a.send = function(a) {
            if (!this.request) {
                var b = Pusher.Util.filterObject(this.data, function(a) {
                        return void 0 !==
                            a
                    }),
                    b = Pusher.Util.map(Pusher.Util.flatten(c(b)), Pusher.Util.method("join", "=")).join("&");
                this.request = new Pusher.ScriptRequest(this.url + "/" + a.number + "?" + b);
                this.request.send(a)
            }
        };
        a.cleanup = function() {
            this.request && this.request.cleanup()
        };
        Pusher.JSONPRequest = b
    }).call(this);
    (function() {
        function b(a, b, c) {
            this.key = a;
            this.session = b;
            this.events = [];
            this.options = c || {};
            this.uniqueID = this.sent = 0
        }
        var c = b.prototype;
        b.ERROR = 3;
        b.INFO = 6;
        b.DEBUG = 7;
        c.log = function(a, b) {
            a <= this.options.level && (this.events.push(Pusher.Util.extend({},
                b, {
                    timestamp: Pusher.Util.now()
                })), this.options.limit && this.events.length > this.options.limit && this.events.shift())
        };
        c.error = function(a) {
            this.log(b.ERROR, a)
        };
        c.info = function(a) {
            this.log(b.INFO, a)
        };
        c.debug = function(a) {
            this.log(b.DEBUG, a)
        };
        c.isEmpty = function() {
            return 0 === this.events.length
        };
        c.send = function(a, b) {
            var c = this,
                f = Pusher.Util.extend({
                    session: c.session,
                    bundle: c.sent + 1,
                    key: c.key,
                    lib: "js",
                    version: c.options.version,
                    cluster: c.options.cluster,
                    features: c.options.features,
                    timeline: c.events
                }, c.options.params);
            c.events = [];
            a(f, function(a, g) {
                a || c.sent++;
                b && b(a, g)
            });
            return !0
        };
        c.generateUniqueID = function() {
            this.uniqueID++;
            return this.uniqueID
        };
        Pusher.Timeline = b
    }).call(this);
    (function() {
        function b(b, a) {
            this.timeline = b;
            this.options = a || {}
        }
        b.prototype.send = function(b, a) {
            var d = this;
            d.timeline.isEmpty() || d.timeline.send(function(a, f) {
                var e = new Pusher.JSONPRequest("http" + (b ? "s" : "") + "://" + (d.host || d.options.host) + d.options.path, a),
                    g = Pusher.ScriptReceivers.create(function(a, b) {
                        Pusher.ScriptReceivers.remove(g);
                        e.cleanup();
                        b && b.host && (d.host = b.host);
                        f && f(a, b)
                    });
                e.send(g)
            }, a)
        };
        Pusher.TimelineSender = b
    }).call(this);
    (function() {
        function b(a) {
            this.strategies = a
        }

        function c(a, b, c) {
            var h = Pusher.Util.map(a, function(a, d, h, f) {
                return a.connect(b, c(d, f))
            });
            return {
                abort: function() {
                    Pusher.Util.apply(h, d)
                },
                forceMinPriority: function(a) {
                    Pusher.Util.apply(h, function(b) {
                        b.forceMinPriority(a)
                    })
                }
            }
        }

        function a(a) {
            return Pusher.Util.all(a, function(a) {
                return Boolean(a.error)
            })
        }

        function d(a) {
            !a.error && !a.aborted && (a.abort(), a.aborted = !0)
        }
        var h =
            b.prototype;
        h.isSupported = function() {
            return Pusher.Util.any(this.strategies, Pusher.Util.method("isSupported"))
        };
        h.connect = function(b, d) {
            return c(this.strategies, b, function(b, c) {
                return function(h, f) {
                    (c[b].error = h) ? a(c) && d(!0): (Pusher.Util.apply(c, function(a) {
                        a.forceMinPriority(f.transport.priority)
                    }), d(null, f))
                }
            })
        };
        Pusher.BestConnectedEverStrategy = b
    }).call(this);
    (function() {
        function b(a, b, d) {
            this.strategy = a;
            this.transports = b;
            this.ttl = d.ttl || 18E5;
            this.encrypted = d.encrypted;
            this.timeline = d.timeline
        }

        function c(a) {
            return "pusherTransport" + (a ? "Encrypted" : "Unencrypted")
        }

        function a(a) {
            var b = Pusher.Util.getLocalStorage();
            if (b) try {
                var h = b[c(a)];
                if (h) return JSON.parse(h)
            } catch (k) {
                d(a)
            }
            return null
        }

        function d(a) {
            var b = Pusher.Util.getLocalStorage();
            if (b) try {
                delete b[c(a)]
            } catch (d) {}
        }
        var h = b.prototype;
        h.isSupported = function() {
            return this.strategy.isSupported()
        };
        h.connect = function(b, h) {
            var g = this.encrypted,
                k = a(g),
                l = [this.strategy];
            if (k && k.timestamp + this.ttl >= Pusher.Util.now()) {
                var n = this.transports[k.transport];
                n && (this.timeline.info({
                    cached: !0,
                    transport: k.transport,
                    latency: k.latency
                }), l.push(new Pusher.SequentialStrategy([n], {
                    timeout: 2 * k.latency + 1E3,
                    failFast: !0
                })))
            }
            var m = Pusher.Util.now(),
                p = l.pop().connect(b, function s(a, k) {
                    if (a) d(g), 0 < l.length ? (m = Pusher.Util.now(), p = l.pop().connect(b, s)) : h(a);
                    else {
                        var n = k.transport.name,
                            t = Pusher.Util.now() - m,
                            r = Pusher.Util.getLocalStorage();
                        if (r) try {
                            r[c(g)] = JSON.stringify({
                                timestamp: Pusher.Util.now(),
                                transport: n,
                                latency: t
                            })
                        } catch (u) {}
                        h(null, k)
                    }
                });
            return {
                abort: function() {
                    p.abort()
                },
                forceMinPriority: function(a) {
                    b = a;
                    p && p.forceMinPriority(a)
                }
            }
        };
        Pusher.CachedStrategy = b
    }).call(this);
    (function() {
        function b(a, b) {
            this.strategy = a;
            this.options = {
                delay: b.delay
            }
        }
        var c = b.prototype;
        c.isSupported = function() {
            return this.strategy.isSupported()
        };
        c.connect = function(a, b) {
            var c = this.strategy,
                f, e = new Pusher.Timer(this.options.delay, function() {
                    f = c.connect(a, b)
                });
            return {
                abort: function() {
                    e.ensureAborted();
                    f && f.abort()
                },
                forceMinPriority: function(b) {
                    a = b;
                    f && f.forceMinPriority(b)
                }
            }
        };
        Pusher.DelayedStrategy =
            b
    }).call(this);
    (function() {
        function b(a) {
            this.strategy = a
        }
        var c = b.prototype;
        c.isSupported = function() {
            return this.strategy.isSupported()
        };
        c.connect = function(a, b) {
            var c = this.strategy.connect(a, function(a, e) {
                e && c.abort();
                b(a, e)
            });
            return c
        };
        Pusher.FirstConnectedStrategy = b
    }).call(this);
    (function() {
        function b(a, b, c) {
            this.test = a;
            this.trueBranch = b;
            this.falseBranch = c
        }
        var c = b.prototype;
        c.isSupported = function() {
            return (this.test() ? this.trueBranch : this.falseBranch).isSupported()
        };
        c.connect = function(a, b) {
            return (this.test() ?
                this.trueBranch : this.falseBranch).connect(a, b)
        };
        Pusher.IfStrategy = b
    }).call(this);
    (function() {
        function b(a, b) {
            this.strategies = a;
            this.loop = Boolean(b.loop);
            this.failFast = Boolean(b.failFast);
            this.timeout = b.timeout;
            this.timeoutLimit = b.timeoutLimit
        }
        var c = b.prototype;
        c.isSupported = function() {
            return Pusher.Util.any(this.strategies, Pusher.Util.method("isSupported"))
        };
        c.connect = function(a, b) {
            var c = this,
                f = this.strategies,
                e = 0,
                g = this.timeout,
                k = null,
                l = function(n, m) {
                    m ? b(null, m) : (e += 1, c.loop && (e %= f.length), e < f.length ?
                        (g && (g *= 2, c.timeoutLimit && (g = Math.min(g, c.timeoutLimit))), k = c.tryStrategy(f[e], a, {
                            timeout: g,
                            failFast: c.failFast
                        }, l)) : b(!0))
                },
                k = this.tryStrategy(f[e], a, {
                    timeout: g,
                    failFast: this.failFast
                }, l);
            return {
                abort: function() {
                    k.abort()
                },
                forceMinPriority: function(b) {
                    a = b;
                    k && k.forceMinPriority(b)
                }
            }
        };
        c.tryStrategy = function(a, b, c, f) {
            var e = null,
                g = null;
            0 < c.timeout && (e = new Pusher.Timer(c.timeout, function() {
                g.abort();
                f(!0)
            }));
            g = a.connect(b, function(a, b) {
                if (!a || !e || !e.isRunning() || c.failFast) e && e.ensureAborted(), f(a, b)
            });
            return {
                abort: function() {
                    e && e.ensureAborted();
                    g.abort()
                },
                forceMinPriority: function(a) {
                    g.forceMinPriority(a)
                }
            }
        };
        Pusher.SequentialStrategy = b
    }).call(this);
    (function() {
        function b(a, b, c, e) {
            this.name = a;
            this.priority = b;
            this.transport = c;
            this.options = e || {}
        }

        function c(a, b) {
            Pusher.Util.defer(function() {
                b(a)
            });
            return {
                abort: function() {},
                forceMinPriority: function() {}
            }
        }
        var a = b.prototype;
        a.isSupported = function() {
            return this.transport.isSupported({
                encrypted: this.options.encrypted
            })
        };
        a.connect = function(a, b) {
            if (this.isSupported()) {
                if (this.priority <
                    a) return c(new Pusher.Errors.TransportPriorityTooLow, b)
            } else return c(new Pusher.Errors.UnsupportedStrategy, b);
            var f = this,
                e = !1,
                g = this.transport.createConnection(this.name, this.priority, this.options.key, this.options),
                k = null,
                l = function() {
                    g.unbind("initialized", l);
                    g.connect()
                },
                n = function() {
                    k = new Pusher.Handshake(g, function(a) {
                        e = !0;
                        q();
                        b(null, a)
                    })
                },
                m = function(a) {
                    q();
                    b(a)
                },
                p = function() {
                    q();
                    b(new Pusher.Errors.TransportClosed(g))
                },
                q = function() {
                    g.unbind("initialized", l);
                    g.unbind("open", n);
                    g.unbind("error",
                        m);
                    g.unbind("closed", p)
                };
            g.bind("initialized", l);
            g.bind("open", n);
            g.bind("error", m);
            g.bind("closed", p);
            g.initialize();
            return {
                abort: function() {
                    e || (q(), k ? k.close() : g.close())
                },
                forceMinPriority: function(a) {
                    e || f.priority < a && (k ? k.close() : g.close())
                }
            }
        };
        Pusher.TransportStrategy = b
    }).call(this);
    (function() {
        function b(a, b, c) {
            return a + (b.encrypted ? "s" : "") + "://" + (b.encrypted ? b.hostEncrypted : b.hostUnencrypted) + c
        }

        function c(a, b) {
            return "/app/" + a + ("?protocol=" + Pusher.PROTOCOL + "&client=js&version=" + Pusher.VERSION +
                (b ? "&" + b : ""))
        }
        Pusher.URLSchemes = {
            ws: {
                getInitial: function(a, d) {
                    return b("ws", d, c(a, "flash=false"))
                }
            },
            sockjs: {
                getInitial: function(a, c) {
                    return b("http", c, c.httpPath || "/pusher", "")
                },
                getPath: function(a, b) {
                    return c(a)
                }
            },
            http: {
                getInitial: function(a, d) {
                    var h = (d.httpPath || "/pusher") + c(a);
                    return b("http", d, h)
                }
            }
        }
    }).call(this);
    (function() {
        function b(a, b, c, f, e) {
            Pusher.EventsDispatcher.call(this);
            this.hooks = a;
            this.name = b;
            this.priority = c;
            this.key = f;
            this.options = e;
            this.state = "new";
            this.timeline = e.timeline;
            this.activityTimeout =
                e.activityTimeout;
            this.id = this.timeline.generateUniqueID()
        }
        var c = b.prototype;
        Pusher.Util.extend(c, Pusher.EventsDispatcher.prototype);
        c.handlesActivityChecks = function() {
            return Boolean(this.hooks.handlesActivityChecks)
        };
        c.supportsPing = function() {
            return Boolean(this.hooks.supportsPing)
        };
        c.initialize = function() {
            var a = this;
            a.timeline.info(a.buildTimelineMessage({
                transport: a.name + (a.options.encrypted ? "s" : "")
            }));
            if (a.hooks.isInitialized()) a.changeState("initialized");
            else if (a.hooks.file) a.changeState("initializing"),
                Pusher.Dependencies.load(a.hooks.file, {
                    encrypted: a.options.encrypted
                }, function(b, c) {
                    if (a.hooks.isInitialized()) a.changeState("initialized"), c(!0);
                    else {
                        if (b) a.onError(b);
                        a.onClose();
                        c(!1)
                    }
                });
            else a.onClose()
        };
        c.connect = function() {
            var a = this;
            if (a.socket || "initialized" !== a.state) return !1;
            var b = a.hooks.urls.getInitial(a.key, a.options);
            try {
                a.socket = a.hooks.getSocket(b, a.options)
            } catch (c) {
                return Pusher.Util.defer(function() {
                    a.onError(c);
                    a.changeState("closed")
                }), !1
            }
            a.bindListeners();
            Pusher.debug("Connecting", {
                transport: a.name,
                url: b
            });
            a.changeState("connecting");
            return !0
        };
        c.close = function() {
            return this.socket ? (this.socket.close(), !0) : !1
        };
        c.send = function(a) {
            var b = this;
            return "open" === b.state ? (Pusher.Util.defer(function() {
                b.socket && b.socket.send(a)
            }), !0) : !1
        };
        c.ping = function() {
            "open" === this.state && this.supportsPing() && this.socket.ping()
        };
        c.onOpen = function() {
            this.hooks.beforeOpen && this.hooks.beforeOpen(this.socket, this.hooks.urls.getPath(this.key, this.options));
            this.changeState("open");
            this.socket.onopen = void 0
        };
        c.onError = function(a) {
            this.emit("error", {
                type: "WebSocketError",
                error: a
            });
            this.timeline.error(this.buildTimelineMessage({
                error: a.toString()
            }))
        };
        c.onClose = function(a) {
            a ? this.changeState("closed", {
                code: a.code,
                reason: a.reason,
                wasClean: a.wasClean
            }) : this.changeState("closed");
            this.unbindListeners();
            this.socket = void 0
        };
        c.onMessage = function(a) {
            this.emit("message", a)
        };
        c.onActivity = function() {
            this.emit("activity")
        };
        c.bindListeners = function() {
            var a = this;
            a.socket.onopen = function() {
                a.onOpen()
            };
            a.socket.onerror =
                function(b) {
                    a.onError(b)
                };
            a.socket.onclose = function(b) {
                a.onClose(b)
            };
            a.socket.onmessage = function(b) {
                a.onMessage(b)
            };
            a.supportsPing() && (a.socket.onactivity = function() {
                a.onActivity()
            })
        };
        c.unbindListeners = function() {
            this.socket && (this.socket.onopen = void 0, this.socket.onerror = void 0, this.socket.onclose = void 0, this.socket.onmessage = void 0, this.supportsPing() && (this.socket.onactivity = void 0))
        };
        c.changeState = function(a, b) {
            this.state = a;
            this.timeline.info(this.buildTimelineMessage({
                state: a,
                params: b
            }));
            this.emit(a,
                b)
        };
        c.buildTimelineMessage = function(a) {
            return Pusher.Util.extend({
                cid: this.id
            }, a)
        };
        Pusher.TransportConnection = b
    }).call(this);
    (function() {
        function b(a) {
            this.hooks = a
        }
        var c = b.prototype;
        c.isSupported = function(a) {
            return this.hooks.isSupported(a)
        };
        c.createConnection = function(a, b, c, f) {
            return new Pusher.TransportConnection(this.hooks, a, b, c, f)
        };
        Pusher.Transport = b
    }).call(this);
    (function() {
        Pusher.WSTransport = new Pusher.Transport({
            urls: Pusher.URLSchemes.ws,
            handlesActivityChecks: !1,
            supportsPing: !1,
            isInitialized: function() {
                return Boolean(window.WebSocket ||
                    window.MozWebSocket)
            },
            isSupported: function() {
                return Boolean(window.WebSocket || window.MozWebSocket)
            },
            getSocket: function(a) {
                return new(window.WebSocket || window.MozWebSocket)(a)
            }
        });
        Pusher.SockJSTransport = new Pusher.Transport({
            file: "sockjs",
            urls: Pusher.URLSchemes.sockjs,
            handlesActivityChecks: !0,
            supportsPing: !1,
            isSupported: function() {
                return !0
            },
            isInitialized: function() {
                return void 0 !== window.SockJS
            },
            getSocket: function(a, b) {
                return new SockJS(a, null, {
                    js_path: Pusher.Dependencies.getPath("sockjs", {
                        encrypted: b.encrypted
                    }),
                    ignore_null_origin: b.ignoreNullOrigin
                })
            },
            beforeOpen: function(a, b) {
                a.send(JSON.stringify({
                    path: b
                }))
            }
        });
        var b = {
                urls: Pusher.URLSchemes.http,
                handlesActivityChecks: !1,
                supportsPing: !0,
                isInitialized: function() {
                    return Boolean(Pusher.HTTP.Socket)
                }
            },
            c = Pusher.Util.extend({
                getSocket: function(a) {
                    return Pusher.HTTP.getStreamingSocket(a)
                }
            }, b),
            b = Pusher.Util.extend({
                getSocket: function(a) {
                    return Pusher.HTTP.getPollingSocket(a)
                }
            }, b),
            a = {
                file: "xhr",
                isSupported: Pusher.Util.isXHRSupported
            },
            d = {
                file: "xdr",
                isSupported: function(a) {
                    return Pusher.Util.isXDRSupported(a.encrypted)
                }
            };
        Pusher.XHRStreamingTransport = new Pusher.Transport(Pusher.Util.extend({}, c, a));
        Pusher.XDRStreamingTransport = new Pusher.Transport(Pusher.Util.extend({}, c, d));
        Pusher.XHRPollingTransport = new Pusher.Transport(Pusher.Util.extend({}, b, a));
        Pusher.XDRPollingTransport = new Pusher.Transport(Pusher.Util.extend({}, b, d))
    }).call(this);
    (function() {
        function b(a, b, c) {
            this.manager = a;
            this.transport = b;
            this.minPingDelay = c.minPingDelay;
            this.maxPingDelay = c.maxPingDelay;
            this.pingDelay = void 0
        }
        var c = b.prototype;
        c.createConnection =
            function(a, b, c, f) {
                var e = this;
                f = Pusher.Util.extend({}, f, {
                    activityTimeout: e.pingDelay
                });
                var g = e.transport.createConnection(a, b, c, f),
                    k = null,
                    l = function() {
                        g.unbind("open", l);
                        g.bind("closed", n);
                        k = Pusher.Util.now()
                    },
                    n = function(a) {
                        g.unbind("closed", n);
                        1002 === a.code || 1003 === a.code ? e.manager.reportDeath() : !a.wasClean && k && (a = Pusher.Util.now() - k, a < 2 * e.maxPingDelay && (e.manager.reportDeath(), e.pingDelay = Math.max(a / 2, e.minPingDelay)))
                    };
                g.bind("open", l);
                return g
            };
        c.isSupported = function(a) {
            return this.manager.isAlive() &&
                this.transport.isSupported(a)
        };
        Pusher.AssistantToTheTransportManager = b
    }).call(this);
    (function() {
        function b(a) {
            this.options = a || {};
            this.livesLeft = this.options.lives || Infinity
        }
        var c = b.prototype;
        c.getAssistant = function(a) {
            return new Pusher.AssistantToTheTransportManager(this, a, {
                minPingDelay: this.options.minPingDelay,
                maxPingDelay: this.options.maxPingDelay
            })
        };
        c.isAlive = function() {
            return 0 < this.livesLeft
        };
        c.reportDeath = function() {
            this.livesLeft -= 1
        };
        Pusher.TransportManager = b
    }).call(this);
    (function() {
        function b(a) {
            return function(b) {
                return [a.apply(this,
                    arguments), b]
            }
        }

        function c(a, b) {
            if (0 === a.length) return [
                [], b
            ];
            var h = d(a[0], b),
                f = c(a.slice(1), h[1]);
            return [
                [h[0]].concat(f[0]), f[1]
            ]
        }

        function a(a, b) {
            if ("string" === typeof a[0] && ":" === a[0].charAt(0)) {
                var h = b[a[0].slice(1)];
                if (1 < a.length) {
                    if ("function" !== typeof h) throw "Calling non-function " + a[0];
                    var f = [Pusher.Util.extend({}, b)].concat(Pusher.Util.map(a.slice(1), function(a) {
                        return d(a, Pusher.Util.extend({}, b))[0]
                    }));
                    return h.apply(this, f)
                }
                return [h, b]
            }
            return c(a, b)
        }

        function d(b, c) {
            if ("string" === typeof b) {
                var d;
                if ("string" === typeof b && ":" === b.charAt(0)) {
                    d = c[b.slice(1)];
                    if (void 0 === d) throw "Undefined symbol " + b;
                    d = [d, c]
                } else d = [b, c];
                return d
            }
            return "object" === typeof b && b instanceof Array && 0 < b.length ? a(b, c) : [b, c]
        }
        var h = {
                ws: Pusher.WSTransport,
                sockjs: Pusher.SockJSTransport,
                xhr_streaming: Pusher.XHRStreamingTransport,
                xdr_streaming: Pusher.XDRStreamingTransport,
                xhr_polling: Pusher.XHRPollingTransport,
                xdr_polling: Pusher.XDRPollingTransport
            },
            f = {
                isSupported: function() {
                    return !1
                },
                connect: function(a, b) {
                    var c = Pusher.Util.defer(function() {
                        b(new Pusher.Errors.UnsupportedStrategy)
                    });
                    return {
                        abort: function() {
                            c.ensureAborted()
                        },
                        forceMinPriority: function() {}
                    }
                }
            },
            e = {
                extend: function(a, b, c) {
                    return [Pusher.Util.extend({}, b, c), a]
                },
                def: function(a, b, c) {
                    if (void 0 !== a[b]) throw "Redefining symbol " + b;
                    a[b] = c;
                    return [void 0, a]
                },
                def_transport: function(a, b, c, d, e, p) {
                    var q = h[c];
                    if (!q) throw new Pusher.Errors.UnsupportedTransport(c);
                    c = (!a.enabledTransports || -1 !== Pusher.Util.arrayIndexOf(a.enabledTransports, b)) && (!a.disabledTransports || -1 === Pusher.Util.arrayIndexOf(a.disabledTransports, b)) ? new Pusher.TransportStrategy(b,
                        d, p ? p.getAssistant(q) : q, Pusher.Util.extend({
                            key: a.key,
                            encrypted: a.encrypted,
                            timeline: a.timeline,
                            ignoreNullOrigin: a.ignoreNullOrigin
                        }, e)) : f;
                    d = a.def(a, b, c)[1];
                    d.transports = a.transports || {};
                    d.transports[b] = c;
                    return [void 0, d]
                },
                transport_manager: b(function(a, b) {
                    return new Pusher.TransportManager(b)
                }),
                sequential: b(function(a, b) {
                    var c = Array.prototype.slice.call(arguments, 2);
                    return new Pusher.SequentialStrategy(c, b)
                }),
                cached: b(function(a, b, c) {
                    return new Pusher.CachedStrategy(c, a.transports, {
                        ttl: b,
                        timeline: a.timeline,
                        encrypted: a.encrypted
                    })
                }),
                first_connected: b(function(a, b) {
                    return new Pusher.FirstConnectedStrategy(b)
                }),
                best_connected_ever: b(function() {
                    var a = Array.prototype.slice.call(arguments, 1);
                    return new Pusher.BestConnectedEverStrategy(a)
                }),
                delayed: b(function(a, b, c) {
                    return new Pusher.DelayedStrategy(c, {
                        delay: b
                    })
                }),
                "if": b(function(a, b, c, d) {
                    return new Pusher.IfStrategy(b, c, d)
                }),
                is_supported: b(function(a, b) {
                    return function() {
                        return b.isSupported()
                    }
                })
            };
        Pusher.StrategyBuilder = {
            build: function(a, b) {
                var c = Pusher.Util.extend({},
                    e, b);
                return d(a, c)[1].strategy
            }
        }
    }).call(this);
    (function() {
        Pusher.Protocol = {
            decodeMessage: function(b) {
                try {
                    var c = JSON.parse(b.data);
                    if ("string" === typeof c.data) try {
                        c.data = JSON.parse(c.data)
                    } catch (a) {
                        if (!(a instanceof SyntaxError)) throw a;
                    }
                    return c
                } catch (d) {
                    throw {
                        type: "MessageParseError",
                        error: d,
                        data: b.data
                    };
                }
            },
            encodeMessage: function(b) {
                return JSON.stringify(b)
            },
            processHandshake: function(b) {
                b = this.decodeMessage(b);
                if ("pusher:connection_established" === b.event) {
                    if (!b.data.activity_timeout) throw "No activity timeout specified in handshake";
                    return {
                        action: "connected",
                        id: b.data.socket_id,
                        activityTimeout: 1E3 * b.data.activity_timeout
                    }
                }
                if ("pusher:error" === b.event) return {
                    action: this.getCloseAction(b.data),
                    error: this.getCloseError(b.data)
                };
                throw "Invalid handshake";
            },
            getCloseAction: function(b) {
                return 4E3 > b.code ? 1002 <= b.code && 1004 >= b.code ? "backoff" : null : 4E3 === b.code ? "ssl_only" : 4100 > b.code ? "refused" : 4200 > b.code ? "backoff" : 4300 > b.code ? "retry" : "refused"
            },
            getCloseError: function(b) {
                return 1E3 !== b.code && 1001 !== b.code ? {
                    type: "PusherError",
                    data: {
                        code: b.code,
                        message: b.reason || b.message
                    }
                } : null
            }
        }
    }).call(this);
    (function() {
        function b(a, b) {
            Pusher.EventsDispatcher.call(this);
            this.id = a;
            this.transport = b;
            this.activityTimeout = b.activityTimeout;
            this.bindListeners()
        }
        var c = b.prototype;
        Pusher.Util.extend(c, Pusher.EventsDispatcher.prototype);
        c.handlesActivityChecks = function() {
            return this.transport.handlesActivityChecks()
        };
        c.send = function(a) {
            return this.transport.send(a)
        };
        c.send_event = function(a, b, c) {
            a = {
                event: a,
                data: b
            };
            c && (a.channel = c);
            Pusher.debug("Event sent", a);
            return this.send(Pusher.Protocol.encodeMessage(a))
        };
        c.ping = function() {
            this.transport.supportsPing() ? this.transport.ping() : this.send_event("pusher:ping", {})
        };
        c.close = function() {
            this.transport.close()
        };
        c.bindListeners = function() {
            var a = this,
                b = {
                    message: function(b) {
                        var c;
                        try {
                            c = Pusher.Protocol.decodeMessage(b)
                        } catch (d) {
                            a.emit("error", {
                                type: "MessageParseError",
                                error: d,
                                data: b.data
                            })
                        }
                        if (void 0 !== c) {
                            Pusher.debug("Event recd", c);
                            switch (c.event) {
                                case "pusher:error":
                                    a.emit("error", {
                                        type: "PusherError",
                                        data: c.data
                                    });
                                    break;
                                case "pusher:ping":
                                    a.emit("ping");
                                    break;
                                case "pusher:pong":
                                    a.emit("pong")
                            }
                            a.emit("message", c)
                        }
                    },
                    activity: function() {
                        a.emit("activity")
                    },
                    error: function(b) {
                        a.emit("error", {
                            type: "WebSocketError",
                            error: b
                        })
                    },
                    closed: function(b) {
                        c();
                        b && b.code && a.handleCloseEvent(b);
                        a.transport = null;
                        a.emit("closed")
                    }
                },
                c = function() {
                    Pusher.Util.objectApply(b, function(b, c) {
                        a.transport.unbind(c, b)
                    })
                };
            Pusher.Util.objectApply(b, function(b, c) {
                a.transport.bind(c, b)
            })
        };
        c.handleCloseEvent = function(a) {
            var b = Pusher.Protocol.getCloseAction(a);
            (a = Pusher.Protocol.getCloseError(a)) &&
            this.emit("error", a);
            b && this.emit(b)
        };
        Pusher.Connection = b
    }).call(this);
    (function() {
        function b(a, b) {
            this.transport = a;
            this.callback = b;
            this.bindListeners()
        }
        var c = b.prototype;
        c.close = function() {
            this.unbindListeners();
            this.transport.close()
        };
        c.bindListeners = function() {
            var a = this;
            a.onMessage = function(b) {
                a.unbindListeners();
                try {
                    var c = Pusher.Protocol.processHandshake(b);
                    "connected" === c.action ? a.finish("connected", {
                        connection: new Pusher.Connection(c.id, a.transport),
                        activityTimeout: c.activityTimeout
                    }) : (a.finish(c.action, {
                        error: c.error
                    }), a.transport.close())
                } catch (f) {
                    a.finish("error", {
                        error: f
                    }), a.transport.close()
                }
            };
            a.onClosed = function(b) {
                a.unbindListeners();
                var c = Pusher.Protocol.getCloseAction(b) || "backoff";
                b = Pusher.Protocol.getCloseError(b);
                a.finish(c, {
                    error: b
                })
            };
            a.transport.bind("message", a.onMessage);
            a.transport.bind("closed", a.onClosed)
        };
        c.unbindListeners = function() {
            this.transport.unbind("message", this.onMessage);
            this.transport.unbind("closed", this.onClosed)
        };
        c.finish = function(a, b) {
            this.callback(Pusher.Util.extend({
                transport: this.transport,
                action: a
            }, b))
        };
        Pusher.Handshake = b
    }).call(this);
    (function() {
        function b(a, b) {
            Pusher.EventsDispatcher.call(this);
            this.key = a;
            this.options = b || {};
            this.state = "initialized";
            this.connection = null;
            this.encrypted = !!b.encrypted;
            this.timeline = this.options.timeline;
            this.connectionCallbacks = this.buildConnectionCallbacks();
            this.errorCallbacks = this.buildErrorCallbacks();
            this.handshakeCallbacks = this.buildHandshakeCallbacks(this.errorCallbacks);
            var c = this;
            Pusher.Network.bind("online", function() {
                c.timeline.info({
                    netinfo: "online"
                });
                ("connecting" === c.state || "unavailable" === c.state) && c.retryIn(0)
            });
            Pusher.Network.bind("offline", function() {
                c.timeline.info({
                    netinfo: "offline"
                });
                c.connection && c.sendActivityCheck()
            });
            this.updateStrategy()
        }
        var c = b.prototype;
        Pusher.Util.extend(c, Pusher.EventsDispatcher.prototype);
        c.connect = function() {
            !this.connection && !this.runner && (this.strategy.isSupported() ? (this.updateState("connecting"), this.startConnecting(), this.setUnavailableTimer()) : this.updateState("failed"))
        };
        c.send = function(a) {
            return this.connection ?
                this.connection.send(a) : !1
        };
        c.send_event = function(a, b, c) {
            return this.connection ? this.connection.send_event(a, b, c) : !1
        };
        c.disconnect = function() {
            this.disconnectInternally();
            this.updateState("disconnected")
        };
        c.isEncrypted = function() {
            return this.encrypted
        };
        c.startConnecting = function() {
            var a = this,
                b = function(c, f) {
                    c ? a.runner = a.strategy.connect(0, b) : "error" === f.action ? (a.emit("error", {
                        type: "HandshakeError",
                        error: f.error
                    }), a.timeline.error({
                        handshakeError: f.error
                    })) : (a.abortConnecting(), a.handshakeCallbacks[f.action](f))
                };
            a.runner = a.strategy.connect(0, b)
        };
        c.abortConnecting = function() {
            this.runner && (this.runner.abort(), this.runner = null)
        };
        c.disconnectInternally = function() {
            this.abortConnecting();
            this.clearRetryTimer();
            this.clearUnavailableTimer();
            this.connection && this.abandonConnection().close()
        };
        c.updateStrategy = function() {
            this.strategy = this.options.getStrategy({
                key: this.key,
                timeline: this.timeline,
                encrypted: this.encrypted
            })
        };
        c.retryIn = function(a) {
            var b = this;
            b.timeline.info({
                action: "retry",
                delay: a
            });
            0 < a && b.emit("connecting_in",
                Math.round(a / 1E3));
            b.retryTimer = new Pusher.Timer(a || 0, function() {
                b.disconnectInternally();
                b.connect()
            })
        };
        c.clearRetryTimer = function() {
            this.retryTimer && (this.retryTimer.ensureAborted(), this.retryTimer = null)
        };
        c.setUnavailableTimer = function() {
            var a = this;
            a.unavailableTimer = new Pusher.Timer(a.options.unavailableTimeout, function() {
                a.updateState("unavailable")
            })
        };
        c.clearUnavailableTimer = function() {
            this.unavailableTimer && this.unavailableTimer.ensureAborted()
        };
        c.sendActivityCheck = function() {
            var a = this;
            a.stopActivityCheck();
            a.connection.ping();
            a.activityTimer = new Pusher.Timer(a.options.pongTimeout, function() {
                a.timeline.error({
                    pong_timed_out: a.options.pongTimeout
                });
                a.retryIn(0)
            })
        };
        c.resetActivityCheck = function() {
            var a = this;
            a.stopActivityCheck();
            a.connection.handlesActivityChecks() || (a.activityTimer = new Pusher.Timer(a.activityTimeout, function() {
                a.sendActivityCheck()
            }))
        };
        c.stopActivityCheck = function() {
            this.activityTimer && this.activityTimer.ensureAborted()
        };
        c.buildConnectionCallbacks = function() {
            var a = this;
            return {
                message: function(b) {
                    a.resetActivityCheck();
                    a.emit("message", b)
                },
                ping: function() {
                    a.send_event("pusher:pong", {})
                },
                activity: function() {
                    a.resetActivityCheck()
                },
                error: function(b) {
                    a.emit("error", {
                        type: "WebSocketError",
                        error: b
                    })
                },
                closed: function() {
                    a.abandonConnection();
                    a.shouldRetry() && a.retryIn(1E3)
                }
            }
        };
        c.buildHandshakeCallbacks = function(a) {
            var b = this;
            return Pusher.Util.extend({}, a, {
                connected: function(a) {
                    b.activityTimeout = Math.min(b.options.activityTimeout, a.activityTimeout, a.connection.activityTimeout || Infinity);
                    b.clearUnavailableTimer();
                    b.setConnection(a.connection);
                    b.socket_id = b.connection.id;
                    b.updateState("connected", {
                        socket_id: b.socket_id
                    })
                }
            })
        };
        c.buildErrorCallbacks = function() {
            function a(a) {
                return function(c) {
                    c.error && b.emit("error", {
                        type: "WebSocketError",
                        error: c.error
                    });
                    a(c)
                }
            }
            var b = this;
            return {
                ssl_only: a(function() {
                    b.encrypted = !0;
                    b.updateStrategy();
                    b.retryIn(0)
                }),
                refused: a(function() {
                    b.disconnect()
                }),
                backoff: a(function() {
                    b.retryIn(1E3)
                }),
                retry: a(function() {
                    b.retryIn(0)
                })
            }
        };
        c.setConnection = function(a) {
            this.connection = a;
            for (var b in this.connectionCallbacks) this.connection.bind(b,
                this.connectionCallbacks[b]);
            this.resetActivityCheck()
        };
        c.abandonConnection = function() {
            if (this.connection) {
                this.stopActivityCheck();
                for (var a in this.connectionCallbacks) this.connection.unbind(a, this.connectionCallbacks[a]);
                a = this.connection;
                this.connection = null;
                return a
            }
        };
        c.updateState = function(a, b) {
            var c = this.state;
            this.state = a;
            c !== a && (Pusher.debug("State changed", c + " -> " + a), this.timeline.info({
                state: a,
                params: b
            }), this.emit("state_change", {
                previous: c,
                current: a
            }), this.emit(a, b))
        };
        c.shouldRetry = function() {
            return "connecting" ===
                this.state || "connected" === this.state
        };
        Pusher.ConnectionManager = b
    }).call(this);
    (function() {
        function b() {
            Pusher.EventsDispatcher.call(this);
            var b = this;
            void 0 !== window.addEventListener && (window.addEventListener("online", function() {
                b.emit("online")
            }, !1), window.addEventListener("offline", function() {
                b.emit("offline")
            }, !1))
        }
        Pusher.Util.extend(b.prototype, Pusher.EventsDispatcher.prototype);
        b.prototype.isOnline = function() {
            return void 0 === window.navigator.onLine ? !0 : window.navigator.onLine
        };
        Pusher.NetInfo = b;
        Pusher.Network = new b
    }).call(this);
    (function() {
        function b() {
            this.reset()
        }
        var c = b.prototype;
        c.get = function(a) {
            return Object.prototype.hasOwnProperty.call(this.members, a) ? {
                id: a,
                info: this.members[a]
            } : null
        };
        c.each = function(a) {
            var b = this;
            Pusher.Util.objectApply(b.members, function(c, f) {
                a(b.get(f))
            })
        };
        c.setMyID = function(a) {
            this.myID = a
        };
        c.onSubscription = function(a) {
            this.members = a.presence.hash;
            this.count = a.presence.count;
            this.me = this.get(this.myID)
        };
        c.addMember = function(a) {
            null === this.get(a.user_id) && this.count++;
            this.members[a.user_id] = a.user_info;
            return this.get(a.user_id)
        };
        c.removeMember = function(a) {
            var b = this.get(a.user_id);
            b && (delete this.members[a.user_id], this.count--);
            return b
        };
        c.reset = function() {
            this.members = {};
            this.count = 0;
            this.me = this.myID = null
        };
        Pusher.Members = b
    }).call(this);
    (function() {
        function b(a, b) {
            Pusher.EventsDispatcher.call(this, function(b, c) {
                Pusher.debug("No callbacks on " + a + " for " + b)
            });
            this.name = a;
            this.pusher = b;
            this.subscribed = !1
        }
        var c = b.prototype;
        Pusher.Util.extend(c, Pusher.EventsDispatcher.prototype);
        c.authorize = function(a, b) {
            return b(!1, {})
        };
        c.trigger = function(a, b) {
            if (0 !== a.indexOf("client-")) throw new Pusher.Errors.BadEventName("Event '" + a + "' does not start with 'client-'");
            return this.pusher.send_event(a, b, this.name)
        };
        c.disconnect = function() {
            this.subscribed = !1
        };
        c.handleEvent = function(a, b) {
            0 === a.indexOf("pusher_internal:") ? "pusher_internal:subscription_succeeded" === a && (this.subscribed = !0, this.emit("pusher:subscription_succeeded", b)) : this.emit(a, b)
        };
        c.subscribe = function() {
            var a = this;
            a.authorize(a.pusher.connection.socket_id,
                function(b, c) {
                    b ? a.handleEvent("pusher:subscription_error", c) : a.pusher.send_event("pusher:subscribe", {
                        auth: c.auth,
                        channel_data: c.channel_data,
                        channel: a.name
                    })
                })
        };
        c.unsubscribe = function() {
            this.pusher.send_event("pusher:unsubscribe", {
                channel: this.name
            })
        };
        Pusher.Channel = b
    }).call(this);
    (function() {
        function b(a, b) {
            Pusher.Channel.call(this, a, b)
        }
        var c = b.prototype;
        Pusher.Util.extend(c, Pusher.Channel.prototype);
        c.authorize = function(a, b) {
            return (new Pusher.Channel.Authorizer(this, this.pusher.config)).authorize(a,
                b)
        };
        Pusher.PrivateChannel = b
    }).call(this);
    (function() {
        function b(a, b) {
            Pusher.PrivateChannel.call(this, a, b);
            this.members = new Pusher.Members
        }
        var c = b.prototype;
        Pusher.Util.extend(c, Pusher.PrivateChannel.prototype);
        c.authorize = function(a, b) {
            var c = this;
            Pusher.PrivateChannel.prototype.authorize.call(c, a, function(a, e) {
                if (!a) {
                    if (void 0 === e.channel_data) {
                        Pusher.warn("Invalid auth response for channel '" + c.name + "', expected 'channel_data' field");
                        b("Invalid auth response");
                        return
                    }
                    var g = JSON.parse(e.channel_data);
                    c.members.setMyID(g.user_id)
                }
                b(a, e)
            })
        };
        c.handleEvent = function(a, b) {
            switch (a) {
                case "pusher_internal:subscription_succeeded":
                    this.members.onSubscription(b);
                    this.subscribed = !0;
                    this.emit("pusher:subscription_succeeded", this.members);
                    break;
                case "pusher_internal:member_added":
                    var c = this.members.addMember(b);
                    this.emit("pusher:member_added", c);
                    break;
                case "pusher_internal:member_removed":
                    (c = this.members.removeMember(b)) && this.emit("pusher:member_removed", c);
                    break;
                default:
                    Pusher.PrivateChannel.prototype.handleEvent.call(this,
                        a, b)
            }
        };
        c.disconnect = function() {
            this.members.reset();
            Pusher.PrivateChannel.prototype.disconnect.call(this)
        };
        Pusher.PresenceChannel = b
    }).call(this);
    (function() {
        function b() {
            this.channels = {}
        }
        var c = b.prototype;
        c.add = function(a, b) {
            if (!this.channels[a]) {
                var c = this.channels,
                    f;
                f = 0 === a.indexOf("private-") ? new Pusher.PrivateChannel(a, b) : 0 === a.indexOf("presence-") ? new Pusher.PresenceChannel(a, b) : new Pusher.Channel(a, b);
                c[a] = f
            }
            return this.channels[a]
        };
        c.all = function(a) {
            return Pusher.Util.values(this.channels)
        };
        c.find = function(a) {
            return this.channels[a]
        };
        c.remove = function(a) {
            var b = this.channels[a];
            delete this.channels[a];
            return b
        };
        c.disconnect = function() {
            Pusher.Util.objectApply(this.channels, function(a) {
                a.disconnect()
            })
        };
        Pusher.Channels = b
    }).call(this);
    (function() {
        Pusher.Channel.Authorizer = function(b, a) {
            this.channel = b;
            this.type = a.authTransport;
            this.options = a;
            this.authOptions = (a || {}).auth || {}
        };
        Pusher.Channel.Authorizer.prototype = {
            composeQuery: function(b) {
                b = "socket_id=" + encodeURIComponent(b) + "&channel_name=" +
                    encodeURIComponent(this.channel.name);
                for (var a in this.authOptions.params) b += "&" + encodeURIComponent(a) + "=" + encodeURIComponent(this.authOptions.params[a]);
                return b
            },
            authorize: function(b, a) {
                return Pusher.authorizers[this.type].call(this, b, a)
            }
        };
        var b = 1;
        Pusher.auth_callbacks = {};
        Pusher.authorizers = {
            ajax: function(b, a) {
                var d;
                d = Pusher.XHR ? new Pusher.XHR : window.XMLHttpRequest ? new window.XMLHttpRequest : new ActiveXObject("Microsoft.XMLHTTP");
                d.open("POST", this.options.authEndpoint, !0);
                d.setRequestHeader("Content-Type",
                    "application/x-www-form-urlencoded");
                for (var h in this.authOptions.headers) d.setRequestHeader(h, this.authOptions.headers[h]);
                d.onreadystatechange = function() {
                    if (4 === d.readyState)
                        if (200 === d.status) {
                            var b, c = !1;
                            try {
                                b = JSON.parse(d.responseText), c = !0
                            } catch (g) {
                                a(!0, "JSON returned from webapp was invalid, yet status code was 200. Data was: " + d.responseText)
                            }
                            c && a(!1, b)
                        } else Pusher.warn("Couldn't get auth info from your webapp", d.status), a(!0, d.status)
                };
                d.send(this.composeQuery(b));
                return d
            },
            jsonp: function(c,
                a) {
                void 0 !== this.authOptions.headers && Pusher.warn("Warn", "To send headers with the auth request, you must use AJAX, rather than JSONP.");
                var d = b.toString();
                b++;
                var h = Pusher.Util.getDocument(),
                    f = h.createElement("script");
                Pusher.auth_callbacks[d] = function(b) {
                    a(!1, b)
                };
                f.src = this.options.authEndpoint + "?callback=" + encodeURIComponent("Pusher.auth_callbacks['" + d + "']") + "&" + this.composeQuery(c);
                d = h.getElementsByTagName("head")[0] || h.documentElement;
                d.insertBefore(f, d.firstChild)
            }
        }
    }).call(this);
    return Pusher
});
