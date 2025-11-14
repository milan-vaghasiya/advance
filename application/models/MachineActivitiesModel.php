<?php
class MachineActivitiesModel extends MasterModel{
    private $machineActivities = "machine_activities";
    private $machinePreventive = "machine_preventive";

    public function getDTRows($data){
        $data['tableName'] = $this->machineActivities;

        $data['searchCol'][] = "activities";
        
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getActivities($id){
        $data['tableName'] = $this->machineActivities;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $data['activities'] = trim($data['activities']);
            if(!empty($data['frequency'])){
                $data['frequency'] = implode(',',$data['frequency']);
            }

            if($this->checkDuplicate($data) > 0):
                $errorMessage['activities'] = "Activity is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $result = $this->store($this->machineActivities, $data, 'Machine Activities');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function checkDuplicate($data){
        $queryData['tableName'] = $this->machineActivities;
        $queryData['where']['activities'] = $data['activities'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->machineActivities, ['id' => $id], 'Machine Activities');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getActivity(){
        $data['tableName'] = $this->machineActivities;
        return $this->rows($data);
    }

    public function getmaintenanceData($data){
        $queryData['tableName'] = $this->machinePreventive;
		$queryData['select'] = 'machine_preventive.*,GROUP_CONCAT(DISTINCT machine_activities.activities) as activities';
        $queryData['leftJoin']['machine_activities'] = "FIND_IN_SET(machine_activities.id,machine_preventive.activity_id) > 0";
		$queryData['where']['machine_id'] = $data['machine_id'];
        $queryData['group_by'][] = "machine_preventive.machine_id,machine_preventive.checking_frequancy";
        return $this->rows($queryData);
	}

    public function saveActivity($data){
        try{
            $this->db->trans_begin();
                  
            foreach($data['checking_frequancy'] as $key=>$value):

                if(!empty($data['activity_id'][$key])):
                    
                    $last_maintence_date = $data['last_maintence_date'][$key];
                    $due_date = date('Y-m-d');
                    if($value == 'Monthly'){ $due_date = date("Y-m-d",strtotime($last_maintence_date.' +1 months')); } 
                    elseif($value == 'Quarterly'){ $due_date = date("Y-m-d",strtotime($last_maintence_date.' +3 months')); }
                    elseif($value == 'Half Yearly'){ $due_date = date("Y-m-d",strtotime($last_maintence_date.' +6 months')); }
                    elseif($value == 'Yearly'){ $due_date = date("Y-m-d",strtotime($last_maintence_date.' +12 months')); }

                    $activityData = [
                        'id' => $data['id'][$key],
                        'machine_id' => $data['machine_id'],
                        'activity_id' => (!empty($data['activity_id'][$key]) ? implode(',',$data['activity_id'][$key]) : ''),
                        'checking_frequancy' => $value,
                        'last_maintence_date' => $last_maintence_date,
                        'due_date' => $due_date,
                        'created_by' => $this->loginId
                    ];
                    $this->store($this->machinePreventive, $activityData);

                endif;
            endforeach;
            
            $result = ['status'=>1,'message'=>'Machine Activity Saved Successfully.','url'=>base_url("items/list/5")];

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