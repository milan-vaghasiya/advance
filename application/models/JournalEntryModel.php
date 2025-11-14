<?php
class JournalEntryModel extends MasterModel{
    private $transMain = "trans_main";
    private $transLedger = "trans_ledger";

    public function getDTRows($data){
        $data['tableName'] = $this->transLedger;
        $data['select'] = 'trans_main.id,trans_ledger.trans_number,trans_ledger.trans_date,party_master.party_name as acc_name,trans_ledger.amount,trans_ledger.remark,trans_ledger.c_or_d';
        
        $data['join']['trans_main'] = "trans_main.id = trans_ledger.trans_main_id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_ledger.vou_acc_id";

        $data['where']['trans_ledger.entry_type'] = $data['entry_type'];
        $data['where']['trans_ledger.trans_date >='] = $this->startYearDate;
		$data['where']['trans_ledger.trans_date <='] = $this->endYearDate;

        $data['order_by']['trans_ledger.trans_date'] = "DESC";
        $data['order_by']['trans_ledger.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_ledger.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_ledger.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "trans_ledger.amount";
        $data['searchCol'][] = "trans_ledger.remark'";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
            if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_number'] = "JV. No. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;
            
            // Filter the array based on 'cr_dr' value 'CR'
            $filteredCrArray = array_filter($data['itemData'], fn($item) => $item['cr_dr'] === 'CR');
            $filteredDrArray = array_filter($data['itemData'], fn($item) => $item['cr_dr'] === 'DR');

            // Get the first key of the filtered array
            $firstCrKey = key($filteredCrArray);
            $firstDrKey = key($filteredDrArray);
            
            $data['vou_acc_id'] = $data['itemData'][$firstDrKey]['acc_id'];
            $data['opp_acc_id'] = $data['itemData'][$firstCrKey]['acc_id'];
            $data['party_id'] = $data['itemData'][$firstCrKey]['acc_id'];
            $data['total_amount'] = $data['itemData'][$firstDrKey]['debit_amount'];	
            $data['taxable_amount'] = $data['itemData'][$firstDrKey]['debit_amount'];
            $data['net_amount'] = $data['itemData'][$firstDrKey]['debit_amount'];
            $data['ledger_eff'] = 1;

            $itemData = $data['itemData'];unset($data['itemData']);
            
            $result = $this->store($this->transMain,$data,"Journal Entry");
            
            //remove old trans
            $this->transMainModel->deleteLedgerTrans($result['id']);

            foreach($itemData as $row):
                
                $transLedgerData = [
                    'id'=>"",
                    'entry_type'=>$data['entry_type'],
                    'trans_main_id'=>$result['id'],
                    'trans_date'=>$data['trans_date'],
                    'trans_number'=>$data['trans_number'],
                    'doc_date'=>$data['trans_date'],
                    'doc_no'=>$data['trans_number'],
                    'c_or_d'=>$row['cr_dr'],
                    'vou_name_l'=>$data['vou_name_l'],
                    'vou_name_s'=>$data['vou_name_s'],
                    'remark'=>$row['item_remark']
                ];

                $transLedgerData['vou_acc_id'] = $row['acc_id'];
                if($row['cr_dr'] == "DR"):
                    $transLedgerData['opp_acc_id'] = $itemData[$firstCrKey]['acc_id'];
                    $transLedgerData['amount'] = $row['debit_amount'];
                else:
                    $transLedgerData['opp_acc_id'] = $itemData[$firstDrKey]['acc_id'];
                    $transLedgerData['amount'] = $row['credit_amount'];
                endif;

                $this->transMainModel->storeTransLedger($transLedgerData);
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
        $queryData['where']['entry_type'] = $data['entry_type'];

        $queryData['where']['trans_date >='] = $this->startYearDate;
        $queryData['where']['trans_date <='] = $this->endYearDate;

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getJournalEntry($id){
        $queryData = array();
        $queryData['tableName']  = $this->transMain;
        $queryData['where']['id'] = $id;
        $result = $this->row($queryData);

        $result->ledgerData = $this->getLedgerTrans($id);
        return $result;
    }

    public function getLedgerTrans($id){
        $queryData = array();
        $queryData['tableName']  = $this->transLedger;
        $queryData['select'] = "trans_ledger.*,party_master.party_name as ledger_name,trans_ledger.vou_acc_id as acc_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = trans_ledger.vou_acc_id";
        $queryData['where']['trans_ledger.trans_main_id'] = $id;
        $result = $this->rows($queryData);
        return $result;
    }

    public function delete($id){
		try{
            $this->db->trans_begin();

			$result = $this->trash($this->transMain,['id'=>$id],'Journal Entry');
            $this->transMainModel->deleteLedgerTrans($id);

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Throwable $e){
			$this->db->trans_rollback();
		    return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

    public function getGstLedgerClosing($data){
        $endDate = (!empty($data['to_date']))?$data['to_date']:$this->endYearDate;
        $endDate = date("Y-m-d",strtotime($endDate));

        $ledgerSummary = $this->db->query("SELECT lb.id as acc_id, am.party_name as ledger_name, (CASE WHEN lb.cl_balance > 0 THEN CONCAT(abs(TRIM(lb.cl_balance) + 0),' CR.') WHEN lb.cl_balance < 0 THEN CONCAT(abs(TRIM(lb.cl_balance) + 0),' DR.') ELSE (TRIM(lb.cl_balance) + 0) END) as cl_balance, (CASE WHEN lb.org_cl_balance > 0 THEN CONCAT(abs(TRIM(lb.org_cl_balance) + 0),' CR.') WHEN lb.org_cl_balance < 0 THEN CONCAT(abs(TRIM(lb.org_cl_balance) + 0),' DR.') ELSE (TRIM(lb.org_cl_balance) + 0) END) as org_cl_balance, (CASE WHEN lb.cl_balance > 0 THEN abs(TRIM(lb.cl_balance) + 0) ELSE 0 END) as cr_amount, (CASE WHEN lb.cl_balance < 0 THEN abs(TRIM(lb.cl_balance) + 0) ELSE 0 END) as dr_amount, abs(TRIM(lb.cl_balance) + 0) as cl_bal,(CASE WHEN lb.cl_balance > 0 THEN 'CR' WHEN lb.cl_balance < 0 THEN 'DR' ELSE '' END) as cl_balance_type, lb.system_code
        FROM (
            SELECT am.id, am.system_code,
            ((ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.trans_date <= '".$endDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) * -1) as cl_balance, 
            (ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.trans_date <= '".$endDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as org_cl_balance
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id AND tl.is_delete = 0
            WHERE am.is_delete = 0 AND am.system_code IN (".$data['system_code'].") GROUP BY am.id
        ) as lb 
        LEFT JOIN party_master as am ON lb.id = am.id 
        LEFT JOIN cities ON am.city_id = cities.id 
        WHERE am.is_delete = 0 
        AND lb.cl_balance ".$data['balance_condition']." 0
        ORDER BY am.party_name")->result();
        
        return $ledgerSummary;
    }
}
?>