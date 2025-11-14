<?php
class SalesEnquiry extends MY_Controller{
    private $indexPage = "sales_enquiry/index";
    private $form = "sales_enquiry/form";   
    private $npdIndex = "npd/index";
    private $feasibleForm = "npd/feasible_form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Sales Enquiry";
		$this->data['headData']->controller = "salesEnquiry";  
        $this->data['headData']->pageUrl = "salesEnquiry";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'salesEnquiry','tableName'=>'se_master']);
	}

    public function index($status=0){
        $this->data['status'] = $status;      
        $this->data['tableHeader'] = getSalesDtHeader("salesEnquiry");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->salesEnquiry->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getSalesEnquiryData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEnquiry(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1,'party_type'=>"1,2"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1,'active_item'=>'1,2']);
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
            $errorMessage['trans_date'] = 'Enquiry Date is required.';
        }else{
            if (($data['trans_date'] < $this->startYearDate) OR ($data['trans_date'] > $this->endYearDate)){
                $errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
            }
        }
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			if(empty($data['id'])):
                $data['trans_no'] = $this->data['entryData']->trans_no;
                $data['trans_number'] = $this->data['entryData']->trans_prefix.$data['trans_no'];
            endif;
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;
            $this->printJson($this->salesEnquiry->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->salesEnquiry->getSalesEnquiry(['id'=>$id,'itemList'=>1]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1,'party_type'=>"1,2"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1,'active_item'=>'1,2']);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->salesEnquiry->delete($id));
        endif;
    }

    public function saveFeasibleRequest(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->salesEnquiry->saveFeasibleRequest($data));
        endif;
    }

    public function npdEnquiry(){
        $this->data['tableHeader'] = getSalesDtHeader("npdSalesEnquiry");
        $this->load->view($this->npdIndex,$this->data);
    }
    
	public function getNpdEnqDTRows($status = 3,$feasible_status = 0){
        $data = $this->input->post();$data['status'] = $status;$data['feasible_status'] = $feasible_status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->salesEnquiry->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getNpSalesEnquiryData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function enqFeasible(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->salesEnquiry->getSalesEnquiryItem(['id'=>$data['id']]);
        $this->data['reasonList'] = $this->comment->getCommentList(['type'=>4]);
        $this->load->view($this->feasibleForm,$this->data);
    }

    public function saveFeasibility(){
        $data = $this->input->post();
        $errorMessage = array();
        if($data['feasible_status'] == 2 && empty($data['feasible_reason'])){
            $errorMessage['feasible_reason'] = "Reason is required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			
            $this->printJson($this->salesEnquiry->saveFeasibility($data));
        endif;
    }

}
?>