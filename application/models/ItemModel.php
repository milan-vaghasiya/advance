<?php
class ItemModel extends MasterModel{
    private $itemMaster = "item_master";
    private $unitMaster = "unit_master";
    private $productProcess = "product_process";
	private $processMaster = "process_master";
    private $inspectionParam = "inspection_param";
    private $item_revision = "item_revision";
    private $itemKit = "item_kit";
	private $dieKit = "die_kit";
	private $tcSpecification = "tc_specification";

	public function getItemCode($item_type=2){
        $queryData['tableName'] = $this->itemMaster;
        $queryData['select'] = "ifnull((MAX(CAST(REGEXP_SUBSTR(item_code,'[0-9]+') AS UNSIGNED)) + 1),1) as item_code";
        $queryData['where']['item_type'] = $item_type;
        $result = $this->row($queryData)->item_code;
        return $result;
    }
	
    public function getDTRows($data){
        $data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,CAST(item_master.gst_per AS FLOAT) as gst_per,item_category.category_name,item_category.is_inspection,material_master.material_grade,employee_master.emp_name as created_name,emp.emp_name as updated_name";
        $data['leftJoin']['material_master'] = "material_master.id = item_master.grade_id"; 
        $data['leftJoin']['item_category'] = "item_category.id  = item_master.category_id";
        $data['leftJoin']['employee_master'] = "employee_master.id  = item_master.created_by";
        $data['leftJoin']['employee_master emp'] = "emp.id  = item_master.updated_by";
		
        $data['where']['item_master.item_type'] = $data['item_type'];
        if($data['is_active'] == 2){
			$data['where']['item_master.is_active'] = 2; 
		}else{
			$data['where']['item_master.is_active !='] = 2; 
		}
		
		if($data['item_type'] == 5){
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "item_master.item_code";
			$data['searchCol'][] = "item_master.item_name";
			$data['searchCol'][] = "item_master.make_brand";
			$data['searchCol'][] = "item_master.part_no";
			$data['searchCol'][] = "item_master.installation_year";
			$data['searchCol'][] = "item_master.prev_maint_req";
		}elseif($data['item_type'] == 6){
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "item_master.item_code";
			$data['searchCol'][] = "item_master.item_name";
			$data['searchCol'][] = "item_category.category_name";
			$data['searchCol'][] = "item_master.uom";
			$data['searchCol'][] = "item_master.hsn_code";
			$data['searchCol'][] = "item_master.gst_per";
			$data['searchCol'][] = "item_master.permissible_error";
			$data['searchCol'][] = "item_master.cal_required";
			$data['searchCol'][] = "item_master.cal_freq";
			$data['searchCol'][] = "item_master.cal_reminder";
		}elseif($data['item_type'] == 3){
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "item_master.item_code";
			$data['searchCol'][] = "item_master.item_name";
			$data['searchCol'][] = "material_master.material_grade";
			$data['searchCol'][] = "item_category.category_name";
			$data['searchCol'][] = "item_master.uom";
			$data['searchCol'][] = "item_master.hsn_code";
			$data['searchCol'][] = "item_master.gst_per";
		}elseif($data['item_type'] == 7){
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "item_master.item_code";
			$data['searchCol'][] = "item_master.item_name";
			$data['searchCol'][] = "material_master.material_grade";
			$data['searchCol'][] = "item_category.category_name";
			$data['searchCol'][] = "item_master.uom";
			$data['searchCol'][] = "item_master.hsn_code";
			$data['searchCol'][] = "item_master.gst_per";
		}elseif($data['item_type'] == 4){
			$data['select'] .= " ,GROUP_CONCAT(DISTINCT fgItem.item_code SEPARATOR ', ') as product_code, GROUP_CONCAT(DISTINCT process_master.process_name SEPARATOR ', ') as process_name";
			$data['leftJoin']['item_master fgItem'] = "FIND_IN_SET(fgItem.id,item_master.fg_id) > 0";
			$data['leftJoin']['process_master'] = "FIND_IN_SET(process_master.id,item_master.process_id) > 0";
			$data['group_by'][] = "item_master.id";
			
			
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "item_master.item_code";
			$data['searchCol'][] = "item_master.item_name";
			$data['searchCol'][] = "item_category.category_name";
			$data['searchCol'][] = 'fgItem.item_code';
			$data['searchCol'][] = 'process_master.process_name';
			$data['searchCol'][] = "item_master.uom";
			$data['searchCol'][] = "item_master.hsn_code";
			$data['searchCol'][] = "item_master.gst_per";
			
		}else{
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "item_master.item_code";
			$data['searchCol'][] = "item_master.item_name";
			$data['searchCol'][] = "item_category.category_name";
			$data['searchCol'][] = "item_master.uom";
			$data['searchCol'][] = "item_master.hsn_code";
			$data['searchCol'][] = "item_master.gst_per";
			if($data['item_type'] == 1){
				$data['searchCol'][] = "item_master.mfg_status";
				$data['searchCol'][] = "item_master.mfg_type";				
			}
		}
		$data['searchCol'][] = "CONCAT(employee_master.emp_name,DATE_FORMAT(item_master.created_at,'%d-%m-%Y %H:%i:%s'))";
		$data['searchCol'][] = "CONCAT(emp.emp_name,DATE_FORMAT(item_master.updated_at,'%d-%m-%Y %H:%i:%s'))";
      

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getItemList($data=array()){
        $queryData['tableName'] = $this->itemMaster;
        $queryData['select'] = "item_master.*,item_master.id as item_id,item_category.category_name,item_category.batch_stock as stock_type,material_master.material_grade";
        $queryData['leftJoin']['item_category'] = "item_category.id  = item_master.category_id";
        $queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
        
		if(!empty($data['active_machine'])):
			$queryData['select'] .= ',bd.id as breakdown_id';
            $queryData['leftJoin']['(SELECT id,machine_id FROM machine_breakdown WHERE is_delete = 0 AND solution IS NULL GROUP BY machine_id) as bd'] = "bd.machine_id  = item_master.id";
        endif;
		
        if(!empty($data['item_type'])):
            $queryData['where_in']['item_master.item_type'] = $data['item_type'];
        endif;

		if(!empty($data['category_id'])):
            $queryData['where_in']['item_master.category_id'] = $data['category_id'];
        endif;

        if(!empty($data['ids'])):
            $queryData['where_in']['item_master.id'] = $data['ids'];
        endif;

        if(!empty($data['not_ids'])):
            $queryData['where_not_in']['item_master.id'] = $data['not_ids'];
        endif;
		
		if(isset($data['is_packing'])):
            $queryData['where']['item_master.is_packing'] = $data['is_packing'];
        endif;
		
		if(isset($data['grade_id'])):
            $queryData['where']['item_master.grade_id'] = $data['grade_id'];
        endif;
        
        if(!empty($data['active_item'])):
            $queryData['where_in']['item_master.is_active'] = $data['active_item'];
        else:
            $queryData['where']['item_master.is_active'] = 1;
        endif;

        return $this->rows($queryData);
    }

    public function getItem($data){
        $queryData['tableName'] = $this->itemMaster;
        $queryData['select'] = "item_master.*,item_category.category_name,item_category.batch_stock as stock_type,material_master.material_grade,material_master.scrap_group,item_master.uom as unit_name";
        $queryData['leftJoin']['item_category'] = "item_category.id  = item_master.category_id";
        $queryData['leftJoin']['material_master'] = "material_master.id  = item_master.grade_id";
		
        if(!empty($data['id'])):
            $queryData['where']['item_master.id'] = $data['id'];
        endif;

        if(!empty($data['item_code'])):
            $queryData['where']['item_master.item_code'] = trim($data['item_code']);
        endif;

        if(!empty($data['item_types'])):
            $queryData['where_in']['item_master.item_type'] = $data['item_types'];
        endif;
        
        if(!empty($data['customWhere'])):
            $queryData['customWhere'][] = $data['customWhere'];
        endif;

        if(!empty($data['item_name'])):
            $queryData['where']['item_master.item_name'] = $data['item_name'];
        endif;

        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
			
            if($this->checkDuplicate($data) > 0):
                $errorMessage['item_name'] = "Item is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;
			
			if($data['item_type'] != 7):
				if(!empty($data['item_code'])):
					if($this->checkDuplicateItemCode($data) > 0):
						$errorMessage['item_code'] = "Item Code is duplicate.";
						return ['status'=>0,'message'=>$errorMessage];
					endif;
				endif;
			endif;
			
			$data['uom'] = (!empty($data['uom']))?$data['uom']:'NOS';
			
            $result = $this->store($this->itemMaster,$data,"Item");
			
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
        $queryData['tableName'] = $this->itemMaster;

        if(!empty($data['item_name']))
            $queryData['where']['item_name'] = $data['item_name'];
        if(!empty($data['item_type']))
            $queryData['where']['item_type'] = $data['item_type'];
		if(!empty($data['item_code']))
            $queryData['where']['item_code'] = $data['item_code'];
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }
	
	public function checkDuplicateItemCode($data){
		$queryData['tableName'] = $this->itemMaster;

        if(!empty($data['item_type']))
            $queryData['where']['item_type'] = $data['item_type'];
		if(!empty($data['item_code']))
            $queryData['where']['item_code'] = $data['item_code'];
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
	}

    public function delete($id){
        try{
            $this->db->trans_begin();

            $checkData['columnName'] = ["item_id","scrap_group","ref_item_id"];
			$checkData['ignoreTable'] = ['item_master','inspection_param','item_revision']; 
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);
            
            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Item is currently in use. you cannot delete it.'];
            endif;

			$this->trash($this->inspectionParam,['item_id'=>$id]); 
            $this->trash($this->item_revision,['item_id'=>$id]); 
            $result = $this->trash($this->itemMaster,['id'=>$id],'Item');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function itemUnits(){
        $queryData['tableName'] = $this->unitMaster;
		return $this->rows($queryData);
	}

    public function itemUnit($id){
        $queryData['tableName'] = $this->unitMaster;
		$queryData['where']['id'] = $id;
		return $this->row($queryData);
	}
	
    public function getUnitNameWiseId($data=array()){
        $data['tableName'] = $this->unitMaster;
        if(!empty($data['unit_name'])){
            $data['where']['unit_name'] = $data['unit_name'];
        }
        return $this->row($data); 
    }

    /* Start Inspection Created By Rashmi @24-04-2024 */
	public function getInspectionParam($param=[]){ 
		$queryData['tableName'] = $this->inspectionParam;
		$queryData['select'] = "inspection_param.*,process_master.process_name,GROUP_CONCAT(DISTINCT item_category.category_name) as category_name,item_master.item_name,item_master.item_code,material_master.material_grade,item_master.part_no,item_master.item_code,party_master.party_name,ecn_master.cust_rev_no,ecn_master.rev_date,ecn_master.drw_no,mcTool.category_name as mc_category,ecn_master.rev_no as revNo,itemMaster.item_code as itemCode,employee_master.emp_name as key_contact,ecn_master.other_approve_date,ecn_master.quality_approve_date,ecn_master.eng_approve_date,GROUP_CONCAT(DISTINCT empMaster.emp_name) as core_team,item_master.mfg_status,rmMaster.dia as dia,gradeMaster.material_grade as rm_grade,gradeMaster.color_code as rm_color";	
		$queryData['leftJoin']['item_master'] = "item_master.id = inspection_param.item_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = inspection_param.process_id";
		$queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
		$queryData['leftJoin']['product_process'] = "product_process.item_id = inspection_param.item_id AND product_process.process_id = inspection_param.process_id AND product_process.is_delete = 0";
		$queryData['leftJoin']['ecn_master'] = "ecn_master.item_id = inspection_param.item_id AND ecn_master.rev_no = inspection_param.rev_no AND ecn_master.is_delete = 0";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = ecn_master.key_contact";
		$queryData['leftJoin']['employee_master empMaster'] = "FIND_IN_SET(empMaster.id, ecn_master.core_team) > 0";
		$queryData['leftJoin']['item_master itemMaster'] = "itemMaster.id = ecn_master.item_id";
		$queryData['leftJoin']['item_category'] = "FIND_IN_SET(item_category.id,inspection_param.category_id) > 0";
		$queryData['leftJoin']['item_category mcTool'] = "mcTool.id = inspection_param.machine_tool";
		
		$queryData['leftJoin']['item_kit'] = "item_kit.item_id = inspection_param.item_id AND item_kit.is_delete = 0";
		$queryData['leftJoin']['item_master rmMaster'] = "rmMaster.id = item_kit.ref_item_id";
		$queryData['leftJoin']['material_master gradeMaster'] = "gradeMaster.id = rmMaster.grade_id";
		
		if(!empty($param['item_id'])){
			$queryData['where']['inspection_param.item_id'] = $param['item_id'];
		}
		
		if(!empty($param['process_id'])){
			$queryData['where']['inspection_param.process_id'] = $param['process_id'];
		}
		
		if(!empty($param['rev_no'])){
			$queryData['where']['inspection_param.rev_no'] = $param['rev_no'];
		}
		if(!empty($param['param_type'])){
			$data['where']['inspection_param.param_type'] = $param['param_type'];
		}
		if(!empty($param['control_method'])){
			$queryData['where']['inspection_param.control_method'] = $param['control_method'];
		}

		if(!empty($param['control_method_not'])){
			$queryData['where_not_in']['inspection_param.control_method'] = $param['control_method_not'];
		}
		if(!empty($param['category_id'])){
			$queryData['customWhere'][] = "FIND_IN_SET(".$param['category_id'].",inspection_param.category_id) > 0";
		} 
		
		if(!empty($param['active_rev'])){
			$queryData['where']['ecn_master.status'] = 2;
		}
		
		$queryData['group_by'][] = "inspection_param.id";
		
		if(!empty($param['order_by'])){
			$queryData['order_by']['product_process.process_no'] = 'ASC'; 
		}else{
			$queryData['order_by']['product_process.sequence'] = 'ASC';
		}
		
		return $this->rows($queryData);
	}
	
	public function saveInspection($data){
		try{
            $this->db->trans_begin();
			
			if($this->checkDuplicateParameter($data) > 0):  
				$errorMessage['general_error'] = "Parameter is duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			endif;

			$result = $this->store($this->inspectionParam,$data,'Parameter');
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}
	
	public function checkDuplicateParameter($data){
        $queryData['tableName'] = $this->inspectionParam;
        if(!empty($data['item_id'])){
            $queryData['where']['item_id'] = $data['item_id'];
		}
		if(!empty($data['rev_no'])){
            $queryData['where']['rev_no'] = $data['rev_no'];
		}
        if(!empty($data['process_id'])){
            $queryData['where']['process_id'] = $data['process_id'];
		}
		if(!empty($data['parameter'])){
            $queryData['where']['parameter'] = $data['parameter'];
		}
		if(!empty($data['specification'])){
            $queryData['where']['specification'] = $data['specification'];
		}
		if(!empty($data['control_method'])){
            $queryData['where']['control_method'] = $data['control_method'];
		}

        if(!empty($data['id'])){
            $queryData['where']['id !='] = $data['id'];
		}

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

	public function saveInspectionParamExcel($postData){
		try{
            $this->db->trans_begin();
			
			foreach($postData as $data){
				if($this->checkDuplicateParameter($data) > 0){ 
					//print_r($this->checkDuplicateParameter($data)); 
					//$this->printQuery();
					//return ['status'=>0, 'message'=>'Duplicate Data Found..'];
				}else{
					$this->store($this->inspectionParam,$data,'Parameter');
				}
			}
			$result = ['status'=>1,'message'=>'Product process saved successfully.'];

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function deleteInspection($id){
		try{
			$this->db->trans_begin();
			$result = $this->trash($this->inspectionParam,['id'=>$id],"Record");
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}
    /*End Inspection */

    /* Product Option */
    public function getProdOptDTRows($data){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.*,item_master.uom AS unit_name,item_category.category_name";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['where']['item_master.item_type'] = 1;
		
        $data['searchCol'][] = "";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = ""; 
        $data['searchCol'][] = ""; 

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function checkProductOptionStatus($id){
		$result = new StdClass; 
        $result->bom=0; $result->process=0;

		$queryData = Array();
		$queryData['tableName'] = $this->itemKit;
		$queryData['where']['item_id'] = $id;
		$bomData = $this->rows($queryData);
		$result->bom = count($bomData);
		
		$queryData = Array();
		$queryData['tableName'] = $this->productProcess;
		$queryData['where']['item_id'] = $id;
		$processData = $this->rows($queryData);
		$result->process = count($processData);
		
        $queryData = Array();
		$queryData['tableName'] = $this->productProcess;
		$queryData['where']['item_id'] = $id;
		$queryData['where']['cycle_time >'] = 0;
		$ctData = $this->rows($queryData);
		$result->cycleTime=count($ctData);

		return $result;
	}

    public function getProductProcessList($param = []){
		$queryData['tableName'] = $this->productProcess;
		$queryData['select'] = "product_process.id, product_process.item_id, product_process.process_id, product_process.cycle_time, product_process.finish_wt, product_process.sequence, process_master.process_name, product_process.process_cost, product_process.mfg_instruction, product_process.output_qty, product_process.uom, product_process.drawing_file, product_process.process_no, product_process.is_active";
		$queryData['leftJoin']['process_master'] = "process_master.id = product_process.process_id";
		if(!empty($param['process_cost_sum'])){
			$queryData['select'] .= ',SUM((CASE WHEN product_process.uom = "KGS" THEN product_process.finish_wt ELSE 1 END) * product_process.process_cost) AS total_process_cost';
		}
		if(!empty($param['max_output_qty'])){
			$queryData['select'] .= ',MAX(product_process.output_qty) AS max_output_qty';
		}
		if(!empty($param['order_process_ids'])){
			$queryData['order_by']['FIELD(product_process.process_id,'.$param['order_process_ids'].')'] = '';
		}else{
			$queryData['order_by']['product_process.sequence'] = 'ASC';
		}
		
		if(!empty($param['is_active'])){ $queryData['where_in']['product_process.is_active'] = $param['is_active']; }
		if(!empty($param['item_id'])){ $queryData['where']['product_process.item_id'] = $param['item_id']; }
		if(!empty($param['item_ids'])){ $queryData['where_in']['product_process.item_id'] = $param['item_ids']; }
		if(!empty($param['process_id'])){ $queryData['where_in']['product_process.process_id'] = $param['process_id']; }
		if(!empty($param['group_by'])) { $queryData['group_by'][] = $param['group_by']; }
		
        if(!empty($param['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
	}

    public function groupSearch($data){
		$data['tableName'] = $this->itemKit;
		$data['select'] = 'group_name';
		$data['where']['item_id'] = $data['item_id'];
        $data['group_by'][]="group_name";
		$result = $this->rows($data);
		$searchResult = array();
		foreach ($result as $row) {
			$searchResult[] = $row->group_name;
		}
		return $searchResult;
	}

    public function saveProductKit($data){
		try{
            $this->db->trans_begin();

			if($this->checkDuplicateBom($data) > 0):  
				$errorMessage['kit_item_id'] = "Item Bom is duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			endif;
           

            $itemKitData = [
                'id'=>$data['id'],
                'group_name'=>$data['group_name'],
                'item_id'=>$data['item_id'],
                'ref_item_id'=>$data['kit_item_id'],
                'ref_id'=>$data['ref_id'],
                'process_id'=>$data['process_id'],
                'qty'=>$data['kit_item_qty']
            ];
			if($data['ref_id'] != 0)
			{
				$altData = $this->getProductKitData(['id'=>$data['ref_id'],'single_row'=>1]);
				$itemKitData['alt_item_id'] = (!empty($altData->ref_item_id) ? $altData->ref_item_id : 0);
			}
            $result = $this->store($this->itemKit,$itemKitData,'Product Bom');
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
		   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

    public function checkDuplicateBom($data){
        $queryData['tableName'] = $this->itemKit;

        if(!empty($data['kit_item_id']))
            $queryData['where']['ref_item_id'] = $data['kit_item_id'];
		
		if(!empty($data['item_id']))
			$queryData['where']['item_id'] = $data['item_id'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getProductKitData($param = []){
		$queryData['tableName'] = $this->itemKit;
		$queryData['select'] = "item_kit.*,item_master.item_name,item_master.item_code,item_master.uom,IFNULL(process_master.process_name,'Initial Stage') as process_name,item_master.item_type,product.item_name as product_name,product.item_code as product_code,item_category.category_name,item_master.gst_per,item_master.price";
		$queryData['leftJoin']['item_master'] = "item_master.id = item_kit.ref_item_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = item_kit.process_id";
		$queryData['leftJoin']['item_master product'] = "product.id = item_kit.item_id";
        $queryData['leftJoin']['item_category'] = "item_category.id = item_master.item_type"; 

		if(!empty($param['with_alt_items'])){
			$queryData['select'] .= ',altKit.ref_item_id as alt_ref_item,altKit.process_id as alt_process_id,altKit.qty as alt_qty,altItem.item_code as alt_item_code,altItem.item_name as alt_item_name';
			$queryData['leftJoin']['item_kit altKit'] = "altKit.ref_id = item_kit.id";
			$queryData['leftJoin']['item_master altItem'] = "altItem.id = altKit.ref_item_id";
		}

        if(!empty($param['item_id'])){$queryData['where_in']['item_kit.item_id'] = $param['item_id'];}
        if(!empty($param['process_id'])){$queryData['where']['item_kit.process_id'] = $param['process_id'];}
        if(!empty($param['group_name'])){$queryData['where']['item_kit.group_name'] = $param['group_name'];}
        if(!empty($param['is_main'])){$queryData['where']['item_kit.ref_id'] = 0;}
        if(!empty($param['ref_id'])){$queryData['where']['item_kit.ref_id'] = $param['ref_id'];}
        if(!empty($param['alt_item_id'])){$queryData['where']['item_kit.alt_item_id'] = $param['alt_item_id'];}
        if(!empty($param['id'])){$queryData['where']['item_kit.id'] = $param['id'];}
        if(!empty($param['ref_item_id'])){$queryData['where']['item_kit.ref_item_id'] = $param['ref_item_id'];}
        if(!empty($param['item_type'])){$queryData['where']['item_master.item_type'] = $param['item_type'];}
		if (!empty($param['rm_ids'])) { $queryData['where_in']['item_kit.ref_item_id'] = str_replace("~", ",", $param['rm_ids']); }
		if(!empty($param['not_in_item_type'])){$queryData['where']['item_master.item_type !='] = $param['not_in_item_type'];}
        if(!empty($param['packing_type'])){$queryData['where']['item_kit.packing_type'] = $param['packing_type'];}
		
        if(!empty($param['group_by'])){ $queryData['group_by'][] = $param['group_by']; }
        
		if(!empty($param['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }		
	}

    public function deleteProductKit($id){
        try{
			$this->db->trans_begin();

			$result = $this->trash($this->itemKit,['id'=>$id],'Product Bom');

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function saveProductProcess($data){
		try{
            $this->db->trans_begin();
			
				if($this->checkDuplicateProcess($data) > 0):
					$errorMessage['process_id'] = "Process is duplicate.";
					return ['status'=>2,'message'=>$errorMessage];
				endif;
				$queryData = array();
				$queryData['select'] = "MAX(sequence) as sequence";
				$queryData['where']['item_id'] = $data['item_id'];
				$queryData['where']['is_delete'] = 0;
				$queryData['tableName'] = $this->productProcess;
				$sequence = $this->specificRow($queryData)->sequence;
				$nextsequence = (!empty($sequence))?($sequence + 1):1; 
				
				$productProcessData = [
					'id'=>"",
					'item_id'=>$data['item_id'],
					'process_id'=>$data['process_id'],
					'sequence'=>$nextsequence,
					'created_by' => $this->session->userdata('loginId')
				];
				$this->store($this->productProcess,$productProcessData,'');
    
    		$result = ['status'=>1,'message'=>'Product process saved successfully.'];

    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
	}

	public function checkDuplicateProcess($data){
        $queryData['tableName'] = $this->productProcess;

        if(!empty($data['process_id']))
            $queryData['where']['process_id'] = $data['process_id'];
        if(!empty($data['item_id']))
            $queryData['where']['item_id'] = $data['item_id'];
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

	public function deleteProductProcess($data){
        try{
			$this->db->trans_begin();

			$recData = $this->getProductProcessPrcWise(['item_id'=>$data['item_id'],'process_ids'=>$data['process_id']]);
			
			if(!empty($recData->prc_number)){
				$result = ['status'=>2,'message'=>'The process is currently in use. you cannot delete it.'];
			}else{
				$result = $this->trash($this->productProcess,['id'=>$data['id']],'Product Process');
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

	public function getProductProcessPrcWise($data){
		$queryData['tableName']  = 'prc_master';
		$queryData['select'] = 'prc_master.*,prc_detail.process_ids,prc_master.prc_number';
		$queryData['leftJoin']['prc_detail'] = 'prc_detail.prc_id = prc_master.id';
		if(!empty($data['item_id'])){$queryData['where']['prc_master.item_id'] = $data['item_id'];}
		if(!empty($data['process_ids'])){$queryData['where']['find_in_set("'.$data['process_ids'].'", prc_detail.process_ids) >'] = 0;}
		$result =  $this->row($queryData);
		return $result;
	}

    public function updateProductProcessSequance($data){
		try{
            $this->db->trans_begin();
            
    		$ids = explode(',', $data['id']);
    		$i=1;
    		foreach($ids as $pp_id):
    			$seqData=Array("sequence"=>$i++);
    			$this->edit($this->productProcess,['id'=>$pp_id],$seqData);
    		endforeach;

    		$result = ['status'=>1,'message'=>'Process Sequence updated successfully.'];

    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
	}

	public function setProductionType($data) {
        try{
            $this->db->trans_begin();
            
            $result = $this->store($this->itemMaster, ['id'=> $data['item_id'], 'production_type' => $data['production_type'], 'cutting_flow' => $data['cutting_flow']]);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	    
    }
    
	public function deleteDrawingFile($data){ 
        try{
            $this->db->trans_begin();

			$result = $this->edit($this->productProcess, ['id'=>$data['id'], 'item_id'=>$data['item_id'],'process_id'=>$data['process_id']], ['drawing_file'=>NULL]);

			if (!empty($data['drawing_file'])) {
				$old_file_path = FCPATH."assets/uploads/process_drg/" . $data['drawing_file'];
				if (file_exists($old_file_path)) {
					unlink($old_file_path);
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
	/* End Product Option */
	
	public function getInspectionParameter($postData=[]){
		$data['tableName'] = $this->inspectionParam;
		$data['select'] = "inspection_param.*,item_master.item_name";
		$data['leftJoin']['item_master'] = "item_master.id = inspection_param.item_id";
		if(!empty($postData['item_id'])){ $data['where']['item_id'] = $postData['item_id']; }
        if(!empty($postData['process_id'])){ $data['where']['inspection_param.process_id'] = $postData['process_id']; }
        if(!empty($postData['control_method'])){  $data['where']['find_in_set("'.$postData['control_method'].'",REPLACE(control_method, " ", "")) > '] = 0;  }
        if(!empty($postData['rev_no'])){ $data['where']['find_in_set("'.$postData['rev_no'].'",inspection_param.rev_no) > '] = 0; }
		$data['group_by'][] = "inspection_param.id";
		$result = $this->rows($data);
		//$this->printQuery();
		return $result;
	}

	public function saveProductProcessCycleTime($data){
		try{
            $this->db->trans_begin();
			if(!empty($data['ctData'])):
				foreach($data['ctData'] as $key => $val):
					/*if(!empty($val['process_no'])):
						if($this->checkDuplicateProcessNo($val) > 0): 
							$errorMessage['process_no_'.$key] = "Process No. is duplicate.";
							return ['status'=>0,'message'=>$errorMessage];
						endif;
					endif;*/
					
					$val['drawing_file'] = $data['drawing_file'][$key];
					$val['is_active'] = $data['is_active'][$key];
					$this->store($this->productProcess,$val,'');
				endforeach;
			endif;
    
    		$result = ['status'=>1,'message'=>'Cycle Time Updated successfully.'];
			
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
	}
		
    public function getProductProcessForSelect($id){
		$data['select'] = "process_id";
		$data['where']['item_id'] = $id;
		$data['tableName'] = $this->productProcess;
		$result = $this->rows($data);
		$process = array();
		if($result){foreach($result as $row){$process[] = $row->process_id;}}
		return $process;
	}

	public function getDieSetData($param){
		$data['tableName'] = $this->dieKit;
		$data['select'] = "die_kit.*,item_category.category_name,die_kit.qty,die_kit.id,item_master.item_code,item_master.item_name,item_category.category_code";
		$data['leftJoin']['item_category'] = "item_category.id = die_kit.ref_cat_id";
		$data['leftJoin']['item_master'] = "item_master.id = die_kit.ref_item_id";
		$data['where']['die_kit.item_id'] = $param['item_id'];
		if(!empty($param['group_by'])) { $data['group_by'][] = $param['group_by']; }
		if(!empty($param['category_id'])) { $data['where']['die_kit.ref_cat_id'] = $param['category_id']; }
		$result = $this->rows($data);
		return $result;
	}
	
	public function activeInactiveDie($data){
        try{
            $this->db->trans_begin();

			$this->edit($this->dieKit, ['item_id'=>$data['item_id'], 'ref_cat_id'=>$data['ref_cat_id']], ['is_active'=>$data['is_active']]);

            $result = ['status'=>1,'message'=>"Die ".$data['msg']." Successfully."];
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
	
	/* Die Bom */
	public function saveDieBom($data){
		try{
            $this->db->trans_begin();

			if($this->checkDuplicateDieBom($data) > 0):  
				$errorMessage['ref_item_id'] = "Item Bom is duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			endif;           

            $itemBomData = [
                'id'=>$data['id'],
                'item_id'=>$data['item_id'],
                'ref_cat_id'=>$data['ref_cat_id'],
                'ref_item_id'=>$data['ref_item_id'],
				'qty'=>$data['qty']
            ];
            $result = $this->store($this->dieKit,$itemBomData,'Die Bom');
			
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
		   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

    public function checkDuplicateDieBom($data){
        $queryData['tableName'] = $this->dieKit;
		
		if(!empty($data['item_id']))
			$queryData['where']['item_id'] = $data['item_id'];
		
		if(!empty($data['ref_cat_id']))
			$queryData['where']['ref_cat_id'] = $data['ref_cat_id'];

		if(!empty($data['ref_item_id']))
			$queryData['where']['ref_item_id'] = $data['ref_item_id'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

	public function deleteDieBom($id){
        try{
			$this->db->trans_begin();

			$result = $this->trash($this->dieKit,['id'=>$id],'Die Bom');

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}
	
	public function getLatestRev($param){
		$data['tableName'] = "ecn_master";
		$data['select'] = "ecn_master.*";
		$data['customWhere'][] = 'ecn_master.id IN(SELECT max(id) FROM ecn_master WHERE ecn_master.item_id ='.$param['item_id'].')';
		$result = $this->row($data);
		return $result;
	}

	public function saveItemExcel($postData){
        try{
            $this->db->trans_begin();

			foreach($postData AS $data){
				if($this->checkDuplicate($data) > 0):
					$errorMessage['general_error'] = "Item  is duplicate.";
					return ['status'=>0,'message'=>$errorMessage];
				endif;
				
				$result = $this->store($this->itemMaster,$data,"Item");
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

	public function savePackingStandard($data){
		try{
            $this->db->trans_begin();

			if($this->checkDuplicateStandard($data) > 0):  
				$errorMessage['ref_item_id'] = "Packing Standard is duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			endif;
           
            $result = $this->store($this->itemKit, $data, 'Packing Standard');

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
		   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function checkDuplicateStandard($data){
        $queryData['tableName'] = $this->itemKit;

        if(!empty($data['ref_item_id']))
            $queryData['where']['ref_item_id'] = $data['ref_item_id'];
		
		if(!empty($data['item_id']))
			$queryData['where']['item_id'] = $data['item_id'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

	public function deletePackingStandard($id){
        try{
			$this->db->trans_begin();

			$result = $this->trash($this->itemKit, ['id'=>$id], 'Packing Standard');

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function saveTcSpecification($data){
        try{
            $this->db->trans_begin();
			
            $result = $this->store($this->tcSpecification,$data,"TC Specification");
			
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
	
	public function getTcSpecificationData($data=array()){
        $queryData['tableName'] = $this->tcSpecification;
        $queryData['select'] = "tc_specification.*";
        
		if(!empty($data['id'])):
            $queryData['where']['tc_specification.id'] = $data['id'];
        endif;
		
		if(!empty($data['item_id'])):
            $queryData['where']['tc_specification.item_id'] = $data['item_id'];
        endif;
		
		if(empty($data['singleRow'])){
			$result = $this->row($queryData);
		}else{
			$result = $this->rows($queryData);
		}
        
        return $result;
    }
	
	/** Start Tool Bom*/
	public function getToolBomData($data){
		$queryData['tableName'] = 'tool_kit';
		$queryData['select'] = 'tool_kit.*,item_master.item_name,process_master.process_name,tool_kit.process_id as processIds';
		$queryData['leftJoin']['item_master'] = "item_master.id = tool_kit.tool_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = tool_kit.process_id";
		$queryData['where']['tool_kit.item_id'] = $data['item_id'];
		$result = $this->rows($queryData);
		return $result;
	}

	public function saveToolBom($data){
		try{
            $this->db->trans_begin();

			$result = $this->store('tool_kit',$data,'Tool Bom');

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function deleteToolBom($id){
		try{
			$this->db->trans_begin();
			$result = $this->trash('tool_kit',['id'=>$id],"Tool Bom");
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}
	/** End Tool Bom*/
	
	public function checkDuplicateProcessNo($data){ 
        $queryData['tableName'] = $this->productProcess;

        if(!empty($data['process_no']))
            $queryData['where']['process_no'] = $data['process_no'];
		if(!empty($data['item_id']))
             $queryData['where']['item_id'] = $data['item_id'];
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }
}
?>