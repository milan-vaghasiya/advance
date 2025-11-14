<?php
class InstrumentModel extends MasterModel{
    private $itemMaster = "qc_instruments";

	public function getDTRows($data){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "qc_instruments.*, CONCAT('[',item_category.category_name,'] ',item_category.category_name) as category_name,location_master.location,party_master.party_name";
        $data['leftJoin']['item_category'] = "item_category.id = qc_instruments.category_id";
        $data['leftJoin']['location_master'] = "location_master.id = qc_instruments.location_id";
        $data['leftJoin']['grn_trans'] = "grn_trans.id = qc_instruments.ref_id";
        $data['leftJoin']['grn_master'] = "grn_trans.grn_id = grn_master.id";
        $data['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
        
        if(empty($data['status'])){$data['status'] = 0;}
        
        if($data['status'] != 5){ 
            $data['where']['qc_instruments.status'] = $data['status']; 
        }else{
            $data['where_in']['qc_instruments.status'] = "1,2";
            $data['customWhere'][] = "DATE_SUB(qc_instruments.next_cal_date, INTERVAL qc_instruments.cal_reminder DAY) <= '".date('Y-m-d')."'";
        }
        
        $columns = Array();
        if($data['status'] != 4){
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            if($data['status'] != 0){
                $data['searchCol'][] = "";
            }
            $data['searchCol'][] = "qc_instruments.item_code";
            $data['searchCol'][] = "qc_instruments.item_name";
            $data['searchCol'][] = "party_master.party_name";   
            $data['searchCol'][] = "qc_instruments.price";
            $data['searchCol'][] = "qc_instruments.make_brand";
            $data['searchCol'][] = "qc_instruments.cal_required";
            $data['searchCol'][] = "qc_instruments.cal_freq";
            $data['searchCol'][] = "location_master.location";
            $data['searchCol'][] = "qc_instruments.last_cal_date";
            $data['searchCol'][] = "qc_instruments.next_cal_date";
            $data['searchCol'][] = "(qc_instruments.next_cal_date - (qc_instruments.cal_reminder+1))";
            $data['searchCol'][] = "qc_instruments.created_at";

            if($data['status'] != 0){
                $columns =array('','','','qc_instruments.item_code','qc_instruments.item_name','party_master.party_name','qc_instruments.price','qc_instruments.make_brand','qc_instruments.cal_required','qc_instruments.cal_freq','location_master.location','qc_instruments.last_cal_date','qc_instruments.next_cal_date','','qc_instruments.created_at');
            } else {
                $columns =array('','','qc_instruments.item_code','qc_instruments.item_name','party_master.party_name','qc_instruments.price','qc_instruments.make_brand','qc_instruments.cal_required','qc_instruments.cal_freq','location_master.location','qc_instruments.last_cal_date','qc_instruments.next_cal_date','','qc_instruments.created_at');
            }
        }else{
            
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "qc_instruments.item_code";
            $data['searchCol'][] = "qc_instruments.item_name";
            $data['searchCol'][] = "party_master.party_name";   
            $data['searchCol'][] = "qc_instruments.price";
            $data['searchCol'][] = "qc_instruments.make_brand";
            $data['searchCol'][] = "qc_instruments.cal_required";
            $data['searchCol'][] = "qc_instruments.cal_freq";
            $data['searchCol'][] = "location_master.location";
            $data['searchCol'][] = "DATE_FORMAT(qc_instruments.rejected_at,'%d-%m-%Y')";
            $data['searchCol'][] = "qc_instruments.reject_reason";

            $columns =array('','','qc_instruments.item_code','qc_instruments.item_name','party_master.party_name','qc_instruments.price','qc_instruments.make_brand','qc_instruments.cal_required','qc_instruments.cal_freq','location_master.location','qc_instruments.rejected_at','qc_instruments.reject_reason');
        }

		if(isset($data['order'])){
		    $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
		}else{
            $data['order_by']['qc_instruments.category_id'] = 'ASC';
            $data['order_by']['qc_instruments.serial_no'] = 'ASC';
		}
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getItem($data=[]){
        $queryData['tableName'] = $this->itemMaster;
        $queryData['select'] = 'qc_instruments.*,item_category.category_name,item_category.category_code as cat_code,item_master.item_name,employee_master.emp_name';
        $queryData['leftJoin']['item_category'] = 'item_category.id = qc_instruments.category_id';
        $queryData['leftJoin']['item_master'] = 'item_master.id = qc_instruments.item_id';
        $queryData['leftJoin']['employee_master'] = 'employee_master.id = qc_instruments.created_by';

        if (!empty($data['id'])) { $queryData['where']['qc_instruments.id'] = $data['id']; }

        if (!empty($data['ids'])) { $queryData['where_in']['qc_instruments.id'] = str_replace("~", ",", $data['ids']); }

        if (!empty($data['status'])) { $queryData['where']['qc_instruments.status'] = $data['status']; }
        
        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $gaugeItem = $this->getItem(['id'=>$data['id'], 'single_row'=>1]);
            
            if(empty($data['id'])){
                /*
				$queryData = array();
                $queryData['tableName'] = $this->itemMaster;
                $queryData['select'] = "ifnull(MAX(serial_no) + 1,1) as serial_no";
                $queryData['where']['item_id'] = $gaugeItem->item_id;
                $serial_no = $this->specificRow($queryData)->serial_no;
                
                $data['serial_no'] = $serial_no;
                $data['item_code'] = $data['cat_code'].sprintf("/%02d",$serial_no);
				*/
                $data['item_name'] = $data['item_code'].' '.$data['cat_name'].' '.$data['size'];
            }else{
                $data['item_name'] = $data['item_code'].' '.$gaugeItem->item_name.' '.$data['size'];
            }        
            unset($data['cat_name'],$data['cat_code']);
    
            $result = $this->store($this->itemMaster,$data);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }   
	}

    public function checkDuplicate($postData){
        $data['tableName'] = $this->itemMaster;
        if(!empty($postData['category_id'])){$data['where']['category_id'] = $postData['category_id'];}
        if(!empty($postData['size'])){$data['where']['size'] = $postData['size'];}
        if(!empty($postData['id']))
            $data['where']['id !='] = $postData['id'];

        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->itemMaster,['id'=>$id]);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        } 

    }

    public function saveRejectGauge($data){
        try{
            $this->db->trans_begin();

            $result = $this->edit($this->itemMaster,['id'=>$data['id']],['status'=>4,'reject_reason'=>$data['reject_reason'],'rejected_at'=>date('Y-m-d H:i:s'),'rejected_by'=>$this->loginId]);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }  
    }
}
?>