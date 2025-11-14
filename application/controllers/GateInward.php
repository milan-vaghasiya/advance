<?php
class GateInward extends MY_Controller{
    private $indexPage = "gate_inward/index";
    private $form = "gate_inward/form";
    private $inspectionFrom = "gate_inward/material_inspection";
    private $ic_inspect = "gate_inward/ic_inspect";
	private $test_report = "gate_inward/test_report";
	private $iqc_index = "gate_inward/inward_qc";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Goods Receipt Note";
		$this->data['headData']->controller = "gateInward";
        $this->data['headData']->pageUrl = "gateInward";
    }

    public function index(){
        $this->data['tableHeader'] = getStoreDtHeader("gateInward");
		$this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 1){
        $data = $this->input->post();
        $data['trans_status'] = $status;
        $data['grn_type'] = 1;
        $result = $this->gateInward->getDTRows($data);
        $sendData = array();$i=($data['start']+1);

        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getGateInwardData($row);
        endforeach;

        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addGateInward(){
        $data = $this->input->post();
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,2,3,4,5,6,7,8,9"]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[1,2,3]]);
        $this->data['trans_no'] = $this->gateInward->getNextGrnNo();
        $this->data['trans_prefix'] = 'GI/'.getYearPrefix('SHORT_YEAR').'/';
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList(['final_location'=>1,'location_not_in'=>[$this->FIR_STORE->id,$this->RTD_STORE->id,$this->SCRAP_STORE->id,$this->CUT_STORE->id,$this->PACKING_STORE->id]]);
        $this->load->view($this->form,$this->data);
    }

    public function getPoNumberList(){
        $data = $this->input->post();
        $data['entry_type'] = $this->transMainModel->getEntryType(['controller'=>'purchaseOrders'])->id;
        $poList = $this->purchaseOrder->getPartyWisePoList($data);

        $options = '<option value="">Select Purchase Order</option>';
        foreach($poList as $row):
            $options .= '<option value="'.$row->po_id.'" data-po_no="'.$row->trans_number.'" >'.$row->trans_number.'</option>';
        endforeach;

        $this->printJson(['status'=>1,'poOptions'=>$options]);
    }

    public function getItemList(){
        $data = $this->input->post();
        $data['entry_type'] = $this->transMainModel->getEntryType(['controller'=>'purchaseOrders'])->id;

        $options = '<option value="">Select Item Name</option>';
        $fgOptions = '<option value="">Select Finish Goods</option>';
		
        if(empty($data['po_id'])):
            $itemList = $this->item->getItemList(['item_type'=>"1,2,3,4,5,6,7,8,9"]);
            $options .= getItemListOption($itemList);
			
			 $fgItemList = $this->item->getItemList(['item_type'=>1]);
            $fgOptions .= getItemListOption($fgItemList);
        else:
            $itemList = $this->purchaseOrder->getPendingPoItems($data);
            foreach($itemList as $row):
                $pending_qty = (!empty($row->pending_qty) ? floatval($row->pending_qty) : 0);
                $options .= '<option value="'.$row->item_id.'" data-po_trans_id="'.$row->po_trans_id.'" data-so_trans_id="'.$row->so_trans_id.'" data-fg_item_id="'.$row->fg_item_id.'" data-price="'.$row->price.'" data-disc_per="'.$row->disc_per.'" data-item_type="'.$row->item_type.'">
					'.(!empty($row->item_code)?'[ '.$row->item_code.' ] ':'').$row->item_name.(!empty($row->material_grade) ? ' '.$row->material_grade : '').' [ Pending Qty : '.$pending_qty.' ]
				</option>';
            
				//$fgOptions .= '<option value="'.$row->fg_item_id.'" >'.(!empty($row->fg_item_code)?'[ '.$row->fg_item_code.' ] ':'').$row->fg_item_name.'</option>';
			endforeach;
        endif;

        $this->printJson(['status'=>1,'itemOptions'=>$options,'fgItemOptions'=>$fgOptions]);
    }

    public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();
        
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['batchData']))
            $errorMessage['batch_details'] = "Item Details is required.";
		
        if(empty($data['trans_date'])){
            $errorMessage['trans_date'] = 'GRN Date is required.';
        }else{
            if (($data['trans_date'] < $this->startYearDate) OR ($data['trans_date'] > $this->endYearDate)){
                $errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
            }
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])):
                $data['trans_no'] = $this->gateInward->getNextGrnNo();
                $data['trans_prefix'] = 'GI/'.getYearPrefix('SHORT_YEAR').'/';
                $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
            endif;
            $this->printJson($this->gateInward->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $gateInward = $this->gateInward->getGateInward($data['id']);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>[1,2,3]]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>[1,2,3,4,5,6,7,8,9]]);
        $this->data['gateInwardData'] = $gateInward;
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList(['final_location'=>1,'location_not_in'=>[$this->FIR_STORE->id,$this->RTD_STORE->id,$this->SCRAP_STORE->id,$this->CUT_STORE->id,$this->PACKING_STORE->id]]);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->gateInward->delete($id));
        endif;
    }
    
	public function ir_print($id){
        $irData = $this->gateInward->getInwardItem(['id'=>$id]);
        $companyData = $this->masterModel->getCompanyInfo();  
		$itemList="";$i=1;
        $logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
       
        if($irData->trans_status == 1){
            $header = "UNDER TEST";
            $qrIMG = "";
            $qty = $irData->qty;
            $batchNo = $irData->batch_no;

                $itemList .='<style>.top-table-border th,.top-table-border td{font-size:12px;}</style>
                    <table class="table top-table-border">
                        <tr>
                            <td rowspan="6" style="font-size: 22px;text-rotate: 90;max-width: 20px"> '.$header.' </td>
                            <td colspan="2" class="text-center"><img src="'.$logo.'" style="max-height:40px;"></td>
                            <td colspan="2" class="org_title text-right" style="font-size:18px;">IIR Tag</td>
                        </tr>
                        <tr class="text-left">
                            <th>GI No</th>
                            <td>'.$irData->trans_number.'</td>
                            <th>GI Date</th>
                            <td>'.date("d-m-Y", strtotime($irData->trans_date)).'</td>
                        </tr>
                        <tr class="text-left"> 
                            <th>Part Name</th>
							<td colspan="3">'.$irData->item_name.(!empty($irData->material_grade) ? ' '.$irData->material_grade : '').'</td>
                        </tr>
                        <tr class="text-left"> 
                            <th>Supplier</th>
                            <td colspan="3">'.$irData->party_name.'</td>
                        </tr>
                        <tr class="text-left">
							<th>Batch No.</th>
                            <td>'.$irData->batch_no.'</td>
                            <th>Batch Qty</th>
                            <td>'.$qty.' </td>
                        </tr>
                        <tr class="text-left"> 
                            <th>Printed At</th>
                            <td colspan="3">'.date("d-m-Y h:i:s a").'</td>
                        </tr>
                    </table>';
        }else{
            $batchData = $this->itemStock->getStockTrans(['child_ref_id'=>$irData->id,'trans_type'=>'GRN']);
            $qrIMG = base_url('assets/uploads/iir_qr/'.$irData->id.'.png');
            if(!file_exists($qrIMG)){
                $qrText = ($irData->item_type == 6) ? $irData->item_id.'~'.$irData->item_code : $batchData->item_id.'~'.$batchData->batch_no;
                $file_name = $irData->id;
                $qrIMG = base_url().$this->getQRCode($qrText,'assets/uploads/iir_qr/',$file_name);
            }
            $header = "QC Ok";
            $qty = ($irData->item_type == 6) ? $irData->ok_qty : $batchData->qty;
            $batchNo = ($irData->item_type == 6) ? $irData->item_code : $batchData->batch_no;

            $itemList ='<style>.top-table-border th,.top-table-border td{font-size:12px;}</style>
                    <table class="table top-table-border">
                        <tr>
                            <td rowspan="6" style="font-size: 20px;text-rotate: 90;max-width: 20px"> APPROVED </td>
                            <td><img src="'.$logo.'" style="max-height:40px;"></td>
                            <td class="org_title text-center" style="font-size:16px;">QC OK</td>
                            <td colspan="2" rowspan="2" class="text-center" style="padding:1px;"><img src="'.$qrIMG.'" style="height:25mm;"></td>
                        </tr>
                        <tr class="text-left"> 
                            <td class="text-center"><b>GI No</b><br>'.$irData->trans_number.'</td>
                            <td class="text-center"><b>GI Date</b><br>'.date("d-m-Y", strtotime($irData->trans_date)).'</td>
                        </tr>
                        <tr class="text-left"> 
                            <th>Part Name</th>
                            <td colspan="3">'.$irData->item_name.(!empty($irData->material_grade) ? ' '.$irData->material_grade : '').'</td>
                        </tr>
                        <tr class="text-left"> 
                            <th>Supplier</th>
                            <td colspan="3">'.$irData->party_name.'</td>
                        </tr>
                        <tr class="text-left"> 
                            <th>Batch No.</th>
                            <td>'.$batchNo.'  </td>
                            <th>Batch Qty</th>
                            <td>'.$qty.' </td>
                        </tr>
                        <tr class="text-left"> 
                            <th>Printed At</th>
                            <td colspan="3">'.date("d-m-Y h:i:s a").'</td>
                        </tr>
                    </table>';
        }

        $pdfData = '<div style="width:100mm;height:25mm;">'.$itemList.'</div>';

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 68]]);
		$pdfFileName='IR_PRINT.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P','','','','',2,2,2,2,2,2);
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }

	public function inwardQC($type=1, $status=1){
        $this->data['headData']->pageUrl = "gateInward";
        $this->data['headData']->pageTitle = "Inward QC";
		if($type == 1 && in_array($status,[1,2])){
			$this->data['tableHeader'] = getStoreDtHeader("inwardQC");
		}else if($type = 2){
			$this->data['tableHeader'] = getStoreDtHeader("pendingQc");
		}
		$this->data['type'] = $type;
		$this->data['status'] = $status;
		$this->load->view($this->iqc_index,$this->data);
    }

    public function getInwardQcDTRows($type=1, $status=1){
        $data = $this->input->post();
        $data['trans_status'] = $status;$data['type'] = 'QC'; 

        if($type == 2){
            $result = $this->gateInward->getPendingQcDTRows($data);
        }else{
            $result = $this->gateInward->getDTRows($data);
        }
        $sendData = array();$i=($data['start']+1);

        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
            $row->controller = $this->data['headData']->controller;
            if($type == 2){
                $sendData[] = getPendingQcData($row);
            }else{
                $sendData[] = getInwardQcData($row);
            }
        endforeach;

        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
    public function materialInspection(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->gateInward->getInwardItem(['id'=>$data['id']]);
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList(['final_location'=>1,'location_not_in'=>[$this->FIR_STORE->id,$this->RTD_STORE->id,$this->SCRAP_STORE->id,$this->CUT_STORE->id,$this->PACKING_STORE->id]]);
            
        $this->load->view($this->inspectionFrom,$this->data);   
    }

    public function saveInspectedMaterial(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['location_id'])){
            $errorMessage['location_id'] = "Location is required.";
        }
        if(!empty($data['is_inspection'])){
            if(empty($data['batch_no'])){
                $this->printJson(['status'=>0,'message'=>'Batch is required.']);
            }
            //$gradeData = $this->gateInward->checkTestReportStatus(['grn_trans_id'=>$data['id'],'grade_id'=>$data['grade_id']]);
			$gradeData = $this->gateInward->checkTestReportStatus(['grn_trans_id'=>$data['id'],'item_id'=>$data['fg_item_id']]);			
            $reqArray = explode(",",$gradeData->required_test);
            $testArray = explode(",",$gradeData->tested_report);
            
            $resultArray = array_diff($reqArray,$testArray);
            if(!empty($resultArray)){
                $this->printJson(['status'=>2,'message'=>'Some Test reports are pending']);
            }
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
		    $result = $this->gateInward->saveInspectedMaterial($data);
            $this->printJson($result);
        endif;
    }

    public function getPartyInwards(){
        $data = $this->input->post();
        $this->data['orderItems'] = $this->gateInward->getPendingInwardItems($data);
        $this->load->view('purchase_invoice/create_invoice',$this->data);
    }

    public function inInspection_pdf($id){
		$this->data['inInspectData'] = $inInspectData = $this->gateInward->getInwardItem(['id'=>$id]);
        $this->data['observation'] = $this->gateInward->getInInspectData(['mir_trans_id'=>$id]);
        $this->data['paramData'] = $this->item->getInspectionParameter(['item_id'=>$inInspectData->fg_item_id,'rev_no'=>$inInspectData->rev_no,'control_method'=>'IIR']);

		$inInspectData->fgCode="";
		if(!empty($inInspectData->fgitem_id)): $i=1; 
			$fgData = $this->grnModel->getFinishGoods($inInspectData->fgitem_id);
			$item_code = array_column($fgData,'item_code');
			$inInspectData->fgCode = implode(", ",$item_code);
		endif;

		$prepare = $this->employee->getEmployee(['id'=>$inInspectData->created_by]);
		$prepareBy = $prepare->emp_name.' <br>('.formatDate($inInspectData->created_at).')'; 
		$approveBy = '';
		if(!empty($inInspectData->is_approve)){
			$approve = $this->employee->getEmployee(['id'=>$inInspectData->is_approve]);
			$approveBy .= $approve->emp_name.' <br>('.formatDate($inInspectData->approve_date).')'; 
		}
		$response="";
		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('gate_inward/ic_inspect_pdf',$this->data,true);
		
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">INCOMING INSPECTION REPORT</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">QA/F/10 (Rev.02/dtd.21-02-2019)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">'.$prepareBy.'</td>
							<td style="width:25%;" class="text-center">'.$approveBy.'</td>
						</tr>
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
							<td style="width:25%;" class="text-center"><b>Approved By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		// print_r($pdfData);exit;
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(120,60));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,25,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');		
	}

    public function getInwardQc(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->gateInward->getInwardItem($data);
        $this->data['inInspectData'] =$inInspectData = $this->gateInward->getInInspectData(['mir_trans_id'=>$data['id']]);
        $this->data['fgList'] = $this->item->getProductKitData(['ref_item_id'=>$dataRow->item_id]);
        if(!empty($inInspectData)){
            $this->data['revHtml'] = $this->getItemRevList(['item_id'=>$inInspectData->fg_item_id,'rev_no'=>$inInspectData->rev_no])['revHtml'];
        }
        $this->load->view($this->ic_inspect,$this->data); //04-10-2024
    }


    public function getIncomingInspectionData(){
        $data = $this->input->post();
        $paramData = $this->item->getInspectionParameter(['item_id'=>$data['item_id'],'rev_no'=>$data['rev_no'],'control_method'=>'IIR']);
        $oldData = $this->gateInward->getInInspectData(['mir_trans_id'=>$data['mir_trans_id']]);
        $obj = new StdClass;
        if(!empty($oldData)):
            $obj = json_decode($oldData->observation_sample); 
        endif;
        $tbodyData="";$i=1; $theadData='';
                $theadData .= '<tr class="thead-info" style="text-align:center;">
                            <th rowspan="2" style="width:3%;">#</th>
                            <th rowspan="2" style="width:15%">Parameter</th>
                            <th rowspan="2" style="width:15%">Specification</th>
                            <th colspan="2" style="width:15%">Tolerance</th>
                            <th colspan="2" style="width:15%">Specification Limit</th>
                            <th rowspan="2" style="width:15%">Instrument</th>
                            <th colspan="'.$data['sampling_qty'].'" style="text-align:center;">Observation on Samples</th>
                            <th rowspan="2" style="width:7%">Result</th>
                        </tr>
                        <tr style="text-align:center;">';
                        $theadData .='<th style="width:7%">Min</th>
                                    <th style="width:8%">Max</th>
                                    <th style="width:7%">LSL</th>
                                    <th style="width:8%">USL</th>';
                        for($j=1; $j<=$data['sampling_qty']; $j++):
                            $theadData .= '<th>'.$j.'</th>';
                        endfor;    
                $theadData .='</tr>';
        if(!empty($paramData)):
            foreach($paramData as $row):
                $lsl = floatVal($row->specification) - $row->min;
                $usl = floatVal($row->specification) + $row->max;
                $tbodyData.= '<tr>
                            <td style="text-align:center;width:3px">'.$i++.'</td>
                            <td style="text-align:center;width:10px;">'.$row->parameter.'</td>
                            <td style="text-align:center;width:10px;">'.$row->specification.'</td>   
                            <td style="text-align:center;width:5px;">'.$row->min.'</td>
                            <td style="text-align:center;width:5px;">'.$row->max.'</td>
                            <td style="text-align:center;width:5px;">'.$lsl.'</td>
                            <td style="text-align:center;width:5px;">'.$usl.'</td>
                            <td style="text-align:center;width:10px;">'.$row->instrument.'</td>';
                            $c=0;
                            for($j=1; $j<=$data['sampling_qty']; $j++):
                                $value = (!empty($obj->{$row->id}[$c]) && $c < (count($obj->{$row->id})-1))?$obj->{$row->id}[$c]:'';
                                $tbodyData.=' <td style="min-width:100px;"><input type="text" name="sample'.($j).'_'.$row->id.'" class="form-control" value="'.$value.'"></td>';
                                $c++;
                            endfor;
                            $resultval =  !empty($obj)?(!empty($obj->{$row->id}[$c])?$obj->{$row->id}[count($obj->{$row->id})-1]:''):'';
                            $tbodyData.='<td style="min-width:80px;"><input name="result_'.$row->id.'" class="form-control text-center" value="'.$resultval.'"></td>';
                $tbodyData.='</tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="14" style="text-align:center;">No Data Found</td></tr>';
        endif;
        $this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"theadData"=>$theadData]);
    }

    public function saveInwardQc(){ 
		$data = $this->input->post(); 
        $errorMessage = Array(); 

		if(empty($data['item_id'])){ $errorMessage['item_id'] = "Item is required.";}
		if(empty($data['fg_item_id'])){ $errorMessage['fg_item_id'] = "Item is required.";}
		if(empty($data['rev_no'])){ $errorMessage['rev_no'] = "Rev no is required.";}

        $insParamData = $this->item->getInspectionParameter(['item_id'=>$data['fg_item_id'],'rev_no'=>$data['rev_no'],'control_method'=>'IIR']);
        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";

        $pre_inspection = Array(); $param_ids = Array();

        if(!empty($insParamData)):
            foreach($insParamData as $row):
                $param = Array();
                for($j = 1; $j <= $data['sampling_qty']; $j++):
                    $param[] = $data['sample'.$j.'_'.$row->id];
                    unset($data['sample'.$j.'_'.$row->id]);
                endfor;
                $param[] = $data['result_'.$row->id]; 
                $pre_inspection[$row->id] = $param;
				$param_ids[] = $row->id;
                unset($data['result_'.$row->id]);
            endforeach;
        endif;

        $data['observation_sample'] = json_encode($pre_inspection);
		$data['parameter_ids'] = implode(',',$param_ids);

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])){
                $data['trans_no'] = $this->gateInward->getNextIIRNo();
                $data['trans_number'] = "IIR".sprintf(n2y(date('Y'))."%03d",$data['trans_no']);
                $data['trans_date'] = date("Y_m-d");
                $data['created_by'] = $this->session->userdata('loginId');
            }
            
            $this->printJson($this->gateInward->saveInwardQc($data));
        endif;
	}

    /* Test Report */
    public function getTestReport(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->gateInward->getTestReport(['grn_id'=>$data['grn_id']]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>'1,2,3']); 
        $this->data['giData'] = $this->gateInward->getInwardItem(['id'=>$data['id']]);
		$this->data['itemList'] = $this->instrument->getItem(['status'=>1]);
        $this->load->view($this->test_report,$this->data);
    }

    public function saveTestReport(){
        $data = $this->input->post();
        $errorMessage = array();

        if(isset($data['agency_id']) && $data['agency_id'] == ''){
            $errorMessage['agency_id'] = "Agency Name is required.";
        }
        if(isset($data['test_type']) && $data['test_type'] == ''){
            $errorMessage['test_type'] = "Test Type is required.";
        }
        if(empty($data['sample_qty'])){
            $errorMessage['sample_qty'] = "Sample Qty is required.";
        }
        if(empty($data['batch_no'])){
            $errorMessage['batch_no'] = "Batch No. is required.";
        }
        if(empty($data['heat_no'])){
            $errorMessage['heat_no'] = "Ref./Heat No. is required.";
        }
        if(empty($data['heat_verify'])){
            $this->printJson(['status'=>0,'message'=>'You can not save this test report until you verify heat no.']);
        }
        if($data['agency_id'] == 0){
            if(isset($data['head_id'])){
				foreach($data['head_id'] as $k=>$head_id){
					foreach($data['param'][$head_id] as $key=>$value){
                        if(($value['min'] > 0 || $value['max'] > 0)){ 
                            if($value['result'] == ''){
                                $errorMessage['result'.$key] = "Result. is required.";
                            }/*else{
                                if((($value['requirement'] == 1) && ($value['result'] < $value['min'] || $value['result'] > $value['max'])) || ($value['requirement'] == 2 && $value['result'] < $value['min']) || ($value['requirement'] == 3 && $value['result'] > $value['max']))
                                {
                                    if($data['test_result'] != 'Accept U.D.'){
                                        if($data['test_result'] == 'Accept' || $data['test_result'] == ''){
                                            $errorMessage['test_result'] = "Invalid Test Result.";
                                        }
                                    }
                                }
                            }*/
                        }
					}
				}
			}
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(!empty($_FILES['tc_file']['name'][0])):
                $file_upload = array();$f=1;
                if($_FILES['tc_file']['name'][0] != null || !empty($_FILES['tc_file']['name'][0])):
                    $this->load->library('upload');
                    foreach ($_FILES['tc_file']['tmp_name'] as $key => $value):
                        $_FILES['userfile']['name']     = $_FILES['tc_file']['name'][$key];
                        $_FILES['userfile']['type']     = $_FILES['tc_file']['type'][$key];
                        $_FILES['userfile']['tmp_name'] = $_FILES['tc_file']['tmp_name'][$key];
                        $_FILES['userfile']['error']    = $_FILES['tc_file']['error'][$key];
                        $_FILES['userfile']['size']     = $_FILES['tc_file']['size'][$key];
                        
                        $imagePath = realpath(APPPATH . '../assets/uploads/test_report/');
                        $fileName = 'test_report_'.time().'_'.$_FILES['tc_file']['name'][$key];
                        $config = ['file_name' => $fileName,'allowed_types' => '*','max_size' => 10240,'overwrite' => TRUE, 'upload_path'=>$imagePath];
        
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload()):
                            $errorMessage['tc_file'] = $this->upload->display_errors();
                            $this->printJson(["status"=>0,"message"=>$errorMessage]);
                        else:
                            $uploadData = $this->upload->data();
                            $file_upload[] = $uploadData['file_name'];
                        endif;
                        $f++;
        			endforeach;
        			if(!empty($file_upload)):
        			    $data['tc_file'] = implode(",",$file_upload);
            		endif; 
    			endif;
            endif;

            $headArray = [] ;
            if(!empty($data['head_id'])){
                foreach($data['head_id'] as $k=>$head_id){
                    $json=[];
                    foreach($data['param'][$head_id] as $key=>$value){
                        unset($data['param'][$head_id][$key]['min'], $data['param'][$head_id][$key]['max'], $data['param'][$head_id][$key]['requirement']);
                        $json[str_replace(" ","",$data['param'][$head_id][$key]['param'])]=$data['param'][$head_id][$key];
                    }
                    $headArray[]=[
                        'id'=>$data['insp_id'][$k],
                        'head_id'=>$head_id,
                        'parameter'=>json_encode($json),
                        'grade_id'=>$data['grade_id'],
						'item_id'=>$data['fg_item_id'],
                    ];
                }
            }
            
            $postData = [
                'id' => $data['id'],
                'grn_id' => $data['grn_id'],
                'grn_trans_id' => $data['grn_trans_id'],
                'agency_id' => $data['agency_id'],
                'name_of_agency' => $data['name_of_agency'],
                'test_type' => $data['test_type'],
                'test_report_no' => $data['test_report_no'],
                'inspector_name' => $data['inspector_name'],
                'sample_qty' => $data['sample_qty'],
                'batch_no' => $data['batch_no'],
				'ht_batch' => (!empty($data['ht_batch']) ? $data['ht_batch'] : NULL),
                'heat_no' => $data['heat_no'],
                'test_result' => $data['test_result'],
                'test_remark' => $data['test_remark'],
                'headData'=>$headArray,
                'spc_instruction' => $data['spc_instruction'],
				'ins_type' => (!empty($data['ins_type']) ? $data['ins_type'] : 0),
                'inst_id' => (!empty($data['inst_id']) ? $data['inst_id'] : 0),
                'created_by'=>$this->session->userdata('loginId')
            ];      
			
			if(!empty($data['tc_file'])){
				$postData['tc_file'] = $data['tc_file'];
			}

            $this->printJson($this->gateInward->saveTestReport($postData));
        endif;
    }

    public function testReportHtml(){
        $data = $this->input->post();
		$data['grnData'] = 1;
        $result = $this->gateInward->getTestReport($data);
		$i=1; $tbody='';
        
		if(!empty($result)):
			foreach($result as $row):
                $tdDownload=''; $editBtn=''; $deleteBtn=''; $approveBtn='';

                if(!empty($row->tc_file)) { 
                    $tcFiles = explode(',',$row->tc_file);
                    foreach($tcFiles as $key=>$val):
                        $tdDownload .= '<a href="'.base_url('assets/uploads/test_report/'.$val).'" target="_blank"><i class="fa fa-download"></i><br>';
                    endforeach; 
                }
                $encRow = json_encode($row);
                if(empty($row->approve_by)){
                    $approveParam = "{'postData':{'id' : ".$row->id."}, 'fnsave':'approveTestReport', 'message':'Are you sure want to Approve this Test Report?','res_function':'getTestReportHtml'}";
                    $approveBtn = '<a class="btn btn-sm btn-outline-success permission-modify mr-2" href="javascript:void(0)" datatip="Approve" flow="up" onclick="approvalStore('.$approveParam.');"><i class="fa fa-check"></i></a>'; 
                    
                    $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Test Report','res_function':'getTestReportHtml','fndelete':'deleteTestReport'}";
                    $deleteBtn = '<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-sm btn-outline-danger permission-remove" datatip="Remove" flow="up"><i class="mdi mdi-trash-can-outline"></i></button>';
					
					/*
                    if(($row->name_of_agency == 'Inhouse') || ($row->test_result != '' && !empty($row->tc_file))){
                        $editBtn = "<button type='button' onclick='editTcReport(".$encRow.",this);' class='btn btn-sm btn-outline-warning mr-2' datatip='Edit' flow='up'><i class='fas fa-edit'></i></button>";
                    }
					*/
                }     
				
				$editBtn = "<button type='button' onclick='editTcReport(".$encRow.",this);' class='btn btn-sm btn-outline-warning mr-2' datatip='Edit' flow='up'><i class='fas fa-edit'></i></button>";
				
                $printBtn = '<a class="btn btn-sm btn-outline-primary mr-2" href="'.base_url('gateInward/printTcReport/'.$row->id).'" target="_blank" datatip="Print" flow="up"><i class="fas fa-print"></i></a>';
				
                $printChallan = '<a href="'.base_url('gateInward/printReceiveTcReport/'.$row->id).'"class="btn btn-sm btn-outline-info mr-2" datatip="Challan Print" flow="up" target="_blank"><i class="fas fa-print"></i></a>';

				$tbody.= '<tr>
                        <td class="text-center">'.$i++.'</td>
                        <td>'.$row->name_of_agency.'</td>
                        <td>'.$row->ins_type.'</td>
                        <td>'.(!empty($row->test_description)?$row->test_description:'').'</td>
                        <td class="text-center">'.$row->test_report_no.'</td>
                        <td>'.$row->inspector_name.'</td>
                        <td class="text-center">'.floatval($row->sample_qty).'</td>
                        <td class="text-center">'.$row->batch_no.'</td>
                        <td class="text-center">'.$row->heat_no.'</td>
                        <td class="text-center">'.$row->test_result.'</td>
                        <td class="text-center">'.$tdDownload.'</td>
                        <td>'.$row->test_remark.'</td>
                        <td>'.$row->spc_instruction.'</td>
                        <td>'.(!empty($row->approve_by) ? '<span class="badge bg-success fw-semibold font-11">Approved</span>' : '<span class="badge bg-danger fw-semibold font-11">Pending</span>').'</td>
						<td class="text-center">
                            '.$printBtn.$printChallan.$approveBtn.$editBtn.$deleteBtn.'							
						</td>
					</tr>';
			endforeach;
        else:
            $tbody = '<tr><td colspan="15" class="text-center">No data found.</td></tr>';
		endif;
        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
	}

    public function deleteTestReport(){ 
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$this->printJson($this->gateInward->deleteTestReport($data['id']));
		endif;
    }

    public function getTestTypeList(){
        $data = $this->input->post();
        //$testTypeList = $this->materialGrade->getTcMasterData(['ins_type'=>$data['ins_type'],'grade_id'=>$data['grade_id']]);
		$testTypeList = $this->materialGrade->getTcMasterData(['ins_type'=>$data['ins_type'],'item_id'=>$data['fg_item_id']]);
        if(!empty($data['id'])){
            //$testData = $this->gateInward->getTestReport(['grn_id'=>$data['id'],'single_row'=>1]);
			$testData = $this->gateInward->getTestReport(['id'=>$data['id'],'single_row'=>1]);
        }
        $oldReportData = $this->gateInward->getTestReport(['grn_trans_id'=>$data['grn_trans_id']]);
        $oldReportData = !empty($oldReportData)?$oldReportData:[];
        $options = '<option value="">Select Test Type</option>';
        
        foreach($testTypeList as $row):
		    $selected = (!empty($testData->test_type) && $testData->test_type == $row->test_type)?"selected":"";
            $options .= '<option value="'.$row->test_type.'" '.$selected.' data-sample_1="'.$row->sample_1.'" data-sample_2="'.$row->sample_2.'" data-sample_3="'.$row->sample_3.'">'.$row->test_name.((!in_array($row->test_type,array_column($oldReportData,'test_type'))) ? '[ Pending ]' : '').'</option>';
        endforeach;

        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getTestReportParam(){
        $data = $this->input->post();
		
        //$tcHeadList = $this->materialGrade->getTcMasterData(['grade_id'=>$data['grade_id'],'test_type'=>$data['test_type'],'tcParameter'=>1]);
		$tcHeadList = $this->materialGrade->getTcMasterData(['item_id'=>$data['fg_item_id'],'test_type'=>$data['test_type'],'tcParameter'=>1]);
        $tc = [];$html='';$tcData = [];$totalWeight = "";
        if(!empty($data['main_id'])){
            $tcData = $this->gateInward->getTestParameterData(['main_id'=>$data['main_id']]);
            if(!empty($tcData)){
                foreach($tcData as $row){
                    $tc[$row->head_id]=$row;
                }
            }
        }elseif(!empty($data['old_tc'])){
            //$oldGrnData = $this->gateInward->getTestReport(['lastOldReport'=>1,'grade_id'=>$data['grade_id'],'batch_no'=>$data['batch_no'],'gtr_id'=>$data['id'],'single_row'=>1,'grnData'=>1]);
            $oldGrnData = $this->gateInward->getTestReport(['lastOldReport'=>1,'item_id'=>$data['fg_item_id'],'batch_no'=>$data['batch_no'],'gtr_id'=>$data['id'],'single_row'=>1,'grnData'=>1]); 
			if(!empty($oldGrnData)){
                $totalWeight = $oldGrnData->sample_qty;
                $tcData = $this->gateInward->getTestParameterData(['main_id'=>$oldGrnData->id]);
                
                if(!empty($tcData)){
                    foreach($tcData as $row){
                        unset($row->id);
                        $tc[$row->head_id]=$row;
                    }
                }
            }
        }   
        $tcHeads = array_reduce($tcHeadList, function($tcHeads, $head) { $tcHeads[$head->test_name][] = $head; return $tcHeads; }, []);
        foreach ($tcHeads as $head_name => $heads):
            $jsonData = new stdClass();$tcMaster = [];
            $id="";
            if(!empty($tc[$heads[0]->test_type])){
                $jsonData = json_decode($tc[$heads[0]->test_type]->parameter);
                $id = (!empty($tc[$heads[0]->test_type]->id)?$tc[$heads[0]->test_type]->id:'');
              
            }
           
            if(!empty($heads[0]->parameter)){
                $tcMaster = json_decode($heads[0]->parameter);
            }
            $title = ' <div class="col-md-12"><h6>'.$heads[0]->head_name.' ['.$head_name.'] :</h6></div>   
            <input type="hidden" name="head_id[]"  value="'.$heads[0]->test_type.'">
            <input type="hidden" name="insp_id[]"  value="'.$id.'">';
            $thead = '';$tbody = '';
            
            foreach ($heads as $row):
                $colspan=""; $headValue ='';$headLable='';
                $minReq = (!empty($tcMaster->{str_replace(" ","",$row->insp_param)}->min)?$tcMaster->{str_replace(" ","",$row->insp_param)}->min:0);
                $maxReq = (!empty($tcMaster->{str_replace(" ","",$row->insp_param)}->max)?$tcMaster->{str_replace(" ","",$row->insp_param)}->max:0);
                $otherReq = (!empty($tcMaster->{str_replace(" ","",$row->insp_param)}->other)?$tcMaster->{str_replace(" ","",$row->insp_param)}->other:'');
                if($row->requirement == 1){ $colspan = 2; $headValue = (!empty($tcMaster->{str_replace(" ","",$row->insp_param)}->min)?$tcMaster->{str_replace(" ","",$row->insp_param)}->min.'-':0).(!empty($tcMaster->{str_replace(" ","",$row->insp_param)}->max)?$tcMaster->{str_replace(" ","",$row->insp_param)}->max:'0'); 
                $headLable = 'Min-Max';}
                elseif($row->requirement == 2){$headValue = $minReq; $headLable = 'Min';}
                elseif($row->requirement == 3){$headValue = $maxReq; $headLable = 'Max';}
                elseif($row->requirement == 4){$headValue = $otherReq; $headLable = 'Other';}

                if(!empty($headValue)){
                    $thead .='<th  class="text-center">'.$row->insp_param.'<hr style="margin:0px;border-top:1px solid #123455;">( '.$headLable.' )<br>'.$headValue.' <input type="hidden" name="param['.$row->test_type.']['.$row->param_id.'][param]" value="'.$row->insp_param.'"></th>';

                    $tbody .= '<td>
						<input type="text" class="form-control text-center validateReading" name="param['.$row->test_type.']['.$row->param_id.'][result]"  value="'.(!empty($jsonData->{str_replace(" ","",$row->insp_param)}->result)?$jsonData->{str_replace(" ","",$row->insp_param)}->result:'').'" data-requirement = "'.$row->requirement.'" data-min="'.$minReq.'" data-max="'.$maxReq.'">
                        <input type="hidden" name="param['.$row->test_type.']['.$row->param_id.'][min]" value="'.$minReq.'">
                        <input type="hidden" name="param['.$row->test_type.']['.$row->param_id.'][max]" value="'.$maxReq.'">
                        <input type="hidden" name="param['.$row->test_type.']['.$row->param_id.'][requirement]" value="'.$row->requirement.'">
						<div class="error result'.$row->param_id.'"></div>
					</td>';  
                }
            endforeach;
			
            if(!empty($thead)){
                $html .= $title;
                $html .= '<div class="col-md-12 form-group">
                            <div class="table-responsive">
                                <table class="table excelTable">
                                    <thead class="thead-info">
                                        <tr>'.$thead.'  </tr>
                                    </thead>
                                    <tbody>
                                        <tr> '.$tbody.' </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>';
            }
        endforeach;
        $this->printJson(['status'=>1,'html'=>$html,'totalWeight'=>$totalWeight]);
    }

    public function getTestReportNo(){
        $data = $this->input->post();
        $testReportNo = $this->gateInward->getTestReportNo(['inst_id'=>$data['inst_id']]);
        $this->printJson(['test_report_no'=>$testReportNo]);
    }

    /* Verify Heat No. */
	public function getHeatNo(){
		$data = $this->input->post();
		$this->printJson($this->gateInward->saveHeatNo($data));
	}

    public function approveTestReport(){
		$data = $this->input->post();
		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->gateInward->approveTestReport($data));
		endif;
	}

    public function printTcReport($id,$pdf_type = ""){
        $this->data['dataRow'] = $dataRow = $this->gateInward->getTestReport(['id'=>$id,'single_row'=>1,'grnData'=>1]);
		$this->data['tcData'] = $tcData = $this->gateInward->getTestParameterData(['main_id'=>$id]);
        //$this->data['tcHeadList'] = $tcHeadList = $this->materialGrade->getTcMasterData(['grade_id'=>$dataRow->grade_id,'test_type'=>$dataRow->test_type,'tcParameter'=>1]);
        $this->data['tcHeadList'] = $tcHeadList = $this->materialGrade->getTcMasterData(['item_id'=>$dataRow->fg_item_id,'test_type'=>$dataRow->test_type,'tcParameter'=>1]);
		
		$letter_head = base_url('assets/images/logo_text.png');
		$logo = base_url('assets/images/icon.png');
        $check = '<img src="'.base_url('assets/images/check-square.png').'" style="width:20px;display:inline-block;vertical-align:middle;">';
        $unCheck = '<img src="'.base_url('assets/images/uncheck-square.png').'" style="width:20px;display:inline-block;vertical-align:middle;">';

        $jsonData = (!empty($tcData)) ? json_decode($tcData[0]->parameter) : [];
        $param_result = (!empty($jsonData)) ? $jsonData->{str_replace(" ","",$tcHeadList[0]->insp_param)}->result : '';
        
        $pdfData = $this->load->view('gate_inward/tc_print',$this->data,true);
		
        $htmlHeader = '<table class="table table-bordered">
							<tr>
								<td style="width:25%;" class="text-center"><img src="'.$letter_head.'" style="height:60px;width:95px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">'.(!empty($dataRow->test_description)?$dataRow->test_description:'').' Report</td>
								<th style="width:25%;"><span style="font-size:0.8rem;">'.$dataRow->doc_no.' <br>Date : '.$dataRow->rev_detail.'</td>
							</tr>                   
						</table>';

		$htmlFooter = '<table class="table item-list-bb">
                            <tr>
                                <th style="width:15%" class="text-left">Remarks</th>
                                <td>'.(!empty($dataRow->main_test_remaek) ? $dataRow->main_test_remaek : '').'</td>
                            </tr>
                            <tr>
                                <td colspan="2">Material confirms (OR Does not confirm to) "material grade" as per standard "material standard"</td>
                            </tr>
                        </table>
                        <table class="table item-list-bb">
                            <tr>
                                <th style="width:15%" class="text-left">Material Status</th>

                                <td style="width:25%" class="text-center">'.((!empty($dataRow->test_result) && $dataRow->test_result == 'Accept') ? $check : $unCheck).' Accepted</td>
                                <td style="width:25%" class="text-center">'.((!empty($dataRow->test_result) && $dataRow->test_result == 'Reject') ? $check : $unCheck).' Rejected</td>
                                <td style="width:35%" class="text-center">'.((!empty($dataRow->test_result) && $dataRow->test_result == 'Accept U.D.') ? $check : $unCheck).' Accepted under deviation</td>
                            </tr>
                        </table>
                        <table class="table item-list-bb" style="margin-top:20px">                            
                            <tr>
                                <td rowspan="2">We certify that above findings are correction and are based on results carried out on samples as per our sampling plan.</td>
                                <th style="width:20%">Inspected by</th>
                                <th style="width:20%">Approved by</th>
                            </tr>
                            <tr class="text-center">
                                <td>'.(!empty($dataRow->inspector_name) ? $dataRow->inspector_name : '').'</td>
                                <td>'.(!empty($dataRow->emp_name) ? $dataRow->emp_name : '').'</td>
                            </tr>
                        </table>
                        <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                            <tr>
                                <td style="width:100%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                            </tr>
                        </table>';
        
        $mpdf = new \Mpdf\Mpdf();
        $pdfFileName='tc-'.$id.'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');

        if(!empty($param_result)){
            $mpdf->SetWatermarkImage($logo,0.05,array(100,50));
            $mpdf->showWatermarkImage = true;
        }else{
            $mpdf->SetWatermarkText('Pending',0.05,array(100,50));            
            $mpdf->showWatermarkText = true;
        }

        $mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,25,5,5,5,'','','','','','','','','','A4-P'); 
		$mpdf->WriteHTML($pdfData);
        if(empty($pdf_type)){
            $mpdf->Output($pdfFileName,'I');
        }else{
            $filePath = realpath(APPPATH . '../assets/uploads/tc_report/');
            $pdfFileName = $filePath.'/tc-'.$id . '.pdf';
            $mpdf->Output($pdfFileName,'F');
        }
		
    }

    public function meargeAllTcReport($id){
        $reportList = $this->gateInward->getTestReport(['grn_trans_id'=>$id]);
        $mpdf = new \Mpdf\Mpdf();
        $pdfFileName='All-TC-'.$id.'.pdf';
        if(!empty($reportList)){
            $file_counter=1;//to be used in the loop
            foreach($reportList as $row){
                $this->printTcReport($row->id,1);
                $file=realpath(APPPATH . '../assets/uploads/tc_report/tc-'.$row->id . '.pdf');
                $pagecount = $mpdf->SetSourceFile($file);
                for($i=0; $i< $pagecount; $i++) {
                    $tplId = $mpdf->importPage($i+1);
                    $mpdf->useTemplate($tplId);
                    //add a page except for the last loop of the last document (otherwise we have a blank page)
                    if( $file_counter != count($reportList) ||   ($i+1) != $pagecount)
                    {
                        $mpdf ->addPage();
                    }
        
                }//end for
                unlink(realpath(APPPATH . '../assets/uploads/tc_report/tc-'.$row->id . '.pdf'));
                $file_counter++;
            }
        }
        $mpdf->Output($pdfFileName,'I');
    }

    /* Receive Test Report */
    public function receiveTestReport(){
        $data = $this->input->post();
        $this->data['testData']  = $this->gateInward->getTestReport(['id'=>$data['id'],'grnData'=>1,'single_row'=>1]);
        $this->data['vendorList'] = $this->party->getPartyList(['party_category'=>3]);
        $this->load->view('gate_inward/recieve_test_report',$this->data);
    }

    public function saveReceiveTestReport(){
        $data = $this->input->post();
        $errorMessage = array();
     
		if($data['ins_type'] != 'FIR'){ 
			if(($_FILES['tc_file']['name'] == null)){
				$errorMessage['tc_file'] = "Tc File is required.";
			}
		} 
        if($data['agency_id'] == ""){
            $errorMessage['agency_id'] = "Agency Name is required.";
        }
        if($data['test_type'] == ''){
            $errorMessage['test_type'] = "Test Type is required.";
        }
        if(empty($data['sample_qty'])){
            $errorMessage['sample_qty'] = "Sample Qty is required.";
        }
        if(empty($data['batch_no'])){
            $errorMessage['batch_no'] = "Batch No. is required.";
        }

        
        if(isset($data['head_id'])){
            foreach($data['head_id'] as $k=>$head_id){
                foreach($data['param'][$head_id] as $key=>$value){
                    if(($value['min'] > 0 || $value['max'] > 0)){
                        if($value['result'] == ''){
                            $errorMessage['result'.$key] = "Result. is required.";
                        }/*else{
                            if((($value['requirement'] == 1) && ($value['result'] < $value['min'] || $value['result'] > $value['max'])) || ($value['requirement'] == 2 && $value['result'] < $value['min']) || ($value['requirement'] == 3 && $value['result'] > $value['max']))
                            {
                                if($data['test_result'] != 'Accept U.D.'){
                                    if($data['test_result'] == 'Accept' || $data['test_result'] == ''){
                                        $errorMessage['test_result'] = "Invalid Test Result.";
                                    }
                                }
                            }
                        }*/
                    }
                }
            }
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if($_FILES['tc_file']['name'] != null || !empty($_FILES['tc_file']['name'])):
                $this->load->library('upload');
                $_FILES['userfile']['name']     = $_FILES['tc_file']['name'];
                $_FILES['userfile']['type']     = $_FILES['tc_file']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['tc_file']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['tc_file']['error'];
                $_FILES['userfile']['size']     = $_FILES['tc_file']['size'];

                $imagePath = realpath(APPPATH . '../assets/uploads/test_report/');
                $config = ['file_name' => "test_report".time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                $this->upload->initialize($config);
                if (!$this->upload->do_upload()):
                    $errorMessage['item_image'] = $this->upload->display_errors();
                    $this->printJson(["status"=>0,"message"=>$errorMessage]);
                else:
                    $uploadData = $this->upload->data();
                    $data['tc_file'] = $uploadData['file_name'];
                endif;
            else:
                unset($data['tc_file']);
            endif;
            $headArray = [] ;
            if(!empty($data['head_id'])){
                foreach($data['head_id'] as $k=>$head_id){
                    $json=[];
                    foreach($data['param'][$head_id] as $key=>$value){
                        unset($data['param'][$head_id][$key]['min'], $data['param'][$head_id][$key]['max'], $data['param'][$head_id][$key]['requirement']);
                        $json[str_replace(" ","",$data['param'][$head_id][$key]['param'])]=$data['param'][$head_id][$key];
                    }
                    $headArray[]=[
                        'id'=>$data['insp_id'][$k],
                        'head_id'=>$head_id,
                        'parameter'=>json_encode($json),
                        'grade_id'=>$data['grade_id'],
						'item_id'=>$data['fg_item_id'] 
                    ];
                }
            }
            $postData = [
                'id' => $data['id'],
                'grn_id' => $data['grn_id'],
                'grn_trans_id' => $data['grn_trans_id'],
                'agency_id' => $data['agency_id'],
                'name_of_agency' => $data['name_of_agency'],
                'test_type' => $data['test_type'],
                'test_report_no' => $data['test_report_no'],
                'inspector_name' => $data['inspector_name'],
                'sample_qty' => $data['sample_qty'],
                'batch_no' => $data['batch_no'],
                'heat_no' => $data['heat_no'],
                'test_result' => $data['test_result'],
                'test_remark' => $data['test_remark'],
                'headData'=>$headArray,
                'ins_type' => $data['ins_type'], 
                'created_by'=>$this->session->userdata('loginId')
            ];         

            if(!empty($data['tc_file'])){
                $postData['tc_file'] = $data['tc_file'];
            }  
            $this->printJson($this->gateInward->saveTestReport($postData));
        endif;
    }

    public function printReceiveTcReport($id){
        $this->data['dataRow'] = $dataRow = $this->gateInward->getTestReport(['id'=>$id,'single_row'=>1,'grnData'=>1]);
		
        $this->data['tcData'] = $tcData = $this->gateInward->getTestParameterData(['main_id'=>$id]);
        //$this->data['tcHeadList'] = $tcHeadList = $this->materialGrade->getTcMasterData(['grade_id'=>$tcData[0]->grade_id,'test_type'=>$dataRow->test_type,'tcParameter'=>1]);
		$this->data['tcHeadList'] = $tcHeadList = $this->materialGrade->getTcMasterData(['item_id'=>$tcData[0]->item_id,'test_type'=>$dataRow->test_type,'tcParameter'=>1]);
		
		$logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
				
        $pdfData = $this->load->view('gate_inward/tc_receive_print',$this->data,true);
		
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:100%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';
        
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='tc-'.$id.'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(120,45));
		$mpdf->showWatermarkImage = false;
		
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A5-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }

	public function printGRN($id){
		$this->data['dataRow'] = $grnData = $this->gateInward->getGateInward($id);
		$this->data['partyData'] = $this->party->getParty(['id'=>$grnData->party_id]);
		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
		
		$logo = (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->company_logo):base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
				
		
		
		$prepare = $this->employee->getEmployee(['id'=>$grnData->created_by]);
		$this->data['dataRow']->prepareBy = $prepareBy = $prepare->emp_name.' <br>('.formatDate($grnData->created_at).')'; 
		$this->data['dataRow']->approveBy = $approveBy = '';
		if(!empty($poData->is_approve)){
			$approve = $this->employee->getEmployee(['id'=>$grnData->is_approve]);
			$this->data['dataRow']->approveBy = $approveBy .= $approve->emp_name.' <br>('.formatDate($grnData->approve_date).')'; 
		}

        $pdfData = $this->load->view('gate_inward/print',$this->data,true);
		
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:25%;">PO No. & Date : '.$grnData->trans_number.' ['.formatDate($grnData->trans_date).']</td>
                    <td style="width:25%;"></td>
                    <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';
        
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='GRN-'.$id.'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(120,45));
		$mpdf->showWatermarkImage = true;
		
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

    public function getItemRevList($param = []){
		$data = (!empty($param)) ? $param : $this->input->post();
		
		$revList = $this->ecn->getItemRevision(['item_id'=>$data['item_id']]);
        // print_r($revList);
		$revHtml = '<option value="">Select Revision</option>';
		if(!empty($revList)){
			foreach($revList as $row){
				$selected = (!empty($data['rev_no']) && $data['rev_no'] == $row->rev_no)?'selected':'';
				$revHtml .= '<option value="'.$row->rev_no.'" '.$selected.'>'.$row->rev_no.' [Drw No : '.$row->drw_no.']'.'</option>';
			}
		}

		if(!empty($param)):
			return ['revHtml'=>$revHtml];
		else:
        	$this->printJson(['revHtml'=>$revHtml]);
		endif;		
	}
}
?>