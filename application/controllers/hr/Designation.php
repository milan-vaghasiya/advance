<?php
class Designation extends MY_Controller{
    private $indexPage = "hr/designation/index";
    private $designationForm = "hr/designation/form";
    
	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Designation";
		$this->data['headData']->controller = "hr/designation";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('designation');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->designation->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
		foreach($result['data'] as $row):
			$row->sr_no = $i++;       
			$sendData[] = getDesignationData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addDesignation(){
        $this->load->view($this->designationForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['title']))
            $errorMessage['title'] = "Designation name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->designation->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->designation->getDesignation($data);
        $this->load->view($this->designationForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->designation->delete($id));
        endif;
    }

}
?>