<?php
class DieChallan extends MY_Controller{
    private $indexPage = "die_challan/index";
    private $formPage = "die_challan/form";
    private $returnPage = "die_challan/return_form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Die Challan";
		$this->data['headData']->controller = "dieChallan";
		$this->data['headData']->pageUrl = "dieChallan";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0,$challan_type=2){
        $data = $this->input->post(); 
		$data['challan_type'] = $challan_type;
		$data['status'] = $status;
        $result = $this->dieChallan->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;  
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getDieChallanData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addChallan(){
		$this->data['trans_no'] = $this->dieChallan->nextTransNo();
        $this->data['trans_prefix'] = 'DCH/'.getYearPrefix('SHORT_YEAR').'/';
		$this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyData'] = $this->party->getPartyList(['party_category'=>[1,2,3]]);
        $this->data['prcData'] = $this->sop->getPRCList(['status'=>[1,2]]); 
        $this->data['empData'] = $this->employee->getEmployeeList();
        //$this->data['itemData'] = $this->dieMaster->getDieMasterData(['group_by'=>'die_master.fg_id,die_master.set_no','available_set'=>1,'status'=>1]);
        $this->data['itemData'] = $this->dieMaster->getDieMasterData(['status'=>1]);
        $this->load->view($this->formPage,$this->data);
    }
    
	public function getPartyList(){
        $challan_type = $this->input->post('challan_type'); 
        if($challan_type != 1){
            $partyData = $this->party->getPartyList(['party_category'=>3]);
            $options = '<option value="">Select Issue To</option>';
            foreach($partyData as $row):
                $options .= '<option value="'.$row->id.'">'.$row->party_name.'</option>';
            endforeach;
        }else{
            $empData = $this->employee->getEmployeeList();
            $options = '<option value="0">IN-HOUSE</option>';
            foreach($empData as $row):
                $options .= '<option value="'.$row->id.'">'.$row->emp_name.'</option>';
            endforeach;
        }
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['trans_no'])) {
            $errorMessage['trans_no'] = "Challan No. is required.";
        } 
        if (empty($data['trans_date'])) {
            $errorMessage['trans_date'] = "Challan Date is required.";
        } 
        if (isset($data['party_id']) && $data['party_id'] == '') {
            $errorMessage['party_id'] = "Issue To is required.";
        }        
        if (empty($data['itemData'])) {
            $errorMessage['item_name_error'] = "Item detail is required.";
        }

        if (!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dieChallan->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $this->dieChallan->getDieChallan(['id'=>$id,'itemList'=>1]);
        $this->data['partyData'] = $this->party->getPartyList(['party_category'=>[1,2,3]]);
        $this->data['prcData'] = $this->sop->getPRCList(['status'=>[1,2]]);
        $this->data['empData']  = $this->employee->getEmployeeList();
        //$this->data['itemData'] = $this->dieMaster->getDieMasterData(['group_by'=>'die_master.fg_id,die_master.set_no','available_set'=>1,'status'=>1]);
        $this->data['itemData'] = $this->dieMaster->getDieMasterData(['status'=>1]);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->dieChallan->delete($id));
		endif;
	}

    public function returnChallan(){
		$data = $this->input->post();
        $this->data['id'] = $data['id'];
        $this->data['challan_type'] = $data['challan_type'];
        $this->load->view($this->returnPage,$this->data);
    }

    public function saveReturnDie(){
        $data = $this->input->post(); 
		$errorMessage = array();

		if (empty($data['receive_at'])) {
			$errorMessage['receive_at'] = "Date is required.";
        }
        
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:          
			$this->printJson($this->dieChallan->saveReturnDie($data));
        endif;
    }
    
	public function printChallan($id){
        $this->data['challanData'] = $challanData = $this->dieChallan->getDieChallan(['id'=>$id, 'itemList'=>1]);
		$this->data['companyData'] = $companyData = $this->purchaseOrder->getCompanyInfo();
		$this->data['partyData'] = $partyData = $this->party->getParty(['id'=>$challanData->party_id]);
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
        $pdfData = '<table class="table bg-light-grey"><tr><td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">DIE CHALLAN</td></tr></table>
		
        <table class="table item-list-bb" style="margin-top:5px;">
            <tr>
                <td rowspan="2" style="width:60%;vertical-align:top;">
                    <b>M/S. '.(!empty($challanData->issue_to) ? $challanData->issue_to : '').'</b><br>
                    '.(!empty($partyData->party_address) ? $partyData->party_address : "").(!empty($partyData->party_pincode) ? " ".$partyData->party_pincode : "").'<br>
                    '.(!empty($partyData->city_name) ? "<b>City : </b>".$partyData->city_name : "").'
                    '.(!empty($partyData->state_name) ? "<b>State : </b>".$partyData->state_name : "").'
                    '.(!empty($partyData->country_name) ? "<b>Country : </b>".$partyData->country_name : "").'<br><br>
                    
                    <b>Kind. Attn. : '.(!empty($partyData->contact_person) ? $partyData->contact_person : "").'</b><br>
                    Contact No. : '.(!empty($partyData->party_mobile) ? $partyData->party_mobile : "").'<br>
                    GSTIN : '.(!empty($partyData->gstin) ? $partyData->gstin : "").'
                </td>
                <td>
                    <b>Challan No.</b>
                </td>
                <td>
                    '.(!empty($challanData->trans_number) ? $challanData->trans_number : "").'
                </td>
            </tr>
            <tr class="text-left">
                <th>Challan Date</th>
                <td>'.(!empty($challanData->trans_date) ? formatDate($challanData->trans_date) : "").'</td>
            </tr>
            <tr class="text-left">
                <td colspan="3"><b>Remark : </b>'.(!empty($challanData->remark) ? $challanData->remark : "").'</td>
            </tr>
        </table>
		
		<table class="table item-list-bb" style="margin-top:10px;">
			<tr class="text-center">
				<th style="width:4%;" rowspan="2">No.</th>
				<th style="width:10%;" rowspan="2">Item Code</th>
				<th style="width:28%;" rowspan="2">Item Name</th>
				<th style="width:12%;" rowspan="2">PRC Number</th>
				<th style="width:8%;" rowspan="2">Set No.</th>
				<th colspan="2">Die Components</th>
			</tr>
            <tr class="text-center">
                <th style="width:18%;">Die Code</th>
                <th style="width:20%;">Die Name</th>
            </tr>';
            $i=1;
            if(!empty($challanData->itemList)):
                foreach($challanData->itemList as $row):
                    
                    $pdfData .= '<tr>
                        <td class="text-center">'.$i++.'</td>
                        <td class="text-center">'.(!empty($row->item_code) ? $row->item_code : '').'</td>
                        <td class="text-center">'.(!empty($row->item_name) ? $row->item_name : '').'</td>
                        <td class="text-center">'.(!empty($row->prc_number) ? $row->prc_number : '').'</td>
                        <td class="text-center">'.(!empty($row->die_set_no) ? $row->die_set_no : '').'</td>
                        <td class="text-center">'.(!empty($row->die_code) ? $row->die_code : '').'</td>
                        <td class="text-center">'.(!empty($row->category_name) ? $row->category_name : '').'</td>';

                    $pdfData .= '</tr>';						
                endforeach;
            else:
                $pdfData = '<tr><td class="text-center" colspan="5">No data available.</td></tr>';
            endif;
            // exit;
		$pdfData .= '</table>';
	
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
                            <td style="width:50%;"></td>
							<th style="width:50%;" class="text-right">For, '.$this->data['companyData']->company_name.'</th>
						</tr>
						<tr>
                            <td style="width:50%;"></td>
							<td style="width:50%;" class="text-right">'.$challanData->created_by.'<br>('.formatDate($challanData->created_at).')</td>
						</tr>
						<tr>
                            <td style="width:50%;"></td>
							<td style="width:50%;" class="text-right"><b>Prepared By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:50%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='Die_challan-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,30,30,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>