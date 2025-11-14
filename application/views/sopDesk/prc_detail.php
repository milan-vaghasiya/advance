<?php
    $prcDetail = '<div class="cd-header"><h6 class="m-0 prc_number">PRC DETAIL</h6></div>
                    <div class="sop-body vh-35" data-simplebar>
					    <div>
					        <div class="text-center">
    					        <img src="'.base_url('assets/images/background/dnf_2.png').'" style="width:50%;">
    						    <div class="text-center text-muted font-16 fw-bold">Please click any <strong>PRC</strong> to see Data</div>
						    </div>
					    </div>
					</div>';
    if(!empty($prcData))
    {
        $clearBtn = '';
        if(!empty($prcProcessData[0]->pending_accept) && $prcProcessData[0]->pending_accept  == $prcData->prc_qty){
            $clearParam = "{'postData':{'id' : ".$prcData->prc_id."}, 'message' : 'Are you sure you want to clear this PRC ?','fnsave':'clearPrcData','res_function':'getPrcResponse'}";
            $clearBtn = ' <a class="text-dark" href="javascript:void(0)" datatip="Clear PRC" flow="down" onclick="confirmSOPStore('.$clearParam.')" ><i class="mdi mdi-broom font-26"></i></a>';
        }
    
        $status = "";
        if($prcData->status == 1){$status = '<span class="badge bg-info">Planned</span>';}
        elseif($prcData->status == 2){$status = '<span class="badge bg-primary">Inprogress</span>';}
        elseif($prcData->status == 3){$status = '<span class="badge bg-success">Completed</span>';}
        elseif($prcData->status == 4){$status = '<span class="badge bg-warning">On Hold</span>';}
        elseif($prcData->status == 5){$status = '<span class="badge bg-danger">Closed</span>';}

        $die_data = '';
        
        $prcDetail = '<div class="cd-header" style="padding: 7px 16px;">
                            <h6 class="m-0 prc_number">#'.$prcData->prc_number.' '.$status.'</h6>
                            <p class="mb-0 fs-12 "><i class="far fa-fw fa-clock"></i> <span class="prc_date">'.formatDate($prcData->prc_date,"d M Y").'</span></p>
                            <div class="cd-features">
    							<div class="dropdown d-inline-block">
                                    <a class="text-dark" href="'.base_url('sopDesk/printDetailRouteCard/'.$prcData->prc_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print font-22"></i></a>
                                  
                                    <a class="text-dark" href="'.base_url('sopDesk/').'" datatip="Back" flow="down"><i class="fas fa-backward font-22"></i></a>

                                    '.$clearBtn.'
    							</div>
                            </div>
                        </div>
                        <div class="sop-body vh-35" data-simplebar>
    					    <div class="prcDetail1">
    					        <div class="" style="border-bottom: 1px dashed #e8ebf3;" >
                                    <p class="m-0 font-15">Product</p>
                                    <p class="text-muted fw-semibold1 mb-0">'.$prcData->item_name.'</p>
                                </div>
                                <div class="mt-2" style="border-bottom: 1px dashed #e8ebf3;" >
                                    <p class="m-0 font-15">Customer</p>
                                    <p class="text-muted fw-semibold1 mb-0">'.$prcData->party_name.'</p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-8 col-lg-8">
                                        <div class="mt-2" style="border-bottom: 1px dashed #e8ebf3;" >
                                            <p class="m-0 font-15">Sales Order</p>
                                            <p class="text-muted fw-semibold1 mb-0">'.$prcData->so_number.' '.(!empty($prcData->doc_no) ? '('.$prcData->doc_no.')' : '').'</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-4">
                                        <div class="mt-2" style="border-bottom: 1px dashed #e8ebf3;">
                                            <p class="m-0 font-15">Job Qty<br><span class="text-muted">'.floatval($prcData->prc_qty).' <small>'.$prcData->uom.'</small></span></p>
                                        </div>
                                    </div>
                                </div>';
								
                                if($prcData->status == 2){
									$prcDetail  .= '<div class="mt-2" style="border-bottom: 1px dashed #e8ebf3;">
										<p class="m-0 font-15">Revision No<br></p>
										<select class="form-control select2 rev_no" name="rev_no">
											<option value="">Select Revision</option>';
											if(!empty($revList)){
												foreach($revList as $row){
													$selected = (!empty($prcData->rev_no) && $prcData->rev_no == $row->rev_no)?'selected':'';
													$prcDetail .= '<option value="'.$row->rev_no.'" '.$selected.' data-prc_id="'.$prcData->id.'">'.$row->rev_no.' [Drw No : '.$row->drw_no.']'.'</option>';
												}
											}
									$prcDetail .= '</select>
										<div class="error general_error"></div>
									</div>';
								}
					            $prcDetail .= '<p class="mt-1">'.$prcData->remark.'</p>
								'.$die_data.'
							</div>';
                                
                            if (!empty($invData)){
                                $total_qty = 0;
                                $prcDetail .='<div class="col-md-12 col-lg-12 mt-2">
                                    <table class="table jpExcelTable">
                                        <tr class="bg-light-peach">
                                            <th>Inv No.</th>
                                            <th>Inv Date</th>
                                            <th>Inv Qty</th>
                                        </tr>';
                                        
                                        foreach ($invData as $row){
                                            $total_qty += $row->inv_qty;

                                            $prcDetail .= '<tr>';
                                            $prcDetail .= '<td>' . $row->inv_number . '</td>';
                                            $prcDetail .= '<td>' . formatDate($row->inv_date) . '</td>';
                                            $prcDetail .= '<td>' . floatVal($row->inv_qty) . '</td>';
                                            $prcDetail .= '</tr>';
                                        }

                                        $prcDetail .='<tr><td colspan="2" class="text-right"><b class="mr-2">Total</b></td><td><b>'.$total_qty.'</b></td></tr>';
                                $prcDetail .='</table></div>';
                            }
						$prcDetail .='</div>';
    }
    echo $prcDetail;

?>