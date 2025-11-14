<?php
class SalesInvoice extends MY_Controller{
    private $indexPage = "sales_invoice/index";
    private $fpIndexPage = "sales_invoice/fp_index";
    private $form = "sales_invoice/form";    
    private $packingPrintForm = "sales_invoice/packing_print_form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Tax Invoice";
		$this->data['headData']->controller = "salesInvoice";        
        $this->data['headData']->pageUrl = "salesInvoice";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'salesInvoice']);
	}

    public function index(){
        $this->data['tableHeader'] = getAccountingDtHeader("finalPacking");
        $this->load->view($this->fpIndexPage,$this->data);
    }

    public function invIndex($status = 0){
        $this->data['status'] = $status;
        $this->data['tableHeader'] = getAccountingDtHeader("salesInvoice");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->salesInvoice->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getSalesInvoiceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getFinalPackDTRows(){
        $data = $this->input->post();
        $result = $this->salesInvoice->getFinalPackDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPendingFinalPackingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function addInvoice($jsonUrl = ""){
        if(!empty($jsonUrl)){
            $data = decodeURL($jsonUrl);
           if(!empty($data->packing_id)){
                $finalPackMenu = $this->transMainModel->getEntryType(['controller'=>'finalPacking']);
                $packingData = $this->finalPacking->getPackingData(['id'=>$data->packing_id,'single_row'=>1]);
                $itemData = $this->finalPacking->getPackingItemDetail(['packing_id'=>$data->packing_id]);
            
                $packingData->from_entry_type = $finalPackMenu->id;
                $packingData->ref_id = $data->packing_id;
                $packItem = [];
                foreach($itemData AS $row){
                    if(!isset($packItem[$row->so_trans_id])){
                        $packItem[$row->so_trans_id] = new stdClass();
                        $packItem[$row->so_trans_id] = $row;
                        $packItem[$row->so_trans_id]->total_box_qty = 0;
                        $packItem[$row->so_trans_id]->make = '';

                    }else{
                        $packItem[$row->so_trans_id]->make .= ',';
                    }
                    $packItem[$row->so_trans_id]->total_box_qty += $row->total_qty;
                    $packItem[$row->so_trans_id]->make .= $row->id;
                }
                $packingData->itemData = $packItem;
                $this->data['packingData'] = $packingData;
           }    
        }
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,3,8,10"]);
        $this->data['unitList'] = [];//$this->item->itemUnits();
        $this->data['hsnList'] = [];//$this->hsnModel->getHSNList();
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["20"]]);
        $this->data['transportList'] = $this->transport->getTransportList();

        $this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(2);
		$this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->form,$this->data);
    }

    public function getInvNextNo(){
        $data = $this->input->post();
        $data['vou_name_s'] = $this->data['entryData']->vou_name_short;

        $transNo = $this->transMainModel->getNextNo(['tableName'=>'trans_main','no_column'=>'trans_no','condition'=>' vou_name_s = "'.$data['vou_name_s'].'" AND trans_prefix = "'.$data['trans_prefix'].'" AND trans_date >= "'.$this->startYearDate.'" AND trans_date <= "'.$this->endYearDate.'"']);

        $transNumber = $data['trans_prefix'].$transNo;

        $this->printJson(['status'=>1,'trans_no'=>$transNo,'trans_number'=>$transNumber]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['sp_acc_id']))
            $errorMessage['sp_acc_id'] = "GST Type is required.";
        if(empty($data['sys_per']))
            $errorMessage['sys_per'] = "Bill Per. is required.";
		
        if(empty($data['trans_date'])){
            $errorMessage['trans_date'] = "Date is required.";
        }else{
            if (($data['trans_date'] < $this->startYearDate) OR ($data['trans_date'] > $this->endYearDate)){
                $errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
            }
        }
		
        if(empty($data['itemData'])):
            $errorMessage['itemData'] = "Item Details is required.";
        else:
			$bQty = array();
            foreach($data['itemData'] as $key => $row):    
                if($row['stock_eff'] == 1):
                    $batchDetail = $row['batch_detail'];
                    $batchDetail = json_decode($batchDetail,true); $oldBatchQty = array();
                    if(!empty($row['id'])):
                        $oldItem = $this->salesInvoice->getSalesInvoiceItem(['id'=>$row['id'],'batchDetail'=>1]);

                        $oldBatchDetail = json_decode($oldItem->batch_detail);
                        $oldBatchQty = array_reduce($oldBatchDetail, function($oldBatchDetail, $batch) { 
                            $oldBatchDetail[$batch->remark]= $batch->batch_qty; 
                            return $oldBatchDetail; 
                        }, []);                      
                    endif;

                    $batchQty = (!empty($batchDetail))?array_sum(array_column($batchDetail,'batch_qty')):0;
                    if(floatval($row['qty']) <> floatval($batchQty)):
                        $errorMessage['qty'.$key] = "Invalid Batch Qty.";
                    else:
                        foreach($batchDetail as $batch):
                            if(!empty($batch['batch_qty']) && $batch['batch_qty'] > 0):
                                $postData = [
                                    'location_id' => $batch['location_id'],
                                    'batch_no' => $batch['batch_no'], 
                                    'opt_qty' => $batch['opt_qty'],
                                    'item_id' => $row['item_id'],
                                    'stock_required' => 1,
                                    'single_row' => 1
                                ];                        
                                $stockData = $this->itemStock->getItemStockBatchWise($postData);  
                               
                                $batchKey = "";
                                $batchKey = $batch['remark'];
                                
                                $stockQty = (!empty($stockData->qty))?floatVal($stockData->qty):0;
                                if(!empty($row['id'])):
                                    $stockQty = $stockQty + (isset($oldBatchQty[$batchKey])?$oldBatchQty[$batchKey]:0);
                                endif;
                                
                                if(!isset($bQty[$batchKey])):
                                    $bQty[$batchKey] = $batch['batch_qty'];
                                else: 
                                    $bQty[$batchKey] += $batch['batch_qty'];
                                endif;
        
                                if(empty($stockQty)):
                                    $errorMessage['qty'.$key] = "Stock not available.";
                                else:
                                    if($bQty[$batchKey] > $stockQty):
                                        $errorMessage['qty'.$key] = "Stock not available.".$bQty[$batchKey] .'>'. $stockQty;
                                    endif;
                                endif;
                            endif;
                        endforeach;
                    endif;
                endif;
            endforeach;
        endif;
        if(isset($data['batchDetail'])){
            unset($data['batchDetail']);
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;
            
            $this->printJson($this->salesInvoice->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->salesInvoice->getSalesInvoice(['id'=>$id,'itemList'=>1]);
        $this->data['gstinList'] = $this->party->getPartyGSTDetail(['party_id' => $dataRow->party_id]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category' => "1,2"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,8"]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>$this->TERMS_TYPES["20"]]);
        $this->data['transportList'] = $this->transport->getTransportList();

        $this->data['taxClassList'] = $this->taxClass->getActiveTaxClass(2);
        $this->data['ledgerList'] = $this->party->getPartyList(['group_code'=>["'DT'","'ED'","'EI'","'ID'","'II'"]]);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->salesInvoice->delete($id));
        endif;
    }

    public function printInvoice($jsonData=""){
        if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        else: 
            $postData = $this->input->post();
        endif;
        
        $printTypes = array();
        if(!empty($postData['original'])):
            $printTypes[] = "ORIGINAL";
        endif;

        if(!empty($postData['duplicate'])):
            $printTypes[] = "DUPLICATE";
        endif;

        if(!empty($postData['triplicate'])):
            $printTypes[] = "TRIPLICATE";
        endif;

        if(!empty($postData['extra_copy'])):
            for($i=1;$i<=$postData['extra_copy'];$i++):
                $printTypes[] = "EXTRA COPY";
            endfor;
        endif;

        $postData['header_footer'] = (!empty($postData['header_footer']))?1:0;
        $this->data['header_footer'] = $postData['header_footer'];
        $print_format = (!empty($postData['print_format']) ? 'sales_invoice/print_'.$postData['print_format'] : 'sales_invoice/print');

        $inv_id = (!empty($id))?$id:$postData['id'];

		$this->data['invData'] = $invData = $this->salesInvoice->getSalesInvoice(['id'=>$inv_id,'itemList'=>1,'discStatus'=>1]);

		$this->data['partyData'] = $this->party->getParty(['id'=>$invData->party_id]);
		$this->data['shipToData'] = (!empty($invData->ship_to) ? $this->party->getParty(['id'=>$invData->ship_to]) : []);
        
        $taxClass = $this->taxClass->getTaxClass($invData->tax_class_id);
        $this->data['taxList'] = (!empty($taxClass->tax_ids))?$this->taxMaster->getTaxList(['tax_ids'=>$taxClass->tax_ids]):array();
        $this->data['expenseList'] = (!empty($taxClass->expense_ids))?$this->expenseMaster->getExpenseList(['expense_ids'=>$taxClass->expense_ids]):array();

		$this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
        $this->data['descriptionSetting'] = $this->masterModel->getAccountSettings();
		$this->data['termsData'] = (!empty($invData->termsConditions) ? $invData->termsConditions: "");
		$response="";
		$logo = (!empty($companyData->print_header)) ? base_url("assets/uploads/company_logo/".$companyData->company_logo):'';
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
				
        $pdfData = "";
        $countPT = count($printTypes); $i=0;
        foreach($printTypes as $printType):
            ++$i;           
            $this->data['printType'] = $printType;
            $this->data['maxLinePP'] = (!empty($postData['max_lines']))?$postData['max_lines']:7;
		    $pdfData .= $this->load->view($print_format,$this->data,true);
            if($i != $countPT): $pdfData .= "<pagebreak>"; endif;
        endforeach;
            
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = str_replace(["/","-"," "],"_",$invData->trans_number).'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->SetTitle($pdfFileName); 
        $mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		if(!empty($logo))
		{
		    $mpdf->SetWatermarkImage($logo,0.03,array(100,100));
		    $mpdf->showWatermarkImage = true;
		}
		//$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P','','','','',5,5,(($postData['header_footer'] == 1)?5:35),5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

    public function getPartyInvoiceItems(){
        $data = $this->input->post();
        $this->data['orderItems'] = $this->salesInvoice->getPendingInvoiceItems($data);
        $this->load->view('credit_note/create_creditnote',$this->data);
    }

	public function invPrints($jsonData=""){
		if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        else: 
            $postData = $this->input->post();
        endif;
        $pdiData = $this->pdi->getPdiReportData(['so_id'=>$postData['so_id'],'invDetail'=>1]);
        $pdfFileName = 'INV-'.$postData['so_id'].'.pdf';
        $mpdf = new \Mpdf\Mpdf();
        $fileNumber= 1; $filesTotal = count($pdiData);
        $logo=base_url('assets/images/letterhead_top.png');
        $itemWisePdi = array_reduce($pdiData, function($itemWisePdi, $item) { $itemWisePdi[$item->item_id][] = $item; return $itemWisePdi; }, []);
        foreach($itemWisePdi AS $item){
            /* 3.1 Report */
                $this->data['tcItems'] = $item[0];
                $this->data['qty'] = array_sum(array_column($item,'qty'));
                $this->data['reportList'] =$reportList= $this->gateInward->getTestReportList(['batch_no'=> ("'".implode("','",array_column($item,'rm_batch'))."'"),'fg_item_id'=>$item[0]->item_id,'grnData'=>1,'tcParams'=>1,'group_by'=>'batch_no,test_type']);
            
                $this->data['tcHeadList'] = $this->materialGrade->getTcMasterData(['item_id'=> $item[0]->item_id]);

                $tcData = $this->load->view("sales_invoice/tc_print_view",$this->data,true);
                
                $htmlHeader = '<table class="table">
                                <tr>
                                    <td ><img src="'.$logo.'" ></td>
                                </tr>
                        
                            </table>';
                $htmlFooter = '<table>
                                    <tr>
                                        <td style="width:25%;">QA-F-11 (Rev. 02/dtd.01-01-2025)</td>
                                        <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                                    </tr>
                                </table>';
                                // print_r($tcData);exit;
								
                $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
                $tcMpdf = new \Mpdf\Mpdf();
                $tcMpdf->SetTitle('INV-3.1-'.$item[0]->item_id.'.pdf'); 
                $tcMpdf->WriteHTML($stylesheet,1);
                $tcMpdf->SetHTMLHeader($htmlHeader);
                $tcMpdf->SetHTMLFooter($htmlFooter);
                $tcMpdf->SetDisplayMode('fullpage');
                $tcMpdf->SetWatermarkImage($logo,0.03,array(100,100));
                $tcMpdf->showWatermarkImage = true;
                $tcMpdf->AddPage('P','','','','',5,5,35,25,5,5,'','','','','','','','','','A4-P');
                $tcMpdf->WriteHTML($tcData);
                $tcFile = 'INV-3_1-'.$item[0]->item_id.'.pdf';
                $filePath = realpath(APPPATH . '../assets/uploads/tc_report/');
                $tcMpdf->Output($filePath.'/'.$tcFile , 'F');
                
                $tcFilePath = $filePath.'/'.$tcFile;
                $pagecount = $mpdf->SetSourceFile($tcFilePath);
                for($i=0; $i< $pagecount; $i++) {
                    $tplId = $mpdf->importPage($i+1);
                    $mpdf->useTemplate($tplId);
                    //add a page except for the last loop of the last document (otherwise we have a blank page)
                    $mpdf->addPage();
        
                }//end for
                unlink($tcFilePath);
                $fileNumber++;
			/*** TC Attechment ***/
			/*
			foreach($reportList AS $report){
				if(!empty($report->tc_file)){
					$attchPath = realpath(APPPATH . '../assets/uploads/test_report/'.$report->tc_file);
					if (file_exists($attchPath)) {
						$pagecount = $mpdf->SetSourceFile($attchPath);
						for($i=0; $i< $pagecount; $i++) {
							$tplId = $mpdf->importPage($i+1);
							$mpdf->useTemplate($tplId);
							//add a page except for the last loop of the last document (otherwise we have a blank page)
							$mpdf->addPage();

						}
					}
				}
			}
			*/
			
        }
		
		$fileNum=1;
        foreach($pdiData as $row){
            $this->data['invData'] = $row;
            $fir_file = $this->pdi->printPDIR(["id"=>$row->fir_id, "output_type"=>'F', "pdi_report_type"=>$postData['pdi_type'], "party_logo"=>$postData['party_logo']]);
            if (file_exists($fir_file)) {
                $pagesInFile = $mpdf->SetSourceFile($fir_file);
               
                for ($i = 1; $i <= $pagesInFile; $i++) 
                {
                    $tplId = $mpdf->ImportPage($i); 
                    $size = $mpdf->getTemplateSize($tplId);
        
                    $mpdf->UseTemplate($tplId, 0, 0, $size['width'], $size['height'], true);
                    if (($fileNum < $filesTotal) || ($i != $pagesInFile)) {$mpdf->addPage();};
                    
                }
            }
            $fileNum++;
        }
        $mpdf->Output($pdfFileName, 'I');
    }
	
    public function invPrintsNew($so_id){
        $pdiData = $this->pdi->getPdiReportData(['so_id'=>$so_id,'invDetail'=>1]);
        $pdfFileName = 'INV-'.$so_id.'.pdf';
        $mpdf = new \Mpdf\Mpdf();
        $fileNumber= 1; $filesTotal = count($pdiData);
        $logo=base_url('assets/images/letterhead_top.png');
        $itemWisePdi = array_reduce($pdiData, function($itemWisePdi, $item) { $itemWisePdi[$item->item_id][] = $item; return $itemWisePdi; }, []);
        foreach($itemWisePdi AS $item){
            /* 3.1 Report */
                $this->data['tcItems'] = $item[0];
                $this->data['reportList'] =$reportList= $this->gateInward->getTestReportList(['batch_no'=> ("'".implode("','",array_column($item,'rm_batch'))."'"),'fg_item_id'=>$item[0]->item_id,'grnData'=>1,'group_by'=>'batch_no,test_type','tcParams'=>1]);
                $this->data['tcHeadList'] = $this->materialGrade->getTcMasterData(['item_id'=> $item[0]->item_id]);

                $tcData = $this->load->view("sales_invoice/tc_print_view",$this->data,true);
                $htmlHeader = '<table class="table">
                                <tr>
                                    <td ><img src="'.$logo.'" ></td>
                                </tr>
                        
                            </table>
                            <div class="org_title text-center" style="font-size:1.2rem;"><u>Material Test Certificate</u><br><small>as per EN 10204 : 3.1</small></div>
                                    <table class="table item-list-bb" style="margin-top:5px">
                                        <thead>
                                        <tr class="text-left">
                                            <th  class="bg-light text-left">Customer</th>
                                            <td colspan="2">'.$item[0]->party_name.'</td>
                                            <th  class="bg-light text-left">Tc No</th>
                                            <td></td>
                                            <th  class="bg-light text-left">Tc Date</th>
                                            <td></td>
                                        </tr>
                                        <tr  class="text-left">
                                            <th class="bg-light text-left">Item Name</th>
                                            <td colspan="2">'.$item[0]->item_code.' '.$item[0]->item_name.'</td>
                                            <th  class="bg-light text-left">DC No</th>
                                            <td>'.$item[0]->trans_number.'</td>
                                            <th  class="bg-light text-left">DC Date</th>
                                            <td>'.formatDate($item[0]->trans_date).'</td>
                                        </tr>
                                    <tr class="text-left">
                                            <th  class="bg-light text-left">Part/Drg No :</th>
                                            <td colspan="2">'.$item[0]->drw_no.'</td>
                                            <th  class="bg-light text-left">PO No.</th>
                                            <td>'.$item[0]->doc_no.'</td>
                                            <th  class="bg-light">PO Date.</th>
                                            <td>'.formatDate($item[0]->doc_date).'</td>
                                    </tr>
                                    <tr class="text-left">
                                            <th class="bg-light">Qty Supplied</th>
                                            <td>'.array_sum(array_column($item,'qty')).'</td>
                                            <th class="bg-light"> Material Specifications</th>
                                            <td>'.$item[0]->material_grade.'</td>
                                            <th class="bg-light" colspan="2">Process Route & Supply Condition</th>
                                            <td >As Forged & Machined</td>
                                    </tr>
                                    <tr class="text-left">
                                            
                                            <th colspan="2" class="bg-light"> Heat Treatment</th>
                                            <td colspan="2">Solution Annealing</td>
                                            <th colspan="2" class="bg-light"> Surface Treatment/Platting: </th>
                                            <td colspan="2">Shot Blasting</td>
                                    </tr>
                                        </thead>
                                        
                                    </table>';
                $htmlFooter = '<table class="table item-list-bb">
                                    <tr>
                                        <td colspan="3">
                                            <p><b>Forging Process:</b> Closed Die Forging.</p>
                                            <p><b>Dimensional Inspection:</b> Accepeted As per Respective Component Drawing.</p>
                                            <p><b>Visual Inspection: </b> 100% Checked And Found Satisfactory.</p>
                                            <p><b> Note:</b> All Forging Are Free From Radio Active Contamination.</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            We hereby certify that items mentioned above have been inspected in our presence and are found to be in accordance with the drawing as satisfy the requirement of the specification.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Special Requirement:</th>
                                        <td rowspan="4" style="vertical-align: bottom;text-align: center;"><b>Prepared By:</b></td>
                                        <td rowspan="4" style="vertical-align: bottom;text-align: center;"><b>Approved By:</b></td>
                                    </tr>
                                    <tr>
                                        <td height="18"></td>
                                    </tr>
                                    <tr>
                                        <td height="18"></td>
                                    </tr>
                                    <tr>
                                        <td height="18"></td>
                                    </tr>
                                    
                                </table>
                                <table>
                                    <tr>
                                        <td style="width:25%;">A-F-11 (Rev.01/dtd.20.04.2022)</td>
                                        <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                                    </tr>

                                </table>
                            ';
                $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
                $tcMpdf = new \Mpdf\Mpdf();
                $tcMpdf->SetTitle('INV-3.1-'.$item[0]->item_id.'.pdf'); 
                $tcMpdf->WriteHTML($stylesheet,1);
                $tcMpdf->SetHTMLHeader($htmlHeader);
                $tcMpdf->SetHTMLFooter($htmlFooter);
                $tcMpdf->SetDisplayMode('fullpage');
                $tcMpdf->SetWatermarkImage($logo,0.03,array(100,100));
                $tcMpdf->showWatermarkImage = true;
                $tcMpdf->AddPage('P','','','','',5,5,80,60,5,5,'','','','','','','','','','A4-P');
                $tcMpdf->WriteHTML($tcData);
                $tcFile = 'INV-3_1-'.$item[0]->item_id.'.pdf';
                $filePath = realpath(APPPATH . '../assets/uploads/tc_report/');
                $tcMpdf->Output($filePath.'/'.$tcFile , 'F');
                
                $tcFilePath = $filePath.'/'.$tcFile;
                $pagecount = $mpdf->SetSourceFile($tcFilePath);
                for($i=0; $i< $pagecount; $i++) {
                    $tplId = $mpdf->importPage($i+1);
                    $mpdf->useTemplate($tplId);
                    //add a page except for the last loop of the last document (otherwise we have a blank page)
                    $mpdf->addPage();
        
                }//end for
                unlink($tcFilePath);
                $fileNumber++;
            /*** */
             /*** TC Attechment */
             $attchPath = realpath(APPPATH . '../assets/uploads/test_report/test_report_1737286319_585L_MANSEE.pdf');
                    if (file_exists($attchPath)) {
                        // try {
                            $pagecount = $mpdf->SetSourceFile($attchPath);
                            print_r($pagecount);die;
                            for($i=0; $i< $pagecount; $i++) {
                                $tplId = $mpdf->importPage($i+1);
                                $mpdf->useTemplate($tplId);
                                //add a page except for the last loop of the last document (otherwise we have a blank page)
                                $mpdf->addPage();
                    
                            }
                        // } catch (\Mpdf\MpdfException $e) {
                        //     // print_r("Helllooooo");
                        //     // Log the error and continue the loop if the source file or page import fails
                        //     log_message('error', 'Error importing page from file ' . $attchPath . ': ' . $e->getMessage());
                        //     continue; // Skip to the next iteration
                        // }
                       
                    }
        }
        
        $mpdf->Output($pdfFileName, 'I');
    }

    public function printExportInvoice($id){
        $invData = $this->salesInvoice->getSalesInvoice(['id'=>$id,'itemList'=>0,'packingDetail'=>1]);
        $invData->itemList = $this->finalPacking->getPackingItemDetail(['packing_id'=>$invData->ref_id,'order_by_package'=>1]);
        $this->data['invData'] = $invData;
        $this->data['partyData'] = $this->party->getParty(['id'=>$invData->party_id]);
        $this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
        $taxClass = $this->taxClass->getTaxClass($invData->tax_class_id);
        $this->data['taxList'] = (!empty($taxClass->tax_ids))?$this->taxMaster->getTaxList(['tax_ids'=>$taxClass->tax_ids]):array();
        $this->data['expenseList'] = (!empty($taxClass->expense_ids))?$this->expenseMaster->getExpenseList(['expense_ids'=>$taxClass->expense_ids]):array();

        $logo = (!empty($companyData->print_header)) ? base_url("assets/uploads/company_logo/".$companyData->company_logo):'';
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');

        $htmlFooter = '<table style="border-top:1px solid #545454;margin-top:1px;">
            <tr>
                <td class="text-right">Page No. {PAGENO}/{nbpg}</td>
            </tr>
        </table>';

        $pdfData = "";
        $pdfData .= $this->load->view('sales_invoice/export_invoice_print',$this->data,true);
        $pdfData .= "<pagebreak resetpagenum='1'>";
        $pdfData .= $this->load->view('sales_invoice/export_packing_print',$this->data,true);
        //print_r($pdfData);exit;

        $mpdf = new \Mpdf\Mpdf();
		$pdfFileName = str_replace(["/","-"," "],"_",$invData->trans_number).'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));

		$mpdf->SetTitle($pdfFileName); 
        $mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
        $mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,5,10,5,5,'','','','','','','','','','A4-P');

		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }
	
}
?>