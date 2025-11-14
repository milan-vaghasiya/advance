<?php
class LeaveSettingModel extends MasterModel{
    private $leaveType = "leave_type";
	
	public function getDTRows($data){
        $data['tableName'] = $this->leaveType;
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "leave_type";
        $data['searchCol'][] = "remark";
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getLeaveType($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->leaveType;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            $result = Array();
            if($this->checkDuplicate($data['leave_type'],$data['id']) > 0):
                $errorMessage['leave_type'] = "Leave Type is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;
            $result = $this->store($this->leaveType,$data,'Leave Type');
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function checkDuplicate($leave_type,$id=""){
        $data['tableName'] = $this->leaveType;
        $data['where']['leave_type'] = $leave_type;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        $data['resultType'] = "numRows";
        return $this->specificRow($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $result = Array();
            $result = $this->trash($this->leaveType,['id'=>$id],'Leave Type');
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
        return $result;
    }
	
}
?>