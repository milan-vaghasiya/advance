<?php
class PackingModel extends MasterModel{

    public function getNetxNo(){
        $data['tableName'] ='packing_master';
        $data['select'] = "MAX(trans_no) as trans_no";
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo; 
    }

    public function getDTRows($data){
        $data['tableName'] ='packing_master';
        $data['select'] = "packing_master.*,item_master.item_code,item_master.item_name,IFNULL(stockTrans.stock_qty,0) AS pending_dispatch";
        $data['leftJoin']['item_master'] = "packing_master.item_id = item_master.id";
        $data['leftJoin']['(SELECT 
			SUM(qty) AS stock_qty,batch_no,item_id 
		FROM 
			stock_trans 
		WHERE is_delete = 0 AND location_id = '.$this->RTD_STORE->id.' 
		GROUP BY item_id,batch_no ) AS stockTrans'] = 'stockTrans.item_id = packing_master.item_id AND stockTrans.batch_no = packing_master.trans_number';

        if($data['status'] == 1){
            $data['having'][] = 'packing_master.total_qty >= pending_dispatch';
        }else{
            $data['having'][] = 'packing_master.total_qty < pending_dispatch ';
        }
        $data['where']['packing_master.trans_date >='] = $this->startYearDate;
        $data['where']['packing_master.trans_date <='] = $this->endYearDate;
        
        $data['order_by']['packing_master.trans_no'] = "DESC";

        $data['searchCol'][] = "packing_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(packing_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "packing_master.qty_per_box";
        $data['searchCol'][] = "packing_master.total_box";
        $data['searchCol'][] = "packing_master.total_qty";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "packing_master.remark";
        
		$columns = array('','');
        foreach($data['searchCol'] as $key=>$value):
            $columns[] = $value;
        endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        $result = $this->pagingRows($data);
        // print_r($this->db->last_query());exit;
        return $result;
    }

	public function getPackingStockDTRows($data){
        $data['tableName'] = "stock_trans";
        $data['select'] = "stock_trans.id,stock_trans.trans_date,stock_trans.item_id, item_master.item_code, item_master.item_name, SUM(stock_trans.qty * stock_trans.p_or_m) as stock_qty,stock_trans.opt_qty, stock_trans.batch_no,  stock_trans.location_id, stock_trans.remark,stock_trans.ref_batch,item_master.uom";
		$data['leftJoin']['item_master'] = "stock_trans.item_id = item_master.id";
		
		if(!empty($data['item_type'])){ $data['where']['item_master.item_type'] = $data['item_type']; }
		$data['where']['stock_trans.location_id'] = $this->PACKING_STORE->id;
		
		$data['having'][] = "SUM(stock_trans.qty * stock_trans.p_or_m) > 0";
		$data['group_by'][] = "stock_trans.item_id,stock_trans.location_id,stock_trans.batch_no";
		$data['order_by']['stock_trans.item_id'] = "ASC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "stock_trans.batch_no";
        $data['searchCol'][] = "stock_trans.ref_batch";
        $data['searchCol'][] = "";
        
		$columns = array('','');
        foreach($data['searchCol'] as $key=>$value):
            $columns[] = $value;
        endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
	
    public function save($data){
        try {
            $this->db->trans_begin();

            $batchDetail = $data['batchDetail'];
            unset($data['batchDetail'],$data['total_box_qty']);

            if(empty($data['id'])):
                $data['trans_no'] = $this->getNetxNo();
                $trans_prefix = "DP-".n2m(date('m'))."-";
                $data['trans_number'] = $trans_prefix.$data['trans_no'];
                $data['created_by'] = $this->loginId;
            else:
                /** Remove Stock Transaction **/
				$this->remove("stock_trans",['main_ref_id'=>$data['id'],'trans_type '=>'PCK']);
            endif;
            $data['batch_no'] = implode(",", array_column($batchDetail, "batch_no"));
            $data['batch_qty'] = implode(",", array_column($batchDetail, "batch_qty"));
			$result = $this->store('packing_master',$data);
			$transId = (empty($data['id']))?$result['insert_id']:$data['id'];

			/* Box Stock Deduction */
			/*if(!empty($data['box_item_id']))
			{
				$boxStock = [
					'id'=>'',
					'trans_type' =>'PCK',
					'trans_date' =>$data['trans_date'],  
					'item_id' =>$data['box_item_id'], 
					'location_id' =>$this->PACKING_STORE->id,
					'batch_no' =>'GENERAL', 
					'p_or_m' =>-1, 
					'qty' =>$data['total_box'],
					'ref_no' =>$data['trans_number'],
					'main_ref_id' =>$transId, 
				];
				$this->store('stock_trans',$boxStock);
            }*/
			
            /** Finish Stock Minus from Packing Area*/
            $total_qty = 0;
            foreach($batchDetail as $batch):
                if(floatval($batch['batch_qty']) > 0):
                    $packStock = [
                        'id'=>"",
                        'trans_type' =>'PCK',
                        'trans_date' =>$data['trans_date'],  
                        'item_id' =>$data['item_id'], 
                        'location_id' =>$batch['location_id'],
                        'batch_no' =>$batch['batch_no'], 
                        'p_or_m' =>-1, 
                        'qty' =>$batch['batch_qty'],
                        'ref_no' =>$data['trans_number'],
                        'main_ref_id' =>$transId, 
                        'remark' =>$batch['remark'], 
                    ];
                    $this->store('stock_trans',$packStock);
                    $total_qty += $batch['batch_qty'];
                endif;
            endforeach;
			
            /** Finish Stock Plus in ready to dispatch area box wise */
            $rtdStock = [
                'id'=>"",
                'trans_type' =>'PCK',
                'trans_date' =>$data['trans_date'],  
                'item_id' =>$data['item_id'], 
                'location_id' =>$this->RTD_STORE->id,
                'batch_no' =>$data['trans_number'], 
                'p_or_m' =>1, 
                'qty' =>$total_qty,
                'opt_qty'=>$data['qty_per_box'],
                'ref_no' =>$data['trans_number'],
                'remark'=> $data['batch_no'],
                'main_ref_id' =>$transId, 
            ];
            $this->store('stock_trans',$rtdStock);
           
            
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function delete($id){
        try {
            $this->db->trans_begin();
            $this->remove("stock_trans",['main_ref_id'=>$id,'trans_type '=>'PCK']);
            $result = $this->trash("packing_master",['id'=>$id]);
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
        $queryData['tableName'] = 'packing_master';
        $queryData['select'] = "packing_master.*,item_master.item_code,item_master.item_name";   
        $queryData['leftJoin']['item_master'] = "packing_master.item_id = item_master.id";
        if(!empty($param['id'])){ $queryData['where']['packing_master.id'] = $param['id']; }
        $result = $this->row($queryData);
        return $result;
    }

    public function getBatchDetail($param = []){
        $queryData = [];
        $queryData['tableName'] = 'stock_trans';
        $queryData['select'] = "batch_no,location_id,qty as batch_qty, remark";
        $queryData['where']['trans_type'] = 'PCK';
        $queryData['where']['main_ref_id'] = $param['main_ref_id'];
        $queryData['where']['item_id'] = $param['item_id'];
        $queryData['where']['p_or_m'] = -1;
        $batchData = $this->rows($queryData);
        return $batchData;
    }
    
}
?>