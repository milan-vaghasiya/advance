<?php
class SalesReportModel extends MasterModel{
    private $soMaster = "so_master";
    private $soTrans = "so_trans";
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    public function getOrderMonitoringData($data){
        $queryData = array();
        $queryData['tableName'] = $this->soMaster;
        $queryData['select'] = "so_master.trans_number,so_master.trans_date,so_master.doc_no,so_trans.qty,item_master.item_name,so_trans.price,party_master.party_name,so_trans.id,so_trans.cod_date";
        $queryData['leftJoin']['so_trans'] = "so_trans.trans_main_id = so_master.id";
        $queryData['leftJoin']['party_master'] = "so_master.party_id = party_master.id";
        $queryData['leftJoin']['item_master'] = "so_trans.item_id = item_master.id";
        $queryData['customWhere'][] = "so_master.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        if(!empty($data['party_id'])){
            $queryData['where']['so_master.party_id'] = $data['party_id'];
		}
        if(!empty($data['item_id'])){
            $queryData['where']['so_trans.item_id'] = $data['item_id'];
		}
		if(isset($data['status']) && $data['status'] !== ''){
            $queryData['where_in']['so_trans.trans_status'] = $data['status'];
        }
        $queryData['order_by']['so_master.trans_date'] = "ASC";
        $queryData['order_by']['so_master.trans_number'] = "ASC";

        $result = $this->rows($queryData);
        return $result;
    }

    public function getSalesInvData($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.trans_number as invNo,trans_main.trans_date as invDate,trans_child.qty as invQty,trans_child.ref_id";
        $queryData['leftJoin']['trans_child'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['where']['trans_child.ref_id'] = $data['ref_id'];
        //$queryData['where']['trans_main.from_entry_type'] = 14;
        $result = $this->rows($queryData);
        return $result;
    }

    public function getSalesAnalysisData($data){
        $queryData = array();
        if($data['report_type'] == 1):
            $queryData['tableName'] = $this->transMain;
            $queryData['select'] = "party_name,SUM(taxable_amount) as taxable_amount,SUM(gst_amount) as gst_amount,SUM(net_amount) as net_amount";
            $queryData['where']['trans_date >='] = $data['from_date'];
            $queryData['where']['trans_date <='] = $data['to_date'];
            $queryData['where']['vou_name_s'] = "Sale";
            $queryData['group_by'][] = 'party_id';
            $queryData['order_by']['SUM(taxable_amount)'] = $data['order_by'];
            $result = $this->rows($queryData);
        else:
            $queryData['tableName'] = $this->transChild;
            $queryData['select'] = "trans_child.item_name,SUM(trans_child.qty) as qty,SUM(trans_child.taxable_amount) as taxable_amount,ROUND((SUM(trans_child.taxable_amount) / SUM(trans_child.qty)),2) as price";
            $queryData['leftJoin']['trans_main'] = "trans_child.trans_main_id = trans_main.id";
            $queryData['where']['trans_date >='] = $data['from_date'];
            $queryData['where']['trans_date <='] = $data['to_date'];
            $queryData['where']['vou_name_s'] = "Sale";
            $result = $this->rows($queryData);
        endif;
        return $result;
    }

    public function getMrpReportData($data) {
        $queryData['tableName'] = $this->soTrans;  
        $queryData['select'] = 'so_master.trans_number,so_master.trans_date,bom.item_name as bom_item_name,(stock_data.stock_qty / item_kit.qty) AS plan_qty';
        $queryData['leftJoin']['so_master'] = "so_trans.trans_main_id = so_master.id";
        $queryData['leftJoin']['item_kit'] = "item_kit.item_id = so_trans.item_id AND item_kit.is_delete = 0";
        $queryData['leftJoin']['item_master AS bom'] = "bom.id = item_kit.ref_item_id";
        $queryData['leftJoin']['(SELECT SUM(`stock_trans`.`qty` * `stock_trans`.`p_or_m`) AS stock_qty,`stock_trans`.`item_id` FROM `stock_trans` WHERE is_delete = 0 GROUP BY `stock_trans`.`item_id`) AS stock_data'] = 'stock_data.item_id = item_kit.ref_item_id';
        if(!empty($data['party_id']) && $data['party_id'] != 'ALL'){ $queryData['where']['so_master.party_id'] = $data['party_id']; }
        if(!empty($data['item_id'])){ $queryData['where']['so_trans.item_id'] = $data['item_id']; }
        $queryData['where']['(so_trans.qty - IFNULL(so_trans.dispatch_qty, 0.000)) >'] = 0;
        $queryData['order_by']['so_master.trans_no'] = 'ASC';
        $queryData['order_by']['so_trans.id'] = 'ASC';
        return $this->rows($queryData);
    }

	/* Customer Complaints Report*/
    public function getCustomerComplaintsData($data){
        $queryData = array();
        $queryData['tableName'] = 'customer_complaints';
        $queryData['select'] = "customer_complaints.trans_number,customer_complaints.trans_date,,item_master.item_name,party_master.party_name,trans_main.trans_number as inv_number,customer_complaints.complaint,customer_complaints.report_no,customer_complaints.action_taken,customer_complaints.ref_feedback,customer_complaints.remark,customer_complaints.defect_image,customer_complaints.qty,(trans_child.price * customer_complaints.qty) AS rej_amount,customer_complaints.party_id";
        $queryData['leftJoin']['party_master'] = "customer_complaints.party_id = party_master.id";
        $queryData['leftJoin']['item_master'] = "customer_complaints.item_id = item_master.id";
        $queryData['leftJoin']['trans_child'] = "trans_child.id = customer_complaints.inv_trans_id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['customWhere'][] = "customer_complaints.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        if(!empty($data['party_id'])):
            $queryData['where']['customer_complaints.party_id'] = $data['party_id'];
        endif;
        if(!empty($data['item_id'])):
            $queryData['where']['customer_complaints.item_id'] = $data['item_id'];
        endif;
        if(!empty($data['product_returned'])):
            $queryData['where']['customer_complaints.product_returned'] = $data['product_returned'];
        endif;
        $queryData['order_by']['customer_complaints.trans_date'] = "ASC";
        $queryData['order_by']['customer_complaints.trans_number'] = "ASC";

        $result = $this->rows($queryData);
        return $result;
    }

	public function getDispatchDetailsData($data = array()){
		$queryData['tableName'] = $this->transChild;
		$queryData['select'] = "trans_main.trans_number, trans_main.trans_date, trans_main.party_name, trans_child.item_name, trans_child.hsn_code, stock_trans.batch_no, SUM(ABS(stock_trans.qty)) as dispatch_qty, (trans_child.qty) as inv_qty,so_master.doc_no,so_trans.cod_date";
		
		$queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['stock_trans'] = 'stock_trans.child_ref_id = trans_child.id AND stock_trans.trans_type = "INV" AND stock_trans.p_or_m = "-1" AND stock_trans.is_delete = 0';
		$queryData['leftJoin']['so_master'] = "so_master.id = trans_main.ref_id AND trans_main.from_entry_type = 14";
		$queryData['leftJoin']['so_trans'] = "so_trans.trans_main_id = so_master.id";
		
		$queryData['where']['trans_main.trans_status !='] = 3;
		$queryData['where_in']['trans_main.entry_type'] = [20];
		
		if($data['date_filter'] == 1){
			$queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		}else{
			$queryData['customWhere'][] = "so_trans.cod_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		}
		
		if (!empty($data['party_id'])):
            $queryData['where']['trans_main.party_id'] = $data['party_id'];
        endif;
		
		if(!empty($data['item_id'])):
            $queryData['where']['trans_child.item_id'] = $data['item_id'];
        endif;
		
		$queryData['group_by'][] = "trans_child.trans_main_id, trans_child.item_id, stock_trans.batch_no";
		$queryData['order_by']['trans_main.trans_date']='ASC';
        $queryData['order_by']['trans_main.id']='ASC';
		return $this->rows($queryData);
	}
	
}
?>