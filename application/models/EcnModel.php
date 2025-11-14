<?php
class EcnModel extends MasterModel{

    /** Check List Master */
        public function getCheckListDTRows($data){
            $data['tableName'] = 'ecn_checklist';

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "description";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);
        }

        public function savecheckList($data){
            try{
                $this->db->trans_begin();

                if($this->checkDuplicate($data) > 0):
                    $errorMessage['description'] = "Duplicate Check Point.";
                    return ['status'=>0,'message'=>$errorMessage];
                endif;
                
                $result = $this->store('ecn_checklist',$data,'Check Point');
                
                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Throwable $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function checkDuplicate($data){
            $queryData['tableName'] = 'ecn_checklist';
            $queryData['where']['description'] = $data['description'];
            
            if(!empty($data['id']))
                $queryData['where']['id !='] = $data['id'];
            
            $queryData['resultType'] = "numRows";
            return $this->specificRow($queryData);
        }

        public function deleteCheckList($id){
            try{
                $this->db->trans_begin();
    
                $checkData['columnName'] = ['ecn_checklist_id'];
                $checkData['value'] = $id;
                $checkUsed = $this->checkUsage($checkData);
    
                if($checkUsed == true):
                    return ['status'=>0,'message'=>'The Check point is currently in use. you cannot delete it.'];
                endif;
    
                $result = $this->trash('ecn_checklist',['id'=>$id],'Check Point');
    
                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Throwable $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function getCheckList($data=array()){
            $queryData['tableName'] = 'ecn_checklist';

            if(!empty($data['id'])){ $queryData['where']['id'] = $data['id']; }

            if(!empty($data['single_row'])){
                return $this->row($queryData);
            }else{
                return $this->rows($queryData);
            }
        }
    /** End Master */

    /** ECN */
        public function getDTRows($data){ 
            $data['tableName'] = 'ecn_master';
            $data['select'] = 'ecn_master.*,item_master.item_code,item_master.item_name,ecn_effect.effect_id';
            $data['leftJoin']['item_master'] = "item_master.id = ecn_master.item_id";
            /** ECN EFFECT  */
            $data['leftJoin']['(SELECT id AS effect_id,ecn_id FROM ecn_effect WHERE ecn_effect.is_delete = 0 GROUP BY ecn_id)ecn_effect'] = 'ecn_master.id = ecn_effect.ecn_id';

            $data['where']['ecn_master.status'] = $data['status'];

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "IF(ecn_master.ecn_type = 1,'NPD',(IF(ecn_master.ecn_type = 2,'Customer Change',(IF(ecn_master.ecn_type = 3,'Internal Change','Customer Complain')))))";
            $data['searchCol'][] = "ecn_master.ecn_no";
            $data['searchCol'][] = "DATE_FORMAT(ecn_date,'%d-%m-%Y')";
            $data['searchCol'][] = "CONCAT(item_master.item_code,' ',item_master.item_name)";
            $data['searchCol'][] = "ecn_master.drw_no";
            $data['searchCol'][] = "ecn_master.cust_rev_no";
            $data['searchCol'][] = "DATE_FORMAT(ecn_master.cust_rev_date,'%d-%m-%Y')";
            $data['searchCol'][] = "ecn_master.rev_no";
            $data['searchCol'][] = "DATE_FORMAT(ecn_master.rev_date,'%d-%m-%Y')";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);

        }

        public function saveEcn($data){
            try{
                $this->db->trans_begin();
				
				if(empty($data['id']))
				{
					//Check Duplicate ECN NO
					/*
					if($this->checkEcnDuplicate(['ecn_no'=>$data['ecn_no'],'id'=>$data['id']]) > 0):
						$errorMessage['ecn_no'] = "Duplicate ECN No.";
						return ['status'=>0,'message'=>$errorMessage];
					endif;*/
					//Check Duplicate Rev NO
					if($this->checkEcnDuplicate(['rev_no'=>$data['rev_no'],'item_id'=>$data['item_id'],'id'=>$data['id']]) > 0):
						$errorMessage['rev_no'] = "Duplicate Rev No.";
						return ['status'=>0,'message'=>$errorMessage];
					endif;
					
					//Check Duplicate Customer Rev NO
					if($this->checkEcnDuplicate(['cust_rev_no'=>$data['cust_rev_no'],'item_id'=>$data['item_id'],'id'=>$data['id']]) > 0):
						$errorMessage['cust_rev_no'] = "Duplicate Cust Rev No.";
						return ['status'=>0,'message'=>$errorMessage];
					endif;
				}
                $result = $this->store('ecn_master',$data);
    
                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
               return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function checkEcnDuplicate($data){
            $queryData['tableName'] = 'ecn_master';
            if(!empty($data['ecn_no'])){ $queryData['where']['ecn_no'] = $data['ecn_no']; }
            if(!empty($data['rev_no'])){ $queryData['where']['rev_no'] = $data['rev_no']; }
            if(!empty($data['cust_rev_no'])){ $queryData['where']['cust_rev_no'] = $data['cust_rev_no']; }
            if(!empty($data['item_id'])){ $queryData['where']['item_id'] = $data['item_id']; }
            
            if(!empty($data['id']))
                $queryData['where']['id !='] = $data['id'];
            
            $queryData['resultType'] = "numRows";
            return $this->specificRow($queryData);
        }
        
		public function deleteEcn($id){
            try {
                $this->db->trans_begin();
                $result = $this->trash('ecn_master',['id'=>$id]);
                if ($this->db->trans_status() !== FALSE) :
                    $this->db->trans_commit();
                    return $result;
                endif;
            } catch (\Exception $e) {
                $this->db->trans_rollback();
                return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
            }
        }

        public function getEcn($data){
            $queryData['tableName'] = 'ecn_master';
            $queryData['select'] = 'ecn_master.*,item_master.item_code,item_master.item_name,employee_master.emp_name AS prepareBy,aprove.emp_name AS approveBy,(CASE WHEN ecn_master.status = 2 THEN "Active" WHEN ecn_master.status = 3 THEN "De-Active" END) AS status_label';
            $queryData['leftJoin']['item_master'] = "item_master.id = ecn_master.item_id";
            $queryData['leftJoin']['employee_master'] = "employee_master.id = ecn_master.created_by";
            $queryData['leftJoin']['employee_master aprove'] = "aprove.id = ecn_master.approved_by";
			
            if(!empty($data['id'])){ $queryData['where']['ecn_master.id'] = $data['id']; $data['single_row'] = 1;}
			
            if(isset($data['status'])){ $queryData['where_in']['ecn_master.status'] = $data['status']; }
			
            if(!empty($data['item_id'])){ $queryData['where']['ecn_master.item_id'] = $data['item_id']; }
			
            if(!empty($data['rev_no'])){ $queryData['where']['ecn_master.rev_no'] = $data['rev_no']; }

            if(!empty($data['single_row'])){
                return $this->row($queryData);
            }else{
                return $this->rows($queryData);
            }
        }

        public function getEcnEffectDetail($data){
            $queryData['tableName'] = 'ecn_effect';
            $queryData['select'] = 'ecn_effect.*';
            $queryData['select'] .= ',ecn_checklist.description AS check_point,employee_master.emp_name';
            $queryData['leftJoin']['ecn_checklist'] = 'ecn_checklist.id = ecn_effect.ecn_checklist_id ';
            $queryData['leftJoin']['employee_master'] = 'employee_master.id = ecn_effect.changed_by ';
            if(!empty($data['ecn_id'])){ $queryData['where']['ecn_effect.ecn_id'] = $data['ecn_id']; }
            if(!empty($data['id'])){ $queryData['where']['ecn_effect.id'] = $data['id']; }

            if(!empty($data['single_row'])){
                return $this->row($queryData);
            }else{
                return $this->rows($queryData);
            }
        }
        
        public function saveEcnEffect($data){
            try {
                $this->db->trans_begin();
                $this->trash('ecn_effect',['ecn_id'=>$data['ecn_id']]);
                foreach($data['ecn_checklist_id'] AS $key=>$ecn_checklist_id){
                    $actionData = [
                        'id'=>$data['id'][$key],
                        'ecn_id'=>$data['ecn_id'],
                        'ecn_checklist_id'=>$ecn_checklist_id,
                        'action_detail'=>$data['action_detail'][$key],
                        'changed_by'=>$data['changed_by'][$key],
                        'changed_at'=>$data['changed_at'][$key],
                        'is_delete'=>0
                    ];
                    $this->store('ecn_effect',$actionData);
                }

                if ($this->db->trans_status() !== FALSE) :
                    $this->db->trans_commit();
                    return ['status'=>1,'message'=>'Action Detail saved successfully'];
                endif;
            } catch (\Exception $e) {
                $this->db->trans_rollback();
                return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
            }
        }

        public function saveInventoryDetail($data){
            try{
                $this->db->trans_begin();
    
                $result = $this->store('ecn_master',$data);
    
                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
               return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function getPrevRevisionData($postData){
            $data['tableName'] = 'ecn_master';
            $data['select'] = "ecn_master.*";
            $data['where']['ecn_master.item_id'] = $postData['item_id'];
            //$data['where']['ecn_master.rev_date <'] = $postData['rev_date'];
			$data['where']['ecn_master.id !='] = $postData['ecn_id'];
            $data['order_by']['ecn_master.id'] = "DESC";
            $data['limit'] = 1;
            $result = $this->row($data);
			
            $rev = new stdClass();
            if(!empty($result))
            {
                $rev = $result;
            }
            return $result;
        }

        public function activeEcn($data){
            try{
                $this->db->trans_begin();
    
                // $this->edit("ecn_master",['item_id'=>$data['item_id'],'status'=>2],['status'=>3]);
                $result = $this->store('ecn_master',['id'=>$data['id'],'status'=>$data['status']]);
    
                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
               return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function getItemRevision($param=[]){
            $data['tableName'] = "ecn_master";
            $data['select'] = "ecn_master.*,";
            $data['leftJoin']['item_master'] = "item_master.id = ecn_master.item_id";		
            if(!empty($param['item_id'])){$data['where']['ecn_master.item_id'] = $param['item_id'];}
            if(!empty($param['status'])){
                $data['where_in']['ecn_master.status'] = $param['status'];
            }else{
                $data['where']['ecn_master.status'] = 2;
            }
            $data['order_by']['ecn_master.rev_no'] = 'ASC';
            $data['order_by']['ecn_master.rev_date'] = 'ASC';
            $result = $this->rows($data);
            return $result;
        }
		
        public function getLastRevision($param=[]){
            $data['tableName'] = "ecn_master";
            $data['select'] = "ecn_master.*,";		
            $data['where']['ecn_master.item_id'] = $param['item_id'];
            $data['where']['ecn_master.status'] = 2;
			
            $data['order_by']['ecn_master.created_by'] = 'DESC';
            $data['limit'] = 1;
            $result = $this->row($data);
            return $result;
        }
    /** END ECN */
}
?>