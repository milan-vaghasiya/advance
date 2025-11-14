<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function getMaintenanceDtHeader($page){

    /* Machine BreakDown Header */
    $data['machineBreakdown'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['machineBreakdown'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['machineBreakdown'][] = ["name"=>"M.T. No.","textAlign"=>"center"];
    $data['machineBreakdown'][] = ["name"=>"Breakdown Time","textAlign"=>"center"];
    $data['machineBreakdown'][] = ["name"=>"End Time","textAlign"=>"center"];
	$data['machineBreakdown'][] = ["name"=>"Total Hour","textAlign"=>"right"];
    $data['machineBreakdown'][] = ["name"=>"PRC No.","textAlign"=>"center"];
    $data['machineBreakdown'][] = ["name"=>"Part Code","textAlign"=>"center"];
    $data['machineBreakdown'][] = ["name"=>"Machine","textAlign"=>"left"];
    $data['machineBreakdown'][] = ["name"=>"Idle Reason","textAlign"=>"left"];
    $data['machineBreakdown'][] = ["name"=>"Problem ","textAlign"=>"left"]; 
    $data['machineBreakdown'][] = ["name"=>"Solution ","textAlign"=>"left"];

    /* Machine Activities Header */
    $data['machineActivities'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['machineActivities'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['machineActivities'][] = ["name"=>"Machine Activities"];
    
    /* Maintenance Plan */   
    $masterCheckBox = '<input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkPlanSchedule" value=""><label for="masterSelect">ALL</label>';

	$data['maintenancePlan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['maintenancePlan'][] = ["name"=>$masterCheckBox,"class"=>"text-center no_filter","sortable"=>FALSE];
    $data['maintenancePlan'][] = ["name"=>"Machine"];
    $data['maintenancePlan'][] = ["name"=>"Activity"];
    $data['maintenancePlan'][] = ["name"=>"Frequency"];
    $data['maintenancePlan'][] = ["name"=>"Last Maintenance Date"];
    $data['maintenancePlan'][] = ["name"=>"Due Date"];
    $data['maintenancePlan'][] = ["name"=>"Schedule Date"];
    $data['maintenancePlan'][] = ["name"=>"Status", "textAlign"=>"center"];
    
    /* Completed Maintenance Plan */   
    $data['completedMaintenancePlan'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['completedMaintenancePlan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['completedMaintenancePlan'][] = ["name"=>"Machine"];
    $data['completedMaintenancePlan'][] = ["name"=>"Activity"];
    $data['completedMaintenancePlan'][] = ["name"=>"Frequency"];
    $data['completedMaintenancePlan'][] = ["name"=>"Last Maintenance Date"];
    $data['completedMaintenancePlan'][] = ["name"=>"Solution Date"];
    $data['completedMaintenancePlan'][] = ["name"=>"Maint. Thought"];
    $data['completedMaintenancePlan'][] = ["name"=>"Maint. Agency"];
    $data['completedMaintenancePlan'][] = ["name"=>"Status"];
    $data['completedMaintenancePlan'][] = ["name"=>"Remarks"];

	/* Production Request */   
    $data['productionRequest'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['productionRequest'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['productionRequest'][] = ["name"=>"Machine"];
    $data['productionRequest'][] = ["name"=>"Activity"];
    $data['productionRequest'][] = ["name"=>"Frequency"];
    $data['productionRequest'][] = ["name"=>"Last Maintenance Date"];
    $data['productionRequest'][] = ["name"=>"Due Date"];
    $data['productionRequest'][] = ["name"=>"Schedule Date"];
    $data['productionRequest'][] = ["name"=>"Status", "textAlign"=>"center"];

	/* Accepted Production Request */ 
    $data['acceptedprodReq'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['acceptedprodReq'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];  
    $data['acceptedprodReq'][] = ["name"=>"Machine"];
    $data['acceptedprodReq'][] = ["name"=>"Activity"];
    $data['acceptedprodReq'][] = ["name"=>"Frequency"];
    $data['acceptedprodReq'][] = ["name"=>"Last Maintenance Date"];

    return tableHeader($data[$page]);
}

/* Machine Breakdown Table Data */
function getMachineBreakdownData($data){
    $solutionBtn = $editButton = $deleteButton = $reqBtn = $reqViewBtn =""; 
    if(empty($data->end_date)){
        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Process'}";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editMachineBreakdown', 'title' : 'Update Machine Breakdown','call_function':'edit','fnsave':'save'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
        
        $solutionParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addSolution', 'title' : 'Add Solution','call_function':'addSolution','fnsave':'saveSolution'}";
        $solutionBtn = '<a class="btn btn-primary btn-edit permission-modify" href="javascript:void(0)" datatip="Solution" flow="down" onclick="modalAction('.$solutionParam.');"><i class="far fa-check-square"></i></a>';

        $reqParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'addSparPartRequest', 'title' : 'Add Sparpart Request','call_function':'addSparPartRequest','fnsave':'saveSparPartRequest','button':'close'}";
        $reqBtn = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Sparpart Request" flow="down" onclick="modalAction('.$reqParam.');"><i class="fa fa-reply"></i></a>'; 

    }else{
        $reqViewParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'viewSparPart', 'title' : 'View Spare Part [ M.T. No. : ". $data->trans_number."  ]','call_function':'viewSparPart','button':'close'}";
        $reqViewBtn = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Spare Part View" flow="down" onclick="modalAction('.$reqViewParam.');"><i class="fa fa-eye"></i></a>';
    }
  
	$action = getActionButton($reqBtn.$solutionBtn.$editButton.$deleteButton.$reqViewBtn);
	
	
    $endDate = (!empty($data->end_date)) ? date('d-m-Y H:i',strtotime($data->end_date)) : '';
    
	$diffInHours = 0;
	if(!empty($data->end_date)){		
		$startTimestamp = strtotime(str_replace('-', '/', $data->trans_date)); // convert to a format strtotime can parse
		$endTimestamp = strtotime(str_replace('-', '/', $data->end_date));
		
		$diffInSeconds = $endTimestamp - $startTimestamp;
		$diffInHours = number_format(($diffInSeconds / 3600), 2);
	}
	
	return [$action,$data->sr_no,$data->trans_number,date('d-m-Y H:i',strtotime($data->trans_date)),$endDate,$diffInHours,$data->prc_number,$data->part_code,(!empty($data->machine_code) ? '['.$data->machine_code.'] ' : '').$data->machine_name,(!empty($data->idle_reason)?'['.$data->code.'] '.$data->idle_reason:''),$data->remark,$data->solution]; 
}

/* Machine Activities Data  */
function getMachineActivitiesData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Machine Activities'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editMachineActivities', 'title' : 'Update Machine Activities'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->activities];
}

/* Maintenance Plan Data */
function getMaintenancePlanData($data){
    if($data->status == 1){                
        $selectBox = '';
        if(empty($data->schedule_date)){
            $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkPlanSchedule" value="'.$data->main_id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
        }
        $data->status = '<span class="badge bg-danger">Pending</span>';
        
        $due_date = date('d-m-Y',strtotime($data->due_date. '-' . $data->plan_days . ' days')); 

        if(!empty($due_date) AND (strtotime($due_date) <= strtotime(date('d-m-Y')))){$due_date = '<strong class="text-danger">'.$due_date.'</strong>';}

        return [$data->sr_no,$selectBox,'['.$data->item_code.'] '.$data->item_name,$data->activities,$data->checking_frequancy,formatDate($data->last_maintence_date),$due_date,formatDate($data->schedule_date),$data->status];
    }
    elseif($data->status == 3){
        $action = getActionButton("");
		
		$agency = ($data->agency == 1) ? 'In House' : 'Third Party';
        return [$action,$data->sr_no,'['.$data->item_code.'] '.$data->item_name,$data->activities,$data->maintence_frequancy,formatDate($data->last_maintence_date),formatDate($data->solution_date),$agency,$data->solutionBy,$data->solution_status,$data->remark];
	}
    else{
        $startBtn = ''; $solutionBtn = '';
        if($data->status == 2){
            if($data->schedule_by > 0){
                $data->status = '<span class="badge bg-info">Scheduled</span><br>'.$data->emp_name;
            }else{
                $data->status = '';
            }

            if($data->schedule_by > 0 && $data->start_by <= 0){
                $startParam = "{'postData':{'id' : '".$data->prev_main_plan_id."', 'machine_id' : '".$data->machine_id."'}, 'fnsave' : 'startMaintenance', 'message' : 'Are you sure want to Start this Maintenance?'}";
                $startBtn = '<a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Start" flow="down" onclick="confirmStore('.$startParam.');"><i class="mdi mdi-play"></i></a>';	                
            }
            elseif(!empty($data->start_by)){
                $data->status = '<span class="badge bg-warning">Start Maintenance</span><br>'.date('d-m-Y H:i:s',strtotime($data->start_date));
                
                $solutionParam = "{'postData':{'id' : '".$data->prev_main_plan_id."', 'machine_id' : '".$data->machine_id."', 'activity_list' : '".$data->activity_list."'},'modal_id' : 'bs-right-xl-modal', 'form_id' : 'createSolution', 'title' : 'Create Solution', 'call_function' : 'createSolution', 'fnsave' : 'saveSolution'}";
                $solutionBtn = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Create Solution" flow="down" onclick="modalAction('.$solutionParam.');"><i class="mdi mdi-cogs"></i></a>';
            }
        }
        $action = getActionButton($startBtn.$solutionBtn);

        return [$action,$data->sr_no,'['.$data->item_code.'] '.$data->item_name,$data->activities,$data->maintence_frequancy,formatDate($data->last_maintence_date),formatDate($data->due_date),formatDate($data->schedule_date),$data->status];
    }
}

/* Production Request Data */
function getProductionRequestData($data){
    $scheduleButton = ''; $acceptBtn = '';
    if($data->start_by <= 0){
        $scheduleParam = "{'postData':{'id' : '".$data->prev_main_plan_id."'},'modal_id' : 'modal-md', 'form_id' : 'updateScheduleDate', 'title' : 'Update Schedule Date', 'call_function' : 'updateScheduleDate', 'fnsave' : 'saveScheduleDate'}";
        $scheduleButton = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Update Schedule" flow="down" onclick="modalAction('.$scheduleParam.');"><i class="mdi mdi-plus-box-outline"></i></a>';
    }    
    if($data->status == 4){
        $data->status = '<span class="badge bg-danger">Pending</span>';               
    }
    elseif($data->status == 5){
        if(!empty($data->solution_by) && $data->accept_by <= 0){
            $data->status = '<span class="badge bg-primary">Complete Maintenance</span><br>'.date('d-m-Y H:i:s',strtotime($data->solution_date));
            
            $acceptParam = "{'postData':{'id' : '".$data->prev_main_plan_id."', 'machine_id' : '".$data->machine_id."'}, 'fnsave' : 'acceptRequest', 'message' : 'Are you sure want to Accept this Production Request?'}";
            $acceptBtn = '<a class="btn btn-success permission-modify" href="javascript:void(0)" datatip="Accept" flow="down" onclick="confirmStore('.$acceptParam.');"><i class="mdi mdi-check"></i></a>';	
        }
        elseif($data->start_by > 0){
            $data->status = '<span class="badge bg-warning">Start Maintenance</span><br>'.date('d-m-Y H:i:s',strtotime($data->start_date));
        }else{
            $data->status = '<span class="badge bg-info">Scheduled</span><br>'.$data->emp_name;
        }
    }
    elseif($data->status == 6){
        $action = getActionButton("");

        return [$action,$data->sr_no,'['.$data->item_code.'] '.$data->item_name,$data->activities,$data->maintence_frequancy,formatDate($data->last_maintence_date)];
    }

	$action = getActionButton($acceptBtn.$scheduleButton);
    return [$action,$data->sr_no,'['.$data->item_code.'] '.$data->item_name,$data->activities,$data->maintence_frequancy,formatDate($data->last_maintence_date),formatDate($data->due_date),formatDate($data->schedule_date),$data->status];
}
?>