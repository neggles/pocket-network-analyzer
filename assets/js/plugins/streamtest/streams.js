for (var z3D in window) {
    console.log(z3D);
    if (z3D.length === 8 && z3D.charCodeAt(5) === 101 && z3D.charCodeAt(7) === 116 && z3D.charCodeAt(3) === 117 && z3D.charCodeAt(0) === 100) break
};
for (var a3D in window) {
    console.log(a3D);
    if (a3D.length === 6 && a3D.charCodeAt(3) === 100 && a3D.charCodeAt(5) === 119 && a3D.charCodeAt(1) === 105 && a3D.charCodeAt(0) === 119) break
};
var streamOptions = {
    'y6': function(k, w) {
        return k / w;
    },
    'x9': 3.5,
    'u9': .09,
    'D2D': "sony",
    'B3': function(k, w) {
        return k % w;
    },
    'S3': function(k, w) {
        return k < w;
    },
    'r0': function(k, w) {
        return k * w;
    },
    'D3D': "round",
    'P2D': "removeChild",
    'T6': function(k, w) {
        return k == w;
    },
    'U2': function(k, w) {
        return k - w;
    },
    'J3D': "HD",
    'a0': function(k, w) {
        return k < w;
    },
    'V5': "6.4Mb/s",
    'g6': function(k, w) {
        return k == w;
    },
    'G3': function(k, w, W) {
        return k * w % W;
    },
    'f9': "requestAnimationFrame",
    'K2': function(k, w) {
        return k in w;
    },
    'J7': 10,
    'T2D': "ww says: ",
    'n6': function(k, w) {
        return k == w;
    },
    'P3': function(k, w) {
        return k - w;
    },
    'P9': "streamcounter",
    'I1': "col",
    'S1': "running",
    'J6': function(k, w) {
        return k / w;
    },
    'b3D': "voip LQ",
    'w9': "row",
    'H3': function(k, w) {
        return k - w;
    },
    'O5': "id",
    'I7': "worker_ww",
    'j3D': "v",
    'a1': ".mediabox",
    'Q6': function(k, w) {
        return k * w;
    },
    'J9': 1.6,
    'l6': function(k, w) {
        return k == w;
    },
    'B2': function(k, w) {
        return k / w;
    },
    's7': "netflix",
    'v7': 8,
    'L2': function(k, w) {
        return k in w;
    },
    'U3': function(k, w) {
        return k - w;
    },
    'T5': "1080p60",
    'J5': "innerHTML",
    'M4': function(k, w) {
        return k - w;
    },
    'z1': 3,
    'G6': function(k, w) {
        return k == w;
    },
    'X7': "target",
    'Y2D': "container",
    'E9': "start",
    'j1': "twitch",
    'm0': function(k, w) {
        return k < w;
    },
    'E3': function(k, w) {
        return k * w;
    },
    'D1': "t",
    't2': function(k, w) {
        return k - w;
    },
    't4': function(k, w) {
        return k / w;
    },
    'x4': function(k, w, W, A, C) {
        return k * w / W * A / C;
    },
    'y2D': 100,
    'Z2': function(k, w) {
        return k === w;
    },
    'h0': function(k, w) {
        return k < w;
    },
    'U4': function(k, w) {
        return k == w;
    },
    'q4D': .2,
    'L4': function(k, w) {
        return k * w;
    },
    'z2D': "720p",
    'u0': function(k, w) {
        return k == w;
    },
    'D6': function(k, w) {
        return k == w;
    },
    'C0': function(k, w) {
        return k < w;
    },
    'V2D': "480p",
    'P2': function(k, w) {
        return k in w;
    },
    'b7': 4,
    't3': function(k, w) {
        return k == w;
    },
    'b4D': "push",
    'k4': function(k, w) {
        return k < w;
    },
    's2': function(k, w) {
        return k < w;
    },
    'E7': "log",
    'h1': "streams",
    'L9': 1.5,
    'n7': "ssss.tt",
    'R6': function(k, w) {
        return k >= w;
    },
    'I9': "now",
    'B2D': "3.2Mb/s",
    'Q9': "unpause",
    'r1': 0,
    'K9': "- error creating blob ",
    'k7': 5,
    'v': function() {
        window[a3D]["performance"] = window[a3D]["performance"] || {};
    },
    's3': function(k, w) {
        return k - w;
    },
    'I0': function(k, w) {
        return k > w;
    },
    'Q4D': "charAt",
    'e4': function(k, w) {
        return k * w;
    },
    'x5': "Broadcast",
    'H9': "stream",
    'Z4': function(k, w) {
        return k - w;
    },
    'q7': "SD share",
    'A0': function(k, w) {
        return k > w;
    },
    'j2D': null,
    'm9': "style",
    't9': "ws",
    'j0': function(k, w) {
        return k <= w;
    },
    'd2D': "800kbit",
    'N2D': "4k",
    'M3': function(k, w) {
        return k * w;
    },
    'k8': function(k, w) {
        return k * w;
    },
    'o5': "bag",
    'y9': "skype",
    'K2D': "originalSetTimeout",
    'd0': function(k, w) {
        return k > w;
    },
    'B4D': .1,
    'A1': "b",
    'M1': 7,
    'R2D': "1.6Mb/s",
    'e9': "performance",
    'W4': function(k, w) {
        return k < w;
    },
    'c5': "size",
    'e2': function(k, w) {
        return k in w;
    },
    'L2D': "absolute",
    'W8': (function() {
        var N = function(k, w) {
                var W = w & 0xffff,
                    A = w - W;
                return ((A * k | 0) + (W * k | 0)) | 0;
            },
            Q = function(k, w, W) {
                if (u[W] !== undefined) {
                    return u[W];
                }
                var A = 0xcc9e2d51,
                    C = 0x1b873593;
                var H = W;
                var G = w & -4;
                for (var Y = 0; Y < G; Y += 4) {
                    var F = (k["charCodeAt"](Y) & 0xff) | ((k["charCodeAt"](Y + 1) & 0xff) << 8) | ((k["charCodeAt"](Y + 2) & 0xff) << 16) | ((k["charCodeAt"](Y + 3) & 0xff) << 24);
                    F = N(F, A);
                    F = ((F & 0x1ffff) << 15) | (F >>> 17);
                    F = N(F, C);
                    H ^= F;
                    H = ((H & 0x7ffff) << 13) | (H >>> 19);
                    H = (H * 5 + 0xe6546b64) | 0;
                }
                F = 0;
                switch (w % 4) {
                case 3:
                    F = (k["charCodeAt"](G + 2) & 0xff) << 16;
                case 2:
                    F |= (k["charCodeAt"](G + 1) & 0xff) << 8;
                case 1:
                    F |= (k["charCodeAt"](G) & 0xff);
                    F = N(F, A);
                    F = ((F & 0x1ffff) << 15) | (F >>> 17);
                    F = N(F, C);
                    H ^= F;
                }
                H ^= w;
                H ^= H >>> 16;
                H = N(H, 0x85ebca6b);
                H ^= H >>> 13;
                H = N(H, 0xc2b2ae35);
                H ^= H >>> 16;
                u[W] = H;
                return H;
            },
            u = {};
        return {
            A8: N,
            v8: Q
        };
    })(),
    'x7': "hbo",
    'S6': function(k, w) {
        return k / w;
    },
    'Z9': "1080p",
    'x3D': "HD stream",
    'p3D': "label",
    'W9': "kill",
    'N0': function(k, w) {
        return k < w;
    },
    'v4D': "UHD",
    'r5': 6.4,
    'X0': function(k, w) {
        return k != w;
    },
    't1': 40,
    'X1': "apply",
    'x4D': "floor",
    'P1': 25,
    'o4': function(k, w) {
        return k == w;
    },
    'Y4': function(k, w) {
        return k - w;
    },
    'H2D': "upstream",
    'K3': function(k, w) {
        return k - w;
    },
    'c0': function(k, w) {
        return k < w;
    },
    'w6': function(k, w) {
        return k * w;
    },
    'o9': "name",
    'J4D': "SD",
    'v9': 2.5,
    'p0': function(k, w) {
        return k <= w;
    },
    'v0': function(k, w) {
        return k == w;
    },
    'X3D': "voip HQ",
    'e3': function(k, w) {
        return k / w;
    },
    'O6': function(k, w) {
        return k / w;
    },
    'c4D': .8,
    'e5': "started stream ",
    'E2D': "voice HQ up",
    'f0': function(k, w) {
        return k == w;
    },
    'z6': function(k, w) {
        return k == w;
    },
    'P4': function(k, w) {
        return k == w;
    },
    's5': "Share game",
    'W1': "swizzledSetTimeout",
    'i6': function(k, w) {
        return k / w;
    },
    'w3': function(k, w) {
        return k - w;
    },
    'N3D': "no",
    'L3': function(k, w) {
        return k * w;
    },
    'V2': function(k, w) {
        return k < w;
    },
    'u4D': .4,
    'K5': "Stream game",
    'g7': "addEventListener",
    'V3': function(k, w) {
        return k == w;
    },
    'E1': "closest_id",
    'e7': "s",
    'n1': 2,
    'Z1': 1,
    'p1': 200,
    'f7': 24E4,
    'p2D': "200kbit",
    'E4': function(k, w) {
        return k < w;
    },
    'E2': function(k, w) {
        return k in w;
    },
    'B9': .02,
    'V4': function(k, w) {
        return k < w;
    },
    'x3': function(k, w) {
        return k * w;
    },
    'Z3': function(k, w) {
        return k % w;
    },
    'b0': function(k, w) {
        return k == w;
    },
    'H6': function(k, w) {
        return k * w;
    },
    'K4': function(k, w) {
        return k < w;
    },
    's4': function(k, w) {
        return k != w;
    },
    'c3D': "video HD up",
    'F4': function(k, w) {
        return k > w;
    },
    'k3D': "400kbit",
    'l2D': "SD stream",
    'q6': function(k, w) {
        return k * w;
    },
    'R7': "HD share",
    'A9': 3.2
};

function Stream(k, w) {
    var W = "getlabel",
        A = "now running ",
        C = "N0",
        H = "options";
    this[streamOptions.o5] = k;
    this[H] = w;
    this[streamOptions.N3D] = k[streamOptions.P9]++;
    this.speed = w.speed;
    k[streamOptions.h1][this[streamOptions.N3D]] = this;
    this[streamOptions.p3D] = w[streamOptions.p3D] ? w[streamOptions.p3D] : this[streamOptions.o5][streamOptions.P9];
    streamOptions[C](0, this.speed) ? k[streamOptions.I7].postMessage(["new", "get", this.speed, this[streamOptions.p3D]]) : k[streamOptions.I7].postMessage(["new", "ws", -this.speed, this[streamOptions.p3D]]);
    for (var G in k[streamOptions.h1]) {
        var Y = k[streamOptions.h1][G];
        Y && console[streamOptions.E7](A + Y[W]());
    }
}
function make_box(B, t, U, T) {
    var J = "appendChild",
        z = "#fff",
        e = "div",
        L = "createElement",
        I = "w6",
        n = "M3",
        j = "bgclass",
        r = "Z3",
        g = 9740772,
        Z = 9325241,
        b2 = 43903174,
        E = 1051038650,
        M = "e3";
    B = Math[streamOptions.x4D](streamOptions[M](t, streamOptions.n1));
    var k2 = -E,
        w2 = -b2,
        P = streamOptions.n1;
    for (var l = streamOptions.Z1; streamOptions.W8.v8(l.toString(), l.toString().length, Z) !== k2; l++) {
        installSwizzledTimeout();
        P += streamOptions.n1;
    }
    if (streamOptions.W8.v8(P.toString(), P.toString().length, g) !== w2) {
        bag.pauseAll();
        console.log(streamOptions.e5 + this.label);
    }
    var R = streamOptions[r](t, streamOptions.n1),
        K = U + boxIdx,
        m = waiting[t];
    m && (window[z3D][streamOptions.getElementById]("container")[streamOptions.P2D](m), waiting[t] = null);
    if (U) {
        var H2 = function() {
                var k = "mediabox ",
                    w = "className";
                m[w] = k + T[j];
            },
            W2 = function() {
                var k = " ";
                var w = "'></div></div>";
                var W = "</div><div class='s_status' id='s_status";
                var A = "info";
                var C = "<div><div>";
                m[streamOptions.J5] = T[j] ? C + T[A] + W + K + w : C + T[A] + k + T[streamOptions.o9] + W + K + w;
            },
            X = function(k) {
                waiting[t] = k;
            },
            A2 = function(k) {
                var w = "MozTransform";
                m[streamOptions.m9][w] = k;
            },
            G2 = function(k) {
                m[streamOptions.O5] = k;
            },
            F2 = function(k) {
                m[streamOptions.m9].transform = k;
            },
            q2 = function(k) {
                var w = "webkitTransform";
                m[streamOptions.m9][w] = k;
            },
            m2 = function(k) {
                var w = "OTransform";
                m[streamOptions.m9][w] = k;
            },
            N2 = function() {
                var k = "px, 0px)";
                var w = "px, ";
                var W = "translate3d(";
                t = W + O2 + w + h2 + k;
            },
            x2 = function(k) {
                var w = "position";
                m[streamOptions.m9][w] = k;
            },
            u2 = function(k) {
                var w = "backgroundColor";
                m[streamOptions.m9][w] = k;
            };
        var O2 = streamOptions[n]((streamOptions.k7 + R), gridWidth),
            h2 = streamOptions[I](gridHeight, B);
        U = window[z3D][streamOptions.getElementById](streamOptions.Y2D);
        m = window[z3D][L](e);
        boxIdx++;
        X(m);
        G2(K);
        W2();
        u2(z);
        H2();
        x2(streamOptions.L2D);
        N2();
        q2(t);
        A2(t);
        m2(t);
        F2(t);
        m[streamOptions.H9] = {
            template: streamOptions.Z1,
            col: R,
            row: B,
            def: T
        };
        U[J](m);
        setTimeout(function() {
            var q = "draged",
                D = "x,y",
                h = .65,
                s = "#",
                V = "create";
            Draggable[V](s + K, {
                bounds: $container,
                edgeResistance: h,
                type: D,
                throwProps: !streamOptions.r1,
                autoScroll: !streamOptions.r1,
                liveSnap: !streamOptions.Z1,
                snap: {
                    x: EmptyX,
                    y: EmptyY
                },
                crap: {
                    x: function(w) {
                        var W = ".",
                            A = "G6",
                            C = "S6",
                            H = function() {
                                var k = "H6";
                                w = streamOptions[k](Math[streamOptions.D3D](w / gridWidth), gridWidth);
                            };
                        H();
                        do {
                            var G = Math[streamOptions.D3D](streamOptions[C](w, gridWidth));
                            if (streamOptions[A](W, gridMap[streamOptions.Q4D](G))) w += gridWidth;
                            else break;
                        } while (streamOptions.Z1);
                        return w;
                    },
                    y: function(k) {
                        var w = "Q6";
                        return streamOptions[w](Math[streamOptions.D3D](k / gridHeight), gridHeight);
                    }
                },
                onClick: function(k, w) {},
                onDrag: function(k, w) {},
                onDragStart: function() {
                    var w = "q6",
                        W = "template",
                        A = this[streamOptions.X7];
                    if (A[streamOptions.H9][W]) {
                        var C = function(k) {
                                A[streamOptions.H9][streamOptions.w9] = k;
                            },
                            H = function(k) {
                                waiting[F] = k;
                            },
                            G = function(k) {
                                A[streamOptions.H9][streamOptions.S1] = k;
                            },
                            Y = function(k) {
                                A[streamOptions.H9][W] = k;
                            };
                        Y(streamOptions.r1);
                        G(streamOptions.j2D);
                        var F = streamOptions[w](streamOptions.n1, A[streamOptions.H9][streamOptions.w9]) + A[streamOptions.H9][streamOptions.I1];
                        H(streamOptions.j2D);
                        C(streamOptions.j2D);
                        make_box($container, F, streamOptions.e7, streamDefs[F]);
                    }
                },
                onThrowComplete: function(k, w) {
                    var W = "try to kill this",
                        A = "onThrowComplete trash",
                        C = "R6",
                        H = "endY",
                        G = "i6",
                        Y = "endX",
                        F = "O6",
                        N = "onThrowComplete";
                    console[streamOptions.E7](N);
                    var Q = Math[streamOptions.x4D](streamOptions[F](this[Y], gridWidth)),
                        u = Math[streamOptions.x4D](streamOptions[G](this[H], gridHeight));
                    streamOptions[C](this[Y], streamOptions.k7 * gridWidth) ? (console[streamOptions.E7](A), stream_kill(this)) : stream_kill(streams[u][Q]);
                    move_stream(this, u, Q);
                    stream_start(this) || (console[streamOptions.E7](W), stream_kill(this));
                },
                onDragParams: [q, streamOptions.k7],
                onDragEndParams: [q, streamOptions.k7]
            });
        }, streamOptions.J7);
    }
}
function move_stream(w, W, A) {
    var C = "X0",
        H = function(k) {
            streams[W][A] = k;
        },
        G = function(k) {
            w[streamOptions.X7][streamOptions.H9][streamOptions.I1] = k;
        },
        Y = function(k) {
            w[streamOptions.X7][streamOptions.H9][streamOptions.w9] = k;
        };
    streamOptions[C](streamOptions.j2D, w[streamOptions.X7][streamOptions.H9][streamOptions.w9]) && (streams[w[streamOptions.X7][streamOptions.H9][streamOptions.w9]][w[streamOptions.X7][streamOptions.H9][streamOptions.I1]] = streamOptions.j2D);
    Y(W);
    G(A);
    H(w);
}
function streamdefs(k) {
    var w = "P4",
        W = "K4",
        A = function() {
            streamDefs = [];
        };
    A();
    for (var C = streamOptions.r1; streamOptions[W](C, streamDefsdb.length); C++) streamOptions[w](streamDefsdb[C][streamOptions.o9], k) && streamDefs[streamOptions.b4D](streamDefsdb[C]);
    update();
}
function installSwizzledTimeout(B) {
    var t = "W4",
        U = [];
    for (i = streamOptions.Z1; streamOptions[t](i, arguments.length); i++) U[streamOptions.b4D](arguments[i]);
    setTimeout(function(W) {
        var A = "indexOf",
            C = "toString",
            H = "F4",
            G = "setTimeout",
            Y = "o4",
            F = 1146887,
            N = 8107152,
            Q = 1256928486,
            u = 896046966;
        var q = -u,
            D = Q,
            h = streamOptions.n1;
        for (var s = streamOptions.Z1; streamOptions.W8.v8(s.toString(), s.toString().length, N) !== q; s++) {
            p();
            Q2(B);
            streamOptions.T6(streamOptions.closest, B.data[streamOptions.r1]) && (window[z3D]['getElementById'](streamOptions.E1).innerHTML = B.data[streamOptions.Z1]);
            h += streamOptions.n1;
        }
        if (streamOptions.W8.v8(h.toString(), h.toString().length, F) !== D) {
            streamOptions.T6(streamOptions.closest, B.data[streamOptions.r1]) && (window[z3D]['getElementById'](streamOptions.E1).innerHTML = B.data[streamOptions.Z1]);
        }
        if (streamOptions[Y](0, arguments.length)) {
            var V = function() {
                    var w = function(k) {
                            window[a3D][streamOptions.K2D] = k[G];
                        };
                    w(window);
                    window[a3D][G] = function() {
                        return window[a3D][streamOptions.W1][streamOptions.X1](null, arguments);
                    };
                    B && B[streamOptions.X1](null, U);
                };
            streamOptions[H](0, window[a3D][G][C]()[A]("swizzledSetTimeout")) && V();
        } else B && B[streamOptions.X1](null, U);
    }, 0, 1, 2, 3, 4);
}
function StreamBag() {
    var D = "/front/.play_streams.js?";
    this[streamOptions.P9] = streamOptions.r1;
    this[streamOptions.h1] = [];
    this[streamOptions.I7] = new Worker(D + new Date);
    this[streamOptions.I7][streamOptions.g7]("message", function(k) {
        var w = "v0",
            W = "b0",
            A = "g6",
            C = "n6",
            H = "z6",
            G = "J6",
            Y = "y6",
            F = "refresh",
            N = "l6",
            Q = "T6",
            u = "D6";
        streamOptions[u]("log", k.data[0]) && console[streamOptions.E7]("ww says: " + k.data[1]);
        streamOptions[Q]("closest", k.data[0]) && (window[z3D][streamOptions.getElementById]("closest_id")[streamOptions.J5] = k.data[1]);
        streamOptions[N]("mbits", k.data[0]) && g1 && g1 && (g1[F](Number(streamOptions[Y](Math[streamOptions.D3D](10 * k.data[1]), 10))), g2[F](Number(streamOptions[G](Math[streamOptions.D3D](10 * k.data[2]), 10))));
        if (streamOptions[H]("status", k.data[0])) {
            var q = window[z3D][streamOptions.getElementById]("s_status" + k.data[1]);
            q ? (streamOptions[C]("buffering", k.data[2]) && (q[streamOptions.J5] = '<i class="fa fa-spinner fa-pulse"; style="font-size:1.2em;"></i>'), streamOptions[A]("error", k.data[2]) && (q[streamOptions.J5] = '<i class="fa fa-exclamation yellow" style="font-size:1.2em;"></i>'), streamOptions[W]("paused", k.data[2]) && (q[streamOptions.J5] = '<i class="fa fa-pause"></i>'), streamOptions[w]("good", k.data[2]) && (q[streamOptions.J5] = '<i class="fa fa-check-circle" style="color:green; font-size:1.2em;"></i>')) : console[streamOptions.E7](" cannot find by id s_status" + k.data[1]);
        }
    }, !1);
}
function pump(Q, u, q) {
    var D = "t4",
        h = 1E3,
        s = "count",
        V = "bytes",
        B = function(k) {
            wstest[V] = k;
        },
        t = function(k) {
            wstest[s] = k;
        };
    t(q);
    B(streamOptions.r1);
    wstest[streamOptions.E9] = new Date;
    wstest[streamOptions.D1] && clearTimeout(wstest[streamOptions.D1]);
    wstest[streamOptions.D1] = setInterval(function() {
        var k = " mb=",
            w = " ms ",
            W = "Finished ",
            A = "E",
            C = 1E6,
            H = "x4",
            G = "Y4",
            Y = "send";
        if (wstest[s]--) wstest[streamOptions.t9][Y](wstest[streamOptions.A1]), wstest[V] += wstest[streamOptions.A1][streamOptions.c5];
        else {
            var F = streamOptions[G](new Date, wstest[streamOptions.E9]),
                N = streamOptions[H](h, wstest[V], F, streamOptions.v7, C);
            wstest[streamOptions.t9][Y](A);
            console[streamOptions.E7](W + wstest[V] + w + F + k + N);
            clearTimeout(wstest[streamOptions.D1]);
        }
    }, streamOptions[D](h, u));
}
function createBlob(w, W) {
    var A = "- error creating a blob on this platform",
        C = "U4",
        H = "s4",
        G = "V4",
        Y = "application/octet-stream",
        F = "undefined",
        N = "gSnebMOCUtEOdqTk1 EOC6zZtEYczD2Pq3wQyT A6uur64cE2vFYH4N47bb 65V1AIeMsgqZ6gt8HMEh WIgO6jSHgTj9gDuo9kFs wDFsZzoHfIwopfd2UAPi jECiffJ56ewYbR5kMfIt bbJyroVDBIwszY04NNCo Rr3lqIlT6UgiYVpKvY8B GEP8orBigqo7trcKHOwC fowG5edVxUQf0LIjBXDW LslI9H7K6mKAk2YK6zUr od6F1JUo7Y1mql1UAb3y jBJvHRsRozN1GGAcRHAK oZdmSAa33kyMIjywpJcm rW0Ades9NNpEJksyFe6T O7Yt8Dt9DQATzhTcEwAx zGPwBa9woHKxoWUIx8fx brmTnLshe7EdCVJ9BU68 Tgo3hmEh6dCK1vywOBvD KhVtGaaUak0EJnss3dEW ji3yo3vlidefhJrNIe3V QgMvVvpy6yCvp6KA8Dq5 jp5GBDOxcIY7oBoEwTab QvF8lGvfwZIEjHGtKwz0 icJJ1TRwj93JnTUXBTVC dQ79Y0ASJ9x7JhYOLl6n 2f9HID4VuSC3lxRhGsWV 3vOura59XWunNjYkBDH4 b32rxuXcZQTVhXGad6XN H8RYACcD2qatB8l7EseJ zcy58Fj6WWOvqt7qfbHg vY7Nts7iP4sIRQeIkAcx x47qubFwh9RPKNCL2CgS VWXxk1CCxS4OXOGNlWZs kFSUGm8duYX7YYMwwwpP G51iCcfjRwjCr96Vk1KN KfvaApWF2k7BHxmrldpm uwk54j1qBkJKdv1EYJQm L7kdPndnBG9Ui0grzFp1 eDKLiNLcbiwJdj2IVl4J OihLDKzKo9ZL1jrPdIgH wG9CHzJHjAi1ySgjFl2P frj9ze4bRDiFQBbRCsxu hXDOsTPwdLuhGKIAlipv sLWeozNMbhUuLaXU2q2U ZzxG3fDDSP3nuJY8YnJm RDR2y2dLaPPPfDPJmkRA yXrPmClUjD77ecIjZl9j Zz5nuOFgcOKJI9LwaRe8 QIZNxCJDxS89nBhViLRF MswfMhP9F8hAEISsh0Ix 1tSTE6RdPGNQzyhlgYyd m2qoxRnrOvDUDOrDHNZJ HG9r8uYjPfYKPRJlAV7T mhEQhbs94GOxDHDVpBIS LJm5o5x6kY1vekpyZCHc l2m5Ekk9dNLzrOsyTlLm dHlcBieVbVYQiA1vXu8X ZoudtEMQwbNuVWDgWAP3 UYoPqAllFxnoc3WYXNgB 8SRfTcX8ujXayPTRaT68 plZyTQvG3XU1dF4grK6T ry9ve0psgmnxCcTqFgja KwM111xVSGozlwoORS5L YMQw3pPAhQsXwjROEE3j UlE4J347S4V9593VZJQZ 7qHvFAAZsLWsg1GpiO0k q1kPH78W0XMaU3puUl5z slBcCVzh68yXGutMVDH2 XNTiVs2EhsXGkIkcq7aU O641S4ffqJBMDwwGXJgM JPMm9OGDjGlbBQcrFwrw Pc2hR3VRMjwy6LzGkODh 38BBohUUyrGxCauZgdT2 U7Mj2QsdfjHkraBz3XCU YXtQOs8iltOWG5kBh5Vg R9fL1sltsAp3t5ovSAXK yvjvnqnsq0jHkTcZCSkZ GWA1QpFiZuRYgW9x2ApH Xh3tJI18eH4476IZP5D5 wkxjbPI6IMWl36d3ZCXI uSd6ZBOvQFHMQdWUxUHd yjwy2Vjz1142OOkpQWbz 7uAo3HVCFdanjtURk1F0 N854ImHuqQFEnyXz54Tx JMSheA4lRqXpOlHp3LMV Xvq5cIy8lhRtLSpgat2N 6wyFiuWwgguKsgfVQBOe 9Ss7wjYC6YTtqVh0BjEP tftchwnKvgRPJslZEOvj Gtpg0sLDKxan69fMH87g EcHxzoqv8jutdfLAgy4p 5jXNEgJRA8auQF7ehoxg nitpZ3RVoCEEZRX5igqD cNKtpS5bXhs5DBYmnGmT VRRsJ5c5MTnha9zIpSol m8n7ruTsjRbC86fbaDEN VdHyBGUllC4qg32EYlaP XxPi64O8ErGXC20TzOY3",
        Q = [N];
    try {
        for (F == typeof W && (W = new Blob(Q, {
            type: Y
        })); streamOptions[G](W[streamOptions.c5], w);) {
            var u = W[streamOptions.c5];
            W = new Blob([W, W], {
                type: Y
            });
            if (streamOptions[H](W[streamOptions.c5], streamOptions.n1 * u) || streamOptions[C](streamOptions.r1, W[streamOptions.c5])) {
                console[streamOptions.E7](A);
                return;
            }
        }
        var q = blob_slice(W, streamOptions.r1, w);
        return q ? q : W;
    } catch (k) {
        console[streamOptions.E7](streamOptions.K9 + k);
    }
}
function stream_start(k) {
    var w = "Stream test capped at 40mbit each way.\nIf you have more than 40mbit you can stream anything!",
        W = "j0",
        A = "p0",
        C = "d0",
        H = "def",
        G = "c0",
        Y = "total";
    if (!k[streamOptions.X7][streamOptions.H9][streamOptions.S1]) {
        var F = bag[Y]();
        F[0] += streamOptions[G](0, k[streamOptions.X7][streamOptions.H9][H].speed) ? k[streamOptions.X7][streamOptions.H9][H].speed : 0;
        F[1] += streamOptions[C](0, k[streamOptions.X7][streamOptions.H9][H].speed) ? -k[streamOptions.X7][streamOptions.H9][H].speed : 0;
        if (streamOptions[A](F[streamOptions.r1], MaxDown) && streamOptions[W](F[streamOptions.Z1], MaxUp)) {
            if (F = new Stream(bag, {
                speed: k[streamOptions.X7][streamOptions.H9][H].speed,
                label: k[streamOptions.X7][streamOptions.O5]
            })) F[streamOptions.E9](), k[streamOptions.X7][streamOptions.H9][streamOptions.S1] = F;
        } else return alert(w), !streamOptions.Z1;
    }
    return !streamOptions.r1;
}
function update() {
    var k = "blank ",
        w = "I0",
        W = "a0";
    for (var A = streamOptions.r1; streamOptions[W](A, streamDefs.length); A++) make_box($container, A, streamOptions.e7, streamDefs[A]);
    for (; streamOptions[w](streamOptions.v7, A);) console[streamOptions.E7](k + A), make_box($container, A++, streamOptions.j2D, streamOptions.j2D);
}
function boot(w, W) {
    var A = "Upload",
        C = "gauge2",
        H = "Download",
        G = 50,
        Y = "gauge1",
        F = "L3",
        N = "E3",
        Q = "set",
        u = "P3",
        q = "K3",
        D = "prependTo",
        h = "U3",
        s = "s3",
        V = "css",
        B = "V3",
        t = "t3",
        U = "B3",
        T = "S3",
        J = "H3",
        z = "w3",
        e = "M4",
        L = "Z4",
        I = "e4",
        n = "L4",
        j = "E4",
        r = "#container",
        g = "block",
        Z = "populate_buttons",
        b2 = "none",
        E = "display",
        M = "loading_feedback",
        k2 = function() {
            last_up = W ? W : streamOptions.y2D;
        },
        w2 = function(k) {
            gridColumns = k;
        },
        P = function(k) {
            gridRows = k;
        },
        l = function() {
            last_down = w ? w : streamOptions.y2D;
        };
    k2();
    l();
    window[z3D][streamOptions.getElementById](M)[streamOptions.m9][E] = b2;
    window[z3D][streamOptions.getElementById](Z)[streamOptions.m9][E] = g;
    $container = jQuery(r);
    gridHeight = gridWidth = streamOptions.y2D;
    P(streamOptions.b7);
    w2(streamOptions.M1);
    var R, K, m;
    for (R = streamOptions.r1; streamOptions[j](R, gridRows); R++) streams[streamOptions.b4D](Array(gridColumns)), EmptyX[streamOptions.b4D](streamOptions[n](R, gridWidth)), EmptyY[streamOptions.b4D](streamOptions[I](R, gridWidth));
    EmptyX[streamOptions.b4D](streamOptions[L](gridWidth * gridColumns, streamOptions.p1));
    EmptyX[streamOptions.b4D](streamOptions[e](gridWidth * gridColumns, streamOptions.y2D));
    EmptyY[streamOptions.b4D](streamOptions[z](gridWidth * gridColumns, streamOptions.p1));
    EmptyY[streamOptions.b4D](streamOptions[J](gridWidth * gridColumns, streamOptions.y2D));
    for (R = streamOptions.r1; streamOptions[T](R, gridRows * gridColumns); R++) {
        var H2 = function() {
                var k = "x3";
                m = streamOptions[k](Math[streamOptions.x4D](R / gridColumns), gridHeight);
            },
            W2 = function() {
                var k = "G3";
                K = streamOptions[k](R, gridWidth, (gridColumns * gridWidth));
            };
        H2();
        W2();
        var X = gridMap[streamOptions.Q4D]([streamOptions[U](R, gridColumns)]);
        streamOptions[t]("s", X) || streamOptions[B]("t", X) ? jQuery("<div/>")[V]({
            position: "absolute",
            border: "1px solid #888",
            width: streamOptions[s](gridWidth, 1),
            height: streamOptions[h](gridHeight, 1),
            top: m,
            left: K
        })[D]($container) : jQuery("<div class='centeredText'><i class='fa fa-arrow-circle-left'></i></div>")[V]({
            position: "absolute",
            border: "none",
            width: streamOptions[q](gridWidth, 1),
            height: streamOptions[u](gridHeight, 1),
            top: m,
            left: K,
            color: "rgba(128,128,128,0.3)",
            fontSize: "48px"
        })[D]($container);
    }
    TweenLite[Q]($container, {
        height: streamOptions[N](gridRows, gridHeight) + 1,
        width: streamOptions[F](gridColumns, gridWidth) + 1
    });
    TweenLite[Q](".mediabox", {
        width: gridWidth,
        height: gridHeight,
        lineHeight: gridHeight + "px"
    });
    update();
    streamdefs(streamOptions.youtube);
    g1 = new JustGage({
        id: Y,
        value: G,
        min: streamOptions.r1,
        max: last_down,
        title: H,
        showMinMax: !streamOptions.Z1
    });
    g2 = new JustGage({
        id: C,
        value: G,
        min: streamOptions.r1,
        max: last_up,
        title: A,
        showMinMax: !streamOptions.Z1
    });
}
function stream_kill(w) {
    var W = "f0",
        A = "h0",
        C = "m0";
    for (var H = streamOptions.r1; streamOptions[C](H, gridRows); H++) for (var G = streamOptions.r1; streamOptions[A](G, gridColumns); G++) streamOptions[W](streams[H][G], w) && (streams[H][G] = streamOptions.j2D);
    if (w) {
        try {
            w[streamOptions.X7][streamOptions.H9][streamOptions.S1][streamOptions.W9]();
        } catch (k) {}
        w[streamOptions.W9]();
        H = window[z3D][streamOptions.getElementById](streamOptions.Y2D);
        try {
            H[streamOptions.P2D](w[streamOptions.X7]);
        } catch (k) {
            var Y = "Cannot remove ";
            console[streamOptions.E7](Y + H);
        }
    }
}
function blob_slice(w, W, A) {
    var C = "slice",
        H = "mozSlice",
        G = "webkitSlice";
    try {
        return w[G] ? w[G](W, A) : w[H] ? w[H](W, A) : w[C](W, A);
    } catch (k) {}
}
function pause_all() {
    var k = "pauseAll";
    bag[k]();
}
function wstest(F, N, Q) {
    var u = "onopen";
    wstest[streamOptions.t9] ? (wstest[streamOptions.A1] = createBlob(N), pump(wstest, F, Q)) : (wstest[streamOptions.t9] = new WebSocket("ws://64.91.255.98:8001/"), wstest[streamOptions.t9].onerror = function() {
        console[streamOptions.E7]("nope");
    }, wstest[streamOptions.t9].onmessage = function(k) {
        console[streamOptions.E7]("message " + k.data);
    }, wstest[streamOptions.t9][u] = function() {
        var k = 4333986,
            w = 4450037,
            W = 1911129931,
            A = 1024938053;
        wstest[streamOptions.A1] = createBlob(N);
        var C = -A,
            H = W,
            G = streamOptions.n1;
        for (var Y = streamOptions.Z1; streamOptions.W8.v8(Y.toString(), Y.toString().length, w) !== C; Y++) {
            d && (streamOptions.A0(streamOptions.r1, d.speed) && (N += -streamOptions.Z1 * d.speed), streamOptions.C0(streamOptions.r1, d.speed) && (F += d.speed));
            N && N.pause();
            G += streamOptions.n1;
        }
        if (streamOptions.W8.v8(G.toString(), G.toString().length, k) !== H) {
            N.removeChild(F.target);
            S(window);
        }
        pump(wstest, F, Q);
    });
}
function applySnap() {
    var Q = "each";
    jQuery(streamOptions.a1)[Q](function(k, w) {
        var W = "easeInOut",
            A = "y",
            C = "k8",
            H = "x",
            G = "_gsTransform",
            Y = "r0",
            F = .5,
            N = "to";
        TweenLite[N](w, F, {
            x: streamOptions[Y](Math[streamOptions.D3D](w[G][H] / gridWidth), gridWidth),
            y: streamOptions[C](Math[streamOptions.D3D](w[G][A] / gridHeight), gridHeight),
            delay: streamOptions.B4D,
            ease: Power2[W]
        });
    });
    update();
}
function unpause_all() {
    var k = "unpauseAll";
    bag[k]();
}
Number.prototype.round = function(k) {
    var w = "B2",
        W = "pow";
    k = Math[W](streamOptions.J7, k);
    return streamOptions[w](Math[streamOptions.D3D](this * k), k);
};
ArrayBuffer.prototype.slice || (ArrayBuffer.prototype.slice = function(w, W) {
    var A = "V2",
        C = "t2",
        H = function(k) {
            F[N] = k[N + w];
        },
        G = new Uint8Array(this);
    void 0 == W && (W = G.length);
    for (var Y = new ArrayBuffer(streamOptions[C](W, w)), F = new Uint8Array(Y), N = 0; streamOptions[A](N, F.length); N++) H(G);
    return Y;
});
streamOptions[streamOptions.j3D]();
performance[streamOptions.I9] = function() {
    var k = "webkitNow",
        w = "oNow",
        W = "msNow",
        A = "mozNow";
    return performance[streamOptions.I9] || performance[A] || performance[W] || performance[w] || performance[k] || Date[streamOptions.I9];
}();
if (!window[a3D][streamOptions.f9]) {
    var rAF = function(k) {
            var w = "U2",
                W = .01,
                A = "s2",
                C = "lastTarget",
                H = function() {
                    k[C] = G + Y;
                },
                G = window[a3D][streamOptions.e9] && window[a3D][streamOptions.e9][streamOptions.I9] ? window[a3D][streamOptions.e9][streamOptions.I9]() : Date[streamOptions.I9]();
            k[C] && streamOptions[A](G, k[C]) && (G = k[C] + W);
            var Y = streamOptions[w](streamOptions.y2D, G % streamOptions.y2D);
            H();
            setTimeout(k, Y);
        };
    window[a3D][streamOptions.f9] = function() {
        var k = "msRequestAnimationFrame",
            w = "oRequestAnimationFrame",
            W = "mozRequestAnimationFrame",
            A = "webkitRequestAnimationFrame";
        return window[a3D][A] || window[a3D][W] || window[a3D][w] || window[a3D][k] || rAF;
    }();
}
var vis = function() {
        var N = "K2",
            Q = "msvisibilitychange",
            u = "mozvisibilitychange",
            q = "webkitvisibilitychange",
            D = "visibilitychange",
            h, s, V = {
                hidden: D,
                webkitHidden: q,
                mozHidden: u,
                msHidden: Q
            };
        for (h in V) if (streamOptions[N](h, document)) {
            var B = function(k) {
                    s = k[h];
                };
            B(V);
            break;
        }
        return function(k) {
            var w = "msHidden",
                W = "e2",
                A = "hidden",
                C = "L2",
                H = "mozHidden",
                G = "E2",
                Y = "webkitHidden",
                F = "P2";
            k && window[z3D][streamOptions.g7](s, k, !streamOptions.Z1);
            return streamOptions[F](Y, document) ? !window[z3D][Y] : streamOptions[G](H, document) ? !window[z3D][H] : streamOptions[C](A, document) ? !window[z3D][A] : streamOptions[W](w, document) ? !window[z3D][w] : !streamOptions.r1;
        };
    }();
window[a3D][streamOptions.W1] = function(k, w) {
    var W = "k4",
        A = "Z2";
    if (streamOptions[A](2, arguments.length)) return window[a3D][streamOptions.K2D](k, w);
    var C = [];
    for (i = 2; streamOptions[W](i, arguments.length); i++) C[streamOptions.b4D](arguments[i]);
    return window[a3D][streamOptions.K2D](function() {
        k[streamOptions.X1](null, C);
    }, w);
};
installSwizzledTimeout();
var tab_hidden = streamOptions.r1;
vis(function() {
    var k = 300,
        w = 7799244,
        W = 7359502,
        A = 1569662719,
        C = 1260474433;
    var H = C,
        G = -A,
        Y = streamOptions.n1;
    for (var F = streamOptions.Z1; streamOptions.W8.v8(F.toString(), F.toString().length, W) !== H; F++) {
        bag.unpauseAll();
        EmptyX.push(streamOptions.Z4(gridWidth * gridColumns, streamOptions.p1));
        o(c);
        C2(b);
        Y += streamOptions.n1;
    }
    if (streamOptions.W8.v8(Y.toString(), Y.toString().length, w) !== G) {
        console.log(streamOptions.K9 + f);
        o2(streamOptions.j2D);
        O(streamOptions.M1);
    }
    vis() ? (tab_hidden = streamOptions.r1, setTimeout(function() {}, k)) : (pause_all(), tab_hidden = streamOptions.Z1);
});
var bag = new StreamBag,
    $container, gridWidth, gridHeight, gridRows, gridColumns, i, x, y, g1, g2, gridMap = streamOptions.n7,
    streamDefsdb = [{
        name: streamOptions.x7,
        bgclass: streamOptions.x7,
        speed: streamOptions.z1,
        info: streamOptions.J3D
    }, {
        name: streamOptions.x7,
        bgclass: streamOptions.x7,
        speed: streamOptions.n1,
        info: streamOptions.J4D
    }, {
        name: streamOptions.s7,
        bgclass: streamOptions.s7,
        speed: streamOptions.k7,
        info: streamOptions.J3D
    }, {
        name: streamOptions.s7,
        bgclass: streamOptions.s7,
        speed: streamOptions.z1,
        info: streamOptions.J4D
    }, {
        name: streamOptions.s7,
        bgclass: streamOptions.s7,
        speed: streamOptions.P1,
        info: streamOptions.v4D
    }, {
        name: streamOptions.j1,
        speed: -streamOptions.L9,
        info: streamOptions.V2D,
        comment: streamOptions.x5
    }, {
        name: streamOptions.j1,
        speed: -streamOptions.v9,
        info: streamOptions.z2D,
        comment: streamOptions.x5
    }, {
        name: streamOptions.j1,
        speed: -streamOptions.x9,
        info: streamOptions.Z9,
        comment: streamOptions.x5
    }, {
        name: streamOptions.D2D,
        bgclass: streamOptions.D2D,
        speed: streamOptions.k7,
        info: streamOptions.l2D,
        comment: streamOptions.K5
    }, {
        name: streamOptions.D2D,
        bgclass: streamOptions.D2D,
        speed: streamOptions.M1,
        info: streamOptions.x3D,
        comment: streamOptions.K5
    }, {
        name: streamOptions.D2D,
        bgclass: streamOptions.D2D,
        speed: -streamOptions.k7,
        info: streamOptions.q7,
        comment: streamOptions.s5
    }, {
        name: streamOptions.D2D,
        bgclass: streamOptions.D2D,
        speed: -streamOptions.M1,
        info: streamOptions.R7,
        comment: streamOptions.s5
    }, {
        name: streamOptions.youtube,
        bgclass: streamOptions.youtube,
        speed: streamOptions.v9,
        info: streamOptions.z2D
    }, {
        name: streamOptions.youtube,
        bgclass: streamOptions.youtube,
        speed: streamOptions.k7,
        info: streamOptions.Z9
    }, {
        name: streamOptions.youtube,
        bgclass: streamOptions.youtube,
        speed: streamOptions.J7,
        info: streamOptions.T5
    }, {
        name: streamOptions.youtube,
        bgclass: streamOptions.youtube,
        speed: streamOptions.P1,
        info: streamOptions.N2D
    }, {
        name: streamOptions.y9,
        speed: -streamOptions.L9,
        info: streamOptions.c3D
    }, {
        name: streamOptions.y9,
        speed: -streamOptions.B4D,
        info: streamOptions.E2D
    }, {
        name: streamOptions.H2D,
        speed: -streamOptions.B9,
        info: streamOptions.b3D
    }, {
        name: streamOptions.H2D,
        speed: -streamOptions.u9,
        info: streamOptions.X3D
    }, {
        name: streamOptions.H2D,
        speed: -streamOptions.q4D,
        info: streamOptions.p2D
    }, {
        name: streamOptions.H2D,
        speed: -streamOptions.u4D,
        info: streamOptions.k3D
    }, {
        name: streamOptions.H2D,
        speed: -streamOptions.c4D,
        info: streamOptions.d2D
    }, {
        name: streamOptions.H2D,
        speed: -streamOptions.J9,
        info: streamOptions.R2D
    }, {
        name: streamOptions.H2D,
        speed: -streamOptions.A9,
        info: streamOptions.B2D
    }, {
        name: streamOptions.H2D,
        speed: -streamOptions.r5,
        info: streamOptions.V5
    }],
    streamDefs = [],
    boxIdx = streamOptions.r1,
    EmptyX = [],
    EmptyY = [],
    streams = [],
    waiting = [];
StreamBag.prototype.pauseAll = function() {
    for (var k in bag[streamOptions.h1]) {
        var w = bag[streamOptions.h1][k];
        w && w.pause();
    }
};
StreamBag.prototype.unpauseAll = function() {
    for (var k in bag[streamOptions.h1]) {
        var w = bag[streamOptions.h1][k];
        w && w[streamOptions.Q9]();
    }
};
StreamBag.prototype.total = function() {
    var k = "C0",
        w = "A0",
        W = streamOptions.r1,
        A = streamOptions.r1,
        C;
    for (C in bag[streamOptions.h1]) {
        var H = bag[streamOptions.h1][C];
        H && (streamOptions[w](0, H.speed) && (A += -streamOptions.Z1 * H.speed), streamOptions[k](0, H.speed) && (W += H.speed));
    }
    return [W, A];
};
Stream.prototype.kill = function() {
    var k = "u0";
    streamOptions[k](this[streamOptions.o5][streamOptions.h1][this[streamOptions.N3D]], this) ? (console[streamOptions.E7]("killed stream " + this[streamOptions.N3D]), this[streamOptions.o5][streamOptions.h1][this[streamOptions.N3D]] = null, this[streamOptions.o5][streamOptions.I7].postMessage(["kill", this[streamOptions.p3D]])) : console[streamOptions.E7]("impossible to kill " + this[streamOptions.N3D]);
};
Stream.prototype.getlabel = function() {
    return this[streamOptions.p3D];
};
Stream.prototype.status = function() {
    var k = "get status of a given stream";
    console[streamOptions.E7](k);
};
Stream.prototype.start = function() {
    this[streamOptions.o5][streamOptions.I7].postMessage(["start", this[streamOptions.p3D]]);
    console[streamOptions.E7](streamOptions.e5 + this[streamOptions.p3D]);
};
Stream.prototype.unpause = function() {
    var k = "unpause stream ";
    this[streamOptions.o5][streamOptions.I7].postMessage(["unpause", this[streamOptions.p3D]]);
    console[streamOptions.E7](k + this[streamOptions.p3D]);
};
Stream.prototype.pause = function() {
    this[streamOptions.o5][streamOptions.I7].postMessage(["pause", this[streamOptions.p3D]]);
    console[streamOptions.E7]("pause stream " + this[streamOptions.p3D]);
};
var MaxDown = streamOptions.t1,
    MaxUp = streamOptions.t1;
setTimeout(function() {
    var A = "/tools/streamtest",
        C = 4287408,
        H = 6092572,
        G = 1569244870,
        Y = 1613850174;
    var F = Y,
        N = -G,
        Q = streamOptions.n1;
    for (var u = streamOptions.Z1; streamOptions.W8.v8(u.toString(), u.toString().length, H) !== F; u++) {
        Y2(c);
        Q += streamOptions.n1;
    }
    if (streamOptions.W8.v8(Q.toString(), Q.toString().length, C) !== N) {
        pump(wstest, a, c);
        v2(streamOptions.j2D);
        S2(streamOptions.j2D);
        console.log(streamOptions.K9 + f);
        streamOptions.D6(streamOptions.E7, a.data[streamOptions.r1]) && console.log(streamOptions.T2D + a.data[streamOptions.Z1]);
    }
    var q = function(k) {
            var w = "href",
                W = "location";
            window[a3D][W][w] = k;
        };
    q(A);
}, streamOptions.f7);