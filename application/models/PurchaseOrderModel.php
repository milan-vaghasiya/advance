<?php
class PurchaseOrderModel extends MasterModel{
    private $po_master = "po_master";
    private $po_trans = "po_trans";
    private $transExpense = "trans_expense";
    private $transDetails = "trans_details";
    private $purchase_indent = "purchase_indent";

    public function getDTRows($data){
        $data['tableName'] = $this->po_trans;
        $data['select'] = "po_trans.id as po_trans_id,po_trans.po_id,po_trans.qty,po_trans.item_remark,po_trans.trans_status,po_trans.mill_name,po_trans.delivery_date,po_master.id,po_master.trans_number,DATE_FORMAT(po_master.trans_date,'%d-%m-%Y') as trans_date,party_master.party_name, item_master.item_name, po_master.is_approve, po_trans.dispatch_qty, material_master.material_grade, (CASE WHEN po_trans.from_entry_type = 160 THEN purchase_enquiry.trans_number ELSE purchase_indent.trans_number END) as enq_number,item_master.item_code,po_trans.fg_item_id,fgItem.item_code as fg_item_code,fgItem.item_name as fg_item_name, (po_trans.qty - IFNULL(po_trans.dispatch_qty,0)) as pending_qty,employee_master.emp_name as created_name,emp.emp_name as updated_name,po_trans.created_at,po_trans.updated_at,po_trans.price";

        $data['leftJoin']['po_master'] = "po_master.id = po_trans.po_id";
        $data['leftJoin']['item_master'] = "item_master.id = po_trans.item_id";
        $data['leftJoin']['party_master'] = "party_master.id = po_master.party_id";
        $data['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
        $data['leftJoin']['item_master fgItem'] = "fgItem.id = po_trans.fg_item_id";
        $data['leftJoin']['purchase_enquiry'] = "purchase_enquiry.id = po_trans.ref_id"; 
        $data['leftJoin']['purchase_indent'] = "purchase_indent.id = po_trans.req_id";
        $data['leftJoin']['employee_master'] = "employee_master.id  = po_trans.created_by";
        $data['leftJoin']['employee_master emp'] = "emp.id  = po_trans.updated_by";

        $data['where']['po_trans.entry_type'] = $data['entry_type'];
        if(empty($data['status'])){ $data['status'] = 0; }
		$data['where']['po_trans.trans_status'] = $data['status'];
		
		/* if(empty($data['status'])):
			$data['having'][] = "pending_qty > 0";
			$data['where']['po_master.trans_status != '] = 2;
		elseif($data['status'] == 1):
			$data['having'][] = "pending_qty <= 0";
			$data['where']['po_master.trans_status'] = $data['status'];
		else:
			$data['where']['po_master.trans_status'] = $data['status'];
		endif; */
		
        if(!in_array($data['status'],[0,3])):
			$data['where']['po_master.trans_date >='] = $this->startYearDate;
			$data['where']['po_master.trans_date <='] = $this->endYearDate;
		endif;
		
        $data['order_by']['po_master.trans_date'] = "DESC";
        $data['order_by']['po_master.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "po_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(po_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
		$data['searchCol'][] = "po_trans.mill_name";
        //$data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name,' ',material_master.material_grade)";
		$data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "CONCAT('[',fgItem.item_code,'] ',fgItem.item_name)";
        $data['searchCol'][] = "DATE_FORMAT(po_trans.delivery_date,'%d-%m-%Y')";
		$data['searchCol'][] = "po_trans.price";
		$data['searchCol'][] = "po_trans.qty";
		$data['searchCol'][] = "po_trans.dispatch_qty";
		$data['searchCol'][] = "";
        $data['searchCol'][] = "IF(po_trans.from_entry_type = 160, purchase_enquiry.trans_number, purchase_indent.trans_number)";
        $data['searchCol'][] = "po_trans.item_remark";
		$data['searchCol'][] = "CONCAT(employee_master.emp_name,DATE_FORMAT(po_trans.created_at,'%d-%m-%Y %H:%i:%s'))";
		$data['searchCol'][] = "CONCAT(emp.emp_name,DATE_FORMAT(po_trans.updated_at,'%d-%m-%Y %H:%i:%s'))";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_number'] = "PO. No. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if(!empty($data['id'])):
                $itemList = $this->getPurchaseOrderItems(['id'=>$data['id']]);
                foreach($itemList as $row):
                    $this->trash($this->po_trans,['id'=>$row->id]);
					
					if(!empty($row->req_id)):
                        $this->edit($this->purchase_indent,['id'=>$row->req_id],['order_status'=>1]);
                    endif;

                    if(!empty($row->ref_id)):
                        $this->edit('purchase_enquiry',['id'=>$row->ref_id],['trans_status'=>2]);
                    endif;
                endforeach;

                $this->trash($this->transExpense,['trans_main_id'=>$data['id'],'entry_type'=>$this->data['entryData']->id]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->po_master,'description'=>"PO TERMS"]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->po_master,'description'=>"PO MASTER DETAILS"]);
            endif;
            
            $masterDetails = $data['masterDetails'];
            $itemData = $data['itemData'];

            $transExp = getExpArrayMap(((!empty($data['expenseData']))?$data['expenseData']:array()));
			$expAmount = $transExp['exp_amount'];
            $termsData = (!empty($data['conditions']))?$data['conditions']:array();

            unset($transExp['exp_amount'],$data['itemData'],$data['expenseData'],$data['conditions'],$data['masterDetails']);
			unset($data['igst_acc_id'],$data['cgst_acc_id'],$data['sgst_acc_id'],$data['tcs_acc_id'],$data['tds_acc_id'],$data['round_off_acc_id']);			
            
			$data['doc_date'] = (!empty($data['doc_date']) ? $data['doc_date'] : NULL);
            $result = $this->store($this->po_master,$data,'Purchase Order');

            $masterDetails['id'] = "";
            $masterDetails['main_ref_id'] = $result['id'];
            $masterDetails['table_name'] = $this->po_master;
            $masterDetails['description'] = "PO MASTER DETAILS";
            $this->store($this->transDetails,$masterDetails);

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
                    'table_name' => $this->po_master,
                    'description' => "PO TERMS",
                    'main_ref_id' => $result['id'],
                    't_col_1' => $termsData
                ];
                $this->store($this->transDetails,$termsData);
            endif;

			foreach($itemData as $row): 
                $row['entry_type'] = $data['entry_type'];
                $row['po_id'] = $result['id'];
                $row['delivery_date'] = (!empty($row['delivery_date']) ? $row['delivery_date'] : NULL);
                $row['is_delete'] = 0;
                $this->store($this->po_trans,$row);

                if(!empty($row['ref_id'])):
                    $this->edit('purchase_enquiry',['id'=>$row['ref_id']],['trans_status'=>5]);
                endif;
				if(!empty($row['req_id'])):
					$this->edit($this->purchase_indent,['id'=>$row['req_id']],['order_status'=>2]);
                endif;
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
        $queryData['tableName'] = $this->po_master;
        $queryData['where']['trans_number'] = $data['trans_number'];

		$queryData['where']['po_master.trans_date >='] = $this->startYearDate;
		$queryData['where']['po_master.trans_date <='] = $this->endYearDate;

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getPurchaseOrder($data){
        $queryData = array();
        $queryData['tableName'] = $this->po_master;
        $queryData['select'] = "po_master.*, trans_details.t_col_3 as delivery_address, trans_details.t_col_4 as delivery_pincode,party_master.party_name,party_master.contact_person,party_master.party_mobile,prepare.emp_name as prepareBy,approve.emp_name as approveBy";
        $queryData['leftJoin']['trans_details'] = "po_master.id = trans_details.main_ref_id AND trans_details.description = 'PO MASTER DETAILS' AND trans_details.table_name = '".$this->po_master."'";
        $queryData['leftJoin']['party_master'] = "party_master.id = po_master.party_id";
		$queryData['leftJoin']['employee_master prepare'] = "prepare.id = po_master.created_by";
        $queryData['leftJoin']['employee_master approve'] = "approve.id = po_master.is_approve";
		
        $queryData['where']['po_master.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
            $result->itemList = $this->getPurchaseOrderItems($data);
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
        $queryData['where']['table_name'] = $this->po_master;
        $queryData['where']['description'] = "PO TERMS";
        $result->termsConditions = $this->row($queryData);

        return $result;
    }

    public function getPurchaseOrderItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->po_trans;
        $queryData['select'] = "po_trans.*,item_master.item_name,po_master.trans_number,po_master.trans_date,party_master.party_name,item_master.gst_per,item_master.uom as unit_name,item_master.cnv_value,item_master.com_uom as com_unit,item_master.hsn_code,material_master.material_grade, (CASE WHEN po_trans.from_entry_type = 160 THEN purchase_enquiry.trans_number ELSE purchase_indent.trans_number END) as enq_number,fgItem.item_code as fg_item_code,fgItem.item_name as fg_item_name,item_master.item_type";
        $queryData['leftJoin']['item_master'] = "item_master.id = po_trans.item_id";
        $queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
        $queryData['leftJoin']['po_master'] = "po_master.id = po_trans.po_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = po_master.party_id";
        $queryData['leftJoin']['purchase_enquiry'] = "purchase_enquiry.id = po_trans.ref_id"; 
        $queryData['leftJoin']['purchase_indent'] = "purchase_indent.id = po_trans.req_id";
        $queryData['leftJoin']['item_master fgItem'] = "fgItem.id = po_trans.fg_item_id";

        if(!empty($data['id'])){$queryData['where']['po_trans.po_id'] = $data['id'];} 
        if(!empty($data['item_id'])){$queryData['where']['po_trans.item_id'] = $data['item_id'];}

        if(!empty($data['order_by'])){
            $queryData['order_by']['po_master.trans_date'] = 'DESC';
        }
        
		$result = $this->rows($queryData);
		return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $itemList = $this->getPurchaseOrderItems(['id'=>$id]);
            foreach($itemList as $row):
                $this->trash($this->po_trans,['id'=>$row->id]);
				
                if(!empty($row->req_id)):
                    $this->edit($this->purchase_indent,['id'=>$row->req_id],['order_status'=>1]);
                endif;
                if(!empty($row->ref_id)):
                    $this->edit('purchase_enquiry',['id'=>$row->ref_id],['trans_status'=>2]);
                endif;
            endforeach;

            $this->trash($this->transExpense,['trans_main_id'=>$id,'entry_type'=>$this->data['entryData']->id]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->po_master,'description'=>"PO TERMS"]);
            $result = $this->trash($this->po_master,['id'=>$id],'Purchase Order');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getPartyWisePoList($data){
        $queryData['tableName'] = $this->po_master;
        $queryData['select'] = "po_master.id as po_id,po_master.trans_number,po_trans.trans_status";
        $queryData['leftJoin']['po_trans'] = "po_trans.po_id = po_master.id";

        $queryData['where']['po_master.entry_type'] = $data['entry_type'];
        $queryData['where']['po_master.party_id'] = $data['party_id'];
        $queryData['where']['po_trans.trans_status'] = 3;
        $queryData['where']['po_master.is_approve !='] = 0;
        $queryData['group_by'][] = 'po_master.id';

        return $this->rows($queryData);
    }

    public function getPendingPoItems($data){
        $queryData['tableName'] = $this->po_trans;
        $queryData['select'] = "po_trans.id as po_trans_id,po_trans.item_id,item_master.item_code,item_master.item_name,item_master.item_type,po_trans.qty,po_trans.dispatch_qty as received_qty,(po_trans.qty - po_trans.dispatch_qty) as pending_qty,po_trans.price,po_trans.disc_per,po_trans.trans_status,po_trans.fg_item_id,fgItem.item_name as fg_item_name,fgItem.item_code as fg_item_code,po_trans.so_trans_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = po_trans.item_id";
        $queryData['leftJoin']['item_master fgItem'] = "fgItem.id = po_trans.fg_item_id";
		
		$queryData['where']['po_trans.trans_status'] = 3; 
        $queryData['where']['po_trans.entry_type'] = $data['entry_type'];
        $queryData['where']['po_trans.po_id'] = $data['po_id'];
        $queryData['where']['(po_trans.qty - po_trans.dispatch_qty) >'] = 0;

        return $this->rows($queryData);
    }

    public function getPendingInvoiceItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->po_trans;
        $queryData['select'] = "po_trans.*,(po_trans.qty - po_trans.dispatch_qty) as pending_qty, 1 as stock_eff,po_master.entry_type as main_entry_type,po_master.trans_number,po_master.trans_date,po_master.doc_no,item_master.item_name,unit_master.unit_name";

        $queryData['leftJoin']['po_master'] = "po_trans.po_id = po_master.id";
		$queryData['leftJoin']['item_master'] = "po_trans.item_id = item_master.id";
        $queryData['leftJoin']['unit_master'] = "unit_master.id = po_trans.unit_id";

        $queryData['where']['po_master.party_id'] = $data['party_id'];
        $queryData['where']['po_trans.entry_type'] = $this->data['entryData']->id;
        $queryData['where']['(po_trans.qty - po_trans.dispatch_qty) >'] = 0;
        $queryData['where']['po_trans.trans_status'] = 3;
        return $this->rows($queryData);
    }

	public function changeOrderStatus($postData){ 
        try{
            $this->db->trans_begin();
             //$this->edit($this->po_master,['id'=>$postData['id']],['trans_status'=>$postData['trans_status']]);

             $result = $this->edit($this->po_trans,['id'=>$postData['id']],['trans_status'=>$postData['trans_status']]);

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
    public function getPurchaseOrderByApp($data){
        $queryData = array();
        $queryData['tableName'] = $this->po_master;
        $queryData['select'] = "po_master.*,party_master.party_name,po_trans.qty,item_master.item_name";
        $queryData['leftJoin']['party_master'] = "party_master.id = po_master.party_id";
        $queryData['leftJoin']['po_trans'] = "po_trans.po_id = po_master.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = po_trans.item_id";
        $queryData['where']['po_master.entry_type'] = $this->data['entryData']->id;
        $queryData['where']['po_master.is_approve'] = 0;
        $queryData['where']['po_trans.trans_status'] = 0;
        $queryData['group_by'][] = "po_master.id";
        $queryData['order_by']['po_master.id'] = "DESC";
        $result = $this->rows($queryData);
        return $result;
    }

    public function approvePurchaseOrder($data) {
        try{
            $this->db->trans_begin();

            $date = ($data['is_approve'] == 1) ? date('Y-m-d') : "NULL";
            $isApprove =  ($data['is_approve'] == 1) ? $this->loginId : 0;
            
			if($data['trans_status'] == 3){
				$this->store($this->po_master, ['id'=> $data['id'],'trans_status'=>$data['trans_status'], 'is_approve' => $isApprove, 'approve_date'=>$date]);
				$this->edit($this->po_trans,['po_id'=>$data['id'],'trans_status'=>0],['trans_status'=>$data['trans_status']]);
            }else{
				$this->store($this->po_master, ['id'=> $data['id'],'trans_status'=>$data['trans_status'], 'is_approve' => $isApprove, 'approve_date'=>$date]);
				$this->edit($this->po_trans,['po_id'=>$data['id'],'trans_status'=>3],['trans_status'=>$data['trans_status']]);
			}
            $result = ['status' => 1, 'message' => 'Purchase Order ' . $data['msg'] . ' successfully.'];

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