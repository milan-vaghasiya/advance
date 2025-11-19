<?php
class SalesReport extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Sales Report";
        $this->data['headData']->controller = "reports/salesReport";
    }

    public function orderMonitoring(){
        $this->data['headData']->pageUrl = "reports/salesReport/orderMonitoring";
        $this->data['headData']->pageTitle = "ORDER MONITORING";
        $this->data['pageHeader'] = 'ORDER MONITORING';
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view("reports/sales_report/order_monitoring",$this->data);
    }

    public function getOrderMonitoringData(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $result = $this->salesReport->getOrderMonitoringData($data);
            $tbody=""; $i=1; $blankInTd='';$tfoot=""; $totalQty=0;$totalOrderValue=0;$totalInvQty=0;$totalPendingQty=0;$totalPendingValue=0;
            $blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
         
            if(!empty($result)):
				foreach($result as $row):
					$data['ref_id'] = $row->id;
					$invoiceData = $this->salesReport->getSalesInvData($data);
					$invoiceCount = count($invoiceData);

					$tbody .= '<tr>
						<td class="text-center">'.$i++.'</td>
						<td>'.formatDate($row->trans_date).'</td>
						<td>'.$row->trans_number.'</td>
						<td>'.$row->doc_no.'</td>
						<td>'.$row->party_name.'</td>
						<td>'.$row->item_name.'</td>
						<td>'.floatval($row->qty).'</td>
						<td class="text-right">'.floatval(($row->price * $row->qty)).'</td>
						<td>'.formatDate($row->cod_date).'</td>';
		  
						if($invoiceCount > 0):
							$j=1; $dis_qty = 0;
							foreach($invoiceData as $invRow):
								$daysDiff = '';
								if(!empty($row->cod_date) AND !empty($invRow->invDate)){
									$cod_date = new DateTime($row->cod_date);
									$invDate = new DateTime($invRow->invDate);
									$due_days = $cod_date->diff($invDate)->format("%r%a");
									$daysDiff = ($due_days > 0) ? $due_days : 'On Time';
								}
								$dis_qty += $invRow->invQty;
								$dev_qty = $row->qty - $dis_qty;
								$tbody.='<td>'.formatDate($invRow->invDate).'</td>
										<td>'.$invRow->invNo.'</td>
										<td>'.floatval($invRow->invQty).'</td>
										<td>'.$daysDiff.'</td>
										<td>'.($dev_qty).'</td>
										<td class="text-right">'.numberFormatIndia(($dev_qty * $row->price)).'</td>';

								if($j != $invoiceCount){$tbody.='</tr><tr><td>'.$i++.'</td>'.$blankInTd;}
								
								$totalInvQty += $invRow->invQty;
								
								$totalPendingQty += $dev_qty;
								$totalPendingValue += ($dev_qty * $row->price);
								$j++;
							endforeach;
						else:
							$daysDiff = '';
							if(!empty($row->cod_date)){
								$cod_date = new DateTime($row->cod_date);
								$invDate = new DateTime(date('Y-m-d'));
								$due_days = $cod_date->diff($invDate)->format("%r%a");
								$daysDiff = ($due_days > 0) ? $due_days : 'On Time';
							}
							
							$tbody.='<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>'.$daysDiff.'</td>
									<td>'.floatval($row->qty).'</td>
									<td class="text-right">'.numberFormatIndia(($row->price * $row->qty)).'</td>';
							$totalPendingQty += $row->qty;
							$totalPendingValue += ($row->qty * $row->price);
						endif;
					$tbody.='</tr>';
					
					$totalQty += $row->qty;
					$totalOrderValue += ($row->price * $row->qty);
				endforeach;
            endif;
			$tfoot .= '<tr class="thead-dark">
				<th colspan="6" class="text-right">Total</th>
				<th class="text-center">'.numberFormatIndia($totalQty).'</th> 
				<th class="text-right">'.numberFormatIndia($totalOrderValue).'</th> 
				<th colspan="3"></th>
				<th class="text-center">'.numberFormatIndia($totalInvQty).'</th> 
				<th></th>
				<th class="text-center">'.numberFormatIndia($totalPendingQty).'</th>
				<th class="text-right">'.numberFormatIndia($totalPendingValue).'</th>
			</tr>';
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }

    public function salesAnalysis(){
        $this->data['headData']->pageUrl = "reports/salesReport/salesAnalysis";
        $this->data['headData']->pageTitle = "SALES ANALYSIS";
        $this->data['pageHeader'] = 'SALES ANALYSIS';
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->load->view("reports/sales_report/sales_analysis",$this->data);
    }

    public function getSalesAnalysisData(){
        $data = $this->input->post();
        $result = $this->salesReport->getSalesAnalysisData($data);

        $thead = $tbody = $tfoot = ''; $i=1;
        if($data['report_type'] == 1):
            $thead .= '<tr>
                <th>#</th>
                <th class="text-left">Customer Name</th>
                <th class="text-right">Taxable Amount</th>
                <th class="text-right">GST Amount</th>
                <th class="text-right">Net Amount</th>
            </tr>';

            $taxableAmount = $gstAmount = $netAmount = 0;
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i.'</td>
                    <td class="text-left">'.$row->party_name.'</td>
                    <td class="text-right">'.floatval($row->taxable_amount).'</td>
                    <td class="text-right">'.floatval($row->gst_amount).'</td>
                    <td class="text-right">'.floatval($row->net_amount).'</td>
                </tr>';
                $i++;
                $taxableAmount += floatval($row->taxable_amount);
                $gstAmount += floatval($row->gst_amount);
                $netAmount += floatval($row->net_amount);
            endforeach;

            $tfoot .= '<tr>
                <th colspan="2" class="text-right">Total</th>
                <th class="text-right">'.$taxableAmount.'</th>
                <th class="text-right">'.$gstAmount.'</th>
                <th class="text-right">'.$netAmount.'</th>
            </tr>';
        else:
            $thead .= '<tr>
                <th>#</th>
                <th class="text-left">Item Name</th>
                <th class="text-right">Qty.</th>
                <th class="text-right">Price</th>
                <th class="text-right">Taxable Amount</th>
            </tr>';

            $totalQty = $taxableAmount = 0;
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i.'</td>
                    <td class="text-left">'.$row->item_name.'</td>
                    <td class="text-right">'.floatVal($row->qty).'</td>
                    <td class="text-right">'.floatVal($row->price).'</td>
                    <td class="text-right">'.floatVal($row->taxable_amount).'</td>
                </tr>';
                $i++;
                $totalQty += floatval($row->qty);
                $taxableAmount += floatval($row->taxable_amount);
            endforeach;

            $tfoot .= '<tr>
                <th colspan="2" class="text-right">Total</th>
                <th class="text-right">'.$totalQty.'</th>
                <th></th>
                <th class="text-right">'.$taxableAmount.'</th>
            </tr>';
        endif;

        $this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

    public function mrpReport(){
        $this->data['headData']->pageUrl = "reports/salesReport/mrpReport";
        $this->data['headData']->pageTitle = "MRP REPORT";
        $this->data['pageHeader'] = 'MRP REPORT';
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>'1,2']);
        $this->data['itemList'] = $this->salesOrder->getPendingOrderItems();
        $this->load->view("reports/sales_report/mrp_report",$this->data);
    }

    public function getPendingPartyOrders() {
        $data = $this->input->post();
        $result = $this->salesOrder->getPendingOrderItems($data);

        $itemIds = array_unique(array_column($result, 'item_id'));
        $itemName = array_unique(array_column($result, 'item_name'));

        $options = '<option value="">Select Item</option>';
        foreach($itemIds as $key => $row):
            $options .= '<option value="'.$row.'">'.$itemName[$key].'</option>';
        endforeach;

        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getMrpReport(){
        $data = $this->input->post();
        $result = $this->salesReport->getMrpReportData($data);

         $tfoot=""; $totalQty=0;
        foreach($result as $row):
            $tbody .= '<tr>
                <td class="text-center">'.$i++.'</td>
                <td class="text-left">'.$row->trans_number.'</td>
                <td class="text-left">'.$row->trans_date.'</td>
                <td class="text-left">'.$row->bom_item_name.'</td>
                <td class="text-right">'.floor($row->plan_qty).'</td>
            </tr>';
			$totalQty += floor($row->plan_qty);
        endforeach;
		$tfoot .= '<tr class="thead-dark">
			<th colspan="4" class="text-right">Total</th>
			<th class="text-right">'.$totalQty.'</th> 
        </tr>';
        $this->printJson(['status'=>1,'tbody'=>$tbody, 'tfoot'=>$tfoot]);
    }

    /* Customer Complaints Report*/
    public function customerComplaints(){
        $this->data['headData']->pageUrl = "reports/salesReport/customerComplaints";
        $this->data['headData']->pageTitle = "Customer Complaints";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view("reports/sales_report/customer_complaints",$this->data);
    }

    public function getCustomerComplaintsData($jsonData=''){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else: 
            $data = $this->input->post();
        endif;
      
        $result = $this->salesReport->getCustomerComplaintsData($data);
        $tbody=""; $i=1;
        if(!empty($result)):
            foreach($result as $row):
                $imgFile = '';
                if(!empty($row->defect_image)):
                    $imgPath = base_url('assets/uploads/defect_image/'.$row->defect_image);
                    $imgFile='<div class="picture-item" >
                        <a href="'.$imgPath.'" class="lightbox" target="_blank">
                            <img src="'.$imgPath.'" alt="" class="img-fluid"  width="60" height="60" style="border-radius:0%;border: 0px solid #ccc;padding:3px;"/>
                        </a> 
                        </div> ';
                endif;

                $tbody .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->trans_number.'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->inv_number.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->complaint.'</td>
                    <td>'.$imgFile.'</td>
                    <td>'.$row->report_no.'</td>
                    <td>'.$row->action_taken.'</td>
                    <td>'.$row->ref_feedback.'</td>
                    <td>'.$row->remark.'</td>';
                $tbody.='</tr>';
            endforeach;
        endif;
         
        if(!empty($data['pdf'])):
            $reportTitle = 'Customer Complaints';
            $report_date = formatDate($data['from_date']).' to '.formatDate($data['to_date']);   
            $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
            $thead .='<tr>
                        <th>#</th>
                        <th>Complaint Received Date</th>
                        <th>Complaint No.</th>
                        <th>Customer Name</th>
                        <th>Reference of Complaint</th>
                        <th>Part No.</th>
                        <th>Details of Complaint</th>
                        <th>Defect photos</th>
                        <th>Corrective/ Preventive Action Report No.</th>
                        <th>Action Taken Details</th>
                        <th>Effectiveness</th>
                        <th>Remarks</th>
                    </tr>';

            $logo = base_url('assets/images/logo.png');
            $pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
                <thead class="thead-dark" id="theadData">'.$thead.'</thead>
                <tbody>'.$tbody.'</tbody>
            </table>';
            $htmlHeader = '<table class="table">
                <tr>
                   <td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">Customer Complaints Register</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">MKT/F/04'.'<br>'.'(Rev.01 dtd. 01.01.25</td>
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
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }

	/* Dispatch Details Report */
	public function dispatchDetails(){
        $this->data['headData']->pageUrl = "reports/salesReport/dispatchDetails";
        $this->data['headData']->pageTitle = "Dispatch Details";
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
		$this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view("reports/sales_report/dispatch_details",$this->data);
    }

	public function getDispatchDetailsData(){
        $data = $this->input->post();
      
        $tbody=""; $i=1; $inv_qty = 0;
        $result = $this->salesReport->getDispatchDetailsData($data);
		
        if(!empty($result)):
            foreach($result as $row):
                $tbody .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td>'.$row->trans_number.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
					<td>'.(!empty($row->doc_no) ? $row->doc_no : "").'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->hsn_code.'</td>
					<td>'.formatDate($row->cod_date).'</td>
                    <td>'.$row->batch_no.'</td>
					<td>'.floatVal($row->inv_qty).'</td>';
                $tbody.='</tr>';
				
				$inv_qty += $row->inv_qty;
            endforeach;
        endif;
		
		$tfoot = '<tr class="thead-dark">
			<th colspan="9"></th>
			<th>'.floatVal($inv_qty).'</th>
		</tr>';
        
        $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfootData'=>$tfoot]);
    }
	
}
?>