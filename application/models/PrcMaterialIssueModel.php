<?php
class PrcMaterialIssueModel extends MasterModel{
    private $prc_bom = "prc_bom";
    private $stock_transaction = "stock_transaction";

    public function getDTRows($data){ 
        $data['tableName'] = $this->prc_bom;
        $data['select'] = "prc_bom.*,prc_master.prc_number,prc_master.prc_date,item_master.item_name";
        $data['leftJoin']['prc_master'] = "prc_master.id = prc_bom.prc_id";
        $data['leftJoin']['item_master'] = "item_master.id = prc_bom.item_id";
        $data['where_in']['prc_master.status '] = [1,2];

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['serachCol'][] = "prc_master.prc_number";
        $data['serachCol'][] = "DATE_FORMAT(prc_master.prc_date,'%d-%m-%Y')";
        $data['serachCol'][] = "prc_bom.bom_group";
        $data['serachCol'][] = "item_master.item_name";
        $data['serachCol'][] = "prc_bom.req_qty";
        $data['serachCol'][] = "prc_bom.issue_qty";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
               
        return $this->pagingRows($data);
    }

    /*** FIRST TIME STORE DATA IN PRC BOM TABLE, OTHERWISE GIVE STOCK EFFECT AND UPDATE ISSUE QTY */
    public function save($data){
        try {
            $this->db->trans_begin();
            $prcData = $this->sop->getPRCDetail(['id'=>$data['prc_id']]); // PRC DATA
            $bomData = $this->getPrcBomData(['prc_id'=>$data['prc_id'],'item_id'=>$data['item_id'],'single_row'=>1]); // PRC BOM DATA
            $issueId = !empty($bomData)?$bomData->id:'';
            if(empty($issueId)){
                $kitData = $this->item->getProductKitData(['ref_item_id'=>$data['item_id'],'item_id'=>$prcData->item_id,'single_row'=>1]); // KIT DATA
                $prcBomData = [
                    'id'=>'',
                    'process_id'=>$kitData->process_id,
                    'prc_id'=>$data['prc_id'],
                    'item_id'=>$data['item_id'],
                    'ppc_qty'=>$kitData->qty,
                    'req_qty'=>($kitData->qty*$prcData->prc_qty),
                    'bom_group'=>$kitData->group_name,
                    'created_by'=>$this->loginId,
                    'created_at'=>date("Y-m-d H:i:s"),
                ];
                $bomResult = $this->store($this->prc_bom,$prcBomData);
                $issueId = $bomResult['id'];
            }
            $totalIssueQty = array_sum($data['issue_qty']);
            foreach($data['issue_qty'] as $key=>$issue_qty){
                if($issue_qty > 0){
                    $stockData = [
                        'id' => "",
                        'entry_type' => $data['entry_type'],
                        'unique_id' => 0,
                        'ref_date' => $data['ref_date'],
                        'ref_no' => $prcData->prc_number,
                        'main_ref_id' => $data['prc_id'], // PRC ID
                        'child_ref_id' => $issueId, // PRC BOM ID
                        'location_id' => $data['location_id'][$key],
                        'batch_no' => $data['batch_no'][$key],
                        'item_id' => $data['item_id'],
                        'p_or_m' => -1,
                        'qty' => $issue_qty,
                    ];
                    $this->store($this->stock_transaction,$stockData);
                }
            }
            $setData = array();
            $setData['tableName'] = $this->prc_bom;
            $setData['where']['id'] = $issueId;
            $setData['set']['issue_qty'] = 'issue_qty, + ' .  $totalIssueQty;
            $this->setValue($setData);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material Issue suucessfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getPrcBomData($param){
        $data['tableName'] = $this->prc_bom;
        $data['select'] = "prc_bom.*,prc_master.prc_number,item_master.item_name,item_master.item_type";
        $data['leftJoin']['prc_master'] = "prc_master.id = prc_bom.prc_id";
        $data['leftJoin']['item_master'] = "item_master.id = prc_bom.item_id";
        if(!empty($param['prc_id'])){ $data['where']['prc_bom.prc_id'] = $param['prc_id']; }
        if(!empty($param['item_id'])){ $data['where']['prc_bom.item_id'] = $param['item_id']; }
        if(!empty($param['bom_group'])){ $data['where']['prc_bom.bom_group'] = $param['bom_group']; }

        if(!empty($param['single_row'])){
            $result = $this->row($data);
        }else{
            $result = $this->rows($data);
        }
        return $result;
    }

    
}
?>