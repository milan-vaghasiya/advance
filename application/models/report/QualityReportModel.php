<?php 
class QualityReportModel extends MasterModel
{
    private $stockTransaction = "stock_trans";
    private $grnTrans = "grn_trans";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $jobRejection = "job_rejection";
    private $productKit = "item_kit";

	public function getBatchHistory($data){
        $queryData['tableName'] = 'batch_history';
		$queryData['select'] = "batch_history.id,item_master.item_name,fgItem.item_name as fg_item_name,batch_history.item_id,batch_history.batch_no,batch_history.mill_name,material_master.material_grade,material_master.color_code,SUM(stock_trans.qty * stock_trans.p_or_m) as stock_qty";
		$queryData['leftJoin']['item_master'] = "batch_history.item_id = item_master.id";
		$queryData['leftJoin']['stock_trans'] = "batch_history.item_id = stock_trans.item_id AND batch_history.batch_no = stock_trans.batch_no AND stock_trans.is_delete = 0";
        $queryData['leftJoin']['material_master'] = "item_master.grade_id = material_master.id";
        $queryData['leftJoin']['item_master fgItem'] = "fgItem.id = batch_history.fg_item_id";
        $queryData['group_by'][] = 'batch_history.batch_no,batch_history.item_id';
        $queryData['order_by']['batch_history.batch_no'] = 'DESC';
        $result = $this->rows($queryData);
	   	return $result;
    } 
	
	public function getBatchHistoryData($param=[]){
        $data['tableName'] = "grn_trans";
        $data['select'] = "SUM(grn_trans.qty) as qty,GROUP_CONCAT(DISTINCT grn_master.trans_number) as trans_number,grn_master.trans_date,GROUP_CONCAT(DISTINCT grn_master.doc_no) as doc_no,GROUP_CONCAT(DISTINCT party_master.party_name) as party_name";
        $data['leftJoin']['grn_master'] = "grn_master.id = grn_trans.grn_id";
        $data['leftJoin']['batch_history'] = "batch_history.batch_no = grn_trans.batch_no AND batch_history.item_id = grn_trans.item_id";
        $data['leftJoin']['party_master'] = "party_master.id = batch_history.party_id";

        if(!empty($param['item_id'])) { $data['where']['grn_trans.item_id'] = $param['item_id']; }

        if(!empty($param['batch_no'])) { $data['where']['grn_trans.batch_no'] = $param['batch_no']; }

        $data['group_by'][] = "grn_trans.batch_no,grn_trans.item_id";

        $result = $this->rows($data);
        return $result;
    }

	public function getBatchHistoryTrans($param=[]){
        $data['tableName'] = "stock_trans";
        $data['select'] = "prc_master.id,prc_master.prc_type,prc_master.prc_number,prc_master.prc_qty,item_master.item_code,item_master.item_name,prc_master.cutting_flow,prc_master.status,prc_detail.process_ids,prc_detail.cut_weight,stock_trans.item_id,grnTrans.trans_number,im.item_code as grn_item_code,im.item_name as grn_item_name,SUM(stock_trans.qty) as grn_qty,stock_trans.main_ref_id,sim.item_name as challan_item_name,sim.item_code as challan_item_code,stock_trans.trans_type,stock_trans.ref_no,stock_trans.qty as challan_qty";
        
        $data['leftJoin']['prc_master'] = "prc_master.id = stock_trans.child_ref_id AND prc_master.batch_no = stock_trans.batch_no AND prc_master.is_delete = 0 AND (stock_trans.trans_type = 'SSI')";
        $data['leftJoin']['prc_detail'] = "prc_detail.prc_id = prc_master.id";
        $data['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";

        $data['leftJoin']['(SELECT 
								grn_trans.id,grn_trans.batch_no,grn_trans.grn_id,grn_trans.fg_item_id,grn_master.trans_number
							FROM 
								grn_trans 
							LEFT JOIN grn_master ON grn_master.id = grn_trans.grn_id AND grn_master.is_delete = 0 AND grn_master.grn_type = 3
                            ) grnTrans '] = 'grnTrans.id = stock_trans.child_ref_id AND grnTrans.batch_no = stock_trans.batch_no AND stock_trans.main_ref_id = grnTrans.grn_id AND stock_trans.trans_type = "IGR"';

        $data['leftJoin']['item_master im'] = "im.id = grnTrans.fg_item_id";

        $data['leftJoin']['item_master sim'] = "sim.id = stock_trans.item_id";

        if(!empty($param['item_id'])) { $data['where']['stock_trans.item_id'] = $param['item_id']; }

        if(!empty($param['batch_no'])) { $data['where']['stock_trans.batch_no'] = $param['batch_no']; }

        $data['customWhere'][] = "stock_trans.trans_type IN('SSI','IGR','DLC') AND stock_trans.p_or_m = '-1' ";

        $data['group_by'][] = "stock_trans.item_id,stock_trans.batch_no,stock_trans.child_ref_id";

        $result = $this->rows($data);
        return $result;
    }
	
	public function getSupplierRating($data){
        $queryData['tableName'] = $this->grnTrans;
        $queryData['select'] = "grn_trans.*, grn_master.trans_date,po_trans.delivery_date,item_master.item_name,party_master.party_name,grn_master.trans_number,grn_master.doc_no as inv_no,grn_master.doc_date as inv_date,po_master.trans_number as po_number,po_master.trans_date as po_date";

        if(!empty($data['reportData'])){
            $queryData['select'] .=',SUM(grn_trans.qty) as grnQty,SUM(grn_trans.ok_qty) as okQty,GROUP_CONCAT(grn_trans.item_id SEPARATOR "~") AS itemId';
        }
		$queryData['join']['grn_master'] = "grn_master.id = grn_trans.grn_id";
        $queryData['join']['po_trans'] = "po_trans.id = grn_trans.po_trans_id";
        $queryData['join']['item_master'] = "item_master.id = grn_trans.item_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
        $queryData['leftJoin']['po_master'] = "po_master.id = grn_trans.po_id";
        if(!empty($data['item_id'])):$queryData['where']['grn_trans.item_id'] = $data['item_id'];endif;
        if(!empty($data['party_id'])):$queryData['where']['grn_master.party_id'] = $data['party_id']; endif;
        $queryData['customWhere'][] = "grn_master.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['where']['grn_trans.po_id != '] = 0;
        $queryData['where']['grn_trans.po_trans_id != '] = 0;
        if(!empty($data['group_by'])){ $queryData['group_by'][] = $data['group_by']; }
       
		$result = $this->rows($queryData);
	   	return $result;
    }

    public function getVendorRating($data){
        $queryData['tableName'] = "prc_log";
		$queryData['select'] = "prc_log.*,outsource.delivery_date,item_master.item_name,prc_master.item_id,party_master.party_name,outsource.ch_number,outsource.ch_date,process_master.process_name";

        if(!empty($data['reportData'])){
            $queryData['select'] .=',SUM(prc_log.qty) as grnQty,SUM(prc_log.rej_found) as rejFound,GROUP_CONCAT(prc_master.item_id SEPARATOR "~") AS itemId';
        }
		$queryData['leftJoin']['prc_challan_request'] = "prc_challan_request.id = prc_log.ref_trans_id";
		$queryData['leftJoin']['outsource'] = "outsource.id = prc_challan_request.challan_id";
		$queryData['leftJoin']['prc_master'] = "prc_master.id = prc_log.prc_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = outsource.party_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = prc_log.process_id";

        $queryData['where']['prc_log.process_by'] = 3;
        $queryData['where']['prc_log.trans_type'] = 1;

        $queryData['customWhere'][] = "prc_log.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        if(!empty($data['item_id'])):$queryData['where']['prc_master.item_id'] = $data['item_id'];endif;
        if(!empty($data['party_id'])):$queryData['where']['prc_log.processor_id'] = $data['party_id']; endif;
        if(!empty($data['group_by'])){ $queryData['group_by'][] = $data['group_by']; }

		$result = $this->rows($queryData);
        return $result;
    }
	
    public function getRejectionMonitoring($data){
		$queryData = array();
		$queryData['tableName'] = "rejection_log";
        $queryData['select'] = "rejection_log.*,prc_log.process_by,prc_log.trans_date,prc_log.qty as ok_qty,prc_master.prc_number,item_master.item_code, item_master.item_name,process_master.process_name,shift_master.shift_name,employee_master.emp_name,rejection_comment.remark as rr_comment, process.process_name as rejction_stage, IF(prc_log.process_by = 1, machine.item_code,
        IF(prc_log.process_by = 2,department_master.name, IF(prc_log.process_by = 3,party_master.party_name,''))) as processor_name";
		
		$queryData['select'] .= ", (CASE WHEN rejection_log.rr_by = 0 THEN rr_machine.item_code ELSE party.party_name END) as rr_processor";
		$queryData['select'] .= ", (CASE WHEN rejection_log.rr_by = 0 THEN rr_emp.emp_name ELSE rejection_log.in_ch_no END) as rr_operator";

		$queryData['leftJoin']['prc_log'] = 'prc_log.id = rejection_log.log_id';
		$queryData['leftJoin']['prc_master'] = 'prc_master.id = rejection_log.prc_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = prc_master.item_id';
		$queryData['leftJoin']['process_master'] = 'prc_log.process_id = process_master.id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = prc_log.shift_id';        
        $queryData['leftJoin']['item_master machine'] = "machine.id = prc_log.processor_id";
        $queryData['leftJoin']['department_master'] = "department_master.id = prc_log.processor_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = prc_log.processor_id";
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = prc_log.operator_id';
		$queryData['leftJoin']['rejection_comment'] = 'rejection_comment.id = rejection_log.rr_reason';
        $queryData['leftJoin']['process_master process'] = 'rejection_log.rr_stage = process.id';
        $queryData['leftJoin']['party_master party'] = "party.id = rejection_log.rr_by";
		
        $queryData['leftJoin']['item_master rr_machine'] = "rr_machine.id = rejection_log.machine_id";
		$queryData['leftJoin']['employee_master rr_emp'] = 'rr_emp.id = rejection_log.operator_id';

		$queryData['customWhere'][] = "DATE(rejection_log.created_at) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		if (!empty($data['item_id'])) { $queryData['where']['prc_master.item_id'] = $data['item_id']; }
		if (!empty($data['process_id'])) { $queryData['where']['rejection_log.rr_stage'] = $data['process_id']; }

		if(!empty($data['operator_id'])) { 
            $queryData['where']['rejection_log.operator_id'] = $data['operator_id']; 
        }
		
        if ($data['rr_by']  != 'ALL') { 
            if (empty($data['rr_by'])) { 
                $queryData['where']['rejection_log.rr_by'] = $data['rr_by'];
                if(!empty($data['mc_vn_id'])) { 
                    $queryData['where']['rejection_log.machine_id'] = $data['mc_vn_id']; 
                }
            }else{
                if(!empty($data['mc_vn_id'])) { 
                    $queryData['where']['rejection_log.rr_by'] = $data['mc_vn_id']; 
                }
                $queryData['where']['rejection_log.rr_by >'] = 0;
            }
        }
		
		$queryData['where']['rejection_log.decision_type'] = '1';
		$queryData['where']['rejection_comment.type'] = '1';		
		$queryData['order_by']['rejection_comment.created_at'] = 'DESC';

		return $this->rows($queryData);
	}

    public function getRejectionSummary($data){
        $queryData = array();
        $queryData['tableName'] = "prc_log";
        $queryData['select'] = "prc_log.*,prc_master.item_id,item_master.item_code,SUM(prc_log.rej_qty) as rej_qty,IFNULL(SUM(prc_log.qty),0) as production_qty";

        $queryData['leftJoin']['prc_master'] = "prc_log.prc_id = prc_master.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
        $queryData['where']['prc_log.trans_type'] = 1;
		$queryData['customWhere'][] = "prc_log.trans_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";

        if(!empty($data['item_id'])){
            $queryData['where']['prc_master.item_id'] = $data['item_id'];
        }
        if(!empty($data['process_id'])){
            $queryData['where']['prc_log.process_id'] = $data['process_id'];
        }
        $queryData['group_by'][] = "prc_master.item_id";
        $result = $this->rows($queryData);
        return $result;
    }

	/* Created By @Raj 27-05-2025 */
	public function getSupplierEvalution($data){
		$queryData['tableName'] = $this->grnTrans;
        $queryData['select'] = "grn_trans.*, grn_master.party_id,grn_master.trans_date,po_trans.delivery_date,grn_master.trans_number,grn_master.doc_no as inv_no,grn_master.doc_date as inv_date,SUM(grn_trans.qty) as grnQty,SUM(grn_trans.ok_qty) as okQty";
		
		$queryData['select'] .= ',SUM(CASE 
			WHEN grn_master.trans_date <= po_trans.delivery_date THEN grn_trans.qty 
			ELSE 0 
		END) AS delivered_qty';
		
		$queryData['join']['grn_master'] = "grn_master.id = grn_trans.grn_id";
        $queryData['join']['po_trans'] = "po_trans.id = grn_trans.po_trans_id";
		if(!empty($data['from_date']) && !empty($data['to_date'])){
			$queryData['customWhere'][] = "grn_master.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		}
        $queryData['where']['grn_trans.po_id != '] = 0;
        $queryData['where']['grn_trans.po_trans_id != '] = 0;
		if(!empty($data['party_id'])):$queryData['where']['grn_master.party_id'] = $data['party_id']; endif;
        if(!empty($data['group_by'])){ $queryData['group_by'][] = $data['group_by']; }
       
		$result = $this->row($queryData);
	   	return $result;
	}
	
	public function getVendorEvalution($data){
        $queryData['tableName'] = "prc_log";
		$queryData['select'] = "prc_log.*,outsource.delivery_date,prc_master.item_id,party_master.party_name,outsource.ch_number,outsource.ch_date,process_master.process_name,SUM(prc_log.qty) as grnQty,SUM(prc_log.rej_found) as rejFound";
		
		$queryData['select'] .= ',SUM(CASE 
			WHEN prc_log.trans_date <= outsource.delivery_date THEN prc_log.qty 
			ELSE 0 
		END) AS delivered_qty';
        
		$queryData['leftJoin']['prc_challan_request'] = "prc_challan_request.id = prc_log.ref_trans_id";
		$queryData['leftJoin']['outsource'] = "outsource.id = prc_challan_request.challan_id";
		$queryData['leftJoin']['prc_master'] = "prc_master.id = prc_log.prc_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = outsource.party_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = prc_log.process_id";

        $queryData['where']['prc_log.process_by'] = 3;
        $queryData['where']['prc_log.trans_type'] = 1;
		if(!empty($data['from_date']) && !empty($data['to_date'])){
			$queryData['customWhere'][] = "prc_log.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		}
        if(!empty($data['party_id'])):$queryData['where']['prc_log.processor_id'] = $data['party_id']; endif;
        if(!empty($data['group_by'])){ $queryData['group_by'][] = $data['group_by']; }

		$result = $this->row($queryData);
        return $result;
    }
}
?>