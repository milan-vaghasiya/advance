<?php
class ShiftModel extends MasterModel{
    private $shiftMaster = "shift_master";

    public function getDTRows($data){		
        $data['tableName'] = $this->shiftMaster;

        $data['where']['latest_id > '] = 0;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "shift_name";
        $data['searchCol'][] = "shift_start";
        $data['searchCol'][] = "shift_end";
        $data['searchCol'][] = "production_hour";
        $data['searchCol'][] = "total_lunch_time";
        $data['serachCol'][] = "total_shift_time";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getShift($data){
        $queryData['where']['id'] = $data['id'];
        $queryData['tableName'] = $this->shiftMaster;
        return $this->row($queryData);
    }

    public function getShiftList($data=array()){
        $queryData['tableName'] = $this->shiftMaster;
        $queryData['where']['latest_id > '] = 0;
        return $this->rows($queryData);
    }

    public function save($data){
		try {
            $this->db->trans_begin();
            
			if($this->checkDuplicate($data) > 0):
				$errorMessage['shift_name'] = "Shift Name is duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			endif;

            if(empty($data['id'])):
                $result = $this->store($this->shiftMaster,$data,'Shift');
                $updateLatestID = $this->store($this->shiftMaster,['id'=>$result['insert_id'],'latest_id'=>$result['insert_id']],'Shift');
            else:
                $refRecord = $data;$refRecord['id']="";$refRecord['latest_id']=0;
                $addNewRecord = $this->store($this->shiftMaster,$refRecord,'Shift');
                $data['latest_id']=$addNewRecord['insert_id'];
                $result = $this->store($this->shiftMaster,$data,'Shift');
            endif;			
			
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        }catch (\Throwable $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }        
    }

    public function checkDuplicate($data){
        $queryData['tableName'] = $this->shiftMaster;
        $queryData['where']['shift_name'] = $data['shift_name'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
			$queryData['where']['latest_id > '] = 0;
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
		try{
            $this->db->trans_begin();

            $checkData['columnName'] = ['shift_id'];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Shift is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->shiftMaster,['id'=>$id],'Shift');

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