<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function getExportDtHeader($page){
    /* Packing List Header */
    $data['packingList'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['packingList'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['packingList'][] = ["name"=>"Pck. No."];
    $data['packingList'][] = ["name"=>"Pck. Date"];
    $data['packingList'][] = ["name"=>"Customer Name"];
    $data['packingList'][] = ["name"=>"Total Box"];
    $data['packingList'][] = ["name"=>"Total Pallets"];
    $data['packingList'][] = ["name"=>"Total Net Weight"];
    $data['packingList'][] = ["name"=>"Total Gross Weight"];

    /* Commercial Invoice Header */
    $data['commercialInvoice'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['commercialInvoice'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['commercialInvoice'][] = ["name"=>"Inv. No."];
    $data['commercialInvoice'][] = ["name"=>"Inv. Date"];
    $data['commercialInvoice'][] = ["name"=>"Customer Name"];
    $data['commercialInvoice'][] = ["name"=>"Net Amount"];

    /* Commercial Invoice Header */
    $data['customInvoice'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['customInvoice'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['customInvoice'][] = ["name"=>"Inv. No."];
    $data['customInvoice'][] = ["name"=>"Inv. Date"];
    $data['customInvoice'][] = ["name"=>"Customer Name"];
    $data['customInvoice'][] = ["name"=>"Net Amount"];

    return tableHeader($data[$page]);
}

/* Packing List Table Data */
function getPackingListData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('packingList/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Packing List'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $print = '<a href="'.base_url('packingList/printPackingList/'.$data->id).'" class="btn btn-info btn-edit permission-approve1" datatip="Print Packing List" flow="down" target="_balnk"><i class="fa fa-print"></i></a>';

    $commercialInv = '<a href="'.base_url('commercialInvoice/create/'.$data->id).'" class="btn btn-primary" datatip="Create Com. Inv." flow="down" target="_blank"><i class="fa fa-file-text"></i></a>';

    if($data->trans_status == 1): $commercialInv = $editButton = $deleteButton = ""; endif;

    $action = getActionButton($print.$commercialInv.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,$data->trans_date,$data->party_name,$data->total_box,$data->total_pallets,$data->net_weight,$data->gross_weight];
}

/* Commercial Invoice Table Data */
function getCommercialInvoiceData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('commercialInvoice/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Commercial Invoice'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $print = '<a href="'.base_url('commercialInvoice/printCommercialInvoice/'.$data->id).'" class="btn btn-info btn-edit permission-approve1" datatip="Print Com. Inv." flow="down" target="_blank"><i class="fa fa-print"></i></a>';

    $customInv = '<a href="'.base_url('customInvoice/create/'.$data->id).'" class="btn btn-primary" datatip="Create Com. Inv." flow="down" target="_blank"><i class="fa fa-file-text"></i></a>';

    if($data->trans_status == 1): $editButton = $deleteButton = ""; endif;

    $action = getActionButton($print.$customInv.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,$data->trans_date,$data->party_name,$data->net_amount];
}

/* Commercial Invoice Table Data */
function getCustomInvoiceData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('customInvoice/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Custom Invoice'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $print = '<a href="'.base_url('customInvoice/printCustomInvoice/'.$data->id).'" class="btn btn-info btn-edit permission-approve1" datatip="Print Cust. Inv." flow="down" target="_blank"><i class="fa fa-print"></i></a>';

    if($data->trans_status == 1): $editButton = $deleteButton = ""; endif;

    $action = getActionButton($print.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,$data->trans_date,$data->party_name,$data->net_amount];
}