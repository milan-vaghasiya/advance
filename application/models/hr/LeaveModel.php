<?php
class LeaveModel extends MasterModel{
    private $leaveMaster = "leave_master";
	private $leaveType = "leave_type";
	private $leaveAuthority = "leave_authority";
    private $empDesignation = "emp_designation";
    private $empMaster = "employee_master";
	private $empRole = ["1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager"];
	
	public function getDTRows($data){
        $data['tableName'] = $this->leaveMaster;
		$data['select'] = "leave_master.*,employee_master.emp_name,employee_master.emp_designation,employee_master.emp_profile,employee_master.emp_code,leave_type.leave_type";
        $data['join']['employee_master'] = "employee_master.id = leave_master.emp_id";
        $data['leftJoin']['leave_type'] = "leave_type.id = leave_master.leave_type_id";

        if($data['login_emp_id'] != 1):
            $data['where']['leave_master.emp_id'] = $data['login_emp_id'];
        endif;
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "emp_name";
        $data['searchCol'][] = "emp_code";
        $data['searchCol'][] = "leave_type";
        $data['searchCol'][] = "leave_reason";
        $data['searchCol'][] = "start_date";
        $data['searchCol'][] = "end_date";
        $data['searchCol'][] = "total_days";
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
	
    public function getLeaveType(){
        $data['tableName'] = $this->leaveType;
        $leaveType = $this->rows($data);
		return $leaveType;
    }
	
    public function getEmpData($id){
		$data['where']['id'] = $id;
        $data['tableName'] = $this->empMaster;
        return $this->row($data);
    }
	
    public function getLeave($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->leaveMaster;
        return $this->row($data);
    }
	
    public function getEmpLeaves($emp_id,$leave_type_id,$start_date,$end_date){
		
		$emp_leaves = Array();
		
        $empData = $this->getEmpData($emp_id);
		if(!empty($leave_type_id)){$data['where']['id'] = $leave_type_id;}
		$data['tableName'] = $this->leaveType;
		$leaveType = $this->rows($data);
		if(!empty($leaveType))
		{
			foreach($leaveType as $row)
			{
				$lq=array();$max_leave=0;$leave_period=1;
				$data1['select'] = "SUM(total_days) as total_days";
				$data1['where']['emp_id'] = $emp_id;
				$data1['where']['approve_status'] = 1;
				$data1['where']['start_date>='] = $start_date;
				$data1['where']['end_date<='] = $end_date;
				$data1['where']['leave_type_id'] = $row->id;
				$data1['tableName'] = $this->leaveMaster;
				$used_leaves = $this->specificRow($data1)->total_days;
				if(empty($used_leaves)){$used_leaves=0;}
				$lq['emp_id'] = $emp_id;
				$lq['leave_type_id'] = $row->id;
				$lq['leave_type'] = $row->leave_type;
				$lq['max_leave'] = $max_leave;
				$lq['leave_period'] = $leave_period;
				$lq['used_leaves'] = $used_leaves;
				$lq['remain_leaves'] = $max_leave - $used_leaves;
				$emp_leaves[] = $lq;
			}
		}
		return $emp_leaves;
    }

    public function getLeveAuthority($emp_id){
        $data['tableName'] = $this->leaveAuthority;
        $data['select'] = "leave_authority.id";
        $data['where']['emp_id'] = $emp_id;
        $data['order_by']['priority'] = "ASC";
        return $this->rows($data);
    }
	
    public function save($data){
        try{
            $this->db->trans_begin();
            $result = Array();
            $result =$this->store($this->leaveMaster,$data,'Leave');
            
    		if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
		return $result;
    }

    public function checkDuplicate($leave_type,$id=""){
        $data['tableName'] = $this->leaveMaster;
        $data['where']['leave_type'] = $leave_type;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $result = Array();
            $result =$this->trash($this->leaveMaster,['id'=>$id],'Leave');
            
    		if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
		return $result;
    }

    public function getEmpPolicy($id){
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.*, attendance_policy.short_leave_hour, attendance_policy.no_short_leave";
        $data['join']['attendance_policy'] = "attendance_policy.id = employee_master.attendance_policy";
        $data['where']['employee_master.id'] = $id;
        return $this->row($data);
    }

    public function getEmpLeavePolicy($emp_id,$start_date,$end_date){
        $data['tableName'] = $this->leaveMaster;
        $data['where']['approve_status !='] = 2;
        $data['where']['leave_type_id'] = -1;
        $data['where']['emp_id'] = $emp_id;
        $queryData['customWhere'][] = "start_date BETWEEN '".$start_date."' AND '".$end_date."'";
        return $this->rows($data);
    }
    
    public function getLeaveQuota($postData){
        $data = array();
        $data['tableName'] = 'employee_master';
        $data['select'] = 'employee_master.shl_policy';
        $data['where']['id'] = $postData['emp_id'];
        $empData = $this->row($data);
        
        $data = array();
        $data['tableName'] = 'leave_master';
        $data['select'] = 'count(id) as id';
        $data['where']['approve_status'] = 1;
        $data['where']['month(start_date)'] = date('m', strtotime($postData['start_date']));
        $data['where']['year(start_date)'] = date('Y', strtotime($postData['start_date']));
        $leaveCountData = $this->row($data);
        
        return ['status'=>1,'max_leave'=>$empData->shl_policy,'used_leave'=>$leaveCountData->id];
    }
}
?>