<?php
class MachineActivities extends MY_Controller
{
    private $indexPage = "machine_activity/index";
    private $activityForm = "machine_activity/form";
    private $set_activity = "machine_activity/set_activity";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Machine Activities";
		$this->data['headData']->controller = "machineActivities";
		$this->data['headData']->pageUrl = "machineActivities";
	}
	
	public function index(){
        $this->data['tableHeader'] = getMaintenanceDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->activities->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getMachineActivitiesData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMachineActivities(){
        $this->data['freqList'] = $this->frequency;
        $this->load->view($this->activityForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['activities']))
            $errorMessage['activities'] = "Activities is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->activities->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->activities->getActivities($this->input->post('id'));
        $this->data['freqList'] = $this->frequency; 
        $this->load->view($this->activityForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->activities->delete($id));
        endif;
    }   

    public function setActivity(){
        $data = $this->input->post();
        $this->data['machine_id'] = $data['machine_id'];
        $this->data['activityData'] = $this->activities->getActivity();
		$this->data['dataRow'] = $this->activities->getmaintenanceData($data);
        $this->data['freqList'] = $this->frequency;
        $this->load->view($this->set_activity,$this->data); 
    }

    public function saveActivity() {
		$data = $this->input->post();
        $errorMessage = array();
        
        $act = 'False';
        foreach($data['checking_frequancy'] as $key=>$value){
            if(!empty($data['activity_id'][$key])){
                $act = 'True';
            }
        }
        if($act == 'False'){
            $errorMessage['activity_error'] = "Please select atleast one activity.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->activities->saveActivity($data));
        endif;
    } 
}
?>