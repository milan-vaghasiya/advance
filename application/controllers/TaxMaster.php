<?php
class TaxMaster extends MY_Controller{
    private $indexPage = "tax_master/index";
    private $expMasterForm = "tax_master/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Tax Master";
		$this->data['headData']->controller = "taxMaster";
		$this->data['headData']->pageUrl = "taxMaster";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->taxMaster->getDTRows($data);
        $sendData = array();$i=($data['start'] + 1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getTaxMasterData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addtaxMaster(){
        $this->data['ledgerData'] = $this->party->getPartyList(['group_code'=>["'DT'"]]);
        $this->load->view($this->expMasterForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['name']))
            $errorMessage['name'] = "Tax Name is required.";
        if(empty($data['tax_type']))
            $errorMessage['tax_type'] = "Tax Type is required.";
        if($data['calculation_type'] == "")
            $errorMessage = "Calcu. Type is required.";
        if(empty($data['acc_id']))
            $errorMessage['acc_id'] = "Ledger name is required.";
        if(empty($data['add_or_deduct']))
            $errorMessage['add_or_deduct'] = "Amount effect is required.";
            
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->taxMaster->save($data));
        endif;
    }

    public function edit(){
        $this->data['ledgerData'] = $this->party->getPartyList(['group_code'=>["'DT'"]]);
        $this->data['dataRow'] = $this->taxMaster->getTaxMaster($this->input->post('id'));
        $this->load->view($this->expMasterForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->taxMaster->delete($id));
        endif;
    }
    
}
?>