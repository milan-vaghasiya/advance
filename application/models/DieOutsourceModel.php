<?php
class DieOutsourceModel extends MasterModel{
    var $die_production = "die_production";
    var $die_outsource = "die_outsource";

    public function getNextChallanNo(){
		$queryData = array(); 
		$queryData['tableName'] = 'die_outsource';
        $queryData['select'] = "MAX(ch_no) as ch_no ";	
		$queryData['where']['die_outsource.ch_date >='] = $this->startYearDate;
		$queryData['where']['die_outsource.ch_date <='] = $this->endYearDate;

		$ch_no = $this->specificRow($queryData)->ch_no;
		$ch_no = $ch_no + 1;
		return $ch_no;
    }

    public function getDTRows($data)
    {
        $data['tableName'] = $this->die_production;
        $data['select'] = 'die_production.*,item_master.item_name as fg_item_name,item_category.category_name,item_master.item_code as fg_item_code';
        $data['leftJoin']['item_master'] = 'item_master.id = die_production.fg_item_id';
        $data['leftJoin']['item_category'] = 'item_category.id = die_production.item_id';
        $data['where']['die_production.status'] = 3;

        $data['searchCol'][] = "die_production.trans_number";
        $data['searchCol'][] = "die_production.trans_date";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "item_category.remark";
        
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getChallanDTRows($data)
    {
        $data['tableName'] = $this->die_outsource;
        $data['select'] = 'die_outsource.*,die_production.trans_number,item_master.item_name as fg_item_name,item_category.category_name,item_master.item_code as fg_item_code';
        $data['leftJoin']['die_production'] = 'die_production.id = die_outsource.dp_id';
        $data['leftJoin']['item_master'] = 'item_master.id = die_production.fg_item_id';
        $data['leftJoin']['item_category'] = 'item_category.id = die_production.item_id';
        $data['where']['die_outsource.status'] = $data['status'];

        $data['searchCol'][] = "die_outsource.ch_number";
        $data['searchCol'][] = "DATE_FORMAT(die_outsource.ch_date,'%d-%m-%Y')";
        $data['searchCol'][] = "die_production.trans_number";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "item_category.category_name";
        
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function save($data){
		try {
			$this->db->trans_begin();

            $ch_prefix =  'DC'.n2y(date("Y")).n2m(date("m"));
            $ch_no = $this->getNextChallanNo();
            $ch_number = $ch_prefix.str_pad($ch_no,2,0,STR_PAD_LEFT); 
            foreach($data['dp_id'] as $key=>$dp_id){
                $challanData = [
                    'id'=>'',
                    'party_id'=>$data['party_id'],
                    'ch_date'=>$data['ch_date'],
                    'ch_no'=>$ch_no,
                    'ch_number'=>$ch_number,
                    'dp_id'=>$dp_id,
                    'item_id'=>$data['item_id'][$key]
                ];
                $this->store('die_outsource',$challanData, 'Challan');
                $this->edit('die_production',['id'=>$dp_id],['status'=>4,'material_value'=>$data['material_value'][$key],'material_cost'=>$data['material_cost'][$key],'mhr'=>$data['mhr'][$key],'material_weight'=>$data['material_weight'][$key]]);
            }
			
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status'=>1,'message'=>'Challan saved successfully'];
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function delete($ch_number){
        try {
			$this->db->trans_begin();
            $chData = $this->getDieSourceData(['ch_number'=>$ch_number,'status'=>2]);
            if(!empty($chData)){
                return ['status' =>0, 'message' => "You cannot delete this challan, because some of items has already received"];
            }
			
            $chData = $this->getDieSourceData(['ch_number'=>$ch_number]);
            foreach ($chData as $row) {
                $this->edit('die_production',['id'=>$row->dp_id],['status'=>3]);
            }
            $result = $this->trash('die_outsource', ['ch_number'=>$ch_number], 'Challan');
			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function getDieSourceData($param){
		$data['tableName'] = 'die_outsource';
		$data['select'] = 'die_outsource.*,party_master.party_name,party_master.party_address,party_master.gstin,employee_master.emp_name,item_category.category_name,die_production.qty,die_production.trans_number,die_production.material_weight,die_production.material_cost,die_production.trans_type,material_master.material_grade,die_production.mhr';
        $data['leftJoin']['party_master'] = 'party_master.id = die_outsource.party_id';
        $data['leftJoin']['employee_master'] = 'employee_master.id = die_outsource.created_by';
        $data['leftJoin']['die_production'] = 'die_production.id = die_outsource.dp_id';
        $data['leftJoin']['item_category'] = 'item_category.id = die_production.item_id';
        $data['leftJoin']['die_bom'] = 'die_bom.die_id = die_outsource.dp_id';
        $data['leftJoin']['item_master'] = 'item_master.id = die_bom.item_id';
        $data['leftJoin']['material_master'] = 'material_master.id = item_master.grade_id';
       
		if(!empty($param['id'])){$data['where']['die_outsource.id'] = $param['id'];}
		if(!empty($param['ch_number'])){$data['where']['die_outsource.ch_number'] = $param['ch_number'];}
		if(!empty($param['status'])){$data['where']['die_outsource.status'] = $param['status'];}
        if(!empty($param['single_row'])){
            return  $this->row($data);
        }else{
            return  $this->rows($data);
        }
	}

}
?>