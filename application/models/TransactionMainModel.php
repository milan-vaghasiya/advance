<?php
class TransactionMainModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
	private $mir = "mir";

	public function getEntryType($data){
		$queryData['tableName'] = "sub_menu_master";
		$queryData['where']['sub_controller_name'] = $data['controller'];
		$result = $this->row($queryData);

		if(!empty($data['tableName'])):
			$nextNo = $this->nextTransNo($result->id,1,$result->vou_name_short,$data['tableName']);
		else:
			$nextNo = $this->nextTransNo($result->id,1,$result->vou_name_short);
		endif;

		$nextNo = (!empty($nextNo))?($nextNo + 1):$result->auto_start_no;

		$result->trans_no = $nextNo;
		$result->trans_prefix = $result->vou_prefix.$this->shortYear.'/';

		return $result;
	}

    public function nextTransNo($entry_type,$last_no = 0,$vouNameS = "",$tableName = ""){
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['entry_type'] = $entry_type;
		if(!empty($vouNameS)):
			$data['where']['vou_name_s'] = $vouNameS;
		endif;

		if(!empty($tableName)):
			$data['tableName'] = $tableName;
		else:
			$data['tableName'] = $this->transMain;
		endif;

		$data['where']['trans_date >='] = $this->startYearDate;
		$data['where']['trans_date <='] = $this->endYearDate;
		$trans_no = $this->specificRow($data)->trans_no;
		$trans_no = (empty($last_no))?($trans_no + 1):$trans_no;
		return $trans_no;
    }

	public function getNextNo($data){
		$columnName = $data['no_column'];

		$queryData['tableName'] = $data['tableName'];
        $queryData['select'] = "ifnull(MAX(".$columnName." + 1),1) as ".$columnName;
		$queryData['customWhere'][] = $data['condition'];
        $result = $this->row($queryData)->{$columnName};

		return $result;
	}

	public function getMirNextNo($type = 1){
        $queryData['tableName'] = $this->mir;
        $queryData['select'] = "ifnull(MAX(trans_no + 1),1) as next_no";
        $queryData['where']['trans_type'] = $type;
        $queryData['where']['trans_date >='] = $this->startYearDate;
        $queryData['where']['trans_date <='] = $this->endYearDate;
        return $this->row($queryData)->next_no;
    }

	public function getStockUniqueId($data){
		$queryData = array();
		$queryData['tableName'] = "stock_transaction";
		$queryData['select'] = "unique_id";

		$queryData['where']['item_id'] = $data['item_id'];
		$queryData['where']['location_id'] = $data['location_id'];
		$queryData['where']['batch_no'] = $data['batch_no'];

		if(!empty($data['party_id'])):
			$queryData['where']['party_id'] = $data['party_id'];
		endif;

		$result = $this->row($queryData);

		if(!empty($result->unique_id)):
			return $result->unique_id;
		endif;

		$queryData = array();
		$queryData['tableName'] = "stock_transaction";
		$queryData['select'] = "ifnull((MAX(unique_id) + 1),1) as unique_id";
		return $this->row($queryData)->unique_id;
	}

	public function ledgerEffects($transMainData,$expenseData = array()){
		try{
			$this->deleteLedgerTrans($transMainData['id']);
			$this->deleteExpenseTrans($transMainData['id'],$transMainData['entry_type']);
			
			$partyData = $this->party->getParty(['id'=>$transMainData['opp_acc_id']]);
			if(!empty($partyData)):
				$transLedgerData['currency'] = (!empty($partyData->currency))?$partyData->currency:"INR";
				$transLedgerData['inrrate'] = (!empty($partyData->inrrate) && $partyData->inrrate > 0)?$partyData->inrrate:1;
			endif;

			//Regular Payment Vouchers
			if(in_array($transMainData['vou_name_s'],["BCRct","BCPmt","AdSal","EmpLoan"])):
				$cord = getCrDrEff($transMainData['vou_name_s']);
				//Save Party Account Detail
				$transLedgerData = ['id'=>"",'entry_type'=>$transMainData['entry_type'],'trans_main_id'=>$transMainData['id'],'trans_date'=>$transMainData['trans_date'],'trans_number'=>$transMainData['trans_number'],'doc_date'=>$transMainData['doc_date'],'doc_no'=>$transMainData['doc_no'],'vou_acc_id'=>$transMainData['opp_acc_id'],'opp_acc_id'=>$transMainData['vou_acc_id'],'amount'=>$transMainData['net_amount'],'c_or_d'=>$cord['opp_type'],'remark'=>$transMainData['remark'],'trans_mode'=>$transMainData['payment_mode'],'vou_name_l'=>$transMainData['vou_name_l'],'vou_name_s'=>$transMainData['vou_name_s']];
				$this->storeTransLedger($transLedgerData);

				//Save Bill Wise New Reference
				$billWiseAmount = ($transMainData['net_amount'] + abs($transMainData['round_off_amount']));
				$transBillWiseData = ['id'=>"",'entry_type'=>$transMainData['entry_type'],'trans_main_id'=>$transMainData['id'],'trans_date'=>$transMainData['trans_date'],'trans_number'=>$transMainData['trans_number'],'party_id'=>$transMainData['opp_acc_id'],'amount'=>$billWiseAmount,'c_or_d'=>$cord['opp_type'],'ref_type'=>1];
				$this->storeTransBillWise($transBillWiseData);

				//Save BankCash Account Detail
				$transLedgerData['vou_acc_id'] = $transMainData['vou_acc_id'];
				$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
				$transLedgerData['c_or_d'] = $cord['vou_type'];
				$this->storeTransLedger($transLedgerData);

				if(!empty($transMainData['round_off_amount'])):
					//Kasar Account Effect
					$transLedgerData['vou_acc_id'] = $transMainData['opp_acc_id'];
					$transLedgerData['opp_acc_id'] = $transMainData['round_off_acc_id'];
					$transLedgerData['amount'] = abs($transMainData['round_off_amount']);
					$transLedgerData['c_or_d'] = $cord['opp_type'];
					$this->storeTransLedger($transLedgerData);

					//Kasar BankCash Account Detail
					$transLedgerData['vou_acc_id'] = $transMainData['round_off_acc_id'];
					$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
					$transLedgerData['amount'] = abs($transMainData['round_off_amount']);
					$transLedgerData['c_or_d'] = $cord['vou_type'];
					$this->storeTransLedger($transLedgerData);
				endif;
			endif;

			//GST Payment Voucher
			if(in_array($transMainData['vou_name_s'],["BCGSTPmt"])):
				$cord = getCrDrEff("BCPmt");
				//Save Party Account Detail
				foreach($transMainData['itemData'] as $row):
					if(!empty(floatval($row['amount']))):
						$transLedgerData = ['id'=>"",'entry_type'=>$transMainData['entry_type'],'trans_main_id'=>$transMainData['id'],'trans_date'=>$transMainData['trans_date'],'trans_number'=>$transMainData['trans_number'],'doc_date'=>$transMainData['doc_date'],'doc_no'=>$transMainData['doc_no'],'vou_acc_id'=>$row['acc_id'],'opp_acc_id'=>$transMainData['vou_acc_id'],'amount'=>$row['amount'],'c_or_d'=>$cord['opp_type'],'remark'=>$transMainData['remark'],'trans_mode'=>"",'vou_name_l'=>$transMainData['vou_name_l'],'vou_name_s'=>$transMainData['vou_name_s']];
						$this->storeTransLedger($transLedgerData);
					endif;
				endforeach;

				//Save BankCash Account Detail
				$transLedgerData['vou_acc_id'] = $transMainData['vou_acc_id'];
				$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
				$transLedgerData['c_or_d'] = $cord['vou_type'];
				$transLedgerData['amount'] = $transMainData['net_amount'];
				$this->storeTransLedger($transLedgerData);
			endif;

			if(in_array($transMainData['vou_name_s'],["Purc","Sale","C.N.","D.N.","GExp","GInc"])):
				if($transMainData['ledger_eff'] == 1):
					if(!empty($expenseData)):
						$expenseData['id'] = "";
						$expenseData['trans_main_id'] = $transMainData['id'];
						$expenseData['entry_type'] = $transMainData['entry_type'];
						$this->store('trans_expense',$expenseData);
					endif;

					$cord = getCrDrEff($transMainData['vou_name_s']);
					//Save Party Account Detail
					$transLedgerData = ['id'=>'','entry_type'=>$transMainData['entry_type'],'trans_main_id'=>$transMainData['id'],'trans_date'=>$transMainData['trans_date'],'trans_number'=>$transMainData['trans_number'],'doc_date'=>$transMainData['doc_date'],'doc_no'=>$transMainData['doc_no'],'vou_acc_id'=>$transMainData['opp_acc_id'],'opp_acc_id'=>$transMainData['vou_acc_id'],'amount'=>$transMainData['net_amount'],'c_or_d'=>$cord['vou_type'],'remark'=>$transMainData['remark'],'vou_name_l'=>$transMainData['vou_name_l'],'vou_name_s'=>$transMainData['vou_name_s']];
					$this->storeTransLedger($transLedgerData);

					//Save Party Bill Wise New Reference
					if($transMainData['memo_type'] != "CASH"):
						$transMainData['tds_amount'] = (isset($transMainData['tds_amount']))?$transMainData['tds_amount']:0;
						$billWiseAmount = ($transMainData['net_amount'] - abs($transMainData['tds_amount']));
						$transBillWiseData = ['id'=>"",'entry_type'=>$transMainData['entry_type'],'trans_main_id'=>$transMainData['id'],'trans_date'=>$transMainData['trans_date'],'trans_number'=>$transMainData['trans_number'],'party_id'=>$transMainData['opp_acc_id'],'amount'=>$billWiseAmount,'c_or_d'=>$cord['vou_type'],'ref_type'=>1];
						$this->storeTransBillWise($transBillWiseData);
					endif;

					//Save Sale/Purc Account Detail
					if(!in_array($transMainData['vou_name_s'],["GExp","GInc"])):
						if(!isset($transMainData['sp_acc_id'])):
							$accType = getSPAccCode($transMainData['vou_name_s'],$transMainData['gst_type'],$transMainData['sales_type']);
							if(!empty($accType)):
								$spAcc = $this->party->getParty(['system_code'=>$accType]);
								$transMainData['sp_acc_id'] = (!empty($spAcc))?$spAcc->id:0;
								$this->edit('trans_main',['id'=>$transMainData['id']],['sp_acc_id'=>$transMainData['sp_acc_id']]);
							else:
								$transMainData['sp_acc_id'] = 0;
							endif;
						endif;
						$transLedgerData['vou_acc_id'] = $transMainData['sp_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = abs($transMainData['taxable_amount']);
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
					else:
						$gstExpenseTrans = $this->gstExpense->getGstExpenseItems(['id'=>$transMainData['id']]);
						foreach($gstExpenseTrans as $row):
							$transLedgerData['vou_acc_id'] = $row->item_id;
							$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
							$transLedgerData['amount'] = abs($row->taxable_amount);
							$transLedgerData['c_or_d'] = $cord['opp_type'];
							$this->storeTransLedger($transLedgerData);
						endforeach;
					endif;

					//Save Tax Account Detail
					if($transMainData['gst_type'] == 2):
						if($transMainData['igst_amount'] <> 0):
							//RCM HAVALA Effect for IGST Account
							if($transMainData['tax_class'] == "PURURDIGSTACC"):
								$rcmIgstAcc = $this->party->getParty(['system_code'=>'RCMPAYIGSTACC']);
								if(!empty($rcmIgstAcc)):
									$transLedgerData['vou_acc_id'] = $transMainData['igst_acc_id'];
									$transLedgerData['opp_acc_id'] = $rcmIgstAcc->id;
									$transLedgerData['amount'] = abs($transMainData['igst_amount']);
									$transLedgerData['c_or_d'] = $cord['opp_type'];
									$this->storeTransLedger($transLedgerData);

									$transLedgerData['vou_acc_id'] = $rcmIgstAcc->id;
									$transLedgerData['opp_acc_id'] = $transMainData['igst_acc_id'];
									$transLedgerData['amount'] = abs($transMainData['igst_amount']);
									$transLedgerData['c_or_d'] = $cord['vou_type'];
									$this->storeTransLedger($transLedgerData);
								endif;
							else:
								$transLedgerData['vou_acc_id'] = $transMainData['igst_acc_id'];
								$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
								$transLedgerData['amount'] = abs($transMainData['igst_amount']);
								$transLedgerData['c_or_d'] = $cord['opp_type'];
								$this->storeTransLedger($transLedgerData);
							endif;

							//Ineligible ITC Havala Effect
							if(!empty($transMainData['itc']) && $transMainData['itc'] == "Ineligible"):
								$itcIgstAcc = $this->party->getParty(['system_code'=>'INEITCIGSTACC']);
								if(!empty($itcIgstAcc)):
									$transLedgerData['vou_acc_id'] = $transMainData['igst_acc_id'];
									$transLedgerData['opp_acc_id'] = $itcIgstAcc->id;
									$transLedgerData['amount'] = abs($transMainData['igst_amount']);
									$transLedgerData['c_or_d'] = $cord['vou_type'];
									$this->storeTransLedger($transLedgerData);

									$transLedgerData['vou_acc_id'] = $itcIgstAcc->id;
									$transLedgerData['opp_acc_id'] = $transMainData['igst_acc_id'];
									$transLedgerData['amount'] = abs($transMainData['igst_amount']);
									$transLedgerData['c_or_d'] = $cord['opp_type'];
									$this->storeTransLedger($transLedgerData);
								endif;
							endif;
						endif;
					else:
						if($transMainData['cgst_amount'] <> 0 && $transMainData['sgst_amount'] <> 0):
							//RCM HAVALA Effect for CGST and SGST Account
							if($transMainData['tax_class'] == "PURURDGSTACC"):
								$rcmCgstAcc = $this->party->getParty(['system_code'=>'RCMPAYCGSTACC']);
								if(!empty($rcmCgstAcc)):
									$transLedgerData['vou_acc_id'] = $transMainData['cgst_acc_id'];
									$transLedgerData['opp_acc_id'] = $rcmCgstAcc->id;
									$transLedgerData['amount'] = abs($transMainData['cgst_amount']);
									$transLedgerData['c_or_d'] = $cord['opp_type'];
									$this->storeTransLedger($transLedgerData);

									$transLedgerData['vou_acc_id'] = $rcmCgstAcc->id;
									$transLedgerData['opp_acc_id'] = $transMainData['cgst_acc_id'];
									$transLedgerData['amount'] = abs($transMainData['cgst_amount']);
									$transLedgerData['c_or_d'] = $cord['vou_type'];
									$this->storeTransLedger($transLedgerData);
								endif;

								$rcmSgstAcc = $this->party->getParty(['system_code'=>'RCMPAYSGSTACC']);
								if(!empty($rcmSgstAcc)):
									$transLedgerData['vou_acc_id'] = $transMainData['sgst_acc_id'];
									$transLedgerData['opp_acc_id'] = $rcmSgstAcc->id;
									$transLedgerData['amount'] = abs($transMainData['sgst_amount']);
									$transLedgerData['c_or_d'] = $cord['opp_type'];
									$this->storeTransLedger($transLedgerData);

									$transLedgerData['vou_acc_id'] = $rcmSgstAcc->id;
									$transLedgerData['opp_acc_id'] = $transMainData['sgst_acc_id'];
									$transLedgerData['amount'] = abs($transMainData['sgst_amount']);
									$transLedgerData['c_or_d'] = $cord['vou_type'];
									$this->storeTransLedger($transLedgerData);
								endif;
							else:
								$transLedgerData['vou_acc_id'] = $transMainData['cgst_acc_id'];
								$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
								$transLedgerData['amount'] = abs($transMainData['cgst_amount']);
								$transLedgerData['c_or_d'] = $cord['opp_type'];
								$this->storeTransLedger($transLedgerData);

								$transLedgerData['vou_acc_id'] = $transMainData['sgst_acc_id'];
								$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
								$transLedgerData['amount'] = abs($transMainData['sgst_amount']);
								$transLedgerData['c_or_d'] = $cord['opp_type'];
								$this->storeTransLedger($transLedgerData);
							endif;
						endif;

						//Ineligible ITC Havala Effect
						if(!empty($transMainData['itc']) && $transMainData['itc'] == "Ineligible"):
							$itcCgstAcc = $this->party->getParty(['system_code'=>'INEITCCGSTACC']);							
							if(!empty($itcCgstAcc)):
								$transLedgerData['vou_acc_id'] = $transMainData['cgst_acc_id'];
								$transLedgerData['opp_acc_id'] = $itcCgstAcc->id;
								$transLedgerData['amount'] = abs($transMainData['cgst_amount']);
								$transLedgerData['c_or_d'] = $cord['vou_type'];
								$this->storeTransLedger($transLedgerData);

								$transLedgerData['vou_acc_id'] = $itcCgstAcc->id;
								$transLedgerData['opp_acc_id'] = $transMainData['cgst_acc_id'];
								$transLedgerData['amount'] = abs($transMainData['cgst_amount']);
								$transLedgerData['c_or_d'] = $cord['opp_type'];
								$this->storeTransLedger($transLedgerData);
							endif;

							$itcSgstAcc = $this->party->getParty(['system_code'=>'INEITCSGSTACC']);
							if(!empty($itcSgstAcc)):
								$transLedgerData['vou_acc_id'] = $transMainData['sgst_acc_id'];
								$transLedgerData['opp_acc_id'] = $itcSgstAcc->id;
								$transLedgerData['amount'] = abs($transMainData['sgst_amount']);
								$transLedgerData['c_or_d'] = $cord['vou_type'];
								$this->storeTransLedger($transLedgerData);

								$transLedgerData['vou_acc_id'] = $itcSgstAcc->id;
								$transLedgerData['opp_acc_id'] = $transMainData['sgst_acc_id'];
								$transLedgerData['amount'] = abs($transMainData['sgst_amount']);
								$transLedgerData['c_or_d'] = $cord['opp_type'];
								$this->storeTransLedger($transLedgerData);
							endif;
						endif;
					endif;

					if((isset($transMainData['cess_amount'])) && $transMainData['cess_amount'] <> 0):
						$transLedgerData['vou_acc_id'] = $transMainData['cess_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = abs($transMainData['cess_amount']);
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
					endif;

					if((isset($transMainData['cess_qty_amount'])) && $transMainData['cess_qty_amount'] <> 0):
						$transLedgerData['vou_acc_id'] = $transMainData['cess_qty_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = abs($transMainData['cess_qty_amount']);
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
					endif;

					if((isset($transMainData['tcs_amount'])) && $transMainData['tcs_amount'] <> 0):
						$transLedgerData['vou_acc_id'] = $transMainData['tcs_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = abs($transMainData['tcs_amount']);
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
					endif;

					if((isset($transMainData['tds_amount'])) && $transMainData['tds_amount'] <> 0):
						$transLedgerData['vou_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['tds_acc_id'];
						$transLedgerData['amount'] = abs($transMainData['tds_amount']);
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
						
						$transLedgerData['vou_acc_id'] = $transMainData['tds_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = abs($transMainData['tds_amount']);
						$transLedgerData['c_or_d'] = $cord['vou_type'];
						$this->storeTransLedger($transLedgerData);
					endif;

					//Save Expense Account Detail
					$expType = (in_array($transMainData['vou_name_s'],["Purc","D.N.","GExp"]))?1:2;
					$expenseMaster = $this->expenseMaster->getActiveExpenseList($expType);
					$expBFTAmt = 0;
					foreach($expenseMaster as $row):
						if(isset($expenseData[$row->map_code."_acc_id"]) && isset($expenseData[$row->map_code.'_amount'])):
							if($expenseData[$row->map_code.'_amount'] <> 0 && $row->map_code != "roff"): 
								$transLedgerData['vou_acc_id'] = $expenseData[$row->map_code."_acc_id"];
								$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
								$transLedgerData['amount'] = abs($expenseData[$row->map_code.'_amount']);
								$transLedgerData['c_or_d'] = (in_array($transMainData['vou_name_s'],["Purc","C.N.","GExp"]))?(($row->add_or_deduct == 1)?"DR":"CR"):(($row->add_or_deduct == 1)?"CR":"DR");
								$this->storeTransLedger($transLedgerData);

								if($row->position == 1): $expBFTAmt += floatval($expenseData[$row->map_code.'_amount']); endif;
							endif;
						endif;
					endforeach;

					//Before Tax Expense Amount add to Items and calculate gst
					if($expBFTAmt <> 0):
						//remove old expense amount and gst amount
						$setData = array();
						$setData['tableName'] = $this->transChild;
						$setData['where']['trans_main_id'] = $transLedgerData['trans_main_id'];
						$setData['update']['exp_taxable_amount'] = 0;
						$setData['update']['exp_gst_amount'] = 0;
						$this->setValue($setData);

						//Get Invoice Items
						$queryData = [];
						$queryData['tableName'] = $this->transChild;
						$queryData['select'] = "id,gst_per";
						$queryData['where']['trans_main_id'] = $transLedgerData['trans_main_id'];
						$queryData['whereFalse']['gst_per'] = "(SELECT MAX(gst_per) as gst_per FROM trans_child WHERE is_delete = 0 AND trans_main_id = ".$transLedgerData['trans_main_id'].")";
						$itemDetails = $this->rows($queryData);

						$itemCount = count($itemDetails);
						$expTaxableAmt = 0;
						if($itemCount > 0):
							$expTaxableAmt = round(($expBFTAmt / $itemCount),3);
							foreach($itemDetails as $row):
								$expGstAmt = 0;
								if(floatval($row->gst_per) > 0):
									$expGstAmt = round(( ($expTaxableAmt * $row->gst_per) / 100),3);
								endif;

								//update new values
								$setData = array();
								$setData['tableName'] = $this->transChild;
								$setData['where']['id'] = $row->id;
								$setData['update']['exp_taxable_amount'] = $expTaxableAmt;
								$setData['update']['exp_gst_amount'] = $expGstAmt;
								$this->setValue($setData);
							endforeach;
						endif;
					endif;

					//Save Round off Account Detail 
					if(isset($transMainData['round_off_amount']) && $transMainData['round_off_amount'] <> 0): 
						$transLedgerData['vou_acc_id'] = $transMainData["round_off_acc_id"];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = abs($transMainData['round_off_amount']);
						$transLedgerData['c_or_d'] = (in_array($transMainData['vou_name_s'],["Purc","C.N.","GExp"]))?(($transMainData['round_off_amount'] > 0)?"DR":"CR"):(($transMainData['round_off_amount'] > 0)?"CR":"DR");
						$this->storeTransLedger($transLedgerData);
					endif;
				endif;
			endif;

			return true;
		}catch(\Throwable $e){
			return false;
        }		
	}

	public function storeTransLedger($data){
		try{
			$data['p_or_m'] = ($data['c_or_d'] == "DR")?-1:1;			
			$this->store("trans_ledger",$data);			
			return true;
		}catch(\Throwable $e){
			return false;
        }			
	}

	public function deleteLedgerTrans($trans_main_id){
		try{
			$queryData = array();
			$queryData['tableName'] = "trans_ledger";
			$queryData['where']['trans_main_id'] = $trans_main_id;
			$transLedgerData = $this->rows($queryData);

			if(!empty($transLedgerData)):
				$this->remove("trans_ledger",['trans_main_id'=>$trans_main_id]);
				//$this->deleteExpenseTrans($trans_main_id);
				$this->remove("trans_billwise",['trans_main_id'=>$trans_main_id,'party_id'=>$transLedgerData[0]->vou_acc_id]);
			endif;
			return true;
		}catch(\Throwable $e){
			return false;
        }
	}

	public function deleteExpenseTrans($trans_main_id,$entry_type){
		try{
			$this->trash('trans_expense',['trans_main_id'=>$trans_main_id,'entry_type'=>$entry_type]);
			return true;
		}catch(\Throwable $e){
			return false;
		}
	}

	public function storeTransBillWise($data){
		try{
			$data['p_or_m'] = ($data['c_or_d'] == "DR")?-1:1;
			$this->store("trans_billwise",$data);
			return true;
		}catch(\Throwable $e){
			return false;
        }
	}

	public function checkBillWiseRef($data){
		$queryData = array();
		$queryData['tableName'] = "trans_billwise";
		$queryData['where']['entry_type'] = $data['entry_type'];
		$queryData['where']['trans_main_id'] = $data['id'];
		$queryData['where']['party_id'] = $data['party_id'];
		$queryData['where']['ref_type'] = 1;
		$billwiseData = $this->rows($queryData);
		
		if(!empty($billwiseData)):
			$i=0;
			foreach($billwiseData as $row):
				$queryData = array();
				$queryData['tableName'] = "trans_billwise";
				$queryData['select'] = "SUM(amount) as total_amount";
				$queryData['where']['ag_ref_id'] = $row->id;
				$billwiseAdjustmentData = $this->row($queryData);
				
				if(!empty($billwiseAdjustmentData) && floatval($billwiseAdjustmentData->total_amount) > 0):					
					$i++;
				endif;
			endforeach;

			if($i > 0):	return true; endif;
		endif;

		$queryData = array();
		$queryData['tableName'] = "trans_billwise";
		$queryData['where']['entry_type'] = $data['entry_type'];
		$queryData['where']['trans_main_id'] = $data['id'];
		$queryData['where']['party_id'] = $data['party_id'];
		$queryData['where']['ref_type'] = 2;
		$billwiseAgRefData = $this->rows($queryData);
		
		if(!empty($billwiseAgRefData)):
			$i=0;
			foreach($billwiseAgRefData as $row):
				$queryData = [];
				$queryData['tableName'] = "trans_billwise";
				$queryData['where']['id'] = $row->ag_ref_id;
				$agRefData = $this->row($queryData);

				if(empty($agRefData)):
					$this->remove("trans_billwise",['id'=>$row->id]);
				else:
					$i++;
				endif;
			endforeach;
			
			if($i > 0):	return true; endif;
		endif;

		$this->remove("trans_billwise",['trans_main_id'=>$data['id'],'party_id'=>$data['party_id']]);
		return false;
	}

	public function getPartyInvoiceList($data){
        $queryData  = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.id,trans_main.entry_type,trans_main.trans_number,DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y') as trans_date";
        $queryData['where']['party_id'] = $data['party_id'];
        $queryData['where_in']['vou_name_s'] = (in_array($data['order_type'],["Increase Sales","Decrease Sales","Sales Return"]))?"'Sale','GInc'":"'Purc','GExp'";
        $queryData['like']['trans_main.trans_number'] = $data['doc_no'];
        $result = $this->rows($queryData);
        return $result;
    }

	public function getPartyNetInvoiceSum($postData){
		$queryData  = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "ifnull(SUM(CASE WHEN trans_main.vou_name_s = 'Sale' THEN net_amount ELSE taxable_amount END),0) as net_amount_sum";
        $queryData['where']['trans_main.party_id'] = $postData['party_id'];
        $queryData['where_in']['trans_main.vou_name_s'] = $postData['vou_name_s'];
		$queryData['where']['trans_main.trans_date >='] = $this->startYearDate;
		$queryData['where']['trans_main.trans_date <='] = $this->endYearDate;
		
		if(!empty($postData['id'])):
			$queryData['where']['trans_main.id <'] = $postData['id'];
		endif;
        $result = $this->row($queryData);
		
        return ['status'=>1,'netInvoiceSum'=>$result->net_amount_sum];
	}
}	
?>