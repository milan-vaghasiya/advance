<?php
class TransportModel extends MasterModel{
    private $transportMaster = "transport_master";

    public function getDTRows($data){
        $data['tableName'] = $this->transportMaster;
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "transport_name";
        $data['searchCol'][] = "transport_id";
        $data['searchCol'][] = "address";
		
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		
        return $this->pagingRows($data);
    }

    public function getTransport($data){
        $queryData['tableName'] = $this->transportMaster;
        $queryData['where']['id'] = $data['id'];
        return $this->row($queryData);
    }
	
    public function getTransportList($data = array()){
        $queryData['tableName'] = $this->transportMaster;
        return $this->rows($queryData);
    }

    public function save($data){
		try{
            $this->db->trans_begin();

            $result = $this->store($this->transportMaster,$data,'Transport');

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

            $checkData['columnName'] = ["transporter","i_col_1"];
            $checkData['table_condition']['trans_detail']['where']['i_col_1']['table_name'] = "trans_main";
            $checkData['table_condition']['trans_detail']['where_in']['i_col_1']['description'] = ["PO MASTER DETAILS"];            
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The transport is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->transportMaster,['id'=>$id],'Transport');

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