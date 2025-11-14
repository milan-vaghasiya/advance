<?php
class TestTypeModel extends MasterModel
{     
    private $test_type = "test_type";
    private $test_parameter = "test_parameter"; 

    public function getDTRows($data){
        $data['tableName'] = $this->test_type;
        $data['select'] = "test_type.id,test_type.test_name";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "test_type.test_name";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getTestType($data){
        $queryData['tableName'] = $this->test_type;
        $queryData['where']['id'] = $data['id'];
        return $this->row($queryData);
    }

    public function getTypeList($param = []){
        $data['tableName'] = $this->test_type;
        if(!empty($param['id'])){
            $data['where_in']['test_type.id'] = $param['id'];
        }
        return $this->rows($data);
    }

    public function saveTestType($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->test_type,$data,'Test Type');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
	
	/**** Test Parameter ***/
	
	public function getTestParameter($param = []){
        $queryData['tableName'] = $this->test_parameter;
        $queryData['select'] = 'test_parameter.*,test_type.test_name,test_type.head_name';
        $queryData['leftJoin']['test_type'] = 'test_parameter.test_type = test_type.id';
        if(!empty($param['test_type'])){$queryData['where_in']['test_type'] = $param['test_type'];}
        if(!empty($param['group_by'])){
            $queryData['group_by'][] = $param['group_by'];
        }
        
        $result = $this->rows($queryData);
        return $result;
    }

    public function saveTestParam($param){
        try{
            $this->db->trans_begin();
            
            $result = $this->store($this->test_parameter, $param, 'Test Parameter');         

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function removeTestParameter($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->test_parameter,['id'=>$id],'Test Parameter');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function testHeadSearch(){
		$data['tableName'] = 'test_type';
		$data['select'] = 'head_name';
        $data['group_by'][] = 'head_name';
		$result = $this->rows($data);
		$searchResult = array();
		foreach ($result as $row) {
			$searchResult[] = $row->head_name;
		}
		return  $searchResult;
	}
}
?>