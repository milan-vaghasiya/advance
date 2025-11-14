<?php
    $prcMaterial = '<div class="text-center">
				        <img src="'.base_url('assets/images/background/dnf_3.png').'" style="width:50%;">
					    <div class="text-center text-muted font-16 fw-bold">Please click any <strong>PRC</strong> to see Data</div>
				    </div>';
	if(!empty($prcMaterialData))
	{
        $prcMaterial = "";
        foreach($prcMaterialData as $row){
            $rq = $prcData->prc_qty * $row->ppc_qty;
            $rm_name="";$mt_grade = "";
            if($prcData->prc_type == 1 && $row->process_id == 0 && $prcData->cutting_flow == 2){
                $uq = ((!empty($prcProcessData[0]->ok_qty) OR !empty($prcProcessData[0]->rej_found))?(floatval(($prcProcessData[0]->ok_qty + $prcProcessData[0]->rej_found) * $row->ppc_qty)/$prcProcessData[0]->output_qty):0);

                $rm_name = (!empty($cuttingBatch->item_name) ? $cuttingBatch->item_name : '');
                $mt_grade = (!empty($cuttingBatch->material_grade) ? $cuttingBatch->material_grade : '');
            }else{
                $uq = floatval($row->production_qty * $row->ppc_qty)/$row->output_qty;
                $rm_name=$row->item_name;
                $mt_grade = $row->material_grade;
            }
            $iq = floatval($row->issue_qty);
            $return = (!empty($row->return_qty) && $row->return_qty > 0)?floatval($row->return_qty):0;
            $sq = $iq - ($uq + $return);
            $returnParam = "{'postData':{'prc_id' : ".$row->prc_id.",'item_id' : ".$row->item_id."},'modal_id' : 'bs-right-lg-modal', 'call_function':'materialReturn', 'form_id' : 'materialReturn', 'title' : 'Return Material', 'js_store_fn' : 'storeSop', 'fnsave' : 'storeReturned','button':'close'}";
            
			$prcMaterial .= '<div class=" grid_item" style="width:100%;">
                                <div class="card sh-perfect">
                                    <div class="card-body">                                    
                                        <div class="task-box">
                                            <div class="float-end">
                                                <div class="dropdown d-inline-block">
                                                    <a class="dropdown-toggle" id="dLabel1" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                                        <i class="las la-ellipsis-v font-24 text-muted"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="javascript:void(0)" class="dropdown-item btn btn-danger permission-modify" datatip="Return Material" flow="down"  onclick="loadform('.$returnParam.')"><i class="icon icon-action-redo"></i> Return</a>
                                                     </div>
                                                </div>
                                            </div>
                                            <h5 class="mt-0 fs-15 cursor-pointer" >'.$rm_name.' <br><small>Material Grade : '.$mt_grade.'</small></h5>
                                            <div class="d-flex justify-content-between mb-0">  
                                                <!--<h6 class="fw-semibold mb-0">Supplier : <span class="text-muted font-weight-normal"> '.$row->supplier_name.'</span></h6>--> 
                                                <h6 class="fw-semibold mb-0">Batch No. : <span class="text-muted font-weight-normal"> '.(($prcData->cutting_flow == 2)?((!empty($prcData->batch_no))?'('.$prcData->batch_no.')  ':''):'').$row->batch_no.'</span></h6> 
                                            </div>
                                            
                                            <div class="d-flex justify-content-between">  
                                                <h6 class=fw-semibold">Consumption Per Piece: <span class="text-muted font-weight-normal"> '.floatval($row->ppc_qty).'</span></h6>    
                                                <h6 class="fw-semibold">UOM : <span class="text-muted font-weight-normal"> '.$row->uom.'</span></h6>    
                                            </div>
                                            <hr class="hr-dashed my-5px">
                                            <div class="media align-items-center btn-group process-tags">
                                                <span class="badge bg-light-peach btn flex-fill" datatip="Required Qty" flow="down">RQ : '.floatval($rq).'</span>
                                                <span class="badge bg-light-teal btn flex-fill" datatip="Issue Qty" flow="down">IQ : '.floatval($iq).'</span>
                                                <span class="badge bg-light-cream btn flex-fill" datatip="Used Qty" flow="down">UQ : '.floatval($uq).'</span>
                                                <span class="badge bg-light-cream btn flex-fill" datatip="Return Qty" flow="down">MR : '.floatval($return).'</span>
                                                <span class="badge bg-light-raspberry btn flex-fill" datatip="Stock Qty" flow="down">SQ : '.round($sq,3).'</span>
                                            </div>                                       
                                        </div>
                                    </div>
                                </div>
                            </div>';
        }
    }
    echo $prcMaterial;

?>