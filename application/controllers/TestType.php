<?php
class TestType extends MY_Controller{ 
    private $index = "test_type/index";
    private $form = "test_type/form";
    private $parameterForm = "test_parameter/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Test Type";
		$this->data['headData']->controller = "testType";
        $this->data['headData']->pageUrl = "testType";
	} 
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }
	
    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->testType->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getRmTestTypeData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addTestType(){
        $this->load->view($this->form, $this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();
        if(empty($data['test_name'])){ $errorMessage['test_name'] = "Test Name is required."; }
        if(empty($data['head_name'])){ $errorMessage['head_name'] = "Head Name is required."; }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->testType->saveTestType($data));
        endif;
    }

    public function edit(){     
        $data = $this->input->post();
        $this->data['dataRow'] = $this->testType->getTestType($data);
        $this->load->view($this->form, $this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->testType->trash('test_type',['id'=>$id]));
        endif;
    }
	
	/*** Test Prameter ***/
    public function addTestParameter(){
        $data['id'] = $this->input->post('id');
        $this->data['test_type'] = $data['id'];
        $this->load->view($this->parameterForm, $this->data);
    }

    public function getTestParaHtml(){
        $data = $this->input->post();
        $result = $this->testType->getTestParameter($data);
        $tbody = '';$i=1;
        foreach($result as $row):
            $deleteParam = "{'postData':{'id' : '".$row->id."'},'message' : 'Test parameter','fndelete':'removeTestParameter','res_function':'resTrashTestPara'}";
            $req = ['','Range','Min','Max','Other'];
            $tbody .= '<tr>
                <td>'.$i.'</td>
                <td>'.$row->parameter.'</td>
                <td>'.$req[$row->requirement].'</td>
                <td class="text-center">
                    <button type="button" onclick="trash('.$deleteParam.');" class="btn btn-outline-danger btn-sm waves-effect waves-light btn-delete permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
                </td>
            </tr>';
            $i++;
        endforeach;

        if(empty($tbody)):
            $tbody = '<tr>
                <td colspan="4" class="text-center">No data available in table</td>
            </tr>';
        endif;

        $this->printJson(['status'=>0,'tbodyData'=>$tbody]);
    }

    public function saveTestParam(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['test_type']))
			$errorMessage['test_type'] = "Test Type is required.";
        if(empty($data['parameter']))
			$errorMessage['parameter'] = "Parameter is required.";
        if(empty($data['requirement'])):
			$errorMessage['requirement'] = "Requirement is required.";
		endif;
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->testType->saveTestParam($data));
        endif;
    }

    public function removeTestParameter(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->testType->removeTestParameter($id));
        endif;
    }

    public function testHeadSearch(){
		$this->printJson($this->testType->testHeadSearch());
	}
}
?>