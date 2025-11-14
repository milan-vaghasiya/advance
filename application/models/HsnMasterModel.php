<?php
class HsnMasterModel extends MasterModel{
    private $hsnMaster = "hsn_master";
    
    public function getDTRows($data){
        $data['tableName'] = $this->hsnMaster;

		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
		$data['searchCol'][] = "hsn";
        $data['searchCol'][] = "cgst";
        $data['searchCol'][] = "sgst";
        $data['searchCol'][] = "igst";
        $data['searchCol'][] = "description";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getHSNDetail($data){
        if(!empty($data['id'])):
            $queryData['where']['id'] = $data['id'];
        endif;
        if(!empty($data['hsn'])):
            $queryData['where']['hsn'] = $data['hsn'];
        endif;
        $queryData['tableName'] = $this->hsnMaster;
        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['hsn'] = "HSN is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $data['igst'] = $data['gst_per'];
            $data['cgst'] = $data['sgst'] = (!empty($data['igst']))?($data['igst']/2):0;
            $result = $this->store($this->hsnMaster,$data,'HSN Master');

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
        $queryData['tableName'] = $this->hsnMaster;
        $queryData['where']['hsn'] = trim($data['hsn']);
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $hsnData = $this->getHSNDetail(['id'=>$id]);

            $checkData['columnName'] = ["hsn_code"];
            $checkData['value'] = $hsnData->hsn;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The HSN Code is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->hsnMaster,['id'=>$id],'HSN Master');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function getHSNList($data=array()){
        $queryData['tableName'] = $this->hsnMaster;
        return $this->rows($queryData);
    }

    public function getHSNListOnType(){
		$data['tableName'] = 'item_master';
		$data['select'] = 'DISTINCT(hsn_code)';
		return $this->rows($data);
    }
}
?>