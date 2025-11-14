<?php
class PackingListModel extends masterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $transDetails = "trans_details";

    public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_main.id, trans_main.trans_number, DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y') as trans_date, trans_main.party_name, trans_main.trans_status, SUM(trans_child.total_box) as total_box, SUM(trans_child.pallet_qty) as total_pallets, SUM(trans_child.net_weight) as net_weight, SUM(trans_child.gross_weight) as gross_weight";

        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";

        $data['where']['trans_main.entry_type'] = $data['entry_type'];

        if($data['status'] == 0):
            $data['where']['trans_main.trans_status'] = 0;

            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        elseif($data['status'] == 1):
            $data['where']['trans_main.trans_status'] = 1;

            $data['where']['trans_main.trans_date >='] = $this->startYearDate;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        endif;        

        $data['group_by'][] = "trans_main.id";
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.trans_no'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "SUM(trans_child.total_box)";
        $data['searchCol'][] = "SUM(trans_child.pallet_qty)";
        $data['searchCol'][] = "SUM(trans_child.net_weight)";
        $data['searchCol'][] = "SUM(trans_child.gross_weight)";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];

            if($this->checkDuplicate($data) > 0):
                $errorMessage['trans_number'] = "Pck. No. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            if(!empty($data['id'])):
                $this->trash($this->transChild,['trans_main_id'=>$data['id']]);
                $this->remove($this->transDetails,['main_ref_id'=>$data['id'],'table_name'=>$this->transMain,'description'=>"EXPPCK MASTER DETAILS"]);                
            endif;
            
            $masterDetails = (!empty($data['masterDetails']))?$data['masterDetails']:array();
            $itemData = $data['itemData'];

            unset($data['itemData'],$data['masterDetails']);		

            $result = $this->store($this->transMain,$data,'Packing List');

            if(!empty($masterDetails)):
                $masterDetails['id'] = "";
                $masterDetails['main_ref_id'] = $result['id'];
                $masterDetails['table_name'] = $this->transMain;
                $masterDetails['description'] = "EXPPCK MASTER DETAILS";
                $masterDetails['date_col_1'] = (!empty($masterDetails['date_col_1']))?$masterDetails['date_col_1']:NULL;
                $this->store($this->transDetails,$masterDetails);
            endif;

            $i=1;
            foreach($itemData as $row):
                $row['entry_type'] = $data['entry_type'];
                $row['trans_main_id'] = $result['id'];
                $row['is_delete'] = 0;
				
                $itemTrans = $this->store($this->transChild,$row);
            endforeach;

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

        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];

        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function getPackingList($data){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.*";

        $queryData['select'] .= ",trans_details.t_col_1 as consignee,
        trans_details.t_col_2 as buyer_address,
        trans_details.t_col_3 as terms_and_method_of_payment,
        
        trans_details.date_col_1 as date_of_departure,

        trans_details.s_col_1 as buyer_name,
        trans_details.s_col_2 as method_of_dispatch,
        trans_details.s_col_3 as type_of_shipment,
        trans_details.s_col_4 as country_of_origin,
        trans_details.s_col_5 as country_of_fd,
        trans_details.s_col_6 as port_of_loading,
        trans_details.s_col_7 as port_of_discharge,
        trans_details.s_col_8 as final_destination,
        trans_details.s_col_9 as delivery_type,
        trans_details.s_col_10 as delivery_location";

        $queryData['leftJoin']['trans_details'] = "trans_main.id = trans_details.main_ref_id AND trans_details.description = 'EXPPCK MASTER DETAILS' AND trans_details.table_name = '".$this->transMain."'";

        $queryData['where']['trans_main.id'] = $data['id'];
        $result = $this->row($queryData);

        if($data['itemList'] == 1):
            $result->itemList = $this->getPackingListItems($data);
        endif;

        return $result;
    }

    public function getPackingListItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*,item_master.hsn_code,item_master.gst_per as gst_per,item_master.uom as unit_name";
        $queryData['leftJoin']['item_master'] = "trans_child.item_id = item_master.id";
        $queryData['where']['trans_child.trans_main_id'] = $data['id'];
        $result = $this->rows($queryData);
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
            
            $this->trash($this->transChild,['trans_main_id'=>$id]);
            $this->remove($this->transDetails,['main_ref_id'=>$id,'table_name'=>$this->transMain,'description'=>"EXPPCK MASTER DETAILS"]);
            $result = $this->trash($this->transMain,['id'=>$id],'Packing List');

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