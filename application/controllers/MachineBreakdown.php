<?php
class MachineBreakdown extends MY_Controller{
    private $indexPage = "machine_breakdown/index";
    private $formPage = "machine_breakdown/form";   
    private $solution_form = "machine_breakdown/solution_form";   
    private $sparpart_request = "machine_breakdown/sparpart_request";  
    private $sparpart_view = "machine_breakdown/sparpart_view";

	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Machine Breakdown";
		$this->data['headData']->controller = "machineBreakdown";
        $this->data['headData']->pageUrl = "machineBreakdown";
	}
	
	public function index(){
        $this->data['tableHeader'] = getMaintenanceDtHeader($this->data['headData']->controller); 
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows($status=1){
        $data = $this->input->post(); $data['status']=$status;
        $result = $this->machineBreakdown->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row): 
            $row->sr_no = $i++;         
            $sendData[] = getMachineBreakdownData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMachineBreakdown(){
        $data = $this->input->post(); 
        $this->data['trans_no'] = $this->machineBreakdown->getNextMachineNo();
        $this->data['trans_number'] = 'MT/'.getYearPrefix('SHORT_YEAR').'/'.$this->data['trans_no'];
        $this->data['prc_id'] = (!empty($data['prc_id']) ?$data['prc_id'] : 0);
        $this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
        $this->data['prcList'] = $this->sop->getPRCList(['status'=>2]);
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();

        if(empty($data['trans_date']))
			$errorMessage['trans_date'] = "Breakdown Time is required.";

        if(empty($data['machine_id']))
            $errorMessage['machine_id'] = "Machine is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])):
                $data['trans_no'] = $this->machineBreakdown->getNextMachineNo();
                $data['trans_number'] = 'MT/'.getYearPrefix('SHORT_YEAR').'/'.$data['trans_no'];
            endif;
            $this->printJson($this->machineBreakdown->save($data));
        endif;
    }

    public function addSolution(){
        $data = $this->input->post(); 
        $this->data['dataRow'] = $this->machineBreakdown->getMachineBreakdown(['id'=>$data['id']]);
        $this->data['reasonList'] = $this->comment->getCommentList(['type'=>2]);
        $this->load->view($this->solution_form,$this->data);
    }

    public function saveSolution(){
        $data = $this->input->post();
		$errorMessage = array();

        if(empty($data['end_date']))
			$errorMessage['end_date'] = "End Time is required.";

        if(empty($data['idle_reason']))
			$errorMessage['idle_reason'] = "Idle Reason is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->machineBreakdown->save($data));
        endif;
    }

    public function edit(){     
        $data = $this->input->post();
        $this->data['dataRow'] = $this->machineBreakdown->getMachineBreakdown(['id'=>$data['id']]);
        $this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
        $this->data['prcList'] = $this->sop->getPRCList(['status'=>2]);
        $this->data['reasonList'] = $this->comment->getCommentList(['type'=>2]);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->machineBreakdown->delete($id));
        endif;
    }

    // Sparpart Data
    public function addSparPartRequest(){
        $data = $this->input->post();
        $this->data['trans_no'] = $this->machineBreakdown->getNextRequestNo();
        $this->data['trans_number'] = 'REQ/'.getYearPrefix('SHORT_YEAR').'/'.$this->data['trans_no'];
        $this->data['ref_id'] = $data['id'];
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>2]);
        $this->load->view($this->sparpart_request,$this->data);
    }

    public function saveSparPartRequest(){
        $data = $this->input->post();
        $errorMessage = array();

		if(empty($data['item_id'])){
            $errorMessage['item_id'] = "Item is required.";
        }
        if(empty($data['req_qty'])){
            $errorMessage['req_qty'] = "Qty. is required.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])):
                $data['trans_no'] = $this->machineBreakdown->getNextRequestNo();
                $data['trans_number'] = 'REQ/'.getYearPrefix('SHORT_YEAR').'/'.$data['trans_no'];
            endif;
            $this->printJson($this->machineBreakdown->saveSparPartRequest($data));
        endif;
    }

    public function getSparPartRequestHtml(){
        $data = $this->input->post();
        $recData = $this->machineBreakdown->getSparPartRequestData(['ref_id'=>$data['ref_id'],'multi_row'=>1]);

		$i=1; $tbody='';        
		if(!empty($recData)):
			foreach($recData as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id.", 'ref_id' : '".$row->ref_id."', 'issue_qty' : '".$row->issue_qty."'}, 'message' : 'Receive Item', 'res_function' : 'getSparPartRequestHtml', 'fndelete' : 'deleteSparPartRequest','controller':'store'}";
                
				$tbody .= '<tr class="text-center">
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td class="text-left">'.$row->trans_number.'</td>
                    <td class="text-left">'.$row->item_name.'</td>
                    <td>'.floatval($row->req_qty).'</td>
                    <td class="text-center">
                        <button type="button" onclick="trash('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
                    </td>
                </tr>';
			endforeach;
        else:
            $tbody = '<tr><td colspan="6" class="text-center">No data found.</td></tr>';
		endif;

        $this->printJson(['status' => 1, 'tbodyData' => $tbody]);
	} 
    
    public function viewSparPart(){
        $data = $this->input->post();
        $this->data['sparpartData'] = $this->machineBreakdown->getSparPartRequestData(['ref_id'=>$data['id'],'multi_row'=>1,'req_type'=>2]);
        $this->load->view($this->sparpart_view,$this->data);
    }
}
?>