<?php
class PurchaseInvoiceModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $transExpense = "trans_expense";
    private $transDetails = "trans_details";
    private $stockTrans = "stock_transaction";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "id,trans_number,trans_date,party_name,taxable_amount,gst_amount,net_amount";

        $data['where']['trans_main.entry_type'] = $data['entry_type'];

        $data['where']['trans_main.trans_date >='] = $this->startYearDate;
        $data['where']['trans_main.trans_date <='] = $this->endYearDate;

        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_main.taxable_amount";
        $data['searchCol'][] = "trans_main.gst_amount";
        $data['searchCol'][] = "trans_main.net_amount";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if(empty($data['id'])):
                $data['trans_no'] = $this->transMainModel->nextTransNo($data['entry_type']);
                $data['doc_no'] = $data['trans_prefix'].$data['trans_no'];
            endif;

            if(!empty($data['id'])):
                $dataRow = $this->getPurchaseInvoice(['id'=>$data['id'],'itemList'=>1]);

                $checkBillWiseRef = $this->transMainModel->checkBillWiseRef(['id'=>$dataRow->id,'party_id'=>$dataRow->party_id,'entry_type'=>$dataRow->entry_type]);
                if($checkBillWiseRef == true):
                    return ['status'=>2,'message'=>'Bill Wise Reference already adjusted. if you want to update this entry first unset all adjustment.'];
                endif;

                foreach($dataRow->itemList as $row):
					if(!empty($row->ref_id) && $row->from_entry_type == 9):
                        $setData = array();
                        $setData['tableName'] = "grn_trans";
                        $setData['where']['id'] = $row->ref_id;
                        $setData['set_value']['inv_qty'] = 'IF(`inv_qty` - '.$row->qty.' >= 0, `inv_qty` - '.$row->qty.', 0)';
                        $setData['update']['trans_status'] = "(CASE WHEN inv_qty >= qty THEN 1 ELSE 0 END)";
                        $this->setValue($setData);
                    endif;

                    if(!empty($row->ref_id) && $row->from_entry_type != 9):
                        $setData = array();
                        $setData['tableName'] = 'po_trans';
                        $setData['where']['id'] = $row->ref_id;
                        $setData['set_value']['dispatch_qty'] = 'IF(`dispatch_qty` - '.$row->qty.' >= 0, `dispatch_qty` - '.$row->qty.', 0)';
                        $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 0 END)";
                        $this->setValue($setData);
                    endif;

                    $this->trash($this->transChild,['id'=>$row->id]);
                endforeach;

				if(!empty($dataRow->ref_id) && $dataRow->from_entry_type == 9):
                    $oldRefIds = explode(",",$dataRow->ref_id);
                    foreach($oldRefIds as $main_id):
                        $setData = array();
                        $setData['tableName'] = "grn_master";
                        $setData['where']['id'] = $main_id;
                        $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM grn_trans WHERE mir_id = ".$main_id." AND is_delete = 0)";
                        $this->setValue($setData);
                    endforeach;
                endif;

                if(!empty($dataRow->ref_id) && $dataRow->from_entry_type != 9):
                    $oldRefIds = explode(",",$dataRow->ref_id);
                    foreach($oldRefIds as $main_id):
                        $setData = array();
                        $setData['tableName'] = 'po_master';
                        $setData['where']['id'] = $main_id;
                        $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM po_trans WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
                        $this->setValue($setData);
                    endforeach;
                endif;

                $this->trash($this->transExpense,['trans_main_id'=>$data['id']]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"PURINV TERMS"]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"PURINV MASTER DETAILS"]);
                $this->remove($this->stockTrans,['main_ref_id'=>$data['id'],'entry_type'=>$data['entry_type']]);
            endif;
            
            if($data['memo_type'] == "CASH"):
				$cashAccData = $this->party->getParty(['system_code'=>"CASHACC"]);
				$data['opp_acc_id'] = $cashAccData->id;
			else:
				$data['opp_acc_id'] = $data['party_id'];
			endif;
            $data['p_or_m'] = 1;
            $data['ledger_eff'] = 1;
            $data['gstin'] = (!empty($data['gstin']))?$data['gstin']:"URP";
            $data['disc_amount'] = array_sum(array_column($data['itemData'],'disc_amount'));;
            $data['total_amount'] = $data['taxable_amount'] + $data['disc_amount'];
            $data['igst_amount'] = (!empty($data['igst_amount']))?$data['igst_amount']:0;
            $data['cgst_amount'] = (!empty($data['cgst_amount']))?$data['cgst_amount']:0;
            $data['sgst_amount'] = (!empty($data['sgst_amount']))?$data['sgst_amount']:0;
            $data['gst_amount'] = $data['igst_amount'] + $data['cgst_amount'] + $data['sgst_amount'];

            $accType = getSystemCode($data['vou_name_s'],false);
            if(!empty($accType)):
				$spAcc = $this->party->getParty(['system_code'=>$accType]);
                $data['vou_acc_id'] = (!empty($spAcc))?$spAcc->id:0;
            else:
                $data['vou_acc_id'] = 0;
            endif;

            $masterDetails = (isset($data['masterDetails']))?$data['masterDetails']:array();
            $itemData = $data['itemData'];

            $transExp = getExpArrayMap(((!empty($data['expenseData']))?$data['expenseData']:array()));
			$expAmount = $transExp['exp_amount'];
            $termsData = (!empty($data['conditions']))?$data['conditions']:array();

            unset($transExp['exp_amount'],$data['itemData'],$data['expenseData'],$data['conditions'],$data['masterDetails']);		

            $result = $this->store($this->transMain,$data,'Purchase Invoice');

            if(!empty($masterDetails)):
                $masterDetails['id'] = "";
                $masterDetails['main_ref_id'] = $result['id'];
                $masterDetails['table_name'] = $this->transMain;
                $masterDetails['description'] = "PURINV MASTER DETAILS";
                $this->store($this->transDetails,$masterDetails);
            endif;

            $expenseData = array();
            if($expAmount <> 0):				
				$expenseData = $transExp;
			endif;

            if(!empty($termsData)):
                $termsData = [
                    'id' =>"",
                    'table_name' => $this->transMain,
                    'description' => "PURINV TERMS",
                    'main_ref_id' => $result['id'],
                    't_col_1' => $termsData
                ];
                $this->store($this->transDetails,$termsData);
            endif;

            $i=1;
            foreach($itemData as $row):
                $row['entry_type'] = $data['entry_type'];
                $row['trans_main_id'] = $result['id'];
                $row['is_delete'] = 0;
                $itemTrans = $this->store($this->transChild,$row);

                if($row['stock_eff'] == 1):
                    $stockData = [
                        'id' => "",
                        'entry_type' => $data['entry_type'],
                        'unique_id' => 0, //$this->transMainModel->getStockUniqueId(['location_id' => $this->RTD_STORE->id,'batch_no' => "GB",'item_id' => $row['item_id']]),
                        'ref_date' => $data['trans_date'],
                        'ref_no' => $data['trans_number'],
                        'main_ref_id' => $result['id'],
                        'child_ref_id' => $itemTrans['id'],
                        'location_id' => $this->RTD_STORE->id,
                        'batch_no' => "GB",
                        'party_id' => $data['party_id'],
                        'item_id' => $row['item_id'],
                        'p_or_m' => 1,
                        'qty' => $row['qty'],
                        'price' => $row['price']
                    ];

                    $this->store($this->stockTrans,$stockData);
                endif;

			if(!empty($row['ref_id']) && $row['from_entry_type'] == 9):
                    $setData = array();
                    $setData['tableName'] = "grn_trans";
                    $setData['where']['id'] = $row['ref_id'];
                    $setData['set']['inv_qty'] = 'inv_qty, + '.$row['qty'];
                    $setData['update']['trans_status'] = "(CASE WHEN inv_qty >= qty THEN 1 ELSE 0 END)";
                    $this->setValue($setData);
                endif;

                if(!empty($row['ref_id']) && $row['from_entry_type'] != 9):
                    $setData = array();
                    $setData['tableName'] = 'po_trans';
                    $setData['where']['id'] = $row['ref_id'];
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$row['qty'];
                    $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 0 END)";
                    $this->setValue($setData);
                endif;
            endforeach;

			if(!empty($data['ref_id']) && $data['from_entry_type'] == 9):
                $refIds = explode(",",$data['ref_id']);
                foreach($refIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = "grn_master";
                    $setData['where']['id'] = $main_id;
                    $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM grn_trans WHERE mir_id = ".$main_id." AND is_delete = 0)";
                    $this->setValue($setData);
                endforeach;
            endif;

            if(!empty($data['ref_id']) && $data['from_entry_type'] != 9):
                $refIds = explode(",",$data['ref_id']);
                foreach($refIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = 'po_master';
                    $setData['where']['id'] = $main_id;
                    $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM po_trans WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
                    $this->setValue($setData);
                endforeach;
            endif;
            
            $data['id'] = $result['id'];
            $this->transMainModel->ledgerEffects($data,$expenseData);

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
        $queryData['tableName'] = $this->transMain;

        $queryData['where']['doc_no'] = $data['doc_no'];
        $queryData['where']['party_id'] = $data['party_id'];
        $queryData['where']['entry_type'] = $data['entry_type'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getPurchaseInvoice($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.*,trans_details.t_col_1 as contact_person,trans_details.t_col_2 as contact_no,trans_details.t_col_3 as ship_address";
        $queryData['leftJoin']['trans_details'] = "trans_main.id = trans_details.main_ref_id AND trans_details.description = 'PURINV MASTER DETAILS' AND trans_details.table_name = '".$this->transMain."'";
        $queryData['where']['trans_main.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
            $result->itemList = $this->getPurchaseInvoiceItems($data);
        endif;

        $queryData = array();
        $queryData['tableName'] = $this->transExpense;
        $queryData['where']['trans_main_id'] = $data['id'];
        $result->expenseData = $this->row($queryData);

        $queryData = array();
        $queryData['tableName'] = $this->transDetails;
        $queryData['select'] = "t_col_1 as condition";
        $queryData['where']['main_ref_id'] = $data['id'];
        $queryData['where']['table_name'] = $this->transMain;
        $queryData['where']['description'] = "PURINV TERMS";
        $result->termsConditions = $this->row($queryData);

        return $result;
    }

    public function getPurchaseInvoiceItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*";
        $queryData['where']['trans_child.trans_main_id'] = $data['id'];
        $result = $this->rows($queryData);
        return $result;
    }

    public function getPurchaseInvoiceItem($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['id'] = $data['id'];
        $result = $this->row($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $postData["table_name"] = $this->transMain;
            $postData['where'] = [['column_name'=>'from_entry_type','column_value'=>$this->data['entryData']->id]];
            $postData['find'] = [['column_name'=>'ref_id','column_value'=>$id]];
            $checkRef = $this->checkEntryReference($postData);
            if($checkRef['status'] == 0):
                return $checkRef;
            endif;

            $dataRow = $this->getPurchaseInvoice(['id'=>$id,'itemList'=>1]);

            $checkBillWiseRef = $this->transMainModel->checkBillWiseRef(['id'=>$dataRow->id,'party_id'=>$dataRow->party_id,'entry_type'=>$dataRow->entry_type]);
            if($checkBillWiseRef == true):
                return ['status'=>0,'message'=>'Bill Wise Reference already adjusted. if you want to delete this entry first unset all adjustment.'];
            endif;

            foreach($dataRow->itemList as $row):
                if(!empty($row->ref_id) && $row->from_entry_type == 9):
                    $setData = array();
                    $setData['tableName'] = "grn_trans";
                    $setData['where']['id'] = $row->ref_id;
                    $setData['set_value']['inv_qty'] = 'IF(`inv_qty` - '.$row->qty.' >= 0, `inv_qty` - '.$row->qty.', 0)';
                    $setData['update']['trans_status'] = "(CASE WHEN inv_qty >= qty THEN 1 ELSE 0 END)";
                    $this->setValue($setData);
                endif;

                if(!empty($row->ref_id) && $row->from_entry_type != 9):
                    $setData = array();
                    $setData['tableName'] = 'po_trans';
                    $setData['where']['id'] = $row->ref_id;
                    $setData['set_value']['dispatch_qty'] = 'IF(`dispatch_qty` - '.$row->qty.' >= 0, `dispatch_qty` - '.$row->qty.', 0)';
                    $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 0 END)";
                    $this->setValue($setData);
                endif;

                $this->trash($this->transChild,['id'=>$row->id]);
            endforeach;

			if(!empty($dataRow->ref_id) && $dataRow->from_entry_type == 9):
                $oldRefIds = explode(",",$dataRow->ref_id);
                foreach($oldRefIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = "grn_master";
                    $setData['where']['id'] = $main_id;
                    $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM grn_trans WHERE mir_id = ".$main_id." AND is_delete = 0)";
                    $this->setValue($setData);
                endforeach;
            endif;

            if(!empty($dataRow->ref_id) && $dataRow->from_entry_type != 9):
                $oldRefIds = explode(",",$dataRow->ref_id);
                foreach($oldRefIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = 'po_master';
                    $setData['where']['id'] = $main_id;
                    $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM po_trans WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
                    $this->setValue($setData);
                endforeach;
            endif;

            $this->transMainModel->deleteLedgerTrans($id);

            $this->trash($this->transExpense,['trans_main_id'=>$id]);
            
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"PURINV TERMS"]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"PURINV MASTER DETAILS"]);

            $this->remove($this->stockTrans,['main_ref_id'=>$dataRow->id,'entry_type'=>$dataRow->entry_type]);

            $result = $this->trash($this->transMain,['id'=>$id],'Purchase Invoice');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getPendingInvoiceItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*,(trans_child.qty - trans_child.dispatch_qty) as pending_qty,trans_main.entry_type as main_entry_type,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,";

        $queryData['leftJoin']['trans_main'] = "trans_child.trans_main_id = trans_main.id";

        $queryData['where']['trans_main.id'] = $data['id'];
        $queryData['where']['trans_child.entry_type'] = $this->data['entryData']->id;
        $queryData['where']['(trans_child.qty - trans_child.dispatch_qty) >'] = 0;
        return $this->rows($queryData);
    }

}
?>