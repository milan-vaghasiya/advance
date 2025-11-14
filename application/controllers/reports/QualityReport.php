<?php
class QualityReport extends MY_Controller
{

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Quality Report";
		$this->data['headData']->controller = "reports/qualityReport";
		$this->data['refTypes'] = array('','GRN','Purchase Invoice','Material Issue','Delivery Challan','Sales Invoice','Manual Manage Stock','Production Finish','Visual Inspection','Store Transfer','Return Stock From Production');
	}

	/* Batch History */
	public function batchHistory(){
		$data = $this->input->post();
		$this->data['headData']->pageUrl = "reports/qualityReport/batchHistory";
        $this->data['headData']->pageTitle = "BATCH HISTORY REPORT";
		$this->data['batchData'] = $this->qualityReport->getBatchHistory($data);
        $this->load->view("reports/qc_report/batch_history",$this->data);
    }
	
	public function getBatchHistory($item_id="",$batch_no=""){
        $this->data['item_id'] = $item_id = decodeURL($item_id);
        $this->data['batch_no'] = decodeURL($batch_no);
		$this->data['headData']->pageTitle = "Batch History";
		$this->data['itemData'] = $this->item->getItem(['id'=>$item_id]);
        $this->load->view('reports/qc_report/batch_history_trans',$this->data);
    }
	
	public function getBatchHistoryData(){
		$data = $this->input->post();		
        $batchData = $this->qualityReport->getBatchHistoryData($data);
		
		$i=1; $tbody=""; $blankInTd="";$tfoot=""; 
		$totalQty=0;$totalPrcQty=0;$totalIssueQty=0;$totalUsedQty =0;$totalReturnQty=0;$totalStockQty=0;$totalBalanceQty=0;$totalEndPiece=0; $totalScrap=0;
		$blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
		
		if(!empty($batchData)):
			foreach($batchData as $row):
				$prcData = $this->qualityReport->getBatchHistoryTrans($data);
				$prcCount = count($prcData);

				$balanceQty = floatval($row->qty);
				
				$tbody .= '<tr>
					<td class="text-center">'.$i++.'</td>
					<td>'.$row->trans_number.'</td>
					<td>'.formatDate($row->trans_date).'</td>
					<td>'.$row->doc_no.'</td>
					<td>'.floatval($row->qty).'</td>
					<td>'.$row->party_name.'</td>';
		
					if($prcCount > 0):
						$j=1;
						foreach($prcData as $prcRow):
							$usedQty=0; $returnQty=0; $stockQty=0; $prcProcessData=[]; $endPiece=0; $scrap=0;

							$grnQty = (!empty($prcRow->grn_qty) ? floatval($prcRow->grn_qty) : 0);

							if(!empty($prcRow->prc_type)){
								if($prcRow->prc_type == 2){
									$stockData = $this->cutting->getCuttingBomData(['prc_id'=>$prcRow->id,'single_row'=>1,'stock_data'=>1,'production_data'=>1]);

									$usedQty = (!empty($stockData->cutting_cons) ? floatval($stockData->cutting_cons) : 0);
									$stockQty = $stockData->issue_qty - (( $stockData->cutting_cons) + $stockData->return_qty);
									$returnQty = (!empty($stockData->total_return) ? floatval($stockData->total_return) : 0);									
									$endPiece = (!empty($stockData->total_end_pcs) ? floatval($stockData->total_end_pcs) : 0);
									$scrap = (!empty($stockData->scrap_qty) ? floatval($stockData->scrap_qty) : 0);

									$balanceQty -= ($stockData->issue_qty - $returnQty);
								}else{
									$stockData = $this->sop->getPrcBomData(['prc_id'=>$prcRow->id,'item_id'=>$prcRow->item_id,'production_data'=>1,'stock_data'=>1,'cutting_batch'=>(($prcRow->cutting_flow == 2)?1:''),'single_row'=>1]);
									
									if($prcRow->status > 1){ 
										$prcProcessData = $this->sop->getPRCProcessList(['prc_id'=>$prcRow->id,'process_id'=>$prcRow->process_ids,'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'move_type'=>1]);
									}
									elseif(!empty($prcRow->process_ids)){ 
										$prcProcessData = $this->sop->getProcessFromPRC(['process_ids'=>$prcRow->process_ids,'item_id'=>$prcRow->item_id]); 
									}

									if(!empty($stockData)){ 
										if($stockData->process_id == 0 && $prcRow->cutting_flow == 2){
											$usedQty = ((!empty($prcProcessData))?(floatval(($prcProcessData[0]->ok_qty + $prcProcessData[0]->rej_found) * $stockData->ppc_qty)/$prcProcessData[0]->output_qty):0);
										}else{
											$usedQty = floatval($stockData->production_qty * $stockData->ppc_qty)/$stockData->output_qty;
										}
										$returnQty = (!empty($stockData->return_qty) && $stockData->return_qty > 0)?floatval($stockData->return_qty):0;
										$stockQty = $stockData->issue_qty - ($usedQty + $returnQty);
										
										$balanceQty -= ($stockData->issue_qty - $returnQty);
									}
								}
							}else{
								$balanceQty -= $grnQty;
							}
							
							if(!empty($prcRow->cut_weight)){
								$wtArr = explode('-',$prcRow->cut_weight);
								if(!empty($wtArr[1])){
									$prcRow->cut_weight = $wtArr[1];
								}
							}

							$prcItem = (!empty($prcRow->item_code) ? '[ '.$prcRow->item_code.' ] ' : '').$prcRow->item_name;
							$grnItem = (!empty($prcRow->grn_item_code) ? '[ '.$prcRow->grn_item_code.' ] ' : '').$prcRow->grn_item_name;

							$prcIssueQty = (!empty($stockData->issue_qty) ? floatval($stockData->issue_qty) : 0);
							$issueQty = (($grnQty > 0) ? $grnQty : (($prcIssueQty > 0) ? $prcIssueQty : 0));
							$stockQty = ($stockQty > 0) ? $stockQty : 0;
							$balanceQty = ((!empty($balanceQty) && round($balanceQty) > 0) ? floatval($balanceQty) : 0); 
														
							$tbody.='<td>'.((!empty($grnItem)) ? $grnItem : (!empty($prcItem) ? $prcItem : '')).'</td>
								<td>'.(!empty($prcRow->trans_number) ? $prcRow->trans_number : $prcRow->prc_number).'</td>
								<td>'.(!empty($prcRow->prc_qty) ? floatval($prcRow->prc_qty) : 0).'</td>
								<td>'.(!empty($prcRow->cut_weight) ? floatval($prcRow->cut_weight) : 0).'</td>
								<td>'.$issueQty.'</td>
								<td>'.$usedQty.'</td>
								<td>'.$endPiece.'</td>
								<td>'.$scrap.'</td>
								<td>'.$returnQty.'</td>
								<td>'.$stockQty.'</td>
								<td>'.$balanceQty.'</td>';

							if($j != $prcCount){$tbody.='</tr><tr><td>'.$i++.'</td>'.$blankInTd;}
							$j++;
							
							$totalPrcQty += $prcRow->prc_qty;
							$totalIssueQty += $issueQty;
							$totalUsedQty += $usedQty;
							$totalEndPiece += $endPiece;
							$totalScrap += $scrap;
							$totalReturnQty += $returnQty;
							$totalStockQty += $stockQty;
							$totalBalanceQty = $balanceQty;
						endforeach;
					else:
						$tbody.='<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
					endif;
				$tbody.='</tr>';
				
				$totalQty += $row->qty;
			endforeach;
		endif;
		$tfoot .= '<tr class="thead-dark">
			<th colspan="4" class="text-right">Total</th>
			<th class="text-center">'.$totalQty.'</th> 
			<th colspan="3"></th>
			<th class="text-center">'.$totalPrcQty.'</th> 
			<th></th>
			<th class="text-center">'.$totalIssueQty.'</th> 
			<th class="text-center">'.$totalUsedQty.'</th> 
			<th class="text-center">'.$totalEndPiece.'</th> 
			<th class="text-center">'.$totalScrap.'</th>
			<th class="text-center">'.$totalReturnQty.'</th> 
			<th class="text-center">'.$totalStockQty.'</th> 
			<th class="text-center">'.$totalBalanceQty.'</th> 
		</tr>';
        $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
    }
	
	/* Supplier Rating Report */
	public function supplierRating(){
		$this->data['headData']->pageUrl = "reports/qualityReport/supplierRating";
        $this->data['headData']->pageTitle = "SUPPLIER RATING REPORT";
        $this->data['pageHeader'] = 'SUPPLIER RATING REPORT';
		$this->data['supplierData'] = $this->party->getPartyList(['party_category'=>2]);
	
        $this->load->view("reports/qc_report/supplier_rating",$this->data);
    }

	public function getSupplierRating($jsonData=''){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else: 
            $data = $this->input->post();
        endif;
		
		$supplierItems = $this->qualityReport->getSupplierRating($data);

		$tbodyData=""; $tfootData="";$i=1; $totalQty=0;$inspQty=0;$totalOkQty=0;$totalRejQty=0;$totalShortQty=0;$totalRate=0;$totalAvgRate=0;

		foreach($supplierItems as $items):
			$idata['item_id'] = $items->item_id;
			$idata['from_date'] =$data['from_date'];
			$idata['to_date'] = $data['to_date'];
			$idata['party_id'] = $data['party_id'];
			
			$supplierData = $this->qualityReport->getSupplierRating($idata);
			$qty=0; $t1=0; $t2=0; $t3=0; $wdate ="";
			foreach($supplierData as $row):		
				$qty += $row->qty;
				$wdate = date('Y-m-d',strtotime("+7 day", strtotime($row->delivery_date)));
				
				if($row->trans_date <= $row->delivery_date){$t1 += $row->qty;}
				elseif($row->trans_date <= $wdate){$t2 += $row->qty;}
				else{$t3 += $row->qty;}	

				$daysDiff = '';
				if(!empty($items->trans_date) AND !empty($items->delivery_date)){
					$trans_date = new DateTime($items->trans_date);
					$delivery_date = new DateTime($items->delivery_date);
					$due_days = $delivery_date->diff($trans_date)->format("%r%a");
					$daysDiff = ($due_days > 0) ? $due_days : 'On Time';
				}
			endforeach;
			
			$deliveryRating = ($t1 / $items->qty) * 100;

			$qualityRating = ($items->ok_qty / $items->qty) * 100;
			$totalDrate = ($deliveryRating > 100) ? 30 : round(($deliveryRating * 30)/100,2);
			$totalQrate = ($qualityRating > 100) ? 60 : round(($qualityRating * 60)/100,2);
			$totalRating = $totalDrate + $totalQrate;

				$tbodyData .= '<tr>
					<td class="text-center">'.$i++.'</td>
					<td>'.$items->party_name.'</td>
					<td>'.$items->trans_number.'<br/>'.formatDate($items->trans_date).'</td>
					<td>'.$items->inv_no.'<br/>'.formatDate($items->inv_date).'</td>
					<td>'.$items->po_number.'<br/>'.formatDate($items->po_date).'</td>
					<td>'.$items->item_name.'</td>
					<td>'.$items->qty.'</td>
					<td>'.$items->ok_qty.'</td>
					<td>'.$items->reject_qty.'</td>
					<td>'.$items->short_qty.'</td>
					<td>'.$totalQrate.'</td>
					<td>'.formatDate($items->delivery_date).'</td>
					<td>'.$daysDiff.'</td>
					<td>'.$totalDrate.'</td>
					<td>'.$totalRating.'</td>';
				$tbodyData .='</tr>';
				$totalQty += $items->qty;
				$totalOkQty += $items->ok_qty;
				$totalRejQty += $items->reject_qty;
				$totalShortQty += $items->short_qty;
				$totalRate += $totalRating;
		endforeach;
		$totalAvgRate = $totalRate/($i-1);
		$tfootData .= '<tr class="thead-dark">
						<th colspan="6" class="text-right">Total</th>
						<th>'.$totalQty.'</th>
						<th>'.$totalOkQty.'</th>
						<th>'.$totalRejQty.'</th>
						<th>'.$totalShortQty.'</th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>		
						<th>'.round($totalAvgRate,2).'</th>
					</tr>';

		if(!empty($data['is_pdf'])):
			$reportTitle = 'Supplier Rating';
			$report_date = formatDate($data['from_date']).' to '.formatDate($data['to_date']);  $report_date = date('d-m-Y',strtotime($data['from_date'])).' to '.date('d-m-Y',strtotime($data['to_date']));
			$header = 'Sub-Contractor Rating: '. $report_date.' '; 
			$thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
			$thead .=	'<tr class="text-center">
							<th colspan="15" class="org_title ">'.$header.'</th>
						</tr>
						<tr>
							<th>Sr No.</th>
							<th>Supplier Name</th>
							<th>GRN No & Date</th>
							<th>CH/Inv. No. & Date</th>
							<th>Po No. & Date</th>
							<th>Item Description</th>
							<th>GRN Qty.</th>
							<th>Accepted Qty</th>
							<th>Rejected Qty</th>
							<th>Short Qty</th>
							<th>Quality Rating <br>(%)</th>
							<th>Delivery Date</th>
							<th>Delay Days</th>
							<th>Delivered Rating <br>(%)</th>
							<th>Total</th>
						</tr>';

			$logo = base_url('assets/images/logo.png');
			$pdfData = '<table class="table table-bordered item-list-bb">
				<thead class="thead-dark" id="theadData">'.$thead.'</thead>
				<tbody>'.$tbodyData.'</tbody>
				<tfoot>'.$tfootData.'</tfoot>
			</table>';
			$htmlHeader = '<table class="table">
				<tr>
				<td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
					<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">SUPPLIER RATING REPORT</td>
					<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"></td>
				</tr>
			</table>';
			$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
				<tr>
					<td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
					<td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
				</tr>
			</table>';

			$mpdf = new \Mpdf\Mpdf();
			$filePath = realpath(APPPATH . '../assets/uploads/');
			$pdfFileName = $filePath.'/supplierRating.pdf';
			$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
			$mpdf->WriteHTML($stylesheet,1);
			$mpdf->SetDisplayMode('fullpage');
			$mpdf->SetWatermarkImage($logo,0.05,array(100,100));
			$mpdf->showWatermarkImage = true;
			$mpdf->SetProtection(array('print'));
			$mpdf->SetHTMLFooter($htmlFooter);
			$mpdf->SetHTMLHeader($htmlHeader);
		    $mpdf->AddPage('L','','','','',5,5,19,20,3,3,'','','','','','','','','','A4-L');
			$mpdf->WriteHTML($pdfData);
			$mpdf->Output($pdfFileName,'I');
        else:
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"tfootData"=>$tfootData]);
		endif;
	}
	
	/* Vendor Rating report  */
	public function vendorRating(){
		$this->data['headData']->pageUrl = "reports/qualityReport/vendorRating";
        $this->data['headData']->pageTitle = "VENDOR RATING REPORT";
        $this->data['pageHeader'] = 'VENDOR RATING REPORT';
		$this->data['vendorData'] = $this->party->getPartyList(['party_category'=>3]);
        $this->load->view("reports/qc_report/vendor_rating",$this->data);
    }

	public function getVendorRating($jsonData=""){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else: 
            $data = $this->input->post();
        endif;

		$vendorData = $this->qualityReport->getVendorRating($data);

		$tbodyData=""; $tfootData="";$i=1; $totalQty=0;$inspQty=0;$totalOkQty=0;$totalRejQty=0;$totalRate=0;$totalAvgRate=0;
		foreach($vendorData as $items):
			$idata['item_id'] = $items->item_id;
			$idata['from_date'] =$data['from_date'];
			$idata['to_date'] = $data['to_date'];
			$idata['party_id'] = $data['party_id'];
						
			$vendorChData = $this->qualityReport->getVendorRating($idata);
			$qty=0; $t1=0; $t2=0; $t3=0; $wdate ="";$okqty=0;$rejFound=0;

			foreach($vendorChData as $row):		
				$qty+= $row->qty + $row->rej_found;
				$wdate = date('Y-m-d',strtotime("+7 day", strtotime($row->delivery_date)));
				
				if($row->trans_date <= $row->delivery_date){$t1 += $row->qty;}
				elseif($row->trans_date <= $wdate){$t2 += $row->qty;}
				else{$t3 += $row->qty;}
				$okqty+= $row->qty;
				$rejFound+= $row->rej_found;

				$daysDiff = '';
				if(!empty($items->trans_date) AND !empty($items->delivery_date)){
					$trans_date = new DateTime($items->trans_date);
					$delivery_date = new DateTime($items->delivery_date);
					$due_days = $delivery_date->diff($trans_date)->format("%r%a");
					$daysDiff = ($due_days > 0) ? $due_days : 'On Time';
				}
			endforeach;
			
			$deliveryRating = (!empty($t1) ? ($t1 / $qty) * 100 : 0);
			$qualityRating = (!empty($okqty) ? ($okqty / $qty) * 100 :0);
			$totalDrate = ($deliveryRating > 100) ? 30 : round(($deliveryRating * 30)/100,2);
			$totalQrate = ($qualityRating > 100) ? 60 : round(($qualityRating * 60)/100,2);
			$totalRating = $totalDrate + $totalQrate;
				$tbodyData .= '<tr>
					<td class="text-center">'.$i++.'</td>
					<td>'.$items->party_name.'</td>
					<td>'.$items->ch_number.'<br/>'.formatDate($items->ch_date).'</td>
					<td>'.$items->in_challan_no.'</td>
					<td>'.formatDate($items->trans_date).'</td>
					<td>'.$items->item_name.'</td>
					<td>'.$items->process_name.'</td>
					<td>'.$qty .'</td>
					<td>'.$okqty.'</td>
					<td>'.$rejFound.'</td>
					<td>'.$totalQrate.'</td>
					<td>'.formatDate($items->delivery_date).'</td>
					<td>'.$daysDiff.'</td>
					<td>'.$totalDrate.'</td>
					<td>'.$totalRating.'</td>';
				$tbodyData .='</tr>';
				$totalQty += $qty;
				$totalOkQty += $okqty;
				$totalRejQty += $rejFound;
				$totalRate += $totalRating;
		endforeach;
		$totalAvgRate = $totalRate/($i-1);

		$tfootData .= '<tr class="thead-dark">
						<th colspan="7" class="text-right">Total</th>
						<th>'.$totalQty.'</th>
						<th>'.$totalOkQty.'</th>
						<th>'.$totalRejQty.'</th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>	
						<th>'.round($totalAvgRate,2).'</th>
					</tr>';

		if(!empty($data['is_pdf'])):
			$reportTitle = 'Vendor Rating';
			$report_date = formatDate($data['from_date']).' to '.formatDate($data['to_date']);  $report_date = date('d-m-Y',strtotime($data['from_date'])).' to '.date('d-m-Y',strtotime($data['to_date']));
			$header = 'Sub-Contractor Rating: '. $report_date.' '; 
			$thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
			$thead .=	'<tr class="text-center">
							<th colspan="15" class="org_title ">'.$header.'</th>
						</tr>
						<tr>
							<th>#</th>
							<th>Vendor Name</th>
							<th>Challan No. & Date</th>
							<th>In Chllan No.</th>
							<th>Receive Date</th>
							<th>Item Description</th>
							<th>Process</th>
							<th>Challan Qty.</th>
							<th>Accepted Qty</th>
							<th>Rejected Qty</th>
							<th>Quality Rating <br>(%)</th>
							<th>Delivery Date</th>
							<th>Delay Days</th>
							<th>Delivered Rating <br>(%)</th>
							<th>Total</th>
						</tr>';

			$logo = base_url('assets/images/logo.png');
			$pdfData = '<table class="table table-bordered item-list-bb">
				<thead class="thead-dark" id="theadData">'.$thead.'</thead>
				<tbody>'.$tbodyData.'</tbody>
				<tfoot>'.$tfootData.'</tfoot>
			</table>';
			$htmlHeader = '<table class="table">
				<tr>
				<td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
					<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">VENDOR RATING REPORT</td>
					<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"></td>
				</tr>
			</table>';
			$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
				<tr>
					<td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
					<td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
				</tr>
			</table>';

			$mpdf = new \Mpdf\Mpdf();
			$filePath = realpath(APPPATH . '../assets/uploads/');
			$pdfFileName = $filePath.'/vendorRating.pdf';
			$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
			$mpdf->WriteHTML($stylesheet,1);
			$mpdf->SetDisplayMode('fullpage');
			$mpdf->SetWatermarkImage($logo,0.05,array(100,100));
			$mpdf->showWatermarkImage = true;
			$mpdf->SetProtection(array('print'));
			$mpdf->SetHTMLFooter($htmlFooter);
			$mpdf->SetHTMLHeader($htmlHeader);
		    $mpdf->AddPage('L','','','','',5,5,19,20,3,3,'','','','','','','','','','A4-L');
			$mpdf->WriteHTML($pdfData);
			$mpdf->Output($pdfFileName,'I');
        else:
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"tfootData"=>$tfootData]);
		endif;
	}
	
	/* Rejection Monitoring  avruti*/
	public function rejectionMonitoring(){
		$this->data['headData']->pageUrl = "reports/qualityReport/rejectionMonitoring";
        $this->data['headData']->pageTitle = "REJECTION MONITORING REPORT";
        $this->data['pageHeader'] = 'REJECTION MONITORING REPORT';
		$this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
		$this->data['processList'] = $this->process->getProcessList();
		$this->data['employeeList'] = $this->employee->getEmployeeList();
        $this->load->view("reports/qc_report/rejection_monitoring",$this->data);
	}

	public function getRejectionMonitoring(){
		$data = $this->input->post();
		$errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['to_date'] = "Invalid date.";

		$rejData = $this->qualityReport->getRejectionMonitoring($data);

		$tbodyData=""; $tfootData="";$i=1; $totalRejQty=0;$totalProdQty = 0;

		foreach($rejData as $row):
			$prodQty = ($row->qty + $row->ok_qty);
			$rejRate = 0;
			if($row->qty > 0 && $prodQty > 0){ $rejRate = round((($row->qty*100)/$prodQty),2); }
			$tbodyData .= '<tr>
								<td class="text-center">'.$i++.'</td>
								<td>'.formatDate($row->decision_date).'</td>
								<td>'.(!empty($row->item_code) ? $row->item_code : $row->item_name) . '</td>
								<td>'.$row->prc_number.'</td>
								
								<td>'.round($prodQty,2).'</td>
								<td>'.round($row->qty,2).'</td>
								<td>'.round($rejRate,2).' %</td>
								<td>'.$row->rr_comment.'</td>
								
								<td>'.$row->process_name.'</td>
								<td>'.(($row->process_by != 3) ? 'INHOUSE' : 'JW-SUPPLIER') .'</td>
								<td>'.$row->processor_name.'</td>
								<td>'.$row->emp_name.'</td>
								
								<td>'.$row->rejction_stage.'</td>
								<td>'.(empty($row->rr_by) ? 'INHOUSE' : 'JW-SUPPLIER') .'</td>
								<td>'.$row->rr_processor.'</td>
								<td>'.$row->rr_operator.'</td>';
			$tbodyData .='</tr>';
			$totalProdQty += $prodQty;
			$totalRejQty += $row->qty;
		endforeach;
		$tfootData .= '<tr class="thead-dark">
						<th colspan="4" style="text-align:right !important;">Total</th>
						<th>'.round($totalProdQty,2).'</th>
						<th>'.round($totalRejQty,2).'</th>
						<th>'.round((($totalRejQty*100)/$totalProdQty),2).'</th>
						<th></th>
						
						<th></th><th></th><th></th><th></th>
						<th></th><th></th><th></th><th></th>
					</tr>';

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"tfootData"=>$tfootData]);
		endif;
	}

	public function rejectionAnalysis(){
		$this->data['headData']->pageUrl = "reports/qualityReport/rejectionAnalysis";
        $this->data['headData']->pageTitle = "REJECTION ANALISYS REPORT";
        $this->data['pageHeader'] = 'REJECTION ANALISYS REPORT';
		$this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
		$this->data['processList'] = $this->process->getProcessList();
        $this->load->view("reports/qc_report/rejection_analisys",$this->data);

	}

	public function getRejectionAnalysisData(){
		$data = $this->input->post();

		$rejectionSummary = $this->qualityReport->getRejectionSummary($data);
		$tbody = '';$i=1; $total_prod_qty = 0; $total_rej_qty = 0; $trans_rej_total = 0;
		foreach($rejectionSummary as $rejSum):
			$data['item_id'] = $rejSum->item_id;
			$transaction = $this->qualityReport->getRejectionMonitoring($data);

			$qualitySummaryRate = (!empty($rejSum->production_qty) && !empty($rejSum->rej_qty))?(round(((($rejSum->production_qty - $rejSum->rej_qty) * 100)/ $rejSum->production_qty),2)):0;
			$rejSummaryRate = (!empty($rejSum->production_qty) && !empty($rejSum->rej_qty))?(round((($rejSum->rej_qty * 100)/ $rejSum->production_qty),2)):0;
			
			$transRows = '';$j = 0;$transRowFirst = ''; 
			if(!empty($transaction)):
				foreach($transaction as $row):
					if($j == 0):						
						$transRowFirst .= '<td class="text-center">'.$row->qty.'</td>';
						$transRowFirst .= '<td class="text-left">'.$row->remark.'</td>';
						$transRowFirst .= '<td class="text-left">'.(!empty($row->rr_stage) ? $row->rejction_stage : 'Raw Material').'</td>';
						$transRowFirst .= '<td class="text-left">'.(!empty($row->rr_by) ? $row->vendor_name : 'In House').'</td>';
						$transRowFirst .= '<td class="text-left">'.$row->rr_comment.'</td>';					
					else:
						$transRows .= '<tr>';
							$transRows .= '<td>-</td>';
							$transRows .= '<td>-</td>';
							$transRows .= '<td>-</td>';
							$transRows .= '<td>-</td>';
							$transRows .= '<td>-</td>';
							$transRows .= '<td>-</td>';
							$transRows .= '<td class="text-center">'.$row->qty.'</td>';
							$transRows .= '<td class="text-left">'.$row->remark.'</td>';
							$transRows .= '<td class="text-left">'.(!empty($row->rr_stage) ? $row->rejction_stage : 'Raw Material').'</td>';
							$transRows .= '<td class="text-left">'.(!empty($row->rr_by) ? $row->vendor_name : 'In House') .'</td>';
							$transRows .= '<td class="text-left">'.$row->rr_comment.'</td>';
						$transRows .= '</tr>';
					endif;
					$j++;
					$trans_rej_total += $row->qty;
				endforeach;
			else:
				$transRowFirst = '<td></td>';
				$transRowFirst .= '<td></td>';
				$transRowFirst .= '<td></td>';
				$transRowFirst .= '<td></td>';
				$transRowFirst .= '<td></td>';
			endif;

			$tbody .= '<tr>';
				$tbody .= '<td class="text-center">'.$i++.'</td>';
				$tbody .= '<td class="text-left">'.$rejSum->item_code.'</td>';
				$tbody .= '<td class="text-center">'.$rejSum->production_qty.'</td>';
				$tbody .= '<td class="text-center">'.$rejSum->rej_qty.'</td>';
				$tbody .= '<td class="text-center">'.$qualitySummaryRate.'</td>';
				$tbody .= '<td class="text-center">'.$rejSummaryRate.'</td>';
				$tbody .= $transRowFirst;
			$tbody .= '</tr>';
			$tbody .= $transRows;

			$total_prod_qty += $rejSum->production_qty;
			$total_rej_qty += $rejSum->rej_qty;
		endforeach;

		$tfooter = '';

		$qualityRate = (!empty($total_prod_qty) && !empty($total_rej_qty))?round(((($total_prod_qty - $total_rej_qty) * 100)/ $total_prod_qty),2):0;
		$rejectionRate = (!empty($total_prod_qty) && !empty($total_rej_qty))?round((($total_rej_qty * 100)/ $total_prod_qty),2):0;
		$tfooter .= '<tr>';
			$tfooter .= '<th class="text-right" colspan="2">Total</th>';
			$tfooter .= '<th>'.$total_prod_qty.'</th>';
			$tfooter .= '<th>'.$total_rej_qty.'</th>';
			$tfooter .= '<th>'.$qualityRate.'</th>';
			$tfooter .= '<th>'.$rejectionRate.'</th>';
			$tfooter .= '<th>'.$trans_rej_total.'</th>';
			$tfooter .= '<th colspan="4"></th>';
		$tfooter .= '</tr>';

		$this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfooter]);
	}
	
	public function getProcessByWiseList(){
		$data = $this->input->post();		
		$option = '<option value="">Select ALL</option>';

		if(empty($data['rr_by'])){
			$machineList = $this->item->getItemList(['item_type'=>5,'active_machine'=>1]);
			if(!empty($machineList)){
				foreach($machineList as $row){
					$option .= '<option value="'.$row->id.'">'.$row->item_code.'</option>';
				}
			}
		}else{
			$partyList = $this->party->getPartyList(['party_category'=>'2,3']);
			if(!empty($partyList)){
				foreach($partyList as $row){
					$option .= '<option value="'.$row->id.'">'.$row->party_name.'</option>';
				}
			}
		}

		$this->printJson(['status'=>1, 'option'=>$option]);
	}

	/* Supplier Rating Summary Report */
	public function supplierRatingSummary(){
		$this->data['headData']->pageUrl = "reports/qualityReport/supplierRatingSummary";
		$this->data['headData']->pageTitle = "SUPPLIER RATING SUMMARY REPORT";
		$this->data['pageHeader'] = 'SUPPLIER RATING SUMMARY REPORT';
		$this->data['supplierData'] = $this->party->getPartyList(['party_category'=>['2,3']]);
	
		$this->load->view("reports/qc_report/supplier_rating_summary",$this->data);
	}

	public function getSupplierRatingSummary($jsonData=""){
        if(!empty($jsonData)){
            $data = (array) decodeURL($jsonData);
        }else{
            $data = $this->input->post();
        }
		$errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['to_date'] = "Invalid date.";
		
		if($data['type'] == 1){
			$supplierItems = $this->party->getPartyList(['party_category' => 2,'id'=>$data['party_id']]);
		}else{
			$supplierItems = $this->party->getPartyList(['party_category' => 3,'id'=>$data['party_id']]);
		}
		
		$tbodyData=""; $tfootData="";$i=1; $totalRate=0; 

		foreach($supplierItems as $row):
			$idata['from_date'] =$data['from_date'];
			$idata['to_date'] = $data['to_date'];
			$idata['party_id'] = $row->id;
			
			if($data['type'] == 1){
				$supplierData = $this->qualityReport->getSupplierEvalution($idata);
				
				$t1 = $supplierData->delivered_qty;
				$deliveryRating = (!empty($supplierData->grnQty) ? ($t1 / $supplierData->grnQty) * 100 : 0);
				$qualityRating = (!empty($supplierData->grnQty) ? ($supplierData->okQty / $supplierData->grnQty) * 100 : 0);
			}else{
				$jobWorkData = $this->qualityReport->getVendorEvalution($idata);
				
				$t1 = $jobWorkData->delivered_qty;
				$qty = (!empty($jobWorkData->rejFound) ? $jobWorkData->grnQty + $jobWorkData->rejFound : 0);
				$deliveryRating = (!empty($qty) ? ($t1 / $qty) * 100 : 0);
				$qualityRating = (!empty($qty) ? ($jobWorkData->grnQty / $qty) * 100 :0);
			}
			
			$totalDrate = ($deliveryRating > 100) ? 40 : round(($deliveryRating * 40)/100,2);
			$totalQrate = ($qualityRating > 100) ? 60 : round(($qualityRating * 60)/100,2);
			
			$totalSupplierRate = ($totalDrate + $totalQrate);
			if ($totalSupplierRate == 0) {
				continue;
			}
			
			$tbodyData .= '<tr>
				<td class="text-center">'.$i++.'</td>
				<td>'.$row->party_name.'</td>
				<td>'.$totalDrate.'</td>
				<td>'.$totalQrate.'</td>
				<td>'.$totalSupplierRate.'</td>';
			$tbodyData .='</tr>';
			$totalRate += $totalSupplierRate;
		endforeach;
		
		$totalAvgRate = $totalRate/($i-1);
		$tfootData .= '<tr class="thead-dark">
						<th colspan="4" class="text-right">Overall Rating</th>
						<th>'.round($totalAvgRate,2).'</th>
					</tr>';

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:

			if(!empty($data['is_pdf'])){
				$report_date = date('d-m-Y',strtotime($data['from_date'])).' to '.date('d-m-Y',strtotime($data['to_date']));
				$header = 'Sub-Contractor Rating: '. $report_date.' ';
				$label = (($data['type'] == 1) ? "SUPPLIER RATING SUMMARY ": "VENDOR RATING SUMMARY ");
				
				$logo = base_url('assets/images/logo.png');
	 
				$thead = '<tr class="text-center">
							<th colspan="5" class="org_title ">'.$header.'</th>
						</tr>
						<tr class="text-center">
								<th >Sr No.</th>
								<th >Sub-Cont. Name</th>
								<th>Delivery Rating <br> (40%)</th>
								<th>Quality Rating <br> (60%)</th>
								<th>Total <br> (100%)</th>
						</tr>';
			
				$pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
								<thead class="thead-dark" id="theadData">'.$thead.'</thead>
								<tbody>'.$tbodyData.'</tbody>
								<tfoot>
									'.$tfootData.'
								</tfoot>
							</table>';
			
						
				$htmlHeader = '<table class="table">
								<tr>
									<td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
									<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$label .' </td>
									<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">PUR/F/07'.'<br>'.'(02/01.01.2025)</td>
								</tr>

							</table>';
				$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
									<tr>
										<td style="width:50%;font-size:12px;"></td>
										<td style="width:50%;text-align:right;font-size:10px;">Page No. {PAGENO}/{nbpg}</td>
									</tr>
								</table>';
	
				$mpdf = new \Mpdf\Mpdf();
				$filePath = realpath(APPPATH . '../assets/uploads/');
				$pdfFileName = $filePath.'/supplierRating.pdf';
				$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
				$mpdf->WriteHTML($stylesheet,1);
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->SetWatermarkImage($logo,0.05,array(100,100));
				$mpdf->showWatermarkImage = true;
				$mpdf->SetProtection(array('print'));
				$mpdf->SetHTMLFooter($htmlFooter);
				$mpdf->SetHTMLHeader($htmlHeader);
				$mpdf->AddPage('L','','','','',3,3,18,3,3,3,'','','','','','','','','','A4-P');
				$mpdf->WriteHTML($pdfData);
				$mpdf->Output($pdfFileName,'I');
			}
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"tfootData"=>$tfootData]);
		endif;
	}
	
	/* Supplier Evalution Summary Report */
	public function supplierEvalution(){
		$this->data['headData']->pageUrl = "reports/qualityReport/supplierEvalution";
		$this->data['headData']->pageTitle = "SUPPLIER EVALUTION SUMMARY-JOBWORK";
		$this->data['pageHeader'] = 'SUPPLIER EVALUTION SUMMARY-JOBWORK';
		$this->data['supplierData'] = $this->party->getPartyList(['party_category'=>['2,3']]);
	
		$this->load->view("reports/qc_report/supplier_evalution",$this->data);
	}
	
	/* Created By @Raj 27-05-2025 */
	public function getSupplierEvalution($jsonData=""){
        if(!empty($jsonData)){
            $data = (array) decodeURL($jsonData);
        }else{
            $data = $this->input->post();
        }
		
		$start = mktime(0, 0, 0, $data['from_month'], 1, $data['from_year']);
		$end   = mktime(0, 0, 0, $data['to_month'], 1, $data['to_year']);
		$months = (($data['to_year'] - $data['from_year']) * 12) + ($data['to_month'] - $data['from_month']);
		
		$errorMessage = array();
		if($months < 0 || $months > 12)
            $errorMessage['date_range_error'] = "Invalid Financial Year.";
		
		if($data['type'] == 1){
			$supplierList = $this->party->getPartyList(['party_category' => 2,'id'=>$data['party_id']]);
		}else{
			$supplierList = $this->party->getPartyList(['party_category' => 3,'id'=>$data['party_id']]);
		}
		
		$tbodyData=""; $thead="";$j=1; $totalRate=0; 
		$report_date = date('F-Y',strtotime($data['from_year']."-".$data['from_month']."-01")).' to '.date('F-Y',strtotime($data['to_year']."-".$data['to_month']."-31"));
		
		$thead .= '<tr class="text-center">
					<th colspan="'.(4+$months).'" class="org_title">SUPPLIER EVALUTION SUMMARY-JOBWORK: '.$report_date.'</th>
				</tr>
				<tr class="text-center">
					<th style="min-width:50px;">Sr No.</th>
					<th style="min-width:50px;">Supplier Name</th>';
					for ($i = 0; $i <= $months; $i++) {
						$current = strtotime("+$i months", $start);
						$thead .= '<th>'.date('M-y', $current).'</th>';
					}
		$thead .= '<th>Total</th>
				</tr>';
		
		foreach($supplierList as $row):
			$rowData = '<tr>
				<td class="text-center">'.$j.'</td>
				<td>'.$row->party_name.'</td>';
			$totalAvgRate = 0; $no_of_month = 0; $hasValidRate = false;

			for($i = 0; $i <= $months; $i++) {
				$current = strtotime("+$i months", $start);
				$idata['from_date'] = date("Y-m-01",$current);
				$idata['to_date'] = date("Y-m-t",$current);
				$idata['party_id'] = $row->id;

				if($data['type'] == 1){
					$supplierData = $this->qualityReport->getSupplierEvalution($idata);
					$t1 = $supplierData->delivered_qty;
					$deliveryRating = (!empty($supplierData->grnQty) ? ($t1 / $supplierData->grnQty) * 100 : 0);
					$qualityRating = (!empty($supplierData->grnQty) ? ($supplierData->okQty / $supplierData->grnQty) * 100 : 0);
				}else{
					$jobWorkData = $this->qualityReport->getVendorEvalution($idata);
					$t1 = $jobWorkData->delivered_qty;
					$qty = (!empty($jobWorkData->rejFound) ? $jobWorkData->grnQty + $jobWorkData->rejFound : 0);
					$deliveryRating = (!empty($qty) ? ($t1 / $qty) * 100 : 0);
					$qualityRating = (!empty($qty) ? ($jobWorkData->grnQty / $qty) * 100 :0);
				}

				$totalDrate = ($deliveryRating > 100) ? 40 : round(($deliveryRating * 40)/100,2);
				$totalQrate = ($qualityRating > 100) ? 60 : round(($qualityRating * 60)/100,2);

				$totalRate = ($totalDrate + $totalQrate);
				$hasValidRate = $hasValidRate || ($totalRate > 0);
				$totalAvgRate += $totalRate;
				$no_of_month += ($totalRate > 0) ? 1 : 0;
				$rowData .= '<td>'.($totalRate).'</td>';
			}

			if ($hasValidRate) {
				$rowData .= '<td>'.(!empty($no_of_month) ? round(($totalAvgRate / $no_of_month),2) : 0).'</td>
					</tr>';
				$tbodyData .= $rowData;
				$j++;
			}
		endforeach;

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			if(!empty($data['is_pdf'])){
				$header = 'Sub-Contractor Rating: '. $report_date.' ';
				$label = (($data['type'] == 1) ? "SUPPLIER RATING SUMMARY ": "VENDOR RATING SUMMARY ");
				
				$logo = base_url('assets/images/logo.png');
	 
				$pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
								<thead class="thead-dark" id="theadData">'.$thead.'</thead>
								<tbody>'.$tbodyData.'</tbody>
							</table>';
				$htmlHeader = '<table class="table">
								<tr>
									<td style="width:15%;"><img src="'.$logo.'" style="height:50px;"></td>
									<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:70%">'.$label .' </td>
									<td class="org_title text-uppercase text-right" style="font-size:1rem;width:15%">PUR/F/07'.'<br>'.'(02/01.01.2025)</td>
								</tr>

							</table>';
				$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
									<tr>
										<td style="width:50%;font-size:12px;"></td>
										<td style="width:50%;text-align:right;font-size:10px;">Page No. {PAGENO}/{nbpg}</td>
									</tr>
								</table>';
	
				$mpdf = new \Mpdf\Mpdf();
				$filePath = realpath(APPPATH . '../assets/uploads/');
				$pdfFileName = $filePath.'/supplierRating.pdf';
				$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
				$mpdf->WriteHTML($stylesheet,1);
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->SetWatermarkImage($logo,0.05,array(100,100));
				$mpdf->showWatermarkImage = true;
				$mpdf->SetProtection(array('print'));
				$mpdf->SetHTMLFooter($htmlFooter);
				$mpdf->SetHTMLHeader($htmlHeader);
				$mpdf->AddPage('L','','','','',3,3,18,3,3,3,'','','','','','','','','','A4-P');
				$mpdf->WriteHTML($pdfData);
				$mpdf->Output($pdfFileName,'I');
			}
			$this->printJson(['status'=>1,"theadData"=>$thead,"tbodyData"=>$tbodyData]);
		endif;
	}
}
?>