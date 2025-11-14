<?php
class VendorInspectionModel extends MasterModel{
    private $prcLog = "prc_log";
    private $productionInspection = "production_inspection";

    public function getDTRows($data){
        $data['tableName'] = $this->prcLog;
		
		$data['select'] = 'prc_log.*,employee_master.emp_name,shift_master.shift_name,prc_log_detail.remark,prc_detail.process_ids,prc_master.item_id,prc_master.prc_number,prc_master.status, item_master.item_name, item_master.item_code, process_master.process_name,prc_log_detail.start_time,prc_log_detail.end_time';
		$data['select'] .=',outsource.ch_number as challan_no,party_master.party_name,party_master.id as vendor_id,production_inspection.inspection_file';
		
		$data['leftJoin']['outsource'] = 'outsource.id  = prc_log.ref_id';
		$data['leftJoin']['party_master'] = "party_master.id = outsource.party_id";
		//$data['leftJoin']['prc_challan_request'] = "prc_challan_request.id = prc_log.prc_id";
		$data['leftJoin']['prc_master'] = "prc_master.id = prc_log.prc_id";
		$data['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
		$data['leftJoin']['process_master'] = "process_master.id = prc_log.process_id";
		
		$data['leftJoin']['employee_master'] = "employee_master.id = prc_log.operator_id";
        $data['leftJoin']['shift_master'] = "shift_master.id = prc_log.shift_id";
        $data['leftJoin']['prc_log_detail'] = "prc_log_detail.log_id = prc_log.id AND prc_log_detail.is_delete = 0";
        $data['leftJoin']['prc_detail'] = "prc_detail.prc_id = prc_log.prc_id";
        $data['leftJoin']['production_inspection'] = "prc_log.id = production_inspection.ref_id";
		
		$data['where']['prc_log.rqc_status'] = $data['status'];
		$data['where']['prc_log.trans_type'] = 1;
		$data['where']['prc_log.process_by'] = 3;
		$data['where']['prc_log.qty != '] = 0;
		
		$data['where']['prc_log.trans_date >='] = $this->startYearDate;
		$data['where']['prc_log.trans_date <='] = $this->endYearDate;
		
		$data['order_by']['prc_log.trans_date'] = "ASC";
		
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "outsource.ch_number"; 
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "prc_log.in_challan_no";
        $data['searchCol'][] = "prc_log.qty";
		$data['searchCol'][] = "DATE_FORMAT(prc_log.trans_date,'%d-%m-%Y')";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function saveVendorInspection($data){
		try{
            $this->db->trans_begin();

    		$result = $this->store($this->productionInspection,$data,'Vendor Inspection');
			
			if(!empty($data['ref_id'])):
				$this->edit($this->prcLog,['id'=>$data['ref_id']],['rqc_status'=>1],"");
			endif;

    		if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}
	
	public function delete($id){
        try{
            $this->db->trans_begin();
			
			if(!empty($id)):
				$result = $this->trash($this->productionInspection,['ref_id'=>$id,'report_type'=>3],'Vendor Inspection');
				
				$this->edit($this->prcLog,['id'=>$id],['rqc_status'=>0],"");
			endif;
			
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
    }

    public function getVendorInspectData($data) {
        $queryData = array();
        $queryData['tableName'] = $this->productionInspection;
        $queryData['select'] = "production_inspection.*,process_master.process_name,prc_master.prc_number,prc_master.prc_date,item_master.item_name,,employee_master.emp_name,ecn_master.rev_no, ecn_master.cust_rev_no, ecn_master.drw_no,outsource.ch_number as challan_no";
		$queryData['leftJoin']['process_master'] = "process_master.id = production_inspection.process_id";
        $queryData['leftJoin']['prc_master'] = "prc_master.id = production_inspection.prc_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = production_inspection.item_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = production_inspection.operator_id";
        $queryData['leftJoin']['prc_log'] = "prc_log.id = production_inspection.ref_id";
		$queryData['leftJoin']['outsource'] = 'outsource.id  = prc_log.ref_id';
		$queryData['leftJoin']['(SELECT drw_no,cust_rev_date,cust_rev_no,rev_no,rev_date,item_id FROM ecn_master WHERE is_delete=0 AND status=2 GROUP BY item_id ORDER BY ecn_date DESC) as ecn_master'] = "ecn_master.item_id = production_inspection.item_id";
		
		if(!empty($data['id'])){ $queryData['where']['production_inspection.id'] = $data['id']; }
		if(!empty($data['ref_id'])){ $queryData['where']['production_inspection.ref_id'] = $data['ref_id']; }
		if(!empty($data['report_type'])){ $queryData['where']['production_inspection.report_type'] = $data['report_type']; }
        if(!empty($data['prc_id'])){ $queryData['where']['production_inspection.prc_id'] = $data['prc_id']; }
        if(!empty($data['process_id'])){ $queryData['where']['production_inspection.process_id'] = $data['process_id']; }
        if(!empty($data['insp_date'])){ $queryData['where']['production_inspection.insp_date'] = $data['insp_date']; }

        if(empty($data['single_row'])){
			return $this->row($queryData);
		}else{ 
			return $this->rows($queryData); 
		}
    }
}
?>