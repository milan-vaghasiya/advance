<?php
class DesignationModel extends MasterModel{
    private $designationMaster = "emp_designation";
    private $departmentMaster = "department_master";
    
	public function getDTRows($data){
        $data['tableName'] = $this->designationMaster;
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "title";
        $data['searchCol'][] = "description";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getDesignations($data=array()){
        $queryData['tableName'] = $this->designationMaster;
        return $this->rows($queryData);
    }

    public function getDesignation($data){
        $queryData['tableName'] = $this->designationMaster;
        $queryData['where']['id'] = $data['id'];
        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['title'] = "Designation name is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $result = $this->store($this->designationMaster,$data,'Designation');

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
        $queryData['tableName'] = $this->designationMaster;
        $queryData['where']['title'] = $data['title'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $checkData['columnName'] = ['emp_designation'];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Designation is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->designationMaster,['id'=>$id],'Designation');

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