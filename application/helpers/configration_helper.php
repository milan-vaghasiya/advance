<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getConfigDtHeader($page){
    /* terms header */
    $data['terms'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['terms'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['terms'][] = ["name"=>"Title"];
    $data['terms'][] = ["name"=>"Type"];
    $data['terms'][] = ["name"=>"Conditions"];

    /* Transport Header*/
    $data['transport'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['transport'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['transport'][] = ["name"=>"Transport Name"];
    $data['transport'][] = ["name"=>"Transport ID"];
    $data['transport'][] = ["name"=>"Address"];

    /* HSN Master header */
    $data['hsnMaster'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['hsnMaster'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['hsnMaster'][] = ["name"=>"HSN"];
    $data['hsnMaster'][] = ["name"=>"CGST"];
    $data['hsnMaster'][] = ["name"=>"SGST"];
    $data['hsnMaster'][] = ["name"=>"IGST"];
    $data['hsnMaster'][] = ["name"=>"Description"];

    /* Material Grade header */
    $data['materialGrade'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['materialGrade'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['materialGrade'][] = ["name"=>"Material Grade"];
    $data['materialGrade'][] = ["name"=>"Standard"];
    $data['materialGrade'][] = ["name"=>"Colour Code"];

    /* Scrap Group Header*/
    $data['scrapGroup'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['scrapGroup'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['scrapGroup'][] = ["name"=>"Scrap Group Name"];
    $data['scrapGroup'][] = ["name"=>"Unit Name"];

    /* Vehicle Type header */
    $data['vehicleType'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['vehicleType'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['vehicleType'][] = ["name"=>"Vehicle Type"];
    $data['vehicleType'][] = ["name"=>"Remark"];

    /* Tax Master Header */
    $data['taxMaster'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['taxMaster'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['taxMaster'][] = ["name" => "Tax Name"];
    $data['taxMaster'][] = ["name" => "Tax Type"];
    $data['taxMaster'][] = ["name" => "Calcu. Type"];
    $data['taxMaster'][] = ["name" => "Ledger Name"];
    $data['taxMaster'][] = ["name" => "Is Active"];
    $data['taxMaster'][] = ["name" => "Add/Deduct"];

    /* Expense Master Header */
    $data['expenseMaster'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['expenseMaster'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['expenseMaster'][] = ["name" => "Exp. Name"];
    $data['expenseMaster'][] = ["name" => "Entry Name"];
    $data['expenseMaster'][] = ["name" => "Sequence"];
    $data['expenseMaster'][] = ["name" => "Calcu. Type"];
    $data['expenseMaster'][] = ["name" => "Ledger Name"];
    $data['expenseMaster'][] = ["name" => "Is Active"];
    $data['expenseMaster'][] = ["name" => "Add/Deduct"];

    /* Tax Class Header */
    $data['taxClass'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['taxClass'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['taxClass'][] = ["name" => "Class Name"];
    $data['taxClass'][] = ["name" => "Type"];
    $data['taxClass'][] = ["name" => "Ledger Name"];
    $data['taxClass'][] = ["name" => "Is Active"];

    /* Group Master Header */
    $data['groupMaster'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['groupMaster'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['groupMaster'][] = ["name" => "Group Code"];
    $data['groupMaster'][] = ["name" => "Group Name"];
    $data['groupMaster'][] = ["name" => "Perent Group Name"];
    $data['groupMaster'][] = ["name" => "Nature"];
    $data['groupMaster'][] = ["name" => "Effect IN"];

    /* Test Head header */
    $data['testType'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['testType'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['testType'][] = ["name"=>"Test Name"];

    /* Currency Master header */
	$data['currencyMaster'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"srnoPosition"=>0];
    $data['currencyMaster'][] = ["name"=>"Currency Name"];
    $data['currencyMaster'][] = ["name"=>"Code"];
    $data['currencyMaster'][] = ["name"=>"Symbol"];
    $data['currencyMaster'][] = ["name"=>"Rate in INR","sortable"=>FALSE];

    return tableHeader($data[$page]);
}

/* Terms Table Data */
function getTermsData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Terms'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editTerms', 'title' : 'Update Terms','call_function':'edit','txt_editor' : 'conditions'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title,$data->type,$data->conditions];
}

/* Transport Data */
function getTransportData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Transport'}";
    $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-md-modal', 'form_id' : 'editTransport', 'title' : 'Update Transport','call_function':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->transport_name,$data->transport_id,$data->address];
}

/* HSN Master Table Data */
function getHSNMasterData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'HSN Master'}";
    $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-md-modal', 'form_id' : 'editHsnMaster', 'title' : 'Update HSN Master','call_function':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
	$action = getActionButton($deleteButton);
    return [$action,$data->sr_no,$data->hsn,$data->cgst,$data->sgst,$data->igst,$data->description];
}

/* Material Grade Table Data */
function getMaterialData($data){  
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Material Grade'}";
    $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-md-modal', 'form_id' : 'editMaterialGrade', 'title' : 'Update Material Grade','call_function':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    //16-11-24
    $approveBtn=''; $insBtn = '';$reOpenBtn ="";
    if(empty($data->approve_by)){
        $insParam = "{'postData':{'id' : ".$data->id."},'button' : 'both', 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'tcParameter', 'title' : 'TC Parameter','call_function':'getInspectionParam', 'fnedit' : 'getInspectionParam', 'fnsave' : 'saveInspectionParam'}";

        $approveParam = "{'postData':{'id' : ".$data->id.",'approve_by':'1'}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'tcParameter', 'title' : 'Approve TC Parameter','call_function':'getInspectionParam', 'fnedit' : 'getInspectionParam', 'fnsave' : 'saveInspectionParam', 'savebtn_text':'Approve','savebtn_icon':'fa fa-check'}";
        $approveBtn = '<a class="btn btn-primary btn-approval" href="javascript:void(0)" datatip="Approve" flow="down" onclick="modalAction('.$approveParam.');"><i class="fa fa-check"></i></a>';
    }
    else{
        $insParam = "{'postData':{'id' : ".$data->id."},'button' : 'both', 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'tcParameter', 'title' : 'TC Parameter','call_function':'getInspectionParam', 'fnedit' : 'getInspectionParam', 'fnsave' : 'saveInspectionParam','button':'close'}";
    
        $reOpenParam = "{'postData':{'id' : ".$data->id.",'approve_by':0,'msg':'Re Open'},'fnsave':'reOpenTcParam','message':'Are you sure want to Re Open this Tc Parameter?'}";
        $reOpenBtn = '<a class="btn btn-dark permission-modify" href="javascript:void(0)" datatip="Re-Open" flow="down" onclick="confirmStore('.$reOpenParam.');"><i class="mdi mdi-close"></i></a>';
    }

    $insBtn = '<a class="btn btn-info btn-edit" href="javascript:void(0)" datatip="TC Parameter" flow="down" onclick="modalAction('.$insParam.');"><i class="fa fa-file"></i></a>';
	//$action = getActionButton($approveBtn.$reOpenBtn.$insBtn.$editButton.$deleteButton);
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->material_grade,$data->standard,$data->color_code];
}

/* Scrap Group Data */
function getScrapGroupData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Scrap Group'}";
    $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-md-modal', 'form_id' : 'editScrap', 'title' : 'Update Scrap Group','call_function':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->item_name,$data->uom];
}

/* Vehicle Type Data */
function getVehicleTypeData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Vehicle Type'}";
    $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-md-modal', 'form_id' : 'editVehicleType', 'title' : 'Update Vehicle Type','call_function':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->vehicle_type,$data->remark];
}

/* Tax Master Table Data */
function getTaxMasterData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Tax'}";
    $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editTax', 'title' : 'Update Tax','call_function':'edit'}";
    
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    $deleteButton = "";

    $action = getActionButton($editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->name,$data->tax_type_name,$data->calc_type_name,$data->acc_name,$data->is_active_name,$data->add_or_deduct_name];
}

/* Expense Master Table Data */
function getExpenseMasterData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Expense'}";
    $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editExpense', 'title' : 'Update Expense','call_function':'edit'}";
    

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->exp_name,$data->entry_name,$data->seq,$data->calc_type_name,$data->party_name,$data->is_active_name,$data->add_or_deduct_name];
}

/* Tax Class Table Data */
function getTaxClassData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Tax Class'}";
    $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editTaxClass', 'title' : 'Update Tax Class','call_function':'edit'}";
    

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->tax_class_name,$data->sp_type_name,$data->sp_acc_name,$data->is_active_name];
}

/* Group Master Table data */
function getGroupMasterData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Group'}";
    $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-md-modal', 'form_id' : 'editGroup', 'title' : 'Update Group','call_function':'edit'}";
    

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    if(!empty($data->is_default)):
        $editButton = $deleteButton = "";
    endif;

    $action = getActionButton($editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->group_code,$data->name,$data->perent_group_name,$data->nature,$data->bs_type_name];
}

function getRmTestTypeData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Test Type'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editTestType', 'title' : 'Update Test Type'}";
	$parameterBom = "{'postData':{'id' : '".$data->id."'},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'testParameter', 'title' : 'Test Parameter For ".$data->test_name."','call_function':'addTestParameter','fnsave':'saveTestParam','button':'close','js_store_fn':'customStore','fnget':'getTestParaHtml'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
    $testParameter = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Test Parameter" flow="down" onclick="modalAction('.$parameterBom.');"><i class="fa fa-plus"></i></a>';
	
	$action = getActionButton($testParameter.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->test_name];
}

/* Currency Master Table Data */
function getCurrencyMasterData($data){
    return [$data->sr_no,$data->currency_name,$data->currency,$data->code2000,$data->inrinput];
}
?>