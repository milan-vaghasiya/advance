<?php
class MaintenancePlanModel extends MasterModel{
    private $prevMaintenancePlan = "preventive_maintenance_plan";
    private $machinePreventive = "machine_preventive";
    private $mcBreakdown = "machine_breakdown";

    public function getDTRows($data){
        $hr = '<hr class="m-0">';
        $data['tableName'] = $this->machinePreventive;
        $data['select'] = "machine_preventive.*,GROUP_CONCAT(DISTINCT machine_activities.activities SEPARATOR '".$hr."') as activities,item_master.item_name,item_master.item_code,preventive_maintenance_plan.id as prev_main_plan_id,GROUP_CONCAT(DISTINCT machine_preventive.id) as main_id,employee_master.emp_name,item_master.plan_days"; 

        $data['leftJoin']['machine_activities'] = "FIND_IN_SET(machine_activities.id,machine_preventive.activity_id) > 0";
        $data['leftJoin']['item_master'] = "item_master.id = machine_preventive.machine_id";
        $data['leftJoin']['preventive_maintenance_plan'] = "machine_activities.id = preventive_maintenance_plan.activity_id";
        $data['leftJoin']['employee_master'] = "preventive_maintenance_plan.schedule_by = employee_master.id";

        $data['where']['machine_preventive.checking_frequancy !='] = 'Daily';
        $data['customWhere'][] = 'machine_preventive.activity_id != "" AND machine_preventive.activity_id IS NOT NULL'; 
        $data['customWhere'][] = 'machine_preventive.schedule_date IS NULL'; 
        $data['group_by'][] = "machine_preventive.machine_id,machine_preventive.checking_frequancy";

		if(!empty($data['maintence_frequancy'])){ $data['where']['machine_preventive.checking_frequancy'] = $data['maintence_frequancy']; }
        
        //if(!empty($data['status']) && $data['status'] == 1){
			//$data['customWhere'][] = "DATE_SUB(machine_preventive.due_date, INTERVAL item_master.plan_days DAY) <= '".date('Y-m-d')."'";
        //}
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "CONCAT(item_master.item_code,item_master.item_name)";
        $data['searchCol'][] = "machine_activities.activities";
        $data['searchCol'][] = "machine_preventive.checking_frequancy";
        $data['searchCol'][] = "DATE_FORMAT(machine_preventive.created_at,'%d-%m-%Y')";
        $data['searchCol'][] = "DATE_FORMAT(machine_preventive.created_at,'%d-%m-%Y')";
        $data['searchCol'][] = "DATE_FORMAT(machine_preventive.schedule_date,'%d-%m-%Y')";   
        $data['searchCol'][] = ""; 

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getProdRequestDTRows($data){
        $hr = '<hr class="m-0">';
        $data['tableName'] = $this->prevMaintenancePlan;
        $data['select'] = "preventive_maintenance_plan.*,GROUP_CONCAT(DISTINCT machine_activities.activities SEPARATOR '".$hr."') as activities,item_master.item_name,item_master.item_code,GROUP_CONCAT(DISTINCT preventive_maintenance_plan.id) as prev_main_plan_id,employee_master.emp_name,GROUP_CONCAT(DISTINCT machine_activities.activities) as activity_list,(CASE WHEN preventive_maintenance_plan.agency = 1 THEN preventive_maintenance_plan.solution_by ELSE party_master.party_name END) as solutionBy";

        $data['leftJoin']['machine_activities'] = "FIND_IN_SET(machine_activities.id,preventive_maintenance_plan.activity_id) > 0";
        $data['leftJoin']['item_master'] = "item_master.id = preventive_maintenance_plan.machine_id";
        $data['leftJoin']['employee_master'] = "preventive_maintenance_plan.schedule_by = employee_master.id";
        $data['leftJoin']['party_master'] = "party_master.id = preventive_maintenance_plan.solution_by";
        $data['group_by'][] = "preventive_maintenance_plan.machine_id,preventive_maintenance_plan.maintence_frequancy";

		if(!empty($data['maintence_frequancy'])){ $data['where']['preventive_maintenance_plan.maintence_frequancy'] = $data['maintence_frequancy']; }

		if($data['status'] == 2){ 
            $data['where']['preventive_maintenance_plan.schedule_by >='] = 0; 
            $data['customWhere'][] = 'preventive_maintenance_plan.solution_by IS NULL'; 
        }elseif($data['status'] == 3){ 
            $data['customWhere'][] = 'preventive_maintenance_plan.solution_by IS NOT NULL'; 
        }elseif($data['status'] == 4){ 
            $data['where']['preventive_maintenance_plan.schedule_by'] = 0; 
        }elseif($data['status'] == 5){ 
            $data['where']['preventive_maintenance_plan.schedule_by >'] = 0; 
            $data['where']['preventive_maintenance_plan.accept_by'] = 0; 
        }elseif($data['status'] == 6){ 
            $data['where']['preventive_maintenance_plan.accept_by >'] = 0; 
        }

		$data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "machine_activities.activities";
        $data['searchCol'][] = "preventive_maintenance_plan.maintence_frequancy";
        $data['searchCol'][] = "DATE_FORMAT(preventive_maintenance_plan.last_maintence_date,'%d-%m-%Y')";

        if(($data['status'] == 3)){
            $data['searchCol'][] = "DATE_FORMAT(preventive_maintenance_plan.solution_date,'%d-%m-%Y')";
            $data['searchCol'][] = "if(preventive_maintenance_plan.agency = 1,'In House','Third Party')";
            $data['searchCol'][] = "preventive_maintenance_plan.solution_by";
            $data['searchCol'][] = "preventive_maintenance_plan.solution_status";
            $data['searchCol'][] = "preventive_maintenance_plan.remark";
        }
        if(!in_array($data['status'],[3,6])){
            $data['searchCol'][] = "DATE_FORMAT(preventive_maintenance_plan.due_date,'%d-%m-%Y')";
            $data['searchCol'][] = "DATE_FORMAT(preventive_maintenance_plan.schedule_date,'%d-%m-%Y')";     
            $data['searchCol'][] = "";             
        }
        
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getActivitiesForSchedule($param){
        $hr = '<hr class="m-0">';
        $data['tableName'] = $this->machinePreventive;
        $data['select'] = "machine_preventive.*,GROUP_CONCAT(DISTINCT machine_activities.activities SEPARATOR '".$hr."') as activities,item_master.item_name,item_master.item_code,preventive_maintenance_plan.schedule_date,preventive_maintenance_plan.id as prev_main_plan_id,GROUP_CONCAT(DISTINCT machine_preventive.id) as main_id,GROUP_CONCAT(DISTINCT machine_preventive.activity_id) as activity_id";

        $data['leftJoin']['machine_activities'] = "FIND_IN_SET(machine_activities.id,machine_preventive.activity_id) > 0";
        $data['leftJoin']['item_master'] = "item_master.id = machine_preventive.machine_id";
        $data['leftJoin']['preventive_maintenance_plan'] = "machine_activities.id = preventive_maintenance_plan.activity_id";
        $data['where_in']['machine_preventive.id'] = $param['ids'];
        $data['group_by'][] = "machine_preventive.machine_id,machine_preventive.checking_frequancy";
        return $this->rows($data);
    }

    public function saveSchedule($data){
        try{
            $this->db->trans_begin();

            foreach($data['main_id'] as $key=>$value){
                if(!empty($value)){
                    $mainIdArray = explode(',',$value);
                    $activityIdArray = array_unique(explode(',', $data['activity_id'][$key]));
                    $i=0;
                    foreach($mainIdArray as $mainId){
                        $planData = [
                            'id' => $data['id'][$key],
                            'machine_id' => $data['machine_id'][$key],
                            'activity_id' => $activityIdArray[$i],
                            'maintence_frequancy' => $data['checking_frequancy'][$key],
                            'last_maintence_date' => date('Y-m-d',strtotime($data['last_maintenance_date'][$key])),
                            'due_date' => date('Y-m-d',strtotime($data['due_date'][$key])),
                            'schedule_date' => $data['schedule_date'],
                            'created_by' => $data['created_by'],
                            'mc_prev_id' => $mainId,
                        ];
                        $this->store($this->prevMaintenancePlan, $planData);
                        $this->edit($this->machinePreventive, ['id'=>$mainId], ['schedule_date'=>$data['schedule_date']]);
                        $i++;
                    }
                }
            }
            $result = ['status'=>1, 'message'=>'Maintenance Plan Schedule Saved Successfully.'];
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
	
    public function getPreventiveMaintanancePlan($param){
        $data['tableName'] = $this->prevMaintenancePlan;
        $data['select'] = "GROUP_CONCAT(DISTINCT preventive_maintenance_plan.id) as prev_main_plan_id,preventive_maintenance_plan.schedule_date";
        $data['where_in']['preventive_maintenance_plan.id'] = $param['id'];
        $data['group_by'][] = "preventive_maintenance_plan.machine_id,preventive_maintenance_plan.maintence_frequancy";
        return $this->row($data);
    }

    public function saveScheduleDate($data){
        try{
            $this->db->trans_begin();
            
            $mainIdArray = explode(',',$data['prev_main_plan_id']);
            foreach($mainIdArray as $key=>$mainId){
                $sData = [
                    'id' => $mainId,
                    'schedule_date' => $data['schedule_date'],
                    'schedule_by' => $this->loginId
                ];
                $this->store($this->prevMaintenancePlan, $sData);

                $queryData['tableName'] = $this->prevMaintenancePlan;
                $queryData['where']['id'] = $mainId;
                $prevMainData = $this->row($queryData);
                
                $this->store($this->machinePreventive, ['id'=>$prevMainData->mc_prev_id, 'schedule_date'=>$data['schedule_date']]);
            }
            $result = ['status'=>1,'message'=>"Schedule Updated Successfully."];
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    } 
    
    public function startMaintenance($data) {
        try{
            $this->db->trans_begin();
            
            $mainIdArray = explode(',',$data['id']);
            foreach($mainIdArray as $key=>$mainId){
                $sData = [
                    'id' => $mainId,
                    'start_date' => date('Y-m-d H:i:s'),
                    'start_by' => $this->loginId
                ];
                $this->store($this->prevMaintenancePlan, $sData);
            }

            $mcData = $this->machineBreakdown->getMachineBreakdown(['machine_id'=>$data['machine_id'], 'pending_solution'=>1, 'multi_row'=>1]);
            if(empty($mcData)){
                $breakdownData = [
                    'id' => '',
                    'trans_date' => date('Y-m-d H:i:s'),
                    'machine_id' => $data['machine_id'],
                    'idle_reason' => '-1',
                    'created_by' => $this->loginId,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->store($this->mcBreakdown, $breakdownData);
            }else{
                return ['status'=>0,'message'=>"You can not start the maintenance because machine is already in breakdown."];                
            }

            $result = ['status'=>1,'message'=>"Maintenance Started Successfully."];
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getActivityForSolution($param){
        $data['tableName'] = $this->prevMaintenancePlan;
        $data['select'] = "preventive_maintenance_plan.*,machine_activities.activities";
        $data['leftJoin']['machine_activities'] = "machine_activities.id = preventive_maintenance_plan.activity_id";
        $data['where_in']['preventive_maintenance_plan.id'] = $param['id'];
        return $this->rows($data);
    }
    
    public function saveSolution($data) {
        try{
            $this->db->trans_begin();
               
            if($data['agency'] == 2){
                $data['solution_by'] = $data['vendor_id'];
            }
            unset($data['vendor_id']);

            foreach($data['id'] as $key=>$mainId){
                $sData = [
                    'id' => $mainId,
                    'solution_date' => date('Y-m-d H:i:s'),
                    'solution_by' => $data['solution_by'],
                    'solution_status' => $data['solution_status'][$key],
                    'agency' => $data['agency'],
                    'remark' => $data['remark'][$key]
                ];
                $this->store($this->prevMaintenancePlan, $sData);

                $queryData['tableName'] = $this->prevMaintenancePlan;
                $queryData['where']['id'] = $mainId;
                $prevMainData = $this->row($queryData);

                $last_maintence_date = date('Y-m-d');
                $due_date = '';
                if($prevMainData->maintence_frequancy == 'Monthly'){ $due_date = date("Y-m-d",strtotime($last_maintence_date.' +1 months')); }
                elseif($prevMainData->maintence_frequancy == 'Quarterly'){ $due_date = date("Y-m-d",strtotime($last_maintence_date.' +3 months')); }
                elseif($prevMainData->maintence_frequancy == 'Half Yearly'){ $due_date = date("Y-m-d",strtotime($last_maintence_date.' +6 months')); }
                elseif($prevMainData->maintence_frequancy == 'Yearly'){ $due_date = date("Y-m-d",strtotime($last_maintence_date.' +12 months')); }
                
                $this->store($this->machinePreventive, ['id'=>$prevMainData->mc_prev_id, 'schedule_date'=>NULL, 'last_maintence_date'=>$last_maintence_date, 'due_date'=>$due_date]);
            }

            $this->edit($this->mcBreakdown, ['machine_id'=>$data['machine_id']], ['end_date'=>date('Y-m-d H:i:s'), 'solution'=>$data['activity_list']]);

            $result = ['status'=>1,'message'=>"Maintenance Solution Saved Successfully."];
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function acceptRequest($data) {
        try{
            $this->db->trans_begin();
            
            $mainIdArray = explode(',',$data['id']);
            foreach($mainIdArray as $key=>$mainId){
                $sData = [
                    'id' => $mainId,
                    'accept_date' => date('Y-m-d H:i:s'),
                    'accept_by' => $this->loginId
                ];
                $this->store($this->prevMaintenancePlan, $sData);
            }
            
            $result = ['status'=>1,'message'=>"Production Request Accepted Successfully."];
            
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