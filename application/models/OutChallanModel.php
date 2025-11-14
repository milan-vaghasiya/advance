<?php
class OutChallanModel extends MasterModel{
    private $ch_master = "in_out_challan";
    private $ch_trans = "in_out_challan_trans";
    private $instrumentsReturn = "instruments_return";

    public function nextTransNo($challan_type){
        $data['tableName'] = $this->ch_master;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['challan_type'] = $challan_type;
        $data['where']['trans_date >= '] = $this->startYearDate;
        $data['where']['trans_date <= '] = $this->endYearDate;
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;        
		return $nextTransNo;
    }
	
	public function getDTRows($data){
        $data['tableName'] = $this->ch_trans;
        $data['select'] = "in_out_challan_trans.id as out_challan_trans_id,in_out_challan_trans.qty,in_out_challan_trans.receive_qty,in_out_challan_trans.item_remark,party_master.party_name,item_master.item_name,in_out_challan.trans_number,in_out_challan.trans_date,in_out_challan.remark,in_out_challan.id";
        $data['leftJoin']['in_out_challan'] = "in_out_challan.id = in_out_challan_trans.trans_main_id";
        $data['leftJoin']['party_master'] = "party_master.id = in_out_challan.party_id";
        $data['leftJoin']['item_master'] = "item_master.id = in_out_challan_trans.item_id";

        $data['where']['in_out_challan.challan_type'] = 2;
        $data['where']['in_out_challan.trans_date >='] = $this->startYearDate;
        $data['where']['in_out_challan.trans_date <='] = $this->endYearDate;
        
		if(!empty($data['status']) && $data['status'] == 1){
			$data['customWhere'][] = '((in_out_challan_trans.qty - in_out_challan_trans.receive_qty) != 0 AND in_out_challan_trans.is_returnable="YES")';
		}else{
			$data['where']['in_out_challan_trans.is_returnable'] = "YES";
			$data['customWhere'][] = '(in_out_challan_trans.qty - in_out_challan_trans.receive_qty) = 0';
		}

		$data['group_by'][] = "in_out_challan_trans.id";
        $data['order_by']['in_out_challan.trans_date'] = "DESC";
        $data['order_by']['in_out_challan.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "in_out_challan.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(in_out_challan.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "in_out_challan_trans.qty";
        $data['searchCol'][] = "in_out_challan.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }
    
    public function getDTRows_old($data){
        $data['tableName'] = $this->ch_master;
        $data['select'] = "in_out_challan.*,party_master.party_name,chTrans.qty,chTrans.receive_qty";
        $data['leftJoin']['party_master'] = "party_master.id = in_out_challan.party_id";
        $data['leftJoin']['(SELECT SUM(qty) as qty,SUM(receive_qty) as receive_qty,trans_main_id FROM in_out_challan_trans WHERE is_delete = 0 AND is_returnable = "YES" GROUP BY trans_main_id) as chTrans'] = "chTrans.trans_main_id = in_out_challan.id";

        $data['where']['in_out_challan.challan_type'] = 2;
        $data['where']['in_out_challan.trans_date >='] = $this->startYearDate;
        $data['where']['in_out_challan.trans_date <='] = $this->endYearDate;

        $data['order_by']['in_out_challan.trans_date'] = "DESC";
        $data['order_by']['in_out_challan.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "in_out_challan.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(in_out_challan.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "in_out_challan.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_number'] = "Challan No. is duplicate.";
                $result = ['status'=>0,'message'=>$errorMessage];
            endif;

            if(!empty($data['id'])):
                $this->trash($this->ch_trans,['trans_main_id'=>$data['id']]);
            endif;
            
            $itemData = $data['itemData']; unset($data['itemData']);		

            $result = $this->store($this->ch_master, $data, 'Out Challan');

            foreach($itemData as $row):
                $row['trans_main_id'] = $result['id'];
                $row['is_delete'] = 0;
                $this->store($this->ch_trans,$row);
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function checkDuplicate($data){
        $queryData['tableName'] = $this->ch_master;
        $queryData['where']['trans_number'] = $data['trans_number'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getOutChallan($data){
        $queryData = array();
        $queryData['tableName'] = $this->ch_master;
        $queryData['select'] = "in_out_challan.*,prepare.emp_name as prepared_by";
        $queryData['leftJoin']['employee_master prepare'] = "prepare.id = in_out_challan.created_by";

        $queryData['where']['in_out_challan.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
            $result->itemList = $this->getOutChallanItems(['trans_main_id'=>$data['id']]);
        endif;
        return $result;
    }
    
    public function getOutChallanItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->ch_trans;
        $queryData['select'] = "in_out_challan_trans.*,item_master.item_name,item_master.item_code";
        $queryData['leftJoin']['item_master'] = "item_master.id = in_out_challan_trans.item_id";

        if(!empty($data['trans_main_id'])) { $queryData['where']['in_out_challan_trans.trans_main_id'] = $data['trans_main_id']; }

        if(!empty($data['id'])) { $queryData['where']['in_out_challan_trans.id'] = $data['id']; }
        
        if(!empty($data['is_returnable'])) { $queryData['where']['in_out_challan_trans.is_returnable'] = $data['is_returnable']; }
        
        if(!empty($data['customWhere'])) { $queryData['customWhere'][] = $data['customWhere']; }

        if(!empty($data['single_row'])):
            $result = $this->row($queryData);
        else:
            $result = $this->rows($queryData);
        endif;

        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $this->trash($this->instrumentsReturn, ['ref_id'=>$id]);
            $this->trash($this->ch_trans, ['trans_main_id'=>$id]);
            $result = $this->trash($this->ch_master, ['id'=>$id], 'Out Challan');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getReceiveItemTrans($data){
        $queryData['tableName'] = $this->instrumentsReturn;
        $queryData['select'] = 'instruments_return.*,item_master.item_name';
        $queryData['leftJoin']['in_out_challan_trans'] = "in_out_challan_trans.id = instruments_return.challan_trans_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = in_out_challan_trans.item_id";
        $queryData['where']['instruments_return.ref_id'] = $data['ref_id'];
        $result = $this->rows($queryData);
        return $result;
    }

    public function saveReceiveItem($data){ 
        try{
            $this->db->trans_begin();

            $result = $this->store($this->instrumentsReturn, $data, 'Receive Item');

            $setData = Array();
            $setData['tableName'] = $this->ch_trans;
            $setData['where']['id'] = $data['challan_trans_id'];
            $setData['set']['receive_qty'] = 'receive_qty, + '.$data['receive_qty'];
            $this->setValue($setData);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function deleteReceiveItem($data){ 
        try{
            $this->db->trans_begin();
            
            $result = $this->trash($this->instrumentsReturn, ['id'=>$data['id']], 'Receive Item');

            $setData = Array();
            $setData['tableName'] = $this->ch_trans;
            $setData['where']['id'] = $data['challan_trans_id'];
            $setData['set']['receive_qty'] = 'receive_qty, - '.floatval($data['receive_qty']);
            $this->setValue($setData);

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