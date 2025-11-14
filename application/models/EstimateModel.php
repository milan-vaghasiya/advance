<?php
class EstimateModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $transExpense = "trans_expense";
    private $transDetails = "trans_details";
    private $stockTrans = "stock_transaction";
    private $partyMaster = "party_master";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.*";

        $data['where']['trans_main.entry_type'] = $data['entry_type'];

        if($data['status'] == 0):
            $data['where']['trans_main.trans_status !='] = 3;
        elseif($data['status'] == 1):
            $data['where']['trans_main.trans_status'] = 3;
        endif;

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
            endif;
            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];

            if(!empty($data['id'])):
                $this->trash($this->transChild,['trans_main_id'=>$data['id']]);
                $this->trash($this->transExpense,['trans_main_id'=>$data['id']]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"ES TERMS"]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"ES MASTER DETAILS"]);
                $this->remove($this->stockTrans,['main_ref_id'=>$data['id'],'entry_type'=>$data['entry_type']]);
            endif;
            
            if($data['memo_type'] == "CASH"):
				$cashAccData = $this->party->getParty(['system_code'=>"CASHACC"]);
				$data['opp_acc_id'] = $cashAccData->id;
			else:
				$data['opp_acc_id'] = $data['party_id'];
			endif;

            $data['ledger_eff'] = 0;
            $data['gstin'] = (!empty($data['gstin']))?$data['gstin']:"URP";
            $data['disc_amount'] = array_sum(array_column($data['itemData'],'disc_amount'));;
            $data['total_amount'] = $data['taxable_amount'] + $data['disc_amount'];
            $data['gst_amount'] = 0;//$data['igst_amount'] + $data['cgst_amount'] + $data['sgst_amount'];

            $accType = getSystemCode("Sale",false);
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
            $termsData = (!empty($data['termsData']))?$data['termsData']:array();

            unset($transExp['exp_amount'],$data['itemData'],$data['expenseData'],$data['termsData'],$data['masterDetails']);		

            $result = $this->store($this->transMain,$data,'Estimate');

            if(!empty($masterDetails)):
                $masterDetails['id'] = "";
                $masterDetails['main_ref_id'] = $result['id'];
                $masterDetails['table_name'] = $this->transMain;
                $masterDetails['description'] = "ES MASTER DETAILS";
                $this->store($this->transDetails,$masterDetails);
            endif;

            $expenseData = array();
            if($expAmount <> 0):				
				$expenseData = $transExp;
                $expenseData['id'] = "";
				$expenseData['trans_main_id'] = $result['id'];
                $this->store($this->transExpense,$expenseData);
			endif;

            if(!empty($termsData)):
                foreach($termsData as $row):
                    $row['id'] = "";
                    $row['table_name'] = $this->transMain;
                    $row['description'] = "ES TERMS";
                    $row['main_ref_id'] = $result['id'];
                    $this->store($this->transDetails,$row);
                endforeach;
            endif;

            $i=1;
            foreach($itemData as $row):
                $row['entry_type'] = $data['entry_type'];
                $row['trans_main_id'] = $result['id'];
                $row['gst_amount'] = $row['igst_amount'] = $row['cgst_amount'] = $row['sgst_amount'] = 0;
                $row['is_delete'] = 0;

                $itemTrans = $this->store($this->transChild,$row);

                if($row['stock_eff'] == 1):
                    $stockData = [
                        'id' => "",
                        'entry_type' => $data['entry_type'],
                        'unique_id' => 0,
                        'ref_date' => $data['trans_date'],
                        'ref_no' => $data['trans_number'],
                        'main_ref_id' => $result['id'],
                        'child_ref_id' => $itemTrans['id'],
                        'location_id' => $this->RTD_STORE->id,
                        'batch_no' => "GB",
                        'party_id' => $data['party_id'],
                        'item_id' => $row['item_id'],
                        'p_or_m' => -1,
                        'qty' => $row['qty'],
                        'price' => $row['price']
                    ];

                    $this->store($this->stockTrans,$stockData);
                endif;
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
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['trans_number'] = $data['trans_number'];
        $queryData['where']['entry_type'] = $this->data['entryData']->id;
        $queryData['where']['trans_date >='] = $this->startYearDate;
        $queryData['where']['trans_date <='] = $this->endYearDate;

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getEstimate($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.*,trans_details.i_col_1 as bill_per,trans_details.t_col_1 as contact_person,trans_details.t_col_2 as contact_no,trans_details.t_col_3 as ship_address";
        $queryData['leftJoin']['trans_details'] = "trans_main.id = trans_details.main_ref_id AND trans_details.description = 'ES MASTER DETAILS' AND trans_details.table_name = '".$this->transMain."'";
        $queryData['where']['trans_main.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
            $result->itemList = $this->getEstimateItems($data);
        endif;

        $queryData = array();
        $queryData['tableName'] = $this->transExpense;
        $queryData['where']['trans_main_id'] = $data['id'];
        $result->expenseData = $this->row($queryData);

        $queryData = array();
        $queryData['tableName'] = $this->transDetails;
        $queryData['select'] = "i_col_1 as term_id,t_col_1 as term_title,t_col_2 as condition";
        $queryData['where']['main_ref_id'] = $data['id'];
        $queryData['where']['table_name'] = $this->transMain;
        $queryData['where']['description'] = "ES TERMS";
        $result->termsConditions = $this->rows($queryData);

        return $result;
    }

    public function getEstimateItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*";
        $queryData['where']['trans_child.trans_main_id'] = $data['id'];
        $result = $this->rows($queryData);
        return $result;
    }

    public function getEstimateItem($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['id'] = $data['id'];
        $result = $this->row($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
           
            $this->trash($this->transExpense,['trans_main_id'=>$id]);
            $this->trash($this->transChild,['trans_main_id'=>$id]);
            
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"ES TERMS"]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"ES MASTER DETAILS"]);

            $this->remove($this->stockTrans,['main_ref_id'=>$id,'entry_type'=>$this->data['entryData']->id]);

            $result = $this->trash($this->transMain,['id'=>$id],'Estimate');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getEstimatePaymentDTRows($data){
        $data['tableName'] = $this->transDetails;
        $data['select'] = "trans_details.id,trans_details.main_ref_id,trans_details.date_col_1 as entry_date,trans_details.i_col_1 as party_id,trans_details.t_col_1 as received_by,trans_details.t_col_3 as reference_no,trans_details.d_col_1 as amount,trans_details.t_col_2 as remark,party_master.party_name";

        $data['leftJoin']['party_master'] = "party_master.id = trans_details.i_col_1";

        $data['where']['trans_details.table_name'] = $this->transMain;
        $data['where']['trans_details.description'] = "EST PAYMENT";

        $data['where']['trans_details.date_col_1 >='] = $this->startYearDate;
        $data['where']['trans_details.date_col_1 <='] = $this->endYearDate;

        $data['order_by']['trans_details.date_col_1'] = "DESC";
        $data['order_by']['trans_details.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(trans_details.date_col_1,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "trans_details.t_col_1";
        $data['searchCol'][] = "trans_details.t_col_3";
        $data['searchCol'][] = "trans_details.d_col_1";
        $data['searchCol'][] = "trans_details.t_col_2";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function saveEstimatePayment($data){
        try{
            $this->db->trans_begin();

            $postData = [
                'id' => ((!empty($data['id']))?$data['id']:""),
                'main_ref_id' => $data['main_ref_id'],
                'i_col_1' => $data['party_id'],
                'table_name' => $this->transMain,
                'description' => "EST PAYMENT",
                'date_col_1' => $data['entry_date'],
                't_col_1' => $data['received_by'],
                't_col_3' => $data['reference_no'],
                'd_col_1' => $data['amount'],
                't_col_2' => $data['remark']
            ];
            $result = $this->store($this->transDetails,$postData,'Payment');

            if(!empty($data['main_ref_id'])):
                $setData = array();
                $setData['tableName'] = $this->transMain;
                $setData['where']['id'] = $data['main_ref_id'];
                $setData['set']['rop_amount'] = 'rop_amount, + '.$data['amount'];
                $this->setValue($setData);
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

    public function getEstimatePayments($data){
        $queryData = array();
        $queryData['tableName'] = $this->transDetails;
        $queryData['select'] = "id,main_ref_id,date_col_1 as entry_date,t_col_1 as received_by,d_col_1 as amount,t_col_2 as remark";
        $queryData['where']['main_ref_id'] = $data['main_ref_id'];
        $queryData['where']['table_name'] = $this->transMain;
        $queryData['where']['description'] = "EST PAYMENT";
        $result = $this->rows($queryData);
        return $result;
    }

    public function getEstimatePayment($data){
        $queryData = array();
        $queryData['tableName'] = $this->transDetails;
        $queryData['select'] = "trans_details.id,trans_details.main_ref_id,trans_details.date_col_1 as entry_date,trans_details.i_col_1 as party_id,trans_details.t_col_1 as received_by,trans_details.t_col_3 as reference_no,trans_details.d_col_1 as amount,trans_details.t_col_2 as remark";
        $queryData['where']['id'] = $data['id'];
        $result = $this->row($queryData);
        return $result;
    }

    public function deleteEstimatePayment($id){
        try{
            $this->db->trans_begin();
            
            $vouData = $this->getEstimatePayment(['id'=>$id]);

            if(!empty($vouData->main_ref_id)):
                $setData = array();
                $setData['tableName'] = $this->transMain;
                $setData['where']['id'] = $vouData->main_ref_id;
                $setData['set']['rop_amount'] = 'rop_amount, - '.$vouData->amount;
                $this->setValue($setData);
            endif;
            
            $result = $this->trash($this->transDetails,['id'=>$id],'Payment');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getLedgerSummary($data){
        $party_id = ((!empty($data['acc_id']))?" AND trans_main.party_id = ".$data['acc_id']:"");
        $party_master_id = ((!empty($data['acc_id']))?"AND party_master.id = ".$data['acc_id']:"");
        $acc_id = ((!empty($data['acc_id']))?"AND trans_details.i_col_1 = ".$data['acc_id']:"");

        $query = $this->db->query(" SELECT party_master.id as party_id, party_master.party_name, 
            (IFNULL(party_master.other_op_balance,0) + IFNULL(es.op_amount,0) + IFNULL(pm.op_amount,0)) as other_op_bal, 
            (IFNULL(party_master.other_op_balance,0) + IFNULL(es.op_amount,0) + IFNULL(pm.op_amount,0) + IFNULL(es.cl_amount,0) + IFNULL(pm.cl_amount,0)) as other_cl_bal
            FROM party_master
            LEFT JOIN (
                SELECT trans_main.party_id,SUM((CASE WHEN trans_main.trans_date < '".$data['from_date']."' THEN (trans_main.net_amount * -1) ELSE 0 END)) as op_amount,SUM((CASE WHEN trans_main.trans_date >= '".$data['from_date']."' AND trans_main.trans_date <= '".$data['to_date']."' THEN (trans_main.net_amount * -1) ELSE 0 END)) as cl_amount
                FROM trans_main
                WHERE trans_main.vou_name_s = 'ES'
                AND trans_main.is_delete = 0
                ".$party_id."
                GROUP BY trans_main.party_id
            ) as es ON es.party_id = party_master.id
            LEFT JOIN (
                SELECT trans_details.i_col_1 as party_id, SUM((CASE WHEN trans_details.date_col_1 < '".$data['from_date']."' THEN trans_details.d_col_1 ELSE 0 END)) as op_amount,SUM((CASE WHEN trans_details.date_col_1 >= '".$data['from_date']."' AND trans_details.date_col_1 <= '".$data['to_date']."' THEN trans_details.d_col_1 ELSE 0 END)) as cl_amount
                FROM trans_details 
                WHERE trans_details.description = 'EST PAYMENT'
                AND trans_details.table_name = 'trans_main'
                AND trans_details.is_delete = 0
                ".$acc_id."
                GROUP BY trans_details.i_col_1
            ) as pm ON pm.party_id = party_master.id
            WHERE party_master.is_delete = 0             
            AND party_master.group_code = 'SD'
            ".$party_master_id."
            ORDER BY party_master.party_name
        ");

        if(!empty($data['acc_id'])):
            $result = $query->row();
        else:
            $result = $query->result();
        endif;
        return $result;
    }

    public function getLedgerDetails($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "id,vou_name_s,trans_date,trans_number,net_amount as dr_amount, 0 as cr_amount, '-1' as p_or_m,net_amount as amount";
        $queryData['where']['entry_type'] = $this->data['entryData']->id;
        $queryData['where']['party_id'] = $data['acc_id'];
        $queryData['where']['trans_date >='] = $data['from_date'];
        $queryData['where']['trans_date <='] = $data['to_date'];
        $estimate = $this->rows($queryData);

        $queryData = array();
        $queryData['tableName'] = $this->transDetails;
        $queryData['select'] = "trans_details.id,'CashRct' as vou_name_s,trans_details.date_col_1 as trans_date,'' as trans_number,0 as dr_amount,trans_details.d_col_1 as cr_amount,'1' as p_or_m,trans_details.d_col_1 as amount";
        $queryData['where']['trans_details.i_col_1'] = $data['acc_id'];
        $queryData['where']['trans_details.date_col_1 >='] = $data['from_date'];
        $queryData['where']['trans_details.date_col_1 <='] = $data['to_date'];
        $queryData['where']['trans_details.d_col_1 > '] = 0;
        $estimatePayment = $this->rows($queryData);

        $ledgerDetails = array_merge($estimate,$estimatePayment);
        array_multisort(array_column($ledgerDetails, 'trans_date'), SORT_ASC, $ledgerDetails);
        return $ledgerDetails;
    }

    public function saveOpeningBalance($data){
        try{
            $this->db->trans_begin();

            if(!empty($data['id'])):
				$data['other_op_balance'] = floatval(($data['other_op_balance'] * $data['balance_type']));
                unset($data['balance_type']);

                $this->store("party_master",$data);
            else:
                return ['status'=>2,'message'=>'Somthing is wrong...Ledger not found.'];
            endif;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Ledger Opening Balance updated successfully.'];
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

}
?>