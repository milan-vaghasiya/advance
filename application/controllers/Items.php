<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Items extends MY_Controller{
    private $indexPage = "item_master/index";
    private $form = "item_master/form";
    private $itemKitForm = "item_master/item_kit";
    private $productProcessForm = "item_master/product_process";
    private $excel_upload_form = "item_master/excel_upload_form";
    private $insp_param_form = "item_master/insp_param_form";
	private $machineForm = "item_master/machine_form";
    private $gauge_instr_form = "item_master/gauge_instrument_form";
	private $popInspForm = "item_master/pop_insp_form"; 
	private $fgForm = "item_master/fg_form";
	private $consForm = "item_master/cons_form";
	private $rmForm = "item_master/rm_form";
    private $dieBlockForm = "item_master/die_blocks_form";
	private $packingForm = "item_master/packing_form";
	private $fixtureForm = "item_master/fixture_form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Item Master";
		$this->data['headData']->controller = "items";        
	}

    public function list($item_type = 0){
        $this->data['headData']->pageUrl = "items/list/".$item_type;
        $this->data['item_type'] = $item_type;
        $headerName = str_replace(" ","_",strtolower($this->itemTypes[$item_type]));
		$this->data['headData']->pageTitle = $this->itemTypes[$item_type];
        $this->data['tableHeader'] = getMasterDtHeader($headerName);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($item_type = 0,$is_active = 1){
        $data = $this->input->post();
		$data['item_type'] = $item_type;
		$data['is_active'] = $is_active;
		
        $result = $this->item->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->item_type_text = $this->itemTypes[$row->item_type];
            $sendData[] = getProductData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	/* UPDATED BY : AVT DATE:18-12-2024 */
    public function addItem(){
        $data = $this->input->post();
        $this->data['item_type'] = $data['item_type'];
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['category_type'=>$data['item_type'],'final_category'=>1]);
        $this->data['hsnData'] = $this->hsnModel->getHSNList();
        $this->data['materialGrade'] = $this->materialGrade->getMaterialGrades();
		
        if($data['item_type'] == 1){
			$this->data['party_id'] = (!empty($data['party_id']) ?  $data['party_id'] : 0);
            $this->data['is_active'] = (!empty($data['is_active']) ? $data['is_active'] : '1');
			$this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
			$this->data['tcHeadList'] = $this->testType->getTypeList();
            $this->load->view($this->fgForm,$this->data);
        }elseif($data['item_type'] == 2){
            $itemCode = $this->item->getItemCode(2);
            $this->data['item_code'] = 'C'.lpad($itemCode, '5', '0');
            $this->load->view($this->consForm,$this->data);
        }elseif($data['item_type'] == 3){
            $itemCode = $this->item->getItemCode(3);
            $this->data['item_code'] = 'RM'.lpad($itemCode, '5', '0'); 
            $this->load->view($this->rmForm,$this->data);
        }elseif($data['item_type'] == 4){
            $this->data['fgList'] = $this->item->getItemList(['item_type' => 1]);
            $this->load->view($this->fixtureForm,$this->data);
        }elseif($data['item_type'] == 5){
            $this->load->view($this->machineForm,$this->data);
        }elseif($data['item_type'] == 6){
            $this->load->view($this->gauge_instr_form,$this->data);
        }elseif($data['item_type'] == 7){
            $this->load->view($this->dieBlockForm,$this->data);
        }elseif($data['item_type'] == 9){
            $itemCode = $this->item->getItemCode(9);
            $this->data['item_code'] = 'P'.lpad($itemCode, '5', '0');
            $this->load->view($this->packingForm,$this->data);
        }
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['item_name'])){ $errorMessage['item_name'] = "Item Name is required.";}

        if(empty($data['category_id'])){ $errorMessage['category_id'] = "Category is required."; }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(!empty($_FILES['item_image']['name'])):
                $attachment = "";
                $this->load->library('upload');
                //$this->load->library('image_lib');
                
                $_FILES['userfile']['name']     = $_FILES['item_image']['name'];
                $_FILES['userfile']['type']     = $_FILES['item_image']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['item_image']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['item_image']['error'];
                $_FILES['userfile']['size']     = $_FILES['item_image']['size'];

                $imagePath = realpath(APPPATH . '../assets/uploads/item_image/');

                $fileName = preg_replace('/[^A-Za-z0-9]+/', '_', strtolower($_FILES['item_image']['name']));
                $fileName = time();
                $config = ['file_name' => $fileName, 'allowed_types' => 'jpg|jpeg|png|gif|JPG|JPEG|PNG', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

                $this->upload->initialize($config);

                if(!$this->upload->do_upload()):
                    $errorMessage['item_image'] = $fileName . " => " . $this->upload->display_errors();
                else:
                    $uploadData = $this->upload->data();
                    $attachment = $uploadData['file_name'];

                    /* $imgConfig['image_library'] = 'gd2';
                    $imgConfig['source_image'] = $uploadData['full_path'];
                    $imgConfig['maintain_ratio'] = TRUE;
                    $imgConfig['width'] = 640;
                    $imgConfig['height'] = 480;
                    $imgConfig['quality'] = "50%";

                    $this->image_lib->clear();
                    $this->image_lib->initialize($imgConfig);

                    if (!$this->image_lib->resize()) :
                        $errorMessage['item_image'] .= $fileName . " => " . $this->image_lib->display_errors();
                    endif; */
                endif;

                if(!empty($errorMessage['item_image'])):
                    if (file_exists($imagePath . '/' . $fileName)) : unlink($imagePath . '/' . $fileName); endif;
                endif;
                
                $data['item_image'] = $attachment;
                if(!empty($data['item_image'])):
                    if (file_exists($imagePath . '/' . $data['old_image'])) : unlink($imagePath . '/' . $data['old_image']); endif;
                endif;
                
            endif;
            unset($data['old_image']);
            
            if(!empty($data['product_type'])){
                $data['item_type'] = $data['product_type'];
                unset($data['product_type']);
            }
			$data['tc_head'] = (!empty($data['tc_head'])?implode(",",$data['tc_head']):'');
            $data['fg_id'] = (!empty($data['fg_id'])?implode(",",$data['fg_id']):'');
            $data['process_id'] = (!empty($data['process_id'])?implode(",",$data['process_id']):'');
            $this->printJson($this->item->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $itemDetail = $this->item->getItem($data);
        $this->data['item_type'] = $itemDetail->item_type;
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['category_type'=>$itemDetail->item_type,'final_category'=>1]);
        $this->data['hsnData'] = $this->hsnModel->getHSNList();
        $this->data['materialGrade'] = $this->materialGrade->getMaterialGrades();

        if($itemDetail->item_type == 1){
			$this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
			$this->data['tcHeadList'] = $this->testType->getTypeList();
            $this->load->view($this->fgForm,$this->data);
        }elseif($itemDetail->item_type == 2){
            $this->load->view($this->consForm,$this->data);
        }elseif($itemDetail->item_type == 3){
            $this->load->view($this->rmForm,$this->data);
        }elseif($itemDetail->item_type == 4){
            $this->data['fgList'] = $this->item->getItemList(['item_type' => 1]);
            $this->data['processData'] = $this->getProcessList(['process_id'=>$itemDetail->process_id,'fg_id'=>$itemDetail->fg_id]);
            $this->load->view($this->fixtureForm,$this->data);
        }elseif($itemDetail->item_type == 5){
            $this->load->view($this->machineForm,$this->data);
        }elseif($itemDetail->item_type == 6){
            $this->load->view($this->gauge_instr_form,$this->data);
        }elseif($itemDetail->item_type == 7){
            $this->load->view($this->dieBlockForm,$this->data);
        }elseif($itemDetail->item_type == 9){
            $this->load->view($this->packingForm,$this->data);
        }else{
            $this->load->view($this->form,$this->data);
        }
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->delete($id));
        endif;
    }

    public function getItemList(){
        $data = $this->input->post();
        $itemList = $this->item->getItemList($data);
        $this->printJson(['status'=>1,'data'=>['itemList'=>$itemList]]);
    }

    public function getItemDetails(){
        $data = $this->input->post();
        $itemDetail = $this->item->getItem($data);
        $this->printJson(['status'=>1,'data'=>['itemDetail'=>$itemDetail]]);
    }
    
    /* Product Excel Upload */ 
    /* Created By :- Avruti @12-04-2024 */
    public function addProductExcel(){
        $this->load->view($this->excel_upload_form,$this->data);
    }

    /* Created By :- Avruti @12-04-2024 */
    public function saveProductExcel(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['itemData']))
            $errorMessage['itemData'] = "Enter Item detail";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            foreach($data['itemData'] as $row):
                $catData = $this->itemCategory->getCategory(['category_name'=>$row['category_id']]);
                $unitData = $this->item->getUnitNameWiseId(['unit_name'=>$row['unit_id']]);  

                $row['category_id'] = $catData->id;
                $row['unit_id'] = $unitData->id;
                $row['item_type'] = 1;
                $row['packing_standard'] = (!empty($row['packing_standard'] > 0)) ? $row['packing_standard'] : 1 ; 

                $result = $this->item->save($row);
            endforeach;
           
            $this->printJson(['status'=>1,'message'=>'Item saved successfully.']);
        endif;
    }

    public function checkItemDuplicate(){
        $data = $this->input->post();
        $customWhere = "item_name = '".$data['item_name']."' ";
        $itemData = $this->item->getItem(['customWhere'=>$customWhere]);
        $this->printJson(['status'=>1,'item_id'=>(!empty($itemData->id)?$itemData->id:"")]);
    }
	
    /**** Inspection Parameters */
    public function addInspectionParameter(){
        $data = $this->input->post();
        $this->data['item_id'] = $data['id'];
        $this->data['processList'] = $this->item->getProductProcessList(['item_id'=>$data['id']]);
        $this->data['revisionList'] = $this->ecn->getItemRevision(['item_id'=>$data['id']]);
		$this->data['machineList'] = $this->itemCategory->getCategoryList(['category_type'=>5, 'final_category'=>1]);
        $this->load->view($this->insp_param_form,$this->data);
    }

    public function saveInspection(){
        $data = $this->input->post();
        $errorMessage = array();
        $itmData = $this->item->getItem(['id'=>$data['item_id']]);   
		if(isset($data['rev_no']) && $data['rev_no'] == '' && $itmData->item_type == 1){ $errorMessage['rev_no'] = "Revision is required."; }
        if(empty($data['control_method'])){ $errorMessage['control_method'] = "Control Method is required."; }
        elseif(empty($data['process_id']) && in_array($data['control_method'],['IPR,SAR'])){ $errorMessage['process_id'] = "Process is required."; }
        if(empty($data['parameter'])){ $errorMessage['parameter'] = "Parameter is required."; }   
        if(empty($data['specification'])){  $errorMessage['specification'] = "Specification is required."; }
        if(isset($data['control_method']) && $data['control_method'] == 'POP'){
            if(empty($data['category_id'])){$errorMessage['category_id'] = "Category is required."; }
            else{
                $data['category_id'] = implode(",",$data['category_id']);
            }
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if($data['control_method'] != 'POP'){
                $data['control_method'] = implode(",",$data['control_method']);
            }
            $result = $this->item->saveInspection($data);
            $result['process_id'] = (!empty($data['process_id'])?$data['process_id']:'');
            $this->printJson($result);
        endif;
    }

    public function inspectionHtml(){
        $data = $this->input->post(); 
        $revNo = $this->ecn->getEcn(['item_id'=>$data['item_id'],'status'=>2,'single_row'=>1]);
        $rev_no = (!empty($revNo->rev_no))?$revNo->rev_no:'';
        $paramData = $this->item->getInspectionParam(['item_id'=>$data['item_id'],'rev_no'=>$rev_no,'control_method_not'=>"'POP'"]); 
		
        $tbodyData="";$i=1; 
        $processList = array_reduce($paramData, function($processList, $process) { $processList[$process->process_name] = $process; return $processList; }, []);
        foreach ($processList as $process_name=>$processes): 
			$rowParam = "'".$processes->item_id."','".$processes->process_id."','".$processes->rev_no."'"; 
            $tbodyData .= '<button onclick="processDetail('.$rowParam.');" class="nav-tab btn waves-effect waves-light btn-outline-info process_tab" id="prid_'.$processes->process_id.'" style="outline:0px" data-toggle="tab">
							'.(!empty($process_name)?$process_name:'General').'
						</button>';
        endforeach;

        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

    public function getParameterData(){
        $data = $this->input->post();
        $paramData = $this->item->getInspectionParam(['item_id'=>$data['item_id'],'process_id'=>$data['process_id'],'active_rev'=>1,'control_method_not'=>"'POP'"]); 
		$tbodyData="";$i=1; $theadData="";
        $theadData = '<tr>
                        <th style="width:5%;">#</th>
                        <th>Rev No</th>
                        <th>Product</th>
                        <th>Process</th>
                        <th>Machine Tools for Manufacturing</th>
                        <th>Specification</th>
                        <th>Min</th>
                        <th>Max</th>
                        <th>Special Char. Class</th>
                        <th>Instrument</th>
                        <th>Size</th>
                        <th>Frequency</th>
                        <th>Frequency Unit</th>
                        <th>Tool Name</th>
                        <th>RPM</th>
                        <th>Feed</th>
                        <th>Reaction Plan</th>
                        <th>Control Method</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>';

                foreach($paramData as $row): 
                $deleteParam = "{'postData':{'id' : ".$row->id.",'process_id':".$row->process_id."},'res_function':'inspectionHtml','fndelete':'deleteInspection'}";

                $editBtn = "<button type='button' onclick='editInspParam(".json_encode($row).",this);' class='btn btn-sm btn-outline-info waves-effect waves-light btn-sm permission-modify' datatip='Edit'><i class='far fa-edit'></i></button>";
                $tbodyData .= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->rev_no.'</td>
                                <td>'.(($row->param_type == 1)?$row->parameter:'').'</td>
                                <td>'.(($row->param_type == 2)?$row->parameter:'').'</td>
                                <td>'.$row->mc_category.'</td>
                                <td>'.$row->specification.'</td>
                                <td>'.(($row->min == 0.00)?'-':$row->min).'</td>
                                <td>'.(($row->max == 0.00)?'-':$row->max).'</td>
                                <td class="text-center"><img src="'.((!empty($row->char_class))?base_url("/assets/images/symbols/".$row->char_class.'.png'):'').'" style="width:20px;"></td>
                                <td>'.$row->instrument.'</td>
                                <td>'.$row->size.'</td>
                                <td>'.$row->frequency.'</td>
                                <td>'.$row->freq_unit.'</td>
                                <td>'.$row->tool_name.'</td>
                                <td>'.$row->rpm.'</td>
                                <td>'.$row->feed.'</td>
                                <td>'.$row->reaction_plan.'</td>
                                <td>'.$row->control_method.'</td>
                                <td class="text-center">
                                    '.$editBtn.'
                                    <button type="button" onclick="trash('.$deleteParam.');" class="btn btn-outline-danger  btn-sm waves-effect waves-light permission-remove" datatip="Remove"><i class="mdi mdi-trash-can-outline"></i></button>
                                </td>
                            </tr>';
                endforeach;

        $this->printJson(['status'=>1,'theadData'=>$theadData,'tbodyData'=>$tbodyData]);
    }
	
    public function deleteInspection(){
        $data=$this->input->post(); 
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $result = $this->item->deleteInspection($data['id']);
            $result['process_id'] = (!empty($data['process_id'])?$data['process_id']:'');
            $this->printJson($result);
        endif;
    }

    /* Product Inspection Parameter Excel Upload */
    public function createProductInspExcel($item_id){
        $processData = $this->item->getProductProcessList(['item_id'=>$item_id]);
        $machineList = $this->itemCategory->getCategoryList(['category_type'=>5, 'final_category'=>1]);
        $spreadsheet = new Spreadsheet();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        
        $html = "<tr>
            <th>rev_no</th>
            <th>param_type</th>
            <th>parameter</th>
            <th>machine_tool</th>
            <th>specification</th>
            <th>min</th>
            <th>max</th>
            <th>char_class</th>
            <th>instrument</th>
            <th>size</th>
            <th>frequency</th>
            <th>freq_unit</th>
            <th>tool_name</th>
            <th>rpm</th>
            <th>feed</th>
            <th>reaction_plan</th>
            <th>Control_Method</th>
        </tr>"; 
        $exlData = '<table>' . $html . '</table>'; 
        $spreadsheet = $reader->loadFromString($exlData);
        $excelSheet = $spreadsheet->getActiveSheet();
        $excelSheet = $excelSheet->setTitle('Inspection');
        
        $hcol = $excelSheet->getHighestColumn();
        $hrow = $excelSheet->getHighestRow();
        $packFullRange = 'A1:' . $hcol . $hrow;
        foreach (range('A', $hcol) as $col) :
            $excelSheet->getColumnDimension($col)->setAutoSize(true);
        endforeach;
        for ($i = 2; $i <= 5; $i++) {

            /*** Process Code Drop down */
            $objValidation2 = $excelSheet->getCell('B' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"Product,Process"');
            $objValidation2->setShowDropDown(true);
            
            /*$objValidation2 = $excelSheet->getCell('D' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"' . '<pre>'.str_replace('/',' ',implode(',', array_column($machineList, 'category_name'))) . '"');
            $objValidation2->setShowDropDown(true);*/

            $objValidation2 = $excelSheet->getCell('H' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"'.implode(',',array_keys($this->classArray)).'"');
            $objValidation2->setShowDropDown(true);

            $objValidation2 = $excelSheet->getCell('L' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"Hrs,Lot"');
            $objValidation2->setShowDropDown(true);

            $objValidation2 = $excelSheet->getCell('Q' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"IIR,IPR,FIR,SAR"');
            $objValidation2->setShowDropDown(true);
        }

        // Add RM Cutting Sheet
        $rmCuttingData = '<table>' . $html . '</table>';
        $reader->setSheetIndex(1);
        $spreadsheet = $reader->loadFromString($rmCuttingData, $spreadsheet);
        $rmCuttingSheet = $spreadsheet->getSheet(1);
        $rmCuttingSheet->setTitle('RM Cutting');

        $hcol = $rmCuttingSheet->getHighestColumn();
        $hrow = $rmCuttingSheet->getHighestRow();
        $packFullRange = 'A1:' . $hcol . $hrow;
        foreach (range('A', $hcol) as $col) :
            $rmCuttingSheet->getColumnDimension($col)->setAutoSize(true);
        endforeach;

        for ($i = 2; $i <= 5; $i++) {

            /*** Process Code Drop down */
            $objValidation2 = $rmCuttingSheet->getCell('B' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"Product,Process"');
            $objValidation2->setShowDropDown(true);
            
            /* $objValidation2 = $rmCuttingSheet->getCell('D' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"' . implode(',', array_column($machineList, 'category_name')) . '"');
            $objValidation2->setShowDropDown(true); */

            $objValidation2 = $rmCuttingSheet->getCell('H' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"'.implode(',',array_keys($this->classArray)).'"');
            $objValidation2->setShowDropDown(true);

            $objValidation2 = $rmCuttingSheet->getCell('L' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"Hrs,Lot"');
            $objValidation2->setShowDropDown(true);

            $objValidation2 = $rmCuttingSheet->getCell('Q' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"IIR,IPR,FIR,SAR"');
            $objValidation2->setShowDropDown(true);
        }

        $i = 2;
        if(!empty($processData)):
            foreach ($processData as $row) :
            
                $pdfData = '<table>' . $html . '</table>';

                $reader->setSheetIndex($i);

                $spreadsheet = $reader->loadFromString($pdfData, $spreadsheet);

                $row->process_name = trim(preg_replace('/[^A-Za-z0-9\-]/', ' ', $row->process_name));
                $row->process_name = substr(trim(str_replace('-', ' ', $row->process_name)),0,30);
                $spreadsheet->getSheet($i)->setTitle($row->process_name);
                $excelSheet = $spreadsheet->getSheet($i);
                $hcol = $excelSheet->getHighestColumn();
                $hrow = $excelSheet->getHighestRow();
                $packFullRange = 'A1:' . $hcol . $hrow;
                foreach (range('A', $hcol) as $col) :
                    $excelSheet->getColumnDimension($col)->setAutoSize(true);
                endforeach;
                for ($j = 2; $j <= 5; $j++) {

                    /*** Process Code Drop down */
                    $objValidation2 = $excelSheet->getCell('B' . $j)->getDataValidation();
                    $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $objValidation2->setAllowBlank(false);
                    $objValidation2->setShowInputMessage(true);
                    $objValidation2->setShowDropDown(true);
                    $objValidation2->setPromptTitle('Pick from list');
                    $objValidation2->setPrompt('Please pick a value from the drop-down list.');
                    $objValidation2->setErrorTitle('Input error');
                    $objValidation2->setError('Value is not in list');
                    $objValidation2->setFormula1('"Product,Process"');
                    $objValidation2->setShowDropDown(true);

                    $objValidation2 = $excelSheet->getCell('H' . $j)->getDataValidation();
                    $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $objValidation2->setAllowBlank(false);
                    $objValidation2->setShowInputMessage(true);
                    $objValidation2->setShowDropDown(true);
                    $objValidation2->setPromptTitle('Pick from list');
                    $objValidation2->setPrompt('Please pick a value from the drop-down list.');
                    $objValidation2->setErrorTitle('Input error');
                    $objValidation2->setError('Value is not in list');
                    $objValidation2->setFormula1('"'.implode(',',array_keys($this->classArray)).'"');
                    $objValidation2->setShowDropDown(true);

                    $objValidation2 = $excelSheet->getCell('L' . $j)->getDataValidation();
                    $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $objValidation2->setAllowBlank(false);
                    $objValidation2->setShowInputMessage(true);
                    $objValidation2->setShowDropDown(true);
                    $objValidation2->setPromptTitle('Pick from list');
                    $objValidation2->setPrompt('Please pick a value from the drop-down list.');
                    $objValidation2->setErrorTitle('Input error');
                    $objValidation2->setError('Value is not in list');
                    $objValidation2->setFormula1('"Hrs,Lot"');
                    $objValidation2->setShowDropDown(true);

                    $objValidation2 = $excelSheet->getCell('Q' . $j)->getDataValidation();
                    $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $objValidation2->setAllowBlank(false);
                    $objValidation2->setShowInputMessage(true);
                    $objValidation2->setShowDropDown(true);
                    $objValidation2->setPromptTitle('Pick from list');
                    $objValidation2->setPrompt('Please pick a value from the drop-down list.');
                    $objValidation2->setErrorTitle('Input error');
                    $objValidation2->setError('Value is not in list');
                    $objValidation2->setFormula1('"IIR,IPR,FIR,SAR"');
                    $objValidation2->setShowDropDown(true);

                }
                $i++;
            endforeach;
            
        endif;

        $fileDirectory = realpath(APPPATH . '../assets/uploads/product_inspection');
        $fileName = '/product_inspection_' . time() . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileDirectory . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        redirect(base_url('assets/uploads/product_inspection') . $fileName);
    }
	
    public function importProductExcel(){
        $postData = $this->input->post();
        $insp_excel = '';
        if (isset($_FILES['insp_excel']['name']) || !empty($_FILES['insp_excel']['name'])) :
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['insp_excel']['name'];
            $_FILES['userfile']['type']     = $_FILES['insp_excel']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['insp_excel']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['insp_excel']['error'];
            $_FILES['userfile']['size']     = $_FILES['insp_excel']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/product_inspection');
            $config = ['file_name' => "inspection_" . $_FILES['userfile']['name'], 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

            $this->upload->initialize($config);
            if (!$this->upload->do_upload()) :
                $errorMessage['insp_excel'] = $this->upload->display_errors();
                $this->printJson(["status" => 0, "message" => $errorMessage]);
            else :
                $uploadData = $this->upload->data();
                $insp_excel = $uploadData['file_name'];
            endif;
            
            if (!empty($insp_excel)) {
                $processData = [];
                $processData = $this->item->getProductProcessList(['item_id'=>$postData['item_id']]);
                $prsDt = new stdClass(); 
                $prsDt->process_name = 'Inspection';
                $processData[] = $prsDt;

                $prsDt1 = new stdClass(); 
                $prsDt1->process_name = 'RM Cutting';
                $rmPrs = $this->process->getProcess(['process_name'=>$prsDt1->process_name]);
                $prsDt1->process_id = (!empty($rmPrs) ? $rmPrs->id : 0);
                $processData[] = $prsDt1;
                $revData = $this->ecn->getItemRevision(['item_id'=>$postData['item_id']]);
				
                $revArr = array_column($revData , 'rev_no');
                $row = 0;$paramData=[];
                foreach ($processData as $prs) :
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($imagePath . '/' . $insp_excel);
                    
                    $prs->process_name = trim(preg_replace('/[^A-Za-z0-9\-]/', ' ', $prs->process_name));
                    $prs->process_name = substr(trim(str_replace('-', ' ', $prs->process_name)),0,30);
                    
					$exl_sheet = $spreadsheet->getSheetByName($prs->process_name);
                    $fileData = (!empty($exl_sheet) ? array($exl_sheet->toArray(null, true, true, true)) : []);
                    $fieldArray = array();
                    if (!empty($fileData)) {
                        $fieldArray = $fileData[0][1];
                        for ($i = 2; $i <= count($fileData[0]); $i++) {
                            $rowData = array();
                            $c = 'A';
                            foreach ($fileData[0][$i] as $key => $colData) :
								$field_val = strtolower($fieldArray[$c]);
								$rowData[$field_val] = $colData;
								$c++;
                            endforeach;
							if(empty($revArr)){
                                $this->printJson(['status' => 0, 'message' => 'Revision No. is required.']);
                            }
                            if(isset($rowData['rev_no']) && $rowData['rev_no'] != ''){
                                if(!in_array($rowData['rev_no'], $revArr)){
                                    $this->printJson(['status' => 0, 'message' => 'Revision No Mismatch...!']);
                                }
                            }
                            if(!empty($rowData['parameter'])):
								$mcData = (!empty($rowData['machine_tool']) ? $this->itemCategory->getCategory(['category_name'=>trim($rowData['machine_tool'])]) : []);

                                $paramData[]=[
                                    'id'=>'',
                                    'process_id'=>(!empty($prs->process_id)?$prs->process_id:''),
                                    'item_id'=>$postData['item_id'],
                                    'rev_no'=>(isset($rowData['rev_no']) && $rowData['rev_no'] != '' ? $rowData['rev_no'] : NULL),
                                    'param_type'=>(!empty($rowData['param_type'] && $rowData['param_type'] == 'Product') ? 1 : 2),
                                    'parameter'=>$rowData['parameter'],
                                    'machine_tool'=>(!empty($mcData->id) ? $mcData->id : 0),
                                    'specification'=>$rowData['specification'],
                                    'min'=>(!empty($rowData['min']) ? $rowData['min'] : NULL),
                                    'max'=>(!empty($rowData['max']) ? $rowData['max'] : NULL),
                                    'char_class'=>(!empty($rowData['char_class']) ? $rowData['char_class'] : NULL),
                                    'instrument'=>$rowData['instrument'],
                                    'size'=>(!empty($rowData['size']) ? $rowData['size'] : NULL),
                                    'frequency'=>(!empty($rowData['frequency']) ? $rowData['frequency'] : NULL),
                                    'freq_unit'=>(!empty($rowData['freq_unit']) ? $rowData['freq_unit'] : NULL),
                                    'control_method'=>(!empty($rowData['control_method']) ? $rowData['control_method'] : NULL),
									'reaction_plan'=>(!empty($rowData['reaction_plan']) ? $rowData['reaction_plan'] : NULL),
                                    'feed'=>(!empty($rowData['feed']) ? $rowData['feed'] : NULL),
                                    'rpm'=>(!empty($rowData['rpm']) ? $rowData['rpm'] : NULL),
                                    'tool_name'=>(!empty($rowData['tool_name']) ? $rowData['tool_name'] : NULL),
                                    'created_by'=>$this->loginId,
                                    'created_at'=>date("Y-m-d H:i:s"),
                                ]; 
                                $row++;
                            endif;
                        }
                    }
                endforeach;
				
                if(!empty($paramData)){
                    $result = $this->item->saveInspectionParamExcel($paramData);
                    $this->printJson($result);
                }else{
                    $this->printJson(['status' => 0, 'message' => 'Data not found...!']);
                }
                
            } else {
                $this->printJson(['status' => 0, 'message' => 'Data not found...!']);
            }
        else :
            $this->printJson(['status' => 0, 'message' => 'Please Select File!']);
        endif;
    }
	/***************************/

    public function printControlPlan(){
        $data = $this->input->post();
        $this->data['item_id'] = $data['item_id'];
        $this->data['revisionList'] = $this->ecn->getItemRevision(['item_id'=>$data['item_id']]);
        $this->load->view('item_master/cp_print_view',$this->data);
    }

	public function getControlPlanPrint($item_id='',$rev_no=''){
        $paramData = $this->item->getInspectionParam(['item_id'=>$item_id,'rev_no'=>$rev_no,'order_by'=>'process_no']);
        $revisionData = $this->ecn->getItemRevision(['item_id'=>$item_id,'status'=>'2,3']);
        $tcData = $this->materialGrade->getTcMasterData(['item_id'=>$paramData[0]->item_id,'test_name'=>'Chemical Testing']);

        $check = '<img src="'.base_url('assets/images/check-square.png').'" style="width:15px;display:inline-block;vertical-align:middle;">';
        $unCheck = '<img src="'.base_url('assets/images/uncheck-square.png').'" style="width:15px;display:inline-block;vertical-align:middle;">';
		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();

		$logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $letter_head =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');

        $processList = array_reduce($paramData, function($processList, $process) { $processList[$process->process_name][] = $process; return $processList; }, []);
        $mpdf = new \Mpdf\Mpdf();
        $pdfFileName='CP'.$item_id.'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));

        $pData = '';
        if (!empty($tcData)) {
            foreach($tcData as $row) {
                $parameter = json_decode($row->parameter);
                if ($parameter) {
                    foreach($parameter as $key => $value) {
                        if (!empty($value->max) && $value->max !== '-') {
                            $minText = (!empty($value->min) && $value->min !== '-') ? $value->min . '–' : '';
                            $maxtext = (!empty($value->max) && empty($minText)) ? ' Max ' : '';
                            $max = $value->max. $maxtext;
                            $pData .= $value->param . ' = ' . $minText . $max . ' <br>';
                        }
                    }
                }
            }
            $pData = rtrim($pData, '<br> ');
        }
        $grade = (!empty($reqData->material_grade) ? '<br><b>Material : </b>'. (!empty($paramData[0]->rm_grade)?$paramData[0]->rm_grade:'') : '');
        $parameter = (!empty($pData) ? '<br>'.$pData : '');
       
        $staticTable = ' <table>
					<tr>
						<td>
							<img src="'.$letter_head.'" class="img">
						</td>
					</tr>
                    <tr>
                        <td class="text-center org_title" style="font-size:1.5rem;">Control Plan</td>
                    </tr>
				</table>
                
                <table class="table tag_print_table text-left" style="margin-top:5px;">
                    <tr>
                        <th style="width:15%">Supplier Name</th>
                        <td style="width:20%">'.$companyData->company_name.'</td>
                        <th style="width:15%">Customer Name</th>
                        <td style="width:20%">'.(!empty($processes[0]->party_name)?$processes[0]->party_name:'').'</td>
                        <th style="width:15%">Control Plan No</th>  
						<td style="width:15%">'.'CP/'.(!empty($processes[0]->item_code)?$processes[0]->item_code:'').'/'.(!empty($processes[0]->cust_rev_no)?$processes[0]->cust_rev_no:'').'/'.(!empty($processes[0]->revNo)?$processes[0]->revNo:'').'</td>
                    </tr>
                    <tr>
                        <th>Cust. Drg. Rev. No.</th>
                        <td>'.(!empty($processes[0]->cust_rev_no)?$processes[0]->cust_rev_no:'').'</td>
                        <th>Cust. Part No.</th>
                         <td>'.(!empty($processes[0]->drw_no)?$processes[0]->drw_no:'').'</td>
                        <th>Date(Org)</th>
                        <td>'.(!empty($processes[0]->created_at)?formatDate($processes[0]->created_at):'').'</td>
                    </tr>
                    <tr>
                        <th>ATPL Part No.</th>
                        <td>'.(!empty($processes[0]->item_code)?$processes[0]->item_code:'').'</td>
                        <th>Material Used</th>
                        <td>'.(!empty($processes[0]->material_grade)?$processes[0]->material_grade:'').'</td>
                        <th>CP Rev. No. & Date</th>
						<td>'.(!empty($processes[0]->revNo)?$processes[0]->revNo:'').'/'.(!empty($processes[0]->rev_date)?formatDate($processes[0]->rev_date):'').'</td>
                    </tr>
                    <tr>
                        <th colspan="2">'.((!empty($processes[0]->mfg_status) && $processes[0]->mfg_status == 'Prototype') ? $check : $unCheck).' Prototype &nbsp; &nbsp; &nbsp;
                        '.((!empty($processes[0]->mfg_status) && $processes[0]->mfg_status == 'Pre Launch') ? $check : $unCheck).' Pre Launch &nbsp; &nbsp; &nbsp;
                        '.((!empty($processes[0]->mfg_status) && $processes[0]->mfg_status == 'Reguler') ? $check : $unCheck).' Reguler 
                        </th>
                        <th>Core Team</th>
                        <td>'.(!empty($processes[0]->core_team)?$processes[0]->core_team:'').'</td>
                        <th>Key Contact</th>
                        <td>'.(!empty($processes[0]->key_contact)?$processes[0]->key_contact:'').'</td>
                    </tr>
                      <tr>
                        <th>Cust. Engineering Approval Date</th>
                        <td>'.(!empty($processes[0]->eng_approve_date)?formatDate($processes[0]->eng_approve_date):'').'</td>
                        <th>Cust. Quality Approval Date</th>
                        <td>'.(!empty($processes[0]->quality_approve_date)?formatDate($processes[0]->quality_approve_date):'').'</td>
                        <th>Other Approval Date</th>
                        <td>'.(!empty($processes[0]->other_approve_date)?formatDate($processes[0]->other_approve_date):'').'</td>
                    </tr>
                    <tr>
                        <th>Part Description</th>
                        <td colspan="2">'.(!empty($processes[0]->item_name)?$processes[0]->item_name:'').'</td>
                        <th>Process Name</th>
                        <td colspan="2">'.(!empty($processes[0]->process_name)?$processes[0]->process_name:'').'</td>
                    </tr>
                </table>
                <table class="table item-list-bb" style="margin-top:10px">
                    <thead>
                        <tr class="bg-light">
                            <th rowspan="2">Process Description</th>
                            <th rowspan="2">Machine, Device, Jigs, Tools for Manufacturing</th>
                            <th colspan="7">Characteristics</th>
                            <th colspan="4">Methods</th>
                            <th rowspan="2">Reaction Plan</th>
                        </tr>
                        <tr class="bg-light">
                            <th>Sr. No.</th>
                            <th>Product Characteristic</th>
                            <th>Process Characteristic</th> 
                            <th>Special Char. Class</th>
                            <th style="width:130px;">Product / Process Specification / Tolerances</th>
                            <th>Min</th> 
                            <th>Max</th>
                            <th>Evaluation Measurement Techniques</th>
                            <th>Error Proofing</th>
                            <th>Sample Size / Frequency</th>
                            <th>Control Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td rowspan="4">Raw Material receipt, Inspection & Storage</td>
                            <td rowspan="2">Dimension Inspection</td>
                            <td>1</td>
                            <td>Bar Diameter</td>
                            <td></td>
                            <td></td>
                            <td>'.(!empty($paramData[0]->dia)?$paramData[0]->dia:'').'</td>
                            <td></td>
                            <td></td>
                            <td>Vernier Caliper</td>
                            <td>-</td>
                            <td rowspan="2">5 Nos./Per Batch</td>
                            <td rowspan="2">Approved steel mill, supplier TC & receiving material test record</td>
                            <td rowspan="4">Indentify NC Product, Kept Seperatly, Segregate and inform to suppiler for decision
                                (**Dia of raw material will be acc. To availability stock of material)
                            </td>
                        </tr>
                        <tr class="text-center">
                            <td>2</td>
                            <td>Bar Color Label/Code</td>
                            <td></td>
                            <td></td>
                            <td>'.(!empty($paramData[0]->rm_color)?$paramData[0]->rm_color:'').'</td>
                            <td></td>
                            <td></td>
                            <td>Visually</td>
                            <td>-</td>
                          
                           
                        </tr>
                        <tr class="text-center">
                            <td>Chemical Analysis</td>
                            <td>3</td>
                            <td>Chemical Composition</td>
                            <td></td>
                            <td></td>
                            <td class="text-left"> '.$grade .$parameter.'</td>
                            <td></td>
                            <td></td>
                            <td>Spectro Analysis</td>
                            <td>-</td>
                            <td>100%/Per Batch</td>
                            <td>Receving Material Test Record QA-F-06</td>
                          
                        </tr>
                        <tr class="text-center">
                            <td>Visual Inspection</td>
                            <td>4</td>
                            <td>Appereance</td>
                            <td></td>
                            <td></td>
                            <td>Free From Laps, Seam, Crack.</td>
                            <td></td>
                            <td></td>
                            <td>Visually</td>
                            <td>-</td>
                            <td>100%/Per Batch</td>
                            <td>Receving Material Test Record QA-F-06</td>
                           
                        </tr>
                    </tbody>
                </table>';

        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo,0.05,array(45,45));
        $mpdf->showWatermarkImage = true;
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('L','','','','',10,5,5,5,5,5,'','','','','','','','','','A4-L');
        $mpdf->WriteHTML($staticTable);

        foreach ($processList as $process_name=>$processes):
            $pdfData ='<table>
					<tr>
						<td>
							<img src="'.$letter_head.'" class="img">
						</td>
					</tr>
                    <tr>
                        <td class="text-center org_title" style="font-size:1.5rem;">Control Plan</td>
                    </tr>
				</table>
                
                <table class="table tag_print_table text-left" style="margin-top:5px;">
                    <tr>
                        <th style="width:15%">Supplier Name</th>
                        <td style="width:20%">'.$companyData->company_name.'</td>
                        <th style="width:15%">Customer Name</th>
                        <td style="width:20%">'.(!empty($processes[0]->party_name)?$processes[0]->party_name:'').'</td>
                        <th style="width:15%">Control Plan No</th>  
						<td style="width:15%">'.'CP/'.(!empty($processes[0]->item_code)?$processes[0]->item_code:'').'/'.(!empty($processes[0]->cust_rev_no)?$processes[0]->cust_rev_no:'').'/'.(!empty($processes[0]->revNo)?$processes[0]->revNo:'').'</td>
                    </tr>
                    <tr>
                        <th>Cust. Drg. Rev. No.</th>
                        <td>'.(!empty($processes[0]->cust_rev_no)?$processes[0]->cust_rev_no:'').'</td>
                        <th>Cust. Part No.</th>
                         <td>'.(!empty($processes[0]->drw_no)?$processes[0]->drw_no:'').'</td>
                        <th>Date(Org)</th>
                        <td>'.(!empty($processes[0]->created_at)?formatDate($processes[0]->created_at):'').'</td>
                    </tr>
                    <tr>
                        <th>ATPL Part No.</th>
                        <td>'.(!empty($processes[0]->item_code)?$processes[0]->item_code:'').'</td>
                        <th>Material Used</th>
                        <td>'.(!empty($processes[0]->material_grade)?$processes[0]->material_grade:'').'</td>
                        <th>CP Rev. No. & Date</th>
						<td>'.(!empty($processes[0]->revNo)?$processes[0]->revNo:'').'/'.(!empty($processes[0]->rev_date)?formatDate($processes[0]->rev_date):'').'</td>
                    </tr>
                    <tr>
                        <th colspan="2">'.((!empty($processes[0]->mfg_status) && $processes[0]->mfg_status == 'Prototype') ? $check : $unCheck).' Prototype &nbsp; &nbsp; &nbsp;
                        '.((!empty($processes[0]->mfg_status) && $processes[0]->mfg_status == 'Pre Launch') ? $check : $unCheck).' Pre Launch &nbsp; &nbsp; &nbsp;
                        '.((!empty($processes[0]->mfg_status) && $processes[0]->mfg_status == 'Reguler') ? $check : $unCheck).' Reguler 
                        </th>
                        <th>Core Team</th>
                        <td>'.(!empty($processes[0]->core_team)?$processes[0]->core_team:'').'</td>
                        <th>Key Contact</th>
                        <td>'.(!empty($processes[0]->key_contact)?$processes[0]->key_contact:'').'</td>
                    </tr>
                      <tr>
                        <th>Cust. Engineering Approval Date</th>
                        <td>'.(!empty($processes[0]->eng_approve_date)?formatDate($processes[0]->eng_approve_date):'').'</td>
                        <th>Cust. Quality Approval Date</th>
                        <td>'.(!empty($processes[0]->quality_approve_date)?formatDate($processes[0]->quality_approve_date):'').'</td>
                        <th>Other Approval Date</th>
                        <td>'.(!empty($processes[0]->other_approve_date)?formatDate($processes[0]->other_approve_date):'').'</td>
                    </tr>
                    <tr>
                        <th>Part Description</th>
                        <td colspan="2">'.(!empty($processes[0]->item_name)?$processes[0]->item_name:'').'</td>
                        <th>Process Name</th>
                        <td colspan="2">'.(!empty($processes[0]->process_name)?$processes[0]->process_name:'').'</td>
                    </tr>
                </table>
                
                <table class="table item-list-bb" style="margin-top:10px">
                        <thead>
                            <tr class="bg-light">
                                <th rowspan="2">Process Description</th>
                                <th rowspan="2">Machine, Device, Jigs, Tools for Manufacturing</th>
                                <th colspan="7">Characteristics</th>
                                <th colspan="4">Methods</th>
                                <th rowspan="2">Reaction Plan</th>
                            </tr>
                            <tr class="bg-light">
                                <th>Sr. No.</th>
                                <th>Product Characteristic</th>
                                <th>Process Characteristic</th> 
                                <th>Special Char. Class</th>
                                <th>Product / Process Specification / Tolerances</th>
                                <th>Min</th> 
                                <th>Max</th>
                                <th>Evaluation Measurement Techniques</th>
                                <th>Error Proofing</th>
                                <th>Sample Size / Frequency</th>
                                <th>Control Method</th>
                            </tr>
                        </thead>
                            <tbody>';
                              
                            $i = 1;
                            foreach($processes AS $row):
                                $pdfData .= ' 
                                <tr class="text-center">
                                    <td>'.$row->process_name.'</td>
                                    <td>'.$row->mc_category.'</td>
                                    <td>'.$i++.'</td>
                                    <td>'.(($row->param_type == 1)?$row->parameter:'-').'</td>
                                    <td>'.(($row->param_type == 2)?$row->parameter:'-').'</td>
                                    <td>'.((!empty($row->char_class))?'<img src="'.base_url("/assets/images/symbols/".$row->char_class.'.png').'" style="width:20px;">':'').'</td>
                                    <td>'.$row->specification.'</td>
                                    <td>'.$row->min.'</td>
                                    <td>'.$row->max.'</td>
                                    <td>'.$row->instrument.'</td>
                                    <td>-</td>
                                    <td>'.$row->size.'/'.$row->frequency.' '.$row->freq_unit.'</td>
                                    <td>'.$row->control_method.'</td>
                                    <td>'.$row->reaction_plan.'</td>
                                </tr>';
                            endforeach;
                            
            $pdfData .='</tbody> </table>';

            $pdfData .= '<table class="table item-list-bb" style="margin-top:10px">
                <thead class="thead-info">
                    <tr class="text-center bg-light">
                        <th>#</th>
                        <th>Drawing No.</th>
                        <th>Cust. Rev. No.</th>
                        <th>Cust. Rev. Date</th>
                        <th>ATL Rev. No.</th>
                        <th>ATL Rev. Date</th>
                    </tr>
                </thead>
                <tbody>';                                
                if(!empty($revisionData)):
                    $j=1;
                    foreach($revisionData as $row):
                        $pdfData.= '<tr class="text-center">
                            <td>'.$j++.'</td>
                            <td>'.$row->drw_no.'</td>
                            <td>'.$row->cust_rev_no.'</td>
                            <td>'.formatDate($row->cust_rev_date).'</td>
                            <td>'.$row->rev_no.'</td>
                            <td>'.formatDate($row->rev_date).'</td>
                        </tr>';
                    endforeach;
                endif;
                $pdfData .= '</tbody>
            </table>';
        
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo,0.05,array(45,45));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetProtection(array('print'));
            $mpdf->AddPage('L','','','','',10,5,5,5,5,5,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData); 
        endforeach;
		
		$mpdf->Output($pdfFileName,'I');
    }
    	
    /** POP Inspection parameter */
    public function addPopInspection(){
        $id=$this->input->post('id');
        $this->data['item_id'] = $id;
        $this->data['catData'] = $this->itemCategory->getCategoryList(['category_type'=>11,'final_category'=>1]);
        $this->load->view($this->popInspForm,$this->data);
    }
	
	public function popInspectionHtml(){
        $data = $this->input->post();
        $paramData = $this->item->getInspectionParam(['item_id'=>$data['item_id'],'control_method'=>'POP']);
        $tbodyData="";$i=1; 
        if(!empty($paramData)):
            $i=1;
            foreach($paramData as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id."},'res_function':'popInspectionHtml','fndelete':'deleteInspection'}";
                $tbodyData.= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$row->category_name.'</td>
                            <td>'.$row->parameter.'</td>
                            <td>'.$row->specification.'</td>
                            <td>'.$row->min.'</td>
                            <td>'.$row->max.'</td>
                            <td>'.$row->instrument.'</td>
                            <td class="text-center">
							<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
						</td>
                    </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="8" style="text-align:center;">No Data Found</td></tr>';
        endif;

        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

    /**** Upload Item Excel */
	public function uploadItemExcel(){
        $data = $this->input->post();
        $this->data['item_type'] = $data['item_type'];
        $this->load->view('item_master/item_excel_form',$this->data);
    }

	public function createItemMasterExcel($item_type){
        $categoryList = $this->itemCategory->getCategoryList(['category_type'=>$item_type,'final_category'=>1]);
        $unitData = $this->item->itemUnits();
        $spreadsheet = new Spreadsheet();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $html = '<tr class="text-center">
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Category</th>                
                <th>UOM</th>
                <th>HSN Code</th>
                <th>GST %</th>';

        if (in_array($item_type, [1,3,7])) {
            $html .= '<th>Material Grade</th>';
        }
        if ($item_type == 1) {
            $html .= '<th>Weight (Kg)</th>';                    
        }

        if (in_array($item_type, [1,2,5,6,9])) {
            $html .= '<th>Price</th>';                    
        }

        if (in_array($item_type,[1,2,7,9])) {
            $html .= '<th>Size</th>';
        }

        if (!in_array($item_type,[3,7])) { 
            $html .= '<th>Make/Brand</th>';
        }

        if (in_array($item_type,[2,9])) { 
            $html .= '<th>Serial Number</th>';
        }

        if ($item_type == 2) {
            $html .= '<th>No. of Corner</th>
                    <th>Dia</th>
                    <th>Length (mm)</th>
                    <th>Flute Length (mm)</th>';
        }

        if (in_array($item_type,[3,7])) {
            $html .= '<th>Section (Dia)</th>';
        }

        if ($item_type == 6) {
            $html .=  '<th>Size/Range</th>
                    <th>Permissible Error</th>
                    <th>Calibration Req?</th>
                    <th>Cali. Frequency (Month)</th>
                    <th>Cali.Reminder (Days Before)</th>';
        }

        if ($item_type == 5) {
            $html .=  '<th>Serial Number</th>
					<th>Installed On</th>
                    <th>Pre. Maintenance?</th>
                    <th>Plan Days</th>
                    <th>Specification</th>';
        }else{
            $html .=  '<th>Description</th>';
        }
                
        $html .= '</tr>';

        $exlData = '<table>' . $html . '</table>';
        $spreadsheet = $reader->loadFromString($exlData);
        $excelSheet = $spreadsheet->getActiveSheet();
        $excelSheet = $excelSheet->setTitle($this->itemTypes[$item_type]);
        
        $hcol = $excelSheet->getHighestColumn();
        $hrow = $excelSheet->getHighestRow();
        $packFullRange = 'A1:' . $hcol . $hrow;
        foreach (range('A', $hcol) as $col) :
            $excelSheet->getColumnDimension($col)->setAutoSize(true);
        endforeach;
        for ($i = 2; $i <= 5; $i++) {            
    
            // Apply Category Drop-Down
            $this->applyDropDownValidation($excelSheet, 'C' . $i, $categoryList, 'category_name', 'Pick from list', 'Please pick a value from the drop-down list.', 'Input error', 'Value is not in list');
            
            // Apply Unit Drop-Down
            $this->applyDropDownValidation($excelSheet, 'D' . $i, $unitData, 'unit_name', 'Pick from list', 'Please pick a value from the drop-down list.', 'Input error', 'Value is not in list');
        }

        $fileDirectory = realpath(APPPATH . '../assets/uploads/product_inspection');
        $fileName = '/'.str_replace('_', ' ', $this->itemTypes[$item_type]).'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileDirectory . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        redirect(base_url('assets/uploads/product_inspection') . $fileName);
    }
	
    public function applyDropDownValidation($excelSheet, $cell, $listData, $columnName, $promptTitle, $promptMessage, $errorTitle, $errorMessage) {
        $objValidation = $excelSheet->getCell($cell)->getDataValidation();
        $objValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $objValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        $objValidation->setAllowBlank(false);
        $objValidation->setShowInputMessage(true);
        $objValidation->setShowDropDown(true);
        $objValidation->setPromptTitle($promptTitle);
        $objValidation->setPrompt($promptMessage);
        $objValidation->setErrorTitle($errorTitle);
        $objValidation->setError($errorMessage);
        $objValidation->setFormula1('"' . implode(',', array_column($listData, $columnName)) . '"');
        return $objValidation;
    }  
    
    public function saveUploadedExcel(){
        $data = $this->input->post();$postData = [];

        if(!isset($data['item_code'])){
            $errorMessage['general_error'] = "Item Detail is required."; 

        }else{
            $i=1;
            foreach($data['item_code'] AS $key=>$item_code){
                if(empty($item_code)){
                    $errorMessage['item_code'.$i] = "Code is required."; 
                }
                if(empty($data['item_name'][ $key])){
                    $errorMessage['item_name'.$i] = "Name is required."; 
                }
                $catData = ((!empty($data['category_name'][$key])) ? $this->itemCategory->getCategory(['category_name'=>$data['category_name'][$key]]) : []);
                $mtData = ((!empty($data['material_grade'][$key])) ? $this->materialGrade->getMaterial(['material_grade'=>$data['material_grade'][$key]]) : []);

                $postData[] = [
                    'id' => '',
                    'item_type' => (!empty($data['item_type']) ? $data['item_type'] : 0),
                    'item_code' => (!empty($data['item_code'][$key]) ? $data['item_code'][$key] : NULL),
                    'item_name' => (!empty($data['item_name'][$key]) ? $data['item_name'][$key] : NULL),
                    'category_id' => (!empty($catData->id) ? $catData->id : 0),
                    'uom' => (!empty($data['uom'][$key]) ? $data['uom'][$key] : NULL),
                    'hsn_code' => (!empty($data['hsn_code'][$key]) ? $data['hsn_code'][$key] : NULL),
                    'gst_per' => (!empty($data['gst_per'][$key]) ? $data['gst_per'][$key] : 0),
                    'part_no' => (!empty($data['part_no'][$key]) ? $data['part_no'][$key] : NULL),
                    'grade_id' => (!empty($mtData->id) ? $mtData->id : 0),
                    'wt_pcs' => (!empty($data['wt_pcs'][$key]) ? $data['wt_pcs'][$key] : 0),
                    'price' => (!empty($data['price'][$key]) ? $data['price'][$key] : 0),
                    'size' => (!empty($data['size'][$key]) ? $data['size'][$key] : NULL),
                    'make_brand' => (!empty($data['make_brand'][$key]) ? $data['make_brand'][$key] : NULL),
                    'prev_maint_req' => (!empty($data['prev_maint_req'][$key]) ? $data['prev_maint_req'][$key] : 'No'),
                    'plan_days' => (!empty($data['plan_days'][$key]) ? $data['plan_days'][$key] : 0), 					
                    'no_corner' => (!empty($data['no_corner'][$key]) ? $data['no_corner'][$key] : 0),
                    'dia' => (!empty($data['dia'][$key]) ? $data['dia'][$key] : 0),
                    'length' => (!empty($data['length_mm'][$key]) ? $data['length_mm'][$key] : 0),
                    'flute_length' => (!empty($data['flute_length'][$key]) ? $data['flute_length'][$key] : 0),
                    'installation_year' => (!empty($data['installation_year'][$key]) ? $data['installation_year'][$key] : NULL),                    
                    'permissible_error' => (!empty($data['permissible_error'][$key]) ? $data['permissible_error'][$key] : NULL),
                    'cal_required' => (!empty($data['cal_required'][$key]) ? $data['cal_required'][$key] : 'YES'),
                    'cal_freq' => (!empty($data['cal_freq'][$key]) ? $data['cal_freq'][$key] : 0),
                    'cal_reminder' => (!empty($data['cal_reminder'][$key]) ? $data['cal_reminder'][$key] : 0),
                    'description' => (!empty($data['description'][$key]) ? $data['description'][$key] : NULL),
                ];
                $i++;
            }
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->item->saveItemExcel($postData));
        endif;
    }

    public function checkDuplicateItems(){
        $data = $this->input->post();
        $customWhere = "item_name = '".$data['item_name']."'";
        $customWhere .= (!empty($data['item_code']) ? " OR item_code = '".$data['item_code']."'" : '');

        if(!empty($data['item_type']) && in_array($data['item_type'],[2,9,3])){
            $postData = ['item_types'=>$data['item_type'], 'customWhere'=>$customWhere];
        }else{
            $postData = ['item_types'=>$data['item_type'], 'item_name'=>$data['item_name'], 'item_code'=>$data['item_code']];
        }
        $itemData = $this->item->getItem($postData);
        $this->printJson(['status'=>1,'item_id'=>(!empty($itemData->id) ? $itemData->id : "")]);
    }

    /* POP Inspection Parameter Excel Upload */
    public function createPopInspExcel($item_id){
        $categoryList = $this->itemCategory->getCategoryList(['category_type'=>11,'final_category'=>1]);
        $spreadsheet = new Spreadsheet();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        
        $html = "<tr>
            <th>category_name</th>
            <th>parameter</th>
            <th>specification</th>
            <th>min</th>
            <th>max</th>
            <th>instrument</th>
        </tr>";

        $exlData = '<table>' . $html . '</table>';
        $spreadsheet = $reader->loadFromString($exlData);
        $excelSheet = $spreadsheet->getActiveSheet();
        $excelSheet = $excelSheet->setTitle('POP Inspection');
        
        $hcol = $excelSheet->getHighestColumn();
        $hrow = $excelSheet->getHighestRow();
        $packFullRange = 'A1:' . $hcol . $hrow;
        foreach (range('A', $hcol) as $col) :
            $excelSheet->getColumnDimension($col)->setAutoSize(true);
        endforeach;
        for ($i = 2; $i <= 5; $i++) {
            // Apply Category Drop-Down
            $this->applyDropDownValidation($excelSheet, 'A' . $i, $categoryList, 'category_name', 'Pick from list', 'Please pick a value from the drop-down list.', 'Input error', 'Value is not in list');
        }

        $fileDirectory = realpath(APPPATH . '../assets/uploads/pop_inspection');
        $fileName = '/pop_inspection_'.time().'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileDirectory . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        redirect(base_url('assets/uploads/pop_inspection') . $fileName);
    }
	
    public function importPopExcel(){
        $postData = $this->input->post();
        $insp_excel = '';
        if (isset($_FILES['insp_excel']['name']) || !empty($_FILES['insp_excel']['name'])) :
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['insp_excel']['name'];
            $_FILES['userfile']['type']     = $_FILES['insp_excel']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['insp_excel']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['insp_excel']['error'];
            $_FILES['userfile']['size']     = $_FILES['insp_excel']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/pop_inspection');
            $config = ['file_name' => $_FILES['userfile']['name'], 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

            $this->upload->initialize($config);
            if (!$this->upload->do_upload()) :
                $errorMessage['insp_excel'] = $this->upload->display_errors();
                $this->printJson(["status" => 0, "message" => $errorMessage]);
            else :
                $uploadData = $this->upload->data();
                $insp_excel = $uploadData['file_name'];
            endif;
            
            if (!empty($insp_excel)) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($imagePath . '/' . $insp_excel);
                $exl_sheet = $spreadsheet->getSheetByName('POP Inspection');
                $fileData = (!empty($exl_sheet) ? array($exl_sheet->toArray(null, true, true, true)) : []);
            
                $fieldArray = array();
                if (!empty($fileData)) {
                    $fieldArray = $fileData[0][1];
                    for ($i = 2; $i <= count($fileData[0]); $i++) {
                        $rowData = array();
                        $c = 'A';
                        foreach ($fileData[0][$i] as $key => $colData) :
                                $field_val = strtolower($fieldArray[$c]);
                                $rowData[$field_val] = $colData;
                                $c++;
                        endforeach;
                        if(!empty($rowData['parameter'])):
                            $catData = ((!empty($rowData['category_name'])) ? $this->itemCategory->getCategory(['category_name'=>$rowData['category_name']]) : []);

                            $paramData[]=[
                                'id' => '',
                                'item_id' => $postData['item_id'],
                                'control_method' => $postData['control_method'],
                                'category_id' => (!empty($catData->id) ? $catData->id : 0),
                                'parameter' => $rowData['parameter'],
                                'specification' => $rowData['specification'],
                                'min' => (!empty($rowData['min']) ? $rowData['min'] : NULL),
                                'max' => (!empty($rowData['max']) ? $rowData['max'] : NULL),
                                'instrument' => $rowData['instrument'],
                                'created_by' => $this->loginId,
                                'created_at' => date("Y-m-d H:i:s"),
                            ];
                        endif;
                    }
                }
				
                if(!empty($paramData)){
                    $result = $this->item->saveInspectionParamExcel($paramData);
                    $this->printJson($result);
                }else{
                    $this->printJson(['status' => 0, 'message' => 'Data not found...!']);
                }                
            } else {
                $this->printJson(['status' => 0, 'message' => 'Data not found...!']);
            }
        else :
            $this->printJson(['status' => 0, 'message' => 'Please Select File!']);
        endif;
    }

    /* Item Details */
    public function itemDetails($item_id){
		$this->data['itemData'] = $itemData = $this->item->getItem(['id'=>$item_id]);
        //$this->data['rawMaterial'] = $this->item->getItemList(['item_type'=>'1,2,3']);
		$this->data['rawMaterial'] = $this->item->getItemList(['item_type'=>'1,2,3', 'grade_id'=>$itemData->grade_id]);
        $this->data['process'] = $this->item->getProductProcessList(['item_id'=>$item_id]);
        $this->data['mbOptions'] = $this->getMainBomItems(['item_id'=>$item_id]);
        $this->data['processDataList'] = $this->process->getProcessList(['ingonre_process'=>3]); //	IGNORE RM CUTTING PROCESS
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['category_type'=>11,'final_category'=>1]);
        $this->data['dieMaterial'] = $this->item->getItemList(['item_type'=>7]);        
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>9]);      
        $this->data['consumable'] = $this->item->getItemList(['item_type'=>2]);
		$this->data['revList'] = $this->ecn->getEcn(['item_id'=>$item_id, 'status'=>'2,3']);
		$this->data['tcHeadList'] = (!empty($itemData->tc_head))?$this->testType->getTestParameter(['test_type'=>$itemData->tc_head]):[];
        $this->data['tcData'] = $this->materialGrade->getTcMasterData(['item_id'=>$itemData->id]);
        
        $this->data['revisionList'] = $this->ecn->getItemRevision(['item_id'=>$item_id]);
		$this->data['machineList'] = $this->itemCategory->getCategoryList(['category_type'=>5, 'final_category'=>1]);
		$this->data['tcSpecification'] = $this->item->getTcSpecificationData(['item_id'=>$item_id]);
        $this->load->view('item_master/item_details',$this->data);
    }

    public function productOptionPrint($id){
		$this->data['itemData'] = $this->item->getProductKitData(['item_id'=>$id,'not_in_item_type'=>9]);
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
		$mpdf->SetWatermarkImage($logo,0.05,array(50,50));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

    /* Product BOM */
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
	
	public function deleteProductKit(){ 
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$this->printJson($this->item->deleteProductKit($data['id']));
		endif;
    }

    public function productKitHtml(){
        $data = $this->input->post();
		$productKitData = $this->item->getProductKitData(['item_id'=>$data['item_id'],'not_in_item_type'=>9]);
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

    /* Product Process */
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

    public function productProcessHtml(){
        $data = $this->input->post();
		$processData = $this->item->getProductProcessList(['item_id'=>$data['item_id'],'is_active'=>1]);

        $tbody = ''; $options = '<option value="">Select Process</option>';
        if (!empty($processData)) :
            $i = 1;            
            foreach ($processData as $row) :
                $deleteParam = "{'postData':{'id' : ".$row->id.",'item_id' : ".$row->item_id.",'process_id' : ".$row->process_id."},'message' : 'Product Process','res_function':'getProductProcessHtml','fndelete':'deleteProductProcess'}";
                $tbody .= '<tr id="'.$row->id.'">
					<td class="text-center">'.$i++.'</td>
					<td>'.$row->process_name.'</td>
					<td class="text-center">'.$row->sequence.'</td>
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

    public function setProductionType(){
        $data = $this->input->post();
        if(empty($data['item_id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->setProductionType($data));
        endif;
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

    /* Product Cycle Time */
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

    public function cycleTimeHtml($item_id=""){
        $data = $this->input->post();
        $item_id = (!empty($data['item_id']) ? $data['item_id'] : $item_id);
        $processData = $this->item->getProductProcessList(['item_id'=>$item_id]);   
		$unitList = $this->item->itemUnits();

        $html = "";
        if (!empty($processData)) :
            $i = 1; $j=0;
            foreach ($processData as $row) :
				$deleteParam = "{'postData':{'id' : ".$row->id.",'item_id' : ".$row->item_id.",'process_id' : ".$row->process_id.",'drawing_file' : '".$row->drawing_file."'},'message' : ' Process Detail','res_function':'getCycleTimeHtml','fndelete':'deleteDrawingFile'}";
                $deleteButton = '<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>';
			
                $pid = (!empty($row->id)) ? $row->id : "";
                $ct = (!empty($row->cycle_time)) ? $row->cycle_time : "";
                $fgwt = (!empty($row->finish_wt)) ? $row->finish_wt : "";
                $conv_ratio = (!empty($row->conv_ratio)) ? $row->conv_ratio : "";
                $process_cost = (!empty($row->process_cost)) ? $row->process_cost : "";
                $output_qty = (!empty($row->output_qty)) ? $row->output_qty : "";
                $process_no = (!empty($row->process_no)) ? $row->process_no : "";
                $item_id = (!empty($row->item_id)) ? $row->item_id : "";
                $html .= '<tr id="' . $row->id . '">
                    <td class="text-center">' . $i++ . '</td>
                    <td>' . $row->process_name . '</td>
					<td class="text-center">
                        <input type="text" name="ctData['.$j.'][process_no]" id="process_no_'.$j.'" class="form-control numericOnly" step="1" value="' . $process_no . '" />
                    </td> 
                    <td class="text-center">
                        <input type="text" name="ctData['.$j.'][cycle_time]" class="form-control numericOnly" step="1" value="' . $ct . '" />
                        <input type="hidden" name="ctData['.$j.'][id]" value="' . $pid . '" />
                        <input type="hidden" name="ctData['.$j.'][item_id]" value="' . $item_id . '" />
                    </td>
                    <td class="text-center">
                        <input type="text" name="ctData['.$j.'][finish_wt]" class="form-control floatOnly" step="1" value="' . $fgwt . '" />
                    </td> 
                    <td class="text-center" hidden>
                        <input type="text" name="ctData['.$j.'][conv_ratio]" class="form-control floatOnly" step="1" value="' . $conv_ratio . '" />
                    </td> 
                    <td class="text-center">
                        <input type="text" name="ctData['.$j.'][process_cost]" class="form-control floatOnly" step="1" value="' . $process_cost . '" />
                    </td>
                    <td>
                        <select name="ctData['.$j.'][uom]" class="form-control select2">
							<option value="NOS" '.((!empty($row->uom) && 'NOS' == $row->uom) ? "selected" : "").'>NOS</option>
							<option value="KGS" '.((!empty($row->uom) && 'KGS' == $row->uom) ? "selected" : "").'>KGS</option>
						</select>
                    </td>
                    <td class="text-center">
                        <input type="text" name="ctData['.$j.'][output_qty]" class="form-control numericOnly validateOutQty" min="1"  value="' . $output_qty	. '" />
                    </td>  
                    <td class="text-center">
                        <input type="text" name="ctData['.$j.'][mfg_instruction]" class="form-control" value="'.(!empty($row->mfg_instruction) ? $row->mfg_instruction : '').'" />
                    </td>                                 
                    <td class="d-flex align-items-center">
                        <input type="file" name="drawing_file[]" class="form-control" />
                        <input type="hidden" name="old_drawing_file[]" value="'.$row->drawing_file.'">
					</td>
                    <td>
                        '.(!empty($row->drawing_file) ? '<a class="text-primary font-bold ml-5" id="supplier_file" href="'.base_url("assets/uploads/process_drg/".$row->drawing_file).'" download=""><i class="fa fa-download" aria-hidden="true"></i></a>' : "") .' 
						'.(!empty($row->drawing_file) ? $deleteButton : "").'
					</td>
					<td>
						<select name="is_active[]" class="form-control">
							<option value="1" '.(($row->is_active == 1) ? "selected" : "").'>Active</option>
							<option value="2" '.(($row->is_active == 2) ? "selected" : "").'>In-Active</option>
						</select>
					</td>
				</tr>';
                $j++;
            endforeach;
        else :
            $html .= '<tr><td colspan="10" class="text-center">No Data Found.</td></tr>';
        endif;

        $this->printJson(['status'=>1,'tbodyData'=>$html]);
    }
	
	public function deleteDrawingFile(){ 
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$this->printJson($this->item->deleteDrawingFile($data));
		endif;
    }
	
    /* Die BOM */
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

    public function deleteDieBom(){ 
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$this->printJson($this->item->deleteDieBom($data['id']));
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
						<td class="text-center">'.$i++.'</td>
						<td>'.$row->category_name.'</td>
						<td>'.(!empty($row->item_code) ? "[".$row->item_code."] " : "").$row->item_name.'</td>
						<td class="text-center">'.$row->qty.'</td>
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

    /* Packing Standard */
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
			unset($data['form_type']);
			$this->printJson($this->item->savePackingStandard($data));
		endif;
    }
	
	public function deletePackingStandard(){ 
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$this->printJson($this->item->deletePackingStandard($data['id']));
		endif;
    }

    public function packingStandardHtml(){
        $data = $this->input->post();
        $packData = $this->item->getProductKitData(['item_id'=>$data['item_id'], 'item_type'=>9]);
		$i=1; $tbody='';
        
		if(!empty($packData)):
			foreach($packData as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Process','res_function':'getPackingStandardHtml','fndelete':'deletePackingStandard'}";
                
				$tbody.= '<tr>
						<td class="text-center">'.$i++.'</td>
                        <td>'.(($row->packing_type == 1)?'Primary Packing':'Final Packing').'</td>
                        <td>'.$row->group_name.'</td>
						<td>'.(!empty($row->item_code) ? '['.$row->item_code.']' : '').$row->item_name.'</td>
						<td class="text-center">'.$row->qty.'</td>
						<td class="text-center">'.$row->pack_wt.'</td>
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

    public function editRevison(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->ecn->getEcn(['id'=>$data['id'],'single_row'=>1]);
        $this->load->view('ecn/revision_file',$this->data);
    }

    public function saveRevisonFile(){
        $data = $this->input->post();
        $errorMessage = array();
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(isset($_FILES['company_drg']['name'])):
                if($_FILES['company_drg']['name'] != null || !empty($_FILES['company_drg']['name'])):
                    $this->load->library('upload');
                    $_FILES['userfile']['name']     = $_FILES['company_drg']['name'];
                    $_FILES['userfile']['type']     = $_FILES['company_drg']['type'];
                    $_FILES['userfile']['tmp_name'] = $_FILES['company_drg']['tmp_name'];
                    $_FILES['userfile']['error']    = $_FILES['company_drg']['error'];
                    $_FILES['userfile']['size']     = $_FILES['company_drg']['size'];
                    
                    $imagePath = realpath(APPPATH . '../assets/uploads/ecn/');
                    $config = ['file_name' => 'ecn-'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload()):
                        $errorMessage['company_drg'] = $this->upload->display_errors();
                        $this->printJson(["status"=>0,"message"=>$errorMessage]);
                    else:
                        $uploadData = $this->upload->data();
                        $data['company_drg'] = $uploadData['file_name'];
                    endif;
                endif;
            endif;

            if(isset($_FILES['customer_drg']['name'])):
                if($_FILES['customer_drg']['name'] != null || !empty($_FILES['customer_drg']['name'])):
                    $this->load->library('upload');
                    $_FILES['userfile']['name']     = $_FILES['customer_drg']['name'];
                    $_FILES['userfile']['type']     = $_FILES['customer_drg']['type'];
                    $_FILES['userfile']['tmp_name'] = $_FILES['customer_drg']['tmp_name'];
                    $_FILES['userfile']['error']    = $_FILES['customer_drg']['error'];
                    $_FILES['userfile']['size']     = $_FILES['customer_drg']['size'];
                    
                    $imagePath = realpath(APPPATH . '../assets/uploads/ecn/');
                    $config = ['file_name' => 'ecn-'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload()):
                        $errorMessage['customer_drg'] = $this->upload->display_errors();
                        $this->printJson(["status"=>0,"message"=>$errorMessage]);
                    else:
                        $uploadData = $this->upload->data();
                        $data['customer_drg'] = $uploadData['file_name'];
                    endif;
                endif;
            endif;
        
            $this->printJson($this->ecn->saveEcn($data));
        endif;
    }

	public function revisionHtml(){
        $data = $this->input->post();
        $revData = $this->ecn->getEcn(['item_id'=>$data['item_id'], 'status'=>'2,3']);
		$i=1; $tbody='';
        
		if(!empty($revData)):
			foreach($revData as $row):
                $actionButton = '';

                $editRevParam = "{'postData':{'id' : ".$row->id."}, 'modal_id' : 'bs-right-md-modal', 'form_id' : 'editRevision', 'title' : 'Update Revision','call_function':'editRevison', 'fnsave' : 'saveRevisonFile','res_function':'getRevisionHtml','js_store_fn':'customStore'}";
                
                $actionButton = '<a class="btn btn-sm btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="edit" flow="left" onclick="modalAction('.$editRevParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

                $actionButton .= '<a class="btn btn-sm btn-tumblr btn-edit permission-modify" href="'.base_url('items/getControlPlanPrint/'.$row->item_id.'/'.$row->rev_no).'" datatip="Print" flow="left" target="_blank"><i class="fas fa-print"></i></a>';
               
                $actionButton .= '<a class="btn btn-sm btn-info  btn-edit permission-modify excel" flow="left" datatip="excel" target="_blank" data-item_id = "'.$row->item_id.'" data-rev_no = "'.$row->rev_no.'"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>';
         						
				$tbody.= '<tr>
						<td class="text-center">'.$i++.'</td>
						<td>'.$row->drw_no.'</td>
						<td class="text-center">'.$row->cust_rev_no.'</td>
						<td class="text-center">'.formatDate($row->cust_rev_date).'</td>
						<td class="text-center">'.$row->rev_no.'</td>
						<td class="text-center">'.formatDate($row->rev_date).'</td>
						<td class="text-center">'.$row->status_label.'</td>
                        <td class="text-center">'.(!empty($row->customer_drg) ? '<a href="'.base_url("assets/uploads/ecn/".$row->customer_drg).'" target="_blank"><i class="fa fa-download"></i></a>' : '').'</td>
                        <td class="text-center">'.(!empty($row->company_drg) ? '<a href="'.base_url("assets/uploads/ecn/".$row->company_drg).'" target="_blank"><i class="fa fa-download"></i></a>' : '').'</td>
						<td class="text-center">'.$actionButton.'</td>
						
					</tr>';
			endforeach;
        else:
            $tbody = '<tr><td colspan="10" class="text-center">No data found.</td></tr>';
		endif;

        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
	}
	
	public function getRevisionExcel($jsonData = "") {
        $data = (Array) decodeURL($jsonData);
        $processData = $this->item->getProductProcessList(['item_id' => $data['item_id']]);

        $spreadsheet = new Spreadsheet();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
    
        // HTML header for table
        $htmlHeader = "<tr>
            <th>rev_no</th>
            <th>param_type</th>
            <th>parameter</th>
            <th>machine_tool</th>
            <th>specification</th>
            <th>min</th>
            <th>max</th>
            <th>char_class</th>
            <th>instrument</th>
            <th>size</th>
            <th>frequency</th>
            <th>freq_unit</th>
            <th>tool_name</th>
            <th>rpm</th>
            <th>feed</th>
            <th>reaction_plan</th>
            <th>control_method</th>
        </tr>";
    
        $fileDirectory = realpath(APPPATH . '../assets/uploads/product_inspection');
        if (!is_dir($fileDirectory)) {
            mkdir($fileDirectory, 0777, true);
        }
    
        $i = 0; 
        if (!empty($processData)) {
            foreach ($processData as $row) {  
                $paramData = $this->item->getInspectionParam(['item_id' => $data['item_id'],'rev_no' => $data['rev_no'],'process_id' => $row->process_id]);

                $tbody = '';
                foreach ($paramData as $value) { 
					$tbody .= '<tr class="text-center">
						<td>' . htmlspecialchars($value->rev_no) . '</td>
						<td>' . (($value->param_type == 1) ? 'Product' : 'Process') . '</td>
						<td>' . (!empty($value->parameter) ? htmlspecialchars($value->parameter) : '') . '</td>
						<td>' . (!empty($value->mc_category) ? htmlspecialchars($value->mc_category) :''). '</td>
						<td>' . htmlspecialchars($value->specification) . '</td>
						<td>' . (($value->min == 0.00) ? '-' : htmlspecialchars($value->min)) . '</td>
						<td>' . (($value->max == 0.00) ? '-' : htmlspecialchars($value->max)) . '</td>
						<td class="text-center">' . htmlspecialchars($value->char_class) . '</td>
						<td>' . htmlspecialchars($value->instrument) . '</td>
						<td>' . htmlspecialchars($value->size) . '</td>
						<td>' . htmlspecialchars($value->frequency) . '</td>
						<td>' . htmlspecialchars($value->freq_unit) . '</td>
						<td>' . htmlspecialchars($value->tool_name) . '</td>
						<td>' . htmlspecialchars($value->rpm) . '</td>
						<td>' . htmlspecialchars($value->feed) . '</td>
						<td>' . htmlspecialchars($value->reaction_plan) . '</td>
						<td>' . htmlspecialchars($value->control_method) . '</td>
					</tr>';
                }
                $processData = '<table><thead>' . $htmlHeader . '</thead><tbody>' . $tbody . '</tbody></table>';
                $reader->setSheetIndex($i);
                $spreadsheet = $reader->loadFromString($processData, $spreadsheet);
    
                $row->process_name = trim(preg_replace('/[^A-Za-z0-9\-]/', ' ', $row->process_name));
                $row->process_name = substr(trim(str_replace('-', ' ', $row->process_name)), 0, 30);
                $spreadsheet->getSheet($i)->setTitle($row->process_name);
    
                $processSheet = $spreadsheet->getSheet($i);
                $highestColumn = $processSheet->getHighestColumn();
                foreach (range('A', $highestColumn) as $col) {
                    $processSheet->getColumnDimension($col)->setAutoSize(true);
                }
                $i++;
            }
        }
    
        // Save the file
        $fileName = '/product_inspection_' . time() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileDirectory . $fileName);
    
        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
        header("Pragma: no-cache");
        header("Expires: 0");
 
        redirect(base_url('assets/uploads/product_inspection') . $fileName);
    }
	
	public function saveTcSpecification(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['item_id']))
			$errorMessage['item_id'] = "Item Id is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->item->saveTcSpecification($data));
        endif;
    }

	public function getProcessList($param=[]){ 
		$data = (!empty($param)) ? $param : $this->input->post(); 
        $process = '';
        $processList = $this->item->getProductProcessList(['item_ids'=>$data['fg_id'],'group_by'=>'product_process.process_id']);
		if(!empty($processList)){
			foreach($processList as $row){
				$selected = (!empty($data['process_id']) && in_array($row->process_id, explode(',', $data['process_id'])))?'selected':'';
				$process .= '<option value="'.$row->process_id.'" '.$selected.'>'.$row->process_name.'</option>';

			}
		}
        if(!empty($param)):
			return $process;
		else:
        	$this->printJson(['status'=>1,'processOption'=>$process]);
		endif;	
    }

    /** Start Tool Bom*/
	public function toolBomHtml(){
        $data = $this->input->post();
        $toolData = $this->item->getToolBomData(['item_id'=>$data['item_id']]);
        $tbodyData="";$i=1; 
        if(!empty($toolData)):
            $i=1;
            foreach($toolData as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id."},'res_function':'toolBomHtml','fndelete':'deleteToolBom'}";
                $editBtn = "<button type='button' onclick='editToolBom(".json_encode($row).",this);' class='btn btn-sm btn-outline-info waves-effect waves-light btn-sm permission-modify' datatip='Edit'><i class='far fa-edit'></i></button>";
                $tbodyData.= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$row->process_name.'</td>
                            <td>'.$row->item_name.'</td>
                            <td>'.$row->tool_life.'</td>
                            <td>'.$row->cutting_lenght.'</td>
                            <td>'.$row->no_of_pass.'</td>
                            <td>'.$row->rpm.'</td>
                            <td>'.$row->feed.'</td>
                            <td>'.$row->part_life.'</td>
                            <td class="text-center">
                            '.$editBtn.'
							<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
						</td>
                    </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="10" style="text-align:center;">No Data Found</td></tr>';
        endif;

        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

    public function saveToolBom(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['tool_id']))
			$errorMessage['tool_id'] = "Tool Name is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->item->saveToolBom($data));
        endif;
    }

    public function deleteToolBom(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->deleteToolBom($id));
        endif;
    }
	/** End Tool Bom*/

    public function getCategoryList(){
		$data = $this->input->post();
		$categoryList = $this->itemCategory->getCategoryList(['category_type' => $data['product_type'],'final_category'=>1]);
		$options = '<option value="">Select</option>';
		if(!empty($categoryList)){
			foreach($categoryList as $row){
				$options .= '<option value="'.$row->id.'">'.$row->category_name.'</option>';
			}
		}
		$this->printJson(['status'=>1, 'options'=>$options]);
	}
}
?>