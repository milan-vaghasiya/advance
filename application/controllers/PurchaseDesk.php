<?php
class PurchaseDesk extends MY_Controller{
	private $indexPage = "purchase_desk/index";
    private $form = "purchase_desk/form";
	private $quoteForm = "purchase_desk/quote_form";

	public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "PURCHASE DESK";
		$this->data['headData']->controller = "purchaseDesk";
		$this->data['headData']->pageUrl = "purchaseDesk";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'purchaseDesk','tableName'=>'purchase_enquiry']);
	}
	
	public function index(){
		$this->data['headData']->pageTitle = "Purchase Desk";
		$this->data['tableHeader'] = getPurchaseDtHeader("purchaseDesk");
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows($status = 1){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->purchase->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPurchaseDeskData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function addEnquiry(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>'1,2,3']);
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['final_category'=>0, 'ref_id'=>0]);
		$this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["8"]]);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->load->view($this->form,$this->data);
    }

    public function saveEnquiry(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_no'])){
            $errorMessage['trans_no'] = 'Enquiry No. is required.';
		}
		if(empty($data['trans_date'])){
            $errorMessage['trans_date'] = 'Enquiry Date is required.';
		}else{
			if (($data['trans_date'] < $this->startYearDate) OR ($data['trans_date'] > $this->endYearDate)){
				$errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
			}
		}
        if(empty($data['party_id'][0])){
            $errorMessage['party_id'] = "Supplier Name is required.";
		}
		if(empty($data['itemData']))
            $errorMessage['itemData'] = "Item Details is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])):
                $data['trans_no'] = $this->data['entryData']->trans_no;
                $data['trans_number'] = $this->data['entryData']->trans_prefix.$data['trans_no'];
            endif;
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;

            $p_id = count($data['party_id']);
            if($p_id > 0){
                foreach($data['party_id'] as $row){
                    $data['party_id'] = $row;
                    $result = $this->purchase->saveEnquiry($data);
                }
            }
            $this->printJson($result);
        endif;
    }

    public function editEnquiry($jsonData=""){
		$data = array();
		if(!empty($jsonData)){$data = (Array) decodeURL($jsonData);}
		if(empty($data['is_regenerate'])){
			$this->data['dataRow'] = $dataRow = $this->purchase->getPurchaseEnqList(['trans_number'=>$data['trans_number'],'party_id'=>$data['party_id']]);
        }else{
			$this->data['dataRow'] = $dataRow = $this->purchase->getPurchaseEnqList(['trans_number'=>$data['trans_number'],'party_id'=>$data['party_id'],'status'=>3]);
		}
		$this->data['partyList'] = $this->party->getPartyList(['party_category'=>'1,2,3']);
		$this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["8"]]);
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['final_category'=>0, 'ref_id'=>0]);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['party_id'] = $data['party_id'];
		$this->data['is_regenerate'] = $data['is_regenerate'];
		$this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
		$this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->load->view($this->form,$this->data);
    }

    public function deleteEnquiry(){
        $data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchase->deleteEnquiry($data));
		endif;
    }

	public function saveQuotation(){
        $data = $this->input->post();
        $errorMessage = array();
		
		if(empty($data['enq_id'][0])):
            $errorMessage['item_name_error'] = "Please select Item.";
        else:
			if(!empty($data['enq_id'])):
				foreach($data['enq_id'] as $key=>$value):
					foreach($data['item_id'] as $key=>$value):
						if($data['feasible'][$key] != 2):
							if(empty($value)):
								$errorMessage['item_id'.$data['enq_id'][$key]] = "Item Name is required.";
							endif;
						endif;
					endforeach;
					
					foreach($data['qty'] as $key=>$value):
						if($data['feasible'][$key] != 2):
							if(empty($value)):
								$errorMessage['qty'.$data['enq_id'][$key]] = "Qty is required.";
							endif;
						endif;
					endforeach;
					
					foreach($data['price'] as $key=>$value):
						if($data['feasible'][$key] != 2):
							if(empty($value)):
								$errorMessage['price'.$data['enq_id'][$key]] = "Price is required.";
							endif;
						endif;
					endforeach;
					
					foreach($data['quote_no'] as $key=>$value):
						if($data['feasible'][$key] != 2):
							if(empty($value)):
								$errorMessage['quote_no'.$data['enq_id'][$key]] = "Quotation No. is required.";
							endif;
						endif;
					endforeach;
					
					foreach($data['quote_date'] as $key=>$value):
						if($data['feasible'][$key] != 2):
							if(empty($value)):
								$errorMessage['quote_date'.$data['enq_id'][$key]] = "Quotation Date is required.";
							endif;
						endif;
					endforeach;
					
					foreach($data['delivery_date'] as $key=>$value):
						if($data['feasible'][$key] != 2):
							if(empty($value)):
								$errorMessage['delivery_date'.$data['enq_id'][$key]] = "Delivery Date is required.";
							endif;
						endif;
					endforeach;
				endforeach;
			endif;
		endif;
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$result = $this->purchase->saveQuotation($data);
            $this->printJson($result);
        endif;
    }

    public function chageEnqStatus(){
		$data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$result = $this->purchase->chageEnqStatus($data);
			$result['enq_id'] = $data['enq_id'];
			$this->printJson($result);
		endif;
	}
	
	public function getItemList(){
		$data = $this->input->post();
		$itemList = $this->item->getItemList($data);
		$options = '<option value="">Select Item</option>
					<option value="-1">New Item</option>';
		if(!empty($itemList)){
			foreach($itemList as $row){
				$selected = (!empty($data['item_id']) && $data['item_id'] == $row->id) ? "selected" : "";
				$row->item_name = (!empty($row->item_code)) ? '['.$row->item_code.'] '.$row->item_name : $row->item_name;
				$options .= '<option value="'.$row->id.'" '.$selected.'>'.$row->item_name.'</option>';
			}
		}
		$this->printJson(['status'=>1, 'options'=>$options]);
	}
	
	public function getItemDetails(){
		$data = $this->input->post();
		$unitList = $this->item->itemUnits();
		$itemData = $this->item->getItem($data);

		$options = '<option value="0">--</option>';
		if(!empty($unitList)){
			foreach($unitList as $row){
				$selected = (!empty($itemData->unit_id) && $itemData->unit_id == $row->id) ? 'selected' : '';
				$disabled = (!empty($itemData->unit_id) && $itemData->unit_id != $row->id) ? 'disabled' : '';
				$options .= '<option value="'.$row->id.'" '.$selected.' '.$disabled.'>['.$row->unit_name.'] '.$row->description.'</option>';
			}
		}
		$this->printJson(['status'=>1, 'options'=>$options]);
	}
	
	public function quoteConfirm(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->purchase->getEnquiryData($data);
        $this->load->view($this->quoteForm,$this->data);
    }

	public function addEnqFromIndent($id){ 
		$this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>'1,2,3']);
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['final_category'=>0, 'ref_id'=>0]);
		$this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["8"]]);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['indentItemList'] = $this->purchaseIndent->getPurchaseRequestForOrder($id);
		$this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view($this->form,$this->data);
	}

	public function printEnquiry($jsonData=""){
		$data = (!empty($jsonData) ? (Array) decodeURL($jsonData) : []);
		$this->data['dataRow'] = $this->purchase->getPurchaseEnqList($data);
		$enqData = $this->data['dataRow'][0];
		$this->data['partyData'] = $this->party->getParty(['id'=>$data['party_id']]);
		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
		$this->data['termsData'] = (!empty($enqData->termsConditions) ? $enqData->termsConditions: "");
		
		$logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
						
		
		$prepare = $this->employee->getEmployee(['id'=>$enqData->created_by]);
		$this->data['prepareBy'] = $prepareBy = $prepare->emp_name.' <br>('.formatDate($enqData->created_at).')'; 
		$this->data['approveBy'] = $approveBy = '';
		if(!empty($enqData->is_approve)){
			$approve = $this->employee->getEmployee(['id'=>$enqData->is_approve]);
			$this->data['approveBy'] = $approveBy .= $approve->emp_name.' <br>('.formatDate($enqData->approve_date).')'; 
		}

        $pdfData = $this->load->view('purchase_desk/print',$this->data,true);
		
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:25%;">Enquiry No. & Date : '.$enqData->trans_number.' ['.formatDate($enqData->trans_date).']</td>
                    <td style="width:25%;"></td>
                    <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';
        
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = $data['party_id'].'_'.str_replace(["/","-"," "],"_",$data['trans_number']).'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(100,100));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

	public function addEnqFromForecast($postData){ 
		$data = (!empty($postData) ? json_decode(urldecode($postData)) : []);
		$so_ids = implode(',',array_column($data,'id'));
		// $qty = array_column($data,'qty');
		$this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>'1,2,3']);
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['final_category'=>0, 'ref_id'=>0]);
		$this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["8"]]);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
		// $this->data['qty'] = (!empty($qty) ? $qty : 0);
        // $this->data['rmList'] = $this->item->getProductKitData(['rm_ids'=>$ids, 'group_by'=>'item_kit.ref_item_id']);
		$this->data['rmList'] = $this->purchaseIndent->getForecastDtRows(['so_ids'=>$so_ids,'rowData'=>'1']);
        $this->load->view($this->form,$this->data);
	}
}
?>