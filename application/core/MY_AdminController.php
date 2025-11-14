<?php 
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class MY_AdminController extends CI_Controller{
    public function __construct(){
		parent::__construct();
        $this->isLoggedin();
		$this->data['headData'] = new StdClass;
		$this->load->library('form_validation');
        $this->load->model('admin/MasterModel',"masterModel");
        $this->load->model('admin/ClientMasterModel',"clientMaster");

        $this->setSessionVariables(["masterModel","clientMaster"]);
    }

    public function setSessionVariables($modelNames){
		$this->loginId = $this->session->userdata('loginId');
		$this->userName = $this->session->userdata('user_name');
		$this->userRole = $this->session->userdata('role');
		$this->userRoleName = $this->session->userdata('roleName');

		$models = $modelNames;
		foreach($models as $modelName):
			$modelName = trim($modelName);

			$this->{$modelName}->loginId = $this->loginId;
			$this->{$modelName}->userName = $this->userName;
			$this->{$modelName}->userRole = $this->userRole;
			$this->{$modelName}->userRoleName = $this->userRoleName;
		endforeach;
		return true;
	}

    public function isLoggedin(){
		if(!$this->session->userdata("loginId")):
			echo '<script>window.location.href="'.base_url().'";</script>';
		endif;
		return true;
	}

    public function printJson($data){
		print json_encode($data);exit;
	}

	public function getStatesOptions($postData=array()){
        $country_id = (!empty($postData['country_id']))?$postData['country_id']:$this->input->post('country_id');

        $result = $this->clientMaster->getStates(['country_id'=>$country_id]);

        $html = '<option value="">Select State</option>';
        foreach ($result as $row) :
            $selected = (!empty($postData['state_id']) && $row->id == $postData['state_id']) ? "selected" : "";
            $html .= '<option value="' . $row->id . '" ' . $selected . '>' . $row->name . '</option>';
        endforeach;

        if(!empty($postData)):
            return $html;
        else:
            $this->printJson(['status'=>1,'result'=>$html]);
        endif;
    }

    public function getCitiesOptions($postData=array()){
        $state_id = (!empty($postData['state_id']))?$postData['state_id']:$this->input->post('state_id');

        $result = $this->clientMaster->getCities(['state_id'=>$state_id]);
        
        $html = '<option value="">Select City</option>';
        foreach ($result as $row) :
            $selected = (!empty($postData['city_id']) && $row->id == $postData['city_id']) ? "selected" : "";
            $html .= '<option value="' . $row->id . '" ' . $selected . '>' . $row->name . '</option>';
        endforeach;

        if(!empty($postData)):
            return $html;
        else:
            $this->printJson(['status'=>1,'result'=>$html]);
        endif;
    }
}