<?php 
class LineInspection extends MY_Controller
{
    private $indexPage = "line_inspection/index";
    private $iprIndexPage = "line_inspection/ipr_index";
    private $formPage = "line_inspection/form";
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "SAR/IPR";
		$this->data['headData']->controller = "lineInspection";
        $this->data['headData']->pageUrl = "lineInspection";
	}
	 
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader('runningJobs');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->lineInspection->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getRunningJobsData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addLineInspection(){
        $data = $this->input->post();
        if(!empty($data['prc_type']) && $data['prc_type'] == 2){
            $dataRow = $this->sop->getPRCDetail(['prc_id'=>$data['prc_id']]);
            $dataRow->process_id = 3;
            $dataRow->current_process = 'RM Cutting';
            $this->data['dataRow'] = $dataRow;
        }else{
            $this->data['dataRow'] = $dataRow = $this->sop->getPRCProcessList(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'single_row'=>1]);
        }
        $this->data['operatorList'] = $this->employee->getEmployeeList();
        $this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
        $defaultRev = $this->ecn->getLastRevision(['item_id'=>$dataRow->item_id]);
        $this->data['last_rev_no'] = (!empty($defaultRev->rev_no) ? $defaultRev->rev_no : "");
		
        $this->data['inspParamHtml'] = $this->getInspectionParameter(['item_id'=>$dataRow->item_id,'process_id'=>$dataRow->process_id,'control_method'=>$data['control_method'],'rev_no'=>(!empty($dataRow->rev_no) ? $dataRow->rev_no : $this->data['last_rev_no'])]);
        $this->data['report_type'] = $data['report_type'];
        $this->data['control_method'] = $data['control_method'];
		$this->load->view($this->formPage,$this->data);
	}

    public function saveLineInspection(){ 
		$data = $this->input->post();
        $errorMessage = Array(); 

		if(empty($data['item_id'])){ $errorMessage['item_id'] = "Item is required.";}
		if(empty($data['rev_no'])){ $errorMessage['general'] = "Revision required.";}

        $insParamData = $this->item->getInspectionParameter(['item_id'=>$data['item_id'],'process_id'=>$data['process_id'],'control_method'=>$data['control_method'],'rev_no'=>$data['rev_no']]);
        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";

        $pre_inspection = Array(); $param_ids = Array();

        if(!empty($insParamData)):
            foreach($insParamData as $row):
                $param = Array();
                $param[] = (!empty($data['reading_'.$row->id]) ? $data['reading_'.$row->id] : ""); 
                $param[] = (!empty($data['result_'.$row->id]) ? $data['result_'.$row->id] : ""); 
                $param[] = (!empty($data['remark_'.$row->id]) ? $data['remark_'.$row->id] : ""); 
                $pre_inspection[$row->id] = $param;
				$param_ids[] = $row->id;
                unset($data['result_'.$row->id],$data['reading_'.$row->id],$data['remark_'.$row->id]);
            endforeach;
        endif;

        $data['observation_sample'] = json_encode($pre_inspection);
		$data['parameter_ids'] = implode(',',$param_ids);
        $data['param_count'] = count($insParamData);

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            unset($data['control_method']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->lineInspection->saveLineInspection($data));
        endif;
	}

    public function iprIndex(){
        $this->data['tableHeader'] = getQualityDtHeader('lineInspection');
        $this->load->view($this->iprIndexPage,$this->data);
    }

    public function getIPRDTRows(){
        $data = $this->input->post();
        $result = $this->lineInspection->getIPRDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getLineInspectionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function editLineInspection(){
        $data = $this->input->post();
        $this->data['lineInspData'] =$dataRow= $this->lineInspection->getLineInspectData(['id'=>$data['id']]);
        $this->data['operatorList'] = $this->employee->getEmployeeList();
        $this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
		$this->data['inspParamHtml'] = $this->getInspectionParameter(['item_id'=>$dataRow->item_id,'process_id'=>$dataRow->process_id,'control_method'=>'IPR','rev_no'=>$dataRow->rev_no,'id'=>$dataRow->id]);
        $this->data['control_method'] = 'IPR';
        $this->load->view($this->formPage,$this->data);
       
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->lineInspection->delete($id));
        endif;
    }

	function printLineInspection($id){
		$this->data['lineInspectData'] = $inspData = $this->lineInspection->getLineInspectData(['id'=>$id]);
        $this->data['paramData'] = $paramData = $this->item->getInspectionParameter(['item_id'=>$inspData->item_id,'process_id'=>$inspData->process_id ,'control_method'=>'IPR','rev_no'=>$inspData->rev_no]); 
		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();

        $tbodyData="";$theadData="";$i=1;$blankRow='';$theadData2="";
        if(!empty($paramData)):
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
					<td style="text-align:left;">'.$row->instrument.'</td>';
                
                    $objData = $this->lineInspection->getLineInspectData(['prc_id'=>$inspData->prc_id,'process_id'=>$inspData->process_id,'insp_date'=>$inspData->insp_date,'rev_no'=>$inspData->rev_no]);
                    $rcount = count($objData);
                    foreach($objData as $read):
                        if($i==1){
                            $insp_time = (!empty($read->insp_time)?date("h:i A",strtotime($read->insp_time)):'');
                            $theadData .= '<th style="text-align:center;" colspan="3">'.$insp_time.'<br/>'.$read->created_name.'</th>';
                            $theadData2 .='<th>Reading</th><th>Result</th><th>Remark</th>';
                        }
                        $obj = New StdClass; 
                        $obj = json_decode($read->observation_sample);
                        
                        $paramItems .= '<td style="text-align:center;">'.((!empty($obj->{$row->id}[0]))?$obj->{$row->id}[0]:'').'</td>';
                        $paramItems .= '<td style="text-align:center;">'.((!empty($obj->{$row->id}[1]))?$obj->{$row->id}[1]:'').'</td>';
                        $paramItems .= '<td style="text-align:left;">'.((!empty($obj->{$row->id}[2]))?$obj->{$row->id}[2]:'').'</td>';
                    endforeach;
                    $paramItems .= '</tr>';
                $tbodyData .= $paramItems;
                $i++;
            endforeach;
            /*for($j=15; $i<=$j; $i++):
                $blankRow .= '<tr>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>';
					for($td=1; $td<=$rcount; $td++){ $blankRow .= '<td></td><td></td><td></td>'; }
                $blankRow .= '</tr>';
            endfor;
            $tbodyData .= $blankRow;*/
        else:
            $tbodyData.= '<tr><td colspan="8" style="text-align:center;">No Data Found</td></tr>';
        endif;
        $this->data['rcount'] = $rcount;
        $this->data['theadData'] = $theadData;
        $this->data['theadData2'] = $theadData2;
        $this->data['tbodyData'] = $tbodyData;
		$pdfData = $this->load->view('line_inspection/printLineInspection',$this->data,true);

		$logo = base_url('assets/images/logo.png');
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">INPROCESS INSPECTION REPORT</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">QA/F/03 (Rev.02/01.01.2025)</td><!--06-05-25-->
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
	
    public function getInspectionParameter($data){
        // $data = $this->input->post();
        $paramData = $this->item->getInspectionParameter(['item_id'=>$data['item_id'],'process_id'=>$data['process_id'],'control_method'=>$data['control_method'],'rev_no'=>$data['rev_no']]);
        $obj = new StdClass;
        if(!empty($data['id'])){
            $oldData = $this->lineInspection->getLineInspectData(['id'=>$data['id']]);
            if(!empty($oldData)):
                $obj = json_decode($oldData->observation_sample); 
            endif;
        }
       
        $tbodyData="";$i=1; 
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
                $tbodyData.='<td ><input type="text" name="reading_'.$row->id.'" class="form-control text-center"  value="'.(!empty($obj->{$row->id}[0])?$obj->{$row->id}[0]:'').'"></td>';
                $tbodyData.='<td >
                                <select class="form-control" name="result_'.$row->id.'">
                                    <option value="OK" '.((!empty($obj->{$row->id}[1]) && $obj->{$row->id}[1] == 'OK')?'selected':'').'>OK</option>
                                    <option value="NOT OK" '.((!empty($obj->{$row->id}[1]) && $obj->{$row->id}[1] == 'NOT OK')?'selected':'').'>NOT OK</option>
                                </select>
                            </td>';
                $tbodyData.='<td ><input type="text" name="remark_'.$row->id.'" class="form-control text-center"  value="'.(!empty($obj->{$row->id}[2])?$obj->{$row->id}[2]:'').'"></td>';
                $tbodyData.='</tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="13" style="text-align:center;">No Data Found</td></tr>';
        endif;
        return $tbodyData;
        // $this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
    }

	public function addFirstPieceSAR(){
        $data = $this->input->post();
        $this->data['ref_id'] = (!empty($data['id']) ? $data['id'] : 0);

        if(!empty($data['prc_type']) && $data['prc_type'] == 2){
            $dataRow = $this->sop->getPRCDetail(['prc_id'=>$data['prc_id']]);
            $dataRow->process_id = 3;
            $dataRow->current_process = 'RM Cutting';
            $this->data['dataRow'] = $dataRow;
        }else{
            $this->data['dataRow'] = $dataRow = $this->sop->getPRCProcessList(['prc_id'=>$data['prc_id'],'process_id'=>$data['process_id'],'single_row'=>1]);
        }

        $this->data['operatorList'] = $this->employee->getEmployeeList();
        $this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
        $defaultRev = $this->ecn->getLastRevision(['item_id'=>$dataRow->item_id]);
        $this->data['last_rev_no'] = (!empty($defaultRev->rev_no) ? $defaultRev->rev_no : "");
		
        $this->data['inspParamHtml'] = $this->getInspectionParameter(['item_id'=>$dataRow->item_id,'process_id'=>$dataRow->process_id,'control_method'=>$data['control_method'],'rev_no'=>(!empty($dataRow->rev_no) ? $dataRow->rev_no : $this->data['last_rev_no'])]);

        $this->data['report_type'] = $data['report_type'];
        $this->data['control_method'] = $data['control_method'];
		$this->load->view('line_inspection/sar_form',$this->data);
	}

    public function editFirstPieceSAR(){
        $data = $this->input->post();
        $this->data['lineInspData'] = $dataRow = $this->lineInspection->getLineInspectData(['id'=>$data['id']]);
        $this->data['operatorList'] = $this->employee->getEmployeeList();
        $this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
        $this->data['inspParamHtml'] = $this->getInspectionParameter(['item_id'=>$dataRow->item_id,'process_id'=>$dataRow->process_id,'control_method'=>'SAR','rev_no'=>$dataRow->rev_no,'id'=>$dataRow->id]);
        $this->data['control_method'] = 'SAR';
		$this->load->view('line_inspection/sar_form',$this->data);       
    }

	function printSAR($id){
		$this->data['lineInspectData'] = $inspData = $this->lineInspection->getLineInspectData(['id'=>$id, 'sarData'=>1]);
        $this->data['paramData'] = $this->item->getInspectionParameter(['item_id'=>$inspData->item_id,'process_id'=>$inspData->process_id ,'control_method'=>'SAR','rev_no'=>$inspData->rev_no]);
		$this->data['companyData'] = $this->masterModel->getCompanyInfo();
        
		$pdfData = $this->load->view('line_inspection/print_sar',$this->data,true);

		$logo = base_url('assets/images/logo.png');

		$htmlHeader = '<table class="table">
            <tr>
                <td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
                <td class="org_title text-center" style="font-size:1rem;width:50%">SETUP APPROVAL REPORT</td>
                <td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">QA-F-13 (Rev.04/01.01.2025)</td> <!-- 06-05-25 -->
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
		$pdfFileName = 'SAR-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
        $mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('L','','','','',5,5,25,20,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
}
?>