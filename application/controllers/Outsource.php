<?php
class Outsource extends MY_Controller
{
    private $indexPage = "outsource/index";
    private $formPage = "outsource/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Outsource";
		$this->data['headData']->controller = "outsource";
		$this->data['headData']->pageUrl = "outsource";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionDtHeader('outsource');
        $this->data['testTypeList'] = $this->testType->getTypeList();
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->outsource->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
       
	   foreach($result['data'] as $row):          
            $row->sr_no = $i++;
			$row->unit_id = $this->unit_id;
            $row->trans_status = $status;
            if($status == 2){
                $sendData[] = getRmChallanData($row);
            }else{
                $sendData[] = getOutsourceData($row);
            } 
        endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addChallan(){
        $this->data['ch_prefix'] = 'VC/'.getYearPrefix('SHORT_YEAR').'/';
        $this->data['ch_no'] = $this->outsource->getNextChallanNo();
        $this->data['requestData']=$this->sop->getChallanrequestData(['pending_challan'=>1]);
        $this->data['vendorList'] = $this->party->getPartyList(['party_category'=>3]);
		$this->data['transportList'] = $this->transport->getTransportList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['party_id'])){ $errorMessage['party_id'] = "Vendor is required.";}
        if(empty($data['id'])){ $errorMessage['general_error'] = "Select Item ";}else{
            foreach($data['id'] as $key=>$id){
                $reqData = $this->sop->getChallanRequestData(['id'=>$id,'single_row'=>1]);
                if($data['ch_qty'][$key] > $reqData->qty || empty($data['ch_qty'][$key])){
                    $errorMessage['chQty' . $id] = "Qty. is invalid.";
                }else{
                    $processArray = explode(",",$reqData->process_ids);
                    $currentProcessKey = array_search($reqData->process_id,$processArray);
                    foreach($processArray as $pk=>$process){
                        if($pk < $currentProcessKey){
                            $processData = $this->item->getProductProcessList(['item_id'=>$reqData->item_id,'process_id'=>$process,'process_cost_sum'=>1,'single_row'=>1]);
                            if(!empty($processData->total_process_cost) && $processData->total_process_cost <= 0){
                                $errorMessage['processError' . $id] = 'Enter cost in the previous process.';
                            }
                        }
                    }
                }
				if($data['material_wt'][$key] <= 0){
                    $errorMessage['chMtWt' . $id] = "Weight is required.";
                }
            }
        }
		
        if(empty($data['ch_date'])){
            $errorMessage['ch_date'] = "Date is required."; 
        }else{
			if (($data['ch_date'] < $this->startYearDate) OR ($data['ch_date'] > $this->endYearDate)){
				$errorMessage['ch_date'] = "Invalid Date (Out of Financial Year).";
			}
		}

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->outsource->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->outsource->delete($id));
        endif;
    }  
    
    public function outSourcePrint($id){
        $this->data['outSourceData'] = $this->outsource->getOutSourceData(['id'=>$id]);
        $this->data['reqData'] = $this->sop->getChallanRequestData(['challan_id'=>$id]);
        $this->data['companyData'] = $this->outsource->getCompanyInfo();	

        $logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
    
        $pdfData = $this->load->view('outsource/print', $this->data, true);        
		$mpdf = new \Mpdf\Mpdf();
        $pdfFileName='VC-'.$id.'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(120,45));
        $mpdf->showWatermarkImage = true;
		$mpdf->AddPage('P','','','','',10,5,5,15,5,5,'','','','','','','','','','A4-P');
		
        $mpdf->WriteHTML($pdfData);
		ob_clean();
		$mpdf->Output($pdfFileName, 'I');
		
    }

    public function jobworkOutChallan($jsonData=""){
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

        $id = (!empty($postData['id']) ? $postData['id'] : '');
        $req_id = (!empty($postData['req_id']) ? $postData['req_id'] : '');
        $test_type = (!empty($postData['test_type']) ? $postData['test_type'] : '');

        $this->data['outSourceData'] = $this->outsource->getOutSourceData(['id'=>$id]);
        $this->data['reqData'] = $this->sop->getChallanRequestData(['challan_id'=>$id,'id'=>$req_id,'single_row'=>1,'challan_receive'=>1]);
        $prcBom = $this->sop->getPrcBomData(['prc_id'=>$this->data['reqData']->prc_id,'stock_data'=>1,'single_row'=>1]);
		
        $this->data['tcData'] = (!empty($test_type)) ? $this->materialGrade->getTcMasterData(['item_id'=>$this->data['reqData']->item_id,'test_type'=>$test_type]) : [];
		
		if($this->data['reqData']->cutting_flow == 1){
            $this->data['description_good'] = $prcBom->category_name;
            $this->data['material_wt'] = (!empty($prcBom->uom) && $prcBom->uom == 'KGS') ? $prcBom->ppc_qty : $prcBom->wt_pcs;
        }else{
            $cuttingBatch = $this->sop->getCuttingBatchDetail(['prc_number'=>$prcBom->batch_no,'heat_no'=>$prcBom->heat_no,'single_row'=>1]);
            $this->data['description_good'] = $cuttingBatch->category_name;
            $this->data['material_wt'] = $cuttingBatch->ppc_qty;
        }
		
        $this->data['companyData'] = $this->outsource->getCompanyInfo();	
        
        $logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
		
        $pdfData = "";
        $countPT = count($printTypes); $i=0;
        foreach($printTypes as $printType):
            ++$i;           
            $this->data['printType'] = $printType;
		    $pdfData .= $this->load->view('outsource/print', $this->data, true);
            if($i != $countPT): $pdfData .= "<pagebreak>"; endif;
        endforeach;
        
		//print_r($pdfData); exit;
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = 'Challan_' . $id . '.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->SetTitle($pdfFileName); 
        $mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		//$mpdf->SetProtection(array('print'));
		if(!empty($logo))
		{
		    $mpdf->SetWatermarkImage($logo,0.03,array(100,100));
		    $mpdf->showWatermarkImage = true;
		}
		$mpdf->AddPage('P', '', '', '', '', 5, 5, 5, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

    public function getMaterialValue(){
        $data = $this->input->post();
        $challanData = $this->sop->getChallanRequestData(['id'=>$data['id'],'single_row'=>1]);
        $prcBom = $this->sop->getPrcBomData(['prc_id'=>$challanData->prc_id,'stock_data'=>1,'single_row'=>1]);
		
        if($challanData->cutting_flow == 1){
            $item_id = $prcBom->item_id;
            $rmPriceData = $this->item->getItem(['id'=>$item_id]);
            if($rmPriceData->uom == 'KGS'){
                $material_wt = $prcBom->ppc_qty;
            }else{
                $material_wt = $rmPriceData->wt_pcs;
            }
            $mtPrice = $this->gateInward->getLastPurchasePrice(['item_id'=>$item_id]);
			$material_price = ((!empty($mtPrice->price))?($mtPrice->price * $prcBom->ppc_qty):0);
        }else{
            $cuttingBatch = $this->sop->getCuttingBatchDetail(['prc_number'=>$prcBom->batch_no,'single_row'=>1]);
            $item_id = $cuttingBatch->item_id;
            $rmPriceData = $this->item->getItem(['id'=>$item_id]);
            $material_wt = $cuttingBatch->ppc_qty;
            $mtPrice = $this->gateInward->getLastPurchasePrice(['item_id'=>$item_id]);
			$material_price = ((!empty($mtPrice->price))?($mtPrice->price * $material_wt):0);
        }
       
        $processArray = explode(",",$challanData->process_ids);
        $currentProcessKey = array_search($challanData->process_id,$processArray);
        $prevProcess = [];$pre_process_cost = 0;
        $processCostError = '';
        foreach($processArray as $key=>$process){
            if($key < $currentProcessKey){
                $prevProcess[] = $process;
                $processData = $this->item->getProductProcessList(['item_id'=>$challanData->item_id,'process_id'=>$process,'process_cost_sum'=>1,'single_row'=>1]);
                if(!empty($processData->total_process_cost) && $processData->total_process_cost <= 0){
                    $processCostError = 'Enter cost in the previous process.';
                }else{
                    $pre_process_cost += $processData->total_process_cost;
                }
                
            }
        }
        // $processData = $this->item->getProductProcessList(['item_id'=>$challanData->item_id,'process_id'=>$prevProcess,'process_cost_sum'=>1,'single_row'=>1]);
       
		$rmPrice = (!empty($rmPriceData->price)) ? $rmPriceData->price : 0;
		$mtPrice = ((!empty($mtPrice->price) && $mtPrice->price > 0)?$mtPrice->price:$rmPrice);
        
        $process_cost = ((!empty($pre_process_cost))?($pre_process_cost):0)+$rmPrice;
        $cost_per_pcs = $material_price + $process_cost;
        $this->printJson(['status'=>1,'cost_per_pcs'=>round($cost_per_pcs,3),'material_wt'=>$material_wt,'material_price'=>$mtPrice,'pre_process_cost'=>$process_cost,'processCostError'=>$processCostError]);
    }

	public function jobWorkBillIndex(){
        $this->data['tableHeader'] = getProductionDtHeader('jwbill');
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-d"));
        $this->load->view('outsource/jwbill_index',$this->data); 
    }

    public function getjobWorkBillDTRows($from_date="",$to_date=""){
        $data = $this->input->post();
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $result = $this->outsource->getJwbDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
            $sendData[] = getjobWorkBillData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function jobWorkBill(){
        $data = $this->input->post();
        $this->data['log_ids'] = $data['log_ids'];
        $this->load->view('outsource/jwbill_form', $this->data);
    }

    public function saveJobWorkBill(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['bill_no']))
            $errorMessage['bill_no'] = "Bill No. is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->outsource->saveJobWorkBill($data));
        endif;
    }

	public function rmChallanPrint($jsonData=""){
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

        $id = (!empty($postData['id']) ? $postData['id'] : '');
        $req_id = (!empty($postData['req_id']) ? $postData['req_id'] : '');

        $this->data['outSourceData'] = $this->outsource->getOutSourceData(['id'=>$id]);
        $this->data['reqData'] = $this->sop->getChallanRequestData(['challan_id'=>$id,'id'=>$req_id,'single_row'=>1,'challan_receive'=>1,'rm_challan'=>1]);
        $this->data['tcData'] = (!empty($test_type)) ? $this->materialGrade->getTcMasterData(['item_id'=>$this->data['reqData']->ref_item_id,'test_type'=>$test_type]) : [];
        $this->data['companyData'] = $this->outsource->getCompanyInfo();	
        
        $logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
		
        $pdfData = "";
        $countPT = count($printTypes); $i=0;
        foreach($printTypes as $printType):
            ++$i;           
            $this->data['printType'] = $printType;
		    $pdfData .= $this->load->view('outsource/challan_print', $this->data, true);
            if($i != $countPT): $pdfData .= "<pagebreak>"; endif;
        endforeach;
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = 'Challan_' . $id . '.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->SetTitle($pdfFileName); 
        $mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		if(!empty($logo))
		{
		    $mpdf->SetWatermarkImage($logo,0.03,array(100,100));
		    $mpdf->showWatermarkImage = true;
		}
		$mpdf->AddPage('P', '', '', '', '', 5, 5, 5, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
}
?>