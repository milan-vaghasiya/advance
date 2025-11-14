<?php
class ItemCategoryModel extends MasterModel{
    private $itemCategory = "item_category";

    public function getDTRows($data){
        $data['tableName'] = $this->itemCategory;
        $data['select'] = "item_category.*,IFNULL(pc.category_name,'NA') as parent_category_name,(CASE WHEN item_category.final_category = 1 THEN 'YES' ELSE 'NO' END) as is_final_text,(CASE WHEN item_category.batch_stock = 1 THEN 'Batch Wise' WHEN item_category.batch_stock = 2 THEN 'Serial Wise' ELSE 'None' END) as stock_type_text,(CASE WHEN item_category.is_return = 1 THEN 'YES' ELSE 'NO' END) as is_returnable_text,(CASE WHEN item_category.is_inspection = 1 THEN 'YES' ELSE 'NO' END) as is_inspection_text"; //25-11-24

        $data['leftJoin']['item_category as pc'] = 'pc.id = item_category.ref_id';

        if(empty($data['parent_id'])):
            $data['where']['item_category.final_category'] = 0;
            $data['where']['item_category.ref_id'] = 0;
        else:
            $data['where']['item_category.ref_id'] = $data['parent_id'];
        endif;

		$data['order_by']['category_type'] = 'ASC';
        $data['order_by']['category_level'] = 'ASC';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "item_category.category_code";
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "pc.category_name";
        $data['searchCol'][] = "(CASE WHEN item_category.final_category = 1 THEN 'YES' ELSE 'NO' END)";//25-11-24
        $data['searchCol'][] = "(CASE WHEN item_category.is_return = 1 THEN 'YES' ELSE 'NO' END)";
        $data['searchCol'][] = "(CASE WHEN item_category.is_inspection = 1 THEN 'YES' ELSE 'NO' END)";
        $data['searchCol'][] = "item_category.remark";

        
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getCategoryList($data=array()){
        $queryData['tableName'] = $this->itemCategory;  
        
        if(isset($data['final_category'])):
            $queryData['where']['final_category'] = $data['final_category'];
        endif;

        if(isset($data['ref_id'])):
            $queryData['where']['ref_id'] = $data['ref_id'];
        endif;

        if(!empty($data['category_type'])):
            $queryData['where_in']['category_type'] = $data['category_type'];
        endif;

		$queryData['order_by']['category_type'] = 'ASC';
        $queryData['order_by']['category_level'] = 'ASC';
        return $this->rows($queryData);
    }

    //12-04-2024
    public function getCategory($data){
        $queryData['tableName'] = $this->itemCategory;
        if(!empty($data['id'])){
            $queryData['where']['id'] = $data['id'];
        }
        if(!empty($data['category_name'])){
            $queryData['where']['category_name'] = $data['category_name'];
        }
        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            if($this->checkDuplicate($data) > 0):
                $errorMessage['category_name'] = "Category Name is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $result = $this->store($this->itemCategory,$data,'Item Category');

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
        $queryData['tableName'] = $this->itemCategory;
        $queryData['where']['category_name'] = $data['category_name'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $checkData['columnName'] = [];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Item Category is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->itemCategory,['id'=>$id],'Item Category');

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