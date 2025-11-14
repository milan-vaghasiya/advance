<?php
class RejectionComments extends MY_Controller
{
    private $indexPage = "rejection_comment/index";
    private $formPage = "rejection_comment/form"; 

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Reason Master";
		$this->data['headData']->controller = "rejectionComments";		
        $this->data['headData']->pageUrl = "rejectionComments";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($type=1){
        $data = $this->input->post(); $data['type'] = $type;
        $result = $this->comment->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getRejectionCommentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addRejectionComment(){
        $this->load->view($this->formPage,$this->data);
    }
    
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['remark']))
             $errorMessage['remark'] = "Reason is required.";
        if($data['type'] == 2): 
            if(empty($data['code']))
                $errorMessage['code'] = "Code is required.";
        endif;
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->comment->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->comment->getComment($this->input->post('id'));
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->comment->delete($id));
        endif;
    }
}
?>