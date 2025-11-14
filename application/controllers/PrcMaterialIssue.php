<?php
class PrcMaterialIssue extends MY_Controller{

    public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "PRC Material Issue";
		$this->data['headData']->controller = "prcMaterialIssue";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'prcMaterialIssue']);
	}
	
	public function index(){
		$this->data['headData']->pageTitle = "PRC Material Issue";
        $this->data['tableHeader'] = getStoreDtHeader('prcMaterialIssue');
        $this->load->view('prc_material/index',$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->prcMaterialIssue->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPrcMaterialIssueData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPrcMaterial(){
        $this->data['prcList'] = $this->sop->getPRCList(['status'=>'1,2']);
        $this->load->view('prc_material/form',$this->data);
    }

    /** GET BOM GROUP LIST ON PRODUCT */
    public function getBomGroupList(){
        $data = $this->input->post();
        $groupList = $this->item->getProductKitData(['item_id'=>$data['item_id'],'group_by'=>'group_name']);
        $groupOptions = '<option value="">Select Group</option>';
        if(!empty($groupList)){
            foreach($groupList as $row){    
                $groupOptions .= ' <option value="'.$row->group_name.'">'.$row->group_name.'</option>';
            }
        }
        $this->printJson(['status'=>1,'groupOptions'=>$groupOptions]);
    }

    /** GET BOM ITEMS */
    public function getBomList(){
        $data = $this->input->post(); $bomOptions = '<option value="">Select BOM Item</option>';
        /** Find Previous issue item of selected bom group if found then return only issued item else all item of selected group */
        $prevIssueData = $this->prcMaterialIssue->getPrcBomData(['prc_id'=>$data['prc_id'],'bom_group'=>$data['bom_group'],'single_row'=>1]);
        if(!empty($prevIssueData)){
            $bomOptions .= ' <option value="'.$prevIssueData->item_id.'" data-item_type="'.$row->item_type.'">'.$prevIssueData->item_name.'</option>';
        }else{
            $itemList = $this->item->getProductKitData(['item_id'=>$data['item_id'],'group_name'=>$data['bom_group']]);
            if(!empty($itemList)){
                foreach($itemList as $row){    
                    $bomOptions .= ' <option value="'.$row->ref_item_id.'" data-item_type="'.$row->item_type.'">'.$row->item_name.'</option>';
                }
            }
        }
        $this->printJson(['status'=>1,'bomOptions'=>$bomOptions]);
    }

    public function getBatchWiseStock(){
        $data = $this->input->post();
        $batchData = $this->itemStock->getItemStockBatchWise(['item_id'=>$data['item_id'],'group_by'=>'item_id,location_id,batch_no','stock_required'=>1]);
        $tbodyData = '';$i=1;
        if(!empty($batchData)){
            foreach($batchData as $row){
                $tbodyData .= '<tr>    
                    <td>'.$i.'</td>
                    <td>'.$row->location.' ['.$row->store_name.']'.'</td>
                    <td>'.$row->batch_no.'</td>
                    <td>'.$row->qty.'</td>
                    <td>
                        <input type="text" name="issue_qty[]" class="form-control batchQty" id="issue_qty_'.$i.'">
                        <input type="hidden" name="batch_no[]" class="form-control" id="batch_no_'.$i.'" value="'.$row->batch_no.'">
                        <input type="hidden" name="location_id[]" class="form-control" id="location_id_'.$i.'" value="'.$row->location_id.'">
                        <div class="error issue_qty'.$i.'"></div>
                    </td>
                </tr>';
                $i++;
            }
        }else{
            $tbodyData .= '<tr><th colspan="5" class="text-center">No stock available.</th></tr>';
        }
        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

    public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();
        if ($data['prc_id'] == ""){ $errorMessage['prc_id'] = "PRC is required."; }   
        if ($data['bom_group'] == ""){ $errorMessage['bom_group'] = "Group is required."; }   
        if ($data['item_id'] == ""){ $errorMessage['item_id'] = "Group is required."; } 
        if(empty($data['issue_qty']) || (array_sum($data['issue_qty']) <= 0)){
            $errorMessage['general_batch_no'] = " Issue Qty is required";
        }else{
            $itemData = $this->item->getItem(['id'=>$data['item_id']]);
            $i=1;$batch_no = "";$batchCount=0;
            if($itemData->item_type == 3){
                $oldBatchData = $this->itemStock->getItemStockBatchWise(['main_ref_id'=>$data['prc_id'],'entry_type'=>$this->data['entryData']->id,'item_id'=>$data['item_id'],'single_row'=>1]);
                $batch_no = !empty($oldBatchData->batch_no)?$oldBatchData->batch_no:'';
            }
            foreach($data['issue_qty'] as $key=>$issue_qty){
                if($issue_qty > 0){
                    $stockData = $this->itemStock->getItemStockBatchWise(['location_id'=>$data['location_id'][$key],'batch_no'=>$data['batch_no'][$key],'item_id'=>$data['item_id'],'single_row'=>1]);
                    if($issue_qty > $stockData->qty){
                        $errorMessage['issue_qty'.$i] = " Stock not available.";
                    }
                }
                if($batch_no != $data['batch_no'][$key]){
                    $batchCount++;
                }
                $batch_no = $data['batch_no'][$key];
                $i++;  
            }
            if($batchCount > 1 && $itemData->item_type == 3){
                $errorMessage['general_batch_no'] = "Multiple Batch no allowed";
            }
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            unset($data['error_msg']);
            $data['entry_type'] = $this->data['entryData']->id;
            $result = $this->prcMaterialIssue->save($data);
            $this->printJson($result);
        endif;
        
    }
}
?>