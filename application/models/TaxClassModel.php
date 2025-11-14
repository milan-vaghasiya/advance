<?php
class TaxClassModel extends MasterModel{
    private $taxClass = "tax_class_master";

    public function getDTRows($data){
        $data['tableName'] = $this->taxClass;
        $data['select'] = 'tax_class_master.*, (CASE WHEN tax_class_master.sp_type =1 THEN "Purchase" WHEN tax_class_master.sp_type =2 THEN "Sales" ELSE "" END) as sp_type_name,
        (CASE WHEN tax_class_master.is_active =1 THEN "Active" WHEN tax_class_master.is_active =0 THEN "Inactive" ELSE "" END) as is_active_name,
        party_master.party_name as sp_acc_name';

        $data['leftJoin']['party_master'] = 'party_master.id = tax_class_master.sp_acc_id';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "tax_class_master.tax_class_name";
        $data['searchCol'][] = "(CASE WHEN tax_class_master.sp_type = 1 THEN 'Purchase' WHEN tax_class_master.sp_type = 2 THEN 'Sales' ELSE '' END)";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "(CASE WHEN tax_class_master.is_active =1 THEN 'Active' WHEN tax_class_master.is_active =0 THEN 'Inactive' ELSE '' END)";
       
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getTaxClass($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->taxClass;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['tax_class_name'] = "Tax Class is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if($data['is_defualt'] == 1):
                $setData = [];
                $setData['tableName'] = $this->taxClass;
                $setData['where']['is_defualt'] = 1;
                $setData['where']['sp_type'] = $data['sp_type'];
                $setData['update']['is_defualt'] = 0;
                $this->setValue($setData);
            endif;

            if(in_array($data['tax_class'],["SALESGSTACC","SALESJOBGSTACC","PURGSTACC","PURJOBGSTACC","PURURDGSTACC"])):
                $data['gst_type'] = 1;
            elseif(in_array($data['tax_class'],["SALESIGSTACC","SALESJOBIGSTACC","EXPORTGSTACC","SEZSTFACC","SEZSGSTACC","DEEMEDEXP","PURIGSTACC","PURJOBIGSTACC","PURURDIGSTACC","IMPORTACC","IMPORTSACC","SEZRACC"])):
                $data['gst_type'] = 2;
            elseif(in_array($data['tax_class'],["SALESTFACC","SALESEXEMPTEDTFACC","SALESNONGST","EXPORTTFACC","PURTFACC","PUREXEMPTEDTFACC","PURNONGST"])):
                $data['gst_type'] = 3;
            endif;
            
            $result = $this->store($this->taxClass,$data,'Tax Class');            

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
        $queryData['tableName'] = $this->taxClass;
        $queryData['where']['tax_class_name'] = $data['tax_class_name'];        
        $queryData['where']['sp_type'] = $data['sp_type'];      
        $queryData['where']['sp_acc_id'] = $data['sp_acc_id'];      

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $checkData['columnName'] = ["tax_class_id"];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The tax class is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->taxClass,['id'=>$id],'Tax Class');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getActiveTaxClass($type = "",$tax_class = ""){
        $queryData = array();
        $queryData['tableName'] = $this->taxClass;
        $queryData['where']['is_active'] = 1;

        if(!empty($type))
            $queryData['where']['sp_type'] = $type;
        if(!empty($tax_class))
            $queryData['where_in']['tax_class'] = $tax_class;
        
        $queryData['order_by']['is_defualt'] = "DESC";
        $result = $this->rows($queryData);
        return $result;
    }
}
?>