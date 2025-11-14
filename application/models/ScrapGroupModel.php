<?php
class ScrapGroupModel extends MasterModel{
    private $item_master = "item_master";

    public function getDTRows($data){
        $data['tableName'] = $this->item_master;
        $data['select'] = "item_master.*";
        // $data['leftJoin']['unit_master'] = "unit_master.id  = item_master.unit_id";
        $data['where']['item_master.item_type'] = $data['item_type'];

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "item_master.uom";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getScrapGroup($data){
        $data['tableName'] = $this->item_master;
        if(!empty($data['id'])):
            $data['where']['item_master.id'] = $data['id'];
        endif;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->item_master,$data,"Scrap Group");

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
            $result = $this->trash($this->item_master,['id'=>$id],'Scrap Group');

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