<?php
class PaymentVoucher extends MY_Controller{
    private $index = "payment_voucher/index";
    private $form = "payment_voucher/form";	
	private $bankRecon = "payment_voucher/bank_reconciliation";
	
	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "PaymentVoucher";
		$this->data['headData']->controller = "paymentVoucher";
        $this->data['headData']->pageUrl = "paymentVoucher";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'paymentVoucher']);
	}

	public function index(){
		$this->data['tableHeader'] = getAccountingDtHeader("paymentVoucher");
		$this->load->view($this->index,$this->data);
	}

    public function getDtRows($status="BCRct"){
        $data = $this->input->post();$data['vou_name_s'] = $status;
		$result = $this->paymentVoucher->getDtRows($data); 
		$sendData = array(); $i=($data['start'] + 1);
		foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$sendData[] = getPaymentVoucher($row);
		endforeach;
		$result['data'] = $sendData;
		$this->printJson($result);
	}

    public function addPaymentVoucher(){
		$this->data['partyList'] = $this->party->getPartyList();
		$this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>['"BA"','"BOL"','"BOA"','"CS"']]);

		$this->data['trans_prefix'] =  $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] =  $this->data['entryData']->trans_no;	
        $this->data['trans_number']	= $this->data['trans_prefix'].$this->data['trans_no'];
		$this->load->view($this->form,$this->data);
	}

    public function getTransNo(){
		$data = $this->input->post();
        if($data['vou_name_s'] == "BCRct"):
            $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        else:
            $this->data['trans_prefix'] = str_replace("R","P",$this->data['entryData']->trans_prefix);
        endif;
		
        $this->data['trans_no'] = $this->transMainModel->nextTransNo($this->data['entryData']->id,0,$data['vou_name_s']);
        $this->data['trans_number']	= $this->data['trans_prefix'].$this->data['trans_no'];

		$this->printJson(['status'=>1,'data'=>$this->data]);
	}

    public function getReference($data=array()){
		$postData = (!empty($data))?$data:$this->input->post();

        $postData['vou_name_s'] = (($postData['vou_name_s'] == "BCRct")?"Sale":"Purc");
		$referenceData = $this->paymentVoucher->getPartyInvoiceList($postData);		
		
		$optionsHtml = '<option value="">Select Reference</option>';
		foreach($referenceData as $row):
            $selected = (!empty($postData['ref_id']) && $row->id == $postData['ref_id'])?"selected":"";
			$optionsHtml .= '<option value="'.$row->id.'" '.$selected.'>'.$row->trans_number.'</option>';
		endforeach;
		
        if(!empty($data)):
            return $optionsHtml;
        else:
		    $this->printJson(['status'=>1,'referenceData'=>$optionsHtml]);
        endif;
	}

    public function save(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['trans_date']))
			$errorMessage['trans_date'] = "Voucher Date is required.";
		if(empty($data['vou_name_s']))
			$errorMessage['vou_name_s'] = "Entry Type is required.";
		if(empty($data['opp_acc_id']))
			$errorMessage['opp_acc_id'] = "Party Name is required.";
		if(empty($data['vou_acc_id']))
			$errorMessage['vou_acc_id'] = "Ledger Name is required.";
		if(empty($data['payment_mode']))
			$errorMessage['payment_mode'] = "Payment Mode is required.";
		if(empty($data['net_amount']))
			$errorMessage['net_amount'] = "Amount is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['vou_name_l'] = (($data['vou_name_s'] == "BCRct")?"Bank/Cash Received":"Bank/Cash Paid");
			$data['party_id'] = $data['opp_acc_id'];
            $data['entry_type'] = $this->data['entryData']->id;
			$this->printJson($this->paymentVoucher->save($data));
		endif;
	}

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->paymentVoucher->getVoucher($data['id']);
		$this->data['partyList'] = $this->party->getPartyList();
		$this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>['"BA"','"BOL"','"BOA"','"CS"']]);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->paymentVoucher->delete($id));
        endif;
    }

	public function bankReconciliation($acc_id="",$from_date="",$to_date=""){
		$this->data['acc_id'] = $acc_id;
        $this->data['from_date'] = $from_date;
        $this->data['to_date'] = $to_date;
        $this->data['bank_name'] = (!empty($acc_id))?$this->party->getParty(['id'=>$acc_id])->party_name:"";
        $this->load->view($this->bankRecon,$this->data);
	}

	public function getBankTransactions(){
		$data = $this->input->post();

		$result = $this->paymentVoucher->getBankTransactions($data);
		$tbody = "";

		$i = 0;
		foreach($result as $row):
			$tbody .= '<tr>
				<td>'.($i+1).'</td>
				<td>'.formatDate($row->trans_date).'</td>
				<td>'.$row->trans_number.'</td>
				<td>'.$row->opp_acc_name.'</td>
				<td>'.$row->doc_no.'</td>
				<td>'.formatDate($row->doc_date).'</td>
				<td>'.$row->vou_acc_name.'</td>
				<td>'.$row->net_amount.'</td>
				<td>
					<input type="date" name="item_data['.$i.'][recon_date]" class="form-control" value="'.((!empty($row->recon_date))?$row->recon_date:"").'">
					<input type="hidden" name="item_data['.$i.'][id]" value="'.$row->id.'">
					<input type="hidden" name="item_data['.$i.'][trans_date]" value="'.$row->trans_date.'">
					<div class="error recon_date_'.$i.'"></div>
				</td>
			</tr>';
			$i++;
		endforeach;
		$this->printJson(['status'=>1,'tbody'=>$tbody]);
	}

	public function saveBankReconciliation(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['item_data'])):
            $errorMessage['general_error'] = "Bank Reconciliation data is required.";
        else:
            foreach($data['item_data'] as $key=>$row):
                if(!empty($row['recon_date']) && $row['trans_date'] > $row['recon_date']):
                    $errorMessage['recon_date_'.$key] = "Invalid Date.";
                endif;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->paymentVoucher->saveBankReconciliation($data));
        endif;
    }

	public function printReceipt($jsonData=""){
        if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        else: 
            $postData = $this->input->post();
        endif;
        
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

        $p_id = (!empty($id))?$id:$postData['id'];

		$this->data['pvData'] = $pvData = $this->paymentVoucher->getVoucher($p_id);
		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url($companyData->print_header);

        $htmlFooter = '<table style="border-top:1px solid #545454;margin-top:1px;">
            <tr>
                <td class="text-right">Page No. {PAGENO}/{nbpg}</td>
            </tr>
        </table>';
				
        $pdfData = "";
        $countPT = count($printTypes); $i=0;
        foreach($printTypes as $printType):
            ++$i;           
            $this->data['printType'] = $printType;
            $this->data['maxLinePP'] = (!empty($postData['max_lines']))?$postData['max_lines']:15;
		    $pdfData .= $this->load->view('payment_voucher/print',$this->data,true); 
            if($i != $countPT): $pdfData .= "<pagebreak resetpagenum='1'>"; endif;
        endforeach;
            
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = str_replace(["/","-"," "],"_",$pvData->trans_number).'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->SetTitle($pdfFileName); 
        $mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
        $mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,(($postData['header_footer'] == 1)?5:35),10,5,5,'','','','','','','','','','A4-P');
    
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>