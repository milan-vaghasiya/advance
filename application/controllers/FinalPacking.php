<?php

class FinalPacking extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Final Packing";
		$this->data['headData']->controller = "finalPacking";
    }

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader("finalPacking");
        $this->load->view('final_packing/index',$this->data);
    }

    public function getDTRows($status = 1){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->finalPacking->getDTRows($data);

        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
			$row->tab_status = $status;
            $sendData[] = getFinalPackingData($row);
        endforeach;

        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPacking(){
        $this->data['trans_no'] = $this->finalPacking->getNetxNo();
        $this->data['trans_number'] = "FP/".getYearPrefix('SHORT_YEAR').'/'.$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['packMtList'] = $this->item->getItemList(['item_type'=>9]);
        $this->data['transportList'] = $this->transport->getTransportList();
        $this->load->view('final_packing/form',$this->data);
    }

    public function getPendingOrders($param = []){
        $data = (!empty($param))?$param :$this->input->post();
        $customHaving = "";
        if(!empty($param)){ $customHaving = '(so_trans.qty - so_trans.dispatch_qty) > 0 OR so_trans.id IN('.implode(",",$data['so_trans_ids']).')'; }
        else{ $customHaving = '(so_trans.qty - so_trans.dispatch_qty) > 0'; }
        
        $soData = $this->salesOrder->getPendingOrderItems(['party_id'=>$data['party_id'],'customHaving'=>$customHaving]);
        $options = '<option value="">Select Item</option>';
        if(!empty($soData)){
            foreach($soData AS $row){
                if($row->is_packing != 0){
                    $value = (!empty($row->item_code)? "[".$row->item_code."] " : "").$row->item_name;
			        $value .= (!empty($row->trans_number))?' ('.$row->trans_number.' | Pend. Qty: ' .floatval($row->pending_qty).' | Del.Date: ' . formatDate($row->cod_date).')':'';

                    $options .= '<option value="'.$row->id.'" data-item_id = "'.$row->item_id.'" data-item_name="'.$row->item_name.'"  data-packing_type="'.$row->is_packing.'">'.$value.'</option>';
                }
               
            }
        }
        if(!empty($param)){
            return $options;
        }else{
            $this->printJson(['status'=>1,'options'=>$options]);
        }
        
    }

	/*
    public function getPackedBoxData(){
        $data = $this->input->post(); 
        $itemData = $this->item->getItem(['id'=>$data['item_id']]);
        $tbodyData = "";$theadData="";$tfootData="";
        if($itemData->is_packing == 1){
            //IF Packing Type Primary + Final
            $tbodyData = "";$i=1;
            $postData = ['item_id'=>$data['item_id']];
            $data['batchDetail'] = (!empty($data['batchDetail']))?json_decode($data['batchDetail'],true):[];
            //IF record Edit
            if(!empty($data['batchDetail'])):		
                $primary_pack_id = array_column($data['batchDetail'],'primary_pack_id');
                $primary_pack_id = "'".implode("', '",$primary_pack_id)."'";
                $postData['customHaving'] = "(primary_packing.total_box - dispatch_qty) > 0 OR (primary_packing.id IN (".$primary_pack_id."))";
            else:
                $postData['customHaving'] = "(primary_packing.total_box - dispatch_qty) > 0";
            endif;
            if(!empty($data['batchDetail'])):			
                $batchDetail = array_reduce($data['batchDetail'],function($item,$row){ $item[$row['primary_pack_id']] = $row['box_qty'];   return $item;   },[]);
            endif;
            $stockData = $this->primaryPacking->getBoxStockData($postData);
            if(!empty($stockData)){
                foreach($stockData AS $row){
                    $qty = (!empty($batchDetail[$row->id])?($batchDetail[$row->id] * $row->qty_per_box ):'');
                    $row->box_stock =  $row->box_stock + ((!empty($batchDetail[$row->id]))?$batchDetail[$row->id]:0);
                    $total_qty = $row->box_stock * $row->qty_per_box;
                    $batchId = trim(preg_replace('/[^A-Za-z0-9]/', '', $row->batch_no)).$row->id.$row->item_id;
                    $box_qty = (!empty($batchDetail[$row->id])?$batchDetail[$row->id]:'');
                   
                    $tbodyData .= '<tr>
                                        <td>'.$row->trans_number.'</td>
                                        <td>'.$row->batch_no.'</td>
                                        <td>'.$row->box_stock.'<br>('.$row->box_stock.' X '.$row->qty_per_box.')</td>
                                        <td>
                                            <input type="text" id="box_qty_'.$i.'" name="batchDetail['.$i.'][box_qty]" class="form-control numericOnly calculateBoxQty" data-srno="'.$i.'" value="'.$box_qty.'">
                                            <input type="hidden" name="batchDetail['.$i.'][batch_qty]" id="batch_qty_'.$i.'" class="calculateBatchQty" value="'.$qty.'">
                                            <input type="hidden" name="batchDetail['.$i.'][primary_pack_id]" id="primary_pack_id'.$i.'" class="" value="'.$row->id.'">
                                            <input type="hidden" name="batchDetail['.$i.'][qty_per_box]" id="qty_per_box_'.$i.'" value="'.floatval($row->qty_per_box).'">
                                            <input type="hidden" name="batchDetail['.$i.'][batch_no]" id="batch_no_'.$i.'" value="'.$row->batch_no.'">
                                            <input type="hidden" name="batchDetail['.$i.'][batch_stock]" id="batch_stock_'.$i.'" value="'.floatVal($total_qty).'">
                                            <input type="hidden" name="batchDetail['.$i.'][remark]" id="batch_id_'.$i.'" value="'.$batchId.'">
                                            <div class="error batch_qty_'.$i.'"></div>
                                        </td>
                                  </tr>';
                                  $i++;
                }
            }
            $theadData ='<tr>
                            <th>Box No</th>
                            <th>Batch No.</th>
                            <th>Stock (Box Qty)</th>
                            <th>Box Qty.</th>
                        </tr>';
            $tfootData ='<tr>
                            <th colspan="3" class="text-right">Total Box</th>
                            <th>
                                <span class="total_box">0</span>
                                <input type="hidden" id="total_box"  class="itemFormInput">
                                <input type="hidden" id="total_qty"  class="itemFormInput">
                            </th>
                        </tr>';
        }
        elseif($itemData->is_packing == 2){
            //IF Packing Type Only Final
            $data['batchDetail'] = (!empty($data['batchDetail']))?json_decode($data['batchDetail'],true):[];
            $postData = ["item_id" => $data['item_id'], 'location_ids'=> $this->PACKING_STORE->id, 'stock_required'=>1, 'group_by'=>'location_id,batch_no'];
            //IF Record Edit		
            if(!empty($data['batchDetail']) && !empty($data['id'])):			
                $batch_no = array_column($data['batchDetail'],'batch_no');
                $batch_no = "'".implode("', '",$batch_no)."'";
                $postData['customHaving'] = "(SUM(stock_trans.qty * stock_trans.p_or_m) > 0 OR (stock_trans.batch_no IN (".$batch_no.") ))";
            endif;

            $batchData = $this->itemStock->getItemStockBatchWise($postData);
            $batchDetail = [];
            if(!empty($data['batchDetail'])):	 //GENERATE BATCH KEY		
                $batchDetail = array_reduce($data['batchDetail'],function($item,$row){
                    $batchId = trim(preg_replace('/[^A-Za-z0-9]/', '', $row['batch_no']));
                    $item[$batchId] = $row['batch_qty'];
                    return $item;
                },[]);
            endif;
            $i=1;
            if(!empty($batchData)):
                foreach($batchData as $row):
                    $batchId = trim(preg_replace('/[^A-Za-z0-9]/', '', $row->batch_no));
                    $location_name = '['.$row->store_name.'] '.$row->location;
                    $qty = (isset($batchDetail[$batchId]))?$batchDetail[$batchId]:0;
                    if(!empty($data['id'])): $row->qty = $row->qty + $qty; endif;
                  
                    $tbodyData .= '<tr id="'.$batchId.'" data-ind="'.$i.'">
                                <td>'.$row->batch_no.'</td>
                                <td> '.floatval($row->qty).'  </td>
                                <td>
                                    <input type="text" name="batchDetail['.$i.'][batch_qty]" id="batch_qty_'.$i.'" class="calculateBatchQty form-control checkRow'.$batchId.' batchNoIp batchQtyIp"  value="'.$qty.'" data-srno="'.$i.'">
                                    <input type="hidden" class="checkRow'.$batchId.' batchNoIp" name="batchDetail['.$i.'][location_id]" id="location_id_'.$i.'" value="'.$row->location_id.'">
                                    <input type="hidden" class="checkRow'.$batchId.' batchNoIp" name="batchDetail['.$i.'][batch_no]" id="batch_no_'.$i.'" value="'.$row->batch_no.'">
                                    <input type="hidden" class="checkRow'.$batchId.' batchNoIp" name="batchDetail['.$i.'][remark]" id="batch_id_'.$i.'" value="'.$batchId.'">
                                    <input type="hidden" class="checkRow'.$batchId.' batchNoIp" name="batchDetail['.$i.'][batch_stock]" id="batch_stock_'.$i.'" value="'.floatVal($row->qty).'">
                                    <div class="error batch_qty_'.$i.'"></div>
                                </td>
                            </tr>';               
                    $i++;
                endforeach;
            endif;

            if(empty($tbodyData)):
                $tbodyData = '<tr> <td colspan="3" class="text-center">No data available in table</td> </tr>';
            endif;

            $theadData ='<tr>
                            <th>Batch No</th>
                            <th>Stock Qty</th>
                            <th>Packing Qty</th>
                        </tr>';
            $tfootData ='<tr>
                            <th colspan="2" class="text-right">Total Box</th>
                            <th>
                                <input type="text" id="total_qty"  class="itemFormInput form-control" readonly>
                                <input type="hidden" id="total_box"  class="itemFormInput">
                            </th>
                        </tr>';
        }
        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData,'theadData'=>$theadData,'tfootData'=>$tfootData]);
    }
	*/
    
	public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['party_id'])):
            $errorMessage['party_id'] = "Party Name is required.";
        endif;
        if(empty($data['itemData'])):
            $errorMessage['itemData'] = "Item Details is required.";
        
            /* 
			else:
            $bQty = array();$oldBatchQty = array();
            foreach($data['itemData'] as $key => $row):
                $batchDetail = $row['batch_detail'];
                $batchDetail = json_decode($batchDetail,true);
                $i=1;
                if($row['packing_type'] == 1):
                    //IF Packing Type is primary + Final
                    if(!empty($row['id'])):
                        $oldItem = $this->finalPacking->getPackingItemDetail(['id'=>$row['id'],'batchDetail'=>1,'single_row'=>1]);

                        $oldBatchDetail = json_decode($oldItem->batch_detail);
                        $oldBatchQty = array_reduce($oldBatchDetail, function($oldBatchDetail, $batch) { 
                            $oldBatchDetail[$batch->primary_pack_id]= $batch->box_qty; 
                            return $oldBatchDetail; 
                        }, []);                      
                    endif;
                    $batchQty = (!empty($batchDetail))?array_sum(array_column($batchDetail,'batch_qty')):0;
                    if(floatval($row['total_qty']) <> floatval($batchQty)):
                        $errorMessage['qty'.$key] = "Invalid Batch Qty.".$batchQty;
                    else:
                        foreach($batchDetail as $batch):
                            if(!empty($batch['batch_qty']) && $batch['batch_qty'] > 0):
                                $postData = [
                                    'id' => $batch['primary_pack_id'],
                                    'single_row' => 1
                                ];                        
                                $stockData = $this->primaryPacking->getBoxStockData($postData);  
                                
                                $batchKey = $batch['primary_pack_id'];
                                
                                $stockQty = $stockData->box_stock * $stockData->qty_per_box;
                                if(!empty($row['id'])):

                                    $stockQty = $stockQty + (isset($oldBatchQty[$batch['primary_pack_id']])?($oldBatchQty[$batch['primary_pack_id']] * $stockData->qty_per_box):0);
                                    // print_r($stockQty);
                                endif;
                                if(!isset($bQty[$batchKey])):
                                    $bQty[$batchKey] = $batch['batch_qty'] ;
                                else:
                                    $bQty[$batchKey] += $batch['batch_qty'];
                                endif;
                                
                                if(empty($stockQty)):
                                    $errorMessage['qty'.$key] = "Stock not available.";
                                else:
                                    if($bQty[$batchKey] > $stockQty):
                                        $errorMessage['qty'.$key] = "Stock not available.".$bQty[$batchKey] .'>'. $stockQty;
                                    endif;
                                endif;
                            endif;
                            $i++;
                        endforeach;
                    endif;
                else:
                  
                    if(!empty($row['id'])):
                        $oldItem = $this->finalPacking->getFinalPackingBoxDetail(['packing_id'=>$data['id'],'pack_trans_id'=>$row['id']]);
                        $oldBatchArray =array_column($oldItem,'batch_no');
                        $oldQtyArray = array_column($oldItem,'qty');
                    endif;
                    if(!empty($batchDetail)){
                        foreach($batchDetail as $batch):
                            $postData = [
                                'location_id' =>  $this->PACKING_STORE->id,
                                'batch_no' => $batch['batch_no'], 
                                'item_id' => $row['item_id'],
                                'stock_required' => 1,
                                'single_row' => 1
                            ];                        
                            $stockData = $this->itemStock->getItemStockBatchWise($postData);  
                            $batchKey = "";
                            $batchKey = trim(preg_replace('/[^A-Za-z0-9]/', '', $batch['batch_no'])).$row['item_id'];;
                            
                            $stockQty = (!empty($stockData->qty))?floatVal($stockData->qty):0;
                            if(!empty($data['id'])):     
                                $old_qty = $oldQtyArray[array_search($batch['batch_no'], $oldBatchArray)];
                                $stockQty = $stockQty + $old_qty;
                            endif;
                            // print_r($batchKey);
                            // print_r($bQty);
                            if(!isset($bQty[$batchKey])):
                                $bQty[$batchKey] = $batch['batch_qty'] ;
                                // print_r("If");
                            else:
                                $bQty[$batchKey] += $batch['batch_qty'];
                                // print_r("Else");
                            endif;

                            if(empty($stockQty)):
                                $errorMessage['qty'.$key] = "Stock not available.";
                            else:
                                if($bQty[$batchKey] > $stockQty):
                                    $errorMessage['qty'.$key] = "Stock not available.".$bQty[$batchKey] .'>'. $stockQty;
                                endif;
                            endif;
                            $i++;
                        endforeach;;
                    }
                endif;
            endforeach; 
        */
        endif;
		
		if(empty($data['trans_date'])){ 
            $errorMessage['trans_date'] = "Date is required.";
        }else{
            if (($data['trans_date'] < $this->startYearDate) OR ($data['trans_date'] > $this->endYearDate)){
                $errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
            }
        }
	
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            unset($data['packingDetail']);
            $this->printJson($this->finalPacking->save($data));
        endif;
    }
	
    public function edit($id){
        $dataRow = $this->finalPacking->getPackingData(['id'=>$id,'single_row'=>1]); 
        $itemData = $this->finalPacking->getPackingItemDetail(['packing_id'=>$id,'batchDetail'=>1]);
        $dataRow->itemData = $itemData;
        $this->data['dataRow'] = $dataRow;
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemOptions'] = $this->getPendingOrders(['party_id'=>$dataRow->party_id,'so_trans_ids'=>array_column($itemData,'so_trans_id')]);
        $this->data['transportList'] = $this->transport->getTransportList();
        $this->data['packMtList'] = $this->item->getItemList(['item_type'=>9]);
        $this->load->view('final_packing/form',$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->finalPacking->delete($id));
		endif;
	}

    public function finalPackingPrint($packingId){
        $dataRow = $this->finalPacking->getPackingData(['id'=>$packingId,'single_row'=>1]); 
        $itemData = $this->finalPacking->getPackingItemDetail(['packing_id'=>$packingId,'order_by_package'=>1]);
        $this->data['batchData'] = $this->finalPacking->getFinalPackingBoxDetail(['packing_id'=>$packingId,'primaryPackBatch'=>1]);
        $dataRow->itemData = $itemData;
        $this->data['dataRow'] = $dataRow;
        $this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
        $logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
       
        
        if (!empty($this->data['batchData'])) {
            /* Packing Comes From Primary Packing */
            $pdfData = $this->load->view('final_packing/print', $this->data, true);  
        } else {
            /* Direct Final Packing */
            $pdfData = $this->load->view('final_packing/final_packing_print', $this->data, true);  
        }  
       
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
            <tr>
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
		$mpdf->AddPage('L','','','','',5,5,5,30,5,5,'','','','','','','','','','A4-L');
		
		
        $mpdf->WriteHTML($pdfData);
		
		ob_clean();
		$mpdf->Output($pdfFileName, 'I');
    }

	public function bookQty(){
        $data = $this->input->post();
        $this->data['trans_number'] = $data['trans_number'];
        $this->data['dataRow'] = $dataRow = $this->finalPacking->getPackingItemDetail(['id'=>$data['id'], 'single_row'=>1]);
        $itemData = $this->item->getItem(['id'=>$dataRow->item_id]);
        $this->data['packing_type'] = $itemData->is_packing;
        $tbodyData = "";$theadData="";$tfootData="";
        if($itemData->is_packing == 1){
            //IF Packing Type Primary + Final
            $tbodyData = "";$i=1;
            $postData = ['item_id'=>$dataRow->item_id];
            $postData['customHaving'] = "(primary_packing.total_box - dispatch_qty) > 0";
            
            $stockData = $this->primaryPacking->getBoxStockData($postData);
            if(!empty($stockData)){
                foreach($stockData AS $row){
                    $qty = (!empty($batchDetail[$row->id])?($batchDetail[$row->id] * $row->qty_per_box ):'');
                    $row->box_stock =  $row->box_stock + ((!empty($batchDetail[$row->id]))?$batchDetail[$row->id]:0);
                    $total_qty = $row->box_stock * $row->qty_per_box;
                    $batchId = trim(preg_replace('/[^A-Za-z0-9]/', '', $row->batch_no)).$row->id.$row->item_id;
                    $box_qty = (!empty($batchDetail[$row->id])?$batchDetail[$row->id]:'');
                   
                    $tbodyData .= '<tr>
                                        <td>'.$row->trans_number.'</td>
                                        <td>'.$row->batch_no.'</td>
                                        <td>'.$row->box_stock.'<br>('.$row->box_stock.' X '.$row->qty_per_box.')</td>
                                        <td>'.floatval($dataRow->total_qty).'</td>
                                        <td>
                                            <input type="text" id="box_qty_'.$i.'" name="batchDetail['.$i.'][box_qty]" class="form-control numericOnly calculateBoxQty" data-srno="'.$i.'" value="'.$box_qty.'">
                                            <input type="hidden" name="batchDetail['.$i.'][batch_qty]" id="batch_qty_'.$i.'" class="calculateBatchQty" value="'.$qty.'">
                                            <input type="hidden" name="batchDetail['.$i.'][primary_pack_id]" id="primary_pack_id'.$i.'" class="" value="'.$row->id.'">
                                            <input type="hidden" name="batchDetail['.$i.'][qty_per_box]" id="qty_per_box_'.$i.'" value="'.floatval($row->qty_per_box).'">
                                            <input type="hidden" name="batchDetail['.$i.'][batch_no]" id="batch_no_'.$i.'" value="'.$row->batch_no.'">
                                            <input type="hidden" name="batchDetail['.$i.'][batch_stock]" id="batch_stock_'.$i.'" value="'.floatVal($total_qty).'">
                                            <input type="hidden" name="batchDetail['.$i.'][remark]" id="batch_id_'.$i.'" value="'.$batchId.'">
                                            <div class="error batch_qty_'.$i.'"></div>
                                        </td>
                                  </tr>';
                                  $i++;
                }
            }
            $theadData ='<tr>
                            <th>Box No</th>
                            <th>Batch No.</th>
                            <th>Stock (Box Qty)</th>
                            <th>Total Qty</th>
                            <th>Box Qty.</th>
                        </tr>';
            $tfootData ='<tr>
                            <th colspan="3" class="text-right">Total Qty</th>
                            <th>
                                <span class="total_qty">0</span>
                                <input type="hidden" id="total_qty" class="itemFormInput">
                            </th>
                            <th class="text-left">
                                Total Box : 
                                <span class="total_box">0</span>
                                <input type="hidden" id="total_box" class="itemFormInput">
                            </th>
                        </tr>';
        }
        elseif($itemData->is_packing == 2){
            //IF Packing Type Only Final
            $postData = ["item_id" => $dataRow->item_id, 'location_ids'=> $this->PACKING_STORE->id, 'stock_required'=>1, 'group_by'=>'location_id,batch_no'];
            $batchData = $this->itemStock->getItemStockBatchWise($postData);
            
            $batchDetail = []; $i=1;
            if(!empty($batchData)):
                foreach($batchData as $row):
                    $batchId = trim(preg_replace('/[^A-Za-z0-9]/', '', $row->batch_no));
                    $location_name = '['.$row->store_name.'] '.$row->location;
                    $qty = (isset($batchDetail[$batchId]))?$batchDetail[$batchId]:0;
                    if(!empty($dataRow->id)): $row->qty = $row->qty + $qty; endif;
                  
                    $tbodyData .= '<tr id="'.$batchId.'" data-ind="'.$i.'">
                                <td>'.$row->batch_no.'</td>
                                <td>'.floatval($row->qty).'</td>
                                <td>'.floatval($dataRow->total_qty).'</td>
                                <td>
                                    <input type="text" name="batchDetail['.$i.'][batch_qty]" id="batch_qty_'.$i.'" class="calculateBatchQty form-control numericOnly checkRow'.$batchId.' batchNoIp batchQtyIp"  value="'.$qty.'" data-srno="'.$i.'">
                                    <input type="hidden" class="checkRow'.$batchId.' batchNoIp" name="batchDetail['.$i.'][location_id]" id="location_id_'.$i.'" value="'.$row->location_id.'">
                                    <input type="hidden" class="checkRow'.$batchId.' batchNoIp" name="batchDetail['.$i.'][batch_no]" id="batch_no_'.$i.'" value="'.$row->batch_no.'">
                                    <input type="hidden" class="checkRow'.$batchId.' batchNoIp" name="batchDetail['.$i.'][remark]" id="batch_id_'.$i.'" value="'.$batchId.'">
                                    <input type="hidden" class="checkRow'.$batchId.' batchNoIp" name="batchDetail['.$i.'][batch_stock]" id="batch_stock_'.$i.'" value="'.floatVal($row->qty).'">
                                    <div class="error batch_qty_'.$i.'"></div>
                                </td>
                            </tr>';               
                    $i++;
                endforeach;
            endif;

            if(empty($tbodyData)):
                $tbodyData = '<tr> <td colspan="4" class="text-center">No data available in table</td> </tr>';
            endif;

            $theadData ='<tr>
                            <th>Batch No</th>
                            <th>Stock Qty</th>
                            <th>Total Qty</th>
                            <th>Packing Qty</th>
                        </tr>';
            $tfootData ='<tr>
                            <th colspan="3" class="text-right">Total Qty</th>
                            <th>
                                <input type="text" id="total_qty" value="0" class="itemFormInput form-control" readonly>
                                <input type="hidden" id="total_box" class="itemFormInput">
                            </th>
                        </tr>';
        }

        $this->data['theadData'] = $theadData;
        $this->data['tbodyData'] = $tbodyData;
        $this->data['tfootData'] = $tfootData;
        $this->load->view('final_packing/book_qty',$this->data);
    }
	
	public function saveBookQty(){
        $data = $this->input->post();
		$errorMessage = array();		

        if(empty($data['item_id'])){
			$errorMessage['item_id'] = "Item Name is required.";
        }else{
            $bQty = array(); $oldBatchQty = array();
            $batchDetail = $data['batchDetail'];
            $i=1;
            if($data['packing_type'] == 1):
                //IF Packing Type is primary + Final
                if(!empty($data['id'])):
                    $oldItem = $this->finalPacking->getPackingItemDetail(['id'=>$data['id'],'batchDetail'=>1,'single_row'=>1]);

                    $oldBatchDetail = json_decode($oldItem->batch_detail);
                    $oldBatchQty = array_reduce($oldBatchDetail, function($oldBatchDetail, $batch) { 
                        $oldBatchDetail[$batch->primary_pack_id]= $batch->box_qty; 
                        return $oldBatchDetail; 
                    }, []);                      
                endif;
                if(!empty($batchDetail)):
                    $batchQty = (!empty($batchDetail))?array_sum(array_column($batchDetail,'batch_qty')):0;
                    if(floatval($data['total_qty']) <> floatval($batchQty)):
                        $errorMessage['batch_qty_'.$i] = "Invalid Batch Qty.".$batchQty;
                    else:
                        foreach($batchDetail as $batch):
                            if(!empty($batch['batch_qty']) && $batch['batch_qty'] > 0):
                                $postData = [
                                    'id' => $batch['primary_pack_id'],
                                    'single_row' => 1
                                ];                        
                                $stockData = $this->primaryPacking->getBoxStockData($postData);  
                                
                                $batchKey = $batch['primary_pack_id'];
                                
                                $stockQty = $stockData->box_stock * $stockData->qty_per_box;
                                if(!empty($data['id'])):

                                    $stockQty = $stockQty + (isset($oldBatchQty[$batch['primary_pack_id']])?($oldBatchQty[$batch['primary_pack_id']] * $stockData->qty_per_box):0);
                                endif;
                                if(!isset($bQty[$batchKey])):
                                    $bQty[$batchKey] = $batch['batch_qty'] ;
                                else:
                                    $bQty[$batchKey] += $batch['batch_qty'];
                                endif;
                                
                                if(empty($stockQty)):
                                    $errorMessage['qty'.$key] = "Stock not available.";
                                else:
                                    if($bQty[$batchKey] > $stockQty):
                                        $errorMessage['qty'.$key] = "Stock not available.".$bQty[$batchKey] .'>'. $stockQty;
                                    endif;
                                endif;
                            endif;
                            $i++;
                        endforeach;
                    endif;
                endif;
            else:                
                if(!empty($data['id'])):
                    $oldItem = $this->finalPacking->getFinalPackingBoxDetail(['packing_id'=>$data['packing_id'],'pack_trans_id'=>$data['id']]);
                    $oldBatchArray =array_column($oldItem,'batch_no');
                    $oldQtyArray = array_column($oldItem,'qty');
                endif;
                if(!empty($batchDetail)):
                    $batchQty = (!empty($batchDetail))?array_sum(array_column($batchDetail,'batch_qty')):0;
                    if(floatval($data['total_qty']) <> floatval($batchQty)):
                        $errorMessage['batch_qty_'.$i] = "Invalid Batch Qty.".$batchQty;
                    else:
                        foreach($batchDetail as $batch):
                            $postData = [
                                'location_id' =>  $this->PACKING_STORE->id,
                                'batch_no' => $batch['batch_no'], 
                                'item_id' => $data['item_id'],
                                'stock_required' => 1,
                                'single_row' => 1
                            ];                        
                            $stockData = $this->itemStock->getItemStockBatchWise($postData);  
                            $batchKey = "";
                            $batchKey = trim(preg_replace('/[^A-Za-z0-9]/', '', $batch['batch_no'])).$data['item_id'];;
                            
                            $stockQty = (!empty($stockData->qty))?floatVal($stockData->qty):0;
                            // if(!empty($data['packing_id'])):     
                            //     $old_qty = $oldQtyArray[array_search($batch['batch_no'], $oldBatchArray)];
                            //     $stockQty = $stockQty + $old_qty;
                            // endif;
                            if(!isset($bQty[$batchKey])):
                                $bQty[$batchKey] = $batch['batch_qty'] ;
                            else:
                                $bQty[$batchKey] += $batch['batch_qty'];
                            endif;

                            if(empty($stockQty)):
                                $errorMessage['qty'.$key] = "Stock not available.";
                            else:
                                if($bQty[$batchKey] > $stockQty):
                                    $errorMessage['qty'.$key] = "Stock not available.".$bQty[$batchKey] .'>'. $stockQty;
                                endif;
                            endif;
                            $i++;
                        endforeach;
                    endif;
                endif;
            endif;
        }
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->finalPacking->saveBookQty($data));
        endif;
    }
}
?>