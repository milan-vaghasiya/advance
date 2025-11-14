<?php
class MaintenancePlan extends MY_Controller{
    private $indexPage = "maintenance_plan/index";
    private $scheduleForm = "maintenance_plan/schedule_form";
    private $solutionForm = "maintenance_plan/solution_form";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Maintenance Plan";
		$this->data['headData']->controller = "maintenancePlan";
		$this->data['headData']->pageUrl = "maintenancePlan";
	}

    public function index($status=1){
        $this->data['status'] = $status;
        if($status == 1) { $controller = 'maintenancePlan'; }
        elseif($status == 3) { $controller = 'completedMaintenancePlan'; }
        else { $controller = 'productionRequest'; }
        $this->data['tableHeader'] = getMaintenanceDtHeader($controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status']=$status;
        if($status == 1){
            $result = $this->maintenancePlan->getDTRows($data);
        }else{
            $result = $this->maintenancePlan->getProdRequestDTRows($data);
        }
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->status = $status;
            $sendData[] = getMaintenancePlanData($row);
        endforeach;
        $result['data'] = $sendData; 
        $this->printJson($result);
    }

    public function schedulePlan(){
        $data = $this->input->post();        
        $this->data['dataRow'] = $this->maintenancePlan->getActivitiesForSchedule($data);
        $this->load->view($this->scheduleForm,$this->data); 
    }

    public function saveSchedule(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['schedule_date'])){
            $errorMessage['schedule_date'] = "Schedule Date is required.";
        }
        if(empty($data['activity_id'][0])){
            $errorMessage['general_error'] = "Activity is required.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->maintenancePlan->saveSchedule($data));
        endif;
    }

    public function startMaintenance(){
		$data = $this->input->post();		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->maintenancePlan->startMaintenance($data));
		endif;
	}

    public function createSolution(){
        $data = $this->input->post();
        $this->data['machine_id'] = $data['machine_id'];
        $this->data['activity_list'] = $data['activity_list'];
        $this->data['dataRow'] = $this->maintenancePlan->getActivityForSolution($data);
        $this->data['partyData'] = $this->party->getPartyList(['party_category'=>'2,3']);
        $this->load->view($this->solutionForm,$this->data); 
    }

    public function saveSolution(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['id'][0])){
            $errorMessage['general_error'] = "Activities required.";
        }
        if(empty($data['solution_by']) && $data['agency'] == 1){
            $errorMessage['solution_by'] = "Solution By is required.";
        }
        if(empty($data['vendor_id']) && $data['agency'] == 2){
            $errorMessage['solution_by'] = "Solution By is required.";
        }
        if(empty($data['solution_date'])){
            $errorMessage['solution_date'] = "Solution Date is required.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->maintenancePlan->saveSolution($data));
        endif;
    }
}
?>