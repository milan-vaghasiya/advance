<?php
class issueRequisition extends MY_Controller
{
    private $pendIndexPage = "issue_requisition/index";
    private $issueIndexPage = "issue_requisition/issue_req";
    private $formPage = "issue_requisition/form";

    public function __construct(){
		parent::__construct(); 
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Issue Requisition";
		$this->data['headData']->controller = "issueRequisition";
		$this->data['headData']->pageUrl = "issueRequisition";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'issueRequisition']);
	}
	
	public function index($status=1){
        $this->data['status']=$status;
        $this->data['tableHeader'] = getStoreDtHeader('pendingRequisition');
        $this->load->view($this->pendIndexPage, $this->data);
    }

    public function issueReqIndex() {
        $this->data['issueType'] = 1;
        $this->data['tableHeader'] = getStoreDtHeader('issueRequisition');
        $this->load->view($this->issueIndexPage, $this->data);
    }

    public function getDTRows($status = 0){ 
		$data=$this->input->post();
		$data['status'] = $status;
		$result = $this->issueRequisition->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->status = $status;
            $sendData[] = getPendingRequisitionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getIssueDTRows($status = 0){ 
		$data=$this->input->post();
		$data['status'] = $status;
		$result = $this->issueRequisition->getIssueDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->status = $status;
            $sendData[] = getIssueRequisitionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function issueRequisition() {
        $id = $this->input->post('id');
        $this->data['dataRow'] = $dataRow = $this->requisition->getRequisition($id);
        $this->data['batchData'] = $this->itemStock->getItemStockBatchWise(["item_id" => $dataRow->item_id,'stock_required'=>1,'group_by'=>'location_id,batch_no,stock_type']);
        $this->load->view($this->formPage, $this->data);
    }

    public function saveIssueRequisition() {

        $data = $this->input->post();
        $errorMessage = array();

        if(isset($data['batch_no'])){
            $sData = $data['batch_no'];
            for ($i=0; $i < count($sData); $i++) {
                $stockData = $this->itemStock->getItemStockBatchWise(['location_id'=>$data['location_id'][$i],'batch_no'=>$data['batch_no'][$i],'item_id'=>$data['item_id'],'single_row'=>1]);
                $stock_qty = (!empty($stockData)) ? $stockData->qty : 0;
                if($data['batch_qty'][$i] > $stock_qty){
                    $errorMessage['batch_qty_'.$i] = " Stock not available.";
                }
            }

            $sumReqQty = array_sum($data['batch_qty']);
            if($sumReqQty > $data['req_qty']){
                $errorMessage['table_err'] = "Batch Qty is grater than Req Qty";
            }
        } else {
            $errorMessage['table_err'] = "Stock Not Available";
        }

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            unset($data['error_msg']);
            $data['created_by'] = $this->session->userdata('loginId');
            $data['entry_type'] = $this->data['entryData']->id;
            $this->printJson($this->issueRequisition->saveIssueMaterial($data));
        endif;
    }
    
    public function addIssueRequisition() {
        $issue_no = $this->issueRequisition->nextIssueNo();
        $this->data['issue_prefix'] = 'ISU/'.str_pad($issue_no, 5, '0', STR_PAD_LEFT);
        $this->data['issue_no'] = $issue_no;
        $this->data['itemData'] = $this->item->getItemList(['item_type' => 2]);
        // $this->data['mcData'] = $this->item->getItemList(['item_type' => 5]);
        // $this->data['fgData'] = $this->item->getItemList(['item_type' => 1]);
        $this->load->view($this->formPage,$this->data);
    }

    public function getBatchWiseStock() {
        $item_id = $this->input->post('item_id');
        $is_return = $this->item->getItem(['id' => $item_id])->is_return;
        $batchData = $this->itemStock->getItemStockBatchWise(["item_id" => $item_id,'stock_required'=>1,'group_by'=>'location_id,batch_no,stock_type']);
        $tbodyData='';$i=1;
        if (!empty($batchData)) {
            foreach ($batchData as $row) {
                $tbodyData .= '<tr>';
                $tbodyData .= '<td>'.$row->location.'</td>';
                $tbodyData .= '<td>'.$row->batch_no.'</td>';
                $tbodyData .= '<td>'.floatVal($row->qty).'</td>';
                $tbodyData .= '<td>'.$row->stock_type.'</td>';
                $tbodyData .= '<td>
                                    <input type="text" name="batch_qty[]" class="form-control batchQty floatOnly" min="0" value="" />
                                    <div class="error batch_qty_' . $i . '"></div>
                                    <input type="hidden" name="batch_no[]" id="batch_number_' . $i . '" value="' . $row->batch_no . '" />
                                    <input type="hidden" name="location_id[]" id="location_' . $i . '" value="' . $row->location_id . '" />
                                    <input type="hidden" name="stock_type[]" id="stock_type_' . $i . '" value="' . $row->stock_type . '" />
                                </td>';
                $tbodyData .= "</tr>";
                $i++;
            }
        } else {
            $tbodyData .= "<td colspan='5' class='text-center'>No Data</td>";
        }
        $this->printJson(['status' => 1, 'tbodyData' => $tbodyData, 'is_return' => $is_return]);
    }
}
?>