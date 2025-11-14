<?php
class Store extends MY_Controller
{
    private $returnIndexPage = "store/return_index";
    private $issueIndex = "store/issue_index";
    private $issueForm = "store/issue_form";
    private $returnFormPage = "store/return_form";
    private $inspectIndexPage = "store/inspect_index";

    public function __construct(){
		parent::__construct(); 
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Store";
		$this->data['headData']->controller = "store";
	}

    public function issueRequisition($status=1) {
		$this->data['headData']->pageTitle = "RM/SF Issue";
        $this->data['status'] = $status;
        $this->data['item_type'] = "1";
        if($status == 1){
            $this->data['tableHeader'] = getStoreDtHeader('issueRequisition');
        }else{
            $this->data['tableHeader'] = getStoreDtHeader("returnRequisition");
        }
        $this->load->view($this->issueIndex, $this->data);
    }

    public function toolIssue($status=1) {
        $this->data['headData']->pageUrl = "store/toolIssue";
		$this->data['headData']->pageTitle = "Tool Issue";
        $this->data['status'] = $status;
        $this->data['item_type'] = "2";
		
		if($status == 1){
            $this->data['tableHeader'] = getStoreDtHeader('issueRequisition');
        }elseif($status == 2){
            $this->data['tableHeader'] = getStoreDtHeader("returnRequisition");
        }else{
            $this->data['tableHeader'] = getStoreDtHeader("requisition");
        }
        $this->load->view('store/tool_issue_index', $this->data);
    }

    public function getIssueDTRows($status=1,$item_type=1) {
        $data = $this->input->post();
        $data['status'] = $status;
        $data['item_type'] = $item_type;
		if($status == 3){
            $result = $this->store->getSparPartRequestDTRows($data);
        }else{
            $result = $this->store->getIssueDTRows($data);
        }
		
		$sendData = array(); $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->loginId = $this->session->userdata('loginId');
            if($status == 1){
                $sendData[] = getIssueRequisitionData($row);
            }elseif($status == 2){
                $sendData[] = getReturnRequisitionData($row);
            }else{
                $sendData[] = getRequisitionData($row);
            }
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addIssueRequisition($item_type=1) {
        $data = $this->input->post();
        $this->data['item_type'] = (!empty($data['item_type']) ? $data['item_type'] : $item_type);
        $issue_no = $this->store->getNextIssueNo();
        $this->data['issue_number'] = 'ISU/'.str_pad($issue_no, 5, '0', STR_PAD_LEFT);
        $this->data['issue_no'] = $issue_no;
        $this->data['empData'] = $this->employee->getEmployeeList();
        if(isset($data['issue_type'])){
            $this->data['issue_type'] = $data['issue_type'];
           
			if($data['issue_type'] == 1){
                $this->data['prc_id'] = 0;
            }
			
            if($data['issue_type'] == 2){
                $this->data['prc_id'] = $data['prc_id'];
            }elseif($data['issue_type'] == 3){
                $this->data['prc_id'] = $data['die_id'];
            }elseif($data['issue_type'] == 4){
                $this->data['prc_id'] = $data['req_id'];
            }
			
            $this->load->view('store/prc_mtr_issue', $this->data);
        }else{
            if($data['item_type'] == 1){
                $this->data['itemData'] = $this->item->getItemList(['item_type'=>'1,3,7']);
            }else{
                $this->data['itemData'] = $this->item->getItemList(['item_type'=>'2,9']);
            }
            $this->load->view($this->issueForm, $this->data);
        }
        
    }

    public function getBatchWiseStock() {
        $data = $this->input->post(); 
		$item_id = $data['item_id'];
        $location_ids = "";$item_type = "";
        if(isset($data['item_type']) && $data['item_type'] == 1){
            $location_ids = $this->CUT_STORE->id.','.$this->SEMI_STORE->id.','.$this->PACKING_STORE->id;
            $item_type = $data['item_type'];
        }elseif(isset($data['item_type']) && $data['item_type'] == 9){
            $location_ids = $this->PACKING_STORE->id;
            $item_type = $data['item_type'];
        }
        $batchData = $this->itemStock->getItemStockBatchWise(["item_id" => $item_id,'fg_item_id'=>((!empty($data['fg_item_id']))?$data['fg_item_id']:''),'location_ids'=>$location_ids,'not_in_location'=>[$this->FIR_STORE->id,$this->RTD_STORE->id,$this->SCRAP_STORE->id],'stock_required'=>1,'group_by'=>'location_id,batch_no','supplier'=>1]);
        
        $tbodyData='';$i=0;
        if (!empty($batchData)) {
            foreach ($batchData as $row) {
                $batch_no = $row->batch_no.((!empty($row->heat_no))?('<hr style="margin:0px">'.$row->heat_no):'');
                if($item_type == 1){ 
                    $batch_no = $row->batch_no.((!empty($row->ref_batch))?('<hr style="margin:0px">'.$row->ref_batch):''); 
                    $row->heat_no = $row->ref_batch;
                }

                $tbodyData .= '<tr>';
                $tbodyData .= '<td>'.$row->location.'</td>';
                $tbodyData .= '<td>'.$batch_no.'</td>';
                $tbodyData .= '<td>'.floatVal($row->qty).'</td>';
                $tbodyData .= '<td>
						<input type="text" name="batch_qty[]" class="form-control batchQty floatOnly" min="0" value="" />
						<div class="error batch_qty_' . $i . '"></div>
						<input type="hidden" name="batch_no[]" id="batch_number_' . $i . '" value="' . $row->batch_no . '" />
						<input type="hidden" name="heat_no[]" id="heat_no_' . $i . '" value="' . (!empty($row->heat_no) ? $row->heat_no : '') . '" />
						<input type="hidden" name="location_id[]" id="location_' . $i . '" value="' . $row->location_id . '" />
					</td>
				</tr>';
                $i++;
            }
        } else {
            $tbodyData .= "<td colspan='4' class='text-center'>No Data</td>";
        }
        $this->printJson(['status' => 1, 'tbodyData' => $tbodyData]);
    }

    public function saveIssueRequisition() {

        $data = $this->input->post();
        $errorMessage = array(); $prcData = []; $data['bom_batch'] = "";$batchCount = 0;
       
        if(!empty($data['prc_id']) && $data['issue_type'] == 2){
            $prcData = $this->sop->getPrc(['id'=>$data['prc_id']]);
            $data['bom_batch'] = $prcData->batch_no;
            if(!empty($prcData->batch_no)){
                $batchCount++;
            }  
        }
        if(isset($data['batch_no'])){
            if(empty(array_sum($data['batch_qty']))){$errorMessage['table_err'] = "Batch Details is required.";}
            
            foreach($data['batch_no'] AS $key=>$batch_no){
                if($data['batch_qty'][$key] > 0){
                    $stockData = $this->itemStock->getItemStockBatchWise(["item_id" => $data['item_id'],'stock_required'=>1,'group_by'=>'location_id,batch_no','supplier'=>1,'location_id'=>$data['location_id'][$key],'batch_no'=>$data['batch_no'][$key],'single_row'=>1]);
                    $stock_qty = (!empty($stockData)) ? $stockData->qty : 0;
                    if($data['batch_qty'][$key] > $stock_qty){
                        $errorMessage['batch_qty_'.$key] = "Stock not available.";
                    }
                    if(!empty($data['prc_id']) && $data['issue_type'] == 2){
                        $checkBatch = (($prcData->cutting_flow == 2)?$data['heat_no'][$key]:$data['batch_no'][$key]);
                        if($data['bom_batch'] != $checkBatch){
                            $batchCount++;
                            $data['bom_batch'] = $checkBatch;
                        }
                    }
                }else{
                    unset($data['batch_qty'][$key],$data['batch_no'][$key],$data['location_id'][$key],$data['heat_no'][$key]);
                }
            }
        } else {
            $errorMessage['table_err'] = "Batch Details is required.";
        }

        if(!empty($data['prc_id']) && $batchCount > 1 && $data['issue_type'] == 2 && !empty($prcData->mfg_type) && $prcData->mfg_type != 'Assembly') {
            $errorMessage['table_err'] = "Multiple batches are not allowed";
        }
		if(empty($data['issued_to'])){
			$errorMessage['issued_to'] = "Issued To is required.";
		}

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
			$this->printJson($this->store->saveIssueRequisition($data));
        endif;
    }

    public function deleteIssueRequisition() {
        $data = $this->input->post();
        if(empty($data)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->store->deleteIssueRequisition($data));
        endif;
    }

    public function return() {
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->store->getIssueRequest(['id'=>$id]);
        $this->load->view($this->returnFormPage,$this->data);
    }

    public function saveReturnReq() {
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Return Date is required.";

        if(empty($data['usable_qty']) && empty($data['missed_qty']) && empty($data['broken_qty']) && empty($data['scrap_qty'])){ 
            $errorMessage['genral_error'] = "Return Qty. is Required";
        } else {
            $data['usable_qty'] = (!empty($data['usable_qty'])?$data['usable_qty']:0);
            $data['missed_qty'] = (!empty($data['missed_qty'])?$data['missed_qty']:0);
            $data['broken_qty'] = (!empty($data['broken_qty'])?$data['broken_qty']:0);
            $data['scrap_qty'] = (!empty($data['scrap_qty'])?$data['scrap_qty']:0);
            
            $data['total_qty'] = $data['usable_qty'] + $data['missed_qty'] + $data['broken_qty'] + $data['scrap_qty']; 
            
            if($data['total_qty'] > ($data['issue_qty'] - $data['return_qty'])){
                $errorMessage['genral_error'] = "Return Qty. is not Valid";
            }
        }

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            unset($data['issue_qty']);
            unset($data['return_qty']);
            $data['created_by'] = $this->session->userdata('loginId');
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['trans_type'] = 1;
            $this->printJson($this->store->saveReturnReq($data));
        endif;
    }

    public function inspection($trans_type = 1){
		$this->data['headData']->pageTitle = "Inspection";
        $this->data['trans_type'] = $trans_type;
        $this->data['tableHeader'] = getStoreDtHeader('inspection');
        $this->load->view($this->inspectIndexPage, $this->data);
    }

    public function getInspDTRows($trans_type = 1){
		$data=$this->input->post();
        $data['trans_type'] = $trans_type;
		$result = $this->store->getInspDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->trans_type = $trans_type;
            $sendData[] = getInspectionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addInspection() {
        $data = $this->input->post();
        $this->data['dataRow'] = $this->store->getIssueRequest(['id'=>$data['issue_id']]);
        $this->data['locationData'] = $this->storeLocation->getStoreLocationList(['final_location'=>1]);
        $this->data['mtData'] = $this->store->getMaterialData(['id'=>$data['id']]);
        $this->load->view($this->returnFormPage,$this->data);
    }

    public function saveInspection() {
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Trans Date is required.";

        if(empty($data['location_id']))
            $errorMessage['location_id'] = "Location is required.";

        if(empty($data['usable_qty']) && empty($data['missed_qty']) && empty($data['broken_qty']) && empty($data['scrap_qty'])){
            $errorMessage['genral_error'] = "Inspect Qty. is Required";
        } else {
            $data['usable_qty'] = (!empty($data['usable_qty'])?$data['usable_qty']:0);
            $data['missed_qty'] = (!empty($data['missed_qty'])?$data['missed_qty']:0);
            $data['broken_qty'] = (!empty($data['broken_qty'])?$data['broken_qty']:0);
            $data['scrap_qty'] = (!empty($data['scrap_qty'])?$data['scrap_qty']:0);
            
            $data['insp_qty'] = $data['usable_qty'] + $data['missed_qty'] + $data['broken_qty'] + $data['scrap_qty'];
            
            if($data['insp_qty'] != $data['total_qty']) {
                $errorMessage['genral_error'] = "Inspect Qty. is not Valid";
            }
        }

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            unset($data['issue_qty']);
            unset($data['return_qty']);
            $data['created_by'] = $this->session->userdata('loginId');
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['trans_type'] = 2;
           
            $this->printJson($this->store->saveReturnReq($data));
        endif;
    }

    public function prcMaterial() {
        $this->data['headData']->pageUrl = "store/prcMaterial";
		$this->data['headData']->pageTitle = "RM/SF Issue";
        $this->data['tableHeader'] = getStoreDtHeader('prcMaterial');
        $this->load->view('store/prc_material_index', $this->data);
    }

    public function getPrcMaterialDTRows() {
        $data = $this->input->post();
        $result = $this->store->getPrcMaterialDTRows($data);
		$sendData = array(); $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->loginId = $this->session->userdata('loginId');
            $sendData[] = getPrcMaterialData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function dieMaterial() {
		$this->data['headData']->pageTitle = "RM/SF Issue";
        $this->data['tableHeader'] = getStoreDtHeader('dieMaterialReq');
        $this->load->view('store/die_material_index', $this->data);
    }

    public function getDieMaterialReqDTRows() {
        $data = $this->input->post();
        $result = $this->store->getDieMaterialReqDTRows($data);
		$sendData = array(); $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->loginId = $this->session->userdata('loginId');
            $sendData[] = getDieMaterialRequest($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function manualRejection() {
		$this->data['headData']->pageTitle = "Manual Rejection";
        $this->data['tableHeader'] = getStoreDtHeader('manualRejection');
        $this->load->view('store/manual_rej_index', $this->data);
    }

    public function getManualRejDTRows() {
        $data = $this->input->post();
        $result = $this->store->getManualRejDTRows($data);
		$sendData = array(); $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->loginId = $this->session->userdata('loginId');
            $sendData[] = getManualRejectionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addRejection() {
        $this->data['itemList'] = $this->item->getItemList();
        $this->load->view('store/manual_rej_form', $this->data);
    }

    public function saveRejection() {
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['item_id'])){
            $errorMessage['item_id'] = "Item is required.";
        }

        if(isset($data['batch_no'])){
            $sData = $data['batch_no'];
            for ($i=0; $i < count($sData); $i++) {
                $stockData = $this->itemStock->getItemStockBatchWise(['location_id'=>$data['location_id'][$i],'batch_no'=>$data['batch_no'][$i],'item_id'=>$data['item_id'],'single_row'=>1]);
                $stock_qty = (!empty($stockData)) ? $stockData->qty : 0;
                if($data['batch_qty'][$i] > $stock_qty){
                    $errorMessage['batch_qty_'.$i] = "Stock not available.";
                }
            }
        } else {
            $errorMessage['table_err'] = "Batch Details is required.";
        }

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$this->printJson($this->store->saveRejection($data));
        endif;
    }

    public function deleteRejection() {
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->store->deleteRejection($data));
        endif;
    }

    public function getItemsForIssue(){
        $data = $this->input->post();
        $tbodyData = '';
        if($data['issue_type'] == 2){
            $bomData = $this->sop->getPrcBomData(['prc_id'=>$data['prc_id'],'stock_data'=>1]);
            if(!empty($bomData)){
                foreach($bomData As $row){
                    $prcRq = $row->ppc_qty*$row->prc_qty;
                    $prcIq = ((!empty($row->issue_qty))?$row->issue_qty:0);
                    $prcPq =  ($prcRq - $prcIq);
                    $tbodyData.='<tr>
						<td><a href="javascript:void(0)" class="itemDetail" data-item_id = "'.$row->item_id.'" data-item_type = "'.$row->item_type.'"   data-item_name = "'.(((!empty($row->item_code))?$row->item_code.' ':'').$row->item_name).'">'.((!empty($row->item_code))?$row->item_code.' ':'').$row->item_name.' </td>
						<td class="text-center">'.$prcRq.'</td>
						<td class="text-center">'.$prcIq.'</td>
						<td class="text-center">'.(($prcPq > 0 )?$prcPq:0).'</td>
					</tr>';
                }
            }
        }elseif($data['issue_type'] == 3){
            $dieBom = $this->dieProduction->getDieProductionBom(['die_id'=>$data['prc_id'],'multi_rows'=>1]);
            if(!empty($dieBom)){
                foreach($dieBom As $row){
                    $prcRq = $row->bom_qty;
                    $prcIq = ((!empty($row->issue_qty))?$row->issue_qty:0);
                    $prcPq =  ($prcRq - $prcIq);
                    $tbodyData.='<tr>
						<td><a href="javascript:void(0)" class="itemDetail" data-item_id = "'.$row->item_id.'" data-item_type = "'.$row->item_type.'" data-item_name = "'.(((!empty($row->item_code))?$row->item_code.' ':'').$row->item_name).'">'.((!empty($row->item_code))?$row->item_code.' ':'').$row->item_name.' </td>
						<td class="text-center">'.$prcRq.'</td>
						<td class="text-center">'.$prcIq.'</td>
						<td class="text-center">'.(($prcPq > 0 )?$prcPq:0).'</td>
					</tr>';
                }
            }
        }elseif($data['issue_type'] == 4){
            $reqData = $this->machineBreakdown->getSparPartRequestData(['id'=>$data['prc_id']]);
            if(!empty($reqData)){
                
                $issue_qty = ((!empty($reqData->issue_qty))?$reqData->issue_qty:0);
                $pendingQty =  ($reqData->req_qty - $issue_qty);
                $tbodyData.='<tr>
					<td><a href="javascript:void(0)" class="itemDetail" data-item_id = "'.$reqData->item_id.'" data-item_type = "'.$reqData->item_type.'" data-item_name = "'.(((!empty($reqData->item_code))?$reqData->item_code.' ':'').$reqData->item_name).'">'.((!empty($reqData->item_code))?$reqData->item_code.' ':'').$reqData->item_name.' </td>
					<td class="text-center">'.$reqData->req_qty.'</td>
					<td class="text-center">'.$issue_qty.'</td>
					<td class="text-center">'.(($pendingQty > 0 )?$pendingQty:0).'</td>
				</tr>';
                
            }
        }
        if(empty($tbodyData)){
            $tbodyData = '<tr><th colspan="4" class="text-center">No data available.</th></tr>';
        }
        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }
	
	public function deleteSparPartRequest() {
        $data = $this->input->post();
        if(empty($data)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->store->deleteSparPartRequest($data));
        endif;
    }
}