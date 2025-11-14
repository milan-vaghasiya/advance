<?php
class EmployeeCategory extends MY_Controller{
    private $indexPage = "hr/employee_category/index";
    private $categoryForm = "hr/employee_category/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "User Category";
		$this->data['headData']->controller = "hr/employeeCategory";
	}

    public function index(){
        $this->data['tableHeader'] = getHrDtHeader('employeeCategory');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->employeeCategory->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getEmployeeCategoryData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCategory(){
        $this->load->view($this->categoryForm);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['category']))
            $errorMessage['category'] = "Category Name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->employeeCategory->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->employeeCategory->getEmployeeCategory($data);
        $this->load->view($this->categoryForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employeeCategory->delete($id));
        endif;
    }

}
?>