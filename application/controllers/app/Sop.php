<?php
class Sop extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Sop";
		$this->data['headData']->controller = "app/sop";
		$this->data['headData']->pageUrl = "app/sop";
		$this->data['headData']->appMenu = "app/sop";   
	}
	
	public function index(){
        $this->data['rec_per_page'] = 10; // Records Per Page
        $this->load->view('app/sop_desk',$this->data);
    }

    public function getPrcList($parameter = []){
        $next_page = 0;
        $postData = $this->input->post();
		$prcList = Array();
		if(isset($postData['page']) AND isset($postData['start']) AND isset($postData['length']))
        {
            $prcList = $this->sop->getPRCList($postData);
            $next_page = intval($postData['page']) + 1;
        }
        else{ $prcList = $this->sop->getPRCList($postData); }
		$this->data['prcList'] = $prcList;
        $prcList = $this->load->view('app/prc_list_view',$this->data,true);
        $this->printJson(['orderDetail'=>$prcList,'next_page'=>$next_page]);
    }
	
    public function prcDetail($id){
        $this->data['prcData'] = $this->sop->getPRC(['id'=>$id]);
        $this->load->view('app/prc_detail',$this->data);
    }

    public function getPrcDetailHtml(){
        $postData = $this->input->post();
		$this->data['status'] =2;
        $prcData = $this->data['prcData'] = $this->sop->getPRCDetail(['prc_id'=>$postData['id']]);
        $this->data['prcProcessData'] = (!empty($prcData->prcProcessData)) ? $prcData->prcProcessData : [];
        $prcProcessData = $this->sop->getPRCProcessList(['current_process_id'=>0,'prc_id'=>$postData['id'],'log_data'=>1,'movement_data'=>1,'single_row'=>1]); 
        $ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
        $movement_qty =!empty($prcProcessData->movement_qty)?$prcProcessData->movement_qty:0;
        $this->data['pending_movement'] = $ok_qty - $movement_qty;
        $processDetail = $this->load->view('app/prc_detail_view',$this->data,true);
		
        $this->printJson(['processDetail'=>$processDetail]);
    }

    public function prcMovement(){
		$data = $this->input->post();
		$this->data['dataRow'] = $prcProcessData = $this->sop->getPRCProcessList(['id'=>$data['id'],'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'process_bom'=>1,'single_row'=>1]);
		$this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList(['final_location'=>1]);		
		$ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
		$movement_qty =!empty($prcProcessData->movement_qty)?$prcProcessData->movement_qty:0;
		$pending_movement = $ok_qty - $movement_qty;
		$this->data['pending_movement'] = $pending_movement;
		$this->data['masterSetting'] = $this->sop->getAccountSettings();
		$this->load->view('app/prc_movement_form',$this->data);
	}

    public function prcAccept(){
		$data = $this->input->post();
		$this->data['accepted_process_id'] = $data['id'];
		$this->data['prc_id'] = $data['prc_id'];
		$this->data['prev_prc_process_id'] = $data['prev_prc_process_id'];
		$this->load->view('app/accept_prc_qty',$this->data);
	}

	public function prcLog(){
		$data = $this->input->post();
		$this->data['dataRow'] = $prcProcessData = $this->sop->getPRCProcessList(['id'=>$data['id'],'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'process_bom'=>1,'single_row'=>1]);
		if(!empty($data['challan_id'])){
			$this->data['challan_id'] = $data['challan_id'];
			$this->data['ref_trans_id'] = $data['ref_trans_id'];
			$this->data['process_by'] = $data['process_by'];
			$this->data['processor_id'] = $data['processor_id'];
		}
		$this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
		$this->data['shiftData'] = $this->shiftModel->getShiftList();
		$this->data['operatorList'] = $this->employee->getEmployeeList();		

		$in_qty = (!empty($prcProcessData->current_process_id))?(!empty($prcProcessData->in_qty)?$prcProcessData->in_qty:0):$prcProcessData->ok_qty;
		$ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
		$rej_found = !empty($prcProcessData->rej_found)?$prcProcessData->rej_found:0;
		$rw_qty = !empty($prcProcessData->rw_qty)?$prcProcessData->rw_qty:0;
		$rej_qty = !empty($prcProcessData->rej_qty)?$prcProcessData->rej_qty:0;
		$pendingReview = $rej_found - $prcProcessData->review_qty;
		$pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$prcProcessData->ch_qty);
		$this->data['pending_log'] = $pending_production;
		$this->data['masterSetting'] = $this->sop->getAccountSettings();
		$this->load->view('app/prc_log_form',$this->data);
	}

    public function challanRequest(){
		$data = $this->input->post();
		$this->data['dataRow'] = $this->sop->getPRCProcessList(['id'=>$data['id'],'single_row'=>1]);
		$this->load->view('app/prc_challan_request',$this->data);
	}

    public function receiveStoredMaterial(){
        $data = $this->input->post();
		$this->data['dataRow'] = $this->sop->getPRCProcessList(['id'=>$data['id'],'single_row'=>1]);
        $this->data['movementList'] = $this->sop->getProcessMovementList(['prc_process_id'=>$data['id'],'send_to'=>4]);
        $this->load->view('app/receive_movement',$this->data);
    }

    public function addPrcStock(){
		$data = $this->input->post();
		$this->data['dataRow'] = $this->sop->getPRCProcessList(['id'=>$data['id'],'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'single_row'=>1]);
		$this->load->view('app/prc_stock_form',$this->data);
	}

	public function getPrcLogDetail(){
		$data = $this->input->post();
		$this->data['logData'] = $this->sop->getProcessLogList($data);
		$this->data['dataRow'] = $this->sop->getPRCProcessList(['id'=>$data['prc_process_id'],'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'single_row'=>1]);
		$this->load->view('app/log_detail',$this->data);
	}

	public function getPrcMovementDetail(){
		$data = $this->input->post();
		$this->data['movementData'] = $this->sop->getProcessMovementList(['prc_process_id'=>$data['prc_process_id']]);
		$this->data['dataRow'] = $this->sop->getPRCProcessList(['id'=>$data['prc_process_id'],'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'single_row'=>1]);
		$this->load->view('app/movement_detail',$this->data);
	}

	
	public function scanSop(){
        $this->load->view('app/scan_sop',$this->data);
    }

	public function getProcessDetail(){
		$data = $this->input->post();
		$code = explode("/",$data['code']);
		$processData = $this->sop->getPRCProcessList(['prc_id'=>$code[0],'process_id'=>$code[1],'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'single_row'=>1]);
		$html = "";
		if(!empty($processData)){
			$currentProcess = !empty($processData->current_process)?$processData->current_process : 'Initial Stage';
			$lastMove = (!empty($processData->next_process) ? $processData->next_process : 'Ready To Dispatch');
			$in_qty = (!empty($processData->current_process_id))?(!empty($processData->in_qty)?$processData->in_qty:0):$processData->ok_qty;
			$ok_qty = !empty($processData->ok_qty)?$processData->ok_qty:0;
			$rej_found_qty = !empty($processData->rej_found)?$processData->rej_found:0;
			$rej_qty = !empty($processData->rej_qty)?$processData->rej_qty:0;
			$rw_qty = !empty($processData->rw_qty)?$processData->rw_qty:0;
			$pendingReview = $rej_found_qty - $processData->review_qty;
			$pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview);
			$movement_qty =!empty($processData->movement_qty)?$processData->movement_qty:0;
			$short_qty =!empty($processData->short_qty)?$processData->short_qty:0;
			$pending_movement = $ok_qty - ($movement_qty);
			$pending_accept =!empty($processData->pending_accept)?$processData->pending_accept:0;

			$logBtn = "";
			if(!empty($processData->current_process_id) && $pending_production > 0){
				$logParam = "{'postData':{'prc_id' : ".$processData->prc_id.",'process_id' : ".$processData->process_id."},'modal_id' : 'modalLong', 'call_function':'prcLog', 'form_id' : 'addPrcLog', 'title' : 'PRC LOG', 'js_store_fn' : 'storeData', 'fnsave' : 'savePRCLog','save_controller':'sopDesk'}";
				$logBtn = '<a href="javascript:void(0)" onclick="loadform('.$logParam.')" class="btn btn-sm btn-primary text-left  permission-modify" datatip="Add Log" flow="down">Log</a>';
				
			}
			
			$acceptBtn="";
			if($pending_accept > 0){
				$title = '[Pending Qty : '.floatval($pending_accept).']';
				$acceptParam = "{'postData':{'process_id' : ".$processData->process_id.",'prc_id':".$processData->prc_id."},'modal_id' : 'modalLong', 'call_function':'prcAccept', 'form_id' : 'addPrcAccept', 'title' : 'Accept  ".$title."', 'js_store_fn' : 'storeData', 'fnsave' : 'saveAcceptedQty','save_controller':'sopDesk','button':'close'}";
				$acceptBtn = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mr-3 permission-modify" datatip="Accept" flow="down" onclick="loadform('.$acceptParam .')">Accept</a>';
			}

			$movementBtn="";
			if($pending_movement > 0){
				$title = '[Pending Qty : '.floatval($pending_accept).']';
				$movementParam = "{'postData':{'process_id' : ".$processData->process_id.",'prc_id':".$processData->prc_id."},'modal_id' : 'modalLong', 'call_function':'prcMovement', 'form_id' : 'addPrcMovement', 'title' : 'PRC Movement', 'js_store_fn' : 'storeData', 'fnsave' : 'savePRCMovement','save_controller':'sopDesk'}";
				$movementBtn = '<a href="javascript:void(0)" class="btn btn-sm btn-primary text-left  permission-modify" datatip="Movement" flow="down" onclick="loadform('.$movementParam.')">Movement</a>';
			}
			$html .= '<!--<div class=" grid_item" style="width:100%;">
								<div class="card sh-perfect"> -->
									<div class="card-body">                                    
										<div class="task-box">
											
											<h5 class="mt-0 font-14 cursor-pointer" >'.$currentProcess.'</h5>
											<p class="text-muted mb-0 font-13"><span class="fw-semibold">Next Process :</span> '.$lastMove.'</p>
											<div class="d-flex justify-content-between">  
												<h6 class="fw-semibold font-13">Unaccepted : <span class="text-muted font-weight-normal"> '.floatval($pending_accept).'</span></h6>
												<h6 class="fw-semibold font-13">Stock : <span class="text-muted font-weight-normal"> '.floatval($pending_movement).'</span></h6>                          
											</div>
											<hr class="hr-dashed mt-1 mb-2  my-5px">
											<div class="media align-items-center btn-group process-tags">
												<span class="badge bg-light-peach btn flex-fill" style="padding:5px">IN : '.floatval($in_qty).'</span>
												<span class="badge bg-light-teal btn flex-fill" style="padding:5px">OK : '.floatval($ok_qty).'</span>
												<span class="badge bg-light-cream btn flex-fill" style="padding:5px">RJ : '.floatval($rej_qty).'</span>
												<span class="badge bg-light-raspberry btn flex-fill" style="padding:5px">PQ : '.floatval($pending_production).'</span>
											</div> 
											
											<div class="d-flex justify-content-between mt-10" style="margin-top:20px">
												<div >'.$acceptBtn.'</div>
												<div>'.$logBtn.'</div>
												<div>'.$movementBtn.'</div>
											    
											</div>   
											                
										</div>
									</div>
								<!--</div>
							</div>-->';
		}
		$this->printJson(['status'=>1,'html'=>$html]);
	}

	public function getPRCLogHtml(){
		$data = $this->input->post(); 
		if($data['process_by'] != 3){$data['process_by'] = "";} $data['group_by'] = 'prc_log.id';
		$logData = $this->sop->getProcessLogList($data);
		$html="";
		if(!empty($logData)) {
			foreach($logData as $row) {
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deletePRCLog','controller':'sopDesk','res_function':'getPrcLogResponse'}";
				$deleteBtn = '<a class="btn btn-outline-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trashSop('.$deleteParam.');" datatip="Remove" flow="down"><i class="fas fa-trash-alt"></i></a>';

				$html .= '<div class=" grid_item" style="width:100%;">
									<div class="card sh-perfect">
										<div class="card-body">                                    
											<div class="task-box">
												<div class="float-end">
													'.$deleteBtn.'
												</div>                                  
												<p class="mb-0 font-13"><span class="fw-semibold">Department/Machine : </span>'.$row->processor_name.'</p>                                      
												<p class="mb-0 font-13"><span class="fw-semibold">Operator : </span>'.$row->emp_name.'</p>                                      
												<p class="mb-0 font-13"><span class="fw-semibold">Shift : </span>'.$row->shift_name.'</p>                                                                  
												<p class="mb-0 font-13"><span class="fw-semibold">Remark : </span>'.$row->remark.'</p> 

												<hr class="hr-dashed mt-1 mb-2  my-5px">
												<div class="media align-items-center btn-group process-tags">
													<span class="badge bg-light-peach btn flex-fill" style="padding:5px">Date : '.formatDate($row->trans_date).'</span>
													<span class="badge bg-light-teal btn flex-fill" style="padding:5px">Time : '.$row->production_time.'</span>
													<span class="badge bg-light-cream btn flex-fill" style="padding:5px">OK : '.floatval($row->qty).'</span>
													<span class="badge bg-light-raspberry btn flex-fill" style="padding:5px">RJ : '.floatval($row->rej_found).'</span>
												</div>                                     
											</div>
										</div>
									</div>
								</div>';
			}
		}
		$this->printJson(['status'=>1,'html'=>$html]);
	}

	public function getPRCMovementHtml(){
		$data = $this->input->post(); 
		$movementData = $this->sop->getProcessMovementList(['prc_process_id'=>$data['prc_process_id']]);
		$html="";
        if (!empty($movementData)) :
            $i = 1;
            foreach ($movementData as $row) :
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deletePRCMovement','controller':'sopDesk','res_function':'getPrcMovementResponse'}";
				$deleteBtn = '<a class="btn btn-outline-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trashSop('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
	
				$html .= '<div class=" grid_item" style="width:100%;">
									<div class="card sh-perfect">
										<div class="card-body">                                    
											<div class="task-box">
												<div class="float-end">
													'.$deleteBtn.'
												</div>                                  
												<p class="mb-0 font-13"><span class="fw-semibold">Send To : </span>'.$row->send_to_name.'</p>                                      
												<p class="mb-0 font-13"><span class="fw-semibold">Processor : </span>'.$row->processor_name.'</p>                                      
												<p class="mb-0 font-13"><span class="fw-semibold">Remark : </span>'.$row->remark.'</p> 
	
												<hr class="hr-dashed mt-1 mb-2  my-5px">
												<div class="media align-items-center btn-group process-tags">
													<span class="badge bg-light-peach btn flex-fill" style="padding:5px">Date : '.formatDate($row->trans_date).'</span>
													<span class="badge bg-light-cream btn flex-fill" style="padding:5px">Qty. : '.floatval($row->qty).'</span>
													<span class="badge bg-light-raspberry btn flex-fill" style="padding:5px">Wt/Nos : '.floatval($row->wt_nos).'</span>
												</div>                                     
											</div>
										</div>
									</div>
								</div>';
            endforeach;
        else :
            $html = '<td colspan="8" class="text-center">No Data Found.</td>';
        endif;
		$this->printJson(['status'=>1,'html'=>$html]);
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
		$this->printJson(['status'=>1,'html'=>$html,'pendingQty'=>$pending_movement]);
	}

}
?>