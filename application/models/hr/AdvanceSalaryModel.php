<?php
class AdvanceSalaryModel extends MasterModel
{
    private $advanceSalary = "advance_salary";
    private $empMaster = "employee_master";

    
	public function getDTRows($data){
        $data['tableName'] = $this->advanceSalary;
        $data['select'] = "advance_salary.*,employee_master.emp_name,employee_master.emp_code,sanction.emp_name as sanctioned_by_name,(advance_salary.sanctioned_amount - advance_salary.deposit_amount) as pending_amount";
        $data['leftJoin']['employee_master'] = "advance_salary.emp_id = employee_master.id";
        $data['leftJoin']['employee_master as sanction'] = "advance_salary.sanctioned_by = sanction.id";
        $data['where']['advance_salary.type'] = $data['type'];

        if(isset($data['status'])):
            if($data['status'] == 0):
                $data['where']['advance_salary.sanctioned_by'] = 0;
            elseif($data['status'] == 1):
                $data['where']['advance_salary.sanctioned_by !='] = 0;
                $data['where']['(advance_salary.sanctioned_amount - advance_salary.deposit_amount) > '] = 0;
            else:
                $data['where']['advance_salary.sanctioned_by !='] = 0;
                $data['where']['(advance_salary.sanctioned_amount - advance_salary.deposit_amount) <= '] = 0;
            endif;

            if(!empty($data['status'])):
                $data['where']['advance_salary.entry_date >='] = $this->startYearDate;
                $data['where']['advance_salary.entry_date <='] = $this->endYearDate;
            endif;
        endif;
        
        if($data['type'] != 3){
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "entry_date";
            $data['searchCol'][] = "employee_master.emp_name";
            $data['searchCol'][] = "amount";
            $data['searchCol'][] = "reason";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		    if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		    
        }elseif($data['type'] != 2){
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "entry_date";
            $data['searchCol'][] = "employee_master.emp_name";
            $data['searchCol'][] = "amount";
            $data['searchCol'][] = "reason";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		    if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		    
        }else{
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "advance_salary.entry_date";
            $data['searchCol'][] = "employee_master.emp_name";
            $data['searchCol'][] = "advance_salary.amount";
            $data['searchCol'][] = "advance_salary.reason";
            $data['searchCol'][] = "sanction.sanctioned_by_name";
            $data['searchCol'][] = "advance_salary.sanctioned_at";
            $data['searchCol'][] = "advance_salary.sanctioned_amount";
            $data['searchCol'][] = "advance_salary.deposit_amount";
            $data['searchCol'][] = "(advance_salary.sanctioned_amount - advance_salary.deposit_amount)";
            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		    if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        }
        return $this->pagingRows($data);
    }

    public function getAdvanceSalary($id){
        $data['tableName'] = $this->advanceSalary;        
        $data['select'] = "advance_salary.*,employee_master.emp_name,employee_master.emp_code";
        $data['leftJoin']['employee_master'] = "advance_salary.emp_id = employee_master.id";
        $data['where']['advance_salary.id'] = $id;
        return $this->row($data);
    }

    public function save($data){ 
        try{
            $this->db->trans_begin();        
            $result = $this->store($this->advanceSalary,$data,'AdvanceSalary'); 

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	        
    }  

    public function delete($id){
         try{
            $this->db->trans_begin();        
            $result = $this->trash($this->advanceSalary,['id'=>$id],'AdvanceSalary'); 

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