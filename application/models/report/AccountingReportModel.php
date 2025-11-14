<?php
class AccountingReportModel extends MasterModel{

    public function getLedgerSummary($data){
        $startDate = (!empty($data['form_date']))?$data['form_date']:$this->startYearDate;
        $endDate = (!empty($data['to_date']))?$data['to_date']:$this->endYearDate;
        $startDate = date("Y-m-d",strtotime($startDate));
        $endDate = date("Y-m-d",strtotime($endDate));

        $ledgerSummary = $this->db->query("SELECT lb.id as id, am.party_name as account_name, am.group_name, IFNULL(cities.name,'') as city_name, CONCAT(am.credit_days, ' Days') as credit_days , (CASE WHEN lb.op_balance > 0 THEN abs(TRIM(lb.op_balance) + 0) WHEN lb.op_balance < 0 THEN abs(TRIM(lb.op_balance) + 0) ELSE (TRIM(lb.op_balance) + 0) END) as op_balance, (CASE WHEN lb.op_balance > 0 THEN 'CR.' WHEN lb.op_balance < 0 THEN 'DR.' ELSE '' END) as op_balance_type, (TRIM(lb.cr_balance) + 0) as cr_balance, (TRIM(lb.dr_balance) + 0) as dr_balance, (CASE WHEN lb.cl_balance > 0 THEN abs(TRIM(lb.cl_balance) + 0) WHEN lb.cl_balance < 0 THEN abs(TRIM(lb.cl_balance) + 0) ELSE (TRIM(lb.cl_balance) + 0) END) as cl_balance, (CASE WHEN lb.cl_balance > 0 THEN 'CR.' WHEN lb.cl_balance < 0 THEN 'DR.' ELSE '' END) as cl_balance_type
        FROM (
            SELECT am.id, (ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.trans_date < '".$startDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance, 
            SUM( CASE WHEN tl.trans_date >= '".$startDate."' AND tl.trans_date <= '".$endDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN tl.amount ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$startDate."' AND tl.trans_date <= '".$endDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN tl.amount ELSE 0 END ELSE 0 END) as cr_balance,
            (ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.trans_date <= '".$endDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id AND tl.is_delete = 0 
            WHERE am.is_delete = 0 GROUP BY am.id, am.opening_balance
        ) as lb 
        LEFT JOIN party_master as am ON lb.id = am.id 
        LEFT JOIN cities ON am.city_id = cities.id WHERE am.is_delete = 0 
        ORDER BY am.party_name ")->result();
        
        return $ledgerSummary;
    }

    public function getLedgerDetail($data){
        //tl.trans_number AS trans_number, 
        $ledgerTransactions = $this->db->query ("SELECT 
        tl.trans_main_id AS id, 
        tl.entry_type, 
        tl.trans_date, 
        tl.trans_number,
        tl.vou_name_s, 
        tl.amount,
        tl.c_or_d,
        tl.p_or_m,
        am.party_name AS account_name, 
        CASE WHEN tl.c_or_d = 'DR' THEN tl.amount ELSE 0 END AS dr_amount, 
        CASE WHEN tl.c_or_d = 'CR' THEN tl.amount ELSE 0 END AS cr_amount, 
        tl.remark AS remark 
        FROM ( trans_ledger AS tl LEFT JOIN party_master AS am ON am.id = tl.opp_acc_id ) 
        WHERE tl.vou_acc_id = ".$data['acc_id']." 
        AND tl.trans_date >= '".$data['from_date']."' 
        AND tl.trans_date <= '".$data['to_date']."'
        AND tl.is_delete = 0
        ORDER BY tl.trans_date, tl.trans_number")->result();
        return $ledgerTransactions;
    }

    public function getLedgerBalance($data){
        $party_id = (!empty($data['acc_id']))?" AND party_balance.party_id = ".$data['acc_id']:"";

        $ledgerBalance = $this->db->query ("SELECT am.id, am.party_name AS account_name, am.group_code, am.party_mobile AS contact_no, 
            (ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.trans_date < '".$data['from_date']."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance, 
            SUM( CASE WHEN tl.trans_date >= '".$data['from_date']."' AND tl.trans_date <= '".$data['to_date']."' THEN CASE WHEN tl.c_or_d = 'DR' THEN tl.amount ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$data['from_date']."' AND tl.trans_date <= '".$data['to_date']."' THEN CASE WHEN tl.c_or_d = 'CR' THEN tl.amount ELSE 0 END ELSE 0 END) as cr_balance,
            (ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.trans_date <= '".$data['to_date']."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance,
            (ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.recon_date IS NOT NULL AND tl.recon_date <= '".$data['to_date']."' THEN ((tl.amount * tl.p_or_m) * -1) ELSE 0 END )) as bcl_balance
        FROM party_master as am 
        LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id AND tl.is_delete = 0 
        WHERE am.is_delete = 0 
        AND am.id = ".$data['acc_id']."
        GROUP BY am.id")->row();

        $ledgerBalance->op_balance_type=(!empty($ledgerBalance->op_balance) && $ledgerBalance->op_balance >= 0)?(($ledgerBalance->op_balance > 0)?'CR':''):(($ledgerBalance->op_balance < 0)?'DR':'');
        $ledgerBalance->cl_balance_type=(!empty($ledgerBalance->cl_balance) && $ledgerBalance->cl_balance >= 0)?(($ledgerBalance->cl_balance > 0)?'CR':''):(($ledgerBalance->cl_balance < 0)?'DR':'');
        $ledgerBalance->bcl_balance_type=(!empty($ledgerBalance->bcl_balance) && $ledgerBalance->bcl_balance >= 0)?(($ledgerBalance->bcl_balance > 0)?'CR':''):(($ledgerBalance->bcl_balance < 0)?'DR':'');

        return $ledgerBalance;
    }

    public function getRegisterData($data){
        $queryData['tableName'] = 'trans_main';
        $queryData['select'] = 'trans_main.id,trans_main.trans_number,trans_main.doc_no,trans_main.trans_date,trans_main.order_type,trans_main.party_name,trans_main.party_state_code,trans_main.doc_no,trans_main.gstin,trans_main.currency,trans_main.vou_name_s,trans_main.total_amount,trans_main.disc_amount,trans_main.taxable_amount,trans_main.cgst_amount,trans_main.sgst_amount,trans_main.igst_amount,trans_main.cess_amount,trans_main.gst_amount,(trans_main.net_amount - trans_main.taxable_amount - trans_main.gst_amount) as other_amount,trans_main.net_amount';


        $queryData['where_in']['trans_main.vou_name_s'] = $data['vou_name_s'];
        $queryData['where']['trans_main.trans_status !='] = 3;
        $queryData['where']['trans_main.trans_date >='] = $data['from_date'];
        $queryData['where']['trans_main.trans_date <='] = $data['to_date'];

        if (!empty($data['party_id'])):
            $queryData['where']['trans_main.party_id'] = $data['party_id'];
        endif;

        if (!empty($data['state_code'])):
            if ($data['state_code'] == 1):
                $queryData['where']['trans_main.party_state_code']=24;
            endif;
            if ($data['state_code'] == 2) :
                $queryData['where']['trans_main.party_state_code !=']=24;
            endif;
            if($data['state_code'] == 3) :
                $queryData['where']['trans_main.gstin']='URP';
            endif;
        endif;

        $queryData['order_by']['trans_date']='ASC';
        return $this->rows($queryData);
    }

    public function getRegisterDataItemWise($data){
        $queryData['tableName'] = 'trans_child';
        $queryData['select'] = 'trans_main.id,trans_main.trans_number,trans_main.doc_no,trans_main.trans_date,trans_main.order_type,trans_main.party_name,trans_main.party_state_code,trans_main.doc_no,trans_main.gstin,trans_main.currency,trans_main.vou_name_s,trans_child.item_name,trans_child.qty,trans_child.price,trans_child.hsn_code,trans_child.gst_per,trans_child.amount,trans_child.disc_amount,trans_child.taxable_amount,(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,trans_child.cess_amount,trans_child.gst_amount,(trans_main.net_amount - trans_main.taxable_amount - trans_main.gst_amount) as other_amount,trans_main.net_amount';

        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";

        $queryData['where_in']['trans_main.vou_name_s'] = $data['vou_name_s'];
        $queryData['where']['trans_main.trans_status !='] = 3;
        $queryData['where']['trans_main.trans_date >='] = $data['from_date'];
        $queryData['where']['trans_main.trans_date <='] = $data['to_date'];

        if (!empty($data['party_id'])):
            $queryData['where']['trans_main.party_id'] = $data['party_id'];
        endif;
		
		if (!empty($data['item_id'])):
            $queryData['where']['trans_child.item_id'] = $data['item_id'];
        endif;

        if (!empty($data['state_code'])):
            if($data['state_code'] == 1):
                $queryData['where_in']['trans_main.party_state_code']=[24,96];
            endif;
            if($data['state_code'] == 2) :
                $queryData['where_not_in']['trans_main.party_state_code']=[24,96];
            endif;
            if($data['state_code'] == 3) :
                $queryData['where']['trans_main.gstin']='URP';
            endif;
        endif;

        $queryData['order_by']['trans_main.trans_date']='ASC';
        $queryData['order_by']['trans_main.id']='ASC';
        return $this->rows($queryData);
    }

    public function getOutstandingData($postData){
        $os_type = ($postData['os_type']=="R") ? '<' : '>';
        $group_code = (!empty($postData['group_code']))?$postData['group_code']:"'SD','SC'";
		$daysCondition = ',';$daysFields = '';
		
        if(!empty($postData['days_range'])):
		    $i=1;$rangeLength = count($postData['days_range']);$ele=1;
		    $daysCondition = ($rangeLength > 0) ? ',' : '';
		    foreach($postData['days_range'] as $days):		        
		        if($i == 1):
                    $daysCondition .='(ifnull(am.opening_balance,0) + SUM( CASE WHEN DATEDIFF(DATE_ADD( tl.trans_date,INTERVAL am.credit_days day),NOW()) <= '.$days.' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as d'.$ele++.',';
                endif;

		        if($i == $rangeLength):
                    $daysCondition .='(SUM( CASE WHEN DATEDIFF(DATE_ADD( tl.trans_date,INTERVAL am.credit_days day),NOW()) > '.$days.' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as d'.$ele++.',';
                endif;

		        if($i < $rangeLength):
                    $daysCondition .='(SUM( CASE WHEN DATEDIFF(DATE_ADD( tl.trans_date,INTERVAL am.credit_days day),NOW()) BETWEEN '.($days + 1).' AND '.$postData['days_range'][$i].' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as d'.$ele++.',';
                endif;
		        $i++;
            endforeach;
		    for($x=1;$x<=($rangeLength+1);$x++): $daysFields .= ',abs(lb.d'.$x.') as d'.$x; endfor;
		endif;
		
        $receivable = $this->db->query ("SELECT lb.id as id, am.party_name as account_name,am.group_name,am.contact_person, am.party_mobile, ct.name as city_name, abs(lb.cl_balance) as cl_balance ".$daysFields.", lb.trans_date,  DATE_ADD( lb.trans_date,INTERVAL am.credit_days day) as due_date, DATEDIFF(DATE_ADD( lb.trans_date,INTERVAL am.credit_days day),NOW()) as pending_days
        FROM (
            SELECT am.id, (ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.trans_date < '".$postData['from_date']."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance,
            SUM( CASE WHEN tl.trans_date >= '".$postData['from_date']."' AND tl.trans_date <= '".$postData['to_date']."' THEN CASE WHEN tl.c_or_d = 'DR' THEN tl.amount ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$postData['from_date']."' AND tl.trans_date <= '".$postData['to_date']."' THEN CASE WHEN tl.c_or_d = 'CR' THEN tl.amount ELSE 0 END ELSE 0 END) as cr_balance,
            (ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.trans_date <= '".$postData['to_date']."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance ".$daysCondition."
            tl.trans_date           
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id  AND tl.is_delete = 0  
            WHERE am.group_code IN ( ".$group_code." ) AND am.is_delete = 0 GROUP BY am.id
        ) as lb
        LEFT JOIN party_master as am ON lb.id = am.id 
        LEFT JOIN cities as ct ON ct.id = am.city_id
        WHERE lb.cl_balance ".$os_type." 0 AND am.group_code IN ( ".$group_code." ) AND am.is_delete = 0 ORDER BY am.party_name")->result();
        
        return $receivable;
    }

    public function getDuePaymentReminderData($data){
        $paymentSetting = $this->getAccountSettings();
        $remindarDays = ($data['report_type'] == 'Receivable')?$paymentSetting->rrb_days:$paymentSetting->prb_days;

        $queryData = array();
        $queryData['tableName'] = "trans_billwise";

        $queryData['select'] = "trans_billwise.trans_main_id as id, trans_billwise.trans_number, trans_billwise.trans_date, trans_billwise.party_id, party_master.party_name, party_master.party_mobile, (trans_billwise.amount - IFNULL((SELECT sum(tb.amount) FROM trans_billwise AS tb WHERE tb.ag_ref_id = trans_billwise.id AND tb.is_delete = 0), 0)) as due_amount, (CASE WHEN trans_billwise.trans_main_id = 0 AND trans_billwise.entry_type = 0 THEN (SELECT abs(IFNULL(opening_balance,0)) as op_balance FROM party_master WHERE party_master.id = trans_billwise.party_id AND party_master.is_delete = 0) ELSE IF(trans_main.vou_name_s IN ('BCRct','BCPmt'), IFNULL((trans_main.net_amount + abs(trans_main.round_off_amount)),0), IFNULL((trans_main.net_amount),0) ) END) as net_amount, party_master.credit_days, DATE_ADD( trans_main.trans_date,INTERVAL (party_master.credit_days) day) as due_date, DATEDIFF('".$data['due_date']."',DATE_ADD( trans_main.trans_date,INTERVAL (party_master.credit_days) day)) as due_days";

        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_billwise.trans_main_id";
        $queryData['leftJoin']['party_master'] = "trans_main.party_id = party_master.id";        

        $queryData['where_in']['trans_billwise.ref_type'] = [0,1];
        $queryData['where_in']['trans_main.vou_name_s'] = $data['vou_name_s'];
        $queryData['where']['(trans_billwise.amount - IFNULL((SELECT sum(tb.amount) FROM trans_billwise AS tb WHERE tb.ag_ref_id = trans_billwise.id AND tb.is_delete = 0), 0)) <>'] = 0;

        if(!empty($data['party_id'])):
            $queryData['where']['trans_main.party_id'] = $data['party_id'];
        endif;

        if($data['report_type'] == 'Receivable'):
            $queryData['where']['trans_billwise.c_or_d'] = "DR";
        else:
            $queryData['where']['trans_billwise.c_or_d'] = "CR";
        endif;
        
        if(!empty($data['is_reminder'])):
            $queryData['customWhere'][] = "(DATEDIFF(DATE_ADD( trans_main.trans_date,INTERVAL party_master.credit_days day),'".$data['due_date']."')  BETWEEN '0' AND '".$remindarDays."')";
        else:
            if($data['due_type'] == "over_due"):
                $queryData['where']["DATEDIFF('".$data['due_date']."',DATE_ADD( trans_main.trans_date,INTERVAL (party_master.credit_days) day)) >"] = 0;
            elseif($data['due_type'] == "under_due"):
                $queryData['where']["DATEDIFF('".$data['due_date']."',DATE_ADD( trans_main.trans_date,INTERVAL (party_master.credit_days) day)) <="] = 0;
            else:
                $queryData['customWhere'][] = "(DATEDIFF('".$data['due_date']."',DATE_ADD( trans_main.trans_date,INTERVAL (party_master.credit_days) day)) > 0 OR DATEDIFF('".$data['due_date']."',DATE_ADD( trans_main.trans_date,INTERVAL (party_master.credit_days) day)) <= 0)";
            endif;
        endif;
        
        $queryData['order_by']['due_date'] = "ASC";        
        
        if(!empty($data['limit'])):
            $queryData['limit'] = $data['limit'];
        endif;

        $result = $this->rows($queryData);
        return $result;
    }

    public function getBankCashBook($postData){
        $fromDate = $postData['from_date'];
        $toDate = $postData['to_date'];
        $groupCode = $postData['group_code'];

        $bankCashBook = $this->db->query ("SELECT lb.id as id, am.party_name as account_name, am.group_name, abs(lb.op_balance) as op_balance, lb.cr_balance, lb.dr_balance, abs(lb.cl_balance) as cl_balance, abs(lb.bcl_balance) as bcl_balance,
        (CASE WHEN lb.op_balance > 0 THEN 'CR.' WHEN lb.op_balance < 0 THEN 'DR.' ELSE '' END) op_balance_type, 

        (CASE WHEN lb.cl_balance > 0 THEN 'CR.' WHEN lb.cl_balance < 0 THEN 'DR.' ELSE '' END) as cl_balance_type,

        (CASE WHEN lb.bcl_balance > 0 THEN 'CR.' WHEN lb.bcl_balance < 0 THEN 'DR.' ELSE '' END) as bcl_balance_type 
        FROM (
            SELECT am.id, (ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.trans_date < '".$fromDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance, 

            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN tl.amount ELSE 0 END ELSE 0 END) as dr_balance,

            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN tl.amount  ELSE 0 END ELSE 0 END) as cr_balance,

            (ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.trans_date <= '".$toDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance, 

            (ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.recon_date IS NOT NULL AND tl.recon_date <= '".$toDate."' THEN ((tl.amount * tl.p_or_m) * -1) ELSE 0 END )) as bcl_balance

            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id AND tl.is_delete = 0   
            WHERE am.is_delete = 0 AND am.group_code IN ($groupCode) GROUP BY am.id
        ) as lb 
        LEFT JOIN party_master as am ON lb.id = am.id WHERE am.is_delete = 0
        ORDER BY am.party_name")->result();

        return $bankCashBook;
    }

    public function getMonthlySummary($data){
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $vouName = $data['vou_name_s'];

        $this->db->query("set @start_date = '".$from_date."';");
        $this->db->query("set @end_date = '".$to_date."';");
        $this->db->query("set @months = -1;");

        $result = $this->db->query("SELECT DATE_FORMAT(monthList.date_range,'%M, %Y') AS month_name, tm.total_taxable_amount, tm.total_cgst_amount, tm.total_sgst_amount, tm.total_igst_amount, tm.total_net_amount
        FROM (
            SELECT (date_add(@start_date, INTERVAL (@months := @months +1 ) month)) as date_range
            FROM information_schema.COLUMNS monthList
        ) monthList
        LEFT JOIN (
            SELECT DATE_FORMAT(trans_date,'%Y-%m') as month_name, SUM(taxable_amount) as total_taxable_amount, SUM(cgst_amount) as total_cgst_amount, SUM(sgst_amount) as total_sgst_amount, SUM(igst_amount) as total_igst_amount, SUM(net_amount) as total_net_amount
            FROM trans_main 
            WHERE is_delete = 0
            AND vou_name_s IN (".$vouName.")
            AND trans_date >= '".$from_date."'
            AND trans_date <= '".$to_date."'
            AND trans_status != 3
            GROUP BY  DATE_FORMAT(trans_date,'%Y-%m')
        ) AS tm ON tm.month_name = DATE_FORMAT(monthList.date_range,'%Y-%m')
        WHERE monthList.date_range BETWEEN @start_date AND last_day(@end_date)")->result();

        return $result;
    }

    public function getHsnTransactions($data){
        $decreaseEntryType = ($data['report'] == "gstr1")?'"C.N."':'"D.N."';

        $orderTypes = ($data['report'] == "gstr1")?" AND (trans_main.vou_name_s IN ('Sale','GInc') OR (trans_main.vou_name_s IN ('C.N.','D.N.') AND trans_main.order_type IN ('Increase Sales','Decrease Sales','Sales Return')))":" AND (trans_main.vou_name_s IN ('Purc','GExp') OR (trans_main.vou_name_s IN ('C.N.','D.N.') AND trans_main.order_type IN ('Increase Purchase','Decrease Purchase','Purchase Return')))";
        $p_or_m = ($data['report'] == "gstr1")?-1:1;
        $groupBy = ($data['report_type'] == "SUMMARY")?" GROUP BY trans_main.trans_number,trans_child.hsn_code,trans_child.unit_id,trans_child.gst_per":" GROUP BY trans_main.trans_number,trans_child.item_id,trans_child.hsn_code,trans_child.unit_id,trans_child.gst_per";

        $result = $this->db->query("
            SELECT trans_main.vou_name_s,trans_main.trans_number,trans_main.trans_date,trans_main.party_name,trans_main.party_state_code,trans_main.gstin,trans_child.item_name,trans_child.hsn_code,hsn_master.description as hsn_description,trans_child.gst_per,unit_master.unit_name,unit_master.description as unit_description,

            SUM((trans_child.qty * trans_child.p_or_m * $p_or_m)) as qty,

            SUM((trans_child.taxable_amount * trans_child.p_or_m * $p_or_m)) as taxable_amount,
            SUM(CASE WHEN trans_main.gst_type = 1 THEN (trans_child.cgst_amount * trans_child.p_or_m * $p_or_m) ELSE 0 END) as cgst_amount,
            SUM(CASE WHEN trans_main.gst_type = 1 THEN (trans_child.sgst_amount * trans_child.p_or_m * $p_or_m) ELSE 0 END) as sgst_amount,
            SUM(CASE WHEN trans_main.gst_type = 2 THEN (trans_child.igst_amount * trans_child.p_or_m * $p_or_m) ELSE 0 END) as igst_amount,
            SUM((trans_child.cess_amount * trans_child.p_or_m * $p_or_m)) as cess_amount,
            SUM((trans_child.net_amount * trans_child.p_or_m * $p_or_m)) as net_amount
            
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master ON party_master.id = trans_main.party_id
            LEFT JOIN unit_master ON unit_master.id=trans_child.unit_id
            LEFT JOIN hsn_master ON hsn_master.hsn = trans_child.hsn_code
            WHERE trans_child.is_delete = 0
            AND trans_child.hsn_code = '".$data['hsn_code']."'
            ".$orderTypes."
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.trans_status != 3
            GROUP BY trans_main.trans_number,trans_child.hsn_code,trans_child.unit_id,trans_child.gst_per
            ORDER BY trans_main.trans_date,trans_main.id ASC
        ")->result();
        
        return $result;
    }

    public function _productOpeningAndClosingAmount($data){
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));

        $result = $this->db->query("SELECT pm.item_type, (CASE WHEN pm.item_type = 1 THEN 'Finish Goods' WHEN pm.item_type = 2 THEN 'Consumable' WHEN pm.item_type = 3 THEN 'Raw Material' ELSE '' END) as ledger_name, ifnull(SUM(pl.op_amount),0) as op_amount, ifnull(SUM(pl.cl_amount),0) as cl_amount 
        FROM (
        
            SELECT pm.id, pm.item_type,  ost.stock_qty as op_stock, (ost.avg_price * ost.stock_qty) as op_amount, cst.stock_qty as cl_stock,(cst.avg_price * cst.stock_qty) as cl_amount 
            FROM  item_master AS pm 
            LEFT JOIN (	
                SELECT SUM(qty * p_or_m) AS stock_qty, ( SUM(CASE WHEN price > 0 AND p_or_m = 1 THEN (qty * price) ELSE 0 END) / SUM(CASE WHEN price > 0 AND p_or_m = 1 THEN qty ELSE 0 END) ) as avg_price, item_id FROM stock_transaction WHERE is_delete = 0 AND ref_date < '$from_date' GROUP BY item_id 
            ) AS ost ON ost.item_id = pm.id 
            LEFT JOIN ( 
                SELECT SUM(qty * p_or_m) AS stock_qty, ( SUM(CASE WHEN price > 0 AND p_or_m = 1 THEN (qty * price) ELSE 0 END) / SUM(CASE WHEN price > 0 AND p_or_m = 1 THEN qty ELSE 0 END) ) as avg_price, item_id FROM stock_transaction WHERE is_delete = 0 AND ref_date <= '$to_date' GROUP BY item_id 
            ) AS cst ON cst.item_id = pm.id 
            WHERE pm.is_delete = 0 and pm.item_type IN (1,2,3) and (ost.stock_qty <> 0 OR cst.stock_qty <> 0) GROUP BY pm.id
            
        ) as pl 
        LEFT JOIN item_master AS pm ON pl.id = pm.id 
        WHERE ( pl.op_amount <> 0 OR pl.cl_amount <> 0 ) 
        AND pm.is_delete = 0 
        GROUP BY pl.item_type")->result();

        return $result;
    }

    public function _trailAccountSummary($data){
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));

        $result = $this->db->query("SELECT am.party_name as name, am.group_name, am.group_id,
        ifnull((CASE WHEN lb.cl_balance < 0 THEN abs(lb.cl_balance) ELSE 0 END),0) as debit_amount,
        ifnull((CASE WHEN lb.cl_balance > 0 THEN lb.cl_balance ELSE 0 END),0) as credit_amount,
        ifnull(lb.cl_balance,0) as cl_balance
        FROM ( party_master am LEFT JOIN group_master gm ON am.group_id = gm.id ) 
        LEFT JOIN ( 
            SELECT am.id as id, (ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id AND tl.is_delete = 0 
            WHERE am.is_delete = 0 
            GROUP BY am.id 
            ORDER BY am.party_name
        ) as lb ON am.id = lb.id 
        WHERE am.is_delete = 0 
        AND lb.cl_balance <> 0
        ORDER BY gm.bs_type_code, am.group_name, am.party_name")->result();

        return $result;
    }

    public function _trailSubGroupSummary($data){
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $extraWhere = (!empty($data['extra_where']))?" AND ".$data['extra_where']:" ";

        $result = $this->db->query("SELECT gm.id,gm.name as group_name, gm.nature ,gm.bs_type_code, gm.base_group_id, gm.under_group_id,
                (CASE WHEN gm.base_group_id = 0 THEN gm.under_group_id ELSE gm.base_group_id END) as bs_id,
                ifnull((CASE WHEN gs.cl_balance < 0 THEN abs(gs.cl_balance) ELSE 0 END),0) as debit_amount,
                ifnull((CASE WHEN gs.cl_balance > 0 THEN gs.cl_balance ELSE 0 END),0) as credit_amount,
                ifnull(gs.cl_balance,0) as cl_balance
            FROM  group_master as gm 
            LEFT JOIN ( 
                SELECT am.group_id,(ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance 
                FROM party_master as am 
                LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id AND tl.is_delete = 0  
                WHERE  am.is_delete = 0 
                GROUP BY am.group_id
            ) AS gs on gm.id = gs.group_id 
            WHERE  gm.is_delete = 0 
            $extraWhere
            ORDER BY gm.seq
        ")->result();

        return $result;
    }

    public function _trailMainGroupSummary($data){
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $extraWhere = (!empty($data['extra_where']))?" AND ".$data['extra_where']:" ";

        $result = $this->db->query("SELECT bsgm.id,bsgm.name as group_name, bsgm.nature, sugm.debit_amount, sugm.credit_amount, 
            ifnull((sugm.credit_amount - sugm.debit_amount),0) as cl_balance
            FROM group_master as bsgm
            LEFT JOIN (
                SELECT (CASE WHEN gm.base_group_id = 0 THEN gm.under_group_id ELSE gm.base_group_id END) as bs_id,
                ifnull(SUM((CASE WHEN gs.cl_balance < 0 THEN abs(gs.cl_balance) ELSE 0 END)),0) as debit_amount,
                ifnull(SUM((CASE WHEN gs.cl_balance > 0 THEN gs.cl_balance ELSE 0 END)),0) as credit_amount
                FROM  group_master as gm 
                LEFT JOIN ( 
                    SELECT am.id,am.group_id,
                    (ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance 
                    FROM party_master as am 
                    LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id AND tl.is_delete = 0  
                    WHERE  am.is_delete = 0 GROUP BY am.id
                ) AS gs on gm.id = gs.group_id 
                WHERE  gm.is_delete = 0 
                $extraWhere
                GROUP BY (CASE WHEN gm.base_group_id = 0 THEN gm.under_group_id ELSE gm.base_group_id END)
                ORDER BY gm.seq
            ) as sugm ON bsgm.id = sugm.bs_id
            WHERE bsgm.is_delete = 0
            AND ( sugm.debit_amount <> 0 OR sugm.credit_amount <> 0)
            ORDER BY bsgm.seq
        ")->result();

        return $result;
    }

    public function _accountWiseDetail($data){
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $nature = $data['nature'];
        $bs_type_code = $data['bs_type_code'];
        $balance_type = $data['balance_type'];
        $balance = ($data['balance_type'] == "lb.cl_balance > 0")?"lb.cl_balance":"abs(lb.cl_balance) AS cl_balance";

		$innerSelect = "SELECT am.id as id,am.party_name AS name,am.group_id,am.group_name,gm.nature, (ifnull(am.opening_balance,0) + SUM( CASE WHEN  tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance ";
		
		if(!empty($data['bs_type_code']) && $data['bs_type_code'] == "'T'"):
			$innerSelect = "SELECT am.id as id,am.party_name AS name,am.group_id,am.group_name,gm.nature, (SUM( CASE WHEN tl.trans_date >= '$from_date' AND tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance ";
		endif;

        $result = $this->db->query("SELECT lb.id,lb.name,lb.group_id,lb.group_name,lb.nature, $balance
        FROM (
            $innerSelect 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id AND tl.is_delete = 0 
            LEFT JOIN group_master AS gm ON gm.id = am.group_id
            WHERE am.is_delete = 0
            AND gm.nature IN ($nature)
            AND gm.bs_type_code IN ($bs_type_code)
            GROUP BY am.id
            ORDER BY am.party_name
        ) AS lb
        WHERE $balance_type")->result();

        return $result;
    }

    public function _groupWiseSummary($data){
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $nature = $data['nature'];
        $bs_type_code = $data['bs_type_code'];
        $balance_type = $data['balance_type'];
        $balance = ($data['balance_type'] == "gs.cl_balance > 0")?"SUM(gs.cl_balance) AS cl_balance":"SUM(abs(gs.cl_balance)) AS cl_balance";

		
		$innerSelect = "SELECT gm.id, gm.name AS group_name, gm.nature, gm.bs_type_code, gm.seq, (ifnull(am.opening_balance,0) + SUM( CASE WHEN tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance ";
		
		if(!empty($data['bs_type_code']) && $data['bs_type_code'] == "'T'"):
			$innerSelect = "SELECT gm.id, gm.name AS group_name, gm.nature, gm.bs_type_code, gm.seq, (SUM( CASE WHEN tl.trans_date >= '$from_date' AND tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance ";
		endif;
		
        $result = $this->db->query("SELECT gs.id, gs.group_name, gs.nature, gs.bs_type_code, gs.seq, $balance
        FROM (
             $innerSelect
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id AND tl.is_delete = 0
            LEFT JOIN group_master AS gm ON gm.id = am.group_id
            WHERE am.is_delete = 0
            AND gm.nature IN ($nature)
            AND gm.bs_type_code IN ($bs_type_code)            
            GROUP BY am.id
        ) AS gs
        WHERE $balance_type
        GROUP BY gs.id ORDER BY gs.seq")->result();

        return $result;
    }

    public function _netPnlAmount($data){
        $closingStockAmount = (!empty($data['closingAmount']))?$data['closingAmount']:0;
        $openingStockAmount = (!empty($data['openingAmount']))?$data['openingAmount']:0;
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $extraWhere = (!empty($data['extra_where']))?" AND ".$data['extra_where']:" ";

        $result = $this->db->query("SELECT ($closingStockAmount + ifnull(pnl.income,0)) - ($openingStockAmount + ifnull((CASE WHEN pnl.expense < 0 THEN abs(pnl.expense) ELSE pnl.expense * -1 END),0)) as net_pnl_amount 
        FROM ( 
            SELECT ifnull(am.opening_balance,0) + SUM( CASE WHEN gm.nature = 'Income' AND tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END) as income, 
            ifnull(am.opening_balance,0) + SUM( CASE WHEN gm.nature = 'Expenses' AND tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END) as expense 
            FROM ( ( 
                party_master as am 
                LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id AND tl.is_delete = 0
            ) 
            LEFT JOIN group_master gm ON am.group_id = gm.id ) 
            WHERE am.is_delete = 0 $extraWhere 
        ) as pnl")->row();

        return $result;
    }
}
?>