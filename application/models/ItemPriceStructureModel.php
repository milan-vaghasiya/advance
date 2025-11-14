<?php
class ItemPriceStructureModel extends MasterModel{
    private $itemPrice = "item_price_structure";

    public function getDTRows($data){
        $data['tableName'] = $this->itemPrice;

        $data['select'] = "item_price_structure.*,item_master.item_name,item_category.category_name";

        $data['leftJoin']['item_master'] = "item_price_structure.item_id = item_master.id";
        $data['leftJoin']['item_category'] = "item_master.category_id = item_category.id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "item_price_structure.structure_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "item_price_structure.gst_per";
        $data['searchCol'][] = "item_price_structure.mrp";
        $data['searchCol'][] = "item_price_structure.price";
        $data['searchCol'][] = "item_price_structure.dealer_mrp";
        $data['searchCol'][] = "item_price_structure.dealer_price";
        $data['searchCol'][] = "item_price_structure.retail_mrp";
        $data['searchCol'][] = "item_price_structure.retail_price";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function getItemPriceStructure($data){
        $queryData = array();
        $queryData['tableName'] = $this->itemPrice;
        $queryData['where']['structure_id'] = $data['structure_id'];
        $result = $this->rows($queryData);
        return $result;
    }

    public function getPriceStructureList($param = []){
        $queryData = array();
        $queryData['tableName'] = $this->itemPrice;
        $queryData['select'] = "structure_id as id, structure_name, is_defualt";
        
        if(!empty($param['is_defualt'])): $queryData['where']['is_defualt'] = $param['is_defualt']; endif;
        $queryData['group_by'][] = "structure_id";
        if(!empty($param['single_row'])): $result = $this->row($queryData);
        else: $result = $this->rows($queryData); endif;
        return $result;
    }

    public function getNextStructureId(){
        $queryData = array();
        $queryData['tableName'] = $this->itemPrice;
        $queryData['select'] = "ifnull((MAX(structure_id) + 1), 1) as structure_id";
        $result = $this->row($queryData);
        return $result->structure_id;
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['structure_name'] = "Structure name is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if(empty($data['structure_id'])):
                $structure_id = $this->getNextStructureId();
            else:
                $structure_id = $data['structure_id'];
            endif;

            if(!empty($data['is_defualt'])):
                $this->edit($this->itemPrice,['is_defualt'=>1],['is_defualt'=>0]);
            endif;

            foreach($data['itemData'] as $row):
                if(floatval($row['mrp']) > 0 || floatval($row['dealer_mrp']) > 0 || floatval($row['retail_mrp']) > 0):

                    $row['structure_name'] = $data['structure_name'];
                    $row['is_defualt'] = $data['is_defualt'];
                    $row['penalty_price'] = $data['penalty_price'];
                    $row['structure_id'] = $structure_id;

                    if(floatval($row['mrp']) > 0 && floatval($row['gst_per']) > 0):
                        $row['price'] = round(($row['mrp'] / (($row['gst_per'] + 100) / 100)),3);
                    endif;

                    if(floatval($row['dealer_mrp']) > 0 && floatval($row['gst_per']) > 0):
                        $row['dealer_price'] = round(($row['dealer_mrp'] / (($row['gst_per'] + 100) / 100)),3);
                    endif;

                    if(floatval($row['retail_mrp']) > 0 && floatval($row['gst_per']) > 0):
                        $row['retail_price'] = round(($row['retail_mrp'] / (($row['gst_per'] + 100) / 100)),3);
                    endif;

                    $result = $this->store($this->itemPrice,$row,'Price Structure');
                endif;
            endforeach;

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
        $queryData['tableName'] = $this->itemPrice;
        $queryData['where']['structure_name'] = $data['structure_name'];
        
        if(!empty($data['structure_id']))
            $queryData['where']['structure_id !='] = $data['structure_id'];
        
        $queryData['group_by'][] = "structure_id";
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $checkData['columnName'] = ["price_structure_id"];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Price Structure is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->itemPrice,['structure_id'=>$id],'Price Structure');

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