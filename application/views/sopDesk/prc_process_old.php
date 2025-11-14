<?php
    $prcProcess = '<img src="'.base_url('assets/images/background/dnf_1.png').'" style="width:100%;">
				    <h3 class="text-danger text-center font-24 fw-bold line-height-lg">Sorry!<br><span class="text-dark">Data Not Found</span></h3>
				    <div class="text-center text-muted font-16 fw-bold pt-3 pb-1">Pleasae click any <strong>PRC</strong> to see Data</div>';
    if($status > 1)
    {
        if(!empty($prcProcessData))
        {
            $prcProcess="";
            foreach($prcProcessData as $row){
                $currentProcess = !empty($row->current_process)?$row->current_process : 'Initial Stage';
                $lastMove = (!empty($row->next_process) ? $row->next_process : 'Ready To Dispatch');
                $in_qty = (!empty($row->current_process_id))?(!empty($row->in_qty)?$row->in_qty:0):$row->ok_qty;
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

                $logBtn = "";$movementBtn="";$chReqBtn="";$receiveBtn="";
                if(!empty($row->current_process_id)){
                    $logParam = "{'postData':{'id' : ".$row->id."},'modal_id' : 'bs-right-lg-modal', 'call_function':'prcLog', 'form_id' : 'addPrcLog', 'title' : 'PRC LOG', 'js_store_fn' : 'storeSop', 'fnsave' : 'savePRCLog','button':'close'}";
                    $logBtn = '<a href="javascript:void(0)" onclick="loadform('.$logParam.')" class="dropdown-item btn btn-danger permission-modify" datatip="Add Log" flow="down"><i class="fas fa-stop-circle"></i> Log</a>';

                    $title = '[Pending Qty : '.floatval($pending_production).']';
                    $chReqParam = "{'postData':{'id' : ".$row->id.",'prc_id':".$row->prc_id."},'modal_id' : 'bs-right-md-modal', 'call_function':'challanRequest', 'form_id' : 'addChallanRequest', 'title' : 'Challan Request ".$title ."', 'js_store_fn' : 'storeSop', 'fnsave' : 'saveAcceptedQty','button':'close'}";
                    $chReqBtn = '<a href="javascript:void(0)" class="dropdown-item btn btn-danger permission-modify" datatip="Challan Request" flow="down" onclick="loadform('.$chReqParam .')"><i class="fas fa-stop-circle"></i> Challan Request</a>';
                }
                $movementBtn="";
                if(!empty($row->next_process_id)){
                    $movementParam = "{'postData':{'id' : ".$row->id."},'modal_id' : 'bs-right-lg-modal', 'call_function':'prcMovement', 'form_id' : 'addPrcMovement', 'title' : 'PRC Movement', 'js_store_fn' : 'storeSop', 'fnsave' : 'savePRCMovement','button':'close'}";
                    $movementBtn = '<a href="javascript:void(0)" class="dropdown-item btn btn-danger permission-modify" datatip="Movement" flow="down" onclick="loadform('.$movementParam.')"><i class="fas fa-stop-circle"></i> Movement</a>';

                    $receiveParam = "{'postData':{'id' : ".$row->id."},'modal_id' : 'bs-right-lg-modal', 'call_function':'receiveStoredMaterial', 'form_id' : 'addPrcMovement', 'title' : 'Receive From Store', 'js_store_fn' : 'storeSop', 'fnsave' : 'saveReceiveStoredMaterial'}";
                    $receiveBtn = '<a href="javascript:void(0)" class="dropdown-item btn btn-danger permission-modify" datatip="Receive" flow="down" onclick="loadform('.$receiveParam.')"><i class="fas fa-stop-circle"></i> Receive From Store</a>';
                }else{
                    $movementParam = "{'postData':{'id' : ".$row->id."},'modal_id' : 'bs-right-md-modal', 'call_function':'addPrcStock', 'form_id' : 'addPrcMovement', 'title' : 'Add Prc Stock', 'js_store_fn' : 'storeSop', 'fnsave' : 'storePrcStock','button':'close'}";
                    $movementBtn = '<a href="javascript:void(0)" class="dropdown-item btn btn-danger permission-modify" datatip="Movement" flow="down" onclick="loadform('.$movementParam.')"><i class="fas fa-stop-circle"></i> PRC Stock</a>';
                }

                $acceptBtn="";
                if(!empty($row->current_process_id) && ($pending_accept > 0 || $in_qty > 0)){
                    $title = '[Pending Qty : '.floatval($pending_accept).']';
                    $acceptParam = "{'postData':{'id' : ".$row->id.",'prc_id':".$row->prc_id.",'prev_prc_process_id':".$row->prev_prc_process_id."},'modal_id' : 'bs-right-md-modal', 'call_function':'prcAccept', 'form_id' : 'addPrcAccept', 'title' : 'Accept For Production ".$title."', 'js_store_fn' : 'storeSop', 'fnsave' : 'saveAcceptedQty','button':'close'}";
                    $acceptBtn = '<a href="javascript:void(0)" class="dropdown-item btn btn-danger permission-modify" datatip="Accept" flow="down" onclick="loadform('.$acceptParam .')"><i class="fas fa-stop-circle"></i> Accept</a>';
                }

                
               
                $prcProcess .= '<div class=" grid_item" style="width:100%;">
                                    <div class="card sh-perfect">
                                        <div class="card-body">                                    
                                            <div class="task-box">
                                                <div class="task-priority-icon"><i class="fas fa-circle text-primary" style="border: 5px solid #e9edf2;"></i></div>
                                                <div class="float-end">
                                                    <!--<span class="badge badge-soft-pink fw-semibold ms-2 v-super"><i class="far fa-fw fa-clock"></i> 05 days left</span>-->
                                                     <a  target="_blank"  href="'.base_url("sopDesk/prcProcesstag/".$row->id).'" role="button" aria-haspopup="false" aria-expanded="false">
                                                            <i class="fas fa-qrcode"></i>
                                                        </a>
                                                    <div class="dropdown d-inline-block">
                                                        <a class="dropdown-toggle" id="dLabel1" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                                            <i class="las la-ellipsis-v font-24 text-muted"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            '.$acceptBtn.$logBtn.$chReqBtn.$movementBtn.$receiveBtn.'
                                                         </div>
                                                    </div>
                                                </div>
                                                <h5 class="mt-0 fs-15 cursor-pointer" >'.$currentProcess.'</h5>
                                                <p class="text-muted mb-0 font-13"><span class="fw-semibold">Next Process :</span> '.$lastMove.'</p>
                                                <div class="d-flex justify-content-between">  
                                                    <h6 class="fw-semibold">Unaccepted : <span class="text-muted font-weight-normal"> '.floatval($pending_accept).'</span></h6>
                                                    <h6 class="fw-semibold">Stock : <span class="text-muted font-weight-normal"> '.floatval($pending_movement).'</span></h6>                          
                                                </div>
                                                <hr class="hr-dashed my-5px">
                                                <div class="media align-items-center btn-group process-tags">
                                                    <span class="badge bg-light-peach btn flex-fill" datatip="In Qty" flow="down">IN : '.floatval($in_qty).'</span>
                                                    <span class="badge bg-light-teal btn flex-fill" datatip="OK Qty" flow="down">OK : '.floatval($ok_qty).'</span>
                                                    <span class="badge bg-light-cream btn flex-fill" datatip="Rejected Qty" flow="down">RJ : '.floatval($rej_qty).'</span>
                                                    <span class="badge bg-light-raspberry btn flex-fill" datatip="Pending Production" flow="down">PQ : '.floatval($pending_production).'</span>
                                                </div>                                       
                                            </div>
                                        </div>
                                    </div>
                                </div>';
            }
        }
    }
    else
    {
        if(!empty($prcProcessData))
        {
            $i=1;$prcProcess="";
            $prcProcess = '<div class="activity">';
            foreach($prcProcessData as $key=>$row){
                $prcProcess .='<div class="activity-info">
                                    <div class="icon-info-activity"><i class="las bg-soft-primary">'.$i++.'</i></div>
                                    <div class="activity-info-text">
                                        <div class="d-flex justify-content-between align-items-center"><h6 class="m-0  w-75 mt-2">'.$row->process_name.'</h6></div>
                                    </div>
                                </div>';
            }
            $prcProcess .= '</div>';
        }
    }
    
    echo $prcProcess;

?>