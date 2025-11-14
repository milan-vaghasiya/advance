<?php
class Cutting extends MY_Controller{

	public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Cutting";
		$this->data['headData']->controller = "cutting";
	}

    public function index(){
        $this->data['headData']->pageTitle = "Cutting";
        $this->data['headData']->pageUrl = "cutting";
        $this->data['tableHeader'] = getProductionDtHeader('cutting');
        $this->load->view('cutting/index',$this->data);
    }

    public function getDTRows($status = 1){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->cutting->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getCuttingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCuttingPRC(){
        $this->data['prc_prefix'] = 'CUT/'.$this->shortYear.'/';
        $this->data['prc_no'] = $this->sop->getNextPRCNo(2);
        $this->data['customerData'] = $this->salesOrder->getPendingOrderItems(['group_by'=>'party_id']);
        $this->load->view('cutting/form',$this->data);
    }

    public function editCutting(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->sop->getPRC(['id'=>$data['id']]);        
        $this->data['customerData'] = $this->salesOrder->getPendingOrderItems(['group_by'=>'party_id']);
        $this->data['productData'] = $this->getProductList(['party_id'=>$dataRow->party_id,'item_id'=>$dataRow->item_id]);
        $cut_weight = !empty($dataRow->cut_weight) ? explode('-',$dataRow->cut_weight) : array();
		$this->data['min_cut_weight'] = (isset($cut_weight[0]) ? $cut_weight[0] : 0);
		$this->data['max_cut_weight'] = (isset($cut_weight[1]) ? $cut_weight[1] : 0);
        $this->load->view('cutting/form',$this->data);
    }

    public function saveCutting(){
        $data = $this->input->post(); 
        $errorMessage = array();
        if ($data['party_id'] == ""){ $errorMessage['party_id'] = "Customer is required."; }
        if (empty($data['item_id'])){ $errorMessage['item_id'] = "Product is required.";}
        if (empty($data['qty']) || $data['qty'] < 0){ $errorMessage['qty'] = "Quantity is required.";}
        if(empty($data['min_cut_weight']) && empty($data['max_cut_weight'])){
			$errorMessage['minCutWeight'] = "Cut Weight is required.";
		}else{			
			if(!empty($data['min_cut_weight']) && $data['min_cut_weight'] > $data['max_cut_weight']){
				$errorMessage['minCutWeight'] = "Invalid cut weight.";
			}
		}
        $cut_weight = ((!empty($data['min_cut_weight']) && !empty($data['max_cut_weight'])) ? number_format($data['min_cut_weight'],3).'-'.number_format($data['max_cut_weight'],3) : '0.000-0.000');
        if (empty($data['prc_date'])){ $errorMessage['prc_date'] = "PRC Date is required.";}else{
			if (formatDate($data['prc_date'], 'Y-m-d') < $this->startYearDate OR formatDate($data['prc_date'], 'Y-m-d') > $this->endYearDate){
				$errorMessage['prc_date'] = "Invalid Date (Out of Financial Year).";
			}
		}
    
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $masterData = [
                'id'=>$data['id'],
                'prc_type'=>2,
                'prc_no'=>$data['prc_no'],
                'prc_number'=>$data['prc_number'],
                'prc_date'=>$data['prc_date'],
                'item_id'=>$data['item_id'],	
                'party_id'=>$data['party_id'],
                'so_trans_id'=>$data['so_trans_id'],
                'prc_qty'=>$data['qty'],
                'target_date'=>$data['target_date']
            ];
            $prcDetail = [
                'remark'=>$data['remark'],
                'id'=>$data['prc_detail_id'],
                'cutting_length'=>$data['cutting_length'],
                'cutting_dia'=>$data['cutting_dia'],
                'cut_weight'=>$cut_weight,
                'cutting_type'=>$data['cutting_type'],
                'process_ids'=>3
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
            $this->printJson($this->cutting->saveCutting($sendData));
        endif;
    }

    public function delete(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->cutting->delete($data));
        endif;
    }

    public function getProductList($param = []){
		$data = (!empty($param)) ? $param : $this->input->post();
		
		$result = array();
		if(!empty($data['party_id'])){
			$result = $this->salesOrder->getPendingOrderItems(['party_id'=>$data['party_id'],'is_approve'=>1,'prc_type'=>2,'trans_status'=>0]);
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

    public function requiredMaterial(){
		$data = $this->input->post();
        $cut_weight = !empty($data['cut_weight']) ? explode('-',$data['cut_weight']) : array();
		$this->data['prc_id'] = $data['id'];
		$this->data['prc_qty'] = $data['prc_qty'];
		$this->data['fg_item_id'] = $data['item_id'];
		$this->data['cut_weight'] = (!empty($cut_weight[1]) ? $cut_weight[1] : (!empty($cut_weight[0])?$cut_weight[0]:''));
		$this->data['kitData'] = $this->item->getProductKitData(['item_id'=>$data['item_id'],'process_id'=>3]);
		$this->data['prcBom'] = $this->cutting->getCuttingBomData(['prc_id'=>$data['id'],'single_row'=>1,'production_data'=>1]);
		$this->load->view('cutting/cutting_material',$this->data);
	}

	public function savePrcMaterial(){
		$data = $this->input->post();
        $errorMessage = array();

		if (empty($data['item_id'])) { 
			$errorMessage['general_error'] = "Item is required."; 
		}
        $batchCount = 0;
		if (!empty($data['batch_no'])) {
            if(empty(array_sum($data['batch_qty']))){$errorMessage['table_err'] = "Batch Details is required.";}
            $data['bom_batch'] = '';
            if(!empty($data['id'])){
                $oldBom = $this->cutting->getCuttingBomData(['id'=>$data['id'],'prc_id'=>$data['prc_id'],'single_row'=>1]);
                if($oldBom->item_id == $data['item_id']){
                    $data['bom_batch'] = $oldBom->batch_no;
                    $batchCount++;
                }
            }  
            foreach($data['batch_no'] AS $key=>$batch_no){
                if($data['batch_qty'][$key] > 0){
                    $stockData = $this->itemStock->getItemStockBatchWise(["item_id" => $data['item_id'],'stock_required'=>1,'group_by'=>'location_id,batch_no','supplier'=>1,'location_id'=>$data['location_id'][$key],'batch_no'=>$data['batch_no'][$key],'single_row'=>1]);
                    $stock_qty = (!empty($stockData)) ? $stockData->qty : 0;
                    if($data['batch_qty'][$key] > $stock_qty){
                        $errorMessage['batch_qty_'.$key] = "Stock not available.";
                    }
                    if($data['bom_batch'] != $batch_no){
                        $batchCount++;
                        $data['bom_batch'] = $batch_no;
                    }
                }else{
                    unset($data['batch_qty'][$key],$data['batch_no'][$key],$data['location_id'][$key],$data['heat_no'][$key]);
                }
            }
        } else {
            $errorMessage['table_err'] = "Batch Details is required.";
        }

        if($batchCount > 1){
            $errorMessage['table_err'] = "Multiple batches are not allowed";
        }

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->cutting->saveMaterial($data));
        endif;
		
	}

    public function startCuttingPRC(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $prcBom = $this->sop->getPrcBomData(['prc_id'=>$data['id']]);
            if(empty($prcBom)){
                $this->printJson(['status'=>0,'message'=>'Set Required Material for production']);
            }else{
                $this->printJson($this->cutting->startCuttingPRC($data));
            }
            
        endif;
    }

    public function addCuttingLog(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->sop->getPrc(['id'=>$data['id']]);
        $this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
        $this->data['shiftData'] = $this->shiftModel->getShiftList();
        $this->data['operatorList'] = $this->employee->getEmployeeList();
		
		$cut_weight = !empty($dataRow->cut_weight) ? explode('-',$dataRow->cut_weight) : array();
		$this->data['max_cut_weight'] = (!empty($cut_weight[1]) ? $cut_weight[1] : (!empty($cut_weight[0])?$cut_weight[0]:''));
		
        $this->load->view('cutting/cutting_log_form',$this->data);

    }

    public function getCuttingLogHtml(){
        $data = $this->input->post();
        $logData = $this->cutting->getProcessLogList(['prc_id'=>$data['prc_id']]);
        $html = "";
        if(!empty($logData)){
            $i = 1;
            foreach($logData as $row){
                $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deleteCuttingLog','res_function':'getCuttingResponse','controller':'cutting'}";
                $deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
                $html.= '<tr>
                            <td>' . $i++ . '</td>
                            <td>' . formatDate($row->trans_date). '</td>
                            <td>' . floatval($row->qty) . '</td>
                            <td>' . floatval($row->wt_nos) . '</td>
                            <td>' . $row->processor_name . '</td>
                            <td>' . $row->shift_name . '</td>
                            <td>' . $row->emp_name . '</td>
                            <td>' . $row->remark . '</td>
                            <td>' . $deleteBtn . '</td>
                        </tr>';
            }
        }else{
            $html.= '<tr><th class="text-center" colspan="9">No data available</th></tr>';
        }
        $logData = $this->cutting->getCuttingPrcData(['id'=>$data['prc_id'],'production_data'=>1,'single_row'=>1]);
        $this->printJson(['status'=>1,'tbodyData'=>$html,'production_qty'=>$logData->production_qty]);
    }

    public function saveCuttingLog(){
        $data = $this->input->post(); 
        $errorMessage = array();
        if (empty($data['prc_id'])){ $errorMessage['prc_id'] = "Job Card No. is required.";}
        if (empty($data['wt_nos']) OR $data['wt_nos']<=0){ $errorMessage['wt_nos'] = "Weight Per Nos is required.";}
        
        if (empty($data['trans_date']) or $data['trans_date'] == null or $data['trans_date'] == ""){ 
            $errorMessage['trans_date'] = "Date is required."; 
        }else{
			if (($data['trans_date'] < $this->startYearDate) OR ($data['trans_date'] > $this->endYearDate)){
				$errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
			}
		}
		
		if (empty($data['qty'])){
            $errorMessage['qty'] = "Qty. is required.";
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :

            $data['logDetail'] = [
                'id'=>"",
                'remark'=>$data['remark'],
            ];
            unset($data['remark']);
            $this->printJson($this->cutting->saveCuttingLog($data));
        endif;	
    }

    public function deleteCuttingLog(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->cutting->deleteCuttingLog($data));
        endif;
    }

    public function getMaterialDetail(){
        $postData = $this->input->post();
        $this->data['mtData'] = $this->cutting->getCuttingBomData(['prc_id'=>$postData['id'],'single_row'=>1,'stock_data'=>1]);
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList(['id'=>(!empty($this->data['mtData']->location_id)?$this->data['mtData']->location_id:'')]);
        $this->load->view('cutting/material_return',$this->data);
    }

    public function getReturnHtml(){
		$data = $this->input->post();
		$customWhere = '  trans_type="PMR"';
		$batchData = $batchData = $this->itemStock->getItemStockBatchWise(['child_ref_id'=>$data['prc_id'],'customWhere'=>$customWhere,'group_by'=>'stock_trans.id']);
		$html = "";$i=1;
		if(!empty($batchData)){
			foreach($batchData as $row){
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deleteReturn','res_function':'getReturnResponse'}";
				$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
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
		} 
        $endPcsData = $this->endPiece->endPcsReturnData(['prc_id'=>$data['prc_id']]);
        if(!empty($endPcsData)){
			foreach($endPcsData as $row){
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deleteEndPcs','res_function':'getReturnResponse'}";
				$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';
				$html .= '<tr>
							<td>'.$i++.'</td>
							<td>'.formatDate($row->trans_date).'</td>
							<td>End Piece Return</td>
							<td>'.$row->batch_no.'</td>
							<td>'.floatval($row->qty).'</td>
							<td>'.$row->remark.'</td>
							<td>'.$deleteBtn.'</td>
						</tr>';
			}
        }
        if(empty($html)) {
			$html = '<td colspan="7" class="text-center">No Data Found.</td>';
		}
        $stockData = $this->cutting->getCuttingBomData(['prc_id'=>$data['prc_id'],'single_row'=>1,'stock_data'=>1,'production_data'=>1]);
		$stockQty = $stockData->issue_qty - (( $stockData->cutting_cons) + $stockData->return_qty);
		
		$this->printJson(['status'=>1,'tbodyData'=>$html,'issue_qty'=>round($stockData->issue_qty,3),'used_qty'=>round($stockData->cutting_cons,3),'return_qty'=>round($stockData->return_qty,3),'stock_qty'=>round($stockQty,3)]);
	}

    public function storeReturnedMaterial(){
		$data = $this->input->post();
		if(empty($data['item_id'])){ $errorMessage['general_error'] = "Item is required."; }
		if(empty($data['location_id'])){ $errorMessage['location_id'] = "Location is required."; }
		if(empty($data['batch_no'])){ $errorMessage['batch_no'] = "Batch No is required."; }
		if(empty($data['qty'])){ $errorMessage['qty'] = "Qty is required."; }
		else{
            $stockData = $this->cutting->getCuttingBomData(['prc_id'=>$data['prc_id'],'single_row'=>1,'stock_data'=>1,'production_data'=>1]);
            $stockQty = $stockData->issue_qty - (( $stockData->cutting_cons) + $stockData->return_qty);
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
            $this->printJson($this->cutting->storeReturnedMaterial($data));
        endif;
		
	}

    public function deleteReturn(){
		$id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->cutting->deleteReturn($id));
        endif;
	}

    public function deleteEndPcs(){
		$id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->cutting->deleteEndPcs($id));
        endif;
	}

    public function cuttingPrint($id){
        $prcData = $this->data['prcData'] = $this->cutting->getCuttingPrcData(['id'=>$id,'production_data'=>1,'single_row'=>1]);
        $this->data['mtrData'] = $this->cutting->getCuttingBomData(['prc_id'=>$id,'stock_data'=>1,'single_row'=>1]);
        //$this->data['logData'] = $this->cutting->getProcessLogList(['prc_id'=>$id]);
        
        $pdfData = $this->load->view('cutting/print_view',$this->data,true);
        $printedBy = $this->employee->getEmployee(['id'=>$this->loginId]);
        $this->data['letter_head']=base_url('assets/images/letterhead_top.png');
        $htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
        $htmlFooter = '
            <table class="table top-table" style="margin-top:10px;border-top:1px solid #000000;">
                <tr>
                    <td style="width:50%;">
                        Printed at : '.$printedBy->emp_name.' ('.formatDate(date('Y-m-d H:s:i'), 'd-m-Y H:s:i').')
                    </td>
                    <td style="width:50%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';
        $mpdf = new \Mpdf\Mpdf();

        $pdfFileName = 'CUT-' . $id . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->useSubstitutions = false;
        $mpdf->simpleTables = true;

        $mpdf->AddPage('P', '', '', '', '', 5, 5, 38, 20, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }

    public function changePRCStage(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            if(in_array($data['status'],[3,5])){
                $stockData = $this->cutting->getCuttingBomData(['prc_id'=>$data['id'],'single_row'=>1,'stock_data'=>1,'production_data'=>1]);
                //$stockQty = $stockData->issue_qty - (( $stockData->cutting_cons) + $stockData->return_qty);
				
				$iq = round($stockData->issue_qty,3);
				$cc = round($stockData->cutting_cons,3);
				$rq = round($stockData->return_qty,3);
				
				$stockQty = round(($iq - ($cc + $rq)),3);
				
                if($stockQty > 0){ 
                    $this->printJson(['status'=>0,'message'=>'You have material stock, you have to return it then you can change the stage '.$stockQty]); 
                }
            }
            $this->printJson($this->cutting->changePRCStage($data));
        endif;
    }
}
?>