<?php
class RequisitionModel extends MasterModel{

    private $requisitionLog = "requisition_log";
    private $requisitionIssue = "requisition_issue";
    private $itemMaster = "item_master";
    private $itemCategory = "item_category";
    private $materialReturn = "material_return";

    public function nextTransNo(){
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "MAX(log_no) as log_no";
		$trans_no = $this->specificRow($data)->log_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;        
		return $nextTransNo;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "requisition_log.*,item_master.item_name,im1.item_name as mt_name,finish.item_name as fg_name";
        $data['leftJoin']['item_master'] = "item_master.id  = requisition_log.item_id";
        $data['leftJoin']['item_master as finish'] = "finish.id  = requisition_log.fg_id";


        if($data['status'] == 0){
            $data['where']['(requisition_log.req_qty - requisition_log.issue_qty) >'] = 0;
        }

        $data['where']['requisition_log.status'] = $data['status'];

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "requisition_log.log_number";
        $data['searchCol'][] = "DATE_FORMAT(requisition_log.log_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "requisition_log.req_qty";
        $data['searchCol'][] = "requisition_log.issue_qty";
        $data['searchCol'][] = "finish.item_name";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getReturnDTRows($data){
        $data['tableName'] = $this->requisitionIssue;
        $data['select'] = "requisition_issue.*,requisition_log.log_number,requisition_log.log_date,item_master.item_name";
        $data['leftJoin']['requisition_log'] = "requisition_issue.log_id  = requisition_log.id";
        $data['leftJoin']['item_master'] = "item_master.id  = requisition_issue.item_id";
        $data['customWhere'][] = '(requisition_issue.issue_qty - requisition_issue.return_qty) > 0';
        $data['where']['requisition_issue.is_return'] = 1;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "requisition_log.log_number";
        $data['searchCol'][] = "DATE_FORMAT(requisition_log.log_date,'%d-%m-%Y')";
        $data['searchCol'][] = "requisition_issue.issue_number";
        $data['searchCol'][] = "DATE_FORMAT(requisition_issue.issue_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "requisition_issue.issue_qty";
        $data['searchCol'][] = "requisition_issue.return_qty";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getIssueDTRows($data) { 
        $data['tableName'] = $this->requisitionIssue;
        $data['select'] = "requisitionIssue.*";
        $data['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $data['leftJoin']['stock_transaction'] = "stock_transaction.main_ref_id = requisitionIssue.id";

        // $data['searchCol'][] = "CONCAT('REQ',LPAD(requisition_log.log_no, 5, '0'))";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "requisition_log.log_no";
        $data['searchCol'][] = "DATE_FORMAT(requisition_log.issue_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "requisition_log.req_qty";      

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function save($data){
		try {
			$this->db->trans_begin();

            $result = $this->store($this->requisitionLog, $data, 'Requisition');
			
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function delete($id){
        return $this->trash($this->requisitionLog,['id'=>$id]);
    }

    public function saveReturnReq($data) {
        try {
			$this->db->trans_begin();

            $entry_type = (!empty($data['entry_type'])) ? $data['entry_type'] : "" ;
            $issue_number = (!empty($data['issue_number'])) ? $data['issue_number'] : "" ;
            $location_id = (!empty($data['location_id'])) ? $data['location_id'] : "" ;
            unset($data['entry_type']);
            unset($data['issue_number']);
            unset($data['location_id']);

            $data['id'] = '';
            $result = $this->store($this->materialReturn, $data, 'Return Material');

            if($data['trans_type'] == 0){
                $setData = array();
                $setData['tableName'] = $this->requisitionIssue;
                $setData['where']['id'] = $data['issue_id'];
                $setData['set']['return_qty'] = 'return_qty, + ' . $data['total_qty'];
                $this->setValue($setData);
            }

            if($data['trans_type'] == 1){

                if($data['used_qty'] != "" && $data['used_qty'] != 0)
                {
                    $stockPlusQuery = [
                        'id' => "",
                        'entry_type' => $entry_type,
                        'ref_date' => date("Y-m-d"),
                        'location_id'=> $location_id,
                        'batch_no' => $data['batch_no'],
                        'item_id' => $data['item_id'],
                        'qty' => $data['used_qty'],
                        'p_or_m' => 1,
                        'main_ref_id' =>  $data['issue_id'],
                        'child_ref_id' => $result['insert_id'],
                        'ref_no' => $issue_number,
                        'stock_type' => 'USED',
                        'created_by' => $data['created_by']
                    ];
                    $issueTrans = $this->store('stock_transaction', $stockPlusQuery);
                }

                if($data['fresh_qty'] != "" && $data['fresh_qty'] != 0)
                {
                    $stockPlusQuery = [
                        'id' => "",
                        'entry_type' => $entry_type,
                        'ref_date' => date("Y-m-d"),
                        'location_id'=> $location_id,
                        'batch_no' => $data['batch_no'],
                        'item_id' => $data['item_id'],
                        'qty' => $data['fresh_qty'],
                        'p_or_m' => 1,
                        'main_ref_id' =>  $data['issue_id'],
                        'child_ref_id' => $result['insert_id'],
                        'ref_no' => $issue_number,
                        'stock_type' => 'FRESH',
                        'created_by' => $data['created_by']
                    ];
                    $issueTrans = $this->store('stock_transaction', $stockPlusQuery);
                }

                $setData = array();
                $setData['tableName'] = $this->materialReturn;
                $setData['where']['id'] = $data['ref_id'];
                $setData['set']['insp_qty'] = 'insp_qty, + ' . $data['insp_qty'];
                $this->setValue($setData);
            }
			
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function getRequisition($id){
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "requisition_log.*,item_category.is_return,item_master.item_name";
        $data['leftJoin']['item_master'] = "item_master.id  = requisition_log.item_id";
        $data['leftJoin']['item_category'] = "item_category.id  = item_master.category_id";
		$data['where']['requisition_log.id'] = $id;
        return $this->row($data);
    }
    
    
    public function getRequisitionData($param = []){
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "requisition_log.*,item_master.item_name,im1.item_name as mt_name,im2.item_name as fg_name";
        $data['leftJoin']['item_master'] = "item_master.id  = requisition_log.item_id";
        $data['leftJoin']['item_master as im1'] = "im1.id  = requisition_log.mc_id";
        $data['leftJoin']['item_master as im2'] = "im2.id  = requisition_log.fg_id";

        if(isset($param['status']) && $param['status'] == 0){
            $data['where']['(requisition_log.req_qty - requisition_log.issue_qty) >'] = 0;
        }

        if(isset($param['status']) && $param['status'] == 1){
            $data['where']['(requisition_log.req_qty - requisition_log.issue_qty) <= '] = 0;
        }
        $data['where']['requisition_log.status'] = 0;
        return $this->rows($data);
    }

    // 06-04-2024
    public function getIssuedRequisitionData(){
        $data['tableName'] = $this->requisitionIssue;
        $data['select'] = "requisition_issue.*,requisition_log.log_number,item_master.item_name";
        $data['leftJoin']['requisition_log'] = "requisition_issue.log_id  = requisition_log.id";
        $data['leftJoin']['item_master'] = "item_master.id = requisition_issue.item_id";
        $data['where']['(requisition_issue.issue_qty) >'] = 0;
        return $this->rows($data);
    }

    // 06-04-2024
    public function getMaterialReturnData(){
        $data['tableName'] = $this->requisitionIssue;
        $data['select'] = "requisition_issue.*,requisition_log.log_number,requisition_log.log_date,item_master.item_name";
        $data['leftJoin']['requisition_log'] = "requisition_issue.log_id  = requisition_log.id";
        $data['leftJoin']['item_master'] = "item_master.id = requisition_issue.item_id";
        $data['customWhere'][] = '(requisition_issue.issue_qty - requisition_issue.return_qty) > 0';
        $data['where']['requisition_issue.is_return'] = 1;
        return $this->rows($data);
    }
}
?>