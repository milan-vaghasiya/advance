<?php
class Departments extends MY_Controller{
    private $indexPage = "hr/department/index";
    private $departmentForm = "hr/department/form";
	    
	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Departments";
		$this->data['headData']->controller = "hr/departments";
		$this->data['headData']->pageUrl = "hr/departments";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('departments');
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows(){
        $data = $this->input->post();
        $result = $this->department->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
            $sendData[] = getDepartmentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
    public function addDepartment(){
        $this->load->view($this->departmentForm,$this->data);
    }
    
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['name']))
            $errorMessage['name'] = "Department name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->department->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->department->getDepartment($data);
        $this->load->view($this->departmentForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->department->delete($id));
        endif;
    }
    
}
?>