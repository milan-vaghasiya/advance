<?php
class JournalEntry extends MY_Controller{
    private $indexPage = "journal_entry/index";
    private $form = "journal_entry/form";    
    private $gstJrnlForm = "journal_entry/gst_journal_form";
    private $gstHavalaForm = "journal_entry/gst_havala_form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Journal Entry";
		$this->data['headData']->controller = "journalEntry";        
        $this->data['headData']->pageUrl = "journalEntry";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'journalEntry']);
	}

    public function index(){
        $this->data['tableHeader'] = getAccountingDtHeader("journalEntry");
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList();
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->journalEntry->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getJournalEntryData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addJournalEntry(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList();
        $this->load->view($this->form, $this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['itemData'])):
            $errorMessage['item_name_error'] = 'Entry is required.';
        else:
            if(array_sum(array_column($data['itemData'],'credit_amount')) != array_sum(array_column($data['itemData'],'debit_amount'))):
                $errorMessage['total_cr_dr_amt'] = "Cr. Amount and Dr. Amount mismatch.";
            endif;
        endif;
        
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;

            $this->printJson($this->journalEntry->save($data));
        endif;
    }

    public function edit($id=0){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $dataRow = $this->journalEntry->getJournalEntry($id);
        /* $this->data['partyList'] = $this->party->getPartyList();
        $this->load->view($this->form, $this->data); */
        $this->printJson(['status'=>1,'data'=>$dataRow]);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		else:
			$this->printJson($this->journalEntry->delete($id));
		endif;
	}

    public function printJV($id){
		$this->data['jvData'] = $jvData = $this->journalEntry->getJournalEntry($id);

		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
		
		$logo = base_url('assets/images/logo.png');
        $this->data['letter_head'] =  base_url($companyData->print_header);		

        $pdfData = $this->load->view('journal_entry/print',$this->data,true);		
		
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:25%;">JV No. & Date : '.$jvData->trans_number.' ['.formatDate($jvData->trans_date).']</td>
                    <td style="width:25%;"></td>
                    <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';
		$mpdf = new \Mpdf\Mpdf();

		$pdfFileName = $filePath.'/' . str_replace(["/","-"],"_",$jvData->trans_number) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

    public function addGstJournalEntry(){
        $this->data['entry_type'] = $this->data['entryData']->id;     
        $this->load->view($this->gstJrnlForm, $this->data);
    }

    public function getGstLedgers(){
        $data = $this->input->post();
        $result = $this->journalEntry->getGstLedgerClosing($data);
        $this->printJson(['status'=>1,'data'=>$result]);
    }

    public function saveGstJournalEntry(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['itemData']))
            $errorMessage['item_name_error'] = 'Entry is required.';


        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $oppAccounts = $this->party->getPartyList(['system_code'=>["'IGSTACC'","'CGSTACC'","'SGSTACC'","'GSTCESSACC'"]]);
            $groupedOppAccounts = array_reduce($oppAccounts, function($itemData, $row) {
                $itemData[$row->system_code] = $row;
                return $itemData;
            }, []);

            $nextNo = $this->transMainModel->getNextNo(['tableName'=>'trans_main','no_column'=>'trans_no','condition'=>'trans_date >= "'.$this->startYearDate.'" AND trans_date <= "'.$this->endYearDate.'"  AND vou_name_s = "'.$this->data['entryData']->vou_name_short.'" AND trans_prefix = "GSTJRNL/"']);

            $itemData = [];
            foreach($data['itemData'] as $row):
                $itemData = [];                

                $oppAccId = $oppAccName = "";
                if(in_array($row['system_code'],['IGSTOPACC','IGSTIPACC','IGSTIPRCMACC'])):
                    $oppAccId = $groupedOppAccounts['IGSTACC']->id;
                    $oppAccName = $groupedOppAccounts['IGSTACC']->party_name;                    
                elseif(in_array($row['system_code'],['CGSTOPACC','CGSTIPACC','CGSTIPRCMACC'])):
                    $oppAccId = $groupedOppAccounts['CGSTACC']->id;
                    $oppAccName = $groupedOppAccounts['CGSTACC']->party_name;
                elseif(in_array($row['system_code'],['SGSTOPACC','UTGSTOPACC','SGSTIPACC','UTGSTIPACC','SGSTIPRCMACC'])):
                    $oppAccId = $groupedOppAccounts['SGSTACC']->id;
                    $oppAccName = $groupedOppAccounts['SGSTACC']->party_name;
                elseif(in_array($row['system_code'],['CESSOPACC','CESSIPACC'])):
                    $oppAccId = $groupedOppAccounts['GSTCESSACC']->id;
                    $oppAccName = $groupedOppAccounts['GSTCESSACC']->party_name;
                endif;

                unset($row['system_code']);
                $row['price'] = abs($row['price']);
                $itemData[] = $row;

                $cr_dr = ($row['cr_dr'] == "DR")?"CR":"DR";
                $creditAmount = ($row['cr_dr'] == "DR")?$row['debit_amount']:0;
                $debitAmount = ($row['cr_dr'] == "CR")?$row['credit_amount']:0;

                $itemData[] = [
                    'id' => '',
                    'acc_id' => $oppAccId,
                    'ledger_name' => $oppAccName,
                    'price' => $row['price'],
                    'cr_dr' => $cr_dr,
                    'credit_amount' => $creditAmount,
                    'debit_amount' => $debitAmount,
                    'item_remark' => $row['item_remark']
                ];

                $postData['id'] = "";
                $postData['entry_type'] = $this->data['entryData']->id;
                $postData['trans_date'] = $data['trans_date'];
                $postData['trans_prefix'] = "GSTJRNL/";
                $postData['trans_no'] = $nextNo;
                $postData['trans_number'] = $postData['trans_prefix'].$postData['trans_no'];
                $postData['vou_name_l'] = $this->data['entryData']->vou_name_long;
                $postData['vou_name_s'] = $this->data['entryData']->vou_name_short;
                $postData['itemData'] = $itemData;

                $result = $this->journalEntry->save($postData);
                $nextNo++;
            endforeach;

            $this->printJson($result);
        endif;
    }

    public function addGstHavalaEntry(){
        $this->data['entry_type'] = $this->data['entryData']->id;     
        $this->load->view($this->gstHavalaForm, $this->data);
    }

    public function saveGstHavalaEntry(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['itemData']))
            $errorMessage['item_name_error'] = 'Entry is required.';
        if(!empty($data['itemData']) && array_sum(array_column($data['itemData'],'price')) == 0)
            $errorMessage['item_name_error'] = 'Please enter Havala Amount.';

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $oppAccounts = $this->party->getPartyList(['system_code'=>["'IGSTACC'","'CGSTACC'","'SGSTACC'","'GSTCESSACC'"]]);
            $groupedOppAccounts = array_reduce($oppAccounts, function($itemData, $row) {
                $itemData[$row->system_code] = $row;
                return $itemData;
            }, []);

            $nextNo = $this->transMainModel->getNextNo(['tableName'=>'trans_main','no_column'=>'trans_no','condition'=>'trans_date >= "'.$this->startYearDate.'" AND trans_date <= "'.$this->endYearDate.'" AND vou_name_s = "'.$this->data['entryData']->vou_name_short.'" AND trans_prefix = "GSTHVL/"']);

            $itemData = [];
            foreach($data['itemData'] as $row):
                if(!empty(floatval($row['price']))):
                    $itemData = [];                

                    $oppAccId = $oppAccName = "";
                    if(in_array($row['system_code'],['CLIGSTACC','CLIGSTFEESACC','CLIGSTINTACC','CLIGSTOTHACC','CLIGSTPENAACC'])):
                        $oppAccId = $groupedOppAccounts['IGSTACC']->id;
                        $oppAccName = $groupedOppAccounts['IGSTACC']->party_name;                    
                    elseif(in_array($row['system_code'],['CLCGSTACC','CLCGSTFEESACC','CLCGSTINTACC','CLCGSTOTHACC','CLCGSTPENAACC'])):
                        $oppAccId = $groupedOppAccounts['CGSTACC']->id;
                        $oppAccName = $groupedOppAccounts['CGSTACC']->party_name;
                    elseif(in_array($row['system_code'],['CLSGSTACC','CLSGSTFEESACC','CLSGSTINTACC','CLSGSTOTHACC','CLSGSTPENAACC'])):
                        $oppAccId = $groupedOppAccounts['SGSTACC']->id;
                        $oppAccName = $groupedOppAccounts['SGSTACC']->party_name;
                    elseif(in_array($row['system_code'],['CLGSTCESSACC','CLGSTCESSFEESACC','CLGSTCESSINTACC','CLGSTCESSOTHACC','CLGSTCESSPENAACC'])):
                        $oppAccId = $groupedOppAccounts['GSTCESSACC']->id;
                        $oppAccName = $groupedOppAccounts['GSTCESSACC']->party_name;
                    endif;

                    unset($row['system_code']);
                    $row['price'] = abs($row['price']);
                    $row['credit_amount'] = ($row['cr_dr'] == "CR")?abs($row['price']):0;
                    $row['debit_amount'] = ($row['cr_dr'] == "DR")?abs($row['price']):0;
                    $itemData[] = $row;

                    $cr_dr = ($row['cr_dr'] == "DR")?"CR":"DR";
                    $creditAmount = ($cr_dr == "CR")?abs($row['price']):0;
                    $debitAmount = ($cr_dr == "DR")?abs($row['price']):0;

                    $itemData[] = [
                        'id' => '',
                        'acc_id' => $oppAccId,
                        'ledger_name' => $oppAccName,
                        'price' => $row['price'],
                        'cr_dr' => $cr_dr,
                        'credit_amount' => $creditAmount,
                        'debit_amount' => $debitAmount,
                        'item_remark' => $row['item_remark']
                    ];

                    $postData['id'] = "";
                    $postData['entry_type'] = $this->data['entryData']->id;
                    $postData['trans_date'] = $data['trans_date'];
                    $postData['trans_prefix'] = "GSTHVL/";
                    $postData['trans_no'] = $nextNo;
                    $postData['trans_number'] = $postData['trans_prefix'].$postData['trans_no'];
                    $postData['vou_name_l'] = $this->data['entryData']->vou_name_long;
                    $postData['vou_name_s'] = $this->data['entryData']->vou_name_short;
                    
                    $postData['itemData'] = $itemData;

                    $result = $this->journalEntry->save($postData);
                    $nextNo++;
                endif;
            endforeach;

            $this->printJson($result);
        endif;
    }

}
?>