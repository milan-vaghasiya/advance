<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory as io_factory; 
class GstReport extends MY_Controller{
    private $gstr1_report = "reports/gst_report/gstr1_report";
    private $gstr2_report = "reports/gst_report/gstr2_report";
    private $gstr2b_match = "reports/gst_report/gstr2b_match";
    private $gstr3b_report = "reports/gst_report/gstr3b_report";

    private $gstr1ReportTypes = [
        'b2b'=>'b2b,sez,de',
        'b2ba' => 'b2ba',
        'b2cl' => 'b2cl',
        'b2cla' => 'b2cla',
        'b2cs' => 'b2cs',
        'b2csa' => 'b2csa',
        'cdnr' => 'cdnr',
        'cdnra' => 'cdnra',
        'cdnur' => 'cdnur',
        'cdnura' => 'cdnura',
        'exp'=>'exp',
        'expa' => 'expa',
        'at' => 'at',
        'ata' => 'ata',
        'atadj' => 'atadj',
        'atadja' => 'atadja',
        'exemp' => 'exemp',
        'hsn' => 'hsn',
        'docs' => 'docs' 
    ];

    private $gstr2ReportTypes = [
        'b2b' => "b2b",
        'b2bur' => "b2bur",
        'imps' => "imps",
        'impg' => "impg",
        'cdnr' => "cdnr",
        'cdnur' => "cdnur",
        'at' => 'at',
        'atadj' => 'atadj',
        'exemp' => 'exemp',
        'itcr' => 'itcr',
        'hsn' => 'hsnsum'
    ];

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "GST Report";
		$this->data['headData']->controller = "reports/gstReport";
	}

    public function gstr1(){
        $this->data['headData']->pageUrl = "reports/gstReport/gstr1";
        $this->data['headData']->pageTitle = "GSTR 1 REPORT";
        $this->data['pageHeader'] = 'GSTR 1 REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['gstr1Types'] = $this->gstr1ReportTypes;
        $this->load->view($this->gstr1_report, $this->data);
    }

    public function gstr2(){
        $this->data['headData']->pageUrl = "reports/gstReport/gstr2";
        $this->data['headData']->pageTitle = "GSTR 2 REPORT";
        $this->data['pageHeader'] = 'GSTR 2 REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['gstr2Types'] = $this->gstr2ReportTypes;
        $this->load->view($this->gstr2_report, $this->data);
    }

    public function getGstr1Report($jsonData=''){        
        if(!empty($jsonData)):
            $data =(array) decodeURL($jsonData);

            $spreadsheet = new Spreadsheet();
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $styleArray = [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
            ];
            $fontBold = ['font' => ['bold' => true]];
            $alignLeft = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];
            $alignCenter = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
            $alignRight = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];
            $borderStyle = [
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ]
            ];
            $bgPrimary = [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DAEAFA'],
                ],
                'font' => ['bold' => true]
            ];

            $i = 0;$data['report'] = "gstr1";
            foreach($this->gstr1ReportTypes as $key => $value):
                $html = "";
                $html = $this->{$key}($data)['html'];                

                $pdfData = '<table>'.$html.'</table>';
                $pdfData = str_replace('&', '&amp;', $pdfData);
                
                $reader->setSheetIndex($i);

                if($i == 0):
                    $spreadsheet = $reader->loadFromString($pdfData);
                else:
                    $spreadsheet = $reader->loadFromString($pdfData,$spreadsheet);
                endif;

                $spreadsheet->getSheet($i)->setTitle($value);
                $excelSheet = $spreadsheet->getSheet($i);
                $hcol = $excelSheet->getHighestColumn();
                $hrow = $excelSheet->getHighestRow();
                $packFullRange = 'A1:' . $hcol . $hrow;
                foreach (range('A', $hcol) as $col):
                    $excelSheet->getColumnDimension($col)->setAutoSize(true);
                endforeach;
                $excelSheet->getStyle($packFullRange)->applyFromArray($borderStyle);
                $excelSheet->getStyle('A1:'.$hcol.'2')->applyFromArray($bgPrimary);
                $excelSheet->getStyle('A4:'.$hcol.'4')->applyFromArray($bgPrimary);
                $i++;
            endforeach;

            $fileDirectory = realpath(APPPATH . '../assets/uploads/gst_report');
            $fileName = '/gstr1_' . time() . '.xlsx';
            
            $writer = new Xlsx($spreadsheet);
            $writer->save($fileDirectory . $fileName);
            header("Content-Type: application/vnd.ms-excel");
            redirect(base_url('assets/uploads/gst_report') . $fileName);
        else:
            $data = $this->input->post();
            $data['report'] = "gstr1";
            $result = $this->{$data['report_type']}($data);
            $this->printJson($result);
        endif;
    }

    public function getGstr2Report($jsonData=''){        
        if(!empty($jsonData)):
            $data =(array) decodeURL($jsonData);

            $spreadsheet = new Spreadsheet();
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $styleArray = [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
            ];
            $fontBold = ['font' => ['bold' => true]];
            $alignLeft = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];
            $alignCenter = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
            $alignRight = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];
            $borderStyle = [
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ]
            ];
            $bgPrimary = [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DAEAFA'],
                ],
                'font' => ['bold' => true]
            ];

            $i = 0;$data['report'] = "gstr2";
            foreach($this->gstr2ReportTypes as $key => $value):
                $html = "";
                $html = $this->{$key}($data)['html'];                

                $pdfData = '<table>'.$html.'</table>';
                $pdfData = str_replace('&', '&amp;', $pdfData);
                
                $reader->setSheetIndex($i);

                if($i == 0):
                    $spreadsheet = $reader->loadFromString($pdfData);
                else:
                    $spreadsheet = $reader->loadFromString($pdfData,$spreadsheet);
                endif;

                $spreadsheet->getSheet($i)->setTitle($value);
                $excelSheet = $spreadsheet->getSheet($i);
                $hcol = $excelSheet->getHighestColumn();
                $hrow = $excelSheet->getHighestRow();
                $packFullRange = 'A1:' . $hcol . $hrow;
                foreach (range('A', $hcol) as $col):
                    $excelSheet->getColumnDimension($col)->setAutoSize(true);
                endforeach;
                $excelSheet->getStyle($packFullRange)->applyFromArray($borderStyle);
                $excelSheet->getStyle('A1:'.$hcol.'2')->applyFromArray($bgPrimary);
                $excelSheet->getStyle('A4:'.$hcol.'4')->applyFromArray($bgPrimary);
                $i++;
            endforeach;

            $fileDirectory = realpath(APPPATH . '../assets/uploads/gst_report');
            $fileName = '/gstr2_' . time() . '.xlsx';
            
            $writer = new Xlsx($spreadsheet);
            $writer->save($fileDirectory . $fileName);
            header("Content-Type: application/vnd.ms-excel");
            redirect(base_url('assets/uploads/gst_report') . $fileName);
        else:
            $data = $this->input->post();
            $data['report'] = "gstr2";
            $result = $this->{$data['report_type']}($data);
            $this->printJson($result);
        endif;
    }

    public function b2b($data){
        $data['vou_name_s'] = ($data['report'] == "gstr1")?"'Sale','GInc'":"'Purc','GExp'";
        $result = $this->gstReport->_b2b($data);

        $no_of_recipients = $no_of_invoice = $total_invoice_value = $total_taxable_value = 0;
        $total_igst_amount = $total_cgst_amount = $total_sgst_amount = $total_cess = 0;
        $total_aigst_amount = $total_acgst_amount = $total_asgst_amount = $total_acess = 0;

        $html = '';
        if($data['report'] == "gstr1"):
            $transMainId = array(); $tbody = '';
            foreach($result as $row):
                $invType = "Regular B2B";
                if($row->tax_class == "SEZSGSTACC"):
                    $invType = "SEZ supplies with payment";
                elseif($row->tax_class == "SEZSGSTACC"):
                    $invType = "SEZ supplies without payment";
                elseif($row->tax_class == "SEZSGSTACC"):
                    $invType = "Deemed Exp";
                /* elseif(in_array($row->tax_class,["SALESIGSTACC","SALESJOBIGSTACC"])):
                    $invType = "Intra-State supplies attracting IGST"; */
                endif;

                $tbody .= '<tr>
                    <td class="text-left">'.$row->gstin.'</td>
                    <td class="text-left">'.$row->party_name.'</td>
                    <td class="text-left">'.$row->trans_number.'</td>
                    <td class="text-center">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                    <td class="text-left">'.$row->party_state_code.'-'.$row->state_name.'</td>
                    <td class="text-center">N</td>
                    <td class="text-left"></td>
                    <td class="text-left">'.$invType.'</td>
                    <td class="text-left"></td>
                    <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                </tr>';

                if(!in_array($row->id,$transMainId)):
                    $transMainId[] = $row->id;
                    ++$no_of_recipients;
                    ++$no_of_invoice;
                    $total_invoice_value += $row->net_amount;
                endif;

                $total_taxable_value += $row->taxable_amount;
                $total_igst_amount = 0;
                $total_cgst_amount = 0;
                $total_sgst_amount = 0;
                $total_cess += $row->cess_amount;
            endforeach;

            $html .= '<thead class="thead-dark text-center">
                <tr>
                    <th colspan="13">Summary For B2B,SEZ,DE(4A,4B,6B,6C)</th>
                </tr>
                <tr>
                    <th>No. of Recipients</th>
                    <th></th>
                    <th>No. of Invoices</th>
                    <th></th>
                    <th>Total Invoice Value</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Taxable Value</th>
                    <th>Total Cess</th>
                </tr>
                <tr>
                    <td>'.$no_of_recipients.'</td>
                    <td></td>
                    <td>'.$no_of_invoice.'</td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_invoice_value).'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                    <td>'.sprintf("%.2F",$total_cess).'</td>
                </tr>
                <tr>
                    <th>GSTIN/UIN of Recipient</th>
                    <th>Receiver Name</th>
                    <th>Invoice Number</th>
                    <th>Invoice date</th>
                    <th>Invoice Value</th>
                    <th>Place Of Supply</th>
                    <th>Reverse Charge</th>
                    <th>Applicable % of Tax Rate</th>
                    <th>Invoice Type</th>
                    <th>E-Commerce GSTIN</th>
                    <th>Rate</th>
                    <th>Taxable Value</th>
                    <th>Cess Amount</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';
        else:
            $transMainId = array(); $tbody = '';
            foreach($result as $row):
                $invType = "Regular";
                /* if($row->tax_class == "SEZRACC"):
                    $invType = "Received SEZ";
                endif; */

                $isRc = (in_array($row->tax_class,["PURURDGSTACC","PURURDIGSTACC"]))?"Y":"N";

                $tbody .= '<tr>
                    <td class="text-left">'.$row->gstin.'</td>
                    <td class="text-left">'.$row->trans_number.'</td>
                    <td class="text-center">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                    <td class="text-left">'.$row->party_state_code.'-'.$row->state_name.'</td>
                    <td class="text-center">'.$isRc.'</td>
                    <td class="text-left">'.$invType.'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->igst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cgst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->sgst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                    <td class="text-left">'.$row->itc.'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->igst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cgst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->sgst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                </tr>';

                if(!in_array($row->id,$transMainId)):
                    $transMainId[] = $row->id;
                    ++$no_of_recipients;
                    ++$no_of_invoice;
                    $total_invoice_value += $row->net_amount;
                endif;

                $total_taxable_value += $row->taxable_amount;
                $total_igst_amount += $row->igst_amount;
                $total_cgst_amount += $row->cgst_amount;
                $total_sgst_amount += $row->sgst_amount;
                $total_cess += $row->cess_amount;

                $total_aigst_amount += $row->igst_amount;
                $total_acgst_amount += $row->cgst_amount;
                $total_asgst_amount += $row->sgst_amount;
                $total_acess += $row->cess_amount;
            endforeach;

            $html .= '<thead class="thead-dark text-center">
                <tr>
                    <th colspan="18">Summary Of Supplies From Registered Suppliers B2B(3)</th>
                </tr>
                <tr>
                    <th>No. of Recipients</th>
                    <th>No. of Invoices</th>
                    <th></th>
                    <th>Total Invoice Value</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Taxable Value</th>
                    <th>Total Integrated Tax Paid</th>
                    <th>Total Central Tax Paid</th>
                    <th>Total State/UT Tax Paid</th>
                    <th>Total Cess Paid</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <td>'.$no_of_recipients.'</td>
                    <td>'.$no_of_invoice.'</td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_invoice_value).'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                    <td>'.sprintf("%.2F",$total_igst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_cgst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_sgst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_cess).'</td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_aigst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_acgst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_asgst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_acess).'</td>
                </tr>
                <tr>
                    <th>GSTIN of Supplier</th>
                    <th>Invoice Number</th>
                    <th>Invoice date</th>
                    <th>Invoice Value</th>
                    <th>Place Of Supply</th>
                    <th>Reverse Charge</th>
                    <th>Invoice Type</th>
                    <th>Rate</th>
                    <th>Taxable Value</th>
                    <th>Integrated Tax Paid</th>
                    <th>Central Tax Paid</th>
                    <th>State/UT Tax Paid</th>
                    <th>Cess Paid</th>
                    <th>Eligibility For ITC</th>
                    <th>Availed ITC Integrated Tax</th>
                    <th>Availed ITC Central Tax</th>
                    <th>Availed ITC State/UT Tax</th>
                    <th>Availed ITC Cess</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';            
        endif;

        return ['status'=>1,'html'=>$html];
    }

    public function b2bur($data){
        $data['vou_name_s'] = "'Purc','GExp'";
        $result = $this->gstReport->_b2bur($data);

        $no_of_recipients = $no_of_invoice = $total_invoice_value = $total_taxable_value = 0;
        $total_igst_amount = $total_cgst_amount = $total_sgst_amount = $total_cess = 0;
        $total_aigst_amount = $total_acgst_amount = $total_asgst_amount = $total_acess = 0;

        $html = '';
        $transMainId = array(); $tbody = '';
        foreach($result as $row):

            $tbody .= '<tr>
                <td class="text-left">'.$row->party_name.'</td>
                <td class="text-left">'.$row->doc_no.'</td>
                <td class="text-center">'.date("d-M-Y",strtotime($row->doc_date)).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                <td class="text-left">'.$row->party_state_code.'-'.$row->state_name.'</td>
                <td class="text-left">'.(($row->gst_type == 2)?"Intra State":"Inter State").'</td>
                <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->igst_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->cgst_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->sgst_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                <td class="text-left">'.$row->itc.'</td>
                <td class="text-right">0.00</td>
                <td class="text-right">0.00</td>
                <td class="text-right">0.00</td>
                <td class="text-right">0.00</td>
            </tr>';

            if(!in_array($row->id,$transMainId)):
                $transMainId[] = $row->id;
                ++$no_of_recipients;
                ++$no_of_invoice;
                $total_invoice_value += $row->net_amount;
            endif;

            $total_taxable_value += $row->taxable_amount;
            $total_igst_amount += $row->igst_amount;
            $total_cgst_amount += $row->cgst_amount;
            $total_sgst_amount += $row->sgst_amount;
            $total_cess += $row->cess_amount;
        endforeach;

        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th colspan="17">Summary Of Supplies From Unregistered Suppliers B2BUR(4B)</th>
            </tr>
            <tr>
                <th>No. of Recipients</th>
                <th>No. of Invoices</th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Integrated Tax Paid</th>
                <th>Total Central Tax Paid</th>
                <th>Total State/UT Tax Paid</th>
                <th>Total Cess Paid</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td>'.$no_of_recipients.'</td>
                <td>'.$no_of_invoice.'</td>
                <td></td>
                <td>'.sprintf("%.2F",$total_invoice_value).'</td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                <td>'.sprintf("%.2F",$total_igst_amount).'</td>
                <td>'.sprintf("%.2F",$total_cgst_amount).'</td>
                <td>'.sprintf("%.2F",$total_sgst_amount).'</td>
                <td>'.sprintf("%.2F",$total_cess).'</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>Supplier Name</th>
                <th>Invoice Number</th>
                <th>Invoice date</th>
                <th>Invoice Value</th>
                <th>Place Of Supply</th>
                <th>Supply Type</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Integrated Tax Paid</th>
                <th>Central Tax Paid</th>
                <th>State/UT Tax Paid</th>
                <th>Cess Paid</th>
                <th>Eligibility For ITC</th>
                <th>Availed ITC Integrated Tax</th>
                <th>Availed ITC Central Tax</th>
                <th>Availed ITC State/UT Tax</th>
                <th>Availed ITC Cess</th>
            </tr>
        </thead><tbody>'.$tbody.'</tbody>';

        return ['status'=>1,'html'=>$html];
    }

    public function b2ba($data){
        $html = '';
        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th>Summary For B2BA</th>
                <th colspan="3">Original Details</th>
                <th colspan="11">Revised details</th>
            </tr>
            <tr>
                <th>No. of Recipients</th>
                <th></th>
                <th>No. of Invoices</th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td>0</td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
                <th>GSTIN/UIN of Recipient</th>
                <th>Receiver Name</th>
                <th>Original Invoice Number</th>
                <th>Original Invoice date</th>
                <th>Revised Invoice Number</th>
                <th>Revised Invoice date</th>
                <th>Invoice Value</th>
                <th>Place Of Supply</th>
                <th>Reverse Charge</th>
                <th>Applicable % of Tax Rate</th>
                <th>Invoice Type</th>
                <th>E-Commerce GSTIN</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function b2cl($data){
        $data['vou_name_s'] = "'Sale','GInc'";
        $result=$this->gstReport->_b2cl($data);

        $no_of_recipients = $no_of_invoice = $total_invoice_value = $total_taxable_value = $total_cess = 0;
        $transMainId = array(); $html = $tbody = '';
        foreach($result as $row):
            $tbody .= '<tr>
                <td class="text-left">'.$row->trans_number.'</td>
                <td class="text-center">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                <td class="text-left">'.$row->party_state_code.'-'.$row->state_name.'</td>
                <td class="text-center">N</td>
                <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                <td class="text-left"></td>
                <td class="text-left"></td>                
            </tr>';

            if(!in_array($row->id,$transMainId)):
                $transMainId[] = $row->id;
                ++$no_of_recipients;
                ++$no_of_invoice;
                $total_invoice_value += $row->net_amount;
            endif;
            $total_taxable_value += $row->taxable_amount;
            $total_cess += $row->cess_amount;
        endforeach;

        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th colspan="10">Summary For B2CL(5)</th>
            </tr>
            <tr>
                <th>No. of Invoices</th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td>'.$no_of_invoice.'</td>
                <td></td>
                <td>'.sprintf("%.2F",$total_invoice_value).'</td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                <td>'.sprintf("%.2F",$total_cess).'</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>Invoice Number</th>
                <th>Invoice date</th>
                <th>Invoice Value</th>
                <th>Place Of Supply</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
                <th>E-Commerce GSTIN</th>
                <th>Sale from Bonded WH</th>
            </tr>
        </thead><tbody>'.$tbody.'</tbody>';
        
        return ['status'=>1,'html'=>$html];
    }

    public function b2cla($data){
        $html = '';
        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th>Summary For B2CLA</th>
                <th colspan="2">Original Details</th>
                <th colspan="9">Revised details</th>
            </tr>
            <tr>
                <th>No. of Invoices</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>Original Invoice Number</th>
                <th>Original Invoice date</th>
                <th>Original Place Of Supply</th>
                <th>Revised Invoice Number</th>
                <th>Revised Invoice date</th>
                <th>Invoice Value</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
                <th>E-Commerce GSTIN</th>
                <th>Sale from Bonded WH</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function b2cs($data){
        $data['vou_name_s'] = "'Sale','GInc','C.N.','D.N.'";
        $result=$this->gstReport->_b2cs($data);

        $total_taxable_value = $total_cess = 0;
        $transMainId = array(); $html = $tbody = '';
        foreach($result as $row):
            $tbody .= '<tr>
                <td class="text-left">OE</td>
                <td class="text-left">'.$row->party_state_code.' - '.$row->state_name.'</td>
                <td class="text-left"></td>
                <td class="text-right">'.$row->gst_per.'</td>
                <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                <td class="text-left"></td>
            </tr>';

            $total_taxable_value += $row->taxable_amount;
            $total_cess += $row->cess_amount;
        endforeach;

        $html .= '<thead class="thead-dark text-center">
            <tr>    
                <th colspan="7">Summary For B2CS(7)</th>
            </tr>
            <tr>    
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
                <th></th>
            </tr>
            <tr>    
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                <td>'.sprintf("%.2F",$total_cess).'</td>
                <td></td>
            </tr>
            <tr>
                <th>Type</th>
                <th>Place Of Supply</th>
                <th>Applicable % Of Tax</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
                <th>E-Commarce GSTIN</th>
            </tr>
        </thead><tbody>'.$tbody.'</tbody>';

        return ['status'=>1,'html'=>$html];
    }

    public function b2csa($data){
        $html = '';
        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th>Summary For B2CSA</th>
                <th>Original Details</th>
                <th colspan="7">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
                <th></th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
                <td></td>
            </tr>
            <tr>
                <th>Financial Year</th>
                <th>Original Month</th>
                <th>Place Of Supply</th>
                <th>Type</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
                <th>E-Commerce GSTIN</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function cdnr($data){      
        $data['vou_name_s'] = "'C.N.','D.N.'";
        $result = $this->gstReport->_cdnr($data);

        $no_of_recipients = $no_of_invoice = $total_invoice_value = $total_taxable_value = 0;
        $total_igst_amount = $total_cgst_amount = $total_sgst_amount = $total_cess = 0;

        $html = '';
        if($data['report'] == "gstr1"):
            $transMainId = array(); $tbody = '';
            foreach($result as $row):
                $invType = "Regular B2B";
                if($row->tax_class == "SEZSGSTACC"):
                    $invType = "SEZ supplies with payment";
                elseif($row->tax_class == "SEZSGSTACC"):
                    $invType = "SEZ supplies without payment";
                elseif($row->tax_class == "SEZSGSTACC"):
                    $invType = "Deemed Exp";
                /* elseif(in_array($row->tax_class,["SALESIGSTACC","SALESJOBIGSTACC"])):
                    $invType = "Intra-State supplies attracting IGST"; */
                endif;
                
                $tbody .= '<tr>
                    <td class="text-left">'.$row->gstin.'</td>
                    <td class="text-left">'.$row->party_name.'</td>
                    <td class="text-left">'.$row->trans_number.'</td>
                    <td class="text-center">'.$row->trans_date.'</td>
                    <td class="text-center">'.(($row->vou_name_s == "C.N.")?"C":"D").'</td>
                    <td class="text-left">'.$row->party_state_code.'-'.$row->state_name.'</td>
                    <td class="text-center">N</td>
                    <td class="text-left">'.$invType.'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                    <td class="text-center">0.00</td>  
                    <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                </tr>';

                if(!in_array($row->id,$transMainId)):
                    $transMainId[] = $row->id;
                    ++$no_of_recipients;
                    ++$no_of_invoice;
                    $total_invoice_value += $row->net_amount;
                endif;
    
                $total_taxable_value += $row->taxable_amount;
                $total_igst_amount += $row->igst_amount;
                $total_cgst_amount += $row->cgst_amount;
                $total_sgst_amount += $row->sgst_amount;
                $total_cess += $row->cess_amount;
            endforeach;

            $html .= '<thead class="thead-dark text-center">
                <tr>
                    <th colspan="13">Summary For CDNR(9B)</th>
                </tr>
                <tr>
                    <th>No. of Recipient</th>
                    <th></th>
                    <th>No. of Notes</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Note Value</th>
                    <th></th>
                    <th></th>
                    <th>Total Taxable Value</th>
                    <th>Total Cess</th>
                </tr>
                <tr>
                    <td>'.$no_of_recipients.'</td>
                    <td></td>
                    <td>'.$no_of_invoice.'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_invoice_value).'</td>
                    <td></td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                    <td>'.sprintf("%.2F",$total_cess).'</td>
                </tr>
                <tr>
                    <th>GSTIN/UIN of Recipient</th>
                    <th>Receiver Name</th>
                    <th>Note Number</th>
                    <th>Note Date</th>
                    <th>Note Type</th>
                    <th>Place Of Supply</th>
                    <th>Reverse Charge</th>
                    <th>Note Supply Type</th>
                    <th>Note Value</th>
                    <th>Applicable % of Tax Rate</th>
                    <th>Rate</th>
                    <th>Taxable Value</th>
                    <th>Cess Amount</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';
        else:            
            $transMainId = array(); $tbody = '';
            foreach($result as $row):
                $tbody .= '<tr>
                    <td class="text-left">'.$row->gstin.'</td>
                    <td class="text-left">'.$row->trans_number.'</td>
                    <td class="text-left">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                    <td class="text-center">'.$row->doc_no.'</td>
                    <td class="text-center">'.((!empty($row->doc_date))?date("d-M-Y",strtotime($row->doc_date)):"").'</td>
                    <td class="text-center">N</td>
                    <td class="text-center">'.(($row->vou_name_s == "C.N.")?"C":"D").'</td>
                    <td class="text-center">07-Others</td>
                    <td class="text-center">'.(($row->gst_type == 2)?"Intra State":"Inter State").'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->igst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cgst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->sgst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                    <td class="text-center">'.$row->itc.'</td>
                    <td class="text-center">0.00</td>
                    <td class="text-center">0.00</td>
                    <td class="text-center">0.00</td>
                    <td class="text-center">0.00</td>
                </tr>';

                if(!in_array($row->id,$transMainId)):
                    $transMainId[] = $row->id;
                    ++$no_of_recipients;
                    ++$no_of_invoice;
                    $total_invoice_value += $row->net_amount;
                endif;
    
                $total_taxable_value += $row->taxable_amount;
                $total_igst_amount += $row->igst_amount;
                $total_cgst_amount += $row->cgst_amount;
                $total_sgst_amount += $row->sgst_amount;
                $total_cess += $row->cess_amount;
            endforeach;

            $html .= '<thead class="thead-dark text-center">
                <tr>
                    <th colspan="21">Summary For CDNR(6C)</th>
                </tr>
                <tr>
                    <th>No. of Recipient</th>
                    <th>No. of Notes</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Note/Refund Voucher Value</th>
                    <th></th>
                    <th>Total Taxable Value</th>
					<th>Total Integrated Tax Paid</th>
                    <th>Total Central Tax Paid</th>
                    <th>Total State/UT Tax Paid</th>
                    <th>Total Cess</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <td>'.$no_of_recipients.'</td>
                    <td>'.$no_of_invoice.'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_invoice_value).'</td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                    <td>'.sprintf("%.2F",$total_igst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_cgst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_sgst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_cess).'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>GSTIN of Supplier</th>
                    <th>Note/Refund Voucher Number</th>
                    <th>Note/Refund Voucher date</th>
                    <th>Invoice/Advance Payment Voucher Number</th>
                    <th>Invoice/Advance Payment Voucher date</th>
                    <th>Pre GST</th>
                    <th>Document Type</th>
                    <th>Reason For Issuing document</th>
                    <th>Supply Type</th>
                    <th>Note/Refund Voucher Value</th>
                    <th>Rate</th>
                    <th>Taxable Value</th>
                    <th>Integrated Tax Paid</th>
                    <th>Central Tax Paid</th>
                    <th>State/UT Tax Paid</th>
                    <th>Cess Paid</th>
                    <th>Eligibility For ITC</th>
                    <th>Availed ITC Integrated Tax</th>
                    <th>Availed ITC Central Tax</th>
                    <th>Availed ITC State/UT Tax</th>
                    <th>Availed ITC Cess</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';
        endif;
        
        return ['status'=>1,'html'=>$html];
    }

    public function cdnra($data){
        $html = '';
        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th >Summary For CDNRA</th>
                <th colspan="5" class="text-center">Original Details</th>
                <th colspan="9" class="text-center">Revised details</th>
            </tr>
            <tr>
                <th>No. of Recipient</th>
                <th></th>
                <th>No. of Notes/Vouchers</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Note Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td>0</td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
                <th>GSTIN/UIN of Recipient</th>
                <th>Receiver Name</th>
                <th>Original Note Number</th>
                <th>Original Note Date</th>
                <th>Revised Note Number</th>
                <th>Revised Note Date</th>
                <th>Note Type</th>
                <th>Place Of Supply</th>
                <th>Reverse Charge</th>
                <th>Note Supply Type</th>
                <th>Note Value</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function cdnur($data){
        $data['vou_name_s'] = "'C.N.','D.N.'";
        $result = $this->gstReport->_cdnur($data);

        $no_of_recipients = $no_of_invoice = $total_invoice_value = $total_taxable_value = 0;
        $total_igst_amount = $total_cgst_amount = $total_sgst_amount = $total_cess = 0;

        $html = '';
        if($data['report'] == "gstr1"):
            $transMainId = array(); $tbody = '';
            foreach($result as $row):
                $urType = "B2CL";
                if($row->tax_class == "EXPORTGSTACC"):
                    $invType = "EXPWP";
                elseif($row->tax_class == "EXPORTTFACC"):
                    $invType = "EXPWOP";
                endif;

                $tbody .= '<tr>
                    <td class="text-left">'.$urType.'</td>
                    <td class="text-left">'.$row->trans_number.'</td>
                    <td class="text-center">'.$row->trans_date.'</td>
                    <td class="text-center">'.(($row->entry_type == "C.N.")?"C":"D").'</td>
                    <td class="text-left">'.$row->party_state_code.'-'.$row->state_name.'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                    <td class="text-center">N</td>  
                    <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                </tr>';

                if(!in_array($row->id,$transMainId)):
                    $transMainId[] = $row->id;
                    ++$no_of_recipients;
                    ++$no_of_invoice;
                    $total_invoice_value += $row->net_amount;
                endif;
    
                $total_taxable_value += $row->taxable_amount;
                $total_igst_amount += $row->igst_amount;
                $total_cgst_amount += $row->cgst_amount;
                $total_sgst_amount += $row->sgst_amount;
                $total_cess += $row->cess_amount;
            endforeach;

            $html .= '<thead class="thead-dark text-center">
                <tr>
                    <th colspan="10">Summary For CDNUR(9B)</th>
                </tr>
                <tr>
                    <th></th>
                    <th>No. of Notes</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Note Value</th>
                    <th></th>
                    <th></th>
                    <th>Total Taxable Value</th>
                    <th>Total Cess</th>
                </tr>
                <tr>
                    <td></td>
                    <td>'.$no_of_invoice.'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.$total_invoice_value.'</td>
                    <td></td>
                    <td></td>
                    <td>'.$total_taxable_value.'</td>
                    <td>'.$total_cess.'</td>
                </tr>
                <tr>
                    <th>UR Type</th>
                    <th>Note Number</th>
                    <th>Note Date</th>
                    <th>Note Type</th>
                    <th>Place Of Supply</th>
                    <th>Note Value</th>
                    <th>Applicable % of Tax Rate</th>
                    <th>Rate</th>
                    <th>Taxable Value</th>
                    <th>Cess Amount</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';
        else:
            $transMainId = array(); $tbody = '';
            foreach($result as $row):
                $tbody .= '<tr>
                    <td class="text-left">'.$row->trans_number.'</td>
                    <td class="text-left">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                    <td class="text-center">'.$row->doc_no.'</td>
                    <td class="text-center">'.((!empty($row->doc_date))?date("d-M-Y",strtotime($row->doc_date)):"").'</td>
                    <td class="text-center">N</td>
                    <td class="text-center">'.(($row->entry_type == 13)?"C":"D").'</td>
                    <td class="text-center">07-Others</td>
                    <td class="text-center">'.(($row->gst_type == 2)?"Intra State":"Inter State").'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->igst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cgst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->sgst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                    <td class="text-center"></td>
                    <td class="text-center">0.00</td>
                    <td class="text-center">0.00</td>
                    <td class="text-center">0.00</td>
                    <td class="text-center">0.00</td>
                </tr>';

                if(!in_array($row->id,$transMainId)):
                    $transMainId[] = $row->id;
                    ++$no_of_recipients;
                    ++$no_of_invoice;
                    $total_invoice_value += $row->net_amount;
                endif;
    
                $total_taxable_value += $row->taxable_amount;
                $total_igst_amount += $row->igst_amount;
                $total_cgst_amount += $row->cgst_amount;
                $total_sgst_amount += $row->sgst_amount;
                $total_cess += $row->cess_amount;
            endforeach;

            $html .= '<thead class="thead-dark text-center">
                <tr>
                    <th colspan="20">Summary For CDNUR(6C)</th>
                </tr>
                <tr>
                    <th>No. of Vouchers</th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                    <th>Total Note/Voucher Value</th>                    
                    <th></th>                    
                    <th>Total Taxable Value</th>
					<th>Total Integrated Tax Paid</th>
                    <th>Total Central Tax Paid</th>
                    <th>Total State/UT Tax Paid</th>
                    <th>Total Cess</th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                </tr>
                <tr>
                    <td>'.$no_of_invoice.'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.$total_invoice_value.'</td>
                    <td></td>
                    <td>'.$total_taxable_value.'</td>
                    <td>'.$total_igst_amount.'</td>
                    <td>'.$total_cgst_amount.'</td>
                    <td>'.$total_sgst_amount.'</td>
                    <td>'.$total_cess.'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>Note/Voucher Number</th>
                    <th>Note/Voucher date</th>
                    <th>Invoice/Advance Payment Voucher number</th>
                    <th>Invoice/Advance Payment Voucher date</th>
                    <th>Pre GST</th>
                    <th>Document Type</th>
                    <th>Reason For Issuing document</th>
                    <th>Supply Type</th>
                    <th>Note/Voucher Value</th>
                    <th>Rate</th>
                    <th>Taxable Value</th>
                    <th>Integrated Tax Paid</th>
                    <th>Central Tax Paid</th>
                    <th>State/UT Tax Paid</th>
                    <th>Cess Paid</th>
                    <th>Eligibility For ITC</th>
                    <th>Availed ITC Integrated Tax</th>
                    <th>Availed ITC Central Tax</th>
                    <th>Availed ITC State/UT Tax</th>
                    <th>Availed ITC Cess</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';
        endif;
        return ['status'=>1,'html'=>$html];
    }

    public function cdnura($data){
        $html = '';
        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th >Summary For CDNURA</th>
                <th colspan="4" class="text-center">Original Details</th>
                <th colspan="7" class="text-center">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th>No. of Notes/Vouchers</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Note Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
                <th>UR Type</th>
                <th>Original Note Number</th>
                <th>Original Note Date</th>
                <th>Revised Note Number</th>
                <th>Revised Note Date</th>
                <th>Note Type</th>
                <th>Place Of Supply</th>
                <th>Note Value</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function exp($data){
        $data['vou_name_s'] = "'Sale'";
        $result=$this->gstReport->_exp($data);

        $no_of_invoice = $total_invoice_value = $total_taxable_value = $total_cess = 0;
        $transMainId = array(); $html = $tbody = '';
        foreach($result as $row):
            $tbody .= '<tr>
                <td class="text-left">'.(($row->tax_class == "EXPORTGSTACC")?"WPAY":"WOPAY").'</td>
                <td class="text-left">'.$row->trans_number.'</td>
                <td class="text-left">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                <td class="text-left">'.$row->port_code.'</td>
                <td class="text-left">'.$row->ship_bill_no.'</td>
                <td class="text-left">'.date("d-M-Y",strtotime($row->ship_bill_date)).'</td>
                <td class="text-left">'.sprintf("%.2F",$row->gst_per).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
            </tr>';

            if(!in_array($row->id,$transMainId)):
                $transMainId[] = $row->id;
                ++$no_of_invoice;
                $total_invoice_value += $row->net_amount;
            endif;

            $total_taxable_value += $row->taxable_amount;
            $total_cess += $row->cess_amount;
        endforeach;

        $html = '';
        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th colspan="10">Summary For EXP(6)</th>
            </tr>
            <tr>
                <th></th>
                <th>No. of Invoices</th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th>No. of Shipping Bill</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td></td>
                <td>'.$no_of_invoice.'</td>
                <td></td>
                <td>'.$total_invoice_value.'</td>
                <td></td>
                <td>'.$no_of_invoice.'</td>
                <td></td>
                <td></td>
                <td>'.$total_taxable_value.'</td>
                <td>'.$total_cess.'</td>
            </tr>
            <tr>
                <th>Export Type</th>
                <th>Invoice Number</th>
                <th>Invoice date</th>
                <th>Invoice Value</th>
                <th>Port Code</th>
                <th>Shipping Bill Number</th>
                <th>Shipping Bill Date</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead><tbody>'.$tbody.'</tbody>';
        return ['status'=>1,'html'=>$html];
    }

    public function expa($data){
        $html = '';
        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th >Summary For EXP(6)</th>
                <th colspan="2">Original Details</th>
                <th colspan="9">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th>No. of Invoices</th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th>No. of Shipping Bill</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
                <th>Export Type</th>
                <th>Original Invoice Number</th>
                <th>Original Invoice date</th>
                <th>Revised Invoice Number</th>
                <th>Revised Invoice date</th>
                <th>Invoice Value</th>
                <th>Port Code</th>
                <th>Shipping Bill Number</th>
                <th>Shipping Bill Date</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function at($data){
        $result = array();

        $html = '';
        if($data['report'] == "gstr1"):
            $html .= '<thead class="thead-dark text-center">
                <tr>
                    <th colspan="5">Summary For Advance Received (11B)</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Advanced Received</th>
                    <th>Total Cess</th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>0.00</td>
                    <td>0.00</td>
                </tr>
                <tr>
                    <th>Place Of Supply</th>
                    <th>Applicable % of Tax Rate</th>
                    <th>Rate</th>
                    <th>Gross Advance Received</th>
                    <th>Cess Amount</th>
                </tr>
            </thead>';
        else:
            $html .= '<thead class="thead-dark text-center">
                <tr>
                    <th colspan="4">Summary For  Tax Liability on Advance Paid  under reverse charge(10 A)</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th>Total Gross Advance Paid</th>
                    <th>Total Cess</th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>0.00</td>
                    <td>0.00</td>
                </tr>
                <tr>
                    <th>Place Of Supply</th>
                    <th>Rate</th>
                    <th>Gross Advance Paid</th>
                    <th>Cess Amount</th>
                </tr>
            </thead>';
        endif;
        return ['status'=>1,'html'=>$html];
    }

    public function ata($data){
        $html = '';
        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th >Summary For Amended Tax Liability(Advance Received)</th>
                <th colspan="2">Original Details</th>
                <th colspan="4">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Advanced Received</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
                <th>Financial Year  </th>
                <th>Original Month</th>
                <th>Original Place Of Supply</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Gross Advance Received</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function atadj($data){
        $html = '';

        if($data['report'] == "gstr1"):
            $html .= '<thead class="thead-dark text-center">
                <tr>
                    <th colspan="5" class="text-center">Summary For Advance Adjusted (11B)</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Advanced Adjusted</th>
                    <th>Total Cess</th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <th>Place Of Supply</th>
                    <th>Applicable % of Tax Rate</th>
                    <th>Rate</th>
                    <th>Gross Advance Adjusted</th>
                    <th>Cess Amount</th>
                </tr>
            </thead>';
        else:
            $html .= '<thead class="thead-dark text-center">
                <tr>
                    <th colspan="4" class="text-center">Summary For Adjustment of advance tax paid earlier for reverse charge supplies (10 B)</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th>Total Gross Advance Paid to be Adjusted</th>
                    <th>Total Cess Adjusted</th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>0.00</td>
                    <td>0.00</td>
                </tr>
                <tr>
                    <th>Place Of Supply</th>
                    <th>Rate</th>
                    <th>Gross Advance Paid to be Adjusted</th>
                    <th>Cess Adjusted</th>
                </tr>
            </thead>';
        endif;
        return ['status'=>1,'html'=>$html];
    }

    public function atadja($data){
        $html = '';
        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th >Summary For Amendement Of Adjustment Advances</th>
                <th colspan="2">Original Details</th>
                <th colspan="4">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Advanced Adjusted</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
                <th>Financial Year  </th>
                <th>Original Month</th>
                <th>Original Place Of Supply</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Gross Advance Adjusted</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function exemp($data){        
        $result = $this->gstReport->_exemp($data);
        
        $totalComposition = $totalNillRated = $totaExempted = $totalNonGst = 0;
        $html = $tbody = '';
        if($data['report'] == "gstr1"):
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$row->description.'</td>
                    <td>'.sprintf("%.2F",$row->nill_rate_amount).'</td>
                    <td>'.sprintf("%.2F",$row->exe_amount).'</td>
                    <td>'.sprintf("%.2F",$row->non_gst_amount).'</td>
                </tr>';

                $totalNillRated += $row->nill_rate_amount;
                $totaExempted += $row->exe_amount;
                $totalNonGst += $row->non_gst_amount;
            endforeach;

            $html .= '<thead class="thead-dark text-center">
                <tr>
                    <th colspan="5">Summary For Nil rated, exempted and non GST outward supplies (8)</th>
                </tr>
                <tr>
                    <th></th>
                    <th>Total Nil Rated Supplies</th>
                    <th>Total Exempted Supplies</th>
                    <th>Total Non-GST Supplies</th>
                </tr>
                <tr>
                    <td></td>
                    <td>'.$totalNillRated.'</td>
                    <td>'.$totaExempted.'</td>
                    <td>'.$totalNonGst.'</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <th>Nil Rated Supplies</th>
                    <th>Exempted(other than nil rated/non GST supply)</th>
                    <th>Non-GST supplies</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';
        else:
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$row->description.'</td>
                    <td>'.sprintf("%.2F",$row->compo_amount).'</td>
                    <td>'.sprintf("%.2F",$row->nill_rate_amount).'</td>
                    <td>'.sprintf("%.2F",$row->exe_amount).'</td>
                    <td>'.sprintf("%.2F",$row->non_gst_amount).'</td>
                </tr>';

                $totalComposition += $row->compo_amount;
                $totalNillRated += $row->nill_rate_amount;
                $totaExempted += $row->exe_amount;
                $totalNonGst += $row->non_gst_amount;
            endforeach;

            $html .= '<thead class="thead-dark text-center">
                <tr>
                    <th colspan="5">Summary For Composition,Nil rated,exempted and non GST inward supplies</th>
                </tr>
                <tr>
                    <th></th>
                    <th>Total Composition taxable person</th>
                    <th>Total Nil Rated Supplies</th>
                    <th>Total Exempted Supplies</th>
                    <th>Total Non-GST Supplies</th>
                </tr>
                <tr>
                    <td></td>
                    <td>'.$totalComposition.'</td>
                    <td>'.$totalNillRated.'</td>
                    <td>'.$totaExempted.'</td>
                    <td>'.$totalNonGst.'</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <th>Composition taxable person</th>
                    <th>Nil Rated Supplies</th>
                    <th>Exempted (other than nil rated/non GST supply )</th>
                    <th>Non-GST supplies</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';
        endif;
        return ['status'=>1,'html'=>$html];
    }

    public function hsn($data){
        $data['vou_name_s'] = ($data['report'] == "gstr1")?"'Sale','GInc','C.N.','D.N.'":"'Purc','GExp','C.N.','D.N.'";
        $result=$this->gstReport->_hsn($data);

        $total_taxable_value = 0;
        $total_value = 0;
        $total_cgst = 0;
        $total_sgst = 0;
        $total_igst = 0;
        $total_cess = 0;

        if(!empty($result)):
            $total_taxable_value = array_sum(array_column($result,'taxable_amount'));
            $total_value = array_sum(array_column($result,'net_amount'));
            $total_cgst = array_sum(array_column($result,'cgst_amount'));
            $total_sgst = array_sum(array_column($result,'sgst_amount'));
            $total_igst = array_sum(array_column($result,'igst_amount'));       
            $total_cess = array_sum(array_column($result,'cess_amount')); 
        endif;

        $html = '';
        if($data['report'] == "gstr1"):
            $html .= '<thead class="thead-dark text-center">
                <tr>
                    <th colspan="11">Summary For HSN(12)</th>
                </tr>
                <tr>
                    <th>No. of HSN</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Value</th>
                    <th></th>
                    <th>Total Taxable Value</th>
                    <th>Total Integrated Tax</th>
                    <th>Total Central Tax</th>
                    <th>Total State/UT Tax</th>
                    <th>Total Cess</th>
                </tr>
                <tr>
                    <td>'.count($result).'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_value).'</td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                    <td>'.sprintf("%.2F",$total_igst).'</td>
                    <td>'.sprintf("%.2F",$total_cgst).'</td>
                    <td>'.sprintf("%.2F",$total_sgst).'</td>
                    <td>'.sprintf("%.2F",$total_cess).'</td>
                </tr>
                <tr>
                    <th>HSN</th>
                    <th>Description</th>
                    <th>UQC</th>
                    <th>Total Quantity</th>
                    <th>Total Value</th>
                    <th>Rate</th>
                    <th>Taxable Value</th>
                    <th>Integrated Tax Amount</th>
                    <th>Central Tax Amount</th>
                    <th>State/UT Tax Amount</th>
                    <th>Cess Amount</th>
                </tr>
            </thead><tbody>';

            foreach($result as $row):
                $html .= '<tr>
                <td>'.$row->hsn_code.'</td>
                <td>'.$row->hsn_description.'</td>
                <td>'.$row->unit_name.' - '.$row->unit_description.'</td>
                <td>'.$row->qty.'</td>
                <td>'.sprintf("%.2F",$row->net_amount).'</td>
                <td>'.sprintf("%.2F",$row->gst_per).'</td>
                <td>'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td>'.sprintf("%.2F",$row->igst_amount).'</td>
                <td>'.sprintf("%.2F",$row->cgst_amount).'</td>
                <td>'.sprintf("%.2F",$row->sgst_amount).'</td>
                <td>'.sprintf("%.2F",$row->cess_amount).'</td>
                </tr>';
            endforeach;
            $html .='</tbody>';
        else:
            $html .= '<thead class="thead-dark text-center">
                <tr>
                    <th colspan="10">Summary For HSN(13)</th>
                </tr>
                <tr>
                    <th>No. of HSN</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Value</th>
                    <th>Total Taxable Value</th>
                    <th>Total Integrated Tax Amount</th>
                    <th>Total Central Tax Amount</th>
                    <th>Total State/UT Tax Amount</th>
                    <th>Total Cess Amount</th>
                </tr>
                <tr>
                    <td>'.count($result).'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_value).'</td>
                    <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                    <td>'.sprintf("%.2F",$total_igst).'</td>
                    <td>'.sprintf("%.2F",$total_cgst).'</td>
                    <td>'.sprintf("%.2F",$total_sgst).'</td>
                    <td>'.sprintf("%.2F",$total_cess).'</td>
                </tr>
                <tr>
                    <th>HSN</th>
                    <th>Description</th>
                    <th>UQC</th>
                    <th>Total Quantity</th>
                    <th>Total Value</th>
                    <th>Taxable Value</th>
                    <th>Integrated Tax Amount</th>
                    <th>Central Tax Amount</th>
                    <th>State/UT Tax Amount</th>
                    <th>Cess Amount</th>
                </tr>
            </thead><tbody>';

            foreach($result as $row):
                $html .= '<tr>
                <td>'.$row->hsn_code.'</td>
                <td>'.$row->hsn_description.'</td>
                <td>'.$row->unit_name.' - '.$row->unit_description.'</td>
                <td>'.$row->qty.'</td>
                <td>'.sprintf("%.2F",$row->net_amount).'</td>
                <td>'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td>'.sprintf("%.2F",$row->igst_amount).'</td>
                <td>'.sprintf("%.2F",$row->cgst_amount).'</td>
                <td>'.sprintf("%.2F",$row->sgst_amount).'</td>
                <td>'.sprintf("%.2F",$row->cess_amount).'</td>
                </tr>';
            endforeach;
            $html .='</tbody>';
        endif;
        return ['status'=>1,'html'=>$html];
    }

    public function docs($data){
        $data['vou_name_s']="'Sales','GInc','C.N.','D.N.'";
        $result=$this->gstReport->_docs($data);
        $total_number = 0;
        $total_cancelled = 0;

        if(!empty($result)):
            $total_number = (!empty($result))?array_sum(array_column($result,'total_inv')):0;
            $total_cancelled = (!empty($result))?array_sum(array_column($result,'total_cnl_inv')):0;
        endif;

        $html = '';
        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th colspan="5">Summary of documents issued during the tax period(13)</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Number</th>
                <th>Total Cancelled</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$total_number.'</td>
                <td>'.$total_cancelled.'</td>
            </tr>
            <tr>
                <th>Nature of Document</th>
                <th>Sr. No. From</th>
                <th>Sr. No. To</th>
                <th>Total Number</th>
                <th>Cancelled</th>
            </tr>
        </thead>';
        foreach($result as $row):
            $html .= '<tr>
                <td class="text-left">'.$row->document.'</td>
                <td class="text-left">'.$row->trans_prefix.$row->min_trans_no.'</td>
                <td class="text-left">'.$row->trans_prefix.$row->max_trans_no.'</td>
                <td class="text-right">'.$row->total_inv.'</td>
                <td class="text-left">'.$row->total_cnl_inv.'</td>
            </tr>';
        endforeach;
        return ['status'=>1,'html'=>$html];
    }

    public function imps($data){
        $data['vou_name_s'] = "'Purc','GExp'";
        $result=$this->gstReport->_imps($data);
        $html = '';

        $no_of_recipients = $no_of_invoice = $total_invoice_value = $total_taxable_value = 0;
        $total_igst_amount = $total_cgst_amount = $total_sgst_amount = $total_cess = 0;
        $total_aigst_amount = $total_acgst_amount = $total_asgst_amount = $total_acess = 0;

        $html = '';
        $transMainId = array(); $tbody = '';
        foreach($result as $row):

            $tbody .= '<tr>
                <td class="text-center">'.$row->trans_number.'</td>
                <td class="text-center">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                <td class="text-left">'.$row->party_state_code.'-'.$row->state_name.'</td>
                <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->igst_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                <td class="text-left">'.$row->itc.'</td>
                <td class="text-right">0.00</td>
                <td class="text-right">0.00</td>
            </tr>';

            if(!in_array($row->id,$transMainId)):
                $transMainId[] = $row->id;
                ++$no_of_recipients;
                ++$no_of_invoice;
                $total_invoice_value += $row->net_amount;
            endif;

            $total_taxable_value += $row->taxable_amount;
            $total_igst_amount += $row->igst_amount;
            $total_cess += $row->cess_amount;
        endforeach;

        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th colspan="11">Summary For IMPS(4C)</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
				<th>Total Integrated Tax Paid</th>
                <th>Total Cess</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>'.$total_invoice_value.'</td>
                <td></td>
                <td></td>
                <td>'.$total_taxable_value.'</td>
                <td>'.$total_igst_amount.'</td>
                <td>'.$total_cess.'</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>Invoice Number of Reg Recipient</th>
                <th>Invoice Date</th>
                <th>Invoice Value</th>
                <th>Place Of Supply</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Integrated Tax Paid</th>
                <th>Cess Paid</th>
                <th>Eligibility For ITC</th>
                <th>Availed ITC Integrated Tax</th>
                <th>Availed ITC Cess</th>
            </tr>
        </thead><tbody>'.$tbody.'</tbody>';

        return ['status'=>1,'html'=>$html];
    }

    public function impg($data){
        $data['vou_name_s'] = "'Purc','GExp'";
        $result=$this->gstReport->_impg($data);
        $html = '';

        $no_of_recipients = $no_of_invoice = $total_invoice_value = $total_taxable_value = 0;
        $total_igst_amount = $total_cgst_amount = $total_sgst_amount = $total_cess = 0;
        $total_aigst_amount = $total_acgst_amount = $total_asgst_amount = $total_acess = 0;

        $html = '';
        $transMainId = array(); $tbody = '';
        foreach($result as $row):

            $tbody .= '<tr>
                <td class="text-left">'.$row->port_code.'</td>
                <td class="text-left">'.$row->trans_number.'</td>
                <td class="text-left">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                <td class="text-left">'.(($row->tax_class == "IMPORTACC")?"Imports":"Received from SEZ").'</td>
                <td class="text-left">'.$row->gstin.'</td>
                <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->igst_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                <td class="text-left">'.$row->itc.'</td>
                <td class="text-right">0.00</td>
                <td class="text-right">0.00</td>
            </tr>';

            if(!in_array($row->id,$transMainId)):
                $transMainId[] = $row->id;
                ++$no_of_recipients;
                ++$no_of_invoice;
                $total_invoice_value += $row->net_amount;
            endif;

            $total_taxable_value += $row->taxable_amount;
            $total_igst_amount += $row->igst_amount;
            $total_cess += $row->cess_amount;
        endforeach;

        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th colspan="13">Summary For IMPG(5)</th>
            </tr>
            <tr>
                <th></th>
                <th></th>                
                <th></th>
                <th>Total Bill Of Entry Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
				<th>Total Integrated Tax Paid</th>
                <th>Total Cess</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$total_invoice_value.'</td>
                <td></td>
                <td></td>
                <td>'.$total_taxable_value.'</td>
                <td>'.$total_igst_amount.'</td>
                <td>'.$total_cess.'</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>Port Code</th>
                <th>Bill Of Entry Number</th>
                <th>Bill Of Entry Date</th>
                <th>Bill Of Entry Value</th>
                <th>Document type</th>
                <th>GSTIN Of SEZ Supplier</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Integrated Tax Paid</th>
                <th>Cess Paid</th>
                <th>Eligibility For ITC</th>
                <th>Availed ITC Integrated Tax</th>
                <th>Availed ITC Cess</th>
            </tr>
        </thead><tbody>'.$tbody.'</tbody>';
        return ['status'=>1,'html'=>$html];
    }

    public function itcr($data){
        $html = '';
        $html .= '<thead class="thead-dark text-center">
            <tr>
                <th colspan="6">Summary For Input Tax credit Reversal/Reclaim(11)</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th>Total ITC Integrated Tax Amount</th>
                <th>Total ITC Central Tax Amount</th>
                <th>Total ITC State/UT Tax Amount</th>
                <th>Total ITC Cess Amount</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <th>Description for reversal of ITC</th>
                <th>To be added or reduced from output liability</th>
                <th>ITC Integrated Tax Amount</th>
                <th>ITC Central Tax Amount</th>
                <th>ITC State/UT Tax Amount</th>
                <th>ITC Cess Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Amount in terms of Rule 2(2) of ITC Rule</td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>Amount in terms of rule 7 (1) (m)of ITC</td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>Amount in terms of rule 8(1) (h) of the</td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>Amount in terms of rule 7 (2)(a) of ITC</td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>Amount in terms of rule 7(2)(b) of ITC R</td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>On account of amount paid subsequent to</td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>Any other liability</td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
        </tbody>';
        return ['status'=>1,'html'=>$html];
    }

    public function gstr2bMatch(){
        $this->data['headData']->pageTitle = "GSTR 2B Match";
        $this->data['pageHeader'] = 'GSTR 2B Match';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->gstr2b_match, $this->data);
    }

    public function matchGstr2bData(){
        $data = $this->input->post();
        if(empty($data['from_date']))
            $errorMessage["from_date"] = "From date is required.";
        if(empty($data['to_date']))
            $errorMessage['to_date'] = "To Date is required.";
        if(!empty($data['from_date']) && !empty($data['to_date'])):
            if($data['from_date'] > $data['to_date']):
                $errorMessage['from_date'] = "From date invalid.";
            endif;
        endif;

        if(empty($_FILES['json_file']['tmp_name']) || $_FILES['json_file']['tmp_name'] == null):
            $errorMessage['json_file'] = "Please select GSTR 2 json file.";
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $filePath = $_FILES['json_file']['tmp_name'];

            // Read the JSON file
            $jsonData = file_get_contents($filePath);

            // Parse the JSON data
            $gstr2Data = json_decode($jsonData, false);
            

            $data['report'] = "gstr2"; $tbody = '';$html = "";
            if($gstr2Data !== null):
                foreach($this->gstr2ReportTypes as $key=>$val):
                    if(in_array($key,['b2b','cdnr'])):

                        if(in_array($key,['b2b','imps','impg'])):
                            $data['vou_name_s'] = "'Purc','GExp'";
                        elseif(in_array($key,['cdnr'])):
                            $data['vou_name_s'] = "'C.N.','D.N.'";
                        endif;

                        $result = [];
                        $result = $this->gstReport->{"_".$key}($data);
                        $resultData = [];
                        foreach($result as $row):
                            $resultRow = [
                                'type' => 'ERP',
                                'unqId' => "",
                                'status' => "Unmatched",
                                'colorCode' => "#fdbec4",

                                'trdnm' => $row->party_name,
                                'ctin' => $row->gstin,

                                'dt' => formatDate($row->trans_date),
                                'val' => floatval($row->net_amount),
                                'rev' => ((in_array($row->tax_class,["PURURDGSTACC","PURURDIGSTACC"]))?"Y":"N"),
                                'itcavl' => ($row->itc == "Ineligible")?"N":"Y",
                                'diffprcnt' => 1,
                                'pos' => $row->party_state_code,
                                'typ' => "R",
                                'inum' => $row->trans_number,
                                'rsn' => "",

                                'txval' => floatval($row->taxable_amount),
                                'rt' => floatval($row->gst_per),
                                'num' => 0,
                                'cgst' => floatval($row->cgst_amount),
                                'sgst' => floatval($row->sgst_amount),
                                'igst' => floatval($row->igst_amount),
                                'cess' => floatval($row->cess_amount)
                            ];

                            $resultData[] = (array) $resultRow;
                        endforeach;

                        $jsonData = [];
                        $jsonData = (isset($gstr2Data->data->docdata->{$key}))?$gstr2Data->data->docdata->{$key}:array();

                        $jsonDocData = array();
                        foreach($jsonData as $row):
                            $jsonDataKey = (in_array($key,['b2b','imps','impg']))?"inv":'nt';
                            $invAmountKey = (in_array($key,['b2b','imps','impg']))?"inum":'ntnum';
                            foreach($row->{$jsonDataKey} as $inv):
                                foreach($inv->items as $item):
                                    $jsonRow = [
                                        'type' => 'JSON',
                                        'unqId' => "",
                                        'status' => "Unmatched",
                                        'colorCode' => "#fdbec4",

                                        'trdnm' => $row->trdnm,
                                        'ctin' => $row->ctin,

                                        'dt' => formatDate($inv->dt),
                                        'val' => floatval($inv->val),
                                        'rev' => $inv->rev,
                                        'itcavl' => $inv->itcavl,
                                        'diffprcnt' => $inv->diffprcnt,
                                        'pos' => $inv->pos,
                                        'typ' => $inv->typ,
                                        'inum' => $inv->{$invAmountKey},
                                        'rsn' => $inv->rsn,

                                        'txval' => (isset($item->txval))?floatval($item->txval):0,
                                        'rt' => (isset($item->rt))?floatval($item->rt):0,
                                        'num' => (isset($item->num))?$item->num:0,
                                        'cgst' => (isset($item->cgst))?floatval($item->cgst):0,
                                        'sgst' => (isset($item->sgst))?floatval($item->sgst):0,
                                        'igst' => (isset($item->igst))?floatval($item->igst):0,
                                        'cess' => (isset($item->cess))?floatval($item->cess):0
                                    ];

                                    $jsonDocData[] = (array) $jsonRow;
                                endforeach;
                            endforeach;
                        endforeach;
                        $jsonData = $jsonDocData;
                        
                        foreach($resultData as &$row):
                            foreach($jsonData as &$json):
                                if(
                                    trim($row['ctin']) == trim($json['ctin']) &&
                                    trim($row['inum']) == trim($json['inum']) &&
                                    formatDate($row['dt']) == formatDate($json['dt']) && 
                                    floatVal($row['rt']) == floatVal($json['rt'])
                                ):
                                    if (                                          
                                        round(floatVal($row['val'])) == round(floatVal($json['val'])) &&
                                        round(floatVal($row['txval'])) == round(floatVal($json['txval'])) &&
                                        floatVal($row['rt']) == floatVal($json['rt']) &&
                                        round(floatVal($row['cgst'])) == round(floatVal($json['cgst'])) &&
                                        round(floatVal($row['sgst'])) == round(floatVal($json['sgst'])) &&
                                        round(floatVal($row['igst'])) == round(floatVal($json['igst'])) &&
                                        round(floatVal($row['cess'])) == round(floatVal($json['cess']))
                                    ):
                                        $row['status'] = $json['status'] = "Matched";
                                        $row['colorCode'] = $json['colorCode'] = "#cdffcd";
                                    else:
                                        $row['status'] = $json['status'] = "Unmatched";
                                        $row['colorCode'] = $json['colorCode'] = "#fdbec4";
                                    endif;

                                    $unqId = hexdec(uniqid());
                                    $row['unqId'] = $unqId;
                                    $json['unqId'] = $unqId;
                                    break;
                                endif;
                            endforeach;
                        endforeach;                        

                        foreach($jsonData as &$json):
                            if(empty($json['unqId'])):
                                foreach($resultData as &$row):
                                    if(
                                        trim($row['ctin']) == trim($json['ctin']) &&
                                        trim($row['inum']) == trim($json['inum']) &&
                                        formatDate($row['dt']) == formatDate($json['dt']) && 
                                        floatVal($row['rt']) == floatVal($json['rt'])
                                    ):
                                        if (                                          
                                            round(floatVal($row['val'])) == round(floatVal($json['val'])) &&
                                            round(floatVal($row['txval'])) == round(floatVal($json['txval'])) &&
                                            floatVal($row['rt']) == floatVal($json['rt']) &&
                                            round(floatVal($row['cgst'])) == round(floatVal($json['cgst'])) &&
                                            round(floatVal($row['sgst'])) == round(floatVal($json['sgst'])) &&
                                            round(floatVal($row['igst'])) == round(floatVal($json['igst'])) &&
                                            round(floatVal($row['cess'])) == round(floatVal($json['cess']))
                                        ):
                                            $row['status'] = $json['status'] = "Matched";
                                            $row['colorCode'] = $json['colorCode'] = "#cdffcd";
                                        else:
                                            $row['status'] = $json['status'] = "Unmatched";
                                            $row['colorCode'] = $json['colorCode'] = "#fdbec4";
                                        endif;

                                        $unqId = hexdec(uniqid());
                                        $row['unqId'] = $unqId;
                                        $json['unqId'] = $unqId;
                                        break;
                                    endif;
                                endforeach;
                            endif;
                        endforeach;
                        
                        foreach($resultData as &$row):
                            if(empty($row['unqId'])):
                                $unqId = hexdec(uniqid());
                                $row['unqId'] = $unqId;
                                $row['status'] = "NOT FOUND IN JSON";

                                $jsonData[] = [
                                    'type' => 'JSON',
                                    'unqId' => $unqId,
                                    'status' => "NOT FOUND IN JSON",
                                    'colorCode' => "#fdbec4",

                                    'trdnm' => "",
                                    'ctin' => "",

                                    'dt' => $row['dt'],
                                    'val' => "",
                                    'rev' => "",
                                    'itcavl' => "",
                                    'diffprcnt' => "",
                                    'pos' => "",
                                    'typ' => "",
                                    'inum' => "",
                                    'rsn' => "",

                                    'txval' => "",
                                    'rt' => "",
                                    'num' => "",
                                    'cgst' => "",
                                    'sgst' => "",
                                    'igst' => "",
                                    'cess' => ""
                                ];                                
                            endif;
                        endforeach;
                        
                        foreach($jsonData as &$row):
                            if(empty($row['unqId'])):
                                $unqId = hexdec(uniqid());
                                $row['unqId'] = $unqId;
                                $row['status'] = "NOT FOUND IN ERP";

                                $resultData[] = [
                                    'type' => 'ERP',
                                    'unqId' => $unqId,
                                    'status' => "NOT FOUND IN ERP",
                                    'colorCode' => "#fdbec4",

                                    'trdnm' => "",
                                    'ctin' => "",

                                    'dt' => $row['dt'],
                                    'val' => "",
                                    'rev' => "",
                                    'itcavl' => "",
                                    'diffprcnt' => "",
                                    'pos' => "",
                                    'typ' => "",
                                    'inum' => "",
                                    'rsn' => "",

                                    'txval' => "",
                                    'rt' => "",
                                    'num' => "",
                                    'cgst' => "",
                                    'sgst' => "",
                                    'igst' => "",
                                    'cess' => ""
                                ];
                            endif;
                        endforeach;

                        /* if(in_array($key,['cdnr'])):
                            print_r($resultData);print_r("***");
                        endif; */
                        /* print_r(count($resultData));print_r("***");
                        print_r(count($jsonData));exit; */  
                        
                        array_multisort(array_column($resultData, 'dt'), SORT_ASC, $resultData);

                        $newResultData = [];
                        foreach($resultData as $res): if($res['status'] == "NOT FOUND IN ERP"): $res['dt'] = ""; endif; $newResultData[$res['unqId']] = $res; endforeach;                        
                        $resultData = $newResultData;

                        $newJsonData = [];
                        foreach($jsonData as $jd): if($jd['status'] == "NOT FOUND IN JSON"): $jd['dt'] = ""; endif; $newJsonData[$jd['unqId']] = $jd; endforeach;
                        $jsonData = $newJsonData;
                        
                        /* if(in_array($key,['cdnr'])):
                            print_r($resultData);exit;
                        endif; */
                        /* print_r(count($resultData));print_r("***");
                        print_r(count($jsonData));exit; */
                        
                        $balnkRow = '<tr style="background-color: none; ">
                            <td style="height:20px;"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>';

                        $tbodyHtml = "";
                        foreach($resultData as $rowKey=>$resRow):
                            $tbodyHtml = '';
                            $json = (isset($jsonData[$rowKey]))?$jsonData[$rowKey]:"";
                            
                            $tbodyHtml .= '<tr style="background-color: '.$resRow['colorCode'].'; ">
                                <td>'.$resRow['status'].'</td>
                                <td>'.strtoupper($key).'</td>
                                <td>'.$resRow['ctin'].'</td>
                                <td>'.$resRow['trdnm'].'</td>
                                <td>'.$resRow['inum'].'</td>
                                <td>'.$resRow['dt'].'</td>
                                <td>'.$resRow['val'].'</td>
                                <td>'.$resRow['rev'].'</td>
                                <td>'.$resRow['pos'].'</td>
                                <td>'.$resRow['typ'].'</td>
                                <td>'.$resRow['txval'].'</td>
                                <td>'.$resRow['rt'].'</td>
                                <td>'.$resRow['cgst'].'</td>
                                <td>'.$resRow['sgst'].'</td>
                                <td>'.$resRow['igst'].'</td>
                                <td>'.$resRow['cess'].'</td>
                            </tr>';

                            if(!empty($json)):
                                $tbodyHtml .= '<tr style="background-color: '.$resRow['colorCode'].'; ">
                                    <td>'.$json['status'].'</td>
                                    <td>'.strtoupper($key).'</td>
                                    <td>'.$json['ctin'].'</td>
                                    <td>'.$json['trdnm'].'</td>
                                    <td>'.$json['inum'].'</td>
                                    <td>'.$json['dt'].'</td>
                                    <td>'.$json['val'].'</td>
                                    <td>'.$json['rev'].'</td>
                                    <td>'.$json['pos'].'</td>
                                    <td>'.$json['typ'].'</td>
                                    <td>'.$json['txval'].'</td>
                                    <td>'.$json['rt'].'</td>
                                    <td>'.$json['cgst'].'</td>
                                    <td>'.$json['sgst'].'</td>
                                    <td>'.$json['igst'].'</td>
                                    <td>'.$json['cess'].'</td>
                                </tr>';
                            else:
                                $tbodyHtml .= $balnkRow;
                            endif;

                            $tbodyHtml .= $balnkRow;

                            if($data['match_status'] == 1 && in_array($resRow['status'],["Unmatched","NOT FOUND IN ERP","NOT FOUND IN JSON"])):
                                $tbodyHtml = '';
                                $tbody .= $tbodyHtml;
                            elseif($data['match_status'] == 2 && in_array($resRow['status'],["Matched","NOT FOUND IN ERP","NOT FOUND IN JSON"])):
                                $tbodyHtml = '';
                                $tbody .= $tbodyHtml;
                            elseif($data['match_status'] == 3 && in_array($resRow['status'],["Matched","Unmatched","NOT FOUND IN JSON"])):
                                $tbodyHtml = '';
                                $tbody .= $tbodyHtml;
                            elseif($data['match_status'] == 4 && in_array($resRow['status'],["Matched","Unmatched","NOT FOUND IN ERP"])):
                                $tbodyHtml = '';
                                $tbody .= $tbodyHtml;
                            else:
                                $tbody .= $tbodyHtml;
                            endif;
                        endforeach;
                    endif;
                endforeach; 
                
                //print_r($tbody);
                //exit;

                $tableData = '<thead class="thead-dark">
                    <tr>
                        <th>Status</th>
                        <th>Section</th>
                        <th>GSTIN</th>
                        <th>Party Name</th>
                        <th>INV. No.</th>
                        <th>INV Date</th>
                        <th>Invoice Value</th>
                        <th>Revarse Charge</th>
                        <th>POS</th>
                        <th>Invoice Type</th>
                        <th>Taxable Value</th>
                        <th>Rate</th>
                        <th>CGST</th>
                        <th>SGST</th>
                        <th>IGST</th>
                        <th>CESS</th>
                    </tr>
                </thead><tbody>'.$tbody.'</tbody>';

                $this->printJson(['status'=>1,'tableData'=>$tableData]);
            else:
                $this->printJson(['status'=>0,'message'=>["json_file"=>"Failed to parse JSON data."]]);
            endif;
        endif;
    }

    public function gstr3b(){
        $this->data['headData']->pageTitle = "GSTR 3B";
        $this->data['pageHeader'] = 'GSTR 3B';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->gstr3b_report, $this->data);
    }

    public function getGstr3bSummary($jsonData=""){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else:
            $data = $this->input->post();
        endif; 

        $data['customCondition'] = "((trans_main.vou_name_s IN ('C.N.','D.N.') AND trans_main.gstin = 'URP' AND trans_main.order_type IN ('Increase Sales','Decrease Sales','Sales Return')) OR trans_main.vou_name_s IN ('Sale','GInc'))";
        $data['vou_name_s'] = ["'Sale'","'GInc'","'D.N.'","'C.N.'"];
        $data['not_tax_class'] = ["'SALESTFACC'","'SALESCTFACC'","'SALESEXEMPTEDTFACC'","'SALESCEXEMPTEDTFACC'","'SALESNONGST'","'SALESCNONGST'"];
        $outwardTaxableSupply = $this->gstReport->_taxableSummary($data);

        $data['registered'] = 1;
        $data['vou_name_s'] = ["'D.N.'","'C.N.'"];
        $data['order_type'] = ["'Increase Sales'","'Decrease Sales'","'Sales Return'"]; unset($data['not_tax_class'],$data['customCondition']);
        $cndnRegistered = $this->gstReport->_taxableSummary($data);

        $data['registered'] = 0;
        $data['vou_name_s'] = ["'Sale'","'GInc'"];
        $data['tax_class'] = ["'SALESTFACC'","'SALESCTFACC'","'SALESEXEMPTEDTFACC'","'SALESCEXEMPTEDTFACC'"]; unset($data['order_type']);
        $nillExemptedSupply = $this->gstReport->_taxableSummary($data);

        $data['vou_name_s'] = ["'Purc'","'GExp'"];
        $data['tax_class'] = ["'PURURDGSTACC'","'PURURDIGSTACC'"]; unset($data['registered']);
        $inwardSupplyRCM = $this->gstReport->_taxableSummary($data);

        $data['vou_name_s'] = ["'Sale'","'GInc'","'D.N.'","'C.N.'"]; unset($data['tax_class']);
        $stateWiseSummary = $this->gstReport->_stateWiseURSummary($data);

        $data['registered'] = 1;
        $data['vou_name_s'] = ["'Purc'","'GExp'"];
        $data['not_tax_class'] = ["'PURURDGSTACC'","'PURURDIGSTACC'","'PURTFACC'","'PUREXEMPTEDTFACC'","'PURNONGST'","'PURCTFACC'","'PURCEXEMPTEDTFACC'","'PURCNONGST'"];
        $data['itc'] = ["'Inputs'","'Capital Goods'","'Input Services'","'Ineligible'"];
        $allOtherItc = $this->gstReport->_taxableSummary($data);

        $data['vou_name_s'] = ["'D.N.'","'C.N.'"];
        $data['order_type'] = ["'Increase Purchase','Decrease Purchase','Purchase Return'"]; unset($data['not_tax_class'],$data['itc']);
        $cndnPurcRegistered = $this->gstReport->_taxableSummary($data);

        $data['vou_name_s'] = ["'Purc'","'GExp'"];
        $data['not_tax_class'] = ["'PURURDGSTACC'","'PURURDIGSTACC'"];
        $data['itc'] = ["'Ineligible'"]; unset($data['order_type']);
        $others = $this->gstReport->_taxableSummary($data);

        //$data['registered'] = 0;
        $data['vou_name_s'] = ["'Purc'","'GExp'"];
        $data['tax_class'] = ["'PURTFACC'","'PUREXEMPTEDTFACC'","'PURNONGST'"]; unset($data['itc'],$data['not_tax_class'],$data['registered']);
        $nillExemptedInwardSupply = $this->gstReport->_taxableSummary($data);

        
       //$data['registered'] = 0;
        $data['vou_name_s'] = ["'Purc'","'GExp'"];
        $data['tax_class'] = ["'PURCTFACC'","'PURCEXEMPTEDTFACC'","'PURCNONGST'"]; unset($data['itc'],$data['not_tax_class']);
        $nillExemptedInwardInterSupply = $this->gstReport->_taxableSummary($data);

        $html = '<tbody>';
            /* Sales Summary Start */
            $html .= '<tr class="bg-grey">
                <th style="width:30%;">Particulars</th>
                <th style="width:15%;">Place of Supply</th>
                <th style="width:14%;" class="text-right">Taxable Value</th>
                <th style="width:11%;" class="text-right">IGST</th>
                <th style="width:11%;" class="text-right">CGST</th>
                <th style="width:11%;" class="text-right">SGST / UTGST</th>
                <th style="width:8%;" class="text-right">Cess</th>
            </tr>';

            $html .= '<tr>
                <td><b> 3.1 Detail of Outward Supplies and Inward Supplies Liable to reverse charge </b></td>
                <td colspan="6"></td>
            </tr>';

            $html .= '<tr>
                <td>(a) Outward Taxable Supply</td>
                <td></td>
                <td class="text-right">'.((!empty(abs($outwardTaxableSupply->taxable_amount)))?($outwardTaxableSupply->taxable_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($outwardTaxableSupply->igst_amount)))?($outwardTaxableSupply->igst_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($outwardTaxableSupply->cgst_amount)))?($outwardTaxableSupply->cgst_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($outwardTaxableSupply->sgst_amount)))?($outwardTaxableSupply->sgst_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($outwardTaxableSupply->cess_amount)))?($outwardTaxableSupply->cess_amount * -1):"").'</td>
            </tr>';

            $html .= '<tr>
                <td>Credit/Debit Note - Sales - Registered</td>
                <td></td>
                <td class="text-right">'.((!empty(abs($cndnRegistered->taxable_amount)))?($cndnRegistered->taxable_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($cndnRegistered->igst_amount)))?($cndnRegistered->igst_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($cndnRegistered->cgst_amount)))?($cndnRegistered->cgst_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($cndnRegistered->sgst_amount)))?($cndnRegistered->sgst_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($cndnRegistered->cess_amount)))?($cndnRegistered->cess_amount * -1):"").'</td>
            </tr>';

            $html .= '<tr>
                <td>(c) Nill/Exempted Supply (Other Then Export)</td>
                <td></td>
                <td class="text-right">'.((!empty(abs($nillExemptedSupply->taxable_amount)))?($nillExemptedSupply->taxable_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($nillExemptedSupply->igst_amount)))?($nillExemptedSupply->igst_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($nillExemptedSupply->cgst_amount)))?($nillExemptedSupply->cgst_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($nillExemptedSupply->sgst_amount)))?($nillExemptedSupply->sgst_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($nillExemptedSupply->cess_amount)))?($nillExemptedSupply->cess_amount * -1):"").'</td>
            </tr>';

            $html .= '<tr>
                <td>(d) Inward Supplies (Liable to Reverse Charge)</td>
                <td></td>
                <td class="text-right">'.((!empty(abs($inwardSupplyRCM->taxable_amount)))?($inwardSupplyRCM->taxable_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($inwardSupplyRCM->igst_amount)))?($inwardSupplyRCM->igst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($inwardSupplyRCM->cgst_amount)))?($inwardSupplyRCM->cgst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($inwardSupplyRCM->sgst_amount)))?($inwardSupplyRCM->sgst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($inwardSupplyRCM->cess_amount)))?($inwardSupplyRCM->cess_amount):"").'</td>
            </tr>';

            $html .= '<tr>
                <td><b> 3.2 of the supply shown in 3.1 (a), Detail of inter-state supply made to unregistered person, composition taxable persion and Consumer. </b></td>
                <td colspan="6"></td>
            </tr>';

            foreach($stateWiseSummary as $row):
                $html .= '<tr>
                    <td>Inter State Supply To Unregistered Person</td>
                    <td>'.$row->party_state_code.' - '.$row->state_name.'</td>
                    <td class="text-right">'.((!empty(abs($row->taxable_amount)))?($row->taxable_amount * -1):"").'</td>
                    <td class="text-right">'.((!empty(abs($row->igst_amount)))?($row->igst_amount * -1):"").'</td>
                    <td class="text-right">'.((!empty(abs($row->cgst_amount)))?($row->cgst_amount * -1):"").'</td>
                    <td class="text-right">'.((!empty(abs($row->sgst_amount)))?($row->sgst_amount * -1):"").'</td>
                    <td class="text-right">'.((!empty(abs($row->cess_amount)))?($row->cess_amount * -1):"").'</td>
                </tr>';
            endforeach;

            $totalOutwardTaxableValue = ($outwardTaxableSupply->taxable_amount * -1) + ($cndnRegistered->taxable_amount * -1) + ($nillExemptedSupply->taxable_amount * -1) + ($inwardSupplyRCM->taxable_amount);

            $totalOutwardIgstValue = ($outwardTaxableSupply->igst_amount * -1) + ($cndnRegistered->igst_amount * -1) + ($nillExemptedSupply->igst_amount * -1) + ($inwardSupplyRCM->igst_amount);

            $totalOutwardCgstValue = ($outwardTaxableSupply->cgst_amount * -1) + ($cndnRegistered->cgst_amount * -1) + ($nillExemptedSupply->cgst_amount * -1) + ($inwardSupplyRCM->cgst_amount);

            $totalOutwardSgstValue = ($outwardTaxableSupply->sgst_amount * -1) + ($cndnRegistered->sgst_amount * -1) + ($nillExemptedSupply->sgst_amount * -1) + ($inwardSupplyRCM->sgst_amount);

            $totalOutwardCessValue = ($outwardTaxableSupply->cess_amount * -1) + ($cndnRegistered->cess_amount * -1) + ($nillExemptedSupply->cess_amount * -1) + ($inwardSupplyRCM->cess_amount);

            $html .= '<tr class="bg-grey">
                <th>Total</th>
                <th></th>
                <th class="text-right">'.$totalOutwardTaxableValue.'</th>
                <th class="text-right">'.$totalOutwardIgstValue.'</th>
                <th class="text-right">'.$totalOutwardCgstValue.'</th>
                <th class="text-right">'.$totalOutwardSgstValue.'</th>
                <th class="text-right">'.$totalOutwardCessValue.'</th>
            </tr>';
            /* Sales Summary End */

            $html .= '<tr><td colspan="7" style="height:20px;"></td></tr>';

            /* Purchase Summary Start */
            $html .= '<tr class="bg-grey">
                <th style="width:30%;">Particulars</th>
                <th style="width:15%;">Place of Supply</th>
                <th style="width:14%;" class="text-right">Taxable Value</th>
                <th style="width:11%;" class="text-right">IGST</th>
                <th style="width:11%;" class="text-right">CGST</th>
                <th style="width:11%;" class="text-right">SGST / UTGST</th>
                <th style="width:8%;" class="text-right">Cess</th>
            </tr>';

            $html .= '<tr>
                <td><b> 4. Eligible ITC </b></td>
                <td colspan="6"></td>
            </tr>';

            $html .= '<tr>
                <td>(3) Inward Supplies (Liable to Reverse Charge)</td>
                <td></td>
                <td class="text-right">'.((!empty(abs($inwardSupplyRCM->taxable_amount)))?($inwardSupplyRCM->taxable_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($inwardSupplyRCM->igst_amount)))?($inwardSupplyRCM->igst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($inwardSupplyRCM->cgst_amount)))?($inwardSupplyRCM->cgst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($inwardSupplyRCM->sgst_amount)))?($inwardSupplyRCM->sgst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($inwardSupplyRCM->cess_amount)))?($inwardSupplyRCM->cess_amount):"").'</td>
            </tr>';

            $html .= '<tr>
                <td>(5) All Other ITC</td>
                <td></td>
                <td class="text-right">'.((!empty(abs($allOtherItc->taxable_amount)))?($allOtherItc->taxable_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($allOtherItc->igst_amount)))?($allOtherItc->igst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($allOtherItc->cgst_amount)))?($allOtherItc->cgst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($allOtherItc->sgst_amount)))?($allOtherItc->sgst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($allOtherItc->cess_amount)))?($allOtherItc->cess_amount):"").'</td>
            </tr>';

            $html .= '<tr>
                <td>Credit/Debit Note - Purchase - Registered</td>
                <td></td>
                <td class="text-right">'.((!empty(abs($cndnPurcRegistered->taxable_amount)))?$cndnPurcRegistered->taxable_amount:"").'</td>
                <td class="text-right">'.((!empty(abs($cndnPurcRegistered->igst_amount)))?$cndnPurcRegistered->igst_amount:"").'</td>
                <td class="text-right">'.((!empty(abs($cndnPurcRegistered->cgst_amount)))?$cndnPurcRegistered->cgst_amount:"").'</td>
                <td class="text-right">'.((!empty(abs($cndnPurcRegistered->sgst_amount)))?$cndnPurcRegistered->sgst_amount:"").'</td>
                <td class="text-right">'.((!empty(abs($cndnPurcRegistered->cess_amount)))?$cndnPurcRegistered->cess_amount:"").'</td>
            </tr>';

            $html .= '<tr>
                <td><b> (1) As Per Rule 42&43<br>(2) Others </b></td>
                <td colspan="6"></td>
            </tr>';
            
            $html .= '<tr>
                <td>(2) Others.</td>
                <td></td>
                <td class="text-right">'.((!empty(abs($others->taxable_amount)))?($others->taxable_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($others->igst_amount)))?($others->igst_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($others->cgst_amount)))?($others->cgst_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($others->sgst_amount)))?($others->sgst_amount * -1):"").'</td>
                <td class="text-right">'.((!empty(abs($others->cess_amount)))?($others->cess_amount * -1):"").'</td>
            </tr>';

            $html .= '<tr>
                <td><b> (5) Value of Exempt, Nill-Rateed and Non-GST inward supplies </b></td>
                <td colspan="6"></td>
            </tr>';

            $html .= '<tr>
                <td>Exempt and Nil Rated Supply Intra-State</td>
                <td></td>
                <td class="text-right">'.((!empty(abs($nillExemptedInwardSupply->taxable_amount)))?($nillExemptedInwardSupply->taxable_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($nillExemptedInwardSupply->igst_amount)))?($nillExemptedInwardSupply->igst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($nillExemptedInwardSupply->cgst_amount)))?($nillExemptedInwardSupply->cgst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($nillExemptedInwardSupply->sgst_amount)))?($nillExemptedInwardSupply->sgst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($nillExemptedInwardSupply->cess_amount)))?($nillExemptedInwardSupply->cess_amount):"").'</td>
            </tr>';

            $html .= '<tr>
                <td>Exempt and Nil Rated Supply Inter-State</td>
                <td></td>
                <td class="text-right">'.((!empty(abs($nillExemptedInwardInterSupply->taxable_amount)))?($nillExemptedInwardInterSupply->taxable_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($nillExemptedInwardInterSupply->igst_amount)))?($nillExemptedInwardInterSupply->igst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($nillExemptedInwardInterSupply->cgst_amount)))?($nillExemptedInwardInterSupply->cgst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($nillExemptedInwardInterSupply->sgst_amount)))?($nillExemptedInwardInterSupply->sgst_amount):"").'</td>
                <td class="text-right">'.((!empty(abs($nillExemptedInwardInterSupply->cess_amount)))?($nillExemptedInwardInterSupply->cess_amount):"").'</td>
            </tr>';

            $totalInwardTaxableValue = ($inwardSupplyRCM->taxable_amount) + ($allOtherItc->taxable_amount) + ($cndnPurcRegistered->taxable_amount) + ($others->taxable_amount * -1) + ($nillExemptedInwardSupply->taxable_amount)  + ($nillExemptedInwardInterSupply->taxable_amount);

            $totalInwardIgstValue = ($inwardSupplyRCM->igst_amount) + ($allOtherItc->igst_amount) + ($cndnPurcRegistered->igst_amount) + ($others->igst_amount * -1) + ($nillExemptedInwardSupply->igst_amount) + ($nillExemptedInwardInterSupply->igst_amount);

            $totalInwardCgstValue = ($inwardSupplyRCM->cgst_amount) + ($allOtherItc->cgst_amount) + ($cndnPurcRegistered->cgst_amount) + ($others->cgst_amount * -1) + ($nillExemptedInwardSupply->cgst_amount) + ($nillExemptedInwardInterSupply->cgst_amount);

            $totalInwardSgstValue = ($inwardSupplyRCM->sgst_amount) + ($allOtherItc->sgst_amount) + ($cndnPurcRegistered->sgst_amount) + ($others->sgst_amount * -1) + ($nillExemptedInwardSupply->sgst_amount) + ($nillExemptedInwardInterSupply->sgst_amount);

            $totalInwardCessValue = ($inwardSupplyRCM->cess_amount) + ($allOtherItc->cess_amount) + ($cndnPurcRegistered->cess_amount) + ($others->cess_amount * -1) + ($nillExemptedInwardSupply->cess_amount) + ($nillExemptedInwardInterSupply->cess_amount);

            $html .= '<tr class="bg-grey">
                <th>Total</th>
                <th></th>
                <th class="text-right">'.$totalInwardTaxableValue.'</th>
                <th class="text-right">'.$totalInwardIgstValue.'</th>
                <th class="text-right">'.$totalInwardCgstValue.'</th>
                <th class="text-right">'.$totalInwardSgstValue.'</th>
                <th class="text-right">'.$totalInwardCessValue.'</th>
            </tr>';
            /* Purchase Summary End */
        $html .= '</tbody>';

        if(!empty($data['pdf'])):
            $report_date = date('d-m-Y',strtotime($data['from_date'])).' to '.date('d-m-Y',strtotime($data['to_date']));

            $companyData = $this->masterModel->getCompanyInfo();
			$logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
			$logo = base_url('assets/images/' . $logoFile);

            $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                <tr>
                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;">'.$companyData->company_name.'</td>
                </tr>
            </table>
            <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                <tr>
                    <td class="org-address text-center" style="font-size:13px;" colspan="3">'.$companyData->company_address.'</td>
                </tr>
                <tr>
                    <td class="org-address text-center" style="font-size:13px;">GSTIN : '.$companyData->company_gst_no.'</td>
                    <td class="org-address text-center" style="font-size:13px;">State : '.$companyData->company_state_code.' - '.$companyData->company_state.'</td>
                    <td class="org-address text-center" style="font-size:13px;">PAN No. : '.$companyData->company_pan_no.'</td>
                </tr>
            </table>
            <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:10px;">
                <tr>
                    <td class="org_title text-uppercase text-left" style="font-size:1rem;width:50%">Sales / Purchase Summary</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:50%">Date : '.$report_date.'</td>
                </tr>
            </table>';  
			$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';

            $pdfData = '<table class="table table-borderless">
                '.$html.'
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
            $mpdf->AddPage('P','','','','',5,5,30,5,3,3,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);
            
            ob_clean();
            $mpdf->Output("GSTR3B.pdf", 'I');
        else:
            $this->printJson(['status'=>1,'html'=>$html]);
        endif;
    }
}
?>