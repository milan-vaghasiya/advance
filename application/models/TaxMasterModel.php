<?php
class TaxMasterModel extends MasterModel{
    private $taxMaster = "tax_master";
    private $partyMaster = "party_master";
    
	public function getDTRows($data){
        $data['tableName'] = $this->taxMaster;
        $data['select'] = 'tax_master.*, party_master.party_name as acc_name, (CASE WHEN tax_master.tax_type =1 THEN "Purchase" WHEN tax_master.tax_type =2 THEN "Sales" ELSE "" END) as tax_type_name,
        (CASE WHEN tax_master.calculation_type = 0 THEN "Total Qty" WHEN tax_master.calculation_type = 1 THEN "Basic Amount" WHEN tax_master.calculation_type = 2 THEN "Net Amount" ELSE ""  END) as calc_type_name,
        (CASE WHEN tax_master.is_active =1 THEN "Active" WHEN tax_master.is_active =0 THEN "Inactive" ELSE "" END) as is_active_name,
        (CASE WHEN tax_master.add_or_deduct =1 THEN "Add" WHEN tax_master.add_or_deduct =-1 THEN "Deduct" ELSE "" END) as add_or_deduct_name';

        $data['leftJoin']['party_master'] = 'party_master.id = tax_master.acc_id';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "tax_master.name";
        $data['searchCol'][] = "(CASE WHEN tax_master.tax_type =1 THEN 'Purchase' WHEN tax_master.tax_type =2 THEN 'Sales' ELSE '' END)";
        $data['searchCol'][] = "(CASE WHEN tax_master.calculation_type = 0 THEN 'Total Qty' WHEN tax_master.calculation_type = 1 THEN 'Basic Amount' WHEN tax_master.calculation_type = 2 THEN 'Net Amount' ELSE ''  END)";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "(CASE WHEN tax_master.is_active =1 THEN 'Active' WHEN tax_master.is_active =0 THEN 'Inactive' ELSE '' END)";
        $data['searchCol'][] = "(CASE WHEN tax_master.add_or_deduct =1 THEN 'Add' WHEN tax_master.add_or_deduct =-1 THEN 'Deduct' ELSE '' END)";
       
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getTaxMaster($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->taxMaster;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['name'] = "Tax Name is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;
            
            $result = $this->store($this->taxMaster,$data,'Tax Master');            

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
        $queryData['tableName'] = $this->taxMaster;
        $queryData['where']['name'] = $data['name'];        
        $queryData['where']['tax_type'] = $data['tax_type'];        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->taxMaster,['id'=>$id],'Tax Master');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
	
    public function getActiveTaxList($type = ""){
        $queryData = array();
        $queryData['tableName'] = $this->taxMaster;
        $queryData['where']['is_active'] = 1;
        if(!empty($type))
            $queryData['where']['tax_type'] = $type;
        
        $result = $this->rows($queryData);
        return $result;
    }

    public function getTaxList($data){
        $queryData = array();
        $queryData['tableName'] = $this->taxMaster;
        
        if(!empty($data['tax_ids']))
            $queryData['where_in']['id'] = $data['tax_ids'];
        if(!empty($data['type']))
            $queryData['where']['tax_type'] = $data['type'];
        
        $result = $this->rows($queryData);
        return $result;
    }
}
?>