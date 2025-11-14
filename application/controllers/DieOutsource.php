<?php
class DieOutsource extends MY_Controller
{
    private $indexPage = "die_outsource/index";
    private $challanIndex = "die_outsource/challan_index";
    private $formPage = "die_outsource/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Outsource";
		$this->data['headData']->controller = "dieOutsource";
		$this->data['headData']->pageUrl = "dieOutsource";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionDtHeader('dieOutsource');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->dieOutsource->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getDieOutsourceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function challanIndex($status = 1){
        $this->data['status'] = $status;
        $this->data['tableHeader'] = getProductionDtHeader('dieOutsourceChallan');
        $this->load->view($this->challanIndex,$this->data);
    }

    public function getChallanDTRows($status = 1){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->dieOutsource->getChallanDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getDieOutsourceChallanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addChallan(){
        $data = $this->input->post();
        $this->data['ch_prefix'] = 'DC'.n2y(date("Y")).n2m(date("m"));
        $this->data['ch_no'] = $this->dieOutsource->getNextChallanNo();
        $this->data['dieProdList'] = $this->dieProduction->getDieProductionData(['id'=>$data['ids'],'material_value'=>1]); 
        $this->data['vendorList'] = $this->party->getPartyList(['party_category'=>3]);
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['party_id'])){ $errorMessage['party_id'] = "Vendor is required.";}
        if(empty($data['dp_id'])){ $errorMessage['general_error'] = "Select Item ";}
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dieOutsource->save($data));
        endif;
    }

   
    public function delete(){
        $ch_number = $this->input->post('ch_number');
        if(empty($ch_number)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->dieOutsource->delete($ch_number));
        endif;
    }  
    
    public function dieOutSourcePrint($ch_number){
        $this->data['dieOutSourceData'] = $this->dieOutsource->getDieSourceData(['ch_number'=>$ch_number]);
        $this->data['companyData'] = $this->outsource->getCompanyInfo();	

        $logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
    
        $pdfData = $this->load->view('die_outsource/print', $this->data, true);        
		$mpdf = new \Mpdf\Mpdf();
        $pdfFileName='VC-'.$id.'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(120,45));
        $mpdf->showWatermarkImage = true;
		$mpdf->AddPage('P','','','','',10,5,5,15,5,5,'','','','','','','','','','A4-P');
		
        $mpdf->WriteHTML($pdfData);
		ob_clean();
		$mpdf->Output($pdfFileName, 'I');
		
    }

}
?>