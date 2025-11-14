<?php
class PurchaseIndentModel extends MasterModel
{
    private $purchase_indent = "purchase_indent";

    public function getDTRows($data){
        $data['tableName'] = $this->purchase_indent;
		$data['select'] = "purchase_indent.*,item_master.item_code,item_master.item_name,item_master.uom,fgItem.item_code as fg_item_code,fgItem.item_name as fg_item_name,created.emp_name as created_name";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_indent.item_id";
        $data['leftJoin']['item_master fgItem'] = "fgItem.id = purchase_indent.fg_item_id";
		$data['leftJoin']['employee_master created'] = "created.id = purchase_indent.created_by";

        $data['where']['purchase_indent.order_status'] = $data['status'];
        $data['where']['purchase_indent.entry_type'] = $data['entry_type'];

		if($data['status'] != 1){
			$data['where']['purchase_indent.trans_date >='] = $this->startYearDate;
			$data['where']['purchase_indent.trans_date <='] = $this->endYearDate;
		}
		
        $data['order_by']['purchase_indent.trans_date'] = "DESC";
        $data['order_by']['purchase_indent.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
		if(!empty($data['pInd'])){ 
            $data['searchCol'][] = "";
        }
        $data['searchCol'][] = "purchase_indent.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(purchase_indent.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "CONCAT('[',fgItem.item_code,'] ',fgItem.item_name)";
        $data['searchCol'][] = "purchase_indent.qty";
        $data['searchCol'][] = "DATE_FORMAT(purchase_indent.delivery_date,'%d-%m-%Y')";
        $data['searchCol'][] = "purchase_indent.remark";
		$data['searchCol'][] = "created.emp_name";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            
            $result = $this->store($this->purchase_indent,$data,'purchase Request');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

	public function getPurchaseRequest($data){
        $data['tableName'] = $this->purchase_indent;
        $data['select'] = "purchase_indent.*,item_master.item_type,item_master.item_name,item_master.uom";//08-10-2024
        $data['leftJoin']['item_master'] = "item_master.id = purchase_indent.item_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.item_type";
        if(!empty($data['id'])):
            $data['where']['purchase_indent.id'] = $data['id'];
        endif;
        return $this->row($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->purchase_indent,['id'=>$id],'Purchase Request');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function changeReqStatus($postData){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->purchase_indent,$postData,'Purchase Request');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getPurchaseRequestForOrder($id){
        $data['tableName'] = $this->purchase_indent;
        $data['select'] = "purchase_indent.*,item_master.item_name,item_master.item_code,item_master.gst_per,item_master.price,item_master.uom,item_master.hsn_code,item_master.com_uom,item_master.item_type,item_category.category_name";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_indent.item_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.item_type";
        $data['where_in']['purchase_indent.id'] = str_replace("~", ",", $id);
        $result = $this->rows($data);
        return $result;
    }

	/*public function getForecastDtRows($data)
    {
		$style = 'style="margin:0px;padding:0px"';
        $data['tableName'] = "so_trans";
		$data['select'] = "`item_kit`.`ref_item_id`, so_trans.item_id, `item_master`.`item_name`, `item_master`.`item_code`,
                            GROUP_CONCAT(so_master.trans_number SEPARATOR '<hr ".$style.">') AS so_number, 
                            GROUP_CONCAT(DISTINCT so_trans.id SEPARATOR '~') AS so_trans_id, 
                            GROUP_CONCAT(so_trans.qty SEPARATOR '<hr ".$style.">') AS so_qty, 
                            IFNULL(SUM(so_trans.qty), 0) AS total_qty,
                            IFNULL(SUM(so_trans.dispatch_qty), 0) AS total_dispatch_qty, 
                            IFNULL(stock.prd_finish_Stock, 0) AS prd_finish_Stock, 
                            IFNULL(stock.rtd_Stock, 0) AS rtd_Stock, 
                            SUM(
                                IF(
                                    ((so_trans.qty -  so_trans.dispatch_qty) - ((IFNULL(prc_master.wip_qty, 0)-(IFNULL(stock.prd_finish_Stock, 0)+IFNULL(stock.rtd_Stock, 0) + so_trans.dispatch_qty)))) +  IFNULL(stock.prd_finish_Stock, 0) +IFNULL(stock.rtd_Stock, 0) > 0,
                                    ((so_trans.qty -  so_trans.dispatch_qty) - ((IFNULL(prc_master.wip_qty, 0)-(IFNULL(stock.prd_finish_Stock, 0)+IFNULL(stock.rtd_Stock, 0) + so_trans.dispatch_qty)))) +  IFNULL(stock.prd_finish_Stock, 0) +IFNULL(stock.rtd_Stock, 0),
                                    0)) AS shortage_qty, 
                            SUM(
                                IF(
                                    ((so_trans.qty -  so_trans.dispatch_qty) - ((IFNULL(prc_master.wip_qty, 0)-(IFNULL(stock.prd_finish_Stock, 0)+IFNULL(stock.rtd_Stock, 0) + so_trans.dispatch_qty)))) +  IFNULL(stock.prd_finish_Stock, 0) +IFNULL(stock.rtd_Stock, 0) > 0,
                                    (((so_trans.qty -  so_trans.dispatch_qty) - ((IFNULL(prc_master.wip_qty, 0)-(IFNULL(stock.prd_finish_Stock, 0)+IFNULL(stock.rtd_Stock, 0) + so_trans.dispatch_qty)))) +  IFNULL(stock.prd_finish_Stock, 0) +IFNULL(stock.rtd_Stock, 0)) * item_kit.qty,
                                    0)) AS required_material,
                            IFNULL(rmStock.rm_stock,0) AS rm_stock,
                            IFNULL(po_trans.pending_po,0) AS pending_po,
                            IFNULL(grn_trans.pending_grn,0) AS pending_grn,
                            IFNULL(purchase_indent.pending_req,0) AS pending_req";

        $data['join']['item_kit'] = "item_kit.item_id = so_trans.item_id AND item_kit.ref_id = 0 AND item_kit.alt_item_id = 0 AND item_kit.is_delete = 0";
        $data['leftJoin']['item_master'] = "item_master.id = item_kit.ref_item_id";
        $data['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id ";
        //prc_master
		$data['leftJoin']['(SELECT SUM(prc_qty) as wip_qty,item_id  
                                FROM prc_master 
                                WHERE is_delete = 0 AND prc_type = 1
                                GROUP BY item_id
                            ) prc_master'] = 'prc_master.item_id = so_trans.item_id';
        //stock_trans FG STOCK
		$data['leftJoin']['(SELECT SUM(CASE WHEN location_id = '.$this->PACKING_STORE->id.' THEN (qty*p_or_m) ELSE 0 END) AS prd_finish_Stock,
                                SUM(CASE WHEN location_id = '.$this->RTD_STORE->id.' THEN (qty*p_or_m) ELSE 0 END) AS rtd_Stock,item_id 
                                FROM stock_trans 
                                WHERE is_delete = 0 AND 
                                location_id IN('.$this->PACKING_STORE->id.','.$this->RTD_STORE->id.') 
                                GROUP BY item_id
                            ) stock'] = 'stock.item_id = so_trans.item_id';
        //stock_trans RM STOCK
        $data['leftJoin']['(SELECT SUM((qty*p_or_m)) AS rm_stock,item_id
                                FROM stock_trans 
                                WHERE is_delete = 0
                                GROUP BY item_id
                            ) rmStock '] = 'rmStock.item_id = item_kit.ref_item_id';
        // po_trans PENDING PO
        $data['leftJoin']['(SELECT SUM(qty - dispatch_qty) AS pending_po,item_id
                                FROM po_trans 
                                WHERE is_delete = 0 AND po_trans.trans_status = 0
                                GROUP BY item_id
                            ) po_trans'] = 'po_trans.item_id = item_kit.ref_item_id ';
        //grn_trans PENDING GRN
        $data['leftJoin']['(SELECT SUM(qty) AS pending_grn,item_id
                                FROM grn_trans 
                                WHERE is_delete = 0 AND grn_trans.trans_status =1
                                GROUP BY item_id
                            ) grn_trans'] = 'grn_trans.item_id = item_kit.ref_item_id    ';
        //purchase_indent PENDING REQ
        $data['leftJoin']['(SELECT SUM(qty) AS pending_req,item_id
                                FROM purchase_indent 
                                WHERE is_delete = 0 AND purchase_indent.order_status =1
                                GROUP BY item_id
                            ) purchase_indent'] = 'purchase_indent.item_id = item_kit.ref_item_id';

		$data['group_by'][]='item_kit.ref_item_id';
		$data['having'][]='(total_qty - total_dispatch_qty) > 0';
		$data['having'][]='(required_material - (rm_stock + pending_req + pending_po + pending_grn)) > 0';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "so_master.trans_number";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }*/

    public function getForecastDtRows($data){
		$style = 'style="margin:0px;padding:0px"';
        $data['tableName'] = "so_trans";
		$data['select'] = "`item_kit`.`ref_item_id`,so_trans.item_id,fg_master.item_name as fg_item_name, `item_master`.`item_name`, `item_master`.`item_code`,
                            (so_master.trans_number) AS so_number, 
                            (so_trans.id ) AS so_trans_id, 
                            (so_trans.qty) AS so_qty, 
                            so_trans.dispatch_qty, 
                            IFNULL(stock.prd_finish_Stock, 0) AS prd_finish_Stock, 
                            IFNULL(stock.rtd_Stock, 0) AS rtd_Stock, 
                            IFNULL(prc_master.wip_qty, 0) AS wip_qty, 
                            (
                                IF(
                                    ((so_trans.qty -  so_trans.dispatch_qty) - ((IFNULL(prc_master.wip_qty, 0)-(IFNULL(stock.prd_finish_Stock, 0)+IFNULL(stock.rtd_Stock, 0) + so_trans.dispatch_qty)))) +  IFNULL(stock.prd_finish_Stock, 0) +IFNULL(stock.rtd_Stock, 0) > 0,
                                    ((so_trans.qty -  so_trans.dispatch_qty) - ((IFNULL(prc_master.wip_qty, 0)-(IFNULL(stock.prd_finish_Stock, 0)+IFNULL(stock.rtd_Stock, 0) + so_trans.dispatch_qty)))) +  IFNULL(stock.prd_finish_Stock, 0) +IFNULL(stock.rtd_Stock, 0),
                                    0)) AS shortage_qty, 
                            (
                               IF(
                                    ((so_trans.qty -  so_trans.dispatch_qty) - ((IFNULL(prc_master.wip_qty, 0)+(so_trans.dispatch_qty))))  > 0,
                                    (((so_trans.qty -  so_trans.dispatch_qty) - ((IFNULL(prc_master.wip_qty, 0)+(so_trans.dispatch_qty))))) * item_kit.qty,
                                    0)) AS required_material,

                            IFNULL(rmStock.rm_stock,0) AS rm_stock,
                            IFNULL(po_trans.pending_po,0) AS pending_po,
                            IFNULL(grn_trans.pending_grn,0) AS pending_grn,item_master.uom,item_master.gst_per,so_trans.price,item_category.category_name,item_master.item_type";
                            

        $data['join']['item_kit'] = "item_kit.item_id = so_trans.item_id AND item_kit.ref_id = 0 AND item_kit.alt_item_id = 0 AND item_kit.packing_type = 0 AND item_kit.is_delete = 0";
        $data['leftJoin']['item_master'] = "item_master.id = item_kit.ref_item_id";
		$data['leftJoin']['item_master fg_master'] = "fg_master.id = so_trans.item_id";
        $data['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id ";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.item_type"; 

		$data['leftJoin']['(SELECT SUM(prc_qty) as wip_qty,so_trans_id  
                                FROM prc_master 
                                WHERE is_delete = 0 AND prc_type = 1
                                GROUP BY so_trans_id
                            ) prc_master'] = 'prc_master.so_trans_id = so_trans.id';

        //stock_trans FG STOCK
		$data['leftJoin']['(SELECT SUM(CASE WHEN location_id = '.$this->PACKING_STORE->id.' THEN (qty*p_or_m) ELSE 0 END) AS prd_finish_Stock,
                                SUM(CASE WHEN location_id = '.$this->RTD_STORE->id.' THEN (qty*p_or_m) ELSE 0 END) AS rtd_Stock,item_id 
                                FROM stock_trans 
                                WHERE is_delete = 0 AND 
                                location_id IN('.$this->PACKING_STORE->id.','.$this->RTD_STORE->id.') 
                                GROUP BY item_id
                            ) stock'] = 'stock.item_id = so_trans.item_id';
        
		//stock_trans RM STOCK
        $data['leftJoin']['(SELECT 
								SUM((stock_trans.qty*stock_trans.p_or_m)) AS rm_stock,stock_trans.item_id,batch_history.fg_item_id
							FROM 
								stock_trans 
							LEFT JOIN batch_history ON stock_trans.item_id = batch_history.item_id AND stock_trans.batch_no = batch_history.batch_no AND batch_history.is_delete = 0
							WHERE 
								stock_trans.is_delete = 0
                            GROUP BY stock_trans.item_id,batch_history.fg_item_id) rmStock '] = 'rmStock.item_id = item_kit.ref_item_id AND rmStock.fg_item_id = so_trans.item_id';
        
		// po_trans PENDING PO
        $data['leftJoin']['(SELECT 
								SUM(qty - dispatch_qty) AS pending_po,so_trans_id
                            FROM 
								po_trans 
                            WHERE 
								is_delete = 0 AND po_trans.trans_status IN (0,3)
                            GROUP BY 
							so_trans_id) po_trans'] = 'po_trans.so_trans_id = so_trans.id ';
        
		//grn_trans PENDING GRN
        $data['leftJoin']['(SELECT SUM(qty) AS pending_grn,so_trans_id
                                FROM grn_trans 
                                WHERE is_delete = 0 AND grn_trans.trans_status =1
                                GROUP BY so_trans_id
                            ) grn_trans'] = 'grn_trans.so_trans_id = so_trans.id';

		$data['group_by'][]='so_trans.id';

		$data['where']['so_trans.trans_status !='] = 2;
		
		if(!empty($data['status']) && $data['status'] == 2){
			$data['having'][]='(so_qty - dispatch_qty) > 0';
			$data['having'][]='(required_material - (rm_stock + pending_po + pending_grn)) <= 0';
		}else{
			$data['having'][]='(so_qty - dispatch_qty) > 0';
			$data['having'][]='(required_material - (rm_stock + pending_po + pending_grn)) > 0';
		}
		
		$data['order_by']['so_master.trans_date']='DESC';
		$data['order_by']['so_master.id']='DESC';
		
        if(!empty($data['rowData'])){
            if(!empty($data['so_ids'])){
                $data['where_in']['so_trans.id'] = str_replace("~", ",", $data['so_ids']);
            }
            $result = $this->rows($data);
        }else{
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
			$data['searchCol'][] = "CONCAT(item_master.item_code, ' ', item_master.item_name)"; 
            $data['searchCol'][] = "so_master.trans_number";
            $data['searchCol'][] = "fg_master.item_name";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
         
    
            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            $result = $this->pagingRows($data);
        }
        return $result;
    }

    /******************************** */
    /** 
     * This Function will hepls to find Finish good requirement current to 3 month
     * Find Total Requirement , Buffre stock, Wh NOrms Month wise
     * Select WIP Qty, Packing Area,RTD ,Pending PRC 
     */
    public function getFGReqRows(){
		$style = 'style="margin:0px;padding:0px"';
        $data['tableName'] = "so_trans";
		$data['select'] = "so_trans.item_id,
							IFNULL(stock.prd_finish_Stock,0) AS prd_finish_Stock,
							IFNULL(stock.rtd_Stock,0) AS rtd_Stock,
							IFNULL(prc_master.wip_qty, 0) as wip_qty,
                            SUM(so_trans.qty) AS order_qty,
                            SUM(so_trans.dispatch_qty) AS dispatch_qty";
        $data['leftJoin']['item_master'] = "item_master.id = so_trans.item_id";
        
        /** Join to find WIP quantity. */
		$data['leftJoin']['(SELECT SUM(prc_qty - (prc_detail.rej_qty + prc_detail.stored_qty)) as wip_qty,item_id  
								FROM prc_master 
								JOIN prc_detail ON prc_detail.prc_id = prc_master.id
								WHERE 
								prc_master.is_delete = 0 
								AND prc_type = 1 
								AND prc_master.status IN(1,2) 
								GROUP BY item_id
							) prc_master'] = 'prc_master.item_id = item_master.id';
        /** Join to find Packing Area & RTD Stock quantity. */
		$data['leftJoin']['(SELECT SUM(CASE WHEN location_id = '.$this->PACKING_STORE->id.' THEN (qty*p_or_m) ELSE 0 END) AS prd_finish_Stock,
								   SUM(CASE WHEN location_id = '.$this->RTD_STORE->id.' THEN (qty*p_or_m) ELSE 0 END) AS rtd_Stock,item_id 
								   FROM stock_trans 
								   WHERE is_delete = 0 
								   AND location_id IN('.$this->PACKING_STORE->id.','.$this->RTD_STORE->id.') 
								   GROUP BY item_id
							) stock'] = 'stock.item_id = item_master.id';
        

        $data['where_in']['so_trans.trans_status'] = 3;
		$data['group_by'][]='so_trans.item_id';
        $data['having'][]='(order_qty - dispatch_qty) > 0';
    

        if(!empty($data['result_type'])){
            $result = $this->getData($data,$data['result_type']);
        }else{
            $result = $this->rows($data);
        }
        return $result;
    }

    /**
     * PRC generate thy gyu hoy material issue krvanu baki hoy te req. find krva mate
     */
    public function getPendingPrcRm($param){
        $queryData['tableName'] = 'prc_master';
        $queryData['select'] = 'prc_master.item_id,item_kit.ref_item_id,IFNULL(issueReg.issue_qty,0) AS issue_qty,SUM((prc_master.prc_qty * item_kit.qty) - IFNULL(issueReg.issue_qty,0)) AS req_qty';
        $queryData['leftJoin']['item_kit'] = 'item_kit.item_id = prc_master.item_id AND item_kit.is_delete = 0 AND item_kit.ref_id = 0';
        $queryData['leftJoin']['(SELECT SUM(issue_register.issue_qty) AS issue_qty,prc_id,item_id FROM issue_register WHERE issue_type = 2 AND is_delete = 0 GROUP BY prc_id,item_id) issueReg'] = 'prc_master.id = issueReg.prc_id AND item_kit.ref_item_id = issueReg.item_id';

        $queryData['where_in']['prc_master.item_id'] = $param['item_id'];
        $queryData['where_in']['prc_master.status'] = '1,2';
        $queryData['having'][] = 'req_qty > 0';
        $queryData['group_by'][] = 'prc_master.item_id,item_kit.ref_item_id';
        return $this->rows($queryData);
    }

    /** 
     * This function will hepls to find RM Stock, Pending GRN & Pending QC
     */
    public function getRMStock($data){
        $data['tableName'] = "item_master";
        $data['select'] = 'item_master.id AS item_id,item_master.item_code,item_master.item_name,
                           IFNULL(mt_stock.material_stock,0) AS material_stock,
                           IFNULL(po_trans.pending_po,0) AS pending_po,
                           IFNULL(grn_trans.pending_grn,0) AS pending_grn';
         /** BOM Material Stock  */
        $data['leftJoin']['(SELECT SUM(qty*p_or_m) AS material_stock,item_id 
								   FROM stock_trans 
								   WHERE is_delete = 0
								   GROUP BY item_id
							) mt_stock'] = 'mt_stock.item_id = item_master.id';
        // po_trans PENDING PO
        $data['leftJoin']['(SELECT SUM(qty - dispatch_qty) AS pending_po,item_id
                                FROM po_trans 
                                WHERE is_delete = 0 AND po_trans.trans_status = 0
                                GROUP BY item_id
                            ) po_trans'] = 'po_trans.item_id = item_master.id';
        //grn_trans PENDING GRN
        $data['leftJoin']['(SELECT SUM(qty) AS pending_grn,item_id
                                FROM grn_trans 
                                WHERE is_delete = 0 AND grn_trans.trans_status =1
                                GROUP BY item_id
                            ) grn_trans'] = 'grn_trans.item_id = item_master.id';
        $data['where_in']['item_master.id'] = $data['item_id'];
       
        $result = $this->rows($data);
        return $result;

    }
}
