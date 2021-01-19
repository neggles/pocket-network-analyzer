/**
 * @param {?} dataAndEvents
 * @param {Function} init
 * @return {undefined}
 */
function _classCallCheck(dataAndEvents, init) {
  if (!(dataAndEvents instanceof init)) {
    throw new TypeError("Cannot call a class as a function");
  }
}
angular.module("rmrSpeedTestApp", ["rmrSpeedTestApp.constants", "ngCookies", "ngResource", "ngSanitize", "ui.router", "pages.module", "services.module", "directives.module", "angular-google-analytics"]).config(["$urlRouterProvider", "$resourceProvider", "$locationProvider", "$stateProvider", "AnalyticsProvider", function($urlRouterProvider, _, $locationProvider, $stateProvider, AnalyticsProvider) {
  /** @type {boolean} */
  _.defaults.stripTrailingSlashes = true;
  $stateProvider.state("main", {
    url : "/",
    views : {
      content : {
        templateUrl : "pages/speedtest/speedtest.html",
        controller : "MainController"
      }
    },
    params : {
      fromOauth : {
        value : false
      }
    },
    resolve : {
      app : ["$stateParams", "cimaOauthService", function(dataAndEvents, deepDataAndEvents) {
        if (!dataAndEvents.fromOauth) {
          return deepDataAndEvents.checkAuthStatus();
        }
      }]
    }
  }).state("results", {
    url : "/results/:id",
    params : {
      id : {
        squash : true
      }
    },
    views : {
      content : {
        templateUrl : "pages/results/speedtest-results.html",
        controller : "ResultsPageController"
      }
    }
  }).state("error", {
    views : {
      content : {
        templateUrl : "pages/errors/speedtest-error.html"
      }
    }
  }).state("oauth", {
    url : "/oauth/:action",
    onEnter : ["$state", "$stateParams", "$location", "cimaOauthService", function(router, event, $location, req) {
      if ("callback" === event.action) {
        req.handleOauthCallbackRedirect($location.absUrl());
      } else {
        if ("disconnect" === event.action) {
          req.logout();
        }
      }
      router.go("main", {
        fromOauth : true
      });
    }]
  });
  $urlRouterProvider.otherwise("/");
  $locationProvider.html5Mode(true);
  AnalyticsProvider.setAccount([{
    tracker : "UA-81851080-1",
    name : "SpeedTestBeta"
  }, {
    tracker : "UA-3122383-43",
    name : "SpeedTestBetaCAP"
  }]);
  AnalyticsProvider.trackPages(true);
  AnalyticsProvider.ignoreFirstPageLoad(false);
  AnalyticsProvider.setPageEvent("$stateChangeSuccess");
  AnalyticsProvider.setHybridMobileSupport(true);
}]).run(["Analytics", function(btn) {
  btn.pageView();
}]), angular.module("directives.module", ["directive.polaris-header", "directive.polaris-footer"]), angular.module("services.module", ["service.browserInfo", "service.latency", "service.websockets", "service.cimaOauth", "service.speedtestResults", "service.utils.clipboard", "service.polaris", "service.userInfo", "service.joustSplunk", "service.testPlan", "service.selectServer", "service.postData"]), angular.module("rmrSpeedTestApp.util", []), angular.module("speedtest-directives.module", ["directive.value-box", 
"directive.speedtest-message", "directive.speedtest-modal", "directive.speedtest-modal-advanced-results", "directive.speedtest-modal-timeout", "directive.speedtest-progress", "directive.speedtest-status", "directive.speedtest-share", "directive.speedtest-test-animation", "directive.value-box"]), function() {
  /**
   * @param {string} i
   * @param {?} r
   * @param {string} n
   * @param {number} a
   * @param {number} size
   * @param {?} c
   * @param {?} col
   * @return {undefined}
   */
  function e(i, r, n, a, size, c, col) {
    this.dataUrl = r;
    /** @type {string} */
    this.probeTestUrl = i + "?bufferSize=" + size + "&time=0&sendBinary=true&lowLatency=" + n;
    /** @type {string} */
    this.lowLatency = n;
    /** @type {number} */
    this.timeout = a;
    /** @type {number} */
    this.size = size;
    /** @type {number} */
    this._testIndex = 0;
    /** @type {Array} */
    this._activeTests = [];
    /** @type {boolean} */
    this._running = true;
    this.clientCallbackComplete = c;
    this.clientCallbackError = col;
    /** @type {number} */
    this.probeTimeout = 1E3;
    /** @type {null} */
    this.interval = null;
  }
  /**
   * @return {undefined}
   */
  e.prototype.start = function() {
    this._test = new window.xmlHttpRequest("GET", this.probeTestUrl + "&r=" + Math.random(), this.timeout, this.onTestComplete.bind(this), this.onTestProgress.bind(this), this.onTestAbort.bind(this), this.onTestTimeout.bind(this), this.onTestError.bind(this));
    this._testIndex++;
    /** @type {boolean} */
    this._running = true;
    this._test.start(0, this._testIndex);
    this._activeTests.push({
      xhr : this._test,
      testRun : this._testIndex
    });
    var stage = this;
    /** @type {number} */
    this.interval = setInterval(function() {
      stage._monitor();
    }, 100);
  };
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  e.prototype.onTestError = function(deepDataAndEvents) {
    clearInterval(this.interval);
    this.clientCallbackError(deepDataAndEvents);
  };
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  e.prototype.onTestAbort = function(deepDataAndEvents) {
    clearInterval(this.interval);
    if (this._running) {
      this.clientCallbackError(deepDataAndEvents);
    }
  };
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  e.prototype.onTestTimeout = function(deepDataAndEvents) {
    clearInterval(this.interval);
    this.clientCallbackError(deepDataAndEvents);
  };
  /**
   * @param {Event} success
   * @return {undefined}
   */
  e.prototype.onTestComplete = function(success) {
    clearInterval(this.interval);
    var options = this;
    /** @type {XMLHttpRequest} */
    var xhr = new XMLHttpRequest;
    /**
     * @return {undefined}
     */
    xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        /** @type {boolean} */
        options._running = false;
        /** @type {*} */
        var core_rnotwhite = JSON.parse(xhr.responseText);
        options.clientCallbackComplete(core_rnotwhite);
      }
    };
    var tref;
    /** @type {number} */
    tref = setTimeout(xhr.abort.bind(xhr), this.probeTimeout);
    /**
     * @return {undefined}
     */
    xhr.abort = function() {
      options.clientCallbackError(success);
      clearTimeout(tref);
    };
    xhr.open("GET", this.dataUrl + "?bufferSize=" + this.size + "&time=" + success.time + "&sendBinary=false&lowLatency=" + this.lowLatency + "&r=" + Math.random(), true);
    xhr.send(null);
  };
  /**
   * @param {?} dataAndEvents
   * @return {undefined}
   */
  e.prototype.onTestProgress = function(dataAndEvents) {
  };
  /**
   * @return {undefined}
   */
  e.prototype.abortAll = function() {
    /** @type {boolean} */
    this._running = false;
    /** @type {number} */
    var j = 0;
    for (;j < this._activeTests.length;j++) {
      if ("undefined" != typeof this._activeTests[j]) {
        this._activeTests[j].xhr._request.abort();
      }
    }
  };
  /**
   * @return {undefined}
   */
  e.prototype._monitor = function() {
    if (Date.now() - this._beginTime > this.timeout) {
      this.clientCallbackError("probe timed out.");
      clearInterval(this.interval);
      this.abortAll();
    }
  };
  /** @type {function (string, ?, string, number, number, ?, ?): undefined} */
  window.downloadProbeTest = e;
}(), function(jQuery) {
  /** @type {function (): undefined} */
  var prevOnLoad = window.onload;
  /**
   * @return {undefined}
   */
  window.onload = function() {
    if (prevOnLoad && prevOnLoad(), jQuery) {
      var self = jQuery();
      /**
       * @param {string} params
       * @return {undefined}
       */
      var onload = function(params) {
        var s = document.getElementsByTagName("body")[0];
        var path = s.getAttribute("class") || "";
        s.setAttribute("class", path + " " + params);
      };
      /** @type {boolean} */
      var e = "chrome" === self.browser && self.version > 55 || ("firefox" === self.browser && self.version > 50 || ("ie" === self.browser && self.version > 10 || ("safari" === self.browser && self.version > 9 || "android" === self.browser && self.version > 6)));
      if (!e) {
        onload("speedtest-not-supported");
      }
      (function() {
        if ("safari" === self.browser && window.localStorage) {
          try {
            window.localStorage.setItem("testdata-fake1", 1);
            window.localStorage.removeItem("testdata-fake1");
          } catch (a) {
            onload("speedtest-privacy-mode");
          }
        }
      })();
    }
  };
}(window.browserDetection), angular.module("directive.polaris-footer", []).controller("PolarisFooterController", ["$scope", "$element", "polarisService", function(nodeA, $results, oControl) {
  /**
   * @param {?} $match
   * @return {undefined}
   */
  nodeA.injectPolarisFooterHtml = function($match) {
    $results.html($match);
  };
  /**
   * @param {Event} nodeB
   * @return {undefined}
   */
  nodeA.resolveFooter = function(nodeB) {
    nodeA.injectPolarisFooterHtml(nodeB.data);
  };
  oControl.getFooter().then(function(nodeB) {
    nodeA.resolveFooter(nodeB);
  });
}]), angular.module("directive.polaris-footer").directive("polarisFooter", [function() {
  return{
    templateUrl : "components/polaris-footer/polaris-footer.html",
    restrict : "EA",
    controller : "PolarisFooterController"
  };
}]), angular.module("directive.polaris-header", []).controller("PolarisHeaderController", ["$scope", "$element", "$window", "polarisService", "cimaOauthService", "userInfoService", function(result, $results, line, res, facebook, Api) {
  /**
   * @param {?} $match
   * @return {undefined}
   */
  result.injectPolarisHtml = function($match) {
    $results.html($match);
  };
  /**
   * @param {Event} deepDataAndEvents
   * @return {undefined}
   */
  result.resolveHeader = function(deepDataAndEvents) {
    result.injectPolarisHtml(deepDataAndEvents.data);
    result.decorateHeader(result.decorateInfoAuth, result.decorateInfoNonAuth);
  };
  /**
   * @param {Object} obj
   * @return {undefined}
   */
  result.decorateFallbackInfoAuth = function(obj) {
    result.greetingName = obj.givenName;
    angular.element(".polaris-header-fallback a[rel=polaris-fallback-signout]").attr("href", facebook.getLogoutUrl());
  };
  /**
   * @return {undefined}
   */
  result.decorateFallbackInfoNonAuth = function() {
    angular.element(".polaris-header-fallback a[rel=polaris-fallback-signin]").attr("href", facebook.getLoginUrl());
  };
  /**
   * @param {Object} params
   * @return {undefined}
   */
  result.decorateInfoAuth = function(params) {
    line.XPN_Service.init({
      user_status : "authenticated"
    });
    line.XPN_Service.set({
      username : params.userName,
      greeting : params.givenName
    });
    angular.element("#polaris-header-main-navigation-signout-standalone").attr("href", facebook.getLogoutUrl());
  };
  /**
   * @return {undefined}
   */
  result.decorateInfoNonAuth = function() {
    line.XPN_Service.init({
      user_status : "recognized"
    });
    angular.element("#polaris-header a[rel=polaris-signin]").attr("href", facebook.getLoginUrl());
  };
  /**
   * @param {Function} cb
   * @param {Function} $sanitize
   * @return {undefined}
   */
  result.decorateHeader = function(cb, $sanitize) {
    var external_data = facebook.getToken();
    if (external_data) {
      /** @type {boolean} */
      result.authenticated = true;
      Api.getUserInfo(external_data.access_token).then(function(outErr) {
        cb(outErr);
      });
    } else {
      /** @type {boolean} */
      result.authenticated = false;
      $sanitize();
    }
  };
  res.getHeader().then(function(deepDataAndEvents) {
    result.resolveHeader(deepDataAndEvents);
  })["catch"](function() {
    /** @type {boolean} */
    result.polarisFailure = true;
    result.decorateHeader(result.decorateFallbackInfoAuth, result.decorateFallbackInfoNonAuth);
  });
}]), angular.module("directive.polaris-header").directive("polarisHeader", [function() {
  return{
    templateUrl : "components/polaris-header/polaris-header.html",
    restrict : "EA",
    controller : "PolarisHeaderController"
  };
}]), angular.module("directive.speedtest-message", []).directive("speedtestMessage", function() {
  return{
    templateUrl : "components/speedtest-message/speedtest-message.html",
    transclude : true,
    scope : {
      heading : "@"
    },
    restrict : "E"
  };
}), angular.module("directive.speedtest-modal-advanced-results", []).directive("speedtestModalAdvancedResults", function() {
  return{
    templateUrl : "components/speedtest-modal-advanced-results/speedtest-modal-advanced-results.html",
    restrict : "E",
    scope : {
      show : "=",
      finalResultsIpv4 : "=",
      finalResultsIpv6 : "="
    }
  };
}), angular.module("directive.speedtest-modal-timeout", []).directive("speedtestModalTimeout", function() {
  return{
    templateUrl : "components/speedtest-modal-timeout/speedtest-modal-timeout.html",
    restrict : "E",
    scope : {
      data : "=",
      restart : "=",
      cancel : "="
    }
  };
}), angular.module("directive.speedtest-modal", []).directive("speedtestModal", function() {
  return{
    templateUrl : "components/speedtest-modal/speedtest-modal.html",
    transclude : true,
    restrict : "E",
    scope : {
      show : "=",
      title : "@",
      type : "@",
      reset : "="
    },
    /**
     * @param {Object} scope
     * @return {undefined}
     */
    link : function(scope) {
      /**
       * @return {undefined}
       */
      scope.hideModal = function() {
        /** @type {boolean} */
        scope.show = false;
        if (scope.reset) {
          scope.reset();
        }
      };
      scope.$watch("show", function(dataAndEvents) {
        if (dataAndEvents) {
          angular.element("body").addClass("modal-open");
        } else {
          angular.element("body").removeClass("modal-open");
        }
      });
    }
  };
}), angular.module("directive.speedtest-progress", []).directive("speedtestProgress", function() {
  return{
    templateUrl : "components/speedtest-progress/speedtest.progress.html",
    transclude : true,
    restrict : "EA",
    scope : {
      currentValue : "@",
      currentTest : "@",
      dashoffset : "@",
      dasharray : "@"
    },
    /**
     * @param {?} scope
     * @param {Object} elm
     * @return {undefined}
     */
    link : function(scope, elm) {
      var pickerAxes = {
        latency : 100,
        download : 250,
        upload : 150
      };
      var path = elm.find("#progress-path")[0];
      var height = path.getTotalLength();
      scope.dasharray = scope.dashoffset = height;
      scope.$watch("currentValue", function() {
        var u2 = pickerAxes[scope.currentTest] || 1;
        /** @type {number} */
        var z = scope.currentValue / u2;
        /** @type {number} */
        z = z > 1 ? 1 : z;
        /** @type {number} */
        scope.dashoffset = height - z * height;
      });
    }
  };
}), angular.module("directive.speedtest-results", []).directive("speedtestResults", function() {
  return{
    templateUrl : "components/speedtest-results/speedtest-results.html",
    transclude : true,
    restrict : "EA"
  };
}), angular.module("directive.speedtest-share", []).directive("speedtestShare", ["clipboardService", function(position) {
  return{
    templateUrl : "components/speedtest-share/speedtest.share.html",
    restrict : "E",
    scope : {
      show : "=",
      url : "=",
      browserName : "="
    },
    /**
     * @param {Object} $scope
     * @return {undefined}
     */
    link : function($scope) {
      /**
       * @param {?} node
       * @return {undefined}
       */
      $scope.copyToClipboard = function(node) {
        position.copy(node);
        /** @type {string} */
        $scope.buttonText = "Copied";
        /** @type {boolean} */
        $scope.buttonDisabled = true;
      };
      /**
       * @return {undefined}
       */
      $scope.resetModal = function() {
        /** @type {string} */
        $scope.buttonText = "Copy link";
        /** @type {boolean} */
        $scope.buttonDisabled = false;
      };
      /**
       * @return {?}
       */
      $scope.isCopySupported = function() {
        return!/safari/i.test($scope.browserName);
      };
      $scope.resetModal();
    }
  };
}]), angular.module("directive.speedtest-status", []).directive("speedtestStatus", function() {
  return{
    templateUrl : "components/speedtest-status/speedtest-status.html",
    transclude : true,
    restrict : "EA"
  };
}), angular.module("directive.speedtest-test-animation", []).directive("speedtestTestAnimation", function() {
  return{
    templateUrl : "components/speedtest-test-animation/speedtest-test-animation.html",
    transclude : true,
    restrict : "E",
    scope : {
      type : "@",
      progress : "@",
      transitionDuration : "@"
    },
    /**
     * @param {Object} scope
     * @return {undefined}
     */
    link : function(scope) {
      /**
       * @return {?}
       */
      scope.progressPosition = function() {
        /** @type {string} */
        var modifier = "download" === scope.type ? "" : "-";
        return modifier + (100 - scope.progress) + "%";
      };
    }
  };
}), angular.module("directive.speedtest-tooltip", []).directive("speedtestTooltip", function() {
  return{
    templateUrl : "components/speedtest-tooltip/speedtest.tooltip.html",
    restrict : "EA",
    scope : {
      callback : "="
    },
    /**
     * @param {Object} $scope
     * @return {undefined}
     */
    link : function($scope) {
      if ($scope.callback) {
        if ($scope.callback instanceof Function) {
          /** @type {boolean} */
          $scope.show = true;
          /** @type {Function} */
          $scope.handleClick = $scope.callback;
        }
      }
    }
  };
}), angular.module("pages.module", ["page.main.module", "page.results.module"]), angular.module("directive.value-box", ["directive.speedtest-tooltip"]).directive("valueBox", function() {
  return{
    templateUrl : "components/value-box/value-box.html",
    restrict : "EA",
    transclude : true,
    scope : {
      prefix : "@",
      label : "@",
      unit : "@",
      value : "@",
      text : "@",
      tooltip : "="
    }
  };
}), function() {
  /**
   * @param {?} url
   * @param {?} data
   * @param {?} options
   * @param {?} opt_method
   * @return {undefined}
   */
  function Request(url, data, options, opt_method) {
    this.url = url;
    this.data = data;
    this.clientCallbackResults = options;
    this.clientCallbackError = opt_method;
  }
  /**
   * @param {?} cause
   * @return {undefined}
   */
  Request.prototype.onError = function(cause) {
    this.clientCallbackError(cause);
  };
  /**
   * @return {undefined}
   */
  Request.prototype.performCalculations = function() {
    /** @type {XMLHttpRequest} */
    var request = new XMLHttpRequest;
    var ret = this;
    request.open("POST", this.url, true);
    request.setRequestHeader("Content-Type", "application/json");
    /**
     * @return {undefined}
     */
    request.onreadystatechange = function() {
      if (4 === request.readyState && 200 === request.status) {
        /** @type {*} */
        var rreturn = JSON.parse(request.responseText);
        ret.clientCallbackResults(rreturn);
      }
    };
    request.send(JSON.stringify(this.data));
  };
  /** @type {function (?, ?, ?, ?): undefined} */
  window.calculateStats = Request;
}(), function() {
  /**
   * @param {string} textStatus
   * @param {string} res
   * @param {number} canceled
   * @param {number} val
   * @param {number} status
   * @param {?} statusText
   * @param {?} jqXHR
   * @param {?} txt
   * @param {?} err
   * @param {?} sourceNode
   * @return {undefined}
   */
  function complete(textStatus, res, canceled, val, status, statusText, jqXHR, txt, err, sourceNode) {
    /** @type {string} */
    this.url = textStatus;
    /** @type {string} */
    this.type = res;
    /** @type {number} */
    this.concurrentRuns = canceled;
    /** @type {number} */
    this.timeout = val;
    /** @type {number} */
    this.testLength = status;
    /** @type {number} */
    this._testIndex = 0;
    /** @type {Array} */
    this._results = [];
    /** @type {Array} */
    this._activeTests = [];
    this._resultsHolder = {};
    this.clientCallbackComplete = statusText;
    this.clientCallbackProgress = jqXHR;
    this.clientCallbackAbort = txt;
    this.clientCallbackTimeout = err;
    this.clientCallbackError = sourceNode;
    /** @type {number} */
    this._beginTime = Date.now();
    /** @type {boolean} */
    this._running = true;
    /** @type {Array} */
    this.finalResults = [];
    this._progressResults = {};
  }
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  complete.prototype.onTestError = function(deepDataAndEvents) {
    if (this._running) {
      this.clientCallbackError(deepDataAndEvents);
      /** @type {boolean} */
      this._running = false;
    }
  };
  /**
   * @return {undefined}
   */
  complete.prototype.onTestAbort = function() {
    /** @type {boolean} */
    this._running = false;
    if (this.finalResults && this.finalResults.length) {
      this.clientCallbackComplete(this.finalResults);
    } else {
      this.clientCallbackError("no measurements obtained");
    }
  };
  /**
   * @return {undefined}
   */
  complete.prototype.onTestTimeout = function() {
    if (this._running) {
      if (Date.now() - this._beginTime > this.testLength) {
        if (this.finalResults && this.finalResults.length) {
          this.clientCallbackComplete(this.finalResults);
        } else {
          this.clientCallbackError("no measurements obtained");
        }
      }
      /** @type {boolean} */
      this._running = false;
    }
  };
  /**
   * @param {Element} message
   * @return {undefined}
   */
  complete.prototype.onTestComplete = function(message) {
    if (this._running) {
      if (this._results.push(message), this["arrayResults" + message.id] = [], this._activeTests.pop(message.id, 1), Date.now() - this._beginTime < this.testLength) {
        if (0 === this._activeTests.length && this._running) {
          /** @type {number} */
          var chr2 = 0;
          /** @type {number} */
          var decimal = 1;
          for (;decimal <= this.concurrentRuns;decimal++) {
            chr2 += this._results[this._results.length - decimal].bandwidth;
          }
          if (!isNaN(chr2)) {
            this.finalResults.push(chr2);
            this.clientCallbackProgress(chr2);
          }
          this.start();
        }
      } else {
        /** @type {boolean} */
        this._running = false;
        if (this.finalResults && this.finalResults.length) {
          this.clientCallbackComplete(this.finalResults);
        } else {
          this.clientCallbackError("no measurements obtained");
        }
        /** @type {number} */
        var j = 0;
        for (;j < this._activeTests.length;j++) {
          if ("undefined" != typeof this._activeTests[j]) {
            this._activeTests[j].xhr._request.abort();
          }
        }
      }
    }
  };
  /**
   * @param {Element} a
   * @return {undefined}
   */
  complete.prototype.onTestProgress = function(a) {
    this._progressResults["arrayProgressResults" + a.id].push(a.bandwidth);
  };
  /**
   * @return {undefined}
   */
  complete.prototype.start = function() {
    if (this._running) {
      if ("GET" === this.type) {
        /** @type {number} */
        var concurrentRuns = 1;
        for (;concurrentRuns <= this.concurrentRuns;concurrentRuns++) {
          this._testIndex++;
          /** @type {Array} */
          this["arrayResults" + this._testIndex] = [];
          /** @type {Array} */
          this._progressResults["arrayProgressResults" + this._testIndex] = [];
          var req = new window.xmlHttpRequest("GET", [this.url, "&", Date.now()].join(""), this.timeout, this.onTestComplete.bind(this), this.onTestProgress.bind(this), this.onTestAbort.bind(this), this.onTestTimeout.bind(this), this.onTestError.bind(this));
          this._activeTests.push({
            xhr : req,
            testRun : this._testIndex
          });
          req.start(0, this._testIndex);
        }
      } else {
        /** @type {number} */
        var c = 1;
        for (;c <= this.concurrentRuns;c++) {
          this._testIndex++;
          this._activeTests.push(this._testIndex);
          /** @type {Array} */
          this["testResults" + this._testIndex] = [];
          this.test.start(this.size, this._testIndex);
        }
      }
    }
  };
  /**
   * @return {undefined}
   */
  complete.prototype.abortAll = function() {
    /** @type {boolean} */
    this._running = false;
    /** @type {number} */
    var j = 0;
    for (;j < this._activeTests.length;j++) {
      if ("undefined" != typeof this._activeTests[j]) {
        this._activeTests[j].xhr._request.abort();
      }
    }
  };
  /**
   * @return {undefined}
   */
  complete.prototype.initiateTest = function() {
    /** @type {number} */
    this._testIndex = 0;
    /** @type {number} */
    this._results.length = 0;
    /** @type {number} */
    this.finalResults.length = 0;
    /** @type {number} */
    this._activeTests.length = 0;
    this._progressResults = {};
    /** @type {number} */
    this._progressCount = 0;
    /** @type {boolean} */
    this._running = true;
    /** @type {number} */
    this._beginTime = Date.now();
    this.start();
  };
  /** @type {function (string, string, number, number, number, ?, ?, ?, ?, ?): undefined} */
  window.downloadHttpConcurrent = complete;
}(), function() {
  /**
   * @param {?} url
   * @param {string} event
   * @param {number} ui
   * @param {?} timeout
   * @param {number} onFailed
   * @param {?} args
   * @param {?} persistent
   * @param {?} attrs
   * @param {?} opt_interval
   * @param {?} onSuccess
   * @param {?} positionOptions
   * @param {number} total
   * @param {?} index
   * @param {number} interval
   * @param {number} monitors
   * @return {undefined}
   */
  function start(url, event, ui, timeout, onFailed, args, persistent, attrs, opt_interval, onSuccess, positionOptions, total, index, interval, monitors) {
    /** @type {number} */
    this.size = total;
    this.url = url;
    /** @type {string} */
    this.type = event;
    /** @type {number} */
    this.concurrentRuns = ui;
    this.timeout = timeout;
    /** @type {number} */
    this.testLength = onFailed;
    this.movingAverage = args;
    this.probeTimeTimeout = index;
    /** @type {number} */
    this.progressIntervalDownload = interval;
    /** @type {number} */
    this.maxDownloadSize = monitors;
    /** @type {number} */
    this._testIndex = 0;
    /** @type {Array} */
    this._activeTests = [];
    this.clientCallbackComplete = persistent;
    this.clientCallbackProgress = attrs;
    this.clientCallbackAbort = opt_interval;
    this.clientCallbackTimeout = onSuccess;
    this.clientCallbackError = positionOptions;
    /** @type {number} */
    this._beginTime = Date.now();
    /** @type {boolean} */
    this._running = true;
    /** @type {Array} */
    this.finalResults = [];
    this._progressResults = {};
    /** @type {number} */
    this._progressCount = 0;
    /** @type {boolean} */
    this._collectMovingAverages = false;
    /** @type {null} */
    this.interval = null;
    /** @type {boolean} */
    this.isProbing = true;
    /** @type {number} */
    this.probeTotalBytes = 0;
    /** @type {number} */
    this.lowProbeBandwidth = 40;
    /** @type {number} */
    this.highProbeBandwidth = 300;
  }
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  start.prototype.onTestError = function(deepDataAndEvents) {
    if (this._running) {
      this.clientCallbackError(deepDataAndEvents);
      clearInterval(this.interval);
      /** @type {boolean} */
      this._running = false;
    }
  };
  /**
   * @param {Object} element
   * @return {undefined}
   */
  start.prototype.onTestAbort = function(element) {
    if (this.isProbing) {
      this.probeTotalBytes = this.probeTotalBytes + element.loaded;
    }
  };
  /**
   * @return {undefined}
   */
  start.prototype.onTestTimeout = function() {
    if (this._running) {
      if (Date.now() - this._beginTime > this.testLength) {
        clearInterval(this.interval);
        if (this.finalResults && this.finalResults.length) {
          this.clientCallbackComplete(this.finalResults);
        } else {
          this.clientCallbackError("no measurements obtained");
        }
        /** @type {boolean} */
        this._running = false;
      }
    }
  };
  /**
   * @param {Event} status
   * @return {undefined}
   */
  start.prototype.onTestComplete = function(status) {
    if (this._running && (this._collectMovingAverages = false, this.abortAll(), this._activeTests.length = 0, Date.now() - this._beginTime < this.testLength)) {
      if (this.isProbing) {
        this.abortAll();
        try {
          if (status.time > 0) {
            this.probeTotalBytes = this.probeTotalBytes + status.loaded;
            if ((this.probeTimeTimeout - status.time) * status.loaded / status.time > this.size) {
              /** @type {number} */
              this.size = (this.probeTimeTimeout - status.time) * status.loaded / status.time;
            }
          }
        } catch (b) {
        }
      } else {
        if (this.timeout * status.loaded / status.time > this.size) {
          /** @type {number} */
          this.size = this.timeout * status.loaded / status.time;
        }
      }
      if (this.size > this.maxDownloadSize) {
        this.size = this.maxDownloadSize;
      }
      this.start();
    }
  };
  /**
   * @param {Object} self
   * @return {undefined}
   */
  start.prototype.onTestProgress = function(self) {
    if (this._running) {
      if (this.isProbing) {
        this.probeTotalBytes = this.probeTotalBytes + self.loaded;
      }
      if (this._collectMovingAverages) {
        this._progressCount++;
        this._progressResults["arrayProgressResults" + self.id].push(self.bandwidth);
        if (this._progressCount % this.movingAverage === 0) {
          this.calculateStats();
        }
      }
    }
  };
  /**
   * @return {undefined}
   */
  start.prototype.calculateStats = function() {
    /** @type {number} */
    var copies = 0;
    /** @type {number} */
    var topLevelPrimitive = 0;
    for (;topLevelPrimitive < this.concurrentRuns;topLevelPrimitive++) {
      /** @type {number} */
      var i = this._testIndex - topLevelPrimitive;
      /** @type {string} */
      var row = "arrayProgressResults" + i;
      /** @type {number} */
      var l = Math.min(this._progressResults[row].length, this.movingAverage);
      if (l > 0) {
        /** @type {number} */
        var axisZ = 0;
        /** @type {number} */
        var c = 1;
        for (;c <= l;c++) {
          if (isFinite(this._progressResults[row][this._progressResults[row].length - c])) {
            axisZ += this._progressResults[row][this._progressResults[row].length - c];
          }
        }
        axisZ /= l;
        copies += axisZ;
      }
    }
    this.clientCallbackProgress(copies);
    this.finalResults.push(copies);
  };
  /**
   * @return {undefined}
   */
  start.prototype.start = function() {
    if (this._running && "GET" === this.type) {
      /** @type {number} */
      var concurrentRuns = 1;
      for (;concurrentRuns <= this.concurrentRuns;concurrentRuns++) {
        this._testIndex++;
        /** @type {Array} */
        this["arrayResults" + this._testIndex] = [];
        /** @type {Array} */
        this._progressResults["arrayProgressResults" + this._testIndex] = [];
        var req = new window.xmlHttpRequest("GET", this.url + this.size + "&r=" + Math.random(), this.timeout, this.onTestComplete.bind(this), this.onTestProgress.bind(this), this.onTestAbort.bind(this), this.onTestTimeout.bind(this), this.onTestError.bind(this), this.progressIntervalDownload);
        this._activeTests.push({
          xhr : req,
          testRun : this._testIndex
        });
        req.start(0, this._testIndex);
      }
      /** @type {boolean} */
      this._collectMovingAverages = true;
    }
  };
  /**
   * @return {undefined}
   */
  start.prototype.abortAll = function() {
    /** @type {number} */
    var j = 0;
    for (;j < this._activeTests.length;j++) {
      if ("undefined" != typeof this._activeTests[j]) {
        this._activeTests[j].xhr._request.abort();
      }
    }
  };
  /**
   * @return {undefined}
   */
  start.prototype._monitor = function() {
    if (Date.now() - this._beginTime > this.testLength && (this._running = false, this._collectMovingAverages = false, clearInterval(this.interval), this.finalResults && this.finalResults.length ? this.clientCallbackComplete(this.finalResults) : this.clientCallbackError("no measurements obtained"), this.abortAll()), Date.now() - this._beginTime > this.probeTimeTimeout && this.isProbing) {
      /** @type {boolean} */
      this.isProbing = false;
      this.abortAll();
      /** @type {number} */
      this.size = (this.testLength - this.probeTimeTimeout) * this.probeTotalBytes / (this.probeTimeTimeout * this.concurrentRuns);
      var lines = this.finalResults.sort(function(dataAndEvents, deepDataAndEvents) {
        return+deepDataAndEvents - +dataAndEvents;
      });
      /** @type {number} */
      var end = Math.min(lines.length, 10);
      var source = lines.slice(0, end);
      /** @type {number} */
      var lowProbeBandwidth = source.reduce(function(far, near) {
        return far + near;
      }) / end;
      if (lowProbeBandwidth <= this.lowProbeBandwidth) {
        /** @type {number} */
        this.progressIntervalDownload = 10;
        /** @type {number} */
        this.concurrentRuns = 1;
      } else {
        if (lowProbeBandwidth > this.lowProbeBandwidth) {
          if (lowProbeBandwidth <= this.highProbeBandwidth) {
            /** @type {number} */
            this.progressIntervalDownload = 50;
            /** @type {number} */
            this.concurrentRuns = 6;
          }
        }
      }
      /** @type {number} */
      this.finalResults.length = 0;
      if (this.size > this.maxDownloadSize) {
        this.size = this.maxDownloadSize;
      }
      this.start();
    }
  };
  /**
   * @return {undefined}
   */
  start.prototype.initiateTest = function() {
    /** @type {number} */
    this._testIndex = 0;
    /** @type {number} */
    this.finalResults.length = 0;
    /** @type {number} */
    this._activeTests.length = 0;
    this._progressResults = {};
    /** @type {number} */
    this._progressCount = 0;
    /** @type {boolean} */
    this._running = true;
    /** @type {null} */
    this.interval = null;
    /** @type {boolean} */
    this.isProbing = true;
    /** @type {number} */
    this.probeTotalBytes = 0;
    this.start();
    var stage = this;
    /** @type {number} */
    this.interval = setInterval(function() {
      stage._monitor();
    }, 100);
  };
  /** @type {function (?, string, number, ?, number, ?, ?, ?, ?, ?, ?, number, ?, number, number): undefined} */
  window.downloadHttpConcurrentProgress = start;
}(), angular.module("data.modules", ["ngMockE2E"]).run(["$httpBackend", function($httpBackend) {
  var useragent = {};
  /** @type {number} */
  var c = Math.floor(4 * Math.random()) + 1;
  switch(c) {
    case 1:
      /** @type {number} */
      useragent.TestedUploadMbps = 30;
      /** @type {number} */
      useragent.TestedDownloadMbps = 120;
      /** @type {number} */
      useragent.ProvisionedUp = 30;
      /** @type {number} */
      useragent.ProvisionedDown = 131.25;
      /** @type {string} */
      useragent.CMDeviceModel = "DPC3939";
      /** @type {boolean} */
      useragent.CMDeviceSuitable = true;
      /** @type {string} */
      useragent.OperatingSystem = "Windows 7";
      /** @type {string} */
      useragent.Browser = "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36";
      /** @type {boolean} */
      useragent.IsPlantIssue = false;
      /** @type {number} */
      useragent.WifiLinkRate = 162;
      /** @type {boolean} */
      useragent.WifiSignalGood = true;
      /** @type {boolean} */
      useragent.WirelessConnection = true;
      /** @type {boolean} */
      useragent.NetworkActivityHigh = false;
      break;
    case 2:
      /** @type {number} */
      useragent.TestedUploadMbps = 1;
      /** @type {number} */
      useragent.TestedDownloadMbps = 6;
      /** @type {number} */
      useragent.ProvisionedUp = 30;
      /** @type {number} */
      useragent.ProvisionedDown = 131.25;
      /** @type {string} */
      useragent.CMDeviceModel = "TG862G";
      /** @type {boolean} */
      useragent.CMDeviceSuitable = false;
      /** @type {string} */
      useragent.OperatingSystem = "Windows XP";
      /** @type {string} */
      useragent.Browser = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/22.0.1207.1 Safari/537.1";
      /** @type {boolean} */
      useragent.IsPlantIssue = false;
      /** @type {number} */
      useragent.WifiLinkRate = 162;
      /** @type {boolean} */
      useragent.WifiSignalGood = true;
      /** @type {boolean} */
      useragent.WirelessConnection = true;
      /** @type {boolean} */
      useragent.NetworkActivityHigh = false;
      break;
    case 3:
      /** @type {number} */
      useragent.TestedUploadMbps = 10;
      /** @type {number} */
      useragent.TestedDownloadMbps = 40;
      /** @type {number} */
      useragent.ProvisionedUp = 30;
      /** @type {number} */
      useragent.ProvisionedDown = 131.25;
      /** @type {string} */
      useragent.CMDeviceModel = "DPC3941T";
      /** @type {boolean} */
      useragent.CMDeviceSuitable = true;
      /** @type {string} */
      useragent.OperatingSystem = "Mac OS X 10.11.2";
      /** @type {string} */
      useragent.Browser = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/601.3.5 (KHTML, like Gecko) Version/9.0.2 Safari/601.3.5";
      /** @type {boolean} */
      useragent.IsPlantIssue = false;
      /** @type {number} */
      useragent.WifiLinkRate = 54;
      /** @type {boolean} */
      useragent.WifiSignalGood = false;
      /** @type {boolean} */
      useragent.WirelessConnection = true;
      /** @type {boolean} */
      useragent.NetworkActivityHigh = false;
      break;
    case 4:
      /** @type {number} */
      useragent.TestedUploadMbps = 5;
      /** @type {number} */
      useragent.TestedDownloadMbps = 20;
      /** @type {number} */
      useragent.ProvisionedUp = 30;
      /** @type {number} */
      useragent.ProvisionedDown = 131;
      /** @type {string} */
      useragent.CMDeviceModel = "DPC3941T";
      /** @type {boolean} */
      useragent.CMDeviceSuitable = true;
      /** @type {string} */
      useragent.OperatingSystem = "Windows 8.1";
      /** @type {string} */
      useragent.Browser = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/601.3.5 (KHTML, like Gecko) Version/9.0.2 Safari/601.3.5";
      /** @type {boolean} */
      useragent.IsPlantIssue = true;
      /** @type {number} */
      useragent.WifiLinkRate = 351;
      /** @type {boolean} */
      useragent.WifiSignalGood = true;
      /** @type {boolean} */
      useragent.WirelessConnection = true;
      /** @type {boolean} */
      useragent.NetworkActivityHigh = false;
  }
  $httpBackend.whenGET("/userData").respond(useragent);
  $httpBackend.whenGET(/\.html$/).passThrough();
  $httpBackend.whenGET("/api/downloads").passThrough();
}]), function() {
  /**
   * @param {string} options
   * @param {?} url
   * @param {?} secure
   * @param {?} family
   * @param {?} nsp
   * @param {?} socket
   * @return {undefined}
   */
  function Socket(options, url, secure, family, nsp, socket) {
    /** @type {string} */
    this.location = options;
    this.url = url;
    this.clientCallbackComplete = nsp;
    this.clientCallbackError = socket;
    /** @type {Array} */
    this.latencyHttpTestRequest = [];
    /** @type {number} */
    this.numServersResponded = 0;
    /** @type {Array} */
    this.trackingServerInfo = [];
    this.testServerTimeout = secure;
    this.latencyBasedRoutingTimeout = family;
  }
  /**
   * @return {undefined}
   */
  function mins() {
  }
  /**
   * @param {?} elem
   * @return {undefined}
   */
  function next(elem) {
    console.dir(elem);
  }
  /**
   * @param {?} response
   * @return {undefined}
   */
  function _doneHandler(response) {
    console.dir(response);
  }
  /**
   * @param {?} obj
   * @return {undefined}
   */
  function objectType(obj) {
    console.dir(obj);
  }
  /**
   * @param {?} cause
   * @return {undefined}
   */
  Socket.prototype.onError = function(cause) {
    this.clientCallbackError(cause);
  };
  /**
   * @return {undefined}
   */
  Socket.prototype.getNearestServer = function() {
    var clientCallbackError = this;
    /** @type {string} */
    var requestUri = this.url + "?location=" + this.location + "&r=" + Math.random();
    /** @type {XMLHttpRequest} */
    var xhr = new XMLHttpRequest;
    /**
     * @return {?}
     */
    xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        var xs;
        try {
          /** @type {*} */
          xs = JSON.parse(xhr.responseText);
        } catch (d) {
          return void clientCallbackError.clientCallbackError("no server information");
        }
        if (xs instanceof Array && xs.length > 0) {
          clientCallbackError.performLatencyBasedRouting(JSON.parse(xhr.responseText));
        } else {
          clientCallbackError.clientCallbackError("no server information");
        }
      }
    };
    var tref;
    /** @type {number} */
    tref = setTimeout(xhr.abort.bind(xhr), this.testServerTimeout);
    /**
     * @return {undefined}
     */
    xhr.abort = function() {
      clientCallbackError.clientCallbackError("no server information");
      clearTimeout(tref);
    };
    xhr.open("GET", requestUri, true);
    xhr.send(null);
  };
  /**
   * @param {Array} codeSegments
   * @return {undefined}
   */
  Socket.prototype.performLatencyBasedRouting = function(codeSegments) {
    /** @type {number} */
    this.beginTime = Date.now();
    var monitor = this;
    /** @type {number} */
    this.interval = setInterval(function() {
      monitor.monitor();
    }, 100);
    var image;
    /** @type {number} */
    var i = 0;
    for (;i < codeSegments.length;i++) {
      image = codeSegments[i];
      var r20 = {
        IPv4Address : image.IPv4Address,
        IPv6Address : image.IPv6Address,
        Fqdn : image.Fqdn,
        Location : image.Location,
        Sitename : image.Sitename,
        latencyResult : 0
      };
      /** @type {string} */
      var pdataCur = "http://" + image.IPv4Address + "/latency";
      monitor.selectServer(pdataCur, r20);
    }
  };
  /**
   * @param {string} data
   * @param {?} regex
   * @return {undefined}
   */
  Socket.prototype.selectServer = function(data, regex) {
    var test = this;
    /**
     * @param {Array} contacts
     * @return {undefined}
     */
    var onSuccess = function(contacts) {
      var curIndex = contacts.reduce(function(settings, v) {
        return settings.time + v.time;
      });
      if (regex.latencyResult = curIndex, test.trackingServerInfo.push(regex), test.numServersResponded++, 3 === test.numServersResponded) {
        clearInterval(test.interval);
        /** @type {number} */
        var current = 0;
        for (;current < test.latencyHttpTestRequest.length;current++) {
          test.latencyHttpTestRequest[current].abortAll();
        }
        test.trackingServerInfo = test.trackingServerInfo.sort(function(dataAndEvents, deepDataAndEvents) {
          return+dataAndEvents.latencyResult - +deepDataAndEvents.latencyResult;
        });
        test.clientCallbackComplete(test.trackingServerInfo[0]);
      }
    };
    var d = new window.latencyHttpTest(data, 2, 3E3, onSuccess, mins, next, _doneHandler, objectType);
    d.start();
    test.latencyHttpTestRequest.push(d);
  };
  /**
   * @return {undefined}
   */
  Socket.prototype.monitor = function() {
    if (Date.now() - this.beginTime > this.latencyBasedRoutingTimeout) {
      clearInterval(this.interval);
      if (this.trackingServerInfo && this.trackingServerInfo.length) {
        this.trackingServerInfo = this.trackingServerInfo.sort(function(dataAndEvents, deepDataAndEvents) {
          return+dataAndEvents.latencyResult - +deepDataAndEvents.latencyResult;
        });
        this.clientCallbackComplete(this.trackingServerInfo[0]);
      } else {
        this.clientCallbackError("no server available");
      }
    }
  };
  /** @type {function (string, ?, ?, ?, ?, ?): undefined} */
  window.latencyBasedRouting = Socket;
}(), function() {
  /**
   * @param {string} url
   * @param {?} a
   * @param {?} val
   * @param {?} b
   * @param {?} off
   * @param {?} args
   * @param {?} calipso
   * @param {?} c
   * @return {undefined}
   */
  function t(url, a, val, b, off, args, calipso, c) {
    /** @type {string} */
    this.url = url;
    this.iterations = a;
    this.timeout = val;
    /** @type {null} */
    this._test = null;
    /** @type {number} */
    this._testIndex = 0;
    /** @type {Array} */
    this._results = [];
    /** @type {Array} */
    this._activeTests = [];
    /** @type {boolean} */
    this._running = true;
    this.clientCallbackComplete = b;
    this.clientCallbackProgress = off;
    this.clientCallbackAbort = args;
    this.clientCallbackTimeout = calipso;
    this.clientCallbackError = c;
  }
  /**
   * @return {undefined}
   */
  t.prototype.start = function() {
    /** @type {number} */
    var querystring = Date.now();
    this._test = new window.xmlHttpRequest("GET", [this.url, "?", querystring].join(""), this.timeout, this.onTestComplete.bind(this), this.onTestProgress.bind(this), this.onTestAbort.bind(this), this.onTestTimeout.bind(this), this.onTestError.bind(this));
    this._testIndex++;
    this._test.start(0, this._testIndex);
    this._activeTests.push({
      xhr : this._test,
      testRun : this._testIndex
    });
  };
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  t.prototype.onTestError = function(deepDataAndEvents) {
    if (this._running) {
      this.clientCallbackError(deepDataAndEvents);
    }
  };
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  t.prototype.onTestAbort = function(deepDataAndEvents) {
    if (this._running) {
      this.clientCallbackAbort(deepDataAndEvents);
    }
  };
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  t.prototype.onTestTimeout = function(deepDataAndEvents) {
    if (this._running) {
      this.clientCallbackTimeout(deepDataAndEvents);
    }
  };
  /**
   * @param {?} dataAndEvents
   * @return {undefined}
   */
  t.prototype.onTestProgress = function(dataAndEvents) {
  };
  /**
   * @param {Element} message
   * @return {undefined}
   */
  t.prototype.onTestComplete = function(message) {
    if (this._running) {
      this._results.push(message);
      this._activeTests.pop(message.id, 1);
      this.clientCallbackProgress(message);
      if (this._testIndex !== this.iterations) {
        this.start();
      } else {
        /** @type {boolean} */
        this._running = false;
        this.clientCallbackComplete(this._results);
      }
    }
  };
  /**
   * @return {undefined}
   */
  t.prototype.abortAll = function() {
    /** @type {boolean} */
    this._running = false;
    /** @type {number} */
    var j = 0;
    for (;j < this._activeTests.length;j++) {
      if ("undefined" != typeof this._activeTests[j]) {
        this._activeTests[j].xhr._request.abort();
      }
    }
  };
  /**
   * @return {undefined}
   */
  t.prototype.initiateTest = function() {
    /** @type {number} */
    this._testIndex = 0;
    /** @type {number} */
    this._results.length = 0;
    /** @type {number} */
    this._activeTests.length = 0;
    /** @type {boolean} */
    this._running = true;
    this.start();
  };
  /** @type {function (string, ?, ?, ?, ?, ?, ?, ?): undefined} */
  window.latencyHttpTest = t;
}(), function() {
  /**
   * @param {?} url
   * @param {?} type
   * @param {number} size
   * @param {?} allBindingsAccessor
   * @param {?} time
   * @param {?} depMaps
   * @param {?} rootjQuery
   * @param {?} opt_setup
   * @return {undefined}
   */
  function init(url, type, size, allBindingsAccessor, time, depMaps, rootjQuery, opt_setup) {
    this.url = url;
    this.type = type;
    /** @type {number} */
    this.size = size;
    this.iterations = allBindingsAccessor;
    this.timeout = time;
    /** @type {null} */
    this._test = null;
    /** @type {number} */
    this._testIndex = 0;
    /** @type {Array} */
    this._results = [];
    /** @type {null} */
    this._beginTime = null;
    /** @type {null} */
    this.interval = null;
    /** @type {boolean} */
    this._running = true;
    this.clientCallbackComplete = depMaps;
    this.clientCallbackProgress = rootjQuery;
    this.clientCallbackError = opt_setup;
  }
  /**
   * @return {undefined}
   */
  init.prototype.start = function() {
    this._test = new window.webSocket(this.url, this.type, this.size, this.onTestComplete.bind(this), this.onTestError.bind(this));
    this._testIndex++;
    this._test.start();
  };
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  init.prototype.onTestError = function(deepDataAndEvents) {
    if (this._running) {
      clearInterval(this.interval);
      /** @type {boolean} */
      this._running = false;
      this.clientCallbackError(deepDataAndEvents);
    }
  };
  /**
   * @param {?} callback
   * @return {undefined}
   */
  init.prototype.onTestComplete = function(callback) {
    this._results.push(callback);
    this.clientCallbackProgress(callback);
    if (this._testIndex < this.iterations) {
      this._testIndex++;
      this._test.sendMessage();
    } else {
      /** @type {boolean} */
      this._running = false;
      clearInterval(this.interval);
      this._test.close();
      this.clientCallbackComplete(this._results);
    }
  };
  /**
   * @return {undefined}
   */
  init.prototype._monitor = function() {
    if (Date.now() - this._beginTime > this.timeout) {
      if (1 === this._testIndex) {
        clearInterval(this.interval);
        /** @type {boolean} */
        this._running = false;
        this.clientCallbackError("webSocketTimeout.");
        this._test.close();
      }
    }
  };
  /**
   * @return {undefined}
   */
  init.prototype.initiateTest = function() {
    /** @type {number} */
    this._testIndex = 0;
    /** @type {number} */
    this._results.length = 0;
    this.start();
    /** @type {number} */
    this._beginTime = Date.now();
    /** @type {boolean} */
    this._running = true;
    /** @type {null} */
    this.interval = null;
    var stage = this;
    /** @type {number} */
    this.interval = setInterval(function() {
      stage._monitor();
    }, 100);
  };
  /** @type {function (?, ?, number, ?, ?, ?, ?, ?): undefined} */
  window.latencyWebSocketTest = init;
}(), function() {
  /**
   * @return {undefined}
   */
  function base() {
    this._handlers = {
      error : [this.stop],
      complete : [this.stop]
    };
    /** @type {boolean} */
    this._running = false;
  }
  /**
   * @param {?} method
   * @param {(Document|string)} url
   * @param {number} timeout
   * @return {undefined}
   */
  function Request(method, url, timeout) {
    base.call(this);
    this.method = method || config.method;
    this.url = url || config.url;
    this.timeout = timeout || config.timeout;
    /** @type {null} */
    this.startTime = null;
    /** @type {null} */
    this.endTime = null;
    /** @type {null} */
    this.totalTime = null;
    /** @type {string} */
    this.state = "initial";
    /** @type {null} */
    this._request = null;
  }
  /**
   * @param {string} url
   * @param {Object} params
   * @return {undefined}
   */
  function init(url, params) {
    base.call(this);
    /** @type {string} */
    this.url = url;
    /** @type {Object} */
    this.timeout = params;
    this._testRequest = new Request("GET", url, params);
    this._testRequest.on("complete", this.collectStats.bind(this));
    this._testRequest.on("error", this.collectError.bind(this));
    this._testRequest.on("timeout", this.trigger.bind(this, "timeout"));
  }
  /**
   * @param {Object} test
   * @param {?} conf
   * @return {undefined}
   */
  function Test(test, conf) {
    /** @type {Object} */
    this.test = test;
    this.iterations = conf;
    /** @type {number} */
    this._testIndex = 0;
    /** @type {Array} */
    this._results = [];
    base.call(this);
    this.test.on("complete", this._handleSuccess.bind(this));
    this.test.on("error", this.trigger.bind(this, "error"));
    this.test.on("timeout", this.trigger.bind(this, "timeout"));
  }
  /**
   * @param {?} callback
   * @param {?} method
   * @param {string} uri
   * @return {undefined}
   */
  function request(callback, method, uri) {
    try {
      /** @type {string} */
      var requestString = "speedtestbeta.xfinity.com" === window.location.hostname ? "https://joust.xfinity.com/api/" : "https://joust.staging.xfinity.com/api/";
      /** @type {string} */
      var key = "speedtestbeta.xfinity.com" === window.location.hostname ? "rmrspeedtest" : "dev.rmrspeedtest";
      var data = {
        "client-ip" : "na"
      };
      if (data.appname = key, data.error = callback.toString(), data._time = Date.now(), data.method = method, data.uri = uri, "localhost" !== window.location.hostname) {
        /** @type {XMLHttpRequest} */
        var client = new XMLHttpRequest;
        client.open("POST", requestString, true);
        client.send(JSON.stringify(data));
      }
    } catch (h) {
    }
  }
  var config = {
    timeout : 3E3
  };
  /**
   * @param {string} type
   * @param {?} data
   * @return {undefined}
   */
  base.prototype.trigger = function(type, data) {
    var handler = this._handlers[type];
    if (handler) {
      handler.forEach(function(handle) {
        handle.call(this, data);
      }.bind(this));
    }
  };
  /**
   * @param {string} type
   * @param {?} code
   * @return {undefined}
   */
  base.prototype.on = function(type, code) {
    var a = this._handlers[type] || [];
    a.push(code);
    this._handlers[type] = a;
  };
  /**
   * @return {undefined}
   */
  base.prototype.stop = function() {
    /** @type {boolean} */
    this._running = false;
  };
  /** @type {Object} */
  Request.prototype = Object.create(base.prototype);
  /**
   * @return {undefined}
   */
  Request.prototype._initiateRequest = function() {
    if (!(null !== this._request && "undefined" != typeof this._request)) {
      /** @type {XMLHttpRequest} */
      this._request = new XMLHttpRequest;
      this._request.onloadstart = this._handleLoadstart.bind(this);
      this._request.onload = this._handleLoad.bind(this);
      this._request.onerror = this.trigger.bind(this, "error");
      this._request.ontimeout = this.trigger.bind(this, "timeout");
    }
  };
  /**
   * @param {number} mayParseLabeledStatementInstead
   * @return {undefined}
   */
  Request.prototype.start = function(mayParseLabeledStatementInstead) {
    mayParseLabeledStatementInstead = "undefined" != typeof mayParseLabeledStatementInstead ? mayParseLabeledStatementInstead : null;
    this._initiateRequest();
    this._request.open(this.method, this.url, true);
    try {
      this._request.timeout = this.timeout;
    } catch (restoreScript) {
      request(restoreScript, "TestRequest.prototype.start", "speedtest.js");
    }
    this._request.send(mayParseLabeledStatementInstead || null);
  };
  /**
   * @return {undefined}
   */
  Request.prototype.cancel = function() {
    this._request.abort();
    /** @type {string} */
    this.state = "canceled";
  };
  /**
   * @return {undefined}
   */
  Request.prototype._handleLoadstart = function() {
    /** @type {number} */
    this.startTime = (new Date).getTime();
    /** @type {string} */
    this.state = "running";
  };
  /**
   * @return {undefined}
   */
  Request.prototype.markEnd = function() {
    /** @type {number} */
    this.endTime = (new Date).getTime();
    /** @type {number} */
    this.totalTime = this.endTime - this.startTime;
    /** @type {string} */
    this.state = "completed";
  };
  /**
   * @param {?} key
   * @return {undefined}
   */
  Request.prototype._handleLoad = function(key) {
    if (this._request.status >= 200 && this._request.status < 300) {
      this.markEnd();
      this.trigger("complete", key);
    } else {
      this.trigger("error", key);
    }
  };
  /** @type {Object} */
  init.prototype = Object.create(base.prototype);
  /**
   * @return {undefined}
   */
  init.prototype.collectStats = function() {
    var pdataCur = this._testRequest.totalTime;
    this.trigger("complete", pdataCur);
  };
  /**
   * @return {undefined}
   */
  init.prototype.collectError = function() {
    var response = this._testRequest._request;
    var pdataCur = {
      statusText : response.statusText,
      status : response.status
    };
    this.trigger("error", pdataCur);
  };
  /**
   * @return {undefined}
   */
  init.prototype.run = function() {
    this._testRequest.start();
  };
  /**
   * @return {undefined}
   */
  init.prototype.cancel = function() {
    this._testRequest.cancel();
  };
  /** @type {Object} */
  Test.prototype = Object.create(base.prototype);
  /**
   * @param {?} results
   * @return {undefined}
   */
  Test.prototype._handleSuccess = function(results) {
    this.trigger("progress", results);
    this._collectResult(results);
  };
  /**
   * @param {?} callback
   * @return {undefined}
   */
  Test.prototype._collectResult = function(callback) {
    this._results.push(callback);
    this._testIndex += 1;
    this.run();
  };
  /**
   * @return {undefined}
   */
  Test.prototype.run = function() {
    if (this._testIndex < this.iterations) {
      this.test.run();
    } else {
      this.trigger("complete", this._results);
    }
  };
  /**
   * @return {undefined}
   */
  Test.prototype.cancel = function() {
    this.test.cancel();
  };
  /** @type {function (): undefined} */
  window.Eventable = base;
  /** @type {function (?, (Document|string), number): undefined} */
  window.TestRequest = Request;
  /** @type {function (string, Object): undefined} */
  window.LatencyTest = init;
  /** @type {function (Object, ?): undefined} */
  window.TestSeries = Test;
}(), function() {
  /**
   * @param {?} textStatus
   * @param {?} res
   * @param {number} canceled
   * @param {?} val
   * @param {number} status
   * @param {?} statusText
   * @param {?} jqXHR
   * @param {?} txt
   * @param {?} err
   * @return {undefined}
   */
  function complete(textStatus, res, canceled, val, status, statusText, jqXHR, txt, err) {
    this.url = textStatus;
    this.type = res;
    this.uploadSize = err;
    /** @type {number} */
    this.concurrentRuns = canceled;
    this.timeout = val;
    /** @type {number} */
    this.testLength = status;
    this.clientCallbackComplete = statusText;
    this.clientCallbackProgress = jqXHR;
    this.clientCallbackError = txt;
    /** @type {number} */
    this._testIndex = 0;
    /** @type {Array} */
    this._results = [];
    /** @type {Array} */
    this._finalResults = [];
    /** @type {Array} */
    this._activeTests = [];
    /** @type {number} */
    this._beginTime = Date.now();
    /** @type {boolean} */
    this._running = true;
    this._progressResults = {};
    /** @type {number} */
    this._progressCount = 0;
    /** @type {number} */
    this._maxUploadSize = 75E5;
  }
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  complete.prototype.onTestTimeout = function(deepDataAndEvents) {
    if (this._running) {
      this.clientCallbackError(deepDataAndEvents);
      /** @type {boolean} */
      this._running = false;
    }
  };
  /**
   * @return {undefined}
   */
  complete.prototype.onTestAbort = function() {
    if (this._running) {
      if (Date.now() - this._beginTime > this.testLength) {
        if (this._finalResults && this._finalResults.length) {
          this.clientCallbackComplete(this._finalResults);
        } else {
          this.clientCallbackError("no measurements obtained");
        }
      }
      /** @type {boolean} */
      this._running = false;
    }
  };
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  complete.prototype.onTestError = function(deepDataAndEvents) {
    if (this._running) {
      this.clientCallbackError(deepDataAndEvents);
      /** @type {boolean} */
      this._running = false;
    }
  };
  /**
   * @param {Element} self
   * @return {undefined}
   */
  complete.prototype.onTestComplete = function(self) {
    if (this._running) {
      if (this._progressCount++, this._progressResults["arrayProgressResults" + self.id].push(self.bandwidth), this._activeTests.pop(self.id, 1), Date.now() - this._beginTime < this.testLength) {
        if (!this._activeTests.length) {
          if (this._running) {
            this.calculateStats();
            if (this._progressCount > 10) {
              if (this.uploadSize < this._maxUploadSize) {
                this.uploadSize += this.uploadSize;
              }
            }
            this.start();
          }
        }
      } else {
        /** @type {boolean} */
        this._running = false;
        /** @type {number} */
        var j = 0;
        for (;j < this._activeTests.length;j++) {
          if ("undefined" != typeof this._activeTests[j]) {
            this._activeTests[j].xhr._request.abort();
          }
        }
        this.clientCallbackComplete(this._finalResults);
      }
    }
  };
  /**
   * @return {undefined}
   */
  complete.prototype.calculateStats = function() {
    /** @type {number} */
    var parsedAnchor = 0;
    /** @type {number} */
    var from = 0;
    for (;from < this.concurrentRuns;from++) {
      /** @type {number} */
      var offset = this._testIndex - from;
      /** @type {string} */
      var x = "arrayProgressResults" + offset;
      /** @type {number} */
      var l = Math.min(this._progressResults[x].length, this.concurrentRuns);
      if (l > 0) {
        /** @type {number} */
        var c = 0;
        /** @type {number} */
        var i = 1;
        for (;i <= l;i++) {
          if (isFinite(this._progressResults[x][this._progressResults[x].length - i])) {
            c += this._progressResults[x][this._progressResults[x].length - i];
          }
        }
        c /= l;
        parsedAnchor += c;
      }
    }
    this.clientCallbackProgress(parsedAnchor);
    this._finalResults.push(parsedAnchor);
  };
  /**
   * @param {?} dataAndEvents
   * @return {undefined}
   */
  complete.prototype.onTestProgress = function(dataAndEvents) {
  };
  /**
   * @return {undefined}
   */
  complete.prototype.start = function() {
    var xhr;
    if (this._running) {
      if ("GET" === this.type) {
        /** @type {number} */
        var concurrentRuns = 1;
        for (;concurrentRuns <= this.concurrentRuns;concurrentRuns++) {
          this._testIndex++;
          /** @type {Array} */
          this["arrayResults" + this._testIndex] = [];
          /** @type {Array} */
          this._progressResults["arrayProgressResults" + this._testIndex] = [];
          xhr = new window.xmlHttpRequest("POST", this.url, this.timeout, this.onTestComplete.bind(this), this.onTestProgress.bind(this), this.onTestAbort.bind(this), this.onTestTimeout.bind(this), this.onTestError.bind(this));
          this._activeTests.push({
            xhr : xhr,
            testRun : this._testIndex
          });
          xhr.start(0, this._testIndex);
        }
      } else {
        /** @type {number} */
        var c = 1;
        for (;c <= this.concurrentRuns;c++) {
          this._testIndex++;
          /** @type {Array} */
          this["arrayResults" + this._testIndex] = [];
          /** @type {Array} */
          this._progressResults["arrayProgressResults" + this._testIndex] = [];
          xhr = new window.xmlHttpRequest("POST", this.url, this.timeout, this.onTestComplete.bind(this), this.onTestProgress.bind(this), this.onTestAbort.bind(this), this.onTestTimeout.bind(this), this.onTestError.bind(this));
          this._activeTests.push({
            xhr : xhr,
            testRun : this._testIndex
          });
          xhr.start(this.uploadSize, this._testIndex);
        }
      }
    }
  };
  /**
   * @return {undefined}
   */
  complete.prototype.abortAll = function() {
    /** @type {boolean} */
    this._running = false;
    /** @type {number} */
    var j = 0;
    for (;j < this._activeTests.length;j++) {
      if ("undefined" != typeof this._activeTests[j]) {
        this._activeTests[j].xhr._request.abort();
      }
    }
  };
  /**
   * @return {undefined}
   */
  complete.prototype.initiateTest = function() {
    /** @type {number} */
    this._testIndex = 0;
    /** @type {number} */
    this._results.length = 0;
    /** @type {number} */
    this._finalResults.length = 0;
    /** @type {number} */
    this._activeTests.length = 0;
    this._progressResults = {};
    /** @type {number} */
    this._progressCount = 0;
    /** @type {boolean} */
    this._running = true;
    /** @type {number} */
    this._beginTime = Date.now();
    this.start();
  };
  /** @type {function (?, ?, number, ?, number, ?, ?, ?, ?): undefined} */
  window.uploadHttpConcurrent = complete;
}(), function() {
  /**
   * @param {?} url
   * @param {?} type
   * @param {number} a
   * @param {?} val
   * @param {number} off
   * @param {?} b
   * @param {?} args
   * @param {?} calipso
   * @param {?} c
   * @param {?} w
   * @param {?} d
   * @return {undefined}
   */
  function t(url, type, a, val, off, b, args, calipso, c, w, d) {
    this.url = url;
    this.type = type;
    this.uploadSize = d;
    /** @type {number} */
    this.concurrentRuns = a;
    this.timeout = val;
    /** @type {number} */
    this.testLength = off;
    this.clientCallbackComplete = calipso;
    this.clientCallbackProgress = c;
    this.clientCallbackError = w;
    this.movingAverage = b;
    this.uiMovingAverage = args;
    /** @type {number} */
    this._testIndex = 0;
    /** @type {Array} */
    this._results = [];
    /** @type {Array} */
    this._finalResults = [];
    /** @type {Array} */
    this._activeTests = [];
    /** @type {number} */
    this._beginTime = Date.now();
    /** @type {boolean} */
    this._running = true;
    this._progressResults = {};
    /** @type {number} */
    this._progressCount = 0;
    /** @type {number} */
    this.uiProgressCount = 0;
    /** @type {boolean} */
    this._collectMovingAverages = false;
    /** @type {null} */
    this._payload = null;
    /** @type {null} */
    this.interval = null;
  }
  /**
   * @param {number} max
   * @return {?}
   */
  function init(max) {
    /**
     * @return {?}
     */
    function randomString() {
      return Math.random().toString();
    }
    /** @type {number} */
    var maxChars = max / 2;
    var str = randomString();
    for (;str.length <= maxChars;) {
      str += randomString();
    }
    str += str.substring(0, max - str.length);
    var r;
    try {
      /** @type {Blob} */
      r = new Blob([str], {
        type : "application/octet-stream"
      });
    } catch (f) {
      /** @type {BlobBuilder} */
      var bb = new BlobBuilder;
      bb.append(str);
      /** @type {Blob} */
      r = bb.getBlob("application/octet-stream");
    }
    return r;
  }
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  t.prototype.onTestTimeout = function(deepDataAndEvents) {
    if (this._running) {
      this.clientCallbackError(deepDataAndEvents);
      /** @type {boolean} */
      this._running = false;
    }
  };
  /**
   * @return {undefined}
   */
  t.prototype.onTestAbort = function() {
    if (this._running) {
      if (Date.now() - this._beginTime > this.testLength) {
        if (this._finalResults && this._finalResults.length) {
          this.clientCallbackComplete(this._finalResults);
        } else {
          this.clientCallbackError("no measurements obtained");
        }
        /** @type {boolean} */
        this._running = false;
      }
    }
  };
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  t.prototype.onTestError = function(deepDataAndEvents) {
    if (this._running) {
      this.clientCallbackError(deepDataAndEvents);
      /** @type {boolean} */
      this._running = false;
    }
  };
  /**
   * @param {?} a
   * @return {undefined}
   */
  t.prototype.onTestComplete = function(a) {
    if (this._running) {
      /** @type {boolean} */
      this._collectMovingAverages = false;
      if (1 === this.concurrentRuns) {
        if (0 === this._progressCount) {
          this.clientCallbackProgress(a.bandwidth);
          this._finalResults.push(a.bandwidth);
        }
      }
      /** @type {number} */
      var j = 0;
      for (;j < this._activeTests.length;j++) {
        if ("undefined" != typeof this._activeTests[j]) {
          this._activeTests[j].xhr._request.abort();
        }
      }
      /** @type {number} */
      this._activeTests.length = 0;
      if (Date.now() - this._beginTime < this.testLength) {
        /** @type {number} */
        this._progressCount = 0;
        this.start();
      } else {
        if (this._running) {
          /** @type {boolean} */
          this._running = false;
          if (this._finalResults && this._finalResults.length) {
            this.clientCallbackComplete(this._finalResults);
          } else {
            this.clientCallbackError("no measurements obtained");
          }
        }
      }
    }
  };
  /**
   * @return {undefined}
   */
  t.prototype.calculateStats = function() {
    /** @type {number} */
    var copies = 0;
    /** @type {number} */
    var topLevelPrimitive = 0;
    for (;topLevelPrimitive < this.concurrentRuns;topLevelPrimitive++) {
      /** @type {number} */
      var i = this._testIndex - topLevelPrimitive;
      /** @type {string} */
      var uid = "arrayProgressResults" + i;
      /** @type {number} */
      var steps = Math.min(this._progressResults[uid].length, this.movingAverage);
      if (steps > 0) {
        /** @type {number} */
        var t = 0;
        /** @type {number} */
        var step = 1;
        for (;step <= steps;step++) {
          if (isFinite(this._progressResults[uid][this._progressResults[uid].length - step])) {
            t += this._progressResults[uid][this._progressResults[uid].length - step];
          }
        }
        t /= steps;
        copies += t;
      }
    }
    if (this.uiProgressCount % this.uiMovingAverage === 0) {
      this.updateUi();
    }
    this._finalResults.push(copies);
  };
  /**
   * @return {undefined}
   */
  t.prototype.updateUi = function() {
    /** @type {number} */
    var d = Math.min(this._finalResults.length, this.movingAverage);
    if (d > 0) {
      /** @type {number} */
      var t = 0;
      /** @type {number} */
      var start = 1;
      for (;start <= d;start++) {
        t += this._finalResults[this._finalResults.length - start];
      }
      t /= d;
      this.clientCallbackProgress(t);
    }
  };
  /**
   * @param {Element} a
   * @return {undefined}
   */
  t.prototype.onTestProgress = function(a) {
    if (this._running) {
      if (Date.now() - this._beginTime > this.testLength) {
        clearInterval(this.interval);
        this.abortAll();
        if (this._finalResults && this._finalResults.length) {
          this.clientCallbackComplete(this._finalResults);
        } else {
          this.clientCallbackError("no measurements obtained");
        }
        /** @type {boolean} */
        this._running = false;
      }
      if (this._collectMovingAverages) {
        this._progressCount++;
        this.uiProgressCount++;
        this._progressResults["arrayProgressResults" + a.id].push(a.bandwidth);
        if (this._progressCount % this.movingAverage === 0) {
          this.calculateStats();
        }
      }
    }
  };
  /**
   * @return {undefined}
   */
  t.prototype.start = function() {
    var xhr;
    if (this._running) {
      if ("GET" === this.type) {
        /** @type {number} */
        var concurrentRuns = 1;
        for (;concurrentRuns <= this.concurrentRuns;concurrentRuns++) {
          this._testIndex++;
          /** @type {Array} */
          this["arrayResults" + this._testIndex] = [];
          /** @type {Array} */
          this._progressResults["arrayProgressResults" + this._testIndex] = [];
          xhr = new window.xmlHttpRequest("POST", this.url, this.timeout, this.onTestComplete.bind(this), this.onTestProgress.bind(this), this.onTestAbort.bind(this), this.onTestTimeout.bind(this), this.onTestError.bind(this));
          this._activeTests.push({
            xhr : xhr,
            testRun : this._testIndex
          });
          xhr.start(0, this._testIndex);
        }
      } else {
        /** @type {number} */
        var d = 1;
        for (;d <= this.concurrentRuns;d++) {
          this._testIndex++;
          /** @type {Array} */
          this["arrayResults" + this._testIndex] = [];
          /** @type {Array} */
          this._progressResults["arrayProgressResults" + this._testIndex] = [];
          xhr = new window.xmlHttpRequest("POST", this.url, this.timeout, this.onTestComplete.bind(this), this.onTestProgress.bind(this), this.onTestAbort.bind(this), this.onTestTimeout.bind(this), this.onTestError.bind(this));
          this._activeTests.push({
            xhr : xhr,
            testRun : this._testIndex
          });
          if (null === this._payload) {
            this._payload = init(this.uploadSize);
          }
          xhr.start(this.uploadSize, this._testIndex, this._payload);
        }
        /** @type {boolean} */
        this._collectMovingAverages = true;
      }
    }
  };
  /**
   * @return {undefined}
   */
  t.prototype.abortAll = function() {
    /** @type {boolean} */
    this._running = false;
    /** @type {number} */
    var j = 0;
    for (;j < this._activeTests.length;j++) {
      if ("undefined" != typeof this._activeTests[j]) {
        this._activeTests[j].xhr._request.abort();
      }
    }
  };
  /**
   * @return {undefined}
   */
  t.prototype._monitor = function() {
    if (Date.now() - this._beginTime > this.testLength) {
      clearInterval(this.interval);
      /** @type {boolean} */
      this._running = false;
      /** @type {boolean} */
      this._collectMovingAverages = false;
      clearInterval(this.interval);
      if (this._finalResults && this._finalResults.length) {
        this.clientCallbackComplete(this._finalResults);
      } else {
        this.clientCallbackError("no measurements obtained");
      }
      this.abortAll();
    }
  };
  /**
   * @return {undefined}
   */
  t.prototype.initiateTest = function() {
    /** @type {number} */
    this._testIndex = 0;
    /** @type {number} */
    this._results.length = 0;
    /** @type {number} */
    this._finalResults.length = 0;
    /** @type {number} */
    this._activeTests.length = 0;
    this._progressResults = {};
    /** @type {number} */
    this._progressCount = 0;
    /** @type {number} */
    this.uiProgressCount = 0;
    /** @type {boolean} */
    this._running = true;
    /** @type {boolean} */
    this._collectMovingAverages = false;
    /** @type {null} */
    this._payload = null;
    /** @type {number} */
    this._beginTime = Date.now();
    /** @type {null} */
    this.interval = null;
    this.start();
    var stage = this;
    /** @type {number} */
    this.interval = setInterval(function() {
      stage._monitor();
    }, 100);
  };
  /** @type {function (?, ?, number, ?, number, ?, ?, ?, ?, ?, ?): undefined} */
  window.uploadHttpConcurrentProgress = t;
}(), function() {
  /**
   * @param {string} i
   * @param {?} r
   * @param {string} n
   * @param {number} a
   * @param {number} size
   * @param {?} c
   * @param {?} col
   * @return {undefined}
   */
  function e(i, r, n, a, size, c, col) {
    /** @type {string} */
    this.probeTestUrl = i + "?bufferSize=" + size + "&time=0&lowLatency=" + n;
    this.dataUrl = r;
    /** @type {string} */
    this.lowLatency = n;
    /** @type {number} */
    this.timeout = a;
    /** @type {number} */
    this.size = size;
    /** @type {number} */
    this._testIndex = 0;
    /** @type {Array} */
    this._activeTests = [];
    /** @type {boolean} */
    this._running = true;
    this.clientCallbackComplete = c;
    this.clientCallbackError = col;
    /** @type {number} */
    this.probeTimeout = 1E3;
    /** @type {null} */
    this.interval = null;
  }
  /**
   * @param {number} max
   * @return {?}
   */
  function init(max) {
    /**
     * @return {?}
     */
    function randomString() {
      return Math.random().toString();
    }
    /** @type {number} */
    var maxChars = max / 2;
    var str = randomString();
    for (;str.length <= maxChars;) {
      str += randomString();
    }
    str += str.substring(0, max - str.length);
    var r;
    try {
      /** @type {Blob} */
      r = new Blob([str], {
        type : "application/octet-stream"
      });
    } catch (f) {
      /** @type {BlobBuilder} */
      var bb = new BlobBuilder;
      bb.append(str);
      /** @type {Blob} */
      r = bb.getBlob("application/octet-stream");
    }
    return r;
  }
  /**
   * @return {undefined}
   */
  e.prototype.start = function() {
    this._test = new window.xmlHttpRequest("POST", this.probeTestUrl, this.timeout, this.onTestComplete.bind(this), this.onTestProgress.bind(this), this.onTestAbort.bind(this), this.onTestTimeout.bind(this), this.onTestError.bind(this));
    this._testIndex++;
    /** @type {boolean} */
    this._running = true;
    this._activeTests.push({
      xhr : this._test,
      testRun : this._testIndex
    });
    this._test.start(this.size, this._testIndex, init(this.size));
    var stage = this;
    /** @type {number} */
    this.interval = setInterval(function() {
      stage._monitor();
    }, 100);
  };
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  e.prototype.onTestError = function(deepDataAndEvents) {
    this.clientCallbackError(deepDataAndEvents);
    clearInterval(this.interval);
  };
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  e.prototype.onTestAbort = function(deepDataAndEvents) {
    clearInterval(this.interval);
    if (this._running) {
      this.clientCallbackError(deepDataAndEvents);
    }
  };
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  e.prototype.onTestTimeout = function(deepDataAndEvents) {
    clearInterval(this.interval);
    this.clientCallbackError(deepDataAndEvents);
  };
  /**
   * @param {Object} m
   * @return {undefined}
   */
  e.prototype.onTestComplete = function(m) {
    clearInterval(this.interval);
    var self = this;
    /** @type {XMLHttpRequest} */
    var xhr = new XMLHttpRequest;
    /**
     * @return {undefined}
     */
    xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        /** @type {boolean} */
        self._running = false;
        /** @type {string} */
        var resp = xhr.responseText;
        self.clientCallbackComplete(resp);
      }
    };
    var tref;
    /** @type {number} */
    tref = setTimeout(xhr.abort.bind(xhr), this.probeTimeout);
    /**
     * @return {undefined}
     */
    xhr.abort = function() {
      self.clientCallbackError(m);
      clearTimeout(tref);
    };
    xhr.open("GET", this.dataUrl + "?bufferSize=" + this.size + "&time=" + m.totalTime + "&lowLatency=" + this.lowLatency + "&r=" + Math.random(), true);
    xhr.send(null);
  };
  /**
   * @param {?} dataAndEvents
   * @return {undefined}
   */
  e.prototype.onTestProgress = function(dataAndEvents) {
  };
  /**
   * @return {undefined}
   */
  e.prototype.abortAll = function() {
    /** @type {boolean} */
    this._running = false;
    /** @type {number} */
    var j = 0;
    for (;j < this._activeTests.length;j++) {
      if ("undefined" != typeof this._activeTests[j]) {
        this._activeTests[j].xhr._request.abort();
      }
    }
  };
  /**
   * @return {undefined}
   */
  e.prototype._monitor = function() {
    if (Date.now() - this._beginTime > this.timeout) {
      this.clientCallbackError("probe timed out.");
      clearInterval(this.interval);
      this.abortAll();
    }
  };
  /** @type {function (string, ?, string, number, number, ?, ?): undefined} */
  window.uploadProbeTest = e;
}(), function() {
  /**
   * @param {?} url
   * @param {?} res
   * @param {number} err
   * @param {?} key
   * @param {?} name
   * @return {undefined}
   */
  function self(url, res, err, key, name) {
    this.url = url;
    this.type = res;
    /** @type {number} */
    this.transferSize = err;
    this.callbackOnMessage = key;
    this.callbackOnError = name;
  }
  /**
   * @return {undefined}
   */
  self.prototype.start = function() {
    if (!(null !== this._request && "undefined" != typeof this._request)) {
      /** @type {WebSocket} */
      this._request = new WebSocket(this.url);
      this._request.onopen = this._handleOnOpen.bind(this);
      this._request.onmessage = this._handleOnMessage.bind(this);
      this._request.onclose = this._handleOnClose.bind(this);
      this._request.onerror = this._handleOnError.bind(this);
    }
  };
  /**
   * @return {undefined}
   */
  self.prototype._handleOnOpen = function() {
    var responseObj = {
      data : Date.now().toString(),
      flag : "latency"
    };
    this._request.send(JSON.stringify(responseObj), {
      mask : true
    });
  };
  /**
   * @return {undefined}
   */
  self.prototype.sendMessage = function() {
    var responseObj = {
      data : Date.now().toString(),
      flag : "latency"
    };
    this._request.send(JSON.stringify(responseObj), {
      mask : true
    });
  };
  /**
   * @param {MessageEvent} event
   * @return {undefined}
   */
  self.prototype._handleOnMessage = function(event) {
    /** @type {number} */
    var element = Date.now() - parseInt(event.data);
    var e = {};
    /** @type {number} */
    e.time = element;
    /** @type {string} */
    e.unit = "ms";
    this.callbackOnMessage(e);
  };
  /**
   * @param {?} deepDataAndEvents
   * @return {undefined}
   */
  self.prototype._handleOnError = function(deepDataAndEvents) {
    this.callbackOnError(deepDataAndEvents);
  };
  /**
   * @param {Object} err
   * @return {undefined}
   */
  self.prototype._handleOnClose = function(err) {
    if (null !== err) {
      if (1006 === err.code) {
        this.callbackOnError("connection error");
      }
    }
  };
  /**
   * @return {undefined}
   */
  self.prototype.close = function() {
    this._request.close();
  };
  /** @type {function (?, ?, number, ?, ?): undefined} */
  window.webSocket = self;
}(), function() {
  /**
   * @param {?} method
   * @param {?} url
   * @param {?} val
   * @param {?} err
   * @param {?} key
   * @param {?} name
   * @param {?} callbackTimeout
   * @param {?} count
   * @param {number} child
   * @return {undefined}
   */
  function self(method, url, val, err, key, name, callbackTimeout, count, child) {
    this.method = method;
    this.url = url;
    this.timeout = val;
    /** @type {null} */
    this.startTime = null;
    /** @type {null} */
    this.endTime = null;
    /** @type {null} */
    this.bandwidth = null;
    /** @type {number} */
    this.latency = 0;
    /** @type {null} */
    this.id = null;
    /** @type {number} */
    this.prevTime = 0;
    /** @type {number} */
    this.prevLoad = 0;
    /** @type {number} */
    this.progressCount = 0;
    /** @type {number} */
    this.totalBytes = 0;
    /** @type {number} */
    this.currentTime = 0;
    /** @type {number} */
    this.progressIntervalDownload = child;
    /** @type {number} */
    this.progressIntervalUpload = 50;
    this.callbackComplete = err;
    this.callbackProgress = key;
    this.callbackAbort = name;
    this.callbackTimeout = callbackTimeout;
    this.callbackError = count;
    /** @type {null} */
    this.requestTimeout = null;
    /** @type {null} */
    this._request = null;
  }
  /**
   * @return {undefined}
   */
  self.prototype._initiateRequest = function() {
    if (!(null !== this._request && "undefined" != typeof this._request)) {
      /** @type {XMLHttpRequest} */
      this._request = new XMLHttpRequest;
      this._request.onloadstart = this._handleLoadstart.bind(this);
      this._request.onload = this._handleLoad.bind(this);
      this._request.onabort = this._handleAbort.bind(this);
      this._request.timout = this._handleTimeout.bind(this);
      /** @type {number} */
      this.requestTimeout = setTimeout(this._request.abort.bind(this._request), this.timeout);
      this._request.onerror = this._handleError.bind(this);
      this._request.onreadystatechange = this._handleOnReadyStateChange.bind(this);
      if ("GET" === this.method) {
        this._request.onprogress = this._handleOnProgressDownload.bind(this);
      } else {
        this._request.upload.onprogress = this._handleOnProgressUpload.bind(this);
      }
    }
  };
  /**
   * @param {number} mayParseLabeledStatementInstead
   * @param {?} id
   * @param {?} body
   * @return {undefined}
   */
  self.prototype.start = function(mayParseLabeledStatementInstead, id, body) {
    this._initiateRequest();
    this.id = id;
    /** @type {number} */
    this.transferSize = mayParseLabeledStatementInstead;
    this._request.open(this.method, this.url, true);
    this._request.timeout = this.timeout;
    if ("POST" === this.method) {
      this._request.send(body);
    } else {
      this._request.send(null);
    }
  };
  /**
   * @return {undefined}
   */
  self.prototype._handleLoadstart = function() {
    /** @type {number} */
    this.startTime = Date.now();
    /** @type {number} */
    this.prevTime = Date.now();
  };
  /**
   * @return {undefined}
   */
  self.prototype._handleError = function() {
    var resultObj = {
      statusText : this._request.statusText,
      status : this._request.status
    };
    this.callbackError(resultObj);
  };
  /**
   * @param {Object} event
   * @return {undefined}
   */
  self.prototype._handleTimeout = function(event) {
    /** @type {number} */
    this.totalTime = this.endTime - this.startTime;
    /** @type {number} */
    var a = 8 * event.loaded / 1E6;
    /** @type {number} */
    var b = this.totalTime / 1E3;
    var options = {};
    /** @type {number} */
    options.latency = this.totalTime;
    /** @type {number} */
    options.bandwidth = a / b;
    options.id = this.id;
    this.callbackTimeout(options);
  };
  /**
   * @param {Object} event
   * @return {undefined}
   */
  self.prototype._handleAbort = function(event) {
    clearTimeout(this.requestTimeout);
    /** @type {number} */
    this.totalTime = Date.now() - this.startTime;
    /** @type {number} */
    var x = 8 * event.loaded / 1E6;
    /** @type {number} */
    var f = this.totalTime / 1E3;
    var result = {};
    /** @type {number} */
    result.time = this.totalTime;
    result.loaded = event.loaded;
    /** @type {number} */
    result.bandwidth = x / f;
    result.id = this.id;
    this.callbackAbort(result);
  };
  /**
   * @return {undefined}
   */
  self.prototype.close = function() {
    this._request.abort();
  };
  /**
   * @return {?}
   */
  self.prototype._handleOnReadyStateChange = function() {
    if (4 === this._request.readyState && 200 === this._request.status) {
      var root = {};
      if (root.totalTime = Date.now() - this.startTime, root.id = this.id, "POST" === this.method) {
        /** @type {number} */
        var a = 8 * this.transferSize / 1E6;
        /** @type {number} */
        var b = root.totalTime / 1E3;
        return root.bandwidth = a / b, void(isFinite(root.bandwidth) && this.callbackComplete(root));
      }
    }
    if (this._request.status > 399) {
      var resultObj = {
        statusText : this._request.statusText,
        status : this._request.status
      };
      return void this.callbackError(resultObj);
    }
  };
  /**
   * @param {Object} event
   * @return {undefined}
   */
  self.prototype._handleLoad = function(event) {
    /** @type {number} */
    this.totalTime = Date.now() - this.startTime;
    var e = {};
    /** @type {number} */
    e.time = this.totalTime;
    this.totalBytes += event.loaded;
    /** @type {number} */
    var x = 8 * event.loaded / 1E6;
    /** @type {number} */
    var factor = this.totalTime / 1E3;
    /** @type {number} */
    e.bandwidth = x / factor;
    e.loaded = event.loaded;
    e.id = this.id;
    if (isFinite(e.bandwidth)) {
      if ("GET" === this.method) {
        this.callbackComplete(e);
      }
    }
  };
  /**
   * @param {Object} event
   * @return {undefined}
   */
  self.prototype._handleOnProgressDownload = function(event) {
    if (this.progressCount > 1) {
      var options = {};
      options.id = this.id;
      /** @type {number} */
      this.currentTime = Date.now();
      /** @type {number} */
      options.totalTime = this.currentTime - this.prevTime;
      /** @type {number} */
      var a = 8 * (event.loaded - this.prevLoad) / 1E6;
      if (options.totalTime > this.progressIntervalDownload) {
        /** @type {number} */
        var b = options.totalTime / 1E3;
        /** @type {number} */
        options.bandwidth = a / b;
        options.loaded = event.loaded;
        options.startTime = this.startTime;
        if (isFinite(options.bandwidth)) {
          this.callbackProgress(options);
          /** @type {number} */
          this.prevTime = this.currentTime;
          this.prevLoad = event.loaded;
        }
      }
    }
    this.progressCount++;
  };
  /**
   * @param {Object} e
   * @return {undefined}
   */
  self.prototype._handleOnProgressUpload = function(e) {
    if (this.progressCount > 1 && e.lengthComputable) {
      var root = {};
      if (root.id = this.id, this.currentTime = Date.now(), root.totalTime = this.currentTime - this.prevTime, root.totalTime > this.progressIntervalUpload) {
        /** @type {number} */
        var a = 8 * (e.loaded - this.prevLoad) / 1E6;
        /** @type {number} */
        var b = root.totalTime / 1E3;
        /** @type {number} */
        root.bandwidth = a / b;
        if (isFinite(root.bandwidth)) {
          this.callbackProgress(root);
          /** @type {number} */
          this.prevTime = this.currentTime;
          this.prevLoad = e.loaded;
        }
      }
    }
    this.progressCount++;
  };
  /** @type {function (?, ?, ?, ?, ?, ?, ?, ?, number): undefined} */
  window.xmlHttpRequest = self;
}(), angular.module("page-module.help", []), angular.module("page.results.module", ["directive.speedtest-results"]).controller("ResultsPageController", ["$scope", "speedtestResultsService", "$stateParams", "$state", function($scope, $this, depMap, api) {
  /** @type {string} */
  var validate = "";
  var id = depMap.id;
  /** @type {boolean} */
  $scope.resultsVisible = false;
  /** @type {Array} */
  $scope.speedTestResults = [];
  if (id) {
    $this.getResults(validate, id).then(function(response) {
      /** @type {boolean} */
      $scope.resultsVisible = true;
      $scope.speedTestResults = {};
      angular.forEach(response.data, function(data) {
        $scope.speedTestResults[data.ipAddress] = {
          ipAddress : data.ipAddress,
          latency : data.latency,
          download : data.downloadspeed,
          upload : data.uploadSpeed,
          clientIpAddress : data.clientIpAddress,
          downloadPeak : data.downloadPeak,
          uploadPeak : data.uploadPeak,
          serverLocation : data.serverLocation,
          serverSitename : data.serverSitename,
          serverIpAddress : data.serverIpAddress
        };
      });
      if ($scope.speedTestResults.IPv4.clientIpAddress || $scope.speedTestResults.IPv6.clientIpAddress) {
        $scope.clientIpAddress = $scope.speedTestResults.IPv6 ? $scope.speedTestResults.IPv6.clientIpAddress : $scope.speedTestResults.IPv4.clientIpAddress;
        $scope.serverIpAddress = $scope.speedTestResults.IPv4.serverIpAddress;
      }
      if ($scope.speedTestResults.IPv4.serverLocation) {
        $scope.serverLocation = $scope.speedTestResults.IPv4.serverLocation;
        $scope.serverSitename = $scope.speedTestResults.IPv4.serverSitename;
      }
    }, function() {
      api.go("error");
    });
  } else {
    api.go("main");
  }
}]), function() {
  /**
   * @param {string} self
   * @param {Object} $scope
   * @param {?} allBindingsAccessor
   * @param {Object} module
   * @param {?} depMaps
   * @param {?} done
   * @param {Object} values
   * @param {?} offset
   * @param {Object} params
   * @param {?} rootjQuery
   * @param {?} opt_setup
   * @param {?} client
   * @param {?} session
   * @param {?} logger
   * @param {?} element
   * @param {?} parser
   * @return {undefined}
   */
  var CustomerEditController = function init(self, $scope, allBindingsAccessor, module, depMaps, done, values, offset, params, rootjQuery, opt_setup, client, session, logger, element, parser) {
    /**
     * @param {string} node
     * @return {undefined}
     */
    function render(node) {
      parser.getServerLocation(node, config.LOCATIONS_TIMEOUT).then(function(response) {
        /** @type {boolean} */
        P = true;
        if (response.data) {
          if (response.data.Item) {
            path = response.data.Item.Sitename.S;
            url = response.data.Item.Location.S;
          }
        }
      })["catch"](function(generatedLine) {
        var e = {
          "client-ip" : $scope.clientIPv4Address
        };
        logger.logError(e, config.CONTROLLER_NAME, "getServerLocation", JSON.stringify(generatedLine));
        /** @type {boolean} */
        P = true;
      });
    }
    /**
     * @param {string} element
     * @return {undefined}
     */
    function get(element) {
      /**
       * @param {Object} item
       * @return {undefined}
       */
      function get(item) {
        if ("{}" !== JSON.stringify(item)) {
          /** @type {string} */
          data.baseUrlIPv4 = item.IPv4Address + ":" + data.port;
          /** @type {string} */
          data.baseUrlIPv6 = "[" + item.IPv6Address + "]:" + data.port;
          /** @type {string} */
          data.webSocketUrlIPv4 = "ws://" + item.IPv4Address + ":" + data.webSocketPort;
          /** @type {string} */
          data.webSocketUrlIPv6 = "ws://v6-" + item.Fqdn + ":" + data.webSocketPort;
          path = item.Sitename;
          url = item.Location;
          /** @type {boolean} */
          P = true;
        } else {
          /** @type {boolean} */
          P = true;
        }
      }
      /**
       * @param {?} err
       * @return {undefined}
       */
      function onError(err) {
        /** @type {boolean} */
        P = true;
        var e = {
          "client-ip" : $scope.clientIPv4Address
        };
        logger.logError(e, config.CONTROLLER_NAME, "getNearestServer", JSON.stringify(err));
      }
      var wrapper = new window.latencyBasedRouting(element, "/api/testservers", config.TEST_SERVER_TIMEOUT, config.LATENCY_BASED_ROUTING_TIMEOUT, get, onError);
      wrapper.getNearestServer();
    }
    /**
     * @param {?} deferred
     * @return {undefined}
     */
    function fn(deferred) {
      var templatePromise = values(function() {
        if (P) {
          deferred.resolve();
          values.cancel(templatePromise);
          $scope.finalResultsIPv4.appVersion = data.appVersion;
          $scope.finalResultsIPv4.clientIp = $scope.clientIPv4Address;
          $scope.finalResultsIPv4.sitename = path;
          $scope.finalResultsIPv4.location = url;
          /** @type {string} */
          arr = "http://" + data.baseUrlIPv4 + "/download";
          /** @type {string} */
          options = "http://" + data.baseUrlIPv4 + "/uploadProbe";
          /** @type {string} */
          key = "http://" + data.baseUrlIPv4 + "/upload";
          /** @type {string} */
          name = "http://" + data.baseUrlIPv4 + "/calculator";
          if (vertical) {
            $scope.finalResultsIPv6.clientIp = $scope.clientIPv6Address;
            /** @type {string} */
            c = "http://" + data.baseUrlIPv6 + "/download";
            /** @type {string} */
            o = "http://" + data.baseUrlIPv6 + "/upload";
          }
          /** @type {string} */
          $scope.dialMessage = "TESTING PING ...";
          /** @type {string} */
          $scope.currentTest = "latency";
          /** @type {string} */
          $scope.testDuration = "0ms";
          /** @type {number} */
          $scope.testProgress = 0;
          /** @type {number} */
          $scope.currentValue = 0;
        } else {
          /** @type {string} */
          $scope.dialMessage = "FINDING CLOSEST SERVER ...";
        }
      }, 50);
    }
    /**
     * @param {string} name
     * @return {?}
     */
    function next(name) {
      return function() {
        var delay = module.defer();
        if (val2 = Date.now(), err) {
          delay.reject({
            msg : ["latency", name + "Test", "failed"].join(" ")
          });
        } else {
          if ("IPv6" === name && !vertical) {
            return delay.resolve(), delay.promise;
          }
          var path = id ? data["webSocketUrl" + name] : "http://" + data["baseUrl" + name] + "/latency";
          void(id ? parse(path, name, 10, delay) : resolve(path, name, 10, delay));
        }
        return delay.promise;
      };
    }
    /**
     * @param {string} str
     * @param {string} arg
     * @param {number} opt_attributes
     * @param {Object} delay
     * @return {?}
     */
    function parse(str, arg, opt_attributes, delay) {
      /**
       * @param {Array} msg
       * @return {undefined}
       */
      function callback(msg) {
        notify(msg, arg, delay);
      }
      /**
       * @param {?} str
       * @return {undefined}
       */
      function isEmpty(str) {
      }
      /**
       * @param {?} obj
       * @return {?}
       */
      function promise(obj) {
        if ("IPv6" === arg) {
          var e = {
            "client-ip" : $scope.clientIPv4Address
          };
          return logger.logError(e, config.CONTROLLER_NAME, "IPv6WebSocketsError", JSON.stringify(obj)), vertical = false, delay.resolve(), delay.promise;
        }
        /** @type {boolean} */
        id = false;
        /** @type {string} */
        var json = "http://" + data.baseUrlIPv4 + "/latency";
        resolve(json, "IPv4", 10, delay);
      }
      var dataBuffer = new window.latencyWebSocketTest(str, "GET", "0", opt_attributes, config.LATENCY_TIMEOUT, callback, isEmpty, promise);
      return dataBuffer.initiateTest(), delay.promise;
    }
    /**
     * @param {string} path
     * @param {string} msg
     * @param {number} opt_attributes
     * @param {Object} delay
     * @return {undefined}
     */
    function resolve(path, msg, opt_attributes, delay) {
      /**
       * @param {Array} element
       * @return {?}
       */
      function next(element) {
        return err ? void delay.reject({
          msg : "performLatencyTest failed"
        }) : void notify(element, msg, delay);
      }
      /**
       * @param {?} object
       * @return {undefined}
       */
      function keys(object) {
      }
      /**
       * @return {undefined}
       */
      function map() {
        delay.reject({
          msg : "performLatencyTest::onabort",
          timeoutModalData : true
        });
      }
      /**
       * @return {undefined}
       */
      function fn() {
        if ("IPv6" === msg) {
          $scope.$apply(function() {
            /** @type {boolean} */
            vertical = false;
          });
        } else {
          $scope.$apply(function() {
            /** @type {boolean} */
            $scope.timeoutModalData.show = true;
          });
        }
        delay.reject({
          msg : "performLatencyTest::ontimeout"
        });
      }
      /**
       * @return {undefined}
       */
      function when() {
        delay.reject({
          msg : "performLatencyTest::onerror",
          timeoutModalData : true
        });
      }
      var outFile = new window.latencyHttpTest(path, opt_attributes, config.LATENCY_TIMEOUT, next, keys, map, fn, when);
      outFile.initiateTest();
    }
    /**
     * @param {Array} params
     * @param {string} arg
     * @param {Object} delay
     * @return {undefined}
     */
    function notify(params, arg, delay) {
      var width = params.length;
      /** @type {number} */
      var x = "IPv6" === arg ? 10 : 0;
      var dimension = vertical ? 2 * width : width;
      var y = "IPv6" === arg ? 2 * width : width;
      var templatePromise = values(function() {
        if (err) {
          return values.cancel(templatePromise), templatePromise = null, void delay.reject({
            msg : "performLatencyTest $interval failed"
          });
        }
        if (++x, $scope.currentValue = x / dimension * 100, x === y) {
          params.sort(function(e, frame) {
            return+e.time - +frame.time;
          });
          var copies = params[0].time;
          if ("IPv4" === arg) {
            /** @type {Array} */
            out = [];
            out.push(copies);
          }
          $scope["finalResults" + arg].latency = copies;
          $scope["latencyResult" + arg] = copies;
          delay.resolve();
        }
      }, config.LATENCY_INTERVAL, width, true);
    }
    /**
     * @param {string} url
     * @return {?}
     */
    function update(url) {
      return function() {
        var defer = module.defer();
        if (val2 = Date.now(), err) {
          defer.reject({
            msg : ["download", url + "Test", "failed"].join(" ")
          });
        } else {
          if ("IPv6" === url && !vertical) {
            return defer.resolve(), defer.promise;
          }
          /** @type {string} */
          $scope.currentResult = "--";
          /** @type {number} */
          $scope.testProgress = 0;
          /** @type {string} */
          $scope.dialMessage = "TESTING DOWNLOAD SPEED FOR " + url;
          done(function() {
            start(url, defer);
          }, $scope.testDelay);
        }
        return defer.promise;
      };
    }
    /**
     * @param {?} deferred
     * @return {undefined}
     */
    function onError(deferred) {
      /**
       * @param {number} type
       * @return {?}
       */
      function promise(type) {
        return output = config.UPLOAD_TEST_SIZE, type && (check() && (output = type)), deferred.resolve(), deferred.promise;
      }
      /**
       * @param {?} value
       * @return {?}
       */
      function fulfill(value) {
        return deferred.resolve(), deferred.promise;
      }
      var node = new window.uploadProbeTest(key, options, false, config.PROBE_TIMEOUT, config.UPLOAD_PROBE_SIZE, promise, fulfill);
      node.start();
    }
    /**
     * @param {string} string
     * @param {Object} defer
     * @return {undefined}
     */
    function start(string, defer) {
      /**
       * @param {Object} t
       * @return {?}
       */
      function render(t) {
        /** @type {string} */
        var i = "download";
        return t ? (done(function() {
          var mean = t.stats.mean;
          $scope["finalResults" + string][i + "Peak"] = t.peakValue.toFixed(2);
          var v0 = mean < 1 ? mean.toPrecision(2) : mean.toFixed(1);
          if ("IPv4" === string) {
            $scope["finalValues" + string][i] = v0;
          }
          $scope["finalResults" + string][i] = v0;
        }), defer.resolve()) : defer.reject({
          msg : "abort set - downloadBandwidth"
        }), defer.promise;
      }
      /**
       * @param {?} error
       * @return {?}
       */
      function initialize(error) {
        return defer.reject(error), defer.promise;
      }
      /**
       * @param {string} value
       * @return {undefined}
       */
      function interval(value) {
        /** @type {string} */
        obj["downloadResults" + string] = value;
        var node = new window.calculateStats(name, value, render, initialize);
        node.performCalculations();
        /** @type {number} */
        $scope.testProgress = 100;
        clearInterval(scrollIntervalId);
      }
      /**
       * @param {number} value
       * @return {undefined}
       */
      function loaded(value) {
        $scope.currentResult = value < 1 ? value.toPrecision(2) : value.toFixed(1);
        $scope.currentValue = value < 1 ? value.toPrecision(2) : value.toFixed(1);
        $scope.$apply();
      }
      /**
       * @param {?} error
       * @return {?}
       */
      function check(error) {
        return clearInterval(scrollIntervalId), defer.reject(error), defer.promise;
      }
      /**
       * @param {?} err
       * @return {?}
       */
      function done(err) {
        return clearInterval(scrollIntervalId), defer.reject(err), defer.promise;
      }
      /**
       * @param {?} err
       * @return {?}
       */
      function handler(err) {
        return clearInterval(scrollIntervalId), defer.reject(err), defer.promise;
      }
      /**
       * @return {undefined}
       */
      function loop() {
        /** @type {number} */
        var newVal = (Date.now() - start) / config.DOWNLOAD_TIMEOUT * 100;
        /** @type {string} */
        $scope.currentTest = "download";
        /** @type {number} */
        $scope.testProgress = newVal;
        if (Date.now() - start > config.DOWNLOAD_TIMEOUT) {
          clearInterval(scrollIntervalId);
        }
      }
      /** @type {null} */
      var scrollIntervalId = null;
      /** @type {null} */
      var start = null;
      var arr2 = "IPv6" === string ? c : arr;
      var httpRequest = new window.downloadHttpConcurrentProgress(arr2 + "?bufferSize=", "GET", config.DOWNLOAD_CURRENT_TESTS, config.DOWNLOAD_TIMEOUT, config.DOWNLOAD_TIMEOUT, config.DOWNLOAD_MOVINGAVERAGE, interval, loaded, check, done, handler, config.DOWNLOAD_SIZE, config.PROBE_TIMEOUT, config.DOWNLOAD_PROGRESS_INTERVAL, data.maxDownloadSize);
      httpRequest.initiateTest();
      /** @type {number} */
      start = Date.now();
      /** @type {number} */
      scrollIntervalId = setInterval(function() {
        loop();
      }, 750);
    }
    /**
     * @param {string} type
     * @return {?}
     */
    function search(type) {
      return function() {
        var defer = module.defer();
        if (val2 = Date.now(), err) {
          defer.reject({
            msg : ["upload", type + "Test", "failed"].join(" ")
          });
        } else {
          if ("IPv6" === type && !vertical) {
            return defer.resolve(), defer.promise;
          }
          /** @type {string} */
          $scope.currentResult = "--";
          /** @type {number} */
          $scope.currentValue = 0;
          /** @type {number} */
          $scope.testProgress = 0;
          /** @type {string} */
          $scope.dialMessage = "TESTING UPLOAD SPEED FOR " + type;
          done(function() {
            success(type, defer);
          }, $scope.testDelay);
        }
        return defer.promise;
      };
    }
    /**
     * @param {string} object
     * @param {Object} defer
     * @return {undefined}
     */
    function success(object, defer) {
      /**
       * @param {Object} t
       * @return {?}
       */
      function render(t) {
        /** @type {string} */
        var i = "upload";
        return t ? (done(function() {
          var mean = t.stats.mean;
          $scope["finalResults" + object][i + "Peak"] = t.peakValue.toFixed(2);
          var v0 = mean < 1 ? mean.toPrecision(2) : mean.toFixed(1);
          if ("IPv4" === object) {
            $scope["finalValues" + object][i] = v0;
          }
          $scope["finalResults" + object][i] = v0;
        }), defer.resolve()) : defer.reject({
          msg : "abort set - uploadBandwidth"
        }), values.cancel(templatePromise), templatePromise = null, defer.promise;
      }
      /**
       * @param {?} e
       * @return {?}
       */
      function onRejected(e) {
        return defer.reject(e), defer.promise;
      }
      /**
       * @param {string} fn
       * @return {undefined}
       */
      function nextTick(fn) {
        /** @type {string} */
        obj["uploadResults" + object] = fn;
        var test = new window.calculateStats(name, fn, render, onRejected);
        test.performCalculations();
        /** @type {number} */
        $scope.testProgress = 100;
        clearInterval(scrollIntervalId);
      }
      /**
       * @param {number} value
       * @return {undefined}
       */
      function _sprintf_format(value) {
        $scope.currentResult = value < 1 ? value.toPrecision(2) : value.toFixed(1);
        $scope.currentValue = value < 1 ? value.toPrecision(2) : value.toFixed(1);
        $scope.$apply();
      }
      /**
       * @param {?} err
       * @return {?}
       */
      function finish(err) {
        return clearInterval(scrollIntervalId), defer.reject(err), defer.promise;
      }
      /**
       * @return {undefined}
       */
      function next() {
        /** @type {number} */
        var newVal = (Date.now() - start) / config.UPLOAD_TIMEOUT * 100;
        /** @type {string} */
        $scope.currentTest = "upload";
        /** @type {number} */
        $scope.testProgress = newVal;
        if (Date.now() - start > config.UPLOAD_TIMEOUT) {
          clearInterval(scrollIntervalId);
        }
      }
      /** @type {null} */
      var scrollIntervalId = null;
      /** @type {null} */
      var start = null;
      var url = "IPv6" === object ? o : key;
      /** @type {number} */
      start = Date.now();
      if (!(navigator.appVersion.indexOf("MSIE") === -1 && (navigator.appVersion.indexOf("Trident") === -1 && navigator.appVersion.indexOf("Edge") === -1))) {
        /** @type {number} */
        output = config.MICROSOFT_UPLOAD_TEST_SIZE;
        /** @type {number} */
        config.UI_MOVING_AVERAGE = config.MICROSOFT_UI_MOVINGAVERAGE;
      }
      var req = new window.uploadHttpConcurrentProgress(url, "POST", config.UPLOAD_CURRENT_TESTS, config.UPLOAD_TIMEOUT, config.UPLOAD_TIMEOUT, config.UPLOAD_MOVINGAVERAGE, config.UI_MOVING_AVERAGE, nextTick, _sprintf_format, finish, output);
      req.initiateTest();
      /** @type {number} */
      scrollIntervalId = setInterval(function() {
        next();
      }, 750);
    }
    /**
     * @return {?}
     */
    function check() {
      return/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent);
    }
    /**
     * @return {undefined}
     */
    function setup() {
      var options = session.getToken();
      if (options) {
        client.getUserInfo(options.access_token).then(function(result) {
          n = result.userName;
          /** @type {boolean} */
          CIMA_LOGGED_IN = true;
        });
      } else {
        /** @type {boolean} */
        CIMA_LOGGED_IN = false;
      }
    }
    /**
     * @param {?} input
     * @return {?}
     */
    function load(input) {
      return client.getUserInfo(session.getToken()).then(function(dataAndEvents) {
        var ACCOUNTNAME = dataAndEvents.customerGuid || ($scope.clientIPv6Address || $scope.clientIPv4Address);
        return angular.extend(input, {
          ACCOUNTNAME : ACCOUNTNAME
        });
      });
    }
    _classCallCheck(this, init);
    var global = params.browser;
    var ret = global.name;
    var ver = global.version;
    /** @type {boolean} */
    var id = !!window.WebSocket;
    var config = {};
    /** @type {number} */
    config.TEST_TRANSITION_DURATION = 500;
    /** @type {number} */
    config.DOWNLOAD_TIMEOUT = 18E3;
    /** @type {number} */
    config.UPLOAD_TIMEOUT = 2E4;
    /** @type {number} */
    config.LATENCY_INTERVAL = 200;
    /** @type {number} */
    config.XHR_STATECOMPLETE = 4;
    /** @type {number} */
    config.XHR_STATUSSUCCESS = 200;
    /** @type {number} */
    config.DOWNLOAD_CURRENT_TESTS = 6;
    /** @type {number} */
    config.DOWNLOAD_MOVINGAVERAGE = 10;
    /** @type {number} */
    config.UPLOAD_CURRENT_TESTS = 1;
    /** @type {number} */
    config.UPLOAD_MOVINGAVERAGE = 1;
    /** @type {number} */
    config.PROBE_TIMEOUT = 3E3;
    /** @type {number} */
    config.LATENCY_TIMEOUT = 3E3;
    /** @type {number} */
    config.DOWNLOAD_SIZE = 1E4;
    /** @type {number} */
    config.UPLOAD_PROBE_SIZE = 194872;
    /** @type {string} */
    config.CONTROLLER_NAME = "speedtest.controller.js";
    /** @type {number} */
    config.UPLOAD_TEST_SIZE = 25526506;
    /** @type {number} */
    config.UI_MOVING_AVERAGE = 10;
    /** @type {number} */
    config.MICROSOFT_UPLOAD_TEST_SIZE = 17526506;
    /** @type {number} */
    config.MICROSOFT_UI_MOVINGAVERAGE = 2;
    /** @type {number} */
    config.TEST_SERVER_TIMEOUT = 2E3;
    /** @type {number} */
    config.LATENCY_BASED_ROUTING_TIMEOUT = 3E3;
    /** @type {number} */
    config.IPV6_TIMEOUT = 3E3;
    /** @type {number} */
    config.LOCATIONS_TIMEOUT = 3E3;
    /** @type {number} */
    config.DOWNLOAD_PROGRESS_INTERVAL = 10;
    var n;
    /** @type {boolean} */
    var CIMA_LOGGED_IN = false;
    /** @type {number} */
    var N = 100;
    var data = {};
    /** @type {boolean} */
    var P = false;
    /** @type {boolean} */
    var vertical = false;
    /** @type {number} */
    data.lowLatencyThreshold = 0;
    var path;
    var url;
    var arr;
    var c;
    var options;
    var key;
    var o;
    var name;
    /** @type {number} */
    var output = 10526506;
    /** @type {boolean} */
    var err = false;
    /** @type {Array} */
    var out = [];
    /** @type {string} */
    $scope.pingLabel = "Ping";
    /** @type {string} */
    $scope.downloadLabel = "Download";
    /** @type {string} */
    $scope.uploadLabel = "Upload";
    /** @type {string} */
    $scope.pingUnit = "ms";
    /** @type {string} */
    $scope.downloadUnit = "Mbps";
    /** @type {string} */
    $scope.uploadUnit = "Mbps";
    var obj = {};
    /** @type {Array} */
    obj.downloadResultsIPv4 = [];
    /** @type {Array} */
    obj.downloadResultsIPv6 = [];
    /** @type {Array} */
    obj.uploadResultsIPv4 = [];
    /** @type {Array} */
    obj.uploadResultsIPv6 = [];
    /**
     * @return {undefined}
     */
    $scope.tooltipPing = function() {
      /** @type {boolean} */
      $scope.pingVisible = true;
    };
    /**
     * @return {undefined}
     */
    $scope.tooltipDownload = function() {
      /** @type {boolean} */
      $scope.downloadVisible = true;
    };
    /**
     * @return {undefined}
     */
    $scope.tooltipUpload = function() {
      /** @type {boolean} */
      $scope.uploadVisible = true;
    };
    /**
     * @return {undefined}
     */
    $scope.startTest = function() {
      /** @type {string} */
      $scope.results = "";
      /** @type {boolean} */
      $scope.welcomeVisible = true;
      /** @type {boolean} */
      $scope.restartTestVisible = false;
      /** @type {string} */
      $scope.dialMessage = "";
      /** @type {boolean} */
      $scope.resultsButtonsVisible = false;
      /** @type {boolean} */
      $scope.startTestVisible = true;
      /** @type {boolean} */
      $scope.resultsHidden = true;
      /** @type {string} */
      $scope.latencyResultIPv4 = "--";
      /** @type {string} */
      $scope.latencyResultIPv6 = "--";
      /** @type {Array} */
      $scope.latencyResultsIPv4 = [];
      /** @type {Array} */
      $scope.latencyResultsIPv6 = [];
      /** @type {Array} */
      $scope.uploadResultsIPv4 = [];
      /** @type {Array} */
      $scope.uploadResultsIPv6 = [];
      /** @type {number} */
      $scope.testDelay = 2500;
      /** @type {Array} */
      $scope.uploadResults = [];
      /** @type {Array} */
      $scope.uploadRequests = [];
      /** @type {number} */
      $scope.concurrentUploadEvents = 0;
      /** @type {number} */
      $scope.uploadMovingAverage = 10;
      /** @type {number} */
      $scope.runUploadTestLimit = 2;
      /** @type {number} */
      $scope.uploadData = 0;
      /** @type {boolean} */
      $scope.isTestStarted = false;
      /** @type {boolean} */
      $scope.helpVisible = false;
      /** @type {boolean} */
      $scope.feedbackVisible = true;
      /** @type {boolean} */
      $scope.showShareResultsModal = false;
      if (!$scope.timeoutModalData) {
        $scope.timeoutModalData = {
          show : false
        };
      }
      window.timeoutModalData = $scope.timeoutModalData;
      $scope.finalResultsIPv6 = {};
      $scope.finalResultsIPv4 = {};
      $scope.finalValuesIPv4 = {};
      /** @type {Array} */
      out = [];
      /** @type {number} */
      $scope.currentValue = 0;
    };
    /**
     * @return {undefined}
     */
    $scope.restartTest = function() {
      /** @type {boolean} */
      $scope.timeoutModalData.show = false;
      $scope.startTest();
      /** @type {boolean} */
      err = false;
      /** @type {boolean} */
      $scope.abort = err;
      $scope.hideWelcome();
    };
    /**
     * @return {?}
     */
    $scope.getTestPlan = function() {
      return element.getTestPlan(config.IPV6_TIMEOUT).then(function(transformed) {
        /** @type {Object} */
        data = transformed;
        $scope.clientIPv4Address = data.clientIPv4Address;
        /** @type {number} */
        N = data.supportsIPv6 ? 200 : 100;
        if (data.supportsIPv6) {
          /** @type {boolean} */
          vertical = true;
          $scope.clientIPv6Address = data.clientIPv6Address;
        }
        if (data.clientLocation) {
          get(data.clientLocation);
        } else {
          render(data.osHostName);
        }
      }, function(generatedLine) {
        show("getTestPlan" + JSON.stringify(generatedLine));
      })["catch"](function(generatedLine) {
        show("getTestPlan" + JSON.stringify(generatedLine));
      });
    };
    var templatePromise;
    /** @type {number} */
    var val2 = Date.now();
    /**
     * @param {(Array|string)} e
     * @return {undefined}
     */
    var show = function(e) {
      var msg;
      var s = {
        "client-ip" : $scope.clientIPv4Address
      };
      if (e && e.msg) {
        if (e.timeoutModalData) {
          msg = e.msg;
          /** @type {boolean} */
          $scope.timeoutModalData.show = true;
        }
      } else {
        if ("string" == typeof e) {
          /** @type {(Array|string)} */
          msg = e;
        }
      }
      /** @type {boolean} */
      err = true;
      /** @type {boolean} */
      $scope.abort = err;
      $scope.startTest();
      /** @type {string} */
      $scope.dialMessage = "We're sorry, an error has occurred. Please try again in a few moments.";
      /** @type {string} */
      $scope.currentResult = "";
      /** @type {number} */
      $scope.currentValue = 0;
      /** @type {string} */
      $scope.currentTest = "";
      /** @type {number} */
      $scope.testDuration = 0;
      /** @type {number} */
      $scope.testProgress = 0;
      /** @type {boolean} */
      $scope.startTestVisible = false;
      /** @type {boolean} */
      $scope.restartTestVisible = true;
      /** @type {boolean} */
      $scope.backToWelcomeVisible = false;
      values.cancel(templatePromise);
      logger.logError(s, config.CONTROLLER_NAME, "showTestError", msg);
    };
    /**
     * @return {undefined}
     */
    $scope.hideWelcome = function() {
      /** @type {boolean} */
      $scope.isTestStarted = true;
      /** @type {boolean} */
      $scope.welcomeVisible = false;
      /** @type {string} */
      $scope.dialMessage = "";
      /** @type {boolean} */
      $scope.startTestVisible = false;
      /** @type {number} */
      obj.downloadResultsIPv4.length = 0;
      /** @type {number} */
      obj.downloadResultsIPv6.length = 0;
      /** @type {number} */
      obj.uploadResultsIPv4.length = 0;
      /** @type {number} */
      obj.uploadResultsIPv6.length = 0;
      /** @type {number} */
      val2 = Date.now();
      templatePromise = values(function() {
        /** @type {number} */
        var val1 = Date.now();
        if (Math.abs(val1 - val2) >= 1.25 * config.UPLOAD_TIMEOUT) {
          show("overall-catchall test");
          values.cancel(templatePromise);
          /** @type {null} */
          templatePromise = null;
        }
      }, 1.25 * config.UPLOAD_TIMEOUT);
      try {
        $scope.transitionToLatencyTest().then(next("IPv4"), show).then(next("IPv6"), show).then($scope.transitionToDownloadTest, show).then(update("IPv6"), show).then(update("IPv4"), show).then($scope.transitionToUploadTest, show).then($scope.uploadProbeIPv4Test, show).then(search("IPv6"), show).then(search("IPv4"), show).then($scope.speedtestComplete, show)["catch"](function(completeEvent) {
          show(completeEvent);
        });
      } catch (index) {
        show(index);
      }
    };
    /**
     * @return {?}
     */
    $scope.transitionToLatencyTest = function() {
      /** @type {number} */
      val2 = Date.now();
      var deferred = module.defer();
      return err ? deferred.reject({
        msg : "transitionToLatencyTest failed"
      }) : fn(deferred), deferred.promise;
    };
    /**
     * @return {?}
     */
    $scope.transitionToDownloadTest = function() {
      /** @type {number} */
      val2 = Date.now();
      var defer = module.defer();
      return err ? defer.reject({
        msg : "transitionToDownloadTest failed"
      }) : done(function() {
        /** @type {string} */
        $scope.currentTest = "download";
        /** @type {string} */
        $scope.testDuration = "0ms";
        /** @type {number} */
        $scope.testProgress = 0;
        /** @type {number} */
        $scope.currentValue = 0;
        /** @type {string} */
        $scope.dialMessage = "TESTING DOWNLOAD SPEED ...";
        /** @type {string} */
        $scope.currentResult = "--";
        if (1 === out.length) {
          $scope.finalValuesIPv4.latency = out[0];
          /** @type {Array} */
          out = [];
        }
        defer.resolve();
      }, 250), defer.promise;
    };
    /**
     * @return {?}
     */
    $scope.transitionToUploadTest = function() {
      /** @type {number} */
      val2 = Date.now();
      var defer = module.defer();
      return err ? defer.reject({
        msg : "transitionToUploadTest failed"
      }) : setTimeout(function() {
        /** @type {string} */
        $scope.currentTest = "upload";
        /** @type {string} */
        $scope.testDuration = "0ms";
        /** @type {number} */
        $scope.testProgress = 0;
        /** @type {number} */
        $scope.currentValue = 0;
        /** @type {string} */
        $scope.dialMessage = "TESTING UPLOAD SPEED ...";
        /** @type {string} */
        $scope.currentResult = "--";
        if (2 === out.length) {
          $scope.finalValuesIPv4[out[0]] = out[1];
          /** @type {Array} */
          out = [];
        }
        defer.resolve();
      }, 250), defer.promise;
    };
    /**
     * @return {?}
     */
    $scope.uploadProbeIPv4Test = function() {
      var deferred = module.defer();
      return val2 = Date.now(), err ? deferred.reject({
        msg : "upProbeIPv4Test failed"
      }) : done(function() {
        onError(deferred);
      }, $scope.testDelay), deferred.promise;
    };
    /**
     * @return {?}
     */
    $scope.speedtestComplete = function() {
      var defer = module.defer();
      return values.cancel(templatePromise), templatePromise = null, err ? defer.reject({
        msg : "speedTestComplete::abort"
      }) : ($scope.currentValue = 0, $scope.currentResult = "", $scope.dialMessage = "TEST COMPLETE", $scope.restartTestVisible = true, $scope.speedtestResults(), $scope.resultsButtonsVisible = true, $scope.currentTest = "", $scope.testDuration = 0, $scope.testProgress = 0, 2 === out.length && ($scope.finalValuesIPv4[out[0]] = out[1], out = []), defer.resolve()), defer.promise;
    };
    /**
     * @return {undefined}
     */
    $scope.speedtestResults = function() {
      var RESULT_DATE = offset("date")(Date.now(), "M/d/yyyy h:mm:ss");
      /** @type {number} */
      var text = +vertical;
      $scope.finalResultsIPv4.serverIpAddress = data.baseUrlIPv4.split(":")[0];
      var input = {
        RESULT_DATE : RESULT_DATE,
        SERVER_IPV4_ADDRESS : $scope.finalResultsIPv4.serverIpAddress,
        CLIENT_IPV4_ADDRESS : $scope.clientIPv4Address,
        IPV4_LATENCY : $scope.finalResultsIPv4.latency,
        IPV4_DOWNLOAD_MBPS : $scope.finalResultsIPv4.download,
        IPV4_UPLOAD_MBPS : $scope.finalResultsIPv4.upload,
        IPV4_DOWNLOAD_PEAK : $scope.finalResultsIPv4.downloadPeak,
        IPV4_UPLOAD_PEAK : $scope.finalResultsIPv4.uploadPeak,
        TEST_METHOD : "http",
        SERVER_NAME : "",
        OPERATIVE_SYSTEM : data.osType,
        PLATFORM : data.osPlatform,
        BROWSER_NAME : ret,
        BROWSER_VERSION : ver,
        SERVER_LOCATION : url,
        SERVER_SITENAME : path,
        CIMA_USERNAME : n,
        CIMA_LOGGED_IN : CIMA_LOGGED_IN
      };
      /** @type {string} */
      input.ipv4DownloadData = obj.downloadResultsIPv4.toString();
      /** @type {string} */
      input.ipv4UploadData = obj.uploadResultsIPv4.toString();
      input.supportWebSocket = id;
      if (text) {
        /** @type {number} */
        input.IPV6 = text;
        input.SERVER_IPV6_ADDRESS = data.baseUrlIPv6.replace(/[[]/g, "").split("]")[0];
        input.CLIENT_IPV6_ADDRESS = $scope.clientIPv6Address;
        input.IPV6_LATENCY = $scope.finalResultsIPv6.latency;
        input.IPV6_DOWNLOAD_MBPS = $scope.finalResultsIPv6.download;
        input.IPV6_UPLOAD_MBPS = $scope.finalResultsIPv6.upload;
        input.IPV6_DOWNLOAD_PEAK = $scope.finalResultsIPv6.downloadPeak;
        input.IPV6_UPLOAD_PEAK = $scope.finalResultsIPv6.uploadPeak;
        /** @type {string} */
        input.ipv6DownloadData = obj.downloadResultsIPv6.toString();
        /** @type {string} */
        input.ipv6UploadData = obj.uploadResultsIPv6.toString();
      }
      var headers = {
        headers : {
          "Content-Type" : "application/json;charset=UTF-8"
        }
      };
      load(input).then(function(command) {
        /** @type {string} */
        command = JSON.stringify([command]);
        self.post("/api/speedtestdatas", command, headers).then(function(evt) {
          /** @type {string} */
          $scope.resultId = window.location.origin + "/results/" + evt.data.id;
        }, function(completeEvent) {
          show(completeEvent);
        })["catch"](function(completeEvent) {
          show(completeEvent);
        });
      });
    };
    /**
     * @return {undefined}
     */
    $scope.shareResult = function() {
      /** @type {boolean} */
      $scope.showShareResultsModal = true;
    };
    /**
     * @return {undefined}
     */
    $scope.showResults = function() {
      /** @type {boolean} */
      $scope.resultsHidden = false;
    };
    /**
     * @return {undefined}
     */
    $scope.showWelcome = function() {
      window.location.reload();
    };
    $scope.startTest();
    $scope.getTestPlan();
    setup();
  };
  /** @type {Array} */
  CustomerEditController.$inject = ["$http", "$scope", "$log", "$q", "$cookies", "$timeout", "$interval", "$filter", "browserInfo", "latencyService", "websocketsService", "userInfoService", "cimaOauthService", "joustSplunkService", "testPlanService", "selectServerService"];
  angular.module("page.main.module", ["speedtest-directives.module"]).controller("MainController", CustomerEditController);
}(), function(ng, dataAndEvents) {
  ng.module("rmrSpeedTestApp.constants", []).constant("appConfig", {
    userRoles : ["guest", "user", "admin"]
  });
}(angular), angular.module("service.browserInfo", []).factory("browserInfo", function() {
  var methods = {
    options : [],
    header : [navigator.platform, navigator.userAgent, navigator.appVersion, navigator.vendor, window.opera],
    dataos : [{
      name : "Microsoft Windows Phone",
      value : "Windows Phone",
      version : "OS"
    }, {
      name : "Microsoft Windows",
      value : "Win",
      version : "NT"
    }, {
      name : "Apple iPhone",
      value : "iPhone",
      version : "OS"
    }, {
      name : "Apple iPad",
      value : "iPad",
      version : "OS"
    }, {
      name : "Amazon Kindle",
      value : "Silk",
      version : "Silk"
    }, {
      name : "Google Android",
      value : "Android",
      version : "Android"
    }, {
      name : "PlayBook",
      value : "PlayBook",
      version : "OS"
    }, {
      name : "RIM BlackBerry",
      value : "BlackBerry",
      version : "/"
    }, {
      name : "Apple Macintosh",
      value : "Mac",
      version : "OS X"
    }, {
      name : "Linux",
      value : "Linux",
      version : "rv"
    }, {
      name : "Palm",
      value : "Palm",
      version : "PalmOS"
    }],
    databrowser : [{
      name : "Google Chrome",
      value : "Chrome",
      version : "Chrome"
    }, {
      name : "Google Chrome for iOS",
      value : "CriOS",
      version : "CriOS"
    }, {
      name : "Mozilla Firefox",
      value : "Firefox",
      version : "Firefox"
    }, {
      name : "Apple Safari",
      value : "Safari",
      version : "Version"
    }, {
      name : "Microsoft Internet Explorer",
      value : "MSIE",
      version : "MSIE"
    }, {
      name : "Opera",
      value : "Opera",
      version : "Opera"
    }, {
      name : "RIM BlackBerry",
      value : "CLDC",
      version : "CLDC"
    }, {
      name : "Mozilla",
      value : "Mozilla",
      version : "Mozilla"
    }],
    /**
     * @return {?}
     */
    init : function() {
      var udataCur = this.header.join(" ");
      var os = this.matchItem(udataCur, this.dataos);
      var browser = this.matchItem(udataCur, this.databrowser);
      return{
        os : os,
        browser : browser
      };
    },
    /**
     * @param {string} value
     * @param {Array} data
     * @return {?}
     */
    matchItem : function(value, data) {
      var i;
      var re;
      var pattern;
      var isFunction;
      var message;
      /** @type {string} */
      var errorName = "unknown";
      /** @type {string} */
      var CORDOVA_JS_BUILD_LABEL = "0";
      /** @type {number} */
      i = 0;
      for (;i < data.length;i += 1) {
        if (re = new RegExp(data[i].value, "i"), isFunction = re.test(value)) {
          errorName = data[i].name;
          /** @type {RegExp} */
          pattern = new RegExp(data[i].version + "[- /:;]([\\d._]+)", "i");
          message = value.match(pattern);
          if (message) {
            if (message[1]) {
              message = message[1];
            }
            CORDOVA_JS_BUILD_LABEL = message.split(/[._]+/).join(".");
          }
          break;
        }
      }
      return{
        name : errorName,
        version : CORDOVA_JS_BUILD_LABEL
      };
    }
  };
  return methods.init();
}), angular.module("service.cimaOauth", []).service("cimaOauthService", ["$window", "$q", function(global, $q) {
  /**
   * @return {?}
   */
  function maybeBackup() {
    return origin.search(/(speedteststage|speedtestdev)/) !== -1 ? "https://login-qa4.rmr.net/" : "https://login.rmr.net/";
  }
  /**
   * @return {?}
   */
  function extend() {
    return origin.search(/(local|speedtestdev)/) !== -1 ? "speed-test-2.0-dev" : origin.search(/(speedtestbeta|speedteststage)/) !== -1 ? "speed-test-2.0-stage" : "speed-test-2.0";
  }
  /**
   * @return {undefined}
   */
  function enumerate() {
    global.JSO.store.saveState(origin, params);
  }
  /**
   * @return {undefined}
   */
  function fnReadCookie() {
    if (document.cookie.indexOf("isAuth=1") < 0) {
      eraseCookie("isAuth", "1", 7);
    }
  }
  /**
   * @return {?}
   */
  function url() {
    return document.location.hostname.split(".").slice(-2).join(".");
  }
  /**
   * @param {string} isAuth
   * @param {string} name
   * @param {number} opt_attributes
   * @return {undefined}
   */
  function eraseCookie(isAuth, name, opt_attributes) {
    /** @type {string} */
    var spaces = "";
    if (opt_attributes) {
      /** @type {Date} */
      var date = new Date;
      date.setTime(date.getTime() + 24 * opt_attributes * 60 * 60 * 1E3);
      /** @type {string} */
      spaces = "; expires=" + date.toGMTString();
    }
    /** @type {string} */
    document.cookie = isAuth + "=" + name + spaces + ";domain=" + url() + ";path=/";
  }
  /**
   * @param {string} label
   * @param {string} dataType
   * @return {undefined}
   */
  function run(label, dataType) {
    /** @type {string} */
    document.cookie = label + "=" + dataType + "; expires=Thu, 01 Jan 1970 00:00:01 GMT;domain=" + url() + ";path=/";
  }
  var origin = global.location.origin;
  if (!origin) {
    /** @type {string} */
    origin = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ":" + window.location.port : "");
  }
  var oldconfig = extend();
  /** @type {string} */
  var r20 = maybeBackup() + "oauth/authorize";
  /** @type {string} */
  var restoreScript = maybeBackup() + "oauth/sp-logout";
  var params = {
    response_type : "token",
    client_id : oldconfig,
    redirect_uri : origin + "/oauth/callback",
    state : origin,
    providerID : oldconfig,
    authorization : r20 + "?prompt=none"
  };
  this.jso = new global.JSO(params);
  enumerate();
  /**
   * @return {?}
   */
  this.getToken = function() {
    return this.jso.checkToken();
  };
  /**
   * @return {?}
   */
  this.checkAuthStatus = function() {
    var invokeDfd = $q.defer();
    return this.getToken() ? (invokeDfd.resolve(), invokeDfd.promise) : (this.jso.getToken(function() {
      this.successfulLogin();
      invokeDfd.resolve();
    }.bind(this)), invokeDfd.promise);
  };
  /**
   * @param {?} err
   * @return {undefined}
   */
  this.handleOauthCallbackRedirect = function(err) {
    if (this.jso.URLcontainsToken(err)) {
      this.jso.callback(err, this.successfulLogin.bind(this), oldconfig);
    }
  };
  /**
   * @return {undefined}
   */
  this.logout = function() {
    this.jso.wipeTokens();
    run("isAuth", "1");
  };
  /**
   * @return {undefined}
   */
  this.successfulLogin = function() {
    fnReadCookie();
  };
  /**
   * @return {?}
   */
  this.getLoginUrl = function() {
    return global.JSO.utils.encodeURL(r20, params);
  };
  /**
   * @return {?}
   */
  this.getLogoutUrl = function() {
    return global.JSO.utils.encodeURL(restoreScript, params);
  };
}]), angular.module("service.joustSplunk", []).service("joustSplunkService", ["$location", "$http", function(serverObj, config) {
  /** @type {string} */
  var dot = "";
  /**
   * @param {string} data
   * @param {string} url
   * @param {string} name
   * @param {string} error
   * @return {undefined}
   */
  this.logError = function(data, url, name, error) {
    /** @type {string} */
    var formData = "speedtestbeta.xfinity.com" === serverObj.host() ? "https://joust.xfinity.com/api/" : "https://joust.staging.xfinity.com/api/";
    /** @type {string} */
    var key = "speedtestbeta.xfinity.com" === serverObj.host() ? "rmrspeedtest" : "dev.rmrspeedtest";
    var headers = {
      headers : {
        "Content-Type" : "application/json;charset=UTF-8"
      }
    };
    /** @type {string} */
    data.appname = key;
    /** @type {string} */
    data.error = error;
    /** @type {number} */
    data._time = Date.now();
    /** @type {string} */
    data.method = name;
    /** @type {string} */
    data.uri = url;
    /** @type {string} */
    dot = data;
    /** @type {string} */
    var obj = JSON.stringify(data);
    if ("localhost" !== serverObj.host()) {
      config.post(formData, obj, headers).then(function(dataAndEvents) {
      })["catch"](function(dataAndEvents) {
      });
    }
  };
}]), angular.module("service.latency", []).service("latencyService", ["$window", function(dataAndEvents) {
  this.LatencyTest = dataAndEvents.LatencyTest;
  this.TestSeries = dataAndEvents.TestSeries;
}]), angular.module("service.polaris", []).service("polarisService", ["$http", function($http) {
  /**
   * @return {?}
   */
  this.getHeader = function() {
    return $http.get("/api/polaris/header", {
      cache : true
    })["catch"](function(test) {
      console.log("Error fetching polaris header xml: %s %s", test.status, test.data);
    });
  };
  /**
   * @return {?}
   */
  this.getFooter = function() {
    return $http.get("/api/polaris/footer", {
      cache : true
    })["catch"](function(test) {
      console.log("Error fetching polaris footer xml: %s %s", test.status, test.data);
    });
  };
}]), angular.module("service.postData", []).service("postDataService", ["$http", function($http) {
  /**
   * @param {string} task
   * @return {?}
   */
  this.calculator = function(task) {
    /** @type {string} */
    var appFrontendUrl = "/api/calculators";
    return $http({
      method : "POST",
      url : appFrontendUrl,
      data : task
    });
  };
}]), angular.module("service.speedtestResults", []).service("speedtestResultsService", ["$http", function(headers) {
  /**
   * @param {string} name
   * @param {string} value
   * @return {?}
   */
  this.getResults = function(name, value) {
    return headers.get(name + "/api/results/" + value);
  };
}]), angular.module("service.selectServer", []).service("selectServerService", ["$http", function($) {
  /**
   * @param {string} dataAndEvents
   * @param {number} hold
   * @return {?}
   */
  this.getServerLocation = function(dataAndEvents, hold) {
    /** @type {string} */
    var cacheKey = "/api/locations?hostname=" + dataAndEvents;
    return $.get(cacheKey, {
      timeout : hold
    });
  };
  /**
   * @param {string} itemId
   * @return {?}
   */
  this.getNearestServer = function(itemId) {
    /** @type {string} */
    var cacheKey = "/api/testservers?location=" + itemId;
    return $.get(cacheKey);
  };
}]), angular.module("service.testPlan", []).service("testPlanService", ["$http", "$q", "joustSplunkService", function(collection, $q, self) {
  /**
   * @param {number} hold
   * @return {?}
   */
  this.getTestPlan = function(hold) {
    var deferred = $q.defer();
    /** @type {string} */
    var next = "/api/testplans";
    var timeout = collection.get(next);
    return timeout.then(function(e) {
      var that = {};
      /** @type {*} */
      var options = "string" == typeof e.data ? JSON.parse(e.data) : e.data;
      that.osType = options.osType;
      that.osPlatform = options.osPlatform;
      that.osHostName = options.osHostName;
      that.appVersion = options.appVersion;
      that.webSocketUrlIPv4 = options.webSocketUrlIPv4;
      that.webSocketPort = options.webSocketPort;
      that.clientIPv4Address = options.clientIPAddress;
      that.lowLatencyThreshold = options.lowLatencyTheshold;
      that.baseUrlIPv4 = options.baseUrlIPv4;
      that.port = options.port;
      that.clientLocation = options.clientLocation;
      that.maxDownloadSize = options.maxDownloadSize;
      if (options.hasIPv6) {
        that.baseUrlIPv6 = options.baseUrlIPv6;
        that.webSocketUrlIPv6 = options.webSocketUrlIPv6;
        collection.get("http://" + that.baseUrlIPv6 + "/api/testplans", {
          timeout : hold
        }).then(function(event) {
          /** @type {boolean} */
          that.supportsIPv6 = true;
          that.clientIPv6Address = "string" == typeof event.data ? JSON.parse(event.data).clientIPAddress : event.data.clientIPAddress;
          deferred.resolve(that);
        })["catch"](function(err) {
          var e = {
            "client-ip" : that.clientIPv6Address
          };
          self.logError(e, "testPlanService", "getTestPlanIPv6", JSON.stringify(err));
          deferred.resolve(that);
        });
      } else {
        deferred.resolve(that);
      }
    })["catch"](function(err) {
      var e = {
        "client-ip" : "callFailedIPv4"
      };
      self.logError(e, "testPlanService", "getTestPlanIPv4", JSON.stringify(err));
      deferred.reject(err);
    }), deferred.promise;
  };
}]), angular.module("service.userInfo", []).service("userInfoService", ["$http", "$q", function($http, $q) {
  /**
   * @param {string} res
   * @return {?}
   */
  this.getUserInfo = function(res) {
    if (this.userInfo) {
      return $q.when(this.userInfo);
    }
    /** @type {string} */
    var auth = "Bearer " + res;
    return $http.get("https://login-st.rmr.net/oauth/userinfo", {
      cache : true,
      headers : {
        Authorization : auth,
        Accept : "application/json",
        "Content-Type" : "text/plain"
      }
    }).then(this.parseAndSetUserInfo.bind(this))["catch"](this.logError.bind(this));
  };
  /**
   * @param {MessageEvent} td
   * @return {?}
   */
  this.parseAndSetUserInfo = function(td) {
    return td.data ? (this.userInfo = {
      givenName : td.data.given_name || "Guest",
      userName : td.data.preferred_username ? td.data.preferred_username.split("@")[0] : "Guest",
      customerGuid : td.data.sub
    }, this.userInfo) : this.logError({
      status : 200,
      data : "Empty Response"
    });
  };
  /**
   * @param {?} event
   * @return {?}
   */
  this.logError = function(event) {
    return console.log("Error fetching user account info: %s %s", event.status, event.data), this.userInfo = {
      givenName : "Guest",
      userName : "Guest"
    }, this.userInfo;
  };
}]), angular.module("service.utils.clipboard", []).service("clipboardService", function() {
  /**
   * @param {?} container
   * @return {undefined}
   */
  this.copy = function(container) {
    /** @type {(Element|null)} */
    var target = document.querySelector(container);
    if (target && target.select) {
      target.focus();
      target.setSelectionRange(0, target.value.length);
      try {
        if (document.execCommand("copy")) {
          target.blur();
        } else {
          if (window.clipboardData) {
            window.clipboardData.setData("text/plain", target.value);
          }
        }
      } catch (c) {
        console.log("please press Ctrl/Cmd+C to copy");
      }
    } else {
      console.log("does not work");
    }
  };
}), angular.module("service.websockets", []).service("websocketsService", ["$q", "$interval", function($q, cb) {
  /**
   * @param {?} uri
   * @param {?} maxRange
   * @return {?}
   */
  this.RunLatencyTest = function(uri, maxRange) {
    var ws;
    var defer = $q.defer();
    /** @type {Array} */
    var out = [];
    /** @type {boolean} */
    var h = false;
    try {
      /** @type {WebSocket} */
      ws = new WebSocket(uri);
    } catch (i) {
      return defer.reject({
        msg : "No WebSocket Connection."
      }), defer.promise;
    }
    return ws.onopen = function() {
      /**
       * @return {undefined}
       */
      function init() {
        /** @type {boolean} */
        h = true;
        var results = {
          data : Date.now().toString(),
          flag : "latency"
        };
        ws.send(JSON.stringify(results), {
          mask : true
        });
        /**
         * @param {MessageEvent} event
         * @return {undefined}
         */
        ws.onmessage = function(event) {
          /** @type {number} */
          var copies = Date.now() - parseInt(event.data);
          out.push(copies);
          /** @type {boolean} */
          h = false;
        };
      }
      /** @type {number} */
      var i = 1;
      var scrollIntervalId = cb(function() {
        if (i <= maxRange) {
          if (!h) {
            init();
            i++;
          }
        } else {
          clearInterval(scrollIntervalId);
          defer.resolve(out);
          ws.close();
        }
      }, 100);
    }, ws.onerror = function(url) {
      defer.reject({
        msg : "No WebSocket Connection." + url
      });
    }, defer.promise;
  };
}]), angular.module("rmrSpeedTestApp").run(["$templateCache", function($templateCache) {
  $templateCache.put("components/polaris-footer/polaris-footer.html", "<div class=polaris-footer-fallback></div>");
  $templateCache.put("components/polaris-header/polaris-header.html", "<div class=polaris-header-fallback><div class=polaris-header-fallback--right ng-if=polarisFailure><div id=polaris-header-fallback--auth ng-hide=!authenticated><div id=polaris-header-name>Hi, {{greetingName}}</div><a rel=polaris-fallback-signout href=/logout>Sign Out</a></div><a rel=polaris-fallback-signin class=auth href=/signin ng-hide=authenticated>Sign In</a></div></div>");
  $templateCache.put("components/speedtest-help/speedtest-help.html", "<section class=speedtest-help><h1 class=content-header>Need help?</h1><div class=content-wrap><p>Check out our FAQs for more information.</p><p><a href=http://www.xfinity.com/resources/internet-speed.html class=speedtest-button>Learn more</a></p></div></section>");
  $templateCache.put("components/speedtest-message/speedtest-message.html", '<section class=speedtest-message><h1 class="content-header header1">{{heading}}</h1><div class=content-wrap ng-transclude></div></section>');
  $templateCache.put("components/speedtest-modal-advanced-results/speedtest-modal-advanced-results.html", '<speedtest-modal show=show title="Advanced results"><div class="tag tag--dark mb3 heading4">IPv4</div><div class=mb4><value-box prefix=prefix label=Ping unit=ms value="{{finalResultsIpv4.latency || \'--\'}}"></value-box><value-box prefix=prefix label=Download unit=Mbps value="{{finalResultsIpv4.download || \'--\'}}" text="Peak: {{finalResultsIpv4.downloadPeak || \'--\'}}"></value-box><value-box prefix=prefix label=Upload unit=Mbps value="{{finalResultsIpv4.upload || \'--\'}}" text="Peak: {{finalResultsIpv4.uploadPeak || \'--\'}}"></value-box></div><div class="tag tag--dark mb3 heading4" ng-if=finalResultsIpv6.latency>IPv6</div><div class=mb4 ng-if=finalResultsIpv6.latency><value-box prefix=prefix value="{{finalResultsIpv6.latency || \'--\'}}"></value-box><value-box prefix=prefix value="{{finalResultsIpv6.download || \'--\'}}" text="Peak: {{finalResultsIpv6.downloadPeak || \'--\'}}"></value-box><value-box prefix=prefix value="{{finalResultsIpv6.upload || \'--\'}}" text="Peak: {{finalResultsIpv6.uploadPeak || \'--\'}}"></value-box></div><div class=body3>App version: <span class=app-version>{{finalResultsIpv4.appVersion}}</span></div><div ng-if=finalResultsIpv4.location class=body3>Server location: <span class=location>{{finalResultsIpv4.location}}, {{finalResultsIpv4.sitename}}</span></div><div class=body3>Server IP: <span class=server-ipaddress>{{finalResultsIpv4.serverIpAddress}}</span></div><div class=body3>Client IP: <span class=client-ipaddress>{{finalResultsIpv6.clientIp || finalResultsIpv4.clientIp}}</span></div></speedtest-modal>');
  $templateCache.put("components/speedtest-modal-timeout/speedtest-modal-timeout.html", '<speedtest-modal show=data.show title="No connection found" type=error><p class=speedtest-modal-dialog-body-text>We\u2019re sorry, an error has occurred. Please check your Internet connection and try again.</p><button class=speedtest-button ng-click=restart()>Try again</button></speedtest-modal>');
  $templateCache.put("components/speedtest-modal/speedtest-modal.html", '<div class=speedtest-modal ng-show=show><div class=speedtest-modal-overlay ng-click=hideModal()></div><div class=speedtest-modal-dialog><button class=speedtest-modal-dialog-close-icon ng-click=hideModal()><svg xmlns=http://www.w3.org/2000/svg x=0px y=0px wdth=21px height=20px viewbox="0 0 14 14" enable-background="new 0 0 14 14"><path d="M7.256 7.125l5.997 5.997-.63.63L6.627 7.76.634 13.752l-.63-.63L6 7.124.003 1.128l.63-.63L6.63 6.49 12.622.498l.63.63-5.996 5.998z" fill=#B1B9BF fill-rule="evenodd"></svg></button><div class=speedtest-modal-dialog-header><div class="speedtest-modal-dialog-icon speedtest-icon--{{type}} speedtest-icon mb2" ng-if=type></div><h2 class="speedtest-modal__title display3">{{title}}</h2><hr class=speedtest-modal-dialog-hr></div><div class="speedtest-modal-dialog-body body1" ng-transclude></div></div></div>');
  $templateCache.put("components/speedtest-progress/speedtest.progress.html", '<svg class="progress-bar {{currentTest}}" viewbox="256 348 318 272" version=1.1 xmlns=http://www.w3.org/2000/svg xmlns:xlink=http://www.w3.org/1999/xlink><defs><lineargradient x1=10.9769752% y1=-6.5999349% x2=87.0361328% y2=100% id=progress-path-gradient><stop stop-color=#4FB3E1 offset=0%></stop><stop stop-color=#50AFDF offset=0%></stop><stop stop-color=#51AADE offset=8.13435187%></stop><stop stop-color=#5C72CB offset=100%></stop></lineargradient></defs><path d="M303.981531,618 C242.672823,556.691293 242.672823,457.290238 303.981531,395.981531 C365.290238,334.672823 464.691293,334.672823 526,395.981531 C587.308707,457.290238 587.308707,556.691293 526,618" stroke=#DDE2E6 stroke-width=4 stroke-linecap=round fill=none></path><path d="M303.981531,618 C242.672823,556.691293 242.672823,457.290238 303.981531,395.981531 C365.290238,334.672823 464.691293,334.672823 526,395.981531 C587.308707,457.290238 587.308707,556.691293 526,618" id=progress-path stroke=url(#progress-path-gradient) stroke-width=4 stroke-linecap=round fill=none stroke-dashoffset={{dashoffset}} stroke-dasharray={{dasharray}}></path></svg><ng-transclude class="speedtest-overlay flex"></ng-transclude>');
  $templateCache.put("components/speedtest-results/speedtest-results.html", '<div class="speedtest-results results-bottom-padding"><div class="tag tag--dark mb ipv4-header">IPv4</div><div class="mb4 ipv4-results"><value-box prefix=ipv4-latency label=Ping unit=ms value={{speedTestResults.IPv4.latency}}></value-box><value-box prefix=ipv4-download label=Download unit=Mbps value={{speedTestResults.IPv4.download}} text="Peak: {{speedTestResults.IPv4.downloadPeak || \'--\'}}"></value-box><value-box prefix=ipv4-upload label=Upload unit=Mbps value={{speedTestResults.IPv4.upload}} text="Peak: {{speedTestResults.IPv4.uploadPeak || \'--\'}}"></value-box></div><div class="tag tag--dark mb3 ipv6-header" ng-if=speedTestResults.IPv6>IPv6</div><div ng-if=speedTestResults.IPv6 class=ipv4-results><value-box prefix=ipv6-latency value={{speedTestResults.IPv6.latency}}></value-box><value-box prefix=ipv6-download value={{speedTestResults.IPv6.download}} text="Peak: {{speedTestResults.IPv6.downloadPeak || \'--\'}}"></value-box><value-box prefix=ipv6-upload value={{speedTestResults.IPv6.upload}} text="Peak: {{speedTestResults.IPv6.uploadPeak || \'--\'}}"></value-box></div></div>');
  $templateCache.put("components/speedtest-share/speedtest.share.html", '<speedtest-modal show=show title="Share your results" reset=resetModal><p class=speedtest-modal-dialog-body-text>Copy the link below to share your XFINITY Speed Test results.</p><input class="speedtest-modal-dialog-body-text speedtest-share-result body1" ng-model="url"> <button class="speedtest-button action-copy-share-results-link" ng-class="{\'speedtest-button--disabled\': buttonDisabled}" ng-click="copyToClipboard(\'.speedtest-share-result\')" ng-if=isCopySupported()>{{buttonText}}</button></speedtest-modal>');
  $templateCache.put("components/speedtest-status/speedtest-status.html", '<div class="col col1of3 speedtest-status"><div class="status pie"><div class="slice slice-right"></div><div class="slice slice-left"></div><div class=percent><p class=speedtest-status-text>{{currentTest}}</p></div></div></div>');
  $templateCache.put("components/speedtest-test-animation/speedtest-test-animation.html", "<div class=\"speedtest-animation {{type}}\"><div class=\"speedtest-icon speedtest-icon--person speedtest-animation__node\"></div><div class=speedtest-animation__connection><div class=speedtest-animation__signal ng-style=\"{'-ms-transform':'translate(' + progressPosition() + ')',\n                '-webkit-transform':'translate(' + progressPosition() + ')',\n                'transform':'translate(' + progressPosition() + ')',\n                'transition':'transform ' + transitionDuration + ' linear'}\"></div></div><div class=\"speedtest-icon speedtest-icon--server speedtest-animation__node\"></div></div>");
  $templateCache.put("components/speedtest-tooltip/speedtest.tooltip.html", '<a ng-if=show class=tool-tip ng-click=handleClick() href><svg class=tooltip-icon width=18 height=18 viewbox="790 452 18 18" xmlns=http://www.w3.org/2000/svg><g fill=none fill-rule=evenodd><path d="M799 453c4.417 0 8 3.583 8 8s-3.583 8-8 8-8-3.583-8-8 3.583-8 8-8z" stroke=#2B9CD8 stroke-linecap=round stroke-linejoin=round></path><path d="M799.99 463.017c-.06 0-.204.112-.43.34-.223.227-.42.34-.588.34-.024 0-.045-.005-.07-.015-.024-.014-.034-.04-.034-.084 0-.035.017-.1.05-.195.026-.09.075-.215.145-.365l.87-1.8c.076-.134.124-.26.15-.38.02-.122.034-.237.034-.345 0-.26-.087-.48-.258-.664-.175-.186-.432-.276-.773-.276-.216 0-.46.07-.737.216-.282.142-.52.3-.717.462-.223.18-.403.35-.55.51-.146.165-.215.283-.215.356 0 .02.028.07.09.15.06.08.122.118.185.118.052 0 .222-.143.507-.428.286-.28.477-.424.578-.424.038 0 .07.014.09.038.025.025.035.066.035.122 0 .06-.014.125-.038.202-.028.076-.073.174-.132.295l-1.09 2.13c-.044.108-.086.212-.124.317-.04.104-.057.22-.057.34 0 .195.073.37.216.522.145.156.378.232.698.232.466 0 .984-.212 1.562-.636.574-.425.862-.696.862-.81 0-.03-.03-.08-.09-.157-.063-.074-.118-.113-.17-.113M800.83 457.024c-.202-.194-.45-.292-.738-.292-.282 0-.525.098-.73.292-.21.195-.31.425-.31.693 0 .264.1.497.31.688.205.195.448.292.73.292.29 0 .536-.097.737-.292.2-.19.302-.424.302-.688 0-.268-.1-.498-.303-.693" fill=#2EA0DD></path></g></svg></a>');
  $templateCache.put("components/value-box/value-box.html", '<div class="value-box {{prefix}}-value-box"><div class="value-box__label {{prefix}}-label body3" ng-if=label>{{label}} (<span class="value-box__unit {{prefix}}-unit">{{unit}}</span>)<speedtest-tooltip callback=tooltip></speedtest-tooltip></div><div class="value-box__value {{prefix}}-box-value display2 mb1">{{value}}</div><div class="value-box__text {{prefix}}-text heading4" ng-if=text>{{text}}</div></div>');
  $templateCache.put("pages/errors/speedtest-error.html", '<div class="speedtest-container speedtest-error"><div class=welcome-wrapper style="padding: 20px 0 100%"><h1>Not found <span>:(</span></h1><p>Sorry, but the id you were trying to view does not exist.</p></div></div>');
  $templateCache.put("pages/help/help.html", "<div>show the help page now</div>");
  $templateCache.put("pages/results/speedtest-results.html", "<section class=welcome-header><h1 class=header1>Your XFINITY Speed Test results</h1></section><div class=\"welcome-wrapper speedtest-resultbox\"><h2>Here's how fast you're going</h2><speedtest-results ng-if=resultsVisible></speedtest-results><div class=client-location><div ng-if=serverLocation class=location-content>Server location: <span class=location>{{serverLocation}}, {{serverSitename}}</span></div><div ng-if=clientIpAddress class=serveripaddress-content>Server IP: <span class=server-ipAddress>{{serverIpAddress}}</span></div><div ng-if=clientIpAddress class=clientipaddress-content>Client IP: <span class=client-ipAddress>{{clientIpAddress}}</span></div></div></div><speedtest-help></speedtest-help><polaris-footer></polaris-footer>");
  $templateCache.put("pages/speedtest/speedtest.html", '<polaris-header></polaris-header><section class=welcome-header><h1 class=heading1>Welcome to the XFINITY Speed Test</h1><div class=tag><i class=beta-icon></i></div><p class=body1>For best results, please limit the amount of applications and devices using your Internet connection.</p></section><div class=welcome-wrapper><div class="speedtest-dashboard general-inner"><div class=progress-bar-wrapper><speedtest-progress current-value={{currentValue}} current-test={{currentTest}}><div class=ma-auto><div ng-show=currentResult class="speedtest-current-result-text display1 qa-message-currentResult">{{currentResult}}</div><div ng-show=dialMessage class="mb3 heading4 dial-message qa-dial-message" ng-class="{\'dial-message-error\': abort}">{{dialMessage}}</div><div class=test-actions><button ng-click=hideWelcome() ng-show=startTestVisible class="speedtest-button action-start-test">Start test</button> <button ng-click=restartTest() ng-show=restartTestVisible class="speedtest-button action-retest">Test again</button> <button ng-click=showWelcome() ng-show=backToWelcomeVisible class="speedtest-button action-back-to-welcome">Back to Welcome Page</button></div></div></speedtest-progress><speedtest-test-animation type={{currentTest}} progress={{testProgress}} transition-duration="{{testDuration || \'250ms\'}}"></speedtest-test-animation></div><div class=speedtest-results-wrapper><h2 class=heading1 ng-show=resultsButtonsVisible>Here\'s how fast you\'re going</h2><div class=speedtest-results-ipv4-wrapper><value-box prefix=ping label={{pingLabel}} unit={{pingUnit}} value="{{finalValuesIPv4.latency || \'--\'}}" tooltip=tooltipPing></value-box><value-box prefix=download label={{downloadLabel}} unit={{downloadUnit}} value="{{finalValuesIPv4.download || \'--\'}}" tooltip=tooltipDownload></value-box><value-box prefix=upload label={{uploadLabel}} unit={{uploadUnit}} value="{{finalValuesIPv4.upload || \'--\'}}" tooltip=tooltipUpload></value-box></div><div class=speedtest-results-buttons-wrapper ng-show=resultsButtonsVisible><button class="speedtest-button action-share-result" ng-click=shareResult()>Share results</button> <button class="speedtest-button no-border action-view-advanced-results" ng-click="advancedVisible = true">View advanced results</button></div></div></div></div><speedtest-modal-advanced-results show=advancedVisible final-results-ipv4=finalResultsIPv4 final-results-ipv6=finalResultsIPv6></speedtest-modal-advanced-results><speedtest-message ng-if=feedbackVisible heading="Help us improve this feature"><p class=body1>We\'d love to hear from you!</p><p class=message-link-wrapper><a target=_blank href="http://speedtest2beta.wufoo.com/forms/z1cd3r6b18pati9/" class=speedtest-button>Provide feedback</a></p></speedtest-message><speedtest-message ng-if=helpVisible heading="Need help?"><p class=message-link-wrapper>Check out our FAQs for more information.</p><p><a target=_blank href=http://www.xfinity.com/resources/internet-speed.html class=speedtest-button>Learn more</a></p></speedtest-message><speedtest-modal-timeout data=timeoutModalData restart=restartTest cancel=showWelcome></speedtest-modal-timeout><speedtest-share show=showShareResultsModal url=resultId browser-name=browserName></speedtest-share><speedtest-modal show=pingVisible title="Ping (ms)" type=ping>Ping is the reaction time of your connection, or how fast you get a response after you\'ve sent out a request. A fast ping means your connection is more responsive, especially for applications where timing is everything, like multi-player video games.</speedtest-modal><speedtest-modal show=downloadVisible title="Download speeds (Mbps)" type=download>Download speeds measure how quickly you can pull data from the server to your home. Most connections are designed to download much faster than they upload. The majority of online activity--such as loading website or streaming videos--consists of downloads.</speedtest-modal><speedtest-modal show=uploadVisible title="Upload speeds (Mbps)" type=upload>Upload speeds measure data that\'s transferred from your computer or mobile device to the Internet. Most connections are designed to download much faster than they upload.</speedtest-modal><polaris-footer></polaris-footer>');
}]);
