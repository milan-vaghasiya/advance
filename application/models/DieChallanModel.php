<?php
class DieChallanModel extends MasterModel{
    private $dieChallan = "die_challan";
    private $dieChallanTrans = "die_challan_trans";

    public function nextTransNo(){
        $data['tableName'] = $this->dieChallan;
        $data['select'] = "MAX(trans_no) as trans_no";
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;        
		return $nextTransNo;
    }
    
	public function getDTRows($data){
        $data['tableName'] = $this->dieChallanTrans;
        $data['select'] = "die_challan_trans.*,die_challan.trans_number,die_challan.trans_date,die_challan.challan_type,(CASE WHEN die_challan.challan_type = 2 THEN 'Vendor Issue' ELSE 'In-House Issue' END) as challan_type_lbl,item_master.item_name,item_master.item_code,(CASE WHEN die_challan.challan_type = 2 THEN party_master.party_name WHEN die_challan.challan_type = 1 AND die_challan.party_id = 0 THEN 'In-House' ELSE employee_master.emp_name END) as issue_to,prc_master.prc_number,die_master.item_code as die_code";

        $data['leftJoin']['die_challan'] = "die_challan.id = die_challan_trans.challan_id";
        $data['leftJoin']['item_master'] = "die_challan_trans.item_id = item_master.id";
        $data['leftJoin']['party_master'] = "party_master.id = die_challan.party_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = die_challan.party_id";
        $data['leftJoin']['prc_master'] = "prc_master.id = die_challan_trans.prc_id";
        $data['leftJoin']['die_master'] = "die_master.id = die_challan_trans.die_id";
        
        //$data['where']['die_challan.challan_type'] = $data['challan_type'];
		$data['where']['die_challan_trans.trans_status'] = $data['status'];  
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "die_challan.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(die_challan.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "IF(die_challan.challan_type = 2, party_master.party_name, IF(die_challan.challan_type = 1 AND die_challan.party_id = 0, 'In-House', employee_master.emp_name))";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "prc_master.prc_number"; 
        $data['searchCol'][] = "die_challan_trans.item_remark";

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
                $transData = $this->getDieChallanItems(['id'=>$data['id']]); // Get Old Transaction
                $this->trash($this->dieChallanTrans,['challan_id'=>$data['id']]); // Reverse Challan Transaction
                foreach($transData AS $row){
                    $this->edit("die_master",['fg_id'=>$row->item_id,'set_no'=>$row->die_set_no],['status'=>1]); // Reverse Die Status
                }
            endif;
            
            $itemData = $data['itemData']; unset($data['itemData']);		

            $result = $this->store($this->dieChallan,$data,'Die Challan'); // Save Challan Master

            foreach($itemData as $row):
                $row['challan_id'] = $result['id'];
                $row['is_delete'] = 0;
                $this->store($this->dieChallanTrans,$row); // Save Challan Transaction

                $this->edit("die_master",['fg_id'=>$row['item_id'],'set_no'=>$row['die_set_no']],['status'=>2]); // Update Die status to issued
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
        $queryData['tableName'] = $this->dieChallan;
        $queryData['where']['trans_number'] = $data['trans_number'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

	public function getDieChallan($data){
        $queryData = array();
        $queryData['tableName'] = $this->dieChallan;
        $queryData['select'] = "die_challan.*,(CASE WHEN die_challan.challan_type = 2 THEN party_master.party_name WHEN die_challan.challan_type = 1 AND die_challan.party_id = 0 THEN 'In-House' ELSE employee_master.emp_name END) as issue_to,emp.emp_name as created_by";
        $queryData['leftJoin']['party_master'] = "party_master.id = die_challan.party_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = die_challan.party_id";
        $queryData['leftJoin']['employee_master emp'] = "emp.id = die_challan.created_by";

        $queryData['where']['die_challan.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
            $result->itemList = $this->getDieChallanItems($data);
        endif;
        return $result;
    }

    public function getDieChallanItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->dieChallanTrans;
		$queryData['select'] = "die_challan_trans.*, die_challan.challan_type, item_master.item_name, item_master.item_code, prc_master.prc_number, die_master.item_code as die_code, item_category.category_name";
        $queryData['leftJoin']['item_master'] = "item_master.id = die_challan_trans.item_id";
        $queryData['leftJoin']['die_master'] = "die_master.id = die_challan_trans.die_id";
        $queryData['leftJoin']['item_category'] = 'item_category.id = die_master.category_id';
        $queryData['leftJoin']['prc_master'] = "prc_master.id = die_challan_trans.prc_id";
        $queryData['leftJoin']['die_challan'] = "die_challan.id = die_challan_trans.challan_id";
        
        if(!empty($data['id'])) { $queryData['where']['die_challan_trans.challan_id'] = $data['id']; }

        if(!empty($data['trans_id'])) { $queryData['where']['die_challan_trans.id'] = $data['trans_id']; }

        if(isset($data['trans_status'])) { $queryData['where']['die_challan_trans.trans_status'] = $data['trans_status']; }

        if(isset($data['item_id'])) { $queryData['where']['die_challan_trans.item_id'] = $data['item_id']; }

        if(isset($data['prc_id'])) { $queryData['where']['die_challan_trans.prc_id'] = $data['prc_id']; }

        if(isset($data['challan_type'])) { $queryData['where']['die_challan.challan_type'] = $data['challan_type']; }

        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }

    public function getDieVolume($data = []){
        $queryData = array();
        $queryData['tableName'] = $this->dieChallanTrans;
        $queryData['select'] = "SUM(die_challan_trans.volume) AS total_volume";
        $queryData['leftJoin']['die_challan'] = "die_challan.id = die_challan_trans.challan_id";

        if(!empty($data['id'])) { $queryData['where']['die_challan_trans.challan_id'] = $data['id']; }

        if(!empty($data['trans_id'])) { $queryData['where']['die_challan_trans.id'] = $data['trans_id']; }

        if(isset($data['trans_status'])) { $queryData['where']['die_challan_trans.trans_status'] = $data['trans_status']; }

        if(!empty($data['item_id'])) { $queryData['where']['die_challan_trans.item_id'] = $data['item_id']; }

        if(!empty($data['prc_id'])) { $queryData['where']['die_challan_trans.prc_id'] = $data['prc_id']; }

        if(!empty($data['die_set_no'])) { $queryData['where']['die_challan_trans.die_set_no'] = $data['die_set_no']; }

        if(!empty($data['challan_type'])) { $queryData['where']['die_challan.challan_type'] = $data['challan_type']; }

        if(!empty($data['group_by'])){ $queryData['group_by'][]= $data['group_by']; }

        if(!empty($data['single_row'])){ return $this->row($queryData);  }
        else{  return $this->rows($queryData); }
    }
	
    public function delete($id){
        try{
            $this->db->trans_begin();
            $transData = $this->getDieChallanItems(['id'=>$id]); // Get Old Transaction
            foreach($transData AS $row){
                if($row->trans_status == 1){
                    return ['status'=>0,'message'=>"You can not delete this challan"];
                }
                $this->edit("die_master",['fg_id'=>$row->item_id,'set_no'=>$row->die_set_no],['status'=>1]); // Reverse Die Status
            }
            $this->trash($this->dieChallanTrans,['challan_id'=>$id]);
            $result = $this->trash($this->dieChallan,['id'=>$id],'Sales Enquiry');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function saveReturnDie($data){
        try{
            $this->db->trans_begin();
            $transData = $this->getDieChallanItems(['trans_id'=>$data['id'],'single_row'=>1]);
            if($transData->challan_type == 1){
                $logData = $this->sop->getProcessLogList(['prc_id'=>$transData->prc_id,'die_set_no'=>$transData->die_set_no,'process_by'=>1,'grouped_data'=>1,'rejection_review_data'=>1,'single_row'=>1]);
                $oldDieData = $this->getDieVolume(['prc_id'=>$transData->prc_id,'trans_status'=>1,'item_id'=>$transData->item_id,'challan_type'=>1,'die_set_no'=>$transData->die_set_no,'single_row'=>1]);
                $oldVolume = ((!empty($oldDieData->total_volume))?$oldDieData->total_volume:0);
                $totalProdQty = 0 ;
                if(!empty($logData)):
                    $totalProdQty = $logData->ok_qty + $logData->rej_qty + $logData->rw_qty + ($logData->rej_found - $logData->review_qty);
                endif;
                
                $data['volume'] = $totalProdQty - $oldVolume;
            }
            $chData = [
                'receive_by' => $this->loginId,
                'receive_at' => $data['receive_at'],
                'volume' => $data['volume'],
                'in_ch_no' => $data['in_ch_no'],
                'return_remark' => $data['return_remark'],
                'trans_status' => 1
            ];
            $this->edit($this->dieChallanTrans, ['id'=>$data['id']], $chData);

            $setData = array();
            $setData['tableName'] = 'die_master';
            $setData['where']['set_no'] = $transData->die_set_no;
            $setData['where']['fg_id'] = $transData->item_id;
            $setData['set']['volume'] = 'volume, + ' . (!empty($data['volume'])? $data['volume'] : 0);
            $this->setValue($setData);

            $this->edit("die_master",['fg_id'=>$transData->item_id,'set_no'=>$transData->die_set_no],['status'=>1]);
            $result = ['status'=>1, 'message'=>'Return Challan Saved Successfully.'];
            
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