<?php 
class StockVerificationModel extends MasterModel
{
    private $itemMaster = "item_master";
	private $stockVerification = "stock_verification";
	private $stockTrans = "stock_trans";

    public function getDTRows($data) {
        $data['tableName'] = $this->stockTrans;
        $data['select'] = "stock_trans.*,SUM(stock_trans.qty * stock_trans.p_or_m) as stock_qty,item_master.item_name,item_master.item_code,item_master.item_type";
        $data['leftJoin']['item_master'] = "stock_trans.item_id = item_master.id";

        if(!empty($data['item_type'])){
			$data['where']['item_master.item_type'] = $data['item_type'];
		}
		$data['group_by'][] = 'stock_trans.item_id';

        $data['searchCol'][] = "";
		$data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

	public function save($data){ 
		foreach($data['physical_qty'] as $key=>$value):
			if($value != ''):
				$varQty = $value - $data['stock_qty'][$key];
				$verificationData = [
					'id' => '',
					'entry_date' => formatDate($data['entry_date'],'Y-m-d'),
					'item_id' => $data['item_id'],
					'location_id'=> $data['location_id'][$key],
					'batch_no'=> $data['batch_no'][$key],  
					'stock_qty' => $data['stock_qty'][$key],
					'physical_qty' => $value,
					'variation_qty' => $varQty,
					'reason' => $data['reason'][$key],
					'created_by' => $this->loginId
				];
				$verifyData = $this->store($this->stockVerification,$verificationData);

				/*** UPDATE STOCK TRANSACTION DATA ***/
				if($varQty != 0){
					if($varQty > 0){ $transType = 1; }else{ $transType = -1; }
					$stockQueryData['id']="";
					$stockQueryData['location_id'] = $data['location_id'][$key];
					if(!empty($data['batch_no'][$key])){$stockQueryData['batch_no'] = $data['batch_no'][$key];}
					$stockQueryData['p_or_m'] = $transType;
					$stockQueryData['item_id'] = $data['item_id'];
					$stockQueryData['qty'] = abs($varQty);
					$stockQueryData['trans_type'] = 'SVR';
					$stockQueryData['main_ref_id'] = $verifyData['insert_id'];
					$stockQueryData['trans_date']= formatDate($data['entry_date'],'Y-m-d');
					$stockQueryData['created_by'] = $this->loginId;
					$this->store($this->stockTrans,$stockQueryData);
				}
			endif;
		endforeach;
		return ['status'=>1,'message'=>"Stock Verified successfully."];
	}
}
?>