<?php
class Inspection extends MY_Controller
{
    private $indexPage = "inpection/index";
    private $returnFormPage = "requisition/return_form";

    public function __construct(){
		parent::__construct(); 
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Inspection";
		$this->data['headData']->controller = "inspection";
		$this->data['headData']->pageUrl = "inspection";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'inspection']);
	}
	
	public function index($trans_type = 0){
        $this->data['trans_type']=$trans_type;
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($trans_type = 0){ 
		$data=$this->input->post();
        $data['trans_type'] = $trans_type;
		$result = $this->inspection->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->trans_type = $trans_type;
            $sendData[] = getInspectionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function inspection() {
        $data = $this->input->post();
        $this->data['dataRow'] = $this->issueRequisition->getIssuRequisition($data['issue_id']);
        $this->data['locationData'] = $this->storeLocation->getStoreLocationList(['store_type'=>'0,15','final_location'=>1]);
        $this->data['mtData'] = $this->inspection->getMaterialData($data['id']);
        $this->load->view($this->returnFormPage,$this->data);
    }

    public function saveInspection() {
        $data = $this->input->post();

        $errorMessage = array();

        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Trans Date is required.";

        if(empty($data['location_id']))
            $errorMessage['location_err'] = "Location is required.";

        if(empty($data['used_qty']) && empty($data['fresh_qty']) && empty($data['missed_qty']) && empty($data['broken_qty']) && empty($data['scrap_qty'])){
            $errorMessage['genral_error'] = "Inspect Qty. is Required";
        } else {
            $data['used_qty'] = (!empty($data['used_qty'])?$data['used_qty']:0);
            $data['fresh_qty'] = (!empty($data['fresh_qty'])?$data['fresh_qty']:0);
            $data['missed_qty'] = (!empty($data['missed_qty'])?$data['missed_qty']:0);
            $data['broken_qty'] = (!empty($data['broken_qty'])?$data['broken_qty']:0);
            $data['scrap_qty'] = (!empty($data['scrap_qty'])?$data['scrap_qty']:0);
            
            $data['insp_qty'] = $data['used_qty'] + $data['fresh_qty'] + $data['missed_qty'] + $data['broken_qty'] + $data['scrap_qty'];
            
            if($data['insp_qty'] != $data['total_qty']) {
                $errorMessage['genral_error'] = "Inspect Qty. is not Valid";
            }
        }

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            unset($data['issue_qty']);
            unset($data['return_qty']);
            $data['created_by'] = $this->session->userdata('loginId');
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['trans_type'] = 1;
            $data['entry_type'] = $this->data['entryData']->id;
            $this->printJson($this->requisition->saveReturnReq($data));
        endif;
    }
}
?>