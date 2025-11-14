<?php
    $enqDetail = '<div class="cd-header"><h6 class="m-0 enq_number">ENQUIRY DETAIL</h6></div>
                    <div class="sop-body vh-35" data-simplebar>
                        <div>
                            <div class="text-center">
                                <img src="'.base_url('assets/images/background/dnf_2.png').'" style="width:50%;">
                                <div class="text-center text-muted font-16 fw-bold">Please click any <strong>Enquiry</strong> to see Data</div>
                            </div>
                        </div>
                    </div>';
    if(!empty($enqData))
    {
        $btn = '';
        if($enqData->trans_status == 1 && empty($enqData->enq_id)){
            $editParam = "{'postData':{'id' : ".$enqData->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editEnquiry', 'title' : 'Update Purchase Enquiry', 'call_function' : 'editEnquiry', 'fnsave' : 'saveEnquiry', 'js_store_fn' : 'storeEnquiry', 'txt_editor' : 'item_remark'}";
			$btn .= ' <a class="dropdown-item text-success" href="javascript:void(0)" datatip="Edit" flow="down" onclick="loadform('.$editParam.')"><i class="mdi mdi-square-edit-outline"></i> Edit</a>';

			$deleteParam = "{'postData':{'id' : ".$enqData->id."},'message' : 'Purchase Enquiry','res_function':'loadDesk','fndelete':'deleteEnquiry'}";
			$btn .= ' <a class="dropdown-item text-danger" href="javascript:void(0)" datatip="Delete" flow="down" onclick="trashEnquiry('.$deleteParam.')"><i class="mdi mdi-trash-can-outline"></i> Delete</a>';   
        }
        elseif($enqData->trans_status == 2 && empty($enqData->order_number)){
			$btn .= ' <a href="'.base_url('purchaseOrders/createOrder/'.$enqData->id).'" class="dropdown-item text-primary" href="javascript:void(0)" datatip="Create Order" flow="down"><i class="fas fa-file"></i> Create Order</a>';
        }

        $enqDetail = '<div class="cd-header" style="padding: 7px 16px;">
                            <h6 class="m-0 enq_number">#'.$enqData->trans_number.'</h6>
                            <p class="mb-0 text-muted fs-12 "><i class="far fa-fw fa-clock"></i> <span class="enq_date">'.formatDate($enqData->trans_date,"d M Y").'</span></p>

                            <div class="cd-features">';
                                if($enqData->trans_status != 3):
                                $enqDetail .= '<div class="dropdown d-inline-block">
    								<a class="dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0)" role="button">
    									<i class="las la-ellipsis-v font-22 text-muted"></i>
    								</a>
    								<div class="dropdown-menu dropdown-menu-end">'.$btn.'</div>
    							</div>';
                                endif;
                            $enqDetail .= '</div>
                            
                        </div>
                        <div class="enq-body vh-35" data-simplebar>
    					    <div class="enqDetail1 ml-20">
                                <div class="mt-2" style="border-bottom: 1px dashed #e8ebf3;">
                                    <p class="m-0 font-15">Supplier</p>
                                    <p class="text-muted fw-semibold1 mb-0">'.$enqData->party_name.'</p>
                                </div>
    					        <div class="mt-2" style="border-bottom: 1px dashed #e8ebf3;">
                                    <p class="m-0 font-15">Product Type</p>
                                    <p class="text-muted fw-semibold1 mb-0">'.$enqData->category_name.'</p>
                                </div>
                                <div class="mt-2" style="border-bottom: 1px dashed #e8ebf3;">
                                    <p class="m-0 font-15">Product</p>
                                    <p class="text-muted fw-semibold1 mb-0">'.$enqData->item_name.'</p>
                                </div>
                                <div class="mt-2" style="border-bottom: 1px dashed #e8ebf3;">
                                    <p class="m-0 font-15">Qty<br><span class="text-muted">'.floatval($enqData->qty).' '.$enqData->uom.'</span></p>
                                </div>
                                <p class="mt-1">'.$enqData->item_remark.'</p>
    					    </div>
    					</div>';
    }
    echo $enqDetail;

?>