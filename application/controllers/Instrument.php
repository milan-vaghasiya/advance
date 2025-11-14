<?php
class Instrument extends MY_Controller
{
    private $indexPage = "instrument/index";
    private $formPage = "instrument/form";
    private $indexUsed = "instrument/index_used";
    private $requestForm = "purchase_request/purchase_request";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Gauges & Instruments";
		$this->data['headData']->controller = "instrument";
	}
	
	public function index($status=1){
        $this->data['status']=$status;
        
		if(in_array($status,[1,5])):
			$controller = 'instrumentChk'; 
		elseif(in_array($status,[4])):
			$controller = 'instrumentRej'; 
		else: 
			$controller = 'instrument';
		endif;
		
        $this->data['tableHeader'] = getQualityDtHeader($controller);
        $this->load->view($this->indexPage,$this->data);
    }

	public function indexUsed($status=2){
		$this->data['status']=$status;
        $this->data['tableHeader'] = getQualityDtHeader('qcChallan');
        $this->load->view($this->indexUsed,$this->data);
    }

    public function getDTRows($status=1){ 
		$data=$this->input->post();
		$data['status']=$status;
		$result = $this->instrument->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getInstrumentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function getChallanDTRows($status=2){ 
		$data=$this->input->post();
		$data['status']=$status;
		$result = $this->qcChallan->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = 'qcChallan';
            $sendData[] = getQcChallanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addInstrument(){
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['ref_id'=>6]);
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList();
        $this->data['status'] = 1;
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['size']))
            $errorMessage['size'] = "Instrument Size is required.";
        if (empty($data['category_id']))
            $errorMessage['category_id'] = "Category is required.";
        if (empty($data['location_id']))
            $errorMessage['location_id'] = "Location is required.";
		if (empty($data['item_code']))
            $errorMessage['item_code'] = "Item Code is required.";
		
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            if(!empty($_FILES['certi_file'])):
                if($_FILES['certi_file']['name'] != null || !empty($_FILES['certi_file']['name'])):
                    $this->load->library('upload');
                    $_FILES['userfile']['name']     = $_FILES['certi_file']['name'];
                    $_FILES['userfile']['type']     = $_FILES['certi_file']['type'];
                    $_FILES['userfile']['tmp_name'] = $_FILES['certi_file']['tmp_name'];
                    $_FILES['userfile']['error']    = $_FILES['certi_file']['error'];
                    $_FILES['userfile']['size']     = $_FILES['certi_file']['size'];
                    
                    $imagePath = realpath(APPPATH . '../assets/uploads/instrument/');
                    $config = ['file_name' => 'certi_file_'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];
    
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload()):
                        $errorMessage['certi_file'] = $this->upload->display_errors();
                        $this->printJson(["status"=>0,"message"=>$errorMessage]);
                    else:
                        $uploadData = $this->upload->data();
                        $data['certi_file'] = $uploadData['file_name'];
                    endif;
                endif;
            endif;
          
            $data['next_cal_date'] = date('Y-m-d', strtotime($data['last_cal_date'] . "+".$data['cal_freq']." months") );
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->instrument->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['ref_id'=>6]);
        $this->data['dataRow'] = $this->instrument->getItem(['id'=>$id, 'single_row'=>1]);
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList();
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->instrument->delete($id));
        endif;
    }
    
    public function inwardGauge(){
        $data = $this->input->post();
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['ref_id'=>6]);
        $this->data['dataRow'] = $this->instrument->getItem(['id'=>$data['id'], 'single_row'=>1]);
        $this->data['status'] = $data['status'];
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList();
        $this->load->view($this->formPage,$this->data);
    }
	
	public function saveRejectGauge(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['reject_reason'])):
            $errorMessage['reject_reason'] = "Reject Reason is required.";
        endif;
        
        $data['id'] = $data['gauge_id'];
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->instrument->saveRejectGauge($data));
        endif;
    }
	
    public function addPurchaseRequest(){
        $this->data['itemData'] = $this->item->getItemLists(6);
        $this->load->view($this->requestForm,$this->data);
    }

    public function savePurchaseRequest(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['req_item_id'][0]))
            $errorMessage['req_item_id'] = "Item Name is required.";
        if(empty($data['req_date']))
            $errorMessage['req_date'] = "Request Date is required.";
        if(empty($data['req_qty'][0]))
            $errorMessage['req_qty'] = "Request Qty. is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['item_data'] = "";$itemArray = array();
			if(isset($data['req_item_id']) && !empty($data['req_item_id'])):
				foreach($data['req_item_id'] as $key=>$value):
					$itemArray[] = [
						'req_item_id' => $value,
						'req_qty' => $data['req_qty'][$key],
						'req_item_name' => $data['req_item_name'][$key]
					];
				endforeach;
				$data['item_data'] = json_encode($itemArray);
			endif;
            unset($data['req_item_id'], $data['req_item_name'], $data['req_qty']);
            $this->printJson($this->jobMaterial->savePurchaseRequest($data));
        endif;
    }

    public function printIssueHistoryCardData($id){
        $this->data['insData'] = $insData = $this->instrument->getItem(['id'=>$id, 'single_row'=>1]);
        $this->data['dataRow'] = $this->qcChallan->getQcChallanTrans(['item_id'=>$id, 'challan_type'=>[1,2]]);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('instrument/printIssueHistory',$this->data,true);
		
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1.5rem;width:50%">Issue History Card</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;"></td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-top:1px solid #000000;">
						<tr>
							<td style="width:33%;"></td>
                            <td style="width:33%;"></td>
							<td style="width:33%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));

		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,25,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

    public function printCalHistoryCardData($id){
        $this->data['insData'] = $insData = $this->instrument->getItem(['id'=>$id, 'single_row'=>1]);
        $this->data['calData'] = $this->qcChallan->getQcChallanTrans(['item_id'=>$id,'challan_type'=>3]);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('instrument/printCalHistory',$this->data,true);
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1.5rem;width:50%">Calibration History Card</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;"></td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-top:1px solid #000000;">
						<tr>
							<td style="width:33%;"></td>
                            <td style="width:33%;"></td>
							<td style="width:33%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
        $mpdf = new \Mpdf\Mpdf();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));

		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,25,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData); 
		$mpdf->Output($pdfFileName,'I');
	}
}
?>