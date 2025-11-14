<?php
class ProformaInvoice extends MY_Controller{
    private $indexPage = "proforma_invoice/index";
    private $form = "proforma_invoice/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Proforma Invoice";
		$this->data['headData']->controller = "proformaInvoice";        
        $this->data['headData']->pageUrl = "proformaInvoice";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'proformaInvoice','tableName'=>'pinv_master']);
	}

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader("proformaInvoice");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['trans_status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->proformaInvoice->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getProformaInvoiceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function createPINV($id){
        $dataRow = $this->salesOrder->getSalesOrder(['id'=>$id,'itemList'=>1,'is_approve'=>1]);        
        $dataRow->from_entry_type = $dataRow->entry_type;
        $dataRow->ref_id = $dataRow->id;
        $dataRow->entry_type = "";
        $dataRow->id = "";
        $dataRow->trans_prefix = "";
        $dataRow->trans_no = "";
        $dataRow->trans_number = "";

        $itemList = array();
        foreach($dataRow->itemList as $row):
            $row->from_entry_type = $row->entry_type;
            $row->ref_id = $row->id;
            $row->entry_type = "";
            $row->id = "";
            $row->unit_name = $row->uom;

            $row->taxable_amount = $row->amount = round(($row->qty * $row->price),2);
            if(!empty($row->disc_per) && !empty($row->amount)):
                $row->disc_amount = round((($row->disc_per * $row->amount) / 100),2);
                $row->taxable_amount = $row->taxable_amount - $row->disc_amount;
            endif;

            $row->net_amount = $row->taxable_amount;
            if(!empty($row->taxable_amount) && !empty($row->gst_per)):
                $row->gst_amount = round((($row->gst_per * $row->taxable_amount) / 100),2);

                $row->igst_per = $row->gst_per;
                $row->igst_amount = $row->gst_amount;

                $row->cgst_per = round(($row->gst_per / 2),2);
                $row->cgst_amount = round(($row->gst_amount / 2),2);
                $row->sgst_per = round(($row->gst_per / 2),2);
                $row->sgst_amount = round(($row->gst_amount / 2),2);

                $row->net_amount = $row->taxable_amount + $row->gst_amount;
            endif;
            $itemList[] = $row;
        endforeach;
        $dataRow->itemList = $itemList;
        
        $this->data['dataRow'] = $dataRow;
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category' => "1,2",'party_type'=>"0,1"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1,'active_item'=>"1,2"]);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Sales']);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();

        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['itemData']))
            $errorMessage['itemData'] = "Item Details is required.";

        if(empty($data['trans_date'])){
            $errorMessage['trans_date'] = "Date is required.";
        }else{
            if (($data['trans_date'] < $this->startYearDate) OR ($data['trans_date'] > $this->endYearDate)){
                $errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
            }
        }
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:            
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;
            $this->printJson($this->proformaInvoice->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->proformaInvoice->getProformaInvoice(['id'=>$id,'itemList'=>1]);
        $this->data['gstinList'] = $this->party->getPartyGSTDetail(['party_id' => $dataRow->party_id]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category' => "1,2"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Sales']);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->proformaInvoice->delete($id));
        endif;
    }

    public function printInvoice($jsonData=""){
		$data = (!empty($jsonData) ? (Array) decodeURL($jsonData) : []);

        $this->data['dataRow'] = $dataRow = $this->proformaInvoice->getProformaInvoice(['id'=>$data['id'],'itemList'=>1]);
        $this->data['partyData'] = $this->party->getParty(['id'=>$dataRow->party_id]);
        $this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
		$this->data['termsData'] = (!empty($dataRow->termsConditions) ? $dataRow->termsConditions: "");
        $this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		
        $logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
       
        if($data['pdf_type'] == "Domestic"){
            $pdfData = $this->load->view('proforma_invoice/print', $this->data, true);    
        }else{
            $pdfData = $this->load->view('proforma_invoice/print_inv', $this->data, true);     //International Print
        }

        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
            <tr>
                <td style="width:25%;">PINV. No. & Date : '.$dataRow->trans_number . ' [' . formatDate($dataRow->trans_date) . ']</td>
                <td style="width:25%;"></td>
                <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
            </tr>
        </table>';
                
		$mpdf = new \Mpdf\Mpdf();
		$filePath = realpath(APPPATH . '../assets/uploads/proforma_invoice/');
        $pdfFileName = $filePath.'/' . str_replace(["/","-"],"_",$dataRow->trans_number) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(120,45));
        $mpdf->showWatermarkImage = true;
        $mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,5,15,5,5,'','','','','','','','','','A4-P');		
        $mpdf->WriteHTML($pdfData);		
		ob_clean();
		$mpdf->Output($pdfFileName, 'I');
		
    }

    public function approveProformaInvoice(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->proformaInvoice->approveProformaInvoice($data));
        endif;
    }
}
?>