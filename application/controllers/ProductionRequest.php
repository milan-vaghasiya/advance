<?php
class ProductionRequest extends MY_Controller{
    private $indexPage = "production_request/index";
    private $scheduleForm = "production_request/schedule_form";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Production Request";
		$this->data['headData']->controller = "productionRequest";
		$this->data['headData']->pageUrl = "productionRequest";
	}
	
	public function index($status=4){
        $this->data['status'] = $status;
        if($status == 6) { $controller = 'acceptedprodReq'; }
        else { $controller = 'productionRequest'; }
        $this->data['tableHeader'] = getMaintenanceDtHeader($controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=4){
        $data = $this->input->post(); $data['status']=$status;
        $result = $this->maintenancePlan->getProdRequestDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->status = $status;
            $sendData[] = getProductionRequestData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function updateScheduleDate(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->maintenancePlan->getPreventiveMaintanancePlan($data);
        $this->load->view($this->scheduleForm,$this->data); 
    }

    public function saveScheduleDate(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['schedule_date'])){
            $errorMessage['schedule_date'] = "Schedule Date is required.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->maintenancePlan->saveScheduleDate($data));
        endif;
    }

    public function acceptRequest(){
		$data = $this->input->post();		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->maintenancePlan->acceptRequest($data));
		endif;
	}
}
?> 