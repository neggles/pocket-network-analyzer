            <div class="modal fade" id="<?php echo $id; ?>">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title text-center"><?php echo $title; ?></h4>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-condensed" id="<?php echo $type; ?>Table">
                                    <thead>
                                        <tr>
                                            <th>IP Address</th>
                                            <th>MAC</th>
                                            <th>Device Manuf.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($clientList->networks as $network) :
                                            $parts = preg_split('/(\s)/', $network, 3);
                                            echo '<tr>';
                                            if (isset($parts[0]) && $parts[0] !== "") :
                                                echo '<td class="ipAddr">' . $parts[0] . '</td>';
                                            endif;
                                            if (isset($parts[1])) :
                                                echo '<td class="macAddr">' . $parts[1] . '</td>';
                                            endif;
                                            if (isset($parts[1])) :
                                                echo '<td class="deviceManuf">' . $this->manuf->searchByMac($parts[1]) . '</td>';
                                            endif;
                                            echo '</tr>';
                                        endforeach;
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->