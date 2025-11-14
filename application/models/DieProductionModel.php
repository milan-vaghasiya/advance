<?php
class DieProductionModel extends MasterModel{
    private $die_production = "die_production";
    private $die_master = 'die_master';
    private $dieKit= "die_kit";
    private $die_history = "die_history";

    public function nextSrNo($data){
        $queryData['select'] = "MAX(sr_no) as sr_no";
        $queryData['where']['category_id'] = $data['category_id'];
        $queryData['where']['fg_id'] = $data['fg_id'];
        $queryData['tableName'] = $this->die_master;
		$sr_no = $this->specificRow($queryData)->sr_no;
		$nextSrNo = (!empty($sr_no))?(++$sr_no):'A'; 
		return $nextSrNo;
    }

    public function nextRecutNo($data){
        $queryData['select'] = "MAX(recut_no) as recut_no";
        $queryData['where']['die_id'] = $data['die_id'];
        $queryData['tableName'] = $this->die_history;
		$recut_no = $this->specificRow($queryData)->recut_no;
		$nextRecutNo = !empty($recut_no)?$recut_no+1:1; 
		return $nextRecutNo;
    }

    public function getNextDieNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'die_production';
        $queryData['select'] = "MAX(trans_no) as trans_no ";
        	
		$queryData['where']['die_production.trans_date >='] = $this->startYearDate;
		$queryData['where']['die_production.trans_date <='] = $this->endYearDate;

		$trans_no = $this->specificRow($queryData)->trans_no;
		$trans_no = (empty($this->last_trans_no))?($trans_no + 1):$trans_no;
		return $trans_no;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->die_production;
        $data['select'] = 'die_production.*,IF(die_production.trans_type = 1,"New","Recut") as transType,item_master.item_name as fg_item_name,item_category.category_name,item_master.item_code as fg_item_code,ifnull(die_bom.bom_qty,0) as bom_qty,ifnull(issue_register.issue_qty,0) as issue_qty,die_master.item_code as die_code,issue_register.item_id as issue_item_id,die_log.id as log_id,die_log.process_by'; // 05-08-2024 
        $data['leftJoin']['item_master'] = 'item_master.id = die_production.fg_item_id';
        $data['leftJoin']['item_category'] = 'item_category.id = die_production.item_id';
        $data['leftJoin']['die_bom'] = 'die_bom.die_id = die_production.id';
        $data['leftJoin']['die_master'] = 'die_master.id = die_production.die_ref_id';
        $data['leftJoin']['die_log'] = 'die_log.die_id = die_production.id';
        $data['leftJoin']['issue_register'] = 'issue_register.prc_id = die_production.id AND issue_register.issue_type = 3 AND die_bom.item_id = issue_register.item_id';
        $data['group_by'][] = "die_production.id";
        
        if($data['status'] == 9){
            $data['where_in']['die_production.status'] = '7,8,9';
        }else{
            $data['where']['die_production.status'] = $data['status'];
        }
        
		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
        $data['searchCol'][] = "die_production.trans_number";
        $data['searchCol'][] = "die_production.trans_date";
        $data['searchCol'][] = 'IF(die_production.trans_type = 1,"New","Recut")';
        $data['searchCol'][] = "item_category.category_name";
		$data['searchCol'][] = "die_master.item_code";       
		$data['searchCol'][] = "die_production.remark";
        $data['searchCol'][] = "die_production.status";
        
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getScrapDTRows($data){
        $data['tableName'] = $this->die_history;
        $data['select'] = 'die_history.*,item_master.item_code as fg_item_code,item_master.item_name as fg_item_name,item_category.category_code,item_category.category_name,die_master.sr_no,die_master.set_no'; 
        $data['leftJoin']['die_master'] = 'die_master.id = die_history.die_id ';
        $data['leftJoin']['die_production'] = 'die_production.id = die_history.die_job_id ';
        $data['leftJoin']['item_category'] = 'item_category.id = die_master.category_id';
        $data['leftJoin']['item_master'] = 'item_master.id = die_master.fg_id';
        $data['customWhere'][] = '((die_history.material_weight - die_history.weight) - die_history.scrap_qty) > 0';
       
        $data['searchCol'][] = "CPNCAT(item_master.item_code,item_master.item_name)";
        $data['searchCol'][] = "item_category.category_code";
        $data['searchCol'][] = 'die_master.item_code';
        $data['searchCol'][] = "die_history.weight";
        $data['searchCol'][] = "die_history.material_weight";
        $data['searchCol'][] = "(die_history.material_weight - die_history.weight)";
        
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

	public function getDieProduction($data){
        $queryData['tableName'] = $this->die_production;
        $queryData['select'] = 'die_production.*,item_master.item_code as fg_item_code,item_master.item_name as fg_item_name,item_category.category_code,item_category.category_name';
        $queryData['leftJoin']['item_master'] = 'item_master.id = die_production.fg_item_id';
        $queryData['leftJoin']['item_category'] = 'item_category.id = die_production.item_id';
        if(!empty($data['id'])) {$queryData['where']['die_production.id'] = $data['id'];}
        return $this->row($queryData);
    }

    public function getDieProductionData($data){
        $queryData['tableName'] = $this->die_production;
        $queryData['select'] = 'die_production.*,item_master.item_code as fg_item_code,item_master.item_name as fg_item_name,item_category.category_name,item_category.category_code,die_log.production_time,die_log.processor_id ,die_log.id as log_id';
        $queryData['leftJoin']['item_master'] = 'item_master.id = die_production.fg_item_id';
        $queryData['leftJoin']['item_category'] = 'item_category.id = die_production.item_id';
        $queryData['leftJoin']['die_log'] = 'die_log.die_id = die_production.id';
        if(!empty($data['material_value'])){
            $queryData['select'] .= ',issue_register.issue_qty,grnTrans.price as rm_price,die_history.weight as material_weight,die_history.material_value as material_rate,die_history.total_value';
            $queryData['leftJoin']['die_bom'] = 'die_bom.die_id = die_production.id';
            $queryData['leftJoin']['issue_register'] = 'issue_register.prc_id = die_production.id AND issue_register.issue_type = 3 AND die_bom.item_id = issue_register.item_id';
            $queryData['leftJoin']['(SELECT price,item_id FROM grn_trans WHERE is_delete = 0 ORDER BY id DESC) as grnTrans'] = 'grnTrans.item_id = issue_register.item_id';
            $queryData['leftJoin']['die_master'] = 'die_master.id = die_production.die_ref_id';
            $queryData['leftJoin']['die_history'] = 'die_history.die_id = die_master.id AND die_history.recut_no = die_master.recut_no';
        }
        if(!empty($data['id'])) {$queryData['where_in']['die_production.id'] = $data['id'];}
        if(!empty($data['category_id'])) {$queryData['where']['die_production.item_id'] = $data['category_id'];}
        if(!empty($data['item_id'])) {$queryData['where']['die_production.fg_item_id'] = $data['item_id'];}
        if(!empty($data['status'])) {$queryData['where']['die_production.status'] = $data['status'];}
        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }

    public function getDieKitData($data) {
        $queryData['tableName'] = $this->dieKit;
        $queryData['select'] = 'die_kit.*,item_master.item_name,item_category.category_name,item_master.item_code,rm.item_code as rm_code,rm.item_name as rm_name';
        $queryData['leftJoin']['item_master rm'] = 'rm.id = die_kit.ref_item_id';
        $queryData['leftJoin']['item_master'] = 'item_master.id = die_kit.item_id';
        $queryData['leftJoin']['item_category'] = 'item_category.id = die_kit.ref_cat_id';
        $queryData['where']['die_kit.item_id'] = $data['fg_item_id'];
        $queryData['group_by'][] = "die_kit.ref_cat_id"; 
        return $this->rows($queryData);
    }

    public function getDieProductionBom($data){
        $queryData['tableName'] = 'die_bom';
        $queryData['select'] = 'die_bom.*,item_master.item_code,item_master.item_name,SUM(issue_register.issue_qty) as issue_qty,material_master.scrap_group,scrap.price as scrap_rate,item_master.item_type';
        $queryData['leftJoin']['die_production'] = 'die_production.id = die_bom.die_id';
        $queryData['leftJoin']['item_master'] = 'item_master.id = die_bom.item_id';
        $queryData['leftJoin']['material_master'] = 'material_master.id = item_master.grade_id ';
        $queryData['leftJoin']['item_master scrap'] = 'material_master.scrap_group = scrap.id ';
        $queryData['leftJoin']['issue_register'] = 'issue_register.prc_id = die_bom.die_id AND issue_register.issue_type = 3 AND die_bom.item_id = issue_register.item_id';
        $queryData['group_by'][] = 'die_bom.id';
        if(!empty($data['die_id'])) {$queryData['where']['die_bom.die_id'] = $data['die_id'];}
        if(!empty($data['customWhere'])) {$queryData['customWhere'][] = $data['customWhere'];}
        if(!empty($data['multi_rows'])){
            return $this->rows($queryData);
        }
        return $this->row($queryData);
    }

    public function save($data){
        try {
            $this->db->trans_begin();

            foreach ($data['dp_ref_id'] as $key => $value){
                if($data['dp_qty'][$key] > 0){
                    for($i = 1; $i <= $data['dp_qty'][$key]; $i++){                    
                        $trans_no = $this->getNextDieNo();
                        $trans_number = "DP".n2y(date("Y")).n2m(date("m")).str_pad($trans_no,2,'0',STR_PAD_LEFT);
    
                        $dpData = array(
                            'id' => '',
                            'trans_type' => 1,
                            'trans_date' => $data['trans_date'],
                            'trans_no' => $trans_no,
                            'trans_number' => $trans_number,
                            'item_id' => $value,
                            'fg_item_id' => $data['fg_item_id'],
                            'remark' => $data['remark'],
                            'created_by' => $this->session->userdata('loginId')
                        );
                        $trans_no++;
                        $this->store($this->die_production, $dpData);
                    }
                }
            }
            $result = ['status' => 1, 'message' => "Die Production Saved Successfully."];
            
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

	public function delete($id){
        try{
            $this->db->trans_begin();

            /* Remove Material Request */            
            $this->trash('die_bom', ['die_id'=>$id]);
            
            /* Remove Issued Material */
            $this->trash('issue_register', ['issue_type'=>3, 'prc_id'=>$id]);
            $this->trash('stock_trans', ['trans_type'=>'SDI', 'child_ref_id'=>$id]);

            /* Remove Log Detail */
            $logData = $this->getDieLogData(['die_id'=>$id]);
            $logIds = (!empty($logData) ? implode(',',array_column($logData,'id')) : []);

            if(!empty($logIds)):
                $setData = array();
                $setData['tableName'] = 'die_log_detail';
                $setData['where_in']['log_id'] = $logIds;
                $setData['update']['is_delete'] = 1;
                $this->setValue($setData);
            endif;

            /* Remove Log */
            $this->trash('die_log', ['die_id'=>$id]);
            $this->trash("die_outsource", ['dp_id'=>$id]);

            /* Remove POP Report & Die Master & History */
    		$this->trash('die_pop_report', ['die_job_id'=>$id]);
            $this->trash($this->die_master, ['die_job_id'=>$id]);
            $this->trash($this->die_history, ['die_job_id'=>$id]);

            $result = $this->trash($this->die_production, ['id' => $id], 'Die Production');

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
	
    /*public function changeStatus($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->die_production, ['id'=> $data['id'], 'status' => $data['status']]);      
            // If production status is approve,reject or recut
            if(in_array($data['status'],[7,8,9])){
                $dieProData = $this->getDieProductionData(['id'=>$data['id'],'single_row'=>1]);
                $dmData = $this->dieMaster->getDieMasterData(['die_job_id'=>$dieProData->id, 'single_row'=>1]);
                // UPDATE DIE WEIGHT IN DIE MASTER
                $dieMasterData = [
                    'capacity' => !empty($data['capacity'])?$data['capacity']:'',
                    'weight' => !empty($data['weight'])?$data['weight']:'',
                    'height' => !empty($data['height'])?$data['height']:''
                ];
                if($dieProData->trans_type == 2){ //IF RECUT                     
                    $status = 1; if($data['status'] == 7){ $status = 3; } elseif($data['status'] == 8){ $status = 4; }
                    //if die in alread in set then check set
                    $dieMasterData = ['status'=>$status];
                    if($dmData->set_no > 0){
                        $set = $this->dieMaster->getDieMasterData(['category_id'=>$dmData->category_id,'fg_id'=>$dmData->fg_id,'set_no'=>$dmData->set_no,'status_not'=>'3,4', 'single_row'=>1]);
                        $itemCode =  $dieProData->category_code.'-'.$dieProData->fg_item_code.'-0'.$dmData->sr_no.'/'.lpad($dmData->recut_no,2); //Generate Die Code
                        if(!empty($set)){
                            $dieMasterData['item_code'] = $itemCode;
                            $dieMasterData['set_no'] = 0;
                            $dieMasterData['status'] = 0;
                        }
                    }
                }
                $dm = $this->edit($this->die_master,['die_job_id'=>$data['id']], $dieMasterData);
                $bomData = $this->getDieProductionBom(['customWhere'=>'die_production.die_ref_id = '.$dieProData->die_ref_id.' AND die_production.trans_type = 1']);
                $mt_rate = ($dieProData->material_cost > 0 && $dieProData->material_weight > 0) ? $dieProData->material_cost / $dieProData->material_weight : 0;
                $material_cost = $mt_rate*$data['weight'];
                $process_cost = $dieProData->mhr*$dieProData->production_time;
                $scrap_cost = ($dieProData->material_weight -$data['weight']) * (!empty($bomData->scrap_rate)?$bomData->scrap_rate:0);
                $total_value = ($material_cost + $process_cost) - $scrap_cost;
                $dieHistory = [
					'capacity' => !empty($data['capacity'])?$data['capacity']:'',
                    'weight' => !empty($data['weight'])?$data['weight']:'',
                    'height' => !empty($data['height'])?$data['height']:'',
                    'category_id' => $dieProData->item_id,
					'material_value' => $dieProData->material_value,
                    'material_weight' => $dieProData->material_weight,
                    'material_cost' => $material_cost,
                    'process_cost' => $process_cost,
                    'total_value' => $total_value,
                ];
                $this->edit($this->die_history,['die_job_id'=>$data['id']], $dieHistory);
                if($data['status'] == 7){
                    $this->recutDie(['id'=>$dmData->id]);
                }
            }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }*/

	public function changeStatus($data){
        try{
            $this->db->trans_begin();

            if(!empty($data['type']) && $data['type'] == 'Component'){
                $result = $this->store($this->die_master, ['id'=> $data['id'], 'status' => $data['status']]);
            }else{
                $result = $this->store($this->die_production, ['id'=> $data['id'], 'status' => $data['status']]);
            }

            // If production status is approve,reject or recut
            if(in_array($data['status'],[7,8,9]) || ((!empty($data['type']) && $data['type'] == 'Component') && in_array($data['status'],[1,4,6]))){
                if(!empty($data['type']) && $data['type'] == 'Component'){
                    $dmData = $this->dieMaster->getDieMasterData(['id'=>$data['id'], 'single_row'=>1]);
                }else{
                    $dieProData = $this->getDieProductionData(['id'=>$data['id'],'single_row'=>1]);
                    $dmData = $this->dieMaster->getDieMasterData(['die_job_id'=>$dieProData->id, 'single_row'=>1]);
                }
                
                // UPDATE DIE WEIGHT IN DIE SET
                $dieMasterData = [
                    'capacity' => (!empty($data['capacity'])?$data['capacity']:''),
                    'weight' => (!empty($data['weight'])?$data['weight']:''),
                    'height' => (!empty($data['height'])?$data['height']:''),
                    'length' => (!empty($data['length'])?$data['length']:''),
                    'width' => (!empty($data['width'])?$data['width']:''),
					'material_value' => (!empty($data['material_value'])?$data['material_value']:'')
                ];
				if(!empty($data['attach_file'])){
                    $dieMasterData['attach_file'] = $data['attach_file'];
                }
                
                if(!empty($dieProData)){                      
                    $status = 1; if($data['status'] == 7){ $status = 3; } elseif($data['status'] == 8){ $status = 4; }
                    //if die in alread in set then check set
                    $dieMasterData = ['status'=>$status];
                    //IF RECUT
                    if(($dieProData->trans_type == 2)){
                        if($dmData->set_no > 0){
                            $set = $this->dieMaster->getDieMasterData(['category_id'=>$dmData->category_id,'fg_id'=>$dmData->fg_id,'set_no'=>$dmData->set_no,'status_not'=>'3,4', 'single_row'=>1]);
                            $itemCode =  $dieProData->category_code.'-'.$dieProData->fg_item_code.'-0'.$dmData->sr_no.'/'.lpad($dmData->recut_no,2); //Generate Die Code
                            if(!empty($set)){
                                $dieMasterData['item_code'] = $itemCode;
                                $dieMasterData['set_no'] = 0;
                                $dieMasterData['status'] = 0;
                            }
                        }
                    }
                }elseif(!empty($data['type']) && $data['type'] == 'Component'){ 
                    $dieMasterData['status'] = $data['status'];
                }

                if(!empty($data['type']) && $data['type'] == 'Component'){
                    $dm = $this->edit($this->die_master,['id'=>$data['id']], $dieMasterData);
                }else{
                    $dm = $this->edit($this->die_master,['die_job_id'=>$data['id']], $dieMasterData);
                }

                if(!empty($dieProData)){
                    $bomData = $this->getDieProductionBom(['customWhere'=>'die_production.die_ref_id = '.$dieProData->die_ref_id.' AND die_production.trans_type = 1']);
                    $mt_rate = ($dieProData->material_cost > 0 && $dieProData->material_weight > 0) ? $dieProData->material_cost / $dieProData->material_weight : 0;
                    $material_cost = $mt_rate*$data['weight'];
                    $process_cost = $dieProData->mhr*$dieProData->production_time;
                    $scrap_cost = ($dieProData->material_weight -$data['weight']) * (!empty($bomData->scrap_rate)?$bomData->scrap_rate:0);
                    $total_value = ($material_cost + $process_cost) - $scrap_cost;
                }

                $dieHistory = [
					'capacity' => (!empty($data['capacity'])?$data['capacity']:''),
                    'weight' => (!empty($data['weight'])?$data['weight']:''),
                    'height' => (!empty($data['height'])?$data['height']:''),
                    'length' => (!empty($data['length'])?$data['length']:''),
                    'width' => (!empty($data['width'])?$data['width']:''),
                    'category_id' => (!empty($dieProData->item_id)) ? $dieProData->item_id : (!empty($dmData->category_id) ? $dmData->category_id : ''),
                    'material_value' => (!empty($dieProData->material_value)) ? $dieProData->material_value : (!empty($data['material_value']) ? $data['material_value'] :0),
                    'material_weight' => (!empty($dieProData->material_weight) ? $dieProData->material_weight : 0),
                    'material_cost' => (!empty($material_cost) ? $material_cost : 0),
                    'process_cost' => (!empty($process_cost) ? $process_cost : 0),
                    'total_value' => (!empty($total_value) ? $total_value : 0),
                ];
				if(!empty($data['attach_file'])){
                    $dieHistory['attach_file'] = $data['attach_file'];
                }
                if(!empty($data['type']) && $data['type'] == 'Component'){
                    $this->edit($this->die_history,['die_id'=>$data['id']], $dieHistory);
                }else{
                    $this->edit($this->die_history,['die_job_id'=>$data['id']], $dieHistory);
                }
                if($data['status'] == 7 || ((!empty($data['type']) && $data['type'] == 'Component') && $data['status'] == 6)){
                    $this->recutDie(['id'=>$dmData->id]);
                }
            }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function recutDie($data){
        try{
            $this->db->trans_begin();

            $dmData = $this->dieMaster->getDieMasterData(['id'=>$data['id'], 'single_row'=>1]);

            $this->edit($this->die_master,['id'=>$data['id']],['status'=>3]);
            if($dmData->set_no > 0){
                // Update Production Qty in previous dieset
                // $customWhere = 'prc_log.die_id = '.$dmData->set_no.' AND prc_master.item_id = '.$dmData->fg_id.' ';
                // $prcLogData = $this->sop->getProcessLogList(['customWhere'=>$customWhere, 'group_by'=>'prc_master.item_id', 'total_log_qty'=>1, 'single_row'=>1]);
                // $totalProductionQty = (!empty($prcLogData->total_log_qty) ? $prcLogData->total_log_qty : 0);
                $dieHistoryData = $this->getDieRecutData(['die_id'=>$dmData->id, 'group_by'=>'die_history.die_id', 'single_row'=>1]); 
                // $current_volume = $totalProductionQty - $dieHistoryData->total_volume;
                $this->edit($this->die_history, ['die_id'=>$dmData->id, 'recut_no'=>$dmData->recut_no,'id'=>$dieHistoryData->last_recut_die], ['volume'=>$dmData->volume]);
                $this->edit($this->die_master,['id'=>$data['id']],['volume'=>0]);
            }
            $trans_no = $this->dieProduction->getNextDieNo();
            $trans_number = "DP".n2y(date("Y")).n2m(date("m")).str_pad($trans_no,2,'0',STR_PAD_LEFT);
            $dpData=[
                        'id' => '',
                        'trans_type' => 2,
                        'trans_date' => date("Y-m-d"),
                        'trans_no' => $trans_no,
                        'trans_number' => $trans_number,
                        'item_id' => $dmData->category_id,
                        'fg_item_id' => $dmData->fg_id,
                        'die_ref_id' => $data['id'],
                        'created_by' => $this->session->userdata('loginId')
                    ];
            $result = $this->store('die_production', $dpData);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getDieMasterList($data){
        $queryData['tableName'] = $this->die_master;
        $queryData['select'] = "die_master.*,item_category.category_code,item_master.item_code as fg_item_code";
        $queryData['leftJoin']['item_category'] = "item_category.id = die_master.category_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = die_master.fg_id";

        if(!empty($data['category_id'])) {
            $queryData['where']['die_master.category_id'] = $data['category_id'];
        }

        if(!empty($data['category_ids'])) {
            $queryData['where_in']['die_master.category_id'] = $data['category_ids'];
        }

        if(!empty($data['fg_id'])) {
            $queryData['where']['die_master.fg_id'] = $data['fg_id'];
        }   

        if(isset($data['set_no'])) {
            $queryData['where']['die_master.set_no'] = $data['set_no'];
        }
        
        if(isset($data['id'])) {
            $queryData['where']['die_master.id'] = $data['id'];
        }

        if(isset($data['status'])) {
            $queryData['where_in']['die_master.status'] = $data['status'];
        }

        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }

    /* Material Issue */
    public function saveMaterialIssue($data){
		try {
			$this->db->trans_begin();
			
            $bomData = [
                'id' => $data['id'],
                'die_id' => $data['die_id'],
                'item_id' => $data['item_id'],
                'bom_qty' => $data['qty'],
                'category_name' => $data['category_name']
            ];
            $result = $this->store('die_bom', $bomData, 'Material Issue');

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    /* Die Log */
    public function saveDieLog($data){
		try {
			$this->db->trans_begin();     
            $logDetail = !empty($data['logDetail'])?$data['logDetail']:[];unset($data['logDetail']);
            $result = $this->store('die_log', $data, 'Die Log');	
            if(!empty($logDetail)){
                $logDetail['log_id'] = $result['id'];
                $this->store('die_log_detail', $logDetail);	
                $data['production_time'] = $logDetail['production_time'];
            }
            $dieProdUpdate = ['id'=>$data['die_id']];
            if($data['process_by'] == 1){
                $prodData = $this->dieProduction->getDieProductionData(['id'=>$data['die_id'],'material_value'=>1,'single_row'=>1]); 
                $mcData = $this->item->getItem(['id'=>$data['processor_id']]);
                if($prodData->trans_type == 1){
                    $dieProdUpdate['material_value'] = $prodData->rm_price;
                    $dieProdUpdate['material_cost']=$prodData->rm_price*$prodData->issue_qty;
                    $dieProdUpdate['material_weight'] = $prodData->issue_qty;
                }else{
                    $dieProdUpdate['material_value'] = $prodData->material_rate;
                    $dieProdUpdate['material_cost']=$prodData->total_value;
                    $dieProdUpdate['material_weight'] = $prodData->material_weight;
                }
                $dieProdUpdate['mhr'] = !empty($mcData->mhr)?$mcData->mhr:0;
            }else{
                $dieProdUpdate['status'] = 5;
            }
            $this->store('die_production', $dieProdUpdate);
            $time = explode(":",$data['production_time']);$productionTime = 0;
            if(!empty($time[0]) && $time[0] >0){
                $productionTime = $time[0];
            }
            if(!empty($time[1]) && $time[1] >=0){
                $productionTime += ($time[1]/60);
            }
            $setData = array();
            $setData['tableName'] = 'die_log';
            $setData['where']['id'] = $result['id'];
            $setData['set']['production_time'] = 'production_time, + '.$productionTime;
            $this->setValue($setData);
			/** IF VENDOR RECEIVE */
            if($data['process_by'] == 2){
                $this->store("die_outsource",['id'=>$data['ref_id'],'status'=>2]);
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

	public function deleteDieLog($param){
		try {
			$this->db->trans_begin();

			$logData = $this->getDieLogData(['id'=>$param['id'],'single_row'=>1]);
            if($logData->status !=  5){
                return ['status' => 2, 'message' => "You can not delete this log"];
            }
			if(!empty($logData)){
                $status = 2;
                if($logData->process_by == 2){
                    $this->store("die_outsource",['id'=>$logData->ref_id,'status'=>1]);
                    $status = 4;
                }
                $this->store("die_production",['id'=>$logData->die_id,'status'=>$status]);
				$result = $this->trash('die_log',['id'=>$param['id']]);

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

    public function getDieLogData($param){
		$queryData['tableName'] = "die_log";
		$queryData['select'] = "die_log.*,employee_master.emp_name,shift_master.shift_name,die_production.trans_number,die_production.status,item_master.item_code as fg_item_code,item_master.item_name as fg_item_name,item_category.category_code,item_category.category_name,die_production.material_cost,die_production.mhr,die_history.total_value";		
        
		$queryData['select'] .=', IF(die_log.process_by = 1, machine.item_code, IF(die_log.process_by = 2,party_master.party_name,"")) as processor_name';
        $queryData['leftJoin']['die_production'] = "die_production.id = die_log.die_id";
		$queryData['leftJoin']['item_master machine'] = "machine.id = die_log.processor_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = die_log.processor_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = die_log.operator_id";
        $queryData['leftJoin']['shift_master'] = "shift_master.id = die_log.shift_id";
        $queryData['leftJoin']['item_master'] = 'item_master.id = die_production.fg_item_id';
        $queryData['leftJoin']['item_category'] = 'item_category.id = die_production.item_id';
        $queryData['leftJoin']['die_history'] = 'die_history.die_job_id = die_production.id';
        		
		if(!empty($param['id'])){ $queryData['where']['die_log.id'] = $param['id']; }
		
		if(!empty($param['die_id'])){ $queryData['where']['die_log.die_id'] = $param['die_id']; }	
		
        if(!empty($param['single_row'])):
            return $this->row($queryData);
        else:
            return $this->rows($queryData);
        endif;
    }

    public function getDieLogTransData($param){
		$queryData['tableName'] = "die_log_detail";
		$queryData['select'] = "die_log_detail.*,employee_master.emp_name,shift_master.shift_name,die_production.trans_number,die_production.status,item_master.item_code as fg_item_code,item_master.item_name as fg_item_name,item_category.category_code,item_category.category_name,die_production.material_cost,die_production.mhr,die_history.total_value,die_log.in_challan_no";		
        
		$queryData['select'] .=', IF(die_log.process_by = 1, machine.item_code, IF(die_log.process_by = 2,party_master.party_name,"")) as processor_name';
        $queryData['leftJoin']['die_log'] = "die_log.id = die_log_detail.log_id";
        $queryData['leftJoin']['die_production'] = "die_production.id = die_log.die_id";
		$queryData['leftJoin']['item_master machine'] = "machine.id = die_log.processor_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = die_log.processor_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = die_log.operator_id";
        $queryData['leftJoin']['shift_master'] = "shift_master.id = die_log.shift_id";
        $queryData['leftJoin']['item_master'] = 'item_master.id = die_production.fg_item_id';
        $queryData['leftJoin']['item_category'] = 'item_category.id = die_production.item_id';
        $queryData['leftJoin']['die_history'] = 'die_history.die_job_id = die_production.id';
        		
		if(!empty($param['id'])){ $queryData['where']['die_log.id'] = $param['id']; }
		
		if(!empty($param['die_id'])){ $queryData['where']['die_log.die_id'] = $param['die_id']; }	
		
        if(!empty($param['single_row'])):
            return $this->row($queryData);
        else:
            return $this->rows($queryData);
        endif;
    }

    public function deleteLogTrans($param){
		try {
			$this->db->trans_begin();

			$result = $this->trash('die_log_detail',['id'=>$param['id']]);

			$time = explode(":",$param['production_time']); $productionTime = 0;
            if(!empty($time[0]) && $time[0] > 0){
                $productionTime = $time[0];
            }
            if(!empty($time[1]) && $time[1] >= 0){
                $productionTime -= ($time[1]/60);
            }

			$setData = array();
            $setData['tableName'] = 'die_log';
            $setData['where']['id'] = $param['log_id'];
            $setData['set']['production_time'] = 'production_time, - '.$productionTime;
            $this->setValue($setData);

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    /* POP Report */
    public function getNextPopReportNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'die_pop_report';
        $queryData['select'] = "MAX(trans_no) as trans_no ";
        	
		$queryData['where']['die_pop_report.report_date >='] = $this->startYearDate;
		$queryData['where']['die_pop_report.report_date <='] = $this->endYearDate;

		$trans_no = $this->specificRow($queryData)->trans_no;
		$trans_no = (empty($this->last_trans_no))?($trans_no + 1):$trans_no;
		return $trans_no;
    }

	public function savePopReport($data){
		try{
            $this->db->trans_begin();

            $insp_type = !empty($data['insp_type'])?$data['insp_type']:'';
            $insp_job_id = !empty($data['insp_die_id'])?$data['insp_die_id']:'';
            $type = !empty($data['type'])?$data['type']:'';
            $die_main_id = !empty($data['die_main_id'])?$data['die_main_id']:'';
            unset($data['other_die'],$data['insp_type'],$data['type'],$data['die_main_id']);
            
            if(!empty($type) && $type == 'Component'){
                $data['die_id'] = $die_main_id;
                $this->store('die_master', ['id'=>$die_main_id, 'status'=>5]);
            }else{                
                $dieData = $this->saveDieMasterData(['die_job_id'=>$data['die_job_id']]);
                $data['die_id'] = $dieData['die_id'];$insp_die_id="";
                if(!empty($insp_type) && $insp_type == 1){
                    $refDie = $this->saveDieMasterData(['die_job_id'=>$insp_job_id]);
                    $insp_die_id = $refDie['die_id'];
                    $data['insp_die_id'] = $refDie['die_id'];
                }
                $this->store('die_production', ['id'=>$data['die_job_id'], 'status'=>6]);
            }
    		$result = $this->store('die_pop_report', $data, 'POP Report');
            
            if($insp_type == 1){
                if(!empty($type) && $type == 'Component'){
                    $data['die_id'] = $die_main_id;
                    $data['insp_die_id'] = $insp_job_id;
                    $this->store('die_master', ['id'=>$die_main_id, 'status'=>5]);                    
                }else{
                    $die_id = $data['die_id'];
                    $data['die_id'] = $insp_die_id;
                    $data['insp_die_id'] = $die_id;
                    $data['die_job_id'] = $insp_job_id;                    
                    $this->store('die_production', ['id'=>$data['die_job_id'], 'status'=>6]);
                }
                $data['ref_pop_id'] = $result['id'];

                $popRslt = $this->store('die_pop_report', $data, 'POP Report');                
                $this->store('die_pop_report', ['id'=>$result['id'],'ref_pop_id'=>$popRslt['id']], 'POP Report');
            }

    		if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}

    public function saveDieMasterData($data){
        try{
            $this->db->trans_begin();
            $dieProData = $this->getDieProductionData(['id'=>$data['die_job_id'],'material_value'=>1,'single_row'=>1]);
            $die_id="";
            if($dieProData->trans_type == 1){
                // If new production insert die in die_master
                $srNo = $this->nextSrNo(['category_id' => $dieProData->item_id, 'fg_id' => $dieProData->fg_item_id]);
                $status = 0;
                $dieNo =  $dieProData->category_code.'-'.$dieProData->fg_item_code.'-0'.$srNo; //Generate Die Code

                $dieMasterData = [
                    'id' => '',
                    'die_job_id' => $data['die_job_id'],
                    'category_id' => $dieProData->item_id,
                    'item_code' => $dieNo,
                    'fg_id' => $dieProData->fg_item_id,
                    'sr_no' => $srNo,
                    'material_value' => (!empty($dieProData->material_value) ? $dieProData->material_value : $dieProData->rm_price), 
                    'status' => $status,
                    'created_by' => $this->session->userdata('loginId'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $dm = $this->store($this->die_master, $dieMasterData, 'Die Master');
                $this->store($this->die_production, ['id'=>$data['die_job_id'],'die_ref_id'=>$dm['id']]);

                $dieHistory = [
                    'id' => '',
                    'fg_id' => $dieProData->fg_item_id,
                    'die_job_id' => $data['die_job_id'],
					'category_id' => $dieProData->item_id,
                    'die_id' => $dm['id'],
                    'volume' => 0, 
                    'recut_date' => date('Y-m-d'),
                    'created_by' => $this->loginId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'material_value'=>((!empty($dieProData->material_value) && $dieProData->material_value > 0)?$dieProData->material_value:(!empty($dieProData->rm_price)?$dieProData->rm_price:0)),
                    'material_weight'=>(!empty($dieProData->material_weight) && $dieProData->material_weight > 0)?$dieProData->material_weight:0,
                ];
                $this->store($this->die_history, $dieHistory);
                $die_id = $dm['id'];
            }
            else{
                // if recut is
                $dmData = $this->dieMaster->getDieMasterData(['id'=>$dieProData->die_ref_id, 'single_row'=>1]);                    
                $recut_no = $this->nextRecutNo(['die_id'=>$dieProData->die_ref_id]); //Next recut no
                $itemCode =  $dieProData->category_code.'-'.$dieProData->fg_item_code.'-'.$dmData->set_no.$dmData->sr_no.'/'.lpad($recut_no,2); //Generate Die Code
                
                //if die in alread in set then check set
                $updateDie = [
                    'id'=>$dieProData->die_ref_id, 
                    'item_code'=>$itemCode,
                    'recut_no'=>$recut_no,
                    'material_value' => (!empty($dieProData->material_value) ? $dieProData->material_value : $dieProData->rm_price),
                    'die_job_id' => $data['die_job_id'],
                ];
                
                $this->store($this->die_master, $updateDie);
                $dieHistory = [
                    'id' => '',
                    'die_job_id' => $data['die_job_id'],
                    'fg_id' => $dieProData->fg_item_id,
                    'die_id' => $dieProData->die_ref_id,
					'category_id' => $dieProData->item_id,
                    'volume' => 0, 
                    'recut_no' => $recut_no,
                    'recut_date' => date('Y-m-d'),
                    'created_by' => $this->loginId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'material_value'=>(!empty($dieProData->material_value) && $dieProData->material_value > 0)?$dieProData->material_value:$dieProData->rm_price,
                    'material_weight'=>(!empty($dieProData->material_weight) && $dieProData->material_weight > 0)?$dieProData->material_weight:0,
                ];
                $this->store($this->die_history, $dieHistory);     
                $die_id = $dieProData->die_ref_id;              
            }
            $result = ['die_id'=>$die_id];
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
	public function getPopReport($data){
		$queryData['tableName'] = 'die_pop_report';
        $queryData['select'] = 'die_pop_report.*,item_category.category_code,item_master.item_code as fg_item_code,item_master.item_name as fg_item_name,party_master.party_name,die_master.weight,die_master.height,die_master.set_no,die_master.sr_no,die_master.recut_no,refFg.item_code as ref_fg_code,refCat.category_code as ref_cat_code,refDie.sr_no as ref_sr_no,refDie.set_no as ref_set_no,refDie.recut_no as ref_recut_no,die_production.trans_number as prod_number,die_production.trans_date as prod_date';
		$queryData['leftJoin']['die_production'] = 'die_production.id = die_pop_report.die_job_id';
        $queryData['leftJoin']['die_master'] = 'die_master.id = die_pop_report.die_id';
        $queryData['leftJoin']['item_master'] = 'die_production.fg_item_id = item_master.id';
        $queryData['leftJoin']['item_category'] = 'die_master.category_id  = item_category.id';
        $queryData['leftJoin']['party_master'] = 'party_master.id = item_master.party_id';
        $queryData['leftJoin']['die_master refDie'] = 'refDie.id = die_pop_report.insp_die_id';
        $queryData['leftJoin']['item_master refFg'] = 'refDie.fg_id = refFg.id';
        $queryData['leftJoin']['item_category refCat'] = 'refDie.category_id = refCat.id';
        if(!empty($data['die_id'])) {$queryData['where']['die_pop_report.die_id'] = $data['die_id'];}
        if(!empty($data['die_job_id'])) {$queryData['where']['die_pop_report.die_job_id'] = $data['die_job_id'];}
        return $this->row($queryData);
	}

    /* Get Die Recut Data From Die History */
    public function getDieRecutData($param){
        $data['tableName'] = $this->die_history;
        $data['select'] = "die_history.*,SUM(die_history.volume) as total_volume,die_master.item_code as die_code,item_category.category_name,item_master.item_name,item_master.item_code,MAX(die_history.id) as last_recut_die,die_production.trans_number";
        $data['leftJoin']['die_master'] = "die_master.id = die_history.die_id";
        $data['leftJoin']['die_production'] = "die_production.id = die_history.die_job_id";
        $data['leftJoin']['item_category'] = "item_category.id = die_master.category_id";
        $data['leftJoin']['item_master'] = "item_master.id = die_history.fg_id";

        if(!empty($param['category_id'])) { $data['where']['die_master.category_id'] = $param['category_id']; } 
        
        if(!empty($param['die_id'])) { $data['where']['die_history.die_id'] = $param['die_id']; }

        if(!empty($param['die_job_id'])) { $data['where']['die_history.die_job_id'] = $param['die_job_id']; }

        if(!empty($param['fg_id'])) { $data['where']['die_history.fg_id'] = $param['fg_id']; }

        if(!empty($param['group_by'])) { $data['group_by'][] = $param['group_by']; }
        else{ $data['group_by'][] = "die_history.id"; }

        if(!empty($param['single_row'])):
            return $this->row($data);
        else:
            return $this->rows($data);
        endif;
    }

    /* Save Material Value */
    public function saveMaterialValue($data){
		try {
			$this->db->trans_begin();     

            $result = $this->store($this->die_production, $data, 'Material Value');	
            
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function generateScrap($data) {
        try {
            $this->db->trans_begin();
            
            $bomData = $this->getDieProductionBom(['die_id'=>$data['id']]);
            $prodData = $this->getDieRecutData(['die_job_id'=>$data['id'],'single_row'=>1]);
            if(!empty($bomData->scrap_group)){
                $scrap_qty = ($prodData->material_weight - $prodData->weight);
                $entryData = $this->transMainModel->getEntryType(['controller'=>'dieProduction']);
                $stockData = [
                    'id'=>'',
                    'entry_type'=>$entryData->id,
                    'ref_date'=>date("Y-m-d"),
                    'ref_no'=>$prodData->trans_number,
                    'main_ref_id'=>$prodData->die_id,
                    'child_ref_id'=>$prodData->die_job_id,
                    'location_id '=>$this->SCRAP_STORE->id,
                    'batch_no'=>$prodData->trans_number,
                    'item_id'=>$bomData->scrap_group,
                    'p_or_m'=>1,
                    'qty'=> $scrap_qty,
                    'created_by'=>$this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $result = $this->store("stock_transaction",$stockData);
                $this->edit($this->die_history,['id'=>$prodData->id],['scrap_qty'=>$scrap_qty]);
            }else{
                return ['status'=>2,'message'=>'Scrap Group Required.'];
            }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
}
?>