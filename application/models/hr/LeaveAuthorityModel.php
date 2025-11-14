<?php
class LeaveAuthorityModel extends MasterModel
{
    private $empMaster = "employee_master";
	
	public function getLeaveAuthority($postData){
		$postData['tableName'] = $this->empMaster;	
		$postData['select'] = "employee_master.id,employee_master.emp_code,employee_master.emp_name,employee_master.pla_id,employee_master.fla_id,";
		$postData['select'] .= '
		(
		    SELECT GROUP_CONCAT(pla_emp.emp_name) FROM employee_master as pla_emp WHERE FIND_IN_SET(pla_emp.id, employee_master.pla_id)
		) as plaList,
		(
		    SELECT GROUP_CONCAT(fla_emp.emp_name) FROM employee_master as fla_emp WHERE FIND_IN_SET(fla_emp.id, employee_master.fla_id)
		) as flaList';
        $postData['where']['employee_master.emp_role !='] = "-1";
        if(!empty($postData['dept_id'])){$postData['where']['employee_master.emp_dept_id'] = $postData['dept_id'];}
        $postData['where']['employee_master.is_active'] = 1;
        $result = $this->rows($postData);
        return $result;
    }
    
    public function saveAuthority($data){
        try{
            $this->db->trans_begin();

            $result = Array();
            if(!empty($data['id'])):			         
                $result = $this->edit($this->empMaster, ['id'=>$data['id']], ['pla_id' => $data['pla_id'], 'fla_id' => $data['fla_id'], 'updated_by' => $data['updated_by'], 'updated_at' => $data['updated_at']]);
            endif;

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