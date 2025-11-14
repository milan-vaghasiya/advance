<?php
class HsnMaster extends MY_Controller{
    private $indexPage = "hsn_master/index";
    private $hsnForm = "hsn_master/form";   

	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "HSN Master";
		$this->data['headData']->controller = "hsnMaster";
        $this->data['headData']->pageUrl = "hsnMaster";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $data = $this->input->post(); 
        $result = $this->hsnModel->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row): 
            $row->sr_no = $i++;         
            $sendData[] = getHSNMasterData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addHSN(){
        $this->load->view($this->hsnForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();

        if(empty($data['hsn']))
			$errorMessage['hsn'] = "HSN is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->hsnModel->save($data));
        endif;
    }

    public function edit(){     
        $data = $this->input->post();
        $this->data['dataRow'] = $this->hsnModel->getHSNDetail($data);    
        $this->load->view($this->hsnForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->hsnModel->delete($id));
        endif;
    }

    public function getHSNListOnType(){
        $result = $this->hsnModel->getHSNListOnType();
		$searchResult = array();
		foreach($result as $row){ $searchResult[] = $row->hsn_code; }
		$this->printJson($searchResult);
	}
}