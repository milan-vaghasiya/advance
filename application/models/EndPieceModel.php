<?php
class EndPieceModel extends MasterModel{

    public function getDTRows($data) {
        $data['tableName'] = 'end_piece_return';
        $data['select'] = "end_piece_return.*,prc_master.prc_number,item_master.item_code,item_master.item_name";
        $data['select'] .= ',IFNULL(reviewed_stock.review_qty,0) AS review_qty';
        $data['leftJoin']['prc_master'] = "prc_master.id  = end_piece_return.prc_id";
        $data['leftJoin']['item_master'] = "item_master.id  = end_piece_return.item_id";
        $data['leftJoin']['(SELECT SUM(stock_trans.qty) as review_qty,
                                main_ref_id,stock_trans.item_id
                                FROM stock_trans
                                WHERE stock_trans.is_delete=0
                                    AND stock_trans.trans_type = "EPS"
                                GROUP BY stock_trans.main_ref_id
                           ) AS reviewed_stock'] = 'reviewed_stock.main_ref_id = end_piece_return.id';
        if($data['status'] == 1){
            $data['having'][] = 'end_piece_return.qty > review_qty';
        }else{
            $data['having'][] = 'end_piece_return.qty = review_qty';
        }
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(end_piece_return.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "prc_master.prc_number";
        $data['searchCol'][] = "CONCAT(item_master.item_code,' ',item_master.item_name)";
        $data['searchCol'][] = "end_piece_return.end_pcs";
        $data['searchCol'][] = "end_piece_return.qty";
        $data['searchCol'][] = "IFNULL(reviewed_stock.review_qty,0)";
        $data['searchCol'][] = "end_piece_return.batch_no";
        $data['searchCol'][] = "end_piece_return.remark";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function endPcsReturnData($param = []){
        $data['tableName'] = 'end_piece_return';
        $data['select'] = "end_piece_return.*,prc_master.prc_number,item_master.item_code,item_master.item_name";
        $data['leftJoin']['prc_master'] = "prc_master.id  = end_piece_return.prc_id";
        $data['leftJoin']['item_master'] = "item_master.id  = end_piece_return.item_id";

        if(!empty($param['stock_data'])){
            $data['select'] .= ',IFNULL(reviewed_stock.review_qty,0) AS review_qty';
            $data['leftJoin']['(SELECT SUM(stock_trans.qty) as review_qty,
                                    main_ref_id,stock_trans.item_id
                                    FROM stock_trans
                                    WHERE stock_trans.is_delete=0
                                        AND stock_trans.trans_type = "EPS"
                                        AND main_ref_id ="'.$param['id'].'"
                                    GROUP BY stock_trans.main_ref_id
                               ) AS reviewed_stock'] = 'reviewed_stock.main_ref_id = end_piece_return.id';
        }
        if(!empty($param['id'])){ $data['where']['end_piece_return.id'] = $param['id']; }
        if(!empty($param['prc_id'])){ $data['where']['end_piece_return.prc_id'] = $param['prc_id']; }
        if(!empty($param['item_id'])){ $data['where']['end_piece_return.item_id'] = $param['item_id']; }

        if(!empty($param['single_row'])){
            $result = $this->row($data);
        }else{
            $result = $this->rows($data);
        }
        return $result;
    }

    public function saveStock($data){
		try {
			$this->db->trans_begin();

                if($data['return_type'] == 2){
                    $data['location_id'] = $this->SCRAP_STORE->id;
                    $itemData = $this->item->getItem(['id'=>$data['item_id']]);
                    if(empty($itemData->scrap_group)){
                        return ['status'=>0,'message'=>'Scrap Group required.'];
                    }else{
                        $data['item_id'] = $itemData->scrap_group;
                    }
                }
                $stockData = [
                    'id'=>'',
                    'trans_type'=>"EPS",
                    'trans_date'=>date("Y-m-d"),
                    'ref_no'=>$data['prc_number'],
                    'main_ref_id'=>$data['return_id'],
                    'child_ref_id'=>$data['prc_id'],
                    'location_id '=>$data['location_id'],
                    'batch_no'=>$data['batch_no'],
                    'item_id'=>$data['item_id'],
                    'p_or_m'=>1,
                    'qty'=>$data['qty'],
                    'remark'=>$data['remark'],
                    'created_by'=>$this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $result = $this->store("stock_trans",$stockData);
			

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
		
	}
    public function delete($id){
		try {
			$this->db->trans_begin();
			$returnData = $this->itemStock->getStockTrans(['id'=>$id]);
			$stock = $this->itemStock->getItemStockBatchWise(['location_id'=>$returnData->location_id,'batch_no'=>$returnData->batch_no,'item_id'=> $returnData->item_id,'single_row'=>1]);
			if($returnData->qty > $stock->qty){ 
				return ['status'=>0,'message'=>'You can not delete this record']; 
			}

			$result = $this->remove('stock_trans',['id'=>$id]);

			if($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		}catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}
}
?> 