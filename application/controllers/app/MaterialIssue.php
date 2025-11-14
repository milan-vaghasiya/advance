<?php
class MaterialIssue extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Material Issue";
		$this->data['headData']->controller = "app/materialIssue";
		$this->data['headData']->pageUrl = "app/materialIssue";
		$this->data['headData']->appMenu = "app/materialIssue";   
	}
	
	public function index(){
        $this->data['rec_per_page'] = 10; // Records Per Page
        $this->load->view('app/mt_issue_index',$this->data);
    }

    public function getMaterialIssueData($parameter = []){
        $postData = $this->input->post(); 
            if(empty($postData)){$fnCall = 'Outside';}else{$fnCall = 'Ajax'; }
            $next_page = 0;
            
            $issueData = Array();$status = $postData['status'];
            if(isset($postData['page']) AND isset($postData['start']) AND isset($postData['length']))
            {
                if($postData['status'] == 1){
                    $postData['status'] ='pending';
                    $issueData = $this->store->getRequestList($postData); 
                }else{
                    $issueData = $this->store->getMaterialIssueData($postData);
                }
                $next_page = intval($postData['page']) + 1;
                
            }
            else{ 
                if($postData['status'] == 1){
                    $postData['status'] ='pending';
                    $issueData = $this->store->getRequestList($postData); 
                }else{
                    $issueData = $this->store->getMaterialIssueData($postData);
                }
            }
            
           
            $this->data['issueData'] = $issueData;
            $this->data['status'] = $status;
            $leadDetail ='';
            $leadDetail = $this->load->view('app/issue_view',$this->data,true);
            
            if($fnCall == 'Ajax'){$this->printJson(['orderDetail'=>$leadDetail,'next_page'=>$next_page]);}
            else{return $leadDetail;}
    }
	
    public function prcDetail($id){
        $this->data['prcData'] = $this->sop->getPRC(['id'=>$id]);
        $this->load->view('app/prc_detail',$this->data);
    }

    public function issueMaterial($req_id = "",$item_id = ""){
        $this->data['req_id'] = $req_id;
        $this->data['item_id'] = $item_id;
		$this->data['prcData'] = $this->sop->getPRCList(['status'=>'0,1,2']);
		$this->data['empData'] = $this->employee->getEmployeeList();
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>'1,2,3']);
        $this->load->view('app/mt_issue_form',$this->data);
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
                                <div class="media-content w-auto">
                                    <div class="d-flex mt-0">
                                        <div style="width:70%">
                                            <h6 class="name mb-0">'.$stockData->location.' <small>Stock : '.floatVal($stockData->qty).'</small></h6> 
                                            <p class="mb-0 mt-0"> Batch No : '.$stockData->batch_no.'</p>
                                        </div>
                                        <div >
                                            <div class="dz-stepper border-1 rounded-stepper stepper-fill  active p-0 m-0 float-end font-12" >
                                                <input class="stepper batchQty qty  active font-12 p-0 m-0" type="number"  name="batch_qty[]" data-stock_qty="'.floatVal($stockData->qty).'" data-qr_code="'.$stockData->item_id.'~'.$stockData->batch_no.'~'.$stockData->location_id.'" value="'.floatVal($stockData->qty).'" min="0">
                                            </div>
                                        </div>
                                        <div >
                                            <button class="m-1 btn btn-sm btn-danger " onclick="Remove(this)"><i class="ti-trash"></i></button>
                                        </div>                                       
                                        <input type="hidden" name="batch_no[]"  value="' . $stockData->batch_no . '" />
                                        <input type="hidden" name="heat_no[]"  value="' . $stockData->heat_no . '" />
                                        <input type="hidden" name="location_id[]"  value="' . $stockData->location_id . '" />
                                        <input type="hidden" name="item_name[]"  value="' . $stockData->item_name . '" />
                                        <div class="error batch_qty_'.$data['code'].'"></div>
                                    </div>
                                    <div class="divider"></div>
                                </div>
							</div>  
                        </td>
                        
                    </tr>';
            $this->printJson(['status'=>1,'html'=> $html,'item_name'=>$stockData->item_name]);
        }else{
            $this->printJson(['status'=>0,'message'=>'Something is wrong... Stock not available']);
        }
    }

    public function getStockData(){
        $data = $this->input->post();
        $postData['item_id'] = $data['item_id'];
        $postData['stock_required'] = 1;
        $postData['group_by'] = "stock_transaction.location_id,stock_transaction.batch_no";
        $stockData = $this->itemStock->getItemStockBatchWise($postData);
        $html="";
        if(!empty($stockData)){
            foreach($stockData as $row){
                if(!empty($row->qty) && $row->qty > 0){
                    $html = '<tr>
                                <td>
                                    <div class="listItem mt-0">
                                        <!--<div class="media-content">
                                            <div>
                                                <h6 class="name mb-0">'.$row->location.' <small>Stock : '.floatVal($row->qty).'</small></h6> 
                                                <p class="mb-0 mt-0"> Batch No : '.$row->batch_no.' | Heat No :'.$row->heat_no.'</p>
                                            </div>
                                        </div>-->
                                        <!--<div>
                                        <h6 class="name mb-0">'.$row->location.' <small>Stock : '.floatVal($row->qty).'</small></h6> 
                                        </div>-->
                                         
                                        <div class="media-content w-auto">
                                            <div class="d-flex mt-0">
                                                <div style="width:70%">
                                                    <h6 class="name mb-0">'.$row->location.' <small>Stock : '.floatVal($row->qty).'</small></h6> 
                                                    <p class="mb-0 mt-0"> Batch No : '.$row->batch_no.'</p>
                                                </div>
                                                <div >
                                                    <div class="dz-stepper border-1 rounded-stepper stepper-fill  active p-0 m-0 float-end font-12" >
                                                        <input class="stepper batchQty qty  active font-12 p-0 m-0" type="number"  name="batch_qty[]" data-stock_qty="'.floatVal($row->qty).'" data-qr_code="'.$row->item_id.'~'.$row->batch_no.'~'.$row->location_id.'" value="'.floatVal($row->qty).'" min="0">
                                                    </div>
                                                </div>
                                                <div >
                                                    <button class="m-1 btn btn-sm btn-danger " onclick="Remove(this)"><i class="ti-trash"></i></button>
                                                </div>
                                                <input type="hidden" name="qrCode[]" value="'.$row->item_id.'~'.$row->batch_no.'~'.$row->location_id.'">
                                                <input type="hidden" name="batch_no[]"  value="' . $row->batch_no . '" />
                                                <input type="hidden" name="heat_no[]"  value="' . $row->heat_no . '" />
                                                <input type="hidden" name="location_id[]"  value="' . $row->location_id . '" />
                                                <input type="hidden" name="item_name[]"  value="' . $row->item_name . '" />
                                                <div class="error batch_qty_'.$row->item_id.'~'.$row->batch_no.'~'.$row->location_id.'"></div>
                                            </div>
                                           <div class="divider"></div>
                                        </div> 
                                    </div>
                                    
                                </td>
                                
                            </tr>';
                    $this->printJson(['status'=>1,'html'=> $html]);
                }
            }
        }else{
            $this->printJson(['status'=>0,'message'=>'Something is wrong... Stock not available']);
        }
        
    }
}
?>