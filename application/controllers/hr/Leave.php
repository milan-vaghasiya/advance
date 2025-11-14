<?php
class Leave extends MY_Controller
{
    private $indexPage = "hr/leave/index";
    private $leaveForm = "hr/leave/leave_form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Leave";
		$this->data['headData']->controller = "hr/leave";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('leave');
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows(){
		$postData = $this->input->post();
		$postData['login_emp_id']=$this->session->userdata('loginId');
        $result = $this->leave->getDTRows($postData);
        $sendData = array();$i=1;$count=0;
		
		foreach($result['data'] as $row):
			$row->sr_no = $i++;
			
			if($row->approve_status == 3 || $row->approve_status == 4):
				$row->status = '<span class="font-13 font-weight-bold badge bg-danger">Declined</span>';$row->approveButtonLabel = 'Declined';
			elseif($row->approve_status == 2):
				$row->status = '<span class="font-13 font-weight-bold badge bg-success">Final Approved</span>';$row->approveButtonLabel = 'Final Approved';
			elseif($row->approve_status == 1):
				$row->status = '<span class="font-13 font-weight-bold badge bg-primary">Primary Approved</span>';$row->approveButtonLabel = 'Primary Approved';
			else:
				$row->status = '<span class="font-13 font-weight-bold badge bg-info">Pending</span>';$row->approveButtonLabel = 'Pending';
			endif;
			$row->showLeaveAction = false;
			$sendData[] = getLeaveData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addLeave(){
        $this->data['leaveType'] = $this->leave->getLeaveType();
        $this->data['empData'] = $this->employee->getEmployee($this->session->userdata('loginId'));
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->load->view($this->leaveForm,$this->data);
    }
    
    public function getLeaveQuota(){
        $data = $this->input->post();
        $result = $this->leave->getLeaveQuota($data);
        $this->printJson($result);
    }

    public function getEmpLeaves(){
		$login_id = $this->session->userdata('loginId');
		$start_date=date("Y-m-d",strtotime($this->session->userdata('startDate')));
		$end_date=date("Y-m-d",strtotime($this->session->userdata('endDate')));
        $this->printJson($this->leave->getEmpLeaves($login_id,$this->input->post('leave_type_id'),$start_date,$end_date)[0]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
		
		if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee is required.";
        if(empty($data['leave_type_id'])):
            $errorMessage['leave_type_id'] = "Leave Type is required.";
		endif;

		if(empty($data['start_date']))
            $errorMessage['start_date'] = "Start Date is required.";
            
        if(empty($data['type_leave'])){
            if(empty($data['start_section'])){
    			$errorMessage['start_section'] = "Start Section is required.";
            }
    		if($data['start_date'] != $data['end_date']){
        		if(empty($data['end_section'])){ $errorMessage['end_section'] = "End Section is required."; }
    		}
    		if(empty($data['end_date'])){ $errorMessage['end_date'] = "End Date is required."; }    		
        }else{
            
            if(empty($data['end_time'])){ $errorMessage['end_time'] = "End Time is required."; }
    		else{ $data['end_date'] =  date('Y-m-d H:i:s', strtotime($data['start_date'].' '.$data['end_time'])); }
            
            if(empty($data['start_time'])){ $errorMessage['start_time'] = "Start Time is required."; }
            else{ $data['start_date'] = date('Y-m-d H:i:s', strtotime($data['start_date'].' '.$data['start_time'])); }
            
            if($data['total_days'] > 240)
                $errorMessage['total_days'] = "Invalid Total Mins.";
                
			$data['leave_type'] = "Short Leave";
        }   
        if(empty($data['leave_reason']))
            $errorMessage['leave_reason'] = "Reason is required.";    
		if(empty($data['total_days']))
            $errorMessage['generalError'] = "You have to apply atleast 1 Day Leave";
			
		if(!empty($errorMessage)):
				$this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            unset($data['start_time'],$data['end_time']);
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['created_by'] = $this->session->userdata('loginId');
			$this->printJson($this->leave->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['leaveType'] = $this->leave->getLeaveType();
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->data['empData'] = $this->employee->getEmployee($this->session->userdata('loginId'));
        $this->data['dataRow'] = $this->leave->getLeave($id);
        $this->load->view($this->leaveForm,$this->data);
    }

    public function approveLeave(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['id']))
            $errorMessage['generalError'] = "Leave is not defined.";
		if(empty($data['approved_date']))
            $errorMessage['approved_date'] = "Approve Date is required.";
		if(empty($data['approve_status']))
            $errorMessage['approve_status'] = "Status is required.";
			
		if(!empty($errorMessage)):
				$this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$data['approved_by'] = $this->session->userdata('loginId');
			$this->printJson($this->leave->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->leave->delete($id));
        endif;
    }
}
?>