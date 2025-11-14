<?php 
class PaymentVoucherModel extends MasterModel{
	private $transMain = "trans_main";
	private $transLedger = "trans_ledger";

	public function getDtRows($data){
		$data['tableName'] = $this->transMain;
		$data['select'] = "trans_main.*,opp_acc.party_name as opp_acc_name,vou_acc.party_name as vou_acc_name";

        $data['leftJoin']['party_master as opp_acc'] = "opp_acc.id = trans_main.opp_acc_id";
        $data['leftJoin']['party_master as vou_acc'] = "vou_acc.id = trans_main.vou_acc_id";

        if(!empty($data['vou_name_s'])):
            $data['where']['vou_name_s'] = $data['vou_name_s'];
        endif;
		
        $data['where']['trans_main.trans_date >='] = $this->startYearDate;
        $data['where']['trans_main.trans_date <='] = $this->endYearDate;

		
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "opp_acc.party_name";
        $data['searchCol'][] = "vou_acc.party_name";
        $data['searchCol'][] = "trans_main.net_amount";
        $data['searchCol'][] = "trans_main.doc_no";
        $data['searchCol'][] = "trans_main.doc_date";
        $data['searchCol'][] = "trans_main.remark";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
	}

    public function getPartyInvoiceList($data){
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "id,trans_number,(net_amount - rop_amount) as due_amount";
        $queryData['where']['party_id'] = $data['party_id'];
        $queryData['where']['vou_name_s'] = $data['vou_name_s'];
        if(empty($data['ref_id'])):
            $queryData['where']['(net_amount - rop_amount) >'] = 0;
        else:
            $queryData['cusomWhere'][] = "((net_amount - rop_amount) > 0 OR id = ".$data['ref_id'].")";
        endif;
        return $this->rows($queryData);
    }

	public function save($data){
		try{
			$this->db->trans_begin();

            if(empty($data['id'])):
                if($data['vou_name_s'] == "BCRct"):
                    $data['trans_prefix'] = $this->data['entryData']->trans_prefix;
                else:
                    $data['trans_prefix'] = str_replace("R","P",$this->data['entryData']->trans_prefix);
                endif;
                
                $data['trans_no'] = $this->transMainModel->nextTransNo($this->data['entryData']->id,0,$data['vou_name_s']);
            endif;

            $data['trans_number']	= $data['trans_prefix'].$data['trans_no'];

            if(!empty($data['id'])):
                $vouData = $this->getVoucher($data['id']);

                $checkBillWiseRef = $this->transMainModel->checkBillWiseRef(['id'=>$vouData->id,'party_id'=>$vouData->party_id,'entry_type'=>$vouData->entry_type]);
                if($checkBillWiseRef == true):
                    return ['status'=>2,'message'=>'Bill Wise Reference already adjusted. if you want to update this entry first unset all adjustment.'];
                endif;

                if($vouData->vou_name_s != $data['vou_name_s']):
                    if($data['vou_name_s'] == "BCRct"):
                        $data['trans_prefix'] = $this->data['entryData']->trans_prefix;
                    else:
                        $data['trans_prefix'] = str_replace("R","P",$this->data['entryData']->trans_prefix);
                    endif;
                    
                    $data['trans_no'] = $this->transMainModel->nextTransNo($this->data['entryData']->id,0,$data['vou_name_s']);
                    $data['trans_number']	= $data['trans_prefix'].$data['trans_no'];
                endif;
            endif;

            $data['p_or_m'] = ($data['vou_name_s'] == "BCRct")?1:-1;
			$data['doc_date'] = (!empty($data['doc_date']))?$data['doc_date']:null;

            $data['round_off_amount'] = floatval($data['round_off_amount']);
            if(!empty($data['round_off_amount'])):
                $kasarAccount = $this->party->getParty(['system_code'=>"KASARACC"]);
				$data['round_off_acc_id'] = $kasarAccount->id;
            endif;

			$result = $this->store($this->transMain,$data,'Voucher');
			$data['id'] = $result['id'];

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

	public function getVoucher($id){
        $data['tableName'] = $this->transMain;
		$data['select'] = "trans_main.*,party_master.party_name, reference.trans_no as inv_no, reference.trans_prefix as inv_prefix, reference.trans_date as inv_date";
		$data['join']['party_master'] = "party_master.id = trans_main.party_id";
		$data['leftJoin']['trans_main as reference'] = "reference.id = trans_main.ref_id";
        $data['where']['trans_main.id'] = $id;
        return $this->row($data);
    }  

	public function delete($id){
		try{
			$this->db->trans_begin();

            $vouData = $this->getVoucher($id);

            $checkBillWiseRef = $this->transMainModel->checkBillWiseRef(['id'=>$vouData->id,'party_id'=>$vouData->party_id,'entry_type'=>$vouData->entry_type]);
            if($checkBillWiseRef == true):
                return ['status'=>0,'message'=>'Bill Wise Reference already adjusted. if you want to delete this entry first unset all adjustment.'];
            endif;
			
			$result= $this->trash($this->transMain,['id'=>$id],'PaymentVoucher');
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

    public function getBankTransactions($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.id,trans_main.trans_number,trans_main.trans_date,opp_acc.party_name as opp_acc_name,vou_acc.party_name as vou_acc_name,trans_main.doc_no,trans_main.doc_date,trans_main.payment_mode,trans_main.net_amount,trans_main.recon_date";

        $queryData['leftJoin']['party_master as opp_acc'] = "opp_acc.id = trans_main.opp_acc_id";
        $queryData['leftJoin']['party_master as vou_acc'] = "vou_acc.id = trans_main.vou_acc_id";
        $queryData['where']['trans_main.payment_mode !='] = "CASH";
        $queryData['where']['trans_main.trans_date >='] = $data['from_date'];
        $queryData['where']['trans_main.trans_date <='] = $data['to_date'];
        $queryData['where_in']['trans_main.vou_name_s'] = ["'BCRct'","'BCPmt'","'BCGSTPmt'"];
        
        if(!empty($data['acc_id'])):
            $queryData['where']['trans_main.vou_acc_id'] = $data['acc_id'];
        endif;

        if($data['status'] != -1):
            if($data['status'] == 0):
                $queryData['customWhere'][] = 'trans_main.recon_date IS NULL'; 
            else:
                $queryData['customWhere'][] = 'trans_main.recon_date IS NOT NULL'; 
            endif;
        endif;

        $queryData['order_by']['trans_main.trans_date'] = "ASC";

        $result = $this->rows($queryData);
        return $result;
    }

    public function saveBankReconciliation($data){
        try{
            $this->db->trans_begin();

            foreach($data['item_data'] as $row):
                if(!empty($row['recon_date'])):
                    $this->edit($this->transMain,['id'=>$row['id']],['recon_date'=>$row['recon_date']]);
                    $this->edit($this->transLedger,['trans_main_id'=>$row['id']],['recon_date'=>$row['recon_date']]);
                endif;
            endforeach;         
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>"Bank Reconciliation saved successfully."];
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
}
?>