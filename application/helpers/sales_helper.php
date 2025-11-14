<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function getSalesDtHeader($page){

    /* Sales Enquiry Header */
    $data['salesEnquiry'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['salesEnquiry'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['salesEnquiry'][] = ["name"=>"SE. No."];
    $data['salesEnquiry'][] = ["name"=>"SE. Date"];
    $data['salesEnquiry'][] = ["name"=>"Customer Name"];
    $data['salesEnquiry'][] = ["name"=>"Item Name"];
    $data['salesEnquiry'][] = ["name"=>"Qty"];
    $data['salesEnquiry'][] = ["name"=>"Feasible Status"];

    /* Sales Quotation Header */
    $data['salesQuotation'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['salesQuotation'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['salesQuotation'][] = ["name"=>"Rev. No.","textAlign"=>"center"];
	$data['salesQuotation'][] = ["name"=>"SQ. No."];
	$data['salesQuotation'][] = ["name"=>"SQ. Date"];
	$data['salesQuotation'][] = ["name"=>"Customer Name"];
	$data['salesQuotation'][] = ["name"=>"Item Name"];
    $data['salesQuotation'][] = ["name"=>"Qty"];
    $data['salesQuotation'][] = ["name"=>"Price"];
    $data['salesQuotation'][] = ["name"=>"Approved By"];
    $data['salesQuotation'][] = ["name"=>"Approved Date"];

    /* Sales Order Header */
    $data['salesOrders'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['salesOrders'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['salesOrders'][] = ["name"=>"Rev. No.","textAlign"=>"center"];
	$data['salesOrders'][] = ["name"=>"SO. No."];
	$data['salesOrders'][] = ["name"=>"SO. Date"];
	$data['salesOrders'][] = ["name"=>"Cust. PO. No."];
	$data['salesOrders'][] = ["name"=>"Dispatch Date"];
	$data['salesOrders'][] = ["name"=>"Customer Name"];
	$data['salesOrders'][] = ["name"=>"Item Name"];
    $data['salesOrders'][] = ["name"=>"Price"];
	//$data['salesOrders'][] = ["name"=>"Drg. Number"];
	$data['salesOrders'][] = ["name"=>"Stock Qty"];
    $data['salesOrders'][] = ["name"=>"Order Qty"];
    $data['salesOrders'][] = ["name"=>"WIP Qty"];
	$data['salesOrders'][] = ["name"=>"Item WIP"];
    $data['salesOrders'][] = ["name"=>"Dispatch Qty"];
    $data['salesOrders'][] = ["name"=>"Pending Qty"];
    $data['salesOrders'][] = ["name"=>"Due Days"];
    $data['salesOrders'][] = ["name"=>"Created By/At"];
    $data['salesOrders'][] = ["name"=>"Updated By/At"];

    /* Party Order Header */
    $data['partyOrders'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['partyOrders'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['partyOrders'][] = ["name"=>"Order Status"];
	$data['partyOrders'][] = ["name"=>"SO. No."];
	$data['partyOrders'][] = ["name"=>"SO. Date"];
	$data['partyOrders'][] = ["name"=>"Item Name"];
	$data['partyOrders'][] = ["name"=>"Brand Name"];
    $data['partyOrders'][] = ["name"=>"Order Qty"];
    $data['partyOrders'][] = ["name"=>"Received Qty"];
    $data['partyOrders'][] = ["name"=>"Pending Qty"];
 
    /* Delivery Challan Header */
    $data['deliveryChallan'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['deliveryChallan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['deliveryChallan'][] = ["name"=>"DC. No."];
	$data['deliveryChallan'][] = ["name"=>"DC. Date"];
	$data['deliveryChallan'][] = ["name"=>"Customer Name"];
	$data['deliveryChallan'][] = ["name"=>"Remark"];

    /* Estimate [Cash] Header */
    $data['estimate'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['estimate'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['estimate'][] = ["name"=>"Inv No."];
	$data['estimate'][] = ["name"=>"Inv Date"];
	$data['estimate'][] = ["name"=>"Customer Name"];
	$data['estimate'][] = ["name"=>"Taxable Amount"];
    $data['estimate'][] = ["name"=>"Net Amount"];

    /* Estimate Payments [Cash] Header */
    $data['estimatePayment'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['estimatePayment'][] = ["name"=>"#","class"=>"text-center no_filter noExport","sortable"=>FALSE]; 
	$data['estimatePayment'][] = ["name"=>"Vou. Date"];
	$data['estimatePayment'][] = ["name"=>"Customer Name"];
	$data['estimatePayment'][] = ["name"=>"Received By"];
	$data['estimatePayment'][] = ["name"=>"Reference No."];
    $data['estimatePayment'][] = ["name"=>"Amount"];
    $data['estimatePayment'][] = ["name"=>"Remark"];
    
    /* Customer Complaints Header */
	$data['customerComplaints'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['customerComplaints'][] = ["name"=>"#","class"=>"text-center no_filter noExport","sortable"=>FALSE]; 
    $data['customerComplaints'][] = ["name"=>"Complaint No."];
    $data['customerComplaints'][] = ["name"=>"Receipt Date"];
    $data['customerComplaints'][] = ["name"=>"Customer Name"];
    $data['customerComplaints'][] = ["name"=>"Ref. of Complaint"];
    $data['customerComplaints'][] = ["name"=>"Item Description"];
    $data['customerComplaints'][] = ["name"=>"Details of Complaint"];
    $data['customerComplaints'][] = ["name"=>"Defect Photos"];
    $data['customerComplaints'][] = ["name"=>"Product Returned"];
    $data['customerComplaints'][] = ["name"=>"8D Report"];
    $data['customerComplaints'][] = ["name"=>"Details of  Action Taken"];
    $data['customerComplaints'][] = ["name"=>"Reference of feed back to Customer"];
    $data['customerComplaints'][] = ["name"=>"Remarks"];
	
    /* Pending Dispatch Plan Header */
    $data['pendingPlan'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['pendingPlan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['pendingPlan'][] = ["name"=>"SO. No."];
    $data['pendingPlan'][] = ["name"=>"SO. Date"];
    $data['pendingPlan'][] = ["name"=>"Party"];
    $data['pendingPlan'][] = ["name"=>"Product"];
    $data['pendingPlan'][] = ["name"=>"Order Qty"];
    $data['pendingPlan'][] = ["name"=>"Plan Qty"];
    $data['pendingPlan'][] = ["name"=>"Dispatch Qty"];
    $data['pendingPlan'][] = ["name"=>"Pending Qty"];

    /* Dispatch Plan Header */
    $data['dispatchPlan'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['dispatchPlan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['dispatchPlan'][] = ["name"=>"Plan No."];
    $data['dispatchPlan'][] = ["name"=>"Plan Date"];
    $data['dispatchPlan'][] = ["name"=>"Party"];
    $data['dispatchPlan'][] = ["name"=>"SO. No."];
    $data['dispatchPlan'][] = ["name"=>"SO. Date"];
    $data['dispatchPlan'][] = ["name"=>"Product"];
    $data['dispatchPlan'][] = ["name"=>"Qty"];

    /* Proforma Invoice Header */
    $data['proformaInvoice'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['proformaInvoice'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['proformaInvoice'][] = ["name"=>"PINV. No."];
	$data['proformaInvoice'][] = ["name"=>"PINV. Date"];
	$data['proformaInvoice'][] = ["name"=>"Customer Name"];
    $data['proformaInvoice'][] = ["name"=>"Taxable Amount"];
    $data['proformaInvoice'][] = ["name"=>"GST Amount"];
    $data['proformaInvoice'][] = ["name"=>"Net Amount"];

    /* packing Header */
    $data['packing'][] = ["name"=>"Action"];
    $data['packing'][] = ["name"=>"#","textAlign"=>"center"];
    $data['packing'][] = ["name"=>"Packing No."];
    $data['packing'][] = ["name"=>"Packing Date"];
    $data['packing'][] = ["name"=>"Product Name"];
    $data['packing'][] = ["name"=>"Box Capacity"];
    $data['packing'][] = ["name"=>"Total Box"];
    $data['packing'][] = ["name"=>"Total Qty."];
    $data['packing'][] = ["name"=>"Pending Dispatch"];
    $data['packing'][] = ["name"=>"Remark"];
	
	/* Packing Stock Header */
	$data['packing_stock'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['packing_stock'][] = ["name"=>"Item Description"];
	$data['packing_stock'][] = ["name"=>"PRC No"];
	$data['packing_stock'][] = ["name"=>"Batch No"];
    $data['packing_stock'][] = ["name"=>"Balance Qty."];

    /** NPD Enquiry */
    $data['npdSalesEnquiry'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['npdSalesEnquiry'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['npdSalesEnquiry'][] = ["name"=>"SE. No."];
    $data['npdSalesEnquiry'][] = ["name"=>"SE. Date"];
    $data['npdSalesEnquiry'][] = ["name"=>"Customer Name"];
    $data['npdSalesEnquiry'][] = ["name"=>"Item Name"];
    $data['npdSalesEnquiry'][] = ["name"=>"Qty"];

    /* Primary Packing Header */
    $data['primaryPacking'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['primaryPacking'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['primaryPacking'][] = ["name"=>"Packing No."];
    $data['primaryPacking'][] = ["name"=>"Packing Date"];
    $data['primaryPacking'][] = ["name"=>"Product Name"];
    $data['primaryPacking'][] = ["name"=>"Box Capacity"];
    $data['primaryPacking'][] = ["name"=>"Total Box"];
    $data['primaryPacking'][] = ["name"=>"Total Qty."];

    /* Final Packing Header */
    $data['finalPacking'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['finalPacking'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['finalPacking'][] = ["name"=>"Packing No."];
    $data['finalPacking'][] = ["name"=>"Packing Date"];
    $data['finalPacking'][] = ["name"=>"Party Name"];
    $data['finalPacking'][] = ["name"=>"S.O. No"];
    $data['finalPacking'][] = ["name"=>"Product Name"];
    $data['finalPacking'][] = ["name"=>"Total Box"];
    $data['finalPacking'][] = ["name"=>"Total Qty."];
    $data['finalPacking'][] = ["name"=>"Inv. No."];
	
    return tableHeader($data[$page]);
}

/* Sales Enquiry Table data */
function getSalesEnquiryData($data){
    $quotationBtn=""; $editButton=""; $deleteButton=""; $feasReqBtn = "";
    if(empty($data->trans_status) || ($data->trans_status == 3 && $data->feasible_status == 1)):
        if($data->trans_status == 0):
            $editButton = '<a class="btn btn-success permission-modify" href="'.base_url('salesEnquiry/edit/'.$data->trans_main_id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

            $deleteParam = "{'postData':{'id' : ".$data->trans_main_id."},'message' : 'Sales Enquiry'}";
            $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

            $feasReqParam = "{'postData':{'id' : ".$data->id."},'fnsave':'saveFeasibleRequest','message':'Are you sure you want to sent request for feasibility ?'}";
            $feasReqBtn = '<a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Feasibility Request" flow="down" onclick="confirmStore('.$feasReqParam.');"><i class="fas fa-paper-plane"></i></a>';
        endif;
        $quotationBtn = '<a href="'.base_url('salesQuotation/createQuotation/'.$data->trans_main_id).'" class="btn btn-primary permission-write" datatip="Create Quotation" flow="down"><i class="fa fa-file-alt"></i></a>';  
    endif;

    $action = getActionButton($feasReqBtn.$quotationBtn.$editButton.$deleteButton);
    $feasible_status = "";
    if($data->trans_status == 3 && $data->feasible_status==0){
        $feasible_status = "<span class='badge bg-primary fs-13'>Requested For Feasibility</span>";
    }elseif($data->feasible_status==1){
        $feasible_status = '<span class="badge bg-info fs-13">Feasibled</span>';
    }elseif($data->feasible_status==2){
        $feasible_status = '<span class="badge bg-warning fs-13">Regretted</span>';
    }

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->item_name,round($data->qty,2),$feasible_status];
}

/* Sales Quotation Table data */
function getSalesQuotationData($data){
    $editButton = $deleteButton = $approveBtn = $rejectBtn = $pinvBtn = $revision = $orderBtn = "";
    
    if(empty($data->trans_status)){
        if(empty($data->internal_approve)){
            $approveParam = "{'postData':{'id' : ".$data->trans_main_id.", 'internal_approve' : 1, 'msg' : 'Approved'},'fnsave':'approveQuotation','message':'Are you sure want to Approve this Sales Quotation?'}";
            $approveBtn = '<a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="confirmStore('.$approveParam.');"><i class="mdi mdi-check"></i></a>';
            
            $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('salesQuotation/edit/'.$data->trans_main_id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

            $deleteParam = "{'postData':{'id' : ".$data->trans_main_id."},'message' : 'Sales Quotation'}";
            $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

            $revision = '<a href="'.base_url('salesQuotation/reviseQuotation/'.$data->trans_main_id).'" class="btn btn-primary btn-edit permission-modify" datatip="Revision" flow="down"><i class="fa fa-retweet"></i></a>';

        }else{
            if(empty($data->is_approve)){
                $rejectParam = "{'postData':{'id' : ".$data->trans_main_id.", 'internal_approve' : 0, 'msg' : 'Rejected'},'fnsave':'approveQuotation','message':'Are you sure want to Reject this Sales Quotation?'}";
                $rejectBtn = '<a class="btn btn-dark permission-modify" href="javascript:void(0)" datatip="Reject" flow="down" onclick="confirmStore('.$rejectParam.');"><i class="mdi mdi-close"></i></a>'; 

                $approveParam = "{'postData':{'id' : ".$data->trans_main_id.",'is_approve':1},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'approveSq', 'title' : 'Customer Approval','call_function':'confirmQuotation','fnsave':'saveConfirmQuotation'}";
                $approveBtn = '<a class="btn btn-success permission-modify" href="javascript:void(0)" datatip="Customer Approval" flow="down" onclick="modalAction('.$approveParam.');"><i class="mdi mdi-check-all"></i></a>';
                
                $revision = '<a href="'.base_url('salesQuotation/reviseQuotation/'.$data->trans_main_id).'" class="btn btn-primary btn-edit permission-modify" datatip="Revision" flow="down"><i class="fa fa-retweet"></i></a>';
            }else{
                $rejectParam = "{'postData':{'id' : ".$data->trans_main_id.", 'internal_approve' : 0, 'msg' : 'Rejected'},'fnsave':'approveQuotation','message':'Are you sure want to Reject this Sales Quotation?'}";
                $rejectBtn = '<a class="btn btn-dark permission-modify" href="javascript:void(0)" datatip="Reject" flow="down" onclick="confirmStore('.$rejectParam.');"><i class="mdi mdi-close"></i></a>';

                // if(empty($data->is_pinv)){
                //     $pinvBtn = '<a href="'.base_url('proformaInvoice/createInvoice/'.$data->trans_main_id).'" class="btn btn-warning permission-modify" datatip="Create Proforma Invoice" flow="down"><i class="fas fa-plus"></i></a>'; 
                // }
            }
        }
    }
    if(!empty($data->is_approve) && $data->trans_status != 1){
        $rejectParam = "{'postData':{'id' : ".$data->id.",'trans_status':2},'fnsave':'saveConfirmQuotation','message':'Are you sure want to Re-Open this Sales Quotation?'}";
		$rejectBtn = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Re-Open SQ" flow="down" onclick="confirmStore('.$rejectParam.');"><i class="mdi mdi-close"></i></a>';
    }
 
    $printBtn = '<a class="btn btn-dribbble btn-edit permission-approve" href="'.base_url('salesQuotation/printQuotation/'.$data->trans_main_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $printRevisionBtn = '<a class="btn btn-facebook btn-edit permission-approve createSalesQuotation" datatip="View Revised Quatation" data-id="'.$data->trans_main_id.'" data-sq_no="'.$data->trans_number.'" flow="down"><i class="fas fa-eye" ></i></a>';

    $action = getActionButton($printBtn.$orderBtn.$pinvBtn.$approveBtn.$rejectBtn.$revision.$editButton.$deleteButton);

    $rev_no = sprintf("%02d",$data->quote_rev_no);
    
    if($data->quote_rev_no != 0):
        $revParam = "{'postData' : {'trans_number' : '".$data->trans_number."'}, 'modal_id' : 'modal-md', 'form_id' : 'revisionList', 'title' : 'Quotation Revision History','call_function':'revisionHistory','button':'close'}";
        $rev_no = '<a href="javascript:void(0)" datatip="Revision History" flow="down" onclick="modalAction('.$revParam.');">'.sprintf("%02d",$data->quote_rev_no).'</a>';
    endif;

    return [$action,$data->sr_no,$rev_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->item_name,round($data->qty,2),round($data->price,2),$data->approve_by_name,((!empty($data->approve_date))?formatDate($data->approve_date):"")];
}

/* Sales Order Table data */
function getSalesOrderData($data){
    $editButton = $deleteButton = $approveBtn = $rejectBtn = $shortClose = $reOpenBtn = $printBtn = $pinvBtn = $revision = "";

    if(in_array($data->trans_status,[0,3])):
        if(empty($data->is_approve) && empty($data->trans_status)):
            $approveParam = "{'postData':{'id' : ".$data->id.",'is_approve':1, 'trans_status' : ".$data->trans_status.",'msg':'Approved'},'fnsave':'approveSalesOrder','message':'Are you sure want to Approve this Sales Order?'}";
            $approveBtn = '<a class="btn btn-success permission-modify" href="javascript:void(0)" datatip="Approve SO" flow="down" onclick="confirmStore('.$approveParam.');"><i class="mdi mdi-check"></i></a>';    

            $editButton = '<a class="btn btn-warning btn-edit permission-modify" href="'.base_url('salesOrders/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

            $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Sales Order'}";
            $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
        else:
            $rejectParam = "{'postData':{'id' : ".$data->id.", 'trans_status' : ".$data->trans_status.",'is_approve':0,'msg':'Reject'},'fnsave':'approveSalesOrder','message':'Are you sure want to Reject this Sales Order?'}";
            $rejectBtn = '<a class="btn btn-dark permission-modify" href="javascript:void(0)" datatip="Un-Approve" flow="down" onclick="confirmStore('.$rejectParam.');"><i class="mdi mdi-close"></i></a>'; 
            $shortCloseParam = "{'postData':{'id' : ".$data->trans_child_id.", 'trans_status' : 2},'fnsave':'changeOrderStatus','message':'Are you sure want to Short Close this Sales Order?'}";
            $shortClose = '<a class="btn btn-instagram permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" onclick="confirmStore('.$shortCloseParam.');"><i class="mdi mdi-close-circle-outline"></i></a>';    
			
			$pinvBtn = '<a href="'.base_url('proformaInvoice/createPINV/'.$data->id).'" class="btn btn-primary permission-write" datatip="Create Pinv" flow="down"><i class="fa fa-file-alt"></i></a>';  
		endif; 

        $revision = '<a href="'.base_url('salesOrders/reviseOrder/'.$data->id).'" class="btn btn-primary btn-edit permission-modify" datatip="Revision" flow="down"><i class="fa fa-retweet"></i></a>'; 
    endif;

    if($data->trans_status == 2):
        $reOpenParam = "{'postData':{'id' : ".$data->trans_child_id.", 'trans_status' : 3},'fnsave':'changeOrderStatus','message':'Are you sure want to Re-Open this Sales Order?'}";
        $reOpenBtn = '<a class="btn btn-instagram permission-modify" href="javascript:void(0)" datatip="Re-Open" flow="down" onclick="confirmStore('.$reOpenParam.');"><i class="mdi mdi-replay"></i></a>'; 
    endif;

    //So Print
    $print1 = ['id'=>$data->id];
    $printBtn = '<a class="btn btn-dribbble btn-edit permission-approve1" href="'.base_url('salesOrders/printOrder/'.encodeurl($print1)).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    //Internal Print
	$print2 = ['id'=>$data->id, 'pdf_type'=>"Internal"];
    $printInternalBtn = '<a class="btn btn-info btn-edit permission-approve1" href="'.base_url('salesOrders/printOrder/'.encodeurl($print2)).'" target="_blank" datatip="Print Internal" flow="down"><i class="fas fa-print" ></i></a>';

    $action = getActionButton($printBtn.$printInternalBtn.$pinvBtn.$approveBtn.$revision.$rejectBtn.$shortClose.$reOpenBtn.$editButton.$deleteButton);

	$poDetail = "";
	if(!empty($data->doc_no)){$poDetail = $data->doc_no.'<br><small>('.$data->doc_date.')</small>';}
	$del_date = (!empty($data->cod_date) ? formatDate($data->cod_date,'d-m-Y') : '');	
	
	if(!empty($data->drw_no)){$data->item_name .= '<br><small>(Drg. No. : '.$data->drw_no.')</small>';}
	
	$createdBy = $data->created_name.(!empty($data->created_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($data->created_at)) : '');
    $updatedBy = $data->updated_name.(!empty($data->updated_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($data->updated_at)) : '');

    $rev_no = sprintf("%02d",$data->so_rev_no);        
    if($data->so_rev_no != 0):
        $revParam = "{'postData' : {'id' : '".$data->id."'}, 'modal_id' : 'modal-md', 'form_id' : 'revisionList', 'title' : 'Order Revision History','call_function':'revisionHistory','button':'close'}";
        $rev_no = '<a href="javascript:void(0)" datatip="Revision History" flow="down" onclick="modalAction('.$revParam.');">'.sprintf("%02d",$data->so_rev_no).'</a>';
    endif;
	
	return [$action,$data->sr_no,$rev_no,$data->trans_number,$data->trans_date,$poDetail,$del_date,$data->party_name,$data->item_name,round($data->price,2),round($data->stock_qty,2),round($data->qty,2),round($data->wip_qty,2),round($data->item_wip_qty,2),round($data->dispatch_qty,2),round($data->pending_qty,2),$data->due_days,$createdBy,$updatedBy];
}

/* Party Order Table Data */
function getPartyOrderData($data){
    $action = getActionButton("");

    return [$action,$data->sr_no,$data->order_status,$data->trans_number,$data->trans_date,$data->item_name,$data->brand_name,$data->qty,$data->dispatch_qty,$data->pending_qty];
}

/* Delivery Challan Table Data */
function getDeliveryChallanData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('deliveryChallan/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Delivery Challan'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $printBtn = '<a class="btn btn-info btn-edit permission-approve1" href="'.base_url('deliveryChallan/printChallan/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $title = 'Annexure Detail For : '.$data->trans_number;
    $annexureParam = "{'postData' : {'id' : '".$data->id."'}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addAnnexureDetail', 'title' : '". $title."','call_function':'addAnnexureDetail','button':'close'}";
    $annexureBtn  = '<a href="javascript:void(0)" class="btn btn-primary btn-edit permission-modify" datatip="Annexure Detail" flow="down" onclick="modalAction('.$annexureParam.');"><i class="fas fa-boxes" ></i></a>';
    
    $annexurePrint = '<a class="btn btn-secondary btn-edit permission-approve1" href="'.base_url('deliveryChallan/printAnnexure/'.$data->id).'" target="_blank" datatip="Annexure Print" flow="down"><i class="fas fa-print" ></i></a>';

    if($data->trans_status > 0):
        $editButton = $deleteButton = "";
    endif;

    $action = getActionButton($printBtn.$annexureBtn.$annexurePrint.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->remark];
}

/* Estimate [Cash] Table Data */
function getEstimateData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('estimate/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Estimate'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $printBtn = '<a class="btn btn-info btn-edit" href="'.base_url('estimate/printEstimate/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    if($data->trans_no == 0):
        $editButton = $deleteButton = "";
    endif;

    $action = getActionButton($printBtn.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,$data->trans_date,$data->party_name,$data->taxable_amount,$data->net_amount];
}

/* Estimate Payment [Cash] Table Data */
function getEstimatePaymentData($data){
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'estimatePayment', 'title' : 'Payment Voucher','call_function':'estimatePayment','fnsave':'saveEstimatePayment'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Payment Voucher','fndelete':'deleteEstimatePayment'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,formatDate($data->entry_date),$data->party_name,$data->received_by,$data->reference_no,$data->amount,$data->remark];
}

/* Customer Complaints Table Data*/
function getCustomerComplaintsData($data){
    $editButton=""; $deleteButton=""; $solution=""; $downloadBtn="";

	$editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'customerComplaints', 'title' : 'Update Customer Complaints','call_function':'edit'}";
	$editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

	$deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Delete Customer Complaints'}";
	$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
    if(empty($data->status)){
    
        $solParam = "{'postData' :{'id' : ".$data->id."}, 'modal_id' : 'modal-md', 'form_id' : 'solutionForm', 'title' : 'Solution', 'call_function' : 'complaintSolution', 'fnsave' : 'saveSolution'}";
        $solution = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Solution" flow="down" onclick="modalAction('.$solParam.');"><i class="fas fa-check" ></i></a>';
    }
    if($data->product_returned == 1){
        $product_returned = "No";
    }else{
        $product_returned = "Yes";
    }
	if(!empty($data->defect_image)){
		$downloadBtn = '<a class="btn btn-primary btn-edit" href="'.base_url('customerComplaints/printCustomerComplaints/'.encodeurl(['defect_image'=>$data->defect_image])).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print"></i></a>';
	}
	
    $report_8d = (!empty($data->report_8d) ? '<a href="'.base_url("assets/uploads/8d_report/".$data->report_8d).'" target="_blank"><i class="fa fa-download"></i></a>' : "");
	
    $action = getActionButton($solution.$editButton.$deleteButton);
	return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->inv_number,$data->item_name,$data->complaint,$downloadBtn,$product_returned,$report_8d,$data->action_taken,$data->ref_feedback,$data->remark];
}

/* Pending Dispatch Plan Table data */
function getPendingPlanData($data){
    $planBtn = '<a class="btn btn-success permission-modify" href="'.base_url('dispatchPlan/addPlan/'.$data->party_id).'" datatip="Add Dispatch Plan" flow="down"><i class="fas fa-plus"></i></a>';

    $action = getActionButton($planBtn);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,(!empty($data->item_code) ? '['.$data->item_code.'] ' : '').$data->item_name,floatVal($data->qty),floatval($data->plan_qty),floatval($data->dispatch_qty),floatval($data->pending_qty)];
}

/* Dispatch Plan Table data */
function getDispatchPlanData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Dispatch Plan'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($deleteButton);

    return [$action,$data->sr_no,$data->plan_number,formatDate($data->plan_date),$data->party_name,$data->so_number,formatDate($data->so_date),(!empty($data->item_code) ? '['.$data->item_code.'] ' : '').$data->item_name,floatVal($data->qty)];
}

/* Proforma Invoice Table data */
function getProformaInvoiceData($data){
    $editButton = '<a class="btn btn-warning btn-edit permission-modify" href="'.base_url('proformaInvoice/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

	$deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Proforma Invoice'}";
	$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

	$print1 = ['id'=>$data->id, 'pdf_type'=>"Domestic"];
	$print2 = ['id'=>$data->id, 'pdf_type'=>"International"];
    //Domestic Print
    $printBtn = '<a class="btn btn-success btn-edit permission-approve1" href="'.base_url('proformaInvoice/printInvoice/'.encodeurl($print1)).'" target="_blank" datatip="Print Domestic" flow="down"><i class="fas fa-print" ></i></a>';
    //International Print
    $printIntBtn = '<a class="btn btn-primary btn-edit permission-approve1" href="'.base_url('proformaInvoice/printInvoice/'.encodeurl($print2)).'" target="_blank" datatip="Print International" flow="down"><i class="fas fa-print" ></i></a>';

    $approveBtn ="";
    if(empty($data->is_approve)):
        $approveParam = "{'postData':{'id' : ".$data->id.",'is_approve':1,'msg':'Approved'},'fnsave':'approveProformaInvoice','message':'Are you sure want to Approve this ProformaInvoice?'}";
        $approveBtn = '<a class="btn btn-info permission-approve" href="javascript:void(0)" datatip="Approve PINV" flow="down" onclick="confirmStore('.$approveParam.');"><i class="mdi mdi-check"></i></a>';    
    else:
        $editButton = $deleteButton =  $approveBtn ="";
    endif;
   
    $action = getActionButton($approveBtn.$printBtn.$printIntBtn.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
}

/* Packing Data */
function getPackingData($data){
    $edit = $delete = "";
    if($data->pending_dispatch == $data->total_qty){
        $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editPacking', 'title' : 'Update Packing'}";
        $edit = '<a class="btn btn-success btn-sm btn-edit permission-modify" href="javascript:void(0)" onclick="modalAction('.$editParam.');" datatip="Edit" flow="down"><i class="mdi mdi-square-edit-outline"></i></a>';
    
        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Packing Order'}";
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    }
    $printBtn = '<a class="btn btn-info btn-edit" href="'.base_url('packing/packedBoxSticker/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    $item_name = (!empty($data->item_code) ? "[".$data->item_code."] ".$data->item_name : $data->item_name);
    $action = getActionButton($printBtn.$edit.$delete);
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$item_name,$data->qty_per_box,$data->total_box,$data->total_qty,$data->pending_dispatch,$data->remark];
}

/* Packing Stock Data */
function getPackingStockData($data){
    $item_name = (!empty($data->item_code) ? "[".$data->item_code."] ".$data->item_name : $data->item_name);
    return [$data->sr_no,$item_name,$data->batch_no,$data->ref_batch,floatVal($data->stock_qty)];
}

function getNpSalesEnquiryData($data){
    $feasBtn = "";
    if($data->trans_status == 3 && $data->feasible_status == 0):
        $feaParam = "{'postData' :{'id' : ".$data->id."}, 'modal_id' : 'modal-md', 'form_id' : 'Feasible', 'title' : 'Feasible', 'call_function' : 'enqFeasible', 'fnsave' : 'saveFeasibility'}";
        $feasBtn = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Feasible" flow="down" onclick="modalAction('.$feaParam.');"><i class="fas fa-check" ></i></a>';
    endif;

    $action = getActionButton($feasBtn);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->item_name,round($data->qty,2)];
}

/* Primary Packing Data */
function gePrimaryPackingData($data){
    $edit = $delete = "";
    if($data->dispatch_qty == 0){
        $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'editPacking', 'title' : 'Update Packing'}";
        $edit = '<a class="btn btn-success btn-sm btn-edit permission-modify" href="javascript:void(0)" onclick="modalAction('.$editParam.');" datatip="Edit" flow="down"><i class="mdi mdi-square-edit-outline"></i></a>';
    
        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Packing Order'}";
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    }
   
    $item_name = (!empty($data->item_code) ? "[".$data->item_code."] ".$data->item_name : $data->item_name);
    $action = getActionButton($edit.$delete);
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$item_name,$data->qty_per_box,$data->total_box,$data->total_qty];
}

/* Final Packing Data */
function getFinalPackingData($data){
    $edit = $delete = $bookBtn = "";
    if (in_array($data->tab_status, [1,3])) {
        if ($data->tab_status == 1) {
            $edit = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('finalPacking/edit/'.$data->packing_id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

            $bookParam = "{'postData' :{'id' : ".$data->id.", 'trans_number' : '".$data->trans_number."'}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'bookQty', 'title' : 'Book Qty', 'call_function' : 'bookQty', 'fnsave' : 'saveBookQty'}";
            $bookBtn = '<a class="btn btn-primary permission-modify" href="javascript:void(0)" datatip="Book Qty" flow="down" onclick="modalAction('.$bookParam.');"><i class="fas fa-plus"></i></a>';
        }

        $deleteParam = "{'postData':{'id' : ".$data->packing_id."},'message' : 'Packing'}";
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    }

    $printBtn = '<a class="btn btn-info btn-edit" href="'.base_url('finalPacking/finalPackingPrint/'.$data->packing_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $item_name = (!empty($data->item_code) ? "[".$data->item_code."] ".$data->item_name : $data->item_name);

    $action = getActionButton($printBtn.$bookBtn.$edit.$delete);
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->so_number,$item_name,$data->total_box,$data->total_qty,$data->inv_number];
}
?>