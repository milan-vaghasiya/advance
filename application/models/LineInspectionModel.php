<?php
class LineInspectionModel extends MasterModel{
    private $materialMaster = "material_master";
    private $productionInspection = "production_inspection";

    public function getDTRows($data){
        $data['tableName'] = 'prc_movement';
		$data['select'] = 'prc_master.id AS prc_id,process_master.id AS process_id,process_master.process_name,item_master.item_code,item_master.item_name,prc_master.prc_number,prc_master.prc_date, prc_master.batch_no,prc_master.prc_qty';

		$data['leftJoin']['prc_master'] = 'prc_master.id = prc_movement.prc_id';
		$data['leftJoin']['item_master'] = 'prc_master.item_id = item_master.id';
		$data['leftJoin']['process_master'] = 'process_master.id = prc_movement.next_process_id';

        $data['where']['process_master.line_inspection'] = 1;
        $data['group_by'][] = 'prc_movement.prc_id,prc_movement.next_process_id';
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "prc_master.prc_date"; 
        $data['searchCol'][] = "prc_master.batch_no";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.item_name";
		$data['searchCol'][] = "prc_master.prc_qty";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getIPRDTRows($data){
        $data['tableName'] = "production_inspection";
		$data['select'] = "production_inspection.*,process_master.process_name,prc_master.prc_number,prc_master.prc_date,prc_master.batch_no,item_master.item_name,employee_master.emp_name,machine.item_name as machine_name";
		$data['leftJoin']['process_master'] = "process_master.id = production_inspection.process_id";
        $data['leftJoin']['prc_master'] = "prc_master.id = production_inspection.prc_id";
        $data['leftJoin']['item_master'] = "item_master.id = production_inspection.item_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = production_inspection.operator_id";
        $data['leftJoin']['item_master machine'] = "machine.id = production_inspection.machine_id";
        $data['where_in']['production_inspection.report_type'] = [1,4,5];

		if (!empty($data['insp_type']) && $data['insp_type'] == 1) { $data['where']['production_inspection.report_type'] = $data['insp_type']; }
        elseif (!empty($data['insp_type']) && $data['insp_type'] == 4) { $data['where_in']['production_inspection.report_type'] = [4,5]; }

		$data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(production_inspection.insp_date,'%d-%m-%Y')";
        $data['searchCol'][] = "DATE_FORMAT(production_inspection.insp_time,'%H:%i:%s')"; 
        $data['searchCol'][] = "IF(production_inspection.report_type IN (4,5), 'SAR', 'IPR')"; 
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "DATE_FORMAT(prc_master.prc_date,'%d-%m-%Y')"; 
        $data['searchCol'][] = "prc_master.batch_no";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "machine.item_name";
        $data['searchCol'][] = "production_inspection.sampling_qty";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function saveLineInspection($data){
		try{
            $this->db->trans_begin();

    		$result = $this->store($this->productionInspection,$data,'Line Inspection');
			
			if((!empty($data['report_type']) && $data['report_type'] == 5) && !empty($data['ref_id'])){
                $this->edit($this->productionInspection, ['id'=>$data['ref_id']], ['ref_id'=>$result['id']]);
            }

    		if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}

    public function getLineInspectData($data) {
        $queryData = array();
        $queryData['tableName'] = $this->productionInspection;
        $queryData['select'] = "production_inspection.*, process_master.process_name, prc_master.prc_number, prc_master.prc_date, item_master.item_name, employee_master.emp_name, machine.item_name as machine_name, ecn_master.rev_no, ecn_master.cust_rev_no, ecn_master.drw_no, emp.emp_name as created_name";
		$queryData['leftJoin']['process_master'] = "process_master.id = production_inspection.process_id";
        $queryData['leftJoin']['prc_master'] = "prc_master.id = production_inspection.prc_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = production_inspection.item_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = production_inspection.operator_id";
        $queryData['leftJoin']['item_master machine'] = "machine.id = production_inspection.machine_id";
		$queryData['leftJoin']['(SELECT drw_no,cust_rev_date,cust_rev_no,rev_no,rev_date,item_id FROM ecn_master WHERE is_delete=0 AND status=2 GROUP BY item_id ORDER BY ecn_date DESC) as ecn_master'] = "ecn_master.item_id = production_inspection.item_id";
		$queryData['leftJoin']['employee_master emp'] = "emp.id = production_inspection.created_by";
        
		if(!empty($data['sarData'])){
            $queryData['select'] .= ",pi.observation_sample as last_piece_sample";            
            $queryData['leftJoin']['production_inspection pi'] = "pi.ref_id = production_inspection.id";
        }

		// if(!empty($data['id'])){ $queryData['where']['production_inspection.id'] = $data['id']; }
        if(!empty($data['prc_id'])){ $queryData['where']['production_inspection.prc_id'] = $data['prc_id']; }
        if(!empty($data['process_id'])){ $queryData['where']['production_inspection.process_id'] = $data['process_id']; }
        if(!empty($data['insp_date'])){ $queryData['where']['production_inspection.insp_date'] = $data['insp_date']; }
		
        if(!empty($data['id'])){
			$queryData['where']['production_inspection.id'] = $data['id']; 
			return $this->row($queryData);
		}else{ 
			return $this->rows($queryData); 
		}
        // return $this->row($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
			$result = $this->trash($this->productionInspection,['id'=>$id],'Line Inspection');
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
    }
}
?>