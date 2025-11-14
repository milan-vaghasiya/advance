<?php $this->load->view('includes/header'); ?>
<style>li.nav-item{padding:0px 3px;}.process-tags span{font-size:0.75rem;color:#000;box-shadow: 0px 1px 1px rgba(9, 30, 66, 0.25), 0px 0px 1px 1px rgba(9, 30, 66, 0.13);}.prcList{padding:0.4rem;}</style>

<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <?php
            if(!empty($processList)){
                foreach($processList as $row){
                    $in_qty = (!empty($row->in_qty)?$row->in_qty:0);
                    $ok_qty = !empty($row->ok_qty)?$row->ok_qty:0;
                    $rej_found_qty = !empty($row->rej_found)?$row->rej_found:0;
                    $rej_qty = !empty($row->rej_qty)?$row->rej_qty:0;
                    $rw_qty = !empty($row->rw_qty)?$row->rw_qty:0;
                    $pendingReview = $rej_found_qty - $row->review_qty;
                    $pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview);
                    $movement_qty =!empty($row->movement_qty)?$row->movement_qty:0;
                    $short_qty =!empty($row->short_qty)?$row->short_qty:0;
                    $pending_movement = $ok_qty - ($movement_qty);
                    $pending_accept =!empty($row->pending_accept)?$row->pending_accept:0;
                    ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card report-card sh-perfect">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col">
                                        <!--<a href="javascript:void(0)" class="text-dark mb-1 fw-semibold"><?=$row->process_name?></a>-->
                                        <div class="bot_style">
                                            <h2 class="heading"><?=$row->process_name?></h2>
                                        </div>
                                    </div>
                                    <!--
                                    <div class="col-auto align-self-center">
                                        <div class="button-items"> 
                                            <a href="<?=base_url($headData->controller.'/productionLog/'.$row->id)?>" datatip="Manufacturing" flow="down" class="btn btn-outline-primary btn-icon-circle btn-icon-circle-sm"><i class="fas fa-cogs"></i></a>
                                            <a href="<?=base_url($headData->controller.'/mfgStore/'.$row->id)?>" datatip="Stock" flow="down" class="btn btn-outline-success btn-icon-circle btn-icon-circle-sm"><i class="fas fa-database"></i></a>
                                        </div>
                                    </div>
                                    -->
                                    <!--
                                    <hr class="hr-dashed my-5px">
                                    <div class="media align-items-center btn-group  process-tags">
                                        <span class="badge bg-light-peach  flex-fill mr-2" datatip="In Qty" flow="down">IN : <?=floatval($in_qty)?></span>
                                        <span class="badge bg-light-teal  flex-fill mr-2" datatip="OK Qty" flow="down">OK : <?=($row->id != 1)?floatval($ok_qty):floatval($row->accepted_qty)?></span>
                                        <span class="badge bg-light-raspberry  flex-fill mr-2" datatip="Pending Production" flow="down">PQ : <?=(($row->id != 1)?floatval($pending_production):"")?></span>
                                        <span class="badge bg-light-cream  flex-fill mr-2" datatip="Stock Qty" flow="down">Stock : <?=($row->id != 1)?floatval($pending_movement):''?></span>
                                    </div>
                                    -->
                                </div>
                            </div>
                            <div class="stage-footer">
                                <div class="col-md-3 text-center badge bg-light-sky flex-fill br-0">
                                    <h2 class="font-light fs-18 m-0"><?=floatval($pendingReview)?></h2>
                                    <h4 class="text-uppercase fs-15 m-0">Pending QC</h4>
                                </div>
                                <!-- <div class="col-md-3 text-center badge bg-light-teal flex-fill br-0">
                                    <h2 class="font-light fs-18 m-0"><?=($row->id != 1)?floatval($ok_qty):floatval($row->accepted_qty)?></h2>
                                    <h4 class="text-uppercase fs-15 m-0">Ok</h4>
                                </div> -->
                                <div class="col-md-3 text-center badge bg-light-raspberry flex-fill br-0">
                                    <h2 class="font-light fs-18 m-0"><?=(($row->id != 1)?floatval($pending_production):"0")?></h2>
                                    <h4 class="text-uppercase fs-15 m-0">Pending</h4>
                                </div>
                                <div class="col-md-3 text-center badge bg-light-cream flex-fill br-0">
                                    <h2 class="font-light fs-18 m-0"><?=($row->id != 1)?floatval($pending_movement):'0'?></h2>
                                    <h4 class="text-uppercase fs-15 m-0">Stock</h4>
                                </div>
                            </div>
                            <div class="stage-footer">
								<a role="button" href="<?=base_url($headData->controller.'/productionLog/'.$row->id)?>" target="_blank" class="stage-btn mfg-btn m-0 p-3" datatip="Manufacturing" flow="down" >
								    <i class="fas fa-cogs fs-13"></i> <span class="lable">Manufacturing</span>
								</a>
								<a href="<?=base_url($headData->controller.'/mfgStore/'.$row->id)?>" target="_blank" class="stage-btn stk-btn m-0 p-3" datatip="Stock" flow="down">
								    <i class="fas fa-eye fs-13"></i> <span class="lable">View Stock</span>
								</a>
							</div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>