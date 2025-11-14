<?php
class StockTrans extends MY_Controller{
    private $indexPage = "stock_trans/index";
    private $form = "stock_trans/form";    

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "FG Stock Inward";
		$this->data['headData']->controller = "stockTrans";        
        $this->data['headData']->pageUrl = "stockTrans/stockRegister";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'stockTrans/stockRegister']);
	}

    public function index(){
        $this->data['tableHeader'] = getStoreDtHeader("stockTrans");
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function stockRegister(){
        $this->data['pageHeader'] = 'STOCK REGISTER';
        $this->data['headData']->pageUrl = "stockTrans/stockRegister";
        $this->load->view("stock_trans/item_stock",$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();
        $data['entry_type'] = $this->data['entryData']->id;
        $data['item_type']=1;
        $result = $this->itemStock->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getStockTransData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addStock(){
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>[1,2,3,4,7,8,9]]);
        $this->load->view($this->form, $this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		

        if(empty($data['item_id']))
			$errorMessage['item_id'] = "Item Name is required.";
        if(empty(floatVal($data['qty'])))
			$errorMessage['qty'] = "Qty is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['entry_type'] = $this->data['entryData']->id;
            $data['location_id'] = $this->RTD_STORE->id;
            $this->printJson($this->itemStock->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->itemStock->delete($id));
        endif;
    }
}
?>