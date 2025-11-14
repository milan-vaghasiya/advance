<?php
class SalesOrder extends MY_Controller{
    private $so_list = "app/so_index";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "SalesOrder";
		$this->data['headData']->controller = "app/salesOrder";    
		$this->data['headData']->pageUrl = "app/salesOrder";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'salesOrders','tableName'=>'so_master']);
    }


    public function index(){
		$this->data['headData']->appMenu = "app/salesOrder";
        $this->data['rec_per_page'] = 10; // Records Per Page
        $this->load->view($this->so_list, $this->data);
    }

    public function getSalesOrderData($fnCall = "Ajax"){
        $postData = $this->input->post();
        $postData['entry_type'] = $this->data['entryData']->id;
		if(empty($postData)){$fnCall = 'Outside';}
        $next_page = 0;
		$soData = Array();
		if(isset($postData['page']) AND isset($postData['start']) AND isset($postData['length']))
        {
            $soData = $this->salesOrder->getSalesOrderByApp($postData);
            $next_page = intval($postData['page']) + 1;
            
        }
        else{ $soData = $this->salesOrder->getSalesOrderByApp($postData); 
        }
		
		$this->data['soData'] = $soData;
		$soDetail ='';
		$soDetail = $this->load->view('app/so_list',$this->data,true);
		
        if($fnCall == 'Ajax'){$this->printJson(['orderDetail'=>$soDetail,'next_page'=>$next_page]);}
		else{return $soDetail;}
    }

    public function approveSalesOrder(){
		$data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesOrder->approveSalesOrder($data));
		endif;
	}

}
?>