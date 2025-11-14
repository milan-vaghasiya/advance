<?php
class CustomField extends MY_Controller{
    private $index = "custom_field/index";
    private $form = "custom_field/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Custom Field";
		$this->data['headData']->controller = "customField";
		$this->data['headData']->pageUrl = "customField";
	}
    
    public function index(){
        $this->data['tableHeader'] = getMasterDtHeader('customField');
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($type=0){
        $data = $this->input->post(); $data['type']=$type;
        $result = $this->customField->getDTRows($data);
        $sendData = array();$i=($data['start'] + 1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getCustomFieldData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCustomField(){
        $this->data['nextIndex'] = $this->customField->getNextFieldIndex();
        $this->load->view($this->form,$this->data);
    }    

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
       
        if(empty($data['field_name'])){ $errorMessage['field_name'] = "Field is required."; }
        if(empty($data['field_type'])){ $errorMessage['field_type'] = "Field type is required."; }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->customField->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post(); 
        $this->data['dataRow'] = $this->customField->getCustomFieldDetail($data); 
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->customField->delete($id));
        endif;
    }
}
?>