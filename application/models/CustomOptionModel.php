<?php
class CustomOptionModel extends MasterModel{
    private $udf_value = "udf_value";
    private $udf = "udf";

    public function getDTRows($data){
        $data['tableName'] = $this->udf_value;
        $data['select'] = "udf_value.*,udf.field_name";
        $data['leftJoin']['udf'] = "udf.id = udf_value.type";

        if(!empty($data['type'])){
            $data['where']['type'] = $data['type'];
        }
    
		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
		$data['searchCol'][] = "udf.field_name";
		$data['searchCol'][] = "title";
	
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
 	
    public function save($data){
        try{
            $this->db->trans_begin();
            
            if($this->checkDuplicate($data['title'],$data['id']) > 0):
                $errorMessage['title'] = "Title is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            else:
                $result = $this->store($this->udf_value,$data,'Title');
            endif;            

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

			$result = $this->trash($this->udf_value,['id'=>$id],"Record");
            
			if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}

    public function checkDuplicate($name,$id=""){
        $data['tableName'] = $this->udf_value;
        $data['where']['title'] = $name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;

        $data['resultType'] = "numRows";
        return $this->specificRow($data);
    }

	public function getSizeMaster($param = []){
        $data['tableName'] = $this->udf_value;
        if(!empty($param['id'])){ $data['where']['udf_value.id']=$param['id']; }
        return $this->row($data);
    }
	
    public function getSizeList(){
        $data['tableName'] = $this->udf_value;
        $data['where']['type'] = 1;
        return $this->rows($data);
    }

    public function getMasterList($param = []){
        $data['tableName'] = $this->udf_value;
        return $this->rows($data);
    }
}
?>