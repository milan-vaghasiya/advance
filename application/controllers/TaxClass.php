<?php
class TaxClass extends MY_Controller{
    private $index = "tax_class/index";
    private $form = "tax_class/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Tax Class";
		$this->data['headData']->controller = "taxClass";
		$this->data['headData']->pageUrl = "taxClass";
	}

    public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->taxClass->getDTRows($data);
        $sendData = array();$i=($data['start'] + 1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getTaxClassData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addTaxClass(){
        $this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'SA'","'PA'","'FA'","'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['tax_class_name']))
            $errorMessage['tax_class_name'] = "Class Name is required.";
        if(empty($data['sp_acc_id']))
            $errorMessage['sp_acc_id'] = "Ledger Name is required.";
        if(empty($data['tax_class']))
            $errorMessage['tax_class'] = "Class code is required.";
        /* if(empty($data['tax_ids']))
            $errorMessage['tax_ids'] = "Tax Name is required."; 
        if(empty($data['expense_ids']))
            $errorMessage['expense_ids'] = "Expense Name is required.";*/
            
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->taxClass->save($data));
        endif;
    }

    public function edit(){
        $this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'SA'","'PA'","'FA'","'ED'","'EI'","'ID'","'II'"]]);
        $this->data['dataRow'] = $this->taxClass->getTaxClass($this->input->post('id'));
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->taxClass->delete($id));
        endif;
    }

    public function getTaxClassAccountList(){
        $type = $this->input->post('type');
        $this->data['taxList'] = $this->taxMaster->getActiveTaxList($type);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList($type);
        $this->printJson(['status'=>1,'data'=>$this->data]);
    }
}
?>