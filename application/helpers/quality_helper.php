<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getQualityDtHeader($page){

    /* Instrument Header */
    $masterInstSelect = '<input type="checkbox" id="masterInstSelect" class="filled-in chk-col-success BulkInstChallan" value=""><label for="masterInstSelect">ALL</label>';
    $data['instrumentChk'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['instrumentChk'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['instrumentChk'][] = ["name"=>$masterInstSelect,"class"=>"no_filter","textAlign"=>"center","orderable"=>"false"];
	$data['instrumentChk'][] = ["name"=>"Code"];
    $data['instrumentChk'][] = ["name"=>"Description"];
    $data['instrumentChk'][] = ["name"=>"Supplier"];
    $data['instrumentChk'][] = ["name"=>"Price"];
    $data['instrumentChk'][] = ["name"=>"Make"];
    $data['instrumentChk'][] = ["name"=>"Required"];
    $data['instrumentChk'][] = ["name"=>"Frequency<br>(Months)"];
    $data['instrumentChk'][] = ["name"=>"Location"];
	$data['instrumentChk'][] = ["name"=>"Cal Date"];
	$data['instrumentChk'][] = ["name"=>"Due Date"];
	$data['instrumentChk'][] = ["name"=>"Plan Date"];
	$data['instrumentChk'][] = ["name"=>"Inward Date"];
	
    /* Instrument Header Without Checkbox*/
    $data['instrument'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['instrument'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['instrument'][] = ["name"=>"Code"];
    $data['instrument'][] = ["name"=>"Description"];
    $data['instrument'][] = ["name"=>"Supplier"];
    $data['instrument'][] = ["name"=>"Price"];
    $data['instrument'][] = ["name"=>"Make"];
    $data['instrument'][] = ["name"=>"Required"];
    $data['instrument'][] = ["name"=>"Frequency<br>(Months)"];
    $data['instrument'][] = ["name"=>"Location"];
    $data['instrument'][] = ["name"=>"Cal Date"];
    $data['instrument'][] = ["name"=>"Due Date"];
    $data['instrument'][] = ["name"=>"Plan Date"];
    $data['instrument'][] = ["name"=>"Inward Date"];
	
    /* Instrument Header Rejected */
    $data['instrumentRej'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['instrumentRej'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['instrumentRej'][] = ["name"=>"Code"];
    $data['instrumentRej'][] = ["name"=>"Description"];
    $data['instrumentRej'][] = ["name"=>"Supplier"];
    $data['instrumentRej'][] = ["name"=>"Price"];
    $data['instrumentRej'][] = ["name"=>"Make"];
    $data['instrumentRej'][] = ["name"=>"Required"];
    $data['instrumentRej'][] = ["name"=>"Frequency<br>(Months)"];
    $data['instrumentRej'][] = ["name"=>"Location"];
    $data['instrumentRej'][] = ["name"=>"Reject Date"];
    $data['instrumentRej'][] = ["name"=>"Reject Reason"];

    /* In Challan Header */
    $data['qcChallan'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['qcChallan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['qcChallan'][] = ["name"=>"Ch. No."];
    $data['qcChallan'][] = ["name"=>"Ch. Date"];
    $data['qcChallan'][] = ["name"=>"Ch. Type"];
    $data['qcChallan'][] = ["name"=>"Handover To"];
    $data['qcChallan'][] = ["name"=>"Issue To"];
    $data['qcChallan'][] = ["name"=>"Code"];
    $data['qcChallan'][] = ["name"=>"Description"];
    $data['qcChallan'][] = ["name"=>"Remark"];

    /* Calibration Item Details*/
    $data['calibrationData'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['calibrationData'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['calibrationData'][] = ["name"=>"Calibration Agency"];
    $data['calibrationData'][] = ["name"=>"Calibration No."];
    $data['calibrationData'][] = ["name"=>"Certificate File"];
    $data['calibrationData'][] = ["name"=>"Remark"];

    /* Running Jobs Header */
	$data['runningJobs'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['runningJobs'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['runningJobs'][] = ["name"=>"Prc No"];
    $data['runningJobs'][] = ["name"=>"Prc Date"];
    $data['runningJobs'][] = ["name"=>"Batch No"];
    $data['runningJobs'][] = ["name"=>"Process"];
    $data['runningJobs'][] = ["name"=>"Item Name"];
	$data['runningJobs'][] = ["name"=>"Prc Qty"];

    /* Line Inspection Header */
	$data['lineInspection'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['lineInspection'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['lineInspection'][] = ["name"=>"Inspection Date"];
    $data['lineInspection'][] = ["name"=>"Inspection Time"];
    $data['lineInspection'][] = ["name"=>"Inspection Type"];
    $data['lineInspection'][] = ["name"=>"Prc No"];
    $data['lineInspection'][] = ["name"=>"Prc Date"];
	$data['lineInspection'][] = ["name"=>"Batch No"];
    $data['lineInspection'][] = ["name"=>"Process"];
    $data['lineInspection'][] = ["name"=>"Item Name"];
    $data['lineInspection'][] = ["name"=>"Operator"];
    $data['lineInspection'][] = ["name"=>"Machine Name"];
    $data['lineInspection'][] = ["name"=>"Sampling Qty"];
	
	/* Pending Fir Header */
	$data['pendingFir'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['pendingFir'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['pendingFir'][] = ["name"=>"Prc No."];
    $data['pendingFir'][] = ["name"=>"Item Name"];
    $data['pendingFir'][] = ["name"=>"Qty"];

    /* Final Inspection Header */
	$data['finalInspection'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['finalInspection'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['finalInspection'][] = ["name"=>"Inspection Date"];
    $data['finalInspection'][] = ["name"=>"Fir No"];
    $data['finalInspection'][] = ["name"=>"Prc No."];
    $data['finalInspection'][] = ["name"=>"Item Name"];
    $data['finalInspection'][] = ["name"=>"Ok Qty"];
    $data['finalInspection'][] = ["name"=>"Rej Qty"];
    $data['finalInspection'][] = ["name"=>"Total Qty"];
	
    /* Sales Invoice For PDI Header */
    $data['pdi'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['pdi'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['pdi'][] = ["name"=>"Inv No."];
	$data['pdi'][] = ["name"=>"Inv Date"];
	$data['pdi'][] = ["name"=>"Customer Name"];
	$data['pdi'][] = ["name"=>"Item"];
	$data['pdi'][] = ["name"=>"Qty"];
	
	/* Vendor Inspection Header */
    $data['vendorInspection'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['vendorInspection'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['vendorInspection'][] = ["name"=>"PRC No"];
    $data['vendorInspection'][] = ["name"=>"Challan No"];
    $data['vendorInspection'][] = ["name"=>"Vendor"];
    $data['vendorInspection'][] = ["name"=>"Product"];
    $data['vendorInspection'][] = ["name"=>"Process"];
    $data['vendorInspection'][] = ["name"=>"In Challan No"];
    $data['vendorInspection'][] = ["name"=>"Ok Qty"];
    $data['vendorInspection'][] = ["name"=>"Receive Date"];

    return tableHeader($data[$page]);
}

/* Instrument Data */
function getInstrumentData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Instrument'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $inward=''; $reject=''; $editButton = '';
    if(empty($data->status)){
        $inwardParam = "{'postData':{'id' : ".$data->id.",'status' : '1'}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'inwardGauge', 'title' : 'Inward Gauge', 'call_function':'inwardGauge', 'fnsave' : 'save'}";
        $inward = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Inward Gauge" flow="down" onclick="modalAction('.$inwardParam.');"><i class="mdi mdi-reply" ></i></a>';
    }elseif($data->status == 1){
        $reject = '<a href="javascript:void(0)" class="btn btn-dark rejectGauge permission-modify" data-id="'.$data->id.'" data-gauge_code="'.$data->item_code.'" datatip="Reject" flow="down"><i class="fa fa-close" ></i></a>';
    
        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editGauge', 'title' : 'Update Gauge', 'fnsave' : 'save', 'call_function' : 'edit'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    }
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkInstChallan" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    
    $calPrintBtn = '<a class="btn btn-warning btn-edit permission-read" href="'.base_url('instrument/printCalHistoryCardData/'.$data->id).'" target="_blank" datatip="Calibration History Card" flow="down"><i class="fas fa-print" ></i></a>';
    $issuePrintBtn = '<a class="btn btn-dark btn-edit permission-read" href="'.base_url('instrument/printIssueHistoryCardData/'.$data->id).'" target="_blank" datatip="Issue History Card" flow="down"><i class="fas fa-print" ></i></a>';
     
    $deleteButton='';
    $action = getActionButton($issuePrintBtn.$calPrintBtn.$reject.$inward.$editButton.$deleteButton);    
    
    $lcd = (!empty($data->last_cal_date)) ? date('d-m-Y',strtotime($data->last_cal_date)) : '';
    $ncd = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-1 days")) : '';
    $pdate = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-".($data->cal_reminder+1)." days")) : '';
    if(!empty($ncd) AND (strtotime($ncd) <= strtotime(date('d-m-Y')))){$ncd = '<strong class="text-danger">'.$ncd.'</strong>';}
	if(!empty($pdate) AND (strtotime($pdate) <= strtotime(date('d-m-Y')))){$pdate = '<strong style="color:#ffbc34;">'.$pdate.'</strong>';}
	
    $itemCode = '<a href="'.base_url("qcChallan/calibrationData/".$data->id).'" datatip="View Details" flow="down">'.$data->item_code.'</a>';

	if(in_array($data->status,[1,5])){	
        return [$action,$data->sr_no,$selectBox,$itemCode,$data->item_name,$data->party_name,$data->price,$data->make_brand,$data->cal_required,$data->cal_freq,$data->location,$lcd,$ncd,$pdate,formatDate($data->created_at)];
	}elseif(in_array($data->status,[4])){
		return [$action,$data->sr_no,$itemCode,$data->item_name,$data->party_name,$data->price,$data->make_brand,$data->cal_required,$data->cal_freq,$data->location,formatDate($data->rejected_at,'d-m-Y h:i'),$data->reject_reason];
	}else{
	    return [$action,$data->sr_no,$itemCode,$data->item_name,$data->party_name,$data->price,$data->make_brand,$data->cal_required,$data->cal_freq,$data->location,$lcd,$ncd,$pdate,formatDate($data->created_at)];
	}
}

/* In-Challan Data */
function getQcChallanData($data){
    $returnBtn=''; $caliBtn=''; $edit=''; $delete='';
    
    if(empty($data->trans_status)){
        if(empty($data->receive_by)){
            $edit = '<a href="'.base_url('qcChallan/edit/'.$data->challan_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

            $deleteParam = "{'postData':{'id' : ".$data->challan_id."},'controller':'qcChallan','message' : 'Challan'}";
            $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

            if($data->challan_type != 3){
                $rtnParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-lg-modal', 'button':'both', 'form_id' : 'returnChallan', 'title' : 'Return Challan', 'call_function' : 'returnChallan', 'fnsave' : 'saveCalibration','controller' : 'qcChallan'}";
                $returnBtn = '<a href="javascript:void(0)" class="btn btn-info permission-write" onclick="modalAction('.$rtnParam.');" datatip="Return" flow="down"><i class="mdi mdi-reply"></i></a>';
            }else{
                $calParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-lg-modal','button' : 'both', 'form_id' : 'getCalibration', 'title' : 'Calibration For [".$data->item_code."]', 'call_function' : 'getCalibration', 'fnsave' : 'saveCalibration', 'controller' : 'qcChallan'}";
                $caliBtn = '<a class="btn btn-info permission-write" href="javascript:void(0)" datatip="Calibration" flow="down" onclick="modalAction('.$calParam.');"><i class="fas fa-tachometer-alt"></i></a>';
            }
        }
    }

    $data->party_name = (!empty($data->party_name))? $data->party_name : 'IN-HOUSE';
    $data->challan_type = (($data->challan_type==1)? 'IN-House Issue' : (($data->challan_type==2) ? 'Vendor Issue':'Calibration'));
        
    $printBtn = '<a class="btn btn-info btn-edit" href="'.base_url('qcChallan/printChallan/'.$data->challan_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    
    $action = getActionButton($printBtn.$caliBtn.$returnBtn.$edit.$delete);
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->challan_type,$data->handover_to,$data->party_name,$data->item_code,$data->item_name,$data->item_remark];
}

/* Get Calibration Table Data */
function getCalibration($data){  
	$caliParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'getCalibration', 'title' : 'Calibration For [".$data->item_code."]', 'button' : 'both','call_function' : 'getCalibration', 'fnsave' : 'saveCalibration','controller' : 'qcChallan'}";
    $caliBtn = '<a class="btn btn-success btn-contact permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$caliParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    
	$download = '';
    if(!empty($data->certificate_file)){
		$download ='<a href="'.base_url('assets/uploads/instrument/'.$data->certificate_file).'" target="_blank"><i class="fa fa-download"></i></a>';
    }                                
    
    $action = getActionButton($caliBtn);  
    $party_name = !empty($data->party_name) ? $data->party_name : 'IN-HOUSE';
    return[$action,$data->sr_no,$party_name,$data->in_ch_no,$download,$data->item_remark];
}


/*Running Jobs Table Data */
function getRunningJobsData($data){ 
	$reportParam = "{'postData':{'prc_id' : ".$data->prc_id.",'process_id' : ".$data->process_id.", 'control_method' : 'IPR', 'report_type' : '1'},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'iirInsp', 'title' : 'Line Inspection','call_function':'addLineInspection','fnsave':'saveLineInspection'}";
	$lineInspection = '<a href="javascript:void(0)" type="button" class="btn btn-info permission-modify" datatip="Line Inspection" flow="down" onclick="modalAction('.$reportParam.');"><i class="fa fa-file-alt"></i></a>';

    $setupReportParam = "{'postData':{'prc_id' : ".$data->prc_id.",'process_id' : ".$data->process_id.", 'control_method' : 'SAR', 'report_type' : '4'},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'addFirstPieceSAR', 'title' : 'First Piece Inspection (SAR)', 'call_function' : 'addFirstPieceSAR','fnsave':'saveLineInspection'}";
	$setupReportBtn = '<a href="javascript:void(0)" type="button" class="btn btn-dribbble permission-modify" datatip="First Piece Inspection (SAR)" flow="down" onclick="modalAction('.$setupReportParam.');"><i class="fa fa-file-alt"></i></a>';

    $action = getActionButton($lineInspection.$setupReportBtn);  
	return[$action,$data->sr_no,$data->prc_number,formatDate($data->prc_date),$data->batch_no,$data->process_name,$data->item_name,$data->prc_qty];
}

/*Line Inspection Table Data */
function getLineInspectionData($data){	
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Line Inspection'}";
    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $lastPieceInsp = '';
    if (!empty($data->report_type) && in_array($data->report_type,[4,5])) {
        if (empty($data->ref_id)) {
            $lastPieceParam = "{'postData':{'id' : ".$data->id.", 'prc_id' : '".$data->prc_id."', 'process_id' : '".$data->process_id."', 'control_method' : 'SAR', 'report_type' : '5'},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'addFirstPieceSAR', 'title' : 'Last Piece Inspection (SAR)', 'call_function' : 'addFirstPieceSAR','fnsave':'saveLineInspection'}";
            $lastPieceInsp = '<a href="javascript:void(0)" type="button" class="btn btn-primary permission-modify" datatip="Last Piece Inspection (SAR)" flow="down" onclick="modalAction('.$lastPieceParam.');"><i class="fa fa-file-alt"></i></a>';
        }

        $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'editFirstPieceSAR', 'title' : 'Update First Piece Inspection (SAR)', 'fnsave' : 'saveLineInspection', 'call_function' : 'editFirstPieceSAR'}";
        $edit = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
        
        $printBtn = '<a class="btn btn-info btn-edit" href="'.base_url('lineInspection/printSAR/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    } else {
        $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'editIPR', 'title' : 'Update Line Inspection', 'fnsave' : 'saveLineInspection', 'call_function' : 'editLineInspection'}";
        $edit = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
        
        $printBtn = '<a class="btn btn-info btn-edit" href="'.base_url('lineInspection/printLineInspection/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    }
   
	$action = getActionButton($printBtn.$lastPieceInsp.$edit.$delete);
		
    return[$action,$data->sr_no,formatDate($data->insp_date),$data->insp_time,(in_array($data->report_type,[4,5]) ? 'SAR' : 'IPR'),$data->prc_number,formatDate($data->prc_date),$data->batch_no,$data->process_name,$data->item_name,$data->emp_name,$data->machine_name,$data->sampling_qty];
}

/*Pending Fir Table Data */
function getPendingFirData($data){ 
	$reportParam = "{'postData':{'id' : ".$data->id.",'main_ref_id':".$data->main_ref_id."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'firInsp', 'title' : 'Final Inspection','call_function':'AddFinalInspection','fnsave':'saveFinalInspection'}";
	$finalInspection = '<a href="javascript:void(0)" type="button" class="btn btn-info permission-modify" datatip="Final Inspection" flow="down" onclick="modalAction('.$reportParam.');"><i class="fa fa-file-alt"></i></a>';

    $action = getActionButton($finalInspection);  
    return[$action,$data->sr_no,$data->prc_number,$data->item_name,$data->qty];
}

/*Final Inspection Table Data */
function getFinalInspectionData($data){ 
	$totlQty = "";
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Final Inspection'}";
    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $printBtn = '<a class="btn btn-info btn-edit" href="'.base_url('finalInspection/printFinalInspection/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
	$totlQty = ($data->ok_qty + $data->rej_found);
   
	$action = getActionButton($printBtn.$delete);
    return[$action,$data->sr_no,formatDate($data->insp_date),$data->trans_number,$data->prc_number,$data->item_name,$data->ok_qty,$data->rej_found,$totlQty];
}

/* Sales Invoice Table Data  For PDI*/
function getPdiReportData($data){
    $pdiParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'pdiReport', 'title' : 'Generate PDI Report', 'call_function' : 'addPDI', 'fnsave' : 'save', 'js_store_fn' : 'customStore'}";
    $pdiBtn = '<a href="javascript:void(0)" class="btn btn-danger" datatip="Generate Report" flow="down" onclick="modalAction('.$pdiParam.');"><i class="fas fa-clipboard-check"></i></a>';
	
	$print="";
	if($data->trans_status == 3):
		$urlData = ['so_trans_id'=>$data->id,'pdi_type'=>$data->pdi_type,'party_logo'=>$data->party_logo];
		$print = '<a class="btn btn-info btn-edit" href="'.base_url('pdi/printPdi/'.encodeURL($urlData)).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print"></i></a>';
	endif;

    $action = getActionButton($pdiBtn.$print);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->item_name,$data->qty];
}

/*Vendor Inspection Table Data */
function getVendorInspectionData($data){
	$vendorInspection = $deleteButton = $vendorInspPrintBtn = "";
	if(empty($data->rqc_status)):
		$reportParam = "{'postData':{'id':".$data->id.",'prc_id' : ".$data->prc_id.",'process_id' : ".$data->process_id.", 'challan_no' : '".$data->challan_no."','vendor_id':".$data->vendor_id."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'iirInsp', 'title' : 'Vendor Inspection','call_function':'AddVendorInspection','fnsave':'saveVendorInspection'}";
		$vendorInspection = '<a href="javascript:void(0)" type="button" class="btn btn-info permission-modify" datatip="Vendor Inspection" flow="down" onclick="modalAction('.$reportParam.');"><i class="fa fa-file-alt"></i></a>';
	else:
		$vendorInspPrintBtn = '<a class="btn btn-warning btn-edit permission-read" href="'.base_url('vendorInspection/printVendorInspection/'.$data->id).'" target="_blank" datatip="Vendor Inspection" flow="down"><i class="fas fa-print" ></i></a>';
		
		$deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Vendor Inspection'}";
		$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	endif;
	
    $download = '';
    if(!empty($data->inspection_file)){
        $download = '<a class="btn btn-info" href="'.base_url("assets/uploads/inspection/".$data->inspection_file).'" target="_blank" datatip="Inspection Report" flow="down"><i class="fa fa-download" ></i></a>';
    }

    $action = getActionButton($vendorInspection.$download.$vendorInspPrintBtn.$deleteButton);  
    return[$action,$data->sr_no,$data->prc_number,$data->challan_no,$data->party_name,$data->item_name,$data->process_name,$data->in_challan_no,$data->qty,formatDate($data->trans_date)];
}
?>