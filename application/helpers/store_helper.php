<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getStoreDtHeader($page){
    /* Location Master header */
    $data['storeLocation'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['storeLocation'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['storeLocation'][] = ["name"=>"Store Name"];
    $data['storeLocation'][] = ["name"=>"Location"];
    $data['storeLocation'][] = ["name"=>"Remark"];

    /* Gate Entry */
    $data['gateEntry'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['gateEntry'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['gateEntry'][] = ["name"=> "GE No.", "textAlign" => "center"];
    $data['gateEntry'][] = ["name" => "GE Date", "textAlign" => "center"];
    $data['gateEntry'][] = ["name" => "Transport"];
    $data['gateEntry'][] = ["name" => "LR No."];
    $data['gateEntry'][] = ["name" => "Vehicle Type"];
    $data['gateEntry'][] = ["name" => "Vehicle No."];
    $data['gateEntry'][] = ['name' => "Invoice No."];
    $data['gateEntry'][] = ['name' => "Invoice Date"];
    $data['gateEntry'][] = ['name' => "Challan No."];
    $data['gateEntry'][] = ['name' => "Challan Date"];

    /* Gate Inward Pending GE Tab Header */
    $data['pendingGE'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['pendingGE'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['pendingGE'][] = ["name"=> "GE No.", "textAlign" => "center"];
    $data['pendingGE'][] = ["name" => "GE Date", "textAlign" => "center"];
    $data['pendingGE'][] = ["name" => "Party Name"];
    $data['pendingGE'][] = ["name" => "Inv. No."];
    $data['pendingGE'][] = ["name" => "Inv. Date"];
    $data['pendingGE'][] = ['name' => "CH. NO."];
    $data['pendingGE'][] = ['name' => "CH. Date"];

    /* Gate Inward Pending/Compeleted Tab Header */
    $data['gateInward'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['gateInward'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['gateInward'][] = ["name"=> "GRN No.", "textAlign" => "center"];
    $data['gateInward'][] = ["name" => "GRN Date", "textAlign" => "center"];
    $data['gateInward'][] = ["name" => "Party Name"];
    $data['gateInward'][] = ["name" => "GRN Type"];
    $data['gateInward'][] = ["name" => "Item Name"];
	$data['gateInward'][] = ["name"=>"Finish Goods"];
    $data['gateInward'][] = ["name" => "Qty"];
    $data['gateInward'][] = ["name" => "UOM"];
    $data['gateInward'][] = ["name" => "Price"];
    $data['gateInward'][] = ["name" => "PO. NO."];  
    $data['gateInward'][] = ["name" => "Remark"];
    
    /* Inward QC Pending/Compeleted Tab Header */
    $data['inwardQC'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['inwardQC'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['inwardQC'][] = ["name"=> "GRN No.", "textAlign" => "center"];
    $data['inwardQC'][] = ["name" => "GRN Date", "textAlign" => "center"];
    $data['inwardQC'][] = ["name" => "Party Name"];
    $data['inwardQC'][] = ["name" => "GRN Type"];
    $data['inwardQC'][] = ["name" => "Item Name"];
	$data['inwardQC'][] = ["name"=>"Finish Goods"];
    $data['inwardQC'][] = ["name" => "Location"];
    $data['inwardQC'][] = ["name" => "Batch No."];
    $data['inwardQC'][] = ["name" => "Ref./Heat No."];
    $data['inwardQC'][] = ["name" => "Price"];
    $data['inwardQC'][] = ["name" => "Receive Qty"];
    $data['inwardQC'][] = ["name" => "UOM"];
    $data['inwardQC'][] = ["name" => "Ok Qty"];
    $data['inwardQC'][] = ["name" => "Reject Qty"];
    $data['inwardQC'][] = ["name" => "Short Qty"];
    $data['inwardQC'][] = ["name" => "PO. NO."]; 
    $data['inwardQC'][] = ["name" => "Remark"];
	
	/* Pending QC Header */
    $data['pendingQc'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['pendingQc'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['pendingQc'][] = ["name"=>"GRN No."];
    $data['pendingQc'][] = ["name"=>"Name Of Agency."];
    $data['pendingQc'][] = ["name"=>"Inspection Type"];
    $data['pendingQc'][] = ["name"=>"Test Type"];
    $data['pendingQc'][] = ["name"=>"Item Name"];
    $data['pendingQc'][] = ["name"=>"Sample Qty"];
    $data['pendingQc'][] = ["name"=>"Batch No."];
    $data['pendingQc'][] = ["name"=>"Ref./Heat No."];
    $data['pendingQc'][] = ["name" => "Remark"];

    /* FG Stock Inward Table Header */
    $data['stockTrans'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['stockTrans'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['stockTrans'][] = ["name" => "Date"];
    $data['stockTrans'][] = ["name" => "Item Name"];
    $data['stockTrans'][] = ["name" => "Qty"];
    $data['stockTrans'][] = ["name" => "Remark"];

    /* LIST OF STOCK VERIFICATION  */
    $data['stockVerification'][] = ["name"=>"#","textAlign"=>"center","srnoPosition"=>0];
    $data['stockVerification'][] = ["name"=>"Item Code."];
    $data['stockVerification'][] = ["name"=>"Item Name"];
    $data['stockVerification'][] = ["name"=>"Stock Register Qty."];
    $data['stockVerification'][] = ["name"=>"Action","textAlign"=>"center"];

    /* Return Requisition Table Header */
    $data['returnRequisition'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['returnRequisition'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['returnRequisition'][] = ["name" => "Issue Number"];
    $data['returnRequisition'][] = ["name" => "Issue Date"];
    $data['returnRequisition'][] = ["name" => "Item Name"];
    $data['returnRequisition'][] = ["name" => "Issue Qty"];
    $data['returnRequisition'][] = ["name" => "Return Qty"];
    $data['returnRequisition'][] = ["name" => "Pending Qty"];
	$data['returnRequisition'][] = ["name" => "Remark"]; 

    /* Issued Requisition Table Header */
    $data['issueRequisition'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['issueRequisition'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['issueRequisition'][] = ["name" => "Issued Number"];
    $data['issueRequisition'][] = ["name" => "Issue Date"];
    $data['issueRequisition'][] = ["name" => "PRC No"];
    $data['issueRequisition'][] = ["name" => "Item Name"];
    $data['issueRequisition'][] = ["name" => "Issue Qty"];
    $data['issueRequisition'][] = ["name" => "Batch No"];
    $data['issueRequisition'][] = ["name" => "Issue To"];
    $data['issueRequisition'][] = ["name" => "Unit"];
    $data['issueRequisition'][] = ["name" => "Created By/At"];
	$data['issueRequisition'][] = ["name" => "Remark"];

    /* Inspection Table Header */
    $data['inspection'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['inspection'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['inspection'][] = ["name" => "Issued Number"];
    $data['inspection'][] = ["name" => "Trans Date"];
    $data['inspection'][] = ["name" => "Total Qty"];
    $data['inspection'][] = ["name" => "Batch No"];
    $data['inspection'][] = ["name" => "Remark"];

    /* PRC Material Issue Table Header */
    $data['prcMaterial'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['prcMaterial'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['prcMaterial'][] = ["name" => "PRC No."];
    $data['prcMaterial'][] = ["name" => "PRC Date"];
    $data['prcMaterial'][] = ["name" => "Finish Good"];
    $data['prcMaterial'][] = ["name" => "Required Qty"];
    $data['prcMaterial'][] = ["name" => "Issue Qty"];
	
    /* Die Material Request header */
    $data['dieMaterialReq'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['dieMaterialReq'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['dieMaterialReq'][] = ["name"=>"Job No."];
    $data['dieMaterialReq'][] = ["name"=>"Die"];
    $data['dieMaterialReq'][] = ["name"=>"Product"];
    $data['dieMaterialReq'][] = ["name"=>"Required Material"];
    $data['dieMaterialReq'][] = ["name"=>"Rquired Qty"];
    $data['dieMaterialReq'][] = ["name"=>"Issue Qty"];

    /* Manual Rejection header */
    $data['manualRejection'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['manualRejection'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['manualRejection'][] = ["name"=>"Rej. Date"];
    $data['manualRejection'][] = ["name"=>"Product"];
    $data['manualRejection'][] = ["name"=>"Qty"];
    $data['manualRejection'][] = ["name"=>"Location"];
    $data['manualRejection'][] = ["name"=>"Batch No."];
    $data['manualRejection'][] = ["name"=>"Remark"];
	
	/* Opening Stock Table Header */
    $data['openingStock'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['openingStock'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['openingStock'][] = ["name" => "Date"];
	$data['openingStock'][] = ["name" => "Item Name"];
    $data['openingStock'][] = ["name" => "Location"];
	$data['openingStock'][] = ["name" => "Batch No."];
    $data['openingStock'][] = ["name" => "Qty"];

    /** End Piece Data */
    $data['endPiece'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['endPiece'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['endPiece'][] = ["name"=>"Date"];
    $data['endPiece'][] = ["name"=>"PRC No"];
    $data['endPiece'][] = ["name"=>"Item Name"];
    $data['endPiece'][] = ["name"=>"End Piece"];
    $data['endPiece'][] = ["name"=>"Qty"];
    $data['endPiece'][] = ["name"=>"Review Qty"];
    $data['endPiece'][] = ["name"=>"Batch No."];
    $data['endPiece'][] = ["name"=>"Remark"];

    /* Out Challan Header */
    $data['outChallan'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['outChallan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['outChallan'][] = ["name"=>"Challan No."];
    $data['outChallan'][] = ["name"=>"Challan Date"];
    $data['outChallan'][] = ["name"=>"Party Name"];
    $data['outChallan'][] = ["name"=>"Item Name"];
    $data['outChallan'][] = ["name"=>"Qty"];
    $data['outChallan'][] = ["name"=>"Remark"];
	
	/* Internal GRN Header */
    $data['internalGrn'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['internalGrn'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['internalGrn'][] = ["name"=>"GRN No.", "textAlign" => "center"];
    $data['internalGrn'][] = ["name"=>"GRN Date", "textAlign" => "center"];
    $data['internalGrn'][] = ["name"=>"Party Name"];
    $data['internalGrn'][] = ["name"=>"Item Name"];
    $data['internalGrn'][] = ["name"=>"Finish Goods"];
    $data['internalGrn'][] = ["name"=>"Use Batch"];
    $data['internalGrn'][] = ["name"=>"Batch No."];
    $data['internalGrn'][] = ["name"=>"Qty"];
    $data['internalGrn'][] = ["name"=>"UOM"];
    $data['internalGrn'][] = ["name"=>"Price"];
    $data['internalGrn'][] = ["name"=>"Remark"]; 
	
	/*  Requisition Table Header */
    $data['requisition'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['requisition'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['requisition'][] = ["name" => "Req. Number"];
    $data['requisition'][] = ["name" => "Req. Date"];
    $data['requisition'][] = ["name" => "M.T. No."];
    $data['requisition'][] = ["name" => "Item Name"];
    $data['requisition'][] = ["name" => "Req. Qty"];
    $data['requisition'][] = ["name" => "Issue Qty"];

    return tableHeader($data[$page]);
}

/* Store Location Data */
function getStoreLocationData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Store Location'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editStoreLocation', 'title' : 'Update Store Location','call_function':'edit'}";

    $editButton = ''; $deleteButton = '';$qrCode='';
    //if(!empty($data->ref_id) && empty($data->store_type)):
	if(!empty($data->ref_id) && empty($data->store_type) && $data->ref_id != 15): 
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    endif;

    if($data->final_location == 0):
        $locationName = '<a href="' . base_url("storeLocation/list/" . $data->id) . '">' . $data->location . '</a>';
    else:
        $locationName = $data->location;
        $qrCode = '<a href="'.base_url("storeLocation/locationQr/" . $data->id).'" class="btn btn-primary permission-write" target="_blank" datatip="Generate QR" flow="down"><i class="mdi mdi-qrcode"></i></a>';
    endif;
	
	$action = getActionButton($qrCode.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->store_name,$locationName,$data->remark];
}

/* Gate Entry Data  */
function getGateEntryData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Gate Entry'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editGateEntry', 'title' : 'Update Gate Entry','call_function':'edit'}";

    $editButton = "";
    $deleteButton = "";
    if($data->trans_status == 0):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    endif;

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->transport_name,$data->lr,$data->vehicle_type_name,$data->vehicle_no,$data->inv_no,((!empty($data->inv_date))?formatDate($data->inv_date):""),$data->doc_no,((!empty($data->doc_date))?formatDate($data->doc_date):"")];
}

/* GateInward Data Data  */
function getGateInwardData($data){
    $action = '';$editButton='';$deleteButton='';$inspection = '';
	
	if($data->trans_status == 1):
		if($data->type != "Customer Return"):
			$editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'editGateInward', 'title' : 'Update Gate Inward','call_function':'edit'}";
			$editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
		endif;
		
		$deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Gate Inward'}";
		$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
         
        if(empty($data->is_inspection)):
            $insParam = "{'postData':{'id' : ".$data->mir_trans_id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'materialInspection', 'title' : 'Material Inspection','call_function':'materialInspection','fnsave':'saveInspectedMaterial'}";
            $inspection = '<a href="javscript:voide(0);" type="button" class="btn btn-warning " datatip="Inspection" flow="down" onclick="modalAction('.$insParam.');"><i class="fas fa-search"></i></a>';
        endif;
	endif;
	
	$iirTagPrint = '<a href="'.base_url('gateInward/ir_print/'.$data->mir_trans_id).'" type="button" class="btn btn-primary" datatip="IIR Tag" flow="down" target="_blank"><i class="fas fa-print"></i></a>';

	$grnPrint = '<a class="btn btn-success btn-info" href="'.base_url('gateInward/printGRN/'.$data->id).'" target="_blank" datatip="GRN Print" flow="down"><i class="fas fa-print" ></i></a>';

	$action = getActionButton($iirTagPrint.$grnPrint.$inspection.$editButton.$deleteButton);

	$data->item_name = (!empty($data->item_code)) ? '['.$data->item_code.'] '.$data->item_name : $data->item_name;
    $data->fg_item_name = (!empty($data->fg_item_code)) ? '['.$data->fg_item_code.'] '.$data->fg_item_name : $data->fg_item_name;
	return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->type,$data->item_name,$data->fg_item_name,floatval($data->qty),$data->uom,floatval($data->price),$data->po_number,$data->item_remark];
}

/* Inward Qc Data  */
function getInwardQcData($data){
    $inspection = $iirPrint = $iirInsp = $tcButton = $approveBtn = $allTestBtn = "";

    if($data->trans_status == 1){
        $insParam = "{'postData':{'id' : ".$data->mir_trans_id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'materialInspection', 'title' : 'Material Inspection','call_function':'materialInspection','fnsave':'saveInspectedMaterial'}";
        $inspection = '<a href="javscript:voide(0);" type="button" class="btn btn-warning " datatip="Inspection" flow="down" onclick="modalAction('.$insParam.');"><i class="fas fa-search"></i></a>';
    }
   
    if($data->is_inspection == 1):
        $iirParam = "{'postData':{'id' : ".$data->mir_trans_id."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'iirInsp', 'title' : 'Incoming Inspection','call_function':'getInwardQc','fnsave':'saveInwardQc'}";
        $iirInsp = '<a href="javascript:void(0)" type="button" class="btn btn-info permission-modify" datatip="Incoming Inspection" flow="down" onclick="modalAction('.$iirParam.');"><i class="fa fa-file-alt"></i></a>';

        $iirPrint = '<a class="btn btn-dribbble btn-edit permission-modify" href="'.base_url($data->controller.'/inInspection_pdf/'.$data->mir_trans_id).'" target="_blank" datatip="Inspection Print" flow="down"><i class="fas fa-print" ></i></a>';
        
        if(!empty($data->report_count)):
            $allTestBtn = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url($data->controller.'/meargeAllTcReport/'.$data->mir_trans_id).'" target="_blank" datatip="All Test Report" flow="down"><i class="fas fa-print" ></i></a>';
        endif;
    endif;
    $testReport = "{'postData':{'id' : '".$data->mir_trans_id."','grn_id' : '".$data->id."'}, 'button' : 'close', 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'testReport', 'title' : 'Test Report', 'call_function' : 'getTestReport'}";
    $tcButton = '<a class="btn btn-dark btn-salary " href="javascript:void(0)" datatip="Test Report" flow="down" onclick="modalAction('.$testReport.');"><i class="mdi mdi-file-multiple"></i></a>';

    $iirTagPrint = '<a href="'.base_url('gateInward/ir_print/'.$data->mir_trans_id).'" type="button" class="btn btn-primary" datatip="IIR Tag" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    $grnPrint = '<a class="btn btn-success btn-info" href="'.base_url('gateInward/printGRN/'.$data->id).'" target="_blank" datatip="GRN Print" flow="down"><i class="fas fa-print" ></i></a>';

    if($data->type == 3){
        $tcButton = $allTestBtn = $iirPrint = $iirInsp = "";
    }
	
    $action = getActionButton($approveBtn.$inspection.$grnPrint.$iirTagPrint.$tcButton.$allTestBtn.$iirInsp.$iirPrint);

	$data->item_name = (!empty($data->item_code)) ? '['.$data->item_code.'] '.$data->item_name : $data->item_name;
    $data->fg_item_name = (!empty($data->fg_item_code)) ? '['.$data->fg_item_code.'] '.$data->fg_item_name : $data->fg_item_name;
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->type,$data->item_name,$data->fg_item_name,$data->location,$data->batch_no,$data->heat_no,floatval($data->price),floatval($data->qty),$data->uom,floatval($data->ok_qty),floatval($data->reject_qty),floatval($data->short_qty),$data->po_number,$data->item_remark];
}

/* Pending QC Table Data */
function getPendingQcData($data){
    $receiveParam = "{'postData':{'id' : ".$data->id.",'grn_trans_id' : ".$data->grn_trans_id."}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'addReceiveTest', 'title' : 'Receive Test Report', 'call_function':'receiveTestReport', 'fnsave':'saveReceiveTestReport'}";
    $receiveBtn = '<a href="javascript:void(0)" type="button" class="btn btn-info permission-modify" datatip="Receive Test Report" flow="down" onclick="modalAction('.$receiveParam.');"><i class="fa fa-plus"></i></a>';       

    $printBtn = '<a href="'.base_url('gateInward/printReceiveTcReport/'.$data->id).'" type="button" class="btn btn-primary" datatip="Challan Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
				
    $action = getActionButton($receiveBtn.$printBtn);
	
    $test_name = (!empty($data->test_type) ? $data->test_name: "");
    $ins_type = (($data->test_type == 0) ? "GRN": $data->ins_type);
	$data->item_name = (!empty($data->item_code)) ? '['.$data->item_code.'] '.$data->item_name : $data->item_name;
    //return [$action,$data->sr_no,$data->trans_number,$data->name_of_agency,$ins_type,$test_name,$data->item_name.(!empty($data->material_grade) ? ' '.$data->material_grade : ''),floatval($data->sample_qty),$data->batch_no,$data->heat_no];
	return [$action,$data->sr_no,$data->trans_number,$data->name_of_agency,$ins_type,$test_name,$data->item_name,floatval($data->sample_qty),$data->batch_no,$data->heat_no,$data->item_remark];
}

/* FG Stock Inward Table Data */
function getStockTransData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Stock'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($deleteButton);

    return [$action,$data->sr_no,formatDate($data->ref_date),$data->item_name,$data->qty,$data->remark];
}

/* Stock Verification Table Data */
function getStockVerificationData($data){
 
    $editParam = "{'postData':{'id' : ".$data->id.",'item_id': ".$data->item_id."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'editStock', 'title' : 'Update Stock','call_function':'editStock','fnsave':'save'}";
    $editButton = '<a href="javascript:void(0)" type="button" class="btn btn-sm btn-success permission-modify" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    return [$data->sr_no,$data->item_code,$data->item_name,floatVal($data->stock_qty),$editButton];
}

/* Return Requisition Table Data */
function getReturnRequisitionData($data){
    $returnButton = '';
    $issue_qty = floatval($data->issue_qty);
    $return_qty = floatval($data->return_qty);
    if($issue_qty > $return_qty){
        $returnParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'modal-md', 'form_id' : 'closeRequisition', 'fnedit' : 'return', 'call_function' : 'return', 'title' : 'Return Material', 'fnsave' : 'saveReturnReq'}";
        $returnButton = '<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="Return" flow="down" onclick="modalAction('.$returnParam.');"><i class="fa fa-reply" ></i></a>';
    }

    $action = getActionButton($returnButton);
	return [$action,$data->sr_no,$data->issue_number,formatDate($data->issue_date),(($data->item_code) ? "[".$data->item_code."] " : "").$data->item_name,floatval($data->issue_qty),floatval($data->return_qty),floatval($data->pending_qty),$data->remark];
}

/* Issue Requisition Table Data */
function getIssueRequisitionData($data){
    $deleteButton = "";
    $return_qty = floatval($data->return_qty);
    /* if(empty($return_qty) && (empty($data->prc_id) OR $data->issue_type == 4)){ */
    if(empty($return_qty)){
        $deleteParam = "{'postData':{'id' : ".$data->id.",'prc_id' : ".$data->prc_id.",'issue_type' : ".$data->issue_type."}, 'fndelete' : 'deleteIssueRequisition','message' : 'Stock'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    }
    $action = getActionButton($deleteButton);
	
    $unit = ($data->unit_id == 1) ? 'UNIT 1' : 'UNIT 2';
    $createdBy = $data->created_name.(!empty($data->created_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($data->created_at)) : '');
	
    return [$action,$data->sr_no,$data->issue_number,formatDate($data->issue_date),$data->prc_number,(($data->item_code) ? "[".$data->item_code."] " : "").$data->item_name,abs($data->issue_qty),$data->batch_no,$data->issue_name,$unit,$createdBy,$data->remark];
}

/* Inspection Table Data */
function getInspectionData($data) {
    $inspectButton = "";
    if($data->trans_type == 1){
        $inspectParam = "{'postData':{'id' : ".$data->id.",'issue_id' : ".$data->issue_id."},'modal_id' : 'modal-md', 'fnedit' : 'addInspection', 'call_function':'addInspection','form_id' : 'addInspection', 'title' : 'Inspection', 'fnsave' : 'saveInspection'}";
        $inspectButton = '<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="Inspect" flow="down" onclick="modalAction('.$inspectParam.');"><i class="fa fa-search" ></i></a>';
    }
    $action = getActionButton($inspectButton);
    return [$action,$data->sr_no,$data->issue_number,formatDate($data->trans_date),$data->total_qty,$data->batch_no,$data->remark];
}

/* Prc Material Issue Table Data */
function getPrcMaterialData($data){
    $required_qty = round($data->ppc_qty * $data->prc_qty,3);
    $isuueParam = "{'postData':{'prc_id' : ".$data->prc_id.", 'item_id' : '".$data->item_id."', 'required_qty' : '".$required_qty."','issue_type':'2'}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'addIssueRequisition', 'title' : 'Material Issue', 'call_function':'addIssueRequisition', 'fnsave':'saveIssueRequisition','js_store_fn':'storeIssueMaterial'}";
    $issueBtn = '<a href="javascript:void(0)" type="button" class="btn btn-info permission-modify" datatip="Material Issue" flow="down" onclick="modalAction('.$isuueParam.');"><i class="fa fa-plus"></i></a>';  
    $action = getActionButton($issueBtn);
    
    return [$action,$data->sr_no,$data->prc_number,formatDate($data->prc_date),$data->item_name,$required_qty,(!empty($data->issue_qty)?$data->issue_qty:0)];
}

/* Die Material Request Table Data */
function getDieMaterialRequest($data){
 
    $isuueParam = "{'postData':{'die_id' : ".$data->die_id.", 'item_id' : '".$data->item_id."', 'required_qty' : '".$data->bom_qty."','issue_type':'3'}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'addIssueRequisition', 'title' : 'Material Issue', 'call_function':'addIssueRequisition', 'fnsave':'saveIssueRequisition'}";
    $issueBtn = '<a href="javascript:void(0)" type="button" class="btn btn-info permission-modify" datatip="Material Issue" flow="down" onclick="modalAction('.$isuueParam.');"><i class="fa fa-plus"></i></a>';       

    $action = getActionButton($issueBtn);
    return [$action,$data->sr_no,$data->trans_number,$data->category_name,(($data->fg_item_code) ? "[".$data->fg_item_code."] " : "").$data->fg_item_name,(($data->item_code) ? "[".$data->item_code."] " : "").$data->item_name,floatval($data->bom_qty),floatval($data->issue_qty)];
}

/* ManualRejection  Table Data */
function getManualRejectionData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."}, 'fndelete' : 'deleteRejection','message' : 'Rejection'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';   

    $action = getActionButton($deleteButton);
    return [$action,$data->sr_no,formatDate($data->trans_date),(($data->item_code) ? "[".$data->item_code."] " : "").$data->item_name,floatval($data->qty),$data->location,$data->batch_no,$data->remark];
}

/* Opening Stock Table Data */
function getStockInwardData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Stock'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($deleteButton);
	return [$action,$data->sr_no,formatDate($data->trans_date),$data->item_name,$data->location,$data->batch_no,$data->qty];
}

/* End Piece Table Data */
function getEndPieceData($data){
    $stockParam = "{'postData':{'id' : ".$data->id."},'title' : 'Add to stock', 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addStock', 'call_function':'addStock', 'fnsave':'saveStock','button':'close'}";
    $stockButton = '<a class="btn btn-success permission-modify" href="javascript:void(0)" onclick="modalAction('.$stockParam.');" datatip="Add to stock" flow="down"><i class="fas fa-database"></i></a>';

    $action = getActionButton($stockButton);

    return [$action,$data->sr_no,formatDate($data->trans_date),$data->prc_number,$data->item_code.' '.$data->item_name,$data->end_pcs,$data->qty,$data->review_qty,$data->batch_no,$data->remark];
}

/* OutChallan Table Data */
function getOutChallanData($data){
    $editButton = $deleteButton = '';
    if($data->receive_qty == 0):
        $editButton = '<a class="btn btn-success permission-modify" href="'.base_url('outChallan/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Out Challan'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';  
    endif;      
        
    $receiveParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'modal-lg', 'form_id' : 'receiveItem', 'title' : 'Receive Item', 'call_function':'receiveItem', 'fnsave':'saveReceiveItem', 'button' : 'close'}";
    $receiveBtn = '<a href="javascript:void(0)" type="button" class="btn btn-primary permission-modify" datatip="Receive Item" flow="down" onclick="modalAction('.$receiveParam.');"><i class="mdi mdi-reply"></i></a>';    

    $printBtn = '<a class="btn btn-dribbble" href="'.base_url('outChallan/printChallan/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print"></i></a>';

    if(!empty($data->item_remark)){$data->item_name .= '<br><small>(Item Remark : '.$data->item_remark.')</small>';} 

    $action = getActionButton($printBtn.$receiveBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->item_name,$data->qty,$data->remark];
}

/* Internal Grn Data Data  */
function getInternalGrnData($data){
    $action = '';$editButton='';$deleteButton="";$inspection = '';

	$deleteParam = "{'postData':{'id' : ".$data->id.",'mir_trans_id' : ".$data->mir_trans_id."},'message' : 'Gate Inward','controller':'internalGrn'}";	
	if($data->trans_status == 1):
		$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
               
        if(empty($data->is_inspection)):
            $insParam = "{'postData':{'id' : ".$data->mir_trans_id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'materialInspection', 'title' : 'Material Inspection','call_function':'materialInspection','fnsave':'saveInspectedMaterial'}";
            $inspection = '<a href="javscript:voide(0);" type="button" class="btn btn-warning " datatip="Inspection" flow="down" onclick="modalAction('.$insParam.');"><i class="fas fa-search"></i></a>';
        endif;
	endif;
	
	$iirTagPrint = '<a href="'.base_url('gateInward/ir_print/'.$data->mir_trans_id).'" type="button" class="btn btn-primary" datatip="IIR Tag" flow="down" target="_blank"><i class="fas fa-print"></i></a>';

	$grnPrint = '<a class="btn btn-success btn-info" href="'.base_url('gateInward/printGRN/'.$data->id).'" target="_blank" datatip="GRN Print" flow="down"><i class="fas fa-print" ></i></a>';

	$action = getActionButton($iirTagPrint.$grnPrint.$inspection.$editButton.$deleteButton);

	$data->item_name = (!empty($data->item_code)) ? '['.$data->item_code.'] '.$data->item_name : $data->item_name;
    $data->fg_item_name = (!empty($data->fg_item_code)) ? '['.$data->fg_item_code.'] '.$data->fg_item_name : $data->fg_item_name;
	return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->item_name.(!empty($data->material_grade) ? ' '.$data->material_grade : ''),$data->fg_item_name,$data->stock_batch,$data->batch_no,floatval($data->qty),$data->uom,floatval($data->price),$data->item_remark];
}

/*  Requisition Table Data */
function getRequisitionData($data){
    $deleteButton = "";
    $issue_qty = floatval($data->issue_qty);

    if(empty($issue_qty) && empty($data->prc_id)){
        $deleteParam = "{'postData':{'id' : ".$data->id." , 'issue_qty' : '".$data->issue_qty."'}, 'fndelete' : 'deleteSparPartRequest','message' : 'Sparpart Request'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    }

    $isuueParam = "{'postData':{'req_id' : ".$data->id.", 'item_id' : '".$data->item_id."', 'required_qty' : '".$data->req_qty."','issue_type':'4'}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'addIssueRequisition', 'title' : 'Material Issue', 'call_function':'addIssueRequisition', 'fnsave':'saveIssueRequisition','js_store_fn':'storeIssueMaterial'}";
    $issueBtn = '<a href="javascript:void(0)" type="button" class="btn btn-info permission-modify" datatip="Material Issue" flow="down" onclick="modalAction('.$isuueParam.');"><i class="fa fa-plus"></i></a>';  
  
    $action = getActionButton($issueBtn.$deleteButton);
	
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->ticket_number,(($data->item_code) ? "[".$data->item_code."] " : "").$data->item_name,abs($data->req_qty),abs($data->issue_qty)];
}
?>