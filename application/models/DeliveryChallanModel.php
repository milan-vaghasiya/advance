<?php
class DeliveryChallanModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $transDetails = "trans_details";
    private $stockTrans = "stock_trans";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.id,trans_main.trans_number,trans_main.trans_date,trans_main.party_name,trans_main.remark,trans_main.trans_status";

        $data['where']['trans_main.entry_type'] = $data['entry_type'];

        if($data['status'] == 0):
            $data['where']['trans_main.trans_status'] = 0;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        elseif($data['status'] == 1):
            $data['where']['trans_main.trans_status'] = 1;
            $data['where']['trans_main.trans_date >='] = $this->startYearDate;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        endif;

        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_main.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
            if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_number'] = "DC. No. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if(!empty($data['id'])):
                $dataRow = $this->getDeliveryChallan(['id'=>$data['id'],'itemList'=>1]);
                $this->trash("packing_annexure_detail",['dc_id'=>$data['id']]);

                foreach($dataRow->itemList as $row):
                    if(!empty($row->ref_id)):
                        $setData = array();
                        $setData['tableName'] = 'so_trans';
                        $setData['where']['id'] = $row->ref_id;
                        $setData['set_value']['dispatch_qty'] = 'IF(`dispatch_qty` - '.$row->qty.' >= 0, `dispatch_qty` - '.$row->qty.', 0)';
                        $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 3 END)";
                        $this->setValue($setData);
                    endif;

                    $this->trash($this->transChild,['id'=>$row->id]);
                endforeach;

                if(!empty($dataRow->ref_id)):
                    $oldRefIds = explode(",",$dataRow->ref_id);
                    foreach($oldRefIds as $main_id):
                        $setData = array();
                        $setData['tableName'] = 'so_master';
                        $setData['where']['id'] = $main_id;
                        $setData['update']['trans_status'] = "(SELECT IF(COUNT(id) = SUM(IF(trans_status NOT IN (0, 3), 1, 0)),1,3) AS trans_status FROM so_trans WHERE trans_main_id = '".$main_id."' AND is_delete = 0)";
                        $this->setValue($setData);
                    endforeach;
                endif;                
                
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"DC TERMS"]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"DC MASTER DETAILS"]);
                $this->remove($this->stockTrans,['main_ref_id'=>$data['id'],'trans_type'=>'DLC']);
            endif;
            
            $masterDetails = $data['masterDetails'];
            $itemData = $data['itemData'];
            $termsData = (!empty($data['conditions']))?$data['conditions']:array();

            unset($data['itemData'],$data['masterDetails'],$data['termsData']);		

            $result = $this->store($this->transMain,$data,'Delivery Challan');

            if(!empty($masterDetails)):
                $masterDetails['id'] = "";
                $masterDetails['main_ref_id'] = $result['id'];
                $masterDetails['table_name'] = $this->transMain;
                $masterDetails['description'] = "DC MASTER DETAILS";
                $this->store($this->transDetails,$masterDetails);
            endif;

            if(!empty($termsData)):
                $termsData = [
                    'id' =>"",
                    'table_name' => $this->transMain,
                    'description' => "DC TERMS",
                    'main_ref_id' => $result['id'],
                    't_col_1' => $termsData
                ];
                $this->store($this->transDetails,$termsData);
            endif;

            $i=1;
            foreach($itemData as $row):
                $row['entry_type'] = $data['entry_type'];
                // $row['cm_id'] = $data['cm_id'];
                $row['trans_main_id'] = $result['id'];
                $row['is_delete'] = 0;

                $batchDetail = $row['batch_detail']; unset($row['batch_detail']);
                $itemTrans = $this->store($this->transChild,$row);

                if($row['stock_eff'] == 1):
                    $batchDetail = json_decode($batchDetail,true);
                    foreach($batchDetail as $batch):
                        if(floatval($batch['batch_qty']) > 0):
                            $stockData = [
                                'id' => "",
                                'trans_type' => 'DLC',
                                'trans_date' => $data['trans_date'],
                                'item_id' => $row['item_id'],
                                'location_id' => $batch['location_id'],
                                'batch_no' => $batch['batch_no'],
                                'p_or_m' => -1,
                                'qty' => $batch['batch_qty'],
                                'opt_qty' => $batch['opt_qty'],
                                'ref_no' => $data['trans_number'],
                                'main_ref_id' => $result['id'],
                                'child_ref_id' => $itemTrans['id'],
                                'remark' => $batch['remark']
                            ];
        
                            $this->store($this->stockTrans,$stockData);
                        endif;
                    endforeach;
                endif;
                if(!empty($row['ref_id'])):
                    $setData = array();
                    $setData['tableName'] = 'so_trans';
                    $setData['where']['id'] = $row['ref_id'];
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$row['qty'];
                    $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 3 END)";
                    $this->setValue($setData);
                endif;
            endforeach;
            
            if(!empty($data['ref_id'])):
                $refIds = explode(",",$data['ref_id']);
                foreach($refIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = 'so_master';
                    $setData['where']['id'] = $main_id;
                    $setData['update']['trans_status'] = "(SELECT IF(COUNT(id) = SUM(IF(trans_status NOT IN (0, 3), 1, 0)),1,3) AS trans_status FROM so_trans WHERE trans_main_id = '".$main_id."' AND is_delete = 0)";
                    $this->setValue($setData);
                endforeach;
            endif;

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
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['entry_type'] = $data['entry_type'];
        $queryData['where']['trans_number'] = $data['trans_number'];
        
        $queryData['where']['trans_date >='] = $this->startYearDate;
        $queryData['where']['trans_date <='] = $this->endYearDate;

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getDeliveryChallan($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.*,employee_master.emp_name as created_name, transport_master.transport_id, transport_master.transport_name";
        
        $queryData['select'] .= ",trans_details.i_col_1 as transport_id,trans_details.t_col_3 as ship_address";
        $queryData['leftJoin']['trans_details'] = "trans_details.main_ref_id = trans_main.id AND trans_details.description = 'DC MASTER DETAILS' AND trans_details.table_name = 'trans_main'";    

        $queryData['leftJoin']['employee_master'] = "employee_master.id = trans_main.created_by";
        $queryData['leftJoin']['transport_master'] = "transport_master.id = trans_details.i_col_1";
        
        $queryData['where']['trans_main.id'] = $data['id'];
        $result = $this->row($queryData);

        if(!empty($data['itemList']) && $data['itemList'] == 1):
            $result->itemList = $this->getDeliveryChallanItems($data);
        endif;

        $queryData = array();
        $queryData['tableName'] = $this->transDetails;
        $queryData['select'] = "t_col_1 as condition";
        $queryData['where']['main_ref_id'] = $data['id'];
        $queryData['where']['table_name'] = $this->transMain;
        $queryData['where']['description'] = "DC TERMS";
        $result->termsConditions = $this->row($queryData);

        return $result;
    }

    public function getDeliveryChallanItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*";
        $queryData['where']['trans_child.trans_main_id'] = $data['id'];
        $result = $this->rows($queryData);
        foreach($result as &$row):
            $batchData = [];
            if($row->stock_eff == 1):
                $queryData = [];
                $queryData['tableName'] = $this->stockTrans;
                $queryData['select'] = "batch_no,location_id,qty as batch_qty,opt_qty, remark";
                $queryData['where']['trans_type'] = 'DLC';
                $queryData['where']['main_ref_id'] = $row->trans_main_id;
                $queryData['where']['child_ref_id'] = $row->id;
                $queryData['where']['item_id'] = $row->item_id;
                $batchData = $this->rows($queryData);
            endif;
            $row->batch_detail = json_encode($batchData);
        endforeach;
        return $result;
    }

    public function getDeliveryChallanItem($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*";
        $queryData['where']['trans_child.id'] = $data['id'];
        $result = $this->row($queryData);

        if(!empty($data['batchDetail'])):
            $batchData = [];
            if($result->stock_eff == 1):
                $queryData = [];
                $queryData['tableName'] = $this->stockTrans;
                $queryData['select'] = "batch_no,location_id,qty as batch_qty,opt_qty, remark";
                $queryData['where']['trans_type'] = 'DLC';
                $queryData['where']['main_ref_id'] = $result->trans_main_id;
                $queryData['where']['child_ref_id'] = $result->id;
                $queryData['where']['item_id'] = $result->item_id;
                $batchData = $this->rows($queryData);
            endif;
            $result->batch_detail = json_encode($batchData);
        endif;

        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $postData["table_name"] = $this->transMain;
            $postData['where'] = [['column_name'=>'from_entry_type','column_value'=>$this->data['entryData']->id]];
            $postData['find'] = [['column_name'=>'ref_id','column_value'=>$id]];
            $checkRef = $this->checkEntryReference($postData);
            if($checkRef['status'] == 0):
                $this->db->trans_rollback();
                return $checkRef;
            endif;

            $dataRow = $this->getDeliveryChallan(['id'=>$id,'itemList'=>1]);

            foreach($dataRow->itemList as $row):
                if(!empty($row->ref_id)):
                    $setData = array();
                    $setData['tableName'] = 'so_trans';
                    $setData['where']['id'] = $row->ref_id;
                    $setData['set_value']['dispatch_qty'] = 'IF(`dispatch_qty` - '.$row->qty.' >= 0, `dispatch_qty` - '.$row->qty.', 0)';
                    $setData['update']['trans_status'] = "(CASE WHEN dispatch_qty >= qty THEN 1 ELSE 3 END)";
                    $this->setValue($setData);
                endif;

                $this->trash($this->transChild,['id'=>$row->id]);
            endforeach;

            if(!empty($dataRow->ref_id)):
                $oldRefIds = explode(",",$dataRow->ref_id);
                foreach($oldRefIds as $main_id):
                    $setData = array();
                    $setData['tableName'] = 'so_master';
                    $setData['where']['id'] = $main_id;
                    $setData['update']['trans_status'] = "(SELECT IF(COUNT(id) = SUM(IF(trans_status NOT IN (0, 3), 1, 0)),1,3) AS trans_status FROM so_trans WHERE trans_main_id = '".$main_id."' AND is_delete = 0)";
                    $this->setValue($setData);
                endforeach;
            endif;                
                
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"DC TERMS"]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"DC MASTER DETAILS"]);
            $this->remove($this->stockTrans,['main_ref_id'=>$id,'trans_type'=>'DLC']);
            $this->trash("packing_annexure_detail",['dc_id'=>$id]);

            $result = $this->trash($this->transMain,['id'=>$id],'Delivery Challan');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getPendingChallanItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*,(trans_child.qty - trans_child.dispatch_qty) as pending_qty,trans_main.entry_type as main_entry_type,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no";

        $queryData['leftJoin']['trans_main'] = "trans_child.trans_main_id = trans_main.id";

        $queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['where']['trans_child.entry_type'] = $this->data['entryData']->id;
        $queryData['where']['trans_main.trans_status'] = 0;

        $queryData['where']['(trans_child.qty - trans_child.dispatch_qty) >'] = 0;

        $queryData['order_by']['trans_main.trans_no'] = "ASC";
        
        return $this->rows($queryData);
    }

    public function getPackingBoxDetail($param = []){
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "stock_trans.batch_no,stock_trans.qty as batch_qty,stock_trans.opt_qty, stock_trans.remark,IFNULL(packAnnex.cartoon_qty,0) AS cartoon_qty";
        $queryData['leftJoin']['(SELECT SUM(total_qty) AS cartoon_qty,batch_no,dc_trans_id,item_id FROM packing_annexure_detail WHERE is_delete = 0 AND dc_trans_id = '.$param['dc_trans_id'].' GROUP BY dc_trans_id,batch_no) packAnnex'] = 'packAnnex.dc_trans_id = stock_trans.child_ref_id AND packAnnex.batch_no = stock_trans.batch_no';
        $queryData['where']['trans_type'] = 'DLC';
        $queryData['where']['main_ref_id'] =$param['dc_id'];
        $queryData['where']['child_ref_id'] =$param['dc_trans_id'];
        $queryData['where']['stock_trans.item_id'] = $param['item_id'];
        if(!empty($param['batch_no'])){ $queryData['where']['stock_trans.batch_no'] = $param['batch_no']; }
        if(!empty($param['single_row'])){
            return $this->row($queryData);
        }
        $queryData['having'][] = '(batch_qty-cartoon_qty) > 0' ;
        return $this->rows($queryData);
    }

    public function saveAnnexureDetail($data){
        try{
            $this->db->trans_begin();

            foreach($data['batchDetail'] AS $batch){
                if(!empty($batch['batch_qty']) && $batch['batch_qty'] > 0){
                    $boxData = [
                        'id'=>'',
                        'dc_id'=>$data['dc_id'],
                        'dc_trans_id'=>$data['dc_trans_id'],
                        'item_id'=>$data['item_id'],
                        'cartoon_no'=>$data['cartoon_no'],
                        'batch_no'=>$batch['batch_no'],
                        'box_qty'=>($batch['batch_qty']/$batch['opt_qty']),
                        'total_qty'=>$batch['batch_qty'],
                        'qty_per_box'=>$batch['opt_qty'],
                    ];
                    $this->store("packing_annexure_detail",$boxData);
                }
            }

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Data Saved Successfully'];
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getAnnexureDetail($param = []){
        $queryData = array();
        $queryData['tableName'] ="packing_annexure_detail";
        $queryData['select'] = "packing_annexure_detail.*,item_master.item_name,item_master.item_code";

        $queryData['leftJoin']['item_master'] = "packing_annexure_detail.item_id = item_master.id";

        if(!empty($param['dc_id'])){ $queryData['where']['packing_annexure_detail.dc_id'] =$param['dc_id'];  }
        if(!empty($param['item_id'])){ $queryData['where']['packing_annexure_detail.item_id'] =$param['item_id'];  }
        if(!empty($param['dc_trans_id'])){ $queryData['where']['packing_annexure_detail.dc_trans_id'] =$param['dc_trans_id'];  }
        
        return $this->rows($queryData);
    }

    public function deleteAnnexureDetail($param){
		try {
			$this->db->trans_begin();

			$result = $this->trash("packing_annexure_detail",['id'=>$param['id']]);

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