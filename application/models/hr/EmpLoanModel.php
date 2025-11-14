<?php
class EmpLoanModel extends MasterModel
{
    private $empMaster = "employee_master";
    private $empLoan = "emp_loan";
    
	public function getDTRows($data){
        $data['tableName'] = $this->empLoan;
        $data['select'] = "emp_loan.*,employee_master.emp_name,employee_master.emp_code";
        $data['leftJoin']['employee_master'] = "emp_loan.emp_id = employee_master.id";
        $data['where']['emp_loan.trans_status'] = $data['trans_status'];

        if(!empty($data['trans_status'])):
            $data['where']['emp_loan.entry_date >='] = $this->startYearDate;
            $data['where']['emp_loan.entry_date <='] = $this->endYearDate;
        endif;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_number";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "emp_loan.entry_date";
        $data['searchCol'][] = "emp_loan.demand_amount";
        $data['searchCol'][] = "emp_loan.reason";
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getEmpLoan($id)
    {
        $data['tableName'] = $this->empLoan;
        $data['select'] = "emp_loan.*,employee_master.emp_name,employee_master.emp_code,employee_master.emp_contact,approve.emp_name as approve_by_name,dept.name as dept_name,edesign.title as emp_designation";
        $data['leftJoin']['employee_master'] = "emp_loan.emp_id = employee_master.id";
        $data['leftJoin']['employee_master approve'] = "emp_loan.approved_by = approve.id";
        $data['leftJoin']['department_master dept'] = "employee_master.emp_dept_id = dept.id";
        $data['leftJoin']['emp_designation edesign'] = "employee_master.emp_designation = edesign.id";
        $data['where']['emp_loan.id'] = $id;
        return $this->row($data);
    }
    
	public function getEmployeeList()
    {
        $data['tableName'] = $this->empMaster;
        return $this->rows($data);
    }

    public function save($data)
    {
        try{
			$this->db->trans_begin();
            if(empty($data['id'])){
                $data['trans_no'] = $this->getNextLoanNo();
                $data['trans_number']=sprintf("L%04d",$data['trans_no']);
            }
			$result = $this->store($this->empLoan,$data,'Loan');
			$data['id'] = (empty($data['id']))?$result['insert_id']:$data['id'];
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Exception $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }              
    }

   

    public function getNextLoanNo(){
        $data['tableName'] = $this->empLoan;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['emp_loan.entry_date >='] = $this->startYearDate;
        $data['where']['emp_loan.entry_date <='] = $this->endYearDate;
        $maxNo = $this->specificRow($data)->trans_no;
		$nextNo = (!empty($maxNo))?($maxNo + 1):1;
		return $nextNo;
    }

    public function delete($id)
    {
        try{
			$this->db->trans_begin();
			
			$result= $this->trash($this->empLoan,['id'=>$id],'Loan');
			// $this->transMainModel->deleteLedgerTrans($id);

			if($this->db->trans_status() !== FALSE):
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