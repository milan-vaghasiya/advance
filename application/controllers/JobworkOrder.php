<?php
class JobworkOrder extends MY_Controller{

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Jobwork Order";
		$this->data['headData']->controller = "jobworkOrder";
		$this->data['headData']->pageUrl = "jobworkOrder";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionDtHeader('jobworkOrder');
        $this->load->view('jobwork_order/index',$this->data);
    }

    public function getDTRows($trans_status = 1){
        $data = $this->input->post();$data['trans_status'] = $trans_status;
        $result = $this->jobworkOrder->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
			$row->unit_id = $this->unit_id;
            $sendData[] = getJobworkOrderData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addOrder(){
        $trans_prefix = 'JWO/'.getYearPrefix('SHORT_YEAR').'/';
        $this->data['trans_no'] = $this->jobworkOrder->getNextJwoNo();
        $this->data['trans_number'] = $trans_prefix.$this->data['trans_no'];
        $this->data['vendorList'] = $this->party->getPartyList(['party_category'=>3]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["254"]]);
		$this->data['is_revision'] = 0;
        $this->load->view('jobwork_order/form',$this->data);
    }

    public function getProductProcessList(){
        $data = $this->input->post();
        $processData = $this->item->getProductProcessList(['item_id'=>$data['item_id']]);
        $processOption = '<option value="">Select Process</option>';

        if(!empty($processData)){
            foreach($processData AS $row){
                $processOption .= '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
            }
        }

        $this->printJson(['status'=>1,'processOption'=>$processOption]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['vendor_id'])){ $errorMessage['vendor_id'] = "Party Name is required."; }
		if(empty($data['order_date'])){
            $errorMessage['order_date'] = 'Inv. Date is required.';
        }else{
            if (formatDate($data['order_date'], 'Y-m-d') < $this->startYearDate OR formatDate($data['order_date'], 'Y-m-d') > $this->endYearDate){
                $errorMessage['order_date'] = "Invalid Date (Out of Financial Year).";
            }
        }
        if(empty($data['itemData'])):
            $errorMessage['itemData'] = "Item Details is required.";
        endif;
		
		if(!empty($data['itemData'])){
			foreach($data['itemData'] as $row){
				if(empty($row['id']) && empty($row['ref_id'])){
					$getJobWorkTransData = $this->jobworkOrder->getJobworkOrderItems(['vendor_id'=>$data['vendor_id'], 'process_id'=>$row['process_id'], 'item_id'=>$row['item_id'], 'trans_status'=>"1,3"]);
					
					if(count($getJobWorkTransData) > 0){
						$errorMessage['item_id_'.$row['item_id'].'_'.$row['process_id']] = "Jobwork Order already added.";
					}
				}
			}
		}
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->jobworkOrder->save($data));
        endif;

    }

    public function edit($id){
        $dataRow = $this->jobworkOrder->getJobworkOrderData(['id'=>$id,'single_row'=>1]);
        $dataRow->itemData = $this->jobworkOrder->getJobworkOrderItems(['jwo_id'=>$id,'trans_status'=>1]);
        $this->data['dataRow'] = $dataRow;
        $this->data['vendorList'] = $this->party->getPartyList(['party_category'=>3]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["254"]]);
		$this->data['is_revision'] = 0;
        $this->load->view('jobwork_order/form',$this->data);
    }

    public function delete(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobworkOrder->delete($data));
        endif;
    }
    
    public function approveOrder(){
        $postData = $this->input->post();
        if(empty($postData['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobworkOrder->approveOrder($postData));
        endif;
    }

    public function shortCloseOrder(){
        $postData = $this->input->post();
        if(empty($postData['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobworkOrder->shortCloseOrder($postData));
        endif;
    }

	public function createRevision($id){
        $dataRow = $this->jobworkOrder->getJobworkOrderData(['id'=>$id,'single_row'=>1]);
        $dataRow->itemData = $this->jobworkOrder->getJobworkOrderItems(['jwo_id'=>$id,'trans_status'=>3]);
        $this->data['dataRow'] = $dataRow;
        $this->data['is_revision'] = 1;
        $this->data['vendorList'] = $this->party->getPartyList(['party_category'=>3]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["254"]]);
		
		$trans_prefix = 'JWO/'.getYearPrefix('SHORT_YEAR').'/';
        $this->data['trans_no'] = $this->jobworkOrder->getNextJwoNo();
        $this->data['trans_number'] = $trans_prefix.$this->data['trans_no'];
        $this->load->view('jobwork_order/form',$this->data);
    }
	
	public function printJobwork($id){
        $dataRow = $this->jobworkOrder->getJobworkOrderData(['id'=>$id,'single_row'=>1]);
        $dataRow->itemData = $this->jobworkOrder->getJobworkOrderItems(['jwo_id'=>$id,'trans_status'=>"1,2,3"]);
		$this->data['dataRow'] = $dataRow;
        $this->data['companyData'] = $companyData = $this->outsource->getCompanyInfo();	

        $logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
    
        $pdfData = $this->load->view('jobwork_order/print', $this->data, true);    
		
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
			<tr>
				<td style="width:30%;"></td>
				<td style="width:20%;"></td>
				<td style="width:20%;"></td>
				<th class="text-center">For, '.$companyData->company_name.'</th>
			</tr>
			<tr>
				<td colspan="4" height="30"></td>
			</tr>
			<tr>
				<td class="text-center"><br><b>Received By</b></td>
				<td class="text-center"></td>
				<td class="text-center"></td>
				<td class="text-center"><br><b>Authorised By</b></td>
			</tr>
		</table>
		<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
			<tr>
				<td style="width:25%;">Challan No. & Date : '.$dataRow->trans_number.' ['.formatDate($dataRow->order_date).']'.'</td>
				<td style="width:25%;"></td>
				<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
			</tr>
		</table>';
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='JWO-'.$id.'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(100,100));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P','','','','',5,5,10,10,20,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->SetHTMLFooter($htmlFooter);
		ob_clean();
		$mpdf->Output($pdfFileName, 'I');
    }

}
?>