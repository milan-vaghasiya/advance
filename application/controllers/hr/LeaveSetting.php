<?php
class LeaveSetting extends MY_Controller
{
    private $indexPage = "hr/leave/leave_setting";
    private $leaveTypeForm = "hr/leave/leave_setting_form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Leave";
		$this->data['headData']->controller = "hr/leaveSetting";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('leaveSetting');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->leaveSetting->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
		foreach($result['data'] as $row):
			$row->sr_no = $i++;       
			$sendData[] = getLeaveSettingData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addLeaveType(){
        $this->load->view($this->leaveTypeForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['leave_type']))
            $errorMessage['leave_type'] = "Type is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['leave_type'] = ucwords($data['leave_type']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->leaveSetting->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->leaveSetting->getLeaveType($id);
        $this->load->view($this->leaveTypeForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->leaveSetting->delete($id));
        endif;
    }
}
?>