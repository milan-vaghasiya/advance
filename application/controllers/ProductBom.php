<?php
class ProductBom extends MY_Controller
{
    private $indexPage = "product_options/index";
    private $productKitItem = "product_options/product_kit";
    private $viewProductProcess = "product_options/view_product_process";
    private $cycletimeForm = "product_options/ct_form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Product Option";
		$this->data['headData']->controller = "productBom";
		$this->data['headData']->pageUrl = "productBom";
	}

    public function index(){
        $this->data['tableHeader'] = getProductionDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){        
        $data = $this->input->post();
        $result = $this->item->getProdOptDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
			$optionStatus = $this->item->checkProductOptionStatus($row->id);
			$row->bom = (!empty($optionStatus->bom)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->process = (!empty($optionStatus->process)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->cycleTime = (!empty($optionStatus->cycleTime)) ? '<i class="fa fa-check text-primary"></i>' : '';
            $sendData[] = getProductOptionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addProductKitItems(){
        $id = $this->input->post('id');
        $this->data['item_id'] = $id;
        $this->data['rawMaterial'] = $this->item->getItemList(['item_type'=>'1,3']);
        $this->data['process'] = $this->item->getProductProcessList(['item_id'=>$id]);
        $this->load->view($this->productKitItem,$this->data);
    }

    public function groupSearch(){
        $data = $this->input->post();
		$this->printJson($this->item->groupSearch($data));
	}

    public function saveProductKit(){ 
        $data = $this->input->post();
		$errorMessage = array();
		
        if(empty($data['group_name'])){
            $errorMessage['group_name'] = "Group Name is required.";
        }
        if(empty($data['kit_item_id'])){
            $errorMessage['kit_item_id'] = "Item is required.";
        }		
        if(empty($data['kit_item_qty'])){
            $errorMessage['kit_item_qty'] = "Qty. is required.";
        }
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:			
			$this->printJson($this->item->saveProductKit($data));
		endif;
    }

    public function productKitHtml(){
        $data = $this->input->post();
        $productKitData = $this->item->getProductKitData(['item_id'=>$data['item_id']]);
		$i=1; $tbody='';
        
		if(!empty($productKitData)):
			foreach($productKitData as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Process','res_function':'getProductKitHtml','fndelete':'deleteProductKit'}";
				$tbody.= '<tr>
						<td>'.$i++.'</td>
						<td>'.$row->group_name.'</td>
						<td>'.$row->process_name.'</td>
						<td>'.$row->item_name.'</td>
						<td>'.$row->qty.'</td>
						<td class="text-center">
							<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
						</td>
					</tr>';
			endforeach;
        else:
            $tbody = '<tr><td colspan="5" class="text-center">No data found.</td></tr>';
		endif;
        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
	}
	
	public function deleteProductKit(){ 
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$this->printJson($this->item->deleteProductKit($data['id']));
		endif;
    }
	
    public function viewProductProcess(){
        $id = $this->input->post('id');
        $this->data['item_id'] = $id;   
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['productProcess'] = $this->item->getProductProcessForSelect($id);
        $this->load->view($this->viewProductProcess,$this->data);
    }

    public function saveProductProcess(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['process_id'])){
            $errorMessage['process_id'] = "Process is required.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>2,'message'=>$errorMessage]);
        else:
            $data['sequence'] = max($data['process_id']);
            $data['process_id'] = implode(",",$data['process_id']); 
            $this->printJson($this->item->saveProductProcess($data));
        endif;
    }

    public function productProcessHtml(){
        $data = $this->input->post();
        $processData = $this->item->getItemProcess($data['item_id']);
        $processDataList = $this->process->getProcessList();

        $tbody = ''; $options = '<option value="">Select Process</option>';
        if (!empty($processData)) :
            $i = 1;            
            foreach ($processData as $row) :
                $tbody .= '<tr id="'.$row->id.'">
                        <td class="text-center">'.$i++.'</td>
                        <td>'.$row->process_name.'</td>
                        <td>'.$row->sequence.'</td>
                      </tr>';
            endforeach;
        else :
            $tbody .= '<tr><td colspan="3" class="text-center">No data found.</td></tr>';
        endif;
        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
    }

    public function updateProductProcessSequance(){
        $data = $this->input->post();
		$errorMessage = array();		
		if(empty($data['id']))
			$errorMessage['id'] = "Item ID is required.";
		
		if(empty($errorMessage)):
			$this->printJson($this->item->updateProductProcessSequance($data));			
		endif;
    }

    public function addCycleTime(){
        $id = $this->input->post('id'); 
        $this->data['processData'] = $this->item->getItemProcess($id);   
        $this->load->view($this->cycletimeForm,$this->data);
    }

    public function saveCT(){
        $data = $this->input->post();

        $data['loginId'] = $this->session->userdata('loginId');
        $cycleTimeData = ['id' => $data['id'], 'cycle_time' => $data['cycle_time'], 'finish_wt' => $data['finish_wt'], 'conv_ratio' => $data['conv_ratio'], 'loginId' => $data['loginId']];

        $this->printJson($this->item->saveProductProcessCycleTime($cycleTimeData));
    }

	public function productOptionPrint($id){
		$this->data['itemData'] = $this->item->getProductKitData(['item_id'=>$id]);
		$this->data['itemName'] = $this->item->getItem(['id'=>$id]);
        $this->data['processData'] = $this->item->getProductProcessList(['item_id'=>$id]);
		$this->data['companyData'] = $this->masterModel->getCompanyInfo();

		$logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');

        $pdfData = $this->load->view('product_options/print_bom',$this->data,true);
        
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='POPrint-'.$id.'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>