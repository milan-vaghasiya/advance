<?php
class SupplierNcr extends MY_Controller {
    private $indexPage = "supplier_ncr/index";
    private $formPage = "supplier_ncr/form";
    private $solution_form = "supplier_ncr/solution_form";
   

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Supplier NCR";
		$this->data['headData']->controller = "supplierNcr";
		$this->data['headData']->pageUrl = "supplierNcr";
	}
	
	public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->ncr->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getNCRData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addNCR(){
        $this->data['trans_prefix'] = 'NCR/'.$this->shortYear.'/';
        $this->data['trans_no'] = $this->ncr->getNextTransNo();
        $this->data['trans_numer'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>'2,3']);
        $this->data['itemList']=$this->item->getItemList(['item_type'=>1]);
        $this->load->view($this->formPage,$this->data);
    }

    public function save() {
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['party_id']))
            $errorMessage['party_id'] = "Party is required.";

		if (empty($data['complaint']))
            $errorMessage['complaint'] = "Details of Complaint is required.";

        if (empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
		
        if(empty($data['trans_date'])){
            $errorMessage['trans_date'] = 'NCR Date is required.';
        }else{
            if (($data['trans_date'] < $this->startYearDate) OR ($data['trans_date'] > $this->endYearDate)){
                $errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
            }
        }

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $masterData = [
        	    'id' => $data['id'],
				'trans_prefix' => $data['trans_prefix'],
				'trans_no' => $data['trans_no'], 
				'trans_number' => $data['trans_prefix'].$data['trans_no'],
				'trans_date' => date('Y-m-d',strtotime($data['trans_date'])),
				'party_id' => $data['party_id'],
				'grn_trans_id' => $data['grn_trans_id'],
				'ref_of_complaint' => $data['ref_of_complaint'],
				'item_id' => $data['item_id'],
				'complaint' => $data['complaint'],
				'ncr_type' => $data['ncr_type'],
				'batch_no' => $data['batch_no'],
				'qty' => $data['qty'],
				'rej_qty' => $data['rej_qty']
            ];
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->ncr->save($masterData));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>'2,3']);
        $this->data['dataRow'] = $dataRow = $this->ncr->getNCR(['id'=>$data['id']]);
        $itemList = $this->getItemList(['party_id'=>$dataRow->party_id,'ncr_type'=>$dataRow->ncr_type,'item_id'=>$dataRow->item_id]);
        $this->data['itmOptions'] = $itemList['itemOptions'];
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->ncr->delete($id));
        endif;
    }

    public function getItemList($param =[]){
        $data = (!empty($param)) ? $param : $this->input->post();

        $options = '<option value="">Select Item Name</option>';
        if(empty($data['party_id'])):
            $itemList= $this->item->getItemList(['item_type'=>1]);
            foreach($itemList as $row):
                $selected = (!empty($data['item_id']) && $data['item_id'] == $row->id) ? 'selected' : '';
                $options .= '<option value="'.$row->id.'" data-grn_trans_id="0" '.$selected.'>'.((!empty($row->item_code)?'[ '.$row->item_code.' ] ': '').$row->item_name).'</option>';
            endforeach;
        else:
            if($data['ncr_type'] == 1){
                $itemList = $this->gateInward->getInwardItem(['party_id'=>$data['party_id'],'multi_row'=>1]);
                foreach($itemList as $row): 
                    if(!empty($row->fg_item_id)){
                        $selected = (!empty($data['item_id']) && $data['item_id'] == $row->fg_item_id) ? 'selected' : '';
                        $options .= '<option value="'.$row->fg_item_id.'" data-grn_trans_id="'.$row->id.'" data-qty="'.$row->qty.'" data-batch_no="'.$row->batch_no.'" '.$selected.'>'.$row->fg_item_name.(!empty($row->doc_no)?' [Challan No. : '.$row->doc_no.'] ': '').'</option>';
                    }
                endforeach;
            }else{
                $itemList = $this->sop->getProcessLogList(['processor_id'=>$data['party_id'],'trans_type'=>1]);
                foreach($itemList as $row): 
                    if(!empty($row->item_id)){
                        $selected = (!empty($data['item_id']) && $data['item_id'] == $row->item_id) ? 'selected' : '';
                        $options .= '<option value="'.$row->item_id.'" data-grn_trans_id="'.$row->id.'" data-qty="'.$row->qty.'" data-batch_no="'.$row->batch_no.'" '.$selected.'>'.$row->item_name.(!empty($row->in_challan_no)?' [Challan No. : '.$row->in_challan_no.'] ': '').'</option>';
                    }
                endforeach;
            }
        endif;

        if(!empty($param)):
            return['itemOptions'=>$options];
        else:
            $this->printJson(['itemOptions'=>$options]);
        endif;
    }
 
    public function complaintSolution(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->ncr->getNCR(['id'=>$data['id']]);
        $this->load->view($this->solution_form,$this->data);
    }
    
    public function saveSolution(){
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['report_no']))
            $errorMessage['report_no'] = "Report No. is required.";

        if (empty($data['ref_feedback']))
            $errorMessage['ref_feedback'] = "Effectiveness is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			 if(isset($_FILES['report_8d']['name'])):
                if($_FILES['report_8d']['name'] != null || !empty($_FILES['report_8d']['name'])):
                    $this->load->library('upload');
                    $_FILES['userfile']['name']     = $_FILES['report_8d']['name'];
                    $_FILES['userfile']['type']     = $_FILES['report_8d']['type'];
                    $_FILES['userfile']['tmp_name'] = $_FILES['report_8d']['tmp_name'];
                    $_FILES['userfile']['error']    = $_FILES['report_8d']['error'];
                    $_FILES['userfile']['size']     = $_FILES['report_8d']['size'];
                    
                    $imagePath = realpath(APPPATH . '../assets/uploads/8d_report/');
                    $config = ['file_name' => 'report-'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload()):
                        $errorMessage['report_8d'] = $this->upload->display_errors();
                        $this->printJson(["status"=>0,"message"=>$errorMessage]);
                    else:
                        $uploadData = $this->upload->data();
                        $data['report_8d'] = $uploadData['file_name'];
                    endif;
                endif;
            endif;
			
            $data['status'] =1;
            $this->printJson($this->ncr->save($data));
        endif;
    }
}
?>