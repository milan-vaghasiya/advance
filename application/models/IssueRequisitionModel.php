<?php
class IssueRequisitionModel extends MasterModel{

    private $requisitionLog = "requisition_log";
    private $requisitionIssue = "requisition_issue";
    private $itemMaster = "item_master";
    private $itemCategory = "item_category";

    public function nextIssueNo(){
        $data['tableName'] = $this->requisitionIssue;
        $data['select'] = "MAX(issue_no) as issue_no";
		$issue_no = $this->specificRow($data)->issue_no;
		$nextIssueNo = (!empty($issue_no))?($issue_no + 1):1;        
		return $nextIssueNo;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "requisition_log.*,item_master.item_name,(requisition_log.req_qty - requisition_log.issue_qty) as pending_qty,im1.item_name as mt_name,im2.item_name as fg_name";
        $data['leftJoin']['item_master'] = "item_master.id  = requisition_log.item_id";
        $data['leftJoin']['item_master as im1'] = "im1.id  = requisition_log.mc_id";
        $data['leftJoin']['item_master as im2'] = "im2.id  = requisition_log.fg_id";

        $data['where']['requisition_log.status'] = $data['status'];
        $data['where']['(requisition_log.req_qty - requisition_log.issue_qty) >'] = 0;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "requisition_log.log_number";
        $data['searchCol'][] = "requisition_log.log_date";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "requisition_log.req_qty";
        $data['searchCol'][] = "requisition_log.issue_qty";
        $data['searchCol'][] = "(requisition_log.req_qty - requisition_log.issue_qty)";
        $data['searchCol'][] = "im1.item_name";
        $data['searchCol'][] = "im2.item_name";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getIssueDTRows($data) {
        $data['tableName'] = $this->requisitionIssue;
        $data['select'] = "requisition_issue.*,requisition_log.log_number,item_master.item_name";
        $data['leftJoin']['requisition_log'] = "requisition_issue.log_id  = requisition_log.id";
        $data['leftJoin']['item_master'] = "item_master.id = requisition_issue.item_id";

        $data['where']['(requisition_issue.issue_qty) >'] = 0;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "requisition_issue.issue_number";
        $data['searchCol'][] = "DATE_FORMAT(requisition_issue.issue_date,'%d-%m-%Y')";
        $data['searchCol'][] = "requisition_log.log_number";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "requisition_issue.issue_qty";
        $data['searchCol'][] = "requisition_issue.batch_no";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function saveIssueMaterial($data){
        try {
            $this->db->trans_begin();

            foreach ($data['batch_qty'] as $key => $value) {

                $issue_no = $this->nextIssueNo();

                if(!empty($value) && $value > 0) {

                    $issueData = [
                        'id' => '',
                        'log_id' => $data['log_id'],
                        'issue_no' => $issue_no,
                        'issue_number' => 'ISU/'.str_pad($issue_no, 5, '0', STR_PAD_LEFT),
                        'issue_date' => date("Y-m-d"),
                        'item_id' => $data['item_id'],
                        'batch_no' => $data['batch_no'][$key],
                        'is_return' => $data['is_return'],
                        'issue_qty' => $value,
                        'created_by' => $data['created_by']
                    ];
                    $result = $this->store($this->requisitionIssue, $issueData, 'Issue Requisition');

                    $stockMinusQuery = [
                        'id' => "",
                        'entry_type' => $data['entry_type'],
                        'ref_date' => date("Y-m-d"),
                        'location_id'=> $data['location_id'][$key],
                        'batch_no' => $data['batch_no'][$key],
                        'item_id' => $data['item_id'],
                        'qty' => '-'.$value,
                        'p_or_m' => 2,
                        'main_ref_id' =>  $data['log_id'],
                        'child_ref_id' => $result['insert_id'],
                        'stock_type' => $data['stock_type'][$key],
                        'ref_no' => 'ISU/'.str_pad($issue_no, 5, '0', STR_PAD_LEFT),
                        'created_by' => $data['created_by']
                    ];
                    $issueTrans = $this->store('stock_transaction', $stockMinusQuery);
                }
            }

			$setData = array();
			$setData['tableName'] = $this->requisitionLog;
			$setData['where']['id'] = $data['log_id'];
			$setData['set']['issue_qty'] = 'issue_qty, + ' .  array_sum($data['batch_qty']);
			$this->setValue($setData);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material Issue suucessfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getIssuRequisition($id) {
        $data['tableName'] = $this->requisitionIssue;
        $data['select'] = "requisition_issue.*,item_category.is_return,item_master.item_name";
        $data['leftJoin']['requisition_log'] = "requisition_log.id  = requisition_issue.log_id";
        $data['leftJoin']['item_master'] = "item_master.id  = requisition_log.item_id";
        $data['leftJoin']['item_category'] = "item_category.id  = item_master.category_id";
		$data['where']['requisition_issue.id'] = $id;
        return $this->row($data);
    }
}
?>