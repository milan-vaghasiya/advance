<?php
class AccountingReport extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Accounting Report";
        $this->data['headData']->controller = "reports/accountingReport";
    }

    public function salesRegister($startDate="",$endDate=""){
        $this->data['headData']->pageUrl = "reports/accountingReport/salesRegister";
        $this->data['headData']->pageTitle = "SALES REGISTER";
        $this->data['pageHeader'] = 'SALES REGISTER';
        $this->data['startDate'] = (!empty($startDate))?$startDate:getFyDate(date("Y-m-01"));
        $this->data['endDate'] = (!empty($endDate))?$endDate:getFyDate(date("Y-m-d"));
		$this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1"]);
		$this->data['itemList'] = $this->item->getItemList(['item_type'=>'1']);
        $this->load->view("reports/accounting_report/sales_register",$this->data);
    }

	public function getSalesRegisterData(){
        $data = $this->input->post();
        $result = ($data['report_type'] == 1)?$this->accountReport->getRegisterData($data):$this->accountReport->getRegisterDataItemWise($data);

        $thead = '<tr>
            <th>#</th>
            <th>Inv Date</th>
            <th>Inv No.</th>
            <th>Party Name</th>
            <th>Gst No.</th>';

        if($data['report_type'] == 2):
            $thead .= '<th>Item Name</th>';
            $thead .= '<th>HSN Code</th>';
            $thead .= '<th>GST Per(%)</th>';
            $thead .= '<th>Qty.</th>';
            $thead .= '<th>Price</th>';
            $thead .= '<th>Amount</th>';
        else:
            $thead .= '<th>Total Amount</th>';
        endif;

        $thead .= '<th>Disc. Amount</th>
            <th>Taxable Amount</th>
            <th>CGST Amount</th>
            <th>SGST Amount</th>
            <th>IGST Amount</th>
            <th>Other Amount</th>
            <th>Net Amount</th>
        </tr>';

        $tbody = ''; $i =1;
        
        $totalAmount = $totalDiscAmount = $totalTaxableAmount = $totalCgstAmount = $totalSgstAmount = $totalIgstAmount = $totalOtherAmount = $totalNetAmount = 0;

        if($data['report_type'] == 1):
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->trans_number.'</td>
                    <td class="text-left">'.$row->party_name.'</td>
                    <td class="text-left">'.$row->gstin.'</td>
                    <td>'.numberFormatIndia(floatVal($row->total_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->disc_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->taxable_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->cgst_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->sgst_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->igst_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->other_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->net_amount)).'</td>
                </tr>';

                $totalAmount += $row->total_amount;
                $totalDiscAmount += $row->disc_amount;
                $totalTaxableAmount += $row->taxable_amount;
                $totalCgstAmount += $row->cgst_amount;
                $totalSgstAmount += $row->sgst_amount;
                $totalIgstAmount += $row->igst_amount;
                $totalOtherAmount += $row->other_amount;
                $totalNetAmount += $row->net_amount;
            endforeach;
        else:
            $rowspan = 0;
            foreach($result as $row):
				$tbody .= '<tr>
					<td>'.$i++.'</td>
					<td>'.formatDate($row->trans_date).'</td>
					<td>'.$row->trans_number.'</td>
					<td class="text-left">'.$row->party_name.'</td>
					<td class="text-left">'.$row->gstin.'</td>
					<td class="text-left">'.$row->item_name.'</td>
					<td>'.$row->hsn_code.'</td>
					<td>'.floatVal($row->gst_per).'</td>
					<td>'.floatVal($row->qty).'</td>
					<td>'.floatVal($row->price).'</td>
					<td>'.numberFormatIndia(floatVal($row->amount)).'</td>
					<td>'.numberFormatIndia(floatVal($row->disc_amount)).'</td>
					<td>'.numberFormatIndia(floatVal($row->taxable_amount)).'</td>
					<td>'.numberFormatIndia(floatVal($row->cgst_amount)).'</td>
					<td>'.numberFormatIndia(floatVal($row->sgst_amount)).'</td>
					<td>'.numberFormatIndia(floatVal($row->igst_amount)).'</td>
					<td>'.numberFormatIndia(floatVal($row->other_amount)).'</td>
					<td>'.numberFormatIndia(floatVal($row->net_amount)).'</td>
				</tr>';
				
				$totalOtherAmount += $row->other_amount;
				$totalNetAmount += $row->net_amount;
                $totalAmount += $row->amount;
                $totalDiscAmount += $row->disc_amount;
                $totalTaxableAmount += $row->taxable_amount;
                $totalCgstAmount += $row->cgst_amount;
                $totalSgstAmount += $row->sgst_amount;
                $totalIgstAmount += $row->igst_amount;
            endforeach;
        endif;

        $tfoot = '<tr>
            <th colspan="'.(($data['report_type'] == 1)?5:10).'" class="text-right">Total</th>
            <th>'.numberFormatIndia(floatVal($totalAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalDiscAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalTaxableAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalCgstAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalSgstAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalIgstAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalOtherAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalNetAmount)).'</th>
        </tr>';

        $this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }
	
    public function purchaseRegister(){
        $this->data['headData']->pageUrl = "reports/accountingReport/purchaseRegister";
        $this->data['headData']->pageTitle = "PURCHASE REGISTER";
        $this->data['pageHeader'] = 'PURCHASE REGISTER';
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-d"));
        $this->load->view("reports/accounting_report/purchase_register",$this->data);
    }

    public function getPurchaseRegisterData(){
        $data = $this->input->post();
        $result = ($data['report_type'] == 1)?$this->accountReport->getRegisterData($data):$this->accountReport->getRegisterDataItemWise($data);

        $thead = '<tr>
            <th>#</th>
            <th>Inv Date</th>
            <th>Inv No.</th>
            <th>Party Name</th>
            <th>Gst No.</th>';

        if($data['report_type'] == 2):
            $thead .= '<th>Item Name</th>';
            $thead .= '<th>HSN Code</th>';
            $thead .= '<th>GST Per(%)</th>';
            $thead .= '<th>Qty.</th>';
            $thead .= '<th>Price</th>';
            $thead .= '<th>Amount</th>';
        else:
            $thead .= '<th>Total Amount</th>';
        endif;

        $thead .= '<th>Disc. Amount</th>
            <th>Taxable Amount</th>
            <th>CGST Amount</th>
            <th>SGST Amount</th>
            <th>IGST Amount</th>
            <th>Other Amount</th>
            <th>Net Amount</th>
        </tr>';

        $tbody = ''; $i =1;
        
        $totalAmount = $totalDiscAmount = $totalTaxableAmount = $totalCgstAmount = $totalSgstAmount = $totalIgstAmount = $totalOtherAmount = $totalNetAmount = 0;
        if($data['report_type'] == 1):
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->trans_number.'</td>
                    <td class="text-left">'.$row->party_name.'</td>
                    <td class="text-left">'.$row->gstin.'</td>
                    <td>'.numberFormatIndia(floatVal($row->total_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->disc_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->taxable_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->cgst_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->sgst_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->igst_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->other_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->net_amount)).'</td>
                </tr>';

                $totalAmount += $row->total_amount;
                $totalDiscAmount += $row->disc_amount;
                $totalTaxableAmount += $row->taxable_amount;
                $totalCgstAmount += $row->cgst_amount;
                $totalSgstAmount += $row->sgst_amount;
                $totalIgstAmount += $row->igst_amount;
                $totalOtherAmount += $row->other_amount;
                $totalNetAmount += $row->net_amount;
            endforeach;
        else:
            $rowspan = 0;$transMainIds = array();
            foreach($result as $row):
                if(!in_array($row->id,$transMainIds)):
                    $transMainIds[] = $row->id;
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td>'.$row->trans_number.'</td>
                        <td class="text-left">'.$row->party_name.'</td>
                        <td class="text-left">'.$row->gstin.'</td>
                        <td class="text-left">'.$row->item_name.'</td>
                        <td>'.$row->hsn_code.'</td>
                        <td>'.floatVal($row->gst_per).'</td>
                        <td>'.floatVal($row->qty).'</td>
                        <td>'.floatVal($row->price).'</td>
                        <td>'.numberFormatIndia(floatVal($row->amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->disc_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->taxable_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->cgst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->sgst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->igst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->other_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->net_amount)).'</td>
                    </tr>';

                    $totalOtherAmount += $row->other_amount;
                    $totalNetAmount += $row->net_amount;
                else:
                    $tbody .= '<tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-left">'.$row->item_name.'</td>
                        <td>'.$row->hsn_code.'</td>
                        <td>'.floatVal($row->gst_per).'</td>
                        <td>'.floatVal($row->qty).'</td>
                        <td>'.floatVal($row->price).'</td>
                        <td>'.numberFormatIndia(floatVal($row->amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->disc_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->taxable_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->cgst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->sgst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->igst_amount)).'</td>
                        <td></td>
                        <td></td>
                    </tr>';
                endif;

                $totalAmount += $row->amount;
                $totalDiscAmount += $row->disc_amount;
                $totalTaxableAmount += $row->taxable_amount;
                $totalCgstAmount += $row->cgst_amount;
                $totalSgstAmount += $row->sgst_amount;
                $totalIgstAmount += $row->igst_amount;
            endforeach;
        endif;

        $tfoot = '<tr>
            <th colspan="'.(($data['report_type'] == 1)?5:10).'" class="text-right">Total</th>
            <th>'.numberFormatIndia(floatVal($totalAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalDiscAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalTaxableAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalCgstAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalSgstAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalIgstAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalOtherAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalNetAmount)).'</th>
        </tr>';

        $this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

    public function accountLedger(){
        $this->data['headData']->pageUrl = "reports/accountingReport/accountLedger";
        $this->data['headData']->pageTitle = "ACCOUNT LEDGER";
        $this->data['pageHeader'] = 'ACCOUNT LEDGER';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view("reports/accounting_report/account_ledger",$this->data);
    }

    public function getAccountLedgerData($jsonData=""){
        if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        else: 
            $postData = $this->input->post();
        endif;

        $ledgerSummary = $this->accountReport->getLedgerSummary($postData);
        $i=1; $tbody="";
        foreach($ledgerSummary as $row):
            if(empty($jsonData)):
                $accountName = '<a href="' . base_url('reports/accountingReport/ledgerDetail/' . encodeURL($row->id)) . '" target="_blank" datatip="Account Details" flow="down"><b>'.$row->account_name.'</b></a>';
            else:
                $accountName = $row->account_name;
            endif;

            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td class="text-left">'.$accountName.'</td>
                <td class="text-left">'.$row->group_name.'</td>
                <td class="text-right">'.numberFormatIndia($row->op_balance).' '.$row->op_balance_type.'</td>
                <td class="text-right">'.numberFormatIndia($row->cr_balance).'</td>
                <td class="text-right">'.numberFormatIndia($row->dr_balance).'</td>
                <td class="text-right">'.numberFormatIndia($row->cl_balance).' '.$row->cl_balance_type.'</td>
            </tr>';
        endforeach;         
        
        if(!empty($postData['pdf'])):
            $reportTitle = 'ACCOUNT LEDGER';
            $report_date = date('d-m-Y',strtotime($postData['from_date'])).' to '.date('d-m-Y',strtotime($postData['to_date']));   
            $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
            $thead .= '<tr>
                <th>#</th>
                <th class="text-left">Account Name</th>
                <th class="text-left">Group Name</th>
                <th class="text-right">Opening Amount</th>
                <th class="text-right">Credit Amount</th>
                <th class="text-right">Debit Amount</th>
                <th class="text-right">Closing Amount</th>
            </tr>';

            $companyData = $this->masterModel->getCompanyInfo();
            $logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
            $logo = base_url('assets/images/' . $logoFile);
            $letter_head = base_url('assets/images/letterhead_top.png');
            
            $pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
                <thead class="thead-dark" id="theadData">'.$thead.'</thead>
                <tbody>'.$tbody.'</tbody>
            </table>';
            $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                <tr>
                    <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">'.$reportTitle.'</td>
                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">'.$report_date.'</td>
                </tr>
            </table>
            <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
            </table>';
            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';

            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = 'AccountLedger.pdf';
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

    public function ledgerDetail($acc_id,$start_date="",$end_date=""){
        $acc_id = decodeURL($acc_id);
        $this->data['headData']->pageUrl = "reports/accountingReport/accountLedger";
	    $this->data['headData']->pageTitle = "ACCOUNT LEDGER DETAIL";
        $this->data['pageHeader'] = 'ACCOUNT LEDGER DETAIL';
        $ledgerData = $this->party->getParty(['id'=>$acc_id]);
        $this->data['acc_id'] = $acc_id;
        $this->data['acc_name'] = $ledgerData->party_name;
        $this->data['ledgerData'] = $ledgerData;
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view("reports/accounting_report/account_ledger_detail",$this->data);
    }

    public function getLedgerTransaction($jsonData=""){
        if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        else:
            $postData = $this->input->post();
        endif; 
        
        $ledgerTransactions = $this->accountReport->getLedgerDetail($postData);
        $ledgerBalance = $this->accountReport->getLedgerBalance($postData);

        $i=1; $tbody="";$balance = $ledgerBalance->op_balance;
        foreach($ledgerTransactions as $row):
            $balance += round(($row->amount * $row->p_or_m),2); 
            $balance = round($balance,2);
            $balanceText = ($balance > 0)?numberFormatIndia(abs($balance))." CR":(($balance < 0)?numberFormatIndia(abs($balance))." DR":0);

            if(empty($postData['pdf'])):
                $vouPostData = ['id'=>$row->id,'original'=>1,'duplicate'=>0,'triplicate'=>0,'extra_copy'=>0,'header_footer'=>1];
                if($row->vou_name_s == "Sale"):                
                    $row->trans_number = '<a href="'.base_url('salesInvoice/printInvoice/'.encodeURL($vouPostData)).'" target="_blank">'.$row->trans_number.'</a>'; 
                elseif($row->vou_name_s == "C.N."):
                    $row->trans_number = '<a href="'.base_url('creditNote/printCreditNote/'.encodeURL($vouPostData)).'" target="_blank">'.$row->trans_number.'</a>'; 
                elseif($row->vou_name_s == "Purc"):
                    $row->trans_number = '<a href="'.base_url('purchaseInvoice/printInvoice/'.$row->id).'" target="_blank">'.$row->trans_number.'</a>';
                elseif($row->vou_name_s == "D.N."):
                    $row->trans_number = '<a href="'.base_url('debitNote/printDebitNote/'.encodeURL($vouPostData)).'" target="_blank">'.$row->trans_number.'</a>'; 
                elseif(in_array($row->vou_name_s,["BCRct","BCPmt"])):
                    $row->trans_number = '<a href="'.base_url('paymentVoucher/printReceipt/'.encodeURL($vouPostData)).'" target="_blank">'.$row->trans_number.'</a>';
                elseif($row->vou_name_s == "Jrnl"):
                    $row->trans_number = '<a href="'.base_url('journalEntry/printJV/'.$row->id).'" target="_blank">'.$row->trans_number.'</a>';
                endif;
            endif;
            
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.formatDate($row->trans_date).'</td>
                <td>'.$row->account_name.'</td>
                <td>'.$row->vou_name_s.'</td>
                <td>'.$row->trans_number.'</td>
                <td class="text-right">'.numberFormatIndia($row->cr_amount).'</td>
                <td class="text-right">'.numberFormatIndia($row->dr_amount).'</td>
                <td style="text-align: center;">'.$balanceText.'</td>
            </tr>';
        endforeach;    
        
        $ledgerBalance->op_balance = numberFormatIndia(abs($ledgerBalance->op_balance));
        $ledgerBalance->cr_balance = numberFormatIndia(abs($ledgerBalance->cr_balance));
        $ledgerBalance->dr_balance = numberFormatIndia(abs($ledgerBalance->dr_balance));
        $ledgerBalance->cl_balance = numberFormatIndia(abs($ledgerBalance->cl_balance));
        $ledgerBalance->bcl_balance_text = (in_array($ledgerBalance->group_code,['BA','BOL','BOA']))?"As Per Bank Balance : ".numberFormatIndia(abs($ledgerBalance->bcl_balance))." ".$ledgerBalance->bcl_balance_type:"";
        
        if(!empty($postData['pdf'])):
            $acc_name=$this->party->getParty(['id'=>$postData['acc_id']])->party_name;
            $reportTitle = $acc_name;
            $report_date = date('d-m-Y',strtotime($postData['from_date'])).' to '.date('d-m-Y',strtotime($postData['to_date']));   
            $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';

            $companyData = $this->masterModel->getCompanyInfo();
			$logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
			$logo = base_url('assets/images/' . $logoFile);
			$letter_head = base_url('assets/images/letterhead_top.png');

            $thead .= '<tr>
                <th>#</th>
                <th>Date</th>
                <th>Particulars</th>
                <th>Voucher Type</th>
                <th>Ref.No.</th>
                <th>Amount(CR.)</th>
                <th>Amount(DR.)</th>
                <th>Balance</th>
            </tr>';

            $pdfData = '<table id="commanTable" class="table table-bordered item-list-bb" repeat_header="1">
                <thead class="thead-dark" id="theadData">'.$thead.'</thead>
                <tbody id="receivableData">'.$tbody.'</tbody>
                <tfoot class="thead-dark">
                    <tr>
                        <th colspan="5" class="text-right">Total</th>
                        <th id="cr_balance">'.$ledgerBalance->cr_balance.'</th>
                        <th id="dr_balance">'.$ledgerBalance->dr_balance.'</th>
                        <th></th>
                    </tr>
                </tfoot>    
            </table>
            <table class="table" style="border-top:1px solid #036aae;border-bottom:1px solid #036aae;margin-bottom:10px;margin-top:10px;">
                <tr>
                    <td class="org_title text-uppercase text-left" style="font-size:1rem;width:50%">'.$ledgerBalance->bcl_balance_text.'</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:50%"> Closing Balance: '.$ledgerBalance->cl_balance.' '.$ledgerBalance->cl_balance_type.'</td>
                </tr>
            </table>';

            $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                <tr>
                    <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%"></td>
                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"></td>
                </tr>
            </table>
            <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
            </table>
            <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:10px;">
                <tr>
                    <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">Date : '.$report_date.'</td>
                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"> Opening Balance: '.$ledgerBalance->op_balance.' '.$ledgerBalance->op_balance_type.'</td>
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
            $pdfFileName = $filePath.'/AccountLedgerDetail.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,30,5,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);
            
            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        
        else:
            $this->printJson(['status'=>1, 'tbody'=>$tbody,'ledgerBalance'=>$ledgerBalance]);
        endif;
    }

    public function creditNoteRegister(){
        $this->data['headData']->pageUrl = "reports/accountingReport/creditNoteRegister";
        $this->data['headData']->pageTitle = "CREDIT NOTE REGISTER";
        $this->data['pageHeader'] = 'CREDIT NOTE REGISTER';
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-d"));
        $this->load->view("reports/accounting_report/credit_note_register",$this->data);
    }

    public function getCreditNoteRegisterData(){
        $data = $this->input->post();
        $result = ($data['report_type'] == 1)?$this->accountReport->getRegisterData($data):$this->accountReport->getRegisterDataItemWise($data);

        $thead = '<tr>
            <th>#</th>
            <th>CN Date</th>
            <th>CN No.</th>
            <th>CN Type</th>
            <th>Party Name</th>
            <th>Gst No.</th>';

        if($data['report_type'] == 2):
            $thead .= '<th>Item Name</th>';
            $thead .= '<th>HSN Code</th>';
            $thead .= '<th>GST Per(%)</th>';
            $thead .= '<th>Qty.</th>';
            $thead .= '<th>Price</th>';
            $thead .= '<th>Amount</th>';
        else:
            $thead .= '<th>Total Amount</th>';
        endif;
        
        $thead .= '<th>Disc. Amount</th>
            <th>Taxable Amount</th>
            <th>CGST Amount</th>
            <th>SGST Amount</th>
            <th>IGST Amount</th>
            <th>Other Amount</th>
            <th>Net Amount</th>
        </tr>';

        $tbody = ''; $i =1;
        
        $totalAmount = $totalDiscAmount = $totalTaxableAmount = $totalCgstAmount = $totalSgstAmount = $totalIgstAmount = $totalOtherAmount = $totalNetAmount = 0;
        if($data['report_type'] == 1):
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->order_type.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->trans_number.'</td>
                    <td class="text-left">'.$row->party_name.'</td>
                    <td class="text-left">'.$row->gstin.'</td>
                    <td>'.numberFormatIndia(floatVal($row->total_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->disc_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->taxable_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->cgst_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->sgst_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->igst_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->other_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->net_amount)).'</td>
                </tr>';

                $totalAmount += $row->total_amount;
                $totalDiscAmount += $row->disc_amount;
                $totalTaxableAmount += $row->taxable_amount;
                $totalCgstAmount += $row->cgst_amount;
                $totalSgstAmount += $row->sgst_amount;
                $totalIgstAmount += $row->igst_amount;
                $totalOtherAmount += $row->other_amount;
                $totalNetAmount += $row->net_amount;
            endforeach;
        else:
            $rowspan = 0;$transMainIds = array();
            foreach($result as $row):
                if(!in_array($row->id,$transMainIds)):
                    $transMainIds[] = $row->id;
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->order_type.'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td>'.$row->trans_number.'</td>
                        <td class="text-left">'.$row->party_name.'</td>
                        <td class="text-left">'.$row->gstin.'</td>
                        <td class="text-left">'.$row->item_name.'</td>
                        <td>'.$row->hsn_code.'</td>
                        <td>'.floatVal($row->gst_per).'</td>
                        <td>'.floatVal($row->qty).'</td>
                        <td>'.floatVal($row->price).'</td>
                        <td>'.numberFormatIndia(floatVal($row->amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->disc_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->taxable_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->cgst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->sgst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->igst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->other_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->net_amount)).'</td>
                    </tr>';

                    $totalOtherAmount += $row->other_amount;
                    $totalNetAmount += $row->net_amount;
                else:
                    $tbody .= '<tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-left">'.$row->item_name.'</td>
                        <td>'.$row->hsn_code.'</td>
                        <td>'.floatVal($row->gst_per).'</td>
                        <td>'.floatVal($row->qty).'</td>
                        <td>'.floatVal($row->price).'</td>
                        <td>'.numberFormatIndia(floatVal($row->amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->disc_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->taxable_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->cgst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->sgst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->igst_amount)).'</td>
                        <td></td>
                        <td></td>
                    </tr>';
                endif;

                $totalAmount += $row->amount;
                $totalDiscAmount += $row->disc_amount;
                $totalTaxableAmount += $row->taxable_amount;
                $totalCgstAmount += $row->cgst_amount;
                $totalSgstAmount += $row->sgst_amount;
                $totalIgstAmount += $row->igst_amount;
            endforeach;
        endif;

        $tfoot = '<tr>
            <th colspan="'.(($data['report_type'] == 1)?6:11).'" class="text-right">Total</th>
            <th>'.numberFormatIndia(floatVal($totalAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalDiscAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalTaxableAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalCgstAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalSgstAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalIgstAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalOtherAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalNetAmount)).'</th>
        </tr>';

        $this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

    public function debitNoteRegister(){
        $this->data['headData']->pageUrl = "reports/accountingReport/debitNoteRegister";
        $this->data['headData']->pageTitle = "DEBIT NOTE REGISTER";
        $this->data['pageHeader'] = 'DEBIT NOTE REGISTER';
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-d"));
        $this->load->view("reports/accounting_report/debit_note_register",$this->data);
    }

    public function getDebitNoteRegisterData(){
        $data = $this->input->post();
        $result = ($data['report_type'] == 1)?$this->accountReport->getRegisterData($data):$this->accountReport->getRegisterDataItemWise($data);

        $thead = '<tr>
            <th>#</th>
            <th>DN Date</th>
            <th>DN No.</th>
            <th>DN Type</th>
            <th>Party Name</th>
            <th>Gst No.</th>';

        if($data['report_type'] == 2):
            $thead .= '<th>Item Name</th>';
            $thead .= '<th>HSN Code</th>';
            $thead .= '<th>GST Per(%)</th>';
            $thead .= '<th>Qty.</th>';
            $thead .= '<th>Price</th>';
            $thead .= '<th>Amount</th>';
        else:
            $thead .= '<th>Total Amount</th>';
        endif;

        $thead .= '<th>Disc. Amount</th>
            <th>Taxable Amount</th>
            <th>CGST Amount</th>
            <th>SGST Amount</th>
            <th>IGST Amount</th>
            <th>Other Amount</th>
            <th>Net Amount</th>
        </tr>';

        $tbody = ''; $i =1;
        
        $totalAmount = $totalDiscAmount = $totalTaxableAmount = $totalCgstAmount = $totalSgstAmount = $totalIgstAmount = $totalOtherAmount = $totalNetAmount = 0;
        if($data['report_type'] == 1):
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->order_type.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->trans_number.'</td>
                    <td class="text-left">'.$row->party_name.'</td>
                    <td class="text-left">'.$row->gstin.'</td>
                    <td>'.numberFormatIndia(floatVal($row->total_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->disc_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->taxable_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->cgst_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->sgst_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->igst_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->other_amount)).'</td>
                    <td>'.numberFormatIndia(floatVal($row->net_amount)).'</td>
                </tr>';

                $totalAmount += $row->total_amount;
                $totalDiscAmount += $row->disc_amount;
                $totalTaxableAmount += $row->taxable_amount;
                $totalCgstAmount += $row->cgst_amount;
                $totalSgstAmount += $row->sgst_amount;
                $totalIgstAmount += $row->igst_amount;
                $totalOtherAmount += $row->other_amount;
                $totalNetAmount += $row->net_amount;
            endforeach;
        else:
            $rowspan = 0;$transMainIds = array();
            foreach($result as $row):
                if(!in_array($row->id,$transMainIds)):
                    $transMainIds[] = $row->id;
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->order_type.'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td>'.$row->trans_number.'</td>
                        <td class="text-left">'.$row->party_name.'</td>
                        <td class="text-left">'.$row->gstin.'</td>
                        <td class="text-left">'.$row->item_name.'</td>
                        <td>'.$row->hsn_code.'</td>
                        <td>'.floatVal($row->gst_per).'</td>
                        <td>'.floatVal($row->qty).'</td>
                        <td>'.floatVal($row->price).'</td>
                        <td>'.numberFormatIndia(floatVal($row->amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->disc_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->taxable_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->cgst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->sgst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->igst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->other_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->net_amount)).'</td>
                    </tr>';

                    $totalOtherAmount += $row->other_amount;
                    $totalNetAmount += $row->net_amount;
                else:
                    $tbody .= '<tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-left">'.$row->item_name.'</td>
                        <td>'.$row->hsn_code.'</td>
                        <td>'.floatVal($row->gst_per).'</td>
                        <td>'.floatVal($row->qty).'</td>
                        <td>'.floatVal($row->price).'</td>
                        <td>'.numberFormatIndia(floatVal($row->amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->disc_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->taxable_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->cgst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->sgst_amount)).'</td>
                        <td>'.numberFormatIndia(floatVal($row->igst_amount)).'</td>
                        <td></td>
                        <td></td>
                    </tr>';
                endif;

                $totalAmount += $row->amount;
                $totalDiscAmount += $row->disc_amount;
                $totalTaxableAmount += $row->taxable_amount;
                $totalCgstAmount += $row->cgst_amount;
                $totalSgstAmount += $row->sgst_amount;
                $totalIgstAmount += $row->igst_amount;
            endforeach;
        endif;

        $tfoot = '<tr>
            <th colspan="'.(($data['report_type'] == 1)?6:11).'" class="text-right">Total</th>
            <th>'.numberFormatIndia(floatVal($totalAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalDiscAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalTaxableAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalCgstAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalSgstAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalIgstAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalOtherAmount)).'</th>
            <th>'.numberFormatIndia(floatVal($totalNetAmount)).'</th>
        </tr>';

        $this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

    public function outstandingReport(){
        $this->data['headData']->pageUrl = "reports/accountingReport/outstandingReport";
        $this->data['headData']->pageTitle = "OUTSTANDING REGISTER";
        $this->data['pageHeader'] = 'OUTSTANDING REGISTER';
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-d"));
        $this->load->view("reports/accounting_report/outstanding_register",$this->data);
    }

    public function getOutstandingData($jsonData=''){
        if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        else:
            $postData = $this->input->post();
        endif;

        if($postData['report_type']==2):
            $postData['from_date'] = $this->startYearDate;
            $postData['to_date'] = $this->endYearDate;
        endif;

        $outstandingData = $this->accountReport->getOutstandingData($postData);

        $i=1; $thead = $tbody = $tfoot = ""; $daysTotal=Array();
        $totalClBalance = $below30 = $age60 = $age90 = $age120 = $above120 = 0;

        $reportTitle = 'OUTSTANDING LEDGER';
        $report_date = formatDate($postData['from_date']).' to '.formatDate($postData['to_date']);

        $rangeLength = (!empty($postData['days_range'])) ? count($postData['days_range']) : 0;
        $totalHeadCols = ($rangeLength > 0) ? ($rangeLength + 6) : 5;

        if($postData['report_type'] == 1):
            $reportTitle = ($postData['os_type'] == 'R') ? 'RECEIVABLE SUMMARY REPORT' : 'PAYABLE SUMMARY REPORT';
            $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="'.$totalHeadCols.'">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
            $thead .= '<tr>
                <th>#</th>
                <th>Account Name</th>
                <th>Contact Person</th>
                <th>Contact Number</th>
                <th class="text-right">Closing Balance</th>
            </tr>';
        else:
            $reportTitle = ($postData['os_type'] == 'R') ? 'RECEIVABLE AGEWISE REPORT' : 'PAYABLE AGEWISE REPORT';
			$thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="'.$totalHeadCols.'">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
			$thead .= '<tr>
                <th>#</th>
                <th>Account Name</th>
                <th>Contact Person</th>
                <th>Contact Number</th>
                <th class="text-right">Closing Balance</th>';

            $i=1;$dayCols = '';
		    if(!empty($postData['days_range'])):
    		    foreach($postData['days_range'] as $days):
    		        if($i == 1): $dayCols .= '<th class="text-right">Below '.$days.'</th>'; endif;
    		        if($i == $rangeLength): $dayCols .= '<th class="text-right">Above '.$days.'</th>'; endif;
    		        if($i < $rangeLength): $dayCols .= '<th class="text-right">'.($days+1).' - '.$postData['days_range'][$i].'</th>'; endif;
    		        $i++;
                endforeach;
		    endif;
		    $thead .= $dayCols;
		    $thead .= '</tr>';
        endif;

        $i=1;
        foreach($outstandingData as $row):
			$ageGroup = '';
			if($postData['report_type'] == 2):
			    if($rangeLength > 0):
    			    for($x=1;$x<=($rangeLength+1);$x++):
    			        $fieldName = 'd'.$x; if(!isset($daysTotal[$x-1])): $daysTotal[$x-1] = 0; endif;
    			        $ageGroup .= '<td class="text-right">'.numberFormatIndia($row->{$fieldName}).'</td>';
    			        $daysTotal[$x-1] += $row->{$fieldName};
                    endfor;
			    endif;
			endif;

			$accountName = $row->account_name;
			if(empty($jsonData)):
				$accountName = '<a href="' . base_url('reports/accountingReport/ledgerDetail/' . encodeURL($row->id).'/'.$this->startYearDate.'/'.$this->endYearDate) . '" target="_blank" datatip="Account" flow="down"><b>'.$row->account_name.'</b></a>';
            endif;
			
			$tbody .= '<tr>
				<td>'.$i++.'</td>
				<td class="text-left">'.$accountName.'</td>
				<td>'.$row->contact_person.'</td>
				<td>'.$row->party_mobile.'</td>
				<td class="text-right">'.numberFormatIndia($row->cl_balance).'</td>'.$ageGroup.'
			</tr>';

			$totalClBalance += $row->cl_balance;
			
		endforeach;

        if($postData['report_type'] == 1):
            $tfoot = '<tr><th colspan="4" class="text-right">Total</th><th class="text-right">'.numberFormatIndia($totalClBalance).'</th></tr>';
		else:
			$tfoot = '<tr class="text-right"><th colspan="4" class="text-right">Total</th>';
			$tfoot .= '<th>'.numberFormatIndia($totalClBalance).'</th>';
			foreach($daysTotal as $total): $tfoot .= '<th>'.numberFormatIndia($total).'</th>'; endforeach;
			$tfoot .= '</tr>';
        endif;

        if(!empty($jsonData)):
            $companyData = $this->masterModel->getCompanyInfo();
			$logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
			$logo = base_url('assets/images/' . $logoFile);
			$letter_head = base_url('assets/images/letterhead_top.png');

            $pdfData = '<table id="commanTable" class="table table-bordered item-list-bb" repeat_header="1">
                <thead class="thead-dark" id="theadData">'.$thead.'</thead>
                <tbody id="receivableData">'.$tbody.'</tbody>
                <tfoot class="thead-dark tfoot">'.$tfoot.'</tfoot>
            </table>';

            $htmlHeader = '<img src="' . $letter_head . '">';

            $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                <tr>
                    <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">'.$reportTitle.'</td>
                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">Date : '.$report_date.'</td>
                </tr>
            </table>
            <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
            </table>';

			$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';
			
			$mpdf = new \Mpdf\Mpdf();
    		$filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/Outstanding.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
			$mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
			$mpdf->showWatermarkImage = true;
			$mpdf->SetTitle($reportTitle);
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->SetHTMLFooter($htmlFooter);
            //$mpdf->SetProtection(array('print'));
    
    		$mpdf->AddPage('L','','','','',5,5,19,5,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);
    		
    		ob_clean();
    		$mpdf->Output($pdfFileName, 'I');
        else:
            $this->printJson(['status'=>1, 'thead'=>$thead,'tbody'=>$tbody,'tfoot'=>$tfoot]);
        endif;
    }

    public function duePaymentReminder($reportType = ""){
        $this->data['headData']->pageUrl = "reports/accountingReport/duePaymentReminder";
        $this->data['headData']->pageTitle = "DUE PAYMENT REMINDER";
        $this->data['pageHeader'] = 'DUE PAYMENT REMINDER';
        $this->data['report_type'] = $reportType;
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>'1,2,3']);
        $this->load->view("reports/accounting_report/due_payment_reminder",$this->data);
    }

    public function getDuePaymentReminderData(){
        $data = $this->input->post();
        $data['vou_name_s'] = ($data['report_type'] == "Receivable")?"'Sale','GInc','D.N.'":"'Purc','GExp','C.N.'";
        $result = $this->accountReport->getDuePaymentReminderData($data);

        $tbody = '';$i = 1; $vouAmount = $ropAmount = $dueAmount = 0;
        foreach($result as $row):
            $row->rop_amount = floatval($row->net_amount - $row->due_amount);

            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->trans_number.'</td>
                <td>'.formatDate($row->trans_date).'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->party_mobile.'</td>
                <td>'.numberFormatIndia($row->net_amount).'</td>
                <td>'.numberFormatIndia($row->rop_amount).'</td>
                <td>'.numberFormatIndia($row->due_amount).'</td>
                <td>'.formatDate($row->due_date).'</td>
                <td class="'.(($row->due_days > 0)?"text-danger":"text-success").'">'.$row->due_days.'</td>
            </tr>';

            $vouAmount += $row->net_amount;
            $ropAmount += $row->rop_amount;
            $dueAmount += $row->due_amount;
        endforeach;

        $tfoot = '<tr>
            <th colspan="5" class="text-right">Total</th>
            <th>'.numberFormatIndia($vouAmount).'</th>
            <th>'.numberFormatIndia($ropAmount).'</th>
            <th>'.numberFormatIndia($dueAmount).'</th>
            <th></th>
            <th></th>
        </tr>';

        $this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

    public function bankBook(){
        $this->data['headData']->pageUrl = "reports/accountingReport/bankBook";
        $this->data['headData']->pageTitle = "BANK BOOK";
        $this->data['pageHeader'] = 'BANK BOOK';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view("reports/accounting_report/bank_book",$this->data);
    }

    public function getBankBookData($jsonData=''){
        if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        else: 
            $postData = $this->input->post();
        endif;

        $ledgerSummary = $this->accountReport->getBankCashBook($postData);
        $i=1; $tbody="";
        foreach($ledgerSummary as $row):
            if(empty($jsonData)):
                $accountName = '<a href="' . base_url('reports/accountingReport/ledgerDetail/' . encodeURL($row->id)) . '" target="_blank" datatip="Account Details" flow="down"><b>'.$row->account_name.'</b></a>';
            else:
                $accountName = $row->account_name;
            endif;

            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td class="text-left">'.$accountName.'</td>
                <td class="text-left">'.$row->group_name.'</td>
                <td class="text-right">'.numberFormatIndia($row->op_balance).' '.$row->op_balance_type.'</td>
                <td class="text-right">'.numberFormatIndia($row->cr_balance).'</td>
                <td class="text-right">'.numberFormatIndia($row->dr_balance).'</td>
                <td class="text-right">'.numberFormatIndia($row->cl_balance).' '.$row->cl_balance_type.'</td>
                <td class="text-right">'.numberFormatIndia($row->bcl_balance).' '.$row->bcl_balance_type.'</td>
            </tr>';
        endforeach;
        
        if(!empty($postData['pdf'])):
            $reportTitle = 'Bank Book';
            $report_date = formatDate($postData['from_date']).' to '.formatDate($postData['to_date']);   
            $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
            $thead .= '<tr>
                <th>#</th>
                <th class="text-left">Bank Name</th>
                <th class="text-left">Group Name</th>
                <th class="text-right">Opening Amount</th>
                <th class="text-right">Credit Amount</th>
                <th class="text-right">Debit Amount</th>
                <th class="text-right">Closing Amount</th>
                <th class="text-rigth">As Per Bank<br>Closing Amount</th>
            </tr>';

            $companyData = $this->masterModel->getCompanyInfo();
            $logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
            $logo = base_url('assets/images/' . $logoFile);
            $letter_head = base_url('assets/images/letterhead_top.png');
            
            $pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
                <thead class="thead-dark" id="theadData">'.$thead.'</thead>
                <tbody>'.$tbody.'</tbody>
            </table>';
            $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                <tr>
                    <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">'.$reportTitle.'</td>
                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">'.$report_date.'</td>
                </tr>
            </table>
            <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
            </table>';
            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';

            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/AccountLedger.pdf';
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
            $this->printJson(['status'=>1,'tbody'=>$tbody]);
        endif;
    }

    public function cashBook(){
        $this->data['headData']->pageUrl = "reports/accountingReport/cashBook";
        $this->data['headData']->pageTitle = "CASH BOOK";
        $this->data['pageHeader'] = 'CASH BOOK';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view("reports/accounting_report/cash_book",$this->data);
    }

    public function getCashBookData($jsonData=''){
        if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        else: 
            $postData = $this->input->post();
        endif;

        $ledgerSummary = $this->accountReport->getBankCashBook($postData);
        $i=1; $tbody="";
        foreach($ledgerSummary as $row):
            if(empty($jsonData)):
                $accountName = '<a href="' . base_url('reports/accountingReport/ledgerDetail/' . encodeURL($row->id)) . '" target="_blank" datatip="Account Details" flow="down"><b>'.$row->account_name.'</b></a>';
            else:
                $accountName = $row->account_name;
            endif;

            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td class="text-left">'.$accountName.'</td>
                <td class="text-left">'.$row->group_name.'</td>
                <td class="text-right">'.numberFormatIndia($row->op_balance).' '.$row->op_balance_type.'</td>
                <td class="text-right">'.numberFormatIndia($row->cr_balance).'</td>
                <td class="text-right">'.numberFormatIndia($row->dr_balance).'</td>
                <td class="text-right">'.numberFormatIndia($row->cl_balance).' '.$row->cl_balance_type.'</td>
            </tr>';
        endforeach;
        
        if(!empty($postData['pdf'])):
            $reportTitle = 'Cash Book';
            $report_date = formatDate($postData['from_date']).' to '.formatDate($postData['to_date']);   
            $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
            $thead .= '<tr>
                <th>#</th>
                <th class="text-left">Bank Name</th>
                <th class="text-left">Group Name</th>
                <th class="text-right">Opening Amount</th>
                <th class="text-right">Credit Amount</th>
                <th class="text-right">Debit Amount</th>
                <th class="text-right">Closing Amount</th>
            </tr>';

            $companyData = $this->masterModel->getCompanyInfo();
            $logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
            $logo = base_url('assets/images/' . $logoFile);
            $letter_head = base_url('assets/images/letterhead_top.png');
            
            $pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
                <thead class="thead-dark" id="theadData">'.$thead.'</thead>
                <tbody>'.$tbody.'</tbody>
            </table>';
            $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                <tr>
                    <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">'.$reportTitle.'</td>
                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">'.$report_date.'</td>
                </tr>
            </table>
            <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
            </table>';
            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';

            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/AccountLedger.pdf';
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
            $this->printJson(['status'=>1,'tbody'=>$tbody]);
        endif;
    }

    public function salesSummary(){
        $this->data['headData']->pageUrl = "reports/accountingReport/salesSummary";
        $this->data['headData']->pageTitle = "SALES SUMMARY";
        $this->data['pageHeader'] = 'SALES SUMMARY';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view("reports/accounting_report/sales_summary",$this->data);
    }

    public function getSalesSummary(){
        $data = $this->input->post();
        $data['from_date'] = $this->startYearDate;
        $data['to_date'] = $this->endYearDate;
        $data['vou_name_s'] = "'Sale','GInc'";
        $result = $this->accountReport->getMonthlySummary($data);

        $thead = '<tr>
            <th>Month</th>
            <th>Taxable Amount</th>
            <th>IGST Amount</th>
            <th>CGST Amount</th>
            <th>SGST Amount</th>
            <th>Net Amount</th>
        </tr>';

        $tbody = ''; $i =1;
        
        $totalTaxableAmount = $totalCgstAmount = $totalSgstAmount = $totalIgstAmount = $totalNetAmount = 0;
        foreach($result as $row):
            $month = '<a href="'.base_url("reports/accountingReport/salesRegister/".date("Y-m-01",strtotime($row->month_name))."/".date("Y-m-t",strtotime($row->month_name))).'" target="_blank">'.$row->month_name.'</a>';
            $tbody .= '<tr>
                <td class="text-left">'.$month.'</td>
                <td class="text-right">'.floatVal($row->total_taxable_amount).'</td>
                <td class="text-right">'.floatVal($row->total_igst_amount).'</td>
                <td class="text-right">'.floatVal($row->total_cgst_amount).'</td>
                <td class="text-right">'.floatVal($row->total_sgst_amount).'</td>
                <td class="text-right">'.floatVal($row->total_net_amount).'</td>
            </tr>';

            $totalTaxableAmount += $row->total_taxable_amount;
            $totalCgstAmount += $row->total_cgst_amount;
            $totalSgstAmount += $row->total_sgst_amount;
            $totalIgstAmount += $row->total_igst_amount;
            $totalNetAmount += $row->total_net_amount;
        endforeach;

        $tfoot = '<tr>
            <th class="text-left">Total</th>
            <th class="text-right">'.floatVal($totalTaxableAmount).'</th>
            <th class="text-right">'.floatVal($totalIgstAmount).'</th>
            <th class="text-right">'.floatVal($totalCgstAmount).'</th>
            <th class="text-right">'.floatVal($totalSgstAmount).'</th>
            <th class="text-right">'.floatVal($totalNetAmount).'</th>
        </tr>';

        $this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

    public function hsnSummary(){
        $this->data['headData']->pageUrl = "reports/accountingReport/hsnSummary";
        $this->data['headData']->pageTitle = "HSN SUMMARY";
        $this->data['pageHeader'] = 'HSN SUMMARY';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view("reports/accounting_report/hsn_summary",$this->data);
    }

    public function getHsnSummary(){
        $data = $this->input->post();
        $data['vou_name_s'] = ($data['report'] == "gstr1")?"'Sale','GInc','C.N.','D.N.'":"'Purc','GExp','C.N.','D.N.'";
        $result=$this->gstReport->_hsn($data);

        $total_taxable_value = $total_value = $total_cgst = $total_sgst = $total_igst = $total_cess = 0;

        if(!empty($result)):
            $total_taxable_value = array_sum(array_column($result,'taxable_amount'));
            $total_value = array_sum(array_column($result,'net_amount'));
            $total_cgst = array_sum(array_column($result,'cgst_amount'));
            $total_sgst = array_sum(array_column($result,'sgst_amount'));
            $total_igst = array_sum(array_column($result,'igst_amount'));       
            $total_cess = array_sum(array_column($result,'cess_amount')); 
        endif;

        $tbody = '';
        foreach($result as $row):
            $postData = ['hsn_code'=>$row->hsn_code,'report'=>$data['report'],'from_date'=>$data['from_date'],'to_date'=>$data['to_date']];
            $tbody .= '<tr>
                <td><a href="'.base_url("reports/accountingReport/hsnTransactions/".encodeURL($postData)).'" target="_blank">'.$row->hsn_code.'</a></td>
                <td>'.$row->hsn_description.'</td>
                <td><a href="'.base_url("reports/accountingReport/hsnTransactions/".encodeURL($postData)).'" target="_blank">'.$row->unit_name.' - '.$row->unit_description.'</a></td>
                <td>'.$row->qty.'</td>
                <td>'.numberFormatIndia(sprintf("%.2F",$row->net_amount)).'</td>
                <td>'.numberFormatIndia(sprintf("%.2F",$row->gst_per)).'</td>
                <td>'.numberFormatIndia(sprintf("%.2F",$row->taxable_amount)).'</td>
                <td>'.numberFormatIndia(sprintf("%.2F",$row->igst_amount)).'</td>
                <td>'.numberFormatIndia(sprintf("%.2F",$row->cgst_amount)).'</td>
                <td>'.numberFormatIndia(sprintf("%.2F",$row->sgst_amount)).'</td>
                <td>'.numberFormatIndia(sprintf("%.2F",$row->cess_amount)).'</td>
            </tr>';
        endforeach;

        $tfoot = '<tr>
            <th colspan="4" class="text-right">Total</th>
            <th>'.numberFormatIndia(sprintf("%.2F",$total_value)).'</th>
            <th></th>
            <th>'.numberFormatIndia(sprintf("%.2F",$total_taxable_value)).'</th>
            <th>'.numberFormatIndia(sprintf("%.2F",$total_igst)).'</th>
            <th>'.numberFormatIndia(sprintf("%.2F",$total_cgst)).'</th>
            <th>'.numberFormatIndia(sprintf("%.2F",$total_sgst)).'</th>
            <th>'.numberFormatIndia(sprintf("%.2F",$total_cess)).'</th>
        </tr>';

        $this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

    public function hsnTransactions($jsonData=""){
        if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        endif; 
        $this->data['headData']->pageUrl = "reports/accountingReport/hsnSummary";
        $this->data['headData']->pageTitle = "HSN Transactions";
        $this->data['pageHeader'] = 'HSN Transactions';
        $this->data['hsnCode'] = (!empty($postData['hsn_code']))?$postData['hsn_code']:"";
        $this->data['report'] = (!empty($postData['report']))?$postData['report']:"gstr1";
        $this->data['startDate'] = (!empty($postData['from_date']))?$postData['from_date']:getFyDate("Y-m-d",date("Y-m-01"));
        $this->data['endDate'] = (!empty($postData['to_date']))?$postData['to_date']:getFyDate("Y-m-d");
        $this->load->view("reports/accounting_report/hsn_transactions",$this->data);
    }

    public function getHsnTransactions(){
        $data = $this->input->post();
        $data['vou_name_s'] = ($data['report'] == "gstr1")?"'Sale','GInc','C.N.','D.N.'":"'Purc','GExp','C.N.','D.N.'";
        $result = $this->accountReport->getHsnTransactions($data);

        $thead = '<tr class="text-center">
            <th colspan="'.(($data['report_type'] == "ITEMWISE")?"18":"17").'">HSN Code : '.$data['hsn_code'].'</th>
        </tr>
        <tr>
            <th class="text-left">Vou. Type</th>
            <th class="text-left">Vou. No.</th>
            <th class="text-left">Vou. Date</th>
            <th class="text-left">Party Name</th>
            <th class="text-left">POS</th>
            <th class="text-left">GST NO.</th>';

        if($data['report_type'] == "ITEMWISE"):
            $thead .= '<th class="text-left">Item Name</th>';
        endif;
   
        $thead .= '<th class="text-left">HSN</th>
            <th class="text-left">Description</th>
            <th class="text-left">UQC</th>
            <th>Total Quantity</th>
            <th>Total Value</th>
            <th>Rate</th>
            <th>Taxable Value</th>
            <th>Integrated Tax Amount</th>
            <th>Central Tax Amount</th>
            <th>State/UT Tax Amount</th>
            <th>Cess Amount</th>
        </tr>';

        $tbody = '';
        foreach($result as $row):
            $tbody .= '<tr>
                <td>'.$row->vou_name_s.'</td>
                <td>'.$row->trans_number.'</td>
                <td>'.formatDate($row->trans_date).'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->party_state_code.'</td>
                <td>'.$row->gstin.'</td>';

            if($data['report_type'] == "ITEMWISE"):
                $tbody .= '<td>'.$row->item_name.'</td>';
            endif;

            $tbody .= '<td>'.$row->hsn_code.'</td>
                <td>'.$row->hsn_description.'</td>
                <td>'.$row->unit_name.' - '.$row->unit_description.'</td>
                <td>'.$row->qty.'</td>
                <td>'.numberFormatIndia(sprintf("%.2F",$row->net_amount)).'</td>
                <td>'.numberFormatIndia(sprintf("%.2F",$row->gst_per)).'</td>
                <td>'.numberFormatIndia(floatval($row->taxable_amount)).'</td>
                <td>'.numberFormatIndia(sprintf("%.2F",$row->igst_amount)).'</td>
                <td>'.numberFormatIndia(sprintf("%.2F",$row->cgst_amount)).'</td>
                <td>'.numberFormatIndia(sprintf("%.2F",$row->sgst_amount)).'</td>
                <td>'.numberFormatIndia(sprintf("%.2F",$row->cess_amount)).'</td>
            </tr>';
        endforeach;

        $total_taxable_value = $total_value = $total_cgst = $total_sgst = $total_igst = $total_cess = 0;

        if(!empty($result)):
            $total_taxable_value = array_sum(array_column($result,'taxable_amount'));
            $total_value = array_sum(array_column($result,'net_amount'));
            $total_cgst = array_sum(array_column($result,'cgst_amount'));
            $total_sgst = array_sum(array_column($result,'sgst_amount'));
            $total_igst = array_sum(array_column($result,'igst_amount'));       
            $total_cess = array_sum(array_column($result,'cess_amount')); 
        endif;

        $tfoot = '<tr>
            <th colspan="'.(($data['report_type'] == "ITEMWISE")?"11":"10").'" class="text-right">Total</th>
            <th>'.numberFormatIndia(sprintf("%.2F",$total_value)).'</th>
            <th></th>
            <th>'.numberFormatIndia(sprintf("%.2F",$total_taxable_value)).'</th>
            <th>'.numberFormatIndia(sprintf("%.2F",$total_igst)).'</th>
            <th>'.numberFormatIndia(sprintf("%.2F",$total_cgst)).'</th>
            <th>'.numberFormatIndia(sprintf("%.2F",$total_sgst)).'</th>
            <th>'.numberFormatIndia(sprintf("%.2F",$total_cess)).'</th>
        </tr>';

        $this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

    public function trailBalance(){
        $this->data['headData']->pageUrl = "reports/accountingReport/trailBalance";
        $this->data['headData']->pageTitle = "Trail Balance";
        $this->data['pageHeader'] = 'Trail Balance';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view("reports/accounting_report/trail_balance",$this->data);
    }

    public function getTrailBalanceData($jsonData=''){
        if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        else:
            $postData = $this->input->post();
        endif;

        $from_date = $postData['from_date'];
        $to_date = $postData['to_date'];
        $is_consolidated = $postData['is_consolidated'];

        $data = ['from_date'=>$from_date,'to_date'=>$to_date];
        $productAmount = $this->accountReport->_productOpeningAndClosingAmount($data);
        $accountSummary = $this->accountReport->_trailAccountSummary($data);

        $group_ids = implode(",",array_unique(array_column($accountSummary,'group_id')));
        $data['extra_where'] = "gs.cl_balance <> 0
        AND gm.id IN (".$group_ids.")";

        $subGroupSummary = $this->accountReport->_trailSubGroupSummary($data);
        $mainGroupSummary = $this->accountReport->_trailMainGroupSummary($data);

        $tbPostData = [
            'productAmount' => $productAmount,
            'accountSummary' => $accountSummary,
            'subGroupSummary' => $subGroupSummary,
            'mainGroupSummary' => $mainGroupSummary,
            'is_consolidated' => $is_consolidated
        ];
        $trailBalance = $this->_generateTrailBalance($tbPostData);

        $tbody = '';
        foreach($trailBalance as $row):
            $particular = "";
            if($row['is_main'] == 1):
                $particular = "<span style='font-weight:700 !important;'><b>".$row["particular"]."</b></span>";
            elseif($row['is_sub'] == 1):
                $particular = "<span style='font-weight:600 !important; margin-left:10px !important; '>&nbsp;&nbsp;<b>".$row["particular"]."</b></span>";
            else:
                $particular = "<span style='margin-left:20px !important; padding-left:20px !important;'>&nbsp;&nbsp;&nbsp;&nbsp;".$row["particular"]."</span>";
            endif;

            $cl_balance = "";
            if($row['is_main'] == 1):
                /* if(!empty($row["cl_balance"])):
                    $cl_balance = "<b style='font-weight:700;'>".(($row["cl_balance"] > 0)?number_format($row["cl_balance"],2)." Cr.":number_format(abs($row["cl_balance"]),2)." Dr.")."</b>";
                endif; */
            elseif($row['is_sub'] == 1):
                $cl_balance = "<span style='font-weight:600 !important;'><b>".(($row["cl_balance"] > 0)?numberFormatIndia($row["cl_balance"])." Cr.":numberFormatIndia(abs($row["cl_balance"]))." Dr.")."</b></span>";
            else:
                $cl_balance = (($row["cl_balance"] > 0)?numberFormatIndia($row["cl_balance"])." Cr.":numberFormatIndia(abs($row["cl_balance"]))." Dr.");
            endif;

            $cr_amount = "";
            $dr_amount = "";
            if($row['is_main'] == 1):
                $cr_amount = "<span style='font-weight:700 !important;'><b>".((!empty($row['credit_amount']))?numberFormatIndia($row['credit_amount']):"")."</b></span>";
                $dr_amount = "<span style='font-weight:700 !important;'><b>".((!empty($row['debit_amount']))?numberFormatIndia($row['debit_amount']):"")."</b></span>";
            endif;

            $tbody .= '<tr class="'.(($row['is_total'] == 1)?"bg-light":"").'">
                <td>
                    '.$particular.'
                </td>
                <td class="text-center" style="width:140px;">'.$cl_balance.'</td>
                <td class="text-center" style="width:140px;">'.$dr_amount.'</td>
                <td class="text-center" style="width:140px;">'.$cr_amount.'</td>
            </tr>';
        endforeach;

        if(!empty($jsonData)):
            $reportTitle = 'Trail Balance';
            $report_date = formatDate($postData['from_date']).' to '.formatDate($postData['to_date']);   
            $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
            $thead .= '<tr class="bg-light">
                <th class="text-left" colspan="2">Particulars</th>
                <th class="text-center">Debit Amount</th>
                <th class="text-center">Credit Amount</th>
            </tr>';

            $companyData = $this->masterModel->getCompanyInfo();
            $logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
            $logo = base_url('assets/images/' . $logoFile);
            $letter_head = base_url('assets/images/letterhead_top.png');
            
            $pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
                <thead>'.$thead.'</thead>
                <tbody>'.$tbody.'</tbody>
            </table>';
            $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                <tr>
                    <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">'.$reportTitle.'</td>
                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">'.$report_date.'</td>
                </tr>
            </table>
            <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
            </table>';

            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';
            
            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/TrialBalance.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,25,20,3,3,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);
            
            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        
        else:
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }

    public function _generateTrailBalance($postData){
        $openingStock = array_sum(array_column($postData['productAmount'],'op_amount'));
        $is_consolidated = $postData['is_consolidated'];
        
        $dataRow = array();$total_debit_amount = 0; $total_credit_amount = 0;
        foreach($postData['mainGroupSummary'] as $row):
            if($row->group_name == "Stock-in-Hand (Clo.)"):
                if($openingStock > 0):
                    $row->debit_amount = $row->debit_amount + $openingStock;
                    $row->cl_balance = $row->credit_amount - $row->debit_amount;
                endif;
            endif;
            $dataRow[] = ['particular' => $row->group_name, 'debit_amount' => (!empty($row->debit_amount)?$row->debit_amount:0), 'credit_amount' => (!empty($row->credit_amount)?$row->credit_amount:0), 'cl_balance' => (!empty($row->cl_balance)?$row->cl_balance:0), 'is_main' => 1, 'is_sub' => 0,'is_total'=>($is_consolidated == 0)?1:0];

            if($is_consolidated == 0):
                if($row->group_name == "Stock-in-Hand (Clo.)"):
                    if($openingStock > 0):                        
                        foreach($postData['productAmount'] as $prow):
                            $dataRow[] = ['particular' => $prow->ledger_name, 'debit_amount' => $prow->cl_amount, 'credit_amount' => 0, 'cl_balance' => $prow->cl_amount, 'is_main' => 0, 'is_sub' => 1,'is_total'=>0];
                        endforeach;
                    endif;
                endif;
                
                $subGroupKey = array();
                $subGroupKey = array_keys(array_column($postData['subGroupSummary'],"bs_id"),$row->id);                
                foreach($subGroupKey as $k=>$key):
                    $dataRow[] = ['particular' => $postData['subGroupSummary'][$key]->group_name, 'debit_amount' => (!empty($postData['subGroupSummary'][$key]->debit_amount)?$postData['subGroupSummary'][$key]->debit_amount:0), 'credit_amount' => (!empty($postData['subGroupSummary'][$key]->credit_amount)?$postData['subGroupSummary'][$key]->credit_amount:0), 'cl_balance' => (!empty($postData['subGroupSummary'][$key]->cl_balance)?$postData['subGroupSummary'][$key]->cl_balance:0), 'is_main' => 0, 'is_sub' => 1,'is_total'=>0];

                    $accountKey = array();
                    $accountKey = array_keys(array_column($postData['accountSummary'],"group_id"),$postData['subGroupSummary'][$key]->id);
                    foreach($accountKey as $ak=>$acc_key):
                        $dataRow[] = ['particular' => $postData['accountSummary'][$acc_key]->name, 'debit_amount' => (!empty($postData['accountSummary'][$acc_key]->debit_amount)?$postData['accountSummary'][$acc_key]->debit_amount:0), 'credit_amount' => (!empty($postData['accountSummary'][$acc_key]->credit_amount)?$postData['accountSummary'][$acc_key]->credit_amount:0), 'cl_balance' => (!empty($postData['accountSummary'][$acc_key]->cl_balance)?$postData['accountSummary'][$acc_key]->cl_balance:0), 'is_main' => 0, 'is_sub' => 0,'is_total'=>0];
                    endforeach;
                endforeach;                
            endif; 
            $total_debit_amount += $row->debit_amount;
            $total_credit_amount += $row->credit_amount;
        endforeach;

        $total_debit_amount = round($total_debit_amount,2);
        $total_credit_amount = round($total_credit_amount,2);

        $totalAmount = 0;
        if($total_debit_amount > $total_credit_amount):
            $totalAmount = $total_debit_amount;
            $dataRow[] = ['particular' => "Difference In Trail Balance", 'debit_amount' => 0, 'credit_amount' => round(($total_debit_amount - $total_credit_amount),2), 'cl_balance' => 0,'is_main' => 1, 'is_sub' => 0,'is_total'=>0];
        elseif($total_debit_amount < $total_credit_amount):
            $totalAmount = $total_credit_amount;
            $dataRow[] = ['particular' => "Difference In Trail Balance", 'debit_amount' => round(($total_credit_amount - $total_debit_amount),2), 'credit_amount' => 0, 'cl_balance' => 0, 'is_main' => 1, 'is_sub' => 0,'is_total'=>0];
        else:
            $totalAmount = $total_debit_amount;
        endif;

        $dataRow[] = ['particular' => "Total", 'debit_amount' => $totalAmount, 'credit_amount' => $totalAmount, 'cl_balance' => 0, 'is_main' => 1, 'is_sub' => 0,'is_total'=>1];

        return $dataRow;
    }
    
    public function profitAndLoss(){
        $this->data['headData']->pageUrl = "reports/accountingReport/profitAndLoss";
        $this->data['headData']->pageTitle = "Profit and Loss";
        $this->data['pageHeader'] = 'Profit and Loss';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view("reports/accounting_report/profit_and_loss",$this->data);
    }

    public function getProfitAndLossData($jsonData=''){
        if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        else:
            $postData = $this->input->post();
        endif;

        $from_date = $postData['from_date'];
        $to_date = $postData['to_date'];
        $is_consolidated = $postData['is_consolidated'];
        
        $data = ['from_date' => $from_date, "to_date" => $to_date, 'nature'=>"'Expenses','Income'"];
        $productAmount = $this->accountReport->_productOpeningAndClosingAmount($data);

        /* Treading Account Start */
        $data['bs_type_code'] = "'T'";
        $data['balance_type'] = "lb.cl_balance > 0";
        $tiAccountDetails = $this->accountReport->_accountWiseDetail($data);

        $data['bs_type_code'] = "'T'";
        $data['balance_type'] = "lb.cl_balance < 0";
        $teAccountDetails = $this->accountReport->_accountWiseDetail($data);

        $data['bs_type_code'] = "'T'";
        $data['balance_type'] = "gs.cl_balance > 0";
        $tiGroupSummary = $this->accountReport->_groupWiseSummary($data);

        $data['bs_type_code'] = "'T'";
        $data['balance_type'] = "gs.cl_balance < 0";
        $teGroupSummary = $this->accountReport->_groupWiseSummary($data);
        /* Treading Account End */

        /* P&L Account Start */
        $data['bs_type_code'] = "'P'";
        $data['balance_type'] = "lb.cl_balance > 0";
        $piAccountDetails = $this->accountReport->_accountWiseDetail($data);

        $data['bs_type_code'] = "'P'";
        $data['balance_type'] = "lb.cl_balance < 0";
        $peAccountDetails = $this->accountReport->_accountWiseDetail($data);

        $data['bs_type_code'] = "'P'";
        $data['balance_type'] = "gs.cl_balance > 0";
        $piGroupSummary = $this->accountReport->_groupWiseSummary($data);

        $data['bs_type_code'] = "'P'";
        $data['balance_type'] = "gs.cl_balance < 0";
        $peGroupSummary = $this->accountReport->_groupWiseSummary($data);
        /* P&L Account End */

        $pnlPostData = [
            'productAmount' => $productAmount,
            'tiGroupSummary' => $tiGroupSummary,
            'teGroupSummary' => $teGroupSummary,
            'tiAccountDetails' => $tiAccountDetails,
            'teAccountDetails' => $teAccountDetails,
            'piGroupSummary' => $piGroupSummary,
            'peGroupSummary' => $peGroupSummary,
            'piAccountDetails' => $piAccountDetails,
            'peAccountDetails' => $peAccountDetails,
            'is_consolidated' => $is_consolidated
        ];
        $pnlData = $this->_generatePNL($pnlPostData);  
        
        $tbody = '';
        foreach($pnlData as $row):
            if(empty($jsonData)):
                $accountNameL = (!empty($row['ledgerIdL']))?'<a href="' . base_url('reports/accountingReport/ledgerDetail/' . encodeURL($row['ledgerIdL']).'/'.$from_date.'/'.$to_date) . '" target="_blank" datatip="Account" flow="down">'.$row["particularL"].'</a>':$row["particularL"];
            else:
                $accountNameL = $row["particularL"];
            endif;

            $particularL = (!empty($row["isHeadL"]))?"<b>".$accountNameL."</b>":"<span style='margin-left:10px;'>&nbsp;&nbsp;".$accountNameL."</span>";

            $amountLL = "";
            if(!empty($row['isHeadL'])):
                $amountLL = "<b>".((!empty($row['amountLL']))?numberFormatIndia(sprintf("%0.2f",$row['amountLL'])):"")."</b>";
            else:
                $amountLL = ((!empty($row['amountLL']))?numberFormatIndia(sprintf("%0.2f",$row['amountLL'])):"");
            endif;

            $amountLR = "";
            if(!empty($row['isHeadL'])):
                $amountLR = "<b>".((!empty($row['amountLR']))?numberFormatIndia(sprintf("%0.2f",$row['amountLR'])):((!empty($row['particularL']) && $row['isHeadL'])?"0.00":""))."</b>";
            else:
                $amountLR = ((!empty($row['amountLR']))?numberFormatIndia(sprintf("%0.2f",$row['amountLR'])):"");
            endif;

            if(empty($jsonData)):
                $accountNameR = (!empty($row['ledgerIdR']))?'<a href="' . base_url('reports/accountingReport/ledgerDetail/' . encodeURL($row['ledgerIdR']).'/'.$from_date.'/'.$to_date) . '" target="_blank" datatip="Account" flow="down">'.$row["particularR"].'</a>':$row["particularR"];
            else:
                $accountNameR = $row["particularR"];
            endif;            

            $particularR = (!empty($row["isHeadR"]))?"<b>".$accountNameR."</b>":"<span style='margin-left:10px;'>&nbsp;&nbsp;".$accountNameR."</span>";

            $amountRL = "";
            if(!empty($row['isHeadR'])):
                $amountRL = "<b>".((!empty($row['amountRL']))?numberFormatIndia(sprintf("%0.2f",$row['amountRL'])):"")."</b>";
            else:
                $amountRL = ((!empty($row['amountRL']))?numberFormatIndia(sprintf("%0.2f",$row['amountRL'])):"");
            endif;

            $amountRR = "";
            if(!empty($row['isHeadR'])):
                $amountRR = "<b>".((!empty($row['amountRR']))?numberFormatIndia(sprintf("%0.2f",$row['amountRR'])):((!empty($row['particularR']) && $row['isHeadR'])?"0.00":""))."</b>";
            else:
                $amountRR = ((!empty($row['amountRR']))?numberFormatIndia(sprintf("%0.2f",$row['amountRR'])):"");
            endif;

            $tbody .= '<tr class="'.(($row['isTotal'] == 1)?"bg-light":"").'">
                <td style="width:40%;">
                    '.$particularL.'
                </td>';
            if($is_consolidated == 0):
                $tbody .= '<td style="width:10%;" class="text-right">'.$amountLL.'</td>';
            endif;
            $tbody .= '<td style="width:10%;" class="text-right">'.$amountLR.'</td>
                <td style="width:40%;">'.$particularR.'</td>';
            if($is_consolidated == 0):
                $tbody .= '<td style="width:10%;" class="text-right">'.$amountRL.'</td>';
            endif;
            $tbody .= '<td style="width:10%;" class="text-right">'.$amountRR.'</td>
            </tr>';
        endforeach;

        if(!empty($jsonData)):
            $reportTitle = 'Profit and Loss';
            $report_date = formatDate($postData['from_date']).' to '.formatDate($postData['to_date']);   
            $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
            $thColspan = ($is_consolidated == 0)?'colspan="2"':"";
            $thead .= '<tr class="bg-light">';
            $thead .= '<th class="text-left">Particulars</th>';
            $thead .= '<th class="text-center" '.$thColspan.'>Amount</th>';
            $thead .= '<th class="text-left">Particulars</th>';
            $thead .= '<th class="text-center" '.$thColspan.'>Amount</th>';
            $thead .= '</tr>';

            $companyData = $this->masterModel->getCompanyInfo();
            $logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
            $logo = base_url('assets/images/' . $logoFile);
            $letter_head = base_url('assets/images/letterhead_top.png');
            
            $pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
                <thead>'.$thead.'</thead>
                <tbody>'.$tbody.'</tbody>
            </table>';
            $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                <tr>
                    <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">'.$reportTitle.'</td>
                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">'.$report_date.'</td>
                </tr>
            </table>
            <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
            </table>';

            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';
            
            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/TrialBalance.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,25,20,3,3,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);
            
            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        
        else:
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }

    public function _generatePNL($postData){
        $sideTL = array(); $sideTR = array(); $sidePL = array(); $sidePR = array();
        $openingStock = array_sum(array_column($postData['productAmount'],'op_amount'));
        $closingStock = array_sum(array_column($postData['productAmount'],'cl_amount'));
        $is_consolidated = $postData['is_consolidated'];

        if(!empty($openingStock)):
            $sideTL[] = ['perticular'=>"Opening Stock","amountL"=>"","amountR"=>$openingStock,"is_head"=>1,'ledger_id'=>0];
            if($is_consolidated == 0):
                foreach($postData['productAmount'] as $row):
                    $sideTL[] = ['perticular'=>$row->ledger_name,"amountL"=>$row->op_amount,"amountR"=>"","is_head"=>0,'ledger_id'=>0];
                endforeach;
            endif;
        endif;

        foreach($postData['teGroupSummary'] as $row):
            $sideTL[] = ['perticular'=>$row->group_name,"amountL"=>"","amountR"=>$row->cl_balance,"is_head"=>1,'ledger_id'=>0];
            if($is_consolidated == 0):
                $accountDetailsKey = array_keys(array_column($postData['teAccountDetails'],"group_name"),$row->group_name);
                foreach($accountDetailsKey as $k=>$key):
                    $sideTL[] = ['perticular'=>$postData['teAccountDetails'][$key]->name,"amountL"=>$postData['teAccountDetails'][$key]->cl_balance,"amountR"=>"","is_head"=>0,'ledger_id'=>$postData['teAccountDetails'][$key]->id];
                endforeach;  
            endif;  
        endforeach;

        foreach($postData['tiGroupSummary'] as $row):
            if($row->group_name != "Stock-in-Hand (Clo.)"):    
                $sideTR[] = ['perticular'=>$row->group_name,"amountL"=>"","amountR"=>$row->cl_balance,"is_head"=>1,'ledger_id'=>0];
                if($is_consolidated == 0):
                    $accountDetailsKey = array_keys(array_column($postData['tiAccountDetails'],"group_name"),$row->group_name);
                    foreach($accountDetailsKey as $k=>$key):
                        $sideTR[] = ['perticular'=>$postData['tiAccountDetails'][$key]->name,"amountL"=>$postData['tiAccountDetails'][$key]->cl_balance,"amountR"=>"","is_head"=>0,'ledger_id'=>$postData['tiAccountDetails'][$key]->id];
                    endforeach;  
                endif;  
            endif;  
        endforeach;        

        if(!empty($closingStock)):
            $sideTR[] = ['perticular'=>"Stock-in-Hand (Clo.)","amountL"=>"","amountR"=>$closingStock,"is_head"=>1,'ledger_id'=>0];
            if($is_consolidated == 0):
                foreach($postData['productAmount'] as $row):
                    $sideTR[] = ['perticular'=>$row->ledger_name,"amountL"=>$row->cl_amount,"amountR"=>"","is_head"=>0,'ledger_id'=>0];
                endforeach;
            endif;
        endif;

        foreach($postData['peGroupSummary'] as $row):
            $sidePL[] = ['perticular'=>$row->group_name,"amountL"=>"","amountR"=>$row->cl_balance,"is_head"=>1,'ledger_id'=>0];
            if($is_consolidated == 0):
                $accountDetailsKey = array_keys(array_column($postData['peAccountDetails'],"group_name"),$row->group_name);
                foreach($accountDetailsKey as $k=>$key):
                    $sidePL[] = ['perticular'=>$postData['peAccountDetails'][$key]->name,"amountL"=>$postData['peAccountDetails'][$key]->cl_balance,"amountR"=>"","is_head"=>0,'ledger_id'=>$postData['peAccountDetails'][$key]->id];
                endforeach;                        
            endif;
        endforeach;

        foreach($postData['piGroupSummary'] as $row):
            $sidePR[] = ['perticular'=>$row->group_name,"amountL"=>"","amountR"=>$row->cl_balance,"is_head"=>1,'ledger_id'=>0];
            if($is_consolidated == 0):
                $accountDetailsKey = array_keys(array_column($postData['piAccountDetails'],"group_name"),$row->group_name);
                foreach($accountDetailsKey as $k=>$key):
                    $sidePR[] = ['perticular'=>$postData['piAccountDetails'][$key]->name,"amountL"=>$postData['piAccountDetails'][$key]->cl_balance,"amountR"=>"","is_head"=>0,'ledger_id'=>$postData['piAccountDetails'][$key]->id];
                endforeach;
            endif;
        endforeach;

        $countTL = count($sideTL);
        $countTR = count($sideTR);

        $rowCounterT = ($countTL >= $countTR)?$countTL:$countTR;
        $profitLossData = array();
        $particularTL = "";$amountTLL="";$amountTLR="";$isHeadTL="";
        $particularTR = "";$amountTRL="";$amountTRR="";$isHeadTR="";
        $totalAmountTL = 0; $totalAmountTR = 0;

        for($i = 0; $i < $rowCounterT ; $i++):
            $particularTL = "";$amountTLL="";$amountTLR="";$isHeadTL="";$ledgerIdTL=0;
            if(isset($sideTL[$i])):
                $particularTL = $sideTL[$i]['perticular'];
                $amountTLL = $sideTL[$i]['amountL'];
                $amountTLR = $sideTL[$i]['amountR'];
                $isHeadTL = $sideTL[$i]['is_head'];
                $ledgerIdTL = $sideTL[$i]['ledger_id'];
                $totalAmountTL += (!empty($sideTL[$i]['amountR']))?$sideTL[$i]['amountR']:0;
            endif;

            $particularTR = "";$amountTRL="";$amountTRR="";$isHeadTR="";$ledgerIdTR=0;
            if(isset($sideTR[$i])):
                $particularTR = $sideTR[$i]['perticular'];
                $amountTRL = $sideTR[$i]['amountL'];
                $amountTRR = $sideTR[$i]['amountR'];
                $isHeadTR = $sideTR[$i]['is_head'];
                $ledgerIdTR = $sideTR[$i]['ledger_id'];
                $totalAmountTR += (!empty($sideTR[$i]['amountR']))?$sideTR[$i]['amountR']:0;
            endif;

            $profitLossData[] = ["particularL"=>$particularTL,'amountLL'=>$amountTLL,'amountLR'=>$amountTLR,'isHeadL'=>$isHeadTL,"particularR"=>$particularTR,'amountRL'=>$amountTRL,'amountRR'=>$amountTRR,'isHeadR'=>$isHeadTR,'isTotal'=>0,'ledgerIdL'=>$ledgerIdTL,'ledgerIdR'=>$ledgerIdTR];
        endfor;

        $cfAmount = 0;$totalAmountPL = 0; $totalAmountPR = 0;
        if($totalAmountTL > $totalAmountTR):
            $profitLossData[] = ["particularL"=>"",'amountLL'=>"",'amountLR'=>"",'isHeadL'=>0,"particularR"=>"Gross Loss c/o",'amountRL'=>"",'amountRR'=>abs($totalAmountTR - $totalAmountTL),'isHeadR'=>1,'isTotal'=>0,'ledgerIdL'=>0,'ledgerIdR'=>0];

            $profitLossData[] = ["particularL"=>"",'amountLL'=>"",'amountLR'=>$totalAmountTL,'isHeadL'=>1,"particularR"=>"",'amountRL'=>"",'amountRR'=>$totalAmountTL,'isHeadR'=>1,'isTotal'=>1,'ledgerIdL'=>0,'ledgerIdR'=>0];

            $profitLossData[] = ["particularL"=>"Gross Loss b/f",'amountLL'=>"",'amountLR'=>abs($totalAmountTR - $totalAmountTL),'isHeadL'=>1,"particularR"=>"",'amountRL'=>"",'amountRR'=>"",'isHeadR'=>1,'isTotal'=>0,'ledgerIdL'=>0,'ledgerIdR'=>0];

            
            //$sidePL[0] = ['perticular'=>"Gross Loss b/f","amountL"=>"","amountR"=>abs($totalAmountTR - $totalAmountTL),"is_head"=>1];
            $totalAmountPL = abs($totalAmountTR - $totalAmountTL);
            $cfAmount = $totalAmountTL;
        elseif($totalAmountTL < $totalAmountTR):
            $profitLossData[] = ["particularL"=>"Gross Profit c/f",'amountLL'=>"",'amountLR'=>abs($totalAmountTR - $totalAmountTL),'isHeadL'=>1,"particularR"=>"",'amountRL'=>"",'amountRR'=>"",'isHeadR'=>0,'isTotal'=>0,'ledgerIdL'=>0,'ledgerIdR'=>0];

            $profitLossData[] = ["particularL"=>"",'amountLL'=>"",'amountLR'=>$totalAmountTR,'isHeadL'=>1,"particularR"=>"",'amountRL'=>"",'amountRR'=>$totalAmountTR,'isHeadR'=>1,'isTotal'=>1,'ledgerIdL'=>0,'ledgerIdR'=>0];

            $profitLossData[] = ["particularL"=>"",'amountLL'=>"",'amountLR'=>"",'isHeadL'=>1,"particularR"=>"Gross Profit b/f",'amountRL'=>"",'amountRR'=>abs($totalAmountTR - $totalAmountTL),'isHeadR'=>1,'isTotal'=>0,'ledgerIdL'=>0,'ledgerIdR'=>0];
            
            //$sidePR[0] = ['perticular'=>"Gross Profit b/f","amountL"=>"","amountR"=>abs($totalAmountTR - $totalAmountTL),"is_head"=>1];
            $totalAmountPR = abs($totalAmountTR - $totalAmountTL);
            $cfAmount = $totalAmountTR;
        endif;

        $countPL = count($sidePL);
        $countPR = count($sidePR);
        
        $rowCounterP = ($countPL >= $countPR)?$countPL:$countPR;
        $particularPL = "";$amountPLL="";$amountPLR="";$isHeadPL="";
        $particularPR = "";$amountPRL="";$amountPRR="";$isHeadPR="";
        for($j = 0; $j < $rowCounterP ; $j++):
            $particularPL = "";$amountPLL="";$amountPLR="";$isHeadPL="";$ledgerIdPL=0;
            if(isset($sidePL[$j])):
                $particularPL = $sidePL[$j]['perticular'];
                $amountPLL = $sidePL[$j]['amountL'];
                $amountPLR = $sidePL[$j]['amountR'];
                $isHeadPL = $sidePL[$j]['is_head'];
                $ledgerIdPL = $sidePL[$j]['ledger_id'];
                $totalAmountPL += (!empty($sidePL[$j]['amountR']))?$sidePL[$j]['amountR']:0;
            endif;

            $particularPR = "";$amountPRL="";$amountPRR="";$isHeadPR="";$ledgerIdPR=0;
            if(isset($sidePR[$j])):
                $particularPR = $sidePR[$j]['perticular'];
                $amountPRL = $sidePR[$j]['amountL'];
                $amountPRR = $sidePR[$j]['amountR'];
                $isHeadPR = $sidePR[$j]['is_head'];
                $ledgerIdPR = $sidePR[$j]['ledger_id'];
                $totalAmountPR += (!empty($sidePR[$j]['amountR']))?$sidePR[$j]['amountR']:0;
            endif;

            $profitLossData[] = ["particularL"=>$particularPL,'amountLL'=>$amountPLL,'amountLR'=>$amountPLR,'isHeadL'=>$isHeadPL,"particularR"=>$particularPR,'amountRL'=>$amountPRL,'amountRR'=>$amountPRR,'isHeadR'=>$isHeadPR,'isTotal'=>0,'ledgerIdL'=>$ledgerIdPL,'ledgerIdR'=>$ledgerIdPR];
        endfor;

        if($totalAmountPL > $totalAmountPR):
            $profitLossData[] = ["particularL"=>"",'amountLL'=>"",'amountLR'=>"",'isHeadL'=>0,"particularR"=>"Net Loss",'amountRL'=>"",'amountRR'=>abs($totalAmountPL-$totalAmountPR),'isHeadR'=>1,'isTotal'=>0,'ledgerIdL'=>0,'ledgerIdR'=>0];  
            
            $profitLossData[] = ["particularL"=>"Total",'amountLL'=>"",'amountLR'=>$totalAmountPL,'isHeadL'=>1,"particularR"=>"Total",'amountRL'=>"",'amountRR'=>$totalAmountPL,'isHeadR'=>1,'isTotal'=>1,'ledgerIdL'=>0,'ledgerIdR'=>0];
        elseif($totalAmountPL < $totalAmountPR):
            $profitLossData[] = ["particularL"=>"Net Profit",'amountLL'=>"",'amountLR'=>abs($totalAmountPL - $totalAmountPR),'isHeadL'=>1,"particularR"=>"",'amountRL'=>"",'amountRR'=>"",'isHeadR'=>0,'isTotal'=>0,'ledgerIdL'=>0,'ledgerIdR'=>0];

            $profitLossData[] = ["particularL"=>"Total",'amountLL'=>"",'amountLR'=>$totalAmountPR,'isHeadL'=>1,"particularR"=>"Total",'amountRL'=>"",'amountRR'=>$totalAmountPR,'isHeadR'=>1,'isTotal'=>1,'ledgerIdL'=>0,'ledgerIdR'=>0];
        endif;

        return $profitLossData;
    }

    public function balanceSheet(){
        $this->data['headData']->pageUrl = "reports/accountingReport/balanceSheet";
        $this->data['headData']->pageTitle = "Balance Sheet";
        $this->data['pageHeader'] = 'Balance Sheet';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view("reports/accounting_report/balance_sheet",$this->data);
    }

    public function getBalanceSheetData($jsonData = ''){
        if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        else:
            $postData = $this->input->post();
        endif;

        $from_date = $postData['from_date'];
        $to_date = $postData['to_date'];
        $is_consolidated = $postData['is_consolidated'];

        $data = ['from_date' => $from_date, "to_date" => $to_date, 'nature'=>"'Liabilities','Assets'", 'bs_type_code'=>"'B'", 'balance_type' => "lb.cl_balance > 0"];
        $productAmount = $this->accountReport->_productOpeningAndClosingAmount($data);
        
        $liabilitiesAccountDetails = $this->accountReport->_accountWiseDetail($data);
        $data['balance_type'] = "lb.cl_balance < 0";
        $assetsAccountDetails = $this->accountReport->_accountWiseDetail($data);

        $data['balance_type'] = "gs.cl_balance > 0";
        $liabilitiesGroupSummary = $this->accountReport->_groupWiseSummary($data);
        $data['balance_type'] = "gs.cl_balance < 0";
        $assetsGroupSummary = $this->accountReport->_groupWiseSummary($data);

        $data['openingAmount'] = array_sum(array_column($productAmount,'op_amount'));
        $data['closingAmount'] = array_sum(array_column($productAmount,'cl_amount'));
        $data['extra_where'] = "gm.bs_type_code IN ('T','P')";
        $netPnlAmount = $this->accountReport->_netPnlAmount($data);

        $bsPostData = [
            'productAmount' => $productAmount,
            'liabilitiesGroupSummary' => $liabilitiesGroupSummary,
            'liabilitiesAccountDetails' => $liabilitiesAccountDetails,
            'assetsGroupSummary' => $assetsGroupSummary,
            'assetsAccountDetails' => $assetsAccountDetails,
            'netPnlAmount' => $netPnlAmount,
            'is_consolidated' => $is_consolidated
        ];
        $balanceSheetData = $this->_generateBalanceSheet($bsPostData);
		//print_r($balanceSheetData);exit;
        $tbody = '';
        foreach($balanceSheetData as $row):
            if(empty($jsonData)):
			    $accountNameL = (!empty($row['ledgerIdL']))?'<a href="' . base_url('reports/accountingReport/ledgerDetail/' . encodeURL($row['ledgerIdL']).'/'.$from_date.'/'.$to_date) . '" target="_blank" datatip="Account" flow="down">'.$row["particularL"].'</a>':$row["particularL"];
            else:
                $accountNameL = $row["particularL"];
            endif;
            
            $particularL = (!empty($row["isHeadL"]))?"<b>".$accountNameL."</b>":"<span style='margin-left:10px;'>&nbsp;&nbsp;".$accountNameL."</span>";

            $amountLL = "";
            if(!empty($row['isHeadL'])):
                $amountLL = "<b>".((!empty($row['amountLL']))?numberFormatIndia(sprintf("%0.2f",$row['amountLL'])):"")."</b>";
            else:
                $amountLL = ((!empty($row['amountLL']))?numberFormatIndia(sprintf("%0.2f",$row['amountLL'])):"");
            endif;

            $amountLR = "";
            if(!empty($row['isHeadL'])):
                $amountLR = "<b>".((!empty($row['amountLR']))?numberFormatIndia(sprintf("%0.2f",$row['amountLR'])):((!empty($row['particularL']) && $row['isHeadL'])?"0.00":""))."</b>";
            else:
                $amountLR = ((!empty($row['amountLR']))?numberFormatIndia(sprintf("%0.2f",$row['amountLR'])):"");
            endif;

            if(empty($jsonData)):
                $accountNameR = (!empty($row['ledgerIdR']))?'<a href="' . base_url('reports/accountingReport/ledgerDetail/' . encodeURL($row['ledgerIdR']).'/'.$from_date.'/'.$to_date) . '" target="_blank" datatip="Account" flow="down">'.$row["particularR"].'</a>':$row["particularR"];
            else:
                $accountNameR = $row["particularR"];
            endif;

            $particularR = (!empty($row["isHeadR"]))?"<b>".$accountNameR."</b>":"<span style='margin-left:10px;'>&nbsp;&nbsp;".$accountNameR."</span>";

            $amountRL = "";
            if(!empty($row['isHeadR'])):
                $amountRL = "<b>".((!empty($row['amountRL']))?numberFormatIndia(sprintf("%0.2f",$row['amountRL'])):"")."</b>";
            else:
                $amountRL = ((!empty($row['amountRL']))?numberFormatIndia(sprintf("%0.2f",$row['amountRL'])):"");
            endif;

            $amountRR = "";
            if(!empty($row['isHeadR'])):
                $amountRR = "<b>".((!empty($row['amountRR']))?numberFormatIndia(sprintf("%0.2f",$row['amountRR'])):((!empty($row['particularR']) && $row['isHeadR'])?"0.00":""))."</b>";
            else:
                $amountRR = ((!empty($row['amountRR']))?numberFormatIndia(sprintf("%0.2f",$row['amountRR'])):"");
            endif;

            $tbody .= '<tr class="'.(($row['isTotal'] == 1)?"bg-light":"").'">
                <td style="width:40%;">
                    '.$particularL.'
                </td>';
            if($is_consolidated == 0):
                $tbody .= '<td style="width:10%;" class="text-right">'.$amountLL.'</td>';
            endif;
            $tbody .= '<td style="width:10%;" class="text-right">'.$amountLR.'</td>
                <td style="width:40%;">'.$particularR.'</td>';
            if($is_consolidated == 0):
                $tbody .= '<td style="width:10%;" class="text-right">'.$amountRL.'</td>';
            endif;
            $tbody .= '<td style="width:10%;" class="text-right">'.$amountRR.'</td>
            </tr>';
        endforeach;

        if(!empty($jsonData)):
            $reportTitle = 'Balance Sheet';
            $report_date = formatDate($postData['from_date']).' to '.formatDate($postData['to_date']);   
            $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
            $thColspan = ($is_consolidated == 0)?'colspan="2"':"";
            $thead .= '<tr class="bg-light">';
            $thead .= '<th class="text-left">Liabilities</th>';
            $thead .= '<th class="text-center" '.$thColspan.'>Amount</th>';
            $thead .= '<th class="text-left">Assets</th>';
            $thead .= '<th class="text-center" '.$thColspan.'>Amount</th>';
            $thead .= '</tr>';

            $companyData = $this->masterModel->getCompanyInfo();
            $logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
            $logo = base_url('assets/images/' . $logoFile);
            $letter_head = base_url('assets/images/letterhead_top.png');
            
            $pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
                <thead>'.$thead.'</thead>
                <tbody>'.$tbody.'</tbody>
            </table>';
            $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                <tr>
                    <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">'.$reportTitle.'</td>
                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">'.$report_date.'</td>
                </tr>
            </table>
            <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
            </table>';

            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';
            
            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/TrialBalance.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,25,20,3,3,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);
            
            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        
        else:
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }

    public function _generateBalanceSheet($postData){
        $balanceSheetData = array();
        $sideTL = array(); $sideTR = array(); $sidePL = array(); $sidePR = array();
        $openingStock = array_sum(array_column($postData['productAmount'],'op_amount'));
        $closingStock = array_sum(array_column($postData['productAmount'],'cl_amount'));
        $is_consolidated = $postData['is_consolidated'];

        $assetsData = array(); $liabilitiesData = array();
        $currentAssets = 0;$ledger_id=0;
        foreach($postData['liabilitiesGroupSummary'] as $row):
            if($row->group_name != "Profit & Loss A/c"):
                $liabilitiesData[] = ['perticular'=>$row->group_name,"amountL"=>"","amountR"=>$row->cl_balance,"is_head"=>1,'ledger_id'=>0];
                if($is_consolidated == 0):
                    $accountDetailsKey = array_keys(array_column($postData['liabilitiesAccountDetails'],"group_name"),$row->group_name);
                    foreach($accountDetailsKey as $k=>$key):
                        $liabilitiesData[] = ['perticular'=>$postData['liabilitiesAccountDetails'][$key]->name,"amountL"=>$postData['liabilitiesAccountDetails'][$key]->cl_balance,"amountR"=>"","is_head"=>0,'ledger_id'=>$postData['liabilitiesAccountDetails'][$key]->id];
                    endforeach;
                endif; 
            endif;
        endforeach;

        foreach($postData['assetsGroupSummary'] as $row):
            if($row->group_name != "Profit & Loss A/c"):
                if($row->group_name == "Stock-in-Hand (Clo.)"):
                    $currentAssets = 1;
                    $assetsData[] = ['perticular'=>$row->group_name,"amountL"=>"","amountR"=>$row->cl_balance + $closingStock,"is_head"=>1,'ledger_id'=>0];
                    if($is_consolidated == 0):
                        //$assetsData[] = ['perticular'=>"Closing Stock","amountL"=>$closingStock,"amountR"=>"","is_head"=>0,'ledger_id'=>0];
                        foreach($postData['productAmount'] as $prow):
                            $assetsData[] = ['perticular'=>$prow->ledger_name,"amountL"=>$prow->cl_amount,"amountR"=>"","is_head"=>0,'ledger_id'=>0];
                        endforeach;
                    endif;
                else:
                    $assetsData[] = ['perticular'=>$row->group_name,"amountL"=>"","amountR"=>$row->cl_balance,"is_head"=>1,'ledger_id'=>0];
                endif;
                if($is_consolidated == 0):
                    $accountDetailsKey = array_keys(array_column($postData['assetsAccountDetails'],"group_name"),$row->group_name);
                    foreach($accountDetailsKey as $k=>$key):
                        $assetsData[] = ['perticular'=>$postData['assetsAccountDetails'][$key]->name,"amountL"=>$postData['assetsAccountDetails'][$key]->cl_balance,"amountR"=>"","is_head"=>0,'ledger_id'=>$postData['assetsAccountDetails'][$key]->id];
                    endforeach;
                endif; 
            endif;
        endforeach;

        if($currentAssets == 0):
            if(!empty($closingStock)):
                $assetsData[] = ['perticular'=>"Stock-in-Hand (Clo.)","amountL"=>"","amountR"=>$closingStock,"is_head"=>1,'ledger_id'=>0];
                if($is_consolidated == 0):
                    //$assetsData[] = ['perticular'=>"Closing Stock","amountL"=>$closingStock,"amountR"=>"","is_head"=>0,'ledger_id'=>0];
                    foreach($postData['productAmount'] as $row):
                        $assetsData[] = ['perticular'=>$row->ledger_name,"amountL"=>$row->cl_amount,"amountR"=>"","is_head"=>0,'ledger_id'=>0];
                    endforeach;
                endif;
            endif;
        endif;

        if(in_array("Profit & Loss A/c",array_column($postData['assetsGroupSummary'],'group_name'))):
            $key = array_search("Profit & Loss A/c",array_column($postData['assetsGroupSummary'],'group_name'));
            $postData['netPnlAmount']->net_pnl_amount = abs($postData['netPnlAmount']->net_pnl_amount) - abs($postData['assetsGroupSummary'][$key]->cl_balance);
        endif;

        if(in_array("Profit & Loss A/c",array_column($postData['liabilitiesGroupSummary'],'group_name'))):
            $key = array_search("Profit & Loss A/c",array_column($postData['liabilitiesGroupSummary'],'group_name'));
            $postData['netPnlAmount']->net_pnl_amount = abs($postData['netPnlAmount']->net_pnl_amount) - abs($postData['liabilitiesGroupSummary'][$key]->cl_balance);
        endif;

        $postData['netPnlAmount']->net_pnl_amount = round($postData['netPnlAmount']->net_pnl_amount,2);
        if($postData['netPnlAmount']->net_pnl_amount < 0):
            $assetsData[] = ['perticular'=>"Profit & Loss A/c","amountL"=>"","amountR"=>abs($postData['netPnlAmount']->net_pnl_amount),"is_head"=>1,'ledger_id'=>0];
        elseif($postData['netPnlAmount']->net_pnl_amount > 0):
            $liabilitiesData[] = ['perticular'=>"Profit & Loss A/c","amountL"=>"","amountR"=>abs($postData['netPnlAmount']->net_pnl_amount),"is_head"=>1,'ledger_id'=>0];
        endif;

        $countAssets = count($assetsData);
        $countLiablities = count($liabilitiesData);

        $rowCounter = ($countAssets >= $countLiablities)?$countAssets:$countLiablities;
        $particularL = "";$amountLL="";$amountLR="";$isHeadLL="";
        $particularA = "";$amountAL="";$amountAR="";$isHeadAR="";
        $totalAmountL = 0; $totalAmountA = 0;
        for($i = 0 ; $i < $rowCounter ; $i++):
            $particularL = "";$amountLL="";$amountLR="";$isHeadLL="";$ledgerIdL=0;
            if(isset($liabilitiesData[$i])):
                $particularL = $liabilitiesData[$i]['perticular'];
                $amountLL = $liabilitiesData[$i]['amountL'];
                $amountLR = $liabilitiesData[$i]['amountR'];
                $isHeadLL = $liabilitiesData[$i]['is_head'];
                $ledgerIdL = $liabilitiesData[$i]['ledger_id'];
                $totalAmountL += (!empty($liabilitiesData[$i]['amountR']))?$liabilitiesData[$i]['amountR']:0;
            endif;

            $particularA = "";$amountAL="";$amountAR="";$isHeadAR="";$ledgerIdR=0;
            if(isset($assetsData[$i])):
                $particularA = $assetsData[$i]['perticular'];
                $amountAL = $assetsData[$i]['amountL'];
                $amountAR = $assetsData[$i]['amountR'];
                $isHeadAR = $assetsData[$i]['is_head'];
                $ledgerIdR = $assetsData[$i]['ledger_id'];
                $totalAmountA += (!empty($assetsData[$i]['amountR']))?$assetsData[$i]['amountR']:0;
            endif;

            $balanceSheetData[]  = ["particularL"=>$particularL,'amountLL'=>$amountLL,'amountLR'=>$amountLR,'isHeadL'=>$isHeadLL,"particularR"=>$particularA,'amountRL'=>$amountAL,'amountRR'=>$amountAR,'isHeadR'=>$isHeadAR,'isTotal'=>0,'ledgerIdL'=>$ledgerIdL,'ledgerIdR'=>$ledgerIdR];
        endfor;

        if(sprintf("%0.2f",$totalAmountL) > sprintf("%0.2f",$totalAmountA)):   
            $balanceSheetData[] = ["particularL"=>"",'amountLL'=>"",'amountLR'=>"",'isHeadL'=>1,"particularR"=>"Difference In Balance Sheet",'amountRL'=>"",'amountRR'=>$totalAmountL - $totalAmountA,'isHeadR'=>1,'isTotal'=>0,'ledgerIdL'=>0,'ledgerIdR'=>0];
            
            $balanceSheetData[] = ["particularL"=>"Total",'amountLL'=>"",'amountLR'=>$totalAmountL,'isHeadL'=>1,"particularR"=>"Total",'amountRL'=>"",'amountRR'=>$totalAmountL,'isHeadR'=>1,'isTotal'=>1,'ledgerIdL'=>0,'ledgerIdR'=>0];
        elseif(sprintf("%0.2f",$totalAmountL) < sprintf("%0.2f",$totalAmountA)):
            $balanceSheetData[] = ["particularL"=>"Difference In Balance Sheet",'amountLL'=>"",'amountLR'=>$totalAmountA - $totalAmountL,'isHeadL'=>1,"particularR"=>"",'amountRL'=>"",'amountRR'=>"",'isHeadR'=>1,'isTotal'=>0,'ledgerIdL'=>0,'ledgerIdR'=>0];

            $balanceSheetData[] = ["particularL"=>"Total",'amountLL'=>"",'amountLR'=>$totalAmountA,'isHeadL'=>1,"particularR"=>"Total",'amountRL'=>"",'amountRR'=>$totalAmountA,'isHeadR'=>1,'isTotal'=>1,'ledgerIdL'=>0,'ledgerIdR'=>0];
        elseif(sprintf("%0.2f",$totalAmountL) == sprintf("%0.2f",$totalAmountA)):
            $balanceSheetData[] = ["particularL"=>"Total",'amountLL'=>"",'amountLR'=>$totalAmountL,'isHeadL'=>1,"particularR"=>"Total",'amountRL'=>"",'amountRR'=>$totalAmountA,'isHeadR'=>1,'isTotal'=>1,'ledgerIdL'=>0,'ledgerIdR'=>0];
        endif;

        return $balanceSheetData;
    }

}
?>