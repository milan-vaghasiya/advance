<?php
class OutChallan extends MY_Controller{
    private $indexPage = "out_challan/index";
    private $formPage = "out_challan/form";
    private $receive = "out_challan/receive";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Out Challan";
		$this->data['headData']->controller = "outChallan";
		$this->data['headData']->pageUrl = "outChallan";
	}
	
	public function index(){
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

	public function getDTRows($status = 1){
        $data = $this->input->post();
		$data['status'] = $status;
        $result = $this->outChallan->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;  
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getOutChallanData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addChallan(){
        $this->data['trans_no'] = $this->outChallan->nextTransNo(2);
        $this->data['trans_number'] = 'OCH/'.getShortFY().'/'.$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[1,2,3]]);
        $this->data['itemList'] = $this->item->getItemList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['itemData']))
            $errorMessage['itemData'] = "Item Details is required.";
		
		if(empty($data['trans_date'])){
            $errorMessage['trans_date'] = 'Out Challan Date is required.';
		}else{
			if (($data['trans_date'] < $this->startYearDate) OR ($data['trans_date'] > $this->endYearDate)){
				$errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
			}
		}
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			if(empty($data['id'])):
                $data['trans_no'] = $this->outChallan->nextTransNo(2);
                $data['trans_number'] = 'OCH/'.getShortFY().'/'.$data['trans_no'];
            endif;
            $this->printJson($this->outChallan->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $this->outChallan->getOutChallan(['id'=>$id,'itemList'=>1,'customWhere'=>'((in_out_challan_trans.qty - in_out_challan_trans.receive_qty) != 0)']);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[1,2,3]]);
        $this->data['itemList'] = $this->item->getItemList();       
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->outChallan->delete($id));
		endif;
	}

    public function receiveItem(){
        $data = $this->input->post();
        $this->data['ref_id'] = $data['id'];
        $this->data['options'] = $this->getPendingReceiveItems(['ref_id'=>$data['id']]);
        $this->load->view($this->receive,$this->data);
    }

    public function getPendingReceiveItems($param = []){
        $customWhere = '(in_out_challan_trans.qty - in_out_challan_trans.receive_qty) > 0';
        $itemData = $this->outChallan->getOutChallanItems(['trans_main_id'=>$param['ref_id'], 'is_returnable'=>'YES', 'customWhere'=>$customWhere]);

        $options = '<option value="">Select Item</option>'; $pendingQty=0;
        if(!empty($itemData)):
            foreach ($itemData as $row) :
                $pendingQty = $row->qty - $row->receive_qty;
                $options .= '<option value="'.$row->id.'">'.$row->item_name.' (Pend. Qty : '.$pendingQty.')</option>';
            endforeach;
        endif;

		return $options;
	}

    public function saveReceiveItem(){
        $data = $this->input->post(); 
        $errorMessage = array();

        if(empty($data['receive_date'])){
            $errorMessage['receive_date'] = "Date is required.";
        }
		if(empty($data['challan_trans_id'])){
            $errorMessage['challan_trans_id'] = "Item is required.";
        }
        if(empty($data['receive_qty'])){
            $errorMessage['receive_qty'] = "Qty. is required.";
        }else{
            $inItemData = $this->outChallan->getOutChallanItems(['id'=>$data['challan_trans_id'], 'single_row'=>1]);
            $pendingQty = $inItemData->qty - $inItemData->receive_qty; 
            if($data['receive_qty'] > $pendingQty):
                $errorMessage['receive_qty'] = "Invalid Qty.";
            endif;
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->outChallan->saveReceiveItem($data));
        endif;
    }

    public function outChallanHtml(){
        $data = $this->input->post();
        $recData = $this->outChallan->getReceiveItemTrans(['ref_id'=>$data['ref_id']]);

		$i=1; $tbody='';        
		if(!empty($recData)):
			foreach($recData as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id.", 'challan_trans_id' : '".$row->challan_trans_id."', 'receive_qty' : '".$row->receive_qty."'}, 'message' : 'Receive Item', 'res_function' : 'getOutChallanHtml', 'fndelete' : 'deleteReceiveItem'}";
                
				$tbody .= '<tr class="text-center">
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->receive_date).'</td>
                    <td class="text-left">'.$row->item_name.'</td>
                    <td>'.floatval($row->receive_qty).'</td>
                    <td class="text-center">
                        <button type="button" onclick="trash('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
                    </td>
                </tr>';
			endforeach;
        else:
            $tbody = '<tr><td colspan="5" class="text-center">No data found.</td></tr>';
		endif;

        $this->printJson(['status' => 1, 'tbodyData' => $tbody, 'options' => $this->getPendingReceiveItems(['ref_id'=>$data['ref_id']])]);
	}    

    public function deleteReceiveItem(){
		$data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->outChallan->deleteReceiveItem($data));
		endif;
	}

    public function printChallan($id){
		$this->data['dataRow'] = $dataRow = $this->outChallan->getOutChallan(['id'=>$id, 'itemList'=>1]);
        $this->data['partyData'] = $this->party->getParty(['id'=>$dataRow->party_id]);
		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
		$companyData = $this->outChallan->getCompanyInfo();

		$logo=base_url('assets/images/logo.png');
		$letter_head=base_url('assets/images/letterhead_top.png');

		$pdfData = $this->load->view('out_challan/print_challan',$this->data,true);

		$htmlHeader = '<img src="'.$letter_head.'" class="img">
						<table>
							<tr>
								<th style="width:35%;letter-spacing:2px;" class="text-left fs-14">'.$companyData->company_gst_no.'</th>
								<th style="width:30%;letter-spacing:2px;" class="text-center fs-17">OUT CHALLAN</th>
								<th style="width:35%;letter-spacing:2px;" class="text-right"></th>
							</tr>
						</table>';

		$mpdf = new \Mpdf\Mpdf();
		$filePath = realpath(APPPATH . '../assets/uploads/outChallan/');
		$pdfFileName = $filePath.'/outChallan_' .$id. '.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->AddPage('P','','','','',5,5,32,30,5,5,'','','','','','','','','','A5-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>