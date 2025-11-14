<?php
class StoreModel extends MasterModel{
    private $issueRegister = "issue_register";
    private $materialReturn = "material_return";
    private $stockTransation = "stock_trans";
    private $store_request = "store_request";

    public function getNextIssueNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'issue_register';
        $queryData['select'] = "ifnull(MAX(issue_no + 1),1) as issue_no";
		$queryData['where']['issue_register.issue_date >='] = $this->startYearDate;
		$queryData['where']['issue_register.issue_date <='] = $this->endYearDate;

		$issue_no = $this->specificRow($queryData)->issue_no;
		return $issue_no;
    }

    public function getIssueDTRows($data){
        $data['tableName'] = $this->issueRegister;
        //$data['select'] = "issue_register.*,item_master.item_name,item_master.item_code,(CASE WHEN issue_register.issue_type = 3 THEN die_production.trans_number ELSE (CASE WHEN issue_register.issue_type = 2 THEN prc_master.prc_number ELSE '' END) END) as prc_number,item_master.item_type,(issue_register.issue_qty - issue_register.return_qty) as pending_qty";
        $data['select'] = "issue_register.*,item_master.item_name,item_master.item_code,(CASE WHEN issue_register.issue_type = 3 THEN die_production.trans_number ELSE (CASE WHEN issue_register.issue_type = 2 THEN prc_master.prc_number ELSE (CASE WHEN issue_register.issue_type = 4 THEN store_request.trans_number ELSE '' END) END) END) as prc_number,item_master.item_type,(issue_register.issue_qty - issue_register.return_qty) as pending_qty,employee_master.emp_name as issue_name,employee_master.unit_id,createdBy.emp_name as created_name";
		
        $data['leftJoin']['item_master'] = "item_master.id  = issue_register.item_id";
        $data['leftJoin']['item_category'] = "item_category.id  = item_master.category_id";
        $data['leftJoin']['prc_master'] = "issue_register.prc_id  = prc_master.id";
        $data['leftJoin']['die_production'] = "issue_register.prc_id  = die_production.id";
        $data['leftJoin']['store_request'] = "issue_register.prc_id = store_request.id";
        $data['leftJoin']['employee_master'] = "employee_master.id = issue_register.issued_to";
        $data['leftJoin']['employee_master createdBy'] = "createdBy.id = issue_register.created_by";

        if($data['item_type'] == 1){
            $data['where_in']['item_master.item_type'] = "1,3,7";
        }else{
            $data['where_in']['item_master.item_type'] = "2,9"; 
        }
        if($data['status'] == 2){
            $data['where']['item_category.is_return'] = 1;
			$data['where']['(issue_register.issue_qty - issue_register.return_qty) >'] = 0;
        }
        $data['order_by']['issue_register.issue_number'] = 'ASC';
        if($data['status'] == 1){
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "issue_register.issue_number";
            $data['searchCol'][] = "DATE_FORMAT(issue_register.issue_date,'%d-%m-%Y')";
            $data['searchCol'][] = "prc_master.prc_number";
            $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
            $data['searchCol'][] = "issue_register.issue_qty";
            $data['searchCol'][] = "issue_register.batch_no";
            $data['searchCol'][] = "employee_master.emp_name";
            $data['searchCol'][] = "(CASE WHEN employee_master.unit_id = 1 THEN 'UNIT 1' WHEN employee_master.unit_id = 2 THEN 'UNIT 2' ELSE '' END)";
		    $data['searchCol'][] = "CONCAT(createdBy.emp_name,DATE_FORMAT(issue_register.created_at,'%d-%m-%Y %H:%i:%s'))";
			$data['searchCol'][] = "issue_register.remark";
			
            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        }elseif($data['status'] == 2){
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "issue_register.issue_number";
            $data['searchCol'][] = "DATE_FORMAT(issue_register.issue_date,'%d-%m-%Y')";
            $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
            $data['searchCol'][] = "issue_register.issue_qty";
            $data['searchCol'][] = "issue_register.return_qty";
            $data['searchCol'][] = "ifnull(issue_register.issue_qty - issue_register.return_qty,0.00)";
			$data['searchCol'][] = "issue_register.remark";
            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        }
		
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getInspDTRows($data) {
        $data['tableName'] = $this->materialReturn;
        $data['select'] = "material_return.*,issue_register.issue_number";
        $data['leftJoin']['issue_register'] = "material_return.issue_id  = issue_register.id";
        $data['where']['material_return.trans_type'] = $data['trans_type'];

        if($data['trans_type'] == 1){
            $data['where']['(material_return.total_qty - material_return.insp_qty) > '] = 0;
        }

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "issue_register.issue_number";
        $data['searchCol'][] = "DATE_FORMAT(material_return.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "material_return.total_qty";
        $data['searchCol'][] = "material_return.batch_no";
        $data['searchCol'][] = "material_return.remark";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getIssueRequest($param) {
        $queryData = array();          
		$queryData['tableName'] = "issue_register";
        if(!empty($param['id'])){ $queryData['where']['id'] = $param['id']; }
        if(!empty($param['item_id'])){ $queryData['where']['item_id'] = $param['item_id']; }
        $result = $this->row($queryData);
        return $result;
    }

    public function getMaterialIssueData($param) {
        $queryData = array();          
		$queryData['tableName'] = "issue_register";
        $queryData['select'] = "issue_register.*, IFNULL(im.item_name,'') as item_name, IFNULL(prc_master.prc_number,'') as prc_number,im.uom";
        $queryData['leftJoin']['item_master im'] = "im.id = issue_register.item_id ";
        $queryData['leftJoin']['prc_master'] = "prc_master.id = issue_register.prc_id";

        if(!empty($param['supplier_data'])){
            $queryData['select'] .= ',party_master.party_name,batch_history.party_id';
            $queryData['leftJoin']['batch_history'] = 'issue_register.item_id = issue_register.item_id AND issue_register.batch_no = batch_history.batch_no AND batch_history.is_delete = 0';
            $queryData['leftJoin']['party_master'] = 'batch_history.party_id = party_master.id';
        }
        if(!empty($param['sum_data'])){
            $queryData['select'] .= ',SUM(issue_qty) as issue_qty';
            
        }

        if(!empty($param['id'])){ $queryData['where']['issue_register.id'] = $param['id']; }
        if(!empty($param['item_id'])){ $queryData['where']['issue_register.item_id'] = $param['item_id']; }
        if(!empty($param['prc_id'])){ $queryData['where']['issue_register.prc_id'] = $param['prc_id']; }
        if(!empty($param['batch_no'])){ $queryData['where']['issue_register.batch_no'] = $param['batch_no']; }
        if(!empty($param['issue_date'])){ $queryData['where']['issue_register.issue_date'] = $param['issue_date']; }
        if(!empty($param['from_date'])){ $queryData['where']['issue_register.issue_date >='] = $param['from_date']; }
        if(!empty($param['to_date'])){ $queryData['where']['issue_register.issue_date <='] = $param['to_date'];  }
        if(!empty($param['issue_type'])){ $queryData['where']['issue_register.issue_type'] = $param['issue_type']; }
        if(!empty($param['customWhere'])){ $queryData['customWhere'][] = $param['customWhere']; }

        if(!empty($param['group_by'])){ $queryData['group_by'][] = $param['group_by']; }

        if(!empty($param['skey'])){
			$queryData['like']['issue_register.trans_number'] = str_replace(" ", "%", $param['skey']);
			$queryData['like']['issue_register.trans_date'] = str_replace(" ", "%", $param['skey']);
			$queryData['like']['prc_master.prc_number'] = str_replace(" ", "%", $param['skey']);
			$queryData['like']['im.item_name'] = str_replace(" ", "%", $param['skey']);
        }

        if(!empty($param['limit'])){ $queryData['limit'] = $param['limit']; }
        
		if(isset($param['start'])){ $queryData['start'] = $param['start']; }
		
		if(!empty($param['length'])){ $queryData['length'] = $param['length']; }

        $queryData['order_by']['issue_register.issue_date'] = 'DESC';
		$queryData['order_by']['issue_register.id'] = 'DESC';

        if(!empty($param['single_row'])){
            $result = $this->row($queryData);
        }else{
            $result = $this->rows($queryData);
        }
        
        return $result;
    }

    public function getMaterialData($param) {
        $data['tableName'] = $this->materialReturn;
        $data['select'] = "material_return.*";
        $data['leftJoin']['issue_register'] = "issue_register.id  = material_return.issue_id";
		$data['where']['material_return.id'] = $param['id'];
        return $this->row($data);
    }

    public function saveIssueRequisition($data){           
        try {
            $this->db->trans_begin();

            foreach ($data['batch_qty'] as $key => $value) {

                $issue_no = $this->getNextIssueNo();

                if(!empty($value) && $value > 0) {

                    $issueData = [
                        'id' => '',
                        'issue_type' => (!empty($data['issue_type']) ? $data['issue_type'] : 1),
                        'issue_no' => $issue_no,
                        'issue_number' => 'ISU/'.str_pad($issue_no, 5, '0', STR_PAD_LEFT),
                        'issue_date' => date("Y-m-d"),
                        'item_id' => $data['item_id'],
                        'batch_no' => $data['batch_no'][$key],
                        'heat_no' => $data['heat_no'][$key],
                        'prc_id' => $data['prc_id'],
                        'issue_qty' => $value,
                        'issued_to' => $data['issued_to'],
						'remark' => $data['remark'],
                        'created_by' => $data['created_by']
                    ];
                    $result = $this->store($this->issueRegister, $issueData, 'Issue Requisition');

                    $stockMinusQuery = [
                        'id' => "",
                        'trans_type' => ((!empty($data['issue_type']) && $data['issue_type'] == 3) ? 'SDI' :'SSI'),
                        'trans_date' => date("Y-m-d"),
                        'location_id'=> $data['location_id'][$key],
                        'batch_no' => $data['batch_no'][$key],
                        'item_id' => $data['item_id'],
                        'qty' => $value,
                        'p_or_m' => -1,
                        'main_ref_id' => $result['insert_id'],
                        'child_ref_id' =>  $data['prc_id'],
                        'ref_no' => 'ISU/'.str_pad($issue_no, 5, '0', STR_PAD_LEFT),
                        'created_by' => $data['created_by']
                    ];
                    $issueTrans = $this->store('stock_trans', $stockMinusQuery);
					
					if(!empty($data['prc_id']) && $data['issue_type'] == 4){
                        $setData = Array();
                        $setData['tableName'] = 'store_request';
                        $setData['where']['id'] = $data['prc_id'];
                        $setData['set']['issue_qty'] = 'issue_qty, + '.$value;
                        $this->setValue($setData);
                    }
                }
            }

            if(!empty($data['prc_id']) && $data['issue_type'] == 2){
                $this->store("prc_master",['id'=>$data['prc_id'],'batch_no'=>$data['bom_batch']]);
            }

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material issue Successfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteIssueRequisition($data) {
        try {
            $this->db->trans_begin();

            $issueData = $this->getIssueRequest(['id' =>$data['id']]);
            if(!empty($issueData)){
                /** Cutting & Reguler PRC */
                if(!empty($data['prc_id']) && $issueData->issue_type == 2){
                    $postData =['prc_id'=>$data['prc_id'],'item_id'=>$issueData->item_id,'production_data'=>1,'stock_data'=>1,'single_row'=>1];
		            $prcData = $this->sop->getPrc(['id'=>$data['prc_id']]);
                    if($prcData->prc_type == 1 && $prcData->cutting_flow == 2){
                        $processArray = explode(",",$prcData->process_ids);
                        $postData['log_process_id'] = $processArray[0];
                    }
                   
                    if($prcData->prc_type == 2){
                        $stockData = $this->cutting->getCuttingBomData(['prc_id'=>$data['prc_id'],'single_row'=>1,'stock_data'=>1,'production_data'=>1]);
                        $stockQty = $stockData->issue_qty - (( $stockData->cutting_cons) + $stockData->return_qty);

                    }else{
                        $stockData = $this->sop->getPrcBomData($postData);
                        $stockQty = $stockData->issue_qty - ((( $stockData->production_qty * $stockData->ppc_qty)/$stockData->output_qty) + $stockData->return_qty);
                    }
                    if($issueData->issue_qty > $stockQty){
                        return ['status' => 0, 'message' => 'You can not delete is record'];
                    }
                }

                /** Die Production */
                if(!empty($data['prc_id']) && $issueData->issue_type == 3){
                    $dieData = $this->dieProduction->getDieProduction(['id'=>$data['prc_id']]);
                    if($dieData->status > 2 ){
                        return ['status' => 0, 'message' => 'You can not delete is record'];
                    }
                    $logData = $this->dieProduction->getDieLogData(['die_id'=>$data['prc_id']]);

                    if(!empty($logData)){
                        return ['status' => 0, 'message' => 'You can not delete is record'];
                    }
                }

                $stockData = $this->remove($this->stockTransation, ['trans_type'=>'SSI', 'main_ref_id'=>$data['id'], 'child_ref_id'=>$issueData->prc_id]);
				
				if(!empty($data['prc_id']) && $data['issue_type'] == 4){
                    $setData = Array();
                    $setData['tableName'] = 'store_request';
                    $setData['where']['id'] = $data['prc_id'];
                    $setData['set']['issue_qty'] = 'issue_qty, - '.$issueData->issue_qty;
                    $this->setValue($setData);
                }

                
			}
            $this->trash($this->issueRegister,['id'=>$data['id']], 'Delete Issue Requisitin');

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material Issue suucessfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function saveReturnReq($data) {
        try {
			$this->db->trans_begin();

            $issue_number = (!empty($data['issue_number'])) ? $data['issue_number'] : "" ;
            $location_id = (!empty($data['location_id'])) ? $data['location_id'] : "" ;
            unset($data['issue_number']);unset($data['location_id']);

            $data['id'] = '';
            $result = $this->store($this->materialReturn, $data, 'Return Material');

            if($data['trans_type'] == 1){
                $setData = array();
                $setData['tableName'] = $this->issueRegister;
                $setData['where']['id'] = $data['issue_id'];
                $setData['set']['return_qty'] = 'return_qty, + ' . $data['total_qty'];
                $this->setValue($setData);
            }

            if($data['trans_type'] == 2){

                if($data['usable_qty'] != "" && $data['usable_qty'] != 0)
                {
                    $stockPlusQuery = [
                        'id' => "",
                        'trans_type' => 'SSR',
                        'trans_date' => date("Y-m-d"),
                        'location_id'=> $location_id,
                        'batch_no' => $data['batch_no'],
                        // 'heat_no' => $data['heat_no'],
                        'item_id' => $data['item_id'],
                        'qty' => $data['usable_qty'],
                        'p_or_m' => 1,
                        'main_ref_id' =>  $data['issue_id'],
                        'child_ref_id' => $result['insert_id'],
                        'ref_no' => $issue_number,
                        'created_by' => $data['created_by']
                    ];
                    $issueTrans = $this->store('stock_trans', $stockPlusQuery);
                }

                $setData = array();
                $setData['tableName'] = $this->materialReturn;
                $setData['where']['id'] = $data['ref_id'];
                $setData['set']['insp_qty'] = 'insp_qty, + ' . $data['insp_qty'];
                $this->setValue($setData);
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

	public function getPrcMaterialDTRows($data){
        $data['tableName'] = "prc_bom";
        $data['select'] = "prc_bom.*,prc_master.prc_number,prc_master.prc_date,prc_master.prc_qty,item_master.item_name,item_master.item_type,item_master.uom,item_master.item_code,IFNULL(stock_trans.issue_qty,0) as issue_qty,SUM((prc_bom.ppc_qty * prc_master.prc_qty) - IFNULL(stock_trans.issue_qty,0)) AS pending_qty";

        $data['leftJoin']['prc_master'] = "prc_master.id = prc_bom.prc_id";
        $data['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
        $data['leftJoin']['(SELECT abs(SUM(stock_trans.qty)) as issue_qty,child_ref_id,stock_trans.item_id
                             FROM stock_trans  WHERE stock_trans.is_delete=0 AND stock_trans.trans_type IN("SSI") GROUP BY stock_trans.child_ref_id,stock_trans.item_id) stock_trans']="stock_trans.child_ref_id = prc_bom.prc_id AND prc_bom.item_id = stock_trans.item_id";
        $data['where_in']['prc_master.status'] = [0,1,2];
        $data['where']['prc_type'] = 1;
        $data['group_by'][]='prc_bom.prc_id';
		$data['order_by']['prc_master.prc_date']='DESC';
        // $data['having'][]='pending_qty > 0';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "DATE_FORMAT(prc_master.prc_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "IFNULL(prc_bom.ppc_qty * prc_master.prc_qty,0)";
        $data['searchCol'][] = "stock_trans.issue_qty";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getDieMaterialReqDTRows($data){
        $data['tableName'] = "die_bom";
        $data['select'] = "die_bom.*,die_production.trans_number,item_master.item_name,item_master.item_code,SUM(IFNULL(issue_register.issue_qty,0)) as issue_qty,item_category.category_name,fg.item_name as fg_item_name,fg.item_code as fg_item_code";

        $data['leftJoin']['die_production'] = "die_production.id = die_bom.die_id";
        $data['leftJoin']['item_master'] = "item_master.id = die_bom.item_id";
        $data['leftJoin']['item_master fg'] = 'fg.id = die_production.fg_item_id';
        $data['leftJoin']['item_category'] = 'item_category.id = die_production.item_id';
        $data['leftJoin']['issue_register']="issue_register.prc_id = die_bom.die_id AND issue_register.issue_type = 3 AND die_bom.item_id = issue_register.item_id AND issue_register.is_delete = 0";
        
        $data['having'][] = "(die_bom.bom_qty - issue_qty) > 0";
        $data['group_by'][] = 'die_bom.id';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "die_production.trans_number";
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "CONCAT('[',fg.item_code,'] ',fg.item_name)"; 
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)"; 
		$data['searchCol'][] = "die_bom.bom_qty";
        $data['searchCol'][] = "issue_register.issue_qty";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getManualRejDTRows($data) {
        $data['tableName'] = 'rej_found';
        $data['select'] = "rej_found.*,item_master.item_code,item_master.item_name,location_master.location";
        $data['leftJoin']['item_master'] = "item_master.id = rej_found.item_id";
        $data['leftJoin']['location_master'] = "location_master.id  = rej_found.location_id";
        $data['where']['rej_found.trans_type'] = 1;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(rej_found.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "rej_found.qty";
        $data['searchCol'][] = "location_master.location";
        $data['searchCol'][] = "rej_found.batch_no";
        $data['searchCol'][] = "rej_found.remark";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function saveRejection($data){        
        try {
            $this->db->trans_begin();
       
            foreach ($data['batch_qty'] as $key => $value) {

                if(!empty($value) && $value > 0) {
                    $rejData = [
                        'id' => '',
                        'trans_type' => 1,
                        'trans_date' => date("Y-m-d"),
                        'item_id' => $data['item_id'],
                        'location_id'=> $data['location_id'][$key],
                        'batch_no' => $data['batch_no'][$key],
                        'qty' => $value,
                        'remark' => $data['remark'],
                        'created_by' => $this->loginId,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $result = $this->store('rej_found', $rejData, 'Rejection');

                    $stockMinusQuery = [
                        'id' => '',
                        'trans_type' => 'MRJ',
                        'trans_date' => date("Y-m-d"),
                        'location_id'=> $data['location_id'][$key],
                        'batch_no' => $data['batch_no'][$key],
                        'item_id' => $data['item_id'],
                        'qty' => $value,
                        'p_or_m' => -1,
                        'main_ref_id' => $result['insert_id'],
                        'created_by' => $this->loginId,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $issueTrans = $this->store('stock_trans', $stockMinusQuery);
                }
            }

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material issue Successfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteRejection($data) {
        try {
            $this->db->trans_begin();
			
            $this->trash('stock_trans', ['trans_type'=>'MRJ', 'main_ref_id'=>$data['id']]);

            $result = $this->trash('rej_found', ['id'=>$data['id']], 'Rejection');

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

	public function getSparPartRequestDTRows($data){
        $data['tableName'] = $this->store_request;
        $data['select'] = "store_request.*,item_master.item_name,item_master.item_code,machine_breakdown.trans_number as ticket_number";
        $data['leftJoin']['item_master'] = "item_master.id  = store_request.item_id";
        $data['leftJoin']['machine_breakdown'] = "machine_breakdown.id  = store_request.ref_id";

        $data['where']['store_request.req_type'] = 2;
        $data['where']['(store_request.req_qty - store_request.issue_qty) >'] = 0;
    
        $data['order_by']['store_request.trans_number'] = 'ASC';
    
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "store_request.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(store_request.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "store_request.req_qty";
        $data['searchCol'][] = "store_request.issue_qty";
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
      
		
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function deleteSparPartRequest($data){ 
        try{
            $this->db->trans_begin();
            
            if($data['issue_qty'] > 0){
                return ['status'=>0,'message'=>'You can not delete this Sparpart Request'];
            }
            $result = $this->trash('store_request', ['id'=>$data['id']], 'Sparpart Request');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
}
?> 