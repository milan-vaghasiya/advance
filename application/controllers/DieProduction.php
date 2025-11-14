<?php
class DieProduction extends MY_Controller
{
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Die Production";
		$this->data['headData']->controller = "dieProduction";
        $this->data['headData']->pageUrl = "dieProduction";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionDtHeader($this->data['headData']->controller);
        $this->load->view('die_production/index',$this->data);
    }

    public function getDTRows($status = 1){
        $data = $this->input->post(); 
        $data['status'] = $status;
        $result = $this->dieProduction->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getDieProductionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addDieProduction(){
        $this->data['next_no'] = $this->dieProduction->getNextDieNo();
        $this->data['trans_number'] = "DP".n2y(date("Y")).n2m(date("m")).str_pad($this->data['next_no'],2,'0',STR_PAD_LEFT);
        $this->data['fgItemList'] = $this->item->getItemList(['item_type'=>'1']);
        $this->load->view('die_production/form', $this->data);
    }

    public function getDieKitList() {
        $data = $this->input->post(); 
        $tbodyData='';$diekit_id='';$diekit_item_id='';$diekit_qty='';
        $dieKit = $this->dieProduction->getDieKitData($data);
        if (!empty($dieKit)) {
            $i=1;
            foreach ($dieKit as $row) {
					
                $tbodyData .= '<tr>
                        <td> '.$i.' </td>
                        <td> '.$row->category_name.' </td>
                        <td>
                            <input type="hidden" name="dp_ref_id[]" value="'.$row->ref_cat_id.'" />
                            <input type="hidden" name="row_id[]" id="rowid_'.$i.'" value="'.$i.'">
                            <input type="text" name="dp_qty[]" value="" class="form-control dieKitQty'.$i.'" id="diekit_qty_'.$i.'" data-rowid="'.$i.'">
                            <div class="error kiterr'.$i.'"></div>
                        </td>
                    </tr>';
                    $i++;
            }
        } else {
            $tbodyData = '<td colspan="3" class="text-center">No Data</td>';
        }

        $this->printJson(['status' =>1,'tbodyData'=>$tbodyData]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['dp_ref_id'][0])){
            $errorMessage['bomErr'] = "Bom Data is required.";
        }
		
		if(isset($data['dp_qty'])){
			if(empty(array_sum($data['dp_qty']))){
				$errorMessage['bomErr'] = "Bom Qty is required.";
			}
		}

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->dieProduction->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->dieProduction->delete($id));
        endif;
    }

	public function approveProduction() {
        $data = $this->input->post();
        $this->data['id'] = $data['id'];
		
        if(!empty($data['type']) && $data['type'] == 'Component'){
            $this->data['type'] = $data['type'];
            $this->data['popData'] = $this->dieProduction->getPopReport(['die_id'=>$data['id']]);
        }else{
            $this->data['popData'] = $this->dieProduction->getPopReport(['die_job_id'=>$data['id']]);
        }
		
        $this->data['paramData'] = $this->item->getInspectionParam(['item_id'=>$data['item_id'],'category_id'=>$data['category_id'],'control_method'=>'POP']);
        $this->data['dieRunData'] = $this->dieMaster->getDieHistoryData(['fg_id'=>$data['item_id'],'category_id'=>$data['category_id'],'recut_no'=>0,'order_by'=>1,'limit'=>4]);
        $this->load->view('die_production/approve_form',$this->data);
    }
	
    public function changeStatus() {
        $data = $this->input->post();
        if(empty($data['id']))
            $errorMessage['id'] = "Somthing went wrong...Please try again.";

        if(in_array($data['status'],[9,8,7]) || ((!empty($data['type']) && $data['type'] == 'Component') && in_array($data['status'],[1,4,6]))){
            if(empty($data['min_capacity'])){
                $errorMessage['capacity'] = "Capacity is Require";
            }
			if(empty($data['max_capacity'])){
                $errorMessage['capacity'] = "Capacity is Require";
            }
            if(empty($data['weight'])){
                $errorMessage['weight'] = "Weight is Required";
            }
            if(empty($data['height'])){
                $errorMessage['height'] = "Height is Required";
            }
        }
		if(!empty($data['min_capacity']) && !empty($data['max_capacity'])){ $data['capacity'] = $data['min_capacity'].'-'.$data['max_capacity']; }
		else{ unset($data['min_capacity'],$data['max_capacity']); }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
		
			if(isset($_FILES['attach_file']['name'])):
                if($_FILES['attach_file']['name'] != null || !empty($_FILES['attach_file']['name'])):
                    $this->load->library('upload');
                    $_FILES['userfile']['name']     = $_FILES['attach_file']['name'];
                    $_FILES['userfile']['type']     = $_FILES['attach_file']['type'];
                    $_FILES['userfile']['tmp_name'] = $_FILES['attach_file']['tmp_name'];
                    $_FILES['userfile']['error']    = $_FILES['attach_file']['error'];
                    $_FILES['userfile']['size']     = $_FILES['attach_file']['size'];
                    
                    $imagePath = realpath(APPPATH . '../assets/uploads/die_component/');
                    $config = ['file_name' => 'die_component-'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload()):
                        $errorMessage['attach_file'] = $this->upload->display_errors();
                        $this->printJson(["status"=>0,"message"=>$errorMessage]);
                    else:
                        $uploadData = $this->upload->data();
                        $data['attach_file'] = $uploadData['file_name'];
                    endif;
                endif;
            endif;
		
            $this->printJson($this->dieProduction->changeStatus($data));
        endif;
    }

    /* Material Issue */
    public function materialIssue(){
		$data = $this->input->post();
		$this->data['id'] = $data['id'];
		$this->data['bomData'] = $this->item->getDieSetData(['item_id'=>$data['item_id'],'category_id'=>$data['category_id']]);
        $this->data['dieData'] = $this->dieProduction->getDieProductionBom(['die_id'=>$data['id']]);
		$this->load->view('die_production/issue_form',$this->data);
	}

	public function saveMaterialIssue(){
		$data = $this->input->post();

		if(empty($data['item_id'])){ 
            $errorMessage['general_error'] = "Item is required."; 
        }

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dieProduction->saveMaterialIssue($data));
        endif;		
	}

    /* Log Form */
    public function addDieLog(){
		$data = $this->input->post();
        $this->data['dataRow'] = $this->dieProduction->getDieProductionData(['id'=>$data['id'],'single_row'=>1]);
		$this->data['die_id'] = $data['id'];
        if(!empty($data['challan_id'])){
			$this->data['challan_id'] = $data['challan_id'];
			$this->data['process_by'] = 2;
			$this->data['processor_id'] = $data['party_id'];
		}else{
            $this->data['shiftData'] = $this->shiftModel->getShiftList();
            $this->data['operatorList'] = $this->employee->getEmployeeList();
            $this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
			$this->data['process_by'] = 1;
        }
		
		$this->load->view('die_production/die_log_form',$this->data);
	}
	
	public function saveDieLog(){
		$data = $this->input->post(); 
        $errorMessage = array();
        
        if(empty($data['die_id'])){ 
            $errorMessage['die_id'] = "Die No. is required.";
        }
        if(empty($data['production_time'])){ 
            $errorMessage['production_time'] = "Production Time is required.";
        }
        if(empty($data['start_date_time'])){ 
            $errorMessage['start_date_time'] = "Start Time is required.";
        }
        if(empty($data['end_date_time'])){ 
            $errorMessage['end_date_time'] = "End Time is required.";
        }
        if(empty($data['processor_id'])){ 
            $errorMessage['processor_id'] = "Required.";
        }
		if(empty($data['trans_date']) or $data['trans_date'] == null or $data['trans_date'] == ""){ 
            $errorMessage['trans_date'] = "Date is required."; 
        }
        if(!empty($data['process_by']) && $data['process_by'] == 2){
            if (empty($data['in_challan_no'])){ 
                $errorMessage['in_challan_no'] = "In Challan No. is required.";
            }            
        }        

		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            // print_r($data);exit;	
            if($data['process_by'] == 2){
				
				if($_FILES['attachment']['name'] != null || !empty($_FILES['attachment']['name'])):
                    $this->load->library('upload');
    				$_FILES['userfile']['name']     = $_FILES['attachment']['name'];
    				$_FILES['userfile']['type']     = $_FILES['attachment']['type'];
    				$_FILES['userfile']['tmp_name'] = $_FILES['attachment']['tmp_name'];
    				$_FILES['userfile']['error']    = $_FILES['attachment']['error'];
    				$_FILES['userfile']['size']     = $_FILES['attachment']['size'];
    				
    				$imagePath = realpath(APPPATH . '../assets/uploads/die_outsource/');
    				$config = ['file_name' => 'in_challan-'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];
    
    				$this->upload->initialize($config);
    				if (!$this->upload->do_upload()):
    					$errorMessage['attachment'] = $this->upload->display_errors();
    					$this->printJson(["status"=>0,"message"=>$errorMessage]);
    				else:
    					$uploadData = $this->upload->data();
    					$data['attachment'] = $uploadData['file_name'];
    				endif;
    			endif;
            }else{
                $postData = [
                    'id'=>$data['id'],
                    'die_id' => $data['die_id'],
                    'process_by' =>1,
                    'trans_date' => $data['trans_date'],
                    'processor_id' => $data['processor_id'],
                    'trans_date' => $data['trans_date'],
                    'logDetail'=>[
                        'id'=>'',
                        'operator_id' => $data['operator_id'],
                        'remark' => $data['remark'],
                        'process_description' =>$data['process_description'] ,
                        'start_date_time' => $data['start_date_time'],
                        'end_date_time' => $data['end_date_time'],
                        'program_time' => $data['program_time'],
                        'production_time' => $data['production_time'],
                        'trans_date' => $data['trans_date'],
                    ]
                ];

                $data = $postData;
            }
            
			$this->printJson($this->dieProduction->saveDieLog($data));
		endif;	
	}

	public function deleteDieLog(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->dieProduction->deleteDieLog($data));
        endif;
	}

	public function logDetail(){
        $data = $this->input->post();
        $this->data['logData'] = $this->dieProduction->getDieLogData(['die_id'=>$data['id'],'single_row'=>1]);
        $this->load->view("die_production/die_log_detail",$this->data);
    }

    // 05-08-2024
	public function logTansDetail(){
        $data = $this->input->post();
        $this->data['die_id'] = $data['id'];
        $this->load->view("die_production/log_trans_detail",$this->data);
    }

	public function getLogTransHtml(){
        $data = $this->input->post();
        $logData = $this->dieProduction->getDieLogTransData(['die_id'=>$data['die_id']]);
		$i=1; $tbody='';
        
		if(!empty($logData)):
			foreach($logData as $row):
                $deleteParam = "{'postData':{'id' : '".$row->id."', 'log_id' : '".$row->log_id."', 'production_time' : '".$row->production_time."'},'message' : 'Log','res_function':'getLogTransResponse','fndelete':'deleteLogTrans'}";
				$deleteBtn = "";
                if($row->status == 2){
                    $deleteBtn = '<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>';
                }

                $tbody.= '<tr>
						<td>'.$i++.'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td>'.$row->category_name.'</td>        
                        <td>'.(!empty($row->fg_item_code)?$row->fg_item_code:'').' '.$row->fg_item_name.'</td>     
                        <td>'.$row->trans_number.'</td>  
                        <td>'.$row->processor_name.'</td>       
                        <td>'.$row->emp_name.'</td> 
                        <td>'.$row->production_time.'</td>
                        <td>'.$row->material_cost.'</td>
                        <td>'.$row->mhr.'</td>
                        <td>'.$row->process_description.'</td>
						<td class="text-center">
							'.$deleteBtn.'
						</td>
					</tr>';
			endforeach;
        else:
            $tbody = '<tr><td colspan="12" class="text-center">No data found.</td></tr>';
		endif;
        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
	}
    
    public function deleteLogTrans(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->dieProduction->deleteLogTrans($data));
        endif;
	}
    
    // 05-08-2024
    /* POP Report */
    public function addPopReport(){
		$data = $this->input->post();
		$this->data['id'] = $data['id'];        
        $this->data['trans_no'] = $this->dieProduction->getNextPopReportNo();
        $this->data['trans_number'] = "PCI".n2y(date("Y")).str_pad($this->data['trans_no'],2,'0',STR_PAD_LEFT);
        if(!empty($data['type']) && $data['type'] == 'Component'){
            $dataRow = $this->dieMaster->getDieMasterData(['id'=>$data['id'], 'single_row'=>1]);
            $dataRow->fg_item_id = $dataRow->fg_id;
            $dataRow->item_id = $dataRow->category_id;
            $this->data['dataRow'] = $dataRow;
            $this->data['type'] = $data['type'];
            $this->data['die_main_id'] = $data['id'];
        }else{
            $this->data['dataRow'] = $this->dieProduction->getDieProduction(['id'=>$data['id']]);
        }
		//$this->data['paramData'] = $this->item->getInspectionParam(['item_id'=>$data['item_id'], 'category_id'=>$data['category_id'],'control_method'=>'POP']);
		$this->data['paramData'] = $this->item->getInspectionParam(['item_id'=>$data['item_id'],'control_method'=>'POP']); 
		//$this->item->printQuery();
		$this->load->view('die_production/pop_report',$this->data);
	}
 
    public function getDieListOptions(){
        $data = $this->input->post(); 
        $options = '<option value="">Select Die</option>';
        if($data['insp_type'] == 1){
            $dpData = $this->dieProduction->getDieProductionData(['item_id'=>$data['item_id'],'category_id'=>$data['category_id'],'status'=>5]);
            foreach($dpData as $row){
                $options .= '<option value="'.$row->id.'">'.$row->trans_number.'</option>';
            }
        }else{
            $dmData = $this->dieMaster->getDieMasterData(['fg_id'=>$data['item_id'],'category_id'=>$data['category_id'],'status'=>'0,1']);
            foreach($dmData as $row){
                $options .= '<option value="'.$row->id.'">'.$row->item_code.'</option>';
            }
        }
        $this->printJson(['options'=>$options]);
    }

	public function savePopReport(){
		$data = $this->input->post(); 
        $errorMessage = Array(); 

		if(empty($data['item_id'])){
            $errorMessage['item_id'] = "Item is required.";
        }

        if(!empty($data['other_die']) && empty($data['insp_die_id'])){
            $errorMessage['insp_die_id'] = "Required.";
        }
        $insParamData = $this->item->getInspectionParam(['item_id'=>$data['item_id'], 'category_id'=>$data['category_id'],'control_method'=>'POP']);
        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";

        $pre_inspection = Array(); $param_ids = Array();

        if(!empty($insParamData)):
            foreach($insParamData as $row):
                $param = Array();
                $param[] = (!empty($data['observation_sample_'.$row->id]) ? $data['observation_sample_'.$row->id] : ""); 
                $pre_inspection[$row->id] = $param;

                $param2 = Array();
                $param2[] = (!empty($data['result_'.$row->id]) ? $data['result_'.$row->id] : ""); 
                $pre_inspection_res[$row->id] = $param2;

				$param_ids[] = $row->id;
                unset($data['observation_sample_'.$row->id], $data['result_'.$row->id]);
            endforeach;
        endif;

        $data['observation'] = json_encode($pre_inspection);
        $data['result'] = json_encode($pre_inspection_res);
		$data['parameter_ids'] = implode(',',$param_ids);
        $data['param_count'] = count($insParamData);

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['report_date'] = date('Y-m-d');
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->dieProduction->savePopReport($data));
        endif;		
	}

    public function printPop($id) {
        $this->data['id'] = $this->input->post('id');
        $this->data['popData'] = $this->dieProduction->getPopReport(['die_job_id'=>$id]);
        //$this->data['paramData'] = $this->item->getInspectionParam(['item_id'=>$this->data['popData']->item_id,'category_id'=>$this->data['popData']->category_id,'control_method'=>'POP']);
		$this->data['paramData'] = $this->item->getInspectionParam(['item_id'=>$this->data['popData']->item_id,'control_method'=>'POP']);
        $this->data['itmRev'] = $this->item->getLatestRev(['item_id'=>$this->data['popData']->item_id]);
        $pdfData = $this->load->view('die_production/pop_print',$this->data,true);
        $logo = base_url('assets/images/logo.png');
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1.2rem;width:50%">Die-Plaster Inspection Report</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;"> QA-F-01(REV.02/dtd.) 25.02.23</td>
							</tr>
						</table><hr>';
                        
		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:50%;" class="text-center"></td>
						</tr>
						<tr>
							<td style="width:50%;"></td>
							<td style="width:50%;" class="text-center"><b>Prepared By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$pdfData = '<div style="width:200mm;height:140mm;">'.$pdfData.'</div>';
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='POP'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
         $mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('P','','','','',5,5,30,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }

    /* Material Value */
    public function addMaterialValue(){
		$data = $this->input->post();
        $this->data['dataRow'] = $data;		
		$this->load->view('die_production/material_value',$this->data);
	}
	
	public function saveMaterialValue(){
		$data = $this->input->post(); 
        $errorMessage = array();
        
        if(empty($data['material_value'])){ 
            $errorMessage['material_value'] = "Material value is required.";
        }

		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :	
			$this->printJson($this->dieProduction->saveMaterialValue($data));
		endif;	
	}


    /*** Scrap */
    public function scrapIndex(){
        $this->data['headData']->pageTitle = "Die Production Scrap";
        $this->data['tableHeader'] = getProductionDtHeader('dieScrap');
        $this->load->view('die_production/scrap_index',$this->data);
    }

    public function getScrapDTRows(){
        $data = $this->input->post(); 
        $result = $this->dieProduction->getScrapDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getDieScrapData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function generateScrap(){
        $data = $this->input->post();
        if(empty($data['id'])){
            $errorMessage['id'] = "Somthing went wrong...Please try again.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dieProduction->generateScrap($data));
        endif;
    }

    /*** End SCap */
}
?>
