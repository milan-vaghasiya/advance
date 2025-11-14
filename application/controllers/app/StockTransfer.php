<?php
class stockTransfer extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Stock Transfer";
		$this->data['headData']->controller = "app/stockTransfer";
		$this->data['headData']->pageUrl = "app/stockTransfer";
		$this->data['headData']->appMenu = "app/stockTransfer";   
	}
	
	public function index(){
        $this->data['rec_per_page'] = 10; // Records Per Page
        $this->load->view('app/pending_stock_transfer',$this->data);
    }

    public function getMaterialTransferData($parameter = []){
        $postData = $this->input->post(); 
        if(empty($postData)){$fnCall = 'Outside';}else{$fnCall = 'Ajax'; }
        $next_page = 0;
        
        $issueData = Array();$postData['p_or_m'] =1;$postData['entry_type']=1002;$postData['location_id'] = 0;
        if(isset($postData['page']) AND isset($postData['start']) AND isset($postData['length']))
        {
            $issueData = $this->itemStock->getStockData($postData);
            $next_page = intval($postData['page']) + 1;
        }
        else{ 
            $issueData = $this->itemStock->getStockData($postData);
        }
        
        
        $this->data['issueData'] = $issueData;
        $html ='';
        $html = $this->load->view('app/stock_view',$this->data,true);
        
        if($fnCall == 'Ajax'){$this->printJson(['orderDetail'=>$html,'next_page'=>$next_page]);}
        else{return $html;}
    }
	
   
    public function stockTransfer(){
        $this->load->view('app/stock_transfer',$this->data);
    }

    public function getMaterialData(){
        $data = $this->input->post();
        $qrData = explode("~",$data['code']);
        $postData['item_id'] = $qrData[0];
        $postData['batch_no'] = $qrData[1];
        $postData['location_id'] = $data['location_id'];
        $postData['single_row'] = 1;
        $stockData = $this->itemStock->getItemStockBatchWise($postData);
        $html="";
        if(!empty($stockData->qty) && $stockData->qty > 0){
            $html = '<tr>
                        <td style="pedding:0px;">
                            <div class="listItem mt-0">
                                <div class="media-content">
                                    <div>
                                        <h6 class="name mb-0">'.$stockData->location.' <small>Stock : '.floatVal($stockData->qty).'</small></h6> 
                                        <p class="mb-0 mt-0"> Batch No : '.$stockData->batch_no.' | Heat No :'.$stockData->heat_no.'</p>
                                    </div>
                                </div>
                                <div class="left-content w-auto">
                                    <div class="d-flex mt-0 mb-0">
                                        <p>Qty : '.floatVal($stockData->qty).'</p>
                                        <input type="hidden" name="qrCode" value="'.$data['code'].'">
                                        <input type="hidden" name="batch_qty" data-stock_qty="'.floatVal($stockData->qty).'" data-qr_code="'.$data['code'].'" class="form-control batchQty floatOnly m-1" min="0" value="'.floatVal($stockData->qty).'" readonly/>
                                        <input type="hidden" name="batch_no"  value="' . $stockData->batch_no . '" />
                                        <input type="hidden" name="heat_no"  value="' . $stockData->heat_no . '" />
                                        <input type="hidden" name="location_id"  value="' . $stockData->location_id . '" />
                                        <input type="hidden" name="item_name"  value="' . $stockData->item_name . '" />
                                        <input type="hidden" name="item_id"  value="' . $stockData->item_id . '" />
                                        <div class="error batch_qty_'.$data['code'].'"></div>
                                    </div>
                                    <div class="divider mt-0 p-0"></div>
                                </div>
							</div>
                        </td>
                        
                    </tr>';
            $this->printJson(['status'=>1,'html'=> $html,'item_name'=>$stockData->item_name]);
        }else{
            $this->printJson(['status'=>0,'message'=>'Something is wrong... Stock not available']);
        }
    }

    public function saveStockTransfer(){
        $data = $this->input->post(); 
        $errorMessage = array();
        if(empty($data['qrCode']))
            $errorMessage['table_err'] = "Scan QR Code.";
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:          
            $this->printJson($this->itemStock->saveStockTransfer($data));
        endif;
    }

    public function addLocation(){
        $data = $this->input->post();
        $this->data['id'] = $data['id'];
        $this->load->view('app/stock_location',$this->data);
    }

    public function getLocationData(){
        $data = $this->input->post();
        $qrData =$data['code'];
      
        $locationData = $this->storeLocation->getStoreLocation(['id'=>$data['code']]);
        $html="";
        if(!empty($locationData)){
            $html = $locationData->location.'['.$locationData->store_name.']';
            $this->printJson(['status'=>1,'html'=> $html,'location_id'=>$locationData->id]);
        }else{
            $this->printJson(['status'=>0,'message'=>'Something is wrong... ']);
        }
    }

    public function saveTransferedLocation(){
        $data = $this->input->post(); 
        $errorMessage = array();
        if(empty($data['location_id']))
            $errorMessage['location_id'] = "Location is required.";
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:          
            $this->printJson($this->itemStock->save($data));
        endif;
    }
}
?>