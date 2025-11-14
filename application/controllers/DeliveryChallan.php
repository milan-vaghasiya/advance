<?php
class DeliveryChallan extends MY_Controller{
    private $indexPage = "delivery_challan/index";
    private $form = "delivery_challan/form";    

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Delivery Challan";
		$this->data['headData']->controller = "deliveryChallan";        
        $this->data['headData']->pageUrl = "deliveryChallan";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'deliveryChallan']);
	}

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader("deliveryChallan");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->deliveryChallan->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getDeliveryChallanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addChallan(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category' => "1,2"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>[3]]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Sales']);
        $this->data['transportList'] = $this->transport->getTransportList();
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_no']))
            $errorMessage['trans_number'] = "DC. No. is required.";

        if(empty($data['trans_date'])){
            $errorMessage['trans_date'] = "DC. Date is required.";
        }else{
            if (($data['trans_date'] < $this->startYearDate) OR ($data['trans_date'] > $this->endYearDate)){
                $errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
            }
        }
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['itemData'])):
            $errorMessage['itemData'] = "Item Details is required.";
        else:
            foreach($data['itemData'] as $key => $row):
                $bQty = array();
                if($row['stock_eff'] == 1):
                    $batchDetail = $row['batch_detail'];
                    $batchDetail = json_decode($batchDetail,true);$oldBatchQty = array();
                    if(!empty($row['id'])):
                        $oldItem = $this->deliveryChallan->getDeliveryChallanItem(['id'=>$row['id'],'batchDetail'=>1]);

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
                                    $bQty[$batchKey] = $batch['batch_qty'] ;
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
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])):
                $data['trans_no'] = $this->data['entryData']->trans_no;
                $data['trans_number'] = $this->data['entryData']->trans_prefix.$data['trans_no'];
            endif;
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;
            $this->printJson($this->deliveryChallan->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->deliveryChallan->getDeliveryChallan(['id'=>$id,'itemList'=>1]);
        $this->data['gstinList'] = $this->party->getPartyGSTDetail(['party_id' => $dataRow->party_id]);
        // $this->data['shipToList'] = $this->party->getPartyDeliveryAddressDetails(['party_id' => $dataRow->party_id]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category' => "1,2"]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>[3]]);
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Sales']);
        $this->data['transportList'] = $this->transport->getTransportList();
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->deliveryChallan->delete($id));
        endif;
    }

    public function printChallan($id){
        $this->data['dataRow'] = $dataRow = $this->deliveryChallan->getDeliveryChallan(['id'=>$id,'itemList'=>1]);
        $this->data['partyData'] = $this->party->getParty(['id'=>$dataRow->party_id]);
        $this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
        
        $logo = base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
        
        $pdfData = $this->load->view('delivery_challan/print', $this->data, true);
        
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
            <tr>
                <td style="width:25%;">DC. No. & Date : '.$dataRow->trans_number . ' [' . formatDate($dataRow->trans_date) . ']</td>
                <td style="width:25%;"></td>
                <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
            </tr>
        </table>';
        
		$mpdf = new \Mpdf\Mpdf();
		$filePath = realpath(APPPATH . '../assets/uploads/delivery_challan/');
        $pdfFileName = $filePath.'/' . str_replace(["/","-"],"_",$dataRow->trans_number) . '.pdf';

        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo, 0.03, array(120, 120));
        $mpdf->showWatermarkImage = true;
        $mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,5,15,5,5,'','','','','','','','','','A4-P');
        $mpdf->WriteHTML($pdfData);
		
		ob_clean();
		$mpdf->Output($pdfFileName, 'I');
    }

    public function getPartyChallan(){
        $data = $this->input->post();
        $this->data['orderItems'] = $this->deliveryChallan->getPendingChallanItems($data);
        $this->load->view('sales_invoice/create_invoice',$this->data);
    }

    public function addAnnexureDetail(){
        $data = $this->input->post();
        $this->data['dc_id'] = $data['id'];
        $this->data['dcItemList'] = $this->deliveryChallan->getDeliveryChallanItems(['id'=>$data['id']]);
        $this->load->view('delivery_challan/annexure_detail',$this->data);
    }

    public function getPackingBoxDetail(){
        $data = $this->input->post();
        $batchDetail = $this->deliveryChallan->getPackingBoxDetail($data);
        $tbodyData = "";
        if(!empty($batchDetail)){
            $i=1;
            foreach($batchDetail AS $row){
                $opt_qty = (!empty(floatval($row->opt_qty)))?floatval($row->opt_qty):1;
                $totalBox = ($row->batch_qty/$opt_qty) - ($row->cartoon_qty/$opt_qty) ;
                $stock_qty = $row->batch_qty - $row->cartoon_qty;
                $tbodyData .= ' <tr>
                                    <td>'.$i.'</td>
                                    <td>'.$row->batch_no.'</td>
                                    <td>
                                        '.$totalBox.'
                                        <br>
                                         <small>['.floatval($totalBox).' x '.floatval($opt_qty).' = '.floatval($stock_qty).']</small>
                                    </td>
                                    <td>
                                        <input type="text" id="box_qty_'.$i.'" class="form-control floatOnly calculateBox" data-srno="'.$i.'" value="">
                                        <input type="hidden" name="batchDetail['.$i.'][batch_qty]" id="batch_qty_'.$i.'" class="calculateBatch"  value="">
                                        <input type="hidden" name="batchDetail['.$i.'][opt_qty]" id="opt_qty_'.$i.'" value="'.floatval($opt_qty).'">
                                        <input type="hidden" name="batchDetail['.$i.'][batch_no]" id="batch_no_'.$i.'" value="'.$row->batch_no.'">
                                        <input type="hidden" name="batchDetail['.$i.'][batch_stock]" id="batch_stock_'.$i.'" value="'.floatVal($stock_qty).'">
                                    </td>
                                </tr>';
                    $i++;
            }
        }else{
            $tbodyData .= '<tr><th colspan="4" class="text-center">No Data Available.</th></tr>';
        }
        $this->printJson(['status'=>1,'tbodyData'=> $tbodyData]);
    }

    public function saveAnnexureDetail(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['dc_trans_id'])){ $errorMessage['dc_trans_id'] = "Item is required."; }
        if(empty($data['cartoon_no'])){ $errorMessage['cartoon_no'] = "Cartoon No is required."; }
        if(empty($data['batchDetail'])):
            $errorMessage['batchDetail'] = "Box Details is required.";
        elseif (floatval(array_sum(array_column($data['batchDetail'],'batch_qty'))) <= 0):
            $errorMessage['batchDetail'] = "Box Qty is required.";
        else:
            foreach($data['batchDetail'] as $key=>$batch):
                $postData = [
                    'dc_trans_id' => $data['dc_trans_id'],
                    'dc_id' => $data['dc_id'],
                    'batch_no' => $batch['batch_no'], 
                    'item_id' => $data['item_id'],
                    'single_row' => 1
                ];                        
                $stockData = $this->deliveryChallan->getPackingBoxDetail($postData);  
               
                $stockQty = $stockData->batch_qty - $stockData->cartoon_qty;
                if(empty($stockQty)):
                    $errorMessage['qty'.$key] = "Stock not available.";
                else:
                    if($batch['batch_qty'] > $stockQty):
                        $errorMessage['qty'.$key] = "Stock not available.".$batch['batch_qty'] .'>'. $stockQty;
                    endif;
                endif;
            endforeach;;
           
        endif;
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->deliveryChallan->saveAnnexureDetail($data));
        endif;
    }

    public function deleteAnnexureDetail(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->deliveryChallan->deleteAnnexureDetail($data));
        endif;
	}

    public function getAnnexureHtml(){
        $data = $this->input->post();
        $annexData = $this->deliveryChallan->getAnnexureDetail(['dc_id'=>$data['dc_id']]);
        $tbodyData = "";$i=1;
        if(!empty($annexData)){
            foreach($annexData AS $row){
                $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Record','fndelete' : 'deleteAnnexureDetail','res_function':'getAnnexureResponse','controller':'deliveryChallan'}";
				$deleteBtn = '<a class="btn btn-danger btn-sm btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class=" fas fa-trash-alt"></i></a>';

                $tbodyData .= '<tr>
                                    <td>'.$i++.'</td>
                                    <td>'.(!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name.'</td>
                                    <td>'.$row->cartoon_no.'</td>
                                    <td>'.$row->batch_no.'</td>
                                    <td>'.$row->box_qty.'</td>
                                    <td>'.$row->total_qty.'</td>
                                    <td>'.$deleteBtn.'</td>
                               </tr>';
            }
        }else{
            $tbodyData .= '<tr><th colspan="7" class="text-center">No Data Available.</th></tr>';
        }
        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

    public function printAnnexure($id){
        $this->data['challanData'] =$challanData=$this->deliveryChallan->getDeliveryChallan(['id'=>$id]);
        $this->data['partyData'] = $this->party->getParty(['id'=>$challanData->party_id]);
        $this->data['annexData'] = $this->deliveryChallan->getAnnexureDetail(['dc_id'=>$id]);
        $this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
        
        $logo = base_url('assets/images/logo.png');
        $this->data['letter_head'] =  (!empty($companyData->print_header))?base_url("assets/uploads/company_logo/".$companyData->print_header):base_url('assets/images/letterhead_top.png');
        
        $pdfData = $this->load->view('delivery_challan/packing_print', $this->data, true);
        
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
            <tr>
                <td style="width:25%;">DC. No. & Date : '.$challanData->trans_number . ' [' . formatDate($challanData->trans_date) . ']</td>
                <td style="width:25%;"></td>
                <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
            </tr>
        </table>';
        
		$mpdf = new \Mpdf\Mpdf();
		$filePath = realpath(APPPATH . '../assets/uploads/delivery_challan/');
        $pdfFileName = $filePath.'/' . str_replace(["/","-"],"annex_",$challanData->trans_number) . '.pdf';

        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo, 0.03, array(120, 120));
        $mpdf->showWatermarkImage = true;
        $mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,5,15,5,5,'','','','','','','','','','A4-P');
        $mpdf->WriteHTML($pdfData);
		
		ob_clean();
		$mpdf->Output($pdfFileName, 'I');
    }
}                                                                                                                                                                                                                                                   
?>