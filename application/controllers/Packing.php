<?php
class Packing extends MY_Controller{

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Packing";
		$this->data['headData']->controller = "packing";
	}

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->load->view('packing/index',$this->data);
    }

    public function getDTRows($status=1){
        $data = $this->input->post();$data['status'] = $status;
		$result = $this->packing->getDTRows($data);
        $sendData = array();$i=$data['start']+1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPackingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function packingStock(){
        $this->data['tableHeader'] = getSalesDtHeader("packing_stock");
        $this->load->view('packing/packing_stock_index',$this->data);
    }

    public function getPackingStockDTRows(){
        $data = $this->input->post();
		$data['item_type'] = 1;
		$result = $this->packing->getPackingStockDTRows($data);
        $sendData = array();$i=$data['start']+1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPackingStockData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPacking(){
        $this->data['trans_no'] = $this->packing->getNetxNo();
        $this->data['trans_number'] = "DP-".n2m(date('m'))."-".$this->data['trans_no'];
        $this->data['productData'] = $this->item->getItemList(['item_type'=>1]);
		//$this->data['packingMaterial'] =  $this->item->getItemList(['item_type'=>9]);
        $this->load->view('packing/form',$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array(); 

        if(empty($data['item_id'])){ $errorMessage['item_id'] = "Product Name is required.";}
        if(empty($data['trans_date'])){ $errorMessage['trans_date'] = "Packing Date is required.";}
        if(empty($data['qty_per_box'])){ $errorMessage['qty_per_box'] = "Qty Per Box is required.";}
        if(empty($data['total_box'])){ $errorMessage['total_box'] = "Total Box is required.";}
        if(empty($data['total_box_qty'])){ $errorMessage['total_box_qty'] = "Total Qty is required.";}

        if(!empty($data['total_qty']) && !empty($data['total_box_qty'])){
            if($data['total_qty'] != $data['total_box_qty']){
                $errorMessage['batchDetails'] = "Packing Qty and Total Qty (Nos) is mismatch.";
            }
        }
        $i=1;
        if(!empty($data['id'])):
            $oldItem = $this->packing->getPackingData(['id'=>$data['id']]);
            $oldBatchArray = explode(",",$oldItem->batch_no);
            $oldQtyArray = explode(",",$oldItem->batch_qty);
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
            $this->printJson($this->packing->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $dataRow = $this->packing->getPackingData(['id'=>$id]);
        $batchDetail = $this->packing->getBatchDetail(['main_ref_id'=>$id,'item_id'=>$dataRow->item_id,'p_or_m'=>-1,'trans_type'=>'PCK']);
        // print_r($this->db->last_query());
		$this->data['dataRow']->batchDetail = json_encode($batchDetail);
        $this->data['productData'] = $this->item->getItemList(['item_type'=>1]);
		$this->data['packingMaterial'] =  $this->getPackingMaterial(['item_id'=>$dataRow->item_id, 'box_item_id'=>$dataRow->box_item_id]);
        $this->load->view('packing/form',$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->packing->delete($id));
		endif;
	}

    public function packedBoxSticker($id){
        
        $packData = $this->packing->getPackingData(['id'=>$id]);
        $companyData = $this->masterModel->getCompanyInfo();
        $logo = base_url('assets/images/logo.png');
        $boxData='';
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100,50]]); // Landscap
        $pdfFileName ='pack' . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));

        $qrText = $packData->trans_number;
        $file_name = $packData->id;
        $qrIMG = base_url().$this->getQRCode($qrText,'assets/uploads/packing_qr/',$file_name);
        $boxData = '<div style="text-align:center;padding:1mm 1mm;"> <!--bottom:0px;position:absolute;rotate:-90;-->
					<table class="table item-list-pck" >
						<tr>
							<td class="text-center"><img src='.$logo.' height="35" width="100" /></td>
							<th class="text-center" style="font-size:15px" colspan="2">'.$companyData->company_name.'</th>
							<td class="text-center" style="padding:2px;" rowspan="3" ><img src="'.$qrIMG.'" style="height:32mm;"></td>
						</tr>
						<tr>
							<td class="text-left" style="font-size:11px;" height="38" colspan="3"> <b>'.$packData->item_name.'</b></td>
						</tr>
						<tr>
							<td  class="text-center" >
								Qty/Box <br> <b  style="font-size:20px;">'.floatval($packData->qty_per_box).'</b>
							</td>
							<td  class="text-center">
								Total Box <br> <b  style="font-size:20px;">'.floatVal($packData->total_box).'</b>
							</td>
                            <td  class="text-center">
								Total Qty <br> <b  style="font-size:20px;">'.floatVal($packData->total_qty).'</b>
							</td>
						</tr>
						<tr>
							<td class="text-center" colspan="3">Pack No./Date: <b>'.$packData->trans_number.' / '.date("d-m-Y",strtotime($packData->trans_date)).'</b> <br> Batch No: <b>'.$packData->batch_no.'</b></td>
							<th class="text-center" style="font-size:18px;padding:1px;">'.$packData->item_code.'</th>
						</tr>
					</table></div>';
        $mpdf->AddPage('P', '', '', '', '', 0, 0, 1, 1, 1, 1);
        $mpdf->WriteHTML($boxData);
        $mpdf->Output($pdfFileName, 'I');
    }

	public function getPackingMaterial($param=[]){
        $data = (!empty($param) ? $param : $this->input->post());
        $packData = $this->item->getProductKitData(['item_id'=>$data['item_id'], 'item_type'=>9]);
        $options = (empty($param) ? '<option value="">Loose Packing</option>' : '');
        if(!empty($packData)){
            foreach($packData as $row){
                $selected = ((!empty($data['box_item_id']) && $data['box_item_id'] == $row->ref_item_id) ? 'selected' : '');
                $options .= '<option data-qty="'.floatval($row->qty).'" data-pack_wt="'.floatval($row->pack_wt).'" value="'.$row->ref_item_id.'" '.$selected.'>'.((!empty($row->item_code) ? '['.$row->item_code.']' : '').$row->item_name).'</option>';
            }
        }
        if(!empty($param)){
            return $options;
        }else{
            $this->printJson(['options'=>$options]);

        }
    }

    public function getBatchWiseItemStockForPack(){
        $data = $this->input->post();
		
		$data['location_ids'] = (!empty($data['location_ids']))?$data['location_ids']:[$this->RTD_STORE->id];
		$readOnly = (!empty($data['qty_readonly']))?$data['qty_readonly']:'';
		
		$data['batchDetail'] = (!empty($data['batchDetail']))?json_decode($data['batchDetail'],true):[];

		$postData = ["item_id" => $data['item_id'], 'location_ids'=> $data['location_ids'], 'stock_required'=>1, 'group_by'=>'location_id,batch_no'];		
		if(!empty($data['batchDetail']) && !empty($data['id'])):			
			$batch_no = array_column($data['batchDetail'],'batch_no');
			$batch_no = "'".implode("', '",$batch_no)."'";
			$postData['customHaving'] = "(SUM(stock_trans.qty * stock_trans.p_or_m) > 0 OR (stock_trans.batch_no IN (".$batch_no.") ))";
		endif;

		$batchData = $this->itemStock->getItemStockBatchWise($postData);
		$batchDetail = [];
		if(!empty($data['batchDetail'])):			
			$batchDetail = array_reduce($data['batchDetail'],function($item,$row){
				$item[$row['remark']] = $row['batch_qty'];
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
                            <td>['.$row->store_name.'] '.$row->location.'</td>
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

        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
    }
}
?>