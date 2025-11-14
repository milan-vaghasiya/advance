<?php
class SalesInvoiceModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $transExpense = "trans_expense";
    private $transDetails = "trans_details";
    private $stockTrans = "stock_trans";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
		$data['select'] = "trans_main.id,trans_main.trans_number,trans_main.trans_date,trans_main.party_name,trans_main.taxable_amount,trans_main.gst_amount,trans_main.net_amount,trans_main.ewb_status,trans_main.eway_bill_no,trans_main.party_id,trans_main.e_inv_status,trans_main.e_inv_no,trans_status,transChild.inv_item_count,transChild.pdi_report_count,party_master.pdi_type,party_master.party_logo,trans_main.tax_class";

		//51-TOOL COST CATEGORY
        $data['leftJoin']['(SELECT 
				count(trans_child.id) AS inv_item_count,
				SUM(IF(trans_child.trans_status = 3,1,0)) AS pdi_report_count,
				trans_child.trans_main_id 
			FROM 
				trans_child 
			JOIN item_master ON item_master.id = trans_child.item_id
			WHERE 
				trans_child.is_delete = 0 AND item_master.category_id != 51
			GROUP BY trans_child.trans_main_id) AS transChild '] = "transChild.trans_main_id = trans_main.id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		
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
        $data['searchCol'][] = "trans_main.gst_amount";
        $data['searchCol'][] = "trans_main.net_amount";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function getFinalPackDTRows($data){
        $data['tableName'] = 'final_packing_master';
        $data['select'] = "final_packing_master.*, party_master.party_name, finalPackTrans.total_package, finalPackTrans.total_box, finalPackTrans.total_qty, IFNULL(st.stock_qty,0) as stock_qty";

        $data['leftJoin']['party_master'] = "party_master.id = final_packing_master.party_id";
        $data['leftJoin']['(SELECT COUNT(DISTINCT package_no) AS total_package,SUM(total_box) AS total_box,SUM(total_qty) AS total_qty,packing_id FROM final_packing_trans where final_packing_trans.is_delete=0 GROUP BY packing_id) finalPackTrans'] = 'finalPackTrans.packing_id = final_packing_master.id';
        $data['leftJoin']['(SELECT SUM(qty * p_or_m) as stock_qty,trans_type,main_ref_id FROM stock_trans WHERE is_delete = 0 AND trans_type IN("FPK") AND p_or_m = 1 GROUP BY main_ref_id) as st'] = "st.main_ref_id = final_packing_master.id";

        $data['where']['final_packing_master.status'] = 1;
		
		$data['having'][] = "total_qty = stock_qty";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "final_packing_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(final_packing_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "finalPackTrans.total_package";
        $data['searchCol'][] = "finalPackTrans.total_box";
        $data['searchCol'][] = "finalPackTrans.total_qty";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $cahsEntryNew = false;
            if(empty($data['id'])):
                $cahsEntryNew = true;
            endif;
            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];

            if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_number'] = "Inv. No. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if(!empty($data['id'])):
                $dataRow = $this->getSalesInvoice(['id'=>$data['id'],'itemList'=>1]);

                $checkBillWiseRef = $this->transMainModel->checkBillWiseRef(['id'=>$dataRow->id,'party_id'=>$dataRow->party_id,'entry_type'=>$dataRow->entry_type]);
                if($checkBillWiseRef == true):
                    return ['status'=>2,'message'=>'Bill Wise Reference already adjusted. if you want to update this entry first unset all adjustment.'];
                endif;

                foreach($dataRow->itemList as $row):
                    if(!empty($row->ref_id)):
                        $setData = array();
                        $setData['tableName'] = ($row->from_entry_type == 14 || $row->from_entry_type == 241)?'so_trans':'trans_child';
                        $setData['where']['id'] = $row->ref_id;
                        $setData['set_value']['dispatch_qty'] = 'IF(`dispatch_qty` - '.$row->qty.' >= 0, `dispatch_qty` - '.$row->qty.', 0)';
                        if($row->from_entry_type == 14 || $row->from_entry_type == 241){
                            $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 3 END)";
                        }else{
                            $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 0 END)";
                        }
                        $this->setValue($setData);
                    endif;

                    $this->trash($this->transChild,['id'=>$row->id]);
                endforeach;

                if(!empty($dataRow->ref_id) && $dataRow->from_entry_type != 241):
                    $oldRefIds = explode(",",$dataRow->ref_id);
                    foreach($oldRefIds as $main_id):
                        $setData = array();
                        $setData['tableName'] = ($dataRow->from_entry_type == 14)?'so_master':'trans_main';
                        $setData['where']['id'] = $main_id;
                        if($dataRow->from_entry_type == 14){
                            $setData['update']['trans_status'] = "(SELECT IF(COUNT(id) = SUM(IF(trans_status NOT IN (0, 3), 1, 0)),1,3) AS trans_status FROM so_trans WHERE trans_main_id = '".$main_id."' AND is_delete = 0)";
                        }else{
                            $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM trans_child WHERE trans_main_id = ".$main_id." AND is_delete = 0)";;
                        }
                        $this->setValue($setData);
                    endforeach;
                elseif($dataRow->from_entry_type == 241):
                    $fpData = $this->finalPacking->getPackingItemDetail(['packing_id'=>$dataRow->ref_id]);
                    foreach($fpData AS $row){
                        $setData = array();
                        $setData['tableName'] = 'so_master';
                        $setData['where']['id'] = $row->so_id;
                        $setData['update']['trans_status'] = "(SELECT IF(COUNT(id) = SUM(IF(trans_status NOT IN (0, 3), 1, 0)),1,3) AS trans_status FROM so_trans WHERE trans_main_id = '".$row->so_id."' AND is_delete = 0)";
                        $this->setValue($setData);
                    }
                    $this->edit("final_packing_master",['id'=>$dataRow->ref_id],['status'=>1]);
                    $this->edit("final_packing_trans",['packing_id'=>$dataRow->ref_id],['inv_trans_id'=>0]);
                endif;
                
                $this->trash($this->transExpense,['trans_main_id'=>$data['id'],'entry_type'=>$this->data['entryData']->id]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"SI TERMS"]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"SI MASTER DETAILS"]);
                $this->remove($this->stockTrans,['main_ref_id'=>$data['id'],'trans_type'=>'INV']);
            endif;
            
            if($data['memo_type'] == "CASH"):
				$cashAccData = $this->party->getParty(['system_code'=>"CASHACC"]);
				$data['opp_acc_id'] = $cashAccData->id;
			else:
				$data['opp_acc_id'] = $data['party_id'];
			endif;
            $data['p_or_m'] = -1;
            $data['ledger_eff'] = 1;
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
            $termsData = (!empty($data['conditions']))?$data['conditions']:array();

            unset($transExp['exp_amount'],$data['itemData'],$data['expenseData'],$data['conditions'],$data['masterDetails']);		

            $result = $this->store($this->transMain,$data,'Sales Invoice');

            if(!empty($masterDetails)):
                $masterDetails['id'] = "";
                $masterDetails['main_ref_id'] = $result['id'];
                $masterDetails['table_name'] = $this->transMain;
                $masterDetails['description'] = "SI MASTER DETAILS";
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
                    'description' => "SI TERMS",
                    'main_ref_id' => $result['id'],
                    't_col_1' => $termsData
                ];
                $this->store($this->transDetails,$termsData);
            endif;

            $i=1;$soIds = [];
            foreach($itemData as $row):
                $row['entry_type'] = $data['entry_type'];
                $row['trans_main_id'] = $result['id'];
                $row['gst_amount'] = $row['igst_amount'];
                $row['is_delete'] = 0;

                $batchDetail = $row['batch_detail']; unset($row['batch_detail']);
                $itemTrans = $this->store($this->transChild,$row);

                if($row['stock_eff'] == 1 && $row['from_entry_type'] != 241):
                    $batchDetail = json_decode($batchDetail,true);
                    foreach($batchDetail as $batch):
                        if(floatval($batch['batch_qty']) > 0):
                            $stockData = [
                                'id' => "",
                                'trans_type' => 'INV',
                                'trans_date' => $data['trans_date'],
                                'item_id' => $row['item_id'],
                                'location_id' => $batch['location_id'],
                                'batch_no' => $batch['batch_no'],
                                'p_or_m' => -1,
                                'qty' => $batch['batch_qty'],
                                'opt_qty' => $batch['opt_qty'],
                                'ref_no' => $data['trans_number'],
                                'main_ref_id' => $result['id'],
                                'child_ref_id' => $itemTrans['id'],
                                'remark' => $batch['remark']
                            ];
        
                            $this->store($this->stockTrans,$stockData);
                        endif;
                    endforeach;
                elseif( $row['from_entry_type'] == 241):
                    /** Get Final PAcking Trans Data  */
                    $finalPackData = $this->finalPacking->getPackingItemDetail(['packing_id'=>$data['ref_id'],'ids'=>$row['make']]);
                    foreach($finalPackData AS $fpData):
                        $soIds[] = $fpData->so_id;
                        if($fpData->packing_type == 1):
                            //IF Primary PAcking Stock Effect
                            $primaryData = $this->finalPacking->getFinalPackingBoxDetail(['pack_trans_id'=>$fpData->id,'primaryPackBatch'=>1]);
                            $primaryBox = array_reduce($primaryData , function($primaryBox, $batch) { $primaryBox[$batch->primary_pack_id][] = $batch; return $primaryBox; }, []);

                            foreach($primaryData AS $pack):
                                $qty = (count($primaryBox[$pack->primary_pack_id]) > 1)?$pack->batch_qty:$pack->qty;
                                $stockMinusData = [
                                                'id' => "",
                                                'trans_type' => 'INV',
                                                'trans_date' => $data['trans_date'],
                                                'item_id' => $row['item_id'],
                                                'location_id' => $this->RTD_FPCK_STORE->id,
                                                'batch_no' => $pack->primary_batch,
                                                'p_or_m' => -1,
                                                'qty' => $qty,
                                                'ref_no' => $data['trans_number'],
                                                'main_ref_id' => $result['id'],
                                                'child_ref_id' => $itemTrans['id'],
                                            ];
                                $this->store('stock_trans',$stockMinusData);
                                
                            endforeach;
                        else:
                            //Direct Final Packing Stock Effect
                            $boxData = $this->finalPacking->getFinalPackingBoxDetail(['pack_trans_id'=>$fpData->id]);
                            foreach($boxData AS $pack):
                                $stockMinusData = [
                                                'id' => "",
                                                'trans_type' => 'INV',
                                                'trans_date' => $data['trans_date'],
                                                'item_id' => $row['item_id'],
                                                'location_id' => $this->RTD_FPCK_STORE->id,
                                                'batch_no' => $pack->batch_no,
                                                'p_or_m' => -1,
                                                'qty' => $pack->qty,
                                                'ref_no' => $data['trans_number'],
                                                'main_ref_id' => $result['id'],
                                                'child_ref_id' => $itemTrans['id'],
                                            ];
                                $this->store('stock_trans',$stockMinusData);
                            endforeach;
                        endif;
                        //Update Invoice Trans ID in final pack trans table
                        $this->edit("final_packing_trans",['id'=>$fpData->id],['inv_trans_id'=>$itemTrans['id']]);
                    endforeach;
                    
                endif;

                if(!empty($row['ref_id'])):
                    $setData = array();
                    $setData['tableName'] = ($data['from_entry_type'] == 14 || $data['from_entry_type'] == 241)?'so_trans':'trans_child';
                    $setData['where']['id'] = $row['ref_id'];
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$row['qty'];
                    if($data['from_entry_type'] == 14|| $data['from_entry_type'] == 241){
                        $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 3 END)";
                    }else{
                        $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 0 END)";
                    }
                    $this->setValue($setData);
                endif;
            endforeach;

            if(!empty($data['ref_id']) && $data['from_entry_type'] != 241):
                $refIds = explode(",",$data['ref_id']);
                foreach($refIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = ($data['from_entry_type'] == 14)?'so_master':'trans_main';
                    $setData['where']['id'] = $main_id;
                    if($data['from_entry_type'] == 14){
                        $setData['update']['trans_status'] = "(SELECT IF(COUNT(id) = SUM(IF(trans_status NOT IN (0, 3), 1, 0)),1,3) AS trans_status FROM so_trans WHERE trans_main_id = '".$main_id."' AND is_delete = 0)";
                    }else{
                       $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM trans_child WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
                    }
                    $this->setValue($setData);
                endforeach;
            elseif($data['from_entry_type'] == 241):
                $this->edit("final_packing_master",['id'=>$data['ref_id']],['status'=>2]);
                if(!empty($soIds)):
                    foreach($soIds as $main_id):
                        $setData = array();
                        $setData['tableName'] = 'so_master';
                        $setData['where']['id'] = $main_id;
                        $setData['update']['trans_status'] = "(SELECT IF(COUNT(id) = SUM(IF(trans_status NOT IN (0, 3), 1, 0)),1,3) AS trans_status FROM so_trans WHERE trans_main_id = '".$main_id."' AND is_delete = 0)";
                        $this->setValue($setData);
                    endforeach;
                endif;
            endif;
            
            $data['id'] = $result['id'];
            $this->transMainModel->ledgerEffects($data,$expenseData);

            if($data['sys_per'] < 100):
                $this->saveCashInvoice($result['id'],$cahsEntryNew);
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
        $queryData['where']['trans_date >='] = $this->startYearDate;
        $queryData['where']['trans_date <='] = $this->endYearDate;

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function saveCashInvoice($id,$cahsEntryNew){
        try{
            $this->db->trans_begin();

            $result = $this->getSalesInvoice(['id'=>$id,'itemList'=>1]);

            $entryData = $this->transMainModel->getEntryType(['controller'=>'estimate']);

            $estimateId = "";
            if($cahsEntryNew == false):
                $queryData = array();
                $queryData['tableName'] = $this->transMain;
                $queryData['select'] = "id";
                $queryData['where']['ref_id'] = $id;
                $queryData['where']['from_entry_type'] = $result->entry_type;
                $estimate = $this->row($queryData);

                if(!empty($estimate->id)):
                    $estimateId = $estimate->id;
                    $this->trash($this->transChild,['trans_main_id'=>$estimateId]);
                endif;
            endif;            

            $itemData = array();
            $totalNetAmount = $titalDiscAmount = $totalAmount = 0;
            foreach($result->itemList as $row):
                $row->ref_id = $row->id;
                $row->id = "";
                $row->from_entry_type = $row->entry_type;
                $row->entry_type = $entryData->id;
                $row->stock_eff = 0;
                $row->gst_per = 0;
                $row->gst_amount = 0;
                $row->igst_per = 0;
                $row->igst_amount = 0;
                $row->cgst_per = 0;
                $row->cgst_amount = 0;
                $row->sgst_per = 0;
                $row->sgst_amount = 0;

                $row->price = ($row->sys_price - $row->price);
                $row->amount = $row->qty * $row->price;
                $row->disc_amount = (!empty(floatVal($row->disc_per)))?round((($row->amount * $row->disc_per)/100),2):0;
                $row->net_amount = $row->taxable_amount = $row->amount - $row->disc_amount;

                unset($row->batch_detail);

                $itemData[] = (array) $row;
                $totalAmount += $row->amount;
                $titalDiscAmount += $row->disc_amount;
                $totalNetAmount += $row->net_amount;
            endforeach;

            $masterData = [
                'id' => $estimateId,
                'entry_type' => $entryData->id,
                'from_entry_type' => $result->entry_type,
                'ref_id' => $result->id,
                'trans_date' => $result->trans_date,
                'trans_number' => $result->trans_number,
                'memo_type' => $result->memo_type,
                'gst_type' => 3,
                'vou_acc_id' => $result->vou_acc_id,
                'opp_acc_id' => $result->opp_acc_id,
                'party_id' => $result->party_id,
				'ship_to' => $result->ship_to,
                'vehicle_no' => $result->vehicle_no,
                'party_name' => $result->party_name,
                'gstin' => $result->gstin,
                'party_state_code' => $result->party_state_code,
                'sales_type' => $result->sales_type,
                'order_type' => $result->order_type,
                'doc_no' => $result->doc_no,
                'doc_date' => $result->doc_date,
                'total_amount' => $totalAmount,
                'taxable_amount' => $totalNetAmount,
                'disc_amount' => $titalDiscAmount,
                'net_amount' => $totalNetAmount,
                'vou_name_l' => $entryData->vou_name_long,
                'vou_name_s' => $entryData->vou_name_short
            ];

            $save = $this->store($this->transMain,$masterData);

            foreach($itemData as $row):
                $row['trans_main_id'] = $save['id'];
                $this->store($this->transChild,$row);
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return true;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return false;
        }
    }

    public function getSalesInvoice($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.*,trans_details.t_col_1 as contact_person,trans_details.t_col_2 as contact_no,trans_details.t_col_3 as ship_address,trans_details.i_col_2 as transport_id,trans_details.t_col_4 as transporter_name,trans_details.t_col_5 as transporter_gst_no,employee_master.emp_name as created_name, IFNULL(final_packing_master.p_type,'') AS p_type";

        if(!empty($data['packingDetail'])):
            $queryData['select'] .= ", final_packing_master.method_of_dispatch, final_packing_master.type_of_shipment, final_packing_master.country_of_origin, final_packing_master.country_of_fd, final_packing_master.port_of_loading, final_packing_master.date_of_departure, final_packing_master.port_of_discharge, final_packing_master.final_destination, final_packing_master.delivery_type, final_packing_master.delivery_location, final_packing_master.terms_of_delivery";
        endif;
        
        $queryData['leftJoin']['final_packing_master'] = "final_packing_master.id = trans_main.ref_id";

        $queryData['leftJoin']['trans_details'] = "trans_main.id = trans_details.main_ref_id AND trans_details.description = 'SI MASTER DETAILS' AND trans_details.table_name = '".$this->transMain."'";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = trans_main.created_by";
		$queryData['where']['trans_main.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
			$data['from_entry_type'] = $result->from_entry_type;
            $result->itemList = $this->getSalesInvoiceItems($data);
        endif;

		if(!empty($data['discStatus'])):
			$disc = $this->getInvWithoutDisc($data['id']);
			$result->discStatus = (!empty($disc)) ? count($disc) : 0;
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
        $queryData['where']['table_name'] = $this->transMain;
        $queryData['where']['description'] = "SI TERMS";
        $result->termsConditions = $this->row($queryData);

        return $result;
    }

    public function getSalesInvoiceItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
		$queryData['select'] = "trans_child.*, ecn_master.rev_no, ecn_master.cust_rev_no, ecn_master.drw_no,so_master.doc_no,so_master.doc_date";
		if($data['from_entry_type'] == 177):
			$queryData['leftJoin']['trans_child dc_trans'] = "dc_trans.id = trans_child.ref_id";
			$queryData['leftJoin']['so_trans'] = "so_trans.id = dc_trans.ref_id";
			$queryData['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id";
		else:
			$queryData['leftJoin']['so_trans'] = "so_trans.id = trans_child.ref_id";
			$queryData['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id";
		endif;

        $queryData['select'] .= ',IFNULL(final_packing_trans.box_detail,"") AS box_detail';
        $queryData['leftJoin']['(SELECT
                MAX(CAST(final_packing_trans.package_no AS UNSIGNED)) AS box_detail, 
                inv_trans_id
            FROM  final_packing_trans
            WHERE final_packing_trans.is_delete = 0
            GROUP BY final_packing_trans.inv_trans_id
        ) final_packing_trans'] = "trans_child.id = final_packing_trans.inv_trans_id";
		
		$queryData['leftJoin']['(SELECT drw_no,cust_rev_date,cust_rev_no,rev_no,rev_date,item_id FROM ecn_master WHERE is_delete=0 AND status=2 GROUP BY item_id ORDER BY ecn_date DESC) as ecn_master'] = "ecn_master.item_id = trans_child.item_id";
        $queryData['where']['trans_child.trans_main_id'] = $data['id'];


        $result = $this->rows($queryData);

        foreach($result as &$row):
            $batchData = [];
            if($row->stock_eff == 1 || $row->from_entry_type == 241):
                $queryData = [];
                $queryData['tableName'] = $this->stockTrans;
                $queryData['select'] = "batch_no,location_id,qty as batch_qty,opt_qty, remark";
                $queryData['where']['trans_type'] = 'INV';
                $queryData['where']['main_ref_id'] = $row->trans_main_id;
                $queryData['where']['child_ref_id'] = $row->id;
                $queryData['where']['item_id'] = $row->item_id;
                $batchData = $this->rows($queryData);
            endif;
            $row->batch_detail = json_encode($batchData);
        endforeach;

        return $result;
    }

    public function getSalesInvoiceItem($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['id'] = $data['id'];
        $result = $this->row($queryData);

        if(!empty($data['batchDetail'])):
            $batchData = [];
            //if($result->stock_eff == 1):
                $queryData = [];
                $queryData['tableName'] = $this->stockTrans;
                $queryData['select'] = "batch_no,location_id,qty as batch_qty,opt_qty, remark";
                $queryData['where']['trans_type'] = 'INV';
                $queryData['where']['main_ref_id'] = $result->trans_main_id;
                $queryData['where']['child_ref_id'] = $result->id;
                $queryData['where']['item_id'] = $result->item_id;
                $batchData = $this->rows($queryData);
            /*
			else:
                if($result->ref_id > 0 && $result->from_entry_type == 177):
                    $queryData = [];
                    $queryData['tableName'] = $this->stockTrans;
                    $queryData['select'] = "batch_no,location_id,qty as batch_qty,opt_qty, remark";
                    $queryData['where']['trans_type'] = 'DLC';
                    // $queryData['where']['main_ref_id'] = $result->trans_main_id;
                    $queryData['where']['child_ref_id'] = $result->ref_id;
                    $queryData['where']['item_id'] = $result->item_id;
                    $batchData = $this->rows($queryData);
                endif;
            endif;
			*/
            $result->batch_detail = json_encode($batchData);
        endif;

        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $dataRow = $this->getSalesInvoice(['id'=>$id,'itemList'=>1]);

            $postData["table_name"] = $this->transMain;
            $postData['where'] = [['column_name'=>'from_entry_type','column_value'=>$this->data['entryData']->id]];
            $postData['find'] = [['column_name'=>'ref_id','column_value'=>$id]];
            $checkRef = $this->checkEntryReference($postData);
            if($checkRef['status'] == 0):
                $this->db->trans_rollback();
                return $checkRef;
            endif;

            $checkBillWiseRef = $this->transMainModel->checkBillWiseRef(['id'=>$dataRow->id,'party_id'=>$dataRow->party_id,'entry_type'=>$dataRow->entry_type]);
            if($checkBillWiseRef == true):
                return ['status'=>0,'message'=>'Bill Wise Reference already adjusted. if you want to delete this entry first unset all adjustment.'];
            endif;

            if($dataRow->sys_per < 100):
                $queryData = array();
                $queryData['tableName'] = $this->transMain;
                $queryData['select'] = "id";
                $queryData['where']['ref_id'] = $dataRow->id;
                $queryData['where']['from_entry_type'] = $dataRow->entry_type;
                $estimateId = $this->row($queryData);

                //Remove Estimate Recoreds
                $this->trash($this->transMain,['id'=>$estimateId->id]);
                $this->trash($this->transChild,['trans_main_id'=>$estimateId->id]);
            endif;

            foreach($dataRow->itemList as $row):
                if(!empty($row->ref_id)):
                    $setData = array();
                    $setData['tableName'] = ($row->from_entry_type == 14 || $row->from_entry_type == 241 )?'so_trans':'trans_child';
                    $setData['where']['id'] = $row->ref_id;
                    $setData['set_value']['dispatch_qty'] = 'IF(`dispatch_qty` - '.$row->qty.' >= 0, `dispatch_qty` - '.$row->qty.', 0)';
                    if($row->from_entry_type == 14  || $row->from_entry_type == 241){
                        $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 3 END)";
                    }else{
                        $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 0 END)";
                    };
                    $this->setValue($setData);
                endif;

                $this->trash($this->transChild,['id'=>$row->id]);
            endforeach;

            if(!empty($dataRow->ref_id) && $dataRow->from_entry_type != 241):
                $oldRefIds = explode(",",$dataRow->ref_id);
                foreach($oldRefIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = ($dataRow->from_entry_type == 14)?'so_master':'trans_main';
                    $setData['where']['id'] = $main_id;
                    if($dataRow->from_entry_type == 14){
                        $setData['update']['trans_status'] = "(SELECT IF(COUNT(id) = SUM(IF(trans_status NOT IN (0, 3), 1, 0)),1,3) AS trans_status FROM so_trans WHERE trans_main_id = '".$main_id."' AND is_delete = 0)";
                    }else{
                        $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM trans_child WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
                    }
                    $this->setValue($setData);
                endforeach;
            elseif($dataRow->from_entry_type == 241):
                $fpData = $this->finalPacking->getPackingItemDetail(['packing_id'=>$dataRow->ref_id]);
                foreach($fpData AS $row){
                    $setData = array();
                    $setData['tableName'] = 'so_master';
                    $setData['where']['id'] = $row->so_id;
                    $setData['update']['trans_status'] = "(SELECT IF(COUNT(id) = SUM(IF(trans_status NOT IN (0, 3), 1, 0)),1,3) AS trans_status FROM so_trans WHERE trans_main_id = '".$row->so_id."' AND is_delete = 0)";
                    $this->setValue($setData);
                }
                $this->edit("final_packing_master",['id'=>$dataRow->ref_id],['status'=>1]);
                $this->edit("final_packing_trans",['packing_id'=>$dataRow->ref_id],['inv_trans_id'=>0]);
            endif;

            $this->transMainModel->deleteLedgerTrans($id);

            $this->trash($this->transExpense,['trans_main_id'=>$id,'entry_type'=>$this->data['entryData']->id]);
            
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"SI TERMS"]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"SI MASTER DETAILS"]);

            $this->remove($this->stockTrans,['main_ref_id'=>$dataRow->id,'trans_type'=>'INV']);

            $result = $this->trash($this->transMain,['id'=>$id],'Sales Invoice');

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

		if(!empty($data['id'])){
            $queryData['where']['trans_main.id'] = $data['id'];
        }
        if(!empty($this->data['entryData']->id)){
			$queryData['where']['trans_child.entry_type'] = $this->data['entryData']->id;
		}
        if(!empty($data['inv_id'])){
            $queryData['where']['trans_child.trans_main_id'] = $data['inv_id'];
        }
        $queryData['where']['(trans_child.qty - trans_child.dispatch_qty) >'] = 0;
		
        return $this->rows($queryData);
    }

	public function getInvWithoutDisc($inv_id){
		$data['tableName'] = $this->transChild;
		$data['where']['trans_main_id'] = $inv_id;
		$data['where']['disc_per > '] = 0;
		return $this->rows($data);
	}

    public function getBatchWiseInvoice($param=[]){
        $queryData['tableName'] = "prc_master";

        $queryData['select'] = "IFNULL(stock_trans.trans_date,'') as inv_date, IFNULL(stock_trans.ref_no,'') as inv_number, SUM(IFNULL(stock_trans.qty,0)) as inv_qty";

        $queryData['leftJoin']['stock_trans'] = "stock_trans.batch_no = prc_master.prc_number AND stock_trans.item_id = prc_master.item_id";

		$queryData['where']['prc_master.id'] = $param['prc_id'];
		$queryData['where']['stock_trans.trans_type'] = "INV";
		$queryData['group_by'][] = "stock_trans.ref_no";
        
        $result = $this->rows($queryData);
		return $result;
    }
}
?>