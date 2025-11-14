<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getAdminDtHeader($page){
    /* Client Master Header */
    $data['clientMaster'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['clientMaster'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['clientMaster'][] = ["name"=>"Company Name"];
    $data['clientMaster'][] = ["name"=>"Contact Person"];
    $data['clientMaster'][] = ["name"=>"Contact No."];
    $data['clientMaster'][] = ["name"=>"Contact Email"];
    $data['clientMaster'][] = ["name"=>"GST No."];
    $data['clientMaster'][] = ["name"=>"City"];

    return tableHeader($data[$page]);
}

function getClientMasterData($data){
    /* $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Terms'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editTerms', 'title' : 'Update Terms','call_function':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>'; */
	
	$action = getActionButton("");
    return [$action,$data->sr_no,$data->company_name,$data->company_contact_person,$data->company_phone,$data->company_email,$data->company_gst_no,$data->company_city];
}