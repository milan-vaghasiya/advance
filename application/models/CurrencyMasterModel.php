<?php
class CurrencyMasterModel extends MasterModel{
    private $currencyMaster = "currency";
    
    public function getDTRows($data){
        $data['tableName'] = $this->currencyMaster;

        $data['searchCol'][] = "currency_name";
        $data['searchCol'][] = "currency";
        $data['searchCol'][] = "code2000";
        $data['searchCol'][] = "inrrate";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            foreach($data['id'] as $key=>$value):
                $cData = ['id'=>$value,'inrrate'=>$data['inrrate'][$key]];
                $result = $this->store($this->currencyMaster, $cData, 'Currency');
            endforeach;

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