<?php
class PrimaryPacking extends MY_Controller{

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Primary Packing";
		$this->data['headData']->controller = "primaryPacking";
	}

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->load->view('primary_packing/index',$this->data);
    }

    public function getDTRows($status=1){
        $data = $this->input->post();$data['status'] = $status;
		$result = $this->primaryPacking->getDTRows($data);
        $sendData = array();$i=$data['start']+1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = gePrimaryPackingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPacking(){
        $this->data['trans_no'] = $this->primaryPacking->getNetxNo();
        $this->data['trans_number'] = "PP/".getYearPrefix('SHORT_YEAR').'/'.$this->data['trans_no'];
        $this->data['productData'] = $this->item->getItemList(['item_type'=>1,'is_packing'=>1]);
        $this->load->view('primary_packing/form',$this->data);
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $dataRow = $this->primaryPacking->getPackingData(['id'=>$id]);
        $batchDetail = $this->primaryPacking->getBatchDetail(['packing_id'=>$id]);
		$this->data['dataRow']->batchDetail = json_encode($batchDetail);
        $this->data['productData'] = $this->item->getItemList(['item_type'=>1,'is_packing'=>1]);
        $this->load->view('primary_packing/form',$this->data);
    }

    public function getBatchWiseItemStockForPack(){
        $data = $this->input->post();
		
		$data['location_ids'] = (!empty($data['location_ids']))?$data['location_ids']:[$this->RTD_STORE->id];
		$readOnly = (!empty($data['qty_readonly']))?$data['qty_readonly']:'';
		
		$data['batchDetail'] = (!empty($data['batchDetail']))?json_decode($data['batchDetail'],true):[];

		$postData = ["item_id" => $data['item_id'], 'location_ids'=> $this->PACKING_STORE->id, 'stock_required'=>1, 'group_by'=>'location_id,batch_no'];		
		if(!empty($data['batchDetail']) && !empty($data['id'])):			
			$batch_no = array_column($data['batchDetail'],'batch_no');
			$batch_no = "'".implode("', '",$batch_no)."'";
			$postData['customHaving'] = "(SUM(stock_trans.qty * stock_trans.p_or_m) > 0 OR (stock_trans.batch_no IN (".$batch_no.") ))";
		endif;

		$batchData = $this->itemStock->getItemStockBatchWise($postData);
		$batchDetail = [];
		if(!empty($data['batchDetail'])):			
			$batchDetail = array_reduce($data['batchDetail'],function($item,$row){
                $batchId = trim(preg_replace('/[^A-Za-z0-9]/', '', $row['batch_no'])).$this->PACKING_STORE->id.$row['item_id'];
				$item[$batchId] = $row['qty'];
				return $item;
			},[]);
		endif;

        $tbody = '';$i=1;
        if(!empty($batchData)):
            foreach($batchData as $row):
				$batchId = trim(preg_replace('/[^A-Za-z0-9]/', '', $row->batch_no)).$row->location_id.$row->item_id;
                
                $location_name = '['.$row->store_name.'] '.$row->location;

				$qty = (isset($batchDetail[$batchId]))?$batchDetail[$batchId]:0;
				
				if(!empty($data['id'])): $row->qty = $row->qty + $qty; endif;
                $checked = (!empty($qty) && $qty > 0)?'checked':'';
                $disabled = (!empty($qty) && $qty > 0)?'':'disabled';
				$tbody .= '<tr id="'.$batchId.'" data-ind="'.$i.'">
                            <td>
                                <input type="checkbox" id="md_'.$batchId.'"  class="filled-in chk-col-success batchNoCheck" data-rowid="'.$batchId.'" value="'.$batchId.'" '.$checked.'  ><label for="md_'.$batchId.'" class="mr-3"></label>
                            </td>
                            <td>'.$row->batch_no.'</td>
                            <td>
                                '.floatval($row->qty).'
                            </td>
                            <td>
                                <input type="text" name="batchDetail['.$i.'][batch_qty]" id="batch_qty_'.$i.'" class="calculateBatchQty form-control checkRow'.$batchId.' batchNoIp batchQtyIp"  value="'.$qty.'" '.$readOnly.' '.$disabled.'>
                                <input type="hidden" class="checkRow'.$batchId.' batchNoIp" name="batchDetail['.$i.'][location_id]" id="location_id_'.$i.'" value="'.$row->location_id.'" '.$disabled.'>
                                <input type="hidden" class="checkRow'.$batchId.' batchNoIp" name="batchDetail['.$i.'][batch_no]" id="batch_no_'.$i.'" value="'.$row->batch_no.'" '.$disabled.'>
                                <input type="hidden" class="checkRow'.$batchId.' batchNoIp" name="batchDetail['.$i.'][remark]" id="batch_id_'.$i.'" value="'.$batchId.'" '.$disabled.'>
                                <input type="hidden" class="checkRow'.$batchId.' batchNoIp" name="batchDetail['.$i.'][batch_stock]" id="batch_stock_'.$i.'" value="'.floatVal($row->qty).'" '.$disabled.'>
                                <div class="error batch_qty_'.$i.'"></div>
                            </td>
                        </tr>';               
				$i++;
            endforeach;
        endif;

		if(empty($tbody)):
            $tbody = '<tr>
                <td colspan="5" class="text-center">No data available in table</td>
            </tr>';
        endif;
        $standardData = $this->item->getProductKitData(['item_id'=>$data['item_id'], 'item_type'=>9,'packing_type'=>1]);
        $standardList = array_reduce($standardData , function($standardList, $item) { $standardList[$item->group_name][] = $item; return $standardList; }, []);
        $packStandardTbody = "";
        if(!empty($standardList)){
            foreach($standardList AS $group_name=>$item){
                $firstRow = true;
                foreach($item AS $key=>$row){
                    $packStandardTbody .= '<tr>';
                    if( $firstRow == true){
                        $checked = (!empty($data['pack_standard']) && $data['pack_standard'] == $row->group_name)?'checked':'';
                        $packStandardTbody .= '<td rowspan="'.count($item).'">
                                                     <input type="checkbox" id="std_md_'.$row->id.'"  class="filled-in chk-col-success standardCheck" data-rowid="'.$row->id.'" value="'.$row->group_name.'" name="pack_standard"  '.$checked.'><label for="std_md_'.$row->id.'" class="mr-3"></label>
                                                </td>
                                               <td rowspan="'.count($item).'">'.$row->group_name.'</td>';
                        $firstRow = false;
                    }
                    $packStandardTbody .= ' <td>'.(!empty($row->item_code) ? '['.$row->item_code.']' : '').$row->item_name.'</td>
                                            <td class="text-center">'.floatval($row->qty).'</td>
                                            <td class="text-center">'.$row->pack_wt.'</td>
                                          </tr>';
                }
            }
        }
        $this->printJson(['status'=>1,'tbodyData'=>$tbody,'packStandardTbody'=>$packStandardTbody]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array(); 

        if(empty($data['item_id'])){ $errorMessage['item_id'] = "Product Name is required.";}
        if(empty($data['trans_date'])){ 
            $errorMessage['trans_date'] = "Packing Date is required.";
        }else{
            if (($data['trans_date'] < $this->startYearDate) OR ($data['trans_date'] > $this->endYearDate)){
                $errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
            }
        }
        if(empty($data['qty_per_box'])){ $errorMessage['qty_per_box'] = "Qty Per Box is required.";}
        if(empty($data['total_box'])){ $errorMessage['total_box'] = "Total Box is required."; }
        if(empty($data['total_box_qty'])){ $errorMessage['total_box_qty'] = "Total Qty is required.";}
        if(empty($data['pack_standard'])){ $errorMessage['packStdDetail'] = "Packing standard required";}
        if(empty($data['batchDetail'])){ $errorMessage['batchDetails'] = "Batch Details is required.";}

        if(!empty($data['total_qty']) && !empty($data['total_box_qty'])){
            if($data['total_qty'] != $data['total_box_qty']){
                $errorMessage['batchDetails'] = "Packing Qty and Total Qty (Nos) is mismatch.";
            }
        }
        $i=1;
        if(!empty($data['id'])):
            $oldItem = $this->primaryPacking->getBatchDetail(['packing_id'=>$data['id']]);
            $oldBatchArray =array_column($oldItem,'batch_no');
            $oldQtyArray = array_column($oldItem,'qty');
        endif;
        if(!empty($data['batchDetail'])){
			foreach($data['batchDetail'] as $key=>$batch):
                $postData = [
                    'location_id' => $batch['location_id'],
                    'batch_no' => $batch['batch_no'], 
                    'item_id' => $data['item_id'],
                    'stock_required' => 1,
                    'single_row' => 1
                ];                        
                $stockData = $this->itemStock->getItemStockBatchWise($postData);  
                $batchKey = "";
                $batchKey = $batch['remark'];
                
                $stockQty = (!empty($stockData->qty))?floatVal($stockData->qty):0;
                if(!empty($data['id'])):     
                    $old_qty = $oldQtyArray[array_search($batch['batch_no'], $oldBatchArray)];
                    $stockQty = $stockQty + $old_qty;
                endif;
                
                if(!isset($bQty[$batchKey])):
                    $bQty[$batchKey] = $batch['batch_qty'] ;
                else:
                    $bQty[$batchKey] += $batch['batch_qty'];
                endif;

                if(empty($stockQty)):
                    $errorMessage['batch_qty_'.$key] = "Stock not available.";
                else:
                    if($bQty[$batchKey] > $stockQty):
                        $errorMessage['batch_qty_'.$key] = "Stock not available.".$bQty[$batchKey] .'>'. $stockQty;
                    endif;
                endif;
            endforeach;;
		}

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->primaryPacking->save($data));
        endif;
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->primaryPacking->delete($id));
		endif;
	}

}
?>