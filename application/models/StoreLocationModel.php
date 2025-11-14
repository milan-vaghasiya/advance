<?php
class StoreLocationModel extends MasterModel{
    private $locationMaster = "location_master";

    public function getDTRows($data){
        $data['tableName'] = $this->locationMaster;

        $data['where']['ref_id'] = $data['ref_id'];

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "store_name";
        $data['searchCol'][] = "location";
        $data['serachCol'][] = "remark";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getStoreLocation($data){
        $queryData = array();
        $queryData['tableName'] = $this->locationMaster;

        if(!empty($data['id']))
            $queryData['where']['id'] = $data['id'];

        if(!empty($data['store_level']))
            $queryData['where']['store_level'] = $data['store_level'];

        return $this->row($queryData);
    }

    public function getStoreLocationList($data = array()){
        $queryData = array();
        $queryData['tableName'] = $this->locationMaster;
        
        if(!empty($data['id']))
            $queryData['where_in']['id'] = $data['id'];

        if(isset($data['store_type']))
            $queryData['where_in']['store_type'] = $data['store_type'];

        if(isset($data['final_location']))
            $queryData['where']['final_location'] = $data['final_location'];

        if(!empty($data['ref_id']))
            $queryData['where']['ref_id'] = $data['ref_id'];

        if(!empty($data['main_store']))
            $queryData['where']['main_store_id'] = 0;

        if(!empty($data['location_not_in']))
            $queryData['where_not_in']['id'] = $data['location_not_in'];

        if(!empty($data['main_store_level']))
            $queryData['customWhere'][] = 'LEFT(store_level, 1) IN ('.$data['main_store_level'].')';
        
        $queryData['order_by']['store_name'] = "ASC";
        $queryData['order_by']['location'] = "ASC";

        return $this->rows($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $data['store_name'] = trim($data['store_name']);
            $data['location'] = trim($data['location']);

            if($this->checkDuplicate($data) > 0):
                $errorMessage['location'] = "Location is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $result = $this->store($this->locationMaster,$data,'Store Location');

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
        $queryData['tableName'] = $this->locationMaster;
        $queryData['where']['store_name'] = $data['store_name'];
        $queryData['where']['location'] = $data['location'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $checkData['columnName'] = ["location_id"];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Store Location is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->locationMaster,['id'=>$id],'Store Location');

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