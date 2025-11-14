<?php
class QcChallan extends MY_Controller{
    private $indexPage = "qc_challan/index";
    private $formPage = "qc_challan/form";
    private $returnPage = "qc_challan/return_form";
    private $calibrationForm = "qc_challan/cali_return";    
    private $calibration = "qc_challan/cali_index";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "QC Challan";
		$this->data['headData']->controller = "qcChallan";
		$this->data['headData']->pageUrl = "qcChallan";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($qc_status=0){
        $data = $this->input->post(); $data['qc_status']=$qc_status;
        $result = $this->qcChallan->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;  
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getQcChallanData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function createChallan($id){
		$this->data['headData']->pageUrl = "qcChallan";
		$this->data['trans_no'] = $this->qcChallan->nextTransNo();
        $this->data['trans_prefix'] = 'QCH/'.getYearPrefix('SHORT_YEAR').'/';
		$this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyData'] = $this->party->getPartyList(['party_category'=>'2,3']);
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['itemData']  = $this->instrument->getItem(['status'=>1]); 
        $this->data['challanItem'] = $this->instrument->getItem(['ids'=>$id]);
        $this->data['empData']  = $this->employee->getEmployeeList();
        $this->load->view($this->formPage,$this->data);
    }

    public function addChallan(){
		$this->data['trans_no'] = $this->qcChallan->nextTransNo();
        $this->data['trans_prefix'] = 'QCH/'.getYearPrefix('SHORT_YEAR').'/';
		$this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyData'] = $this->party->getPartyList(['party_category'=>'2,3']);
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['itemData']  = $this->instrument->getItem(['status'=>1]);
        $this->data['empData']  = $this->employee->getEmployeeList();
        $this->load->view($this->formPage,$this->data);
    }
    
    public function getPartyList(){
        $challan_type = $this->input->post('challan_type'); 
        $options = '<option value="0">IN-HOUSE</option>';
        if($challan_type != 1){
            $partyData = $this->party->getPartyList(['party_category'=>'2,3']);
            foreach($partyData as $row):
                $options .= '<option value="'.$row->id.'">'.$row->party_name.'</option>';
            endforeach;
        }else{
            $deptData = $this->department->getDepartmentList();
            foreach($deptData as $row):
                $options .= '<option value="'.$row->id.'">'.$row->name.'</option>';
            endforeach;
        }
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_no']))
            $errorMessage['trans_no'] = "Challan No is required."; 
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Challan Date is required.";
        
        if(empty($data['item_id'][0]))
            $errorMessage['item_name_error'] = "Items is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $masterData = [
                'id' => $data['id'],
                'trans_prefix' => $data['trans_prefix'],  
                'trans_no' => $data['trans_no'],
                'trans_number' => $data['trans_number'],
                'challan_type' => $data['challan_type'],
                'trans_date' => $data['trans_date'],
                'party_id' => $data['party_id'],
                'emp_id' => $data['emp_id'],
                'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId')
            ];

            $itemData = [
                'id' => $data['trans_id'],
                'item_id' => $data['item_id'],
                'batch_no' => $data['batch_no'],
                'item_remark' => $data['item_remark'],
                'entry_type' => 1,
                'created_by' => $this->session->userdata('loginId')
            ];

            $this->printJson($this->qcChallan->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['partyData'] = $this->party->getPartyList(['party_category'=>'2,3']);
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['itemData']  = $this->instrument->getItem(['status'=>1]);
        $this->data['empData']  = $this->employee->getEmployeeList();
        $this->data['dataRow'] = $this->qcChallan->getQcChallan($id);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->qcChallan->deleteChallan($id));
		endif;
	}

    public function returnChallan(){
		$id = $this->input->post('id');
        $this->data['dataRow'] = $this->qcChallan->getQcChallanTrans(['id'=>$id,'single_row'=>1]);
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList();
        $this->load->view($this->returnPage,$this->data);
    }

    public function calibrationData($id){
        $this->data['tableHeader'] = getQualityDtHeader('calibrationData');
        $this->data['itemData'] = $this->instrument->getItem(['id'=>$id, 'single_row'=>1]);
        $this->load->view($this->calibration,$this->data);
    }

    public function getCalibrationData($id){
		$data=$this->input->post(); 
		$data['item_id'] = $id;
		$result = $this->qcChallan->getCalibrationData($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getCalibration($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getCalibration(){ 
        $data = $this->input->post();
        $this->data['dataRow'] = $this->qcChallan->getQcChallanTrans(['id'=>$data['id'],'single_row'=>1]);
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList();
        $this->load->view($this->calibrationForm,$this->data);
    }

    public function saveCalibration(){
        $data = $this->input->post(); 
		$errorMessage = array();
		if(empty($data['receive_at']))
			$errorMessage['receive_at'] = "Date is required.";
		if(empty($data['in_ch_no']))
			$errorMessage['in_ch_no'] = "Certificate No. is required.";
        if(empty($data['to_location']))
            $errorMessage['to_location'] = "Receive Location is required.";
        
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            if(!empty($_FILES['certificate_file'])):
                if($_FILES['certificate_file']['name'] != null || !empty($_FILES['certificate_file']['name'])):
                    $this->load->library('upload');
                    $_FILES['userfile']['name']     = $_FILES['certificate_file']['name'];
                    $_FILES['userfile']['type']     = $_FILES['certificate_file']['type'];
                    $_FILES['userfile']['tmp_name'] = $_FILES['certificate_file']['tmp_name'];
                    $_FILES['userfile']['error']    = $_FILES['certificate_file']['error'];
                    $_FILES['userfile']['size']     = $_FILES['certificate_file']['size'];
                    
                    $imagePath = realpath(APPPATH . '../assets/uploads/instrument/');
                    $config = ['file_name' => 'instrument-'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];
    
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload()):
                        $errorMessage['certificate_file'] = $this->upload->display_errors();
                        $this->printJson(["status"=>0,"message"=>$errorMessage]);
                    else:
                        $uploadData = $this->upload->data();
                        $data['certificate_file'] = $uploadData['file_name'];
                    endif;
                endif;
            endif;
          
            $itemData = $this->instrument->getItem(['id'=>$data['item_id'], 'single_row'=>1]); 
            $data['next_cal_date'] = date('Y-m-d', strtotime($data['receive_at'] . "+".$itemData->cal_freq." months") );
            $data['created_by'] = $this->session->userdata('loginId');
          
            $response = $this->qcChallan->saveCalibration($data);
			$this->printJson($response);
        endif;
    }
    
	function printChallan($id){
        $this->data['challanData'] = $challanData = $this->qcChallan->getQcChallan($id);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$this->data['partyData'] = (!empty($challanData->party_id) ? $this->party->getParty(['id'=>$challanData->party_id]) : []);
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('qc_challan/print_challan',$this->data,true);
	
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:33%;"></td>
                            <td style="width:33%;"></td>
							<th style="width:33%;">For, '.$this->data['companyData']->company_name.'</th>
						</tr>
						<tr>
                            <td style="width:33%;"></td>
                            <td style="width:33%;"></td>
							<td style="width:33%;" class="text-right">'.$challanData->emp_name.'<br>('.formatDate($challanData->created_at).')</td>
						</tr>
						<tr>
                            <td style="width:33%;"></td>
                            <td style="width:33%;"></td>
							<td style="width:33%;" class="text-right"><b>Prepared By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
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
		$mpdf->SetWatermarkImage($logo,0.03,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,30,30,5,5,'','','','','','','','','','A5-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>