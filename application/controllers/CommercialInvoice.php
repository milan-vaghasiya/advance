<?php
class CommercialInvoice extends MY_Controller{
    private $index = "commercial_invoice/index";
    private $form = "commercial_invoice/form";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Commercial Invoice";
		$this->data['headData']->controller = "commercialInvoice";        
        $this->data['headData']->pageUrl = "commercialInvoice";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'commercialInvoice']);
    }

    public function index(){
        $this->data['tableHeader'] = getExportDtHeader("commercialInvoice");
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->commercialInvoice->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getCommercialInvoiceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function create($id){
        $dataRow = $this->packingList->getPackingList(['id'=>$id,'itemList'=>1]);
        $dataRow->from_entry_type = $dataRow->entry_type;
        $dataRow->ref_id = $dataRow->id;
        $dataRow->id = "";
        $dataRow->entry_type = "";
        $dataRow->trans_prefix = "";
        $dataRow->trans_no = "";

        foreach($dataRow->itemList as &$row): 
            $row->form_entry_type = $row->entry_type;  
            $row->ref_id = $row->id;
            $row->id = "";
            $row->entry_type = "";
        endforeach;        

        $this->data['dataRow'] = $dataRow;
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();

        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
        $this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->form,$this->data);
    }

    public function addCommercialInvoice(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();

        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
        $this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
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
            $this->printJson($this->commercialInvoice->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->commercialInvoice->getCommercialInvoice(['id'=>$id,'itemList'=>1]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category' => 1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();

        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
        $this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->commercialInvoice->delete($id));
        endif;
    }

    public function printCommercialInvoice($id){
        $this->data['dataRow'] = $dataRow = $this->commercialInvoice->getCommercialInvoice(['id'=>$id,'itemList'=>1]);
        $this->data['partyData'] = $this->party->getParty(['id'=>$dataRow->party_id]);
        $this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);

        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
            <tr>
                <td style="width:25%;"></td>
                <td style="width:25%;"></td>
                <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
            </tr>
        </table>';
       
        $pdfData = $this->load->view('commercial_invoice/print', $this->data, true);        
        //print_r($pdfData);exit;
        $mpdf = new \Mpdf\Mpdf();
		$filePath = realpath(APPPATH . '../assets/uploads/commercial_invoice/');
        $pdfFileName = str_replace(["/","-"],"_",$dataRow->trans_number) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));

        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->showWatermarkImage = true;
		$mpdf->SetWatermarkImage($logo,0.05,array(120,45));
        $mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,5,15,5,5,'','','','','','','','','','A4-P');		
        $mpdf->WriteHTML($pdfData);		
		
        ob_clean();
		$mpdf->Output($pdfFileName, 'I');
    }
}
?>