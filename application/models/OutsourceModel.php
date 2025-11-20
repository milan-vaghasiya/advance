<?php
class OutsourceModel extends MasterModel{

    public function getNextChallanNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'outsource';
        $queryData['select'] = "MAX(ch_no) as ch_no ";	
		$queryData['where']['outsource.ch_date >='] = $this->startYearDate;
		$queryData['where']['outsource.ch_date <='] = $this->endYearDate;

		$ch_no = $this->specificRow($queryData)->ch_no;
		$ch_no = $ch_no + 1;
		return $ch_no;
    }

	public function getDTRows($data){
        $data['tableName'] = "prc_challan_request";
		$data['select'] = "prc_challan_request.*,outsource.id as out_id,outsource.ch_number,outsource.ch_date,outsource.party_id,prc_master.prc_date,prc_master.prc_number,prc_master.batch_no,process_master.process_name,item_master.item_name,IFNULL(receiveLog.ok_qty,0) as ok_qty,IFNULL(receiveLog.rej_qty,0) as rej_qty,party_master.party_name,product_process.output_qty,outsource.ewb_status,outsource.eway_bill_no,grn_master.trans_number as grn_number,rmItem.item_name as rm_item";
		$data['leftJoin']['outsource'] = "outsource.id = prc_challan_request.challan_id";
		$data['leftJoin']['prc_master'] = "prc_master.id = prc_challan_request.prc_id";
		$data['leftJoin']['process_master'] = "process_master.id = prc_challan_request.process_id";
		$data['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
		$data['leftJoin']['party_master'] = "party_master.id = outsource.party_id";
        $data['leftJoin']['(SELECT sum(qty) as ok_qty,SUM(rej_found) as rej_qty,process_id,ref_trans_id FROM prc_log WHERE is_delete = 0 AND process_by = 3 GROUP BY process_id,ref_trans_id) as receiveLog'] = "receiveLog.ref_trans_id = prc_challan_request.id AND prc_challan_request.process_id = receiveLog.process_id";
		$data['leftJoin']['product_process'] = "product_process.item_id = prc_master.item_id AND product_process.process_id = prc_challan_request.process_id AND product_process.is_delete = 0 ";
        
		$data['leftJoin']['grn_trans'] = "grn_trans.id = prc_challan_request.jwo_id";
		$data['leftJoin']['grn_master'] = "grn_master.id = grn_trans.grn_id";
		$data['leftJoin']['item_master rmItem'] = "rmItem.id = prc_challan_request.ref_item_id";
        
		if ($data['status'] == 0) :
		    $data['where']['prc_challan_request.prc_id !='] = 0;
            $data['having'][] = "((prc_challan_request.qty - without_process_qty) * product_process.output_qty) > (ok_qty+rej_qty)";
        elseif ($data['status'] == 1) :
		    $data['where']['prc_challan_request.prc_id !='] = 0;
            $data['having'][] = "((prc_challan_request.qty - without_process_qty) * product_process.output_qty) - (ok_qty+rej_qty) <= 0";
        elseif ($data['status'] == 2) :
		    $data['where']['prc_challan_request.prc_id'] = 0;
        endif;
		$data['where']['prc_challan_request.challan_id >'] = 0;
		$data['order_by']['outsource.ch_no'] = 'DESC';
		
        if($data['status'] == 2){
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "DATE_FORMAT(outsource.ch_date,'%d-%m-%Y')";
            $data['searchCol'][] = "outsource.ch_number";
            $data['searchCol'][] = "grn_master.trans_number";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "CONCAT(item_master.item_code,' ',item_master.item_name)";
            $data['searchCol'][] = "prc_challan_request.qty";
            $data['searchCol'][] = "prc_challan_request.price";
            $data['searchCol'][] = "(prc_challan_request.material_value)";
        }else{
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "DATE_FORMAT(outsource.ch_date,'%d-%m-%Y')";
            $data['searchCol'][] = "outsource.ch_number";
            $data['searchCol'][] = "prc_master.prc_number";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "CONCAT(item_master.item_code,' ',item_master.item_name)";
            $data['searchCol'][] = "process_master.process_name";
            $data['searchCol'][] = "prc_master.batch_no";
            $data['searchCol'][] = "prc_challan_request.qty";
            $data['searchCol'][] = "receiveLog.ok_qty";
            $data['searchCol'][] = "receiveLog.rej_qty";
            $data['searchCol'][] = "prc_challan_request.without_process_qty";
            $data['searchCol'][] = "(prc_challan_request.qty - (receiveLog.ok_qty+receiveLog.rej_qty+prc_challan_request.without_process_qty))";
            $data['searchCol'][] = "prc_challan_request.price";
            $data['searchCol'][] = "(prc_challan_request.price * prc_challan_request.qty)";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";

        }

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        
        return $result;
    }
	
    public function save($data){
		try {
			$this->db->trans_begin();
            $ch_prefix = 'VC/'.getYearPrefix('SHORT_YEAR').'/';
            $ch_no = $this->outsource->getNextChallanNo();
            $challanData = [
                'id'=>'',
                'party_id'=>$data['party_id'],
                'ch_date'=>$data['ch_date'],
                'delivery_date'=>$data['delivery_date'],
                'ch_no'=>$ch_no,
                'ch_number'=>$ch_prefix.$ch_no,
                'vehicle_no'=>$data['vehicle_no'],
                'transport_id'=>$data['transport_id'],
                'remark'=>$data['remark']
            ];
            $result = $this->store('outsource',$challanData);
            foreach($data['id'] as $key=>$id){
                $chData = [
                    'id'=>$id,
                    'qty'=>$data['ch_qty'][$key],
                    'challan_type'=>(!empty($data['challan_type']) ? $data['challan_type'] : 1),
                    'price'=>$data['price'][$key],
					'material_value'=>$data['material_value'][$key],
					'material_wt'=>$data['material_wt'][$key],
					'material_price'=>$data['material_price'][$key],
					'pre_process_cost'=>$data['pre_process_cost'][$key],
                    'challan_id'=>$result['id'],
                ];
                $this->store('prc_challan_request',$chData, 'Challan Request');
            }
			
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Record Updated Successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function delete($id){
        try {
			$this->db->trans_begin();
            $chData = $this->sop->getChallanRequestData(['challan_id'=>$id,'challan_receive'=>1]);
            foreach($chData as $row){
                if(($row->ok_qty+$row->rej_qty) > 0){
                    return ['status'=>0,'message'=>'You can not delete this Challan'];
                }
                $this->store("prc_challan_request",['id'=>$row->id,'challan_id'=>0,'qty'=>$row->old_qty]);
            }
			$result = $this->trash('outsource', ['id'=>$id], 'Challan');
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function getOutSourceData($data){
		$data['tableName'] = 'outsource';
		$data['select'] = 'outsource.*,employee_master.emp_name,party_master.party_name,party_master.party_address,party_master.gstin, transport_master.transport_name,party_master.party_mobile';
		$data['leftJoin']['employee_master'] = 'employee_master.id = outsource.created_by';
		$data['leftJoin']['party_master'] = 'party_master.id = outsource.party_id';
		$data['leftJoin']['transport_master'] = 'transport_master.id = outsource.transport_id';
		$data['where']['outsource.id'] = $data['id'];
		return $this->row($data);
	}

	public function getJwbDTRows($data){ 
        $data['tableName'] = 'prc_log';
		$data['select'] = 'prc_log.trans_date, outsource.ch_number as challan_no, party_master.party_name, prc_log.in_challan_no, GROUP_CONCAT(prc_log.id) as log_ids';
		
		$data['leftJoin']['outsource'] = 'outsource.id  = prc_log.ref_id';
		$data['leftJoin']['party_master'] = "party_master.id = outsource.party_id";

		$data['where']['prc_log.trans_type'] = 1;
		$data['where']['prc_log.process_by'] = 3;
		$data['where']['prc_log.qty != '] = 0;

		if(!empty($data['from_date'])) { $data['where']['prc_log.trans_date >='] = $data['from_date']; }
        if(!empty($data['to_date'])) { $data['where']['prc_log.trans_date <='] = $data['to_date']; }
        if(!empty($data['party_id'])) { $data['where']['party_master.id'] = $data['party_id']; }
		
        $data['group_by'][] = 'outsource.party_id,prc_log.trans_date,prc_log.in_challan_no';
		$data['order_by']['prc_log.trans_date'] = "ASC";
		
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
		$data['searchCol'][] = "DATE_FORMAT(prc_log.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "prc_log.in_challan_no";
        $data['searchCol'][] = "outsource.ch_number"; 
        $data['searchCol'][] = "party_master.party_name";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        $result =  $this->pagingRows($data);
	
		return $result;
    }

    public function saveJobWorkBill($data){
        try {
            $this->db->trans_begin();

            $ids = explode(',',$data['ids']);
            foreach($ids as $id){
                $result = $this->edit('prc_log',['id'=>$id],['bill_no'=>$data['bill_no']]);
            }

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;

        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
	
}
?>