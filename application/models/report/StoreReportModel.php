<?php
class StoreReportModel extends MasterModel{
    private $itemMaster = "item_master";
    private $stockTrans = "stock_trans";
	private $po_trans = "po_trans";

	public function getStockRegisterData($data){
        $location_id = "";
        if(isset($data['item_type']) && $data['item_type'] == 0):
            $location_id = ' AND location_id = "'.$this->CUT_STORE->id.'"';
        endif;

        $queryData = array();
        $queryData['tableName'] = $this->itemMaster;
        $queryData['select'] = "item_master.id as item_id,item_master.item_code,item_master.item_name,item_master.item_type,ifnull(st.stock_qty,0) as stock_qty,material_master.material_grade";
        $queryData['leftJoin']['(SELECT SUM(qty * p_or_m) as stock_qty,item_id,location_id FROM stock_trans WHERE is_delete = 0 '.$location_id.' GROUP BY item_id) as st'] = "item_master.id = st.item_id";
        $queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
        if(isset($data['item_type']) && $data['item_type'] == 0){
            $queryData['where']['st.location_id'] = $this->CUT_STORE->id;
        }else{
            $queryData['where']['item_master.item_type'] = $data['item_type'];
        }
        if(!empty($data['stock_type'])):
            if($data['stock_type'] == 1):
                $queryData['where']['ifnull(st.stock_qty,0) > '] = "ifnull(st.stock_qty,0) > 0";
            else:
                $queryData['where']['ifnull(st.stock_qty,0) <= '] = "0";
            endif;
        endif;

        $result = $this->rows($queryData);
		
        return $result;
    }
    
    public function getStockTransaction($data){
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "stock_trans.batch_no,SUM(stock_trans.qty * stock_trans.p_or_m) as stock_qty";
        $queryData['where']['stock_trans.item_id'] = $data['item_id'];
        $queryData['having'][] = "SUM(stock_trans.qty * stock_trans.p_or_m) > 0";
        $queryData['group_by'][] = "stock_trans.batch_no";
        return $this->rows($queryData);
    }

	public function getItemSummary($data){
        $unique_id = "";
        if(!empty($data['unique_id'])):
            $unique_id = " AND unique_id = ".$data['unique_id'];
        endif;

        $batch_no = "";
        if(!empty($data['batch_no'])):
            $batch_no = ' AND batch_no = "'.$data['batch_no'].'"';
        endif;
		
		$location_id = "";
        if(!empty($data['location_id'])):
            $location_id = ' AND location_id = "'.$data['location_id'].'"';
        endif;

        $queryData = array();
        $queryData['tableName'] = $this->itemMaster;
        $queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name,ifnull(st.op_stock_qty,0) as op_stock_qty,ifnull(st.in_stock_qty,0) as in_stock_qty,ifnull(st.out_stock_qty,0) as out_stock_qty,ifnull(st.cl_stock_qty,0) as cl_stock_qty";

        $queryData['leftJoin']['(SELECT 
        item_id,
        SUM((CASE WHEN trans_date < "'.$data['from_date'].'" THEN (qty * p_or_m) ELSE 0 END)) as op_stock_qty,
        
        SUM((CASE WHEN trans_date >= "'.$data['from_date'].'" AND trans_date <= "'.$data['to_date'].'" AND p_or_m = 1 THEN qty ELSE 0 END)) as in_stock_qty,
        
        SUM((CASE WHEN trans_date >= "'.$data['from_date'].'" AND trans_date <= "'.$data['to_date'].'" AND p_or_m = -1 THEN qty ELSE 0 END)) as out_stock_qty,
        
        SUM((CASE WHEN trans_date <= "'.$data['to_date'].'" THEN (qty * p_or_m) ELSE 0 END)) as cl_stock_qty

        FROM stock_trans WHERE is_delete = 0 '.$unique_id.' '.$batch_no.' '.$location_id.' GROUP BY item_id) as st'] = "item_master.id = st.item_id";

        if(!empty($data['item_id'])):
            $queryData['where']['item_master.id'] = $data['item_id'];
            $result = $this->row($queryData);
        else:
            $result = $this->rows($queryData);
        endif;
        return $result;
    }	
	
	public function getItemHistory($data){
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = 'item_master.item_code,item_master.item_name,stock_trans.*,(CASE WHEN stock_trans.p_or_m = 1 THEN stock_trans.qty ELSE 0 END) as in_qty,(CASE WHEN stock_trans.p_or_m = -1 THEN stock_trans.qty ELSE 0 END) as out_qty,location_master.location,employee_master.emp_name';
        $queryData['leftJoin']['item_master'] = "item_master.id = stock_trans.item_id";
		$queryData['leftJoin']['location_master'] = "stock_trans.location_id = location_master.id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = stock_trans.created_by";

        if(!empty($data['item_id'])) { $queryData['where']['stock_trans.item_id'] = $data['item_id']; }

        if(!empty($data['batch_no'])) { $queryData['where']['stock_trans.batch_no'] = $data['batch_no']; }

        if(!empty($data['location_id'])) { $queryData['where']['stock_trans.location_id'] = $data['location_id']; }
		
        if(!empty($data['from_date'])) { $queryData['where']['stock_trans.trans_date >='] = $data['from_date']; }

        if(!empty($data['to_date'])) { $queryData['where']['stock_trans.trans_date <='] = $data['to_date']; }
      
        $queryData['order_by']['stock_trans.id'] = 'ASC';

		$result = $this->rows($queryData);
		return $result;
    }
	
	/* Created By Sagar : 13/05/2024 */
	public function getIssueRegister($data){
        $queryData = array();
        $queryData['tableName'] = "issue_register";
        $queryData['select'] = "issue_register.*,item_master.item_name,prc_master.prc_number,item_category.is_return";
        $queryData['leftJoin']['prc_master'] = "issue_register.prc_id  = prc_master.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = issue_register.item_id";
        $queryData['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $queryData['customWhere'][] = " issue_register.issue_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        if(!empty($data['item_id'])) { $queryData['where']['issue_register.item_id'] = $data['item_id']; }
        if(!empty($data['item_type'])) { $queryData['where']['item_master.item_type'] = $data['item_type']; }
        if(!empty($data['category_id'])) { $queryData['where']['item_master.category_id'] = $data['category_id']; }
        $queryData['order_by'][' issue_register.issue_date'] = 'ASC';
        $result = $this->rows($queryData);
        return $result;
    }

	/* INVENTORY MONITORING REPORT CREATE BY RASHMI 15/05/2024*/
    public function getInventoryMonitor($postData){
        $data['tableName'] =  $this->itemMaster;
		$data['select'] = 'item_master.id, item_master.item_name, item_master.item_code, item_master.item_type, item_master.uom';
		
		if($postData['item_type'] != 1):
    		$data['select'] .= ',SUM(CASE WHEN stock_trans.created_at <="'.date('Y-m-d', strtotime('+1 day',strtotime($postData['to_date']))).'" AND stock_trans.p_or_m = 1 AND stock_trans.is_delete = 0 THEN stock_trans.qty ELSE 0 END) AS rqty';
    		$data['select'] .= ',SUM(CASE WHEN stock_trans.created_at <="'.date('Y-m-d', strtotime('+1 day',strtotime($postData['to_date']))).'" AND stock_trans.p_or_m = -1 AND stock_trans.is_delete = 0 THEN stock_trans.qty ELSE 0 END) AS iqty';
    		$data['select'] .= ',SUM(CASE WHEN stock_trans.trans_type = -1 AND stock_trans.is_delete = 0 THEN stock_trans.qty ELSE 0 END) AS opening_qty';
			
            $data['select'] .= ',(SELECT price FROM po_trans WHERE po_trans.item_id = item_master.id AND is_delete = 0 ORDER BY id DESC LIMIT 1) as price';
		else:
    		$data['select'] .= ',SUM(CASE WHEN stock_trans.created_at <="'.date('Y-m-d', strtotime('+1 day',strtotime($postData['to_date']))).'" AND stock_trans.p_or_m = 1 AND stock_trans.is_delete = 0 THEN stock_trans.qty ELSE 0 END) AS rqty';
    		$data['select'] .= ',SUM(CASE WHEN stock_trans.created_at <="'.date('Y-m-d', strtotime('+1 day',strtotime($postData['to_date']))).'" AND stock_trans.p_or_m = -1 AND stock_trans.is_delete = 0 THEN stock_trans.qty ELSE 0 END) AS iqty';
    		$data['select'] .= ',SUM(CASE WHEN stock_trans.trans_type = -1 AND stock_trans.is_delete = 0 THEN stock_trans.qty ELSE 0 END) AS opening_qty';
			$data['select'] .= ',item_master.price';
		endif;

		$data['leftJoin']['stock_trans'] = 'stock_trans.item_id = item_master.id';
	    $data['where']['item_master.item_type'] = $postData['item_type'];
		$data['where']['stock_trans.is_delete'] = 0;
		$data['order_by']['item_master.item_code'] = 'ASC';
		$data['group_by'][] = 'stock_trans.item_id';
        $result = $this->rows($data);
		return $result;
    }

    public function getBomData($param = []){
        $bomData = $this->db->query("select products_sorted.id,item_id, ref_item_id,qty,item_name,item_code,(select count(item_id) from item_kit ik where ik.item_id=products_sorted.ref_item_id AND ik.is_delete=0) as depth_count
		from (select item_kit.id,item_id, ref_item_id,item_kit.qty,item_master.item_code,item_master.item_name
		from item_kit LEFT JOIN item_master ON item_master.id = ref_item_id WHERE item_kit.is_delete=0 GROUP BY item_id,ref_item_id) products_sorted,
        (select @pv := '".$param['item_id']."') initialisation
		 where   find_in_set(item_id, @pv) and length(@pv := concat(@pv, ',', ref_item_id)) order by ref_item_id,item_id")->result();
        
        return $bomData;
    }
    
    public function getBomCostData($param = []){
        $bomData = $this->db->query("select products_sorted.id,item_id, ref_item_id,qty,item_name,item_code,purchase_price,item_type,(select count(item_id) from item_kit ik where ik.item_id=products_sorted.ref_item_id AND ik.is_delete=0) as depth_count,(select SUM(product_process.process_cost) from product_process where product_process.item_id=products_sorted.ref_item_id) as process_cost, net_weight
		from (select item_kit.id,item_id, ref_item_id,item_kit.qty,item_master.item_code,item_master.item_name,item_master.price as purchase_price,item_master.item_type,fg.wt_pcs as net_weight
		from item_kit LEFT JOIN item_master ON item_master.id = ref_item_id JOIN item_master fg ON fg.id = item_kit.item_id WHERE item_kit.is_delete=0 GROUP BY item_id,ref_item_id) products_sorted,
        (select @pv := '".$param['item_id']."') initialisation
		 where   find_in_set(item_id, @pv) and length(@pv := concat(@pv, ',', ref_item_id)) order by ref_item_id,item_id")->result();
        
        $result =  $bomData;
        return $result;
    }
}
?>