<?php
class CustomerComplaintsModel extends MasterModel{
    private $customerComplaints = "customer_complaints";

    public function getDTRows($data){
        $data['tableName'] = $this->customerComplaints;
        $data['select'] = "customer_complaints.*,party_master.party_name,item_master.item_name,trans_child.trans_main_id,IFNULL(trans_main.trans_number,customer_complaints.ref_complaint) as inv_number";
        $data['leftJoin']['party_master'] = "customer_complaints.party_id = party_master.id";
        $data['leftJoin']['item_master'] = "item_master.id = customer_complaints.item_id";
        $data['leftJoin']['trans_child'] = "trans_child.id = customer_complaints.inv_trans_id";
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['customer_complaints.status'] = $data['status'];

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "customer_complaints.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(customer_complaints.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "IFNULL(trans_main.trans_number,customer_complaints.ref_complaint)";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "customer_complaints.complaint";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "if(customer_complaints.product_returned = 2,'YES','NO')";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "customer_complaints.action_taken";
        $data['searchCol'][] = "customer_complaints.ref_feedback";
        $data['searchCol'][] = "customer_complaints.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getNextTransNo(){
        $data['tableName'] = $this->customerComplaints;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['YEAR(trans_date)'] = date("Y");
        $data['where']['MONTH(trans_date)'] = date("m");
        $maxNo = $this->specificRow($data)->trans_no;
        $nextNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextNo;
    }

    public function getCustomerComplaints($data){
        $queryData['tableName'] = $this->customerComplaints;
        $queryData['where']['id'] = $data['id'];
        return $this->row($queryData);
    }

	public function save($data){   
        try{
            $this->db->trans_begin();
            $result = $this->store($this->customerComplaints,$data);

            if(!empty($result['id'])){
                if(!empty($data['product_returned']) && $data['product_returned'] == 2){

                    $invData = (!empty($data['inv_id']) ? $this->getSalesInvoiceByParty(['id'=>$data['inv_id'], 'item_id'=>$data['item_id'], 'single_row'=>1]) : []);
                    $inv_number = (!empty($invData->trans_number) ? $invData->trans_number : '');

                    $trans_prefix = 'GI/'.getYearPrefix('SHORT_YEAR').'/';
                    $trans_no = $this->gateInward->getNextGrnNo();
                    $trans_number = $trans_prefix.$trans_no;

                    $grnData = [
                        'id'=>"",
                        'grn_type' => 1,
                        'type' => 3,
                        'trans_prefix' => $trans_prefix,
                        'trans_no' => $trans_no,
                        'trans_number' => $trans_number,
                        'trans_date' => $data['trans_date'],
                        'party_id' => $data['party_id'],
                        'doc_no' => $inv_number,
                        'doc_date' => $data['inv_date'],
                        'trans_status' => 1,
                        'batchData' => [[
                            'id'=>"",
                            'ref_id' => $result['id'],
                            'item_id' => $data['item_id'],
                            'batch_no' => $data['batch_no'],
                            'qty' => $data['qty'],
                            'price' => $data['price'], 
                            'trans_status' => 1
                        ]]
                    ]; 
                    $this->gateInward->save($grnData);
                }
            }
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
             return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}
	
    public function delete($id){
        try{
            $this->db->trans_begin();
			
			$complaintsData = $this->getCustomerComplaints(['id'=>$id]);
			if(!empty($complaintsData)){
				$filePath = realpath(APPPATH . '../assets/uploads/defect_image/');
				$defect_image = (!empty($complaintsData->defect_image) ? explode(",",$complaintsData->defect_image) : array());
				foreach($defect_image as $val){
                    if(!empty($val) && file_exists($filePath.'/'.$val)):
                        unlink($filePath.'/'.$val);
                    endif;
				}
			}
			
            $result = $this->trash($this->customerComplaints,['id'=>$id]);
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
             return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

	public function getSalesInvoiceByParty($data){
        $queryData['tableName'] = 'trans_main';
        $queryData['select'] = "trans_main.id,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_number,trans_main.trans_date,trans_child.price";
        $queryData['leftJoin']['trans_child'] = "trans_child.trans_main_id = trans_main.id";
		$queryData['where']['trans_main.entry_type'] = 20;

        if (!empty($data['party_id'])) { $queryData['where']['trans_main.party_id'] = $data['party_id']; }

        if (!empty($data['id'])) { $queryData['where']['trans_main.id'] = $data['id']; }

        if (!empty($data['item_id'])) { $queryData['where']['trans_child.item_id'] = $data['item_id']; }

		if (!empty($data['trans_id'])) { $queryData['where']['trans_child.id'] = $data['trans_id']; }

        if (!empty($data['single_row'])):
            return $this->row($queryData);
        else:
            return $this->rows($queryData);
        endif;
    }
}