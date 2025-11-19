<?php
class SalesOrderModel extends MasterModel{
    private $soMaster = "so_master";
    private $soTrans = "so_trans";
    private $transExpense = "trans_expense";
    private $transDetails = "trans_details";
    private $orderBom = "order_bom";
    private $purchseReq = "purchase_request";
    private $soRevMaster = "so_rev_master";
    private $soRevTrans = "so_rev_trans";

   public function getDTRows($data){
        $data['tableName'] = $this->soTrans;
        $data['select'] = "so_trans.id as trans_child_id,item_master.item_name, item_master.item_name,so_trans.qty,so_trans.dispatch_qty,IF((so_trans.qty - so_trans.dispatch_qty) < 0, 0, (so_trans.qty - so_trans.dispatch_qty)) as pending_qty, IFNULL(so_trans.cod_date,'') as cod_date, (CASE WHEN so_trans.cod_date IS NOT NULL THEN DATEDIFF(so_trans.cod_date, CURDATE()) ELSE 0 END) as due_days, so_master.id,so_master.trans_number, DATE_FORMAT(so_master.trans_date,'%d-%m-%Y') as trans_date,so_master.doc_no, DATE_FORMAT(so_master.doc_date,'%d-%m-%Y') as doc_date,party_master.party_name,so_trans.trans_status,ifnull(st.stock_qty,0) as stock_qty,party_master.sales_executive,so_master.party_id,(CASE WHEN party_master.sales_executive = so_master.party_id THEN 'Client' ELSE 'Office' END) as ordered_by,so_master.is_approve, IFNULL(ecn_master.drw_no,'') as drw_no,employee_master.emp_name as created_name,emp.emp_name as updated_name,so_trans.created_at,so_trans.updated_at,IFNULL((IFNULL(SUM(prc_master.prc_qty),0) * IFNULL(pp.max_output_qty,0)) - (IFNULL(SUM(prc_detail.stored_qty),0) + IFNULL(SUM(prc_detail.rej_qty),0)),0) as wip_qty,so_trans.price,so_master.so_rev_no, 
        
        IFNULL(prcTotals.total_prc_qty,0) AS total_prc_qty,
        IFNULL(pd.total_stored,0) AS total_stored,
        IFNULL(pd.total_rej,0) AS total_rej,
        IFNULL(pp.max_output_qty,0) AS max_output_qty,
        (
            (IFNULL(prcTotals.total_prc_qty,0) * IFNULL(pp.max_output_qty,0))
            - (IFNULL(pd.total_stored,0) + IFNULL(pd.total_rej,0))
        ) AS item_wip_qty"; 

        $data['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id";
        $data['leftJoin']['(SELECT SUM(qty * p_or_m) as stock_qty,item_id FROM stock_trans WHERE is_delete = 0 AND location_id="'.$this->RTD_STORE->id.'" GROUP BY item_id) as st'] = "so_trans.item_id = st.item_id";
        $data['leftJoin']['item_master'] = "item_master.id = so_trans.item_id";
        $data['leftJoin']['ecn_master'] = "ecn_master.item_id = so_trans.item_id AND ecn_master.rev_no = so_trans.rev_no";
		$data['leftJoin']['party_master'] = "party_master.id = so_master.party_id";
        $data['leftJoin']['employee_master'] = "employee_master.id  = so_trans.created_by";
        $data['leftJoin']['employee_master emp'] = "emp.id  = so_trans.updated_by";
		$data['leftJoin']['prc_master'] = "prc_master.so_trans_id = so_trans.id AND prc_master.prc_type = 1 AND prc_master.status = 2";
        $data['leftJoin']['prc_detail'] = "prc_detail.prc_id = prc_master.id";

        $data['leftJoin']['(SELECT item_id, MAX(output_qty) AS max_output_qty FROM product_process WHERE is_delete = 0 GROUP BY item_id) AS pp'] 
        = "pp.item_id = so_trans.item_id";

        $data['leftJoin']['(SELECT item_id, SUM(prc_qty) AS total_prc_qty FROM prc_master WHERE is_delete = 0 AND prc_type = 1 AND status = 2 GROUP BY item_id) AS prcTotals'] = "prcTotals.item_id = so_trans.item_id";

        $data['leftJoin']['(SELECT pm.item_id,SUM(pd.stored_qty) AS total_stored, SUM(pd.rej_qty) AS total_rej FROM prc_detail pd JOIN prc_master pm ON pm.id = pd.prc_id AND pm.is_delete = 0 AND pm.prc_type = 1 AND pm.status = 2 WHERE pd.is_delete = 0 GROUP BY pm.item_id) AS pd'] = "pd.item_id = so_trans.item_id";

        $data['where']['so_trans.entry_type'] = $data['entry_type'];
		
        if(empty($data['status'])):
			$data['status'] = 0;
		endif;
		$data['where']['so_trans.trans_status'] = $data['status'];
		        
        if(!in_array($data['status'],[0,3])):
            $data['where']['so_master.trans_date >='] = $this->startYearDate;
            $data['where']['so_master.trans_date <='] = $this->endYearDate;
        endif;

        $data['order_by']['so_master.trans_date'] = "DESC";
        $data['order_by']['so_master.id'] = "DESC";

        $data['group_by'][] = "so_trans.id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
		$data['searchCol'][] = "so_master.so_rev_no";
        $data['searchCol'][] = "so_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(so_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "so_master.doc_no";
        $data['searchCol'][] = "DATE_FORMAT(so_trans.cod_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "so_trans.price";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "so_trans.qty";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "so_trans.dispatch_qty";
        $data['searchCol'][] = "(so_trans.qty - so_trans.dispatch_qty)";
        $data['searchCol'][] = "DATEDIFF(so_trans.cod_date, CURDATE())";
		$data['searchCol'][] = "CONCAT(employee_master.emp_name,DATE_FORMAT(so_trans.created_at,'%d-%m-%Y %H:%i:%s'))";
		$data['searchCol'][] = "CONCAT(emp.emp_name,DATE_FORMAT(so_trans.updated_at,'%d-%m-%Y %H:%i:%s'))";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            unset($data['tcs_per'],$data['tcs_amount']);
            if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_number'] = "SO. No. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

			if(!empty($data['is_rev'])):
                $this->orderRevision($data['id']);
                $data['so_rev_no'] = $data['so_rev_no'] + 1;
            endif;
			
            if(!empty($data['id'])):
                $dataRow = $this->getSalesOrder(['id'=>$data['id'],'itemList'=>1]);
                foreach($dataRow->itemList as $row):
                    if(!empty($row->ref_id)):
                        $setData = array();
                        $setData['tableName'] = 'sq_trans';
                        $setData['where']['id'] = $row->ref_id;
                        $setData['update']['trans_status'] = 0;
                        $this->setValue($setData);
                    endif;

                    $this->trash($this->soTrans,['id'=>$row->id]);
                endforeach;

                $this->trash($this->transExpense,['trans_main_id'=>$data['id'],'entry_type'=>$this->data['entryData']->id]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->soMaster,'description'=>"SO TERMS"]);
            endif;
            
            $itemData = $data['itemData'];

            $transExp = getExpArrayMap(((!empty($data['expenseData']))?$data['expenseData']:array()));
			$expAmount = $transExp['exp_amount'];
            $termsData = (!empty($data['conditions']))?$data['conditions']:array();

            unset($transExp['exp_amount'],$data['itemData'],$data['expenseData'],$data['conditions'],$data['is_rev']);		

            $result = $this->store($this->soMaster,$data,'Sales Order');

            $expenseData = array();
            if($expAmount <> 0):				
				$expenseData = $transExp;
                $expenseData['id'] = "";
				$expenseData['trans_main_id'] = $result['id'];
				$expenseData['entry_type'] = $data['entry_type'];
                $this->store($this->transExpense,$expenseData);
			endif;

            if(!empty($termsData)):
                $termsData = [
                    'id' =>"",
                    'table_name' => $this->soMaster,
                    'description' => "SO TERMS",
                    'main_ref_id' => $result['id'],
                    't_col_1' => $termsData
                ];
                $this->store($this->transDetails,$termsData);
            endif;

            $i=1;
            foreach($itemData as $row):
                $row['entry_type'] = $data['entry_type'];
                $row['trans_main_id'] = $result['id'];
                $row['cod_date'] = (!empty($row['cod_date']))?$row['cod_date']:NULL;
                $row['is_delete'] = 0;
                $this->store($this->soTrans,$row);

                if(!empty($row['ref_id'])):
                    $setData = array();
                    $setData['tableName'] = 'sq_trans';
                    $setData['where']['id'] = $row['ref_id'];
                    $setData['update']['trans_status'] = "1";
                    $this->setValue($setData);
                endif;
            endforeach;
            
			if(!empty($data['ref_id'])):
                $refIds = explode(",",$data['ref_id']);
                foreach($refIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = 'sq_master';
                    $setData['where']['id'] = $main_id;
                    $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM so_trans WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
                    $this->setValue($setData);
                endforeach;
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
        $queryData['tableName'] = $this->soMaster;
        $queryData['where']['trans_number'] = $data['trans_number'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }
		
    public function getSalesOrder($data){
        $queryData = array();
        $queryData['tableName'] = $this->soMaster;
        $queryData['select'] = "so_master.*,trans_details.t_col_1 as contact_person,trans_details.t_col_2 as contact_no,trans_details.t_col_3 as ship_address,,trans_details.t_col_4 as ship_pincode,employee_master.emp_name as created_name,party_master.party_name,approve.emp_name as approve_by";

        $queryData['leftJoin']['trans_details'] = "so_master.id = trans_details.main_ref_id AND trans_details.description = 'SO MASTER DETAILS' AND trans_details.table_name = '".$this->soMaster."'";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = so_master.created_by";
		$queryData['leftJoin']['employee_master approve'] = "approve.id = so_master.is_approve";
        $queryData['leftJoin']['party_master'] = "party_master.id = so_master.party_id";

        $queryData['where']['so_master.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
			$is_internal = (!empty($data['is_internal']) ? $data['is_internal'] : 0);
		
			$result->itemList = $this->getSalesOrderItems(['trans_main_id'=>$data['id'],'is_internal'=>$is_internal]);
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
        $queryData['where']['table_name'] = $this->soMaster;
        $queryData['where']['description'] = "SO TERMS";
        $result->termsConditions = $this->row($queryData);

        return $result;
    }

	public function getSalesOrderItems($data=[]){
        $queryData = array();
        $queryData['tableName'] = $this->soTrans;
        $queryData['select'] = "so_trans.*,tmref.trans_number as ref_number,item_master.item_name,item_master.wt_pcs,item_master.hsn_code,item_master.uom,item_master.item_code,item_master.cut_weight,material_master.material_grade,ecn_master.rev_no,ecn_master.cust_rev_no,ecn_master.drw_no,item_master.mfg_type";
		$queryData['leftJoin']['sq_trans as tcref'] = "tcref.id = so_trans.ref_id";
        $queryData['leftJoin']['sq_master as tmref'] = "tcref.trans_main_id = tmref.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = so_trans.item_id";
        $queryData['leftJoin']['item_kit'] = "item_master.id = item_kit.ref_item_id";
        $queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
		$queryData['leftJoin']['(SELECT drw_no,cust_rev_date,cust_rev_no,rev_no,rev_date,item_id FROM ecn_master WHERE is_delete=0 AND status=2 GROUP BY item_id ORDER BY ecn_date DESC) as ecn_master'] = "ecn_master.item_id = so_trans.item_id";

		if(!empty($data['is_internal']) && $data['is_internal'] == 1){
			$queryData['select'] .= ', itemKit.qty as cut_wt';
			$queryData['leftJoin']['(SELECT item_kit.item_id, item_kit.qty FROM item_kit WHERE item_kit.is_delete = 0 group by item_kit.item_id) as itemKit'] = "itemKit.item_id = so_trans.item_id";
		}

        if (!empty($data['trans_main_id'])) { $queryData['where']['so_trans.trans_main_id'] = $data['trans_main_id']; }

        if (!empty($data['id'])) { $queryData['where']['so_trans.id'] = $data['id']; }

		$queryData['group_by'][] = 'so_trans.id';
		$queryData['order_by']['so_trans.id'] = 'ASC';
		
        if (!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }
	
    public function delete($id){
        try{
            $this->db->trans_begin();

            $postData["table_name"] = $this->soMaster;
            $postData['where'] = [['column_name'=>'from_entry_type','column_value'=>$this->data['entryData']->id]];
            $postData['find'] = [['column_name'=>'ref_id','column_value'=>$id]];
            $checkRef = $this->checkEntryReference($postData);
            if($checkRef['status'] == 0):
                $this->db->trans_rollback();
                return $checkRef;
            endif;

            $dataRow = $this->getSalesOrder(['id'=>$id,'itemList'=>1]);
            foreach($dataRow->itemList as $row):
				if(!empty($row->ref_id)):
                    $setData = array();
                    $setData['tableName'] = 'sq_trans';
                    $setData['where']['id'] = $row->ref_id;
                    $setData['update']['trans_status'] = 0;
                    $this->setValue($setData);
                endif;
				
				$this->trash($this->soRevTrans, ['ref_id'=>$row->id]);
                $this->trash($this->soTrans,['id'=>$row->id]);
            endforeach;

			if(!empty($dataRow->ref_id)):
                $oldRefIds = explode(",",$dataRow->ref_id);
                foreach($oldRefIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = 'sq_master';
                    $setData['where']['id'] = $main_id;
                    $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM so_trans WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
                    $this->setValue($setData);
                endforeach;
            endif;

            $this->trash($this->transExpense,['trans_main_id'=>$id,'entry_type'=>$this->data['entryData']->id]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->soMaster,'description'=>"SO TERMS"]);
			$this->trash($this->soRevMaster,['ref_id'=>$id]);
			
            $result = $this->trash($this->soMaster,['id'=>$id],'Sales Order');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

	/* UPDATED BY : AVT DATE:13-12-2024 */
    public function getPendingOrderItems($data=array()){
        $queryData = array();
        $queryData['tableName'] = $this->soTrans;
        $queryData['select'] = "so_trans.*,(so_trans.qty - so_trans.dispatch_qty) as pending_qty,so_master.party_id,so_master.entry_type as main_entry_type,so_master.trans_number,so_master.trans_date,so_master.doc_no,item_master.item_name,item_master.hsn_code,item_master.uom,item_master.is_packing,item_master.item_type,item_master.category_id,party_master.party_name,party_master.party_code,IFNULL(dp.plan_qty,0.00) as plan_qty";
        $queryData['leftJoin']['so_master'] = "so_trans.trans_main_id = so_master.id";
        $queryData['leftJoin']['party_master'] = "so_master.party_id = party_master.id";
        $queryData['leftJoin']['item_master'] = "so_trans.item_id = item_master.id";
        $queryData['leftJoin']['(SELECT SUM(qty) AS plan_qty,so_trans_id FROM dispatch_plan WHERE `is_delete` = 0 GROUP BY so_trans_id) AS dp'] = "dp.so_trans_id = so_trans.id";
		
		if(!empty($data['prc_type'])){
			$queryData['select'] .= ",IFNULL(prcMaster.prc_qty,0) as prc_qty";
			$queryData['leftJoin']['(SELECT SUM(prc_qty) as prc_qty,so_trans_id,prc_type FROM prc_master WHERE is_delete = 0 AND prc_type = '.$data['prc_type'].' GROUP BY so_trans_id) as prcMaster'] = "prcMaster.so_trans_id = so_trans.id";
		}

        if(!empty($data['group_by'])){
            $queryData['select'] .= ",SUM(so_trans.qty) as order_qty,SUM(so_trans.dispatch_qty) as total_dispatch, SUM(so_trans.qty - so_trans.dispatch_qty) as pending_dispatch";
			
			if($data['group_by'] == 'party_id'){ $queryData['customWhere'][] = '(so_trans.qty - so_trans.dispatch_qty) > 0'; }
            $queryData['group_by'][] = $data['group_by'];
        }

        if(!empty($this->data['entryData']->id)){
			$queryData['where']['so_trans.entry_type'] = $this->data['entryData']->id;
		}
		
		if(!empty($data['party_id'])){
			$queryData['where']['so_master.party_id'] = $data['party_id'];
		}
		
        if(!empty($data['so_id'])){
			$queryData['where']['so_trans.trans_main_id'] = $data['so_id'];
		}
        
		if(!empty($data['is_approve'])){
			$queryData['where']['so_master.is_approve > '] = 0; 
		}
		
        if(isset($data['trans_status'])){
			$queryData['where']['so_trans.trans_status'] = 3; 
		}
		
        if(isset($data['is_packing'])){
			$queryData['where']['item_master.is_packing'] = $data['is_packing'];
		}

        if(!empty($data['completed_order'])){
            $queryData['where']['(so_trans.qty - so_trans.dispatch_qty) <='] = 0;
        }elseif(!empty($data['mrp_report'])){
            $queryData['having'][] = '(SUM(so_trans.qty) - SUM(so_trans.dispatch_qty)) > 0';
        }elseif(!empty($data['customHaving'])){
            $queryData['having'][] = $data['customHaving'];
        }elseif(!empty($data['group_by'])){
			$queryData['having'][] = '(SUM(so_trans.qty) - SUM(so_trans.dispatch_qty)) > 0';
		}else{
            $queryData['having'][] = '(so_trans.qty - so_trans.dispatch_qty) > 0';
        }

        $queryData['where']['so_master.is_delete'] = 0;

        return $this->rows($queryData);
    }
	
    /* Party Order Start */
    public function getPartyOrderDTRows($data){
        $data['tableName'] = $this->soTrans;
        $data['select'] = "so_trans.id as trans_child_id,item_master.item_name,so_trans.qty,so_trans.dispatch_qty,(so_trans.qty - so_trans.dispatch_qty) as pending_qty,so_master.id,so_master.trans_number,DATE_FORMAT(so_master.trans_date,'%d-%m-%Y') as trans_date,party_master.party_name,so_trans.trans_status,so_trans.brand_name,party_master.sales_executive,so_master.party_id,if(so_master.is_approve > 0,'Accepted','Pending') as order_status";

        $data['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id";
        $data['leftJoin']['party_master'] = "party_master.id = so_master.party_id";

        $data['where']['so_trans.entry_type'] = $data['entry_type'];
        $data['where']['so_trans.created_by'] = $this->loginId;
        $data['customWhere'][] = "so_master.party_id = party_master.sales_executive";

        if($data['status'] == 0):
            $data['where']['so_trans.trans_status'] = 0;
            $data['where']['so_master.trans_date <='] = $this->endYearDate;
        elseif($data['status'] == 1):
            $data['where']['so_trans.trans_status'] = 1;
            $data['where']['so_master.trans_date >='] = $this->startYearDate;
            $data['where']['so_master.trans_date <='] = $this->endYearDate;
        endif;

        $data['order_by']['so_master.trans_date'] = "DESC";
        $data['order_by']['so_master.id'] = "DESC";

        $data['group_by'][] = "so_trans.id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "if(so_master.is_approve > 0,'Accepted','Pending')";
        $data['searchCol'][] = "so_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(so_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "so_trans.brand_name";
        $data['searchCol'][] = "so_trans.qty";
        $data['searchCol'][] = "so_trans.dispatch_qty";
        $data['searchCol'][] = "(so_trans.qty - so_trans.dispatch_qty)";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }
    /* Party Order End */	
	
	public function changeOrderStatus($postData){ 
        try{
            $this->db->trans_begin();

            $result = $this->store($this->soTrans,$postData,'Sales Order');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

	//App by Order
    public function getSalesOrderByApp($data){
        $queryData = array();
        $queryData['tableName'] = $this->soMaster;
        $queryData['select'] = "so_master.*,party_master.party_name,so_trans.qty,item_master.item_name";
        $queryData['leftJoin']['party_master'] = "party_master.id = so_master.party_id";
        $queryData['leftJoin']['so_trans'] = "so_trans.trans_main_id = so_master.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = so_trans.item_id";
        $queryData['where']['so_master.entry_type'] = $this->data['entryData']->id;
        $queryData['where']['so_master.is_approve'] = 0;
        $queryData['where']['so_trans.trans_status'] = 0;
        $queryData['group_by'][] = "so_master.id";
        $queryData['order_by']['so_master.id'] = "DESC";
        $result = $this->rows($queryData);
        return $result;
    }

	public function approveSalesOrder($data) {
        try{
            $this->db->trans_begin();

            $date = ($data['is_approve'] == 1) ? date('Y-m-d') : NULL;
            $isApprove = ($data['is_approve'] == 1) ? $this->loginId : 0;
            $status = (!empty($data['trans_status']) && $data['trans_status'] == 3) ? 0 : 3;
            
            $this->edit($this->soMaster, ['id' => $data['id']], ['is_approve' => $isApprove, 'approve_date'=>$date, 'trans_status' => $status]);
            
            $this->edit($this->soTrans, ['trans_main_id' => $data['id']], ['trans_status' => $status]);
            
            $result = ['status' => 1, 'message' => 'Sales Order ' . $data['msg'] . ' successfully.'];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	    
    }

    public function getOrderByRefid($transIds){
        $data['tableName'] = $this->soMaster;        
        $data['select'] = "so_master.id,so_master.doc_no,so_trans.trans_main_id";
        $data['leftJoin']['so_trans'] = "so_master.id = so_trans.trans_main_id";
        $data['where_in']['so_trans.trans_main_id'] = $transIds;
        $data['group_by'][] = 'so_trans.trans_main_id';
        return $this->rows($data);
    }

    public function getOrderItems($transIds){
        $data['tableName'] = $this->soTrans;  
        $data['select'] = 'so_trans.*,item_master.item_name,item_master.hsn_code,unit_master.unit_name';
        $data['leftJoin']['item_master'] = "item_master.id = so_trans.item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['where_in']['so_trans.id'] = $transIds;
        return $this->rows($data);
    }

	 /* Order Revision */
    public function orderRevision($id){
        try{
            $this->db->trans_begin();

            $soData = $this->getSalesOrder(['id'=>$id,'itemList'=>1]);

            $itemData = $soData->itemList;
            
            $termsData = '';
            if(!empty($soData->termsConditions)):
                $termsData = $soData->termsConditions;
            endif;

            $transExp = (!empty($soData->expenseData))?$soData->expenseData:array();

            unset($soData->itemList,$soData->termsConditions,$soData->expenseData,$soData->created_name,$soData->ref_number,$soData->contact_person,$soData->contact_no,$soData->ship_address,$soData->ship_pincode,$soData->party_name,$soData->approve_by);

            $soData = (array) $soData;
            $soData["ref_id"] = $soData["id"];
            $soData["id"] = "";
            $soData["from_entry_type"] = $soData['entry_type'];
            $soData["entry_type"] = "";
            
            $result = $this->store($this->soRevMaster, $soData, 'Sales Order');

            $expenseData = array();
            if(!empty($transExp)):
				$expenseData = (array) $transExp;
                $expenseData['id'] = "";
				$expenseData['trans_main_id'] = $result['id'];
				$expenseData['entry_type'] = $soData['entry_type'];
                $this->store($this->transExpense,$expenseData);
			endif;

            if(!empty($termsData)):
                $termsData = [
                    'id' =>"",
                    'table_name' => $this->soRevMaster,
                    'description' => "SO TERMS",
                    'main_ref_id' => $result['id'],
                    't_col_1' => $termsData->condition
                ];
                $this->store($this->transDetails,$termsData);
            endif;                     

            $i=1;
            foreach($itemData as $row):
                $row = (array) $row;
                $row['from_entry_type'] = $row['entry_type'];
                $row['entry_type'] = "";
                $row['ref_id'] = $row['id'];
                $row['id'] = "";
                $row['trans_main_id'] = $result['id'];
                $row['is_delete'] = 0;	

                unset($row['ref_number'],$row['item_name'],$row['wt_pcs'],$row['hsn_code'],$row['uom'],$row['item_code'],$row['cut_weight'],$row['material_grade'],$row['cust_rev_no'],$row['drw_no'],$row['mfg_type']);

                $this->store($this->soRevTrans, $row);
            endforeach;            

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getOrderRevisionList($data){
        $queryData = array();
        $queryData['tableName'] = $this->soRevMaster;
        $queryData['select'] = "id,trans_number,so_rev_no,doc_date";
        $queryData['where']['so_rev_master.ref_id'] = $data['id'];
        $queryData['order_by']['so_rev_master.so_rev_no'] = "DESC";
        $result = $this->rows($queryData);
        return $result;
    }

    public function getRevSalesOrder($data){
        $queryData = array();
        $queryData['tableName'] = $this->soRevMaster;
        $queryData['select'] = "so_rev_master.*,trans_details.t_col_1 as contact_person,trans_details.t_col_2 as contact_no,trans_details.t_col_3 as ship_address,,trans_details.t_col_4 as ship_pincode,employee_master.emp_name as created_name,party_master.party_name,approve.emp_name as approve_by";

        $queryData['leftJoin']['trans_details'] = "so_rev_master.id = trans_details.main_ref_id AND trans_details.description = 'SO MASTER DETAILS' AND trans_details.table_name = '".$this->soRevMaster."'";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = so_rev_master.created_by";
		$queryData['leftJoin']['employee_master approve'] = "approve.id = so_rev_master.is_approve";
        $queryData['leftJoin']['party_master'] = "party_master.id = so_rev_master.party_id";

        $queryData['where']['so_rev_master.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
			$result->itemList = $this->getRevSalesOrderItems(['trans_main_id'=>$data['id']]);
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
        $queryData['where']['table_name'] = $this->soRevMaster;
        $queryData['where']['description'] = "SO TERMS";
        $result->termsConditions = $this->row($queryData);

        return $result;
    }

	public function getRevSalesOrderItems($data=[]){
        $queryData = array();
        $queryData['tableName'] = $this->soRevTrans;
        $queryData['select'] = "so_rev_trans.*,tmref.trans_number as ref_number,item_master.item_name,item_master.wt_pcs,item_master.hsn_code,item_master.uom,item_master.item_code,item_master.cut_weight,material_master.material_grade,ecn_master.rev_no,ecn_master.cust_rev_no,ecn_master.drw_no,item_master.mfg_type";
		$queryData['leftJoin']['sq_trans as tcref'] = "tcref.id = so_rev_trans.ref_id";
        $queryData['leftJoin']['sq_master as tmref'] = "tcref.trans_main_id = tmref.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = so_rev_trans.item_id";
        $queryData['leftJoin']['item_kit'] = "item_master.id = item_kit.ref_item_id";
        $queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
		$queryData['leftJoin']['(SELECT drw_no,cust_rev_date,cust_rev_no,rev_no,rev_date,item_id FROM ecn_master WHERE is_delete=0 AND status=2 GROUP BY item_id ORDER BY ecn_date DESC) as ecn_master'] = "ecn_master.item_id = so_rev_trans.item_id";

        if (!empty($data['trans_main_id'])) { $queryData['where']['so_rev_trans.trans_main_id'] = $data['trans_main_id']; }

        if (!empty($data['id'])) { $queryData['where']['so_rev_trans.id'] = $data['id']; }

		$queryData['group_by'][] = 'so_rev_trans.id';
		
        if (!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }
}
?>