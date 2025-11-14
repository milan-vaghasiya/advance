<?php
class RejectionReview extends MY_Controller
{
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Rejection Review";
		$this->data['headData']->controller = "rejectionReview";
		$this->data['headData']->pageUrl = "rejectionReview";
	}
	
	public function index(){
        $this->load->view("rejection_review/index",$this->data);
    }

    public function pendingReviewIndex($source='MFG'){
        $this->data['source'] = $source;
        if($source == 'GRN'){
            $controller = 'grnPendingReview';
        }elseif($source == 'Manual'){
            $controller = 'manualPendingReview';
        }else{
            $controller = 'pendingReview';
        }
        $this->data['tableHeader'] = getProductionDtHeader($controller);
        $this->load->view("rejection_review/pending_review_index",$this->data);
    }

    public function getDTRows($source = 'MFG'){
        $data = $this->input->post();$data['source'] = $source;
        $result = $this->rejectionReview->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $row->source = $source;
            $sendData[] = getPendingReviewData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function reviewedIndex($source='MFG'){
        $this->data['source'] = $source;
        if($source == 'GRN'){
            $controller = 'grnRejectionReview';
        }elseif($source == 'Manual'){
            $controller = 'manualRejectionReview';
        }else{
            $controller = 'rejectionReview';
        }
        $this->data['tableHeader'] = getProductionDtHeader($controller);
        $this->load->view("rejection_review/review_index",$this->data); 
    }

    public function getReviewDTRows($source = 'MFG'){
        $data = $this->input->post();$data['source'] = $source;
        $result = $this->rejectionReview->getReviewDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getRejectionReviewData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function convertToOk(){
        $data = $this->input->post();
        $this->data['source'] = $data['source'];
        if(in_array($data['source'],['GRN','Manual'])){
            $this->data['dataRow'] = $this->rejectionReview->getRejectionData(['id'=>$data['id'],'source'=>$data['source']]); 
        }else{
            $this->data['dataRow'] = $this->sop->getProcessLogList(['id'=>$data['id'],'single_row'=>1]);
        }
        $this->load->view('rejection_review/cft_ok_form', $this->data);
    }

    public function convertToRej(){
        $data = $this->input->post();
        $this->data['source'] = $data['source'];
        if(in_array($data['source'],['GRN','Manual'])){
            $this->data['dataRow'] = $this->rejectionReview->getRejectionData(['id'=>$data['id'],'source'=>$data['source']]); 
        }else{
            $this->data['dataRow'] = $dataRow = $this->sop->getProcessLogList(['id'=>$data['id'],'single_row'=>1]);  

			$stageHtml = '<option value="">Select Process</option>
                          <option value="0" data-process_name="Raw Material" data-process_id="Raw Material">Raw Material</option>';
						  
			if(!empty($dataRow) && $dataRow->cutting_flow == 2){
				$stageHtml .= '<option value="3" data-process_name="RM Cutting" data-process_id="RM Cutting">RM Cutting</option>';
			}

            $prcProcessData = $this->sop->getProcessFromPRC(['process_ids'=>$dataRow->process_ids,'item_id'=>$dataRow->item_id]);
            if (!empty($dataRow->process_id)) {
                $in_process_key = array_keys(array_column($prcProcessData,'id'), $dataRow->process_id)[0];
                foreach ($prcProcessData as $key => $row) {
                    if ($key <= $in_process_key) {
                        $stageHtml .= '<option value="' . $row->id . '" data-process_name="' . $row->process_name . '" data-process_id="' . $row->id . '">' . $row->process_name . '</option>';
                    }
                }
            }
            $this->data['dataRow']->stage = $stageHtml;
        }
        $this->data['rejectionComments'] = $this->comment->getCommentList(['type'=>1]);
        $this->load->view('rejection_review/cft_rej_form', $this->data);
    }

    public function convertToRw(){
        $data = $this->input->post();
        $this->data['source'] = $data['source'];
        $this->data['dataRow'] = $dataRow = $this->sop->getProcessLogList(['id'=>$data['id'],'single_row'=>1]);
        $this->data['reworkComments'] = $this->comment->getCommentList(['type'=>3]);
        $stageHtml = '<option value="">Select Stage</option><option value="0" data-process_name="Raw Material" data-process_id="Raw Material">Raw Material</option>';
        $prcProcessData = $this->sop->getProcessFromPRC(['process_ids'=>$dataRow->process_ids,'item_id'=>$dataRow->item_id]);
        if (!empty($dataRow->process_id)) {
            $in_process_key = array_keys(array_column($prcProcessData,'id'), $dataRow->process_id)[0];
            foreach ($prcProcessData as $key => $row) {
                if ($key <= $in_process_key) {
                    $stageHtml .= '<option value="' . $row->id . '" data-process_name="' . $row->process_name . '" data-process_id="' . $row->id . '">' . $row->process_name . '</option>';
                }
            }
        }
        $this->data['dataRow']->stage = $stageHtml;

        $this->load->view('rejection_review/cft_rw_form', $this->data);
    }

    public function saveReview(){
        $data = $this->input->post(); 
        $errorMessage = array();
        $i = 1;
        if (empty($data['qty'])) :
            $errorMessage['qty'] = "Qty is required.";
        else :
            if($data['source'] == 'MFG'){
                $reviewData = $this->sop->getProcessLogList(['id'=>$data['log_id'],'rejection_review_data'=>1,'single_row'=>1]);
            }
            elseif($data['source'] == 'FIR'){
                $reviewData = $this->finalInspection->getFinalInspectData(['id'=>$data['log_id'],'rejection_review_data'=>1,'single_row'=>1]);
            }
            elseif(in_array($data['source'],['GRN','Manual'])){
                $reviewData = $this->rejectionReview->getRejectionData(['id'=>$data['log_id'],'source'=>$data['source']]);
            }
            
            if ($data['qty'] > ($reviewData->pending_qty)) {
                $errorMessage['qty'] = "Qty is Invalid.";
            }
        endif;
        if(in_array($data['decision_type'],[1,2])){
            if(!in_array($data['source'],['GRN','Manual'])){
                if($data['rr_stage'] == ''){$errorMessage['rr_stage'] = "Process is required.";}else{
                    $data['rr_type'] = (($data['rr_stage'] == 0)?'Raw Material':'Machine');
                    if($data['decision_type'] == 2){
                        $data['rw_process'] = $data['rr_stage'];
                    }
                }
                if($data['rr_by'] == 0){ 
                
                    if(empty($data['mc_op_id'])){
                        //$errorMessage['mc_op_id'] = "Operator & Machine is required.";
                    }else{
                        $opMC = explode("~",$data['mc_op_id']);
                        $data['operator_id'] = $opMC[0];                           
                        $data['machine_id'] = (!empty($opMC[1])?$opMC[1]:'');
                        unset($data['mc_op_id']);
                    }
                }
                if($data['rr_by'] == ''){$errorMessage['rr_by'] = "required.";}
                if($data['rr_by'] == ''){$errorMessage['rr_by'] = "required.";}
            }
            if(empty($data['rr_reason'])){$errorMessage['rr_reason'] = "Reason is required.";}
            if($data['decision_type'] == 3 && empty($data['rw_process'])){$errorMessage['rr_by'] = "required.";}
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->rejectionReview->saveReview($data));
        endif;
    }

    public function getRRByOptions(){
        $data = $this->input->post(); 
        $option = '<option value="">Select</option>';
        if($data['process_id'] == 0){
            $prcData = $this->sop->getPrc(['id'=>$data['prc_id']]);
            $rmData = $this->gateInward->getBatchWiseItemList(['batch_no'=>$prcData->batch_no,'partyData'=>1]);
            if (!empty($rmData)) :
                $option .= '<option value="'.(!empty($rmData->party_id)?$rmData->party_id:0).'">'.(!empty($rmData->party_name)?$rmData->party_name:'Inhouse').'</option>';
            else:
                $option .= '<option value="0">Inhouse</option>';
            endif;
        }elseif($data['process_id'] == 3){
			$option .= '<option value="0">Inhouse</option>';
		}else {
            $vendorData = $this->sop->getProcessLogList(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'group_by'=>'prc_log.process_by,prc_log.processor_id']);
            if (!empty($vendorData)) :
                foreach ($vendorData as $row) :
                    $option .= '<option value="' . (($row->process_by == 3) ? $row->processor_id : 0) . '" >' . ((($row->process_by == 3) ? $row->processor_name : 'Inhouse')) . '</option>';
                endforeach;
            else:
                $option .= '<option value="0">Inhouse</option>';
            endif;
        }

        $this->printJson(['status' => 1, 'rejOption' => $option]);
    }

	public function getOperatorOptions(){
        $data = $this->input->post(); 
		$operatorList = [];
		if(!empty($data['process_id']) && $data['process_id'] == 3){
			$prcMaterialData = $this->sop->getPrcBomData(['prc_id'=>$data['prc_id'],'production_data'=>1,'stock_data'=>1]);
			if(!empty($prcMaterialData)){
				$batch_no = implode(',', array_column($prcMaterialData, 'batch_no'));
				
				$getPrcData = $this->sop->getPRCList(['prc_type'=>2,'status'=>[2,3],'customWhere'=>'FIND_IN_SET(prc_master.prc_number,"'.$batch_no.'") > 0']);
				
				if(!empty($getPrcData)){
					$prc_ids = implode(',', array_column($getPrcData, 'id'));
					$operatorList = $this->cutting->getProcessLogList(['prc_ids'=>$prc_ids,'group_by'=>'prc_log.operator_id,prc_log.processor_id']);
				}
			}
		}else{			
			$operatorList = $this->sop->getProcessLogList(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'group_by'=>'operator_id,processor_id', 'process_by' => 1]);
		}
        $flag = 0;
        $option = '<option value="">Select</option>';
        if(!empty($operatorList)){						
            foreach($operatorList as $row){
                if(!empty($row->operator_id) || !empty($row->processor_id)){
                    $flag = 1;
                    $option .= '<option value="'.($row->operator_id.'~'.$row->processor_id).'">'.$row->emp_name.'  ['.(!empty($row->machine_code) ?  $row->machine_code.' '.$row->machine_name : $row->machine_name).']'.'</option>';
                }
              
            }
        }
        if(empty($flag)){
            $empData = $this->employee->getEmployeeList();
            foreach($empData as $row){
                $option .= '<option value="'.$row->id.'">'.$row->emp_name.' </option>';
            }
        }
        $this->printJson(['status' => 1, 'option' => $option]);
    }
	
    public function getChNoOptions(){
        $data = $this->input->post(); 
        $chList = $this->sop->getProcessLogList(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'processor_id'=>$data['rr_by'],'process_by'=>3,'group_by'=>'prc_log.in_challan_no']);
        $option = '<option value="">Select</option>';
        if($chList){						
            foreach($chList as $row){
                if(!empty($row->in_challan_no)){
                    $option .= '<option value="'.$row->in_challan_no.'">'.$row->in_challan_no.'  </option>';
                }
            }
        }

        $this->printJson(['status' => 1, 'option' => $option]);
    }
    
	public function deleteReview(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->rejectionReview->deleteReview($data));
        endif;
	}

    public function printRejTag($id) {
		$logData = $this->rejectionReview->getReviewData(['id'=>$id,'single_row'=>1]);

        //$vendorName = (!empty($logData->emp_name)) ? $logData->emp_name :  '' ;
        //$machineName = ($logData->process_by == 1)? (!empty($logData->processor_name) ? $logData->processor_name:''):'';
		
		$vendorName = (!empty($logData->rej_emp_name)) ? $logData->rej_emp_name : $logData->emp_name ;
        $machineName = (!empty($logData->rej_machine_name) ? $logData->rej_machine_name:(($logData->process_by == 1)? (!empty($logData->processor_name) ? $logData->processor_name:''):''));
		
		$mtitle = 'Rejection Tag';
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
    
        $itemList = '<table class="table tag_print_table" style="font-size:0.8rem;">
            <tr class="bg-light">
                <td><b>PRC No.</b></td>
                <td><b>Date</b></td>
                <td><b>Ok Qty</b></td>
                <td><b>Rej Qty</b></td>
            </tr>
			<tr>
				<td>' . $logData->prc_number . '</td>
				<td>' . formatDate($logData->created_at) . '</td>
                <td>' . floatval($logData->ok_qty) . '</td>
				<td>' . floatval($logData->qty) . '</td>
			</tr>
			<tr class="bg-light">
				<td><b>Part</b></td>
				<td colspan="3">' . (!empty($logData->item_code) ? '['.$logData->item_code.'] ' : '') . $logData->item_name . '</td>
			</tr>
            <tr>
				<td><b>Process</b></td>
				<td colspan="3">'  . $logData->process_name . '</td>
			</tr>
             <tr>
				<td><b>Rej Process</b></td>
				<td colspan="3">'  . ((!empty($logData->rr_process_name))?$logData->rr_process_name:'Raw Material') . '</td>
			</tr>
			<tr>
				<td><b>Rej Reason</b></td>
				<td colspan="3">' . $logData->reason . '</td>
			</tr>
			<tr>
				<td><b>Vendor/Ope.</b></td>
				<td>' . $vendorName . '</td>
				<td><b>M/c No</b></td>
				<td>' .$machineName . '</td>
			</tr>
			<tr>
				<td><b>Issue By</b></td>
				<td colspan="3">' . $logData->created_name . '</td>
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

    public function getReviewHtml(){
        $data = $this->input->post();
        $reviewData = $this->rejectionReview->getReviewData($data);
        $html = "";$i=1;
        if(!empty($reviewData)){
            foreach($reviewData as $row){
                $deleteParam = "{'postData':{'id' : ".$row->id."},'fndelete':'deleteReview','res_function':'getReviewResponse'}";
                
				$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
				
				$rejTag = ''; $mfgTd ='';
                if($row->source == 'MFG'){
                    if($row->decision_type == 1){
                        $rejTag .= '<a href="' . base_url('rejectionReview/printRejTag/' . $row->id) . '" target="_blank" class="btn btn-sm btn-dark waves-effect waves-light mr-1" title="Rejection Tag"><i class="fas fa-print"></i></a>';
                    }
                    $mfgTd ='<td>' . ((!empty($row->rr_process_name))?$row->rr_process_name:(( $row->decision_type != 5)?'Raw Material':'')) . ' </td>
                            <td>' . ((!empty($row->rr_by_name))?$row->rr_by_name:(( $row->decision_type != 5)?'Inhouse':'')) . '</td>
                            <td>' . $row->rej_emp_name . '</td>
                            <td>' . $row->rej_mc_code . '</td>
                            <td>' . $row->in_ch_no . '</td>';
                }
				
				$html .='<tr class="text-center">
								<td>' . $i++ . '</td>
								<td>' . floatval($row->qty) . '</td>								
                                <td>' . $row->decision. '</td>
								<td>' . $row->reason . ' </td>
								'.$mfgTd.'
								<td>' . $row->rr_comment . '</td>
								<td>' . $rejTag.' '.$deleteBtn . '</td>
							</tr>';
				
            }
        }else{
            $html .='<tr class="text-center"><th colspan="11">No data available</th></tr>';
        }
		$this->printJson(['status'=>1,'tbodyData'=>$html]);
    }
}
?>