<?php
class ExpenseMasterModel extends MasterModel{
    private $expenseMaster = "expense_master";
    
	public function getDTRows($data){
        $data['tableName'] = $this->expenseMaster;
        $data['select'] = 'expense_master.*, party_master.party_name,(CASE WHEN expense_master.calc_type = 1 THEN "Fixed" WHEN expense_master.calc_type = 2 THEN "Percentage" WHEN expense_master.calc_type = 3 THEN "Cumulative" ELSE "" END) as calc_type_name,
        (CASE WHEN expense_master.is_active = 1 THEN "Active" WHEN expense_master.is_active = 0 THEN "Inactive" ELSE "" END) as is_active_name,
        (CASE WHEN expense_master.add_or_deduct = 1 THEN "Add" WHEN expense_master.add_or_deduct = -1 THEN "Deduct" ELSE "" END) as add_or_deduct_name';
        $data['leftJoin']['party_master'] = 'party_master.id = expense_master.acc_id';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "exp_name";
        $data['searchCol'][] = "entry_name";
        $data['searchCol'][] = "seq";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "(CASE WHEN expense_master.calc_type =1 THEN 'Fixed' WHEN expense_master.calc_type =2 THEN 'Percentage' WHEN expense_master.calc_type =3 THEN 'Cumulative' ELSE '' END)";
        $data['searchCol'][] = "(CASE WHEN expense_master.is_active =1 THEN 'Active' WHEN expense_master.is_active =0 THEN 'Inactive' ELSE '' END)";
        $data['searchCol'][] = "(CASE WHEN expense_master.add_or_deduct = 1 THEN 'Add' WHEN expense_master.add_or_deduct = -1 THEN 'Deduct' ELSE '' END)";
       
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getExpenseMaster($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->expenseMaster;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if(empty($data['id'])):
                $limit = $this->expenseLimit($data['entry_type']);
                if($limit > 25):
                    return['status'=>2,'message'=>"You have reached limit. You can't open new expense."];           
                endif;

                $queryData = array();
                $queryData['tableName'] = $this->expenseMaster;
                $queryData['select'] = "map_code";
                $queryData['entry_type'] = $data['entry_type'];
                $mapCodeData = $this->rows($queryData);
                $expCode = "";
                for($i = 1; $i<= 25; $i++):
                    if(!in_array("exp".$i,array_column($mapCodeData,'map_code'))):
                        $expCode = "exp" .$i;
                        break;
                    endif;
                endfor;
                $data['map_code'] = $expCode;
            endif;

            if($this->checkDuplicate($data) > 0):
                $errorMessage['exp_name'] = "Expense Name is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $result = $this->store($this->expenseMaster,$data,'Expense Master');

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
        $queryData['tableName'] = $this->expenseMaster;
        $queryData['where']['exp_name'] = $data['exp_name'];        
        $queryData['where']['entry_type'] = $data['entry_type'];  
              
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData); //$this->printQuery();
    }

    public function expenseLimit($entryType){
        $queryData['tableName'] = $this->expenseMaster;
        $queryData['where']['entry_type'] = $entryType;

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $result = $this->trash($this->expenseMaster,['id'=>$id],'Expense Master');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function getActiveExpenseList($type = ""){
        $queryData = array();
        $queryData['tableName'] = $this->expenseMaster;
        $queryData['where']['is_active'] = 1;
        
        if(!empty($type))
            $queryData['where']['entry_type'] = $type;
        
        $queryData['order_by']['seq'] = "ASC";
        $result = $this->rows($queryData);
        return $result;
    }

    public function getExpenseList($data){
        $queryData = array();
        $queryData['tableName'] = $this->expenseMaster;
        
        if(!empty($data['expense_ids']))
            $queryData['where_in']['id'] = $data['expense_ids'];
        if(!empty($data['type']))
            $queryData['where']['entry_type'] = $data['type'];
        
        $queryData['order_by']['seq'] = "ASC";
        $result = $this->rows($queryData);
        return $result;
    }
}
?>