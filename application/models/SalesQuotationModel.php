<?php
class SalesQuotationModel extends MasterModel{
    private $sqMaster = "sq_master";
    private $sqTrans = "sq_trans";
    private $transExpense = "trans_expense";
    private $transDetails = "trans_details";

	public function getDTRows($data){
        $data['tableName'] = $this->sqTrans;
        $data['select'] = "sq_trans.id,item_master.item_name,sq_trans.qty,sq_trans.price,sq_master.id as trans_main_id,sq_master.trans_number,DATE_FORMAT(sq_master.trans_date,'%d-%m-%Y') as trans_date,sq_master.party_id,party_master.sales_executive,party_master.party_name,sq_master.sales_type,sq_trans.trans_status,sq_trans.is_approve,employee_master.emp_name as approve_by_name,sq_trans.approve_date,sq_master.quote_rev_no,sq_trans.cod_date,sq_master.is_pinv,party_master.party_type,sq_master.internal_approve";

        $data['leftJoin']['sq_master'] = "sq_master.id = sq_trans.trans_main_id";
        $data['leftJoin']['item_master'] = "item_master.id = sq_trans.item_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = sq_trans.is_approve";
        $data['leftJoin']['party_master'] = "party_master.id = sq_master.party_id";

        $data['where']['sq_trans.entry_type'] = $data['entry_type'];
		
        if($data['status'] == 0):
            $data['where']['sq_trans.trans_status'] = 0;
            $data['where']['sq_master.internal_approve'] = 0;
            $data['where']['sq_master.trans_date <='] = $this->endYearDate;
        elseif($data['status'] == 1):
            $data['where']['sq_trans.trans_status'] = 1;
            $data['where']['sq_master.trans_date >='] = $this->startYearDate;
            $data['where']['sq_master.trans_date <='] = $this->endYearDate;
		elseif($data['status'] == 2):
            $data['where']['sq_trans.trans_status'] = 2;
		elseif($data['status'] == 3):
            $data['where']['sq_trans.trans_status'] = 0;
            $data['where']['sq_master.internal_approve !='] = 0;
		endif;

        $data['order_by']['sq_master.trans_date'] = "DESC";
        $data['order_by']['sq_master.id'] = "DESC";

        //$data['group_by'][] = "sq_trans.trans_main_id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "sq_master.quote_rev_no";
        $data['searchCol'][] = "sq_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(sq_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "sq_trans.qty";
        $data['searchCol'][] = "sq_trans.price";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "DATE_FORMAT(sq_master.approve_date,'%d-%m-%Y')";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }
	
    public function save($data){
        try{
            $this->db->trans_begin();

            if(!empty($data['is_rev'])):
                $this->quotationRevision($data['id']);
                $data['quote_rev_no'] = $data['quote_rev_no'] + 1;
            endif;

            if(!empty($data['id'])):
                $dataRow = $this->getSalesQuotation(['id'=>$data['id'],'itemList'=>1]);
                foreach($dataRow->itemList as $row):
                    if(!empty($row->ref_id)):
                        $setData = array();
                        $setData['tableName'] = $this->sqTrans;
                        $setData['where']['id'] = $row->ref_id;
                        $setData['update']['trans_status'] = 0;
                        $this->setValue($setData);
                    endif;

                    $this->trash($this->sqTrans,['id'=>$row->id]);
                endforeach;

                $this->trash($this->transExpense,['trans_main_id'=>$data['id'],'entry_type'=>$this->data['entryData']->id]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->sqMaster,'description'=>"SQ TERMS"]);
            endif;
            
            $itemData = $data['itemData'];

            $transExp = getExpArrayMap(((!empty($data['expenseData']))?$data['expenseData']:array()));
			$expAmount = $transExp['exp_amount'];
            $termsData = (!empty($data['conditions']))?$data['conditions']:array();

            unset($transExp['exp_amount'],$data['itemData'],$data['expenseData'],$data['conditions'],$data['is_rev']);		

            $result = $this->store($this->sqMaster,$data,'Sales Quotation');

            $expenseData = array();
            if($expAmount <> 0):				
				$expenseData = $transExp;
                $expenseData['id'] = "";
				$expenseData['trans_main_id'] = $result['id'];
				$expenseData['entry_type'] = $data['entry_type'];
                $this->store($this->transExpense,$expenseData);
			endif;

            //Terms & Conditions
            if(!empty($termsData)):
                $termsData = [
                    'id' =>"",
                    'table_name' => $this->sqMaster,
                    'description' => "SQ TERMS",
                    'main_ref_id' => $result['id'],
                    't_col_1' => $termsData
                ];
                $this->store($this->transDetails,$termsData);
            endif;

            $i=1;
            foreach($itemData as $row):
                $row['entry_type'] = $data['entry_type'];
                $row['trans_main_id'] = $result['id'];
                $row['is_delete'] = 0;
                $this->store($this->sqTrans,$row);

                if(!empty($row['ref_id'])):
                    $setData = array();
                    $setData['tableName'] = 'se_trans';
                    $setData['where']['id'] = $row['ref_id'];
                    $setData['update']['trans_status'] = "1";
                    $this->setValue($setData);
                endif;
            endforeach;
            
            if(!empty($data['ref_id'])):
                $refIds = explode(",",$data['ref_id']);
                foreach($refIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = 'se_master';
                    $setData['where']['id'] = $main_id;
                    $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM sq_trans WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
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

	/* UPDATED BY : AVT DATE:18-12-2024 */
    public function quotationRevision($id){
        try{
            $this->db->trans_begin();

            $quotationData = $this->getSalesQuotation(['id'=>$id,'itemList'=>1]);

            $itemData = $quotationData->itemList;

            $termsData = '';
            if(!empty($quotationData->termsConditions)):
                $termsData = $quotationData->termsConditions;
            endif;

            $transExp = (!empty($quotationData->expenseData))?$quotationData->expenseData:array();

            unset($quotationData->itemList,$quotationData->termsConditions,$quotationData->expenseData,$quotationData->created_name,$quotationData->internal_aprv_name,$quotationData->ref_number);

            $quotationData = (array) $quotationData;
            $quotationData["ref_id"] = $quotationData["id"];
            $quotationData["id"] = "";
            $quotationData["from_entry_type"] = $quotationData['entry_type'];
            $quotationData["entry_type"] = "";
            
            $result = $this->store($this->sqMaster,$quotationData,'Sales Quotation');

            $expenseData = array();
            if(!empty($transExp)):
				$expenseData = (array) $transExp;
                $expenseData['id'] = "";
				$expenseData['trans_main_id'] = $result['id'];
				$expenseData['entry_type'] = $quotationData['entry_type'];
                $this->store($this->transExpense,$expenseData);
			endif;

            //Terms & Conditions
            if(!empty($termsData)):
                $termsData = [
                    'id' =>"",
                    'table_name' => $this->sqMaster,
                    'description' => "SQ TERMS",
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

                unset($row['item_name'],$row['unit_name'],$row['hsn_code'],$row['item_code'],$row['uom'],$row['material_grade']);
                $this->store($this->sqTrans,$row);
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

    public function checkDuplicate($data){
        $queryData['tableName'] = $this->sqMaster;
        $queryData['where']['trans_number'] = $data['trans_number'];
        $queryData['where']['entry_type'] = $data['entry_type'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getQuotationRevisionList($data){
        $queryData = array();
        $queryData['tableName'] = $this->sqMaster;
        $queryData['select'] = "id,trans_number,quote_rev_no,doc_date";
        $queryData['where']['sq_master.trans_number'] = $data['trans_number'];
        $queryData['order_by']['sq_master.quote_rev_no'] = "DESC";
        $result = $this->rows($queryData);
        return $result;
    }

	/* UPDATED BY : AVT DATE:13-12-2024 */
    public function getSalesQuotation($data){
        $queryData = array();
        $queryData['tableName'] = $this->sqMaster;
		$queryData['select'] = "sq_master.*,employee_master.emp_name as created_name,party_master.party_name,se_master.trans_number as ref_number,se_master.trans_date as ref_date,ia.emp_name as internal_aprv_name";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = sq_master.created_by";
		$queryData['leftJoin']['employee_master ia'] = "ia.id = sq_master.internal_approve";
        $queryData['leftJoin']['party_master'] = "party_master.id = sq_master.party_id";
        $queryData['leftJoin']['se_master'] = "se_master.id = sq_master.ref_id";
        $queryData['where']['sq_master.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
            $result->itemList = $this->getSalesQuotationItems($data);
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
        $queryData['where']['table_name'] = $this->sqMaster;
        $queryData['where']['description'] = "SQ TERMS";
        $result->termsConditions = $this->row($queryData);

        return $result;
    }

    public function getSalesQuotationItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->sqTrans;
        $queryData['select'] = "sq_trans.*,item_master.item_name,item_master.item_code,item_master.uom,item_master.hsn_code,material_master.material_grade";
        $queryData['leftJoin']['item_master'] = "item_master.id = sq_trans.item_id";
        $queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
        $queryData['leftJoin']['ecn_master'] = "ecn_master.rev_no = sq_trans.rev_no AND ecn_master.item_id = sq_trans.item_id";
        $queryData['where']['sq_trans.trans_main_id'] = $data['id'];
		if(!empty($data['is_approve'])){
            $queryData['where']['sq_trans.is_approve'] = 0;
            $queryData['where']['sq_trans.trans_status'] = 0;
        }
        $result = $this->rows($queryData);
        return $result;
    }

    public function getSalesQuotationItem($data){
        $queryData = array();
        $queryData['tableName'] = $this->sqTrans;
        $queryData['select'] = "sq_trans.*,sq_master.party_id";
        $queryData['leftJoin']['sq_master'] = "sq_trans.trans_main_id = sq_master.id";
        $queryData['where']['sq_trans.id'] = $data['id'];
        $result = $this->row($queryData);
        return $result;
    }    

    public function delete($id){
        try{
            $this->db->trans_begin();

            $dataRow = $this->getSalesQuotation(['id'=>$id,'itemList'=>1]);
            foreach($dataRow->itemList as $row):
                if(!empty($row->ref_id)):
                    $setData = array();
                    $setData['tableName'] = 'se_trans';
                    $setData['where']['id'] = $row->ref_id;
                    $setData['update']['trans_status'] = 0;
                    $this->setValue($setData);
                endif;

                $this->trash($this->sqTrans,['id'=>$row->id]);
            endforeach;

            if(!empty($dataRow->ref_id)):
                $oldRefIds = explode(",",$dataRow->ref_id);
                foreach($oldRefIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = 'se_master';
                    $setData['where']['id'] = $main_id;
                    $setData['update']['trans_status'] = "(SELECT IF( COUNT(id) = SUM(IF(trans_status <> 0, 1, 0)) ,1 , 0 ) as trans_status FROM sq_trans WHERE trans_main_id = ".$main_id." AND is_delete = 0)";
                    $this->setValue($setData);
                endforeach;
            endif;

            $this->trash($this->transExpense,['trans_main_id'=>$id,'entry_type'=>$this->data['entryData']->id]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->sqMaster,'description'=>"SQ TERMS"]);
            $result = $this->trash($this->sqMaster,['id'=>$id],'Sales Quotation');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getPendingQuotationItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->sqTrans;
        $queryData['select'] = "sq_trans.*,sq_master.entry_type as main_entry_type,sq_master.trans_number,sq_master.trans_date,sq_master.doc_no,item_master.item_name";
        $queryData['leftJoin']['sq_master'] = "sq_trans.trans_main_id = sq_master.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = sq_trans.item_id";
        $queryData['where']['sq_master.party_id'] = $data['party_id'];
        $queryData['where']['sq_trans.is_approve >'] = 0;
        $queryData['where']['sq_trans.trans_status'] = 0;
        return $this->rows($queryData);
    }

	public function saveConfirmQuotation($data){
		try{
            $this->db->trans_begin();

            if(!empty($data['trans_status'])){
				$this->edit($this->sqTrans, ['id'=> $data['id']], ['trans_status'=>0,'is_approve'=>0,'approve_date'=>NULL]);
            }else{
                foreach($data['trans_id'] as $key=>$value):
                        $trans_status =  ($data['is_approve'][$key] == 1) ? 0 : 2;
                    
                        $transData = [
                            'id' =>  $value,
                            'item_id' =>  $data['item_id'][$key],
                            'approve_date' => $data['approve_date'],
                            'is_approve' => $this->loginId,
                            'trans_status' => $trans_status
                        ];
                       
                        $this->store($this->sqTrans,$transData);
    
                        $this->edit($this->sqMaster, ['id'=>$data['id']], ['is_approve' => $this->loginId, 'approve_date'=> $data['approve_date']]);
						
						if(empty($trans_status)){
							$this->edit('item_master', ['id'=>$data['item_id'][$key],'is_active'=>2], ['is_active'=>1]);
						
							$this->edit('party_master', ['id'=>$data['party_id'],'party_type'=>2], ['party_type'=>1]);
						}
                endforeach;
            }
			$result = ['status' => 1, 'message' => 'Sales Quotation Approve successfully.'];
			
			if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}

	public function approveQuotation($data) {
        try{
            $this->db->trans_begin();

            $isApprove = ($data['internal_approve'] == 1) ? $this->loginId : 0;

            $this->store($this->sqMaster, ['id'=> $data['id'], 'internal_approve' => $isApprove]);
            $result = ['status' => 1, 'message' => 'Sales Quotation '.$data['msg'].' Successfully.'];

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