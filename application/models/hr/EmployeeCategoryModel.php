<?php
class EmployeeCategoryModel extends MasterModel{
    private $empCategory = "emp_category";

    public function getDTRows($data){
        $data['tableName'] = $this->empCategory;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "category";
        $data['searchCol'][] = "overtime";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getEmployeeCategoryList($data=array()){
        $queryData['tableName'] = $this->empCategory;
        return $this->rows($queryData);
    }

    public function getEmployeeCategory($data){
        $queryData['where']['id'] = $data['id'];
        $queryData['tableName'] = $this->empCategory;
        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['category'] = "Category Name is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $result = $this->store($this->empCategory,$data,'Employee Category');

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
        $queryData['tableName'] = $this->empCategory;
        $queryData['where']['category'] = $data['category'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $checkData['columnName'] = ['emp_category'];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);
            
            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Employee Category is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->empCategory,['id'=>$id],'Employee Category');

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