<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function getPurchaseDtHeader($page){

    /* Purchase Order Header */
    $data['purchaseOrders'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['purchaseOrders'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['purchaseOrders'][] = ["name"=>"PO. No."];
	$data['purchaseOrders'][] = ["name"=>"PO. Date"];
	$data['purchaseOrders'][] = ["name"=>"Party Name"];
	$data['purchaseOrders'][] = ["name"=>"Mill Name"];
	$data['purchaseOrders'][] = ["name"=>"Item Name"];
	$data['purchaseOrders'][] = ["name"=>"Finish Goods"];
	$data['purchaseOrders'][] = ["name"=>"Exp. Delivery Date"];
	$data['purchaseOrders'][] = ["name"=>"Price"];
    $data['purchaseOrders'][] = ["name"=>"Order Qty"];
	$data['purchaseOrders'][] = ["name"=>"Receive Qty"];
	$data['purchaseOrders'][] = ["name"=>"Pending Qty"];
    $data['purchaseOrders'][] = ["name"=>"Indent/Enq. No."];
    $data['purchaseOrders'][] = ["name"=>"Remark"];
    $data['purchaseOrders'][] = ["name"=>"Created By/At"];
    $data['purchaseOrders'][] = ["name"=>"Updated By/At"];
	
	 /* Purchase Request Header */
    $data['purchaseRequest'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['purchaseRequest'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['purchaseRequest'][] = ["name"=>"Indent No"];
    $data['purchaseRequest'][] = ["name"=>"Indent Date"];
    $data['purchaseRequest'][] = ["name"=>"Item Name"];
    $data['purchaseRequest'][] = ["name"=>"Finish Goods"];
    $data['purchaseRequest'][] = ["name"=>"Req. Qty"];    
    $data['purchaseRequest'][] = ["name"=>"Delivery Date"];
    $data['purchaseRequest'][] = ["name"=>"Remark"];
    $data['purchaseRequest'][] = ["name"=>"Status"];

     /* Purchase Indent Header */
    $masterCheckBox = '<input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkRequest" value=""><label for="masterSelect">ALL</label>';
    
    $data['purchaseIndent'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['purchaseIndent'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['purchaseIndent'][] = ["name"=>$masterCheckBox,"textAlign"=>"center","class"=>"text-center no_filter","orderable"=>"false"];
	$data['purchaseIndent'][] = ["name"=>"Indent No"];
	$data['purchaseIndent'][] = ["name"=>"Indent Date"];
    $data['purchaseIndent'][] = ["name"=>"Item Name"];
    $data['purchaseIndent'][] = ["name"=>"Finish Goods"];
    $data['purchaseIndent'][] = ["name"=>"Req. Qty"];    
    $data['purchaseIndent'][] = ["name"=>"Delivery Date"];
    $data['purchaseIndent'][] = ["name"=>"Remark"];
    $data['purchaseIndent'][] = ["name"=>"Status"];

    /* Purchase Desk Header */
    $data['purchaseDesk'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['purchaseDesk'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['purchaseDesk'][] = ["name"=>"Enquiry No."];
	$data['purchaseDesk'][] = ["name"=>"Enquiry Date"];
	$data['purchaseDesk'][] = ["name"=>"Supplier"];
	$data['purchaseDesk'][] = ["name"=>"Item Name"];
	$data['purchaseDesk'][] = ["name"=>"Finish Goods"];
	$data['purchaseDesk'][] = ["name"=>"Unit"];
    $data['purchaseDesk'][] = ["name"=>"Quantity"];
	$data['purchaseDesk'][] = ["name"=>"M.O.Q."];
    $data['purchaseDesk'][] = ["name"=>"Price"];
    $data['purchaseDesk'][] = ["name"=>"Lead Time (In Days)"];
    $data['purchaseDesk'][] = ["name"=>"Quot. No."];
    $data['purchaseDesk'][] = ["name"=>"Quot. Date"];
	$data['purchaseDesk'][] = ["name"=>"Delivery Date"];
    $data['purchaseDesk'][] = ["name"=>"Feasible"];
    $data['purchaseDesk'][] = ["name"=>"Remark"];
    $data['purchaseDesk'][] = ["name"=>"Quot. Remark"];

	/* Material Forecast Header */
    $masterCheckBox = '<input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkRequest" value=""><label for="masterSelect">ALL</label>';
	$data['materialForecast'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center","srnoPosition"=>0];
	$data['materialForecast'][] = ["name"=>$masterCheckBox,"class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['materialForecast'][] = ["name"=>"Raw Material","textAlign"=>"center"];
    $data['materialForecast'][] = ["name"=>"SO Number","textAlign"=>"center"];
	$data['materialForecast'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['materialForecast'][] = ["name"=>"SO Qty.","textAlign"=>"center"];
    $data['materialForecast'][] = ["name"=>"Dispatch Qty.","textAlign"=>"center"];
    $data['materialForecast'][] = ["name"=>"WIP Qty.","textAlign"=>"center"];
    $data['materialForecast'][] = ["name"=>"Pending Order Qty.","textAlign"=>"center"]; 
    $data['materialForecast'][] = ["name"=>"Required Material"];
    $data['materialForecast'][] = ["name"=>"Stock Qty"];
    // $data['materialForecast'][] = ["name"=>"Pending Request"];
    $data['materialForecast'][] = ["name"=>"Pending PO"];
    $data['materialForecast'][] = ["name"=>"Pending GRN QC"];
    $data['materialForecast'][] = ["name"=>"Shortage Qty"];

    /* NCR Header */
    $data['supplierNcr'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['supplierNcr'][] = ["name"=>"#","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['supplierNcr'][] = ["name"=>"NCR No."];
    $data['supplierNcr'][] = ["name"=>"NCR Date"];
    $data['supplierNcr'][] = ["name"=>"Customer Name"];
    $data['supplierNcr'][] = ["name"=>"Item Description"];
    $data['supplierNcr'][] = ["name"=>"Batch No."];
    $data['supplierNcr'][] = ["name"=>"Lot Qty."];
    $data['supplierNcr'][] = ["name"=>"Rej Qty."];
    $data['supplierNcr'][] = ["name"=>"Ref. of Complaint"];
    $data['supplierNcr'][] = ["name"=>"Details of Complaint"];
    $data['supplierNcr'][] = ["name"=>"Product Returned"];
    $data['supplierNcr'][] = ["name"=>"Report No."];
    $data['supplierNcr'][] = ["name"=>"8D Report"];
    $data['supplierNcr'][] = ["name"=>"Reference of feed back to Customer"];
    $data['supplierNcr'][] = ["name"=>"Remarks"];

    return tableHeader($data[$page]);
}

function getPurchaseOrderData($data){
    $shortClose =""; $editButton="";  $deleteButton =""; $printBtn = $approveBtn = $rejectBtn = $reOpenBtn="";
   
    if(in_array($data->trans_status,[0,3])):
        if(empty($data->is_approve)):
            $approveParam = "{'postData':{'id' : ".$data->id.", 'trans_status' : 3,'is_approve':1,'msg':'Approved'},'fnsave':'approvePurchaseOrder','message':'Are you sure want to Approve this Purchase Order?'}";
            $approveBtn = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Approve PO" flow="down" onclick="confirmStore('.$approveParam.');"><i class="mdi mdi-check"></i></a>';    
    
            $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('purchaseOrders/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

            $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Purchase Order'}";
            $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
        else:
            $rejectParam = "{'postData':{'id' : ".$data->id.", 'trans_status' : 0,'is_approve':0,'msg':'Reject'},'fnsave':'approvePurchaseOrder','message':'Are you sure want to Reject this Purchase Order?'}";
            $rejectBtn = '<a class="btn btn-dark permission-modify" href="javascript:void(0)" datatip="Un-Approve" flow="down" onclick="confirmStore('.$rejectParam.');"><i class="mdi mdi-close"></i></a>';    

            $shortCloseParam = "{'postData':{'id' : ".$data->po_trans_id.", 'trans_status' : 2},'fnsave':'changeOrderStatus','message':'Are you sure want to Short Close this Purchase Order?'}";
            $shortClose = '<a class="btn btn-instagram permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" onclick="confirmStore('.$shortCloseParam.');"><i class="mdi mdi-close-circle-outline"></i></a>';   
        endif;
    endif;
    if($data->trans_status == 2):
        $reOpenParam = "{'postData':{'id' : ".$data->po_trans_id.", 'trans_status' : 3},'fnsave':'changeOrderStatus','message':'Are you sure want to Re-Open this Purchase Order?'}";
        $reOpenBtn = '<a class="btn btn-instagram permission-modify" href="javascript:void(0)" datatip="Re-Open" flow="down" onclick="confirmStore('.$reOpenParam.');"><i class="mdi mdi-replay"></i></a>';
    endif;
   
    $printBtn = '<a href="javascript:void(0)" class="btn btn-primary printOrderDialog" datatip="Print" flow="down" data-id="'.$data->id.'" data-trans_id="'.$data->po_trans_id.'" data-fn_name="printPO"><i class="fas fa-print"></i></a>';

    $action = getActionButton($approveBtn.$rejectBtn.$shortClose.$reOpenBtn.$printBtn.$editButton.$deleteButton);
	
	$data->item_name = (!empty($data->item_code)) ? '['.$data->item_code.'] '.$data->item_name : $data->item_name;
    $data->fg_item_name = (!empty($data->fg_item_code)) ? '['.$data->fg_item_code.'] '.$data->fg_item_name : $data->fg_item_name;
	
	$createdBy = $data->created_name.(!empty($data->created_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($data->created_at)) : '');
    $updatedBy = $data->updated_name.(!empty($data->updated_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($data->updated_at)) : '');
	
	return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->mill_name,$data->item_name,$data->fg_item_name,formatDate($data->delivery_date),round($data->price,2),round($data->qty,2),round($data->dispatch_qty,2),round(($data->qty - $data->dispatch_qty),2),$data->enq_number,$data->item_remark,$createdBy,$updatedBy];
}

/* Purchase Request Data  */
function getPurchaseRequestData($data){ 
    $shortClose =""; $editButton="";  $deleteButton ="";
    if($data->order_status == 1):
        $shortCloseParam = "{'postData':{'id' : ".$data->id.", 'order_status' : 3},'fnsave':'changeReqStatus','message':'Are you sure want to Short Close this Purchase Request?'}";
        $shortClose = '<a class="btn btn-instagram permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" onclick="confirmStore('.$shortCloseParam.');"><i class="mdi mdi-close-circle-outline"></i></a>';    

        $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-md-modal', 'form_id' : 'editPurchaeRequest', 'title' : 'Update PurchaeRequest','call_function':'edit'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    
        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Purchase Request'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    endif;
  
	$action = getActionButton($shortClose.$editButton.$deleteButton);
	$data->item_name = (!empty($data->item_code)) ? '['.$data->item_code.'] '.$data->item_name : $data->item_name;
    $data->fg_item_name = (!empty($data->fg_item_code)) ? '['.$data->fg_item_code.'] '.$data->fg_item_name : $data->fg_item_name;
	return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->item_name,$data->fg_item_name,$data->qty.' ('.$data->uom.')',formatDate($data->delivery_date),$data->remark,$data->order_status_label];
}

/* Purchase Indent Data  */
function getPurchaseIndentData($data){
    $shortClose=""; $selectBox ="";
    if($data->order_status == 1):
		$selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkRequest" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';   

        $shortCloseParam = "{'postData':{'id' : ".$data->id.", 'order_status' : 3},'fnsave':'changeReqStatus','message':'Are you sure want to Short Close this Purchase Request?'}";
        $shortClose = '<a class="btn btn-instagram permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" onclick="confirmStore('.$shortCloseParam.');"><i class="mdi mdi-close-circle-outline"></i></a>'; 
	endif;

    $action = getActionButton($shortClose);
	$data->item_name = (!empty($data->item_code)) ? '['.$data->item_code.'] '.$data->item_name : $data->item_name;
    $data->fg_item_name = (!empty($data->fg_item_code)) ? '['.$data->fg_item_code.'] '.$data->fg_item_name : $data->fg_item_name;
    return [$action,$data->sr_no,$selectBox,$data->trans_number,formatDate($data->trans_date),$data->item_name,$data->fg_item_name,$data->qty.' ('.$data->uom.')',formatDate($data->delivery_date),$data->remark,$data->order_status_label];
}

/* Purchase Desk Table Data */
function getPurchaseDeskData($data){
	$editButton = $deleteButton = $approveBtn = $rejectBtn = $quoteBtn = $orderButton = $regButton = "";
	$postData = ['trans_number'=>$data->trans_number, 'party_id'=>$data->party_id, 'is_regenerate'=>0];

	if($data->trans_status == 1):
		$editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('purchaseDesk/editEnquiry/'.encodeurl($postData)).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

		$deleteParam = "{'postData':{'id' : ".$data->id.",'trans_number' : '".$data->trans_number."'},'message' : 'Enquiry','fndelete':'deleteEnquiry'}";
		$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
		
		$quoteParam = "{'postData':{'id' : '".$data->id."', 'trans_number' : '".$data->trans_number."', 'party_id' : ".$data->party_id.",'party_name':'".$data->party_name."','trans_date' : '".$data->trans_date."'}, 'modal_id' : 'modal-lg', 'form_id' : 'quoteConfirm', 'title' : 'Convert Quotation','call_function':'quoteConfirm','fnsave' : 'saveQuotation'}";
		$quoteBtn = '<a class="btn btn-primary btn-edit permission-modify" href="javascript:void(0)" datatip="Convert Quotation" flow="down" onclick="quoteConfirm('.$quoteParam.');"><i class="fa fa-file"></i></a>';
	elseif(($data->trans_status == 2 || $data->trans_status == 5) && $data->pending_qty > 0):        
        $ordParam = "{'postData':{'party_id' : ".$data->party_id."},'modal_id' : 'modal-lg', 'form_id' : 'getEnquiryList', 'title' : 'Create Purchase Order', 'call_function' : 'getEnquiryList', 'controller' : 'purchaseOrders', 'savebtn_text' : 'Create Order', 'js_store_fn' : 'createOrder'}";
        $orderButton = ' <a class="btn btn-dark btn-edit permission-modify" href="javascript:void(0)" datatip="Create Order" flow="down" onclick="modalAction('.$ordParam.')"><i class="fas fa-file"></i></a>';
	
		$rejectParam = "{'postData':{'id' : ".$data->id.",'enq_id': ".$data->enq_id.",'val' : 3,'msg':'Rejected'},'fnsave':'chageEnqStatus','message':'Are you sure want to Reject this Quotation?'}";
		$rejectBtn = '<a class="btn btn-dark permission-modify" href="javascript:void(0)" datatip="Reject" flow="down" onclick="confirmStore('.$rejectParam.');"><i class="mdi mdi-close"></i></a>';
	
	elseif($data->trans_status == 4 && $data->feasible == 1):
		$approveParam = "{'postData':{'id' : ".$data->quote_id.",'enq_id': ".$data->enq_id.",'val' : 2,'msg':'Approved'},'fnsave':'chageEnqStatus','message':'Are you sure want to Approve this Quotation?'}";
		$approveBtn = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="confirmStore('.$approveParam.');"><i class="mdi mdi-check"></i></a>';

		$rejectParam = "{'postData':{'id' : ".$data->id.",'enq_id': ".$data->enq_id.",'val' : 3,'msg':'Rejected'},'fnsave':'chageEnqStatus','message':'Are you sure want to Reject this Quotation?'}";
		$rejectBtn = '<a class="btn btn-dark permission-modify" href="javascript:void(0)" datatip="Reject" flow="down" onclick="confirmStore('.$rejectParam.');"><i class="mdi mdi-close"></i></a>';
	endif;

	if($data->feasible == 2):
		$postData = ['trans_number'=>$data->trans_number, 'party_id'=>$data->party_id, 'is_regenerate'=>1];
		$regButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('purchaseDesk/editEnquiry/'.encodeurl($postData)).'" datatip="Regenerate" flow="down" ><i class="fas fa-sync-alt"></i></a>';
	endif;

    $printBtn = '<a class="btn btn-dribbble" href="'.base_url('purchaseDesk/printEnquiry/'.encodeurl($postData)).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print"></i></a>';

	$action = getActionButton($printBtn.$approveBtn.$rejectBtn.$quoteBtn.$orderButton.$regButton.$editButton.$deleteButton);
	
	return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,(!empty($data->item_code) ? '['.$data->item_code.'] ' : '').$data->item_name,(!empty($data->fg_item_code) ? '['.$data->fg_item_code.'] ' : '').$data->fg_item_name,$data->uom,floatval($data->qty),(!empty($data->moq) ? floatval($data->moq) : ''),(!empty($data->price) ? floatval($data->price) : ''),$data->lead_time,$data->quote_no,formatDate($data->quote_date),formatDate($data->delivery_date),((!empty($data->feasible) && $data->feasible == 1) ? 'Yes' : ((!empty($data->feasible) && $data->feasible == 2) ? '<span class="text-danger">No</span>' : '')),$data->item_remark,$data->quote_remark];
}

/** Material Forecast */
function getForecastData($data){    
    $rm_shortage = $data->required_material - ($data->rm_stock + $data->pending_po + $data->pending_grn);
    $pending_so = $data->so_qty - ($data->dispatch_qty + $data->wip_qty);
	
	$selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkRequest" value="'.$data->so_trans_id.'"  ><label for="ref_id_'.$data->sr_no.'"></label>';
	   
	return [$data->sr_no,$selectBox,$data->item_code.' '.$data->item_name,$data->so_number,$data->fg_item_name,floatval($data->so_qty),floatval($data->dispatch_qty),floatval($data->wip_qty),floatval($pending_so),floatval($data->required_material),floatval($data->rm_stock),floatval($data->pending_po),floatval($data->pending_grn),(($rm_shortage > 0)?round($rm_shortage,3):0)];
}

/* NCR Table Data*/
function getNCRData($data){
    $editButton="";$deleteButton="";$solution="";

	$editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'ncr', 'title' : 'Update Customer Complaints','call_function':'edit'}";
	$editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

	$deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Delete Customer Complaints'}";
	$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
    if(empty($data->status)){
    
        $solParam = "{'postData' :{'id' : ".$data->id."}, 'modal_id' : 'modal-md', 'form_id' : 'solutionForm', 'title' : 'Solution', 'call_function' : 'complaintSolution', 'fnsave' : 'saveSolution'}";
        $solution = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Solution" flow="down" onclick="modalAction('.$solParam.');"><i class="fas fa-check" ></i></a>';
    }
	
	
    $download_8d = (!empty($data->report_8d) ? '<a href="'.base_url("assets/uploads/8d_report/".$data->report_8d).'" target="_blank"><i class="fa fa-download"></i></a>' : "");
	
    $action = getActionButton($solution.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->item_name,$data->batch_no,$data->qty,$data->rej_qty,$data->ref_of_complaint,$data->complaint,$data->product_returned,$data->report_no,$download_8d,$data->ref_feedback,$data->remark];
}

?>