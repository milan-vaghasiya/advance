<?php
class GstIncomeModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $transExpense = "trans_expense";
    private $transDetails = "trans_details";

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

            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
            
            if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_number'] = "Inv. No. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if(!empty($data['id'])):
                $dataRow = $this->getGstIncome(['id'=>$data['id'],'itemList'=>0]);

                $checkBillWiseRef = $this->transMainModel->checkBillWiseRef(['id'=>$dataRow->id,'party_id'=>$dataRow->party_id,'entry_type'=>$dataRow->entry_type]);
                if($checkBillWiseRef == true):
                    return ['status'=>2,'message'=>'Bill Wise Reference already adjusted. if you want to update this entry first unset all adjustment.'];
                endif;

                $this->trash($this->transChild,['trans_main_id'=>$data['id']]);
                $this->trash($this->transExpense,['trans_main_id'=>$data['id']]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"GINC TERMS"]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"GINC MASTER DETAILS"]);
            endif;
            
            if($data['memo_type'] == "CASH"):
				$cashAccData = $this->party->getParty(['system_code'=>"CASHACC"]);
				$data['opp_acc_id'] = $cashAccData->id;
			else:
				$data['opp_acc_id'] = $data['party_id'];
			endif;
            $data['p_or_m'] = -1;
            $data['ledger_eff'] = 1;
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

            $masterDetails = (isset($data['masterDetails']))?$data['masterDetails']:array();
            $itemData = $data['itemData'];

            $transExp = getExpArrayMap(((!empty($data['expenseData']))?$data['expenseData']:array()));
			$expAmount = $transExp['exp_amount'];
            $termsData = (!empty($data['conditions']))?$data['conditions']:array();

            unset($transExp['exp_amount'],$data['itemData'],$data['expenseData'],$data['conditions'],$data['masterDetails']);		

            $result = $this->store($this->transMain,$data,'GST Income');

            if(!empty($masterDetails)):
                $masterDetails['id'] = "";
                $masterDetails['main_ref_id'] = $result['id'];
                $masterDetails['table_name'] = $this->transMain;
                $masterDetails['description'] = "GINC MASTER DETAILS";
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
                    'description' => "GINC TERMS",
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
            endforeach;
            
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

        $queryData['where']['trans_number'] = $data['trans_number'];
        $queryData['where']['party_id'] = $data['party_id'];
        $queryData['where']['entry_type'] = $data['entry_type'];

        $queryData['where']['trans_date >='] = $this->startYearDate;
        $queryData['where']['trans_date <='] = $this->endYearDate;

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getGstIncome($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.*,trans_details.t_col_1 as contact_person,trans_details.t_col_2 as contact_no,trans_details.t_col_3 as ship_address";
        $queryData['leftJoin']['trans_details'] = "trans_main.id = trans_details.main_ref_id AND trans_details.description = 'GEXP MASTER DETAILS' AND trans_details.table_name = '".$this->transMain."'";
        $queryData['where']['trans_main.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
            $result->itemList = $this->getGstIncomeItems($data);
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
        $queryData['where']['description'] = "GINC TERMS";
        $result->termsConditions = $this->row($queryData);

        return $result;
    }

    public function getGstIncomeItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*";
        $queryData['where']['trans_child.trans_main_id'] = $data['id'];
        $result = $this->rows($queryData);
        return $result;
    }

    public function getGstIncomeItem($data){
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
                $this->db->trans_rollback();
                return $checkRef;
            endif;

            $dataRow = $this->getGstIncome(['id'=>$id,'itemList'=>0]);

            $checkBillWiseRef = $this->transMainModel->checkBillWiseRef(['id'=>$dataRow->id,'party_id'=>$dataRow->party_id,'entry_type'=>$dataRow->entry_type]);
            if($checkBillWiseRef == true):
                return ['status'=>0,'message'=>'Bill Wise Reference already adjusted. if you want to delete this entry first unset all adjustment.'];
            endif;

            $this->transMainModel->deleteLedgerTrans($id);

            $this->trash($this->transChild,['trans_main_id'=>$id]);
            $this->trash($this->transExpense,['trans_main_id'=>$id]);
            
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"GINC TERMS"]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"GINC MASTER DETAILS"]);

            $result = $this->trash($this->transMain,['id'=>$id],'GST Income');

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