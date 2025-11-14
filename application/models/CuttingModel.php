<?php
class CuttingModel extends MasterModel{

    public function getNextCuttingNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'prc_master';
        $queryData['select'] = "MAX(prc_no ) as prc_no ";
		$queryData['where']['prc_type'] = 2;	
		$queryData['where']['prc_master.prc_date >='] = $this->startYearDate;
		$queryData['where']['prc_master.prc_date <='] = $this->endYearDate;

		$prc_no = $this->specificRow($queryData)->prc_no;
		$prc_no = (empty($this->last_prc_no))?($prc_no + 1):$prc_no;
		return $prc_no;
    }

    public function getDTRows($data){
        $data['tableName'] = "prc_master";
        
        $data['select'] = "prc_master.id, prc_master.prc_number, prc_master.item_id, DATE_FORMAT(prc_master.prc_date,'%d-%m-%Y') as prc_date, prc_master.batch_no, DATE_FORMAT(prc_master.target_date,'%d-%m-%Y') as target_date, prc_master.status, prc_master.prc_qty, , IFNULL(so_master.trans_number,'') as so_number, so_master.trans_date as so_date";
        $data['select'] .= ", IFNULL(im.item_name,'') as item_name,im.uom, IFNULL(pd.remark,'') as job_instruction,pd.cutting_length,pd.cutting_dia,pd.cut_weight, prc_master.prc_type, pd.cutting_type";
        
        $data['leftJoin']['item_master im'] = "im.id = prc_master.item_id";
        $data['leftJoin']['prc_detail pd'] = "pd.prc_id = prc_master.id";
        $data['leftJoin']['so_trans'] = "so_trans.id = prc_master.so_trans_id";
        $data['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id";
		
        $data['select'] .= ',IFNULL(prc_log.production_qty,0) as production_qty';
        $data['leftJoin']['(SELECT SUM(qty) as production_qty,prc_id FROM prc_log WHERE  is_delete = 0  GROUP BY prc_id) prc_log'] = "prc_master.id = prc_log.prc_id";
        
        $data['where']['prc_master.prc_type'] = 2;
        if(!empty($data['status'])){ $data['where_in']['prc_master.status'] = $data['status']; }

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "DATE_FORMAT(prc_master.prc_date,'%d-%m-%Y')";
        $data['searchCol'][] = "so_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(so_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT(im.item_code,' ',im.item_name)";
        $data['searchCol'][] = "prc_master.prc_qty";
        $data['searchCol'][] = "prc_log.production_qty";
        $data['searchCol'][] = "pd.cutting_length";
        $data['searchCol'][] = "pd.cutting_dia";
        $data['searchCol'][] = "pd.cut_weight";
        $data['searchCol'][] = "pd.cutting_type";
        $data['searchCol'][] = "pd.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        $result = $this->pagingRows($data);
        return $result;
    }

    public function saveCutting($param){ 
		try {
			$this->db->trans_begin();
			if(empty($param['masterData']['id'])){
				$prc_no = $this->getNextCuttingNo();
				$prc_prefix = 'CUT/'.getYearPrefix('SHORT_YEAR').'/';
				$param['masterData']['prc_number'] = $prc_prefix.$prc_no;
			}
            $result = $this->store('prc_master', $param['masterData'], 'PRC');
			
			if(!empty($result['id']))
			{
				$param['prcDetail']['prc_id'] = $result['id'];
				$param['prcDetail']['id'] = (!empty($param['prcDetail']['id'])?$param['prcDetail']['id']:'');
				$prcDetail = $this->store('prc_detail', $param['prcDetail'], 'PRC Detail');
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

    public function delete($data){
        try{
            $this->db->trans_begin();
            //Delete Issued Material
            $this->trash("issue_register",['prc_id'=>$data['id']]);
            $this->trash("stock_trans",['child_ref_id'=>$data['id'],'trans_type'=>'SSI']);

            $result = $this->trash('prc_detail',['prc_id'=>$data['id']],'PRC Detail');
            $result = $this->trash('prc_bom',['prc_id'=>$data['id']],'PRC Bom');
            $result = $this->trash('prc_master',['id'=>$data['id']],'PRC');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function saveMaterial($data){
        try {
			$this->db->trans_begin();
            //PRC BOM ENRY
            $bomData = [
                'id'=>$data['id'],
                'prc_id'=>$data['prc_id'],
                'item_id'=>$data['item_id'],
                'ppc_qty'=>$data['ppc_qty'],
                'process_id'=>$data['process_id'],
                'batch_no'=>$data['bom_batch']
            ];
            $result = $this->store('prc_bom',$bomData);

            //Issue Data
			$data['req_id'] = '';
            $data['issue_type'] = 2;
            $data['issued_to'] = $this->loginId;
            $data['created_by'] = $this->loginId;
            $this->store->saveIssueRequisition($data);			

            //Update Batch No in PRC MASTER TABLE
            $this->store('prc_master',['id'=>$data['prc_id'],'batch_no'=>$data['bom_batch']]);

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function getCuttingBomData($param){
        $data['tableName'] = 'prc_bom';
        $data['select'] = "prc_bom.*,prc_master.prc_number,prc_master.prc_qty,item_master.item_name,item_master.item_type,material_master.material_grade,item_master.uom,material_master.color_code";
        $data['leftJoin']['prc_master'] = "prc_master.id = prc_bom.prc_id";
        $data['leftJoin']['item_master'] = "item_master.id = prc_bom.item_id";
        $data['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
        if(!empty($param['production_data'])){
            $prcWhere = ((!empty($param['prc_id']))?' AND prc_log.prc_id='.$param['prc_id']:'');
            $data['select'] .= ',prc_log.cutting_cons';
            $data['leftJoin']['(SELECT SUM((qty+rej_found) * wt_nos) AS cutting_cons,
                                    prc_id,process_id 
                                    FROM prc_log 
                                    WHERE  is_delete = 0 
                                    AND trans_type = 1 
                                    '. $prcWhere.' 
                                    GROUP BY prc_id
                                ) prc_log'] = "prc_bom.prc_id = prc_log.prc_id ".$prcWhere;
        }

        if(!empty($param['stock_data'])){
            $customWhere =( (!empty($param['prc_id']))?' AND child_ref_id ="'.$param['prc_id'].'"':'');
			$customWhere .= ((!empty($param['item_id']))?' AND stock_trans.item_id ="'.$param['item_id'].'"':'');
			$customWhere .= ((!empty($param['batch_no']))?' AND stock_trans.batch_no ="'.$param['batch_no'].'"':'');

            $data['select'] .= ",IFNULL(stock_trans.issue_qty,0) as issue_qty,(IFNULL(stock_trans.return_qty,0) + IFNULL(scrap_stock.scrap_return,0) + IFNULL(end_pcs_stock.end_pcs,0)) as return_qty,IFNULL(stock_trans.supplier_name,'') as supplier_name,IFNULL(stock_trans.batch_no,'') as batch_no,IFNULL(stock_trans.location_id,'') as location_id,stock_trans.heat_no,IFNULL(end_pcs_stock.end_pcs,0) as total_end_pcs, IFNULL(stock_trans.return_qty,0) as total_return, IFNULL(scrap_stock.scrap_return,0) AS scrap_qty";
			
            //Join For Issued Material & Usable Returned Material
            $data['leftJoin']['(SELECT  SUM((CASE WHEN trans_type = "SSI" THEN abs(stock_trans.qty) ELSE 0 END)) AS issue_qty,
                                            SUM(CASE WHEN trans_type = "PMR" THEN stock_trans.qty ELSE 0 END) as return_qty,
                                            GROUP_CONCAT(DISTINCT party_master.party_name) as supplier_name, 
                                            GROUP_CONCAT(DISTINCT stock_trans.batch_no) as batch_no,
                                            GROUP_CONCAT(DISTINCT batch_history.heat_no) as heat_no,
                                            GROUP_CONCAT(DISTINCT stock_trans.location_id) as location_id,
                                            child_ref_id,stock_trans.item_id
                                        FROM stock_trans
                                        LEFT JOIN batch_history ON batch_history.batch_no = stock_trans.batch_no AND batch_history.item_id = stock_trans.item_id AND batch_history.is_delete = 0
                                        LEFT JOIN party_master ON batch_history.party_id = party_master.id
                                        WHERE stock_trans.is_delete=0
                                            AND stock_trans.trans_type IN("SSI","PMR")
                                            '.$customWhere.'
                                        GROUP BY stock_trans.child_ref_id,stock_trans.item_id
                                ) stock_trans'] = 'stock_trans.child_ref_id = prc_bom.prc_id AND prc_bom.item_id = stock_trans.item_id';
            //Join For Returned Scrap
            $data['leftJoin']['(SELECT SUM(stock_trans.qty) as scrap_return,
                                    child_ref_id,stock_trans.item_id
                                    FROM stock_trans
                                    WHERE stock_trans.is_delete=0
                                        AND stock_trans.trans_type = "PMR"
                                        AND child_ref_id ="'.$param['prc_id'].'"
                                    GROUP BY stock_trans.child_ref_id,stock_trans.item_id
                                ) scrap_stock'] = 'scrap_stock.child_ref_id = prc_bom.prc_id AND material_master.scrap_group = scrap_stock.item_id';
            // Join For Retured End Pcs
            $data['leftJoin']['(SELECT SUM(end_piece_return.qty) as end_pcs,
                                    prc_id,end_piece_return.item_id
                                    FROM end_piece_return
                                    WHERE end_piece_return.is_delete=0
                                        AND prc_id ="'.$param['prc_id'].'"
                                       '.((!empty($param['item_id']))?' AND stock_trans.item_id ="'.$param['item_id'].'"':'').'
                                    GROUP BY end_piece_return.prc_id,end_piece_return.item_id
                                ) end_pcs_stock'] = 'end_pcs_stock.prc_id = prc_bom.prc_id AND end_pcs_stock.item_id = prc_bom.item_id';
        }

        if(!empty($param['id'])){ $data['where']['prc_bom.id'] = $param['id']; }
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
   
    public function getCuttingPrcData($param = []){
        $data['tableName'] = 'prc_master';
        $data['select'] = 'prc_master.*,prc_detail.remark,prc_detail.process_ids,prc_detail.id as prc_detail_id,prc_detail.cut_weight,prc_detail.cutting_length,prc_detail.cutting_dia';
        $data['select'] .= ", IFNULL(im.item_name,'') as item_name,IFNULL(im.item_code,'') as item_code, IFNULL(im.uom,'') as uom, IFNULL(pm.party_name,'') as party_name, im.forge_weight, im.wt_pcs,prc_master.rev_no, ecn_master.drw_no, im.production_type, material_master.material_grade";
       	$data['select'] .= ", IFNULL(so_master.trans_number,'') as so_number,so_master.doc_no";
        
        $data['leftJoin']['prc_detail'] = 'prc_detail.prc_id = prc_master.id';
        $data['leftJoin']['item_master im'] = "im.id = prc_master.item_id";
        $data['leftJoin']['ecn_master'] = "im.id = ecn_master.item_id AND ecn_master.rev_no = prc_master.rev_no AND ecn_master.is_delete =0";
        $data['leftJoin']['party_master pm'] = "pm.id = prc_master.party_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = prc_master.created_by";
        $data['leftJoin']['so_trans'] = "so_trans.id = prc_master.so_trans_id";
        $data['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id";
		$data['leftJoin']['material_master'] = "material_master.id = im.grade_id";

        if(!empty($param['id'])){ $data['where']['prc_master.id'] = $param['id']; }

        if(!empty($param['production_data'])){
            $prcIdWh = ((!empty($param['id']))?(' AND prc_log.prc_id = '.$param['id']):'');
            $data['select'] .= ',IFNULL(prc_log.production_qty,0) as production_qty';
            $data['leftJoin']['(SELECT SUM(qty) as production_qty,prc_id 
                                    FROM prc_log
                                    WHERE  is_delete = 0  
                                    '.$prcIdWh.'
                                    GROUP BY prc_id
                                ) prc_log'] = "prc_master.id = prc_log.prc_id";
        }
		
		if(!empty($param['prc_type'])){ $data['where']['prc_master.prc_type'] = $param['prc_type']; }
        
		if(!empty($param['item_id'])){ $data['where']['prc_master.item_id'] = $param['item_id']; }

        if(!empty($param['from_date']) && !empty($param['to_date'])){
            $data['customWhere'][] = "prc_master.prc_date BETWEEN '".$param['from_date']."' AND '".$param['to_date']."'";
        }

        if(!empty($param['single_row'])){
            return $this->row($data);
        }else{
            return $this->rows($data);
        }
        
    }

    public function saveCuttingLog($param){
        try {
            $this->db->trans_begin();
            $prcData = $this->getCuttingPrcData(['id'=>$param['prc_id'],'single_row'=>1]);
              /** Check PRC Status */
            if(!in_array($prcData->status,[1,2,3])){
                return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
            }

            /*** Check Required Material For Production */
            $requiredMaterial = $param['qty'] * $param['wt_nos'] ;
            $stockData = $this->cutting->getCuttingBomData(['prc_id'=>$param['prc_id'],'single_row'=>1,'stock_data'=>1,'production_data'=>1]);
		    $stockQty = $stockData->issue_qty - (( $stockData->cutting_cons) + $stockData->return_qty);
            if(round($requiredMaterial,3) > round($stockQty,3) ){
                return ['status'=>0,'message'=>'Material not available'];
            }
            /** Save prc_log */
            $logDetail = (!empty($param['logDetail']))?$param['logDetail']:[]; unset($param['logDetail']);
            $result = $this->store('prc_log', $param, 'PRC Log');
            if(!empty($logDetail)){
                $logDetail['log_id'] = $result['id'];	
                $this->store('prc_log_detail', $logDetail, 'PRC Log Detail');
            }

            /** Save Stock */
            $stockData = [
                'id'=>'',
                'trans_type'=>"CUT",
                'trans_date'=>$param['trans_date'],
                'ref_no'=> $prcData->prc_number,
                'main_ref_id'=>$param['prc_id'],
                'child_ref_id'=>$result['id'],
                'location_id '=>$this->CUT_STORE->id,
                'batch_no'=>$prcData->prc_number,
                'ref_batch'=>$prcData->batch_no,
                'item_id'=>$prcData->item_id,
                'p_or_m'=>1,
                'qty'=>$param['qty'],
                'created_by'=>$this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $result = $this->store("stock_trans",$stockData);
            // $this->changeOtherPrcStatus(['prc_id'=>$param['prc_id']]); 
            if($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        }catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteCuttingLog($param){
        try {
            $this->db->trans_begin();

            $logData = $this->getProcessLogList(['id'=>$param['id'],'single_row'=>1]);
            if(!in_array($logData->status,[2,3])){
                return ['status'=>0,'message'=>'PRC is not in Inprogress state'];
            }
            if(!empty($logData)){
                $stockData = $this->itemStock->getItemStockBatchWise(['item_id'=>$logData->item_id,'location_id'=>$this->CUT_STORE->id,'batch_no'=>$logData->prc_number,'single_row'=>1]);
                if($logData->qty > $stockData->qty){
                    return ['status'=>0,'message'=>'You can not delete this log'];
                }
                $this->remove('stock_trans',['main_ref_id'=>$logData->prc_id,'child_ref_id'=>$logData->id,'trans_type'=>"CUT"]);
                $this->trash('prc_log_detail',['log_id'=>$param['id']]);
                $result = $this->trash('prc_log',['id'=>$param['id']]);
                
                // $this->changeOtherPrcStatus(['prc_id'=>$logData->prc_id]);
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

    public function getProcessLogList($param=[]){
        $queryData = array();          
		$queryData['tableName'] = "prc_log";
		
		$queryData['select'] = "prc_log.*,employee_master.emp_name,shift_master.shift_name,prc_log_detail.remark,prc_detail.process_ids,prc_master.item_id,prc_master.prc_number,prc_master.status, machine.item_code as machine_code, machine.item_name as machine_name, item_master.item_name, item_master.item_code, created.emp_name as created_name,process_master.process_name";
		$queryData['select'] .=', machine.item_code as processor_name';
		$queryData['leftJoin']['item_master machine'] = "machine.id = prc_log.processor_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = prc_log.operator_id";
        $queryData['leftJoin']['shift_master'] = "shift_master.id = prc_log.shift_id";
        $queryData['leftJoin']['prc_log_detail'] = "prc_log_detail.log_id = prc_log.id AND prc_log_detail.is_delete = 0";
        $queryData['leftJoin']['prc_master'] = "prc_master.id = prc_log.prc_id";
        $queryData['leftJoin']['prc_detail'] = "prc_detail.prc_id = prc_log.prc_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
		$queryData['leftJoin']['employee_master created'] = "created.id = prc_log.created_by";
        $queryData['leftJoin']['process_master'] = "process_master.id = prc_log.process_id";//11-09-2024
		
		if(!empty($param['grouped_data'])){
			$queryData['select'] .=',SUM(prc_log.qty) as ok_qty,SUM(prc_log.rej_found) as rej_found,SUM(prc_log.rej_qty) as rej_qty,SUM(prc_log.rw_qty) as rw_qty,SUM(review_qty) as review_qty';
		}

		if(!empty($param['id'])){ $queryData['where']['prc_log.id'] = $param['id']; }

		
		if(!empty($param['prc_id'])){ $queryData['where']['prc_log.prc_id'] = $param['prc_id']; }	
		
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
		
		if(!empty($param['prc_ids'])){ $queryData['where_in']['prc_log.prc_id'] = $param['prc_ids']; }
		
		if(!empty($param['group_by'])){
			$queryData['group_by'][] = $param['group_by'];
		}

		if(!empty($param['having'])){
			$queryData['having'][] = $param['having'];
		}
		
		if(!empty($param['single_row'])){
			$result = $this->row($queryData);
		}else{
			$result = $this->rows($queryData);
		}
        return $result;  
    }

    public function changeOtherPrcStatus($param){
		try {
			$this->db->trans_begin();
            $prcIdWh = ' AND prc_log.prc_id = '.$param['prc_id'];
			$queryData['tableName'] = "prc_master";
			$queryData['select'] = 'prc_master.prc_qty,IFNULL(prc_log.production_qty,0) as production_qty';
            $queryData['leftJoin']['(SELECT 
                                        (SUM(qty) + SUM(rej_qty))  as production_qty,prc_id 
                                        FROM prc_log 
                                        WHERE  is_delete = 0  '.$prcIdWh.'
                                        GROUP BY prc_id
                                    ) prc_log'] = "prc_master.id = prc_log.prc_id";
			$queryData['where']['prc_master.id'] = $param['prc_id'];
			$prcData = $this->row($queryData);
			$status = 2;
			if($prcData->production_qty >= $prcData->prc_qty){
				$status = 3;
			}
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

    public function startCuttingPRC($data){
		try {
			$this->db->trans_begin();

			$result = $this->store("prc_master",['id'=>$data['id'],'status'=>2]);

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Saved Successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}
    //Retrun Type [1=> Return to stock As useable,2=>End piece return, 3=>scrap]
    public function storeReturnedMaterial($data){
		try {
			$this->db->trans_begin();

            if($data['return_type'] != 2){
                if($data['return_type'] == 3){
                    $data['location_id'] = $this->SCRAP_STORE->id;
                    $itemData = $this->item->getItem(['id'=>$data['item_id']]);
                    if(empty($itemData->scrap_group)){
                        return ['status'=>0,'message'=>'Scrap Group required.'];
                    }else{
                        $data['item_id'] = $itemData->scrap_group;
                    }
                }
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
            }else{
                $endPcsData = [
                    'id'=>'',
                    'trans_date'=>date("Y-m-d"),
                    'item_id'=>$data['item_id'],
                    'prc_id'=>$data['prc_id'],
                    'end_pcs'=>$data['end_pcs'],
                    'qty'=>$data['qty'],
                    'batch_no'=>$data['batch_no'],
                    'location_id '=>$data['location_id'],
                    'remark'=>$data['remark'],
                    'created_by'=>$this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $result = $this->store("end_piece_return",$endPcsData);
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

    public function deleteEndPcs($id){
		try {
			$this->db->trans_begin();
            $endPcData = $this->endPiece->endPcsReturnData(['id'=>$id,'single_row'=>1,'stock_data'=>1]);
            if(!empty($endPcData->review_qty) && $endPcData->review_qty > 0){ 
				return ['status'=>0,'message'=>'You can not delete this record']; 
			}
			$result = $this->trash('end_piece_return',['id'=>$id]);

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
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

    
}
?>