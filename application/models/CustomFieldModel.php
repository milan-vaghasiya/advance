<?php
class CustomFieldModel extends MasterModel{
    private $udf = "udf";

    public function getDTRows($data){
        $data['tableName'] = $this->udf;

		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
		$data['searchCol'][] = "field_name";
		$data['searchCol'][] = "field_type";
	
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getNextFieldIndex(){
        $data['select'] = "MAX(field_idx) as field_idx";
        $data['tableName'] = $this->udf;
		$field_idx = $this->specificRow($data)->field_idx;
		$field_idx = $field_idx + 1;
		return $field_idx;
    }

    public function checkDuplicateField($param = []){
        $data['tableName'] = $this->udf;
        $data['where']['field_name'] = $param['field_name'];
        
        if(!empty($param['id'])){  $data['where']['id !='] = $param['id'];}
        $data['resultType'] = "numRows";
        return $this->specificRow($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicateField(['field_name'=>$data['field_name'],'id'=>$data['id']]) > 0):
                $errorMessage['field_name'] = "Field Name is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $result = $this->store($this->udf,$data,'Field');                        

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function delete($id){
		try{
            $this->db->trans_begin();

			$result = $this->trash($this->udf,['id'=>$id],"Record");
            
			if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}

    public function getCustomFieldDetail($param = []){
        $data['tableName'] = $this->udf;
        if(!empty($param['field_name'])){$data['where']['field_name'] = $param['field_name'];}
        if(!empty($param['id'])){$data['where']['id'] = $param['id'];}
        return $this->row($data);
    }

    public function getCustomFieldList($param = []){
        $data['tableName'] = $this->udf;
        return $this->rows($data);
    }
}
?>