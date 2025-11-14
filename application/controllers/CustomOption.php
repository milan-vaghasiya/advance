<?php
class CustomOption extends MY_Controller{
    private $index = "custom_option/index";
    private $form = "custom_option/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Custom Option";
		$this->data['headData']->controller = "customOption";
		$this->data['headData']->pageUrl = "customOption";
	}
    
	public function index(){
        $this->data['tableHeader'] = getMasterDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($type=0){
        $data = $this->input->post(); $data['type']=$type;
        $result = $this->customOption->getDTRows($data);
        $sendData = array();$i=($data['start'] + 1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getCustomOptionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCustomOption(){
        $this->data['fieldList'] = $this->customField->getCustomFieldList();       
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
       
        if(empty($data['title'])){ $errorMessage['title'] = "Title is required.";}
        if(empty($data['type'])){ $errorMessage['type'] = "Type is required.";}

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->customOption->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post(); 
        $this->data['fieldList'] = $this->customField->getCustomFieldList();
        $this->data['dataRow'] = $this->customOption->getSizeMaster($data); 
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->customOption->delete($id));
        endif;
    }   
}
?>