<?php
class ExpenseMaster extends MY_Controller{
    private $indexPage = "expense_master/index";
    private $expMasterForm = "expense_master/form";
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "ExpenseMaster";
		$this->data['headData']->controller = "expenseMaster";
		$this->data['headData']->pageUrl = "expenseMaster";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->expenseMaster->getDTRows($data);
        $sendData = array();$i=($data['start'] + 1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getExpenseMasterData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addExpenseMaster(){
        $this->data['ledgerData'] = $this->party->getPartyList(['group_code'=>["'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->expMasterForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['exp_name']))
            $errorMessage['exp_name'] = "Expense Name is required.";
        if(empty($data['entry_type']))
            $errorMessage['entry_type'] = "Expense Type is required.";
        if(empty($data['acc_id']))
            $errorMessage['acc_id'] = "Ledger name is required.";
        if(empty($data['calc_type']))
            $errorMessage['calc_type'] = "Calcu. Type is required.";
        if(empty($data['calc_on']))
            $errorMessage['calc_on'] = "Calcu. ON is required.";
        if(empty($data['position']))
            $errorMessage['position'] = "Position is required.";
        if(empty($data['add_or_deduct']))
            $errorMessage['add_or_deduct'] = "Amt. Effect is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->expenseMaster->save($data));
        endif;
    }

    public function edit(){
        $this->data['ledgerData'] = $this->party->getPartyList(['group_code'=>["'ED'","'EI'","'ID'","'II'"]]);
        $this->data['dataRow'] = $this->expenseMaster->getExpenseMaster($this->input->post('id'));
        $this->load->view($this->expMasterForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->expenseMaster->delete($id));
        endif;
    }
    
}
?>