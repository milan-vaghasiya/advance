<?php
class PurchaseOrder extends MY_Controller{
    private $po_index = "app/po_index";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "PurchaseOrder";
		$this->data['headData']->controller = "app/purchaseOrder";    
		$this->data['headData']->pageUrl = "app/purchaseOrder";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'purchaseOrders','tableName'=>'po_master']);
    }


    public function index(){
		$this->data['headData']->appMenu = "app/purchaseOrder";
        $this->data['rec_per_page'] = 10; // Records Per Page
        $this->load->view($this->po_index, $this->data);
    }

    public function getPurchaseOrderData($fnCall = "Ajax"){
        $postData = $this->input->post();
        $postData['entry_type'] = $this->data['entryData']->id;
		if(empty($postData)){$fnCall = 'Outside';}
        $next_page = 0;
		$poData = Array();
		if(isset($postData['page']) AND isset($postData['start']) AND isset($postData['length']))
        {
            $poData = $this->purchaseOrder->getPurchaseOrderByApp($postData);
            $next_page = intval($postData['page']) + 1;
            
        }
        else{ $poData = $this->purchaseOrder->getPurchaseOrderByApp($postData); 
        }
		
		$this->data['poData'] = $poData;
		$poDetail ='';
		$poDetail = $this->load->view('app/po_list',$this->data,true);
		
        if($fnCall == 'Ajax'){$this->printJson(['orderDetail'=>$poDetail,'next_page'=>$next_page]);}
		else{return $poDetail;}
    }

    public function approvePurchaseOrder(){
		$data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseOrder->approvePurchaseOrder($data));
		endif;
	}

}
?>