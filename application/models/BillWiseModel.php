<?php
class BillWiseModel extends MasterModel{
    private $transBillWise = "trans_billwise";

    public function getUnsettledTransactions($data){
        $queryData = array();
        $queryData['tableName'] = $this->transBillWise;
        $queryData['select'] = "trans_billwise.*, trans_billwise.amount - IFNULL((SELECT sum(tb.amount) FROM trans_billwise AS tb WHERE tb.ag_ref_id = trans_billwise.id AND tb.is_delete = 0), 0) as pending_amount,IFNULL((SELECT GROUP_CONCAT(tb.trans_number SEPARATOR '<hr>') as trans_number FROM trans_billwise AS tb WHERE tb.ag_ref_id = trans_billwise.id AND tb.is_delete = 0), '') as settled_vou_no,trans_main.vou_name_s,(CASE WHEN trans_billwise.trans_main_id = 0 AND trans_billwise.entry_type = 0 THEN (SELECT abs(IFNULL(opening_balance,0)) as op_balance FROM party_master WHERE party_master.id = trans_billwise.party_id AND party_master.is_delete = 0) ELSE IF(trans_main.vou_name_s IN ('BCRct','BCPmt'), IFNULL((trans_main.net_amount + abs(trans_main.round_off_amount)),0), IFNULL((trans_main.net_amount),0) ) END) as net_amount";

        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_billwise.trans_main_id";

        if(!empty($data['party_id'])):
            $queryData['where']['trans_billwise.party_id'] = $data['party_id'];
        endif;

        if(empty($data['id'])):
            $queryData['where_in']['trans_billwise.ref_type'] = [0,1];
        else:
            $queryData['where']['trans_billwise.id'] = $data['id'];
        endif;

        if(!empty($data['c_or_d'])):
            $queryData['where']['trans_billwise.c_or_d'] = $data['c_or_d'];
        endif;

        if($data['status'] == 0):
            $queryData['customWhere'][] = "trans_billwise.amount > IFNULL((SELECT sum(tb.amount) FROM trans_billwise AS tb WHERE tb.ag_ref_id = trans_billwise.id AND tb.is_delete = 0), 0)";
        elseif($data['status'] == 1):
            $queryData['customWhere'][] = "trans_billwise.amount <= IFNULL((SELECT sum(tb.amount) FROM trans_billwise AS tb WHERE tb.ag_ref_id = trans_billwise.id AND tb.is_delete = 0), 0)";
        endif;

        $queryData['order_by']['trans_billwise.trans_date'] = "ASC";
        $queryData['order_by']['trans_billwise.trans_main_id'] = "ASC";
        $queryData['group_by'][] = 'trans_billwise.id';

        if(empty($data['id'])):
            $result = $this->rows($queryData);
        else:
            $result = $this->row($queryData);
        endif;
        return $result;
    }

    public function getBillWiseTransaction($data){
        $queryData = array();
        $queryData['tableName'] = $this->transBillWise;
        $queryData['select'] = "trans_billwise.*, trans_billwise.amount - IFNULL((SELECT sum(tb.amount) FROM trans_billwise AS tb WHERE tb.ag_ref_id = trans_billwise.id AND tb.is_delete = 0), 0) as pending_amount,trans_main.vou_name_s,(CASE WHEN trans_billwise.trans_main_id = 0 AND trans_billwise.entry_type = 0 THEN (SELECT abs(IFNULL(opening_balance,0)) as op_balance FROM party_master WHERE party_master.id = trans_billwise.party_id AND party_master.is_delete = 0) ELSE IF(trans_main.vou_name_s IN ('BCRct','BCPmt'), IFNULL((trans_main.net_amount + abs(trans_main.round_off_amount)),0), IFNULL((trans_main.net_amount - abs(trans_main.tds_amount)),0)) END) as net_amount";

        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_billwise.trans_main_id";

        if(!empty($data['id'])):
            $queryData['where']['trans_billwise.id'] = $data['id'];
            $result = $this->row($queryData);
        endif;

        if(!empty($data['ag_ref_id'])):
            $queryData['where']['trans_billwise.ag_ref_id'] = $data['ag_ref_id'];
            $result = $this->rows($queryData);
        endif;        
        return $result;
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $transData = $this->getBillWiseTransaction(['id'=>$data['id']]);
            $agRefData = $this->getBillWiseTransaction(['ag_ref_id'=>$data['id']]);

            $pendingAmount = $transData->pending_amount;
            foreach($data['billWise'] as $row):
                if(!empty($row['amount']) && floatval($row['amount']) > 0):
                    $refData = $this->getBillWiseTransaction(['id'=>$row['ag_ref_id']]);
                    $row['c_or_d'] = ($refData->c_or_d == "CR")?"DR":"CR";
                    $row['p_or_m'] = ($refData->p_or_m == 1)?-1:1;
                    
                    $this->store($this->transBillWise,$row);

                    $pendingAmount -= floatval($row['amount']);
                endif;
            endforeach;

            
            if(!empty($agRefData)):
                $setData = [];
                $setData['tableName'] = $this->transBillWise;
                $setData['where']['id'] = $transData->id;
                $setData['update']['amount'] = "(SELECT SUM(amount) as amount FROM trans_billwise WHERE ag_ref_id = ".$transData->id." AND is_delete = 0)";
                $this->setValue($setData);
            else:
                $this->remove($this->transBillWise,['id'=>$transData->id]);
                if($pendingAmount > 0):
                    $newTransRef = (array) $transData;
                    $newTransRef['id'] = "";
                    $newTransRef['amount'] = $pendingAmount;
                    unset( $newTransRef['pending_amount'], $newTransRef['vou_name_s'], $newTransRef['net_amount']);

                    $this->store($this->transBillWise,$newTransRef);
                endif;
            endif;

            $result = ['status'=>1,'message'=>'Bill Wise Reference saved successfully.'];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function removeSettlement($id){
        try{
            $this->db->trans_begin();

            $transData = $this->getBillWiseTransaction(['id'=>$id]);

            $queryData = [];
            $queryData['tableName'] = $this->transBillWise;
            $queryData['where']['trans_main_id'] = $transData->trans_main_id;
            $queryData['where']['party_id'] = $transData->party_id;
            $queryData['where']['entry_type'] = $transData->entry_type;
            
            $queryData['where']['ref_type'] = 1;
            $billWiseData = $this->row($queryData);

            $queryData = [];
            $queryData['tableName'] = $this->transBillWise;
            $queryData['select'] = "IFNULL(SUM(amount),0) as settled_amount";
            $queryData['where']['trans_main_id'] = $transData->trans_main_id;
            
            $queryData['where']['ref_type'] = 2;
            $settledData = $this->row($queryData);  
            
            if(empty($billWiseData)):
                $newTransRef = (array) $transData;
                $newTransRef['id'] = "";
                $newTransRef['ref_type'] = 1;
                $newTransRef['ag_ref_id'] = 0;
                $newTransRef['amount'] = (($transData->net_amount - $settledData->settled_amount) + $transData->amount);//($transData->trans_number == "OpBal")?$transData->net_amount:
                unset( $newTransRef['pending_amount'], $newTransRef['vou_name_s'], $newTransRef['net_amount']);
                
                $this->store($this->transBillWise,$newTransRef);
            else:
                $setData = [];
                $setData['tableName'] = $this->transBillWise;
                $setData['where']['id'] = $billWiseData->id;
                $setData['set']['amount'] = "amount, + ".floatval($transData->amount);
                $this->setValue($setData);
            endif;

            $result = $this->remove($this->transBillWise,['id'=>$id],'Reference');

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