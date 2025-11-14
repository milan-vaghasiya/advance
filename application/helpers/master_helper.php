<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getMasterDtHeader($page){
    /* Customer Header */
    $data['customer'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['customer'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['customer'][] = ["name"=>"Company Name"];
	$data['customer'][] = ["name"=>"Contact Person"];
    $data['customer'][] = ["name"=>"Contact No."];
    $data['customer'][] = ["name"=>"Party Code"];
    $data['customer'][] = ["name"=>"Currency"];

    /* Supplier Header */
    $data['supplier'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['supplier'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['supplier'][] = ["name"=>"Company Name"];
	$data['supplier'][] = ["name"=>"Contact Person"];
    $data['supplier'][] = ["name"=>"Contact No."];
    $data['supplier'][] = ["name"=>"Party Code"];

    /* Vendor Header */
    $data['vendor'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['vendor'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['vendor'][] = ["name"=>"Company Name"];
	$data['vendor'][] = ["name"=>"Contact Person"];
    $data['vendor'][] = ["name"=>"Contact No."];
    $data['vendor'][] = ["name"=>"Party Address"];
    $data['vendor'][] = ["name"=>"Party Code"];

    /* Ledger Header */
    $data['ledger'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['ledger'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['ledger'][] = ["name"=>"Ledger Name"];
    $data['ledger'][] = ["name"=>"Group Name"];
    $data['ledger'][] = ["name"=>"Op. Balance"];
    $data['ledger'][] = ["name"=>"Cl. Balance"];

    /* Item Category Header */
    $data['itemCategory'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['itemCategory'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['itemCategory'][] = ["name"=>"Code"];//25-11-24
    $data['itemCategory'][] = ["name"=>"Category Name"];
    $data['itemCategory'][] = ["name"=>"Parent Category"];
    $data['itemCategory'][] = ["name"=>"Is Final ?"];
    $data['itemCategory'][] = ["name"=>"Is Returnable?"];//25-11-24
    $data['itemCategory'][] = ["name"=>"Dimensional Inspection"];
    $data['itemCategory'][] = ["name"=>"Remark"];

    /* Finish Goods Header */
    $data['finish_goods'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['finish_goods'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['finish_goods'][] = ["name"=>"Item Code"];
    $data['finish_goods'][] = ["name"=>"Item Name"];
    $data['finish_goods'][] = ["name"=>"Category"];
    $data['finish_goods'][] = ["name"=>"UOM"];
    $data['finish_goods'][] = ["name"=>"HSN Code"];
    $data['finish_goods'][] = ["name"=>"GST (%)"];
	$data['finish_goods'][] = ["name"=>"MFG Status"];
    $data['finish_goods'][] = ["name"=>"MFG Type"];
    $data['finish_goods'][] = ["name"=>"Created By/At"];
    $data['finish_goods'][] = ["name"=>"Updated By/At"];

    /* Row Material Header */
    $data['raw_material'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['raw_material'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['raw_material'][] = ["name"=>"Item Code"];
    $data['raw_material'][] = ["name"=>"Item Name"];
    $data['raw_material'][] = ["name"=>"Material Grade"];
    $data['raw_material'][] = ["name"=>"Category"];
    $data['raw_material'][] = ["name"=>"UOM"];
    $data['raw_material'][] = ["name"=>"HSN Code"];
    $data['raw_material'][] = ["name"=>"GST (%)"];
    $data['raw_material'][] = ["name"=>"Created By/At"];
    $data['raw_material'][] = ["name"=>"Updated By/At"];

    /* Consumable Header */
    $data['consumable'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['consumable'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['consumable'][] = ["name"=>"Item Code"];
    $data['consumable'][] = ["name"=>"Item Name"];
    $data['consumable'][] = ["name"=>"Category"];
    $data['consumable'][] = ["name"=>"UOM"];
    $data['consumable'][] = ["name"=>"HSN Code"];
    $data['consumable'][] = ["name"=>"GST (%)"];
    $data['consumable'][] = ["name"=>"Created By/At"];
    $data['consumable'][] = ["name"=>"Updated By/At"];
	
	/* Machine Master Header */
    $data['machineries'][] = ["name"=>"Action","class"=>"no_filter noExport","sortable"=>FALSE,"textAlign"=>"center"];
	$data['machineries'][] = ["name"=>"#","class"=>"no_filter","sortable"=>FALSE,"textAlign"=>"center"];
    $data['machineries'][] = ["name"=>"Machine Code"];
    $data['machineries'][] = ["name"=>"Machine Name"];
    $data['machineries'][] = ["name"=>"Make/Brand"];
    $data['machineries'][] = ["name"=>"Serial No."];
    $data['machineries'][] = ["name"=>"Installed On"];
    $data['machineries'][] = ["name"=>"Preventive Maintenance?"];
    $data['machineries'][] = ["name"=>"Created By/At"];
    $data['machineries'][] = ["name"=>"Updated By/At"];

	/* Countries Table Header */
    $data['country'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['country'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['country'][] = ["name"=>"Country"];

    /* states Table Header */
    $data['states'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['states'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['states'][] = ["name"=>"States"];

    /* cities Table Header */
    $data['cities'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['cities'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['cities'][] = ["name"=>"Country"];
	$data['cities'][] = ["name"=>"States"];
	$data['cities'][] = ["name"=>"Cities"];

    /** Custom Field Data */
    $data['customField'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['customField'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['customField'][] = ["name"=>"Field"];
    $data['customField'][] = ["name"=>"Field Type"];

    /* Custom Option Header */
    $data['customOption'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['customOption'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['customOption'][] = ["name"=>"Type"];
    $data['customOption'][] = ["name"=>"Title"];

    /* Item Price Structure Header */
    $data['itemPriceStructure'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE,"style"=>""];
	$data['itemPriceStructure'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE,"style"=>""];
    $data['itemPriceStructure'][] = ["name"=>"Structure Name"];
    $data['itemPriceStructure'][] = ["name"=>"Item Namne"];
    $data['itemPriceStructure'][] = ["name"=>"Category Name"];
    $data['itemPriceStructure'][] = ["name"=>"GST (%)"];
    $data['itemPriceStructure'][] = ["name"=>"MRP"];
    $data['itemPriceStructure'][] = ["name"=>"Price"];
    $data['itemPriceStructure'][] = ["name"=>"Dealer MRP"];
    $data['itemPriceStructure'][] = ["name"=>"Dealer Price"];
    $data['itemPriceStructure'][] = ["name"=>"Retail MRP"];
    $data['itemPriceStructure'][] = ["name"=>"Retail Price"];

    /* Gauge & Instrument Header */
    $data['gauges_instruments'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['gauges_instruments'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['gauges_instruments'][] = ["name"=>"Item Code"];
    $data['gauges_instruments'][] = ["name"=>"Item Name"];
    $data['gauges_instruments'][] = ["name"=>"Category"];
    $data['gauges_instruments'][] = ["name"=>"UOM"];
    $data['gauges_instruments'][] = ["name"=>"HSN Code"];
    $data['gauges_instruments'][] = ["name"=>"GST (%)"];
    $data['gauges_instruments'][] = ["name"=>"Permissible Error"];
    $data['gauges_instruments'][] = ["name"=>"Cali. Req?"];
    $data['gauges_instruments'][] = ["name"=>"Cali.Frequency(Month)"];
    $data['gauges_instruments'][] = ["name"=>"Cali.Reminder(Days Before)"];
    $data['gauges_instruments'][] = ["name"=>"Created By/At"];
    $data['gauges_instruments'][] = ["name"=>"Updated By/At"];

    /* Die Blocks Header */
    $data['die_blocks'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['die_blocks'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['die_blocks'][] = ["name"=>"Item Code"];
    $data['die_blocks'][] = ["name"=>"Item Name"];
    $data['die_blocks'][] = ["name"=>"Material Grade"];
    $data['die_blocks'][] = ["name"=>"Category"];
    $data['die_blocks'][] = ["name"=>"UOM"];
    $data['die_blocks'][] = ["name"=>"HSN Code"];
    $data['die_blocks'][] = ["name"=>"GST (%)"];
    $data['die_blocks'][] = ["name"=>"Created By/At"];
    $data['die_blocks'][] = ["name"=>"Updated By/At"];
	
	/* Packing Material Header */
    $data['packing_material'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['packing_material'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['packing_material'][] = ["name"=>"Item Code"];
    $data['packing_material'][] = ["name"=>"Item Name"];
    $data['packing_material'][] = ["name"=>"Category"];
    $data['packing_material'][] = ["name"=>"UOM"];
    $data['packing_material'][] = ["name"=>"HSN Code"];
    $data['packing_material'][] = ["name"=>"GST (%)"];
    $data['packing_material'][] = ["name"=>"Created By/At"];
    $data['packing_material'][] = ["name"=>"Updated By/At"];

    /* ECN Check List Table Header */
    $data['ecnCheckList'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['ecnCheckList'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['ecnCheckList'][] = ["name"=>"Check Point"];

    /* ECN  Table Header */
    $data['ecn'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['ecn'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['ecn'][] = ["name"=>"ECN Type"];
    $data['ecn'][] = ["name"=>"ECN No"];
    $data['ecn'][] = ["name"=>"ECN Date"];
    $data['ecn'][] = ["name"=>"Item"];
    $data['ecn'][] = ["name"=>"Drawing No"];
    $data['ecn'][] = ["name"=>"Cust. Rev. No"];
    $data['ecn'][] = ["name"=>"Cust. Rev. Date"];
    $data['ecn'][] = ["name"=>"Rev No"];
    $data['ecn'][] = ["name"=>"Rev Date"];
	
    /* Fixture Header */
    $data['fixture'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['fixture'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['fixture'][] = ["name"=>"Item Code"];
    $data['fixture'][] = ["name"=>"Item Name"];
    $data['fixture'][] = ["name"=>"Category"];
    $data['fixture'][] = ["name"=>"Product"];
    $data['fixture'][] = ["name"=>"Process"];
    $data['fixture'][] = ["name"=>"UOM"];
    $data['fixture'][] = ["name"=>"HSN Code"];
    $data['fixture'][] = ["name"=>"GST (%)"];
    $data['fixture'][] = ["name"=>"Created By/At"];
    $data['fixture'][] = ["name"=>"Updated By/At"];

    return tableHeader($data[$page]);
}

function getPartyData($data){
    
	$editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : '".(($data->table_status!=4)?"bs-right-lg-modal":"bs-right-md-modal")."', 'form_id' : 'edit".$data->party_category_name."', 'title' : 'Update ".$data->party_category_name."','call_function':'edit'}";
    $editButton = '<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    
	$deleteParam = "{'postData':{'id' : ".$data->id."},'message' : '".$data->party_category_name."'}";
	$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $gstJsonBtn="";$contactBtn=""; $settingBtn = "";
	
    if(in_array($data->table_status,[1,2,3])):
        $settingParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editPartySettings', 'title' : 'Update Party Settings','call_function':'editPartySettings'}";
        $settingBtn = '<a class="btn btn-dark btn-edit permission-modify" href="javascript:void(0)" datatip="Settings" flow="down" onclick="modalAction('.$settingParam.');"><i class="mdi mdi-cogs fs-18"></i></a>';

		$contactParam = "{'postData':{'party_id' : ".$data->id."},'modal_id' : 'modal-lg', 'call_function':'updatePartyContact', 'fnsave' : 'savePartyContact', 'form_id' : 'partyContactForm', 'title' : 'Update Party Contact','button':'close'}";
		$contactBtn = '<a class="btn btn-info btn-contact permission-modify" href="javascript:void(0);" datatip="Party Contact" flow="down" onclick="modalAction('.$contactParam.');"><i class="fa fa-address-book"></i></a>';
    endif;
	
	$download = "";
    if(in_array($data->table_status,[2,3])):
        if(!empty($data->assesment_file)){
            $download = '<a href="'.base_url('assets/uploads/party_assesment/'.$data->assesment_file).'" class="btn btn-primary" target="_blank"><i class="fas fa-download"></i></a>';
        }
    endif;
	
	$storeBtn='';
    if($data->party_category == 3 && empty($data->location_id)):
        $storeParam = "{'postData':{'id' : ".$data->id.", 'store_level' : 3, 'party_name' : '".$data->party_name."'}, 'fnsave' : 'createVendorStore', 'message' : 'Are you sure want to Create Store for this Vendor?'}";
        $storeBtn = '<a class="btn btn-success permission-modify" href="javascript:void(0)" datatip="Create Vendor Store" flow="down" onclick="confirmStore('.$storeParam.');"><i class="mdi mdi-check"></i></a>';    
    endif;

    $action = getActionButton($contactBtn.$settingBtn.$gstJsonBtn.$download.$storeBtn.$editButton.$deleteButton);

    if($data->table_status == 1):
        $responseData = [$action,$data->sr_no,$data->party_name,$data->contact_person,$data->party_mobile,$data->party_code,$data->currency];
    elseif($data->table_status == 2):
        $responseData = [$action,$data->sr_no,$data->party_name,$data->contact_person,$data->party_mobile,$data->party_code];
    elseif($data->table_status == 3):
        $responseData = [$action,$data->sr_no,$data->party_name,$data->contact_person,$data->party_mobile,$data->party_address,$data->party_code];
    else:
        if($data->system_code != ""):
            $gstJsonBtn = $editButton = $deleteButton = "";
        endif;

        if(in_array($data->group_code,["SC","SD"])):
            $gstJsonBtn = $editButton = $deleteButton = "";
        endif;

        $action = getActionButton($contactBtn.$gstJsonBtn.$editButton.$deleteButton);

        $responseData = [$action,$data->sr_no,$data->party_name,$data->group_name,$data->op_balance,$data->cl_balance];
    endif;

    return $responseData;
}

function getItemCategoryData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Item Category'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editItemCategory', 'title' : 'Update Item Category','call_function':'edit'}";

    $editButton=''; $deleteButton='';
	if(!empty($data->ref_id)):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    endif;

    $cat_code ='';
	if($data->ref_id ==6 || $data->ref_id == 7):
        $cat_code = (!empty($data->tool_type))?'['.str_pad($data->tool_type,3,'0',STR_PAD_LEFT).'] ':'';
    endif;

    if($data->final_category == 0):
        $data->category_name = $cat_code.'<a href="' . base_url("itemCategory/list/" . $data->id) . '">' . $data->category_name . '</a>';
    else:
        $data->category_name = $cat_code.$data->category_name;
    endif;

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->category_code,$data->category_name,$data->parent_category_name,$data->is_final_text,$data->is_returnable_text,$data->is_inspection_text,$data->remark];//25-11-24
}

function getProductData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : '".$data->item_type_text."'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editItem', 'title' : 'Update ".$data->item_type_text."','call_function':'edit'}";    
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    
	$printButton =""; $activityButton = "";
    
    if($data->item_type == 1){
        $printParam = "{'postData':{'item_id' : ".$data->id."}, 'modal_id' : 'modal-md', 'form_id' : 'cpPrint', 'title' : 'Print Control Plan','call_function':'printControlPlan','button':'close'}";
        $printButton = '<a class="btn btn-dribbble btn-edit permission-modify" href="javascript:void(0)" datatip="Print Control Plan" flow="down" onclick="modalAction('.$printParam.');"><i class="fas fa-print" ></i></a>';
    }elseif($data->item_type == 5 && $data->prev_maint_req == 'Yes'){
        $activityParam = "{'postData':{'machine_id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'setActivity', 'title' : 'Preventive Maintenance Checklist', 'call_function' : 'setActivity', 'fnsave' : 'saveActivity', 'controller' : 'machineActivities'}";
		$activityButton = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Machine Activity" flow="down" onclick="modalAction('.$activityParam.');"><i class="mdi mdi-checkbox-marked"></i></a>';
    }

    $action = getActionButton($activityButton.$printButton.$editButton.$deleteButton);
    if($data->item_type == 1){
        $data->item_name = '<a href="'.base_url("items/itemDetails/".$data->id).'" datatip="View Item Details" flow="down">'.$data->item_name.'</a>';
    }
	
	$createdBy = $data->created_name.(!empty($data->created_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($data->created_at)) : '');
    $updatedBy = $data->updated_name.(!empty($data->updated_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($data->updated_at)) : '');

    if($data->item_type == 5):
        return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->make_brand,$data->part_no,$data->installation_year,$data->prev_maint_req,$createdBy,$updatedBy];
    elseif($data->item_type == 6):
		return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->category_name,$data->uom,$data->hsn_code,floatVal($data->gst_per),$data->permissible_error,$data->cal_required,$data->cal_freq,$data->cal_reminder,$createdBy,$updatedBy];
    elseif($data->item_type == 3):
        return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->material_grade,$data->category_name,$data->uom,$data->hsn_code,floatVal($data->gst_per),$createdBy,$updatedBy];
    elseif($data->item_type == 4):
		return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->category_name,$data->product_code,$data->process_name,$data->uom,$data->hsn_code,floatVal($data->gst_per),$createdBy,$updatedBy];
	elseif($data->item_type == 7):
        return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->material_grade,$data->category_name,$data->uom,$data->hsn_code,floatVal($data->gst_per),$createdBy,$updatedBy];
    elseif(in_array($data->item_type,[2,9])):  
        return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->category_name,$data->uom,$data->hsn_code,floatVal($data->gst_per),$createdBy,$updatedBy];
    else:
        return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->category_name,$data->uom,$data->hsn_code,floatVal($data->gst_per),$data->mfg_status,$data->mfg_type,$createdBy,$updatedBy];
    endif;
}

/* Countries Table Data */
function getCountriesData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Cusstom Field','fndelete':'delete'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'right_modal', 'form_id' : 'editcustomField', 'title' : 'Update Field Option','fnsave':'save','fnedit':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->name];
}

/* State Table Data */
function getStatesData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Cusstom Field','fndelete':'deleteState'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'right_modal', 'form_id' : 'editState', 'title' : 'Update Field Option','fnsave':'saveState','fnedit':'editState'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->name];
}

/* Cities Table Data */
function getCitiesData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Cities','fndelete':'deleteCities'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';


    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editProcess', 'title' : 'Update Process','call_function':'editCities','fnsave':'saveCities'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    
    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->country_name,$data->state_name,$data->name];
}

/* Custom Field Table Data */
function getCustomFieldData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Cusstom Field','fndelete':'deleteCustomField'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editcustomField', 'title' : 'Update Field Option','fnedit':'editCustomField'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
	$action = getActionButton($editButton);
    return [$action,$data->sr_no,$data->field_name,$data->field_type];
}

/* Custom Option Table Data */
function getCustomOptionData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Custom Option'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editCustomOption', 'title' : 'Update Custom Option'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->field_name,$data->title];
}

function getItemPriceStructureData($data){
    $deleteParam = "{'postData':{'id' : ".$data->structure_id."},'message' : 'Price Structure'}";
    $editParam = "{'postData':{'structure_id' : ".$data->structure_id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editPriceStructure', 'title' : 'Update Price Structure','call_function':'edit'}";
    $copyParam = "{'postData':{'structure_id' : ".$data->structure_id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'copyPriceStructure', 'title' : 'Copy Price Structure','call_function':'copyStructure'}";

    $copyButton = '<a class="btn btn-warning btn-edit permission-write" href="javascript:void(0)" datatip="Copy" flow="down" onclick="modalAction('.$copyParam.');"><i class="fas fa-clone"></i></a>';
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($copyButton.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->structure_name,$data->item_name,$data->category_name,floatval($data->gst_per),floatval($data->mrp),floatval($data->price),floatval($data->dealer_mrp),floatval($data->dealer_price),floatval($data->retail_mrp),floatval($data->retail_price)]; 
}

/* ECN Checklist Table Data */
function getEcnCheckListData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Check Point','fndelete':'deleteCheckList'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editCheckList', 'title' : 'Update Check Point','fnsave':'savecheckList','call_function':'editCheckList'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->description];
}

/* ECN Table Data */
function getEcnData($data){
    $ecnEffect = $inventoryBtn = $editButton = $deleteButton = $approveBtn = $printBtn = $activeBtn = $deActiveBtn = "" ;

    if($data->status == 0){
        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'editEcn', 'title' : 'Update ECN','fnsave':'saveEcn','call_function':'editEcn'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';

        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'ECN','fndelete':'deleteEcn'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

        $title = 'ECN Effect for [ ECN No : '.$data->ecn_no.' | Item : '.$data->item_code.']' ;  
        $ecnEffectParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'ecnEffect', 'title' : '".$title."', 'call_function' : 'ecnEffect', 'fnsave' : 'saveEcnEffect'}";
        $ecnEffect = '<a class="btn btn-primary btn-edit permission-modify" href="javascript:void(0)" datatip="Effect Of Changes" flow="down" onclick="modalAction('.$ecnEffectParam.');"><i class="fas fa-clipboard-list"></i></a>';

        $title = 'Inventory Detail [ ECN No : '.$data->ecn_no.' | Item : '.$data->item_code.']' ;  
        $inventoryParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'inventoryDetail', 'title' : '".$title."', 'call_function' : 'inventoryDetail', 'fnsave' : 'saveInventoryDetail'}";
        $inventoryBtn = '<a class="btn btn-dribbble btn-edit permission-modify" href="javascript:void(0)" datatip="Inventory Detail" flow="down" onclick="modalAction('.$inventoryParam.');"><i class="fas fa-cubes"></i></a>';
    }elseif($data->status == 1){
        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'editEcn', 'title' : 'Update ECN','fnsave':'saveEcn','call_function':'editEcn'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    }
    if(!empty($data->effect_id) && !empty($data->fg_stock) && $data->status == 0){
        $title = 'Approve [ ECN No : '.$data->ecn_no.' | Item : '.$data->item_code.']' ;  
        $approveParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'approveEcn', 'title' : '".$title."', 'call_function' : 'approveEcn', 'fnsave' : 'saveEcnApproval','js_store_fn':'confirmStore','savebtn_text':'Approve'}";
        $approveBtn = '<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="modalAction('.$approveParam.');"><i class="fas fa-check"></i></a>';
    }elseif(in_array($data->status,[1,3])){
        $activeParam = "{'postData':{'id' : ".$data->id.",'item_id' : ".$data->item_id.",'status':'2'},'message':'Are you sure you want to active this ECN' , 'fnsave' : 'activeEcn'}";
        $activeBtn = '<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip="Active" flow="down" onclick="confirmStore('.$activeParam.');"><i class="fas fa-check"></i></a>';
    }elseif($data->status == 2){
        $deActiveParam = "{'postData':{'id' : ".$data->id.",'item_id' : ".$data->item_id.",'status':'3'},'message':'Are you sure you want to De-active this ECN' , 'fnsave' : 'activeEcn'}";
        $deActiveBtn = '<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="De-Active" flow="down" onclick="confirmStore('.$deActiveParam.');"><i class="mdi mdi-close-outline"></i></a>';
    }

    $printBtn = '<a class="btn btn-tumblr btn-edit permission-modify" href="'.base_url('ecn/printEcn/'.$data->id).'" datatip="Print" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    
	$action = getActionButton($activeBtn. $deActiveBtn.$approveBtn.$ecnEffect.$inventoryBtn.$printBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->ecn_type_lbl,$data->ecn_no,formatDate($data->ecn_date),$data->item_code.' '.$data->item_name,$data->drw_no,$data->cust_rev_no,formatDate($data->cust_rev_date),$data->rev_no,formatDate($data->rev_date)];
}
?>