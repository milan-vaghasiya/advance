<?php
class JobworkOrderModel extends MasterModel{

    private $transDetails = "trans_details";

    public function getDTRows($data){
        $data['tableName'] = 'jobwork_order_trans';
        $data['select'] = "jobwork_order_trans.*,party_master.party_code,party_master.party_name,GROUP_CONCAT(process_master.process_name) AS process_name,jobwork_order.order_date,jobwork_order.trans_number,jobwork_order.order_status,item_master.item_code,item_master.item_name";
        $data['leftJoin']['jobwork_order'] = "jobwork_order.id = jobwork_order_trans.jwo_id";
        $data['leftJoin']['party_master'] = "party_master.id = jobwork_order.vendor_id";
        $data['leftJoin']['item_master'] = "item_master.id = jobwork_order_trans.item_id";
        $data['leftJoin']['process_master'] = "FIND_IN_SET(process_master.id,jobwork_order_trans.process_id) > 0";

        $data['where']['jobwork_order_trans.trans_status'] = $data['trans_status'];

        $data['group_by'][] = 'jobwork_order_trans.id';
        
		$data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(jobwork_order.order_date,'%d-%m-%Y')";
        $data['searchCol'][] = "jobwork_order.trans_number";
        $data['searchCol'][] = "party_master.party_name"; 
        $data['searchCol'][] = "CONCAT(item_master.item_code,item_master.item_name)"; 
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "jobwork_order_trans.rate_per_unit";
        $data['searchCol'][] = "jobwork_order_trans.rate";
        $data['searchCol'][] = "jobwork_order_trans.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }

    public function getNextJwoNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'jobwork_order';
        $queryData['select'] = "MAX(trans_no) as trans_no ";	
		$queryData['where']['jobwork_order.order_date >='] = $this->startYearDate;
		$queryData['where']['jobwork_order.order_date <='] = $this->endYearDate;

		$trans_no = $this->specificRow($queryData)->trans_no;
		$trans_no = $trans_no + 1;
		return $trans_no;
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            if(!empty($data['id'])):
                $this->trash('jobwork_order_trans',['jwo_id'=>$data['id'],'trans_status'=>1]);
            else:
                $trans_prefix = 'JWO/'.getYearPrefix('SHORT_YEAR').'/';
                $data['trans_no'] = $this->jobworkOrder->getNextJwoNo();
                $data['trans_number'] = $trans_prefix.$data['trans_no'];
            endif;
            
            $itemData = $data['itemData'];
            $data['terms_conditions'] = (!empty($data['conditions']))?$data['conditions']:'';

            unset($data['itemData'],$data['conditions']);		
			
			if(!empty($data['ref_id'])){
				$this->edit("jobwork_order",['id'=>$data['ref_id']],['order_status'=>2]);
			}
			
            $result = $this->store('jobwork_order',$data,'Jobwork Order');


            foreach($itemData as $row):
				if(!empty($row['ref_id'])){
					$this->edit("jobwork_order_trans",['id'=>$row['ref_id']],['trans_status'=>2]);
				}
				
                $row['jwo_id'] = $result['id'];
                $row['is_delete'] = 0;
                $this->store('jobwork_order_trans',$row);
            endforeach;
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

	public function getJobworkOrderData($param = []){
        $queryData['tableName'] = 'jobwork_order';
        $queryData['select'] = 'jobwork_order.*,party_master.party_name,party_master.party_address,party_master.gstin';
		$queryData['leftJoin']['party_master'] = "party_master.id = jobwork_order.vendor_id";
        if(!empty($param['id'])){ $queryData['where']['jobwork_order.id'] = $param['id']; }
        if(!empty($param['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }
	
    public function delete($data = array()){
        try{
            $this->db->trans_begin();
			
            $result = $this->trash('jobwork_order_trans',['id'=>$data['id'],'trans_status'=>1],'Jobwork Order');
			$getJobWorkData = $this->getJobworkOrderItems(['jwo_id'=>$data['jwo_id']]);
			
			if(empty($getJobWorkData)){			
				$this->trash('jobwork_order',['id'=>$data['jwo_id']],'Jobwork Order');
			}

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function approveOrder($data) {
        try{
            $this->db->trans_begin();

            $result = $this->store('jobwork_order_trans', ['id'=> $data['id'], 'trans_status' => 3]);
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Jobwork Order approved successfully.'];
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	    
    }

    public function shortCloseOrder($data) {
        try{
            $this->db->trans_begin();

            $result = $this->store('jobwork_order_trans', ['id'=> $data['id'], 'trans_status'=>2]);
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Jobwork Order Closed successfully.'];
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	    
    }

    public function getJobworkOrderItems($param = []){
        $data['tableName'] = 'jobwork_order_trans';
        $data['select'] = "jobwork_order_trans.*,party_master.party_code,party_master.party_name,GROUP_CONCAT(process_master.process_name) AS process_name,jobwork_order.order_date,jobwork_order.trans_number,item_master.item_code,item_master.item_name";
        $data['leftJoin']['jobwork_order'] = "jobwork_order.id = jobwork_order_trans.jwo_id";
        $data['leftJoin']['party_master'] = "party_master.id = jobwork_order.vendor_id";
        $data['leftJoin']['item_master'] = "item_master.id = jobwork_order_trans.item_id";
        $data['leftJoin']['process_master'] = "FIND_IN_SET(process_master.id,jobwork_order_trans.process_id) > 0";

        if(!empty($param['id'])){ $data['where']['jobwork_order_trans.id'] = $param['id']; }
        if(!empty($param['jwo_id'])){ $data['where']['jobwork_order_trans.jwo_id'] = $param['jwo_id']; }
        if(!empty($param['trans_status'])){ $data['where_in']['jobwork_order_trans.trans_status'] = $param['trans_status']; }
        if(!empty($param['vendor_id'])){ $data['where']['jobwork_order.vendor_id'] = $param['vendor_id']; }
        if(!empty($param['process_id'])){ $data['where']['jobwork_order_trans.process_id'] = $param['process_id']; }
        if(!empty($param['item_id'])){ $data['where']['jobwork_order_trans.item_id'] = $param['item_id']; }
        if(!empty($param['customWhere'])){
            $data['customWhere'][] = $param['customWhere'];
        }
        $data['group_by'][] = 'jobwork_order_trans.id';
        if(!empty($param['single_row'])){
            return $this->row($data);
        }else{
            return $this->rows($data);
        }

    }
}
?>