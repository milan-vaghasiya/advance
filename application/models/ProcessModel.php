<?php
class ProcessModel extends MasterModel{
    private $processMaster = "process_master";
	
    public function getDTRows($data){
        $data['tableName'] = $this->processMaster;
		$data['where']['is_system'] = 0;
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "process_master.process_name";
        $data['serachCol'][] = "process_master.remark";
		
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

	public function getProcessList($postData=[]){
        $data['tableName'] = $this->processMaster;
        if(!empty($postData['process_ids'])):
            $data['where_in']['id'] = $postData['process_ids'];
        endif;
        if(!empty($postData['ingonre_process'])):
            $data['where_not_in']['id'] = $postData['ingonre_process'];
        endif;
        return $this->rows($data);
    }
	
	public function getProcess($param){
        $data['tableName'] = $this->processMaster;
		
        if(!empty($param['id'])) { $data['where']['id'] = $param['id']; }
        if(!empty($param['process_name'])) { $data['where']['process_name'] = $param['process_name']; }
        
		return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['process_name'] = "Process name is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $result = $this->store($this->processMaster,$data,'Process');  

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
        $queryData['tableName'] = $this->processMaster;
        $queryData['where']['process_name'] = $data['process_name'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $checkData['columnName'] = ['process_id'];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Process is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->processMaster,['id'=>$id],'Process');

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