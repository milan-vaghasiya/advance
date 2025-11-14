<?php
class PrimaryPackingModel extends MasterModel{

    public function getNetxNo(){
        $data['tableName'] ='primary_packing';
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['primary_packing.trans_date >='] = $this->startYearDate;
		$data['where']['primary_packing.trans_date <='] = $this->endYearDate;
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo; 
    }

    public function getDTRows($data){
        $data['tableName'] ='primary_packing';
        $data['select'] = "primary_packing.*,item_master.item_code,item_master.item_name,(primary_packing.total_box-primary_packing.dispatch_qty) AS pending_dispatch";
        $data['leftJoin']['item_master'] = "primary_packing.item_id = item_master.id";
        
        if($data['status'] == 1){
            $data['having'][] = '(primary_packing.total_box - dispatch_qty) > 0';
        }else{
            $data['having'][] = '(primary_packing.total_box - dispatch_qty) = 0';
        }
        $data['where']['primary_packing.trans_date >='] = $this->startYearDate;
        $data['where']['primary_packing.trans_date <='] = $this->endYearDate;
        
        $data['order_by']['primary_packing.trans_no'] = "DESC";

        $data['searchCol'][] = "primary_packing.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(primary_packing.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "primary_packing.qty_per_box";
        $data['searchCol'][] = "primary_packing.total_box";
        $data['searchCol'][] = "primary_packing.total_qty";
        
		$columns = array('','');
        foreach($data['searchCol'] as $key=>$value):
            $columns[] = $value;
        endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        $result = $this->pagingRows($data);
        return $result;
    }  

    public function save($data){
        try {
            $this->db->trans_begin();

            $batchDetail = $data['batchDetail'];$box_type = $data['box_type'];
            unset($data['batchDetail'],$data['total_box_qty'],$data['box_type']);
            $itemData = $this->item->getItem(['id'=>$data['item_id']]);
            if(empty($data['id'])):
                $data['trans_no'] = $this->getNetxNo();
                $trans_prefix = "PP/".getYearPrefix('SHORT_YEAR').'/';
                $data['trans_number'] = $trans_prefix.$data['trans_no'];
                $data['created_by'] = $this->loginId;
            else:
                /** Remove Stock Transaction **/
                $this->trash('primary_packing_batch',['packing_id'=>$data['id']]);
				$this->remove("stock_trans",['main_ref_id'=>$data['id'],'trans_type '=>'PPK']);
            endif;
            $data['final_packing'] = ($itemData->is_packing == 1)?0:1;
			$result = $this->store('primary_packing',$data);
			$packingId = (empty($data['id']))?$result['insert_id']:$data['id'];

            
            $location_id = $this->FPCK_STORE->id ;//($itemData->is_packing == 1)?$this->RTD_STORE->id:$this->FPCK_STORE->id;
            //Stock Effect
            $total_qty = 0;
            foreach($batchDetail as $batch):
                if(floatval($batch['batch_qty']) > 0):
                    //Effect in Packing Batch Table
                    $packBatchDetail = [
                        'id'=>'',
                        'packing_id'=>$packingId,
                        'item_id'=>$data['item_id'],
                        'batch_no'=>$batch['batch_no'],
                        'qty'=>$batch['batch_qty'],
                    ];
                    $packingBatch = $this->store('primary_packing_batch',$packBatchDetail);

                    $packStock = [
                        'id'=>"",
                        'trans_type' =>'PPK',
                        'trans_date' =>$data['trans_date'],  
                        'item_id' =>$data['item_id'], 
                        'location_id' =>$batch['location_id'],
                        'batch_no' =>$batch['batch_no'], 
                        'p_or_m' =>-1, 
                        'qty' =>$batch['batch_qty'],
                        'ref_no' =>$data['trans_number'],
                        'main_ref_id' =>$packingId, 
                        'child_ref_id' =>$packingBatch['insert_id'], 
                    ];
                    $this->store('stock_trans',$packStock);

                    $rtdStock = [
                        'id'=>"",
                        'trans_type' =>'PPK',
                        'trans_date' =>$data['trans_date'],  
                        'item_id' =>$data['item_id'], 
                        'location_id' =>$location_id,
                        'batch_no' =>$batch['batch_no'], 
                        'p_or_m' =>1, 
                        'qty' =>$batch['batch_qty'],
                        'ref_no' =>$data['trans_number'],
                        'main_ref_id' =>$packingId,
                        'child_ref_id' =>$packingBatch['insert_id'], 
                    ];
                    $this->store('stock_trans',$rtdStock);
                    $total_qty += $batch['batch_qty'];
                endif;
            endforeach;
            /*** Stock Minus From Packing Standard */
            $standardData = $this->item->getProductKitData(['item_id'=>$data['item_id'], 'item_type'=>9,'packing_type'=>1,'group_name'=>$data['pack_standard']]);
            if(!empty($standardData)){
                foreach($standardData AS $row){
                    $stockData = $this->itemStock->getItemStockBatchWise(['item_id'=>$row->ref_item_id,'location_id'=>$this->PACKING_STORE->id,'single_row'=>1]);
                    $requiredQty = ceil($total_qty/$row->qty);
                    if(!empty($stockData->qty) && $stockData->qty >= $requiredQty){

                        $boxStock = [
                            'id'=>'',
                            'trans_type' =>'PPK',
                            'trans_date' =>$data['trans_date'],  
                            'item_id' =>$row->ref_item_id, 
                            'location_id' =>$this->PACKING_STORE->id,
                            'batch_no' =>'General Batch', 
                            'p_or_m' =>-1, 
                            'qty' => $requiredQty,
                            'ref_no' =>$data['trans_number'],
                            'main_ref_id' =>$packingId, 
                        ];
                        $this->store('stock_trans',$boxStock);
                    }else{
                        return ['status'=>0,'message'=>'Stock not available - '.$row->item_name];
                    }
                }
            }else{
                return ['status'=>0,'message'=>'Packing Standared is not defined'];
            }
			
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getPackingData($param = []){
        $queryData['tableName'] = 'primary_packing';
        $queryData['select'] = "primary_packing.*,item_master.item_code,item_master.item_name";   
        $queryData['leftJoin']['item_master'] = "primary_packing.item_id = item_master.id";
        if(!empty($param['id'])){ $queryData['where']['primary_packing.id'] = $param['id']; }
        $result = $this->row($queryData);
        return $result;
    }

    public function getBatchDetail($param = []){
        $queryData = [];
        $queryData['tableName'] = 'primary_packing_batch';
        $queryData['select'] = "primary_packing_batch.*";
        $queryData['where']['packing_id'] = $param['packing_id'];
        $batchData = $this->rows($queryData);
        return $batchData;
    }

    public function getBoxStockData($data){
        $queryData['tableName'] = 'primary_packing';
        $queryData['select'] = "primary_packing.*,(primary_packing.total_box - dispatch_qty) AS box_stock,packBatch.batch_no";
        $queryData['leftJoin']['( SELECT GROUP_CONCAT(batch_no) AS batch_no,packing_id
                                        FROM primary_packing_batch
                                        WHERE primary_packing_batch.is_delete = 0
                                        GROUP BY primary_packing_batch.packing_id
                                ) packBatch'] = 'primary_packing.id = packBatch.packing_id';
        if(!empty($data['item_id'])){ $queryData['where']['primary_packing.item_id'] = $data['item_id']; }
        if(!empty($data['id'])){ $queryData['where']['primary_packing.id'] = $data['id']; }
        if(isset($data['final_packing'])){ $queryData['where']['primary_packing.final_packing'] = $data['final_packing']; }
        if(!empty($data['stockRequired'])){
            $queryData['customWhere'][] = "(primary_packing.total_box - dispatch_qty) > 0";
        }
        if(!empty($data['customHaving'])){
            $queryData['having'][] = $data['customHaving'];
        }
        if(!empty($data['single_row'])){
            $batchData = $this->row($queryData);
        }else{
            $batchData = $this->rows($queryData);
        }
        
        return $batchData;
    }

    public function delete($id){
        try {
            $this->db->trans_begin();
            $this->trash('primary_packing_batch',['packing_id'=>$id]);
            $this->remove("stock_trans",['main_ref_id'=>$id,'trans_type '=>'PPK']);
            $result = $this->trash('primary_packing',['id'=>$id]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
}
?>