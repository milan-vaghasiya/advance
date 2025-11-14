<?php
class TaxPaymentModel extends MasterModel{
    private $transMain = "trans_main";
    private $transLedger = "trans_ledger";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = 'trans_main.id,trans_main.trans_number,trans_main.trans_date,opp_acc.party_name as opp_acc_name,vou_acc.party_name as vou_acc_name,trans_main.net_amount,trans_main.doc_no,trans_main.doc_date,trans_main.remark';

        $data['leftJoin']['party_master as opp_acc'] = "opp_acc.id = trans_main.vou_acc_id";
        $data['leftJoin']['party_master as vou_acc'] = "vou_acc.id = trans_main.vou_acc_id";

        $data['where']['trans_main.entry_type'] = $data['entry_type'];
        
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "opp_acc.party_name";
        $data['searchCol'][] = "vou_acc.party_name";
        $data['searchCol'][] = "trans_main.doc_no";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.doc_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.net_amount";
        $data['searchCol'][] = "trans_main.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
			$this->db->trans_begin();

            if(empty($data['id'])):
                $data['trans_no'] = $this->transMainModel->nextTransNo($this->data['entryData']->id,0,$data['vou_name_s']);
            endif;

            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];

            $data['p_or_m'] = ($data['vou_name_s'] == "BCRct")?1:-1;
			$data['doc_date'] = (!empty($data['doc_date']))?$data['doc_date']:null;

            $itemData = $data['itemData']; unset($data['itemData']);
            $result = $this->store($this->transMain,$data,'Voucher');
			$data['id'] = $result['id'];

            $data['itemData'] = $itemData;
            $this->transMainModel->ledgerEffects($data);

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Throwable $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getTaxPayment($data){
        $queryData = [];
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $data['id'];
        $result = $this->row($queryData);

        $queryData = [];
        $queryData['tableName'] = $this->transLedger;
        $queryData['where']['trans_main_id'] = $data['id'];
        $result->ledgerData = $this->rows($queryData);

        return $result;
    }

    public function delete($id){
		try{
			$this->db->trans_begin();
			
			$result= $this->trash($this->transMain,['id'=>$id],'Voucher');
			$this->transMainModel->deleteLedgerTrans($id);

			if($this->db->trans_status() !== FALSE):
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