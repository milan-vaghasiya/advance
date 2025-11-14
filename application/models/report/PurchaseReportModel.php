<?php 
class PurchaseReportModel extends MasterModel
{
    private $po_master = "po_master";
    private $po_trans = "po_trans";
    private $grn_master = "grn_master";
    private $grn_trans = "grn_trans";
	private $purchase_enquiry = "purchase_enquiry";
    private $purchase_quotation = "purchase_quotation";

    public function getPurchaseOrderMonitoring($data){
        $queryData = array();
		$queryData['tableName'] = $this->po_trans;
		$queryData['select'] = 'po_trans.*,po_trans.po_id,po_master.trans_date,item_master.item_name,party_master.party_name,po_master.trans_number,po_master.remark';
        $queryData['join']['po_master'] = "po_master.id = po_trans.po_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = po_trans.item_id";
		$queryData['leftJoin']['party_master'] = 'party_master.id = po_master.party_id';
		if(!empty($data['item_type'])){
            $queryData['where']['item_master.item_type'] = $data['item_type'];
        }
		if(!empty($data['party_id'])){
            $queryData['where']['po_master.party_id'] = $data['party_id'];
        }
		if(isset($data['status']) && $data['status'] !== ''){
            $queryData['where_in']['po_trans.trans_status'] = $data['status'];
        }
        $queryData['customWhere'][] = "po_master.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['po_master.trans_date'] = 'ASC';
		$result = $this->rows($queryData);
        return $result;
    }

    public function getPurchaseReceipt($data){
        $queryData = array();
		$queryData['tableName'] = $this->grn_trans;
		$queryData['select'] = 'grn_master.trans_date,grn_master.trans_no,grn_master.trans_prefix,grn_master.trans_number,grn_master.doc_date,grn_master.doc_no,grn_trans.qty';
		$queryData['leftJoin']['grn_master'] = 'grn_master.id = grn_trans.grn_id';
		$queryData['where']['grn_trans.item_id'] = $data['item_id'];
		$queryData['where']['grn_trans.po_id'] = $data['po_id'];
		$queryData['where']['grn_trans.po_trans_id'] = $data['po_trans_id'];
		$queryData['order_by']['grn_master.trans_date'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
    }

    public function getPurchaseInward($data){
        $queryData = array();
		$queryData['tableName'] = $this->grn_trans;
		$queryData['select'] = 'grn_master.trans_date,grn_master.trans_number,grn_master.doc_no,grn_trans.qty,party_master.party_name,item_master.item_name,po_master.trans_number as po_number,po_master.trans_date as po_date,grn_trans.price';
		$queryData['leftJoin']['grn_master'] = 'grn_master.id = grn_trans.grn_id';
        $queryData['leftJoin']['item_master'] = 'item_master.id = grn_trans.item_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = grn_master.party_id';
		$queryData['leftJoin']['po_master'] = 'po_master.id = grn_trans.po_id';
		$queryData['leftJoin']['po_trans'] = 'po_trans.id = grn_trans.po_trans_id';
        if(!empty($data['item_type'])){
            $queryData['where']['item_master.item_type'] = $data['item_type'];
        }
        $queryData['customWhere'][] = "grn_master.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$result = $this->rows($queryData);
		return $result;
    }

	public function getEnquiryRegisterData($data){
        $queryData = array();
		$queryData['tableName'] = $this->purchase_enquiry;
		$queryData['select'] = 'purchase_enquiry.*,party_master.party_name,purchase_quotation.quote_no,purchase_quotation.quote_date,purchase_quotation.qty as qtnQty,purchase_quotation.price,purchase_quotation.feasible,purchase_quotation.lead_time,employee_master.emp_name,purchase_quotation.approve_date';
		$queryData['leftJoin']['party_master'] = 'party_master.id = purchase_enquiry.party_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = purchase_enquiry.item_id';
		$queryData['leftJoin']['purchase_quotation'] = "purchase_enquiry.id = purchase_quotation.enq_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = purchase_quotation.approve_by";
		if(!empty($data['party_id'])){
            $queryData['where']['purchase_enquiry.party_id'] = $data['party_id'];
        }
        if(!empty($data['item_id'])){
            $queryData['where']['purchase_enquiry.item_id'] = $data['item_id'];
        }
        $queryData['customWhere'][] = "purchase_enquiry.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['purchase_enquiry.trans_date'] = 'ASC';
		$result = $this->rows($queryData);
        return $result;
    }

	/* Supplier Wise Item Report created by rashmi : 15/05/2024 */
    public function getSupplierWiseItem($data){
        $queryData = array();
		$queryData['tableName'] = $this->grn_trans;
		$queryData['select'] = 'grn_master.party_id,grn_trans.item_id,party_master.party_name,item_master.item_name,item_master.item_code';
		$queryData['leftJoin']['grn_master'] = 'grn_master.id = grn_trans.grn_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = grn_master.party_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = grn_trans.item_id';
		if(!empty($data['item_id'])){$queryData['where']['grn_trans.item_id'] = $data['item_id'];}
		if(!empty($data['party_id'])){$queryData['where']['grn_master.party_id'] = $data['party_id'];}
        $queryData['group_by'][] = 'grn_master.party_id';
        $queryData['group_by'][] = 'grn_trans.item_id';
		$result = $this->rows($queryData);
        return $result;
    }

	public function getMarketTrendData($data){
		$queryData = array();          
		$queryData['tableName'] = $this->po_master;
		$queryData['select'] = 'po_master.id,
								po_master.trans_date,
								item_master.item_name,
									(select 
										CASE
											WHEN MONTH(po_master.trans_date) = 4 THEN SUM(pot.price)/count(pot.id)
											ELSE "-"
										END
									from po_trans pot
									where po_master.id = pot.po_id) as april,
									(select 
										CASE
											WHEN MONTH(po_master.trans_date) = 5 THEN SUM(pot.price)/count(pot.id)
											ELSE "-"
										END
									from po_trans pot
									where po_master.id = pot.po_id) as may,
									(select 
										CASE
											WHEN MONTH(po_master.trans_date) = 6 THEN SUM(pot.price)/count(pot.id)
											ELSE "-"
										END
									from po_trans pot
									where po_master.id = pot.po_id) as june,
									(select 
										CASE
											WHEN MONTH(po_master.trans_date) = 7 THEN SUM(pot.price)/count(pot.id)
											ELSE "-"
										END
									from po_trans pot
									where po_master.id = pot.po_id) as july,
									(select 
										CASE
											WHEN MONTH(po_master.trans_date) = 8 THEN SUM(pot.price)/count(pot.id)
											ELSE "-"
										END
									from po_trans pot
									where po_master.id = pot.po_id) as august,
									(select 
										CASE
											WHEN MONTH(po_master.trans_date) = 9 THEN SUM(pot.price)/count(pot.id)
											ELSE "-"
										END
									from po_trans pot
									where po_master.id = pot.po_id) as september,
									(select 
										CASE
											WHEN MONTH(po_master.trans_date) = 10 THEN SUM(pot.price)/count(pot.id)
											ELSE "-"
										END
									from po_trans pot
									where po_master.id = pot.po_id) as october,
									  (select 
										CASE
											WHEN MONTH(po_master.trans_date) = 11 THEN SUM(pot.price)/count(pot.id)
											ELSE "-"
										END
									from po_trans pot
									where po_master.id = pot.po_id) as november,
									(select 
										CASE
											WHEN MONTH(po_master.trans_date) = 12 THEN SUM(pot.price)/count(pot.id)
											ELSE "-"
										END
									from po_trans pot
									where po_master.id = pot.po_id) as december,
									(select 
										CASE
											WHEN MONTH(po_master.trans_date) = 1 THEN SUM(pot.price)/count(pot.id)
											ELSE "-"
										END
									 from po_trans pot
									where po_master.id = pot.po_id) as january,
									(select 
										CASE
											WHEN MONTH(po_master.trans_date) = 2 THEN SUM(pot.price)/count(pot.id)
											ELSE "-"
										END
									 from po_trans pot
									where po_master.id = pot.po_id) as february,
									(select 
										CASE
											WHEN MONTH(po_master.trans_date) = 3 THEN SUM(pot.price)/count(pot.id)
											ELSE "-"
										END
									from po_trans pot
									where po_master.id = pot.po_id) as march';
		$queryData['leftJoin']['po_trans'] = 'po_master.id = po_trans.po_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = po_trans.item_id';
		$queryData['where']['po_master.is_delete'] = 0;
		$result = $this->rows($queryData);
		return $result;
	}

	/* GRN Incoming Rejection/Short Qty Report */
	public function getIncomingRejection($data){
        $queryData = array();
		$queryData['tableName'] = $this->grn_trans;
		$queryData['select'] = 'grn_trans.*,grn_master.trans_date,grn_master.trans_number,party_master.party_name,item_master.item_name';
		$queryData['leftJoin']['grn_master'] = 'grn_master.id = grn_trans.grn_id';
        $queryData['leftJoin']['item_master'] = 'item_master.id = grn_trans.item_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = grn_master.party_id';
		
		if (!empty($data['item_id'])) { $queryData['where']['grn_trans.item_id'] = $data['item_id']; }

		if (!empty($data['party_id'])) { $queryData['where']['grn_master.party_id'] = $data['party_id']; }

        if (!empty($data['from_date']) && !empty($data['to_date'])) { $queryData['customWhere'][] = "grn_master.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'"; }
        
		if (!empty($data['type']) && $data['type'] == 'REJECT') { $queryData['where']['grn_trans.reject_qty >'] = 0; }
		elseif (!empty($data['type']) && $data['type'] == 'SHORT') { $queryData['where']['grn_trans.short_qty >'] = 0; }
		else { $queryData['customWhere'][] = "(grn_trans.reject_qty OR grn_trans.short_qty) > 0"; }

		$result = $this->rows($queryData);
		return $result;
    }

	/* NCR Report*/
    public function getSupplierNCRData($data){
        $queryData = array();
        $queryData['tableName'] = 'ncr_master';
        $queryData['select'] = "ncr_master.trans_number,ncr_master.trans_date,,item_master.item_name,party_master.party_name,ncr_master.complaint,ncr_master.report_no,ncr_master.qty,ncr_master.ref_feedback,ncr_master.remark,ncr_master.challan_no,ncr_master.rej_qty,ncr_master.batch_no,ncr_master.product_returned,ncr_master.ref_of_complaint,(CASE WHEN ncr_master.ncr_type=1 THEN grn_master.doc_no ELSE prc_log.in_challan_no END) as challan_no";
        $queryData['leftJoin']['party_master'] = "ncr_master.party_id = party_master.id";
        $queryData['leftJoin']['item_master'] = "ncr_master.item_id = item_master.id";
        $queryData['leftJoin']['grn_trans'] = "grn_trans.id = ncr_master.grn_trans_id AND ncr_master.ncr_type=1";
        $queryData['leftJoin']['grn_master'] = "grn_master.id = grn_trans.grn_id";
        $queryData['leftJoin']['prc_log'] = "prc_log.id = ncr_master.grn_trans_id AND ncr_master.ncr_type=2";

        $queryData['customWhere'][] = "ncr_master.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        if(!empty($data['party_id'])):
            $queryData['where']['ncr_master.party_id'] = $data['party_id'];
        endif;
        if(!empty($data['item_id'])):
            $queryData['where']['ncr_master.item_id'] = $data['item_id'];
        endif;
        $queryData['order_by']['ncr_master.trans_date'] = "ASC";

        $result = $this->rows($queryData);
        return $result;
    }

	/* Purchase Price Revision History */
	public function getPurchasePriceData($data){
        $queryData = array();
		$queryData['tableName'] = $this->po_trans;
		$queryData['select'] = 'po_trans.price,po_master.trans_date,po_master.trans_number,party_master.party_name,item_master.item_name,item_master.item_code';
        $queryData['leftJoin']['po_master'] = "po_master.id = po_trans.po_id";
		$queryData['leftJoin']['party_master'] = 'party_master.id = po_master.party_id';
        $queryData['leftJoin']['item_master'] = "item_master.id = po_trans.item_id";
		$queryData['where_in']['po_trans.trans_status'] = '1,3';

		if (!empty($data['party_id'])) {
            $queryData['where']['po_master.party_id'] = $data['party_id'];
        }
		
		if (!empty($data['item_id'])) {
            $queryData['where']['po_trans.item_id'] = $data['item_id'];
        }

        $queryData['customWhere'][] = "po_master.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";

		$queryData['order_by']['po_trans.price'] = $data['order_by'];
		$queryData['order_by']['po_master.trans_date'] = $data['order_by'];

		$result = $this->rows($queryData);
        return $result;
    }

	/* Purchase Price Revision History */
	public function getJobworkPriceData($data){
        $queryData = array();
		$queryData['tableName'] = 'outsource';
		$queryData['select'] = 'outsource.ch_date as trans_date,outsource.ch_number as trans_number,party_master.party_name,item_master.item_name,item_master.item_code,prc_challan_request.price,process_master.process_name';
		$queryData['leftJoin']['party_master'] = 'party_master.id = outsource.party_id';
        $queryData['leftJoin']['prc_challan_request'] = "prc_challan_request.challan_id = outsource.id";
        $queryData['leftJoin']['prc_master'] = "prc_master.id = prc_challan_request.prc_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = prc_master.item_id";
        $queryData['leftJoin']['process_master'] = "process_master.id = prc_challan_request.process_id";

		if (!empty($data['party_id'])) {
            $queryData['where']['outsource.party_id'] = $data['party_id'];
        }
		
		if (!empty($data['item_id'])) {
            $queryData['where']['prc_master.item_id'] = $data['item_id'];
        }

		if (!empty($data['process_id'])) {
            $queryData['where']['prc_challan_request.process_id'] = $data['process_id'];
        }

        $queryData['customWhere'][] = "outsource.ch_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";

		$queryData['order_by']['prc_challan_request.price'] = $data['order_by'];
		$queryData['order_by']['outsource.ch_date'] = $data['order_by'];

		$result = $this->rows($queryData);
        return $result;
    }
}
?>