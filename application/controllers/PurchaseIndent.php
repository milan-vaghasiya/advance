<?php
class PurchaseIndent extends MY_Controller
{
    private $indexPage = "purchase_indent/index";
    private $purchase_req_index = "purchase_indent/purchase_req_index";
    private $purchase_req_form = "purchase_indent/purchase_req_form";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Purchase Indent";
        $this->data['headData']->controller = "purchaseIndent";
        $this->data['headData']->pageUrl = "purchaseIndent";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'purchaseIndent/purchaseRequest','tableName'=>'purchase_indent']);
    }

    public function index($status = 1)
    {
        $this->data['status'] = $status;
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($status=1)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $data['pInd']= 1;
        $result = $this->purchaseIndent->getDTRows($data);
        $sendData = array();
        $i=($data['start']+1);
		
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            if ($row->order_status == 1) :
                $row->order_status_label = '<span class="font-10 font-weight-bold badge bg-danger">Pending</span> <br>'.$row->created_name;
            elseif ($row->order_status == 2) :
                $row->order_status_label = '<span class="font-10 font-weight-bold badge bg-success">Completed</span> <br>'.$row->created_name;
            elseif ($row->order_status == 3) :
                $row->order_status_label = '<span class="font-10 font-weight-bold badge bg-dark">Rejected</span> <br>'.$row->created_name;
            endif;
            $sendData[] = getPurchaseIndentData($row);
        endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function changeReqStatus()
    {
        $data = $this->input->post();

        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->purchaseIndent->changeReqStatus($data));
        endif;
    }

    public function purchaseRequest()
    {
		
        $this->data['headData']->pageUrl = "purchaseIndent/purchaseRequest";
        $this->data['headData']->pageTitle = "Purchase Request";
        $this->data['tableHeader'] = getPurchaseDtHeader('purchaseRequest');
        $this->load->view($this->purchase_req_index, $this->data);
    }

    public function getPurchaseReqDTRows($status=1)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->purchaseIndent->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
			
            if ($row->order_status == 1) :
                $row->order_status_label = '<span class="font-10 font-weight-bold badge bg-danger">Pending</span>';
            elseif ($row->order_status == 2) :
                $row->order_status_label = '<span class="font-10 font-weight-bold badge bg-success">Completed</span>';
            elseif ($row->order_status == 3) :
                $row->order_status_label = '<span class="font-10 font-weight-bold badge bg-dark">Rejected</span>';
            endif;

            $sendData[] = getPurchaseRequestData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPurchaseRequest()
    {
        $data = $this->input->post();
        $this->data['item_id'] = ((!empty($data['item_id']))?$data['item_id']:'');
        $this->data['fg_item_id'] = ((!empty($data['fg_item_id']))?$data['fg_item_id']:'');
        $this->data['so_trans_id'] = ((!empty($data['so_trans_id']))?$data['so_trans_id']:'');
        $this->data['qty'] = ((!empty($data['qty']))?$data['qty']:'');
        
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,4,5,6,7,8,9"]);
        $this->load->view($this->purchase_req_form, $this->data);
    }
	   
	public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['item_id']))
            $errorMessage['item_id'] = "Item Name is required.";
        if (empty($data['qty']))
            $errorMessage['qty'] = "Qty. is required.";
		
        if (empty($data['trans_date'])){ $errorMessage['trans_date'] = "Indent Date is required."; }else{
            if (($data['trans_date'] < $this->startYearDate) OR ($data['trans_date'] > $this->endYearDate)){
                $errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
            }
        }

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            if(empty($data['id'])):
                $data['trans_no'] = $this->data['entryData']->trans_no;
                $data['trans_number'] = $this->data['entryData']->trans_prefix.$data['trans_no'];
            endif;
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;
            $this->printJson($this->purchaseIndent->save($data));
        endif;
    }

    public function edit()
    {
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->purchaseIndent->getPurchaseRequest($data);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,4,5,6,7,8,9"]);
        $this->data['fgoption'] = $this->getItemWiseFgList(['fg_item_id'=>$dataRow->fg_item_id,'item_id'=>$dataRow->item_id,'item_type'=>$dataRow->item_type]);
        $this->load->view($this->purchase_req_form, $this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->purchaseIndent->delete($id));
        endif;
    }

    public function materialForecast($status=1){
		$this->data['tableHeader'] = getPurchaseDtHeader('materialForecast');
		$this->data['status'] = $status;
        $this->load->view('purchase_indent/material_forecast',$this->data);
	}

	public function getForecastDtRows($status=1){
		$data = $this->input->post(); $data['status'] = $status;
		$result = $this->purchaseIndent->getForecastDtRows($data);
		$sendData = array();$i=($data['start']+1);
		foreach($result['data'] as $row):          
			$row->sr_no = $i++;         
			$sendData[] = getForecastData($row);
		endforeach;
		$result['data'] = $sendData;
		$this->printJson($result);
	}
  
	/*USE FOR PI,PO,GRN,INTERNAL GRN, BATCH DETAILS FOR INTERNAL GRN */
	public function getItemWiseFgList($param = []){ 
		$data = (!empty($param)) ? $param : $this->input->post();
		
        if(!empty($data['item_type']) && $data['item_type'] == 3){
            $itemList = $this->item->getProductKitData(['ref_item_id'=>$data['item_id']]);
            $fgoption = '<option value="">Select Finish Goods</option>';
            foreach($itemList as $row){
                $selected = (!empty($data['fg_item_id']) && $data['fg_item_id'] == $row->item_id)?'selected':'';
    
                $itemName = (!empty($row->product_code)) ? "[ ".$row->product_code." ] ".$row->product_name : $row->product_name;
                $fgoption .= '<option value="'.$row->item_id.'" '.$selected.'>'.$itemName.'</option>';
            }
        }else{
            $itemList = $this->item->getItemList(['item_type'=>1]);
            $fgoption = '<option value="">Select Finish Goods</option>';
            foreach($itemList as $row){
                $selected = (!empty($data['fg_item_id']) && $data['fg_item_id'] == $row->id)?'selected':'';
    
                $itemName = (!empty($row->item_code)) ? "[ ".$row->item_code." ] ".$row->item_name : $row->item_name;
                $fgoption .= '<option value="'.$row->id.'" '.$selected.'>'.$itemName.'</option>';
            }
        }
      
		$batchoption = '<option value="">Select Batch</option>';
		if(!empty($data['batch_details'])){
			$batchList = $this->itemStock->getItemStockBatchWise(['item_id'=>$data['item_id'],'group_by'=>'item_id,batch_no','stock_required'=>1]);
			
			foreach($batchList as $row){
				$batchoption .= '<option value="'.$row->batch_no.'" >'.$row->batch_no .' (Stock Qty : '.$row->qty.')</option>';
			}
		}

        if(!empty($param)):
			return $fgoption;
		else:
        	$this->printJson(['fgoption'=>$fgoption,'batchNo'=>$batchoption]);
		endif;
    }
  
    /** Purchase Planning */

    public function purchaseForecast(){
		$this->data['headData']->pageTitle = "Purchase Forecast";

        
        $this->load->view('purchase_indent/purchase_planning',$this->data);
    }

    public function getPurchasePlanDTRows(){
        $data = $this->input->post();
         $tbodyData = '';
        /** Finish Good Wise Req And Stock */
        $fgList = $this->purchaseIndent->getFGReqRows();
        if(!empty($fgList)){
            $fgArray = array_reduce($fgList, function($fgArray, $item) { $fgArray[$item->item_id] = $item; return $fgArray; }, []);
            /** extract all finish good item to find rm */
            $fg_ids = array_column($fgList,'item_id');
            /** PRC generate thy gyu hoy material issue krvanu baki hoy te req. find krva mate  */
            $prcRm = $this->purchaseIndent->getPendingPrcRm(['item_id'=>$fg_ids]);
            $prcRmArray=[];
            if(!empty($prcRm)){
                $prcRmArray = array_reduce($prcRm, function($prcRmArray, $item) { $prcRmArray[$item->ref_item_id][$item->item_id] = $item; return $prcRmArray; }, []);
            }
            /** Item Kit Data  */
            $kitData = $this->item->getProductKitData(['item_id'=>$fg_ids,'is_main'=>1]);
            $kitArray = [];$leadTimeArray =[];
            if(!empty($kitData)){
                $kitArray = array_reduce($kitData, function($kitArray, $item) { $kitArray[$item->ref_item_id][$item->item_id] = $item; return $kitArray; }, []);
                /** Find RM Stock */
                $rmIds = array_column($kitData,'ref_item_id');
                $kitStock = $this->purchaseIndent->getRMStock(['item_id'=> $rmIds]);
                $stockArray = array_reduce($kitStock, function($stockArray, $item) { $stockArray[$item->item_id] = $item; return $stockArray; }, []);
  
            }
            /** Loop Rm */
            $rmPlanMap = [];
            if(!empty($kitArray)){
                foreach($kitArray As $key=>$value){
                    $leadTime = 
                    $rmPlanMap[$key] = [
                        'item_id'=>$stockArray[$key]->item_id,
                        'item_code'=>$stockArray[$key]->item_code.' '.$stockArray[$key]->item_name,
                        'stock_qty'=>$stockArray[$key]->material_stock,
                        'pending_po'=>$stockArray[$key]->pending_po,
                        'pending_grn'=>$stockArray[$key]->pending_grn,
                        'total_rm_req'=>0,
                        'total_prc_req'=>0,
                    ];
                    foreach($value AS $row){
                        $fgData = $fgArray[$row->item_id];
                        

                        $totalFinalReq = $fgData->order_qty - $fgData->dispatch_qty;
                        
                        $totalShortageQty  = $totalFinalReq - ( $fgData->wip_qty +$fgData->prd_finish_Stock+$fgData->rtd_Stock);

                        $rmPlanMap[$key]['total_rm_req'] += $totalShortageQty * $row->qty;
                        $rmPlanMap[$key]['total_prc_req'] += (!empty($prcRmArray[$key][$row->item_id]->req_qty))?$prcRmArray[$key][$row->item_id]->req_qty:0;
                    }
                }
                $i=1;
                foreach($rmPlanMap as $row){ 
                    $row = (object)$row;
                    $totalReq =  $row->total_rm_req + $row->total_prc_req;
                    $shortage = $totalReq - ($row->stock_qty + $row->pending_grn +$row->pending_po );
                    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$i.'" class="filled-in chk-col-success BulkRequest" value="'.$row->item_id.'"  data-qty="'.$shortage.'" data-fg_item_id=""><label for="ref_id_'.$i.'"></label>';
                    
                    $altParam = "{'postData':{'item_id' : ".$row->item_id."},'modal_id' : 'bs-right-md-modal', 'form_id' : 'alternateMaterial', 'title' : 'Alternate Material','call_function':'alternateMaterial','button':'close'}";
                    $altBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Alternate Material" flow="down" onclick="modalAction('.$altParam.');"><i class="far fa-clone"></i></a>';
                    $action = getActionButton($altBtn);
                    $tbodyData.='<tr>
                                    <td>'.$action.'</td>  
                                    <td>'.$selectBox.'</td>
                                    <td>'.$i++.'</td>
                                    <td>'.$row->item_code.'</td>
                                    <td>'.$totalReq.'</td>
                                    <td>'.$row->stock_qty.'</td>
                                    <td>'.floatval($row->pending_grn).'</td>
                                    <td>'.floatval($row->pending_po).'</td>
                                    <td>'.$shortage.'</td>
                                </tr>';
                }
            }
        }
        $this->printJson(['tbodyData'=>$tbodyData]);
    }

}
