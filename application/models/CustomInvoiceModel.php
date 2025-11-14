<?php
class CustomInvoiceModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $transDetails = "trans_details";
    private $transExpense = "trans_expense";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.id, trans_main.trans_number, DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y') as trans_date, trans_main.party_name, trans_main.trans_status, trans_main.net_amount";

        $data['where']['trans_main.entry_type'] = $data['entry_type'];

        if($data['status'] == 0):
            $data['where']['trans_main.trans_status'] = 0;

            //$data['where']['trans_main.trans_date <='] = $this->endYearDate;
        elseif($data['status'] == 1):
            $data['where']['trans_main.trans_status'] = 1;

            /* $data['where']['trans_main.trans_date >='] = $this->startYearDate;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate; */
        endif;  
        
        $data['where']['trans_main.trans_date >='] = $this->startYearDate;
        $data['where']['trans_main.trans_date <='] = $this->endYearDate;

        $data['group_by'][] = "trans_main.id";
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.trans_no'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_main.net_amount";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];

            if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_number'] = "Inv. No. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if(!empty($data['id'])):
                $dataRow = $this->getCustomInvoice(['id'=>$data['id'],'itemList'=>1]);
                foreach($dataRow->itemList as $row):
                    if(!empty($row->ref_id)):
                        $setData = array();
                        $setData['tableName'] = $this->transChild;
                        $setData['where']['id'] = $row->ref_id;
                        $setData['set_value']['dispatch_qty'] = 'IF(`dispatch_qty` - '.$row->qty.' >= 0, `dispatch_qty` - '.$row->qty.', 0)';
                        $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 0 END)";
                        $this->setValue($setData);
                    endif;

                    $this->trash($this->transChild,['id'=>$row->id]);
                endforeach;

                if(!empty($dataRow->ref_id)):
                    $oldRefIds = explode(",",$dataRow->ref_id);
                    foreach($oldRefIds as $main_id):
                        $setData = array();
                        $setData['tableName'] = $this->transMain;
                        $setData['where']['id'] = $main_id;
                        $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM trans_child WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
                        $this->setValue($setData);
                    endforeach;
                endif;

                $this->trash($this->transExpense,['trans_main_id'=>$data['id']]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"CUSTINV MASTER DETAILS"]);                
            endif;
            
            $data['opp_acc_id'] = $data['party_id'];
            $data['gstin'] = (!empty($data['gstin']))?$data['gstin']:"URP";
            $data['disc_amount'] = array_sum(array_column($data['itemData'],'disc_amount'));
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

            $masterDetails = (!empty($data['masterDetails']))?$data['masterDetails']:array();
            $itemData = $data['itemData'];

            $transExp = getExpArrayMap(((!empty($data['expenseData']))?$data['expenseData']:array()));
			$expAmount = $transExp['exp_amount'];

            unset($data['itemData'],$data['masterDetails'],$transExp['exp_amount'],$data['expenseData']);		

            $result = $this->store($this->transMain,$data,'Custom Invoice');

            if(!empty($masterDetails)):
                $masterDetails['id'] = "";
                $masterDetails['main_ref_id'] = $result['id'];
                $masterDetails['table_name'] = $this->transMain;
                $masterDetails['description'] = "CUSTINV MASTER DETAILS";
                $masterDetails['date_col_1'] = (!empty($masterDetails['date_col_1']))?$masterDetails['date_col_1']:NULL;
                $this->store($this->transDetails,$masterDetails);
            endif;

            $expenseData = array();
            if($expAmount <> 0):				
				$expenseData = $transExp;
                $expenseData['id'] = "";
                $expenseData['trans_main_id'] = $result['id'];
                $this->store('trans_expense',$expenseData);
			endif;

            $i=1;
            foreach($itemData as $row):
                $row['entry_type'] = $data['entry_type'];
                $row['trans_main_id'] = $result['id'];
                $row['is_delete'] = 0;

                $itemTrans = $this->store($this->transChild,$row);

                if(!empty($row['ref_id'])):
                    $setData = array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $row['ref_id'];
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$row['qty'];
                    $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 0 END)";
                    $this->setValue($setData);
                endif;
            endforeach;

            if(!empty($data['ref_id'])):
                $refIds = explode(",",$data['ref_id']);
                foreach($refIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = $this->transMain;
                    $setData['where']['id'] = $main_id;
                    $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM trans_child WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
                    $this->setValue($setData);
                endforeach;
            endif;

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
        $queryData['where']['entry_type'] = $data['entry_type'];
        $queryData['where']['trans_number'] = $data['trans_number'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getCustomInvoice($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.*";

        $queryData['select'] .= ",trans_details.t_col_1 as consignee,
        trans_details.t_col_2 as buyer_address,
        trans_details.t_col_3 as terms_and_method_of_payment,
        
        trans_details.date_col_1 as date_of_departure,

        trans_details.s_col_1 as buyer_name,
        trans_details.s_col_2 as method_of_dispatch,
        trans_details.s_col_3 as type_of_shipment,
        trans_details.s_col_4 as country_of_origin,
        trans_details.s_col_5 as country_of_fd,
        trans_details.s_col_6 as port_of_loading,
        trans_details.s_col_7 as port_of_discharge,
        trans_details.s_col_8 as final_destination,
        trans_details.s_col_9 as delivery_type,
        trans_details.s_col_10 as delivery_location";

        $queryData['leftJoin']['trans_details'] = "trans_main.id = trans_details.main_ref_id AND trans_details.description = 'CUSTINV MASTER DETAILS' AND trans_details.table_name = '".$this->transMain."'";

        $queryData['where']['trans_main.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
            $result->itemList = $this->getCustomInvoiceItems($data);
        endif;

        $queryData = array();
        $queryData['tableName'] = $this->transExpense;
        $queryData['where']['trans_main_id'] = $data['id'];
        $result->expenseData = $this->row($queryData);

        return $result;
    }

    public function getCustomInvoiceItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*";
        $queryData['leftJoin']['item_master'] = "trans_child.item_id = item_master.id";
        $queryData['where']['trans_child.trans_main_id'] = $data['id'];
        $result = $this->rows($queryData);
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
                $this->db->trans_rollback();
                return $checkRef;
            endif;

            $dataRow = $this->getCustomInvoice(['id'=>$id,'itemList'=>1]);
            foreach($dataRow->itemList as $row):
                if(!empty($row->ref_id)):
                    $setData = array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $row->ref_id;
                    $setData['set_value']['dispatch_qty'] = 'IF(`dispatch_qty` - '.$row->qty.' >= 0, `dispatch_qty` - '.$row->qty.', 0)';
                    $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 0 END)";
                    $this->setValue($setData);
                endif;

                $this->trash($this->transChild,['id'=>$row->id]);
            endforeach;

            if(!empty($dataRow->ref_id)):
                $oldRefIds = explode(",",$dataRow->ref_id);
                foreach($oldRefIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = $this->transMain;
                    $setData['where']['id'] = $main_id;
                    $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM trans_child WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
                    $this->setValue($setData);
                endforeach;
            endif;
            
            $this->trash($this->transExpense,['trans_main_id'=>$id]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"CUSTINV MASTER DETAILS"]);
            $result = $this->trash($this->transMain,['id'=>$id],'Custom Invoice');

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