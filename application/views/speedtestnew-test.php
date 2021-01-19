<script src="/assets/js/plugins/speedtest/dslr-speedtest-clean.js"></script>
<script>
// Single line javascript in header
// or before closing body tag:
//
// https://www.dslreports.com/assets/st/1.6/js/speedtest.js
//

function stoptest() {
  dslr_speedtest({op: 'stop'});
}

function speedtest() {
  log("Start button pushed");

  var o = new Object();

  // Can hint connection type
  // from 0..
  // ["GPRS", "3G", "4G", "WiFi", "Wireless", "Satellite", "DSL", "Cable", "Fiber", "", "Unsure"];
  // undefined == Unsure
  o.conntype = undefined;
  // for complete results, bufferbloat must be true
  // for faster results lacking grades and URL etc
  // set to false
  o.bufferbloat = true;
  
  // hz can be 4 (fastest), 2 (default) or 1 (slowest)
  // determines speed that onstatus is called
  o.hz = 4; 

  o.apiKey = '12345678'; // Test API key

  // fired continuously with basic info
  o.onstatus = function(e) {
    if (e.direction)
      log(e.direction + " megabit/sec: down/up " + e.down + " / " + e.up + " ping=" + e.ping + "ms");
  };

  // fired at 1hz with progress guesstimate
  o.onprogress = function(o) {
    document.getElementById('status').innerHTML = o.doing + " Progress:" + o.progress + "%";
  };

  o.onerror = function(o) {
    // this also marks the test end. oncomplete is not fired
    alert(o.msg);
  };

  // fired once upon successful conclusion
  // o has results.. see log for json version of structure
  o.oncomplete = function(o) {
    var s = JSON.stringify(o);
    log("oncomplete fired " + s);
  };

  // fired if the test wants to ask a question of the user with
  // a YES/NO answer.
  o.onconfirm = function(s) {
    return confirm(s);
  };
  
  // pass any user data in, it is stored 
  // and also returned with result.
  o.udata = { "myuserfield": "myvalue" };

  dslr_speedtest({
    op: 'start',
    params: o
  });
}

$(document).on("click","#startbutton",function(){
    console.log("Start button clicked");
    speedtest();
})

$(document).on("click","#stopbutton",function(){
    console.log("Stop button clicked")
    stoptest();
})

function log(s) {
  var log = $("#logdiv");
  log.html(s + "<br>" + log.html());
}
</script>

<h3>Speed Test library demo</h3>

<p>
  <center>
    <button id='startbutton'>
      Start Test</button>
    <button id='stopbutton'>
      Stop</button>
    <br> status: <span id='status'>
  </span></center>
  <br> Log:
</p>
<div id='logdiv'>
</div>
