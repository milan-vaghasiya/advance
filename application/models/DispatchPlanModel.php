<?php
class DispatchPlanModel extends MasterModel{
    private $dispatchPlan = "dispatch_plan";
    private $soTrans = "so_trans";

    public function getNextPlanNo(){
        $queryData['tableName'] = $this->dispatchPlan;
        $queryData['select'] = "ifnull(MAX(plan_no + 1),1) as next_no";
        $queryData['where']['plan_date >='] = $this->startYearDate;
        $queryData['where']['plan_date <='] = $this->endYearDate;
        return $this->row($queryData)->next_no;
    }

	/* UPDATED BY : AVT DATE:13-12-2024 */
    public function getPendingPlanDTRows($data){
        $data['tableName'] = $this->soTrans;
        $data['select'] = "so_trans.*,so_master.trans_number,so_master.trans_date,so_master.party_id,party_master.party_name,item_master.item_code,item_master.item_name,(so_trans.qty - IFNULL(so_trans.dispatch_qty,0.00)) as pending_qty,IFNULL(dp.plan_qty,0.00) as plan_qty,so_master.is_approve";

        $data['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id";
        $data['leftJoin']['party_master'] = "party_master.id = so_master.party_id";
        $data['leftJoin']['item_master'] = "item_master.id = so_trans.item_id";
        $data['leftJoin']['(SELECT SUM(qty) AS plan_qty,so_trans_id FROM dispatch_plan WHERE `is_delete` = 0 GROUP BY so_trans_id) AS dp'] = "dp.so_trans_id = so_trans.id";

        $data['where']['so_master.is_approve >'] = 0;
		$data['where']['so_trans.trans_status ='] = 3;

        $data['order_by']['so_master.trans_date'] = "DESC";
        $data['order_by']['so_master.id'] = "DESC";

        $data['group_by'][] = "so_trans.id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "so_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(so_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "so_trans.qty";
        $data['searchCol'][] = "IFNULL(dp.plan_qty,0.00)";
        $data['searchCol'][] = "so_trans.dispatch_qty";
        $data['searchCol'][] = "(so_trans.qty - IFNULL(so_trans.dispatch_qty,0.00))";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function getDTRows($data){
        $data['tableName'] = $this->dispatchPlan;
        $data['select'] = "dispatch_plan.*,party_master.party_name,item_master.item_code,item_master.item_name,so_master.trans_number AS so_number,so_master.trans_date AS so_date";
        $data['leftJoin']['party_master'] = "party_master.id = dispatch_plan.party_id";
        $data['leftJoin']['item_master'] = "item_master.id = dispatch_plan.item_id";
        $data['leftJoin']['so_trans'] = "so_trans.id = dispatch_plan.so_trans_id";
        $data['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id ";

        $data['where']['dispatch_plan.plan_date >='] = $this->startYearDate;
        $data['where']['dispatch_plan.plan_date <='] = $this->endYearDate;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "dispatch_plan.plan_number";
        $data['searchCol'][] = "DATE_FORMAT(dispatch_plan.plan_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "so_master.trans_number";
        $data['searchCol'][] = "so_master.trans_date";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "dispatch_plan.qty";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            $plan_prefix = 'DP/'.getShortFY().'/';
            $plan_no = $this->getNextPlanNo();
            foreach($data['so_trans_id'] as $key=>$value){
                if($data['qty'][$value] > 0){
                    $dpData = [
                        'id' => '',
                        'plan_date' => $data['plan_date'],
                        'plan_prefix' => $plan_prefix,
                        'plan_no' => $plan_no,
                        'plan_number' => $plan_prefix.$plan_no,
                        'party_id' => $data['party_id'],
                        'so_trans_id' => $value,
                        'item_id' => $data['item_id'][$value],
                        'qty' => $data['qty'][$value],
                        'created_by' => $this->loginId,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->store($this->dispatchPlan, $dpData);
                }
            }
            $result = ['status'=>1,'message'=>"Dispatch Plan Saved Successfully."];

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

            $result = $this->trash($this->dispatchPlan, ['id'=>$id], 'Dispatch Plan');

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