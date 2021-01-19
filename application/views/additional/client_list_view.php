<!--Client List-->
<div class="col-lg-4">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <div class="row">
                <div class="col-xs-3">
                    <i class="fa fa-wifi fa-3x"></i>
                </div>
                <div class="col-xs-9 text-right">
                    <p class="lead">Client List</p>
                </div>
            </div>
        </div>
        <div class="ibox-content">           
        <?php if ($this->wireless->getInterfaceStatus()) : ?>
            <?php $wirelessList = json_decode($this->wireless->getClientList());?>
            <a href="#" data-toggle="modal" data-target="#wirelessClientsModal">
                <p>Wireless Clients Connected  <span class="badge"><?php echo $wirelessList->count ?></span></p>
            </a>
            <?php $data = array(
                'id'=>'wirelessClientsModal',
                'clientList' => $wirelessList,
                'title' => 'Wireless Clients',
                'type' => 'wireless'
            );?>
            <?php $this->load->view('modals/ethernetClientsModal', $data);?>
    <?php endif; ?>
    <?php if ($this->ethernet->getInterfaceStatus()) : ?>
            <?php $ethernetList = json_decode($this->ethernet->getClientList()); ?>
            <a href="#" data-toggle="modal" data-target="#ethernetClientsModal">
                <p>Ethernet Clients Connected <span class="badge"><?php echo $ethernetList->count; ?> </span></p>
            </a>   
            <?php $data = array(
                'id'=>'ethernetClientsModal',
                'clientList' => $ethernetList,
                'title' => 'Ethernet Clients',
                'type' => 'ethernet'
            );?>

            <?php $this->load->view('modals/ethernetClientsModal', $data);?>
    <?php  endif; ?>        
        </div>
    </div>
</div>
<!--End Client List-->
