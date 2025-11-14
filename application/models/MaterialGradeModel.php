<?php
class MaterialGradeModel extends MasterModel{
    private $materialMaster = "material_master";

    public function getDTRows($data){
        $data['tableName'] = $this->materialMaster;
        $data['order_by']['material_master.id'] = "ASC";
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "material_master.material_grade";
        $data['searchCol'][] = "material_master.standard"; 
        $data['searchCol'][] = "material_master.color_code";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getMaterial($data){
        $data['tableName'] = $this->materialMaster;
        if(!empty($data['id'])):
            $data['where']['id'] = $data['id'];
        endif;
        if(!empty($data['material_grade'])):
            $data['where']['material_grade'] = $data['material_grade'];
        endif;
        return $this->row($data);
    }

    public function getMaterialGrades(){
        $data['tableName'] = $this->materialMaster;
        return $this->rows($data);
    }

    public function standardSearch(){
        $data['tableName'] = $this->materialMaster;
		$data['select'] = 'standard';
		$result = $this->rows($data);
		$searchResult = array();
		foreach ($result as $row) {
			$searchResult[] = $row->standard;
		}
		return  $searchResult;
	}

	public function colorCodeSearch(){
        $data['tableName'] = $this->materialMaster;
		$data['select'] = 'color_code';
		$result = $this->rows($data);
		$searchResult = array();
		foreach ($result as $row) {
			$searchResult[] = $row->color_code;
		}
		return  $searchResult;
	}

    public function save($data){
        try{
            $this->db->trans_begin();
            $data['material_grade'] = trim($data['material_grade']);
            if($this->checkDuplicate($data['material_grade'],$data['id']) > 0):
                $errorMessage['material_grade'] = "Material Grade is duplicate.";
                $result = ['status'=>0,'message'=>$errorMessage];
            else:
                $result = $this->store($this->materialMaster,$data,'Material Grade');
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

    public function checkDuplicate($materialGrade,$id=""){
        $data['tableName'] = $this->materialMaster;
        $data['where']['material_grade'] = $materialGrade;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $result = $this->trash('tc_master',['grade_id'=>$id],'Test Master');
            $result = $this->trash($this->materialMaster,['id'=>$id],'Material Grade');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    /*TC Parameter Details*/
    public function getTcMasterData($data){
        $queryData['tableName'] = 'tc_master';
        $queryData['select'] = 'tc_master.*,test_type.head_name,test_type.test_name,test_type.sample_1,test_type.sample_2,test_type.sample_3';
        $queryData['leftJoin']['test_type'] = 'test_type.id = tc_master.test_type  AND test_type.is_delete = 0';
        if(!empty($data['grade_id'])){$queryData['where']['tc_master.grade_id'] = $data['grade_id'];}
		if(!empty($data['item_id'])){$queryData['where']['tc_master.item_id'] = $data['item_id'];}
        if(!empty($data['test_name'])){$queryData['where']['test_type.test_name'] = $data['test_name'];}
        if(!empty($data['test_type'])){
            $queryData['where_in']['test_type.id'] = $data['test_type'];
        }
        if(!empty($data['ins_type'])){
            $queryData['where']['tc_master.ins_type'] = $data['ins_type'];
        }
        if(!empty($data['tcParameter'])){
            $queryData['select'] .= ',test_parameter.id as param_id,test_parameter.parameter as insp_param,test_parameter.requirement';
            $queryData['leftJoin']['test_parameter'] = 'test_type.id = test_parameter.test_type AND test_parameter.is_delete=0';
        }
        return $this->rows($queryData);
    }

    public function saveInspectionParam($data){ 
        try{
            $this->db->trans_begin();
            $this->edit($this->materialMaster,['id'=>$data['grade_id']],['approve_by'=>$data['approve_by'],'approve_at'=>date('Y-m-d H:i:s')]);
            $this->trash("tc_master",['item_id'=>$data['item_id']]);
            
            foreach($data['headData'] as $row):
                $row['grade_id'] = $data['grade_id'];
				$row['item_id'] = $data['item_id'];
                $row['is_delete']=0;
                $result = $this->store('tc_master',$row);
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

    public function reOpenTcParam($data) {
        try{
            $this->db->trans_begin();

            $date = ($data['approve_by'] == 1) ? date('Y-m-d') : "NULL";
            $isApprove =  ($data['approve_by'] == 1) ? $this->loginId : 0;
            
            $this->store($this->materialMaster, ['id'=> $data['id'], 'approve_by' => $isApprove, 'approve_at'=>$date]);
            $result = ['status' => 1, 'message' => 'TC Parameter ' . $data['msg'] . ' Successfully.'];

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