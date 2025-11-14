<?php
class SalesQuotation extends MY_Controller{
    private $indexPage = "sales_quotation/index";
    private $form = "sales_quotation/form";
    private $revHistory = "sales_quotation/revision_history";
    private $confirmQuotation = "sales_quotation/confirm_quotation";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Sales Quotation";
		$this->data['headData']->controller = "salesQuotation";        
        $this->data['headData']->pageUrl = "salesQuotation";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'salesQuotation','tableName'=>'sq_master']);
	}

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader("salesQuotation");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->salesQuotation->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getSalesQuotationData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function createQuotation($id){
        $dataRow = $this->salesEnquiry->getSalesEnquiry(['id'=>$id,'itemList'=>1]); 
        $this->data['gstinList'] = $this->party->getPartyGSTDetail(['party_id' => $dataRow->party_id]);
        
        $dataRow->from_entry_type = $dataRow->entry_type;
        $dataRow->ref_id = $dataRow->id;
        $dataRow->entry_type = "";
        $dataRow->id = "";
        $dataRow->trans_prefix = "";
        $dataRow->trans_no = "";
        $dataRow->trans_number = "";
		unset($dataRow->itemList);
		
        $this->data['enq_id'] = $id;
        $this->data['dataRow'] = $dataRow;
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category' => "1",'party_type'=>"1,2"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1,'active_item'=>"1,2"]);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["12"]]);
        $this->load->view($this->form,$this->data);
    }
	
    public function getInqRows(){
		$postData = $this->input->post();
		$itemRows = [];
		if(!empty($postData['enq_id']))
		{
			$dataRow = $this->salesEnquiry->getSalesEnquiry(['id'=>$postData['enq_id'],'itemList'=>1]); 
			
			foreach($dataRow->itemList as &$row):
				$row->from_entry_type = $row->entry_type;
				$row->ref_id = $row->id;
				$row->entry_type = "";
				$row->id = "";
				$row->row_index = "";
                $row->unit_id = ($row->uom);

				$row->taxable_amount = $row->amount = round(($row->qty * $row->price),2);
				if(!empty($row->disc_per) && !empty($row->amount)):
					$row->disc_amount = round((($row->disc_per * $row->amount) / 100),2);
					$row->taxable_amount = $row->taxable_amount - $row->disc_amount;
				else:
					$row->disc_per = 0;
					$row->disc_amount = 0;
				endif;
                $row->gst_per = floatVal($row->gst_per);

                $row->igst_per = round($row->gst_per,2);
                $row->cgst_per = $row->sgst_per = (!empty($row->gst_per) && $row->gst_per >0) ? round(($row->gst_per / 2),2) : 0;
                $row->gst_amount = $row->igst_amount = $row->cgst_amount = $row->sgst_amount = 0;

				$row->net_amount = round($row->taxable_amount,2);
  
				if(!empty($row->taxable_amount) AND !empty($row->gst_per)):
					$row->gst_amount = round((($row->gst_per * $row->taxable_amount) / 100),2);
					$row->igst_per = round($row->gst_per,2);
					$row->igst_amount = round($row->gst_amount,2);
					$row->cgst_per = round(($row->gst_per / 2),2);
					$row->cgst_amount = round(($row->gst_amount / 2),2);
					$row->sgst_per = round(($row->gst_per / 2),2);
					$row->sgst_amount = round(($row->gst_amount / 2),2);
					$row->net_amount = round(($row->taxable_amount + $row->gst_amount),2);
				endif;
				unset($row->created_by, $row->created_at, $row->updated_by, $row->updated_at, $row->is_delete);
				$itemRows[] = $row;
			endforeach;
		}
		else	// Get Edit Rows
		{
			$dataRow = $this->salesQuotation->getSalesQuotation(['id'=>$postData['id'],'itemList'=>1]);
			$itemRows = [];
			if(!empty($dataRow->itemList)):
				foreach($dataRow->itemList as $row):
					$row->row_index = "";
					$row->gst_per = floatVal($row->gst_per);
                    $row->unit_id = ($row->uom);
					unset($row->created_by, $row->created_at, $row->updated_by, $row->updated_at, $row->is_delete);
					$itemRows[] = $row;
				endforeach;
			endif;
		}
		
		$this->printJson(['itemRows'=>json_encode($itemRows)]);
    }

    public function addQuotation(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category' => "1",'party_type'=>"1,2"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1,'active_item'=>"1,2"]);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["12"]]);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
		
		if (formatDate($data['trans_date'], 'Y-m-d') < $this->startYearDate OR formatDate($data['trans_date'], 'Y-m-d') > $this->endYearDate)
			$errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
			
        if(empty($data['itemData']))
            $errorMessage['itemData'] = "Item Details is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])):
                $data['trans_no'] = $this->data['entryData']->trans_no;
                $data['trans_number'] = $this->data['entryData']->trans_prefix.$data['trans_no'];
            endif; 
			
            if (!empty($data['is_rev'])) :
                $data['doc_date'] = date('Y-m-d');
            else :
                $data['doc_date'] = formatDate($data['trans_date'], 'Y-m-d');
            endif;

            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short; 
            $this->printJson($this->salesQuotation->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->salesQuotation->getSalesQuotation(['id'=>$id,'itemList'=>1]);
        $this->data['gstinList'] = $this->party->getPartyGSTDetail(['party_id' => $dataRow->party_id]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category' => "1",'party_type'=>"1,2"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1,'active_item'=>"1,2"]);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["12"]]);
        $this->load->view($this->form,$this->data);
    }

    public function reviseQuotation($id){
        $this->data['is_rev'] = 1;
        $this->data['dataRow'] = $dataRow = $this->salesQuotation->getSalesQuotation(['id'=>$id,'itemList'=>1]);
        $this->data['gstinList'] = $this->party->getPartyGSTDetail(['party_id' => $dataRow->party_id]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category' => "1",'party_type'=>"1,2"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1,'active_item'=>"1,2"]);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["12"]]);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->salesQuotation->delete($id));
        endif;
    }

    public function revisionHistory(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->salesQuotation->getQuotationRevisionList($data);
        $this->load->view($this->revHistory,$this->data);
    }    

    public function printQuotation($id,$pdf_type=''){
        $this->data['dataRow'] = $dataRow = $this->salesQuotation->getSalesQuotation(['id'=>$id,'itemList'=>1]);
        $this->data['partyData'] = $this->party->getParty(['id'=>$dataRow->party_id]);
        $this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
        $this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
		$this->data['termsData'] = (!empty($dataRow->termsConditions) ? $dataRow->termsConditions: "");
        
        $logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
        
        $pdfData = $this->load->view('sales_quotation/print', $this->data, true);
        
        
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
            <tr>
                <td style="width:25%;">Qtn. No. & Date : '.$dataRow->trans_number . ' [' . formatDate($dataRow->trans_date) . ']</td>
                <td style="width:25%;"></td>
                <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
            </tr>
        </table>';
        
		$mpdf = new \Mpdf\Mpdf();
		$filePath = realpath(APPPATH . '../assets/uploads/sales_quotation/');
        $pdfFileName = $filePath.'/' . str_replace(["/","-"],"_",$dataRow->trans_number) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(100,100));
        $mpdf->showWatermarkImage = true;
        $mpdf->SetHTMLFooter($htmlFooter);
        
        $mpdf->SetDefaultBodyCSS('background', "url('".$lh_bg."')");
        $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
        
		$mpdf->AddPage('P','','','','',5,5,5,25,5,5,'','','','','','','','','','A4-P');
        $mpdf->WriteHTML($pdfData);
		
		ob_clean();
		$mpdf->Output($pdfFileName, 'I');		
    }
    
    public function getPartyQuotation(){
        $data = $this->input->post();
        $this->data['orderItems'] = $this->salesQuotation->getPendingQuotationItems($data);
        $this->load->view('sales_order/create_order',$this->data);
    }

	public function confirmQuotation(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->salesQuotation->getSalesQuotation(['id'=>$data['id'],'itemList'=>1,'is_approve'=>1]);
        $this->load->view($this->confirmQuotation,$this->data);
    }
	
    public function saveConfirmQuotation(){
        $data = $this->input->post();
        $errorMessage = array();

        if(!empty($data['is_approve'])){
            if(empty($data['approve_date'])):
                $errorMessage['approve_date'] = "Confirm Date is Required";
            endif;    
        }
      
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->salesQuotation->saveConfirmQuotation($data));
        endif;
    }
	
	public function getRevisionList(){
		$data = $this->input->post();
		
		$revList = $this->ecn->getItemRevision(['item_id'=>$data['item_id']]);
		$revHtml = '';
		if(!empty($revList)){
			foreach($revList as $row){
				$selected = (!empty($data['rev_no']) && $data['rev_no'] == $row->rev_no)?'selected':'';
				$revHtml .= '<option value="'.$row->rev_no.'" data-drw_no = "'.$row->drw_no.'" '.$selected.'>'.$row->rev_no.' [Drw No : '.$row->drw_no.']'.'</option>';
			}
		}
        $this->printJson(['revHtml'=>$revHtml]);
	}

    public function approveQuotation(){
		$data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesQuotation->approveQuotation($data));
		endif;
	}

}
?>