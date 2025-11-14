<?php
class PurchaseInvoice extends MY_Controller{
    private $indexPage = "purchase_invoice/index";
    private $form = "purchase_invoice/form";    

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Purchase Invoice";
		$this->data['headData']->controller = "purchaseInvoice";        
        $this->data['headData']->pageUrl = "purchaseInvoice";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'purchaseInvoice']);
	}

    public function index(){
        $this->data['tableHeader'] = getAccountingDtHeader("purchaseInvoice");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->purchaseInvoice->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPurchaseInvoiceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addInvoice(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[1,2,3]]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,4,5,6,7,8,9"]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Purchase']);

        $this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(1);
		$this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_number']))
            $errorMessage['trans_number'] = "Inv No. is required.";
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['sp_acc_id']))
            $errorMessage['sp_acc_id'] = "GST Type is required.";
        if(empty($data['itemData']))
            $errorMessage['itemData'] = "Item Details is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['doc_date'] = date("Y-m-d");
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;
            $this->printJson($this->purchaseInvoice->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->purchaseInvoice->getPurchaseInvoice(['id'=>$id,'itemList'=>1]);
        $this->data['gstinList'] = $this->party->getPartyGSTDetail(['party_id' => $dataRow->party_id]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[1,2,3]]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,4,5,6,7,8,9"]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Purchase']);

        $this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(1);
        $this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->purchaseInvoice->delete($id));
        endif;
    }

    public function getPartyInvoiceItems(){
        $data = $this->input->post();
        $this->data['orderItems'] = $this->purchaseInvoice->getPendingInvoiceItems($data);
        $this->load->view('debit_note/create_debitnote',$this->data);
    }
}
?>