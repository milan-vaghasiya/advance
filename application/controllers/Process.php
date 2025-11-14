<?php
class Process extends MY_Controller
{
    private $indexPage = "process/index";
    private $processForm = "process/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Process";
		$this->data['headData']->controller = "process";
		$this->data['headData']->pageUrl = "process";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->process->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getProcessData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addProcess(){
        $this->load->view($this->processForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['process_name']))
            $errorMessage['process_name'] = "Process name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->process->save($data));
        endif;
    }

    public function edit(){
		$this->data['dataRow'] = $this->process->getProcess(['id'=>$this->input->post('id')]);
        $this->load->view($this->processForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->process->delete($id));
        endif;
    }    
}
?>