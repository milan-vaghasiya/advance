
<?php
class TermsModel extends MasterModel{
    private $terms = "terms";
	
    public function getDTRows($data){
        $data['tableName'] = $this->terms;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "title";
        $data['searchCol'][] = "conditions";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getTerm($data){
        $queryData['where']['id'] = $data['id'];
        $queryData['tableName'] = $this->terms;
        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->terms,$data,'Terms');

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

            $checkData['columnName'] = ["t_col_1"];
            $checkData['table_condition']['trans_detail']['where']['t_col_1']['table_name'] = "trans_main";
            $checkData['table_condition']['trans_detail']['where_in']['t_col_1']['description'] = ["SO TERMS","PO TERMS"];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The term is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->terms,['id'=>$id],'Terms');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

	public function getTermsList($data=array()){
        $queryData['tableName'] = $this->terms;
        if(!empty($data['type'])):
            //$queryData['where']['find_in_set("'.$data['type'].'",type) > '] = 0;
			$queryData['where']['type'] = $data['type'];
        endif;
        return $this->row($queryData);
    }
}
?>