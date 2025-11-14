<?php
class ClientMaster extends MY_AdminController{
    private $index = "admin/client_master/index";
    private $form = "admin/client_master/form";

    public function __construct()	{
		parent::__construct();
		$this->data['headData']->pageTitle = "Client Master";
		$this->data['headData']->controller = "clientMaster";
	}

    public function index(){
        $this->data['tableHeader'] = getAdminDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->clientMaster->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getClientMasterData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addClient(){
        $this->data['countryData'] = $this->clientMaster->getCountries();
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
    
        if(empty($data['company_name']))
            $errorMessage['company_name'] = "Company Name is required.";

        if(empty($data['company_email']))
            $errorMessage['company_email'] = "Company Email is required.";

        if(empty($data['company_contact_person']))
            $errorMessage['company_contact_person'] = "Contact Person is required.";

        if(empty($data['company_phone']))
            $errorMessage['company_phone'] = "Mobile No. is required.";

        if(empty($data['company_city_id']))
            $errorMessage['company_city_id'] = "City Name is required.";

        if(empty($data['company_state_id']))
            $errorMessage['company_state_id'] = "State Name is required.";

        if(empty($data['company_country_id']))
            $errorMessage['company_country_id'] = "Country Name is required.";

        if(empty($data['company_address']))
            $errorMessage['company_address'] = "Address is required.";
            
        if(empty($data['company_pincode']))
            $errorMessage['company_pincode'] = "Pincode is required.";
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->clientMaster->save($data));
        endif;
    }

    
}
?>