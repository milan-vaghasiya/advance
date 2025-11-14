<?php
class PurchaseModel extends MasterModel{
	
	private $purchase_enquiry = "purchase_enquiry";
	private $purchase_quotation = "purchase_quotation";
	private $item_master = "item_master";
    private $transDetails = "trans_details";
	
	public function getDTRows($data){
        $data['tableName'] = $this->purchase_enquiry;
        $data['select'] = "purchase_enquiry.*, IFNULL(pm.party_name,'') as party_name, IFNULL(cat.category_name,'') as category_name,IFNULL(pq.quote_count,0) as quotation_count,pq.price,pq.qty as quot_qty,item_master.uom as unit_name,item_master.cnv_value,item_master.com_uom as com_unit,pq.enq_id,pq.quote_no,purchase_quotation.quote_remark,purchase_quotation.enq_id,purchase_quotation.id as quote_id,po_master.trans_number as order_number,(purchase_enquiry.qty - IFNULL(poTrans.po_qty,0)) as pending_qty,item_master.item_code,fgItem.item_code as fg_item_code,fgItem.item_name as fg_item_name,item_master.item_name,purchase_quotation.quote_no,purchase_quotation.quote_date,purchase_quotation.feasible,purchase_quotation.qty as moq,purchase_quotation.price,purchase_quotation.lead_time,purchase_quotation.delivery_date";
		$data['leftJoin']['party_master pm'] = "pm.id = purchase_enquiry.party_id";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_enquiry.item_id";
        $data['leftJoin']['item_category cat'] = "cat.id = purchase_enquiry.item_type";
        $data['leftJoin']['item_master fgItem'] = "fgItem.id = purchase_enquiry.fg_item_id";
		$data['leftJoin']['(SELECT COUNT(*) as quote_count,enq_id,price,qty,quote_no FROM purchase_quotation WHERE is_delete = 0 GROUP BY enq_id) as pq'] = "pq.enq_id = purchase_enquiry.id";
        $data['leftJoin']['purchase_quotation'] = "purchase_quotation.enq_id = purchase_enquiry.id";		
		$data['leftJoin']['(SELECT SUM(qty) as po_qty,ref_id,po_id,from_entry_type FROM po_trans WHERE is_delete = 0 GROUP BY ref_id) as poTrans'] = "poTrans.ref_id = purchase_enquiry.id AND poTrans.from_entry_type = 160";
		$data['leftJoin']['po_master'] = "poTrans.po_id = po_master.id";
				
		if (in_array($data['status'],[2,4,5])) { $data['where']['purchase_quotation.feasible'] = 1; }
		
		if($data['status'] == 2){ 
			$data['where_in']['purchase_enquiry.trans_status'] = [2,5];
			$data['having'][] = "pending_qty > 0";
		}
		elseif($data['status'] == 5){ 
			$data['where']['purchase_enquiry.trans_status'] = $data['status'];
			$data['having'][] = "pending_qty <= 0";
			
			$data['where']['purchase_enquiry.trans_date >='] = $this->startYearDate;
			$data['where']['purchase_enquiry.trans_date <='] = $this->endYearDate;
		}
		elseif($data['status'] == 3){
			$data['customWhere'][] = "purchase_enquiry.trans_status = ".$data['status']." OR purchase_quotation.feasible = 0";
			
			$data['where']['purchase_enquiry.trans_date >='] = $this->startYearDate;
			$data['where']['purchase_enquiry.trans_date <='] = $this->endYearDate;
		}
		else{ 
			$data['where']['purchase_enquiry.trans_status'] = $data['status'];
		}

		if(!empty($data['id'])){ $data['where']['purchase_enquiry.id'] = $data['id']; }
		if(!empty($data['item_id'])){ $data['where']['purchase_enquiry.item_id'] = $data['item_id']; }
		
		//$data['order_by']['purchase_enquiry.trans_date'] = 'DESC';
		$data['order_by']['purchase_enquiry.id'] = 'DESC';

		$data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "purchase_enquiry.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(purchase_enquiry.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "pm.party_name";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "CONCAT('[',fgItem.item_code,'] ',fgItem.item_name)";
        $data['searchCol'][] = "purchase_enquiry.uom";
        $data['searchCol'][] = "purchase_enquiry.qty";
        $data['searchCol'][] = "purchase_quotation.qty";
        $data['searchCol'][] = "purchase_quotation.price";
        $data['searchCol'][] = "purchase_quotation.lead_time";
        $data['searchCol'][] = "purchase_quotation.quote_no";
        $data['searchCol'][] = "DATE_FORMAT(purchase_quotation.quote_date,'%d-%m-%Y')";
        $data['searchCol'][] = "IF(purchase_quotation.feasible = 1, 'Yes', 'No')";
        $data['searchCol'][] = "purchase_enquiry.item_remark";
        $data['searchCol'][] = "purchase_quotation.quote_remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }
	
	public function getPurchaseEnqList($param=[]){
		$queryData['tableName'] = $this->purchase_enquiry;
        $queryData['select'] = "purchase_enquiry.*, IFNULL(pm.party_name,'') as party_name, IFNULL(cat.category_name,'') as category_name,IFNULL(pq.quote_count,0) as quotation_count,pq.price,pq.qty as quot_qty,item_master.uom as unit_name,item_master.cnv_value,item_master.com_uom as com_unit,pq.enq_id,pq.quote_no,purchase_quotation.quote_remark,item_master.gst_per,pq.quote_date,fgItem.item_code as fg_item_code,fgItem.item_name as fg_item_name,item_master.hsn_code,purchase_quotation.delivery_date,purchase_quotation.mill_name,material_master.material_grade";
		$queryData['leftJoin']['party_master pm'] = "pm.id = purchase_enquiry.party_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = purchase_enquiry.item_id";
		$queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id"; 
        $queryData['leftJoin']['item_category cat'] = "cat.id = purchase_enquiry.item_type";
		$queryData['leftJoin']['(SELECT COUNT(*) as quote_count,enq_id,price,qty,quote_no,quote_date FROM purchase_quotation WHERE is_delete = 0 GROUP BY enq_id) as pq'] = "pq.enq_id = purchase_enquiry.id";
        $queryData['leftJoin']['purchase_quotation'] = "purchase_quotation.enq_id = purchase_enquiry.id";
        $queryData['leftJoin']['item_master fgItem'] = "fgItem.id = purchase_enquiry.fg_item_id"; 
		
		if(!empty($param['orderData'])){
			$queryData['select'] .= ',po_master.trans_number as order_number,(purchase_enquiry.qty - IFNULL(poTrans.po_qty,0)) as pending_qty'; 
			$queryData['leftJoin']['(SELECT SUM(qty) as po_qty,ref_id,po_id,from_entry_type FROM po_trans WHERE is_delete = 0 GROUP BY ref_id) as poTrans'] = "poTrans.ref_id = purchase_enquiry.id AND poTrans.from_entry_type = 160";
			$queryData['leftJoin']['po_master'] = "poTrans.po_id = po_master.id ";
		}
		if(!empty($param['status'])){ 
			$queryData['where_in']['purchase_enquiry.trans_status'] = $param['status'];
		}
		if(!empty($param['id'])){ $queryData['where']['purchase_enquiry.id'] = $param['id']; }
		if(!empty($param['trans_number'])){ $queryData['where']['purchase_enquiry.trans_number'] = $param['trans_number']; }
		if(!empty($param['party_id'])){ $queryData['where']['purchase_enquiry.party_id'] = $param['party_id']; }
		if(!empty($param['item_id'])){ $queryData['where']['purchase_enquiry.item_id'] = $param['item_id']; }
        if(!empty($param['ids'])){ $queryData['where_in']['purchase_enquiry.id'] = str_replace("~", ",", $param['ids']); }
		
		if(!empty($param['skey'])){
			$queryData['like']['purchase_enquiry.trans_number'] = str_replace(" ", "%", $param['skey']);
			$queryData['like']['purchase_enquiry.trans_date'] = str_replace(" ", "%", $param['skey']);
			$queryData['like']['purchase_enquiry.item_name'] = str_replace(" ", "%", $param['skey']);
			$queryData['like']['purchase_enquiry.qty'] = str_replace(" ", "%", $param['skey']);
			$queryData['like']['pm.party_name'] = str_replace(" ", "%", $param['skey']);
        }

        if(!empty($param['limit'])){ $queryData['limit'] = $param['limit']; }
        
		if(isset($param['start'])){ $queryData['start'] = $param['start']; }
		
		if(!empty($param['length'])){ $queryData['length'] = $param['length']; }
		

		$queryData['order_by']['purchase_enquiry.trans_date'] = 'DESC';
		$queryData['order_by']['purchase_enquiry.id'] = 'DESC';
		
        if(!empty($param['single_row'])):
			$result = $this->row($queryData);
		else:
			$result = $this->rows($queryData);
		endif;

		if(!empty($param['trans_number'])){
			$queryData = array();
			$queryData['tableName'] = $this->transDetails;
			$queryData['select'] = "t_col_1 as condition";
			$queryData['where']['t_col_2'] = $param['trans_number'];
			$queryData['where']['table_name'] = $this->purchase_enquiry;
			$queryData['where']['description'] = "PE TERMS";
			$result[0]->termsConditions = $this->row($queryData);
		}

        return $result;  
    }

	public function saveEnquiry($data){
		try {
            $this->db->trans_begin();
			
			if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_no'] = "Enquiry No. is duplicate.";
                $result = ['status'=>0,'message'=>$errorMessage];
            endif;			
			
            $termsData = (!empty($data['conditions']))?$data['conditions']:array();
			unset($data['conditions']);

			if(!empty($data['itemData'])):
				foreach($data['itemData'] as $row):
					$storeData = [
						'id' => $row['id'],
						'req_id' => $row['req_id'],
						'from_entry_type' => $row['from_entry_type'],
						'entry_type' => $data['entry_type'],
						'trans_no' => $data['trans_no'],
						'trans_prefix' => $data['trans_prefix'],
						'trans_number' => $data['trans_number'],
						'trans_date' => $data['trans_date'],
						'party_id' => $data['party_id'],
						'item_type' => $row['item_type'],
						'item_id' => $row['item_id'],
						'fg_item_id' => $row['fg_item_id'],
						'item_name' => $row['item_name'],
						'uom' => $row['uom'],
						'qty' => $row['qty'],
						'item_remark' => $row['item_remark'],
						'remark' => $data['remark'],
						'vou_name_l' => $data['vou_name_l'],
						'vou_name_s' => $data['vou_name_s'],
						'so_trans_id' => $row['so_trans_id'] 
					];
					$result = $this->store($this->purchase_enquiry,$storeData,'Purchase Enquiry');
					
					if(!empty($row['req_id'])):
						$this->edit('purchase_indent',['id'=>$row['req_id']],['order_status'=>2]);
					endif;	
				endforeach;
			endif;							

			if(!empty($data['trans_number'])):
				$this->remove($this->transDetails,['t_col_2'=>$data['trans_number'],'table_name'=>$this->purchase_enquiry,'description'=>"PE TERMS"]);
			endif;

			if(!empty($termsData)):
				$termsData = [
					'id' =>"",
					'table_name' => $this->purchase_enquiry,
					'description' => "PE TERMS",
					't_col_2' => $data['trans_number'],
					't_col_1' => $termsData
				];
				$this->store($this->transDetails,$termsData);
			endif;
					
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
                return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}	
	}
	
	public function checkDuplicate($data){
        $queryData['tableName'] = $this->purchase_enquiry;
        $queryData['where']['trans_number'] = $data['trans_number'];
        $queryData['where']['entry_type'] = $data['entry_type'];

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

	public function deleteEnquiry($data){
        try{
            $this->db->trans_begin();

			$this->remove($this->transDetails,['t_col_2'=>$data['trans_number'],'table_name'=>$this->purchase_enquiry,'description'=>"PE TERMS"]);
            $result = $this->trash($this->purchase_enquiry,['id'=>$data['id']],'Purchase Enquiry');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

	public function saveQuotation($data){
		try {
            $this->db->trans_begin();
			if(!empty($data['enq_id'])):
				foreach($data['enq_id'] as $key=>$value):
					$storeData = [
						'id' => $data['id'][$key],
						'enq_id' => $data['enq_id'][$key],
						'party_id' => $data['party_id'][$key],
						'item_id' => $data['item_id'][$key],
						'feasible' => $data['feasible'][$key],
						'qty' => $data['qty'][$key],
						'price' => $data['price'][$key],
						'lead_time' => $data['lead_time'][$key],
						'quote_no' => $data['quote_no'][$key],
						'quote_date' => (!empty($data['delivery_date'][$key]) ? $data['quote_date'][$key] : NULL),
						'delivery_date' => (!empty($data['delivery_date'][$key]) ? $data['delivery_date'][$key] : NULL),
						'quote_remark' => $data['quote_remark'][$key],
						'mill_name' => $data['mill_name'][$key],
					];
					if($data['feasible'][$key] == 1){
						$result = $this->edit($this->purchase_enquiry, ['id'=>$data['enq_id'][$key]], ['trans_status' => 4]);
					}else{
						$storeData['trans_status'] = 3;
						$result = $this->edit($this->purchase_enquiry, ['id'=>$data['enq_id'][$key]], ['trans_status' => 3]);
					}
					
					$result = $this->store($this->purchase_quotation, $storeData, 'Quotation');
				endforeach;
			endif;
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
                return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}	
	}

	public function getQuotationData($param=[]){
		$queryData['tableName'] = $this->purchase_quotation;		
		$queryData['select'] = "purchase_quotation.*, IFNULL(pm.party_name,'') as party_name,purchase_enquiry.item_name,purchase_enquiry.trans_number as enq_number,purchase_enquiry.trans_date as enq_date,purchase_enquiry.item_type,purchase_enquiry.uom";
        $queryData['leftJoin']['party_master pm'] = "pm.id = purchase_quotation.party_id";
        $queryData['leftJoin']['purchase_enquiry'] = "purchase_enquiry.id = purchase_quotation.enq_id";
		if(!empty($param['lastPOPrice'])){
			$queryData['select'] .= ',purchaseOrder.price as last_po_price';
			$queryData['leftJoin']['(SELECT party_id, po_trans.price, po_trans.item_id FROM po_trans JOIN po_master ON po_master.id = po_trans.po_id WHERE po_trans.is_delete = 0 AND po_trans.id IN( SELECT MAX(pt.id) FROM po_trans pt JOIN po_master po ON po.id = pt.po_id WHERE pt.is_delete = 0 GROUP BY po.party_id, pt.item_id)) as purchaseOrder'] = "purchaseOrder.party_id = purchase_quotation.party_id AND purchaseOrder.item_id = purchase_quotation.item_id "; //04-10-2024
		}
		if(!empty($param['trans_status'])){$queryData['where']['purchase_quotation.trans_status'] = $param['trans_status'];  }
		if(!empty($param['id'])){ $queryData['where']['purchase_quotation.enq_id'] = $param['id']; }
		if(!empty($param['item_id'])){ $queryData['where']['purchase_quotation.item_id'] = $param['item_id']; }
		if(!empty($param['party_id'])){ $queryData['where_in']['purchase_quotation.party_id'] = $param['party_id']; } 
		if(!empty($param['item_name'])){ $queryData['where']['purchase_enquiry.item_name'] = $param['item_name']; }
		if(!empty($param['group_by'])){ $queryData['group_by'][] = $param['group_by']; }
		if(!empty($param['order_by'])){ $queryData['order_by']['purchase_quotation.created_at'] = 'DESC'; }
			
        if(!empty($param['multi_row'])):
			$result = $this->rows($queryData);
		else:
			$result = $this->row($queryData);
		endif;
        return $result;  
    }

	public function chageEnqStatus($data){
		try {
            $this->db->trans_begin();

			$quoteDetail = $this->getQuotationData(['id'=>$data['enq_id']]);

			$item_id = $quoteDetail->item_id;

			if($quoteDetail->item_id == -1){

				$itemData = $this->item->getItem(['item_name'=>$quoteDetail->item_name,'item_types'=>$quoteDetail->item_type]);
				
				if(!empty($itemData->id)){
					$item_id = $itemData->id;
				}else{
					$quoteData = [
						'id' => '',
						'item_name' => $quoteDetail->item_name,
						'item_type' => $quoteDetail->item_type
					];
					$itemData = $this->store($this->item_master, $quoteData);
					$item_id = $itemData['id'];
				}
			}

			$masterData = [
				'id' => $data['id'],
				'trans_status' => $data['val'],
				'approve_by' => ($data['val'] == 2) ? $this->loginId : 0,
				'approve_date' => ($data['val'] == 2) ? date('Y-m-d') : NULL,
				'item_id' => $item_id,
			];
			$this->store($this->purchase_quotation, $masterData);
			$this->store($this->purchase_enquiry, ['id'=>$data['enq_id'],'item_id'=>$item_id,'trans_status'=>$data['val']]);

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return ['status' => 1, 'message' => 'Quotation ' . $data['msg'] . ' successfully.'];
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}
	
	public function getEnquiryData($data = array()){
		$result = $this->getPurchaseEnqList(['trans_number'=>$data['trans_number'],'party_id'=>$data['party_id']]);
		
		if (!empty($result)) :
			$i = 1;
			$html = "";
			foreach ($result as $row) :
				if ($row->trans_status == 1) :
					$checked = "";
					$disabled = "disabled";
					$html .= '<tr>
							<td class="text-center">
								<label for="md_checkbox'.$i.'">'.$i.'</label>
							</td>
							<td>
								'.$row->item_name.'
								<input type="hidden" name="id[]" id="id'.$i.'" class="form-control" value="" />
								<input type="hidden" name="enq_id[]" id="enq_id'.$i.'" class="form-control" value="'.$row->id .'" />
								<input type="hidden" name="party_id[]" id="party_id'.$i.'" class="form-control" value="'.$row->party_id .'" />
								<input type="hidden" name="item_id[]" id="item_id'. $i.'" class="form-control" value="'.$row->item_id .'" />
								<div class="error item_id'.$row->id.'"></div>
							</td>
							<td>
								<select name="feasible[]" id="feasible'.$i .'"  class="form-control">
									<option value="1">Yes</option>
									<option value="2">No</option>
								</select>
								<div class="error feasible'.$row->id.'"></div>
							</td>
							<td>
								<input type="text" name="qty[]" id="qty'.$i.'" class="form-control floatOnly" value="'.$row->qty.'" />
								<div class="error qty'.$row->id.'"></div>
							</td>
							<td>
								<input type="text" name="price[]" id="price'.$i.'" class="form-control floatOnly" value="" />
								<div class="error price'.$row->id.'"></div>
							</td>
							<td>
								<input type="text" name="lead_time[]" id="lead_time'.$i.'" class="form-control floatOnly" value="" />
								<div class="error lead_time'.$row->id.'"></div>
							</td>
							<td>
								<input type="text" name="mill_name[]" id="mill_name'.$i.'" class="form-control" value="" />
								<div class="error mill_name'.$row->id.'"></div>
							</td>
							<td>
								<input type="text" name="quote_no[]" id="quote_no'.$i.'" class="form-control" value="" />
								<div class="error quote_no'.$row->id.'"></div>
							</td>
							<td>
								<input type="date" name="quote_date[]" id="quote_date'.$i.'" class="form-control " value="" min="0" />
								<div class="error quote_date'.$row->id.'"></div>
							</td>
							<td>
								<input type="date" name="delivery_date[]" id="delivery_date'.$i.'" class="form-control" value="" />
								<div class="error delivery_date'.$row->id.'"></div>
							</td>
							<td>
								<input type="text" name="quote_remark[]" id="quote_remark'.$i.'" class="form-control" value="" />
							</td>
						</tr>';
				endif;
				$i++;
			endforeach;
		else :
			$html = '<tr><td colspan="10" class="text-center">No data available in table</td></tr>';
		endif;
		return $html;
	}
}
?>