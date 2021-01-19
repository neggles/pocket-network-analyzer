<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="container-fluid">
    <div class="row">
        <div class="text-center">
            <h1>Remote Tools</h1>
        </div>
    </div>
    </div>

    <div class="container-fluid">
        <div class="row-fluid">

<div class="col-md-4 col-sm-6 col-xs-12" id="ftpButton">	
<?php if ($this->remote->checkFtp()) :?>
        <button role="button" class="btn btn-danger btn-lg btn-block" disabled>FTP is Running</button>      
<?php else : ?>
    <button role="button" id="runFtp" class="btn btn-primary btn-lg btn-block">Run FTP Service</button>
<?php endif; ?>     
</div>

<div class="col-md-4 col-sm-6 col-xs-12" id="sshButton">		
            
            <?php if ($this->remote->checkSsh() == "active") :?>
                <button role="button" id="stopSsh" class="btn btn-danger btn-lg btn-block">Stop SSH Service</button>
            <?php else : ?>
                <button role="button" id="runSsh" class="btn btn-primary btn-lg btn-block">Run SSH Service</button>
            <?php endif; ?>     
</div>	

        </div>

    </div>
<script>
$(document).on('ready',function(){

$(document).on('click','button#runFtp',function(){
    $.ajax({
            method:'post',
            dataType:'json',
                url: "remotetechnician/runftp",
                success: function(data)
                {
                    if(data.status =="error")
                    {
                        alertify.error(data.msg);
                    }
                    else if(data.status == true)
                    {
                        alertify.success(data.msg);
                        $('div#ftpButton').html('<button role="button" class="btn btn-danger btn-lg btn-block" disabled>FTP is Running</button>');
                    }
                    
                }
    })
})

$(document).on('click','button#runSsh',function(){
    $.ajax({
            method:'post',
            dataType:'json',
                url: "remotetechnician/runssh",
                success: function(data)
                {
                    if(data.status == false)
                    {
                        alertify.error(data.msg);
                    }
                    else if(data.status == true)
                    {
                        alertify.success(data.msg);
                        $('div#sshButton').html('<button role="button" class="btn btn-danger btn-lg btn-block" id="stopSsh">Stop SSH Service</button>')
                    }
                }
    })
})

$(document).on('click','button#stopSsh',function(){
    $.ajax({
            method:'post',
            dataType:'json',
                url: "remotetechnician/stopssh",
                success: function(data)
                {
                    if(data.status == false)
                    {
                        alertify.error(data.msg);
                    }
                    else if(data.status == true)
                    {
                        alertify.success(data.msg);
                        $('div#sshButton').html('<button role="button" class="btn btn-primary btn-lg btn-block" id="runSsh">Run SSH Service</button>')

                    }
                    
                }
    })
})

})
</script>
</body>
</html>