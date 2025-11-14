<?php
class AutoCron extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('masterModel');
    }

	public function fillStock(){
	    $postData = $this->input->post();
	    
        $this->db->where('item_id',$postData['item_id']);
        $this->db->where('is_delete',0);
		$this->db->where('qty < 50');
        $result = $this->db->get('tdm_store')->result();
        
        $demandQty = intval($postData['qty']);
        $stockData = Array();$i=0;$cp=0;
        if(!empty($result))
        {
            foreach($result as $row)
            {
                if($demandQty > 0)
                {
                    $qty = $demandQty;
                    $availableQty = ($row->capacity - $row->qty);
                    if($demandQty > $availableQty)
                    {
                        $qty = $availableQty;
                    }
                    
                    $stockData[$i]['pocket_no'] = $row->pocket_no;
                    $stockData[$i]['qty'] = $qty;
                    $stockData[$i]['stock_qty'] = $row->qty;
                    $demandQty -= $qty;
                    $i++;
                }
            }
        }
        
        $response = ['status'=>0,'message'=>'Success','current_position'=>$this->getCP(),'data'=>$stockData];
        
        echo json_encode($response);
    }

	public function getStock(){
	    $postData = $this->input->post();
	    
        $this->db->where('item_id',$postData['item_id']);
        $this->db->where('is_delete',0);
        $this->db->where('qty > 0');
        $this->db->order_by('qty','ASC');
        $result = $this->db->get('tdm_store')->result();
        
        $demandQty = intval($postData['qty']);
        $stockData = Array();$i=0;$totalStock = 0;
        if(!empty($result))
        {
            foreach($result as $row)
            {
                if($demandQty > 0)
                {
                    $stockData[$i]['pocket_no'] = $row->pocket_no;
                    $stockData[$i]['qty'] = $row->qty;
                    $stockData[$i]['stock_qty'] = $row->qty;
                    $totalStock += $row->qty; $i++;
                }
            }
        }
        $resMsg = 'Success';$data = Array();$i=0;
        if($demandQty > $totalStock)
        {
            $resMsg = 'Sorry!...Right Now we have only '.$totalStock.' Nos Available';
            echo json_encode(['status'=>1,'message'=>$resMsg,'data'=>$data]);
        }
        else
        {
            foreach($stockData as $row)
            {
               if($demandQty > 0)
                {
                    $qty = $demandQty;
                    if($demandQty > $row['qty'])
                    {
                        $qty = $row['qty'];
                    }
                    
                    $data[$i]['pocket_no'] = $row['pocket_no'];
                    $data[$i]['qty'] = $qty;
                    $data[$i]['stock_qty'] = $row['stock_qty'];
                    $demandQty -= $qty; $i++;
                } 
            }
            echo json_encode(['status'=>0,'message'=>$resMsg,'current_position'=>$this->getCP(),'data'=>$data]);
        }
        
        
        
        //echo json_encode($response);
    }
    
    

	public function demandStock($param=[]){
	    
        
        echo json_encode($param);
    }
	
	public function getCP(){
		
        $this->db->where('is_delete',0);
		$this->db->where('cp',1);
        $result = $this->db->get('tdm_store')->row();
		
		return (!empty($result->cp) ? $result->cp : 0);
	}

    
}
?>