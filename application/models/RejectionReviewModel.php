<?php
class RejectionReviewModel extends MasterModel
{
    private $rejection_log = "rejection_log";
   

    public function getDTRows($data)
    {
        /** If Rejection From  Production*/
        if($data['source'] == 'MFG'){
            $data['tableName'] = "prc_log";
            $data['select'] = "prc_log.*,prc_master.prc_date,prc_master.prc_number,process_master.process_name,item_master.item_name,item_master.item_code,employee_master.emp_name";
            $data['select'] .=', IF(prc_log.process_by = 1, machine.item_code,
                                        IF(prc_log.process_by = 2,department_master.name,
                                            IF(prc_log.process_by = 3,party_master.party_name,""))) as processor_name,IFNULL(rejection_log.review_qty,0) as review_qty,(prc_log.rej_found-IFNULL(rejection_log.review_qty,0)) as pending_qty';
            $data['leftJoin']['prc_master'] = "prc_master.id = prc_log.prc_id";
            $data['leftJoin']['process_master'] = "process_master.id = prc_log.process_id";
            $data['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
            $data['leftJoin']['item_master machine'] = "machine.id = prc_log.processor_id";
            $data['leftJoin']['department_master'] = "department_master.id = prc_log.processor_id";
            $data['leftJoin']['party_master'] = "party_master.id = prc_log.processor_id";
            $data['leftJoin']['employee_master'] = "employee_master.id = prc_log.operator_id";
            $data['leftJoin']['(SELECT SUM(qty) as review_qty,log_id,prc_id FROM rejection_log WHERE is_delete = 0 AND rejection_log.source="MFG" GROUP BY rejection_log.log_id,prc_id) rejection_log'] = "rejection_log.log_id = prc_log.id AND prc_log.prc_id = rejection_log.prc_id";
            
            $data['where']['prc_log.rej_found >'] = 0;
            $data['having'][] = "pending_qty > 0";
        

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "prc_master.prc_number";
            $data['searchCol'][] = "CONCAT(item_master.item_code,' ',item_master.item_name)";
            $data['searchCol'][] = "DATE_FORMAT(prc_log.trans_date,'%d-%m-%Y')";
            $data['searchCol'][] = "process_master.process_name";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "employee_master.emp_name";
            $data['searchCol'][] = "prc_log.rej_found";
            $data['searchCol'][] = "rejection_log.review_qty";
            $data['searchCol'][] = "(prc_log.rej_found - rejection_log.review_qty)";
        }
        elseif($data['source'] == 'FIR'){
            $data['tableName'] = "production_inspection";
            $data['select'] = "production_inspection.id,production_inspection.insp_date as trans_date,production_inspection.rej_found,prc_master.prc_date,prc_master.prc_number,item_master.item_name,item_master.item_code,employee_master.emp_name,IFNULL(rejection_log.review_qty,0) as review_qty,(production_inspection.rej_found-IFNULL(rejection_log.review_qty,0)) as pending_qty";
            $data['leftJoin']['prc_master'] = "prc_master.id = production_inspection.prc_id";
            $data['leftJoin']['item_master'] = "item_master.id = production_inspection.item_id";
            $data['leftJoin']['employee_master'] = "employee_master.id = production_inspection.created_by";
            $data['leftJoin']['(SELECT SUM(qty) as review_qty,log_id,prc_id FROM rejection_log WHERE is_delete = 0 AND rejection_log.source="FIR" GROUP BY rejection_log.log_id,prc_id) rejection_log'] = "rejection_log.log_id = production_inspection.id";
            
            $data['where']['production_inspection.rej_found >'] = 0;
            $data['where']['production_inspection.report_type'] = 2;
            $data['having'][] = "pending_qty > 0";
        
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "prc_master.prc_number";
            $data['searchCol'][] = "CONCAT(item_master.item_code,' ',item_master.item_name)";
            $data['searchCol'][] = "DATE_FORMAT(prc_log.trans_date,'%d-%m-%Y')";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "employee_master.emp_name";
            $data['searchCol'][] = "prc_log.rej_found";
            $data['searchCol'][] = "rejection_log.review_qty";
            $data['searchCol'][] = "(prc_log.rej_found - rejection_log.review_qty)";
        }
        elseif($data['source'] == 'GRN'){
            $data['tableName'] = "rej_found";
            $data['select'] = "rej_found.*,grn_master.trans_number,grn_master.trans_date,party_master.party_name,item_master.item_code,item_master.item_name,IFNULL(rejection_log.review_qty,0) as review_qty,(rej_found.qty-IFNULL(rejection_log.review_qty,0)) as pending_qty,item_master.item_type";                 
            $data['leftJoin']['grn_trans'] = "grn_trans.id = rej_found.ref_id";
            $data['leftJoin']['grn_master'] = "grn_master.id = grn_trans.grn_id";
            $data['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
            $data['leftJoin']['item_master'] = "item_master.id = grn_trans.item_id";

            $data['leftJoin']['(SELECT SUM(qty) as review_qty,log_id FROM rejection_log WHERE is_delete = 0 AND rejection_log.source="GRN" GROUP BY rejection_log.log_id) rejection_log'] = "rejection_log.log_id = rej_found.id";

            $data['where']['rej_found.trans_type'] = 2;
            $data['where']['item_master.item_type !='] = 6;
            $data['where']['rej_found.qty >'] = 0;
            $data['having'][] = "pending_qty > 0";

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "grn_master.trans_number";
            $data['searchCol'][] = "DATE_FORMAT(grn_master.trans_date,'%d-%m-%Y')";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
            $data['searchCol'][] = "rej_found.qty";
            $data['searchCol'][] = "rejection_log.review_qty";
            $data['searchCol'][] = "(rej_found.qty - rejection_log.review_qty)";
        }
        elseif($data['source'] == 'Manual'){
            $data['tableName'] = "rej_found";
            $data['select'] = "rej_found.*,item_master.item_code,item_master.item_name,IFNULL(rejection_log.review_qty,0) as review_qty,(rej_found.qty-IFNULL(rejection_log.review_qty,0)) as pending_qty,item_master.item_type,location_master.location"; 
            $data['leftJoin']['item_master'] = "item_master.id = rej_found.item_id";
            $data['leftJoin']['location_master'] = "location_master.id  = rej_found.location_id";
            $data['leftJoin']['(SELECT SUM(qty) as review_qty,log_id FROM rejection_log WHERE is_delete = 0 AND rejection_log.source="Manual" GROUP BY rejection_log.log_id) rejection_log'] = "rejection_log.log_id = rej_found.id";
            $data['where']['rej_found.trans_type'] = 1;
            $data['where']['item_master.item_type !='] = 6;
            $data['having'][] = "pending_qty > 0";

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "DATE_FORMAT(rej_found.trans_date,'%d-%m-%Y')";
            $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
            $data['searchCol'][] = "location_master.location";
            $data['searchCol'][] = "rej_found.batch_no";
            $data['searchCol'][] = "rej_found.qty";
            $data['searchCol'][] = "rejection_log.review_qty";
            $data['searchCol'][] = "(rej_found.qty - rejection_log.review_qty)";
        }

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        // $this->printQuery();
        return $result;
    }

    public function getReviewDTRows($data){
        if($data['source'] == 'GRN'){
            $data['tableName'] = "rejection_log";
            $data['select'] = "rejection_log.*,grn_master.trans_number,item_master.item_name,item_master.item_code,IF(rejection_log.decision_type = 1,'Rejection',IF(decision_type = 5,'OK','')) as decision";
            $data['leftJoin']['grn_trans'] = "grn_trans.id = rejection_log.log_id";
            $data['leftJoin']['grn_master'] = "grn_master.id = grn_trans.grn_id";
            $data['leftJoin']['item_master'] = "item_master.id = grn_trans.item_id";
            
            $data['where']['rejection_log.source'] = $data['source'];

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "rejection_log.source";
            $data['searchCol'][] = "grn_master.trans_number";
            $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
            $data['searchCol'][] = "DATE_FORMAT(rejection_log.created_at,'%d-%m-%Y')";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "rejection_log.qty";
        }
        elseif($data['source'] == 'Manual'){
            $data['tableName'] = "rejection_log";
            $data['select'] = "rejection_log.*,item_master.item_name,item_master.item_code,IF(rejection_log.decision_type = 1,'Rejection',IF(decision_type = 5,'OK','')) as decision,location_master.location,rej_found.batch_no";
            $data['leftJoin']['rej_found'] = "rej_found.id = rejection_log.log_id";
            $data['leftJoin']['item_master'] = "item_master.id = rej_found.item_id";
            $data['leftJoin']['location_master'] = "location_master.id = rej_found.location_id";
            
            $data['where']['rejection_log.source'] = $data['source'];

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "rejection_log.source";
            $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
            $data['searchCol'][] = "location_master.location";
            $data['searchCol'][] = "rej_found.batch_no";
            $data['searchCol'][] = "DATE_FORMAT(rejection_log.created_at,'%d-%m-%Y')";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "rejection_log.qty";
        }
        else{
            $data['tableName'] = "rejection_log";
            $data['select'] = "rejection_log.*,prc_master.prc_date,prc_master.prc_number,process_master.process_name,item_master.item_name,item_master.item_code,IF(rejection_log.decision_type = 1,'Rejection',IF(decision_type = 2,'Rework',IF(decision_type = 5,'OK',''))) as decision,rr_process.process_name AS rr_process_name,IF(rejection_log.rr_by>0,rrBY.party_name,'Inhouse') AS rr_by_name,rej_machine.item_code as rej_mc_code,rej_emp.emp_name as rej_emp_name";

            $data['leftJoin']['prc_log'] = "prc_log.id = rejection_log.log_id";
            $data['leftJoin']['prc_master'] = "prc_master.id = rejection_log.prc_id";
            $data['leftJoin']['process_master'] = "process_master.id = prc_log.process_id";
            $data['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
            $data['leftJoin']['process_master rr_process'] = "rr_process.id = rejection_log.rr_stage";
            $data['leftJoin']['party_master rrBY'] = "rrBY.id = rejection_log.rr_by";
            $data['leftJoin']['item_master rej_machine'] = "rej_machine.id = rejection_log.machine_id";
            $data['leftJoin']['employee_master rej_emp'] = "rej_emp.id = rejection_log.operator_id";
            
            $data['where']['rejection_log.source'] = $data['source'];
          
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "prc_master.prc_number";
            $data['searchCol'][] = "CONCAT(item_master.item_code,' ',item_master.item_name)";
            $data['searchCol'][] = "DATE_FORMAT(rejection_log.created_at,'%d-%m-%Y')";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "process_master.process_name";
            $data['searchCol'][] = "rr_process.process_name";
            $data['searchCol'][] = "rejection_log.qty";
            $data['searchCol'][] = "IF(rejection_log.rr_by>0,rrBY.party_name,'Inhouse')";
            $data['searchCol'][] = "rej_machine.item_code";
            $data['searchCol'][] = "rej_emp.emp_name";
            $data['searchCol'][] = "rejection_log.rr_comment";
        }

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }

    public function getReviewData($param = []){
        $queryData['tableName'] = "rejection_log";
        $queryData['select'] = "rejection_log.*,prc_log.process_id,prc_master.prc_number,process_master.process_name,item_master.item_name,item_master.item_code,employee_master.emp_name,prc_log.process_by,rr_process.process_name AS rr_process_name,IF(rejection_log.decision_type = 1,'Rejection',IF(decision_type = 2,'Rework',IF(decision_type = 5,'OK',''))) as decision";
        $queryData['select'] .=',prc_log.rej_found,prc_log.qty as ok_qty,IF(prc_log.process_by = 1, machine.item_code, IF(prc_log.process_by = 2,department_master.name, IF(prc_log.process_by = 3,party_master.party_name,""))) as processor_name,creator.emp_name as created_name,rejection_comment.remark as reason,rej_found.ref_id,grn_trans.grn_id,rej_emp.emp_name as rej_emp_name,rrBY.party_name AS rr_by_name,rej_machine.item_code as rej_mc_code';
		$queryData['leftJoin']['prc_log'] = "prc_log.id = rejection_log.log_id";
        $queryData['leftJoin']['prc_master'] = "prc_master.id = rejection_log.prc_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = prc_log.process_id";
		$queryData['leftJoin']['process_master rr_process'] = "rr_process.id = rejection_log.rr_stage";
		$queryData['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
		$queryData['leftJoin']['item_master machine'] = "machine.id = prc_log.processor_id";
		$queryData['leftJoin']['department_master'] = "department_master.id = prc_log.processor_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = prc_log.processor_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = prc_log.operator_id";
		$queryData['leftJoin']['employee_master creator'] = "creator.id = rejection_log.created_by";
		$queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = rejection_log.rr_reason";
		$queryData['leftJoin']['rej_found'] = "rej_found.id = rejection_log.log_id";
		$queryData['leftJoin']['grn_trans'] = "grn_trans.id = rej_found.ref_id";
		$queryData['leftJoin']['item_master rej_machine'] = "rej_machine.id = rejection_log.machine_id";
		$queryData['leftJoin']['employee_master rej_emp'] = "rej_emp.id = rejection_log.operator_id";
		$queryData['leftJoin']['party_master rrBY'] = "rrBY.id = rejection_log.rr_by";
        
        if(!empty($param['id'])){ $queryData['where']['rejection_log.id'] = $param['id']; }
        if(!empty($param['log_id'])){ 
            $queryData['where']['rejection_log.log_id'] = $param['log_id'];
         }
        if(!empty($param['source'])){ $queryData['where']['rejection_log.source'] = $param['source']; }
        if(!empty($param['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }

    public function saveReview($data){
        try {
			$this->db->trans_begin();
                
            $ref_id = (!empty($data['ref_id']) ? $data['ref_id'] : 0);
            $grn_id = (!empty($data['grn_id']) ? $data['grn_id'] : 0);
            unset($data['ref_id'],$data['grn_id']);

			$result = $this->store('rejection_log', $data, 'Decision');
            if($data['source'] == 'MFG'){
                if($data['decision_type'] == 5 ){
                    $setData = array();
                    $setData['tableName'] = 'prc_log';
                    $setData['where']['id'] = $data['log_id'];
                    $setData['set']['qty'] = 'qty, + ' . $data['qty'];
                    $this->setValue($setData);
                }
    
                if($data['decision_type'] == 1){
                    $setData = array();
                    $setData['tableName'] = 'prc_log';
                    $setData['where']['id'] = $data['log_id'];
                    $setData['set']['rej_qty'] = 'rej_qty, + ' . $data['qty'];
                    $this->setValue($setData);
                }
    
                if($data['decision_type'] == 2){
                    $setData = array();
                    $setData['tableName'] = 'prc_log';
                    $setData['where']['id'] = $data['log_id'];
                    $setData['set']['rw_qty'] = 'rw_qty, + ' . $data['qty'];
                    $this->setValue($setData);
                    
                    $logData = $this->sop->getProcessLogList(['id'=>$data['log_id'],'single_row'=>1]);
                    // print_r($this->db->last_query());
                    //REWORK MOVEMENT
                    $movementData = [
						'id'=>'',
                        'move_type'=>2,
						'prc_id' =>$data['prc_id'],
						'ref_id' =>$result['insert_id'],
						'process_id' => $logData->process_id,
						'next_process_id' =>  $data['rw_process'],
						'trans_date' => date("Y-m-d"),
						'qty' =>  $data['qty'],
					];
					$this->sop->savePRCMovement($movementData);
                }
                $this->sop->changePrcStatus(['prc_id'=>$data['prc_id']]);
            }
            elseif($data['source'] == 'FIR'){
                if($data['decision_type'] == 5 ){
                    $setData = array();
                    $setData['tableName'] = 'production_inspection';
                    $setData['where']['id'] = $data['log_id'];
                    $setData['set']['ok_qty'] = 'ok_qty, + ' . $data['qty'];
                    $this->setValue($setData);
                    $entryData = $this->transMainModel->getEntryType(['controller'=>'finalInspection']);
                    $inspectionData = $this->finalInspection->getFinalInspectData(['id'=>$data['log_id']]);
                    $stockPlusData = [
                        'id' => "",
                        'entry_type' => $entryData->id,
                        'ref_date' => date("Y-m-d"),
                        'ref_no' =>$inspectionData->trans_number,
                        'main_ref_id' => $data['prc_id'],
                        'child_ref_id' => $inspectionData->id,
                        'location_id' => $this->PACKING_STORE->id,
                        'batch_no' =>$inspectionData->prc_number,
                        'item_id' => $inspectionData->item_id,
                        'p_or_m' => 1,
                        'qty' => $data['qty'],
                        'remark' => $result['id'],
                    ];
                    $this->store('stock_transaction',$stockPlusData);
                }
    
                if($data['decision_type'] == 1){
                    $setData = array();
                    $setData['tableName'] = 'production_inspection';
                    $setData['where']['id'] = $data['log_id'];
                    $setData['set']['rej_qty'] = 'rej_qty, + ' . $data['qty'];
                    $this->setValue($setData);
                }
    
                if($data['decision_type'] == 2){
                    $setData = array();
                    $setData['tableName'] = 'production_inspection';
                    $setData['where']['id'] = $data['log_id'];
                    $setData['set']['rw_qty'] = 'rw_qty, + ' . $data['qty'];
                    $this->setValue($setData);
                }
            }
            elseif($data['source'] == 'GRN'){
                if($data['decision_type'] == 5){                    
                    $mirData = $this->gateInward->getGateInward($grn_id);
                    $mirItem = $this->gateInward->getInwardItem(['id'=>$ref_id]);

                    $stockData = [
                        'id' => "",
                        'trans_type' => 'GRN',
                        'trans_date' => date("Y-m-d"),
                        'item_id' => $mirItem->item_id,
                        'location_id' => $mirItem->location_id,
                        'batch_no' => $data['batch_no'],
                        'p_or_m' => 1,
                        'qty' => $data['qty'],
                        'ref_no' => $mirData->trans_number,
                        'main_ref_id' => $mirData->id,
                        'child_ref_id' => $mirItem->id,
                        'remark' => $result['id'],
                    ];
                    $this->store('stock_trans',$stockData);

                    $setData = array();
                    $setData['tableName'] = 'grn_trans';
                    $setData['where']['id'] = $ref_id;
                    $setData['set']['reject_qty'] = 'reject_qty, - ' . $data['qty'];
                    $setData['set']['ok_qty'] = 'ok_qty, + ' . $data['qty'];
                    $this->setValue($setData);
                }
            }
            elseif($data['source'] == 'Manual'){
                if($data['decision_type'] == 5){                    
                    $rejData = $this->getRejectionData(['id'=>$data['log_id'],'source'=>$data['source']]);

                    $stockData = [
                        'id' => "",
                        'trans_type' => 'MRJ',
                        'trans_date' => date("Y-m-d"),
                        'item_id' => $rejData->item_id,
                        'location_id' => $rejData->location_id,
                        'batch_no' => $data['batch_no'],
                        'p_or_m' => 1,
                        'qty' => $data['qty'],
                        'main_ref_id' => $data['log_id'],
                        'remark' => $result['id'],
                    ];
                    $this->store('stock_trans',$stockData);
                }
            }
            
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Record Updated Successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function deleteReview($data){
        try{
            $this->db->trans_begin();

            $reviewData = $this->getReviewData(['id'=>$data['id'],'single_row'=>1]);
            $result = $this->trash('rejection_log',['id'=>$data['id']]);
            if($reviewData->source == 'MFG'){
                if($reviewData->decision_type == 5){
                    $prcProcessData = $this->sop->getPRCProcessList(['process_id'=>$reviewData->process_id,'prc_id'=>$reviewData->prc_id,'log_data'=>1,'movement_data'=>1,'single_row'=>1]); 
                    $ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
                    $movement_qty =!empty($prcProcessData->movement_qty)?$prcProcessData->movement_qty:0;
                    $pending_movement = $ok_qty - $movement_qty;
                    if($reviewData->qty > $pending_movement){
                        return ['status'=>0,'message'=>'You can not delete this Log. Qty is sent to next process'];
                    }
                    $setData = array();
                    $setData['tableName'] = 'prc_log';
                    $setData['where']['id'] = $reviewData->log_id;
                    $setData['set']['qty'] = 'qty, - ' . $reviewData->qty;
                    $this->setValue($setData);
                }
    
                if($reviewData->decision_type == 1){
                    $setData = array();
                    $setData['tableName'] = 'prc_log';
                    $setData['where']['id'] =$reviewData->log_id;
                    $setData['set']['rej_qty'] = 'rej_qty, - ' . $reviewData->qty;
                    $this->setValue($setData);
                    
                }
    
                if($reviewData->decision_type == 2){
                    $setData = array();
                    $setData['tableName'] = 'prc_log';
                    $setData['where']['id'] =$reviewData->log_id;
                    $setData['set']['rw_qty'] = 'rw_qty, - ' . $reviewData->qty;
                    $this->setValue($setData);
                    $this->trash('prc_movement',['ref_id'=>$data['id'],'move_type'=>2]);
                }

                $this->sop->changePrcStatus(['prc_id'=>$reviewData->prc_id]);
            }
            elseif($reviewData->source == 'FIR'){
                if($reviewData->decision_type == 5){
                    $stock = $this->itemStock->getItemStockBatchWise(['location_id'=>$this->PACKING_STORE->id,'batch_no'=>$reviewData->prc_number,'item_id'=> $reviewData->item_id,'single_row'=>1]);
                    if($reviewData->qty > $stock->qty){
                        return ['status'=>0,'message'=>'You can not delete this Log. Qty'];
                    }
                    $setData = array();
                    $setData['tableName'] = 'production_inspection';
                    $setData['where']['id'] = $reviewData->log_id;
                    $setData['set']['ok_qty'] = 'ok_qty, - ' . $reviewData->qty;
                    $this->setValue($setData);
                    $entryData = $this->transMainModel->getEntryType(['controller'=>'finalInspection']);
                    $this->remove('stock_transaction',['entry_type'=>$entryData->id,'main_ref_id'=>$reviewData->prc_id,'child_ref_id'=>$reviewData->log_id,'remark'=>$data['id']]);
                }
    
                if($reviewData->decision_type == 1){
                    $setData = array();
                    $setData['tableName'] = 'production_inspection';
                    $setData['where']['id'] =$reviewData->log_id;
                    $setData['set']['rej_qty'] = 'rej_qty, - ' . $reviewData->qty;
                    $this->setValue($setData);
                }
    
                if($reviewData->decision_type == 2){
                    $setData = array();
                    $setData['tableName'] = 'production_inspection';
                    $setData['where']['id'] =$reviewData->log_id;
                    $setData['set']['rw_qty'] = 'rw_qty, - ' . $reviewData->qty;
                    $this->setValue($setData);

                    
                }
            }
            elseif($reviewData->source == 'GRN'){
                if($reviewData->decision_type == 5){
                    $setData = array();
                    $setData['tableName'] = 'grn_trans';
                    $setData['where']['id'] = $reviewData->ref_id;
                    $setData['set']['reject_qty'] = 'reject_qty, + ' . $reviewData->qty;
                    $setData['set']['ok_qty'] = 'ok_qty, - ' . $reviewData->qty;
                    $this->setValue($setData);

                    $this->trash('stock_trans',['trans_type'=>$reviewData->source,'main_ref_id'=>$reviewData->grn_id,'child_ref_id'=>$reviewData->ref_id,'remark'=>$data['id']]);
                }
            }
            elseif($reviewData->source == 'Manual'){
                if($reviewData->decision_type == 5){
                    $this->trash('stock_trans',['trans_type'=>'MRJ','main_ref_id'=>$reviewData->log_id,'remark'=>$data['id']]);
                }
            }

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getRejectionData($data){
        $queryData['tableName'] = 'rej_found';
        $queryData['select'] = 'rej_found.*,(rej_found.qty-IFNULL(rejection_log.review_qty,0)) as pending_qty,grn_trans.grn_id';
        $queryData['leftJoin']['grn_trans'] = "grn_trans.id = rej_found.ref_id";
        $queryData['leftJoin']['(SELECT SUM(qty) as review_qty,log_id FROM rejection_log WHERE is_delete = 0 AND rejection_log.source="'.$data['source'].'" GROUP BY rejection_log.log_id) rejection_log'] = "rejection_log.log_id = rej_found.id";
        $queryData['where']['rej_found.id'] = $data['id'];
        return $this->row($queryData);
    }
}
