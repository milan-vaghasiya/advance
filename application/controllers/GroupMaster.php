<?php
class GroupMaster extends MY_Controller{
    private $indexPage = "group_master/index";
    private $formPage = "group_master/form";
   
    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Group Master";
		$this->data['headData']->controller = "groupMaster";
		$this->data['headData']->pageUrl = "groupMaster";
	}

    public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
		$data=$this->input->post();
		$result = $this->group->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getGroupMasterData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addGroup(){
        $this->data['masterGroupList'] = $this->group->getGroupList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['name']))
            $errorMessage['name'] = "Group Name is required.";
        if(empty($data['under_group_id']))
            $errorMessage['under_group_id'] = "Perent Group is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->group->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->group->getGroup($data);
        $this->data['masterGroupList'] = $this->group->getGroupList();
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->group->delete($id));
        endif;
    }
}
?>