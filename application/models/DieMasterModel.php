<?php
class DieMasterModel extends MasterModel{
    private $dieMaster = 'die_master';
    private $dieKit= "die_kit";
	private $die_history = "die_history";

    public function nextSetNo($data){
        $queryData['select'] = "MAX(set_no) as set_no";
        if(!empty($data['category_id'])){ $queryData['where']['category_id'] = $data['category_id']; }
        $queryData['where']['fg_id'] = $data['fg_id'];
        $queryData['tableName'] = $this->dieMaster;
		$set_no = $this->specificRow($queryData)->set_no;
		$nextSetNo = (!empty($set_no))?($set_no + 1):1; 
		return $nextSetNo;
    }
	
    /*public function getDieRegister($param=array()) {
		$dieRegister = $this->db->query("SELECT die_master.fg_id,die_master.set_no,CONCAT(item_master.item_code,' ',item_master.item_name) as item_name,die_master.status,die_master.id,die_master.item_code,item_category.category_name,
											MAX(CASE WHEN (die_master.category_id = 26 AND die_master.set_no > 0 AND die_master.status NOT IN (3,4)) THEN CONCAT(item_category.category_code, '-',item_master.item_code, '-', die_master.set_no, die_master.sr_no, '/', LPAD(die_master.recut_no,2,'0')) END) AS DT,
											MAX(CASE WHEN (die_master.category_id = 27 AND die_master.set_no > 0 AND die_master.status NOT IN (3,4)) THEN CONCAT(item_category.category_code, '-',item_master.item_code, '-', die_master.set_no, die_master.sr_no, '/', LPAD(die_master.recut_no,2,'0')) END) AS DB,
											MAX(CASE WHEN (die_master.category_id = 28 AND die_master.set_no > 0 AND die_master.status NOT IN (3,4)) THEN CONCAT(item_category.category_code, '-',item_master.item_code, '-', die_master.set_no, die_master.sr_no, '/', LPAD(die_master.recut_no,2,'0')) END) AS TD,
											MAX(CASE WHEN (die_master.category_id = 29 AND die_master.set_no > 0 AND die_master.status NOT IN (3,4)) THEN CONCAT(item_category.category_code, '-',item_master.item_code, '-', die_master.set_no, die_master.sr_no, '/', LPAD(die_master.recut_no,2,'0')) END) AS TP,
											MAX(CASE WHEN (die_master.category_id = 30 AND die_master.set_no > 0 AND die_master.status NOT IN (3,4)) THEN CONCAT(item_category.category_code, '-',item_master.item_code, '-', die_master.set_no, die_master.sr_no, '/', LPAD(die_master.recut_no,2,'0')) END) AS PP,
											MAX(CASE WHEN (die_master.category_id = 31 AND die_master.set_no > 0 AND die_master.status NOT IN (3,4)) THEN CONCAT(item_category.category_code, '-',item_master.item_code, '-', die_master.set_no, die_master.sr_no, '/', LPAD(die_master.recut_no,2,'0')) END) AS CT,
											MAX(CASE WHEN (die_master.category_id = 32 AND die_master.set_no > 0 AND die_master.status NOT IN (3,4)) THEN CONCAT(item_category.category_code, '-',item_master.item_code, '-', die_master.set_no, die_master.sr_no, '/', LPAD(die_master.recut_no,2,'0')) END) AS CB
											FROM die_master LEFT JOIN item_master ON item_master.id=die_master.fg_id LEFT JOIN item_category ON item_category.id=die_master.category_id WHERE die_master.fg_id > 0 AND die_master.set_no > 0 ".(!empty($param['fg_id']) ? "AND die_master.fg_id = ".$param['fg_id'] : "")." AND die_master.is_delete = 0 GROUP BY die_master.fg_id,die_master.set_no ORDER BY fg_id,set_no ")->result();

        return $dieRegister;
    }*/
	
	public function getDieRegister($param=array()) {
        $data['tableName'] = $this->dieMaster;
        $data['select'] = "die_master.fg_id,die_master.set_no,die_master.status,die_master.id,die_master.item_code,die_master.category_id,CONCAT(item_master.item_code,' ',item_master.item_name) as item_name,item_category.category_name,item_category.category_code,CONCAT(item_category.category_code, '-',item_master.item_code, '-', die_master.set_no, die_master.sr_no, '/', LPAD(die_master.recut_no,2,'0')) as cat_code,die_master.die_job_id";

        $data['leftJoin']['item_master'] = "item_master.id = die_master.fg_id";
        $data['leftJoin']['item_category'] = "item_category.id = die_master.category_id";

        $data['customWhere'][] = "die_master.fg_id > 0 AND die_master.set_no > 0 AND die_master.status NOT IN (3,4)";
        if(!empty($param['fg_id'])) { $data['where']['die_master.fg_id'] = $param['fg_id']; }

        if (!empty($param['group_by'])) { $data['group_by'][] = $param['group_by']; }
        else { $data['group_by'][] = "die_master.fg_id,die_master.set_no,die_master.category_id"; }    

        return $this->rows($data);
    }

    public function saveDieSetData($data) {
        try {
            $this->db->trans_begin();
            $next_set_no = "";
            if(empty($data['set_no'])){
                $next_set_no = $this->nextSetNo(['fg_id' => $data['item_id']]);
            }else{
                $next_set_no = $data['set_no'];
            }
            
			if(!empty($data['category_id'])):
				foreach($data['category_id'] as $category_id){
					$dieData = $this->dieProduction->getDieMasterList(['id'=>$data['die_id'][$category_id],  'single_row'=>1]);
					$itemCode = $dieData->category_code.'-'.$dieData->fg_item_code.'-'.$next_set_no.$dieData->sr_no.(($dieData->recut_no > 0)?'/'.lpad($dieData->recut_no,2):'');
					$dieSetData = array(
						'id' => $data['die_id'][$category_id],
						'item_code' => $itemCode,
						'set_no' => $next_set_no,
						'status' => 1
					);
					$this->store($this->dieMaster, $dieSetData);
				}
			endif;
           
            $result = ['status' => 1, 'message' => "Die Set Saved Successfully."];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    public function getPartListDTRows($data){
        $data['tableName'] = 'die_master';
        $data['select'] = "die_master.status,die_master.category_id,die_master.fg_id,die_master.id,item_category.category_name,fg.item_code as fg_item_code,fg.item_name as fg_item_name,CONCAT(item_category.category_code, '-',fg.item_code, '-', die_master.set_no, die_master.sr_no, '/', LPAD(die_master.recut_no,2,'0')) as die_code,item_category.is_inspection,die_master.die_job_id";
        
        $data['select'] .= ',die_challan.trans_number As ch_number';
        $data['leftJoin']['die_challan_trans'] = 'die_challan_trans.die_id = die_master.id AND die_challan_trans.trans_status = 0 AND die_challan_trans.is_delete = 0';
        $data['leftJoin']['die_challan'] = 'die_challan.id = die_challan_trans.challan_id';
        
        $data['leftJoin']['item_category'] = "die_master.category_id = item_category.id";
        $data['leftJoin']['item_master fg'] = "fg.id = die_master.fg_id";

        if(!empty($data['status'])) { $data['where']['die_master.status'] = $data['status']; }
        else { $data['where_in']['die_master.status'] = [0,5]; }
		
		$data['order_by']['die_master.fg_id'] = 'ASC';
		$data['order_by']['die_master.set_no'] = 'ASC';

		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
		$data['searchCol'][] = "CONCAT(item_category.category_code, '-',fg.item_code, '-', die_master.set_no, die_master.sr_no, '/', LPAD(die_master.recut_no,2,'0'))";
		$data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "CONCAT(fg.item_code,' - ',fg.item_name)";
        $data['searchCol'][] = "die_challan.trans_number";
	
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function changePartStatus($data) {
        try {
            $this->db->trans_begin();

            $msg = $data['msg']; unset($data['msg']);
            $this->store($this->dieMaster, $data);
            $result = ['status' => 1, 'message' => "Part ".$msg." Successfully."];

            
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    // 08-01-2025
    public function getDieMasterData($data) {
        $queryData['tableName'] = $this->dieMaster;
		// $queryData['select'] = 'die_master.*,item_master.item_code as fg_item_code,item_master.item_name as fg_item_name,item_category.category_code,item_category.category_name';

        $queryData['select'] = "die_master.*,CONCAT(item_master.item_code,' ',item_master.item_name) as item_name,item_category.category_name,item_category.category_code,CONCAT(item_category.category_code, '-',item_master.item_code, '-', die_master.set_no, die_master.sr_no, '/', LPAD(die_master.recut_no,2,'0')) as cat_code,die_master.die_job_id,bom.item_code as bom_item_code,bom.item_name as bom_item_name";

        $queryData['leftJoin']['item_master'] = 'item_master.id = die_master.fg_id';
        $queryData['leftJoin']['item_category'] = 'item_category.id = die_master.category_id';
        $queryData['leftJoin']['die_bom'] = "die_master.die_job_id = die_bom.die_id";
        $queryData['leftJoin']['item_master bom'] = "bom.id = die_bom.item_id";
        if(!empty($data['id'])){$queryData['where']['die_master.id'] = $data['id'];}
        if(!empty($data['die_job_id'])){$queryData['where']['die_master.die_job_id'] = $data['die_job_id'];}
        if(!empty($data['fg_id'])){$queryData['where']['die_master.fg_id'] = $data['fg_id'];}
        if(!empty($data['category_id'])){$queryData['where']['die_master.category_id'] = $data['category_id'];}
        if(!empty($data['status'])){$queryData['where_in']['die_master.status'] = $data['status'];}
        if(!empty($data['status_not'])){$queryData['where_not_in']['die_master.status'] = $data['status_not'];}
        if(!empty($data['group_by'])){ $queryData['group_by'][] = $data['group_by']; }

        if(!empty($data['available_set'])){
            $queryData['select'] .= ',COUNT(DISTINCT die_kit.ref_cat_id) as dieKitCount, COUNT(DISTINCT die_master.category_id) as dieCount';
            $queryData['leftJoin']['die_kit'] = 'die_kit.item_id = die_master.fg_id AND die_kit.is_delete = 0';
            $queryData['where']['set_no >'] = 0;
            $queryData['having'][] = "dieKitCount = dieCount";
        }
        if(!empty($data['single_row'])){
            $result = $this->row($queryData);
        } else {
            $result = $this->rows($queryData);
        } 
        return $result;
    }

    public function saveOldDieSet($data) {
        try {
            $this->db->trans_begin();
            
            $dieMasterData = $this->getDieMasterData(['id'=>$data['die_master_id'], 'single_row'=>1]);

            foreach($data['die_set_code'] as $key=>$value){
                if(!empty($value)){    
                    $dieSetData = $this->dieProduction->getDieMasterList(['fg_id'=>$dieMasterData->fg_id, 'category_id'=>$key, 'set_no'=>0, 'single_row'=>1]);
            
                    $itemCode = $dieSetData->category_code.'-'.$dieSetData->fg_item_code.'-'.$dieMasterData->set_no.$dieSetData->sr_no;
                    
                    $transData = [
                        'id' => $dieSetData->id,
                        'item_code' => $itemCode,
                        'set_no' => $dieMasterData->set_no
                    ];
                    $this->store($this->dieMaster, $transData);
                }
            }
            $result = ['status' => 1, 'message' => "Die Set Saved Successfully."];
            
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    } 

    public function getDieHistoryData($param){
        $data['tableName'] = $this->die_history;
        $data['select'] = "die_history.*,die_history.volume as total_volume,die_master.item_code as die_code,item_category.category_name,item_category.category_code,item_master.item_name,item_master.item_code,die_master.set_no,die_master.sr_no,die_log.production_time,die_production.mhr,die_pop_report.trans_number as pop_number,die_pop_report.report_date as pop_date";
        $data['leftJoin']['die_master'] = "die_master.id = die_history.die_id";
        $data['leftJoin']['item_category'] = "item_category.id = die_master.category_id";
        $data['leftJoin']['item_master'] = "item_master.id = die_history.fg_id";
		$data['leftJoin']['die_log'] = "die_log.die_id = die_history.die_job_id";
		$data['leftJoin']['die_production'] = "die_production.id = die_history.die_job_id";
		$data['leftJoin']['die_pop_report'] = "die_pop_report.die_id = die_history.die_id AND die_pop_report.die_job_id = die_history.die_job_id";

        if(!empty($param['category_id'])) { $data['where']['die_master.category_id'] = $param['category_id']; } 
        
        if(!empty($param['id'])) { 
			$data['select'] .= ",scrap.price as scrap_rate";
			$data['leftJoin']['die_history master_die'] = "master_die.die_id = die_history.die_id AND master_die.recut_no = 0 AND master_die.is_delete = 0";
			$data['leftJoin']['die_bom'] = "master_die.die_job_id = die_bom.die_id";
			$data['leftJoin']['item_master material'] = "material.id = die_bom.item_id";
			$data['leftJoin']['material_master'] = "material.grade_id = material_master.id";
			$data['leftJoin']['item_master scrap'] = "scrap.id = material_master.scrap_group";
			
			$data['where']['die_history.id'] = $param['id']; 
		}
		
		if(!empty($param['die_id'])) { $data['where']['die_history.die_id'] = $param['die_id']; }

        if(!empty($param['fg_id'])) { $data['where']['die_history.fg_id'] = $param['fg_id']; }
		
		if(isset($param['recut_no'])) { $data['where']['die_history.recut_no'] = $param['recut_no']; }

        if(!empty($param['group_by'])) { $data['group_by'][] = $param['group_by']; }
		
		if(!empty($param['order_by'])) { $data['order_by']['die_history.id'] = 'DESC'; }
		
		if(!empty($param['limit'])){ $data['limit'] = $param['limit']; }

        if(!empty($param['single_row'])):
            return $this->row($data);
        else:
            return $this->rows($data);
        endif;
    }

	public function getDieEntryDTRows($data){
        $data['tableName'] = 'die_history';
        $data['select'] = "die_history.id,die_master.status,item_category.category_name,fg.item_code as fg_item_code,fg.item_name as fg_item_name";
        $data['leftJoin']['die_master'] = "die_master.id = die_history.die_id";
		$data['leftJoin']['item_category'] = "die_master.category_id = item_category.id";
        $data['leftJoin']['item_master fg'] = "fg.id = die_master.fg_id";

        if($data['trans_status'] == '0') { 
			$data['select'] .= ",CONCAT(item_category.category_code, '-',fg.item_code, '-', die_master.set_no, die_master.sr_no, '/', LPAD(die_history.recut_no,2,'0')) as die_code";
            $data['where_in']['die_master.status'] = ['0,1,2,3'];
            $data['where']['die_history.acc_vou'] = NULL; 
        }elseif($data['trans_status'] == '4'){ 
			$data['select'] .= ",CONCAT(item_category.category_code, '-',fg.item_code, '-', die_master.set_no, die_master.sr_no, '/', LPAD(MAX(die_history.recut_no),2,'0')) as die_code";
            $data['where']['die_master.status'] = '4';
            $data['where']['die_history.rej_vou'] = NULL;
			$data['group_by'][] = 'die_history.die_id';
        }
        
		$data['order_by']['die_master.fg_id'] = 'ASC';
		$data['order_by']['die_master.set_no'] = 'ASC';

		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
		$data['searchCol'][] = "CONCAT(item_category.category_code, '-',fg.item_code, '-', die_master.set_no, die_master.sr_no, '/', LPAD(die_master.recut_no,2,'0'))";
		$data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "CONCAT(fg.item_code,' - ',fg.item_name)";
	
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        $result = $this->pagingRows($data);
        return $result;
    }

    public function saveAccVou($data) {
        try {
            $this->db->trans_begin();

            $result = $this->store($this->die_history, $data);
            
        if ($this->db->trans_status() !== FALSE) :
            $this->db->trans_commit();
            return $result;
        endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

	public function saveDieComponent($data) {
        try {
            $this->db->trans_begin();

            $catData = $this->itemCategory->getCategory(['id'=>$data['category_id']]);
            $itemData = $this->item->getItem(['id'=>$data['item_id']]);

            $srNo = $this->dieProduction->nextSrNo(['category_id'=>$data['category_id'], 'fg_id'=>$data['item_id']]);
            $itemCode = $catData->category_code.'-'.$itemData->item_code.'-0'.$srNo;
            
            $dieData = [
                'id' => $data['id'],
                'category_id' => $data['category_id'],
                'item_code' => $itemCode,
                'fg_id' => $data['item_id'],
                'sr_no' => $srNo,
                'status' => 0,
                'created_by' => $this->loginId,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $result = $this->store($this->dieMaster, $dieData, 'Die Component');

            $dieHistory = [
                'id' => '',
                'fg_id' => $data['item_id'],
                'category_id' => $data['category_id'],
                'die_id' => $result['id'],
                'volume' => 0, 
                'recut_date' => date('Y-m-d'),
                'created_by' => $this->loginId,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->store($this->die_history, $dieHistory);
            
        if ($this->db->trans_status() !== FALSE) :
            $this->db->trans_commit();
            return $result;
        endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteComponent($id){
        try{
            $this->db->trans_begin();

            $this->trash($this->die_history, ['die_id' => $id]);
            $result = $this->trash($this->dieMaster, ['id' => $id], 'Component');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

	public function updateDieHistory($data){
        try {
            $this->db->trans_begin();

            if(empty($data['die_job_id'])){
                $dmData = $this->getDieMasterData(['id'=>$data['die_id'], 'single_row'=>1]);
            }else{
                $dieProData = $this->dieProduction->getDieProductionData(['id'=>$data['die_job_id'],'single_row'=>1]);
                $dmData = $this->getDieMasterData(['die_job_id'=>$dieProData->id, 'single_row'=>1]);
            }

            // UPDATE DIE WEIGHT IN DIE SET
            $dieMasterData = [
                'weight' => (!empty($data['weight'])?$data['weight']:''),
                'height' => (!empty($data['height'])?$data['height']:''),
                'length' => (!empty($data['length'])?$data['length']:''),
                'width' => (!empty($data['width'])?$data['width']:''),
                'material_value' => (!empty($data['material_value'])?$data['material_value']:'') 
            ];
            if(!empty($data['attach_file'])){
                $dieMasterData['attach_file'] = $data['attach_file'];
            }

            if(empty($data['die_job_id'])){
                $dm = $this->edit($this->dieMaster,['id'=>$data['die_id']], $dieMasterData);
            }else{
                $dm = $this->edit($this->dieMaster,['die_job_id'=>$data['die_job_id']], $dieMasterData);
            }

            if(!empty($dieProData)){
                $bomData = $this->dieProduction->getDieProductionBom(['customWhere'=>'die_production.die_ref_id = '.$dieProData->die_ref_id.' AND die_production.trans_type = 1']);
                $mt_rate = ($dieProData->material_cost > 0 && $dieProData->material_weight > 0) ? $dieProData->material_cost / $dieProData->material_weight : 0;
                $material_cost = $mt_rate*$data['weight'];
                $process_cost = $dieProData->mhr*$dieProData->production_time;
                $scrap_cost = ($dieProData->material_weight -$data['weight']) * (!empty($bomData->scrap_rate)?$bomData->scrap_rate:0);
                $total_value = ($material_cost + $process_cost) - $scrap_cost;
            }

            $dieHistory = [
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

            if(empty($data['die_job_id'])){
                $this->edit($this->die_history,['die_id'=>$data['die_id']], $dieHistory);
            }else{
                $this->edit($this->die_history,['die_job_id'=>$data['die_job_id']], $dieHistory);
            }

            $result = ['status' => 1, 'message' => "Die History Updated Successfully."];

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