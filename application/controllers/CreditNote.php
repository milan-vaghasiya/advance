<?php
class CreditNote extends MY_Controller{
    private $indexPage = "credit_note/index";
    private $form = "credit_note/form";    

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Credit Note";
		$this->data['headData']->controller = "creditNote";        
        $this->data['headData']->pageUrl = "creditNote";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'creditNote']);
	}

    public function index(){
        $this->data['tableHeader'] = getAccountingDtHeader("creditNote");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->creditNote->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getCreaditNoteData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCreditNote(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3"]);
        $this->data['itemList'] = $this->item->getItemList();
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Sales']);

        $this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(2);
        $this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['sp_acc_id']))
            $errorMessage['sp_acc_id'] = "GST Type is required.";
        if(empty($data['itemData'])):
            $errorMessage['itemData'] = "Item Details is required.";
        endif;
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;
            $this->printJson($this->creditNote->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->creditNote->getCreditNote(['id'=>$id,'itemList'=>1]);
        $this->data['gstinList'] = $this->party->getPartyGSTDetail(['party_id' => $dataRow->party_id]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category' => "1,2,3"]);
        $this->data['itemList'] = $this->item->getItemList();
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Sales']);

        $this->data['taxClassList'] = $this->taxClass->getActiveTaxClass((($dataRow->order_type == "Increase Purchase")?1:2));
        $this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->creditNote->delete($id));
        endif;
    }

    public function getCreditNoteTypes(){
        $data = $this->input->post();
        $accounts = $this->taxClass->getActiveTaxClass(($data['order_type'] == "Increase Purchase")?1:2);
        $type = ($data['order_type'] == "Increase Purchase")?"PURCHASE":"SALES";
        $accountOptions = getTaxClassListOption($accounts);
        $this->printJson(['status'=>1,'accountOptions'=>$accountOptions,'inv_type'=>$type]);
    }

    public function printCreditNote(){
        $postData = $this->input->post();
        
        $printTypes = array();
        if(!empty($postData['original'])):
            $printTypes[] = "ORIGINAL";
        endif;

        if(!empty($postData['duplicate'])):
            $printTypes[] = "DUPLICATE";
        endif;

        if(!empty($postData['triplicate'])):
            $printTypes[] = "TRIPLICATE";
        endif;

        if(!empty($postData['extra_copy'])):
            for($i=1;$i<=$postData['extra_copy'];$i++):
                $printTypes[] = "EXTRA COPY";
            endfor;
        endif;

        $postData['header_footer'] = (!empty($postData['header_footer']))?1:0;
        $this->data['header_footer'] = $postData['header_footer'];

        $inv_id = (!empty($id))?$id:$postData['id'];

		$this->data['invData'] = $invData = $this->creditNote->getCreditNote(['id'=>$inv_id,'itemList'=>1]);
		$this->data['partyData'] = $this->party->getParty(['id'=>$invData->party_id]);
        
        $taxClass = $this->taxClass->getTaxClass($invData->tax_class_id);
        $this->data['taxList'] = (!empty($taxClass->tax_ids))?$this->taxMaster->getTaxList(['tax_ids'=>$taxClass->tax_ids]):array();
        $this->data['expenseList'] = (!empty($taxClass->expense_ids))?$this->expenseMaster->getExpenseList(['expense_ids'=>$taxClass->expense_ids]):array();

		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
		$this->data['termsData'] = (!empty($invData->termsConditions) ? $invData->termsConditions: "");
		$response="";
		$logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
				
        $pdfData = "";
        $countPT = count($printTypes); $i=0;
        foreach($printTypes as $printType):
            ++$i;
            $this->data['printType'] = $printType;
            $this->data['maxLinePP'] = (!empty($postData['max_lines']))?$postData['max_lines']:10;
		    $pdfData .= $this->load->view('credit_note/print',$this->data,true);
            if($i != $countPT): $pdfData .= "<pagebreak>"; endif;
        endforeach;


            
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = str_replace(["/","-"," "],"_",$invData->trans_number).'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P','','','','',10,5,(($postData['header_footer'] == 1)?5:35),5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }
}
?>