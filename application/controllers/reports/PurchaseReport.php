<?php
class PurchaseReport extends MY_Controller
{
    private $purchase_monitoring = "reports/purchase_report/purchase_monitoring";  
	private $purchase_inward = "reports/purchase_report/purchase_inward";	
	private $enquiry_register = "reports/purchase_report/enquiry_register";
	private $supplier_wise_item = "reports/purchase_report/supplier_wise_item";
	private $market_trend = "reports/purchase_report/market_trend";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
        $this->data['headData']->pageTitle = "PURCHASE REPORT";
		$this->data['headData']->controller = "reports/purchaseReport";
	}
	
    public function purchaseMonitoring(){
        $this->data['headData']->pageTitle = "PURCHASE MONITORING REGISTER";
        $this->data['itemTypeData'] = $this->itemCategory->getCategoryList(['final_category'=>0]);
		$this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3"]);
        $this->load->view($this->purchase_monitoring,$this->data);
    }

    public function getPurchaseMonitoring(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $purchaseData = $this->purchaseReport->getPurchaseOrderMonitoring($data);
            $tbody="";$i=1; $tfoot="";$totalQty=0;$totalReceiveQty=0; $totalBalanceQty=0;$totalAmount=0;$totalPrice=0;
            $blankInTd="";
			
            $blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
         
            if(!empty($purchaseData)):
				foreach($purchaseData as $row):
					$data['item_id'] = $row->item_id; 
					$data['po_id'] = $row->po_id;
					$data['po_trans_id'] = $row->id;
					$receiptData = $this->purchaseReport->getPurchaseReceipt($data);
					$receiptCount = count($receiptData);

					$balanceQty = floatval($row->qty);
					
					$tbody .= '<tr>
						<td class="text-center">'.$i++.'</td>
						<td>'.($row->trans_number).'</td>
						<td>'.formatDate($row->trans_date).'</td>
						<td>'.$row->party_name.'</td>
						<td>'.($row->item_name).'</td>
						<td>'.floatval($row->qty).'</td>
						<td>'.round($row->price,2).'</td>';
		  
						if($receiptCount > 0):
							$j=1;
							foreach($receiptData as $recRow):
								$balanceQty -= floatval($recRow->qty);
								$totalAmt = $recRow->qty * $row->price;
								$gi_no = (!empty($recRow->trans_no))?$recRow->trans_prefix.sprintf("%04d",$recRow->trans_no):'';
								
								$tbody.='<td>'.formatDate($recRow->trans_date).'</td>
									<td>'.$recRow->trans_number.'</td>
									<td>'.$recRow->doc_date.'</td>
									<td>'.$recRow->doc_no.'</td>
									<td>'.floatval($recRow->qty).'</td>
									<td>'.$balanceQty.'</td>
									<td>'.floatval($row->price).'</td>
									<td>'.floatval($totalAmt).'</td>';

								if($j != $receiptCount){$tbody.='</tr><tr><td>'.$i++.'</td>'.$blankInTd;}
								$j++;
								
								$totalReceiveQty += $recRow->qty;
								$totalBalanceQty += $balanceQty;
								$totalPrice += $row->price;
								$totalAmount += $totalAmt;
							endforeach;
						else:
							$tbody.='<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
						endif;
					$tbody.='</tr>';
					
					$totalQty += $row->qty;
				endforeach;
            endif;
			$tfoot .= '<tr class="thead-dark">
					<th colspan="5" class="text-right">Total</th>
					<th class="text-center">'.$totalQty.'</th> 
					<th colspan="5"></th>
					<th class="text-center">'.$totalReceiveQty.'</th> 
					<th class="text-center">'.$totalBalanceQty.'</th> 
					<th class="text-center">'.$totalPrice.'</th> 
					<th class="text-center">'.$totalAmount.'</th> 
			</tr>';
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }

	public function purchaseInward(){
        $this->data['headData']->pageTitle = "PURCHASE INWARD REPORT";
        $this->data['pageHeader'] = 'PURCHASE INWARD REPORT';
        $this->data['itemTypeData'] = $this->itemCategory->getCategoryList(['final_category'=>0]);
        $this->load->view($this->purchase_inward,$this->data);
    }

	public function getPurchaseInward(){
        $data = $this->input->post();
        $inwardData = $this->purchaseReport->getPurchaseInward($data);
        $i=1; $tbody=''; $totalAmt=0; $poNo=''; $tfoot = ''; $totalQty=0; $totalItemPrice=0; $total=0;
        if(!empty($inwardData)){
            foreach($inwardData as $row):
                $totalAmt = ($row->qty * $row->price);
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->trans_number.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->po_number.'</td>
                    <td>'.formatDate($row->po_date).'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.floatVal($row->qty).'</td>
                    <td>'.floatVal($row->price).'</td>
                    <td>'.$totalAmt.'</td>
                </tr>';
                $totalQty += $row->qty; $totalItemPrice += $row->price; $total += $totalAmt;
            endforeach;
        } 
        $tfoot = '<tr>
                <th colspan="7">Total</th>
                <th>'.(!empty($totalQty) ? round($totalQty) : '').'</th>
                <th>'.(!empty($totalItemPrice) ? round($totalItemPrice, 2) : '').'</th>
                <th>'.(!empty($total) ? round($total, 2) : '').'</th>
            </tr>';
        $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
    }

	public function enquiryRegister(){
        $this->data['headData']->pageTitle = "ENQUIRY REGISTER";
        $this->data['itemList'] = $this->item->getItemList();
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>2]);
        $this->load->view($this->enquiry_register,$this->data);
    }

    public function getEnquiryRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $purchaseData = $this->purchaseReport->getEnquiryRegisterData($data);
            
			$tbody="";$i=1;$tfoot="";$totalQty=0;$totalQuotQty=0;
			 
            if(!empty($purchaseData)):
                foreach($purchaseData as $row):
                    $tbody .= '<tr>
                        <td class="text-center">'.$i++.'</td>
                        <td>'.$row->trans_number.'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td>'.$row->party_name.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->qty.'</td>
                        <td>'.$row->quote_no.'</td>
                        <td>'.formatDate($row->quote_date).'</td>
                        <td>'.$row->qtnQty.'</td>
                        <td>'.$row->price.'</td>
                        <td>'.$row->feasible.'</td>
                        <td>'.$row->lead_time.'</td>
                        <td>'.$row->emp_name.'<br>'.formatDate($row->approve_date).'</td>';
                    $tbody.='</tr>';
					
                    $totalQty += $row->qty;
                    $totalQuotQty += $row->qtnQty;
                endforeach;
            endif;
		   $tfoot .= '<tr class="thead-dark">
					<th colspan="5" class="text-right">Total</th>
					<th class="text-center">'.$totalQty.'</th> 
					<th colspan="2"></th>
					<th class="text-center">'.$totalQuotQty.'</th>
					<th colspan="4"></th>
			</tr>';
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }

    /* Supplier Wise Item Report Created By Rashmi : 15/05/2024 */
    public function supplierWiseItem(){
        $this->data['headData']->pageTitle= 'SUPPLIER WISE ITEM & ITEM WISE SUPPLIER REPORT';      
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>'2,3']);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>2]);
        $this->load->view($this->supplier_wise_item,$this->data);
    }

	/* Created By Rashmi : 15/05/2024 */
    public function getSupplierWiseItem(){
        $data = $this->input->post();
        $purchaseData = $this->purchaseReport->getSupplierWiseItem($data);
        $tbody="";$i=1;
        foreach($purchaseData as $row):
			$tbody .= '<tr>
				<td>'.$i++.'</td>
				<td>'.$row->party_name.'</td>
				<td>' . (!empty($row->item_code) ? '[' . $row->item_code . '] ' . $row->item_name : $row->item_name) . '</td>';
			$tbody.='</tr>';
        endforeach;
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

	public function marketTrend(){
        $this->data['headData']->pageTitle = "ENQUIRY REGISTER";
        $this->load->view($this->market_trend,$this->data);
    }
	
	public function getTrendMarket(){
        $data = $this->input->post();
        $errorMessage = array();
		if(empty($data['to_date']))
			$errorMessage['toDate'] = "Invalid date.";
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$marketData = $this->purchaseReport->getMarketTrendData($data);
			$tbody="";$i=1; 
			if(!empty($marketData)):
				foreach($marketData as $row):
					$tbody .= '<tr>
						<td class="text-center">'.$i.'</td>
						<td class="text-center">'.$row->item_name.'</td>
						<td class="text-center">'.(($row->april != '-')?round($row->april,2):'-').'</td>
						<td class="text-center">'.(($row->may != '-')?round($row->may,2):'-').'</td>
						<td class="text-center">'.(($row->june != '-')?round($row->june,2):'-').'</td>
						<td class="text-center">'.(($row->july != '-')?round($row->july,2):'-').'</td>
						<td class="text-center">'.(($row->august != '-')?round($row->august,2):'-').'</td>
						<td class="text-center">'.(($row->september != '-')?round($row->september,2):'-').'</td>
						<td class="text-center">'.(($row->october != '-')?round($row->october,2):'-').'</td>
						<td class="text-center">'.(($row->november != '-')?round($row->november,2):'-').'</td>
						<td class="text-center">'.(($row->december != '-')?round($row->december,2):'-').'</td>
						<td class="text-center">'.(($row->january != '-')?round($row->january,2):'-').'</td>
						<td class="text-center">'.(($row->february != '-')?round($row->february,2):'-').'</td>
						<td class="text-center">'.(($row->march != '-')?round($row->march,2):'-').'</td>';
					$tbody.='</tr>';
					$i++;
				endforeach;
			endif;
			$this->printJson(['status'=>1, 'tbody'=>$tbody]);
		endif;
    }

	/* GRN Incoming Rejection/Short Qty Report */
    public function incomingRejection(){
		$this->data['headData']->pageTitle = "INCOMING REJECTION/SHORT";
        $this->data['headData']->pageUrl = "reports/purchaseReport/incomingRejection";
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,5,6,8"]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[1,2,3]]);
        $this->load->view('reports/purchase_report/incoming_rejection',$this->data);
    }

    public function getIncomingRejection(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $grnData = $this->purchaseReport->getIncomingRejection($data);

            $tbody=""; $i=1; $tfoot=""; $totalQty=0;$totalRejQty=0;$totalShortQty=0;
            if(!empty($grnData)):
                foreach($grnData as $row):
                    $tbody .= '<tr class="text-center">
                        <td>'.$i++.'</td>
                        <td>'.$row->trans_number.'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td>'.$row->party_name.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->qty.'</td>
                        <td>'.$row->reject_qty.'</td>
                        <td>'.$row->short_qty.'</td>
                    </tr>';
                endforeach;
				
				$totalQty += $row->qty;
				$totalRejQty += $row->reject_qty;
				$totalShortQty += $row->short_qty;
            endif;
			$tfoot .= '<tr class="thead-dark">
				<th colspan="5" class="text-right">Total</th>
				<th class="text-center">'.$totalQty.'</th> 
				<th class="text-center">'.$totalRejQty.'</th>
				<th class="text-center">'.$totalShortQty.'</th>
			</tr>';
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }

	/* NCR Report*/
    public function supplierNCRReport(){
        $this->data['headData']->pageUrl = "reports/purchaseReport/supplierNCRReport";
        $this->data['headData']->pageTitle = "Supplier NCR Summary";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>2]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view("reports/purchase_report/ncr_summary",$this->data);
    }

	public function getSupplierNCRData($jsonData=''){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else: 
            $data = $this->input->post();
        endif;
      
        $result = $this->purchaseReport->getSupplierNCRData($data);
        $tbody=""; $tfoot=""; $i=1; $totalQty=0; $totalRejQty=0;
        if(!empty($result)):
            foreach($result as $row):
                $tbody .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td>'.$row->trans_number.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->challan_no.'</td>
                    <td>'.$row->batch_no.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->ref_of_complaint.'</td>
                    <td>'.$row->complaint.'</td>
                    <td>'.$row->qty.'</td>
                    <td>'.$row->rej_qty.'</td>
                    <td>'.$row->product_returned.'</td>
                    <td>'.$row->report_no.'</td>
                    <td>'.$row->ref_feedback.'</td>
                    <td>'.$row->remark.'</td>';
                $tbody.='</tr>';
                $totalQty += $row->qty; 
                $totalRejQty += $row->rej_qty;
            endforeach;
        endif;
         
		$tfoot .= '<tr class="thead-dark">
            <th colspan="9" class="text-right">Total</th>
            <th class="text-center">'.$totalQty.'</th> 
            <th class="text-center">'.$totalRejQty.'</th>
            <th colspan="4"></th>
        </tr>';
        if(!empty($data['pdf'])):
            $thead .='<tr>
                        <th>#</th>
                        <th>NCR No.</th>
                        <th>NCR Date</th>
                        <th>Customer Name</th>
                        <th>Challan No.</th>
                        <th>Batch No.</th>
                        <th>Part No.</th>
                        <th>Ref. of Complaint</th>
                        <th>Details of Complaint</th>
                        <th>Lot Qty.</th>
                        <th>Rej. Qty.</th>
                        <th>CAPA Request</th>
                        <th>CAPA Report No.</th>
                        <th>Effectiveness</th>
                        <th>Remarks</th>
                    </tr>';

            $logo = base_url('assets/images/logo.png');
            $pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
                <thead class="thead-dark" id="theadData">'.$thead.'</thead>
                <tbody>'.$tbody.'</tbody>
                <tfoot>'.$tfoot.'</tfoot>
            </table>';
            $htmlHeader = '<table class="table">
                <tr>
                   <td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">Supplier NCR Summary</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">PUR-F-12'.'<br>'.'(Rev.01 dtd. 01.01.2025)</td>
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
            $pdfFileName = $filePath.'/customerComplaints.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,19,20,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);
            
            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        
        else:
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }

	/* Price Revision History Report */
    public function priceRevisionHistory(){
        $this->data['headData']->pageTitle = "PRICE REVISION HISTORY REPORT";
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,4,5,6,7,8,9"]);
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view('reports/purchase_report/price_revision_history',$this->data);
    }

    public function getPriceRevisionHistory(){
        $data = $this->input->post();        
        if ($data['type'] == 1) {
            $priceData = $this->purchaseReport->getPurchasePriceData($data);
        } else {
            $priceData = $this->purchaseReport->getJobworkPriceData($data);
        }
        
        $tbody=''; $i=1;         
        if (!empty($priceData)) {
            foreach ($priceData as $row) {					
                $tbody .= '<tr class="text-center">
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->trans_number .'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.(!empty($row->item_code) ? '[ '.$row->item_code.' ] ' : '').$row->item_name.'</td>
                    <td>'.(!empty($row->process_name) ? $row->process_name : '').'</td>
                    <td>'.(!empty($row->price) ? round($row->price,2) : '').'</td>
                </tr>';		  					
            }
        }
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }
}
?>