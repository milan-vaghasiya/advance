<?php
class InspectionModel extends MasterModel{

    private $requisitionLog = "requisition_log";
    private $requisitionIssue = "requisition_issue";
    private $itemMaster = "item_master";
    private $itemCategory = "item_category";
    private $materialReturn = "material_return";

    public function getDTRows($data){
        $data['tableName'] = $this->materialReturn;
        $data['select'] = "material_return.*,requisition_issue.issue_number";
        $data['leftJoin']['requisition_issue'] = "material_return.issue_id  = requisition_issue.id";
        $data['leftJoin']['requisition_log'] = "requisition_issue.log_id  = requisition_log.id";
        $data['leftJoin']['item_master'] = "item_master.id  = requisition_log.item_id";
        $data['where']['material_return.trans_type'] = $data['trans_type'];

        if($data['trans_type'] == 0){
            $data['where']['(material_return.total_qty - material_return.insp_qty) > '] = 0;
        }

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "requisition_issue.issue_number";
        $data['searchCol'][] = "DATE_FORMAT(material_return.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "material_return.total_qty";
        $data['searchCol'][] = "material_return.batch_no";
        $data['searchCol'][] = "material_return.remark";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getMaterialData($id) {
        $data['tableName'] = $this->materialReturn;
        $data['select'] = "material_return.*";
        $data['leftJoin']['requisition_issue'] = "requisition_issue.id  = material_return.issue_id";
        $data['leftJoin']['requisition_log'] = "requisition_log.id  = requisition_issue.log_id";
		$data['where']['material_return.id'] = $id;
        return $this->row($data);
    }
}
?>