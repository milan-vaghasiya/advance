<?php
class SopDesk extends MY_Controller{

	public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "SOP DESK";
		$this->data['headData']->controller = "sopDesk";
	}
	
	public function index(){
		$this->data['headData']->pageTitle = "SOP Desk";
		$this->data['tableHeader'] = getProductionDtHeader('sop');
        $this->load->view('sopDesk/sop_index',$this->data);
    }
	
	public function migratePRCRev(){
		$prc_migrate = $this->sop->getPrcListForMigrations();
		exit;
    }
	
    public function getSopDTRows($status = 1){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->sop->getSopDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getSopData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function prcDetail($prc_id){
		$this->data['prc_id'] = $prc_id;
        $this->load->view('sopDesk/prc_view',$this->data);
	}

	public function getPRCList($fnCall = "Ajax"){
        $postData = $this->input->post();
		if(empty($postData)){$fnCall = 'Outside';}
        $next_page = 0;
		
		$prcData = Array();
		if(isset($postData['page']) AND isset($postData['start']) AND isset($postData['length']))
        {
            $prcData = $this->sop->getPRCList($postData);
            $next_page = intval($postData['page']) + 1;
            
        }
        else{ $prcData = $this->sop->getPRCList($postData); }
		
		$this->data['prcData'] = $prcData;
		$prcList ='';
		$prcList = $this->load->view('sopDesk/prc_list',$this->data,true);
        if($fnCall == 'Ajax'){$this->printJson(['prcList'=>$prcList,'next_page'=>$next_page]);}
		else{return $leadDetail;}
    }
    
	public function getPRCDetail(){
        $postData = $this->input->post();
		$prcDetail ='';$prcMaterial ='';$processDetail ='';
		
		$prcData = $this->data['prcData'] = $this->sop->getPRCDetail(['prc_id'=>$postData['id'],'move_type'=>$postData['move_type']]);
		
		if(!empty($prcData)){
			$this->data['move_type'] = $postData['move_type'];
    		$this->data['status'] = (!empty($prcData->status)) ? $prcData->status : 1;
    		$this->data['prcMaterialData'] = $this->sop->getPrcBomData(['prc_id'=>$postData['id'],'production_data'=>1,'stock_data'=>1,'cutting_batch'=>(($prcData->cutting_flow == 2)?1:'')]);
			if(!empty($this->data['prcMaterialData'][0]->batch_no)){
				$this->data['cuttingBatch'] = $this->sop->getCuttingBatchDetail(['prc_number'=>$this->data['prcMaterialData'][0]->batch_no,'heat_no'=>$this->data['prcMaterialData'][0]->heat_no,'single_row'=>1]);
			}
    		$this->data['prcProcessData'] = (!empty($prcData->prcProcessData)) ? $prcData->prcProcessData : [];
			$this->data['dieData'] = $this->dieChallan->getDieChallanItems(['prc_id'=>$postData['id'],'trans_status'=>0,'item_id'=>$prcData->item_id,'challan_type'=>1,'single_row'=>1]); 
			
			$this->data['revList'] = $this->ecn->getItemRevision(['item_id'=>$prcData->item_id,'ecn_status'=>"2,3"]);
			
			$this->data['invData'] = $this->salesInvoice->getBatchWiseInvoice(['prc_id'=>$postData['id']]);
			
    		$prcDetail = $this->load->view('sopDesk/prc_detail',$this->data,true);
    		
    		$prcMaterial = $this->load->view('sopDesk/prc_material',$this->data,true);
    		
    		$processDetail = $this->load->view('sopDesk/prc_process',$this->data,true);
		}
        $this->printJson(['prcDetail'=>$prcDetail,'prcMaterial'=>$prcMaterial,'processDetail'=>$processDetail]);
    }
    
	public function addPrc(){
		$data = $this->input->post();
        $this->data['prc_prefix'] = 'PRC/'.$this->shortYear.'/';
        $this->data['prc_no'] = $this->sop->getNextPRCNo();
        $this->data['customerData'] = $this->salesOrder->getPendingOrderItems(['group_by'=>'party_id']);
		if(!empty($data['item_id'])){
			$dataRow = new stdClass();
			$dataRow->item_id = $data['item_id'];
			$dataRow->party_id = 0;
			$this->data['dataRow'] = $dataRow;
			$this->data['productData'] = $this->getProductList(['item_id'=>$dataRow->item_id]);
			$prdDetail = $this->getItemRevList(['item_id'=>$dataRow->item_id]);
			$this->data['revisionData'] = $prdDetail['revHtml'];
		}
        $this->load->view('sopDesk/prc_form',$this->data);
	}
	
	/* UPDATED BY : AVT DATE:13-12-2024 */
	public function getProductList($param = []){
		$data = (!empty($param)) ? $param : $this->input->post();
		$result = array();
		if(!empty($data['party_id'])){
			$result = $this->salesOrder->getPendingOrderItems(['party_id'=>$data['party_id'],'is_approve'=>1,'prc_type'=>1,'trans_status'=>0]); 
		}else{
			$result = $this->item->getItemList(['item_type'=>1]);
		}
		$selected='';
		$options='<option value="">Select Product</option>';
		foreach($result as $row):
			$row->prc_qty = (!empty($row->prc_qty)? $row->prc_qty : 0);
			$row->qty = (!empty($row->qty)? $row->qty : 0);
            $row->pending_qty = ($row->qty - $row->prc_qty);

			$value = (!empty($row->item_code)? "[".$row->item_code."] " : "").$row->item_name;
			$value .= (!empty($row->trans_number))?' ('.$row->trans_number.' | Pend. Qty: ' . $row->pending_qty.' | Del.Date: ' . formatDate($row->cod_date).')':'';
			
			$so_trans_id=0;
			if(!empty($row->trans_number)){ $so_trans_id = $row->id; }
			
			$selected = (!empty($data['item_id']) && $data['item_id'] == $row->item_id) ? 'selected' : '';
			$options.='<option value="'.$row->item_id.'" data-so_trans_id="'.$so_trans_id.'" '.$selected.'>'.$value.'</option>';
		endforeach;
		if(!empty($param)):
			return $options;
		else:
        	$this->printJson(['options'=>$options]);
		endif;		
	}
	
	public function savePRC(){
		$data = $this->input->post();
		
        $errorMessage = array();
        if ($data['party_id'] == ""){ $errorMessage['party_id'] = "Customer is required."; }
        if (empty($data['item_id'])){ $errorMessage['item_id'] = "Product is required."; }
        if (empty($data['qty']) || $data['qty'] < 0){ $errorMessage['qty'] = "Quantity is required."; }
        if (empty($data['prc_date'])){ $errorMessage['prc_date'] = "PRC Date is required."; }else{
			if (formatDate($data['prc_date'], 'Y-m-d') < $this->startYearDate OR formatDate($data['prc_date'], 'Y-m-d') > $this->endYearDate){
				$errorMessage['prc_date'] = "Invalid Date (Out of Financial Year).";
			}
		}
        //if (empty($data['rev_no'])){ $errorMessage['rev_no'] = "Rev No is required."; }
		/*if (empty($data['first_process'])){ $errorMessage['first_process'] = "Initial Stage is required."; }*/
		
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$masterData = [
				'id'=>$data['id'],
				'prc_no'=>$data['prc_no'],
				'prc_number'=>$data['prc_number'],
				'prc_date'=>$data['prc_date'],
				'party_id'=>$data['party_id'],
				'item_id'=>$data['item_id'],
				'so_trans_id'=>$data['so_trans_id'],
				'prc_qty'=>$data['qty'],
				'target_date'=>$data['target_date'],
				'rev_no'=>$data['rev_no'],
				'mfg_route'=>$data['mfg_route'],
			];
			$prcDetail = [ 
				'remark'=>$data['remark'],
				'id'=>$data['prc_detail_id'],
			];
			if(empty($data['id'])){
				$masterData['created_by'] = $this->session->userdata('loginId');
				$prcDetail['created_by'] = $this->session->userdata('loginId');
			}else{
				$masterData['updated_by'] = $this->session->userdata('loginId');
				$prcDetail['updated_by'] = $this->session->userdata('loginId');
			}
			$sendData['masterData'] = $masterData;
			$sendData['prcDetail'] = $prcDetail;
            $this->printJson($this->sop->savePRC($sendData));
        endif;
	}

	public function getItemRevList($param = []){
		$data = (!empty($param)) ? $param : $this->input->post();
		$revList = $this->ecn->getItemRevision(['item_id'=>$data['item_id']]);
		$revHtml = '<option value="">Select Revision</option>';
		if(!empty($revList)){
			foreach($revList as $row){
				$selected = (!empty($data['rev_no']) && $data['rev_no'] == $row->rev_no)?'selected':'';
				$revHtml .= '<option value="'.$row->rev_no.'" '.$selected.'>'.$row->rev_no.' [Drw No : '.$row->drw_no.']'.'</option>';
			}
		}
		if(!empty($param)):
			return ['revHtml'=>$revHtml];
		else:
        	$this->printJson(['revHtml'=>$revHtml]);
		endif;		
	}

	public function setPrcProcesses(){
		$data = $this->input->post();
		$this->data['prcData']  = $prcData = $this->sop->getPRC(['id'=>$data['id']]);
		$this->data['processList'] = $this->item->getProductProcessList(['item_id'=>$data['item_id'],'order_process_ids'=>$this->data['prcData']->process_ids,'is_active' =>(($prcData->status == 1)?'1': '1,2')]);
		$this->data['acceptData'] = $this->sop->getPRCProcessList(['prc_id'=>$data['id'],'process_id'=>$this->data['prcData']->process_ids,'movement_data'=>1,'pending_accepted'=>1,'move_type'=>1]);
		$this->load->view('sopDesk/process_form',$this->data);
	}
	
	public function startPRC(){
		$data = $this->input->post();
		
		if (empty($data['first_process']) && $data['production_type'] == 1){ $errorMessage['first_process'] = "Initial Stage is required."; }
		if (empty($data['process'])){ $errorMessage['process_error'] = "Process required."; }
		$prcBom = $this->sop->getPrcBomData(['prc_id'=>$data['id']]);
		if(empty($prcBom)){$errorMessage['general_error'] = "Set Required Material for production";}

		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else:
			//If Fix Production then First process is first process of selected process_ids
			if($data['production_type'] == 2){
				$data['first_process'] = $data['process'][0];
			}else{

			}if(!in_array($data['first_process'],$data['process'])){
				$data['process'][] = $data['first_process'];
			}
			$data['process_ids'] = implode(",",$data['process']);
			$this->printJson($this->sop->startPRC($data));
        endif;
	}

	public function prcLog(){
		$data = $this->input->post();
		$this->data['process_from'] = $data['process_from'];
		$this->data['dataRow'] = $this->sop->getPRCProcessList(['process_id'=>$data['process_id'],'prc_id'=>$data['prc_id'],'single_row'=>1]);
		if(!empty($data['challan_id'])){
			$this->data['challan_id'] = $data['challan_id'];
			$this->data['ref_trans_id'] = $data['ref_trans_id'];
			$this->data['process_by'] = $data['process_by'];
			$this->data['processor_id'] = $data['processor_id'];
		}
		$this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
		$this->data['shiftData'] = $this->shiftModel->getShiftList();
		$this->data['operatorList'] = $this->employee->getEmployeeList();
		$this->data['masterSetting'] = $this->sop->getAccountSettings();
		$this->data['trans_type'] = (!empty($data['trans_type']))?$data['trans_type']:1;
		$this->data['dieList'] = $this->dieMaster->getDieMasterData(['group_by'=>'die_master.fg_id,die_master.set_no','fg_id'=>$this->data['dataRow']->item_id,'available_set'=>1,'status'=>1]);
		$this->load->view('sopDesk/prc_log_form',$this->data);
	}
	
	public function savePRCLog(){
		$data = $this->input->post(); 
        $errorMessage = array(); $die_master_ids = '';
        if (empty($data['prc_id'])){ $errorMessage['prc_id'] = "Job Card No. is required.";}
        if (empty($data['process_id'])){ $errorMessage['process_id'] = "Process is required.";}
		if (empty($data['trans_date']) or $data['trans_date'] == null or $data['trans_date'] == ""){ 
			$errorMessage['trans_date'] = "Date is required."; 
		}elseif($data['process_by'] == 3){
			$challanData = $this->sop->getChallanRequestData(['id'=>$data['ref_trans_id'],'challan_receive'=>1,'single_row'=>1]);
			if (($data['trans_date'] < $challanData->trans_date) || ($data['trans_date'] > date('Y-m-d'))){			
				$errorMessage['trans_date'] = "Invalid Date.";
			}
		}
		
        if (empty($data['ok_qty']) && empty($data['rej_found']) && empty($data['without_process_qty'])){
            $errorMessage['production_qty'] = "OK Qty Or Rejection Qty. is required.";
       	}else{
			
			//Check Material Issue Location
			$prcData = $this->sop->getPRC(['id'=>$data['prc_id']]);
			if($prcData->first_process == $data['process_id']){
				$prcMaterialData = $this->sop->getPrcBomData(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'stock_data'=>1]);
				foreach($prcMaterialData AS $row){
					$locationData = $this->storeLocation->getStoreLocation(['id'=>$row->location_id]);
					if(!empty($locationData->party_id) && in_array($data['process_by'],[1,2])){
						$errorMessage['production_qty'] = "The material you issued is at the vendor location.";
					}elseif($data['process_by'] == 3 && empty($locationData->party_id)){
						$errorMessage['production_qty'] = "The material you issued is at the inhouse location.";
					}elseif($data['process_by'] == 3 && !empty($locationData->party_id) && $locationData->party_id != $data['processor_id']){
						$errorMessage['production_qty'] = "The material you issued is Other location.";
					}
				}	
			}
			
			$totalProdQty = (!empty($data['ok_qty']))?$data['ok_qty']:0; $totalProdQty += (!empty($data['rej_found'])) ? $data['rej_found'] : 0; $totalProdQty += (!empty($data['without_process_qty'])) ? $data['without_process_qty'] : 0;
			$pending_production = 0;
			if($data['process_by'] == 3){
				$challanData = $this->sop->getChallanRequestData(['id'=>$data['ref_trans_id'],'challan_receive'=>1,'single_row'=>1]); 
				$pending_production =($challanData->qty * $challanData->output_qty) - ($challanData->ok_qty + $challanData->rej_qty + ($challanData->without_process_qty * $challanData->output_qty));
			}else{
				$prcProcessData = $this->sop->getPRCProcessList(['process_id'=>$data['process_id'],'prc_id'=>$data['prc_id'],'process_from'=>$data['process_from'],'log_data'=>1,'pending_accepted'=>1,'log_process_by'=>1,'single_row'=>1,'move_type'=>$data['trans_type']]); 
				$in_qty = (!empty($prcProcessData->in_qty)?($prcProcessData->in_qty * $prcProcessData->output_qty):0);
				$ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
				$rej_found = !empty($prcProcessData->rej_found)?$prcProcessData->rej_found:0;
				$rw_qty = !empty($prcProcessData->rw_qty)?$prcProcessData->rw_qty:0;
				$rej_qty = !empty($prcProcessData->rej_qty)?$prcProcessData->rej_qty:0;
                $pendingReview = $rej_found - $prcProcessData->review_qty;
                $pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+(($prcProcessData->ch_qty * $prcProcessData->output_qty )));

				if($data['process_by'] == 1 && $prcProcessData->die_required == 1){
					if(empty($data['die_set_no'])){
						$errorMessage['die_set_no'] = "Die is required";
					}else{
						$dmData = $this->dieMaster->getDieMasterData(['set_no'=>$data['die_set_no'], 'fg_id'=>$prcProcessData->item_id, 'status'=>1]);
						$die_master_ids = implode(',',array_column($dmData,'id'));
					}
				}
			}
			if($pending_production < $totalProdQty ||  $totalProdQty < 0) :
				$errorMessage['production_qty'] = "Invalid Qty.";
			endif;
		}
		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :

			$logData = [
				'id'=>'',
				'trans_type' => $data['trans_type'],
				'process_from' => $data['process_from'],
				'prc_id' => $data['prc_id'],
				'process_id' => $data['process_id'],
				'ref_id' => !empty($data['ref_id'])?$data['ref_id']:'',
				'ref_trans_id' => !empty($data['ref_trans_id'])?$data['ref_trans_id']:'',
				'trans_date' => $data['trans_date'],
				'qty' => !empty($data['ok_qty'])?$data['ok_qty']:0,
				'qty_kg' => !empty($data['qty_kg'])?$data['qty_kg']:0,
				'rej_found' =>  !empty($data['rej_found'])?$data['rej_found']:0,
				'without_process_qty' =>  !empty($data['without_process_qty'])?$data['without_process_qty']:0, // Used in outsource Receive Form
				'production_time' => !empty($data['production_time'])?$data['production_time']:0,
				'in_challan_no' => !empty($data['in_challan_no'])?$data['in_challan_no']:0,
				'process_by' => $data['process_by'],
				'processor_id' =>!empty($data['processor_id'])?$data['processor_id']:0,
				'shift_id' => !empty($data['shift_id'])?$data['shift_id']:'',
				'operator_id' => !empty($data['operator_id'])?$data['operator_id']:'',
				'die_set_no' => !empty($data['die_set_no'])?$data['die_set_no']:'',
				'die_master_ids' => $die_master_ids,
				'logDetail'=>[
					'id'=>'',
					'remark'=>(!empty($data['remark'])?$data['remark']:''),
					'rej_reason'=>!empty($data['rej_reason'])?$data['rej_reason']:'',
					'rej_type'=>!empty($data['rej_type'])?$data['rej_type']:'',
					'rej_stage'=>!empty($data['rej_stage'])?$data['rej_stage']:'',
					'rej_by'=>!empty($data['rej_by'])?$data['rej_by']:'',
					'rej_comment'=>!empty($data['rej_comment'])?$data['rej_comment']:'',
					'start_time'=>!empty($data['start_time'])?$data['start_time']:'',
					'end_time'=>!empty($data['end_time'])?$data['end_time']:'',
				]
			];
			//If Final Inspection Process
			if($data['process_id'] == 2){
				if(isset($data['rev_no']) && $data['rev_no'] == ''){ 
					$errorMessage['rev_no'] = "Revision is required."; 
					
					$this->printJson(['status' => 0, 'message' => $errorMessage]);
				} else {
					
					$insParamData =  $this->item->getInspectionParameter(['item_id'=>$data['item_id'],'control_method'=>'FIR','rev_no'=>$data['rev_no']]);
					if(count($insParamData) <= 0) { $errorMessage['general'] = "Item Parameter is required."; }
			
					if (!empty($errorMessage)) { $this->printJson(['status' => 0, 'message' => $errorMessage]); }
							
					$pre_inspection = Array(); $param_ids = Array();
					if(!empty($insParamData)):
						foreach($insParamData as $row):
							$param = Array();
							for($j = 1; $j <= $data['sampling_qty']; $j++):
								$param[] = $data['sample'.$j.'_'.$row->id];
								unset($data['sample'.$j.'_'.$row->id]);
							endfor;
							$param[] = (!empty($data['result_'.$row->id]) ? $data['result_'.$row->id] : ""); 
							unset($data['result_'.$row->id]);

							$pre_inspection[$row->id] = $param;
							$param_ids[] = $row->id;
						endforeach;
					endif;
			
					
					$firData = [
						'observation_sample'=>json_encode($pre_inspection),
						'parameter_ids'=>implode(',',$param_ids),
						'param_count'=>count($insParamData),
						'insp_date'=>$data['trans_date'],
						'rev_no'=>$data['rev_no'],
						'prc_id'=>$data['prc_id'],
						'item_id'=>$data['item_id'],
						'process_id'=>$data['process_id'],
						'trans_no'=>$data['trans_no'],
						'trans_number'=>$data['trans_number'],
						'sampling_qty'=>$data['sampling_qty'],
						'report_type'=>2,
					];
					$logData['firData'] = $firData;
				}
			}

			$this->printJson($this->sop->savePRCLog($logData));
		endif;	
	}

	public function deletePRCLog(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sop->deletePRCLog($data));
        endif;
	}

	public function getPRCLogHtml(){
		$data = $this->input->post(); 
		if($data['process_by'] != 3){$data['process_by'] = "";}
		else{ $data['outsource_without_process'] = 1; }
		if($data['process_id'] == 2){
			$data['fir_data'] = 1;
		}
		$logData = $this->sop->getProcessLogList($data);
		$html="";
        if (!empty($logData)) :
            $i = 1;
            foreach ($logData as $row) :
				$createdBy = $row->created_name.(!empty($row->created_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($row->created_at)) : '');
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deletePRCLog','res_function':'getPrcLogResponse','controller':'sopDesk'}";
				$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trashSop('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
				
				$rejTag = ''; $rejQty = floatval($row->rej_found);
				if(!empty($rejQty)){
					$rejTag .= '<a href="' . base_url('sopDesk/printPRCRejLog/' . $row->id) . '" target="_blank" class="btn btn-sm btn-dark waves-effect waves-light mr-1" title="Rejection Tag"><i class="fas fa-print"></i></a>';
				}
				$fir_print = '';
				if($row->process_id == 2){
					$fir_print = '<a href="' . base_url('sopDesk/printFinalInspection/' . $row->inspection_id) . '" target="_blank" class="btn btn-sm btn-success waves-effect waves-light mr-1" title="Inspection Tag"><i class="fas fa-print"></i></a>';
				}
				if($data['process_id'] == 2){
					$html .='<tr class="text-center">
								<td>' . $i++ . '</td>
								<td>' . formatDate($row->trans_date). '</td>
								<td>' . $row->from_process_name . ' </td>
								<td>' . floatval($row->sampling_qty) . '</td>
								<td>' . floatval($row->qty) . '</td>
								<td>' . floatval($row->rej_found) . '</td>
								<td>' .$fir_print. $rejTag.' '.$deleteBtn . '</td>
							</tr>';
				}else{
					$td = '';
					if($data['process_by'] == 3){
						$td = '<td>'.$row->without_process_qty.'</td><td>'.$row->in_challan_no.'</td>';
					}else{
						$td = '<td>' . $row->emp_name . '</td><td>' . $row->shift_name . '</td>';
						if(!empty($row->die_required) && $row->die_required == 1){
							$td .= '<td>'.$row->die_set_no.'</td><td>'.$row->qty_kg.'</td>';
						}
					}
					$html .='<tr class="text-center">
								<td>' . $i++ . '</td>
								<td>' . formatDate($row->trans_date). '</td>
								<td>' . $row->production_time . ' Min.</td>
								<td>' . $row->from_process_name . ' </td>
								<td>' . $row->processor_name . '</td>
								<td>' . floatval($row->qty) . '</td>
								<td>' . floatval($row->rej_found) . '</td>
								'.$td.'
								<td>' . $row->remark . '</td>
								<td>'.$createdBy.'</td>
								<td>' . $rejTag.' '.$deleteBtn . '</td>
							</tr>';
				}
				
            endforeach;
        else :
            $html = '<td colspan="12" class="text-center">No Data Found.</td>';
        endif;
		if($data['process_by'] != 3){
			$prcProcessData = $this->sop->getPRCProcessList(['process_id'=>$data['process_id'],'prc_id'=>$data['prc_id'],'log_data'=>1,'movement_data'=>1,'log_process_by'=>1,'pending_accepted'=>1,'single_row'=>1,'process_from'=>$data['process_from'],'move_type'=>$data['trans_type']]); 
			$in_qty = (!empty($prcProcessData->in_qty)?($prcProcessData->in_qty * $prcProcessData->output_qty):0);
			$ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
			$rej_found = !empty($prcProcessData->rej_found)?$prcProcessData->rej_found:0;
			$rw_qty = !empty($prcProcessData->rw_qty)?$prcProcessData->rw_qty:0;
			$rej_qty = !empty($prcProcessData->rej_qty)?$prcProcessData->rej_qty:0;
			$pendingReview = $rej_found - $prcProcessData->review_qty;
			$pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+($prcProcessData->ch_qty * $prcProcessData->output_qty ));
		}else{
			$challanData = $this->sop->getChallanRequestData(['id'=>$data['ref_trans_id'],'challan_receive'=>1,'single_row'=>1]); 
			$pending_production =(($challanData->qty - $challanData->without_process_qty) * $challanData->output_qty) - ($challanData->ok_qty + $challanData->rej_qty);
		}
		
		$prcData = $this->sop->getPRC(['id'=>$data['prc_id']]);
        $revList = (!empty($prcData->item_id) ? $this->ecn->getItemRevision(['item_id'=>$prcData->item_id]) : []);
		$revOptions='';
		if(!empty($prcData->rev_no)){
			$revOptions .= '<option value="'.$prcData->rev_no.'">'.$prcData->rev_no.'</option>';
		}
		elseif(!empty($revList)){
			foreach($revList as $row){
				$revOptions .= '<option value="'.$row->rev_no.'">'.$row->rev_no.'</option>';
			}
		}
		$this->printJson(['status'=>1,'tbodyData'=>$html,'pendingQty'=>$pending_production,'revOptions'=>$revOptions]);
	}

	public function prcMovement(){
		$data = $this->input->post();
		$this->data['move_type'] = !empty($data['move_type'])?$data['move_type']:1;
		$this->data['process_from'] = $data['process_from'];
		$this->data['dataRow'] = $this->sop->getPRCProcessList(['process_id'=>$data['process_id'],'prc_id'=>$data['prc_id'],'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'single_row'=>1,'process_bom'=>1]);

		$this->data['processList'] = $this->sop->getPRCProcessList(['item_id'=>$this->data['dataRow']->item_id,'prc_id'=>$data['prc_id'],'process_id'=>$this->data['dataRow']->process_ids]);
		$this->load->view('sopDesk/prc_movement_form',$this->data);
	}

	public function savePRCMovement(){
		$data = $this->input->post(); 
        $errorMessage = array();
        if (empty($data['prc_id'])){ $errorMessage['prc_id'] = "Job Card No. is required.";}
        if (empty($data['process_id'])){ $errorMessage['process_id'] = "Process is required.";}
        if ($data['next_process_id'] == ""){ $errorMessage['next_process_id'] = "Next Process is required.";}
		if (empty($data['trans_date']) or $data['trans_date'] == null or $data['trans_date'] == ""){ $errorMessage['trans_date'] = "Date is required."; }
        if (empty($data['qty'])){
            $errorMessage['qty'] = "Qty. is required.";
       	}else{
			if($data['process_id'] == 1){
				$prcProcessData = $this->sop->getSemiFinishData(['prc_id'=>$data['prc_id'],'pending_accepted'=>1,'single_row'=>1]); 
				$pending_movement =!empty($prcProcessData->pending_accept)?$prcProcessData->pending_accept:0;
			}else{
				$prcProcessData = $this->sop->getPRCProcessList(['process_id'=>$data['process_id'],'prc_id'=>$data['prc_id'],'process_from'=>$data['process_from'],'log_data'=>1,'movement_data'=>1,'single_row'=>1,'move_type'=>$data['move_from']]); 
				$ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
				$movement_qty =!empty($prcProcessData->movement_qty)?$prcProcessData->movement_qty:0;
				$pending_movement = $ok_qty - $movement_qty;
			}
			
			
			if($pending_movement < $data['qty'] ||  $data['qty'] < 0) :
				$errorMessage['qty'] = "Invalid Qty.";
			endif;
		}
		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$logData = [
				'id'=>'',
				'move_type' => $data['move_type'],
				'move_from' => $data['move_from'],
				'process_from' => $data['process_from'],
				'prc_id' => $data['prc_id'],
				'process_id' => $data['process_id'],
				'next_process_id' => $data['next_process_id'],
				'trans_date' => $data['trans_date'],
				'qty' => !empty($data['qty'])?$data['qty']:0,
				'qty_kg' => !empty($data['qty_kg'])?$data['qty_kg']:0,
				'remark' => !empty( $data['remark'])? $data['remark']:'',
			];
			$this->printJson($this->sop->savePRCMovement($logData));
		endif;	
	}

	public function deletePRCMovement(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sop->deletePRCMovement($data));
        endif;
	}

	public function getPRCMovementHtml(){
		$data = $this->input->post(); 
		$movementData = $this->sop->getProcessMovementList(['process_id'=>$data['process_id'],'prc_id'=>$data['prc_id'],'process_from'=>$data['process_from'],'move_from'=>$data['trans_type']]);
		$html="";
        if (!empty($movementData)) :
            $i = 1;
            foreach ($movementData as $row) :
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deletePRCMovement','res_function':'getPrcMovementResponse'}";
				$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trashSop('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
			   
				$printTag = '<a href="' . base_url('sopDesk/printPRCMovement/' . $row->id) . '" target="_blank" class="btn btn-sm btn-info waves-effect waves-light mr-2" title="Print"><i class="fas fa-print"></i></a>';
				$location = "";
				if($row->processor_id == $this->RTD_STORE->id){
					$location = $this->RTD_STORE->location;
				}elseif($row->processor_id == $this->PACKING_STORE->id){
					$location = $this->PACKING_STORE->location;
				}elseif($row->processor_id == $this->SEMI_STORE->id){
					$location = $this->SEMI_STORE->location;
				}else{
					$location = $this->PACKING_STORE->location;
				}
				
				$createdBy = $row->created_name.(!empty($row->created_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($row->created_at)) : '');
				
				$html .='<tr class="text-center">
					<td>' . $i++ . '</td>
					<td>' . formatDate($row->trans_date). '</td>
					<td>' . floatval($row->qty) . '</td>
					<td>' . (!empty($row->next_process_name)?$row->next_process_name:$location) . '</td>
					<td>' . $row->remark . '</td>
					<td>' . $createdBy . '</td>
					<td>' . $printTag.' '.$deleteBtn . '</td>
				</tr>';
            endforeach;
        else :
            $html = '<td colspan="7" class="text-center">No Data Found.</td>';
        endif;
		
		if($data['process_id'] == 1){
			$prcProcessData = $this->sop->getSemiFinishData(['prc_id'=>$data['prc_id'],'pending_accepted'=>1,'single_row'=>1,'trans_type'=>$data['trans_type']]); 
			$pending_movement =!empty($prcProcessData->pending_accept)?$prcProcessData->pending_accept:0;
		}else{
			$prcProcessData = $this->sop->getPRCProcessList(['process_id'=>$data['process_id'],'prc_id'=>$data['prc_id'],'process_from'=>$data['process_from'],'log_data'=>1,'movement_data'=>1,'single_row'=>1,'move_type'=>$data['trans_type']]); 
			$in_qty = (!empty($prcProcessData->in_qty)?$prcProcessData->in_qty:0);
			$ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
			$movement_qty =!empty($prcProcessData->movement_qty)?$prcProcessData->movement_qty:0;
			$pending_movement = $ok_qty - $movement_qty;
		}
		$this->printJson(['status'=>1,'tbodyData'=>$html,'pendingQty'=>$pending_movement]);
	}

	/*** Semi Finish Movement */
	public function semiFinishMovement(){
		$data = $this->input->post();
		$this->data['dataRow'] = $this->sop->getSemiFinishData(['prc_id'=>$data['prc_id'],'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'single_row'=>1,'process_bom'=>1]);
		$this->data['processList'] = $this->item->getProductProcessList(['item_id'=>$this->data['dataRow']->item_id,'process_id'=>$this->data['dataRow']->process_ids]);
		$this->data['semiFinish'] = 1;
		$this->load->view('sopDesk/prc_movement_form',$this->data);
	}

	public function prcAccept(){
		$data = $this->input->post();
		$this->data['accepted_process_id'] = $data['process_id'];
		$this->data['prc_id'] = $data['prc_id'];
		$this->data['trans_type'] = !empty($data['trans_type'])?$data['trans_type']:1;
		$this->data['process_from'] = $data['process_from'];
		$this->load->view('sopDesk/accept_prc_qty',$this->data);
	}

	public function saveAcceptedQty(){
		$data = $this->input->post(); 
		$errorMessage = array();
        if (empty($data['accepted_process_id'])){ $errorMessage['accepted_process_id'] = "Prc Process required.";}
        if (empty($data['accepted_qty']) &&  empty($data['short_qty'])) {  $errorMessage['accepted_qty'] = "Quantity is required.";}
		else{
			$acceptedQty = !empty($data['accepted_qty'])?$data['accepted_qty']:0;
			$shortQty = !empty($data['short_qty'])?$data['short_qty']:0;
			$totalQty = $acceptedQty + $shortQty;
			$prcProcessData = $this->sop->getPRCProcessList(['process_id'=>$data['accepted_process_id'],'prc_id'=>$data['prc_id'],'pending_accepted'=>1,'single_row'=>1,'move_type'=>$data['trans_type'],'process_from'=>$data['process_from']]); 
			
			$pending_accept =!empty($prcProcessData->pending_accept)?$prcProcessData->pending_accept:0;
			if($acceptedQty > $pending_accept){
				$errorMessage['accepted_qty'] = "Accept Quantity is Invalid.".$pending_accept;
			}
			if(!empty($shortQty)){
				$pendingShort =( $pending_accept - $acceptedQty);
				if($shortQty > $pendingShort){
					$errorMessage['short_qty'] = " Short Quantity is Invalid.";
				}
			}
		}
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$data['trans_date'] = date("Y-m-d");
			$data['created_by'] = $this->loginId;
			$data['created_at'] = date("Y-m-d H:i:s");
			$result = $this->sop->saveAcceptedQty($data);
			$this->printJson($result);
		endif;
	}

	public function getPRCAcceptHtml(){
		$data = $this->input->post(); 
		$acceptData = $this->sop->getPrcAcceptData(['accepted_process_id'=>$data['accepted_process_id'],'prc_id'=>$data['prc_id'],'process_from'=>$data['process_from'],'trans_type'=>$data['trans_type']]);
		$html="";
        if (!empty($acceptData)) :
            $i = 1;
            foreach ($acceptData as $row) :
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deletePrcAccept','res_function':'getPrcAcceptResponse','controller':'sopDesk'}";
				$deleteBtn = '<a class="btn btn-outline-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trashSop('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
				$html .='<tr class="text-center">
							<td>' . $i++ . '</td>
							<td>' . formatDate($row->trans_date). '</td>
							<td>'.$row->from_process_name.'</td>
							<td>' . floatval($row->accepted_qty) . ' </td>
							<td hidden>' . floatval($row->short_qty) . '</td>
							<td>' . $deleteBtn . '</td>
						</tr>';
            endforeach;
        else :
            $html = '<td colspan="5" class="text-center">No Data Found.</td>';
        endif;
		$this->printJson(['status'=>1,'tbodyData'=>$html]);
	}

	public function deletePrcAccept(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sop->deletePrcAccept($data));
        endif;
	}

	public function challanRequest(){
		$data = $this->input->post();
		$this->data['trans_type'] = $data['trans_type'];
		$this->data['dataRow'] = $this->sop->getPRCProcessList(['process_id'=>$data['process_id'],'prc_id'=>$data['prc_id'],'single_row'=>1]);

		$this->data['process_from'] = $data['process_from'];
		$this->load->view('sopDesk/prc_challan_request',$this->data);
	}

	public function saveChallanRequest(){
		$data = $this->input->post();
        $errorMessage = array();
        if (empty($data['prc_id'])){ $errorMessage['prc_id'] = "Job Card No. is required.";}
        if (empty($data['process_id'])){ $errorMessage['process_id'] = "Process is required.";}
		if (empty($data['trans_date']) or $data['trans_date'] == null or $data['trans_date'] == ""){ $errorMessage['trans_date'] = "Date is required."; }
        if (empty($data['qty'])){
            $errorMessage['qty'] = "Request qty required";
       	}else{
			$prcProcessData = $this->sop->getPRCProcessList(['process_id'=>$data['process_id'],'prc_id'=>$data['prc_id'],'log_data'=>1,'pending_accepted'=>1,'single_row'=>1,'log_process_by'=>1,'move_type'=>$data['trans_type'],'process_from'=>$data['process_from']]); 
			$in_qty = (!empty($prcProcessData->in_qty)?$prcProcessData->in_qty:0);
			$ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
			$rej_found = !empty($prcProcessData->rej_found)?$prcProcessData->rej_found:0;
			$rw_qty = !empty($prcProcessData->rw_qty)?$prcProcessData->rw_qty:0;
			$rej_qty = !empty($prcProcessData->rej_qty)?$prcProcessData->rej_qty:0;
			$pendingReview = $rej_found - $prcProcessData->review_qty;
			$pending_production =($in_qty) - ((($ok_qty+$rej_qty+$rw_qty+$pendingReview)/$prcProcessData->output_qty) + $prcProcessData->ch_qty);
			
			if($pending_production < $data['qty'] ||  $data['qty'] < 0) :
				$errorMessage['qty'] = "Invalid Qty.";
			else:
				$check_qty = $data['qty'] +((($ok_qty+$rej_qty+$rw_qty+$pendingReview)/$prcProcessData->output_qty) + $prcProcessData->ch_qty);
				$mtResult = $this->sop->checkIssueMaterialForPrc(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'check_qty'=> $check_qty]);
				if($mtResult['status'] == 0){
					$errorMessage['qty'] = "Material Not available";
				}
			endif;
		}
		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$data['old_qty'] = $data['qty'];
			$this->printJson($this->sop->saveChallanRequest($data));
		endif;	
	}

	public function deleteChallanRequest(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sop->deleteChallanRequest($data));
        endif;
	}

	public function getChallanRequestHtml(){
		$data = $this->input->post();
		$requestData = $this->sop->getChallanRequestData(['process_id'=>$data['process_id'],'prc_id'=>$data['prc_id'],'trans_type'=>$data['trans_type'],'process_from'=>$data['process_from']]);
		$html="";
        if (!empty($requestData)) :
            $i = 1;
            foreach ($requestData as $row) :
				$deleteBtn = "";
				if($row->challan_id == 0){
					$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deleteChallanRequest','res_function':'getChallanRequestResponse','controller':'sopDesk'}";
					$deleteBtn = '<a class="btn btn-outline-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trashSop('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
				}
				$createdBy = $row->created_name.(!empty($row->created_at) ? '<hr class="m-0">'.date('d-m-Y H:i:s',strtotime($row->created_at)) : '');
				$html .='<tr class="text-center">
							<td>' . $i++ . '</td>
							<td>' . formatDate($row->trans_date). '</td>
							<td>' . $row->qty . ' </td>
							<td>' . $createdBy . ' </td>
							<td>' . $deleteBtn . '</td>
						</tr>';
            endforeach;
        else :
            $html = '<td colspan="5" class="text-center">No Data Found.</td>';
        endif;
		$prcProcessData = $this->sop->getPRCProcessList(['process_id'=>$data['process_id'],'prc_id'=>$data['prc_id'],'log_data'=>1,'pending_accepted'=>1,'single_row'=>1,'log_process_by'=>1,'move_type'=>$data['trans_type'],'process_from'=>$data['process_from']]); 
		$in_qty = (!empty($prcProcessData->in_qty)?$prcProcessData->in_qty:0);
		$ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
		$rej_found = !empty($prcProcessData->rej_found)?$prcProcessData->rej_found:0;
		$rw_qty = !empty($prcProcessData->rw_qty)?$prcProcessData->rw_qty:0;
		$rej_qty = !empty($prcProcessData->rej_qty)?$prcProcessData->rej_qty:0;
		$pendingReview = $rej_found - $prcProcessData->review_qty;
		$pending_production =($in_qty) - ((($ok_qty+$rej_qty+$rw_qty+$pendingReview)/$prcProcessData->output_qty) + +$prcProcessData->ch_qty);

		$this->printJson(['status'=>1,'tbodyData'=>$html,'pending_ch_qty'=>$pending_production]);
	}

	public function addPrcStock(){
		$data = $this->input->post();
		$this->data['dataRow'] = $this->sop->getPRCProcessList(['id'=>$data['id'],'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'single_row'=>1]);

		$rtd_location = $this->storeLocation->getStoreLocationList(['main_store'=>1, 'store_type'=>1]);
		$locaLevels = implode(',', array_column($rtd_location, 'store_level'));
		$this->data['locationList'] = $this->storeLocation->getStoreLocationList(['final_location'=>1,'main_store_level'=>$locaLevels]);

		$this->load->view('sopDesk/prc_stock_form',$this->data);
	}

	public function getPRCStockHtml(){
		$data = $this->input->post(); 
		$movementData = $this->sop->getProcessMovementList(['prc_process_id'=>$data['prc_process_id']]);
		$html="";
        if (!empty($movementData)) :
            $i = 1;
            foreach ($movementData as $row) :
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deletePRCMovement','res_function':'getStockResponse','controller':'sopDesk'}";
				$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trashSop('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
				$html .='<tr class="text-center">
							<td>' . $i++ . '</td>
							<td>' . formatDate($row->trans_date). '</td>
							<td>' . $row->qty . '</td>
							<td>' . $deleteBtn . '</td>
						</tr>';
            endforeach;
        else :
            $html = '<td colspan="4" class="text-center">No Data Found.</td>';
        endif;

		$prcProcessData = $this->sop->getPRCProcessList(['id'=>$data['prc_process_id'],'log_data'=>1,'movement_data'=>1,'single_row'=>1]); 
		$in_qty = (!empty($prcProcessData->current_process_id))?(!empty($prcProcessData->in_qty)?$prcProcessData->in_qty:0):$prcProcessData->ok_qty;
		$ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
		$movement_qty =!empty($prcProcessData->movement_qty)?$prcProcessData->movement_qty:0;
		$pending_movement = $ok_qty - $movement_qty;
		$this->printJson(['status'=>1,'tbodyData'=>$html,'pendingQty'=>$pending_movement]);
	}

	public function edit(){
		$data = $this->input->post();
        $this->data['customerData'] = $this->salesOrder->getPendingOrderItems(['group_by'=>'party_id']);
		$this->data['dataRow'] = $dataRow = $this->sop->getPRC(['id'=>$data['id']]);
		$this->data['productData'] = $this->getProductList(['party_id'=>$dataRow->party_id,'item_id'=>$dataRow->item_id]);
		$prdDetail = $this->getItemRevList(['item_id'=>$dataRow->item_id,'rev_no'=>$dataRow->rev_no]);
		$this->data['revisionData'] = $prdDetail['revHtml'];
        $this->load->view('sopDesk/prc_form',$this->data);
	}

	public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sop->deletePRC($id));
        endif;
    }

	public function getProcessorList(){
		$process_by = $this->input->post('process_by');		
		$options = '<option value="0">Select</option>';

		if($process_by == 2){
			$deptList = $this->department->getDepartmentList();
			if(!empty($deptList)){
				foreach($deptList as $row){
					$options .= '<option value="'.$row->id.'">'.$row->name.'</option>';
				}
			}
		}else{
			$machineList = $this->item->getItemList(['item_type'=>5,'active_machine'=>1]);
			if(!empty($machineList)){
				foreach($machineList as $row){
					if(empty($row->breakdown_id)){ 
						$options .= '<option value="'.$row->id.'">['.$row->item_code.'] '.$row->item_name.'</option>';
					}
				}
			}
		}
		$this->printJson(['status'=>1, 'options'=>$options]);
	}

	public function receiveStoredMaterial(){
        $data = $this->input->post();
		$this->data['dataRow'] = $this->sop->getPRCProcessList(['id'=>$data['id'],'single_row'=>1]);
        $this->data['movementList'] = $this->sop->getProcessMovementList(['prc_process_id'=>$data['id'],'send_to'=>4]);
        $this->load->view('sopDesk/receive_movement',$this->data);
    }

    public function saveReceiveStoredMaterial(){
        $data = $this->input->post(); 
        $errorMessage = array();
		
        if(empty(array_sum($data['qty']))){ $errorMessage['general_qty'] = "Qty is required.";}
		else{
			foreach($data['qty'] as $key=>$qty){
				$movementData = $this->sop->getProcessMovementList(['id'=>$data['trans_id'][$key],'single_row'=>1]);
				if($qty > $movementData->qty){
					$errorMessage['qty'.$data['trans_id'][$key]] = "Qty is invalid.";
				}
			}
		}
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->sop->saveReceiveStoredMaterial($data));
        endif;
    }

	public function clearPrcData(){
		$data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$result = $this->sop->clearPrcData($data);
			$result['prc_id'] = $data['id'];
            $this->printJson($result);
        endif;
	}

	public function requiredMaterial(){
		$data = $this->input->post();
		$this->data['prc_id'] = $data['id'];
		$this->data['prc_qty'] = $data['prc_qty'];
		$this->data['prc_type'] = (!empty($data['prc_type']) ? $data['prc_type'] : 1);
		$this->data['prcData'] = $this->sop->getPrc(['id'=>$data['id']]);
		$this->data['kitData'] = $this->item->getProductKitData(['item_id'=>$data['item_id'],'is_main'=>1,'with_alt_items'=>1,'not_in_item_type'=>9]);
		$this->data['prcBom'] = $this->sop->getPrcBomData(['prc_id'=>$data['id'],'production_data'=>1,'stock_data'=>1]);
		$this->load->view('sopDesk/prc_bom',$this->data);
	}

	public function savePrcMaterial(){
		$data = $this->input->post();
        $errorMessage = array();

		if (empty($data['item_id'])) { 
			$errorMessage['general_error'] = "Item is required."; 
		}

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->sop->savePrcMaterial($data));
        endif;
		
	}

	public function materialReturn(){
		$data = $this->input->post();
		$this->data['dataRow'] = $this->sop->getPrcBomData(['prc_id'=>$data['prc_id'],'item_id'=>$data['item_id'],'stock_data'=>1,'single_row'=>1]);
		$this->data['locationList'] = $this->storeLocation->getStoreLocationList(['id'=>(!empty($this->data['dataRow']->location_id)?$this->data['dataRow']->location_id:'')]);
		$this->load->view('sopDesk/prc_material_return',$this->data);
	}
	
	public function storeReturnedMaterial(){
		$data = $this->input->post();
		if(empty($data['item_id'])){ $errorMessage['general_error'] = "Item is required."; }
		if(empty($data['location_id'])){ $errorMessage['location_id'] = "Location is required."; }
		if(empty($data['batch_no'])){ $errorMessage['batch_no'] = "Batch No is required."; }
		if(empty($data['qty'])){ $errorMessage['qty'] = "Qty is required."; }
		else{
			$postData =['prc_id'=>$data['prc_id'],'item_id'=>$data['item_id'],'production_data'=>1,'stock_data'=>1,'single_row'=>1];
			$prcData = $this->sop->getPrc(['id'=>$data['prc_id']]);
			if($data['process_id'] == 0){
				$processArray = explode(",",$prcData->process_ids);
				$postData['log_process_id'] = $processArray[0];
			}
			$stockData = $this->sop->getPrcBomData($postData);
			$stockQty = $stockData->issue_qty - (( ($stockData->production_qty * $stockData->ppc_qty)/$stockData->output_qty) + $stockData->return_qty);
			if($data['qty'] > round($stockQty,3)){ $errorMessage['qty'] = "Qty is invalid."; }
			else{
				$customWhere = " stock_trans.trans_type IN('PMR','SSI')";
				$batchData = $this->itemStock->getItemStockBatchWise(['child_ref_id'=>$data['prc_id'],'item_id'=> $data['item_id'],'batch_no'=>$data['batch_no'],'customWhere'=>$customWhere,'single_row'=>1]);
				if($data['qty'] > abs($batchData->qty)){ $errorMessage['qty'] = "Qty is invalid."; }
			}
		}

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->sop->storeReturnedMaterial($data));
        endif;
		
	}

	public function getReturnHtml(){
		$data = $this->input->post();
		$customWhere = '  trans_type="PMR"';
		$batchData = $batchData = $this->itemStock->getItemStockBatchWise(['child_ref_id'=>$data['prc_id'],'item_id'=> $data['item_id'],'customWhere'=>$customWhere,'group_by'=>'stock_trans.id']);
		$html = "";
		if(!empty($batchData)){
			$i=1;
			foreach($batchData as $row){
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deleteReturn','res_function':'getReturnResponse'}";
				$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trashSop('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
				$html .= '<tr>
							<td>'.$i++.'</td>
							<td>'.formatDate($row->trans_date).'</td>
							<td>'.$row->location.'</td>
							<td>'.$row->batch_no.'</td>
							<td>'.$row->qty.'</td>
							<td>'.$row->remark.'</td>
							<td>'.$deleteBtn.'</td>
						</tr>';
			}
		} else {
			$html = '<td colspan="7" class="text-center">No Data Found.</td>';
		}
		$postData =['prc_id'=>$data['prc_id'],'item_id'=>$data['item_id'],'production_data'=>1,'stock_data'=>1,'single_row'=>1];
		$prcData = $this->sop->getPrc(['id'=>$data['prc_id']]);
        if($data['process_id'] == 0){
			$processArray = explode(",",$prcData->process_ids);
			$postData['log_process_id'] = $processArray[0];
		}
		$stockData = $this->sop->getPrcBomData($postData);
		if($prcData->prc_type == 2){
			$stockQty = $stockData->issue_qty - (( $stockData->cutting_cons) + $stockData->return_qty);
		}else{
			$stockQty = $stockData->issue_qty - ((( $stockData->production_qty * $stockData->ppc_qty)/$stockData->output_qty) + $stockData->return_qty);
		}
		
		$this->printJson(['status'=>1,'tbodyData'=>$html,'pending_qty'=>round($stockQty,3)]);
	}

	public function deleteReturn(){
		$id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sop->deleteReturn($id));
        endif;
	}

	function printDetailRouteCard($id){
		$prcData = $this->data['prcData'] = $this->sop->getPRCDetail(['prc_id'=>$id]);
		if(!empty($prcData))
		{
    		$this->data['prcMaterialData'] = $prcMaterialData = $this->sop->getPrcBomData(['prc_id'=>$id,'production_data'=>1,'stock_data'=>1]);
    		$this->data['prcProcessData'] = $this->sop->getPRCProcessList(['prc_id'=>$id,'process_id'=>$prcData->process_ids,'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'rework_data'=>1]); 
			if($prcData->cutting_flow == 2 && !empty($this->data['prcMaterialData'])){
				$this->data['cutingData'] = $this->sop->getCuttingBatchDetail(['prc_number'=>$prcMaterialData[0]->batch_no,'heat_no'=>$prcMaterialData[0]->heat_no,'production_data'=>1,'single_row'=>1]);
			}
			
    		$prcProcessData = $this->sop->getPRCProcessList(['current_process_id'=>0,'prc_id'=>$id,'log_data'=>1,'movement_data'=>1,'single_row'=>1]); 
			$this->data['logData'] = $this->sop->getProcessLogList(['prc_id'=>$id]);
			$this->data['invData'] = $this->salesInvoice->getBatchWiseInvoice(['prc_id'=>$id]);
		}

		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
        $pdfData = $this->load->view('sopDesk/print_route_card', $this->data, true);
		
        $printedBy = $this->employee->getEmployee(['id'=>$this->loginId]);
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
        $htmlFooter = '
			<table class="table top-table" style="margin-top:10px;border-top:1px solid #000000;">
				<tr>
					<td style="width:50%;">
					    Printed By & Date : '.$printedBy->emp_name.' ('.formatDate(date('Y-m-d H:s:i'), 'd-m-Y H:s:i').')
					</td>
					<td style="width:50%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
				</tr>
			</table>';

        $mpdf = new \Mpdf\Mpdf();

        $pdfFileName = 'PRC-' . $id . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->useSubstitutions = false;
		$mpdf->simpleTables = true;

        $mpdf->AddPage('P', '', '', '', '', 5, 5, 30, 20, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }   

	public function printPRCMovement($id) {
		$movementData = $this->sop->getProcessMovementList(['id'=>$id,'single_row'=>1]);

		if (!empty($movementData->next_process_id)) {
            $mtitle = 'Process Tag';
            $revno = date('d.m.Y <br> h:i:s A');
        } else {
            $mtitle = 'Final Inspection	OK Material';
            $revno = 'F QA 25<br>(01/01.10.2021)';
        }

		$logo = base_url('assets/images/logo.png');
		$title = 'Process Tag';
		$qrText = $movementData->prc_id.'/'.$movementData->next_process_id;
		$file_name = $movementData->prc_id.'-'.$movementData->next_process_id;
		$qrIMG = base_url().$this->getQRCode($qrText,'assets/uploads/sop/',$file_name);
		$qrIMG =  '<td  rowspan="3" class="text-right" style="padding:2px;"><img src="'.$qrIMG.'" style="height:25mm;"></td>';

		$topSectionO = '<table class="table">
							<tr>
								<td style="width:20%;"><img src="' . $logo . '" style="height:40px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%;">' . $title . '</td>
								<td style="width:30%;" class="text-right"><span style="font-size:0.8rem;">'. $revno .'</td>
							</tr>
						</table>';

		$itemList = '<table class="table tag_print_table"  style="font-size:0.8rem;">
						<tr class="text-center">
							<td style="width:70px;"><b>PRC No</b></td>
							<td><b>Date</b></td>
							<td><b>PRC Qty</b></td>
							'.$qrIMG .'
						<tr class="text-center">
							<td>' . $movementData->prc_number . '</td>
							<td>' . formatDate($movementData->trans_date) . '</td>
							<td>' . floatVal($movementData->prc_qty) . '</td>
						</tr>
						<tr class="bg-light">
							<td><b>Part</b></td>
							<td colspan="2">' . (!empty($movementData->item_code) ? '['.$movementData->item_code.'] ' : '') . $movementData->item_name . '</td>
						</tr>
					</table>
					<table class="table tag_print_table"  style="font-size:0.8rem;">
						<tr>
							<th style="width:100px;" class="text-left">Qty.</th>
							<td>' . floatVal($movementData->qty) . '</td>
							
						</tr>
						<tr>
							<th class="text-left">Completed Process</th>
							<td >'. $movementData->current_process_name .'</td>
						</tr>
						<tr>
							<th class="text-left">Next Process</th>
							<td  >'. $movementData->next_process_name .'</td>
						</tr>
					</table>';

		$pdfData = '<div style="width:97mm;height:50mm;text-align:center;float:left;padding:0mm 0.7mm;">' . $topSectionO . $itemList . '</div>';

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 60]]);
        $pdfFileName = str_replace(" ", "_", str_replace("/", " ", $mtitle)) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('P', '', '', '', '', 1, 1, 2, 2, 1, 1);
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
	}

	public function printPRCRejLog($id) {
		$logData = $this->sop->getProcessLogList(['id'=>$id,'single_row'=>1]);

        $vendorName = (!empty($logData->emp_name)) ? $logData->emp_name : (!empty($tagData->processor_name) ? $tagData->processor_name : '' );
        
		$title = "";
        $mtitle = "";
        $revno = "";
        $qtyLabel = "";
        $qty = 0;
        
		$mtitle = 'Rejection at M/c';
		$revno = 'R-QC-65 (00/01.10.22)';
		$qtyLabel = "Rej Qty";

        $logo = base_url('assets/images/logo.png');


        $topSection = '<table class="table">
            <tr>
                <td style="width:20%;"><img src="' . $logo . '" style="height:40px;"></td>
                <td class="org_title text-center" style="font-size:1rem;width:50%;">' . $mtitle . ' <br><small><span class="text-dark">' . $title . '</span></small></td>
                <td style="width:30%;" class="text-right"><span style="font-size:0.8rem;">' . $revno . '</td>
            </tr>
        </table>';

        $itemList = '<table class="table table-bordered vendor_challan_table">
			<tr>
				<td style="font-size:0.7rem;"><b>PRC No.</b></td>
				<td style="font-size:0.7rem;">' . $logData->prc_number . '</td>
				<td style="font-size:0.7rem;"><b>Date</b></td>
				<td style="font-size:0.7rem;">' . formatDate($logData->trans_date) . '</td>
			</tr>
			<tr>
				<td style="font-size:0.7rem;"><b>Part</b></td>
				<td style="font-size:0.7rem;" colspan="3">' . (!empty($logData->item_code) ? '['.$logData->item_code.'] ' : '') . $logData->item_name . '</td>
			</tr>
			<tr>
				<td style="font-size:0.7rem;"><b>Process</b></td>
				<td style="font-size:0.7rem;" colspan="3">' . $logData->process_name . '</td>
			</tr>
			<tr>
				<td style="font-size:0.7rem;"><b>Ok Qty</b></td>
				<td style="font-size:0.7rem;">' . floatval($logData->qty) . '</td>
				<td style="font-size:0.7rem;"><b>Rej Qty</b></td>
				<td style="font-size:0.7rem;">' . floatval($logData->rej_found) . '</td>
			</tr>
			<tr>
				<td style="font-size:0.7rem;"><b>Vendor/Ope.</b></td>
				<td style="font-size:0.7rem;">' . $vendorName . '</td>
				<td style="font-size:0.7rem;"><b>M/c No</b></td>
				<td style="font-size:0.7rem;">' . (!empty($logData->machine_code) ? '['.$logData->machine_code.'] ' : '') . $logData->machine_name . '</td>
			</tr>
			<tr>
				<td style="font-size:0.7rem;"><b>Issue By</b></td>
				<td style="font-size:0.7rem;" colspan="3">' . $logData->created_name . '</td>
			</tr>
		</table>';
        $pdfData = '<div style="width:97mm;height:50mm;text-align:center;float:left;padding:0mm 0.7mm;">' . $topSection . $itemList . '</div>';
		
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 60]]);
        $pdfFileName = str_replace(" ", "_", str_replace("/", " ", $mtitle)) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('P', '', '', '', '', 0, 0, 2, 2, 2, 2);
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
	}

	public function prcProcesstag($prc_id,$process_id) {
		$processData = $this->sop->getPRCProcessList(['prc_id'=>$prc_id,'process_id'=>$process_id,'single_row'=>1]);
		$logo = base_url('assets/images/logo.png');
		$title = 'Process Tag';
		$qrText = $prc_id.'/'.$process_id;
		$file_name = $prc_id.'-'.$process_id;
		$qrIMG = base_url().$this->getQRCode($qrText,'assets/uploads/sop/',$file_name);
		$qrIMG =  '<td  rowspan="4" class="text-right" style="padding:2px;"><img src="'.$qrIMG.'" style="height:25mm;"></td>';
		$topSectionO = '<table class="table top-table">
							<tr>
								<td style="width:40%;"><img src="' . $logo . '" style="height:40px;"></td>
								<td class="org_title text-left" style="font-size:1rem;width:60%;">' . $title . '</td>
								
							</tr>
						</table>';
		$itemList = '<table class="table tag_print_table" style="font-size:0.8rem;">
						<tr class="text-center bg-light" >
							<td style="width:20%;"><b>PRC No</b></td>
							<td><b>Date</b></td>
							<td><b>PRC Qty</b></td>
							'.$qrIMG .'
						</tr>
						<tr class="text-center">
							<td>' . $processData->prc_number . '</td>
							<td>' . formatDate($processData->prc_date) . '</td>
							<td>' . floatVal($processData->prc_qty) . '</td>
						</tr>
						<tr class="bg-light">
							<td><b>Part</b></td>
							<td colspan="2">' . (!empty($processData->item_code) ? '['.$processData->item_code.'] ' : '') . $processData->item_name . '</td>
						</tr>
						<tr>
							<th class="text-left" >Process</th>
							<td colspan="2">'. (!empty($processData->current_process)?$processData->current_process:'Initial Stage') .'</td>
						</tr>
						<tr>
							<th class="text-left">Next Process</th>
							<td colspan="3">'. (!empty($processData->next_process)?$processData->next_process:'Final Inspection') .'</td>
						</tr>
					</table>';

		$pdfData = '<div style="width:97mm;height:50mm;text-align:center;float:left;padding:0mm 0.7mm;">' . $topSectionO . $itemList . '</div>';
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 60]]);
        $pdfFileName = str_replace(" ", "_", str_replace("/", " ",'process_tag')) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('P', '', '', '', '', 1, 1, 2, 2, 1, 1);
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
	}

	public function changePRCStage(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sop->changePRCStage($data));
        endif;
    }

	/* Update PRC Qty */
	public function updatePrcQty(){
        $this->data['prc_id'] = $this->input->post('id');
        $this->load->view('sopDesk/prc_update', $this->data);
    }

    public function getUpdatePrcQtyHtml(){
        $data = $this->input->post();
        $logdata = $this->sop->getPRCUpdateLogData(['prc_id'=> $data['prc_id']]); 
        $tbodyData = ''; 
        if(!empty($logdata)): $i=1; 
            foreach($logdata as $row): 
                $deleteParam = $row->id . ",'PRC Qty'";
                $logType = ($row->log_type == 1)?'(+) Add':'(-) Reduce';
                $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deletePrcUpdateQty','res_function':'updatePrcQtyHtml','controller':'sopDesk'}";
                $deleteBtn = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="left"><i class="mdi mdi-trash-can-outline"></i></a>';

                $tbodyData .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->log_date).'</td>
                    <td>'.$logType.'</td>
                    <td>'.$row->qty.'</td>
                    <td>'.$deleteBtn.'</td>
                </tr>';
            endforeach;
		else:
			$tbodyData .= '<tr class="text-center"><td colspan="5">Data not available.</td></tr>';
        endif; 
        $this->printJson(['status' => 1, 'tbodyData' => $tbodyData]);
    }
    
	public function savePrcQty(){
        $data = $this->input->post();  
        $errorMessage = array();

		if (empty($data['qty'])) :
			$errorMessage['qty'] = "Qty is required.";
		endif;

        if ($data['log_type'] == -1) :			
			$prcData = $this->sop->getPRC(['id'=>$data['prc_id']]);
			$process_ids = (!empty($prcData->process_ids) ? explode(',',$prcData->process_ids) : []);
			$processId = (!empty($process_ids) ? $process_ids[0] : 0);

			$prcProcessData = $this->sop->getPRCProcessList(['process_id'=>$processId,'prc_id'=>$data['prc_id'],'log_data'=>1,'movement_data'=>1,'log_process_by'=>1,'pending_accepted'=>1,'single_row'=>1]); 

			$in_qty = (!empty($prcProcessData->in_qty)?$prcProcessData->in_qty:0);
			$ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
			$rej_found = !empty($prcProcessData->rej_found)?$prcProcessData->rej_found:0;
			$rw_qty = !empty($prcProcessData->rw_qty)?$prcProcessData->rw_qty:0;
			$rej_qty = !empty($prcProcessData->rej_qty)?$prcProcessData->rej_qty:0;
			$pendingReview = $rej_found - $prcProcessData->review_qty;
			$pending_production = ($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$prcProcessData->ch_qty);
						
			if($pending_production < $data['qty'] ||  $data['qty'] < 0) :
				$errorMessage['qty'] = "Invalid Qty.".$pending_production;
			endif;
        endif;
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $result = $this->sop->savePrcQty($data);
            $this->printJson($result);
        endif;
    }

    public function deletePrcUpdateQty(){
        $id = $this->input->post('id');		
		$logData = $this->sop->getPRCUpdateLogData(['id'=>$id,'single_row'=>1]);
		$prcData = $this->sop->getPRC(['id'=>$logData->prc_id]);
		$process_ids = (!empty($prcData->process_ids) ? explode(',',$prcData->process_ids) : []);
		$processId = (!empty($process_ids) ? $process_ids[0] : 0);

        $errorMessage = '';
        if ($logData->log_type == 1) :
			$prcProcessData = $this->sop->getPRCProcessList(['process_id'=>$processId,'prc_id'=>$logData->prc_id,'log_data'=>1,'movement_data'=>1,'log_process_by'=>1,'pending_accepted'=>1,'single_row'=>1]); 

			$in_qty = (!empty($prcProcessData->in_qty)?$prcProcessData->in_qty:0);
			$ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
			$rej_found = !empty($prcProcessData->rej_found)?$prcProcessData->rej_found:0;
			$rw_qty = !empty($prcProcessData->rw_qty)?$prcProcessData->rw_qty:0;
			$rej_qty = !empty($prcProcessData->rej_qty)?$prcProcessData->rej_qty:0;
			$pendingReview = $rej_found - $prcProcessData->review_qty;
			$pending_production = ($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$prcProcessData->ch_qty);
			
            if ($pending_production < $logData->qty) :
                $errorMessage = "Sorry...! You can't delete this PRC log because this qty moved to next process.";
            endif;
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $result = $this->sop->deletePrcUpdateQty($id);
            $this->printJson($result);
        endif;
    }

	public function productionShortage(){
		$this->data['headData']->pageTitle = "Production Shortage";
		$this->data['tableHeader'] = getProductionDtHeader('productionShortage');
        $this->load->view('sopDesk/production_shortage',$this->data);
	}

	public function getShortageDtRows(){
		$data = $this->input->post();
		$result = $this->sop->getShortageDtRows($data);
		$sendData = array();$i=($data['start']+1);
		foreach($result['data'] as $row):          
			$row->sr_no = $i++;         
			$sendData[] = getProductionShortageData($row);
		endforeach;
		$result['data'] = $sendData;
		$this->printJson($result);
	}

	/*** */
	public function productionProcess(){
		$this->data['headData']->pageTitle = "Production Process";
		$this->data['headData']->pageUrl = "sopDesk/productionProcess";
		$this->data['processList'] = $this->sop->getSopProcessList();
        $this->load->view('sopDesk/production_process',$this->data);
	}

	public function productionLog($process_id){
		$this->data['headData']->pageTitle = "Production Log";
		$this->data['headData']->pageUrl = "sopDesk/productionProcess";
		if($process_id == 1){
			$this->data['tableHeader'] = getProductionDtHeader('semiFinishedLog');
		}else{
			$this->data['tableHeader'] = getProductionDtHeader('prcLog');
		}
		
		$this->data['process_id'] = $process_id;
		$this->data['processData'] = $this->process->getProcess(['id'=>$process_id]);
        $this->load->view('sopDesk/production_log',$this->data);
	}

	public function getLogDTRows($process_id,$move_type=1){
        $data = $this->input->post();$data['process_id'] = $process_id;$data['move_type'] = $move_type;
		
        $result = $this->sop->getLogDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
			$row->move_type = $move_type;
			if($process_id == 1){
				$sendData[] = getSemiFinishedLogData($row);
			}else{
				$sendData[] = getPrcLogData($row);
			}    
            
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
	public function mfgStore($process_id,$record_type ='Demand'){
		$this->data['headData']->pageTitle = "Mfg. Store";
		if($record_type == 'Demand'){
			$this->data['tableHeader'] = getProductionDtHeader('mfgStoreDemand');
		}elseif($record_type == 'REQUEST'){
			$this->data['tableHeader'] = getProductionDtHeader('mfgStoreDemand');
		}elseif($record_type == 'mfgStoreStock'){
			$this->data['tableHeader'] = getProductionDtHeader('mfgStoreStock');
		}
		
		$this->data['process_id'] = $process_id;
		$this->data['processData'] = $this->process->getProcess(['id'=>$process_id]); 
        $this->load->view('sopDesk/mfg_store',$this->data);
	}

	public function getMfgStoreDTRows($process_id,$record_type = 'DEMAND'){
        $data = $this->input->post();$data['process_id'] = $process_id;$data['record_type'] = $record_type;
		if($record_type == 'STOCK'){
			$data['process_id'] = $process_id;
        	$result = $this->sop->getPendingMoveDTRows($data);
		}else{
			$result = $this->sop->getMfgStoreDTRows($data);
		}
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
			$row->current_process = $process_id;
			if($record_type == 'STOCK'){
				$sendData[] = getMfgStoreStockData($row); 
			}else{
				$sendData[] = getMfgStoreData($row); 
			}
			   
            
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function addMfgRequest(){
		$data = $this->input->post();
		$this->data['req_from'] = $data['process_id'];
		$this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
		$this->data['processList'] = $this->process->getProcessList();
		$trans_no = $this->sop->getNextReqNo();
		$this->data['trans_number'] = 'REQ/'.$this->shortYear.'/'.$trans_no;
        $this->load->view('sopDesk/request_form',$this->data);
	}

	public function editMfgRequest(){
		$data = $this->input->post();
		$this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
		$this->data['processList'] = $this->process->getProcessList();
		$this->data['dataRow'] = $this->sop->getRequestData(['id'=>$data['id'],'single_row'=>1]);
        $this->load->view('sopDesk/request_form',$this->data);
	}

	public function saveMfgRequest(){
		$data = $this->input->post();
        $errorMessage = array();
        if (empty($data['item_id'])){ $errorMessage['item_id'] = "Product is required."; }
        if (empty($data['qty']) || $data['qty'] < 0){ $errorMessage['qty'] = "Quantity is required."; }
        if (empty($data['req_to'])){ $errorMessage['req_to'] = "Request To is required."; }
		
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			
			if(empty($data['id'])){
				$data['trans_no'] = $this->sop->getNextReqNo();
				$data['trans_number'] = 'REQ/'.$this->shortYear.'/'.$data['trans_no'];
				$data['created_by'] = $this->session->userdata('loginId');
			}else{
				$data['updated_by'] = $this->session->userdata('loginId');
			}
            $this->printJson($this->sop->saveMfgRequest($data));
        endif;
	}

	public function deleteMfgRequest(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->sop->deleteMfgRequest($data));
        endif;
	}

	public function issueRequestedItem(){
		$data = $this->input->post();
		$this->data['reqData'] =$reqData = $this->sop->getRequestData(['id'=>$data['id'],'single_row'=>1]);
		$this->data['prcData'] = $this->sop->getProcessLogList(['process_id'=>$reqData->req_to,'item_id'=>$reqData->item_id,'group_by'=>'prc_log.prc_id,prc_log.process_id,prc_log.trans_type,process_from','rejection_review_data'=>1,'movement_data'=>1,'grouped_data'=>1,'having'=>'(ok_qty-movement_qty) > 0']);
        $this->load->view('sopDesk/multi_movement',$this->data);
	}

	public function saveIssuedItem(){
		$data = $this->input->post();
		$errorMessage = array();
        if (empty($data['item_id'])){ $errorMessage['item_id'] = "Product is required."; }
        if (!isset($data['qty'])){ $errorMessage['qty'] = "Quantity is required."; }
		else{
			$totalQty = array_sum($data['qty']);
			if($totalQty <= 0){
				$errorMessage['qty'] = "Quantity is required.";
			}else{
				$i=1;
				foreach($data['qty'] as $key=>$qty){
					if(!empty($qty) && $qty > 0){
						$logData = $this->sop->getProcessLogList(['process_id'=>$data['process_id'],'item_id'=>$data['item_id'],'prc_id'=>$data['prc_id'][$key],'process_from'=>$data['process_from'][$key],'trans_type'=>$data['move_from'][$key],'group_by'=>'prc_log.prc_id,prc_log.process_id,prc_log.trans_type,prc_log.process_from','rejection_review_data'=>1,'movement_data'=>1,'grouped_data'=>1,'single_row'=>1]);
						$pending_qty = $logData->ok_qty - $logData->movement_qty;
						if($qty > $pending_qty){
							$errorMessage['qty'.$i] = "Quantity is Invalid.";
						}
					}
					$i++;
				}
			}
		}
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			
			
            $this->printJson($this->sop->saveIssuedItem($data));
        endif;
	}

	/**** Final Inspection ****/

	public function addFinalInspection(){
        $data = $this->input->post();
		$this->data['trans_type'] = $data['trans_type'];
		$this->data['process_from'] = $data['process_from'];
		$this->data['dataRow'] = $prcProcessData = $this->sop->getPRCProcessList(['process_id'=>$data['process_id'],'prc_id'=>$data['prc_id'],'log_data'=>1,'movement_data'=>1,'log_process_by'=>1,'pending_accepted'=>1,'single_row'=>1,'process_from'=>$data['process_from'],'move_type'=>$data['trans_type']]); 
        $this->data['revList'] = $this->ecn->getItemRevision(['item_id'=>$this->data['dataRow']->item_id]); 

        $this->data['trans_no'] = $this->sop->getFirNextNo();
        $this->data['trans_number'] = "FIR".sprintf(n2y(date('Y'))."%03d",$this->data['trans_no']);
		$this->load->view('sopDesk/fir_form',$this->data);
	}

	public function getFinalInspectionData(){
        $data = $this->input->post();
        $paramData = $this->item->getInspectionParameter(['item_id'=>$data['item_id'],'control_method'=>'FIR','rev_no'=>$data['rev_no']]);
        $tbodyData="";$i=1; $theadData='';
                $theadData .= '<tr class="thead-info" style="text-align:center;">
                            <th rowspan="2" style="width:5%;">#</th>
                            <th rowspan="2">Parameter</th>
                            <th rowspan="2">Specification</th>
                            <th rowspan="2" style="width:10%">Instrument</th>
							<th colspan="2" style="width:10%">Tolerance</th>
							<th colspan="2" style="width:10%">Specification Limit</th>
                            <th colspan="'.$data['sampling_qty'].'" style="text-align:center;">Observation on Samples</th>
							<th rowspan="2" style="width:5%">Result</th>
                        </tr>
                        <tr style="text-align:center;">';
						$theadData .= '<th style="width:5%">Min</th>';
						$theadData .= '	<th style="width:5%">Max</th>';
						$theadData .= '	<th style="width:5%">LSL</th>';
						$theadData .= '	<th style="width:5%">USL</th>';
                        for($j=1; $j<=$data['sampling_qty']; $j++):
                            $theadData .= '<th>'.$j.'</th>';
                        endfor;    
                $theadData .='</tr>';
        if(!empty($paramData)):
            foreach($paramData as $row):
                $lsl = floatVal($row->specification) - $row->min;
                $usl = floatVal($row->specification) + $row->max;
                $tbodyData.= '<tr>
                            <td style="text-align:center;">'.$i++.'</td>
                            <td style="width:10px;">'.$row->parameter.'</td>
                            <td style="width:10px;">'.$row->specification.'</td>    
                            <td style="width:20px;">'.$row->instrument.'</td>
							<td style="width:5px;">'.$row->min.'</td>
							<td style="width:5px;">'.$row->max.'</td>
							<td style="width:5px;">'.$lsl.'</td>
							<td style="width:5px;">'.$usl.'</td>';
                            for($j=1; $j<=$data['sampling_qty']; $j++):
                $tbodyData.=' <td style="min-width:100px;"><input type="text" name="sample'.($j).'_'.$row->id.'" class="form-control" value=""></td>';
							endfor;  
				$tbodyData.='<td><select name="result_'.$row->id.'" class="form-control select2">
									<option value="Ok">Ok</option>
									<option value="Not Ok">Not Ok</option>
								</select></td>';
                $tbodyData.='</tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="11" style="text-align:center;">No Data Found</td></tr>';
        endif;
        $this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"theadData"=>$theadData]);
    }
	
	public function printFinalInspection($id,$output_type = 'I',$type = 0){
		$this->printJson($this->sop->printFinalInspection($id,$output_type,$type));
        /*$this->data['firData'] = $firData = $this->sop->getFinalInspectData(['id'=>$id]);
        $this->data['paramData'] =  $this->item->getInspectionParameter(['item_id'=>$firData->item_id,'control_method'=>'FIR','rev_no'=>$firData->rev_no]);
        $this->data['companyData'] = $this->masterModel->getCompanyInfo();
		
		$logo=base_url('assets/images/logo.png'); 
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('sopDesk/fir_pdf',$this->data,true);
		
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">FINAL INSPECTION REPORT</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">QA/F/02 (01/01.05.17)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">'.$firData->emp_name.'</td>
						</tr>
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='fir_'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(100,100));
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,25,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		if($output_type == 'I'){
			$mpdf->Output($pdfFileName,$output_type);	
		}else{
			$filePath = realpath(APPPATH . '../assets/uploads/fir_reports/');
			$mpdf->Output($filePath.'/'.$fileName, 'F');
			return $filePath.'/'.$fileName;
		}	*/
	}

	/****  END Final Inspection INSPECTION ****/

	public function getExtraIssueMaterial(){
		$data = $this->input->post();
		$this->data['extMaterial'] = $this->store->getMaterialIssueData(['customWhere'=>' issue_register.item_id NOT IN(SELECT item_id FROM prc_bom WHERE prc_id = '.$data['prc_id'].' AND is_delete = 0)','prc_id'=>$data['prc_id'],'issue_type'=>2]);
		$this->load->view("sopDesk/extra_material",$this->data);
	}
	
	public function updateRevNo(){
		$data = $this->input->post();
		if(empty($data['prc_id'])){ $errorMessage['general_error'] = "Process Id is required."; }
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->sop->updateRevNo($data));
        endif;
	}

	public function soPlanning(){
		$this->data['headData']->pageTitle = "Sales Order Planning";
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
		$this->load->view("sopDesk/forging_planning",$this->data);
	}

	public function getSoPlanningData(){
		$data = $this->input->post();
		$planData = $this->sop->getForgingPlanningData($data);
		$tbodyData = '';
		if(!empty($planData)){
			foreach($planData AS $row){
				$pend_forging = $row->prc_qty - $row->forging_qty;
				$tbodyData .= '<tr>
									<td>'.$row->item_code.' '.$row->item_name.'</td>
									<td>'.$row->doc_no.'</td>
									<td>'.$row->rm_name.'</td>
									<td>'.$row->cut_wt.'</td>
									<td>'.$pend_forging.'</td>
									<td>'.($pend_forging * $row->cut_wt).'</td>
									<td>'.$row->batch_no.'</td>
									<td>'.$row->cutting_qty.'</td>
									<td>'.$row->forging_qty.'</td>
									<td>'.formatDate($row->cod_date).'</td>
								</tr>';
			}
		}
		$this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
	}
}
?>