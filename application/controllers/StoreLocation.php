<?php
class StoreLocation extends MY_Controller{
    private $indexPage = "store_location/index";
    private $storeForm = "store_location/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Store Location";
		$this->data['headData']->controller = "storeLocation";
        $this->data['headData']->pageUrl = "storeLocation/list"; 
	}

    public function list($parent_id = 0){
        $this->data['parent_id'] = $parent_id;
        $storeData = $this->storeLocation->getStoreLocation(['id'=>$parent_id]);
        $this->data['storeName'] = (!empty($storeData) && !empty($parent_id))?$storeData->location:"Store Location";
        $this->data['locationName'] = (!empty($storeData))?$storeData->location:"";
        $this->data['ref_id'] = (!empty($storeData))?$storeData->ref_id:0;
        $this->data['tableHeader'] = getStoreDtHeader("storeLocation");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($parent_id = 0){
        $data = $this->input->post(); $data['ref_id'] = $parent_id;
        $result = $this->storeLocation->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getStoreLocationData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addStoreLocation(){
        $data = $this->input->post();
        $this->data['ref_id'] = $data['ref_id'];
        $this->data['location'] = $data['location'];
        $this->data['storeList'] = $this->storeLocation->getStoreLocationList(['final_location'=>0]);
        $this->load->view($this->storeForm, $this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();

        if(empty($data['location']))
            $errorMessage['location'] = "Rack is required.";

        /*if(empty($data['ref_id'])):
            $errorMessage['ref_id'] = "Store Name is required..";
        else:
            if(empty($data['store_name'])):
                $errorMessage['ref_id'] = "Store Name is required.";
            endif;
        endif;*/
		if(empty($data['store_name']))
			$errorMessage['ref_id'] = "Store Name is required.";

        $nextlevel='';
        if($data['store_level'] != ""):
            $level = $this->storeLocation->getStoreLocationList(['ref_id'=>$data['ref_id']]);
            $count = count($level);
            $nextlevel = ($data['store_level'] != 0)?$data['store_level'].'.'.($count+1):($count+1);
            $mainStore = $this->storeLocation->getStoreLocation(['store_level'=>$data['store_level']]);
            $data['main_store_id'] = !empty($mainStore->id)?$mainStore->id:0;
            $data['store_level'] = $nextlevel;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->storeLocation->save($data));
        endif;
    }

    public function edit(){     
        $data = $this->input->post();
        $this->data['dataRow'] = $this->storeLocation->getStoreLocation($data);
        $this->data['storeList'] = $this->storeLocation->getStoreLocationList(['final_location'=>0]);
        $this->load->view($this->storeForm, $this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->storeLocation->delete($id));
        endif;
    }

    public function locationQr($id){
        $locationData = $this->storeLocation->getStoreLocation(['id'=>$id]);
	
        $logo = base_url('assets/images/logo.png');
        
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100,50]]); // Landscap
        $pdfFileName ='machine' . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
            
		$qrIMG=base_url('assets/uploads/location_qr/'.$id.'.png');
		if(!file_exists($qrIMG)){
			$qrText = $id;
			$file_name = $id;
			$qrIMG = base_url().$this->getQRCode($qrText,'assets/uploads/location_qr/',$file_name);
		}

		$boxData = '<div style="text-align:center;padding:1mm 1mm;">
			<table class="table item-list-pck">
				<tr>
					<td class="text-center" style="min-width:20%;" height="20"><img src='.$logo.' height="35" width="100" /></td>
					<td class="text-center" rowspan="2" style="padding:2px;min-width:80%;"><img src="'.$qrIMG.'" style="height:44mm;"></td>
				</tr>
				<tr>
					<th class="text-center" style="font-size:14px;min-width:20%;" height="80">'.$locationData->store_name.'<br>'.$locationData->location.'</th>
				</tr>
			</table></div>';
		$cb++;
		$mpdf->AddPage('P', '', '', '', '', 0, 0, 1, 1, 1, 1);
		$mpdf->WriteHTML($boxData);
        
        $mpdf->Output($pdfFileName, 'I');
	}
}
?>