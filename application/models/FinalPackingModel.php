<?php
class FinalPackingModel extends MasterModel{

    public function getNetxNo(){
        $data['tableName'] ='final_packing_master';
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['final_packing_master.trans_date >='] = $this->startYearDate;
		$data['where']['final_packing_master.trans_date <='] = $this->endYearDate;
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo; 
    }

    public function getDTRows($data){
        $data['tableName'] ='final_packing_trans';
        $data['select'] = "final_packing_trans.*,final_packing_master.trans_number,final_packing_master.trans_date,final_packing_master.status,item_master.item_name,so_master.trans_number AS so_number,party_master.party_name,IFNULL(st.stock_qty,0) as stock_qty,trans_main.trans_number AS inv_number";
        $data['leftJoin']['final_packing_master'] = "final_packing_trans.packing_id = final_packing_master.id";
        $data['leftJoin']['party_master'] = "party_master.id = final_packing_master.party_id";
        $data['leftJoin']['item_master'] = "item_master.id = final_packing_trans.item_id";
        $data['leftJoin']['so_trans'] = "so_trans.id = final_packing_trans.so_trans_id";
        $data['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id";
        $data['leftJoin']['trans_child'] = "trans_child.id = final_packing_trans.inv_trans_id";
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        
        $data['leftJoin']['(SELECT SUM(qty * p_or_m) as stock_qty,trans_type,child_ref_id,item_id FROM stock_trans WHERE is_delete = 0 AND trans_type IN("FPK") AND p_or_m = 1 GROUP BY child_ref_id) as st'] = "st.child_ref_id = final_packing_trans.id AND st.item_id = final_packing_trans.item_id";
        
        if(in_array($data['status'], [1,2])){
            $data['where']['final_packing_master.status'] = $data['status'];
            if($data['status'] == 1){
                $data['having'][] = "total_qty != stock_qty";
            }elseif($data['status'] == 2){
                $data['where']['final_packing_master.trans_date >='] = $this->startYearDate;
                $data['where']['final_packing_master.trans_date <='] = $this->endYearDate;
            }
        }elseif($data['status'] == 3){
            $data['where']['final_packing_master.status'] = 1;
            $data['having'][] = "total_qty = stock_qty";
        }
        
        $data['order_by']['final_packing_master.trans_number'] = "DESC";

		$data['searchCol'][] = ""; 
        $data['searchCol'][] = "";
        $data['searchCol'][] = "final_packing_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(final_packing_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "so_master.trans_number";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "final_packing_trans.total_box";
        $data['searchCol'][] = "final_packing_trans.total_qty";
        $data['searchCol'][] = "trans_main.trans_number";
        
		$columns = array('','');
        foreach($data['searchCol'] as $key=>$value):
            $columns[] = $value;
        endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        $result = $this->pagingRows($data);
        return $result;
    }  

	public function save($data){
        try{
            $this->db->trans_begin();

            if(empty($data['id'])){
                $data['trans_no'] = $this->finalPacking->getNetxNo();
                $data['trans_number'] = "FP/".getYearPrefix('SHORT_YEAR').'/'.$data['trans_no'];
            }else{
                $this->trash("final_packing_trans",['packing_id'=>$data['id']]);
            }

            $itemData = $data['itemData']; unset($data['itemData']);

            $result = $this->store("final_packing_master",$data);
            $packing_id = $result['id'];

            foreach($itemData AS  $row){
                $row['packing_id'] = $packing_id;
                $row['is_delete'] = 0;
                $itemTrans = $this->store('final_packing_trans',$row);       
            }            
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
	
    public function getPackingData($data = []){
        $queryData['tableName'] = "final_packing_master";
        $queryData['select'] = "final_packing_master.*,employee_master.emp_name As prepared_by,transport_master.transport_name,party_master.party_name,party_master.party_address,party_master.party_mobile,transport_master.transport_id as transporter_gst_no";
        $queryData['leftJoin']['party_master'] = "party_master.id = final_packing_master.party_id";
        $queryData['leftJoin']['transport_master'] = 'transport_master.id = final_packing_master.transport_id';
        $queryData['leftJoin']['employee_master'] = 'employee_master.id = final_packing_master.created_by';
        if(!empty($data['id'])){ $queryData['where']['final_packing_master.id'] = $data['id']; }
        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }

    public function getPackingItemDetail($data = [] ){
        $queryData['tableName'] ='final_packing_trans';
        $queryData['select'] = "final_packing_trans.*,item_master.item_name,item_master.item_code,item_master.wt_pcs,so_master.trans_number AS so_number,so_master.doc_no,so_master.doc_date,material_master.material_grade,so_master.id AS so_id,IFNULL(st.stock_qty,0) as stock_qty, trans_child.price as inv_price";
        $queryData['select'] .= ',item_master.gst_per,so_trans.price,item_master.hsn_code,item_master.uom';
        $queryData['leftJoin']['item_master'] = "item_master.id = final_packing_trans.item_id";
        $queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
        $queryData['leftJoin']['so_trans'] = "so_trans.id = final_packing_trans.so_trans_id";
        $queryData['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id";
        $queryData['leftJoin']['trans_child'] = "trans_child.id = final_packing_trans.inv_trans_id";
		$queryData['leftJoin']['(SELECT SUM(qty * p_or_m) as stock_qty,trans_type,child_ref_id,item_id FROM stock_trans WHERE is_delete = 0 AND trans_type IN("FPK") AND p_or_m = 1 GROUP BY child_ref_id) as st'] = "st.child_ref_id = final_packing_trans.id AND st.item_id = final_packing_trans.item_id";
        
        if(!empty($data['packing_id'])){ $queryData['where']['final_packing_trans.packing_id'] = $data['packing_id']; }
        if(!empty($data['id'])){ $queryData['where']['final_packing_trans.id'] = $data['id']; }
        if(!empty($data['ids'])){ $queryData['where_in']['final_packing_trans.id'] = $data['ids']; }
        if(!empty($data['order_by_package'])){
            $queryData['order_by']['final_packing_trans.package_no'] = 'ASC';
        }
        if(!empty($data['single_row'])){
            $result =  $this->row($queryData);
        }else{
            $result = $this->rows($queryData);
        }
        if(!empty($data['batchDetail'])){
            if(empty($data['single_row'])){
                foreach($result as &$row){
                    $queryData = [];
                    $queryData['tableName'] = 'final_packing_box';
                    $queryData['select'] = "batch_no,primary_pack_id,qty as batch_qty,box_qty";
                    $queryData['where']['packing_id'] = $row->packing_id;
                    $queryData['where']['pack_trans_id'] = $row->id;
                    $batchData = $this->rows($queryData);
                    $row->batch_detail = json_encode($batchData);
                }  
            }else{
                $queryData = [];
                $queryData['tableName'] = 'final_packing_box';
                $queryData['select'] = "batch_no,primary_pack_id,qty as batch_qty,box_qty";
                $queryData['where']['packing_id'] = $result->packing_id;
                $queryData['where']['pack_trans_id'] = $result->id;
                $batchData = $this->rows($queryData);
                $result->batch_detail = json_encode($batchData);
            }
            
        }
        return $result ;
    }

    public function getFinalPackingBoxDetail($data = []){
        $queryData['tableName'] ='final_packing_box';
        $queryData['select'] = "final_packing_box.*";
        if(!empty($data['primaryPackBatch'])){
            $queryData['select'] .= ',primary_packing_batch.qty AS batch_qty,(CASE WHEN final_packing_trans.packing_type = 1 THEN prc_master.batch_no ELSE dicrtPRC.batch_no END)AS prd_batch,primary_packing_batch.batch_no AS primary_batch';
            $queryData['leftJoin']['primary_packing_batch'] = "primary_packing_batch.packing_id = final_packing_box.primary_pack_id AND primary_packing_batch.is_delete = 0";
            $queryData['leftJoin']['prc_master'] = "primary_packing_batch.batch_no = prc_master.prc_number";
            $queryData['leftJoin']['prc_master dicrtPRC'] = "dicrtPRC.prc_number = final_packing_box.batch_no";
            $queryData['leftJoin']['final_packing_trans'] = "final_packing_trans.id = final_packing_box.pack_trans_id";
        }
       
        if(!empty($data['packing_id'])){ $queryData['where']['final_packing_box.packing_id'] = $data['packing_id']; }
        if(!empty($data['pack_trans_id'])){ $queryData['where']['final_packing_box.pack_trans_id'] = $data['pack_trans_id']; }

        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }

    public function delete($id){
        try {
            $this->db->trans_begin();
            $boxData = $this->getFinalPackingBoxDetail(['packing_id'=>$id]);
            foreach($boxData AS $row){
                $setData = array();
                $setData['tableName'] = 'primary_packing';
                $setData['where']['id'] = $row->primary_pack_id;
                $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->box_qty;
                $this->setValue($setData);

                $this->trash('final_packing_box',['id'=>$row->id]);
            }
            $this->trash("final_packing_trans",['packing_id'=>$id]);
            $this->remove("stock_trans",[ 'trans_type' => 'FPK','main_ref_id' => $id]);
            $result = $this->trash('final_packing_master',['id'=>$id]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
	
	public function saveBookQty($data){
        try{
            $this->db->trans_begin();

            $batchDetail = $data['batchDetail'];
            $this->edit("final_packing_trans", ['id'=>$data['id']], ['packing_type'=>$data['packing_type']]);
            //IF Packing Type Primary + Final Packing - Reduce Box Stock
            if($data['packing_type'] == 1){
                foreach($batchDetail as $batch):
					if(!empty($batch['box_qty'])):
					
                    $primaryData = $this->primaryPacking->getBatchDetail(['packing_id'=>$batch['primary_pack_id']]);
					
                    $packBatchData = [
                        'id'=>'',
                        'packing_id'=>$data['packing_id'],
                        'pack_trans_id'=>$data['id'],
                        'primary_pack_id'=>$batch['primary_pack_id'],
                        'box_qty'=>$batch['box_qty'],
                        'qty'=>$batch['batch_qty'],
                    ];
                    $this->store("final_packing_box",$packBatchData);

                    $this->edit("final_packing_trans", ['id'=>$data['id']], ['total_box'=>$batch['box_qty']]);

					
                    $setData = array();
                    $setData['tableName'] = 'primary_packing';
                    $setData['where']['id'] = $batch['primary_pack_id'];
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$batch['box_qty'];
                    $this->setValue($setData);
					

                    foreach($primaryData AS $pack):
                        $qty = (count($primaryData) > 1)?$pack->qty:$batch['batch_qty'];
                        $stockMinusData = [
                                        'id' => "",
                                        'trans_type' => 'FPK',
                                        'trans_date' => date('Y-m-d'),
                                        'item_id' => $data['item_id'],
                                        'location_id' => $this->FPCK_STORE->id,
                                        'batch_no' => $pack->batch_no,
                                        'p_or_m' => -1,
                                        'qty' => $qty,
                                        'ref_no' => $data['trans_number'],
                                        'main_ref_id' => $data['packing_id'],
                                        'child_ref_id' => $data['id'],
                                    ];
                        $this->store('stock_trans',$stockMinusData);

                        $stockPlusData = [
                            'id' => "",
                            'trans_type' => 'FPK',
                            'trans_date' => date('Y-m-d'),
                            'item_id' => $data['item_id'],
                            'location_id' => $this->RTD_FPCK_STORE->id,
                            'batch_no' => $pack->batch_no,
                            'p_or_m' => 1,
                            'qty' => $qty,
                            'ref_no' => $data['trans_number'],
                            'main_ref_id' => $data['packing_id'],
                            'child_ref_id' => $data['id'],
                            ];
                        $this->store('stock_trans',$stockPlusData);
                    endforeach;
					endif;
                endforeach;
            }elseif($data['packing_type'] == 2){
                //IF Packing Type Only Final Packing - Reduce Nos Stock
                foreach($batchDetail as $batch):
                    if(floatval($batch['batch_qty']) > 0):
                        $packBatchData = [
                            'id'=>'',
                            'packing_id'=>$data['packing_id'],
                            'pack_trans_id'=>$data['id'],
                            'batch_no'=>$batch['batch_no'],
                            'qty'=>$batch['batch_qty'],
                        ];
                        $this->store("final_packing_box",$packBatchData);
                    
                        $stockMinusData = [
                            'id'=>"",
                            'trans_type' =>'FPK',
                            'trans_date' =>date('Y-m-d'),  
                            'item_id' =>$data['item_id'], 
                            'location_id' => $this->PACKING_STORE->id,
                            'batch_no' =>$batch['batch_no'], 
                            'p_or_m' =>-1, 
                            'qty' =>$batch['batch_qty'],
                            'ref_no' =>$data['trans_number'],
                            'main_ref_id' => $data['packing_id'], 
                            'child_ref_id' =>$data['id'], 
                        ];
                        $this->store('stock_trans',$stockMinusData);        
                    
                        $stockPlusData = [
                            'id'=>"",
                            'trans_type' =>'FPK',
                            'trans_date' =>date('Y-m-d'),  
                            'item_id' =>$data['item_id'], 
                            'location_id' =>$this->RTD_FPCK_STORE->id,
                            'batch_no' =>$batch['batch_no'], 
                            'p_or_m' =>1, 
                            'qty' =>$batch['batch_qty'],
                            'ref_no' =>$data['trans_number'],
                            'main_ref_id' => $data['packing_id'],
                            'child_ref_id' => $data['id'], 
                        ];
                        $this->store('stock_trans',$stockPlusData);
                    endif;
                endforeach;
            }
            
            $packData = $this->getPackingItemDetail(['id'=>$data['id'], 'single_row'=>1]);
            $packingDetail = $packData->packing_detail; 
            $packingDetail = json_decode($packingDetail,true);

            $boxStandard = []; 
            if (!empty($packingDetail)) {
                foreach ($packingDetail as $packing) {
                    $pack_mt_id = $packing['pack_mt_id'];

                    if (isset($boxStandard[$pack_mt_id])) {
                        $boxStandard[$pack_mt_id]['std_qty'] += $packing['std_qty'];
                    } else {
                        $boxStandard[$pack_mt_id] = [
                            'pack_mt_id' => $pack_mt_id,
                            'pack_wt' => $packing['pack_wt'],
                            'std_qty' => $packing['std_qty'],
                            'item_name' => $packing['item_name'],
                        ];
                    }
                }
            }

            // Check if already box minus or not
            $issuedBoxData = $this->itemStock->getStockTrans(['main_ref_id'=>$data['packing_id'],'ref_batch' =>$packData->package_no,'trans_type' =>'FPK','remark' =>'BOX','single_row'=>1]);
           
            if(empty($issuedBoxData)){
                // Final Packing Standard wise bom consumption
                if(!empty($boxStandard)){
                    foreach($boxStandard AS $key=>$bx){
                        $stockData = $this->itemStock->getItemStockBatchWise(['item_id'=>$bx['pack_mt_id'],'location_id'=>$this->PACKING_STORE->id,'single_row'=>1]);
                        $requiredQty = $bx['std_qty'];
                        if(!empty($stockData->qty) && $stockData->qty >= $requiredQty){
                            $boxStock = [
                                'id'=>'',
                                'trans_type' =>'FPK',
                                'trans_date' =>date('Y-m-d'),  
                                'item_id' =>$bx['pack_mt_id'], 
                                'location_id' =>$this->PACKING_STORE->id,
                                'batch_no' =>'General Batch', 
                                'p_or_m' =>-1, 
                                'qty' => $requiredQty,
                                'ref_no' =>$data['trans_number'],
                                'main_ref_id' =>$data['packing_id'], 
                                'child_ref_id' =>$data['id'], 
                                'ref_batch' =>$packData->package_no, 
                                'remark' =>'BOX'
                            ];
                            $this->store('stock_trans',$boxStock);
                        }else{
                            return ['status'=>0,'message'=>'Stock not available - '.$bx['item_name']];
                        }
                    }
                }
            }
            $result = ['status'=>1, 'message'=>'Book Qty Saved Successfully.'];
            
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