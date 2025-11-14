<?php
class GroupMasterModel extends MasterModel{
    private $groupMaster = "group_master";

    public function getDTRows($data){
        $data['tableName'] = $this->groupMaster;

        $data['select'] = "group_master.*,perent_group_master.name as perent_group_name";

        $data['leftJoin']['group_master as perent_group_master'] = "group_master.under_group_id = perent_group_master.id";
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "group_master.group_code";
        $data['searchCol'][] = "group_master.name";
        $data['searchCol'][] = "perent_group_name";
        $data['searchCol'][] = "group_master.nature";
        $data['searchCol'][] = "group_master.bs_type_name";
		
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		
        return $this->pagingRows($data);
    }

    public function getGroup($data){
        $queryData = array();
        $queryData['tableName'] = $this->groupMaster;

        if(!empty($data['group_code']))
            $queryData['where_in']['group_code'] = $data['group_code'];

        if(!empty($data['id']))
            $queryData['where']['id'] = $data['id'];

        if(!empty($data['is_default']))
            $queryData['where']['is_default'] = $data['is_default'];

        $result = $this->row($queryData);
        return $result;
    }

    public function getGroupList($data = array()){
        $queryData = array();
        $queryData['tableName'] = $this->groupMaster;

        if(!empty($data['group_code']))
            $queryData['where_in']['group_code'] = $data['group_code'];

        if(!empty($data['not_group_code']))
            $queryData['where_not_in']['group_code'] = $data['not_group_code'];

        if(!empty($data['is_default']))
            $queryData['where']['is_default'] = $data['is_default'];

        $result = $this->rows($queryData);
        return $result;
    }

    public function checkDuplicate($data){
        $queryData['tableName'] = $this->groupMaster;
        $queryData['where']['name'] = $data['name'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['name'] = "Group Name is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $underGroupData = $this->getGroup(['id'=>$data['under_group_id']]);
            $data['base_group_id'] = $underGroupData->base_group_id;
            $data['base_group_code'] = $underGroupData->base_group_code;
            $data['nature'] = $underGroupData->nature;
            $data['bs_type_code'] = $underGroupData->bs_type_code;
            $data['bs_type_name'] = $underGroupData->bs_type_name;
            $data['gp_effect'] = $underGroupData->gp_effect;
            $data['is_default'] = 0;
            $data['is_active'] = $underGroupData->is_active;
            $data['group_code'] = $underGroupData->group_code;

            $result = $this->store($this->groupMaster,$data,'Group');

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

            $checkData['columnName'] = ["group_id,under_group_id"];        
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Group is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->groupMaster,['id'=>$id],'Group');

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