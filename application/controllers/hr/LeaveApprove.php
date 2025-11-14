<?php
class LeaveApprove extends MY_Controller
{
    private $indexPage = "hr/leave/leave_approve";
    private $leaveForm = "hr/leave/leave_approve_form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Leave Approve";
		$this->data['headData']->controller = "hr/leaveApprove";
	}
	
	public function index(){
		$this->data['tableHeader'] = getHrDtHeader('leaveApprove');
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows($status=0){
		$postData = $this->input->post(); $postData['status']=$status;
		$postData['login_emp_id']=$this->session->userdata('loginId');
		
		$sendData = array();$i=1; 
		$result = $this->leaveApprove->getDTRows($postData);
		
		foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$row->emp_name = $row->emp_name .'<br><small>'.$row->title.'</small>';
			$row->loginId = $this->loginId;
			if($row->approve_status == 0):
				$row->status = '<span class="font-13 font-weight-bold badge bg-info">Primary</span>';
			elseif($row->approve_status == 1):
                $row->status = '<span class="font-13 font-weight-bold badge bg-primary">Final</span>';
            elseif($row->approve_status == 2):
                $row->status = '<span class="font-13 font-weight-bold badge bg-success">Approved</span>';
            elseif($row->approve_status == 3):
				$row->status = '<span class="font-13 font-weight-bold badge bg-danger">Rejected</span>';
			elseif($row->approve_status == 4):
				$row->status = '<span class="font-13 font-weight-bold badge bg-danger">Rejected</span>';
			endif;
			
			$row->showLeaveAction = false;
			$sendData[] = getLeaveApproveData($row);
		endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function approveLeave(){
        $data = $this->input->post();
        
        $errorMessage = array();
        if(empty($data['id']))
            $errorMessage['generalError'] = "Leave is not defined.";
		if(empty($data['approve_status']))
            $errorMessage['approve_status'] = "Status is required.";
			
		if(!empty($errorMessage)):
				$this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$data['fla_id'] = $this->session->userdata('loginId');
			$data['final_approved_at'] = date('Y-m-d H:i:s');
			
			$this->printJson($this->leaveApprove->save($data));
        endif;
    }

    public function getLeaveQuota(){
        $data = $this->input->post();
        $result = $this->leave->getLeaveQuota($data);
        $this->printJson($result);
    }
}
?>