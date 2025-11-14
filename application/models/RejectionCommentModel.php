<?php

class RejectionCommentModel extends MasterModel{
    private $rejectionComment = "rejection_comment";

    public function getDTRows($data){
        $data['tableName'] = $this->rejectionComment;
        $data['where']['type'] = $data['type']; 

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "code";
        $data['searchCol'][] = "remark";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getComment($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->rejectionComment;
        return $this->row($data);
    } 

    public function save($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->rejectionComment,$data,'Rejection Comment');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->rejectionComment,['id'=>$id],'Rejection Comment');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }  
    
    public function getCommentList($param = array()){
        $data['tableName'] = $this->rejectionComment;
        $data['where']['type'] = $param['type'];
        return $this->rows($data);
    }    
}
?>