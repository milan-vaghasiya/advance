<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getHrDtHeader($page){
    /* Department Header */
    $data['departments'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['departments'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['departments'][] = ["name"=>"Department Name"];
    $data['departments'][] = ["name"=>"Remark"];

    /* Designation Header */
    $data['designation'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['designation'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['designation'][] = ["name"=>"Designation Name"];
    $data['designation'][] = ["name"=>"Remark"];

    /* Employee Category Header */
    $data['employeeCategory'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['employeeCategory'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['employeeCategory'][] = ["name"=>"Category Name"];
    $data['employeeCategory'][] = ["name"=>"Over Time"];

    /* Employee Header */
    $data['employees'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['employees'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['employees'][] = ["name"=>"Employee Name"];
    $data['employees'][] = ["name"=>"Emp Code","textAlign"=>'center'];
    $data['employees'][] = ["name"=>"Department"];
    $data['employees'][] = ["name"=>"Designation"];
    $data['employees'][] = ["name"=>"Category","textAlign"=>'center'];
    $data['employees'][] = ["name"=>"Contact No.","textAlign"=>'center'];
    $data['employees'][] = ["name"=>"Pan No.","textAlign"=>'center'];
    $data['employees'][] = ["name"=>"Aadhar No.","textAlign"=>'center'];
    $data['employees'][] = ["name"=>"Unit","textAlign"=>'center'];

	/* Employee Loan Header */
   $data['empLoan'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
   $data['empLoan'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
   $data['empLoan'][] = ["name"=>"Sanction No."];
   $data['empLoan'][] = ["name"=>"Sanction Date"];
   $data['empLoan'][] = ["name"=>"Employee Name"];
   $data['empLoan'][] = ["name"=>"Amount"];
   $data['empLoan'][] = ["name"=>"reason"];
   
    /* Advance Salary Header */
    $data['advanceSalary'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['advanceSalary'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['advanceSalary'][] = ["name"=>"Date"];
    $data['advanceSalary'][] = ["name"=>"Name"];
    $data['advanceSalary'][] = ["name"=>"Amount"];
    $data['advanceSalary'][] = ["name"=>"Reason"];

    /* Leave Header */
    $data['leave'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
    $data['leave'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['leave'][] = ["name"=>"Employee"];
    $data['leave'][] = ["name"=>"Emp Code"];
    $data['leave'][] = ["name"=>"Leave Type"];
    $data['leave'][] = ["name"=>"From"];
    $data['leave'][] = ["name"=>"To"];
    $data['leave'][] = ["name"=>"Leave Days"];
    $data['leave'][] = ["name"=>"Reason"];
    $data['leave'][] = ["name"=>"Status"];

    /* Leave Setting Header */
    $data['leaveSetting'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['leaveSetting'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
	$data['leaveSetting'][] = ["name"=>"Leave Type"];
    $data['leaveSetting'][] = ["name"=>"Remark"];

	/* Leave Approve Header */
	$data['leaveApprove'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['leaveApprove'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
	$data['leaveApprove'][] = ["name"=>"Employee"];
	$data['leaveApprove'][] = ["name"=>"Emp Code"];
	$data['leaveApprove'][] = ["name"=>"Leave Type"];
	$data['leaveApprove'][] = ["name"=>"From"];
	$data['leaveApprove'][] = ["name"=>"To"];
	$data['leaveApprove'][] = ["name"=>"Leave Days"];
	$data['leaveApprove'][] = ["name"=>"Reason"];
	$data['leaveApprove'][] = ["name"=>"Status"];
	
    /* Shift Header */
	$data['shift'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['shift'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
	$data['shift'][] = ["name"=>"Shift Name"];
	$data['shift'][] = ["name"=>"Start Time"];
	$data['shift'][] = ["name"=>"End Time"];
	$data['shift'][] = ["name"=>"Production Time"];
	$data['shift'][] = ["name"=>"Lunch Time"];
	$data['shift'][] = ["name"=>"Shift Hour"];

    return tableHeader($data[$page]);
}

/* Department Table Data */
function getDepartmentData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Department'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editDepartment', 'title' : 'Update Department'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->name,$data->description];
}

/* Designation Table Data */
function getDesignationData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Designation'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editDesignation', 'title' : 'Update Designation','call_function':'edit'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title,$data->description];
}

/* Employee Category Table Data */
function getEmployeeCategoryData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Employee Category'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editEmployeeCategory', 'title' : 'Update Employee Category','call_function':'edit'}";


    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->category,$data->overtime];
}

/* Employee Table Data */
function getEmployeeData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Employee'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editEmployee', 'title' : 'Update Employee','call_function':'edit'}";
    
    $leaveButton = '';$addInDevice = '';$activeButton = '';$empRelieveBtn = '';$editButton = '';$deleteButton = '';

    if($data->is_active == 1):
        $activeParam = "{'postData':{'id' : ".$data->id.", 'is_active' : 0},'fnsave':'activeInactive','message':'Are you sure want to De-Active this Employee?'}";
        $activeButton = '<a class="btn btn-youtube permission-modify" href="javascript:void(0)" datatip="De-Active" flow="down" onclick="confirmStore('.$activeParam.');"><i class="fa fa-ban"></i></a>';    

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

        $empName = $data->emp_name;
    else:
        $activeParam = "{'postData':{'id' : ".$data->id.", 'is_active' : 1},'fnsave':'activeInactive','message':'Are you sure want to Active this Employee?'}";
        $activeButton = '<a class="btn btn-success permission-remove" href="javascript:void(0)" datatip="Active" flow="down" onclick="confirmStore('.$activeParam.');"><i class="fa fa-check"></i></a>';  
          
        $empName = $data->emp_name;
    endif;
    
    $CI = & get_instance();
    $userRole = $CI->session->userdata('role');

    $resetPsw='';
    if(in_array($userRole,[-1,1])):
        $resetParam = "{'postData':{'id' : ".$data->id."},'fnsave':'resetPassword','message':'Are you sure want to Change ".$data->emp_name." Password?'}";
        $resetPsw='<a class="btn btn-danger" href="javascript:void(0)" onclick="confirmStore('.$resetParam.');" datatip="Reset Password" flow="down"><i class="fa fa-key"></i></a>';
    endif;
    
    $unit = ($data->unit_id == 1) ? 'UNIT 1' : 'UNIT 2';
	
    $action = getActionButton($resetPsw.$leaveButton.$addInDevice.$activeButton.$empRelieveBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,$empName,$data->emp_code,$data->dept_name,$data->emp_designation,$data->emp_category,$data->emp_contact,$data->pan_no,$data->aadhar_no,$unit];
}

function getEmpLoanData($data){	
	$deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Employee Loan'}";
	$editButton ="";$deleteButton =""; $printBtn =""; $approveButton="";$senctionBtn="";
    if(empty($data->trans_status)){
        $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editLoan', 'title' : 'Update Loan','call_function':'edit'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
        
		$approveParam = "{'postData':{'id' : ".$data->id.",'approve_type':'1'},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'loanApproval', 'title' : 'Loan Approval','fnsave' : 'saveLoanApproval', 'savebtn_text':'Approve'}";
        $approveButton = '<a class="btn btn-success btn-edit permission-approve" href="javascript:void(0)" datatip="Approve" flow="down" onclick="modalAction('.$approveParam.');"><i class="mdi mdi-check"></i></a>';
    }else{
        $printBtn = '<a class="btn btn-success btn-edit permission-approve" href="'.base_url('hr/empLoan/printLoan/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="mdi mdi-file-pdf" ></i></a>';
    }
    if($data->trans_status == 1){
        $senctionParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'loanApproval', 'title' : 'Senction','fnsave' : 'saveLoanSenction', 'savebtn_text':'Senction','fnedit':'loanSenction'}";

        $senctionBtn = '<a class="btn btn-warning  permission-approve" href="javascript:void(0)" datatip="Senction" flow="down" onclick="modalAction('.$senctionParam.');"><i class="mdi mdi-check"></i></a>';
    }
    $action = getActionButton($senctionBtn.$approveButton.$printBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->entry_date),'['.$data->emp_code.'] '.$data->emp_name,$data->demand_amount,$data->reason];
}

function getAdvanceSalaryData($data){
	$deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Advance Salary'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editExtraHours', 'title' : 'Update Advance Salary'}";
    $sanctionParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'sanctionAdvance', 'title' : 'Sanction Advance','call_function':'sanctionAdvance'}";

    $editButton = '';$deleteButton = '';$sanction = '';

    if(empty($data->sanctioned_by)):
        $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    
        $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

        $sanction = '<a class="btn btn-warning permission-write" href="javascript:void(0)" datatip="Sanction Advance" flow="down" onclick="modalAction('.$sanctionParam.');"><i class="mdi mdi-check"></i></a>';
    endif;
	
    $action = getActionButton($sanction.$editButton.$deleteButton);
    
    return [$action,$data->sr_no,formatDate($data->entry_date),'['.$data->emp_code.'] '.$data->emp_name,floatVal($data->amount),$data->reason,$data->sanctioned_by_name,((!empty($data->sanctioned_at))?formatDate($data->sanctioned_at):""),floatVal($data->sanctioned_amount),floatVal($data->deposit_amount),floatVal($data->pending_amount)];
}

/* Leave Table Data */
function getLeaveData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Leave'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editLeave', 'title' : 'Update Leave'}";
	
    $editButton = '';$deleteButton = '';$approveButton = '';
    if($data->approve_status == 0 AND strtotime($data->end_date) >= strtotime(date('Y-m-d'))){
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    }
    if($data->showLeaveAction){
        $approveButton = '<a class="btn btn-warning btn-leaveAction permission-modify" href="javascript:void(0)" data-id="'.$data->id.'" data-min_date="'.date("Y-m-d",strtotime($data->start_date)).'" datatip="Leave Action" flow="down"><i class="mdi mdi-check"></i></a>';
    }
    
	$action = getActionButton( $approveButton.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->emp_name,$data->emp_code,$data->leave_type,date('d-m-Y',strtotime($data->start_date)),date('d-m-Y',strtotime($data->end_date)),$data->total_days,$data->leave_reason,$data->status];
}

/* Leave Setting Table Data */
function getLeaveSettingData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Leave Type'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editLeaveType', 'title' : 'Update Leave Type'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';

    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->leave_type,$data->remark];
}


/* Leave Approve Table Data */
function getLeaveApproveData($data){
	$approveButton='';
    if($data->approval_type == 1)
    {
        if($data->approve_status == 0 AND (in_array($data->loginId,explode(',',$data->fla_id))))
        {
            $approveButton = '<a class="btn btn-success btn-leaveAction permission-modify" href="javascript:void(0)" data-id="'.$data->id.'" data-emp_id="'.$data->emp_id.'" data-type_leave="'.$data->type_leave.'" data-min_date="'.date("Y-m-d",strtotime($data->start_date)).'" data-created_at="'.date("Y-m-d",strtotime($data->created_at)).'" data-approve_status="'.$data->approve_status.'" datatip="Leave Action" flow="down"><i class="mdi mdi-check"></i></a>';
        }
    }
	
	$start_date = date('d-m-Y',strtotime($data->start_date));
    $end_date = date('d-m-Y',strtotime($data->end_date));
    $total_days = $data->total_days.' Days';
    
    if(!empty($data->type_leave) && $data->type_leave == 'SL'){
        $start_date = date('d-m-Y H:i',strtotime($data->start_date));
        $end_date = date('d-m-Y H:i',strtotime($data->end_date));
        $hours = intval($data->total_days/60);
        $mins = intval($data->total_days%60);
        $total_days = sprintf('%02d',$hours).':'.sprintf('%02d',$mins).' Hours';
    }
    
	$action = getActionButton($approveButton);
    return [$action,$data->sr_no,$data->emp_name,$data->emp_code,$data->leave_type,$start_date,$end_date,$total_days,$data->leave_reason,$data->status];
}


/* get Shift Data */
function getShiftData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Shift'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'editShift', 'title' : 'Update Shift'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

	$action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->shift_name,$data->shift_start,$data->shift_end,$data->production_hour,$data->total_lunch_time,$data->total_shift_time];
}

?>