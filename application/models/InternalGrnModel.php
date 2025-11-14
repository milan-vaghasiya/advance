<?php
class InternalGrnModel extends masterModel{
    private $grn_master = "grn_master";
    private $grn_trans = "grn_trans";

    public function getDTRows($data){
 
        $data['tableName'] = $this->grn_trans;

        $data['select'] = "grn_master.id,grn_master.trans_number,DATE_FORMAT(grn_master.trans_date,'%d-%m-%Y') as trans_date,party_master.party_name,item_master.item_name,grn_trans.trans_status,grn_trans.qty,grn_trans.id as mir_trans_id,item_master.item_type,grn_trans.heat_no,item_category.category_name,item_category.is_inspection,material_master.material_grade,grn_trans.batch_no,item_master.item_code,item_master.uom,grn_trans.fg_item_id,fgItem.item_code as fg_item_code,fgItem.item_name as fg_item_name,grn_trans.price,grn_trans.item_remark,st.batch_no as stock_batch";

        $data['leftJoin']['grn_master'] = "grn_master.id = grn_trans.grn_id";
        $data['leftJoin']['item_master'] = "item_master.id = grn_trans.item_id";
        $data['leftJoin']['item_master fgItem'] = "fgItem.id = grn_trans.fg_item_id";  
        $data['leftJoin']['item_category'] = "item_category.id  = item_master.category_id";
        $data['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
        $data['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";   
        $data['leftJoin']['(SELECT batch_no,trans_type,child_ref_id FROM stock_trans WHERE is_delete = 0 AND trans_type = "IGR") st'] = "st.child_ref_id = grn_trans.id";

        $data['where']['grn_trans.trans_status'] = $data['trans_status'];
        $data['where']['grn_master.grn_type'] = 3;
        $data['order_by']['grn_master.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "grn_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(grn_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name,' ',material_master.material_grade)";
        $data['searchCol'][] =  "CONCAT('[',fgItem.item_code,'] ',fgItem.item_name)";
        $data['searchCol'][] = "st.batch_no";
		$data['searchCol'][] = "grn_trans.batch_no";
        $data['searchCol'][] = "grn_trans.qty";
        $data['searchCol'][] = "grn_trans.price";
        $data['searchCol'][] = "item_master.uom";
        $data['searchCol'][] = "grn_trans.item_remark";
    
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if (isset($data['order'])) {
			$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
		}

		return $this->pagingRows($data);
    }

    public function save($data){ 
        try {
            $this->db->trans_begin();
            $grnData = $this->gateInward->getBatchWiseItemList(['grnData'=>1,'batch_no'=>$data['batch_no']]);
            
            $grnMasterData = [
                'id' => '',
                'grn_type' => $data['grn_type'],
                'trans_no' => $data['trans_no'],
                'trans_number' => $data['trans_number'],
                'trans_prefix' => $data['trans_prefix'],
                'trans_date' => date("Y-m-d"),
                'party_id' => $data['party_id']
            ];
            $grnMaster = $this->store($this->grn_master, $grnMasterData, 'Internal Grn');

            $grnTransData = [
                'id' => '',
                'grn_id' => $grnMaster['insert_id'],
                'item_id' => $data['to_item'],
                'fg_item_id' => $data['fg_item_id'],
                'heat_no' => $data['heat_no'],
                'qty' => array_sum($data['batch_qty']),
                'item_remark' => $data['item_remark'],
                'price' => $grnData->price
            ];
            $grnTrans = $this->store($this->grn_trans, $grnTransData, 'Internal Grn');
            

            foreach ($data['batch_qty'] as $key => $value) {
                if(!empty($value) && $value > 0) {
                    $stockMinusQuery = [
                        'id' => "",
                        'trans_type' => 'IGR',
                        'ref_no' => $data['trans_number'],
                        'trans_date' => $data['trans_date'],
                        'item_id' => $data['item_id'],
                        'location_id' => $data['location_id'][$key],
                        'batch_no' => $data['batch_no'],
                        'p_or_m' => -1,
                        'qty' => $value,
                        'main_ref_id' => $grnMaster['insert_id'],
                        'child_ref_id' =>$grnTrans['insert_id']
                    ];
                    $stockTrans = $this->store('stock_trans', $stockMinusQuery);
                }
            }

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Internal Grn Save Successfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function delete($data){
        try{
            $this->db->trans_begin();
        
            $result = $this->trash($this->grn_master,['id'=>$data['id']],'Gate Inward');        
            $this->trash($this->grn_trans,['id'=>$data['mir_trans_id']]);

            $this->remove('stock_trans',['trans_type'=>'IGR','main_ref_id' => $data['id'],'child_ref_id' => $data['mir_trans_id']]);

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