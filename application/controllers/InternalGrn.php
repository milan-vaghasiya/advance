<?php
class InternalGrn extends MY_Controller{
    private $indexPage = "internal_grn/index";
    private $form = "internal_grn/form";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Internal GRN";
		$this->data['headData']->controller = "internalGrn";
        $this->data['headData']->pageUrl = "internalGrn";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'internalGrn']);
    }

    public function index(){
        $this->data['tableHeader'] = getStoreDtHeader("internalGrn");
		$this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 1){
        $data = $this->input->post();
        $data['trans_status'] = $status;

        $result = $this->internalGrn->getDTRows($data);
        $sendData = array();$i=($data['start']+1);

        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getInternalGrnData($row);
        endforeach;

        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addInternalGrn(){
        $data = $this->input->post();
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"3"]);
        
        $this->data['trans_no'] = $this->gateInward->getNextGrnNo();
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix; //'GI/'.getYearPrefix('SHORT_YEAR').'/';
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();
        
        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item Name is required.";
        if(empty($data['fg_item_id']))
            $errorMessage['fg_item_id'] = "Finish Goods  is required.";
        if(empty($data['batch_no']))
            $errorMessage['batch_no'] = "Batch No is required.";
		if(empty($data['to_item']))
            $errorMessage['to_item'] = "To Item is required.";

        if(isset($data['batch_qty'])){
            if(empty(array_sum($data['batch_qty']))){
                $errorMessage['table_err'] = "Qty is required.";
            }

            $sData = $data['batch_qty']; 
            for ($i=0; $i < count($sData); $i++) {
                $stockData = $this->itemStock->getItemStockBatchWise(['location_id'=>$data['location_id'][$i],'batch_no'=>$data['batch_no'],'item_id'=>$data['item_id'],'single_row'=>1]);
                $stock_qty = (!empty($stockData)) ? $stockData->qty : 0;
                if($data['batch_qty'][$i] > $stock_qty){
                    $errorMessage['batch_qty_'.$i] = "Stock not available.";
                }
            }
        } else {
            $errorMessage['table_err'] = "Batch Details is required.";
        }
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])):
				$data['trans_no'] = $this->gateInward->getNextGrnNo();
				$data['trans_prefix'] = $this->data['entryData']->trans_prefix;
				$data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
            endif;
            $this->printJson($this->internalGrn->save($data));
        endif;
    }

    public function delete(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->internalGrn->delete($data));
        endif;
    }

    public function getBatchWiseStock() {
        $data = $this->input->post(); 
		
        $batchData = $this->itemStock->getItemStockBatchWise(["item_id"=> $data['item_id'],"batch_no" => $data['batch_no'],'stock_required'=>1,'group_by'=>'location_id,batch_no','supplier'=>1]);
        $tbodyData='';$i=0;
        if (!empty($batchData)) {
            foreach ($batchData as $row) {

                $tbodyData .= '<tr>';
                $tbodyData .= '<td>'.$row->location.'</td>';
                $tbodyData .= '<td>'.$row->batch_no.'</td>';
                $tbodyData .= '<td>'.floatVal($row->qty).'</td>';
                $tbodyData .= '<td>
						<input type="text" name="batch_qty[]" class="form-control batchQty floatOnly" min="0" value="" />
						<div class="error batch_qty_' . $i . '"></div>
						<input type="hidden" name="heat_no" id="heat_no_' . $i . '" value="' . (!empty($row->heat_no) ? $row->heat_no : '') . '" />
						<input type="hidden" name="location_id[]" id="location_' . $i . '" value="' . $row->location_id . '" />
						<input type="hidden" name="party_id" id="party_id_' . $i . '" value="' . $row->party_id . '" />
					</td>
				</tr>';
                $i++;
            }

        } else {
            $tbodyData .= "<td colspan='4' class='text-center'>No Data</td>";
        }
        $this->printJson(['status' => 1, 'tbodyData' => $tbodyData]);
    }
   
}
?>