<?php
class ProformaInvoiceModel extends MasterModel{
    private $pinvMaster = "pinv_master";
    private $pinvTrans = "pinv_trans";
    private $transExpense = "trans_expense";
    private $transDetails = "trans_details";

    public function getDTRows($data){
        $data['tableName'] = $this->pinvMaster;
        $data['select'] = "pinv_master.*,party_master.party_name";
        $data['leftJoin']['party_master'] = "party_master.id = pinv_master.party_id";

        $data['where']['pinv_master.entry_type'] = $data['entry_type'];
        if(empty($data['trans_status'])){
            $data['where']['pinv_master.trans_status'] = $data['trans_status'];
            $data['where']['pinv_master.is_approve'] = 0;
        }else{
            $data['where']['pinv_master.is_approve >'] = 0;

        }
        $data['where']['pinv_master.trans_date >='] = $this->startYearDate;
        $data['where']['pinv_master.trans_date <='] = $this->endYearDate;

        $data['order_by']['pinv_master.trans_no'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "pinv_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(pinv_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "pinv_master.party_name";
        $data['searchCol'][] = "pinv_master.taxable_amount";
        $data['searchCol'][] = "pinv_master.gst_amount";
        $data['searchCol'][] = "pinv_master.net_amount";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            unset($data['tcs_per'],$data['tcs_amount']);
            if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_number'] = "PINV. No. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if(!empty($data['id'])):
                $dataRow = $this->getProformaInvoice(['id'=>$data['id'],'itemList'=>1]);
                foreach($dataRow->itemList as $row):
                    $this->trash($this->pinvTrans,['id'=>$row->id]);
                endforeach;

                $this->trash($this->transExpense,['trans_main_id'=>$data['id'],'entry_type'=>$this->data['entryData']->id]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->pinvMaster,'description'=>"PINV TERMS"]);
            endif;
            
            $itemData = $data['itemData'];

            $transExp = getExpArrayMap(((!empty($data['expenseData']))?$data['expenseData']:array()));
			$expAmount = $transExp['exp_amount'];
            $termsData = (!empty($data['conditions']))?$data['conditions']:array();

            unset($transExp['exp_amount'],$data['itemData'],$data['expenseData'],$data['conditions']);		

            $result = $this->store($this->pinvMaster,$data,'Proforma Invoice');

            $expenseData = array();
            if($expAmount <> 0):				
				$expenseData = $transExp;
                $expenseData['id'] = "";
				$expenseData['trans_main_id'] = $result['id'];
				$expenseData['entry_type'] = $data['entry_type'];
                $this->store($this->transExpense,$expenseData);
			endif;

            if(!empty($termsData)):
                $termsData = [
                    'id' =>"",
                    'table_name' => $this->pinvMaster,
                    'description' => "PINV TERMS",
                    'main_ref_id' => $result['id'],
                    't_col_1' => $termsData
                ];
                $this->store($this->transDetails,$termsData);
            endif;

            $i=1;
            foreach($itemData as $row):
                $row['entry_type'] = $data['entry_type'];
                $row['trans_main_id'] = $result['id'];
                $row['cod_date'] = (!empty($row['cod_date']))?$row['cod_date']:NULL;
                $row['is_delete'] = 0;
                $this->store($this->pinvTrans,$row);
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

    public function checkDuplicate($data){
        $queryData['tableName'] = $this->pinvMaster;
        $queryData['where']['trans_number'] = $data['trans_number'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getProformaInvoice($data){
        $queryData = array();
        $queryData['tableName'] = $this->pinvMaster;
		$queryData['select'] = "pinv_master.*,employee_master.emp_name as created_name,party_master.party_name,so_master.doc_no as cust_po_no,so_master.doc_date as cust_po_date,apr.emp_name as approved_by";
        $queryData['leftJoin']['trans_details'] = "pinv_master.id = trans_details.main_ref_id AND trans_details.description = 'PINV MASTER DETAILS' AND trans_details.table_name = '".$this->pinvMaster."'";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = pinv_master.created_by";
		$queryData['leftJoin']['employee_master apr'] = "apr.id = pinv_master.is_approve";
        $queryData['leftJoin']['party_master'] = "party_master.id = pinv_master.party_id";
        $queryData['leftJoin']['so_master'] = "so_master.id = pinv_master.ref_id";

        $queryData['where']['pinv_master.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
            $result->itemList = $this->getProformaInvoiceItems($data);
        endif;

        $queryData = array();
        $queryData['tableName'] = $this->transExpense;
        $queryData['where']['trans_main_id'] = $data['id'];
		$queryData['where']['entry_type'] = $result->entry_type;
        $result->expenseData = $this->row($queryData);

        $queryData = array();
        $queryData['tableName'] = $this->transDetails;
        $queryData['select'] = "t_col_1 as condition";
        $queryData['where']['main_ref_id'] = $data['id'];
        $queryData['where']['table_name'] = $this->pinvMaster;
        $queryData['where']['description'] = "PINV TERMS";
        $result->termsConditions = $this->row($queryData);

        return $result;
    }

    public function getProformaInvoiceItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->pinvTrans;
        $queryData['select'] = "pinv_trans.*,item_master.item_name,item_master.hsn_code,item_master.uom,item_master.item_code";
        $queryData['leftJoin']['item_master'] = "item_master.id = pinv_trans.item_id";
        $queryData['where']['pinv_trans.trans_main_id'] = $data['id'];
        $result = $this->rows($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $postData["table_name"] = $this->pinvMaster;
            $postData['where'] = [['column_name'=>'from_entry_type','column_value'=>$this->data['entryData']->id]];
            $postData['find'] = [['column_name'=>'ref_id','column_value'=>$id]];
            $checkRef = $this->checkEntryReference($postData);
            if($checkRef['status'] == 0):
                $this->db->trans_rollback();
                return $checkRef;
            endif;

            $dataRow = $this->getProformaInvoice(['id'=>$id,'itemList'=>1]);
          
            foreach($dataRow->itemList as $row):
                $this->trash($this->pinvTrans,['id'=>$row->id]);
            endforeach;

            $this->trash($this->transExpense,['trans_main_id'=>$id,'entry_type'=>$this->data['entryData']->id]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->pinvMaster,'description'=>"PINV TERMS"]);
            $result = $this->trash($this->pinvMaster,['id'=>$id],'Proforma Invoice');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function approveProformaInvoice($data){
		try{
            $this->db->trans_begin();
			$date = ($data['is_approve'] == 1)?date('Y-m-d'):null;
			$isApprove =  ($data['is_approve'] == 1)?$this->loginId:0;
			
			$this->store($this->pinvMaster, ['id'=> $data['id'], 'is_approve' => $isApprove, 'approve_date'=>$date]);
			
			$result = ['status' => 1, 'message' => 'Proforma Invoice Approve successfully.'];
			
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