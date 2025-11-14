<?php
class EndPiece extends MY_Controller
{
    public function __construct(){
		parent::__construct(); 
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "End Piece";
		$this->data['headData']->controller = "endPiece";
	}

    public function index() {
		$this->data['headData']->pageTitle = "End Piece";
        $this->data['tableHeader'] = getStoreDtHeader('endPiece');
        $this->load->view('end_piece/index', $this->data);
    }

    public function getDTRows($status = 1){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->endPiece->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getEndPieceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addStock(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->endPiece->endPcsReturnData(['id'=>$data['id'],'single_row'=>1]);
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList(['id'=>(!empty($this->data['dataRow']->location_id)?$this->data['dataRow']->location_id:'')]);
        $this->load->view('end_piece/form', $this->data);
    }

    public function saveStock(){
        $data = $this->input->post();
		if(empty($data['item_id'])){ $errorMessage['general_error'] = "Item is required."; }
		if(empty($data['location_id'])){ $errorMessage['location_id'] = "Location is required."; }
		if(empty($data['batch_no'])){ $errorMessage['batch_no'] = "Batch No is required."; }
		if(empty($data['qty'])){ $errorMessage['qty'] = "Qty is required."; }
		else{
            $stockData = $this->endPiece->endPcsReturnData(['id'=>$data['return_id'],'single_row'=>1,'stock_data'=>1]);
            $stockQty = $stockData->qty - $stockData->review_qty;
			if($data['qty'] > round($stockQty,3)){ $errorMessage['qty'] = "Qty is invalid."; }
		}

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->endPiece->saveStock($data));
        endif;
    }

    public function getEndPcsHtml(){
		$data = $this->input->post();
		$customWhere = '  trans_type="EPS"';
		$batchData = $batchData = $this->itemStock->getItemStockBatchWise(['main_ref_id'=>$data['return_id'],'customWhere'=>$customWhere,'group_by'=>'stock_trans.id']);
		$html = "";$i=1;
		if(!empty($batchData)){
			foreach($batchData as $row){
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'delete','res_function':'getEndPcsResponse'}";
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
       
        if(empty($html)) {
			$html = '<td colspan="7" class="text-center">No Data Found.</td>';
		}
        $stockData = $this->endPiece->endPcsReturnData(['id'=>$data['return_id'],'single_row'=>1,'stock_data'=>1]);
        $stockQty = $stockData->qty - $stockData->review_qty;
		
		$this->printJson(['status'=>1,'tbodyData'=>$html,'pending_qty'=>round($stockQty,3)]);
	}

    public function delete(){
		$id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->endPiece->delete($id));
        endif;
	}
}