<?php
class QcChallanModel extends MasterModel{
    private $qcChallan = "qc_challan";
    private $qcChallanTrans = "qc_challan_trans";

    public function nextTransNo(){
        $data['tableName'] = $this->qcChallan;
        $data['select'] = "MAX(trans_no) as trans_no";
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;        
		return $nextTransNo;
    }
    
    public function getDTRows($data){
        $data['tableName'] = $this->qcChallanTrans;
        $data['select'] = 'qc_challan_trans.*,qc_challan.trans_number,qc_challan.challan_type,qc_challan.trans_date,qc_challan.party_id,qc_instruments.item_name,qc_instruments.item_code,(CASE WHEN qc_challan.challan_type != 1 THEN party_master.party_name ELSE department_master.name END) as party_name,issue_from.emp_name as handover_to';
        $data['leftJoin']['qc_challan'] = "qc_challan.id = qc_challan_trans.challan_id";
        $data['leftJoin']['qc_instruments'] = "qc_challan_trans.item_id = qc_instruments.id";
        $data['leftJoin']['party_master'] = "party_master.id = qc_challan.party_id";
        $data['leftJoin']['department_master'] = "department_master.id = qc_challan.party_id";
        $data['leftJoin']['employee_master as issue_from'] = "issue_from.id = qc_challan.emp_id";
        
        if(empty($data['qc_status'])){ $data['where']['qc_challan_trans.trans_status'] = 0; }
		else{ $data['where']['qc_challan_trans.trans_status >'] = 0; }
            
            if(!empty($data['status']) && $data['status'] == 2){
                $data['where_in']['qc_challan.challan_type'] = '1,2';
            }else{
                $data['where']['qc_challan.challan_type'] = '3';
            }
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "CONCAT('/',qc_challan.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(qc_challan.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "qc_challan.challan_type";
        $data['searchCol'][] = "issue_from.emp_name";
        $data['searchCol'][] = "party_master.party_name";   
        $data['searchCol'][] = "qc_instruments.item_code";
        $data['searchCol'][] = "qc_instruments.item_name";
        $data['searchCol'][] = "qc_challan_trans.item_remark";

		$columns =array('','','qc_challan.trans_no','qc_challan.trans_date','qc_challan.challan_type','issue_from.emp_name','party_master.party_name','qc_instruments.item_code','qc_instruments.item_name','qc_challan_trans.item_remark');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function save($masterData,$itemData){
        try{
            $this->db->trans_begin();
            
            if(empty($masterData['id'])):
                $inChallan = $this->store($this->qcChallan,$masterData);
                $mainId = $inChallan['insert_id'];
                $result = ['status'=>1,'message'=>'Challan Saved Successfully.','url'=>base_url("qcChallan")];
            else:
                $this->store($this->qcChallan,$masterData);
                $mainId = $masterData['id'];
                $challanItems = $this->getQcChallanTrans(['challan_id'=>$mainId]); 
                
                foreach($challanItems as $row):
                    if(!in_array($row->id,$itemData['id'])):
                        $this->trash($this->qcChallanTrans,['id'=>$row->id]);
                        $this->edit('qc_instruments', ['id'=>$row->item_id], ['status'=>1]);
                    endif;
                endforeach;
    
                $result = ['status'=>1,'message'=>'Challan updated Successfully.','url'=>base_url("qcChallan")];
            endif;
    
            foreach($itemData['item_id'] as $key=>$value):
                $item = $this->instrument->getItem(['id'=>$value, 'single_row'=>1]); 
                $transData = [
                    'id' => $itemData['id'][$key],
                    'challan_id' => $mainId,
                    'item_id' => $value,
                    'from_location' => $item->location_id,
                    'batch_no' => $itemData['batch_no'][$key],                
                    'item_remark' => $itemData['item_remark'][$key],
                    'entry_type' => $itemData['entry_type'],
                    'created_by' => $itemData['created_by']
                ];
                /** Insert Record in Delivery Transaction **/
                $saveTrans = $this->store($this->qcChallanTrans,$transData);
                
                if($masterData['challan_type'] != 3):
                    $this->edit('qc_instruments', ['id'=>$value], ['status'=>2]);
                else:
                    $this->edit('qc_instruments', ['id'=>$value], ['status'=>3]);
                endif;
                
                $setData = Array();
                $setData['tableName'] = $this->qcChallan;
                $setData['where']['id'] = $mainId;
                $setData['set']['qty'] = 'qty, + 1 ';
                $this->setValue($setData);
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

	public function getQcChallan($id){
        $queryData['tableName'] = $this->qcChallan;
        $queryData['select'] = 'qc_challan.*,employee_master.emp_name,(CASE WHEN qc_challan.challan_type != 1 THEN party_master.party_name ELSE department_master.name END) as party_name';
        $queryData['leftJoin']['party_master'] = "party_master.id = qc_challan.party_id";
        $queryData['leftJoin']['department_master'] = "department_master.id = qc_challan.party_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = qc_challan.emp_id";
        $queryData['where']['qc_challan.id'] = $id;
        $challanData = $this->row($queryData);
        $challanData->itemData = $this->getQcChallanTrans(['challan_id'=>$id]); 
        return $challanData;
    }
    
    public function getQcChallanTrans($data=[]){
        $queryData = array();
        $queryData['tableName'] = $this->qcChallanTrans;
        $queryData['select'] = 'qc_challan_trans.*,qc_instruments.item_name,qc_instruments.item_code,qc_challan.party_id,party_master.party_name,qc_challan.challan_type,qc_challan.trans_number,qc_challan.trans_date,(CASE WHEN qc_challan.challan_type != 1 THEN party_master.party_name ELSE department_master.name END) as party_name,employee_master.emp_name';

        $queryData['leftJoin']['qc_instruments'] = "qc_challan_trans.item_id = qc_instruments.id";
        $queryData['leftJoin']['qc_challan'] = "qc_challan.id = qc_challan_trans.challan_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = qc_challan.party_id";
        $queryData['leftJoin']['department_master'] = "department_master.id = qc_challan.party_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = qc_challan.created_by";

        if (!empty($data['challan_id'])) { $queryData['where']['qc_challan_trans.challan_id'] = $data['challan_id']; }

        if (!empty($data['id'])) { $queryData['where']['qc_challan_trans.id'] = $data['id']; }

        if (!empty($data['item_id'])) { $queryData['where']['qc_challan_trans.item_id'] = $data['item_id']; }

        if (!empty($data['challan_type'])) { $queryData['where_in']['qc_challan.challan_type'] = $data['challan_type']; }

        if(!empty($data['single_row']) && $data['single_row'] == 1){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }
    
    public function deleteChallan($id){
        try{
            $this->db->trans_begin();

            $transData = $this->getQcChallanTrans(['challan_id'=>$id]);

            foreach($transData as $row):    
                $this->trash($this->qcChallanTrans,['id'=>$row->id]);
                $this->edit('qc_instruments', ['id'=>$row->item_id], ['status'=>1]);
            endforeach;

            $result = $this->trash($this->qcChallan,['id'=>$id],'Challan');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }   
    }

    public function getCalibrationData($data){
        $data['tableName'] = 'qc_challan_trans';
        $data['select'] = 'qc_challan_trans.*,(CASE WHEN qc_challan.challan_type != 1 THEN party_master.party_name ELSE department_master.name END) as party_name ,qc_instruments.item_name,qc_instruments.item_code';
        $data['leftJoin']['qc_challan'] = "qc_challan.id = qc_challan_trans.challan_id";
        $data['leftJoin']['party_master'] = "party_master.id = qc_challan.party_id";
        $data['leftJoin']['department_master'] = "department_master.id = qc_challan.party_id";
        $data['leftJoin']['qc_instruments'] = "qc_challan_trans.item_id = qc_instruments.id";
        $data['where']['qc_challan_trans.item_id'] = $data['item_id'];
        $data['where']['qc_challan.challan_type'] = 3;
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "qc_challan_trans.in_ch_no";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "qc_challan_trans.item_remark";
        $columns =array('','','party_master.party_name','qc_challan_trans.in_ch_no','','qc_challan_trans.item_remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function saveCalibration($data){
        try{
            $this->db->trans_begin();
            if($data['challan_type'] == 3){
                $this->edit('qc_instruments',['id'=>$data['item_id']],['last_cal_date'=>$data['receive_at'],'next_cal_date'=>$data['next_cal_date'],'status'=>1],'Instruments');
                $update = [
                    'receive_by'=>$data['created_by'],
                    'receive_at'=>$data['receive_at'],
                    'to_location'=>$data['to_location'],
                    'in_ch_no'=>$data['in_ch_no'],
                    'trans_status'=>1
                ];
    
                if(!empty($data['certificate_file'])){
                    $update['certificate_file'] = $data['certificate_file'];
                }
                $result = $this->edit($this->qcChallanTrans, ['id'=>$data['challan_id']], $update);
            }elseif($data['challan_type'] == 1 || $data['challan_type'] == 2){
                $this->edit('qc_instruments', ['id'=>$data['item_id']], ['status'=>1]);
                $update = [
                    'receive_by'=>$data['created_by'],
                    'receive_at'=>$data['receive_at'],
                    'to_location'=>$data['to_location'],
                    'in_ch_no'=>$data['in_ch_no'],
                    'return_remark'=>$data['return_remark'],
                    'trans_status'=>1
                ];
                $result = $this->edit($this->qcChallanTrans, ['id'=>$data['challan_id']], $update);
            }
            $setData = Array();
            $setData['tableName'] = $this->qcChallan;
            $setData['where']['id'] = $data['challan_id'];
            $setData['set']['receive_qty'] = 'receive_qty, + 1';
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