<?php
class DepartmentModel extends MasterModel{
    private $departmentMaster = "department_master";

    public function getDTRows($data){
        $data['tableName'] = $this->departmentMaster;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "name";
        $data['searchCol'][] = "description";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getDepartmentList($data=array()){
        $queryData['tableName'] = $this->departmentMaster;
        return $this->rows($queryData);
    }

    public function getDepartment($data){
        $queryData['where']['id'] = $data['id'];
        $queryData['tableName'] = $this->departmentMaster;
        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['name'] = "Department name is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;
            
            $result = $this->store($this->departmentMaster,$data,'Department');
            
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
        $queryData['tableName'] = $this->departmentMaster;
        $queryData['where']['name'] = $data['name'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $checkData['columnName'] = ['emp_dept_id'];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Department is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->departmentMaster,['id'=>$id],'Department');

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