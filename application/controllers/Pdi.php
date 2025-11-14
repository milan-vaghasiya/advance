<?php 
require_once 'vendor/autoload.php';
use Xthiago\PDFVersionConverter\Guesser\RegexGuesser;
class Pdi extends MY_Controller
{
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "PDI";
		$this->data['headData']->controller = "pdi";
        $this->data['headData']->pageUrl = "pdi";
	}
	 
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader('pdi');
        $this->load->view('pdi/index',$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->pdi->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPdiReportData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPDI(){
        $data = $this->input->post();
        $this->data['invData'] =$invData= $this->salesInvoice->getSalesInvoiceItem(['id'=>$data['id'],'batchDetail'=>1]);
        $batchdetail = json_decode($invData->batch_detail);
        $this->data['batchData'] = $this->pdi->getPackingDetail(['batch_no'=>array_column($batchdetail,'batch_no')]);
        $this->data['pdiData'] = $this->pdi->getPdiReportData(['so_trans_id'=>$data['id']]);
		$this->load->view('pdi/form',$this->data);
	}

    public function save(){ 
		$data = $this->input->post(); 
        $errorMessage = Array(); 
        $i = 1;
		foreach($data['batch_no'] AS $key=>$batch_no){
            if(empty($data['qty'][$key])){
                $errorMessage['qty_'.$i] = "Qty is Required.";
            }

            if(empty($data['firdata'][$batch_no]['fir_id'])){
                $errorMessage['fir_'.$i] = "FIR Report is Required.";
            }
            $i++;
        }
        $invData= $this->salesInvoice->getSalesInvoiceItem(['id'=>$data['so_trans_id']]);
        if(floatval($invData->qty) != array_sum($data['qty'])){
            $errorMessage['general_error'] = "Qty does not match with invoice qty";
        }

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->pdi->save($data));
        endif;
	}

	public function printPdi($jsonData=""){
		if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else: 
            $data = $this->input->post();
        endif;
		
        $pdiData = $this->pdi->getPdiReportData(['so_trans_id'=>$data['so_trans_id']]);
       
		$pdfFileName = 'PDI-'.$data['so_trans_id'].'.pdf';
		
        $mpdf = new \Mpdf\Mpdf();
        $fileNumber= 1; $filesTotal = count($pdiData);
		
		foreach($pdiData as $row){
			$fir_file = $this->pdi->printPDIR(["id"=>$row->fir_id, "output_type"=>'F', "pdi_report_type"=>$data['pdi_type'], "party_logo"=>$data['party_logo']]);
			if (file_exists($fir_file)) {
				$pagesInFile = $mpdf->SetSourceFile($fir_file);
			   
				for ($i = 1; $i <= $pagesInFile; $i++) 
				{
					$tplId = $mpdf->ImportPage($i); 
					$size = $mpdf->getTemplateSize($tplId);
		
					$mpdf->UseTemplate($tplId, 0, 0, $size['width'], $size['height'], true);
					if (($fileNumber < $filesTotal) || ($i != $pagesInFile)) {$mpdf ->addPage();};
				}
			}
			$fileNumber++;
		}
        
        $mpdf->Output($pdfFileName, 'I');
    }
	
	/*
    public function printPdi($so_trans_id){
        $pdiData = $this->pdi->getPdiReportData(['so_trans_id'=>$so_trans_id]);
        $pdfFileName = 'PDI-'.$so_trans_id.'.pdf';
        $mpdf = new \Mpdf\Mpdf();
        $fileNumber= 1; $filesTotal = count($pdiData);
        foreach($pdiData as $row){
            $fir_file = $this->sop->printFinalInspection($row->fir_id,'F');
            
            if (file_exists($fir_file)) {
                $pagesInFile = $mpdf->SetSourceFile($fir_file);
               
                for ($i = 1; $i <= $pagesInFile; $i++) 
                {
                    $tplId = $mpdf->ImportPage($i); 
                    $size = $mpdf->getTemplateSize($tplId);
        
                    $mpdf->UseTemplate($tplId, 0, 0, $size['width'], $size['height'], true);
                    if (($fileNumber < $filesTotal) || ($i != $pagesInFile)) {$mpdf ->addPage();};
                    
                }
            }
            $fileNumber++;
        }
        $mpdf->Output($pdfFileName, 'I');
    }
	*/
	
	
}
?>