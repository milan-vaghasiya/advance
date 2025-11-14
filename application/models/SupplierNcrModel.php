<?php
class SupplierNcrModel extends MasterModel{
    private $ncrMaster = "ncr_master";

    public function getNextTransNo(){
        $data['tableName'] = $this->ncrMaster;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['YEAR(trans_date)'] = date("Y");
        $data['where']['MONTH(trans_date)'] = date("m");
        $maxNo = $this->specificRow($data)->trans_no;
        $nextNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextNo;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->ncrMaster;
        $data['select'] = "ncr_master.*,party_master.party_name,item_master.item_name,grn_master.doc_no,grn_trans.grn_id,grn_master.trans_number as grn_number";
        $data['leftJoin']['party_master'] = "ncr_master.party_id = party_master.id";
        $data['leftJoin']['item_master'] = "item_master.id = ncr_master.item_id";
        $data['leftJoin']['grn_trans'] = "grn_trans.id = ncr_master.grn_trans_id";
        $data['leftJoin']['grn_master'] = "grn_master.id = grn_trans.grn_id";
        $data['where']['ncr_master.status'] = $data['status'];

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "ncr_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(ncr_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "ncr_master.batch_no";
        $data['searchCol'][] = "ncr_master.qty";
        $data['searchCol'][] = "ncr_master.rej_qty";
        $data['searchCol'][] = "ncr_master.ref_of_complaint";
        $data['searchCol'][] = "ncr_master.complaint";
        $data['searchCol'][] = "ncr_master.product_returned";
        $data['searchCol'][] = "ncr_master.report_no";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "ncr_master.ref_feedback";
        $data['searchCol'][] = "ncr_master.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getNCR($data){
        $queryData['tableName'] = $this->ncrMaster;
        $queryData['where']['id'] = $data['id'];
        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->ncrMaster,$data);

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
        
            $result = $this->trash($this->ncrMaster,['id'=>$id]);
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
             return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getGrnNoByParty($data){
        $queryData['tableName'] = 'grn_master';
        $queryData['select'] = "grn_master.id,grn_master.trans_number,grn_master.doc_no";
        if(!empty($data['party_id'])){$queryData['where']['party_id'] = $data['party_id'];}
        $resultData = $this->rows($queryData);
        return $resultData;
    }

}