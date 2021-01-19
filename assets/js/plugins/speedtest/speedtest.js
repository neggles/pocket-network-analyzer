'use strict';
window['onload'] = function() {
    OpenSpeedTest.Start()
};
(function(_0xf33fx1) {
    function newElement(element) {
        if (!(this instanceof newElement)) {
            return new newElement(element)
        };
        this['el'] = document['getElementById'](element);
    }
    var _0xf33fx4 = function(element) {
        element && 'function' === typeof element && element()
    };
    newElement['prototype']['fade'] = function(element, _0xf33fx5, _0xf33fx6) {
        var _0xf33fx7 = 'in' === element,
            newElement = _0xf33fx7 ? 0 : 1,
            _0xf33fx8 = 14 / _0xf33fx5,
            _0xf33fx9 = this;
        _0xf33fx7 && (_0xf33fx9['el']['style']['display'] = 'block', _0xf33fx9['el']['style']['opacity'] = newElement);
        var _0xf33fxa = window['setInterval'](function() {
            newElement = _0xf33fx7 ? newElement + _0xf33fx8 : newElement - _0xf33fx8;
            _0xf33fx9['el']['style']['opacity'] = newElement;
            0 >= newElement && (_0xf33fx9['el']['style']['display'] = 'none');
            (0 >= newElement || 1 <= newElement) && window['clearInterval'](_0xf33fxa, _0xf33fx4(_0xf33fx6));
        }, 14);
    };
    var _0xf33fx9 = function() {
        this['app_UI'] = newElement('speedometer');
        this['app_UI_button'] = newElement('start_button_wrapper');
        this['app_loading'] = newElement('loading_app');
        this['speed_current'] = newElement('digital_speed_current');
        this['caption_current'] = newElement('speed_caption_current');
        this['speed_down'] = newElement('digital_speed_down');
        this['speed_caption_down'] = newElement('speed_caption_down');
        this['speed_caption_up'] = newElement('speed_caption_up');
        this['speed_up'] = newElement('digital_speed_up');
        this['right_side_link'] = newElement('right_side_ink');
        this['left_side_link'] = newElement('left_side_ink');
        this['chart_container'] = newElement('chart_container');
        this['progress_bar'] = newElement('progress_bar');
        this['progress_bar_indicator'] = newElement('progress_bar_indicator');
        this['direction_sign'] = newElement('direction_sign');
        this['direction_sign_img'] = newElement('direction_sign_img');
        this['start_button_wrapper'] = newElement('start_button_wrapper');
        this['start_button'] = newElement('starttest');
        this['share_button'] = newElement('sharethis');
        this['ip_container'] = newElement('ip_container');
        this['ping_results'] = newElement('ping_result');
        this['progress_text'] = newElement('progress_text');
        this['ink_segments'] = newElement('ink_segments');
        this['center_ink'] = newElement('one_ink_visible');
        this['scale'] = [{
            degree: 0,
            value: 0
        }, {
            degree: 75,
            value: 0.5
        }, {
            degree: 115,
            value: 3
        }, {
            degree: 175,
            value: 10
        }, {
            degree: 220,
            value: 100
        }, {
            degree: 280,
            value: 1024
        }];
    };
    _0xf33fx9['prototype']['StartTest'] = function() {
        this['start_button_wrapper']['fade']('out', 200);
        this['ip_container']['fade']('out', 200);
        this['speed_down']['fade']('out', 200);
        this['speed_up']['fade']('out', 200);
        this['right_side_link']['fade']('out', 200);
        this['left_side_link']['fade']('out', 200);
        this['speed_caption_down']['fade']('out', 200);
        this['speed_caption_up']['fade']('out', 200);
    };
    _0xf33fx9['prototype']['preparePing'] = function() {
        var element = this;
        element['progress_text']['el']['innerHTML'] = 'Testing Ping...';
        setTimeout(function() {
            element['progress_bar']['fade']('in', 500)
        }, 500);
    };
    _0xf33fx9['prototype']['DisplayPing'] = function(element) {
        this['progress_text']['el']['innerHTML'] = ' Testing download speed...';
        this['ping_results']['el']['innerHTML'] = ' Ping : ' + element + ' ms';
    };
    _0xf33fx9['prototype']['prepareDown'] = function(element) {
        this['caption_current']['el']['innerHTML'] = 'Mbps download';
        this['direction_sign_img']['el']['style']['top'] = '-43px';
        this['direction_sign']['fade']('in', 500);
        this['caption_current']['fade']('in', 500);
        var _0xf33fx5 = this;
        setTimeout(function() {
            _0xf33fx5['speed_current']['fade']('in', 400)
        }, 500);
    };
    _0xf33fx9['prototype']['LiveSpeedMainDisplay'] = function(element) {
        this['speed_current']['el']['innerHTML'] = element
    };
    _0xf33fx9['prototype']['DisplayFinalDown'] = function(element) {
        this['progress_text']['el']['innerHTML'] = '';
        this['progress_text']['el']['innerHTML'] = ' Initializing for upload test...';
        this['left_side_link']['fade']('in', 700);
        var _0xf33fx5 = this;
        setTimeout(function() {
            _0xf33fx5['speed_down']['fade']('in', 500);
            _0xf33fx5['speed_caption_down']['fade']('in', 500);
            _0xf33fx5['speed_down']['el']['innerHTML'] = element;
            _0xf33fx5['speed_current']['fade']('out', 300);
            _0xf33fx5['direction_sign']['fade']('out', 300);
            _0xf33fx5['caption_current']['fade']('out', 300);
            _0xf33fx5['caption_current']['el']['innerHTML'] = '';
        }, 700);
    };
    _0xf33fx9['prototype']['prepareUp'] = function(element) {
        this['caption_current']['el']['innerHTML'] = 'Mbps upload';
        this['progress_text']['el']['innerHTML'] = '';
        this['progress_text']['el']['innerHTML'] = ' Testing upload speed...';
        this['direction_sign_img']['el']['style']['top'] = '0px';
        this['direction_sign']['fade']('in', 500);
        this['caption_current']['fade']('in', 500);
        var _0xf33fx5 = this;
        setTimeout(function() {
            _0xf33fx5['speed_current']['fade']('in', 500)
        }, 700);
    };
    _0xf33fx9['prototype']['DisplayFinalUp'] = function(element) {
        this['right_side_link']['fade']('in', 700);
        var _0xf33fx5 = this;
        setTimeout(function() {
            _0xf33fx5['speed_up']['fade']('in', 500);
            _0xf33fx5['speed_caption_up']['fade']('in', 500);
            _0xf33fx5['speed_up']['el']['innerHTML'] = element;
            _0xf33fx5['speed_current']['fade']('out', 300);
            _0xf33fx5['direction_sign']['fade']('out', 300);
            _0xf33fx5['caption_current']['fade']('out', 300);
            _0xf33fx5['progress_bar']['fade']('out', 500);
            setTimeout(function() {
                _0xf33fx5['start_button_wrapper']['fade']('in', 500)
            }, 700);
        }, 700);
    };
    _0xf33fx9['prototype']['MainSpeedProgress'] = function(element) {
        var _0xf33fx5 = this['getNonlinearDegree'](element);
        element = 75.8786 + _0xf33fx5;
        _0xf33fx5 = 257 * -Math['round'](_0xf33fx5 / 60 - 0.5) + 1;
        this['ink_segments']['el']['style']['left'] = _0xf33fx5 + 'px';
        var _0xf33fx5 = this['center_ink']['el'],
            _0xf33fx6;
        _0xf33fx5: {
            _0xf33fx6 = ['transform', 'WebkitTransform', 'msTransform', 'MozTransform', 'OTransform'];
            for (var _0xf33fx7; _0xf33fx7 = _0xf33fx6['shift']();) {
                if ('undefined' != typeof _0xf33fx5['style'][_0xf33fx7]) {
                    _0xf33fx6 = _0xf33fx7;
                    break _0xf33fx5;
                }
            };_0xf33fx6 = !1;
        }
        _0xf33fx6 && (_0xf33fx5['style'][_0xf33fx6] = 'rotate(' + element + 'deg)');
    };
    _0xf33fx9['prototype']['getNonlinearDegree'] = function(element) {
        var _0xf33fx5 = 0;
        if (0 == element || 0 >= element || isNaN(element)) {
            return 0
        };
        for (; _0xf33fx5 < this['scale']['length'];) {
            if (element > this['scale'][_0xf33fx5]['value']) {
                _0xf33fx5++
            } else {
                return this['scale'][_0xf33fx5 - 1]['degree'] + (element - this['scale'][_0xf33fx5 - 1]['value']) * (this['scale'][_0xf33fx5]['degree'] - this['scale'][_0xf33fx5 - 1]['degree']) / (this['scale'][_0xf33fx5]['value'] - this['scale'][_0xf33fx5 - 1]['value'])
            }
        };
        return this['scale'][this['scale']['length'] - 1]['degree'];
    };
    _0xf33fx9['prototype']['ProgressBar'] = function(element, _0xf33fx5) {
        var _0xf33fx6 = Date['now'](),
            _0xf33fx7 = _0xf33fx5 / 1E3,
            newElement = setInterval(function() {
                var _0xf33fx5 = (Date['now']() - _0xf33fx6) / 1E3,
                    _0xf33fx9 = 100 * _0xf33fx5 / _0xf33fx7;
                this['progress_bar_indicator']['style']['left'] = 1 == element ? 56 - _0xf33fx9 / 100 * -465 + 'px' : 521 - _0xf33fx9 / 100 * 465 + 'px';
                if (101 <= _0xf33fx9['toFixed'](0) || _0xf33fx5 >= _0xf33fx7) {
                    this['progress_bar_indicator']['style']['left'] = 1 == element ? '521px' : '56px', clearInterval(newElement)
                };
            }, 14)
    };
    var _0xf33fxb = function() {
        this['StarttiMer'] = Date['now']();
        this['nextTime'] = 0;
        this['AvgOverallSpeed'] = [];
        this['AvgFinalSpeed'] = [];
    };
    _0xf33fxb['prototype']['ModelReset'] = function() {
        this['StarttiMer'] = Date['now']();
        this['nextTime'] = 0;
        this['AvgOverallSpeed'] = [];
        this['AvgFinalSpeed'] = [];
    };
    _0xf33fxb['prototype']['convertSpeedToString'] = function(element) {
        return 0 > element || isNaN(element) || void(0) == element ? 0 : 1 <= element || 0 == element ? element['toFixed'](1) : element['toFixed'](2)
    };
    _0xf33fxb['prototype']['GetArrSum'] = function(element) {
        this['TempArr'] = element;
        return this['Sum'] = this['TempArr']['reduce'](function(_0xf33fx5, element) {
            if (isNaN(_0xf33fx5) || void(0) == _0xf33fx5 || null == _0xf33fx5 || 0 == _0xf33fx5 || 1 == _0xf33fx5 || '' == _0xf33fx5 || 0 > _0xf33fx5) {
                _0xf33fx5 = 0
            };
            if (isNaN(element) || void(0) == element || null == element || 0 == element || 1 == element || '' == element || 0 > element) {
                element = 0
            };
            return _0xf33fx5 + element;
        });
    };
    _0xf33fxb['prototype']['speedAvg'] = function(element, _0xf33fx5, _0xf33fx6, newElement) {
        this['avgSpeed'] = [];
        this['avgSpeed']['push'](element);
        this['EndtiMer'] = Date['now']() - this['StarttiMer'];
        this['AvgTimer'] = _0xf33fx5;
        this['LiveSpeedtoAvg'] = element;
        this['EndtiMer'] >= this['nextTime'] && (this['nextTime'] += this['AvgTimer'], this['OverallArrSum'] = this.GetArrSum(this['avgSpeed']), this['AvgOverallSpeed']['push'](this.OverallArrSum));
        this['EndtiMer'] >= newElement - _0xf33fx6 && (this['AvgFinalSpeed']['push'](this.OverallArrSum), this['OverallFinalSpeed'] = this.GetArrSum(this.AvgFinalSpeed) / this['AvgFinalSpeed']['length']);
        return this['OverallFinalSpeed'];
    };
    _0xf33fxb['prototype']['lastSpeddtoZero'] = function(element) {
        return 0 > element || isNaN(element) ? 0 : 0.88 * element
    };
    var _0xf33fxa = function() {
        this['DownloadFileName'] = 'downloading';
        this['UploadFileName'] = 'upload';
        this['JsonFileName'] = './self.json';
        this['uploadDuration'] = this['downtestDuration'] = 18E3;
        this['upRequests'] = this['downRequests'] = 6;
        this['speedCalcInterval'] = 60;
        this['FinalDisplayUpSpeed'] = this['FinalDisplaySpeed'] = 5E3;
        this['Model'] = new _0xf33fxb;
        this['View'] = new _0xf33fx9;
        this['Open'] = [];
        this['OverallTime'] = Date['now']();
        this['EventData'] = [];
        this['EventTotal'] = [];
        this['FinalData'] = [0];
        this['SpeedData'] = [0];
        this['LiveSpeed'] = 0;
        this['newTime'] = [];
        this['CurTime'] = [];
        this['SumData'] = [0];
        this['OneStop'] = [];
        this['Stopit'] = void(0);
        this['upData'] = this['downData'] = 0;
        this['xhrlogDown'] = [];
        this['xhrlogUp'] = [];
        this['xhrlogDowndata'] = [];
        this['xhrlogupData'] = [];
        this['xhrN'] = 0;
        this['Urls'] = [];
        this['PingResults'] = [];
        this['vldPingUrlurl'] = [];
        this['totalRequests'] = this['upRequests'];
        var element = '\x04\x01\x05\x02\x10\x05\x10\x01\x10\x04\x02\x10\x05\x02\x04\x01\x04\x01\x05\x02\x10\x05\x10\x01\x10\x04\x02\x10\x05',
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element,
            element = element + element;
        this['base_string_2MB'] = element + element;
        this['SendData'] = this['UploadData'] = this['base_string_2MB']['substring'](0, 2E6);
        this.GetUrls();
        this.Init();
        this['View']['app_UI']['fade']('in', 1500);
        this['View']['app_loading']['el']['style']['display'] = 'none';
        this['View']['app_UI_button']['fade']('in', 1500);
        this.ServerConnect(1);
    };
    _0xf33fxa['prototype']['Init'] = function() {
        var element = this;
        element['Topurl'] = document['referrer'];
        element['urlParts'] = element['Topurl']['replace']('http://', '')['replace']('https://', '')['split'](/[/?#]/);
        element['getdomain'] = element['urlParts'];
        element['domainx'] = element['getdomain'][0];
        console['log'](element['domainx']);
        this['View']['start_button']['el']['addEventListener']('click', function() {
            element.ServerConnect(2);
            this['xhrN']++;
            element['PingResults'] = [];
            element['vldPingUrlurl'] = [];
            element['besthosttogo'] = void(0);
            if (17 == location['hostname']['length']) {
                element['View'].StartTest();
                element['View']['preparePing']();
                for (var _0xf33fx5 = 0; _0xf33fx5 < element['Urls']['length']; _0xf33fx5++) {
                    element['testPing'](_0xf33fx5)
                };
            };
        });
        this['View']['share_button']['el']['addEventListener']('click', function() {
            location['href'] = 'http://openspeedtest.com/results/' + element['return_data']
        });
    };
    _0xf33fxa['prototype']['GetUrls'] = function(element) {
        function _0xf33fx5() {
            for (var _0xf33fx5 = 0; _0xf33fx5 < _0xf33fx6['Urls']['length']; _0xf33fx5++) {
                var element = _0xf33fx5;
                _0xf33fx6['url'] = [];
                _0xf33fx6['Open'][element] = new XMLHttpRequest;
                _0xf33fx6['url'][element] = 'http://' + _0xf33fx6['domainx'] + '/' + _0xf33fx6['DownloadFileName'] + '?n=' + Math['random']();
                _0xf33fx6['Open'][element]['open']('HEAD', _0xf33fx6['url'][element], !0);
                _0xf33fx6['Open'][element]['timeout'] = 2E3;
                _0xf33fx6['Open'][element]['send']();
            }
        }
        var _0xf33fx6 = this;
        this['Open'] = new XMLHttpRequest;
        this['Ipurl'] = _0xf33fx6['JsonFileName'];
        this['Open']['open']('GET', this.Ipurl, !0);
        this['Open']['responseType'] = 'text';
        this['Open']['send'](null);
        this['Open']['onreadystatechange'] = function() {
            4 === _0xf33fx6['Open']['readyState'] & 200 === _0xf33fx6['Open']['status'] && (('string' === typeof _0xf33fx6['Open']['response'] ? JSON['parse'](_0xf33fx6['Open']['response']) : _0xf33fx6['Open']['response'])['forEach'](function(_0xf33fx5) {
                _0xf33fx6['Urls']['push'](_0xf33fx5)
            }), _0xf33fx4(_0xf33fx5()))
        };
    };
    _0xf33fxa['prototype']['testPing'] = function(element) {
        function _0xf33fx5() {
            for (var _0xf33fx5 = [], element = 0; element < _0xf33fx6['PingResults']['length']; element++) {
                _0xf33fx5['push'](parseFloat(_0xf33fx6['PingResults'][element]))
            };
            _0xf33fx6['smallping'] = Math['min']['apply'](Math, _0xf33fx5);
            _0xf33fx5 = _0xf33fx5['indexOf'](_0xf33fx6['smallping']);
            _0xf33fx6['besthosttogo'] = _0xf33fx6['vldPingUrlurl'][_0xf33fx5];
            void(0) == _0xf33fx6['besthosttogo'] || 0 == _0xf33fx6['besthosttogo'] ? (_0xf33fx6['View']['speed_current']['el']['innerHTML'] = 'erroR : O1', alert('Error: 01 | Internet communication error [Please reload this page!]')) : (_0xf33fx6['View'].DisplayPing(_0xf33fx6['smallping']), _0xf33fx6.CtrlUI());
        }
        this['StartPingTime'] = [];
        this['FindHost'] = 0;
        this['PingArr'] = [];
        this['StartPingTime'][element] = Date['now']();
        var _0xf33fx6 = this;
        _0xf33fx6['url'] = [];
        _0xf33fx6['Open'][element] = new XMLHttpRequest;
        _0xf33fx6['url'][element] = 'http://' + _0xf33fx6['domainx'] + '/' + _0xf33fx6['DownloadFileName'] + '?n=' + Math['random']();
        _0xf33fx6['Open'][element]['open']('HEAD', _0xf33fx6['url'][element], !0);
        _0xf33fx6['Open'][element]['timeout'] = 5E3;
        _0xf33fx6['Open'][element]['ontimeout'] = function() {
            _0xf33fx6['FindHost']++;
            _0xf33fx6['FindHost'] == _0xf33fx6['Urls']['length'] && _0xf33fx5();
            0 == _0xf33fx6['vldPingUrlurl']['length'] && _0xf33fx6['FindHost'] == _0xf33fx6['Urls']['length'] && location['reload']();
        };
        _0xf33fx6['Open'][element]['send'](null);
        var newElement = this['StartPingTime'][element];
        _0xf33fx6['Open'][element]['onload'] = function() {
            _0xf33fx6['PingArr'][element] = (Date['now']() - newElement)['toFixed'](0);
            _0xf33fx6['vldPingUrlurl']['push'](_0xf33fx6['Urls'][element]['url']);
            _0xf33fx6['PingResults']['push'](_0xf33fx6['PingArr'][element]);
            _0xf33fx6['FindHost']++;
            _0xf33fx6['FindHost'] == _0xf33fx6['Urls']['length'] && _0xf33fx5();
        };
        _0xf33fx6['Open'][element]['onerror'] = function(element) {
            _0xf33fx6['FindHost']++;
            _0xf33fx6['FindHost'] == _0xf33fx6['Urls']['length'] && _0xf33fx5();
        };
    };
    _0xf33fxa['prototype']['Reseter'] = function(element) {
        this['Model'].ModelReset();
        this['UpBreaker'] = this['Breaker'] = 0;
        this['SpeedData'] = [0];
        this['EventData'] = [];
        this['EventTotal'] = [];
        this['FinalData'] = [0];
        this['LiveSpeed'] = 0;
        this['newTime'] = [];
        this['CurTime'] = [];
        this['SumData'] = [0];
        this['Model']['AvgOverallSpeed'] = [];
        this['Model']['OverallFinalSpeed'] = 0;
        this['Model']['AvgFinalSpeed'] = [];
        this['OverallTime'] = Date['now']();
        this['OneStop'] = [];
        this['Stopit'] = void(0);
        this['totalRequests'] = this['upRequests'];
        this['UpFirst'] = this['DownFirst'] = this['Data2'] = this['Data1'] = void(0);
        this['SendData'] = this['UploadData'];
    };
    _0xf33fxa['prototype']['CtrlUI'] = function(element) {
        var _0xf33fx5 = this;
        _0xf33fx5.Reseter();
        _0xf33fx5['View']['prepareDown']();
        _0xf33fx5['View'].ProgressBar(0, _0xf33fx5['downtestDuration']);
        _0xf33fx5['Stage'] = 0;
        var _0xf33fx6 = setInterval(function() {
            _0xf33fx5['DisplaySpeed'] = _0xf33fx5['Model']['convertSpeedToString'](_0xf33fx5.LiveSpeed);
            _0xf33fx5['View'].LiveSpeedMainDisplay(_0xf33fx5.DisplaySpeed);
            0 == _0xf33fx5['Stage'] ? (_0xf33fx5['View'].MainSpeedProgress(_0xf33fx5.DisplaySpeed), _0xf33fx5['FinalSpeedtoShow'] = _0xf33fx5['Model']['speedAvg'](_0xf33fx5.LiveSpeed, _0xf33fx5['speedCalcInterval'], _0xf33fx5.FinalDisplaySpeed, _0xf33fx5['downtestDuration']), _0xf33fx5['FinalDown'] = _0xf33fx5['FinalSpeedtoShow']) : 1 == _0xf33fx5['Stage'] && (_0xf33fx5['View'].MainSpeedProgress(_0xf33fx5.DisplaySpeed), _0xf33fx5['FinalSpeedtoShow'] = _0xf33fx5['Model']['speedAvg'](_0xf33fx5.LiveSpeed, _0xf33fx5['speedCalcInterval'], _0xf33fx5.FinalDisplayUpSpeed, _0xf33fx5['uploadDuration']));
            if (1 == _0xf33fx5['Breaker'] && void(0) == _0xf33fx5['Stopit']) {
                _0xf33fx5['Stage'] = 10;
                _0xf33fx5['Stopit'] = 0;
                _0xf33fx5['Breaker']++;
                _0xf33fx5['xhrlogDown'][_0xf33fx5['xhrN']] = _0xf33fx5['Model']['convertSpeedToString'](_0xf33fx5.FinalSpeedtoShow);
                0 == _0xf33fx5['FinalSpeedtoShow'] && (_0xf33fx5['FinalSpeedtoShow'] = _0xf33fx5['LiveSpeed']);
                _0xf33fx5['View'].DisplayFinalDown(_0xf33fx5['Model']['convertSpeedToString'](_0xf33fx5.FinalSpeedtoShow));
                var element = _0xf33fx5['LiveSpeed'],
                    newElement = setInterval(function() {
                        element = _0xf33fx5['Model']['lastSpeddtoZero'](element);
                        _0xf33fx5['View'].MainSpeedProgress(element);
                        _0xf33fx5['View'].LiveSpeedMainDisplay(_0xf33fx5['Model']['convertSpeedToString'](element));
                        0 >= element['toFixed'](0) && setTimeout(function() {
                            _0xf33fx5['LiveSpeed'] = 0;
                            clearInterval(newElement);
                        }, 1750);
                    }, 14);
                setTimeout(function() {
                    _0xf33fx5.Reseter();
                    _0xf33fx5['View']['prepareUp']();
                    _0xf33fx5['View'].ProgressBar(1, _0xf33fx5['uploadDuration']);
                    _0xf33fx5.UpRequest(0);
                    _0xf33fx5['UPTimeOuter'] = _0xf33fx5['uploadDuration'] + 500;
                    setTimeout(function() {
                        _0xf33fx5['UpBreaker'] = 1
                    }, _0xf33fx5.UPTimeOuter);
                    _0xf33fx5['Stage'] = 1;
                }, 2500);
            };
            if (1 == _0xf33fx5['UpBreaker']) {
                _0xf33fx5['xhrlogUp'][_0xf33fx5['xhrN']] = _0xf33fx5['Model']['convertSpeedToString'](_0xf33fx5.FinalSpeedtoShow);
                _0xf33fx5.ServerConnect(3);
                0 == _0xf33fx5['FinalSpeedtoShow'] && (_0xf33fx5['FinalSpeedtoShow'] = _0xf33fx5['LiveSpeed']);
                console['log']('While testing download speed = ' + _0xf33fx5['downData'] / 1048576 + ' Megabytes of data used!  & Average download speed in KB/s = ' + 1E3 * _0xf33fx5['FinalDown'] / 8 + ' While testing upload speed = ' + _0xf33fx5['upData'] / 1048576 + ' Megabytes of data used  & Average upload speed in KB/s = ' + 1E3 * _0xf33fx5['FinalSpeedtoShow'] / 8);
                _0xf33fx5['View'].DisplayFinalUp(_0xf33fx5['Model']['convertSpeedToString'](_0xf33fx5.FinalSpeedtoShow));
                var _0xf33fx9 = _0xf33fx5['LiveSpeed'],
                    _0xf33fxa = setInterval(function() {
                        _0xf33fx9 = _0xf33fx5['Model']['lastSpeddtoZero'](_0xf33fx9);
                        _0xf33fx5['View'].MainSpeedProgress(_0xf33fx9);
                        _0xf33fx5['View'].LiveSpeedMainDisplay(_0xf33fx5['Model']['convertSpeedToString'](_0xf33fx9));
                        0 >= _0xf33fx9['toFixed'](0) && setTimeout(function() {
                            _0xf33fx5['LiveSpeed'] = 0;
                            _0xf33fx5['View'].LiveSpeedMainDisplay(0);
                            clearInterval(_0xf33fxa);
                        }, 1E3);
                    }, 14);
                clearInterval(_0xf33fx6);
            };
        }, 14);
        _0xf33fx5.DownRequest(0);
        _0xf33fx5['TimeOuter'] = _0xf33fx5['downtestDuration'] + 500;
        setTimeout(function() {
            _0xf33fx5['Breaker'] = 1;
            _0xf33fx5['timeoutTime'] = 0;
        }, _0xf33fx5.TimeOuter);
    };
    _0xf33fxa['prototype']['DownRequest'] = function(element) {
        var _0xf33fx5 = this;
        _0xf33fx5['Open'][element] = new XMLHttpRequest;
        _0xf33fx5['OneStop'][element] = 0;
        _0xf33fx5['newTime'][element] = Date['now']();
        _0xf33fx5['Open'][element]['open']('GET', 'http://' + _0xf33fx5['domainx'] + '/' + _0xf33fx5['DownloadFileName'] + '?n=' + Math['random'](), !0);
        _0xf33fx5['Open'][element]['timeout'] = _0xf33fx5['downtestDuration'];
        _0xf33fx5['Open'][element]['ontimeout'] = function() {
            _0xf33fx5['Breaker'] = 1
        };
        _0xf33fx5['Open'][element]['send'](null);
        _0xf33fx5['Open'][element]['onprogress'] = function(_0xf33fx6) {
            _0xf33fx5['CurTime'][element] = Date['now']() - _0xf33fx5['newTime'][element];
            _0xf33fx5['OverallCurTime'] = (Date['now']() - _0xf33fx5['OverallTime']) / 1E3;
            _0xf33fx5['EventData'][element] = _0xf33fx6['loaded'];
            _0xf33fx5['EventTotal'][element] = _0xf33fx6['total'];
            _0xf33fx5['SpeedData'][element] = 1.088576 * parseFloat(_0xf33fx5['EventData'][element] / _0xf33fx5['CurTime'][element] * 8E3 / 1048576);
            this['EventLoadedSum'] = +_0xf33fx5['Model'].GetArrSum(_0xf33fx5.EventData);
            this['EventTotalSum'] = +_0xf33fx5['Model'].GetArrSum(_0xf33fx5.SumData);
            _0xf33fx5['downData'] = this['EventLoadedSum'] + this['EventTotalSum'];
            1 >= _0xf33fx5['OneStop'][element] && _0xf33fx5['EventData'][element] != _0xf33fx5['EventTotal'][element] && (_0xf33fx5['SpeedData'][element] = 0.19, _0xf33fx5['OneStop'][element]++);
            _0xf33fx5['LiveSpeed'] = _0xf33fx5['Model'].GetArrSum(_0xf33fx5.SpeedData);
            1 <= _0xf33fx5['Breaker'] ? _0xf33fx5['Open'][element]['abort']() : _0xf33fx5['EventData'][element] >= _0xf33fx5['EventTotal'][element] && (_0xf33fx5['SumData']['push'](_0xf33fx5['EventTotal'][element]), _0xf33fx5['EventData'][element] = 0, _0xf33fx5.DownRequest(element));
            if (void(0) == _0xf33fx5['DownFirst']) {
                for (_0xf33fx5['DownFirst'] = 0, _0xf33fx6 = 1; _0xf33fx6 < _0xf33fx5['downRequests']; _0xf33fx6++) {
                    _0xf33fx5.DownRequest(_0xf33fx6)
                }
            };
        };
    };
    _0xf33fxa['prototype']['UpRequest'] = function(element) {
        var _0xf33fx5 = this;
        _0xf33fx5['OneStop'][element] = 0;
        _0xf33fx5['newTime'][element] = Date['now']();
        _0xf33fx5['Open'][element] = new XMLHttpRequest;
        var _0xf33fx6 = 'http://' + _0xf33fx5['domainx'] + '/' + _0xf33fx5['UploadFileName'] + '?n=' + Math['random']();
        _0xf33fx5['Open'][element]['open']('POST', _0xf33fx6, !0);
        _0xf33fx5['Open'][element]['timeout'] = _0xf33fx5['uploadDuration'];
        _0xf33fx5['Open'][element]['ontimeout'] = function() {
            _0xf33fx5['UpBreaker'] = 1
        };
        _0xf33fx5['Open'][element]['setRequestHeader']('Content-Type', 'application/octet-stream');
        _0xf33fx5['Open'][element]['upload']['onprogress'] = function(_0xf33fx6) {
            _0xf33fx5['CurTime'][element] = Date['now']() - _0xf33fx5['newTime'][element];
            _0xf33fx5['OverallCurTime'] = (Date['now']() - _0xf33fx5['OverallTime']) / 1E3;
            _0xf33fx5['EventData'][element] = _0xf33fx6['loaded'];
            _0xf33fx5['EventTotal'][element] = _0xf33fx6['total'];
            _0xf33fx5['SpeedData'][element] = parseFloat(_0xf33fx5['EventData'][element] / _0xf33fx5['CurTime'][element] * 8E3 / 1048576);
            this['EventLoadedSum'] = +_0xf33fx5['Model'].GetArrSum(_0xf33fx5.EventData);
            this['EventTotalSum'] = +_0xf33fx5['Model'].GetArrSum(_0xf33fx5.SumData);
            _0xf33fx5['upData'] = this['EventLoadedSum'] + this['EventTotalSum'];
            1 >= _0xf33fx5['OneStop'][element] && _0xf33fx5['EventData'][element] != _0xf33fx5['EventTotal'][element] && (_0xf33fx5['SpeedData'][element] = 0.136, _0xf33fx5['OneStop'][element]++);
            _0xf33fx5['LiveSpeed'] = _0xf33fx5['Model'].GetArrSum(_0xf33fx5.SpeedData);
            if (1 == _0xf33fx5['UpBreaker']) {
                console['log']('Upload Request no ' + element + 'Stopped'), _0xf33fx5['Open'][element]['abort']()
            } else {
                if (_0xf33fx5['EventData'][element] >= _0xf33fx5['EventTotal'][element]) {
                    _0xf33fx5['SumData']['push'](_0xf33fx5['EventTotal'][element]);
                    _0xf33fx5['EventData'][element] = 0;
                    if (2 <= _0xf33fx5['totalRequests']++ && 3 >= _0xf33fx5['OverallCurTime'] && void(0) == _0xf33fx5['Data1']) {
                        for (_0xf33fx5['UploadData1'] = _0xf33fx5['SendData'] + _0xf33fx5['SendData'], _0xf33fx5['SendData'] = _0xf33fx5['UploadData1'], _0xf33fx5['Data1'] = 0, _0xf33fx6 = 1; _0xf33fx6 < _0xf33fx5['upRequests'] / 2; _0xf33fx6++) {
                            _0xf33fx5.UpRequest(_0xf33fx6)
                        }
                    };
                    if (10 <= _0xf33fx5['totalRequests']++ && 14 >= _0xf33fx5['OverallCurTime'] && void(0) == _0xf33fx5['Data2']) {
                        for (_0xf33fx5['UploadData2'] = _0xf33fx5['UploadData1'] + _0xf33fx5['UploadData1'], _0xf33fx5['SendData'] = _0xf33fx5['UploadData2'], _0xf33fx5['Data2'] = 0, _0xf33fx6 = _0xf33fx5['upRequests'] / 2; _0xf33fx6 < _0xf33fx5['upRequests']; _0xf33fx6++) {
                            _0xf33fx5.UpRequest(_0xf33fx6)
                        }
                    };
                    _0xf33fx5.UpRequest(element);
                }
            };
        };
        _0xf33fx5['Open'][element]['onerror'] = function(_0xf33fx6) {
            0 == _0xf33fx5['UpBreaker'] && _0xf33fx5.UpRequest(element)
        };
        _0xf33fx5['Open'][element]['send'](Date() + ' Copyright \xA92015, All Rights Reserved by Brandon Bailey ' + _0xf33fx5['SendData'] + Math['random']());
    };
    _0xf33fxa['prototype']['ServerConnect'] = function(element) {
        var _0xf33fx5 = this;
        _0xf33fx5['xhr'] = new XMLHttpRequest;
        _0xf33fx5['url'] = './connect.php';
        1 == element && (_0xf33fx5['url'] = 'http://server.openspeedtest.com/get_ip.php');
        _0xf33fx5['xhr']['open']('POST', _0xf33fx5['url'], !0);
        _0xf33fx5['xhr']['setRequestHeader']('Content-type', 'application/x-www-form-urlencoded');
        _0xf33fx5['xhr']['onreadystatechange'] = function() {
            4 == _0xf33fx5['xhr']['readyState'] && 200 == _0xf33fx5['xhr']['status'] && (_0xf33fx5['return_data'] = _0xf33fx5['xhr']['responseText']['trim'](), 2 == element && (_0xf33fx5['StfS'] = _0xf33fx5['return_data']), 1 == element && (_0xf33fx5['TestServerip'] = _0xf33fx5['domainx']), 3 == element && setTimeout(function() {
                location['href'] = 'http://openspeedtest.com/results/widget.php?r=' + _0xf33fx5['return_data']
            }, 1500))
        };
        2 == element && (_0xf33fx5['logData'] = 'r=n');
        3 == element && (_0xf33fx5['logData'] = 'r=l&d=' + _0xf33fx5['xhrlogDown'][_0xf33fx5['xhrN']] + '&u=' + _0xf33fx5['xhrlogUp'][_0xf33fx5['xhrN']] + '&dd=' + _0xf33fx5['downData'] / 1048576 + '&ud=' + _0xf33fx5['upData'] / 1048576 + '&p=' + _0xf33fx5['smallping'] + '&do=' + _0xf33fx5['domainx'] + '&S=' + _0xf33fx5['StfS'] + '&sip=' + _0xf33fx5['TestServerip']);
        _0xf33fx5['xhr']['send'](_0xf33fx5['logData']);
    };
    _0xf33fx1['Start'] = function() {
        new _0xf33fxa
    };
})(window['OpenSpeedTest'] = window['OpenSpeedTest'] || {});