<?php
class DispatchPlan extends MY_Controller{
    private $indexPage = "dispatch_plan/index";
    private $form = "dispatch_plan/form";   

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Dispatch Plan";
		$this->data['headData']->controller = "dispatchPlan";  
        $this->data['headData']->pageUrl = "dispatchPlan";
	}

    public function index($plan_type='pendingPlan'){
        if($plan_type == 'pendingPlan'){
			$this->data['tableHeader'] = getSalesDtHeader('pendingPlan');
		}else{
            $this->data['tableHeader'] = getSalesDtHeader("dispatchPlan");
        }
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();
        if($status == 0){
            $result = $this->dispatchPlan->getPendingPlanDTRows($data);
		}else{
            $result = $this->dispatchPlan->getDTRows($data);
        }
        $sendData = array(); $i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            if($status == 0){
                $sendData[] = getPendingPlanData($row);
            }else{
                $sendData[] = getDispatchPlanData($row);
            }
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPlan($party_id){
        $plan_prefix = 'DP/'.getShortFY().'/';
        $plan_no = $this->dispatchPlan->getNextPlanNo();
        $this->data['plan_number'] = $plan_prefix.$plan_no;
        $this->data['party_id'] = $party_id;
		$this->data['orderItemList'] = $this->salesOrder->getPendingOrderItems(['party_id'=>$party_id, 'group_by'=>'so_trans.id', 'trans_status'=>0]); 
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['so_trans_id'])){
            $errorMessage['general_error'] = "Item Detail is required.";
        }else{
            foreach($data['so_trans_id'] as $key=>$value){
                if(empty($data['qty'][$value])){
                    $errorMessage['qty_'.$value] = "Qty is required.";                    
                }else{
                    if($data['qty'][$value] > $data['pending_qty'][$value]){
                        $errorMessage['qty_'.$value] = "Invalid qty.";                    
                    }
                }
            }
        }
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dispatchPlan->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->dispatchPlan->delete($id));
        endif;
    }
}
?>