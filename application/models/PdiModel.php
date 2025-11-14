<?php
class PdiModel extends MasterModel{

    public function getDTRows($data){
        $entryData = $this->transMainModel->getEntryType(['controller'=>'salesInvoice']);
        $data['tableName'] = 'trans_child';
		$data['select'] = "trans_child.id,trans_child.item_id,trans_child.qty,trans_child.trans_status,trans_number,trans_date,party_master.party_name,party_master.party_code,item_master.item_name,item_master.item_code,party_master.pdi_type,party_master.party_logo";

        $data['leftJoin']['trans_main'] = 'trans_main.id = trans_child.trans_main_id';
        $data['leftJoin']['party_master'] = 'party_master.id = trans_main.party_id';
		$data['leftJoin']['item_master'] = 'item_master.id = trans_child.item_id';

        $data['where']['trans_main.entry_type'] = $entryData->id;
        $data['where']['trans_child.trans_status'] = $data['status'];
		
		//51-TOOL COST CATEGORY
        $data['where']['item_master.category_id !='] = 51;

        if($data['status'] == 3){
            $data['where']['trans_main.trans_date >='] = $this->startYearDate;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        }
       
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "CONCAT(item_master.item_code,' ',item_master.item_name)";
        $data['searchCol'][] = "trans_child.qty";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function save($data){
		try{
            $this->db->trans_begin();

    		foreach($data['batch_no'] AS $key=>$batch_no){
                $pdiInsert = [
                    'id'=>$data['id'][$key],
                    'so_id'=>$data['so_id'],
                    'so_trans_id'=>$data['so_trans_id'],
                    'fir_id'=>$data['firdata'][$batch_no]['fir_id'],
                    'batch_no'=>$batch_no,
                    'rm_batch'=>$data['firdata'][$batch_no]['rm_batch'],
                    'qty'=>$data['qty'][$key],
                ];
                $this->store("pdi_trans",$pdiInsert);
				
				if(!empty($data['firdata'][$batch_no]['fir_id'])){
					$this->edit("production_inspection",['id'=>$data['firdata'][$batch_no]['fir_id']],['pdi_status'=>1]);
				}
            }
            $result = $this->store('trans_child',['id'=>$data['so_trans_id'],'trans_status'=>3]);
			
    		if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}

    public function getPackingDetail_old($data){
        $queryData['tableName'] = "packing_master";
        $queryData['select'] = 'production_inspection.*,packing_master.trans_number AS pack_batch,stock_trans.batch_no,stock_trans.qty,prc_master.batch_no AS rm_batch,prc_log.qty as ok_qty';
        $queryData['leftJoin']['stock_trans'] = "stock_trans.trans_type = 'PCK' AND stock_trans.p_or_m = '-1' AND stock_trans.main_ref_id = packing_master.id AND stock_trans.is_delete = 0";
        $queryData['leftJoin']['prc_master'] = 'prc_master.prc_number = stock_trans.batch_no AND prc_master.is_delete = 0';
        $queryData['leftJoin']['production_inspection'] = 'prc_master.id = production_inspection.prc_id AND production_inspection.is_delete = 0 AND production_inspection.report_type = 2';
        $queryData['leftJoin']['prc_log'] = 'prc_log.id = production_inspection.ref_id AND prc_log.is_delete = 0 AND production_inspection.report_type = 2';

        $queryData['where_in']['packing_master.trans_number'] = ("'".implode("','",$data['batch_no'])."'");
		$queryData['where']['production_inspection.pdi_status'] = 0;        
        return $this->rows($queryData);

    }

	public function getPackingDetail($data){
        $queryData['tableName'] = "prc_master";
        $queryData['select'] = 'production_inspection.*,prc_master.prc_number AS pack_batch,prc_master.batch_no AS rm_batch,prc_log.qty as ok_qty';
        $queryData['leftJoin']['production_inspection'] = 'prc_master.id = production_inspection.prc_id AND production_inspection.is_delete = 0 AND production_inspection.report_type = 2';
        $queryData['leftJoin']['prc_log'] = 'prc_log.id = production_inspection.ref_id AND prc_log.is_delete = 0 AND production_inspection.report_type = 2';

        $queryData['where_in']['prc_master.prc_number'] = ("'".implode("','",$data['batch_no'])."'");
		//$queryData['where']['production_inspection.pdi_status'] = 0;        
        return $this->rows($queryData);
    }

    public function getPdiReportData($data){
        $queryData['tableName'] = "pdi_trans";
        $queryData['select'] = 'pdi_trans.*,ecn_master.drw_no';
        $queryData['leftJoin']['production_inspection'] = "production_inspection.id = pdi_trans.id";
        $queryData['leftJoin']['ecn_master'] = "production_inspection.rev_no = ecn_master.rev_no AND ecn_master.item_id = production_inspection.item_id AND ecn_master.is_delete = 0 ";

        if(!empty($data['so_trans_id'])){
            $queryData['where']['pdi_trans.so_trans_id'] = $data['so_trans_id'];
        }

        if(!empty($data['invDetail'])){
            $queryData['select'] .= ',trans_main.trans_number,trans_main.trans_no,trans_main.trans_date,so_master.doc_no,so_master.doc_date,trans_main.party_name,trans_child.item_id,trans_child.item_name,item_master.item_code,item_master.grade_id,material_master.material_grade,item_master.mfg_type,tc_specification.forging_prc,tc_specification.dimensional_insp,tc_specification.visual_insp,tc_specification.note,tc_specification.remark,tc_specification.special_req';
			$queryData['select'] .= ' ,(SELECT GROUP_CONCAT(process_master.process_name separator ", ") FROM product_process left join process_master on process_master.id = product_process.process_id WHERE product_process.item_id = trans_child.item_id && process_master.is_ht = 1 && product_process.is_delete = 0) as heat_treatment,ecn.drw_no as drg_no';
			$queryData['select'] .= ' ,(SELECT GROUP_CONCAT(process_master.process_name separator ", ") FROM product_process left join process_master on process_master.id = product_process.process_id WHERE product_process.item_id = trans_child.item_id && process_master.is_surface = 1) as surface';
			$queryData['leftJoin']['trans_main'] = "trans_main.id = pdi_trans.so_id";
            $queryData['leftJoin']['trans_child'] = "trans_child.id = pdi_trans.so_trans_id";
            $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
            $queryData['leftJoin']['material_master'] = "material_master.id = item_master.grade_id";
			$queryData['leftJoin']['tc_specification'] = "tc_specification.item_id = item_master.id";
			$queryData['leftJoin']['so_trans'] = "so_trans.id = trans_child.ref_id";
			$queryData['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id";
			$queryData['leftJoin']['ecn_master ecn'] = "production_inspection.rev_no = ecn_master.rev_no AND ecn.item_id = trans_child.item_id AND ecn.is_delete = 0";
        }
        if(!empty($data['so_id'])){
            $queryData['where']['pdi_trans.so_id'] = $data['so_id'];
        }

        if(!empty($data['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
    }

	public function printPDIR($postData){
		$data['firData'] = $firData = $this->sop->getFinalInspectData(['id'=>$postData['id'],'type'=>1]);
        $data['paramData'] =  $this->item->getInspectionParameter(['item_id'=>$firData->item_id,'control_method'=>'FIR','rev_no'=>$firData->rev_no]);
        $data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
		if(!empty($postData['party_logo'])){
			$data['logo'] = $logo = base_url('assets/uploads/pdi_format/'.$postData['party_logo']);
		}else{			
			$data['logo'] = $logo = base_url('assets/images/logo.png'); 
		}
		$data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$view_pdi = 'pdi/pdir_pdf'.(!empty($postData['pdi_report_type']) ? '_'.$postData['pdi_report_type'] : '');
		
		
		$pdfData = $this->load->view($view_pdi,$data,true);
		
		$headerSize = 53;$footerSize = 25;
		if(empty($postData['pdi_report_type'])){
			$headerSize = 63;$footerSize = 35;
			$htmlHeader = '<table class="table item-list-bb">
				<tr>
					<td class="text-center" rowspan="3" style="width:20%;"><img src="'.$logo.'" style="height:50px;"></td>
					<td class="org_title text-center" rowspan="2" style="font-size:1.3rem;width:70%">PRE-DISPATCH INSPECTION REPORT</td>
					<td class="text-center" width="10%"><b>QA/F/02 (02/01.01.25)</b></td>
				</tr>
			</table>
			<table class="table item-list-bb" style="margin-top:2px;">
				<tr class="text-left">
					<th class="bg-light text-center" width="10%">Part Name</th>
					<td width="30%">'.((!empty($firData->item_name)) ? $firData->item_name : "").'</td>
					<th class="bg-light text-center" width="10%">Supplier Name</th>
					<td width="15%">'.((!empty($companyData->company_name)) ? $companyData->company_name : "").'</td>
					<th class="bg-light text-center" width="15%">PDI Report No. & Date</th>
					<td width="20%">'.(((!empty($firData->trans_number)) ? $firData->trans_number : "").' & '.((!empty($firData->inspection_date)) ? formatDate($firData->inspection_date) : "")).'</td>
				</tr>
				<tr class="text-left">
					<th class="bg-light text-center">Part No.</th>
					<td>'.((!empty($firData->item_code)) ? $firData->item_code : "").'</td>
					<th class="bg-light text-center">Cust. Part No.</th>
					<td>'.((!empty($firData->drw_no)) ? $firData->drw_no : "").'</td>
					<th class="bg-light text-center">DC No. & Date</th>
					<td>'.(((!empty($firData->inv_no)) ? $firData->inv_no : "").' & '.((!empty($firData->inv_date)) ? formatDate($firData->inv_date) : "")).'</td>
					
				</tr>
				<tr class="text-left">
					<th class="bg-light text-center">Drg.Rev. No.</th>
					<td>'.(!empty($firData->rev_no) ? $firData->rev_no : "").'</td>
					<th class="bg-light text-center">Batch No</th>
					<td>'.((!empty($firData->prc_number)) ? $firData->prc_number : "").((!empty($firData->rm_batch)) ? '('.$firData->rm_batch.')' : "").'</td>
					<th class="bg-light text-center">Material Grade</th>
					<td>'.((!empty($firData->material_grade)) ? $firData->material_grade : "").'</td>
				</tr>
				<tr class="text-left">
					<th class="bg-light text-center">Ok Qty</th>
					<td>'.((!empty($firData->pdi_qty)) ? floatval($firData->pdi_qty) : 0).'</td>
					<th class="bg-light text-center">Supplier Condition</th>
					<td colspan="3">'.((!empty($firData->mfg_type)) ? ($firData->mfg_type) : "").'</td>
				</tr>
			</table>';
			
			$htmlFooter = '<table class="table item-list-bb" style="border-bottom:1px solid #000000;">
				<tr>
					<td style="width:100%;" colspan="3"><b>Comments :-</b></td>
				</tr>
				<tr>
					<td><b>Dispatch Qty. :- </b>'.((!empty($firData->pdi_qty)) ? floatval($firData->pdi_qty) : 0).'</td>
					<td width="30%"><b>Inspector Name :- </b> '.$firData->emp_name.'</td>
					<td rowspan="2"><b>Approved by :-</b> '.$firData->approved_by.'</td>
				</tr>
				<tr>
					<td><b>Weight :- </b>'.((!empty($firData->pdi_qty)) ? (floatval($firData->pdi_qty) * $firData->wt_pcs) : 0).'</td>
					<td><b>Inspector Sign. :- </b></td>
				</tr>
			</table>
			<table class="table top-table" style="margin-top:10px;">
				<tr>
					<td style="width:25%;"></td>
					<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
				</tr>
			</table>';
		}
		else if($postData['pdi_report_type'] == "A12"){
			$headerSize = 50;$footerSize = 45;
			$htmlHeader = '<table class="table item-list-bb">
				<tr>
					<td class="text-center" rowspan="3" style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
					<td class="org_title text-center" rowspan="2" style="font-size:1.3rem;width:50%">ISIR-Initial Sample Inspection Report</td>
					<td class="text-left" style="width:15%"><b>Doc. No.: </b></td>
					<td class="text-center" style="width:10%">OIL_QA_IV-003 Rev.0 no.00</td>
				</tr>
				<tr>
					<td class="text-left"><b>Report no.: </b></td>
					<td class="text-center">'.$firData->trans_number.'</td>
				</tr>
			</table>
			<table class="table item-list-bb" style="margin-top:2px;">
				<tr class="text-left">
					<th class="bg-light text-center" width="10%">Part Number</th>
					<td width="15%">'.((!empty($firData->item_code)) ? $firData->item_code : "").'</td>
					<th class="bg-light text-center" width="15%">Rev No</th>
					<td width="20%">'.((!empty($firData->rev_no)) ? ($firData->rev_no) : "").'</td>
					<th class="bg-light text-center">Part Name</th>
					<td>'.((!empty($firData->item_name)) ? $firData->item_name : "").'</td>
				</tr>
				<tr class="text-left">
					<th class="bg-light text-center">Supplier Name</th>
					<td>'.((!empty($companyData->company_name)) ? $companyData->company_name : "").'</td>
					<th class="bg-light text-center">Delivery Note</th>
					<td>'.(!empty($firData->inv_no) ? $firData->inv_no : "").'</td>
					<th class="bg-light text-center">Date</th>
					<td>'.(!empty($firData->inv_date) ? formatDate($firData->inv_date) : "").'</td>
				</tr>
				<tr class="text-left">
					<th class="bg-light text-center">Delivery Qty.</th>
					<td>'.((!empty($firData->pdi_qty)) ? floatval($firData->pdi_qty) : 0).'</td>
					<th class="bg-light text-center">Insp Qty.</th>
					<td>'.((!empty($firData->pdi_qty)) ? floatval($firData->pdi_qty) : 0).'</td>
					<th class="bg-light text-center">Inspected By</th>
					<td>'.(!empty($firData->emp_name) ? $firData->emp_name : "").'</td>
				</tr>
				<tr class="text-left">
					<th class="bg-light text-center">Date</th>
					<td>'.((!empty($firData->inspection_date)) ? formatDate($firData->inspection_date) : "").'</td>
					<th class="bg-light text-center">ATL Part no.</th>
					<td colspan="3">'.((!empty($firData->item_code)) ? $firData->item_code : "").'</td>
				</tr>
			</table>';
			$htmlFooter = '<table class="table item-list-bb" style="border-bottom:1px solid #000000;">
				<tr>
					<th>Sample Weight:</th>
					<th class="text-center" rowspan="2">Decision</th>
					<th class="text-center" rowspan="2">Approved</th>
					<th class="text-center" rowspan="2">Rejection</th>
					<th class="text-center" rowspan="2">CONCLUSIONS-Notes</th>
				</tr>
				<tr>
					<td>Test Reports, Documents enclosed</td>
				</tr>
				<tr>
					<td>1.)</td>
					<td class="text-center">Dimensions</td>
					<td></td>
					<td></td>
					<td rowspan="4">Date <br/><br/> QA Manager</td>
				</tr>
				<tr>
					<td>2.)</td>
					<td class="text-center">Laboratory</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>3.)</td>
					<td class="text-center">Functionality</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>4.)</td>
					<td class="text-center">Appearance</td>
					<td></td>
					<td></td>
				</tr>
			</table>';
		}
		else if($postData['pdi_report_type'] == "A29"){
			$headerSize = 47; $footerSize = 85;
			$sample_size= (!empty($firData->sampling_qty))?floatval($firData->sampling_qty):5;
			$htmlHeader = '<table class="table item-list-bb">
				<tr>
					<td class="text-center" rowspan="3" style="width:20%;"><img src="'.$logo.'" style="height:50px;"></td>
					<td class="org_title text-center" rowspan="2" style="font-size:1.3rem;width:55%">PRE DESPATCH INSPECTION REPORT (PDIR)</td>
					<td class="text-left" style="width:15%"><b>Format Details :</b></td>
					<td class="text-center" colspan="2" style="width:10%">TSS/FR/613-B</td>
				</tr>
				<tr>
					<td class="text-left"><b>PDIR Rev:</b></td>
					<td class="text-center">1</td>
					<td class="text-center">15-Dec-2022</td>
				</tr>
			</table>
			<table class="table item-list-bb" style="margin-top:2px;">
				<tr class="text-left">
					<th class="bg-light text-center" width="10%">Supplier</th>
					<td width="40%">'.((!empty($companyData->company_name)) ? $companyData->company_name : "").'</td>
					<th class="bg-light text-center" width="10%">Customer</th>
					<td width="40%" colspan="3">'.((!empty($firData->party_name)) ? $firData->party_name : "").'</td>
				</tr>
				<tr class="text-left">
					<th class="bg-light text-center">Part Name</th>
					<td>'.((!empty($firData->item_name)) ? $firData->item_name : "").'</td>
					<th class="bg-light text-center">Date</th>
					<td>'.((!empty($firData->inspection_date)) ? formatDate($firData->inspection_date) : "").'</td>
					<th class="bg-light text-center">Invoice No</th>
					<td>'.(!empty($firData->inv_no) ? $firData->inv_no : "").'</td>
				</tr>
				<tr class="text-left">
					<th class="bg-light text-center">Part No / Rev.: </th>
					<td>'.(((!empty($firData->drw_no)) ? $firData->drw_no : "").' / '.((!empty($firData->rev_no)) ? ($firData->rev_no) : "")).'</td>
					<th class="bg-light text-center">Lot Size</th>
					<td>'.((!empty($firData->pdi_qty)) ? floatval($firData->pdi_qty) : 0).'</td>
					<th class="bg-light text-center">Sample Size</th>
					<td>'.((!empty($firData->sampling_qty)) ? floatval($firData->sampling_qty) : 0).'</td>
				</tr>
			</table>';
			$htmlFooter = '<table class="table item-list-bb" style="border-bottom:1px solid #000000;">
				<tr>
					<th class="text-left" style="width:100%;" colspan="'.($sample_size+3).'">VISUAL INSPECTION:</th>
				</tr>
				<tr>
					<td width="3%">1</td>
					<td width="60%"></td>';
					for($j=1;$j<=$sample_size;$j++):
						$htmlFooter .= '<td></td>';
					endfor;
			$htmlFooter .= '<td></td>
				</tr>
				<tr>
					<td width="3%">2</td>
					<td width="60%"></td>';
					for($j=1;$j<=$sample_size;$j++):
						$htmlFooter .= '<td></td>';
					endfor;
			$htmlFooter .= '<td></td>
				</tr>
				<tr>
					<th class="text-left" style="width:100%;" colspan="'.($sample_size+3).'">Supplier\'s\ Remarks:</th>
				</tr>
			</table>
            <table class="table item-list-bb">
				<tr>
					<th class="text-right" style="width:100%;" colspan="'.($sample_size+3).'">Accepted / Rejected / Accepted Under Deviation / Partially Accepted (Qty: Nos)</th>
				</tr>
				<tr>
					<th width="10%">Date: </th>
					<th width="27.5%"></th>
					<td width="30%" class="text-left"><b>Checked By:-</b> '.$firData->approved_by.'</td>
					<td width="30%" class="text-left"><b>Approved By:-</b> '.$firData->emp_name.'</td>
				</tr>
				<tr>
					<th class="text-left" style="width:100%;" colspan="'.($sample_size+3).'">Customer\'s\ Remark:</th>
				</tr>
				<tr>
					<th class="text-right" style="width:100%;" colspan="'.($sample_size+3).'">Accepted / Rejected / Accepted Under Deviation / Partially Accepted (Qty: Nos)</th>
				</tr>
				<tr>
					<th width="10%">Date: </th>
					<th width="27.5%"></th>
					<th width="30%" class="text-left">Checked By:-</th>
					<th width="30%" class="text-left">Approved By:-</th>
				</tr>
				<tr>
					<th class="text-left" style="width:100%;" colspan="'.($sample_size+3).'">* All Dimensions are in "MM" unless otherwise stated</th>
				</tr>
				<tr>
					<th class="text-left" style="width:100%;" colspan="'.($sample_size+3).'">* Sample Size as per the Sampling Plan. But only 05 measured samples are recorded in above PDI Report.</th>
				</tr>
			</table>
			<table class="table top-table" style="margin-top:10px;">
				<tr>
					<td style="width:25%;"></td>
					<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
				</tr>
			</table>';
		}
		else if($postData['pdi_report_type'] == "A86"){
			$headerSize = 66;
			$htmlHeader = '<table class="table item-list-bb">
				<tr>
					<td class="text-center" rowspan="3" style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
					<td class="org_title text-center" rowspan="3" style="font-size:1.3rem;width:50%">PDI / FINAL INSPECTION REPORT</td>
					<td class="text-left"><b>Doc. No.: </b>FM-QCD-06</td>
				</tr>
				<tr>
					<td class="text-left"><b>Effective Date: </b> 15-07-2020</td>
				</tr>
				<tr>
					<td class="text-left"><b>Rev.No/Date: </b>01/08.05.2024</td>
				</tr>
			</table>
			<table class="table item-list-bb" style="margin-top:2px;">
				<tr class="text-left">
					<th class="bg-light text-center" width="10%">Part Number</th>
					<td width="24%">'.((!empty($firData->drw_no)) ? $firData->drw_no : "").'</td>
					<th class="bg-light text-center" width="14%">Work order No.</th>
					<td width="13%">'.(!empty($firData->so_trans_number) ? $firData->so_trans_number : "").'</td>
					<th class="bg-light text-center" width="12%">Invoice Number</th>
					<td width="27%" colspan="3">'.(!empty($firData->inv_no) ? $firData->inv_no : "").'</td>
				</tr>
				<tr class="text-left">
					<th class="bg-light text-center">Part Description</th>
					<td>'.((!empty($firData->item_name)) ? $firData->item_name : "").'</td>
					<th class="bg-light text-center">Inspector  Name</th>
					<td>'.(!empty($firData->emp_name) ? $firData->emp_name : "").'</td>
					<th class="bg-light text-center">Invoice Date</th>
					<td>'.(!empty($firData->inv_date) ? formatDate($firData->inv_date) : "").'</td>
					<th style="text-align:center;" colspan="2">Decision</th>
				</tr>
				<tr class="text-left">
					<th class="bg-light text-center">Drawing No.</th>
					<td>'.(!empty($firData->drw_no) ? $firData->drw_no : "").'</td>
					<th class="bg-light text-center">Inspection Date</th>
					<td>'.((!empty($firData->inspection_date)) ? formatDate($firData->inspection_date) : "").'</td>
					<th class="bg-light text-center">Customer PO</th>
					<td>'.(!empty($firData->doc_no) ? $firData->doc_no : "").'</td>
					<th width="9%;">Accepted</th>
					<td width="4%;"></td>
				</tr>
				<tr class="text-left">
					<th class="bg-light text-center">Drawing Rev.</th>
					<td>'.(!empty($firData->rev_no) ? $firData->rev_no : "").'</td>
					<th class="bg-light text-center">Sampling Inspection Qty.</th>
					<td>'.(!empty($firData->sampling_qty) ? $firData->sampling_qty : "").'</td>
					<th class="bg-light text-center">Lot/Dispatched Qty.</th>
					<td>'.((!empty($firData->pdi_qty)) ? floatval($firData->pdi_qty) : 0).'</td>
					<th>Rejected</th>
					<td></td>
				</tr>
			</table>';
			$htmlFooter = '<table class="table item-list-bb" style="border-bottom:1px solid #000000;">
				<tr>
					<td colspan="2" style="width:100%;"><b>Remark:-</b></td>
				</tr>
				<tr>
					<td style="width:50%;" class="text-left"><b>Insepcted By: </b>'.$firData->approved_by.'</td>
					<td style="width:50%;" class="text-left"><b>Reviewed By: </b>'.$firData->emp_name.'</td>
				</tr>
			</table>
			<table class="table top-table" style="margin-top:10px;">
				<tr>
					<td style="width:25%;"></td>
					<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
				</tr>
			</table>';
		}
		else if($postData['pdi_report_type'] == "A93_A98"){
			$headerSize = 47;$footerSize=28;
			$htmlHeader = '<table class="table item-list-bb">
				<tr>
					<td class="text-center" rowspan="3" style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
					<td class="org_title text-center" rowspan="3" style="font-size:1.3rem;width:45%">Supplier Inspection Report</td>
					<td class="text-left" style="width:30%;"><b>Doc.No.: </b>PHPL/QA/SQ/05</td>
				</tr>
				<tr>
					<td class="text-left"><b>Issue.No & Date: </b> 00 & 21.06.2023</td>
				</tr>
				<tr>
					<td class="text-left"><b>Rev.No & Date: </b>00 & 21.06.2023</td>
				</tr>
			</table>
			<table class="table item-list-bb" style="margin-top:2px;">
				<tr class="text-left">
					<td class="text-center" width="38%"><b>Supplier Name.: </b>'.((!empty($companyData->company_name)) ? $companyData->company_name : "").'</td>
					<td class="text-center" width="35%"><b>Supplier Inspection Date: </b>'.((!empty($firData->inv_date)) ? formatDate($firData->inv_date) : "").'</td>
					<td class="text-center" width="27%"><b>PHC Inspection Date</b>: '.((!empty($firData->inspection_date)) ? formatDate($firData->inspection_date) : "").'</td>
				</tr>
				<tr class="text-left">
					<td class="text-center"><b>Part No.: </b>'.(((!empty($firData->drw_no)) ? $firData->drw_no : "").' / <b>Rev. No: </b>'.((!empty($firData->rev_no)) ? $firData->rev_no : "")).'</td>
					<td class="text-center"><b>Invoice No.: </b>'.((!empty($firData->inv_no)) ? $firData->inv_no : "").' / <b>Qty: </b>'.((!empty($firData->pdi_qty) ? $firData->pdi_qty : "")).'</td>
					<td class="text-center"><b>PHC Sample Qty.: </b>'.((!empty($firData->sampling_qty)) ? $firData->sampling_qty : "").'</td>
				</tr>
				<tr class="text-left">
					<td class="text-center"><b>Part Name: </b>'.((!empty($firData->item_name)) ? $firData->item_name : "").'</td>
					<td class="text-center"><b>MTC Ref.No.: </b>'.((!empty($firData->prc_number)) ? $firData->prc_number : "").((!empty($firData->rm_batch)) ? '('.$firData->rm_batch.')' : "").'</td>
					<td class="text-center"><b>Disposition: </b></td>
				</tr>
			</table>';
			$htmlFooter = '<table class="table item-list-bb" style="border-bottom:1px solid #000000;">
				<tr>
					<td width="28%" rowspan="2"><b>Note (If any) : </b></td>
					<td class="text-left" width="18%"><b>Insepcted By.: </b>'.$firData->approved_by.'</td>
					<td class="text-left" width="18%"><b>Approved By.: </b>'.$firData->emp_name.'</td>
					<td class="text-left" width="18%"><b>Insepcted By.: </b></td>
					<td class="text-left" width="18%"><b>Approved By.: </b></td>
				</tr>
				<tr>
					<td class="text-left" colspan="2"><b>Supplier Comments :</b></td>
					<td class="text-left" colspan="2"><b>PHC Comments :</b></td>
				</tr>
			</table>
			<table class="table top-table" style="margin-top:10px;">
				<tr>
					<td style="width:25%;"></td>
					<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
				</tr>
			</table>';
		}
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='fir_'.$postData['id'].'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(100,100));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		if ($postData['pdi_report_type'] == "A93_A98" || $postData['pdi_report_type'] == "A12") {
			$mpdf->AddPage('L','','','','',5,5,$headerSize,$footerSize,5,5,'','','','','','','','','','A4-L');
		} else {
			$mpdf->AddPage('P','','','','',5,5,$headerSize,$footerSize,5,5,'','','','','','','','','','A4-P');
		}
		$mpdf->WriteHTML($pdfData);
		if($postData['output_type'] == 'I'){
			$mpdf->Output($pdfFileName,$postData['output_type']);	
		}else{
			$filePath = realpath(APPPATH . '../assets/uploads/fir_reports/');
			$mpdf->Output($filePath.'/'.$pdfFileName, 'F');
			return $filePath.'/'.$pdfFileName;
		}	
	}
	
}
?>