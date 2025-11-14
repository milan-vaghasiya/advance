<?php 
class VendorInspection extends MY_Controller
{
    private $indexPage = "vendor_inspection/index";
    private $iprIndexPage = "vendor_inspection/ipr_index";
    private $formPage = "vendor_inspection/form";
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Vendor Inspection";
		$this->data['headData']->controller = "vendorInspection";
        $this->data['headData']->pageUrl = "vendorInspection";
	}
	
	/* Created By @Raj.F 21-02-2025 */
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader('vendorInspection');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();
		$data['status'] = $status;
        $result = $this->vendorInspection->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getVendorInspectionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }


    public function AddVendorInspection(){
        $data = $this->input->post();
        if(!empty($data['prc_type']) && $data['prc_type'] == 2){
            $dataRow = $this->sop->getPRCDetail(['prc_id'=>$data['prc_id']]);
            $dataRow->process_id = 3;
            $dataRow->current_process = 'RM Cutting';
            $this->data['dataRow'] = $dataRow;
        }else{
            $this->data['dataRow'] = $dataRow = $this->sop->getPRCProcessList(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'single_row'=>1]);
        }
		$this->data['challan_no'] = $data['challan_no'];
		$this->data['vendor_id'] = $data['vendor_id'];
        $this->data['operatorList'] = $this->employee->getEmployeeList();
        $this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
        $defaultRev = $this->ecn->getLastRevision(['item_id'=>$dataRow->item_id]);
        $this->data['last_rev_no'] = (!empty($defaultRev->rev_no) ? $defaultRev->rev_no : "");
		$this->data['prcLog'] = $this->sop->getProcessLogList(['id' => $data['id'],'single_row'=>1]);
		
        $this->data['inspParamHtml'] = $this->getInspectionParameter(['item_id'=>$dataRow->item_id,'process_id'=>$dataRow->process_id,'control_method'=>'IPR','rev_no'=>(!empty($dataRow->rev_no) ? $dataRow->rev_no : $this->data['last_rev_no']),'sampling_qty'=>5]);
		
		$this->load->view($this->formPage,$this->data);
	}

    public function saveVendorInspection(){
		$data = $this->input->post(); 
        $errorMessage = Array(); 

		if(empty($data['item_id'])){ $errorMessage['item_id'] = "Item is required.";}
		if(empty($data['rev_no'])){ $errorMessage['rev_no'] = "Revision required.";}

        $insParamData = $this->item->getInspectionParameter(['item_id'=>$data['item_id'],'process_id'=>$data['process_id'],'control_method'=>'IPR','rev_no'=>$data['rev_no'],'sampling_qty'=> $data['sampling_qty']]);
        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";

        $pre_inspection = Array(); $param_ids = Array();

        if(!empty($insParamData)):
            foreach($insParamData as $row):
                $param = Array();
				for($j = 1; $j <= $data['sampling_qty']; $j++):
					$param[] = (!empty($data['sample'.$j.'_'.$row->id]) ? $data['sample'.$j.'_'.$row->id] : 0);
					if(isset($data['sample'.$j.'_'.$row->id])){						
						unset($data['sample'.$j.'_'.$row->id]);
					}
				endfor;
                $param[] = (!empty($data['result_'.$row->id]) ? $data['result_'.$row->id] : "");
                $pre_inspection[$row->id] = $param;
				$param_ids[] = $row->id;
                unset($data['result_'.$row->id]);
            endforeach;
        endif;

        $data['observation_sample'] = json_encode($pre_inspection);
		$data['parameter_ids'] = implode(',',$param_ids);
        $data['param_count'] = count($insParamData);
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(isset($_FILES['inspection_file']['name'])):
                if($_FILES['inspection_file']['name'] != null || !empty($_FILES['inspection_file']['name'])):
                    $this->load->library('upload');
                    $_FILES['userfile']['name']     = $_FILES['inspection_file']['name'];
                    $_FILES['userfile']['type']     = $_FILES['inspection_file']['type'];
                    $_FILES['userfile']['tmp_name'] = $_FILES['inspection_file']['tmp_name'];
                    $_FILES['userfile']['error']    = $_FILES['inspection_file']['error'];
                    $_FILES['userfile']['size']     = $_FILES['inspection_file']['size'];
                    
                    $imagePath = realpath(APPPATH . '../assets/uploads/inspection/');
                    $config = ['file_name' => 'venodr_inspection-'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload()):
                        $errorMessage['inspection_file'] = $this->upload->display_errors();
                        $this->printJson(["status"=>0,"message"=>$errorMessage]);
                    else:
                        $uploadData = $this->upload->data();
                        $data['inspection_file'] = $uploadData['file_name'];
                    endif;
                endif;
            endif;
		
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->vendorInspection->saveVendorInspection($data));
        endif;
	}

    /*public function editLineInspection(){
        $data = $this->input->post();
        $this->data['lineInspData'] =$dataRow= $this->lineInspection->getLineInspectData(['id'=>$data['id']]);
        $this->data['operatorList'] = $this->employee->getEmployeeList();
        $this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
        $this->data['inspParamHtml'] = $this->getInspectionParameter(['item_id'=>$dataRow->item_id,'process_id'=>$dataRow->process_id,'control_method'=>'IPR','rev_no'=>$dataRow->rev_no,'id'=>$dataRow->id]);
        $this->load->view($this->formPage,$this->data);
       
    }*/

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->vendorInspection->delete($id));
        endif;
    }

    public function getInspectionParameter($data){
		if(!empty($data)){
			$postData = $data;
		}else{
			$postData = $this->input->post();
		}
		
        $tbodyData = $theadData = "";$i=1; 
        $paramData = $this->item->getInspectionParameter(['item_id'=>$postData['item_id'],'process_id'=>$postData['process_id'],'control_method'=>'IPR','rev_no'=>$postData['rev_no']]);
        $obj = new StdClass;
        if(!empty($postData['id'])){
            $oldData = $this->lineInspection->getLineInspectData(['id'=>$postData['id']]);
            if(!empty($oldData)):
                $obj = json_decode($oldData->observation_sample); 
            endif;
        }
		
		$theadData .= '<tr style="text-align:center;">
						<th rowspan="2" style="width:3%;">#</th>
						<th rowspan="2" style="width:10%">Parameter</th>
						<th rowspan="2" style="width:5%">Specification</th>
						<th colspan="2" style="width:10%">Tolerance</th>
						<th colspan="2" style="width:10%">Specification Limit</th>
						<th rowspan="2" style="width:5%">Instrument</th>
						<th rowspan="2" style="width:5%">Size</th>
						<th rowspan="2" style="width:5%">Frequency</th>
						<th colspan="'. $postData['sampling_qty'].'">Observation on Samples</th>
						<th rowspan="2" style="width:7%">Result</th>
					</tr>
					<tr style="text-align:center;">
						<th style="width:5%">Min</th>
						<th style="width:5%">Max</th>
						<th style="width:5%">LSL</th>
						<th style="width:5%">USL</th>';
						for($j=1; $j<=$postData['sampling_qty']; $j++):
                            $theadData .= '<th>'.$j.'</th>';
                        endfor;
		$theadData .= '</tr>';
       
        if(!empty($paramData)):
            foreach($paramData as $row):
                $lsl = floatVal($row->specification) - $row->min;
                $usl = floatVal($row->specification) + $row->max;
                $tbodyData.= '<tr>
                            <td style="text-align:center;">'.$i++.'</td>
                            <td>'.$row->parameter.'</td>
                            <td>'.$row->specification.'</td>
                            <td>'.$row->min.'</td>
                            <td>'.$row->max.'</td>
                            <td>'.$lsl.'</td>
                            <td>'.$usl.'</td>    
                            <td>'.$row->instrument.'</td>
                            <td>'.$row->size.'</td>   
                            <td>'.$row->frequency.'</td>';
							for($j=1; $j<=$postData['sampling_qty']; $j++):
				$tbodyData.=' <td style="min-width:100px;"><input type="text" name="sample'.($j).'_'.$row->id.'" class="form-control" value=""></td>';
							endfor;  
                $tbodyData.='<td >
                                <select class="form-control" name="result_'.$row->id.'">
                                    <option value="OK" '.((!empty($obj->{$row->id}[1]) && $obj->{$row->id}[1] == 'OK')?'selected':'').'>OK</option>
                                    <option value="NOT OK" '.((!empty($obj->{$row->id}[1]) && $obj->{$row->id}[1] == 'NOT OK')?'selected':'').'>NOT OK</option>
                                </select>
                            </td>';
                $tbodyData.='</tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="'.(10+$postData['sampling_qty']+1).'" style="text-align:center;">No Data Found</td></tr>';
        endif;
		
		if(empty($postData['is_json'])){			
			return $tbodyData;
		}else{			
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"theadData"=>$theadData]);
		}
    }
	
	public function printVendorInspection($id){
		$this->data['vendorInspectData'] = $inspData = $this->vendorInspection->getVendorInspectData(['ref_id'=>$id, 'report_type'=>3]);
		$this->data['prcLog'] = $this->sop->getProcessLogList(['id' => $id,'single_row'=>1]);
		
        $this->data['paramData'] = $paramData = $this->item->getInspectionParameter(['item_id'=>$inspData->item_id,'process_id'=>$inspData->process_id ,'control_method'=>'IPR','rev_no'=>$inspData->rev_no,'sampling_qty'=>$inspData->sampling_qty]);
		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();

        $tbodyData="";$theadData="";$i=1;$blankRow='';$theadData2="";$sample_size = (int)$inspData->sampling_qty;
        if(!empty($paramData)):
			$obj = json_decode($inspData->observation_sample);
            foreach($paramData as $row):
                $lsl = floatVal($row->specification) - $row->min;
                $usl = floatVal($row->specification) + $row->max;
                $paramItems = '<tr>
                    <td style="text-align:center;" height="30">'.$i.'</td>
                    <td style="text-align:left;">'.$row->parameter.'</td>
                    <td style="text-align:left;">'.$row->specification.'</td>
                    <td>'.$row->min.'</td>
                    <td>'.$row->max.'</td>
                    <td>'.$lsl.'</td>
                    <td>'.$usl.'</td> 
                    <td class="text-center">'.((!empty($row->char_class))?'<img src="'.base_url("/assets/images/symbols/".$row->char_class.'.png').'" style="width:20px;">':'').'</td>
                    <td style="text-align:left;">'.$row->instrument.'</td>
                    <td style="text-align:left;">'.$row->size.'</td>
                    <td style="text-align:left;">'.$row->frequency.'</td>';
					if($i==1){
						$theadData .= '<th colspan="'.$sample_size.'">Observation on Samples</th>';
						for($j=1;$j<=$sample_size;$j++):
							$theadData2 .='<th>'.$j.'</th>';
						endfor;
					}
					$j=0;
					for($j=0;$j<=$sample_size;$j++){
						$paramItems .= '<td style="text-align:center;">'.((!empty($obj->{$row->id}[$j]))?$obj->{$row->id}[$j]:'').'</td>';
					}
					$paramItems .= '</tr>';
                $tbodyData .= $paramItems;
                $i++;
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="14" style="text-align:center;">No Data Found</td></tr>';
        endif;
		
        $this->data['rcount'] = $rcount;
        $this->data['theadData'] = $theadData;
        $this->data['theadData2'] = $theadData2;
        $this->data['tbodyData'] = $tbodyData;
		$pdfData = $this->load->view('vendor_inspection/printVendorInspection',$this->data,true);

		$logo = base_url('assets/images/logo.png');
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">VENDOR INSPECTION REPORT</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">QA/F/03 (Rev.01/17.10.2022)</td>
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
		
        $mpdf = new \Mpdf\Mpdf();
		$pdfFileName='IPR-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
        $mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('L','','','','',5,5,25,20,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	/* Ended By @Raj.F 21-02-2025 */
}
?>