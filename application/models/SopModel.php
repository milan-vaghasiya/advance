<?php
class SopModel extends MasterModel{
    private $last_prc_no = 0;
	/***
		NOTES : To Get Year Prefix Call getYearPrefix($format,$date="") from Format Helper
		Formats : NUMERIC_YEAR, ALPHA_YEAR, SHORT_YEAR, LONG_YEAR
		Date : Optional
	// ***/	
	public function getNextPRCNo($prc_type=1,$mfg_type=""){
		$queryData = array(); 
		$queryData['tableName'] = 'prc_master';
        $queryData['select'] = "MAX(prc_no ) as prc_no ";
		/*if(!empty($mfg_type)):
			$queryData['where']['mfg_type'] = $mfg_type;
		endif;*/
		if(!empty($prc_type)):
			$queryData['where']['prc_type'] = $prc_type;
		endif;		
		$queryData['where']['prc_master.prc_date >='] = $this->startYearDate;
		$queryData['where']['prc_master.prc_date <='] = $this->endYearDate;

		$prc_no = $this->specificRow($queryData)->prc_no;
		$prc_no = (empty($this->last_prc_no))?($prc_no + 1):$prc_no;
		return $prc_no;
    }
	
	public function savePRC($param){ 
		try {
			$this->db->trans_begin();
            $opBalance = array();
			$prc_type = !empty($param['masterData']['prc_type'])?$param['masterData']['prc_type']:1;
			$itemData = $this->item->getItem(['id'=>$param['masterData']['item_id']]);
			if(empty($param['masterData']['id'])){
				
				$prc_no = $this->sop->getNextPRCNo($prc_type);
				$prc_prefix = 'PRC/'.getYearPrefix('SHORT_YEAR').'/';
				$param['masterData']['prc_number'] = $prc_prefix.$prc_no;
			}
			$param['masterData']['cutting_flow'] = $itemData->cutting_flow;
            $result = $this->store('prc_master', $param['masterData'], 'PRC');
			$prc_id = $result['id'];
			if(!empty($result['id']))
			{
				$param['prcDetail']['prc_id'] = $result['id'];
				$param['prcDetail']['id'] = (!empty($param['prcDetail']['id'])?$param['prcDetail']['id']:'');
				$prcDetail = $this->store('prc_detail', $param['prcDetail'], 'PRC Detail');
			}
			// Jo Cutting Process PRC ma na levani hoy to auto PRC BOM Entry
			
			if($itemData->cutting_flow == 2){
				if(!empty($param['masterData']['id'])){
					$this->trash("prc_bom",['prc_id'=>$param['masterData']['id']]);
				}
				$bomData = [
					'id'=>'',
					'prc_id'=>$prc_id,
					'item_id'=>$param['masterData']['item_id'],
					'bom_group'=>$param['masterData']['item_id'],
					'ppc_qty'=>1,
					'multi_heat'=>'Yes',
				];
				$this->store('prc_bom', $bomData);
			}
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}
	
	public function getPRCList($param=[]){
        $queryData = array();          
		$queryData['tableName'] = "prc_master";
		
		$queryData['select'] = "prc_master.id, prc_master.prc_number,prc_master.item_id, im.mfg_type, DATE_FORMAT(prc_master.prc_date,'%d-%m-%Y') as prc_date, DATE_FORMAT(prc_master.target_date,'%d-%m-%Y') as target_date, prc_master.status, prc_master.prc_qty,IFNULL(im.item_code,'') as item_code";
		$queryData['select'] .= ", IFNULL(im.item_name,'') as item_name, im.uom, IFNULL(pm.party_name,'') as party_name, IFNULL(pd.remark,'') as job_instruction,im.production_type";
        
        $queryData['leftJoin']['item_master im'] = "im.id = prc_master.item_id";
        $queryData['leftJoin']['party_master pm'] = "pm.id = prc_master.party_id";
        $queryData['leftJoin']['prc_detail pd'] = "pd.prc_id = prc_master.id";

		if(!empty($param['status'])){ $queryData['where_in']['prc_master.status'] = $param['status']; }
		if(!empty($param['prc_type'])){ $queryData['where']['prc_master.prc_type'] = $param['prc_type']; }
		else{ $queryData['where']['prc_master.prc_type'] = 1; }

		if(!empty($param['mfg_type'])){ $queryData['where']['im.mfg_type'] = $param['mfg_type']; }
		
		if(!empty($param['mfg_route'])){ $queryData['where']['prc_master.mfg_route'] = $param['mfg_route']; }
		
		if(!empty($param['item_id'])){ $queryData['where']['prc_master.item_id'] = $param['item_id']; }
		
		if(!empty($param['so_trans_id'])){ $queryData['where']['prc_master.so_trans_id'] = $param['so_trans_id']; }
		
		if(!empty($param['party_id'])){ $queryData['where']['prc_master.party_id'] = $param['party_id']; }
		
		if(!empty($param['target_date'])){ $queryData['where']['prc_master.target_date'] = $param['target_date']; }
		
		if(!empty($param['overdue'])){ $queryData['where']['prc_master.target_date < '] = $param['overdue']; }
		
		if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }
		
        if(!empty($param['skey'])){
			$queryData['like']['prc_master.prc_number'] = str_replace(" ", "%", $param['skey']);
			$queryData['like']['im.mfg_type'] = str_replace(" ", "%", $param['skey']);
			$queryData['like']['prc_master.prc_date'] = str_replace(" ", "%", $param['skey']);
			$queryData['like']['im.item_name'] = str_replace(" ", "%", $param['skey']);
			$queryData['like']['pm.party_name'] = str_replace(" ", "%", $param['skey']);
        }

        if(!empty($param['limit'])){ $queryData['limit'] = $param['limit']; }
        
		if(isset($param['start'])){ $queryData['start'] = $param['start']; }
		
		if(!empty($param['length'])){ $queryData['length'] = $param['length']; }
		
		$queryData['order_by']['prc_master.prc_date'] = 'DESC';
		$queryData['order_by']['prc_master.id'] = 'DESC';
		$queryData['group_by'][] = "prc_master.id";
        $result = $this->rows($queryData);
        return $result;  
    }
    
	/** GET PRC DETAIL */
	public function getPRCDetail($param=[]){
        $queryData = array();$result = new stdClass();
		$queryData['tableName'] = "prc_detail";
		
		$queryData['select'] = "prc_detail.*, prc_master.prc_number,prc_master.prc_qty, prc_master.item_id, DATE_FORMAT(prc_master.prc_date,'%d-%m-%Y') as prc_date, DATE_FORMAT(prc_master.target_date,'%d-%m-%Y') as target_date, prc_master.status, prc_master.prc_qty,prc_master.prc_type,prc_master.batch_no,employee_master.emp_name";
		$queryData['select'] .= ", IFNULL(im.item_name,'') as item_name,IFNULL(im.item_code,'') as item_code, IFNULL(im.uom,'') as uom,im.mfg_type, IFNULL(pm.party_name,'') as party_name,im.cut_weight,im.forge_weight,im.wt_pcs,prc_master.rev_no,ecn_master.drw_no,im.production_type,prc_master.cutting_flow";
       	$queryData['select'] .= ", IFNULL(so_master.trans_number,'') as so_number,so_master.doc_no";
        
        $queryData['leftJoin']['prc_master'] = "prc_master.id = prc_detail.prc_id";
		// $queryData['leftJoin']['(SELECT MAX(qty_kg) as qty_kg, prc_id FROM prc_log WHERE prc_id = '.$param['prc_id'].' AND is_delete = 0) as prcLog'] = "prcLog.prc_id = prc_detail.prc_id";
        $queryData['leftJoin']['item_master im'] = "im.id = prc_master.item_id";
        $queryData['leftJoin']['ecn_master'] = "im.id = ecn_master.item_id AND ecn_master.rev_no = prc_master.rev_no AND ecn_master.is_delete =0";
        $queryData['leftJoin']['party_master pm'] = "pm.id = prc_master.party_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = prc_master.created_by";
        $queryData['leftJoin']['so_trans'] = "so_trans.id = prc_master.so_trans_id";
        $queryData['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id";
		
		if(!empty($param['prc_id'])){ $queryData['where']['prc_detail.prc_id'] = $param['prc_id']; }
		
		if(!empty($param['id'])){ $queryData['where']['prc_master.id'] = $param['id']; }
		
		if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }
		
        $result = $this->row($queryData);
        if(!empty($result->prc_id))
        {
            $result->prcProcessData = [];
			$move_type = !empty($param['move_type'])?$param['move_type']:1;
            if($result->status > 1){ $result->prcProcessData = $this->getPRCProcessList(['prc_id'=>$result->prc_id,'process_id'=>$result->process_ids,'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'move_type'=>$move_type]); }
            elseif(!empty($result->process_ids)){ $result->prcProcessData = $this->getProcessFromPRC(['process_ids'=>$result->process_ids,'item_id'=>$result->item_id]); }
        }
        return $result;  
    }
    
	/**
	 * GET SINGLE & MULTI PRC PROCESS DATA 
	 * PRODUCTION LOG QTY, MOVEMENT QTY, ACCEPTED QTY
	 * PENDING PRODUCTION QTY = INQTY -(OK QTY+REJ+RW+PENDING REVIEW)
	 * PENDING ACCEPT = PREVIOUS PROCESS'S MOVEMENT QTY - CURRENT ACCEPT)
	 * PENDING MOVEMENT = TOTAL OK QTY -(TOTAL MOVEMENT - NEXT PROCESS'S SHORT QTY)
	*/
	public function getPRCProcessList($param=[]){
        $queryData = array();          
		$queryData['tableName'] = "product_process";
		$queryData['select'] = "product_process.*,prc_master.id as prc_id";
		$queryData['select'] .= ", IFNULL(cp.process_name,'') as current_process,prc_master.prc_number,prc_master.prc_date,prc_master.item_id,prc_master.rev_no,item_master.item_name,item_master.item_code,item_master.production_type, prc_master.prc_qty,prc_detail.first_process,prc_detail.process_ids,cp.die_required,item_master.is_packing";
        $queryData['select'] .= ',product_process.finish_wt,product_process.conv_ratio,product_process.output_qty';
        $queryData['leftJoin']['prc_master'] = "prc_master.item_id = product_process.item_id AND prc_master.is_delete = 0";
        $queryData['leftJoin']['process_master cp'] = "cp.id = product_process.process_id";
        $queryData['leftJoin']['prc_detail'] = "prc_detail.prc_id = prc_master.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
		
		$whereProcessFrom = ""; if(isset($param['process_from'])){ $whereProcessFrom = " AND process_from = ".$param['process_from']; }

		/** GET INWARD QTY (in_qty,pending accept)*/
		if(!empty($param['pending_accepted'])){
			$customMoveWh="";$customWh="";$prcIdWh = '';
			if(!empty($param['move_type'])){ $customMoveWh = " AND move_type = ".$param['move_type']; }
			if(!empty($param['move_type'])){ $customWh = " AND trans_type = ".$param['move_type']; }
			if(!empty($param['prc_id'])){$prcIdWh = " AND prc_id = ".$param['prc_id'];}
			$queryData['select'] .= ",((IFNULL(prevMovement.move_qty,0)-IFNULL(prc_accept_log.short_qty,0)) - IFNULL(prc_accept_log.accepted_qty,0)) as pending_accept,IFNULL(prc_accept_log.accepted_qty,0) as in_qty";

			$queryData['leftJoin']['(SELECT SUM(prc_accept_log.accepted_qty) as accepted_qty,
										SUM(prc_accept_log.short_qty) as short_qty,
										accepted_process_id,prc_id 
										FROM prc_accept_log 
										WHERE prc_accept_log.is_delete=0 
										'.$customWh.$whereProcessFrom.$prcIdWh.'
										GROUP BY accepted_process_id
									) prc_accept_log']="prc_accept_log.accepted_process_id = product_process.process_id AND prc_accept_log.prc_id = prc_master.id";

			$queryData['leftJoin']['(SELECT SUM(prc_movement.qty) as move_qty
										,prc_id,next_process_id 
										FROM prc_movement 
										WHERE prc_movement.is_delete=0 
										AND send_to = 1 
										'.$customMoveWh.$prcIdWh.
										(!empty($param['process_from'])?' AND process_id='.$param['process_from']:'').'
										 GROUP BY next_process_id
									) prevMovement']="prevMovement.next_process_id = product_process.process_id AND prevMovement.prc_id = prc_master.id";

		}

		/** IF LOG DATA  GET (Total production ok qty, rejection found qty, review qty,challan qty) */
		if(!empty($param['log_data'])){
			$customWh = "";$prcIdWh = '';
			if(!empty($param['log_process_by'])){ $customWh = " AND process_by != 3"; }
			if(!empty($param['move_type'])){ $customWh .= " AND trans_type = ".$param['move_type']; }
			if(!empty($param['prc_id'])){$prcIdWh = " AND prc_id = ".$param['prc_id'];}
			
			$queryData['select'] .= ",IFNULL(prcLog.ok_qty,0) as ok_qty, IFNULL(prcLog.rej_qty,0) as rej_qty, IFNULL(prcLog.rw_qty,0) as rw_qty,IFNULL(prcLog.rej_found,0) as rej_found, IFNULL(prc_challan_request.ch_qty,0) as ch_qty,IFNULL(rejection_log.review_qty,0) as review_qty ";
			$queryData['leftJoin']['(SELECT SUM(prc_log.qty) as ok_qty
										, SUM((prc_log.rej_qty)) as rej_qty, 
										SUM((prc_log.rw_qty)) as rw_qty, 
										SUM(prc_log.rej_found) as rej_found,
										process_id,prc_id 
										FROM prc_log 
										WHERE is_delete = 0 '.
										$customWh.$whereProcessFrom.$prcIdWh.' 
										GROUP BY prc_id,process_id
									) prcLog'] =  "prcLog.process_id = product_process.process_id AND prcLog.prc_id = prc_master.id";

			$queryData['leftJoin']['(SELECT SUM(prc_challan_request.qty- prc_challan_request.without_process_qty) as ch_qty,
										process_id,prc_id 
										FROM prc_challan_request 
										WHERE is_delete = 0 
										'.(!empty($param['move_type'])?' 
										AND trans_type ='.$param['move_type']:'').
										$whereProcessFrom.$prcIdWh.' 
										GROUP BY process_id
									) prc_challan_request'] =  "prc_challan_request.process_id = product_process.process_id AND prc_challan_request.prc_id = prc_master.id";

			$queryData['leftJoin']['(SELECT SUM(rejection_log.qty) as review_qty,
										rejection_log.log_id,
										rejection_log.prc_id,
										prc_log.process_id 
										FROM rejection_log
										LEFT JOIN prc_log ON prc_log.id = rejection_log.log_id 
										WHERE rejection_log.is_delete = 0 '.
										$customWh.$whereProcessFrom.' AND 
										source="MFG" 
										'.((!empty($param['prc_id']))?'AND rejection_log.prc_id = '.$param['prc_id']:'').' 
										GROUP BY prc_log.process_id
									) rejection_log'] = "rejection_log.prc_id = prc_master.id AND rejection_log.process_id = product_process.process_id";
		}
		
		/** MOVEMENT GET (Total movement_qty) */
		if(!empty($param['movement_data'])){
			$customMoveWh="";$customWh="";$prcIdWh = '';
			if(!empty($param['move_type'])){ $customMoveWh = " AND move_from = ".$param['move_type']; }
			if(!empty($param['move_type'])){ $customWh = " AND trans_type = ".$param['move_type']; }
			if(!empty($param['prc_id'])){$prcIdWh = " AND prc_id = ".$param['prc_id'];}

			$queryData['select'] .= ",(IFNULL(prc_movement.movement_qty,0)-IFNULL(current_accept_log.short_qty,0)) as movement_qty,IFNULL(current_accept_log.short_qty,0) as short_qty";
			$queryData['leftJoin']['(SELECT SUM(prc_movement.qty) as movement_qty,
										process_id,prc_id 	
										FROM prc_movement 
										WHERE prc_movement.is_delete=0 
										'.$customMoveWh.$whereProcessFrom.$prcIdWh.' AND 
										ref_id = 0 
										GROUP BY process_id
									) prc_movement']="prc_movement.prc_id = prc_master.id AND prc_movement.process_id = product_process.process_id"; //group by prc_id krvanu

			$queryData['leftJoin']['(SELECT SUM(IFNULL(prc_accept_log.accepted_qty,0)) as accepted_qty,
										SUM(IFNULL(prc_accept_log.short_qty,0)) as short_qty,
										accepted_process_id,prc_id 
										FROM prc_accept_log 
										WHERE prc_accept_log.is_delete=0 
										'.$customWh.$whereProcessFrom.$prcIdWh.' 
										GROUP BY accepted_process_id
									) current_accept_log']="current_accept_log.accepted_process_id = product_process.process_id AND current_accept_log.prc_id = prc_master.id";	
		}

		/** GET Rework Data */
		/** IF LOG DATA  GET (Total production ok qty, rejection found qty, review qty,challan qty) */
		if(!empty($param['rework_data'])){
			$prcIdWh = '';
			if(!empty($param['prc_id'])){$prcIdWh = " AND prc_id = ".$param['prc_id'];}
			$queryData['select'] .= ",IFNULL(rwLog.rw_ok_qty,0) as rw_ok_qty";
			$queryData['leftJoin']['(SELECT SUM(prc_log.qty) as rw_ok_qty, 
										SUM((prc_log.rej_qty)) as rw_rej_qty,
										SUM((prc_log.rw_qty)) as rw_qty, 
										SUM(prc_log.rej_found) as rej_found,
										process_id,prc_id 
										FROM prc_log 
										WHERE is_delete = 0  AND 
										trans_type = 2 
										'.$prcIdWh.' 
										GROUP BY process_id
									) rwLog'] =  "rwLog.process_id = product_process.process_id AND rwLog.prc_id = prc_master.id";
		}
		
		if(!empty($param['prc_id'])){ $queryData['where']['prc_master.id'] = $param['prc_id']; }
		if(!empty($param['process_id'])){ $queryData['where_in']['product_process.process_id'] = $param['process_id']; }
		if(!empty($param['prc_type'])){ $queryData['where']['prc_master.prc_type'] = $param['prc_type']; }
		if(!empty($param['item_id'])){ $queryData['where']['product_process.item_id'] = $param['item_id']; }
		if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }
		
		if(!empty($param['order_by_date'])){ $queryData['order_by']['prc_master.prc_date'] = 'DESC'; }
		
		if(!empty($param['process_id'])){ $queryData['order_by']['FIELD(product_process.process_id,'.$param['process_id'].')'] = '';}
		else{ $queryData['order_by']['product_process.sequence'] = 'ASC'; }
		
		if(!empty($param['single_row'])){ $result = $this->row($queryData); }
		else{ $result = $this->rows($queryData); }
		
		$queryData['group_by'][]="prc_master.id,product_process.process_id";
		//$this->printQuery();
        return $result;  
    }

	public function getSemiFinishData($param=[]){
		$queryData = array();          
		$queryData['tableName'] = "prc_master";
		$queryData['select'] = "prc_master.id as prc_id,prc_master.prc_number,prc_master.prc_date,prc_master.item_id,prc_master.rev_no, prc_master.prc_qty,prc_detail.first_process,prc_detail.process_ids";
        $queryData['leftJoin']['prc_detail'] = "prc_detail.prc_id = prc_master.id";
		
		/** IF LOG DATA  GET (Total production ok qty, rejection found qty, review qty,challan qty) */
		if(!empty($param['log_data'])){
			$customWh = "";
			if(!empty($param['log_process_by'])){
				$customWh = " AND process_by != 3";
			}
			if(!empty($param['move_type'])){
				$customWh .= " AND trans_type = ".$param['move_type'];
			}
			$queryData['select'] .= ",IFNULL(prcLog.ok_qty,0) as ok_qty, IFNULL(prcLog.rej_qty,0) as rej_qty, IFNULL(prcLog.rw_qty,0) as rw_qty,IFNULL(prcLog.rej_found,0) as rej_found, IFNULL(prc_challan_request.ch_qty,0) as ch_qty,IFNULL(rejection_log.review_qty,0) as review_qty ";

			$queryData['leftJoin']['(SELECT SUM(prc_log.qty) as ok_qty, SUM((prc_log.rej_qty)) as rej_qty, SUM((prc_log.rw_qty)) as rw_qty, SUM(prc_log.rej_found) as rej_found,process_id,prc_id FROM prc_log WHERE is_delete = 0 AND process_id =1 '.(!empty($param['move_type'])?" AND trans_type = ".$param['move_type']:'').' GROUP BY prc_id,process_id) prcLog'] =  "prcLog.process_id = 1 AND prcLog.prc_id = prc_master.id";

			$queryData['leftJoin']['(SELECT SUM(prc_challan_request.qty) as ch_qty,process_id,prc_id FROM prc_challan_request WHERE is_delete = 0 AND process_id =1 '.(!empty($param['move_type'])?' AND trans_type ='.$param['move_type']:'').' GROUP BY prc_id,process_id) prc_challan_request'] =  "prc_challan_request.process_id = 1 AND prc_challan_request.prc_id = prc_master.id";

			$queryData['leftJoin']['(SELECT SUM(rejection_log.qty) as review_qty,rejection_log.log_id,rejection_log.prc_id,prc_log.process_id FROM rejection_log LEFT JOIN prc_log ON prc_log.id = rejection_log.log_id WHERE rejection_log.is_delete = 0 AND process_id =1 AND source="MFG" GROUP BY prc_log.prc_id,prc_log.process_id) rejection_log'] = "rejection_log.prc_id = prc_master.id AND rejection_log.process_id = 1";
		}
		/** MOVEMENT GET (Total movement_qty) */
		if(!empty($param['movement_data'])){
			$customMoveWh="";$customWh="";
			if(!empty($param['move_type'])){
				$customMoveWh = " AND move_from = ".$param['move_type'];
			}
			if(!empty($param['move_type'])){
				$customWh = " AND trans_type = ".$param['move_type'];
			}
			$queryData['select'] .= ",(IFNULL(prc_movement.movement_qty,0)-IFNULL(current_accept_log.short_qty,0)) as movement_qty,IFNULL(current_accept_log.short_qty,0) as short_qty";
			$queryData['leftJoin']['(SELECT SUM(prc_movement.qty) as movement_qty,process_id,prc_id FROM prc_movement WHERE prc_movement.is_delete=0 AND process_id =1 '.$customMoveWh.' GROUP BY prc_id,process_id) prc_movement']="prc_movement.prc_id = prc_master.id AND prc_movement.process_id = 1";
			
			$queryData['leftJoin']['(SELECT SUM(IFNULL(prc_accept_log.accepted_qty,0)) as accepted_qty,SUM(IFNULL(prc_accept_log.short_qty,0)) as short_qty,accepted_process_id,prc_id FROM prc_accept_log WHERE prc_accept_log.is_delete=0 AND accepted_process_id =1 '.$customWh.' GROUP BY prc_id,accepted_process_id) current_accept_log']="current_accept_log.accepted_process_id = 1 AND current_accept_log.prc_id = prc_master.id";	
		}

		/** GET INWARD QTY (in_qty,pending accept)*/
		if(!empty($param['pending_accepted'])){

			$customMoveWh="";$customWh="";
			if(!empty($param['move_type'])){
				$customMoveWh = " AND move_type = ".$param['move_type'];
			}
			if(!empty($param['move_type'])){
				$customWh = " AND trans_type = ".$param['move_type'];
			}

			$queryData['select'] .= ",((IFNULL(prevMovement.move_qty,0)-IFNULL(prc_accept_log.short_qty,0)) - IFNULL(prc_accept_log.accepted_qty,0)) as pending_accept,IFNULL(prc_accept_log.accepted_qty,0) as in_qty";

			$queryData['leftJoin']['(SELECT SUM(prc_accept_log.accepted_qty) as accepted_qty,SUM(prc_accept_log.short_qty) as short_qty,accepted_process_id,prc_id FROM prc_accept_log WHERE prc_accept_log.is_delete=0  AND accepted_process_id =1 '.$customWh.' GROUP BY prc_id,accepted_process_id) prc_accept_log']="prc_accept_log.accepted_process_id = 1 AND prc_accept_log.prc_id = prc_master.id";

			$queryData['leftJoin']['(SELECT SUM(prc_movement.qty) as move_qty,prc_id,next_process_id FROM prc_movement WHERE prc_movement.is_delete=0  AND next_process_id =1 AND send_to = 1 '.$customMoveWh.' GROUP BY prc_id,next_process_id) prevMovement']="prevMovement.next_process_id = 1 AND prevMovement.prc_id = prc_master.id";
		}

		/** GET Product Process Data */
		
		if(!empty($param['prc_id'])){ $queryData['where']['prc_master.id'] = $param['prc_id']; }
		if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }
		
		if(!empty($param['single_row'])){ $result = $this->row($queryData); }
		else{ $result = $this->rows($queryData); }
        return $result;  
	}

	public function getProcessFromPRC($param=[]){
        $queryData = array();          
		$queryData['tableName'] = "product_process";
		$queryData['select'] = "process_master.process_name,process_master.id";
		$queryData['leftJoin']['process_master'] = 'process_master.id = product_process.process_id';
		if(!empty($param['process_ids'])){ $queryData['where_in']['process_master.id'] = $param['process_ids']; } 
		if(!empty($param['item_id'])){ $queryData['where']['product_process.item_id'] = $param['item_id']; } 
		if(!empty($param['order_process_ids'])){
			$queryData['order_by']['FIELD(product_process.process_id,'.$param['order_process_ids'].')'] = '';
		}else{
			$queryData['order_by']['product_process.sequence'] = 'ASC';
		}
		
        $result = $this->rows($queryData);
        return $result;
    }
    
	public function deletePRC($id){
        try{
            $this->db->trans_begin();
            $result = $this->trash('prc_detail',['prc_id'=>$id],'PRC Detail');
            $result = $this->trash('prc_bom',['prc_id'=>$id],'PRC Bom');
            $result = $this->trash('prc_master',['id'=>$id],'PRC');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

	public function savePRCLog($param){
		try {
			$this->db->trans_begin();
			/** Check PRC Status */
			$prcData = $this->getPRC(['id'=>$param['prc_id']]);
			if(!in_array($prcData->status,[1,2,3])){
				return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
			}
			/*** Check Required Material For Production */
			if($param['process_id'] > 0 && $param['trans_type'] == 1 && !in_array($param['process_id'],[1,2])){

				$prcProcessData = $this->getPRCProcessList(['process_id'=>$param['process_id'],'prc_id'=>$param['prc_id'],'log_data'=>1,'single_row'=>1]); 
				$ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
				$rej_found = !empty($prcProcessData->rej_found)?$prcProcessData->rej_found:0;
				$rw_qty = !empty($prcProcessData->rw_qty)?$prcProcessData->rw_qty:0;
				$rej_qty = !empty($prcProcessData->rej_qty)?$prcProcessData->rej_qty:0;
				$pendingReview = $rej_found - $prcProcessData->review_qty;
				$total_qty =($ok_qty+$rej_qty+$rw_qty+$pendingReview+$param['qty']+$param['rej_found'])/$prcProcessData->output_qty;
				$mtResult = $this->checkIssueMaterialForPrc(['prc_id'=>$param['prc_id'],'process_id'=>$param['process_id'],'check_qty'=>$total_qty]);
				if($mtResult['status'] == 0){
					return $mtResult;
				}
			}
			$logDetail = (!empty($param['logDetail']))?$param['logDetail']:[];
			$without_process_qty = (!empty($param['without_process_qty']))?$param['without_process_qty']:0;
			$firData = (!empty($param['firData']))?$param['firData']:[];
			unset($param['logDetail'],$param['firData'],$param['without_process_qty']);
            $result = $this->store('prc_log', $param, 'PRC Log');
			if(!empty($logDetail)){
				$logDetail['log_id'] = $result['id'];	
				$this->store('prc_log_detail', $logDetail, 'PRC Log Detail');
			}
			if($param['process_id'] == 2){
				$firData['id'] = "";
				$firData['ref_id'] = $result['id'];
				$this->store('production_inspection',$firData,'Final Inspection');
				$this->edit('prc_master', ['id'=>$param['prc_id']], ['rev_no'=>$firData['rev_no']]);
			}
			 
			// IF Vendor Return Log Without process
			if($param['process_by'] == 3 && !empty($without_process_qty)){
				//Set Without Process Qty in prc challan request table
				$setData = array();
                $setData['tableName'] = 'prc_challan_request';
                $setData['where']['id'] = $param['ref_trans_id'];
                $setData['set']['without_process_qty'] = 'without_process_qty, + ' . $without_process_qty;
                $this->setValue($setData);

				//Without process Log
				$logData = [
					'id'=>'',
					'prc_id'=>$param['prc_id'],
					'challan_id'=>$param['ref_id'],
					'challn_req_id'=>$param['ref_trans_id'],
					'log_id'=>$result['id'],
					'in_challan_no'=>$param['in_challan_no'],
					'qty'=>$without_process_qty,
					'created_by'=>$this->loginId,
					'created_at'=>date("Y-m-d H:i:s"),
				];
				$this->store('without_process_log',$logData);
			}

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function deletePRCLog($param){
		try {
			$this->db->trans_begin();

			$logData = $this->getProcessLogList(['id'=>$param['id'],'rejection_review_data'=>1,'single_row'=>1,'outsource_without_process'=>1]);

			$prcData = $this->getPRC(['id'=>$logData->prc_id]);
			if(!in_array($prcData->status,[2,3])){
				return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
			}

			if(!empty($logData)){
				$movementData =  $this->sop->getPRCProcessList(['prc_id'=>$logData->prc_id,'process_id'=>$logData->process_id,'log_data'=>1,'movement_data'=>1,'single_row'=>1]); 
				$pending_movement = $movementData->ok_qty - $movementData->movement_qty;

				if ($logData->review_qty > 0){
					return ['status'=>0,'message'=>'You can not delete this Log. You have to delete rejection review first'];
				}
				if(($logData->qty > $pending_movement) ){
					return ['status'=>0,'message'=>'You can not delete this Log. Qty is sent to next process'];
				}
				//Check If without Process Qty
				if($logData->process_by == 3 && $logData->without_process_qty > 0){
					$prcProcessData = $this->sop->getPRCProcessList(['process_id'=>$logData->process_id,'prc_id'=>$logData->prc_id,'log_data'=>1,'movement_data'=>1,'log_process_by'=>1,'pending_accepted'=>1,'single_row'=>1,'process_from'=>$logData->process_from,'move_type'=>$logData->trans_type]); 
					$in_qty = (!empty($prcProcessData->in_qty)?$prcProcessData->in_qty:0);
					$ok_qty = !empty($prcProcessData->ok_qty)?$prcProcessData->ok_qty:0;
					$rej_found = !empty($prcProcessData->rej_found)?$prcProcessData->rej_found:0;
					$rw_qty = !empty($prcProcessData->rw_qty)?$prcProcessData->rw_qty:0;
					$rej_qty = !empty($prcProcessData->rej_qty)?$prcProcessData->rej_qty:0;
					$pendingReview = $rej_found - $prcProcessData->review_qty;
					$pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$prcProcessData->ch_qty);

					if($logData->without_process_qty > $pending_production){
						return ['status'=>0,'message'=>'You can not delete this Log. Production of returned qty without process has been done. '];
					}
					$setData = array();
					$setData['tableName'] = 'prc_challan_request';
					$setData['where']['id'] = $logData->ref_trans_id;
					$setData['set']['without_process_qty'] = 'without_process_qty, - ' . $logData->without_process_qty;
					$this->setValue($setData);
					$this->trash('without_process_log',['log_id'=>$param['id']]);
				}
				$this->trash('prc_log_detail',['log_id'=>$param['id']]);
				$result = $this->trash('prc_log',['id'=>$param['id']]);
				if($logData->process_id == 2){
					$this->trash('production_inspection',['ref_id'=>$param['id'],'report_type'=>2]);
				}
				$bomData = $this->getPrcBomData(['prc_id'=>$movementData->prc_id,'process_id'=>$logData->process_id,'production_data'=>1,'single_row'=>1]);
					if(!empty($bomData->item_id) && $bomData->production_qty == 0){
						$this->edit("prc_bom",['prc_id'=>$movementData->prc_id,'process_id'=>$logData->process_id],['batch_no'=>'']);
					}
			}else{
				$result = ['status'=>0,'message'=>'Log already deleted'];
			}

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}
	
	public function getProcesStates($param=[]){
        $queryData = array();          
		$queryData['tableName'] = "prc_log";
		
		$queryData['select'] = "SUM(qty) as ok_qty, SUM((rej_qty)) as rej_qty, SUM(rej_found) as rej_found";
        		
		if(!empty($param['id'])){ $queryData['where']['prc_log.id'] = $param['id']; }
		
		if(!empty($param['prc_id'])){ $queryData['where']['prc_log.prc_id'] = $param['prc_id']; }	
		
		if(!empty($param['trans_date'])){ $queryData['where']['prc_log.trans_date'] = $param['trans_date']; }	
		
		if(!empty($param['process_id'])){ $queryData['where']['prc_log.process_id'] = $param['process_id']; }
		
		if(!empty($param['process_by'])){ $queryData['where']['prc_log.process_by'] = $param['process_by']; }
		
		if(!empty($param['processor_id'])){ $queryData['where']['prc_log.processor_id'] = $param['processor_id']; }		
			
		if(!empty($param['operator_id'])){ $queryData['where']['prc_log.operator_id'] = $param['operator_id']; }
		
		if(!empty($param['machine_id'])){ $queryData['where']['prc_log.machine_id'] = $param['machine_id']; }
		
		if(!empty($data['customWhere'])){ $queryData['customWhere'][] = $data['customWhere']; }
		
		if(!empty($param['machine_id'])){ $queryData['where']['prc_log.machine_id'] = $param['machine_id']; }
		
		$result = $this->row($queryData);
        

        return $result;  
    }

	public function getProcessLogList($param=[]){
        $queryData = array();          
		$queryData['tableName'] = "prc_log";
		
		$queryData['select'] = "prc_log.*,employee_master.emp_name,shift_master.shift_name,prc_log_detail.remark,prc_detail.process_ids,prc_master.item_id,prc_master.prc_number,prc_master.status, machine.item_code as machine_code, machine.item_name as machine_name, item_master.item_name, item_master.item_code, created.emp_name as created_name,process_master.process_name,product_process.cycle_time,prc_log_detail.start_time,prc_log_detail.end_time,process_master.die_required,prc_master.batch_no,prc_master.item_id,prc_master.cutting_flow";
		$queryData['select'] .=', IF(prc_log.process_by = 1, machine.item_code, IF(prc_log.process_by = 2,department_master.name, IF(prc_log.process_by = 3,party_master.party_name,""))) as processor_name,fromProcess.process_name AS from_process_name';
		$queryData['leftJoin']['item_master machine'] = "machine.id = prc_log.processor_id";
		$queryData['leftJoin']['department_master'] = "department_master.id = prc_log.processor_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = prc_log.processor_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = prc_log.operator_id";
        $queryData['leftJoin']['shift_master'] = "shift_master.id = prc_log.shift_id";
        $queryData['leftJoin']['prc_log_detail'] = "prc_log_detail.log_id = prc_log.id AND prc_log_detail.is_delete = 0";
        $queryData['leftJoin']['prc_master'] = "prc_master.id = prc_log.prc_id";
        $queryData['leftJoin']['prc_detail'] = "prc_detail.prc_id = prc_log.prc_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
		$queryData['leftJoin']['employee_master created'] = "created.id = prc_log.created_by";
        $queryData['leftJoin']['process_master'] = "process_master.id = prc_log.process_id";
        $queryData['leftJoin']['process_master fromProcess'] = "fromProcess.id = prc_log.process_from";
        $queryData['leftJoin']['product_process'] = "product_process.process_id = prc_log.process_id AND product_process.item_id = prc_master.item_id AND product_process.is_delete = 0";
		
		if(!empty($param['outsource_without_process'])){
			$queryData['select'] .= ',without_process_log.qty AS without_process_qty';
			$queryData['leftJoin']['without_process_log'] = 'without_process_log.log_id  = prc_log.id';
		}

		if(!empty($param['rejection_review_data'])){
			$queryData['select'] .=',IFNULL(rejection_log.review_qty,0) as review_qty,(prc_log.rej_found-IFNULL(rejection_log.review_qty,0)) as pending_qty';
			$queryData['leftJoin']['(SELECT SUM(qty) as review_qty,log_id,prc_id FROM rejection_log WHERE is_delete = 0 AND source="MFG" GROUP BY rejection_log.log_id,prc_id) rejection_log'] = "rejection_log.log_id = prc_log.id AND prc_log.prc_id = rejection_log.prc_id";
		}

		if(!empty($param['fir_data'])){
			$queryData['select'] .=',production_inspection.id AS inspection_id,production_inspection.sampling_qty';
			$queryData['leftJoin']['production_inspection'] = "production_inspection.ref_id = prc_log.id AND production_inspection.report_type = 2";
		}

		if(!empty($param['grouped_data'])){
			$queryData['select'] .=',SUM(prc_log.qty) as ok_qty,SUM(prc_log.rej_found) as rej_found,SUM(prc_log.rej_qty) as rej_qty,SUM(prc_log.rw_qty) as rw_qty,SUM(review_qty) as review_qty';
		}

		if(!empty($param['breakdown'])){
			$queryData['select'] .= ",IFNULL(bd.breakdown_time,0) as breakdown_time";
			$queryData['leftJoin']['(SELECT SUM(TIMESTAMPDIFF(SECOND, trans_date, end_date)) as breakdown_time,machine_id,DATE(trans_date) as trans_date FROM machine_breakdown WHERE is_delete = 0 GROUP BY machine_id,DATE(trans_date)) as bd'] = "bd.machine_id = prc_log.processor_id AND bd.trans_date = prc_log.trans_date"; 
		}

		if(!empty($param['movement_data'])){
			$queryData['select'] .=',IFNULL(prc_movement.movement_qty,0) as movement_qty';
			$queryData['leftJoin']['(SELECT SUM(qty) as movement_qty,process_id,prc_id,move_from FROM prc_movement WHERE is_delete = 0 AND ref_id = 0  GROUP BY prc_id,prc_movement.process_id,move_from) prc_movement'] = "prc_movement.process_id = prc_log.process_id AND prc_log.prc_id = prc_movement.prc_id AND prc_log.trans_type = prc_movement.move_from";
		}

		if(!empty($param['id'])){ $queryData['where']['prc_log.id'] = $param['id']; }

		if(!empty($param['prc_id'])){ $queryData['where']['prc_log.prc_id'] = $param['prc_id']; }

		if(!empty($param['prc_type'])){ $queryData['where']['prc_master.prc_type'] = $param['prc_type']; }

		if(!empty($param['item_id'])){ $queryData['where']['prc_master.item_id'] = $param['item_id']; }		
		
		if(!empty($param['trans_date'])){ $queryData['where']['prc_log.trans_date'] = $param['trans_date']; }	
		
		if(isset($param['process_id'])){ $queryData['where']['prc_log.process_id'] = $param['process_id']; }
		
		if(!empty($param['process_by'])){ $queryData['where']['prc_log.process_by'] = $param['process_by']; }
		
		if(!empty($param['processor_id'])){ $queryData['where']['prc_log.processor_id'] = $param['processor_id']; }		
			
		if(!empty($param['operator_id'])){ $queryData['where']['prc_log.operator_id'] = $param['operator_id']; }
		
		if(!empty($param['machine_id'])){ $queryData['where']['prc_log.machine_id'] = $param['machine_id']; }
		
		if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }
		
		if(!empty($param['machine_id'])){ $queryData['where']['prc_log.machine_id'] = $param['machine_id']; }

		if(!empty($param['ref_id'])){ $queryData['where']['prc_log.ref_id'] = $param['ref_id']; }

		if(isset($param['ref_trans_id'])){ $queryData['where']['prc_log.ref_trans_id'] = $param['ref_trans_id']; }

		if(!empty($param['trans_type'])){ $queryData['where']['prc_log.trans_type'] = $param['trans_type']; }

		if(isset($param['process_from'])){ $queryData['where']['prc_log.process_from'] = $param['process_from']; }

		if(isset($param['die_set_no'])){ $queryData['where']['prc_log.die_set_no'] = $param['die_set_no']; } 
		
		if(!empty($param['group_by'])){
			$queryData['group_by'][] = $param['group_by'];
		}

		if(!empty($param['having'])){
			$queryData['having'][] = $param['having'];
		}
		
		$queryData['order_by']['product_process.sequence'] = 'ASC';
		$queryData['order_by']['prc_log.trans_date'] = 'ASC';
		
		if(!empty($param['single_row'])){
			$result = $this->row($queryData);
		}else{
			$result = $this->rows($queryData);
		}
        return $result;  
    }

	public function getProcessMovementList($param=[]){
        $queryData = array();          
		$queryData['tableName'] = "prc_movement";
		
		$queryData['select'] = "prc_movement.*,prc_master.item_id,prc_master.prc_number,prc_master.prc_qty,location_master.store_name, item_master.item_name, item_master.item_code,ir.batch_no, process_master.process_name as next_process_name,currentProcess.process_name as current_process_name,employee_master.emp_name as created_name";
		$queryData['select'] .=', IF(prc_movement.send_to = 1, machine.item_code, IF(prc_movement.send_to = 2,department_master.name, IF(prc_movement.send_to = 3,party_master.party_name,""))) as processor_name,
								IF(prc_movement.send_to = 1, "Inhouse", IF(prc_movement.send_to = 2,"Department", IF(prc_movement.send_to = 3,"Vendor", IF(prc_movement.send_to = 4,"Stored","")))) as send_to_name';
		$queryData['leftJoin']['item_master machine'] = "machine.id = prc_movement.processor_id";
		$queryData['leftJoin']['department_master'] = "department_master.id = prc_movement.processor_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = prc_movement.processor_id";
		$queryData['leftJoin']['prc_master'] = "prc_master.id = prc_movement.prc_id";
		$queryData['leftJoin']['location_master'] = "location_master.id = prc_movement.processor_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
		$queryData['leftJoin']['process_master currentProcess'] = "currentProcess.id = prc_movement.process_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = prc_movement.next_process_id";
		$queryData['leftJoin']['(SELECT GROUP_CONCAT(DISTINCT batch_no SEPARATOR ", ") AS batch_no,prc_id FROM `issue_register` WHERE is_delete = 0 AND issue_register.issue_type = 2 GROUP BY prc_id) AS ir'] = "ir.prc_id = prc_movement.prc_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = prc_movement.created_by";

        		
		if(!empty($param['id'])){ $queryData['where']['prc_movement.id'] = $param['id']; }

		
		if(!empty($param['prc_id'])){ $queryData['where']['prc_movement.prc_id'] = $param['prc_id']; }	
		
		if(!empty($param['trans_date'])){ $queryData['where']['prc_movement.trans_date'] = $param['trans_date']; }	
		
		if(!empty($param['process_id'])){ $queryData['where']['prc_movement.process_id'] = $param['process_id']; }

		if(!empty($param['next_process_id'])){ $queryData['where']['prc_movement.next_process_id'] = $param['next_process_id']; }
		
		if(!empty($param['send_to'])){ $queryData['where']['prc_movement.send_to'] = $param['send_to']; }
		
		if(!empty($param['processor_id'])){ $queryData['where']['prc_movement.processor_id'] = $param['processor_id']; }

		if(!empty($param['work_type'])){ $queryData['where']['prc_process.work_type'] = $param['work_type']; }		

		if(!empty($param['move_type'])){ $queryData['where']['prc_movement.move_type'] = $param['move_type']; }	

		if(!empty($param['move_from'])){ $queryData['where']['prc_movement.move_from'] = $param['move_from']; }		

		if(!empty($param['process_from'])){ $queryData['where']['prc_movement.process_from'] = $param['process_from']; }		

		if(!empty($param['next_processor_id'])){ $queryData['where']['prc_movement.next_processor_id'] = $param['next_processor_id']; }	

		if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }
		
		if(!empty($param['group_by'])){
			$queryData['group_by'][] = $param['group_by'];
		}
		
		if(!empty($param['single_row'])){
			$result = $this->row($queryData);
		}else{
			$result = $this->rows($queryData);
		}
		
        return $result;  
    }

	public function startPRC($data){
		try {
			$this->db->trans_begin();
            $prcData = $this->getPRC(['id'=>$data['id']]);
			
			if($prcData->status == 1){
				if($prcData->prc_type == 1){
					/*** Movement Entry */
					$movementQty = [
						'id'=>'',
						'prc_id' => $data['id'],
						'process_id' => 0,
						'next_process_id' =>  $data['first_process'],
						'trans_date' => date("Y-m-d"),
						'qty' => $prcData->prc_qty,
					];
					$this->sop->savePRCMovement($movementQty);
					$accept = [
						'id' => '',
						'accepted_process_id' => $data['first_process'],
						'prc_id' => $data['id'],
						'accepted_qty' => $prcData->prc_qty,
						'short_qty' => '',
						'trans_date' => date("Y-m-d"),
						'created_by' => $this->loginId,
						'created_at' => date("Y-m-d H:i:s"),
					];
					$this->sop->saveAcceptedQty($accept);
					$result = $this->edit("prc_detail",['prc_id'=>$data['id']],['first_process'=>$data['first_process']]);	
				}
				/** Inprogress Prc */
				$result = $this->store("prc_master",['id'=>$data['id'],'status'=>2]);
				
			}else{
				$result = $this->edit("prc_detail",['prc_id'=>$data['id']],['first_process'=>$data['first_process']]);	
				$result = $this->edit("prc_movement",['prc_id'=>$data['id'],'process_id'=>0],['next_process_id'=>$data['first_process']]);	
			}
			if(!empty($data['process_ids'])){
				$result = $this->edit("prc_detail",['prc_id'=>$data['id']],['process_ids'=>$data['process_ids']]);	
			}else{
				$result = $this->edit("prc_detail",['prc_id'=>$data['id']],['process_ids'=>null]);	
			}
			

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Saved Successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function savePRCMovement($param){
		try {
			$this->db->trans_begin();

			$prcData = $this->getPRC(['id'=>$param['prc_id']]);
			if(!in_array($prcData->status,[1,2,3])){
				return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
			}
			/*if($param['process_id'] == 1){ // IF Semi-Finished Movement Then AUTO ACCEPT Entry 
				$accept = [
					'id' => '',
					'accepted_process_id' =>1,
					'prc_id' => $param['prc_id'],
					'accepted_qty' => $param['qty'],
					'short_qty' => '',
					'trans_date' => $param['trans_date'],
					'created_by' => $this->loginId,
					'created_at' => date("Y-m-d H:i:s"),
				];
				$this->sop->saveAcceptedQty($accept);
			}*/
			$location_id = "";
			if($param['next_process_id'] == 0){
				$itemData = $this->item->getItem(['id'=>$prcData->item_id]);
				$param['processor_id'] = ($itemData->is_packing == 0)?$this->RTD_STORE->id:$this->PACKING_STORE->id;
				$location_id = ($itemData->is_packing == 0)?$this->RTD_STORE->id:$this->PACKING_STORE->id;
				/* $param['processor_id'] = $this->RTD_STORE->id;
				$location_id = $this->RTD_STORE->id; */
			}elseif($param['next_process_id'] == '-1'){
				$param['next_process_id'] = 0;
				$param['processor_id'] = $this->SEMI_STORE->id;
				$location_id = $this->SEMI_STORE->id;
			}
            $result = $this->store('prc_movement', $param, 'PRC Log');
			/** If Last Process then add to stock */
			if($param['next_process_id'] == 0){
				$prcData = $this->getPRCDetail(['id'=>$param['prc_id']]);
				
				$stockData = [
					'id' => "",
					'trans_type' => 'PRD',
					'trans_date' => $param['trans_date'],
					'ref_no' => $prcData->prc_number,
					'main_ref_id' => $param['prc_id'],
					'child_ref_id' => $result['id'],
					'location_id' => $location_id,
					'batch_no' =>$prcData->prc_number,
					'ref_batch' =>$prcData->batch_no, //For TC Purpose Save rm batch no.
					'item_id' => $prcData->item_id,
					'p_or_m' => 1,
					'qty' => $param['qty'],
				];

				$this->store('stock_trans',$stockData);
			}
			$this->changePrcStatus(['prc_id'=>$param['prc_id']]);
			
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function saveReceiveStoredMaterial($data){
		try {
			$this->db->trans_begin();
			$prcData = $this->getPRC(['id'=>$data['prc_id']]);
			if(!in_array($prcData->status,[2,3])){
				return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
			}
            foreach($data['qty'] as $key=>$qty){
				$setData = array();
                $setData['tableName'] = 'prc_movement';
                $setData['where']['id'] = $data['trans_id'][$key];
                $setData['set']['received_qty'] = 'received_qty, + ' . $qty;
                $setData['set']['qty'] = 'qty, - ' . $qty;
                $this->setValue($setData);
				$movementData = [
					'id'=>'',
					'prc_id' => $data['prc_id'],
					'process_id' => $data['process_id'],
					'next_process_id' => $data['next_process_id'],
					'send_to' =>1,
					'processor_id' =>0,
					'trans_date' => date("Y-m-d"),
					'qty' =>$qty,
					'wt_nos' => 0,
					'remark' => '',
				];
				$result = $this->store("prc_movement",$movementData);
			}
			
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function deletePRCMovement($param){
		try {
			$this->db->trans_begin();
			$movementData = $this->getProcessMovementList(['id'=>$param['id'],'single_row'=>1]);
			$prcData = $this->getPRC(['id'=>$movementData->prc_id]);
			if(!in_array($prcData->status,[2,3])){
				return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
			}
			if(!empty($movementData)){
				if(!empty($movementData->next_process_id)){
					if($movementData->next_process_id == 1){
						$nextProcessData = $this->sop->getSemiFinishData(['prc_id'=>$movementData->prc_id,'pending_accepted'=>1,'single_row'=>1]); 
					}else{
						$nextProcessData =  $this->sop->getPRCProcessList(['process_id'=>$movementData->next_process_id,'prc_id'=>$movementData->prc_id,'pending_accepted'=>1,'single_row'=>1]); 
					}
					if($movementData->qty > $nextProcessData->pending_accept ){
						return ['status'=>0,'message'=>'You can not delete this movement. This movement accepted by next process'];
					}
				}else{
					//$itemData = $this->item->getItem(['id'=>$prcData->item_id]);
					//$location_id = ($itemData->is_packing == 0)?$this->RTD_STORE->id:$this->PACKING_STORE->id;
					/* $stockData = $this->itemStock->getItemStockBatchWise(['item_id'=>$movementData->item_id,'location_id'=>$location_id,'batch_no'=>$movementData->prc_number,'single_row'=>1]); */
					$stockData = $this->itemStock->getItemStockBatchWise(['item_id'=>$movementData->item_id,'location_id'=>$movementData->processor_id,'batch_no'=>$movementData->prc_number,'single_row'=>1]);
					if($movementData->qty > $stockData->qty){
						return ['status'=>0,'message'=>'You can not delete this movement'];
					}
					
					$this->remove('stock_trans',['main_ref_id'=>$movementData->prc_id,'child_ref_id'=>$movementData->id,'trans_type'=>'PRD']);
				}
				
				$result = $this->trash('prc_movement',['id'=>$param['id']]);
				if($movementData->process_id == 0){
					$bomData = $this->getPrcBomData(['prc_id'=>$movementData->prc_id,'process_id'=>0,'production_data'=>1,'single_row'=>1]);
					if(!empty($bomData->item_id) && $bomData->production_qty == 0){
						$this->edit("prc_bom",['prc_id'=>$movementData->prc_id,'process_id'=>0],['batch_no'=>'']);
					}
				}

				if($movementData->process_id == 1){
					$accept = [
						'id' => '',
						'accepted_process_id' =>1,
						'prc_id' => $movementData->prc_id,
						'accepted_qty' => '-'.$movementData->qty,
						'short_qty' => '',
						'trans_date' => $movementData->trans_date,
						'created_by' => $this->loginId,
						'created_at' => date("Y-m-d H:i:s"),
					];
					$this->sop->saveAcceptedQty($accept);
				}
				$this->changePrcStatus(['prc_id'=>$movementData->prc_id]);
			}else{
				$result = ['status'=>0,'message'=>'movement already deleted'];
			}
			

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function saveAcceptedQty($data){
		try {
			$this->db->trans_begin();
			$prcData = $this->getPRC(['id'=>$data['prc_id']]);
			if(!in_array($prcData->status,[1,2,3])){
				return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
			}
			$result = $this->store('prc_accept_log', $data, 'Acceped');
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Record Updated Successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function getPrcAcceptData($param){
		$queryData = array();          
		$queryData['tableName'] = "prc_accept_log";
		$queryData['select'] = "prc_accept_log.*,fromProcess.process_name AS from_process_name";
		$queryData['leftJoin']['process_master fromProcess'] = 'fromProcess.id = prc_accept_log.process_from';
		if(!empty($param['id'])){ $queryData['where']['prc_accept_log.id'] = $param['id']; }
		if(!empty($param['prc_id'])){ $queryData['where']['prc_accept_log.prc_id'] = $param['prc_id']; }
		if(!empty($param['accepted_process_id'])){ $queryData['where']['prc_accept_log.accepted_process_id'] = $param['accepted_process_id']; }	
		if(!empty($param['trans_date'])){ $queryData['where']['prc_accept_log.trans_date'] = $param['trans_date']; }	
		if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }
		if(isset($param['process_from'])){ $queryData['where']['prc_accept_log.process_from'] = $param['process_from']; }	
		if(isset($param['trans_type'])){ $queryData['where']['prc_accept_log.trans_type'] = $param['trans_type']; }	

		if(!empty($param['group_by'])){$queryData['group_by'][] = $param['group_by'];}
		if(!empty($param['single_row'])){ $result = $this->row($queryData); }
		else{ $result = $this->rows($queryData); }
		
        return $result;  
	}

	public function deletePrcAccept($param){
		try {
			$this->db->trans_begin();
			$acceptData = $this->getPrcAcceptData(['id'=>$param['id'],'single_row'=>1]);
			$prcData = $this->getPRC(['id'=>$acceptData->prc_id]);
			if(!in_array($prcData->status,[2,3])){
				return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
			}
			if(!empty($acceptData)){
				$logData =  $this->sop->getPRCProcessList(['process_id'=>$acceptData->accepted_process_id,'prc_id'=>$acceptData->prc_id,'pending_accepted'=>1,'log_data'=>1,'log_process_by'=>1,'single_row'=>1]); 
				
				$in_qty = (!empty($logData->in_qty)?$logData->in_qty:0);
				$ok_qty = !empty($logData->ok_qty)?$logData->ok_qty:0;
				$rej_found = !empty($logData->rej_found)?$logData->rej_found:0;
				$rw_qty = !empty($logData->rw_qty)?$logData->rw_qty:0;
				$rej_qty = !empty($logData->rej_qty)?$logData->rej_qty:0;
                $pendingReview = $rej_found - $logData->review_qty;
                $pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview+$logData->ch_qty);
               
				if($acceptData->accepted_qty > $pending_production ){ return ['status'=>0,'message'=>'You can not unaccept this qty'.$acceptData->accepted_qty .'>' .$pending_production ]; }
				if($acceptData->short_qty > 0){
					$prvProcessData =  $this->sop->getPRCProcessList(['id'=>$acceptData->prc_process_id,'log_data'=>1,'movement_data'=>1,'single_row'=>1]); 
					$ok_qty = !empty($prvProcessData->ok_qty)?$prvProcessData->ok_qty:0;
					$movement_qty =!empty($prvProcessData->movement_qty)?$prvProcessData->movement_qty:0;
					$pending_movement = $ok_qty - $movement_qty;
					if($acceptData->short_qty > $pending_movement){ return ['status'=>0,'message'=>'You can not unaccept this qty']; }
				}
				$result = $this->trash('prc_accept_log',['id'=>$param['id']]);
			}else{
				$result = ['status'=>0,'message'=>'Log already deleted'];
			}
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function saveChallanRequest($data){
		try {
			$this->db->trans_begin();
			$prcData = $this->getPRC(['id'=>$data['prc_id']]);
			if(!in_array($prcData->status,[2,3])){
				return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
			}
			
			$result = $this->store('prc_challan_request', $data, 'Challan Request');
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Record Updated Successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function deleteChallanRequest($data){
		try {
			$this->db->trans_begin();
			$result = $this->trash('prc_challan_request', ['id'=>$data['id']], 'Challan Request');
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Record Updated Successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function getChallanRequestData($param = []){
		$queryData = array();          
		$queryData['tableName'] = "prc_challan_request";
		$queryData['select'] = "prc_challan_request.*,prc_master.item_id,prc_master.prc_date,prc_master.prc_number,process_master.process_name,item_master.item_name,item_master.hsn_code,prc_master.batch_no, material_master.material_grade,item_master.item_code, product_process.finish_wt, product_process.uom, fromProcess.process_name AS from_process_name,prc_master.cutting_flow,product_process.mfg_instruction,prevProcess.finish_wt AS prev_weight,prc_detail.process_ids,product_process.output_qty,product_process.process_cost,prc_master.item_id,employee_master.emp_name as created_name";
		$queryData['leftJoin']['prc_master'] = "prc_master.id = prc_challan_request.prc_id";
		$queryData['leftJoin']['prc_detail'] = "prc_detail.prc_id = prc_challan_request.prc_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = prc_challan_request.process_id";
		$queryData['leftJoin']['process_master fromProcess'] = "fromProcess.id = prc_challan_request.process_from";
		$queryData['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
		$queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
		$queryData['leftJoin']['product_process'] = "product_process.item_id = item_master.id AND product_process.process_id = prc_challan_request.process_id AND product_process.is_delete = 0";
		$queryData['leftJoin']['product_process prevProcess'] = "prevProcess.item_id = item_master.id AND prevProcess.process_id = prc_challan_request.process_from AND prevProcess.is_delete = 0";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = prc_challan_request.created_by";
		
		if(!empty($param['challan_receive'])){
			$queryData['select'] .= ',IFNULL(receiveLog.ok_qty,0) as ok_qty,IFNULL(receiveLog.rej_qty,0) as rej_qty';
			$queryData['leftJoin']['(SELECT sum(qty) as ok_qty,SUM(rej_found) as rej_qty,process_id,ref_trans_id FROM prc_log WHERE is_delete = 0 AND process_by = 3 GROUP BY process_id,ref_trans_id) as receiveLog'] = "receiveLog.ref_trans_id = prc_challan_request.id AND prc_challan_request.process_id = receiveLog.process_id";
		}
		
		if(!empty($param['rm_challan'])){
			$queryData['select'] .= ",grn_master.trans_number as grn_number,rmItem.item_code as rm_item_code,rmItem.item_name as rm_item,rmItem.uom as rm_uom,rmItem.hsn_code as rm_hsn,rmItem.wt_pcs,grn_trans.batch_no as grn_batch,rmMaterial.material_grade as rm_grade";
			$queryData['leftJoin']['grn_trans'] = "grn_trans.id = prc_challan_request.jwo_id";
			$queryData['leftJoin']['grn_master'] = "grn_master.id = grn_trans.grn_id";
			$queryData['leftJoin']['item_master rmItem'] = "rmItem.id = prc_challan_request.ref_item_id";
			$queryData['leftJoin']['material_master rmMaterial'] = "rmMaterial.id = rmItem.grade_id";
		}
		
		if(!empty($param['id'])){ $queryData['where']['prc_challan_request.id'] = $param['id']; }
		if(!empty($param['challan_id'])){ $queryData['where']['prc_challan_request.challan_id'] = $param['challan_id']; }
		if(!empty($param['process_id'])){ $queryData['where']['prc_challan_request.process_id'] = $param['process_id']; }
		if(!empty($param['prc_id'])){ $queryData['where']['prc_challan_request.prc_id'] = $param['prc_id']; }	
		if(!empty($param['trans_date'])){ $queryData['where']['prc_challan_request.trans_date'] = $param['trans_date']; }	
		if(!empty($param['trans_type'])){ $queryData['where']['prc_challan_request.trans_type'] = $param['trans_type']; }	
		if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }
		if(!empty($param['pending_challan'])){ $queryData['where']['prc_challan_request.challan_id'] = 0; }
		if(!empty($param['single_row'])){ $result = $this->row($queryData); }
		else{ $result = $this->rows($queryData); }
		
        return $result; 
	}

	public function getPRC($param){
		$data['tableName'] = 'prc_master';
		$data['select'] = 'prc_master.*,prc_detail.remark,prc_detail.process_ids,prc_detail.id as prc_detail_id,prc_detail.cutting_length,prc_detail.cut_weight,prc_detail.cutting_dia,item_master.item_name,prc_detail.first_process,item_master.production_type,prc_master.cutting_flow,item_master.mfg_type';
		$data['leftJoin']['prc_detail'] = 'prc_detail.prc_id = prc_master.id';
		$data['leftJoin']['item_master'] = 'item_master.id = prc_master.item_id';
		$data['where']['prc_master.id'] = $param['id'];
		return $this->row($data);
	}

	public function clearPrcData($data){
		try {
			$this->db->trans_begin();

			$this->trash('prc_log',['prc_id'=>$data['id']]);
			$this->trash('prc_accept_log',['prc_id'=>$data['id']]);
			$this->trash('prc_movement',['prc_id'=>$data['id']]);
			// $this->trash('prc_bom',['prc_id'=>$data['id']]);
			$result = $this->store('prc_master',['id'=>$data['id'],'status'=>1]);

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function getPrcBomData($param){
        $data['tableName'] = "prc_bom";
        $data['select'] = "prc_bom.*,prc_master.prc_number,prc_master.prc_qty,prc_master.batch_no as heat_no,item_master.item_name,item_master.item_type,material_master.material_grade,item_master.uom,item_category.category_name,IFNULL(product_process.output_qty,1) AS output_qty,item_master.wt_pcs";
        $data['leftJoin']['prc_master'] = "prc_master.id = prc_bom.prc_id";
        $data['leftJoin']['item_master'] = "item_master.id = prc_bom.item_id";
        $data['leftJoin']['item_category'] = "item_category.id = prc_bom.item_id";
        $data['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
		$data['leftJoin']['product_process'] = "product_process.item_id = prc_master.item_id AND product_process.process_id = ".((!empty($param['log_process_id']))?$param['log_process_id']:'prc_bom.process_id')." AND product_process.is_delete = 0 ";
		if(!empty($param['production_data'])){
			$data['select'] .= ",prcLog.production_qty ";
			$data['leftJoin']['(SELECT SUM(qty+rej_found) as production_qty,prc_id,process_id FROM prc_log WHERE  is_delete = 0 AND trans_type = 1  GROUP BY prc_id,process_id) prcLog'] = "prc_bom.prc_id = prcLog.prc_id  ".((!empty($param['prc_id']))?' AND prcLog.prc_id='.$param['prc_id']:'').' '.((!empty($param['log_process_id']))?' AND prcLog.process_id='.$param['log_process_id']:' AND prc_bom.process_id = prcLog.process_id'); //26/12/2024
		}
		if(!empty($param['stock_data'])){
			$customWhere = (!empty($param['prc_id']))?' AND child_ref_id ="'.$param['prc_id'].'"':'';
			$customWhere .= (!empty($param['item_id']))?' AND stock_trans.item_id ="'.$param['item_id'].'"':'';
			$customWhere .= (!empty($param['batch_no']))?' AND stock_trans.batch_no ="'.$param['batch_no'].'"':'';
			$data['select'] .= ",IFNULL(stock_trans.issue_qty,0) as issue_qty,IFNULL(stock_trans.return_qty,0) as return_qty,IFNULL(stock_trans.supplier_name,'') as supplier_name,IFNULL(stock_trans.batch_no,'') as batch_no,IFNULL(stock_trans.location_id,'') as location_id ";

			$data['leftJoin']['(SELECT SUM(CASE WHEN trans_type = "SSI" THEN abs(stock_trans.qty) ELSE 0 END) as issue_qty,
								SUM(CASE WHEN trans_type = "PMR" THEN stock_trans.qty ELSE 0 END) as return_qty,
								GROUP_CONCAT(DISTINCT party_master.party_name) as supplier_name, GROUP_CONCAT(DISTINCT stock_trans.batch_no) as batch_no,
								child_ref_id,stock_trans.item_id, GROUP_CONCAT(DISTINCT stock_trans.location_id) as location_id
								FROM stock_trans
								LEFT JOIN batch_history ON batch_history.batch_no = stock_trans.batch_no 
								LEFT JOIN party_master ON batch_history.party_id = party_master.id 
								WHERE stock_trans.is_delete=0 AND stock_trans.trans_type IN("SSI","PMR") '.$customWhere.' GROUP BY stock_trans.child_ref_id,stock_trans.item_id) stock_trans']="stock_trans.child_ref_id = prc_bom.prc_id AND prc_bom.item_id = stock_trans.item_id";
		}


        if(!empty($param['prc_id'])){ $data['where']['prc_bom.prc_id'] = $param['prc_id']; }
        if(isset($param['process_id'])){ $data['where']['prc_bom.process_id'] = $param['process_id']; }
        if(!empty($param['item_id'])){ $data['where']['prc_bom.item_id'] = $param['item_id']; }
        if(!empty($param['bom_group'])){ $data['where']['prc_bom.bom_group'] = $param['bom_group']; }

        if(!empty($param['single_row'])){
            $result = $this->row($data);
        }else{
            $result = $this->rows($data);
        }
        return $result;
    }

	public function getCuttingBatchDetail($param = []){
		$queryData['tableName'] = 'prc_master';
		$queryData['select'] = 'prc_bom.ppc_qty,prc_bom.item_id,item_master.item_name,item_category.category_name,prc_detail.cut_weight,material_master.material_grade';
		$queryData['leftJoin']['prc_detail'] = 'prc_detail.prc_id = prc_master.id';
		$queryData['leftJoin']['prc_bom'] = 'prc_master.id = prc_bom.prc_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = prc_bom.item_id';
        $queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
		$queryData['leftJoin']['item_category'] = 'item_category.id = item_master.category_id';

		if(!empty($param['production_data'])){
            $queryData['select'] .= ',IFNULL(prc_log.production_qty,0) as production_qty,(SUM(IFNULL(prc_log.total_wt,0))/SUM(IFNULL(prc_log.production_qty,0))) AS avg_wt';
            $queryData['leftJoin']['(SELECT SUM(qty) as production_qty, SUM(wt_nos*qty) as total_wt,prc_id 
                                    FROM prc_log
                                    WHERE  is_delete = 0  
                                    GROUP BY prc_id
                                ) prc_log'] = "prc_master.id = prc_log.prc_id";
        }
		if(!empty($param['prc_number'])){
			$queryData['where_in']['prc_master.prc_number'] = ("'".implode("','",explode(",",$param['prc_number']))."'");
		}
		if(!empty($param['heat_no'])){
			$queryData['where']['prc_master.batch_no'] = $param['heat_no'];
		}
		if(!empty($param['single_row'])){
			$result = $this->row($queryData);
		}else{
			$result = $this->rows($queryData);
		}
		// $this->printQuery();
		return $result;
	}
	
	public function savePrcMaterial($data){
		try {
			$this->db->trans_begin();
			$this->trash('prc_bom',['prc_id'=>$data['prc_id']]);
			foreach($data['item_id'] as $key=>$item_id){
				$bomData = [
					'id'=>$data['id'][$key],
					'prc_id'=>$data['prc_id'],
					'item_id'=>$data['item_id'][$key],
					'ppc_qty'=>$data['ppc_qty'][$key],
					'process_id'=>$data['process_id'][$key],
					'bom_group'=>$data['bom_group'][$key],
					'multi_heat'=>$data['multi_heat'][$key],
					'batch_no'=>'',
					'is_delete'=>0
				];
				$result = $this->store('prc_bom',$bomData);

				if(!empty($data['prc_type']) && $data['prc_type'] == 2){
					$data['req_id'] = '';
					$data['issued_to'] = $this->loginId;
					$data['created_by'] = $this->loginId;
					$data['item_id'] = $item_id;
					$this->store->saveIssueRequisition($data);
				}
			}			

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function checkIssueMaterialForPrc($data){
		try {
			$this->db->trans_begin();
			$prcData = $this->getPRC(['id'=>$data['prc_id']]);
			$processArray = explode(',',$prcData->process_ids);
			$bomUpdate = [];$process_id = $data['process_id'];
			if($data['process_id'] == $processArray[0] && $prcData->cutting_flow == 2 && $prcData->prc_type == 1){ $process_id = '0,'.$data['process_id'];}
			$bomData = $this->getPrcBomData(['prc_id'=>$data['prc_id'],'process_id'=> $process_id]);

			foreach($bomData as $row){
				$required_qty = $row->ppc_qty * $data['check_qty'];
				$location_id ="";
				if($prcData->item_id == $row->item_id){$location_id = $this->CUT_STORE->id;}
				$pram = ['child_ref_id'=>$data['prc_id'],'item_id'=> $row->item_id,'entry_type'=>'"SSI","PMR"','location_id'=>$location_id,'single_row'=>1];
				
				$issueData = $this->itemStock->getItemStockBatchWise($pram);
				
				if(empty($issueData->qty)){
					return ['status'=>0,'message'=>'Please Issue Material'];
				}elseif(round($required_qty,3) > round(abs($issueData->qty),3)){
					return ['status'=>0,'message'=>'Material Not Available'.round($required_qty,3) .'>'. round($issueData->qty,3)];
				}else{
					$this->store('prc_bom',[ 'id'=>$row->id,'batch_no'=>$issueData->ref_batch] );
				}
			}
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'bomUpdate'=>$bomUpdate];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function storeReturnedMaterial($data){
		try {
			$this->db->trans_begin();
			
			$stockData = [
                'id'=>'',
                'trans_type'=>"PMR",
                'trans_date'=>date("Y-m-d"),
                'ref_no'=>$data['prc_number'],
                'main_ref_id'=>$data['prc_bom_id'],
                'child_ref_id'=>$data['prc_id'],
                'location_id '=>$data['location_id'],
                'batch_no'=>$data['batch_no'],
                'item_id'=>$data['item_id'],
                'p_or_m'=>1,
                'qty'=>$data['qty'],
                'remark'=>$data['remark'],
                'created_by'=>$this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $result = $this->store("stock_trans",$stockData);

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function deleteReturn($id){
		try {
			$this->db->trans_begin();
			$returnData = $this->itemStock->getStockTrans(['id'=>$id]);
			$stock = $this->itemStock->getItemStockBatchWise(['location_id'=>$returnData->location_id,'batch_no'=>$returnData->batch_no,'item_id'=> $returnData->item_id,'single_row'=>1]);
			if($returnData->qty > $stock->qty){ 
				return ['status'=>0,'message'=>'You can not delete this record']; 
			}

			$result = $this->remove('stock_trans',['id'=>$id]);

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function changePrcStatus($param){
		try {
			$this->db->trans_begin();
			$queryData['tableName'] = "prc_master";
			$queryData['select'] = 'prc_master.prc_qty,prc_master.item_id,IFNULL(rejection_log.rej_qty,0) as rej_qty,IFNULL(prc_movement.stored_qty,0) as stored_qty';
			$queryData['leftJoin']['(SELECT SUM(qty) as rej_qty,prc_id FROM rejection_log WHERE decision_type = 1 AND source="MFG" AND is_delete = 0 GROUP BY prc_id) rejection_log'] = "prc_master.id = rejection_log.prc_id";
			$queryData['leftJoin']['(SELECT SUM(qty) as stored_qty,prc_id FROM prc_movement WHERE next_process_id = 0 AND is_delete = 0 GROUP BY prc_id) prc_movement'] = "prc_master.id = prc_movement.prc_id";
			$queryData['where']['prc_master.id'] = $param['prc_id'];
			$prcData = $this->row($queryData); 

			$prdProcessData = $this->item->getProductProcessList(['item_id'=>$prcData->item_id,'max_output_qty'=>1,'single_row'=>1]);
			$status = 2;
			if(($prcData->rej_qty + $prcData->stored_qty) >= ($prcData->prc_qty * $prdProcessData->max_output_qty)){
				$status = 3;
			}
			$this->edit('prc_detail',['prc_id'=>$param['prc_id']],['stored_qty'=>$prcData->stored_qty,'rej_qty'=>$prcData->rej_qty]);
			$result = $this->store("prc_master",['id'=>$param['prc_id'],'status'=>$status]);

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function getSopDTRows($data){
        $data['tableName'] = "prc_master";
		
		$data['select'] = "prc_master.id,prc_master.prc_type, prc_master.prc_number,prc_master.item_id, im.mfg_type, prc_master.batch_no, DATE_FORMAT(prc_master.prc_date,'%d-%m-%Y') as prc_date,DATE_FORMAT(prc_master.target_date,'%d-%m-%Y') as 	target_date, prc_master.status, prc_master.prc_qty,pd.remark,prc_master.mfg_route";
		$data['select'] .= ", IFNULL(im.item_name,'') as item_name,IFNULL(im.item_code,'') as item_code, IFNULL(pd.remark,'') as job_instruction, (CASE WHEN prc_master.party_id > 0 THEN IFNULL(pm.party_name,'') ELSE 'SELF' END) as party_name, IFNULL(so_master.trans_number,'') as so_number,so_master.doc_no,so_master.doc_date,so_master.trans_date as so_date,im.mfg_type";
        
        $data['leftJoin']['item_master im'] = "im.id = prc_master.item_id";
        $data['leftJoin']['prc_detail pd'] = "pd.prc_id = prc_master.id";
		$data['leftJoin']['party_master pm'] = "pm.id = prc_master.party_id";
        $data['leftJoin']['so_trans'] = "so_trans.id = prc_master.so_trans_id";
        $data['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id";
		
		$data['where']['prc_master.prc_type'] = 1;
		if(!empty($data['status'])){ $data['where_in']['prc_master.status'] = $data['status']; }
       
		$data['order_by']['prc_master.prc_date'] = 'DESC';
		$data['order_by']['prc_master.id'] = 'DESC';
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "im.mfg_type";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "DATE_FORMAT(prc_master.prc_date,'%d-%m-%Y')";
        $data['searchCol'][] = "IF(prc_master.party_id > 0,pm.party_name,'Self')";
        $data['searchCol'][] = "so_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(so_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "so_master.doc_no";
        $data['searchCol'][] = "CONCAT(im.item_code,' ',im.item_name)";
        $data['searchCol'][] = "prc_master.prc_qty";
        $data['searchCol'][] = "DATE_FORMAT(prc_master.target_date,'%d-%m-%Y')";
        $data['searchCol'][] = "pd.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }

	public function changePRCStage($data){
        try{
            $this->db->trans_begin();

            $result = $this->store('prc_master',$data,'PRC');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
	
	public function getPRCProcess($param){
		$data['tableName'] = "prc_process";
		$data['select'] = "prc_process.*";
		if(!empty($param['prc_id'])) { $data['where']['prc_id'] = $param['prc_id']; }
		if(isset($param['current_process_id'])) { $data['where']['current_process_id'] = $param['current_process_id']; }
		return $this->row($data);
	}

	public function savePrcQty($data){
        try {
            $this->db->trans_begin();

            $operation = ($data['log_type'] == 1) ? '+' : '-';
            $result = $this->store('prc_update', $data, 'PRC Qty');

			// $prcProcessData = $this->getPRCProcess(['prc_id'=>$data['prc_id'], 'current_process_id'=>0]);
			$prcData = $this->getPRC(['id'=>$data['prc_id']]);
			/* Update Movement Qty */
            $setData = array();
            $setData['tableName'] = 'prc_movement';
            $setData['where']['prc_id'] = $data['prc_id'];
            $setData['where']['process_id'] = 0;
            $setData['set']['qty'] = 'qty,' . $operation . $data['qty'];
            $this->setValue($setData);

			$setData = array();
            $setData['tableName'] = 'prc_accept_log';
            $setData['where']['prc_id'] = $data['prc_id'];
            $setData['where']['accepted_process_id'] =$prcData->first_process;
            $setData['set']['accepted_qty'] = 'accepted_qty,' . $operation . $data['qty'];
            $this->setValue($setData);

			/* Update PRC Qty */
            $updateQuery = array();
            $updateQuery['tableName'] = 'prc_master';
            $updateQuery['where']['id'] = $data['prc_id'];
            $updateQuery['set']['prc_qty'] = 'prc_qty,' . $operation . $data['qty'];
            $this->setValue($updateQuery);
			
			$this->changePrcStatus(['prc_id'=>$data['prc_id']]);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

	public function getPRCUpdateLogData($param){
		$data['tableName'] = "prc_update";
		if(!empty($param['prc_id'])) { $data['where']['prc_id'] = $param['prc_id']; }
		if(!empty($param['id'])) { $data['where']['id'] = $param['id']; }
		
		if(!empty($param['single_row'])):
			return $this->row($data);
		else:
			return $this->rows($data);
		endif;
	}

    public function deletePrcUpdateQty($id){
        try {
            $this->db->trans_begin();

			$logData = $this->getPrcUpdateLogData(['id'=>$id,'single_row'=>1]);
            $operation = ($logData->log_type == 1) ? '-' : '+';
            $result = $this->trash('prc_update', ['id' => $id], 'PRC Qty');

			// $prcProcessData = $this->getPRCProcess(['prc_id'=>$logData->prc_id, 'current_process_id'=>0]);
			$prcData = $this->getPRC(['id'=>$logData->prc_id]);
			/* Update Log Qty */
            $setData = array();
            $setData['tableName'] = 'prc_movement';
            $setData['where']['prc_id'] = $logData->prc_id;
            $setData['where']['process_id'] = 0;
            $setData['set']['qty'] = 'qty,' . $operation . $logData->qty;
            $this->setValue($setData);

			$setData = array();
            $setData['tableName'] = 'prc_accept_log';
            $setData['where']['prc_id'] = $logData->prc_id;
            $setData['where']['accepted_process_id'] =$prcData->first_process;
            $setData['set']['accepted_qty'] = 'accepted_qty,' . $operation . $logData->qty;
            $this->setValue($setData);

			/* Update PRC Qty */
            $updateQuery = array();
            $updateQuery['tableName'] = 'prc_master';
            $updateQuery['where']['id'] = $logData->prc_id;
            $updateQuery['set']['prc_qty'] = 'prc_qty,' . $operation . $logData->qty;
            $this->setValue($updateQuery);			

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

	public function getShortageDtRows($data){
		$style = 'style="margin:0px;padding:0px"';
        $data['tableName'] = "so_trans";
		$data['select'] = "party_master.party_name,so_master.party_id,
							GROUP_CONCAT(party_master.party_name SEPARATOR '<hr ".$style.">') AS party_name,
							GROUP_CONCAT(so_master.trans_number SEPARATOR '<hr ".$style.">') AS so_number,
							so_trans.item_id,item_master.item_name,item_master.item_code,
							GROUP_CONCAT(so_trans.qty SEPARATOR '<hr ".$style.">') AS qty,
							IFNULL(SUM(so_trans.qty),0) AS total_qty,
							SUM(IFNULL(so_trans.dispatch_qty,0)) AS total_dispatch_qty,
							IFNULL(stock.prd_finish_Stock,0) AS prd_finish_Stock,
							IFNULL(stock.rtd_Stock,0) AS rtd_Stock,
							IFNULL(prc_master.wip_qty, 0) as wip_qty";

        $data['leftJoin']['item_master'] = "item_master.id = so_trans.item_id";
        $data['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id ";
        $data['leftJoin']['party_master'] = "party_master.id = so_master.party_id";
		$data['leftJoin']['(SELECT SUM(prc_qty - (prc_detail.rej_qty + prc_detail.stored_qty)) as wip_qty,item_id  
								FROM prc_master 
								JOIN prc_detail ON prc_detail.prc_id = prc_master.id
								WHERE 
								prc_master.is_delete = 0 
								AND prc_type = 1 
								AND prc_master.status IN(1,2) 
								GROUP BY item_id
							) prc_master'] = 'prc_master.item_id = item_master.id';
		$data['leftJoin']['(SELECT SUM(CASE WHEN location_id IN('.$this->PACKING_STORE->id.','.$this->SEMI_STORE->id.') THEN (qty*p_or_m) ELSE 0 END) AS prd_finish_Stock,
								   SUM(CASE WHEN location_id = '.$this->RTD_STORE->id.' THEN (qty*p_or_m) ELSE 0 END) AS rtd_Stock,item_id 
								   FROM stock_trans 
								   WHERE is_delete = 0 
								   AND location_id IN('.$this->PACKING_STORE->id.','.$this->RTD_STORE->id.','.$this->SEMI_STORE->id.') 
								   GROUP BY item_id
							) stock'] = 'stock.item_id = item_master.id';

		$data['group_by'][]='so_trans.item_id';
		$data['having'][]='(total_qty - total_dispatch_qty) > 0';
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "so_master.trans_number";
        $data['searchCol'][] = "so_trans.qty";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }

	public function getLogDTRows($data){
		$data['tableName'] = 'prc_movement';
		$data['select'] = 'prc_movement.process_id AS process_from,fromProcess.process_name AS from_process_name,prc_master.id AS prc_id,process_master.id AS process_id,process_master.process_name,item_master.item_code,item_master.item_name,(SUM(qty) - IFNULL(prc_accept_log.short_qty,0)) AS inward_qty,prc_master.prc_number,prc_master.prc_date';
		$data['select'] .= ",IFNULL(prc_log.ok_qty,0) as ok_qty, IFNULL(prc_log.rej_qty,0) as rej_qty, IFNULL(prc_log.rw_qty,0) as rw_qty,IFNULL(prc_log.rej_found,0) as rej_found "; // PRC LOG DATA
		$data['select'] .= ",(IFNULL(current_movement.movement_qty,0)) as movement_qty"; // MOVEMENT DATA
		$data['select'] .= ",(IFNULL(prc_accept_log.accepted_qty,0)) as accepted_qty"; // ACCEPT DATA
		$data['select'] .= ",IFNULL(rejection_log.review_qty,0) AS review_qty"; // REVIEW DATA

		$data['leftJoin']['prc_master'] = 'prc_master.id = prc_movement.prc_id';
		$data['leftJoin']['item_master'] = 'prc_master.item_id = item_master.id';
		$data['leftJoin']['process_master'] = 'process_master.id = prc_movement.next_process_id';
		$data['leftJoin']['process_master fromProcess'] = 'fromProcess.id = prc_movement.process_id';

		$data['leftJoin']['(SELECT SUM(prc_log.qty) as ok_qty, SUM((prc_log.rej_qty)) as rej_qty, SUM((prc_log.rw_qty)) as rw_qty, SUM(prc_log.rej_found) as rej_found,process_id,prc_id,process_from FROM prc_log WHERE is_delete = 0 AND trans_type='.$data['move_type'].' GROUP BY prc_id,process_from,process_id) AS prc_log'] = 'prc_log.process_id = prc_movement.next_process_id AND prc_log.prc_id = prc_movement.prc_id AND prc_log.process_from = prc_movement.process_id'; // LOG JOIN
		
		$data['leftJoin']['(SELECT SUM(prc_movement.qty) as movement_qty,process_id,prc_id,process_from FROM prc_movement WHERE prc_movement.is_delete=0 AND prc_movement.move_from = '.$data['move_type'].' AND ref_id = 0 GROUP BY prc_id,process_from,process_id) current_movement']="current_movement.prc_id = prc_master.id AND current_movement.process_id = prc_movement.next_process_id AND current_movement.process_from = prc_movement.process_id"; // MOVEMENT JOIN

		$data['leftJoin']['(SELECT SUM(prc_accept_log.accepted_qty) as accepted_qty,SUM(prc_accept_log.short_qty) as short_qty,accepted_process_id,prc_id,process_from FROM prc_accept_log WHERE prc_accept_log.is_delete=0 AND trans_type='.$data['move_type'].' GROUP BY prc_id,process_from,accepted_process_id) prc_accept_log']="prc_accept_log.accepted_process_id = prc_movement.next_process_id AND prc_accept_log.process_from = prc_movement.process_id AND prc_accept_log.prc_id = prc_master.id"; // ACCEPT JOIN

		$data['leftJoin']['(SELECT SUM(rejection_log.qty) as review_qty,rejection_log.log_id,rejection_log.prc_id,prc_log.process_id,prc_log.process_from FROM rejection_log LEFT JOIN prc_log ON prc_log.id = rejection_log.log_id WHERE rejection_log.is_delete = 0 AND source="MFG" AND trans_type='.$data['move_type'].' GROUP BY prc_log.prc_id,prc_log.process_from,prc_log.process_id) rejection_log'] = "rejection_log.prc_id = prc_master.id AND rejection_log.process_id = prc_movement.next_process_id AND rejection_log.process_from = prc_movement.process_id"; //REJECTION REVIEW JOIN

		$data['where_in']['prc_movement.next_process_id'] = $data['process_id'];
		$data['where']['prc_master.status'] = 2;
		$data['where']['prc_master.prc_type'] = 1;
		$data['where']['prc_movement.move_type'] = $data['move_type'];

		$data['group_by'][]="prc_master.id,prc_movement.process_id,prc_movement.next_process_id";
		
		$data['having'][] = '((inward_qty - accepted_qty) > 0) OR (accepted_qty - (ok_qty+rej_found)) > 0 OR (ok_qty - movement_qty) > 0';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "prc_master.prc_date";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
	}

	public function getSopProcessList(){
		$queryData['tableName'] = "process_master";

		$queryData['select'] = 'process_master.id,process_master.process_name'
		;
		$queryData['select'] .= ",IFNULL(prcLog.ok_qty,0) as ok_qty, IFNULL(prcLog.rej_qty,0) as rej_qty, IFNULL(prcLog.rw_qty,0) as rw_qty,IFNULL(prcLog.rej_found,0) as rej_found,IFNULL(rejection_log.review_qty,0) as review_qty ";

		$queryData['leftJoin']['(SELECT SUM(prc_log.qty) as ok_qty, SUM((prc_log.rej_qty)) as rej_qty, SUM((prc_log.rw_qty)) as rw_qty, SUM(prc_log.rej_found) as rej_found,process_id,prc_id FROM prc_log JOIN prc_master ON prc_master.id = prc_log.prc_id  WHERE prc_log.is_delete = 0 AND prc_master.status = 2 AND prc_master.prc_type = 1 GROUP BY process_id) prcLog'] =  "prcLog.process_id = process_master.id";

		$queryData['leftJoin']['(SELECT SUM(rejection_log.qty) as review_qty,rejection_log.log_id,rejection_log.prc_id,prc_log.process_id FROM rejection_log LEFT JOIN prc_log ON prc_log.id = rejection_log.log_id JOIN prc_master ON prc_master.id = rejection_log.prc_id  WHERE rejection_log.is_delete = 0 AND source="MFG" AND prc_master.status = 2 GROUP BY prc_log.process_id) rejection_log'] = " rejection_log.process_id = process_master.id";

		$queryData['select'] .= ",(IFNULL(prc_movement.movement_qty,0)-IFNULL(current_accept_log.short_qty,0)) as movement_qty,IFNULL(current_accept_log.short_qty,0) as short_qty";

		$queryData['leftJoin']['(SELECT SUM(prc_movement.qty) as movement_qty,process_id,prc_id FROM prc_movement JOIN prc_master ON prc_master.id = prc_movement.prc_id WHERE prc_movement.is_delete=0 AND prc_master.status = 2 AND ref_id = 0 GROUP BY process_id) prc_movement']="prc_movement.process_id = process_master.id";
		
		$queryData['leftJoin']['(SELECT SUM(IFNULL(prc_accept_log.accepted_qty,0)) as accepted_qty,SUM(IFNULL(prc_accept_log.short_qty,0)) as short_qty,accepted_process_id,prc_id FROM prc_accept_log JOIN prc_master ON prc_master.id = prc_accept_log.prc_id WHERE prc_accept_log.is_delete=0 AND prc_master.status = 2 GROUP BY accepted_process_id) current_accept_log']="current_accept_log.accepted_process_id = process_master.id";
		
		$queryData['select'] .= ",((IFNULL(prevMovement.move_qty,0)-IFNULL(prc_accept_log.short_qty,0))) as in_qty,IFNULL(prc_accept_log.accepted_qty,0) as accepted_qty";

		$queryData['leftJoin']['(SELECT SUM(prc_accept_log.accepted_qty) as accepted_qty,SUM(prc_accept_log.short_qty) as short_qty,accepted_process_id,prc_id FROM prc_accept_log JOIN prc_master ON prc_master.id = prc_accept_log.prc_id WHERE prc_accept_log.is_delete=0 AND prc_master.status = 2 GROUP BY accepted_process_id) prc_accept_log']="prc_accept_log.accepted_process_id = process_master.id";

		$queryData['leftJoin']['(SELECT SUM(prc_movement.qty) as move_qty,prc_id,next_process_id FROM prc_movement JOIN prc_master ON prc_master.id = prc_movement.prc_id WHERE prc_movement.is_delete=0 AND send_to = 1 AND prc_master.status = 2 GROUP BY next_process_id) prevMovement']="prevMovement.next_process_id = process_master.id";

		$result = $this->rows($queryData); 
		return $result ;
	}

	public function getMfgStoreDTRows($data){
		$data['tableName'] = 'mfg_request';
		$data['select'] = 'mfg_request.*,item_master.item_name,item_master.item_code,reqFrom.process_name AS req_from_process,reqTo.process_name AS req_to_process ,employee_master.emp_name AS request_by,IFNULL(prc_movement.issue_qty,0) AS issue_qty';
		
		$data['leftJoin']['item_master'] = 'mfg_request.item_id = item_master.id';
		$data['leftJoin']['process_master reqFrom'] = 'reqFrom.id = mfg_request.req_from';
		$data['leftJoin']['process_master reqTo'] = 'reqTo.id = mfg_request.req_to';
		$data['leftJoin']['employee_master'] = 'employee_master.id = mfg_request.created_by';

		$data['leftJoin']['(SELECT SUM(prc_movement.qty) as issue_qty,process_id,prc_id,request_id FROM prc_movement WHERE is_delete = 0  GROUP BY request_id) AS prc_movement'] = 'prc_movement.request_id = mfg_request.id'; // Movement Data
		
		if($data['record_type'] == 'DEMAND'){
			$data['where']['mfg_request.req_to'] = $data['process_id'];
		}else{
			$data['where']['mfg_request.req_from'] = $data['process_id'];
		}
		
		$data['having'][] = '(mfg_request.qty - issue_qty) > 0';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "mfg_request.trans_number";
        $data['searchCol'][] = "mfg_request.trans_date";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "mfg_request.qty";
        $data['searchCol'][] = "prc_movement.issue_qty";
        $data['searchCol'][] = "";
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
		// print_r($this->db->last_query());
        return $result;
	}

	public function getNextReqNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'mfg_request';
        $queryData['select'] = "MAX(trans_no ) as trans_no ";
				
		$queryData['where']['mfg_request.trans_date >='] = $this->startYearDate;
		$queryData['where']['mfg_request.trans_date <='] = $this->endYearDate;

		$trans_no = $this->specificRow($queryData)->trans_no;
		$trans_no = $trans_no + 1;
		return $trans_no;
	}

	public function saveMfgRequest($param){ 
		try {
			$this->db->trans_begin();
            $result = $this->store('mfg_request', $param);
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Request sent successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function getRequestData($param = []){
		$data['tableName'] = 'mfg_request';
		$data['select'] = 'mfg_request.*,item_master.item_name,item_master.item_code,reqFrom.process_name AS req_from_process,reqTo.process_name AS req_to_process ,employee_master.emp_name AS request_by';
		
		$data['leftJoin']['item_master'] = 'mfg_request.item_id = item_master.id';
		$data['leftJoin']['process_master reqFrom'] = 'reqFrom.id = mfg_request.req_from';
		$data['leftJoin']['process_master reqTo'] = 'reqTo.id = mfg_request.req_to';
		$data['leftJoin']['employee_master'] = 'employee_master.id = mfg_request.created_by';

		if(!empty($param['issueData'])){
			$data['select'] .= ',IFNULL(prc_movement.issue_qty,0) AS issue_qty';
			$data['leftJoin']['(SELECT SUM(prc_movement.qty) as issue_qty,process_id,prc_id,request_id FROM prc_movement WHERE is_delete = 0  GROUP BY request_id) AS prc_movement'] = 'prc_movement.request_id = mfg_request.id'; // Movement Data
		}
		
		
		if(!empty($param['req_to'])){ $data['where']['mfg_request.req_to'] = $param['req_to'];}
		if(!empty($param['req_from'])){ $data['where']['mfg_request.req_from'] = $param['req_from'];}
		if(!empty($param['item_id'])){ $data['where']['mfg_request.item_id'] = $param['item_id'];}
		if(!empty($param['id'])){ $data['where']['mfg_request.id'] = $param['id'];}
		if(!empty($param['single_row'])){
			return $this->row($data);
		}else{
			return $this->rows($data);
		}
		
	}
	
	public function deleteMfgRequest($data){
        try{
            $this->db->trans_begin();

            $checkData['columnName'] = ["request_id"];
            $checkData['value'] = $data['id'];
            $checkUsed = $this->checkUsage($checkData);
            
            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Request in use. you cannot delete it.'];
            endif;

            $result = $this->trash('mfg_request',['id'=>$data['id']],'Request');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

	public function saveIssuedItem($data){
		try {
			$this->db->trans_begin();
            foreach($data['qty'] as $key=>$qty){
				$movementQty = [
					'id'=>'',
					'request_id' => $data['request_id'],
					'move_from' => $data['move_from'][$key],
					'move_type' => $data['move_type'][$key],
					'process_from' => $data['process_from'][$key],
					'prc_id' => $data['prc_id'][$key],
					'process_id' => $data['process_id'],
					'next_process_id' =>  $data['next_process_id'],
					'trans_date' => $data['trans_date'],
					'qty' => $qty,
				];
				$this->sop->savePRCMovement($movementQty);
			}
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Request sent successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function getPendingMoveDTRows($data){
		$data['tableName'] = 'prc_log';
		$data['select'] = 'prc_log.*,fromProcess.process_name AS from_process_name,prc_master.id AS prc_id,process_master.id AS process_id,process_master.process_name,item_master.item_code,item_master.item_name,prc_master.prc_number,prc_master.prc_date';
		$data['select'] .= ",SUM(prc_log.qty) as ok_qty "; // PRC LOG DATA
		$data['select'] .= ",(IFNULL(current_movement.movement_qty,0)) as movement_qty"; // MOVEMENT DATA

		$data['leftJoin']['prc_master'] = 'prc_master.id = prc_log.prc_id';
		$data['leftJoin']['item_master'] = 'prc_master.item_id = item_master.id';
		$data['leftJoin']['process_master'] = 'process_master.id = prc_log.process_id';
		$data['leftJoin']['process_master fromProcess'] = 'fromProcess.id = prc_log.process_from';
		
		$data['leftJoin']['(SELECT SUM(prc_movement.qty) as movement_qty,process_id,prc_id,process_from,move_from FROM prc_movement WHERE prc_movement.is_delete=0  AND ref_id = 0 GROUP BY prc_id,process_from,process_id,move_from) current_movement']="current_movement.prc_id = prc_master.id AND current_movement.process_id = prc_log.process_id AND current_movement.process_from = prc_log.process_from AND current_movement.move_from = prc_log.trans_type"; // MOVEMENT JOIN

	

		$data['where_in']['prc_log.process_id'] = $data['process_id'];
		$data['where']['prc_master.status'] = 2;

		$data['group_by'][]="prc_master.id,prc_log.process_id,prc_log.process_from,prc_log.trans_type";
		
		$data['having'][] = '(ok_qty - movement_qty) > 0';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "prc_master.prc_date";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "fromProcess.process_name";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
	}

	/***Final Inspection */
		public function getFinalInspectData($data) {
			$queryData = array();
			$queryData['tableName'] = 'production_inspection';
			$queryData['select'] = "production_inspection.id, production_inspection.report_type, production_inspection.ref_id, production_inspection.insp_date, production_inspection.insp_time, production_inspection.rev_no, production_inspection.prc_id, production_inspection.item_id, production_inspection.process_id, production_inspection.machine_id, production_inspection.operator_id, production_inspection.sampling_qty, production_inspection.param_count, production_inspection.parameter_ids, production_inspection.observation_sample, production_inspection.trans_no, production_inspection.trans_number,prc_master.prc_number,item_master.item_name,employee_master.emp_name,prc_log.qty AS ok_qty,prc_log.rej_found,ecn_master.rev_no, ecn_master.cust_rev_no, ecn_master.drw_no,material_master.material_grade,item_master.item_code,party_master.party_code,party_master.party_name,item_master.mfg_type,item_master.wt_pcs";
			$queryData['leftJoin']['prc_master'] = "prc_master.id = production_inspection.prc_id";
			$queryData['leftJoin']['party_master'] = "party_master.id = prc_master.party_id";
			$queryData['leftJoin']['item_master'] = "item_master.id = production_inspection.item_id";
			$queryData['leftJoin']['employee_master'] = "employee_master.id = production_inspection.created_by";
			$queryData['leftJoin']['prc_log'] = "prc_log.id = production_inspection.ref_id";
			$queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
			$queryData['leftJoin']['(SELECT drw_no,cust_rev_date,cust_rev_no,rev_no,rev_date,item_id FROM ecn_master WHERE is_delete=0 AND status=2 GROUP BY item_id ORDER BY ecn_date DESC) as ecn_master'] = "ecn_master.item_id = production_inspection.item_id";
			
			if(!empty($data['type']) && $data['type'] == 1){
				$queryData['select'] .= ' ,pdi_trans.so_trans_id, pdi_trans.qty as pdi_qty,trans_main.trans_number as inv_no, trans_main.trans_date as inv_date,trans_main.remark as inv_remark,so_master.doc_no,apr.emp_name as approved_by,pdi_trans.created_at as inspection_date, so_master.trans_number as so_trans_number,pdi_trans.rm_batch';
				$queryData['leftJoin']['pdi_trans'] = "pdi_trans.fir_id = production_inspection.id";
				$queryData['leftJoin']['employee_master apr'] = "apr.id = pdi_trans.created_by";
				$queryData['leftJoin']['trans_main'] = "trans_main.id = pdi_trans.so_id";
				$queryData['leftJoin']['so_master'] = "so_master.id = trans_main.ref_id";
			}
			
			if(!empty($data['rejection_review_data'])){
				$queryData['select'] .= ",IFNULL(rejection_log.review_qty,0) as review_qty,(production_inspection.rej_found-IFNULL(rejection_log.review_qty,0)) as pending_qty";
				$customWhere = !empty($data['id'])?' AND log_id ='.$data['id']:'';
				$queryData['leftJoin']['(SELECT SUM(qty) as review_qty,log_id,prc_id FROM rejection_log WHERE is_delete = 0 '.$customWhere.' AND rejection_log.source="FIR" GROUP BY rejection_log.log_id,prc_id) rejection_log'] = "rejection_log.log_id = production_inspection.id";
			}
			$queryData['where']['production_inspection.id'] = $data['id']; 
			return $this->row($queryData);
			
		}
		
		public function getFirNextNo($type = 2){
			$queryData['tableName'] = 'production_inspection';
			$queryData['select'] = "ifnull(MAX(trans_no + 1),1) as next_no";
			$queryData['where']['report_type'] = $type;
			$queryData['where']['insp_date >='] = $this->startYearDate;
			$queryData['where']['insp_date <='] = $this->endYearDate;
			return $this->row($queryData)->next_no;
		}
	/** End Inspection */

	public function getPrcListForMaterialIssue(){
		$result =  $this->db->query("SELECT id AS id, prc_number AS trans_number, '2' AS entry_type FROM prc_master WHERE is_delete = 0 AND prc_master.status In(1,2) UNION ALL SELECT id AS id, trans_number AS trans_number, '3' AS entry_type FROM die_production WHERE is_delete = 0 AND die_production.status = 2;")->result();	
		return $result;
	}

	public function getItemWiseWipOld($param=[]){
        $queryData = array();          
		$queryData['tableName'] = "product_process";
		$queryData['select'] = "IFNULL(cp.process_name,'') as current_process";
        $queryData['leftJoin']['prc_master'] = "prc_master.item_id = product_process.item_id AND prc_master.is_delete = 0";
        $queryData['leftJoin']['process_master cp'] = "cp.id = product_process.process_id";

		if(!empty($param['log_data'])){			
			$queryData['select'] .= ",IFNULL(SUM(prcLog.ok_qty),0) as ok_qty, IFNULL(SUM(prcLog.rej_qty),0) as rej_qty, IFNULL(SUM(prcLog.rw_qty),0) as rw_qty,IFNULL(SUM(prcLog.rej_found),0) as rej_found, IFNULL(SUM(prc_challan_request.ch_qty),0) as ch_qty,IFNULL(SUM(rejection_log.review_qty),0) as review_qty ";

			$queryData['leftJoin']['(SELECT SUM(prc_log.qty) as ok_qty, SUM((prc_log.rej_qty)) as rej_qty, SUM((prc_log.rw_qty)) as rw_qty, SUM(prc_log.rej_found) as rej_found,process_id,prc_id FROM prc_log WHERE is_delete = 0 GROUP BY process_id,prc_id) prcLog'] =  "prcLog.process_id = product_process.process_id AND prcLog.prc_id = prc_master.id";

			$queryData['leftJoin']['(SELECT SUM(prc_challan_request.qty) as ch_qty,process_id,prc_id FROM prc_challan_request WHERE is_delete = 0  GROUP BY process_id,prc_id) prc_challan_request'] =  "prc_challan_request.process_id = product_process.process_id AND prc_challan_request.prc_id = prc_master.id";

			$queryData['leftJoin']['(SELECT SUM(rejection_log.qty) as review_qty,rejection_log.log_id,rejection_log.prc_id,prc_log.process_id FROM rejection_log LEFT JOIN prc_log ON prc_log.id = rejection_log.log_id WHERE rejection_log.is_delete = 0 AND source="MFG" GROUP BY prc_log.process_id,prc_log.prc_id) rejection_log'] = "rejection_log.prc_id = prc_master.id AND rejection_log.process_id = product_process.process_id";
		}
		
		if(!empty($param['pending_accepted'])){			
			$queryData['select'] .= ",IFNULL(SUM(prc_accept_log.accepted_qty),0) as in_qty";

			$queryData['leftJoin']['(SELECT SUM(prc_accept_log.accepted_qty) as accepted_qty,SUM(prc_accept_log.short_qty) as short_qty, accepted_process_id, prc_id FROM prc_accept_log WHERE prc_accept_log.is_delete = 0 GROUP BY accepted_process_id,prc_id) prc_accept_log']="prc_accept_log.accepted_process_id = product_process.process_id AND prc_accept_log.prc_id = prc_master.id";
		}
		
		if(!empty($param['item_id'])){ $queryData['where']['product_process.item_id'] = $param['item_id']; }

		if(!empty($param['status'])){ $queryData['where_in']['prc_master.status'] = $param['status']; }
		
		if(!empty($param['prc_type'])){ $queryData['where']['prc_master.prc_type'] = $param['prc_type']; }
		
		if(!empty($param['group_by'])):
			$queryData['group_by'][] = $param['group_by'];
		endif;

		$queryData['order_by']['product_process.sequence'] = 'ASC';

		if(!empty($param['single_row'])){ $result = $this->row($queryData); }
		else{ $result = $this->rows($queryData); }

        return $result;  
    }
    
	public function getItemWiseWip($param=[]){
        $queryData = array();          
		$queryData['tableName'] = "product_process";
		$queryData['select'] = "IFNULL(cp.process_name,'') as current_process,product_process.output_qty";
        $queryData['leftJoin']['process_master cp'] = "cp.id = product_process.process_id";

		if(!empty($param['log_data'])){			
			$queryData['select'] .= ",IFNULL((prcLog.ok_qty),0) as ok_qty, IFNULL((prcLog.rej_qty),0) as rej_qty, IFNULL((prcLog.rw_qty),0) as rw_qty,IFNULL((prcLog.rej_found),0) as rej_found, IFNULL((prc_challan_request.ch_qty),0) as ch_qty,IFNULL((rejection_log.review_qty),0) as review_qty,IFNULL((prc_movement.movement_qty),0) as movement_qty";

			$queryData['leftJoin']['( SELECT SUM(prc_log.qty) as ok_qty, 
												SUM((prc_log.rej_qty)) as rej_qty, 
												SUM((prc_log.rw_qty)) as rw_qty, 
												SUM(prc_log.rej_found) as rej_found,
												process_id,prc_id,process_from
											FROM prc_log 
											LEFT JOIN prc_master ON prc_master.id = prc_log.prc_id
											WHERE prc_log.is_delete = 0 AND prc_master.item_id = '.$param['item_id'].'
											GROUP BY process_id,process_from
									) prcLog'] =  "prcLog.process_id = product_process.process_id ";

			$queryData['leftJoin']['(SELECT
											 SUM(prc_challan_request.qty) as ch_qty,process_id,prc_id 
										FROM prc_challan_request 
										LEFT JOIN  prc_master ON prc_master.id = prc_challan_request.prc_id
										WHERE prc_challan_request.is_delete = 0 AND prc_master.item_id = '.$param['item_id'].'
										GROUP BY process_id
									) prc_challan_request'] =  "prc_challan_request.process_id = product_process.process_id ";

			$queryData['leftJoin']['( SELECT SUM(rejection_log.qty) as review_qty,
											rejection_log.log_id,rejection_log.prc_id,prc_log.process_id 
											FROM rejection_log 
											LEFT JOIN prc_log ON prc_log.id = rejection_log.log_id 
											WHERE rejection_log.is_delete = 0 AND source="MFG" AND rejection_log.item_id = '.$param['item_id'].'
											GROUP BY prc_log.process_id
									) rejection_log'] = " rejection_log.process_id = product_process.process_id";

			$queryData['leftJoin']['(SELECT SUM(prc_movement.qty) as movement_qty
									,prc_id,process_id 
									FROM prc_movement 
									LEFT JOIN prc_master ON prc_master.id = prc_movement.prc_id
									WHERE prc_movement.is_delete=0 
									 AND prc_master.item_id =  '.$param['item_id'].'
									
									 GROUP BY process_id
								) prc_movement']="prc_movement.process_id = product_process.process_id";
		}
		
		if(!empty($param['pending_accepted'])){			
			$queryData['select'] .= ",IFNULL((prc_accept_log.accepted_qty),0) as in_qty";

			$queryData['leftJoin']['(SELECT SUM(prc_accept_log.accepted_qty) as accepted_qty,
											SUM(prc_accept_log.short_qty) as short_qty, 
											accepted_process_id, prc_id 
											FROM prc_accept_log 
											LEFT JOIN prc_master ON prc_master.id = prc_accept_log.prc_id
											WHERE prc_accept_log.is_delete = 0 AND prc_master.item_id = '.$param['item_id'].'
											GROUP BY accepted_process_id
									) prc_accept_log']="prc_accept_log.accepted_process_id = product_process.process_id";
			// $queryData['leftJoin']['(SELECT SUM(prc_movement.qty) as move_qty
			// 						,prc_id,next_process_id 
			// 						FROM prc_movement 
			// 						LEFT JOIN prc_master ON prc_master.id = prc_movement.prc_id
			// 						WHERE prc_movement.is_delete=0 
			// 						AND send_to = 1 AND prc_master.item_id =  '.$param['item_id'].'
									
			// 						 GROUP BY next_process_id
			// 					) prevMovement']="prevMovement.next_process_id = product_process.process_id";

		}
		
		if(!empty($param['item_id'])){ $queryData['where']['product_process.item_id'] = $param['item_id']; }
		
		if(!empty($param['group_by'])):
			$queryData['group_by'][] = $param['group_by'];
		endif;

		$queryData['order_by']['product_process.sequence'] = 'ASC';

		if(!empty($param['single_row'])){ $result = $this->row($queryData); }
		else{ $result = $this->rows($queryData); }

        return $result;  
    }
	
	public function getPrcListForMigrations(){
		$data['tableName'] = 'prc_master';
		$data['select'] = 'prc_master.id,prc_master.item_id,prc_master.prc_number';
		$data['customWhere'][] = 'prc_master.rev_no IS NULL';
		$result = $this->rows($data);
		
		print_r('<pre>');$i=0;
		if(!empty($result))
		{
			foreach($result as $row)
			{
				$rev =  $this->db->query("SELECT IFNULL(rev_no,'') as rev_no FROM ecn_master where status=2 AND is_delete=0 AND item_id = ".$row->item_id." ORDER BY rev_date DESC;")->row();
				
				if(!empty($rev->rev_no)){print_r($this->db->last_query());
					print_r(['id'=>$row->id, 'prc_number'=>$row->prc_number, 'item_id'=>$row->item_id, 'revNo'=>$rev->rev_no]);
					$this->edit('prc_master',['id'=>$row->id],['rev_no'=>$rev->rev_no]);
					print_r('<hr>');$i++;
				}
				
			}
			
		}
		echo "<h2>Total << ".$i." >> Records Migrated</h2>";
		return true;
	}

	public function printFinalInspection($id,$output_type = 'I',$type = 0){
		$data['firData'] = $firData = $this->sop->getFinalInspectData(['id'=>$id,'type'=>$type]);
        $data['paramData'] =  $this->item->getInspectionParameter(['item_id'=>$firData->item_id,'control_method'=>'FIR','rev_no'=>$firData->rev_no]);
        $data['companyData'] = $this->masterModel->getCompanyInfo();
		$data['type'] = $type;
		
		$logo=base_url('assets/images/logo.png'); 
		$data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('sopDesk/fir_pdf',$data,true);
		
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">FINAL INSPECTION REPORT</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">QA/F/02 (01/01.05.17)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">'.$firData->emp_name.'</td>
						</tr>
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
					
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='fir_'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(100,100));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,25,25,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		if($output_type == 'I'){
			$mpdf->Output($pdfFileName,$output_type);	
		}else{
			$filePath = realpath(APPPATH . '../assets/uploads/fir_reports/');
			$mpdf->Output($filePath.'/'.$pdfFileName, 'F');
			return $filePath.'/'.$pdfFileName;
		}	
	}

	public function updateRevNo($data){
		try {
			$this->db->trans_begin();
			
			$result = $this->edit("prc_master",['id'=>$data['prc_id']],['rev_no'=>(!empty($data['rev_no']) ? $data['rev_no'] : NULL)]);
			
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Revision No. updated Successfully.'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	/* Get Log For Die Run Count */
	public function getPRCLog($param){
		$data['tableName'] = "prc_log";
		$data['select'] = "SUM(prc_log.qty + prc_log.rej_found) as total_log";
		$data['leftJoin']['prc_master'] = "prc_master.id = prc_log.prc_id";
		$data['where']['prc_master.item_id'] = $param['item_id'];
		$data['customWhere'][] = 'FIND_IN_SET('.$param['die_master_ids'].',prc_log.die_master_ids) > 0';
		return $this->row($data);
	}

	public function getForgingPlanningData($param){
		$style = 'style="margin:0px;padding:0px"';
		$data['tableName'] = "so_trans";
		$data['select'] = 'so_trans.id,so_trans.qty,so_trans.dispatch_qty,so_trans.cod_date,so_master.doc_no,item_master.item_code,item_master.item_name';
		$data['select'] .= ",GROUP_CONCAT(CONCAT(rm.item_code,' ',rm.item_name) SEPARATOR '<hr ".$style.">') AS rm_name,";
		$data['select'] .= ',(SELECT MAX(wt_nos) FROM prc_log JOIN prc_master ON prc_master.id = prc_log.prc_id WHERE prc_master.prc_type = 2 AND prc_log.is_delete =0 AND prc_master.so_trans_id = so_trans.id)  AS cut_wt';
		$data['select']  .= ',(SELECT SUM(prc_qty) FROM prc_master WHERE prc_master.prc_type = 1 AND prc_master.is_delete = 0 AND prc_master.so_trans_id = so_trans.id ) AS prc_qty';
		$data['select'] .= ',IFNULL(prc.forging_qty,0) AS forging_qty,IFNULL(cutting_prc.cutting_qty,0) AS cutting_qty,IFNULL(cutting_prc.batch_no,"") AS batch_no';
		$data['leftJoin']['item_master'] = "item_master.id = so_trans.item_id";
        $data['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id ";
        $data['leftJoin']['item_kit'] = "item_kit.item_id = so_trans.item_id AND item_kit.is_delete = 0 AND item_kit.ref_id = 0 AND item_kit.packing_type = 0";
		$data['leftJoin']['item_master rm'] = "rm.id = item_kit.ref_item_id";
		$data['leftJoin']['(SELECT SUM(prc_log.qty) AS forging_qty,prc_master.so_trans_id 
							FROM prc_log 
							LEFT JOIN prc_master ON prc_master.id = prc_log.prc_id 
							LEFT JOIN process_master ON prc_log.process_id = process_master.id 
							WHERE process_master.process_type = 2 AND process_master.die_required = 1 AND prc_log.is_delete = 0 AND prc_master.prc_type = 1 GROUP BY prc_master.so_trans_id) prc'] = 'prc.so_trans_id = so_trans.id';
		$data['leftJoin']['(SELECT SUM(qty) AS cutting_qty,GROUP_CONCAT(DISTINCT prc_master.batch_no) AS batch_no,so_trans_id FROM prc_log JOIN prc_master ON prc_master.id = prc_log.prc_id WHERE prc_master.prc_type = 2 AND prc_log.is_delete =0 GROUP BY prc_master.so_trans_id) cutting_prc'] = 'cutting_prc.so_trans_id = so_trans.id';
		$data['having'][] = '(so_trans.qty - so_trans.dispatch_qty) > 0';
		$data['where']['so_trans.trans_status'] = 3;
		$data['where']['so_master.is_approve > '] = 0;
		$data['customWhere'][] = "so_master.trans_date BETWEEN '".$param['from_date']."' AND '".$param['to_date']."'";
        if(!empty($param['party_id'])){
            $data['where']['so_master.party_id'] = $param['party_id'];
		}
        if(!empty($param['item_id'])){
            $data['where']['so_trans.item_id'] = $param['item_id'];
		}
		
		$data['group_by'][] = 'so_trans.id';
	
		return $this->rows($data);
	}
}
?>