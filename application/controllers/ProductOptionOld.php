<?php
class ProductOption extends MY_Controller
{
    private $indexPage = "product_options/index";
    private $productKitItem = "product_options/product_kit";
    private $productKitItemNew = "product_options/product_kit_new";
    private $viewProductProcess = "product_options/view_product_process";
    private $cycletimeForm = "product_options/ct_form";
    private $dieSet = "product_options/die_set_form";
    private $packingStandard = "product_options/packing_standard";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Product Option";
		$this->data['headData']->controller = "productOption";
		$this->data['headData']->pageUrl = "productOption";
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
        $this->data['rawMaterial'] = $this->item->getItemList(['item_type'=>'1,2,3']);
        $this->data['process'] = $this->item->getProductProcessList(['item_id'=>$id]);
        $this->data['mbOptions'] = $this->getMainBomItems(['item_id'=>$id]);
        $this->load->view($this->productKitItemNew,$this->data);
    }

    public function getMainBomItems($param = []){
		$mbItems = $this->item->getProductKitData(['is_main'=>1,'item_id'=>$param['item_id']]);
		$mbOptions = '<option value="0">N/A</option>';
		if(!empty($mbItems)){
			foreach($mbItems as $row){
				$mbOptions .='<option value="'.$row->id.'" data-main_item="'.$row->ref_item_id .'" >'.$row->item_name.'</option>';
			}
		}
		return $mbOptions;
	}

    public function groupSearch(){
        $data = $this->input->post();
		$this->printJson($this->item->groupSearch($data));
	}

    public function saveProductKit(){ 
        $data = $this->input->post();
		$errorMessage = array();
		
        if(empty($data['kit_item_id'])){
            $errorMessage['kit_item_id'] = "Item is required.";
        }		
        if(empty($data['kit_item_qty'])){
            $errorMessage['kit_item_qty'] = "Qty. is required.";
        }
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$data['group_name'] = "G";
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
				$alt = ($row->ref_id > 0) ? 'Yes' : '';
				$rowBg = ($row->ref_id > 0) ? 'bg-light' : '';
				$tbody.= '<tr class="text-center '.$rowBg.'">
						<td>'.$i++.'</td>
						<td class="text-left">'.$row->process_name.'</td>
						<td class="text-left">'.$row->item_code.'</td>
						<td class="text-left">'.$row->item_name.'</td>
						<td>'.$alt.'</td>
						<td>'.$row->qty.'</td>
						<td class="text-center">
							<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
						</td>
					</tr>';
			endforeach;
        else:
            $tbody = '<tr><td colspan="7" class="text-center">No data found.</td></tr>';
		endif;
        $this->printJson(['status'=>1,'tbodyData'=>$tbody,'mbOptions'=> $this->getMainBomItems(['item_id'=>$data['item_id']])]);
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
        $data = $this->input->post();
        $this->data['item_id'] = $data['id'];   
        $this->data['processDataList'] = $this->process->getProcessList(['ingonre_process'=>3]); //	IGNORE RM CUTTING PROCESS
        $this->data['itemData'] = $this->item->getItem($data);
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
            $this->printJson($this->item->saveProductProcess($data));
        endif;
    }

    public function deleteProductProcess(){ 
        $data=$this->input->post(); 
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$this->printJson($this->item->deleteProductProcess($data));
		endif;
    }

    public function setProductionType(){
        $data = $this->input->post();
        if(empty($data['item_id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->setProductionType($data));
        endif;
    }
    
    public function productProcessHtml(){
        $data = $this->input->post();
        $processData = $this->item->getProductProcessList(['item_id'=>$data['item_id']]);

        $tbody = ''; $options = '<option value="">Select Process</option>';
        if (!empty($processData)) :
            $i = 1;            
            foreach ($processData as $row) :
                $deleteParam = "{'postData':{'id' : ".$row->id.",'item_id' : ".$row->item_id.",'process_id' : ".$row->process_id."},'message' : 'Product Process','res_function':'getProductProcessHtml','fndelete':'deleteProductProcess'}";
                $tbody .= '<tr id="'.$row->id.'">
                        <td class="text-center">'.$i++.'</td>
                        <td>'.$row->process_name.'</td>
                        <td>'.$row->sequence.'</td>
                       	<td class="text-center">
							<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
						</td>
                      </tr>';
            endforeach;
        else :
            $tbody .= '<tr><td colspan="4" class="text-center">No data found.</td></tr>';
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
        $this->data['processData'] = $this->item->getProductProcessList(['item_id'=>$id]);   
		$this->data['unitList'] = $this->item->itemUnits();
        $this->load->view($this->cycletimeForm,$this->data);
    }

	/* Created By @Raj.F 20-02-2025 */
    public function saveCT(){
        $data = $this->input->post();
		
		/* PRC Wise File Upload */
		for ($i = 0; $i < count($_FILES['drawing_file']['name']); $i++):
			$file_extension = pathinfo($_FILES['drawing_file']['name'][$i], PATHINFO_EXTENSION);
			$file_name = (!empty($_FILES['drawing_file']['name'][$i])) ? time()."_".rand().".".$file_extension : NULL;
			
			if(!empty($file_name)){
				move_uploaded_file($_FILES['drawing_file']['tmp_name'][$i], FCPATH."assets/uploads/process_drg/".$file_name);
			}
			
			if (!empty($data['old_drawing_file'][$i])) {
				$old_file_path = FCPATH."assets/uploads/process_drg/" . $data['old_drawing_file'][$i];
				if (!empty($file_name) && file_exists($old_file_path)) {
					unlink($old_file_path);
				}
			}
			
			$data['drawing_file'][] = ((!empty($data['old_drawing_file'][$i]) && empty($file_name)) ? $data['old_drawing_file'][$i] : $file_name);
		endfor;
		
        $data['loginId'] = $this->session->userdata('loginId');
		
        $this->printJson($this->item->saveProductProcessCycleTime($data));
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

    public function addDieBom(){
        $id = $this->input->post('id');
        $this->data['item_id'] = $id;
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['category_type'=>11,'final_category'=>1]);
        $this->data['rawMaterial'] = $this->item->getItemList(['item_type'=>7]);
        $this->load->view('product_options/die_bom_form',$this->data);
    }

    // 23-07-2024
    public function saveDieBom(){ 
        $data = $this->input->post();
		$errorMessage = array();
		
        if(empty($data['ref_cat_id'])){
            $errorMessage['ref_cat_id'] = "Category is required.";
        }
        if(empty($data['ref_item_id'])){
            $errorMessage['ref_item_id'] = "Item is required.";
        }	
		if(empty($data['qty'])){
            $errorMessage['qty'] = "Bom Qty. is required.";
        }
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:	
			$this->printJson($this->item->saveDieBom($data));
		endif;
    }

    public function dieBomHtml(){
        $data = $this->input->post();
        $dieBomData = $this->item->getDieSetData(['item_id'=>$data['item_id']]);
		$i=1; $tbody='';
        
		if(!empty($dieBomData)):
			foreach($dieBomData as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Die Bom','res_function':'getDieBomHtml','fndelete':'deleteDieBom'}";
				$tbody.= '<tr>
						<td>'.$i++.'</td>
						<td>'.$row->category_name.'</td>
						<td>'.(!empty($row->item_code) ? "[".$row->item_code."] " : "").$row->item_name.'</td>
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

    public function deleteDieBom(){ 
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$this->printJson($this->item->deleteDieBom($data['id']));
		endif;
    }
	
	/*DIE SET*/
	public function addDieSet() {
        $item_id = $this->input->post('id');
        $this->data['item_id'] = $item_id;
        $dieSetData = $this->item->getDieSetData(['item_id'=>$item_id,'group_by'=>'die_kit.ref_cat_id']);
        $dieRegisterData = $this->dieMaster->getDieRegister(['fg_id'=>$item_id]);
           
        /* Category Wise Set */
		$i=1; $catBody=''; 
		if(!empty($dieSetData)):
			foreach($dieSetData as $row):

                $dieList = $this->dieProduction->getDieMasterList(['category_id'=>$row->ref_cat_id, 'fg_id'=>$item_id, 'set_no'=>0]);

				$catBody .= '<tr>
						<td>'.$i++.'</td>
						<td>
                            '.$row->category_name.'
                            <input type="hidden" name="category_id[]" id="category_id'.$row->ref_cat_id.'" value="'.$row->ref_cat_id.'">
                        </td>
						<td>
                            <select name="die_id['.$row->ref_cat_id.']" id="die_id'.$row->ref_cat_id.'" class="form-control select2 req">
                                <option value="">Select Die</option>';
                                if(!empty($dieList)):
                                    foreach($dieList as $die):
                                        $catBody .= '<option value="'.$die->id.'">'.$die->item_code.'</option>';
                                    endforeach;
                                endif;
                            $catBody .= '</select>
                            <div class="error die_id'.$row->ref_cat_id.'"></div>
                        </td>
                    </tr>';
			endforeach;
        else:
            $catBody = '<tr><td colspan="3" class="text-center">No data found.</td></tr>';
		endif;        

        /* Die Wise Set */
        $dieHead = '<tr class="text-center">
                        <th rowspan="2">Sr No.</th>
                        <th colspan="'.count($dieSetData).'">List Of Dies</th>
                        <th rowspan="2" style="width:10%">Action</th>
                    </tr>
                    <tr class="text-center">';
                            foreach ($dieSetData as $row) {
                                $dieHead .= '<th>'.$row->category_name.'</th>';
                            }
        $dieHead .= '</tr>';

        $i=1; $dieBody='';        
		if(!empty($dieRegisterData)):
			foreach($dieRegisterData as $row):

				$dieBody .= '<tr class="text-center dieSetItem_'.$row->id.'">
						<td>
                            '.$row->set_no.'
                            
                            <input type="hidden" name="set_no" id="set_no" value="'.$row->set_no.'">
                            <input type="hidden" name="item_id"  value="'.$row->fg_id.'">
                        </td>';
                        foreach ($dieSetData as $cat) { 
                            $catVal = (isset($row->{$cat->category_code}) ? $row->{$cat->category_code} : '');
                            
                            if(!empty($catVal)) {
                                $dieBody .= '<td>'.$catVal.'</td>';
                            } else
                            {
                                $dieList = $this->dieProduction->getDieMasterList(['category_id'=>$cat->ref_cat_id, 'fg_id'=>$item_id, 'set_no'=>0]);
                                $dieBody .= '<td>
                                <input type="hidden" name="category_id[]" id="category_id" value="'.$cat->ref_cat_id.'">
                                    <select name="die_id['.$cat->ref_cat_id.']" id="die_set_code" class="form-control">
                                        <option value="">Select Die</option>';
                                        if(!empty($dieList)):
                                            foreach($dieList as $die):
                                                $dieBody .= '<option value="'.$die->id.'">'.$die->item_code.'</option>';
                                            endforeach;
                                        endif;
                                    $dieBody .= '</select>
                                    <div class="error die_id'.$cat->ref_cat_id.'"></div>
                                </td>';
                            }
                        }
                        $param = "{postData : {'die_master_id' : '".$row->id."'},'formId':'dieForm','fnsave':'saveDieSet','controller':'productOption'}";
                $dieBody .= '<td>
                                <button type="button" class="btn btn-block waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="dieSetStore('.$param.')" style="height:36px"><i class="fa fa-check"></i> Save</button>
                            </td>
                        </tr>';
			endforeach;
        else:
            $dieBody = '<tr><td colspan="4" class="text-center">No data found.</td></tr>';
		endif;

        $this->data['catBody'] = $catBody;
        $this->data['dieBody'] = $dieBody;
        $this->data['dieHead'] = $dieHead; 

        $this->load->view($this->dieSet,$this->data);
    }

    public function saveDieSet() {
        $data = $this->input->post();
		$errorMessage = array();

		if(!empty($data['category_id'])){
            foreach ($data['category_id'] as $key => $value) {
                if(empty($data['die_id'][$value])){
                    $errorMessage['die_id'.$value] = "Die is required.";
                }
            }
        }
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->dieMaster->saveDieSetData($data));
		endif;
    }

    /* Packing Standard */
    public function addPackingStandard(){
        $id = $this->input->post('id');
        $this->data['item_id'] = $id;
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>9]);
        $this->load->view($this->packingStandard,$this->data);
    }

    public function savePackingStandard(){ 
        $data = $this->input->post();
		$errorMessage = array();
		
        if(empty($data['ref_item_id'])){
            $errorMessage['ref_item_id'] = "Packing Material is required.";
        }		
        if(empty($data['qty'])){
            $errorMessage['qty'] = "Qty is required.";
        }
        if(empty($data['pack_wt'])){
            $errorMessage['pack_wt'] = "Weight is required.";
        }
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->item->savePackingStandard($data));
		endif;
    }

    public function packingStandardHtml(){
        $data = $this->input->post();
        $packData = $this->item->getProductKitData(['item_id'=>$data['item_id'], 'item_type'=>9]);
		$i=1; $tbody='';
        
		if(!empty($packData)):
			foreach($packData as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Process','res_function':'getPackingStandardHtml','fndelete':'deletePackingStandard'}";
                
				$tbody.= '<tr class="text-center">
						<td>'.$i++.'</td>
						<td>'.(!empty($row->item_code) ? '['.$row->item_code.']' : '').$row->item_name.'</td>
						<td>'.$row->qty.'</td>
						<td>'.$row->pack_wt.'</td>
						<td class="text-center">
							<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
						</td>
					</tr>';
			endforeach;
        else:
            $tbody = '<tr><td colspan="5" class="text-center">No data found.</td></tr>';
		endif;
        $this->printJson(['status'=>1,'tbodyData'=>$tbody,'mbOptions'=> $this->getMainBomItems(['item_id'=>$data['item_id']])]);
	}
	
	public function deletePackingStandard(){ 
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$this->printJson($this->item->deletePackingStandard($data['id']));
		endif;
    }
}
?>