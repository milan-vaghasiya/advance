<?php
class ProductionReport extends MY_Controller{
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Production Report";
		$this->data['headData']->controller = "reports/productionReport";
    }

    public function productionAnalysis(){
		$this->data['headData']->pageTitle = "PRODUCTION ANALYSIS";
        $this->data['headData']->pageUrl = "reports/productionReport/productionAnalysis";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
		$this->data['processList'] = $this->process->getProcessList();
        $this->load->view("reports/production_report/production_analysis",$this->data);
    }

    public function getProductionAnalysisData(){
        $data = $this->input->post();
        $customWhere = "prc_log.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$postData = [
			'item_id'=>$data['item_id'],
			//'prc_type'=>1,
			'breakdown'=>1,
			'grouped_data'=>1,
			'rejection_review_data'=>1,
			'customWhere'=>$customWhere,
			'group_by'=>'prc_log.trans_date,prc_master.item_id,prc_log.processor_id,prc_log.process_id'
		];
		if(!empty($data['process_id'])){
			$postData['process_id'] = $data['process_id'];
		}
        $result = $this->sop->getProcessLogList($postData);

        $tbody = '';$tfoot = '';
        $i = 1;  $totalIdeal = 0;$totalActual = 0;$totalLost = 0;
        foreach($result as $row):

            $totalProd = floor($row->ok_qty + $row->rej_qty + $row->rw_qty + $row->pending_qty);
    
            $workingHour = round($row->production_time / 60 ,2);

            $breakdownHour = round($row->breakdown_time / 3600 ,2);

            $ideal_ph=0;
            if(!empty($row->cycle_time) && $row->cycle_time > 0) { $ideal_ph  = floor(3600 / $row->cycle_time); }
            $ideal_total  = floor($ideal_ph * $workingHour);

            $actual_ph=0;
            if(!empty($totalProd) && !empty($workingHour)) { $actual_ph  = floor($totalProd / $workingHour); }

            $lost_ph = ($ideal_ph - $actual_ph);
            $lost_total = ($ideal_total - $totalProd);

            $tbody .= '<tr>
                <td>'.$i.'</td>
                <td>'.formatDate($row->trans_date).'</td>
                <td class="text-left">'.$row->item_name.'</td>
                <td>'.$row->process_name.'</td>
                <td>'.$row->processor_name.'</td>
                <td>'.$row->cycle_time.'</td>
                <td>'.$workingHour.'</td>
                <td>'.$breakdownHour.'</td>
                <td>'.(($ideal_ph > 0) ? $ideal_ph : 0).'</td>
                <td>'.(($ideal_total > 0) ? $ideal_total : 0).'</td>
                <td>'.(($actual_ph > 0) ? $actual_ph : 0).'</td>
                <td>'.(($totalProd > 0) ? $totalProd : 0).'</td>
                <td>'.(($lost_ph > 0) ? $lost_ph : 0).'</td>
                <td>'.(($lost_total > 0) ? $lost_total : 0).'</td>
                <td>
					<a href="'.base_url("reports/productionReport/productionDetail/".$row->trans_date.'/'.$row->item_id.'/'.$row->process_id.'/'.$row->processor_id).'" target="_blank" datatip="Production Detail" flow="left">'.(($lost_total > 0) ? $lost_total + ($breakdownHour * $ideal_ph) : ($breakdownHour * $ideal_ph)).'</a>
				</td>
            </tr>';
            $i++;
            $totalIdeal += (($ideal_total > 0) ? $ideal_total : 0);
            $totalActual += (($totalProd > 0) ? $totalProd : 0);
            $totalLost += (($lost_total > 0) ? $lost_total : 0);
        endforeach;

            $tfoot .= '<tr>
                <th colspan="8" class="text-right">Total</th>
                <th></th>
                <th class="text-center">'.$totalIdeal.'</th>
                <th></th>
                <th class="text-center">'.$totalActual.'</th>
                <th></th>
                <th class="text-center">'.$totalLost.'</th>
                <th></th>

            </tr>';
        $this->printJson(['status' => 1, 'tbody' => $tbody,'tfoot' => $tfoot]);
    }

    public function productionDetail($trans_date = "",$item_id="",$process_id="",$processor_id=""){
		$this->data['headData']->pageTitle = "Production Detail";
        
        $result = $this->sop->getProcessLogList(['item_id'=>$item_id,'process_id'=>$process_id,'processor_id'=>$processor_id,'trans_date'=>$trans_date,'rejection_review_data'=>1]);
        $tbodyData = '';$i=1;
        foreach($result as $row):
            $idealQty = (!empty($row->cycle_time) ? round($row->production_time / $row->cycle_time ,2) : 0);
            $actualQty = ($row->qty + $row->rej_qty + $row->rw_qty + $row->pending_qty);
            $tbodyData .= '<tr>
                <td class="text-center">'.$i++.'</td>
                <td>'.date("d-m-Y H:i:s",strtotime($row->start_time)).'</td>
                <td>'.date("d-m-Y H:i:s",strtotime($row->end_time)).'</td>
                <td>'.$row->prc_number.'</td>
                <td>'.$row->process_name.'</td>
                <td>'.$row->machine_name.'</td>
                <td>'.$row->emp_name.'</td>
                <td>'.$row->shift_name.'</td>
                <td>'.$row->production_time.'</td>
                <td>'.$idealQty.'</td>
                <td>'.$actualQty.'</td>
                <td>'.$row->qty.'</td>
                <td>'.$row->rej_qty.'</td>
                <td></td>
            </tr>';
        endforeach;
        $this->data['tbodyData'] = $tbodyData;
        $this->load->view('reports/production_report/production_detail',$this->data);
    }

	public function stageWiseProduction(){
        $this->data['headData']->pageTitle =  'Stage Wise Production';
        $this->data['headData']->pageUrl = "reports/productionReport/stageWiseProduction";
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,5"]);
        $this->load->view("reports/production_report/stage_wise_production",$this->data);
    }

	public function getStageWiseProductionData(){
        $data = $this->input->post();
        $productProcessData = $this->item->getProductProcessList(['item_id'=>$data['item_id']]);
        $thead = '<tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2">PRC Date</th>
                    <th rowspan="2">PRC No</th>
                    <th rowspan="2">PRC Qty</th>';
        $thead2 = '<tr>';
        $processArray = [];
		
        if(!empty($productProcessData)){
            foreach($productProcessData as $row){
                $thead .= '<th colspan="4">'.$row->process_name.'</th>';
                $thead2 .= '<th>In</th>
                            <th>Ok</th>
                            <th>Rej</th>
                            <th>Pending</th>';
                $processArray[] = $row->process_id;
            }
        } 
        $thead2 .= '</tr>';
        $thead .= '</tr>';
        $prcData = $this->productionReport->getStageWiseProduction(['item_id'=>$data['item_id']]);

        $tbody=''; $i=1; $tfoot='';		
        if(!empty($prcData)){
            $prcArray = [];
            foreach($prcData as $row){
                if(!empty($row->prc_id)){
                    $prcArray[$row->prc_id]['prc_number'] = $row->prc_number;
                    $prcArray[$row->prc_id]['prc_date'] = $row->prc_date;
                    $prcArray[$row->prc_id]['prc_qty'] = $row->prc_qty;
                    $prcArray[$row->prc_id][$row->process_id] = $row;
                }                
            }

            $totalIn = [];$totalOk=[];$totalRej=[];$totalPending = [];
            foreach($prcArray as $prc){
                $tbody .= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.formatDate($prc['prc_date']).'</td>
                            <td>'.$prc['prc_number'].'</td>
                            <td>'.floatVal($prc['prc_qty']).'</td>';
							
					foreach($processArray as $key=>$process_id){
						$in_qty = (!empty($prc[$process_id]->in_qty)?$prc[$process_id]->in_qty:0);
						$ok_qty = !empty($prc[$process_id]->ok_qty)?$prc[$process_id]->ok_qty:0;
						$rej_found_qty = !empty($prc[$process_id]->rej_found)?$prc[$process_id]->rej_found:0;
						$rej_qty = !empty($prc[$process_id]->rej_qty)?$prc[$process_id]->rej_qty:0;
						$rw_qty = !empty($prc[$process_id]->rw_qty)?$prc[$process_id]->rw_qty:0;
						$pendingReview = $rej_found_qty - (!empty($prc[$process_id]->review_qty)?$prc[$process_id]->review_qty:0);
						$pending_production =($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview);
						
						$tbody .= '<td>'.(($in_qty > 0)?floatval($in_qty):0).'</td>
								   <td>'.(($ok_qty > 0)?floatval($ok_qty):0).'</td>
								   <td>'.(($rej_qty > 0)?floatval($rej_qty):0).'</td>
								   <td>'.(($pending_production > 0)?floatval($pending_production):0).'</td>';
					   $totalIn[$process_id][] = $in_qty; $totalOk[$process_id][] = $ok_qty; $totalRej[$process_id][]=$rej_qty; $totalPending[$process_id][]= $pending_production;
					}					
				$tbody .= '</tr>';
            }
            $tfoot = '<tr>
                        <th colspan="4" class="text-right">Total</th>';
            foreach($processArray as $key=>$process_id){
                $tfoot .= '<th>'.((array_sum($totalIn[$process_id]) > 0) ? array_sum($totalIn[$process_id]) : 0).'</th>
						<th>'.((array_sum($totalOk[$process_id]) > 0) ? array_sum($totalOk[$process_id]) : 0).'</th>
						<th>'.((array_sum($totalRej[$process_id]) > 0) ? array_sum($totalRej[$process_id]) : 0).'</th>
						<th>'.((array_sum($totalPending[$process_id]) > 0) ? array_sum($totalPending[$process_id]) : 0).'</th>';
            }                       
            $tfoot .= '</tr>';            
        }        
        $this->printJson(['status'=>1,'tbody'=>$tbody,'thead'=>$thead.$thead2,'tfoot'=>$tfoot]);
    }
	
    /* PRC REGISTER */
	public function prcRegister(){
        $this->data['headData']->pageTitle = 'PRC Register';
        $this->data['headData']->pageUrl = "reports/productionReport/prcRegister";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view("reports/production_report/prc_register",$this->data);
    }

    public function getPrcRegisterData(){
        $data = $this->input->post();

        $jobCardData = $this->productionReport->getPrcRegisterData($data);
        $tbody = ''; $tfoot = '';
        $i = 1;  $totalQty = 0; $totalOkQty = 0;$totalRejQty = 0;
        foreach ($jobCardData as $row) :
            $job_no = '<a href="'.base_url("sopDesk/printDetailRouteCard/".$row->id).'" target="_blank">'.$row->prc_number.'</a>';
            $party_name = (!empty($row->party_name) ? $row->party_name : "Self Stock");
            $sales_order = (!empty($row->so_no) ? $row->so_no : "-");
            $item_name = (!empty($row->item_code)? "[".$row->item_code."] " : "").$row->item_name;

            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>' . $job_no . '</td>
                <td>' . formatDate($row->prc_date) . '</td>
                <td>' . $party_name . '</td>
				<td>' . $sales_order . '</td>
                <td>' . $item_name . '</td>
                <td>' . floatVal($row->prc_qty) . '</td>
                <td>' . floatVal($row->ok_qty) . '</td>
                <td>' . floatVal($row->rej_qty) . '</td>
                <td>' . $row->emp_name . '</td>
                <td>' . $row->job_instruction . '</td>
            </tr>';
            
            $totalQty += floatval($row->prc_qty);
            $totalOkQty += floatval($row->ok_qty);
            $totalRejQty += floatval($row->rej_qty);

        endforeach;
        $tfoot .= '<tr>
            <th colspan="6" class="text-right">Total</th>
            <th class="text-center">'.$totalQty.'</th>
            <th class="text-center">'.$totalOkQty.'</th>
            <th class="text-center">'.$totalRejQty.'</th>
            <th></th>
            <th></th>
        </tr>';
        $this->printJson(['status' => 1, 'tbody' => $tbody,'tfoot' => $tfoot]);
    }

    /* Jobwork Register */
    public function outSourceRegister(){
		$this->data['headData']->pageTitle = "Outsource Register";
        $this->data['headData']->pageUrl = "reports/productionReport/outSourceRegister";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['vendorList'] = $this->party->getPartyList(['party_category'=>3]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view("reports/production_report/outsource_register",$this->data);
    }

    public function getOutSourceRegister(){
        $data = $this->input->post();
        $jobOutData = $this->productionReport->getOutSourceRegister($data);
        $blankInTd = '<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
        $i = 1;
        $tblData = "";$tfoot=""; $totalQty=0; $totalInQty=0;$totalRejQty=0;$totalWtQty=0;$totalPendQty=0;$totalAmount=0;$totalScrap=0;
        foreach ($jobOutData as $row) :
			$prcBom = $this->sop->getPrcBomData(['prc_id'=>$row->prc_id,'stock_data'=>1,'single_row'=>1]);
			$material_weight = 0;
			if($row->cutting_flow == 1){
				$material_weight = (!empty($prcBom->uom) && $prcBom->uom == 'KGS') ? $prcBom->ppc_qty : $prcBom->wt_pcs;
			}else{
				$cuttingBatch = $this->sop->getCuttingBatchDetail(['prc_number'=>$prcBom->batch_no,'heat_no'=>$prcBom->heat_no,'single_row'=>1]);
				$material_weight = $cuttingBatch->ppc_qty;
			}
		
			$beforWt = ((empty($row->process_from))?$material_weight:((!empty($row->prev_weight) && $row->prev_weight > 0)?$row->prev_weight:0)) ;
			$kgs = $beforWt * $row->qty;
			
			$prcKgs = round($kgs,2);
			$itemQty = round($row->qty,2);
			$kg_per_nos = (!empty($itemQty) ? ($prcKgs / $itemQty) : 0);
			$scrap_wt = round($kg_per_nos - $row->finish_wt,4);
			$process_loss = ($row->rej_qty * $kg_per_nos);
		
            $pendingQty = (($row->qty * $row->output_qty)- ($row->ok_qty + $row->rej_qty + $row->without_process_qty));
			$outData = $this->productionReport->getJobInwardData(['ref_id'=>$row->challan_id,'prc_id'=>$row->prc_id,'from_date'=>$data['from_date'],'to_date'=>$data['to_date'],'date_filter'=>$data['date_filter']]);
            $outCount = count($outData); 
            
            //$prcBom = $this->sop->getPrcBomData(['prc_id'=>$row->prc_id,'stock_data'=>1,'single_row'=>1]);
            $material_wt=0;
            if($row->cutting_flow == 1){
                $material_wt = $prcBom->ppc_qty;
            }else{
                $cuttingBatch = $this->sop->getCuttingBatchDetail(['prc_number'=>$prcBom->batch_no,'heat_no'=>$prcBom->heat_no,'single_row'=>1]);
                $material_wt = $cuttingBatch->ppc_qty;
            }
    
			$ch_number = $row->ch_number;
            $tblData .= '<tr>
				<td>' . $i++ . '</td>
				<td>' . $ch_number. '</td>
				<td>' . formatDate($row->ch_date) . '</td>
				<td>' . $row->prc_number. '</td>
				<td>' . $row->party_name. '</td>
				<td>' . (!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name . '</td>
				<td>' . $row->process_name . '</td>
				<td>' . floatVal($row->qty) . '</td>
                <td>' . floatVal($pendingQty) . '</td>
                <td>' . floatVal($row->price) . '</td>
                <td>' . round(($row->price * $row->qty),2) . '</td>';
                            
			$totalQty+= $row->qty;			
            $totalPendQty+= floatVal($pendingQty);
							
            if ($outCount > 0) :
                $usedQty = 0; $j=1;
                foreach ($outData as $outRow) :
					
					$total_scrap = ($scrap_wt * $outRow->qty);
                    $beforWt = ((empty($outRow->process_from)) ? $material_wt : ((!empty($outRow->prev_weight) && $outRow->prev_weight > 0) ? $outRow->prev_weight : 0)) ;
                    $scrapWt = $total_scrap + $process_loss;
                    $kgs = (!empty($row->uom) && $row->uom == 'KGS') ? ($beforWt * $outRow->qty) : '';
                    $amount = ((!empty($row->uom) && $row->uom == 'KGS') ? ($kgs * $row->price) : ($outRow->qty * $row->price));

					$tblData .= '<td>' . formatDate($outRow->trans_date) . '</td>
								<td>' . $row->party_name. '</td>
								<td>' . $outRow->bill_no. '</td>
								<td>' . $outRow->in_challan_no . '</td>
								<td>' . floatVal($outRow->qty) . (!empty($kgs) ? '<hr class="m-0"><small>'.$kgs.' Kgs.</small>' : '') . '</td>
								<td>' . floatVal($outRow->rej_qty) . '</td>
								<td>' . floatVal($outRow->wp_qty). '</td>
								<td>' . floatval($scrapWt) . '</td>
								<td>' . floatval($row->price). '</td>
								<td>' . floatval($amount). '</td>';   
								
                    if ($j != $outCount) {
                        $tblData .= '</tr><tr><td>' . $i++ . '</td>' . $blankInTd;
                    }					
                    $j++;

                    $totalInQty+=floatVal($outRow->qty);
					$totalRejQty += floatVal($outRow->rej_qty);
					$totalWtQty += floatVal($outRow->wp_qty);
                    $totalScrap += floatval($scrapWt);
                    $totalAmount += floatval($amount);
                endforeach;
            else :
                $tblData .= '<td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>';
            endif;
            $tblData .= '</tr>';
        endforeach;
		
        $tfoot = '<tr>
                <th colspan="7" class="text-right">Total</th>
                <th>'.floatVal($totalQty).'</th>                
                <th>'.floatVal($totalPendQty).'</th>
                <th></th>
                <th></th>               
                <th></th>
                <th></th>
                <th></th> 
                <th></th>
                <th>'.floatVal($totalInQty).'</th>
                <th>'.floatVal($totalRejQty).'</th>
                <th>'.floatVal($totalWtQty).'</th>
                <th>'.floatval($totalScrap).'</th>
                <th></th>
                <th>'.floatVal($totalAmount).'</th>

            </tr>';
        $this->printJson(['status' => 1, "tblData" => $tblData,"tfoot"=>$tfoot]);
    }
	
    public function productionLogSheet(){
        $this->data['headData']->pageTitle = 'PRODUCTION LOG SHEET';
        $this->data['headData']->pageUrl = "reports/productionReport/productionLogSheet";
        $this->data['endDate'] = getFyDate(date("Y-m-d"));
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
		$this->data['processList'] = $this->process->getProcessList();
		$this->data['employeeList'] = $this->employee->getEmployeeList();
		$this->data['machineList'] = $this->item->getItemList(['item_type'=>5]);
        $this->load->view("reports/production_report/production_log_sheet", $this->data);
    }

	public function getProductionLogSheet(){
        $data = $this->input->post();
        $productionData = $this->productionReport->getProductionLogSheet($data);

		$tbody = '';$i=1; $tfoot=''; 
        $totalOkQty = $totalRejQty = $totalProdQty = $totalScrap = $totalProdWt = $totalRejPer = $totalPrsScrapWt = $totalPrcQty = $totalRejKG = 0;
		
		$log_date = ((!empty($data['report_type']) && $data['report_type'] == 2) ? "<th>Log Date</th>" : "");
		$thead = '<tr class="text-center">
			<th>#</th>
			<th>Operator Name</th>
			<th>Machine/Department/Vendor</th>
			<th>PRC No.</th>
			'.$log_date.'
			<th>Shift</th>
			<th>Production Time<br>(Min.)</th>
			<th>Product Name</th>
			<th>Material Grade</th>
			<th>Batch No</th>
			<th>Batch Qty</th>
			<th>Total Prod Qty</th>
			<th>Set up</th>
			<th>After Wt/PC</th>
			<th>Before Wt/PC</th>
			<th>Total Prod. Wt.</th>
			<th>Rejection<br/>(NOS)</th>
			<th>Rejection<br/>(KGS)</th>
			<th>Total OK<br/>Prod Qty<br/>NOS</th>
			<th>Scrap<br/>Wt/Pc</th>
			<th>Total Scrap</th>
			<th>Rejection (%)</th>
		</tr>';
		
        if(!empty($productionData)):
            $prcIds = array_unique(array_column($productionData,'prc_id'));
            $cuttingData = $this->productionReport->getPrcCutWeight(['prc_id'=>$prcIds]);

            $cuttingPRC = array_reduce($cuttingData, function($prcData, $prc) { 
                if(!empty($prc->cut_weight)){
                    $weight = explode("-",$prc->cut_weight);
                    $prc->cut_weight = ((!empty($weight[1]) && $weight[1] > 0)?$weight[1]:((!empty($weight[0]))?$weight[0]:0));
                }
                $prcData[$prc->prc_id] = $prc; 
                return $prcData; 
            }, []);
            foreach($productionData as $row):
                $ok_qty = !empty($row->ok_qty)?$row->ok_qty:0;
                $total_rej_qty = !empty($row->total_rej_qty)?$row->total_rej_qty:0;
                $productionQty = $ok_qty + $total_rej_qty;

                if($productionQty > 0): 
                    $forge_wt = !empty($row->finish_wt) ? $row->finish_wt : 0;
                    
                    /* Scrap WT/Pc Calculation */
                    if($row->process_from > 0){
                        $prevWt = $row->prev_fg_wt ;
                    }else{
                        $prevWt = ($row->cutting_flow == 2)?(!empty($cuttingPRC[$row->prc_id]->cut_weight)?$cuttingPRC[$row->prc_id]->cut_weight:0):$cuttingPRC[$row->prc_id]->ppc_qty;
                    }
                    $prsScrapWt = ($forge_wt > 0) ? ($prevWt -  $forge_wt) : 0;
                    /* Scrap WT/Pc Calculation */
                    
                    $total_prod_wt = ($prevWt * $productionQty);
                    $rej_kg = ($forge_wt * $total_rej_qty);
                    $total_scrap = ($prsScrapWt * $productionQty);
                    $rej_per = (($total_rej_qty/$productionQty)*100);

					$log_date = ((!empty($data['report_type']) && $data['report_type'] == 2) ? "<td>".formatDate($row->trans_date)."</td>" : "");
					
					$tbody .= '<tr class="text-center">
                        <td class="text-left">'.$i.'</td>
                        <td class="text-left">'.$row->emp_name.'</td>
                        <td class="text-left">'.$row->processor_name.'</td>
                        <td class="text-left">'.$row->prc_number.'</td>
						'.$log_date.'
						<td class="text-left">'.$row->shift_name.'</td>
                        <td>'.$row->production_time.'</td>
                        <td class="text-left">'.$row->item_name.'</td>
                        <td class="text-left">'.$row->material_grade.'</td>
                        <td>'.$row->batch_no.'</td>
                        <td>'.(int)$row->prc_qty.'</td>
                        <td>'.(($productionQty > 0) ? (int)$productionQty : 0).'</td>
                        <td class="text-left">'.$row->process_name.'</td>
                        <td>'.(($forge_wt > 0) ? sprintf('%0.3f',$forge_wt) : 0).'</td>
                        <td>'.(($prevWt > 0) ? sprintf('%0.3f',$prevWt) : 0).'</td>
                        <td>'.(($total_prod_wt > 0) ? sprintf('%0.3f',$total_prod_wt) : 0).'</td>
                        <td>'.(($total_rej_qty > 0) ? (int)$total_rej_qty : 0).'</td>
                        <td>'.(($rej_kg > 0) ? sprintf('%0.3f',$rej_kg) : 0).'</td>
                        <td>'.(($ok_qty > 0) ? (int)$ok_qty : 0).'</td>
                        <td>'.(($prsScrapWt > 0) ? sprintf('%0.3f',$prsScrapWt) : 0).'</td>
                        <td>'.(($total_scrap > 0) ? sprintf('%0.3f',$total_scrap) : 0).'</td>
                        <td>'.round($rej_per,2).'</td>
                    </tr>';
					
                    $i++;
					
					$totalRejKG += $rej_kg;
                    $totalRejQty += $total_rej_qty;
                    $totalProdQty += $productionQty;
                    $totalScrap += $total_scrap;
                    $totalProdWt += $total_prod_wt;
                    $totalOkQty += $ok_qty;
                    $totalPrsScrapWt += $prsScrapWt;
                    $totalRejPer += $rej_per;
                endif;
            endforeach;
			$colspan = ((!empty($data['report_type']) && $data['report_type'] == 2) ? 11 : 10);
			
			$tfoot .= '<tr>
                <th colspan="'.$colspan.'" class="text-right">Total</th>
                <th class="text-center">'.(int)$totalProdQty.'</th>
                <th colspan="3"></th>
                <th class="text-center">'.sprintf('%0.3f',$totalProdWt).'</th>
                <th class="text-center">'.(int)$totalRejQty.'</th>
                <th class="text-center">'.sprintf('%0.3f',$totalRejKG).'</th>
                <th class="text-center">'.(int)$totalOkQty.'</th>
                <th class="text-center">'.sprintf('%0.3f',$totalPrsScrapWt).'</th>
                <th class="text-center">'.sprintf('%0.3f',$totalScrap).'</th>
                <th class="text-center"></th>
            </tr>';
        endif;
		$this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfoot, 'thead'=>$thead]);
    }

    /* Production Bom Report */
	public function productionBom(){
        $this->data['headData']->pageTitle = 'Production Bom Report';
        $this->data['headData']->pageUrl = "reports/productionReport/productionBom";
        $this->data['itemData'] = $this->item->getItemList([1,3]);
        $this->load->view("reports/production_report/production_bom",$this->data);
    }

    public function getItemBomData(){
        $data = $this->input->post();
        $result = $this->item->getProductKitData($data);
        $i = 1;
		$tbody = "";
        $thead =  "";
        $tfoot = ""; $totalQty = 0;
		
        if(!empty($data['item_id'])){
            $thead = '<tr>
                        <th>#</th>
                        <th>BOM Item</th>
                        <th>BOM Item Code</th>
                        <th>BOM Qty</th>
                    </tr>';
        }else{
            $thead = '<tr>
                         <th>#</th>
                        <th>Product Name</th>
                        <th>Product Code</th>
                        <th>BOM Qty</th>
                    </tr>';
        }
		foreach ($result as $row) :
            
			$tbody .= '<tr class="text-center">';
            if(!empty($data['item_id'])){
                $tbody .= ' <td>' . $i++ . '</td>
                            <td>' . $row->item_name . '</td>
                            <td>' . $row->item_code . '</td>
                            <td>' . $row->qty . '</td>';
            }else{
                $tbody .= ' <td>' . $i++ . '</td>
                            <td>' . $row->product_name . '</td>
                            <td>' . $row->product_code . '</td>
                            <td>' . $row->qty . '</td>';
            }
						
			$tbody .= '</tr>';
			
            $totalQty += $row->qty;
		endforeach;
		
        $tfoot .= '<tr class="thead-dark">
            <th colspan="3" class="text-right">Total</th>
            <th class="text-center">'.$totalQty.'</th> 
        </tr>';
		
        $this->printJson(['status' => 1, 'tbody' => $tbody, 'thead' => $thead, 'tfoot' => $tfoot]);
    }

    /* Material Requirement Planning */
	public function materialReqPlan(){
        $this->data['headData']->pageTitle = 'Material Requirement Planning';
        $this->data['headData']->pageUrl = "reports/productionReport/materialReqPlan";
        $this->data['itemList'] = $this->salesOrder->getPendingOrderItems(['group_by'=>'so_trans.item_id', 'mrp_report'=>1,'trans_status'=>3]);
        $this->load->view("reports/production_report/material_req_plan",$this->data);
    }

	public function getMaterialReqPlanData(){
        $data = $this->input->post();

        $stockData = $this->itemStock->getItemStockBatchWise(['item_id' => $data['item_id'], 'stock_required' => 1, 'group_by'=>'stock_trans.location_id,stock_trans.batch_no']);

        $packStockData = $this->itemStock->getItemStockBatchWise(['item_id'=>$data['item_id'],'location_ids'=>[$this->PACKING_STORE->id,$this->FPCK_STORE->id],'single_row'=>1,'stock_required' => 1,'group_by'=>'stock_trans.location_id']);//28-03-25

        $stockTbody = ""; $stockTfoot = ""; $totalStockQty = 0;
		
        if(!empty($stockData)):
            $i=1;
            foreach($stockData as $row):
                $stockTbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>['.$row->store_name.'] '.$row->location.'</td>
                    <td>'.$row->batch_no.'</td>
                    <td>'.$row->qty.'</td>
                </tr>';
                $totalStockQty += $row->qty; 
            endforeach;            
        else:
            $stockTbody .= '<tr>
                <td colspan="4" class="text-center">
                    No data available in table
                </td>
            </tr>';
        endif;

        $stockTfoot = '<tr>
            <th colspan="3" class="text-right">Total</th>
            <th>'.$totalStockQty.'</th>
        </tr>';
        $data['pending_qty'] = $data['pending_qty'] - $totalStockQty;

        $wipTbody=''; $wipTfoot=''; $totalWIPQty=0;
        $wipData = $this->sop->getItemWiseWip(['item_id'=>$data['item_id'],'log_data'=>1,'pending_accepted'=>1,'group_by'=>'product_process.process_id','status'=>'1,2']);
        if(!empty($wipData)):
            $i=1;
            foreach($wipData as $row):
                $in_qty = (!empty($row->in_qty)?$row->in_qty:0);
                $ok_qty = (!empty($row->ok_qty)?$row->ok_qty:0);
                $movement_qty = (!empty($row->movement_qty)?$row->movement_qty:0);
                $rej_found_qty = (!empty($row->rej_found)?$row->rej_found:0);
                $rej_qty = (!empty($row->rej_qty)?$row->rej_qty:0);
                $rw_qty = (!empty($row->rw_qty)?$row->rw_qty:0);
                $pendingReview = $rej_found_qty - $row->review_qty;
                $pending_production = (($in_qty) - ($ok_qty+$rej_qty+$rw_qty+$pendingReview));
                $pending_movement = $in_qty - $movement_qty;
                $total = $pending_production + $pending_movement ;
                $wipTbody.='<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->current_process.'</td>
                    <td>'.floatVal($pending_production).'</td>
                    <td>'.floatVal($pending_movement).'</td>
                    <td>'.floatVal($total).' </td>
                </tr>';
                $totalWIPQty += $total;
            endforeach;
        else:
            $wipTbody .= '<tr>
                <td colspan="3" class="text-center">
                    No data available in table
                </td>
            </tr>';
        endif;

        $wipTfoot = '<tr>
            <th colspan="4" class="text-right">Total</th>
            <th>'.$totalWIPQty.'</th>
        </tr>';
        $data['pending_qty'] = $data['pending_qty'] - $totalWIPQty;

        $materialTbody = "";$req_qty = 0;
        if($data['pending_qty'] > 0):
            $req_qty = $data['pending_qty'];
            $itemBom = $this->item->getProductKitData(['item_id'=>$data['item_id']]);
            if(!empty($itemBom)):
                $i=1;
                foreach($itemBom as $row):
                    $itemStock = $this->itemStock->getItemStockBatchWise(['item_id' => $row->ref_item_id,'single_row'=>1]);
                    $shortage = (($data['pending_qty'] * $row->qty) - $itemStock->qty);
                    $materialTbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.($data['pending_qty'] * $row->qty).' <small>'.$row->uom.'</small> </td>
                        <td>'.((!empty($itemStock->qty) && $itemStock->qty > 0)?$itemStock->qty:0).' <small>'.$row->uom.'</small></td>
                        <td>'.$shortage.' <small>'.$row->uom.'</small></td> 
                    </tr>';
                endforeach;
            else:
                $materialTbody .= '<tr>
                    <td colspan="5" class="text-center">
                        Material not found.
                    </td>
                </tr>';
            endif;
        else:
            $materialTbody .= '<tr>
                <td colspan="5" class="text-center">
                    No data available in table
                </td>
            </tr>';
        endif;        
        
        $this->printJson(['status' => 1,'stockTbody'=>$stockTbody, 'stockTfoot'=>$stockTfoot, 'wipTbody'=>$wipTbody, 'wipTfoot'=>$wipTfoot, 'materialTbody'=>$materialTbody,'req_qty'=>$req_qty,'totalStockQty'=>$totalStockQty,'totalWIPQty'=>$totalWIPQty,'in_packing'=>(!empty($packStockData->qty) ? $packStockData->qty :0)]);//28-03-25
    }
	
	/* WIP RM Repor*/
    public function wipRawMaterialReport(){
        $this->data['headData']->pageTitle = 'WIP RM Report';
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>"1,3"]);
        $this->load->view("reports/production_report/wip_raw_material", $this->data);
    }

    public function getWIPRawMaterialReport(){
        $data = $this->input->post();
        $prcData = $this->productionReport->getWIPRawMaterialData(['item_id'=>$data['item_id'],'stock_data'=>1,'production_data'=>1]);

        $tbody = '';$i=1; $tfoot=''; $totalIssueQty = 0;$totalUsedQty = 0;$totalReturnQty = 0;$totalStockQty = 0;
        foreach($prcData as $row):
            $usedQty=0; $returnQty=0; $stockQty=0; $issue_qty=0; $prcProcessData=[];
            if($row->prc_type == 2){
                $usedQty = (!empty($row->cutting_cons) ? floatval($row->cutting_cons) : 0);
                $stockQty = $row->issue_qty - ($usedQty + $row->cutting_return_qty);
                $returnQty = (!empty($row->cutting_return_qty) ? floatval($row->cutting_return_qty) : 0);
            }else{
                if($row->status > 1){ 
                    $prcProcessData = $this->sop->getPRCProcessList(['prc_id'=>$row->id,'process_id'=>$row->process_ids,'log_data'=>1,'movement_data'=>1,'pending_accepted'=>1,'move_type'=>1]);
                    }
                elseif(!empty($row->process_ids)){ 
                    $prcProcessData = $this->sop->getProcessFromPRC(['process_ids'=>$row->process_ids,'item_id'=>$row->item_id]); 
                }
                if($row->process_id == 0 && $row->cutting_flow == 2){
                    $usedQty = ((!empty($prcProcessData))?(floatval(($prcProcessData[0]->ok_qty + $prcProcessData[0]->rej_found) * $row->ppc_qty)/$prcProcessData[0]->output_qty):0);
                }else{
                    $usedQty = floatval($row->production_qty * $row->ppc_qty)/$row->output_qty;
                }
                $stockQty = $row->issue_qty - ($usedQty + $row->return_qty);
                $returnQty = (!empty($row->return_qty) && $row->return_qty > 0)?floatval($row->return_qty):0;
            }
            if(round($stockQty) > 0){ 
                $tbody.='<tr>
                            <td>'.$i++.'</td>
                            <td>'.$row->prc_number.'</td>
                            <td>'.(!empty($row->item_code) ? '[ '.$row->item_code.' ] ' : '').$row->item_name.'</td>
                            <td>'.(!empty($row->issue_qty) ? floatval($row->issue_qty) : 0).'</td>
                            <td>'.$usedQty.'</td>
                            <td>'.$returnQty.'</td>
                            <td>'.round($stockQty).'</td>
                        </tr>';
                        $i++;

                $totalIssueQty += floatval($row->issue_qty);
                $totalUsedQty += floatval($usedQty);
                $totalReturnQty += floatval($returnQty);
                $totalStockQty += floatval($stockQty);
            }
        endforeach;
        $tfoot .= '<tr>
                <th colspan="3" class="text-right">Total</th>
                <th class="text-center">'.$totalIssueQty.'</th> 
                <th class="text-center">'.$totalUsedQty.'</th>
                <th class="text-center">'.$totalReturnQty.'</th>
                <th class="text-center">'.$totalStockQty.'</th>
         </tr>';
        $this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

	/* Cutting Register */
    public function cuttingRegister(){
        $this->data['headData']->pageTitle = "CUTTING REGISTER"; 
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
		$this->data['employeeList'] = $this->employee->getEmployeeList();
        $this->load->view('reports/production_report/cutting_register',$this->data);
    }

	public function getCuttingRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['prc_type'] = 2;
            $cuttingData = $this->productionReport->getCuttingPrcLogData($data);
            $tbody=""; $i=1; $tfoot=""; $totalQty=0; $totalAmt=0;
         
            if(!empty($cuttingData)):
                foreach($cuttingData as $row):
					$stockData = $this->cutting->getCuttingBomData(['prc_id'=>$row->prc_id,'single_row'=>1,'stock_data'=>1,'production_data'=>1]);
					
					$total_cunsumption_wt = ($row->qty * $row->wt_nos);
					$processRate = (!empty($row->process_rate) ? $row->process_rate : $row->cut_rate);
					$total_amt = ($processRate * $row->qty);
                    $tbody .= '<tr>
                        <td class="text-center">'.$i++.'</td>
                        <td>'.$row->prc_number.'</td>
                        <td>'.$row->operator_name.'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td>'.(!empty($row->machine_code) ? $row->machine_code : $row->machine_name).'</td>
                        <td>'.$row->batch_no.'</td>
                        <td>'.$row->material_grade.'</td>
                        <td>'.$row->cutting_dia.'</td>
                        <td>'.$row->cut_rate.'</td>
                        <td>'.(!empty($row->item_code) ? $row->item_code : $row->item_name).'</td>
                        <td>'.$row->qty.'</td>
						<td>'.$row->wt_nos.'</td>
                        <td>'.$row->cutting_length.'</td>
                        <td>'.$total_cunsumption_wt.'</td>
						<td>'.(!empty($stockData->total_end_pcs) ? round($stockData->total_end_pcs,3) : 0).'</td>
						<td>'.$total_amt.'</td>';
            
                    $tbody .= '</tr>';
					
                    $totalQty += $row->qty;
					$totalAmt += $total_amt;
                endforeach;
            endif;
			$tfoot .= '<tr class="thead-dark">
                    <th colspan="10" class="text-right">Total</th>
					<th class="text-center">'.numberFormatIndia($totalQty).'</th> 
					<th colspan="4"></th>
					<th class="text-center">'.numberFormatIndia($totalAmt).'</th> 
                </tr>';
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }

    public function paretoAnalysis(){
        $this->data['headData']->pageTitle = 'PARETO ANALYSIS REPORT';
        $this->data['headData']->pageUrl = "reports/productionReport/paretoAnalysis";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view("reports/production_report/pareto_analysis",$this->data);
    }

	public function getParetoAnalysisData($jsonData=""){
        if(!empty($jsonData)){
            $data = (array) decodeURL($jsonData);
        }else{
            $data = $this->input->post();
        }
        $productionData = $this->productionReport->getParetoAnalysisData($data);
        
        $detailTbody = ''; $tfoot = '';$rejDetailTbody = ""; $sumDetailTbody = "";
		
        $total_ok_qty = 0;$total_rej_qty = 0;$total_rw_qty = 0;
        if(!empty($productionData)){
            $i=1;
            foreach($productionData AS $row){
                $ok_qty = (!empty($row->total_ok_qty))?$row->total_ok_qty:0;
                $rej_qty = (!empty($row->total_rej_qty))?$row->total_rej_qty:0;
                $rw_qty = (!empty($row->total_rw_qty))?$row->total_rw_qty:0;
                $totalProduction = $row->total_ok_qty + $rej_qty + $rw_qty;
                $ok_per = $rej_per = $rw_per = 0;
                if($totalProduction > 0){
                    $ok_per = round((($ok_qty * 100)/ $totalProduction),2);
                    $rej_per = round((($rej_qty * 100)/ $totalProduction),2);
                    $rw_per = round((($rw_qty * 100)/ $totalProduction),2);
                }
                $total_ok_qty += $ok_qty;
                $total_rej_qty += $rej_qty;
                $total_rw_qty += $rw_qty;
                $detailTbody .= '<tr>
					<td>'.$i++.'</td>
					<td class="text-left">'.$row->item_code.'</td>
					<td class="text-left">'.$row->process_name.'</td>
					<td class="text-right">'.$totalProduction.'</td>
					<td class="text-right">'.floatval($ok_qty).'</td>
					<td class="text-right">'.sprintf('%.2f',$ok_per).'</td>
					<td class="text-right">'.floatval($rw_qty).'</td>
					<td class="text-right">'.sprintf('%.2f',$rw_per).'</td>
					<td class="text-right">'.floatval($rej_qty).'</td>
					<td class="text-right">'.sprintf('%.2f',$rej_per).'</td>
				</tr>';
            }

            $rejDetail = $this->productionReport->getParetoAnalysisRejData($data);
            if(!empty($rejDetail)){
                $total_per =0;
                foreach($rejDetail AS $row){
                    $per = ((($row->rej_qty * 100)/$total_rej_qty));
                    $total_per += $per;
                    $rejDetailTbody .= '<tr>
						<td>'.$row->rej_reason.'</td>
						<td class="text-right">'.floatval($row->rej_qty).'</td>
						<td class="text-right">'.sprintf('%.2f',$per).'</td>             
					</tr>';
                }

                $rejDetailTbody .= '<tr class="bg-light">
					<td class="text-right border-bottom-0"><b>Total</b></td>
					<td class=" border-bottom-0 text-right">'.floatval(array_sum(array_column($rejDetail,'rej_qty'))).'</td>
					<td class="text-dark border-bottom-0 text-right"><strong>'.$total_per.'</strong></td>
				</tr>';
            }

        }
        $totalQty = $total_ok_qty + $total_rw_qty + $total_rej_qty;
        $total_ok_per = $total_rej_per = $total_rw_per = 0;
        if($totalQty > 0){
            $total_ok_per = round((($total_ok_qty * 100)/ $totalQty),2);
            $total_rej_per = round((($total_rej_qty * 100)/ $totalQty),2);
            $total_rw_per = round((($total_rw_qty * 100)/ $totalQty),2);
        }
        $sumDetailTbody = ' <tr>
				<td>  <b>Ok</b>   </td>
				<td class="text-right">'.floatval($total_ok_qty).'</td>
				<td class="text-right">'.sprintf('%.2f',$total_ok_per).'</td>                                                        
			</tr>
			<tr>
				<td><b>Rework</b> </td>
				<td class="text-right">'.floatval($total_rw_qty).'</td>
				<td class="text-right">'.sprintf('%.2f',$total_rw_per).'</td>                                                       
			</tr>
			<tr>
				<td><b>Rejection</b> </td>
				<td class="text-right">'.floatval($total_rej_qty).'</td>
				<td class="text-right">'.sprintf('%.2f',$total_rej_per).'</td>                                                       
			</tr>
			<tr class="bg-light">
				<td><b>TOTAL</b> </td>
				<td class="text-right"><strong>'.floatval($total_ok_qty + $total_rw_qty + $total_rej_qty).'</strong></td>
				<td class="text-right"><strong>100.00</strong></td>                                                       
			</tr>';
      
        if(!empty($data['is_pdf'])){
			$process_name = '';
			$prsArr = array_unique(array_column($productionData,'process_name'));
			$process_name = implode(',',$prsArr);
			
			$report_date = date('d-m-Y',strtotime($data['from_date'])).' to '.date('d-m-Y',strtotime($data['to_date']));
            $processBy = ($data['process_by'] == 1) ? 'Inhouse' : 'Vendor';          
            $header = 'MONTH: '. $report_date.' ('.$processBy.' - '.$process_name.')';
			
            $logo = base_url('assets/images/logo.png');
             
            $thead = '<tr class="text-left">
                        <th colspan="8" style="padding:8px;" class="org_title">'.$header.'</th>
                        <th colspan="2" style="padding:8px;" class="org_title">DATE : '.date('d-m-Y').'</th>
                    </tr>';
 
            $thead .= '<tr>
                        <th rowspan="2" style="width:5%">Sr.</th>
                        <th rowspan="2" style="width:15%">Part Name</th> 
                        <th rowspan="2" style="width:15%">Process</th>
                        <th rowspan="2" style="width:10%">Inspected</th>
                        <th colspan="2" style="width:15%">Ok</th>
                        <th colspan="2" style="width:15%">Rework</th>
                        <th colspan="2" style="width:15%">Rejection</th>
                    </tr>
                    <tr>
                        <th>Qty</th>
                        <th>%</th>
                        <th>Qty</th>
                        <th>%</th>
                        <th>Qty</th>
                        <th>%</th>
                    </tr>';
            $theadRej = '<tr class="text-left">
                            <th>Details of Defect</th>
                            <th class="text-right">Quantity</th>                                                        
                            <th class="text-right">%</th>
                        </tr>';
                        
            $pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
                            <thead class="thead-dark" id="theadData">'.$thead.'</thead>
                            <tbody>'.$detailTbody.'</tbody>
                            <tfoot>
                                <tr class="thead-dark">
                                    <th colspan="4" class="text-right">Total</th>
                                    <th class="text-right">'.$total_ok_qty.'</th> 
                                    <th class="text-right">'.sprintf('%.2f',round($total_ok_per,2)).'</th>
                                    <th class="text-right">'.$total_rw_qty.'</th>
                                    <th class="text-right">'.sprintf('%.2f',round($total_rw_per,2)).'</th>
                                    <th class="text-right">'.$total_rej_qty.'</th>
                                    <th class="text-right">'.sprintf('%.2f',round($total_rej_per,2)).'</th>
                                </tr>
                            </tfoot>
                        </table>';
            $pdfData .= '<table style="width:100%; margin-top:10px;">
                            <tr>
                                <td style="font-weight:bold; font-size:14px;">Total Rejection</td>
                            </tr>
                        </table>';
   
            $pdfData .= '<table class="table table-bordered item-list-bb" repeat_header="1">
                            <thead class="bg-light" id="theadData">'.$theadRej.'</thead>
                            <tbody>'.$rejDetailTbody.'</tbody>
                        </table>';
                    
            $htmlHeader = '<table class="table">
                            <tr>
                                <td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
                                <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">PARETO ANALYSIS REPORT</td>
                                <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">QA/F/20'.'<br>'.'(01/01.01.2025)</td>
                            </tr>
                        </table>';
            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                                <tr>
                                    <td style="width:50%;font-size:12px;"></td>
                                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                                </tr>
                            </table>';

            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/paretoAnalysis.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo,0.05,array(100,100));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->AddPage('L','','','','',3,3,18,3,3,3,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName,'I');
        }
        $this->printJson(['status' => 1, 'detailTbody' => $detailTbody,'tfoot' => $tfoot,'rejDetailTbody'=>$rejDetailTbody,'sumDetailTbody'=>$sumDetailTbody]);
    }
	
    public function poorQualityCost(){
        $this->data['headData']->pageTitle = 'COST OF POOR QUALITY';
        $this->data['headData']->pageUrl = "reports/productionReport/poorQualityCost";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view("reports/production_report/poor_quality_analysis",$this->data);
    }

	public function poorQualityCostData($jsonData=""){
        if(!empty($jsonData)){
            $data = (array) decodeURL($jsonData);
        }else{
            $data = $this->input->post();
        }
        $productionData = $this->productionReport->getPoorQualitydata($data);
        $tbody = '';$i=1; $tfoot = "";
        if(!empty($productionData)){
            $prcIds = array_unique(array_column($productionData,'prc_id'));
            $cuttingData = $this->productionReport->getPrcCutWeight(['prc_id'=>$prcIds]);
         
            $array1 = array_map(function($itm) { return (array) $itm; }, $productionData);
            $array2 = array_map(function($itm) { return (array) $itm; }, $cuttingData);
			foreach ($array1 as &$item1) { 
				foreach ($array2 as $item2) { 
					if ($item1['prc_id'] == $item2['prc_id']) { $item1 = array_merge($item1, $item2); } 
				} 
			}
			
            $groupedItems = array_reduce($array1, function ($result, $item) {
                $itemId = $item['item_id'];
                if($item['cutting_flow'] == 2){
                    $weight = explode("-",$item['cut_weight']);
                    $cut_weight = ((!empty($weight[1]) && $weight[1] > 0)?$weight[1]:((!empty($weight[0]))?$weight[0]:0));
                }else{
                    $cut_weight = $item['ppc_qty'];
                }
                
                if (isset($result[$itemId])) {
                    $result[$itemId]['ih_forge_qty'] += $item['ih_forge_qty'];
                    $result[$itemId]['v_forge_qty'] += $item['v_forge_qty'];
                    $result[$itemId]['ih_mc_qty'] += $item['ih_mc_qty'];
                    $result[$itemId]['v_mc_qty'] += $item['v_mc_qty'];
                    $result[$itemId]['ih_forge_cost'] += $item['ih_forge_cost'] + (($cut_weight * $item['ih_forge_qty']) * ($item['mt_price'])) +($item['cut_rate'] * $item['ih_forge_qty']);
                    $result[$itemId]['v_forge_cost'] += $item['v_forge_cost'] + (($cut_weight * $item['v_forge_qty']) * ($item['mt_price'])) +($item['cut_rate'] * $item['v_forge_qty']);
                    $result[$itemId]['ih_mc_cost'] += $item['ih_mc_cost'] + (($cut_weight * $item['ih_mc_qty']) * ($item['mt_price'])) +($item['cut_rate'] * $item['ih_mc_qty']);
                    $result[$itemId]['v_mc_cost'] += $item['v_mc_cost'] + (($cut_weight * $item['v_mc_qty']) * ($item['mt_price'])) +($item['cut_rate'] * $item['v_mc_qty']);
                } else {
                    $result[$itemId] = $item;
                    $result[$itemId]['ih_forge_cost'] = $item['ih_forge_cost'] + (($cut_weight * $item['ih_forge_qty']) * ($item['mt_price'])) +($item['cut_rate'] * $item['ih_forge_qty']);
                    $result[$itemId]['v_forge_cost'] = $item['v_forge_cost'] + (($cut_weight * $item['v_forge_qty']) * ($item['mt_price'])) +($item['cut_rate'] * $item['v_forge_qty']);
                    $result[$itemId]['ih_mc_cost'] = $item['ih_mc_cost'] + (($cut_weight * $item['ih_mc_qty']) * ($item['mt_price'])) +($item['cut_rate'] *  $item['ih_mc_qty']);
                    $result[$itemId]['v_mc_cost'] = $item['v_mc_cost'] + (($cut_weight * $item['v_mc_qty']) * ($item['mt_price'])) +($item['cut_rate'] * $item['v_mc_qty']);
                }
                return $result;
            }, []); 
            $groupedItems = array_filter($groupedItems, function($value) { return (object)$value; });
			$groupedItems = array_map(function($itm) { return (object) $itm; }, $groupedItems);

            $total_ih_forge = $total_v_forge = $total_ih_mc = $total_v_mc = $total_cust_cost = $total_cost = 0;
            $total_ih_forge_qty = $total_v_forge_qty = $total_ih_mc_qty = $total_v_mc_qty = $total_cust_qty = 0; 
            foreach($groupedItems AS $row){
                $cust_cost = $row->cust_qty * $row->cust_price;
                $cost = $row->ih_forge_cost+$row->v_forge_cost+$row->ih_mc_cost+$row->v_mc_cost+$cust_cost;
                $ih_forge_rate =((!empty($row->ih_forge_qty) && $row->ih_forge_qty > 0)?$row->ih_forge_cost/$row->ih_forge_qty:0);
                $v_forge_rate =((!empty($row->v_forge_qty) && $row->v_forge_qty > 0 )?$row->v_forge_cost/$row->v_forge_qty:0);
                $ih_mc_rate =((!empty($row->ih_mc_qty) && $row->ih_mc_qty > 0)?$row->ih_mc_cost/$row->ih_mc_qty:0);
                $v_mc_rate =((!empty($row->v_mc_qty) && $row->v_mc_qty > 0)?$row->v_mc_cost/$row->v_mc_qty:0);     

                $tbody .= ' <tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->item_code.'</td>
                                <td class="text-right">'.round($row->ih_forge_qty,2).'</td>
                                <td class="text-right">'.round($ih_forge_rate,2).'</td>
                                <td class="text-right">'.sprintf('%.2f',round($row->ih_forge_cost,2)).'</td>

                                <td class="text-right">'.round($row->v_forge_qty,2).'</td>
                                <td class="text-right">'.round($v_forge_rate,2).'</td>
                                <td class="text-right">'.sprintf('%.2f',round($row->v_forge_cost,2)).'</td>

                                <td class="text-right">'.round($row->ih_mc_qty,2).'</td>
                                <td class="text-right">'.round($ih_mc_rate,2).'</td>
                                <td class="text-right">'.sprintf('%.2f',round($row->ih_mc_cost,2)).'</td>

                                <td class="text-right">'.round($row->v_mc_qty,2).'</td>
                                <td class="text-right">'.round($v_mc_rate,2).'</td>
                                <td class="text-right">'.sprintf('%.2f',round($row->v_mc_cost,2)).'</td>
                                
                                <td class="text-right">'.round($row->cust_qty,2).'</td>
                                <td class="text-right">'.round($row->cust_price,2).'</td>
                                <td class="text-right">'.sprintf('%.2f',round($cust_cost,2)).'</td>

                                <td class="text-right">'.sprintf('%.2f',round($cost,2)).'</td>
                            </tr>';

                            $total_ih_forge_qty += $row->ih_forge_qty;
                            $total_v_forge_qty += $row->v_forge_qty;
                            $total_ih_mc_qty += $row->ih_mc_qty; 
                            $total_v_mc_qty += $row->v_mc_qty;
                            $total_cust_qty += $row->cust_qty;

                            $total_ih_forge += $row->ih_forge_cost;
                            $total_v_forge += $row->v_forge_cost;
                            $total_ih_mc += $row->ih_mc_cost; 
                            $total_v_mc += $row->v_mc_cost;
                            $total_cust_cost += $cust_cost;
                            $total_cost += $cost;
            }

            $tfoot = '<tr class="text-right">
                        <th colspan="2" class="text-right">Total</th>
                        <th class="text-right">'. $total_ih_forge_qty.'</th>                        
                        <th></th>                        
                        <th class="text-right">'. round($total_ih_forge,2).'</th>   
                        
                        <th class="text-right">'. $total_v_forge_qty.'</th>                        
                        <th></th>                        
                        <th class="text-right">'. round($total_v_forge,2).'</th>
                        
                        <th class="text-right">'. $total_ih_mc_qty.'</th>                        
                        <th></th>                        
                        <th class="text-right">'. round($total_ih_mc,2).'</th>    
                        
                        <th class="text-right">'. $total_v_mc_qty.'</th>                        
                        <th></th>                        
                        <th class="text-right">'. round($total_v_mc,2).'</th>  
                        
                        <th class="text-right">'. $total_cust_qty.'</th>                        
                        <th></th>                        
                        <th class="text-right">'. round($total_cust_cost,2).'</th>

                        <th class="text-right">'. round($total_cost,2).'</th>      
                      </tr>';
        }
        if($data['is_pdf'] == 1){
            $logo = base_url('assets/images/logo.png');
            $report_date = date('d-m-Y',strtotime($data['from_date'])).' to '.date('d-m-Y',strtotime($data['to_date'])); 
			
            $thead = '<tr class="text-left">
                        <th colspan="11" style="padding:8px;" class="org_title">MONTH : '.$report_date.'</th>
                        <th colspan="7" style="padding:8px;" class="org_title">DATE : '.date('d-m-Y').'</th>
                    </tr>';
    
            $thead .= '<tr>
                        <th  rowspan="2">#</th>
                        <th  rowspan="2">Part</th>
                        <th  colspan="3">InHouse - Forging Rej.</th>
                        <th  colspan="3">Job Work - Forging Rej.</th>
                        <th  colspan="3">InHouse - Machining Rej.</th>
                        <th  colspan="3">Job Work - Machining Rej.</th>
                        <th  colspan="3">Customers Return Rej.</th>
                        <th  rowspan="2">Total Cost</th>
                    </tr>
                    <tr>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Cost</th>

                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Cost</th>

                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Cost</th>

                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Cost</th>

                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Cost</th>
                    </tr>';
                        
            $pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
                            <thead class="thead-dark" id="theadData">'.$thead.'</thead>
                            <tbody>'.$tbody.'</tbody>
                            <tfoot>'.$tfoot.'</tfoot>
                        </table>';
            $htmlHeader = '<table class="table">
                            <tr>
                                <td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
                                <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">COST OF POOR QUALITY</td>
                                <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">QA-F-25'.'<br>'.'(Rev.02 - 01-01.2025)</td>
                            </tr>
                        </table>';
            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                                <tr>
                                    <td style="width:50%;font-size:12px;"></td>
                                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                                </tr>
                            </table>';

            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/poorQualityCost.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo,0.05,array(100,100));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->AddPage('P','','','','',5,5,19,20,3,3,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName,'I');
        }
        $this->printJson(['status' => 1, 'tbody' => $tbody,'tfoot' => $tfoot]);
    }

    /* Job Costing Report */
	public function jobCosting(){
        $this->data['headData']->pageTitle = 'Job Costing After Production';
        $this->data['headData']->pageUrl = "reports/productionReport/jobCosting";
        $this->data['prcList'] = $this->sop->getPRCList(['status'=>3]);
        $this->load->view("reports/production_report/job_costing",$this->data);
    }

    public function getJobCostingData($jsonData=""){
        if(!empty($jsonData)){
            $data = (array) decodeURL($jsonData);
        }else{
            $data = $this->input->post();
        }
        $prcData = $this->sop->getPRC(['id'=>$data['prc_id']]);

        //PRC Data
        $prcDetail = '<tr>
                            <td>'.$prcData->prc_number.'</td>
                            <td>'.formatDate($prcData->prc_date).'</td>
                            <td>'.(!empty($prcData->item_code)?'['.$prcData->item_code.']':'').$prcData->item_name.'</td>
                            <td>'.floatval($prcData->prc_qty).'</td>
                        </tr>';

        // Material Cost
        $bomData = $this->sop->getPrcBomData(['prc_id'=>$data['prc_id'],'stock_data'=>1,'cutting_batch'=>(($prcData->cutting_flow == 2)?1:'')]);
      
        $materialDetail = '';$totalMaterialCost = 0; $inputWt = 0;
        foreach($bomData AS $row){
            if($prcData->cutting_flow == 2 && $row->item_id == $prcData->item_id && strpos($row->batch_no, 'CUT') !== false){
                $cuttingBatch = $this->sop->getCuttingBatchDetail(['prc_number'=>$bomData[0]->batch_no,'heat_no'=>$bomData[0]->heat_no,'single_row'=>1]);
              
                $material_wt = $cuttingBatch->ppc_qty;
                $item_id = $cuttingBatch->item_id;
                $item_name = $cuttingBatch->item_name;
                $inputWt = $cuttingBatch->ppc_qty;
            }else{
                $item_id = $row->item_id;
                $material_wt = $row->ppc_qty;
                $item_name = $row->item_name;
                if($row->uom == 'KGS'){
                    $inputWt = $prcBom->ppc_qty;
                }else{
                    $inputWt = $row->wt_pcs;
                }
            }
            $mtPrice = $this->gateInward->getLastPurchasePrice(['item_id'=>$item_id]);
            $material_price = ((!empty($mtPrice->price))?($mtPrice->price * $material_wt):0);
            $total_material_price = ($material_price * $prcData->prc_qty);
            $materialDetail .= '<tr>
                                    <td>'.$item_name.'</td>
                                    <td class="text-center">'.($material_wt * $prcData->prc_qty).'</td>
                                    <td class="text-center">'.$mtPrice->price.'</td>
                                    <td class="text-center">'.$material_price.'</td>
                                    <td class="text-center">'.$total_material_price.'</td>
                              </tr>';
            $totalMaterialCost += $total_material_price;
        }
        $i=1; $tbody=""; $totalProcessCost=0; $stock=0;$totalScrapCost = 0;
        
        $result = $this->sop->getPRCProcessList(['prc_id'=>$data['prc_id'], 'process_id'=>$prcData->process_ids, 'log_data'=>1]);
        if(!empty($result)) :
            foreach ($result as $row) :
                $finidhWt = $row->finish_wt;
                $prsScrapWt = ($finidhWt > 0) ? ($row->ok_qty + $row->rej_qty) * ($inputWt -  $finidhWt) : 0;
                $prsScrapWt = ($prsScrapWt > 0) ? round( $prsScrapWt,2) : 0;

                $costing = (!empty($row->process_cost) && !empty($row->ok_qty))? $row->process_cost * $row->ok_qty : 0;
                $costing = round($costing,2);
                $scrapCost = ($prsScrapWt + ($row->rej_qty*$finidhWt)) * $row->process_cost;
                $scrapCost = round($scrapCost,2);
                $tbody .= '<tr class="text-center">
                            <td>'.$i++.'</td>
                            <td class="text-left">'.$row->current_process.'</td>
                            <td>'.floatval($row->ok_qty).'</td>
                            <td>'.floatval($row->rej_qty).'</td>
                            <td>'.floatval($prsScrapWt).'</td>
                            <td>'.floatVal($row->process_cost).'</td>
                            <td>'.floatVal($costing).'</td>
                            <td>'.floatval($scrapCost).'</td>
                        </tr>';
                $totalProcessCost += $costing;
                $totalScrapCost += $scrapCost;
                $inputWt =  $finidhWt;
            endforeach;
        endif;
        
        $tfoot = '<tr >
            <th colspan="6" class="text-right">TOTAL</th>
            <th class="text-center">' . $totalProcessCost . '</th>
            <th class="text-center">' . $totalScrapCost . '</th>
        </tr>';
        $finalCostDetail= ' <tr class="text-left ">
                                <th style="width:20%" class="bg-light">Material Cost</th>
                                <td>'.$totalMaterialCost.'</td>
                            </tr>
                            <tr class="text-left">
                                <th class="bg-light">Process Cost</th>
                                <td>'.$totalProcessCost.'</td>
                            </tr>
                            <tr class="text-left">
                                <th class="bg-light">Scrap Recovery</th>
                                <td>'.$totalScrapCost.'</td>
                            </tr>
                            <tr class="text-left">
                                <th class="bg-light">Final Cost</th>
                                <td>'.(($totalMaterialCost + $totalProcessCost) - $totalScrapCost).'</td>
                            </tr>';
        if($data['is_pdf'] == 1){
            $logo = base_url('assets/images/logo.png');
			
            //PRC Detail 
            $pdfData = '<table class="table top-table-border">
                            <thead >
                                <tr class="thead-info text-center">
                                    <th class="bg-light">PRC  Number</th>
                                    <th class="bg-light">PRC  Date</th>
                                    <th class="bg-light">Product</th>
                                    <th class="bg-light">Prc Qty</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                '.$prcDetail.'
                            </tbody>
                        </table>';
            //Material Data 
            $pdfData .= '<h5>Material Cost : </h5>
                        <table class="table top-table-border" repeat_header="1">
                            <thead class="bg-light" id="theadData">
                                <tr>
                                    <th class="bg-light">Material Name</th>
                                    <th class="bg-light">Qty</th>
                                    <th class="bg-light">RM Rate </th>
                                    <th class="bg-light">Costing/Pcs </th>
                                    <th class="bg-light">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>'.$materialDetail.'</tbody>
                        </table>';
            // Process Cost : 
            $pdfData .= '<h5>Process Cost : </h5>
                        <table class="table top-table-border" repeat_header="1">
                            <thead class="thead-dark" id="theadData">
                                <tr>
                                    <th class="bg-light">#</th>
                                    <th class="bg-light">Process Name</th>
                                    <th class="bg-light">OK Qty.</th>
                                    <th class="bg-light">Rej Qty.</th>
                                    <th class="bg-light">Scrap Qty.</th>
                                    <th class="bg-light">Costing/Pcs</th>
                                    <th class="bg-light">Total Costing</th>
                                    <th class="bg-light">Scrap Cost</th>                                            
                                </tr>
                            </thead>
                            <tbody>'.$tbody.'</tbody>
                            <tfoot>'.$tfoot.'</tfoot>
                        </table>';
            //Final Cost
            $pdfData .= '<h5>Final Cost :  </h5>
                        <table class="table top-table-border" repeat_header="1">
                            '.$finalCostDetail.'
                        </table>';
            $htmlHeader = '<table class="table">
                                <tr>
                                    <td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
                                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">PRC Cost</td>
                                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"></td>
                                </tr>
                            </table>';
            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                                <tr>
                                    <td style="width:50%;font-size:12px;"></td>
                                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                                </tr>
                            </table>';

            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/job_cost_'.$data['prc_id'].'.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo,0.05,array(100,100));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->AddPage('P','','','','',5,5,19,20,3,3,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName,'I');
        }else{
            $this->printJson(['status' => 1, 'tbody' => $tbody,'tfoot' => $tfoot,'prcDetail' => $prcDetail,'materialDetail' =>$materialDetail,'finalCostDetail'=>$finalCostDetail]);
        }
        
    }
	
    /**Overall summary report  */
    public function overallSummaryReport(){
        $this->data['headData']->pageTitle = 'OVERALL SUMMARY REPORT';
        // $this->data['headData']->pageUrl = "reports/productionReport/overallSummaryReport";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view("reports/production_report/overall_summary_report",$this->data);
    }

    public function overallSummaryReportData($jsonData=""){
        
        if(!empty($jsonData)){
            $data = (array) decodeURL($jsonData);
        }else{
            $data = $this->input->post();
        }
        $data['group_by'] = 'prc_master.item_id,prc_log.process_id,prc_log.process_by,process_master.process_type';
        $productionData = $this->productionReport->getParetoAnalysisData($data);
        
        $inhouseForge = [];$inhouseMc = [];$jwForge = [];$jwMc=[];$finalProcess = [];
        $totalIhForgeRej = $totaljwForgeRej = $totalIhMcRej = $totalJwMcRej = $totalFinalProcessRej = 0;$totalProductionQty = 0;
        $totalRejQty =  0;
        $totalIhForgeRejPrice = $totaljwForgeRejPrice = $totalIhMcRejPrice = $totalJwMcRejPrice = $totalFinalProcessRejPrice = 0;
        if(!empty($productionData)){
            $item_ids = implode(",",array_unique(array_column($productionData,'item_id')));
            $lastSales = $this->productionReport->getLastSalesPrice(['to_date'=>$data['to_date'],'item_id'=>$item_ids]);
            $itemPriceData = array_reduce($lastSales,function($priceData,$item){
                $priceData[$item->item_id] = $item->price;
                return $priceData;
            },[]);
            foreach($productionData AS $row){
                if($row->process_id == 2){
                    //Final Inspection Process
                    $totalFinalProcessRej += $row->total_rej_qty;
                }elseif($row->process_by == 1 AND $row->process_type == 2){
                    //IF Inhouse Forge Process
                    if(!isset($inhouseForge[$row->item_id]['lot_qty'])){
                        $inhouseForge[$row->item_id]['lot_qty'] = 0;
                        $inhouseForge[$row->item_id]['rej_qty'] = 0;
                    }
                    $inhouseForge[$row->item_id]['item_code'] = $row->item_code;
                    $inhouseForge[$row->item_id]['lot_qty'] += $row->total_ok_qty + $row->total_rej_qty + $row->total_rw_qty;
                    $inhouseForge[$row->item_id]['rej_qty'] += $row->total_rej_qty;
                    $totalIhForgeRej += $row->total_rej_qty;
                    $totalIhForgeRejPrice += ($row->total_rej_qty * (!empty($itemPriceData[$row->item_id]))?$itemPriceData[$row->item_id]:0);
                }elseif($row->process_by == 3 AND $row->process_type == 2){
                    //Jobwork Forge Process
                    if(!isset($jwForge[$row->item_id]['lot_qty'])){
                        $jwForge[$row->item_id]['lot_qty'] = 0;
                        $jwForge[$row->item_id]['rej_qty'] = 0;
                    }
                    $jwForge[$row->item_id]['item_code'] = $row->item_code;
                    $jwForge[$row->item_id]['lot_qty'] += $row->total_ok_qty + $row->total_rej_qty + $row->total_rw_qty;
                    $jwForge[$row->item_id]['rej_qty'] += $row->total_rej_qty;
                    $totaljwForgeRej += $row->total_rej_qty;
                    $totaljwForgeRejPrice += ($row->total_rej_qty * (!empty($itemPriceData[$row->item_id]))?$itemPriceData[$row->item_id]:0);

                }elseif($row->process_by == 1 AND $row->process_type == 1){
                    //IF Inhouse MAchining Process
                    if(!isset($inhouseMc[$row->item_id]['lot_qty'])){
                        $inhouseMc[$row->item_id]['lot_qty'] = 0;
                        $inhouseMc[$row->item_id]['rej_qty'] = 0;
                    }
                    $inhouseMc[$row->item_id]['item_code'] = $row->item_code;
                    $inhouseMc[$row->item_id]['lot_qty'] += $row->total_ok_qty + $row->total_rej_qty + $row->total_rw_qty;
                    $inhouseMc[$row->item_id]['rej_qty'] += $row->total_rej_qty;
                    $totalIhMcRej += $row->total_rej_qty; 
                    $totalIhMcRejPrice += ($row->total_rej_qty * (!empty($itemPriceData[$row->item_id]))?$itemPriceData[$row->item_id]:0);
                }elseif($row->process_by == 3 AND $row->process_type == 1){
                    //Jobwork MAchining Process
                    if(!isset($jwMc[$row->item_id]['lot_qty'])){
                        $jwMc[$row->item_id]['lot_qty'] = 0;
                        $jwMc[$row->item_id]['rej_qty'] = 0;
                    }
                    $jwMc[$row->item_id]['item_code'] = $row->item_code;
                    $jwMc[$row->item_id]['lot_qty'] += $row->total_ok_qty + $row->total_rej_qty + $row->total_rw_qty;
                    $jwMc[$row->item_id]['rej_qty'] += $row->total_rej_qty;
                    $totalJwMcRej += $row->total_rej_qty; 
                    $totalJwMcRejPrice += ($row->total_rej_qty * (!empty($itemPriceData[$row->item_id]))?$itemPriceData[$row->item_id]:0);
                }
                $totalProductionQty += $row->total_ok_qty + $row->total_rej_qty + $row->total_rw_qty;
                $totalRejQty += $row->total_rej_qty;
               
            }
        }
        $totalIhFPer = ($totalIhForgeRej * 100)/$totalProductionQty; //Inhouse Forge Rejection Total
        $totalJwFPer = ($totaljwForgeRej * 100)/$totalProductionQty; // Jobwork Forge Rejection
        $totalIhMcPer = ($totalIhMcRej * 100)/$totalProductionQty; //Inhouse Mc Rejection Total
        $totalJwMcPer = ($totalJwMcRej * 100)/$totalProductionQty; // Jobwork Mc Rejection
        $totalJFinalPer = ($totalFinalProcessRej * 100)/$totalProductionQty; // Final Stage Rejection

        $totalIhRejPer = $totalIhFPer + $totalIhMcPer +  $totalJFinalPer; //Total InHouse Rejection
        $totalFRejPer = $totalIhFPer + $totalJwFPer; //Overall Forging Rejection (InH + JW)

        // Top 10 InHouse FRG Rej. Qty.
        $inhouseForgeLog = [];$top10IhForgeTbody = '';
        foreach($inhouseForge AS $row){
            if($row['rej_qty'] > 0){
                $row['rej_per'] = ($row['rej_qty'] * 100)/$row['lot_qty'];
                $inhouseForgeLog[] = $row;
            }
        }
        if(!empty($inhouseForgeLog)){
            usort($inhouseForgeLog, function($a, $b) { return $b['rej_per'] <=> $a['rej_per']; });// Sort descending by rej_per
            $top10IhForge = array_slice($inhouseForgeLog, 0, 10);// Get top 10 records
            foreach ($top10IhForge as $item) {
                $top10IhForgeTbody .='<tr>
                                        <td>'.$item['item_code'].'</td>
                                        <td>'.$item['lot_qty'].'</td>
                                        <td>'.$item['rej_qty'].'</td>
                                        <td>'.sprintf("%.2f",$item['rej_per']).'%</td>
                                    </tr>';
            }
        }


        // Top 10 Jobwork FRG Rej. Qty.
        $jwForgeLog = [];$top10JwForgeTbody = '';
        foreach($jwForge AS $row){
            if($row['rej_qty'] > 0){
                $row['rej_per'] = ($row['rej_qty'] * 100)/$row['lot_qty'];
                $jwForgeLog[] = $row;
            }
        }
        if(!empty($jwForgeLog)){
            usort($jwForgeLog, function($a, $b) { return $b['rej_per'] <=> $a['rej_per']; }); // Sort descending by rej_per
            $top10JwForge = array_slice($jwForgeLog, 0, 10); // Get top 10 records
            foreach ($top10JwForge as $item) {
                $top10JwForgeTbody .='<tr>
                                        <td>'.$item['item_code'].'</td>
                                        <td>'.$item['lot_qty'].'</td>
                                        <td>'.$item['rej_qty'].'</td>
                                        <td>'.sprintf("%.2f",$item['rej_per']).'%</td>
                                    </tr>';
            }
        }

        // Top 10 Inhouse MAchining Rej. Qty.
        $ihMcLog = [];$top10IhMcTbody = '';
        foreach($inhouseMc AS $row){
            if($row['rej_qty'] > 0){
                $row['rej_per'] = ($row['rej_qty'] * 100)/$row['lot_qty'];
                $ihMcLog[] = $row;
            }
        }
        if(!empty($ihMcLog)){
            usort($ihMcLog, function($a, $b) { return $b['rej_per'] <=> $a['rej_per']; }); // Sort descending by rej_per
            $top10ihMc = array_slice($ihMcLog, 0, 10); // Get top 10 records
            foreach ($top10ihMc as $item) {
                $top10IhMcTbody .='<tr>
                                        <td>'.$item['item_code'].'</td>
                                        <td>'.$item['lot_qty'].'</td>
                                        <td>'.$item['rej_qty'].'</td>
                                        <td>'.sprintf("%.2f",$item['rej_per']).'%</td>
                                    </tr>';
            }
        }

        // Top 10 Jobwork Machine Rej. Qty.
        $jwMcLog = [];$top10JwMcTbody = '';
        foreach($jwMc AS $row){
            if($row['rej_qty'] > 0){
                $row['rej_per'] = ($row['rej_qty'] * 100)/$row['lot_qty'];
                $jwMcLog[] = $row;
            }
        }
        if(!empty($jwMcLog)){
            usort($jwMcLog, function($a, $b) { return $b['rej_per'] <=> $a['rej_per']; }); // Sort descending by rej_per
            $top10jwMc = array_slice($jwMcLog, 0, 10); // Get top 10 records
            foreach ($top10jwMc as $item) {
                $top10JwMcTbody .='<tr>
                                        <td>'.$item['item_code'].'</td>
                                        <td>'.$item['lot_qty'].'</td>
                                        <td>'.$item['rej_qty'].'</td>
                                        <td>'.sprintf("%.2f",$item['rej_per']).'%</td>
                                    </tr>';
            }
        }
        $custRejData = $this->salesReport->getCustomerComplaintsData(['from_date'=>$data['from_date'],'to_date'=>$data['to_date'],'product_returned'=>2]);
        $cust_rej_qty = ((!empty($custRejData)?array_sum(array_column($custRejData,'qty')):0));
        $cust_rej_amount = ((!empty($custRejData)?array_sum(array_column($custRejData,'rej_amount')):0));
        // Total Sales Qty. 
        $salesData = $this->productionReport->getSalesData($data);
        $sqPer = (!empty($salesData->sales_qty)?(($cust_rej_qty*100)/$salesData->sales_qty):0);
        $saPer = (!empty($salesData->sales_amount)?(($cust_rej_amount*100)/$salesData->sales_amount):0);
        $salesQtyTbody ='<tr>
                            <td>Sales Qty</td>
                            <td >'.$salesData->sales_qty.'</td>
                            <td rowspan="2">'.sprintf("%.2f",$sqPer).'%</td>
                        </tr>
                        <tr>
                            <td>Rej Qty</td>
                            <td >'.$cust_rej_qty.'</td>
                        </tr>';
        $salesValueTbody ='<tr>
                            <td>Sales Value</td>
                            <td>'.$salesData->sales_amount.'</td>
                            <td rowspan="2">'.sprintf("%.2f",$saPer).'%</td>
                        </tr>
                        <tr>
                            <td>Rej Value</td>
                            <td >'.$cust_rej_amount.'</td>
                        </tr>';
        $custRejTbody = '';
        if(!empty($custRejData)){
            $custRej = array_reduce($custRejData, function($rejData, $rej) { 
                
                $rejData[$rej->party_id] = $rej;
                if(!isset( $rejData[$rej->party_id]->total_rej_amount)){
                    $rejData[$rej->party_id]->total_rej_amount = 0;
                }
                $rejData[$rej->party_id]->total_rej_amount+= $rej->rej_amount;
                return $rejData; 
            }, []);
            foreach($custRej AS $row){
                $custRejTbody.= '<tr class="text-center">
                                <td>'.$row->party_name.'</td>
                                <td>'.$row->rej_amount.'</td>
                            </tr>';
            }
        }
        $totalRejQty = $totalIhForgeRej+$totaljwForgeRej+$totalIhMcRej+$totalJwMcRej+ $cust_rej_qty;
        $ihForgePR = ($totalIhForgeRej*100)/$totalRejQty;
        $jwForgePR = ($totaljwForgeRej*100)/$totalRejQty;
        $ihMcPR = ($totalIhMcRej*100)/$totalRejQty;
        $jwMcPR = ($totalJwMcRej*100)/$totalRejQty;
        $custRejPR = ($cust_rej_qty*100)/$totalRejQty;
        $copqTbody ='<tr>
                        <td>InHouse - Forging Rej.</td>
                        <td class="text-center">'.$totalIhForgeRej.'</td>
                        <td class="text-center">'.$totalIhForgeRejPrice.'</td>
                        <td class="text-center">'.sprintf("%.2f",$ihForgePR).'%</td>
                    </tr>
                    <tr>
                        <td>Job Work - Forging Rej.</td>
                        <td class="text-center">'.$totaljwForgeRej.'</td>
                        <td class="text-center">'.$totaljwForgeRejPrice.'</td>
                        <td class="text-center">'.sprintf("%.2f",$jwForgePR).'%</td>
                    </tr>
                    <tr>
                        <td>InHouse - M/C. Rej.</td>
                        <td class="text-center">'.$totalIhMcRej.'</td>
                        <td class="text-center">'.$totalIhMcRejPrice.'</td>
                        <td class="text-center">'.sprintf("%.2f",$ihMcPR).'%</td>
                    </tr>
                    <tr>
                        <td>Job Work - M/C. Rej.</td>
                        <td class="text-center">'.$totalJwMcRej.'</td>
                        <td class="text-center">'.$totalJwMcRejPrice.'</td>
                        <td class="text-center">'.sprintf("%.2f",$jwMcPR).'%</td>
                    </tr>
                    <tr>
                        <td>Customer'."'".'s End Rej.</td>
                        <td class="text-center">'.$cust_rej_qty.'</td>
                        <td class="text-center">'.$cust_rej_amount.'</td>
                        <td class="text-center">'.sprintf("%.2f",$custRejPR).'%</td>
                    </tr>';
        $copqAmount = $totalIhForgeRejPrice + $totaljwForgeRejPrice + $totalIhMcRejPrice + $totalJwMcRejPrice + $cust_rej_amount;
        $copqTFoot ='<tr class="bg-light-cream">
                        <th text-right>Total</th>
                        <th class="text-center">'.$totalRejQty.'</th>
                        <th class="text-center">'.$copqAmount.'</th>
                        <th class="text-center">100%</th>
                    </tr>';
        
        
        $this->printJson(['status' => 1,'totalIhForgeRej'=>sprintf("%.2f",$totalIhFPer).'%','totaljwForgeRej'=>sprintf("%.2f",$totalJwFPer).'%','totalIhMcRej'=>sprintf("%.2f",$totalIhMcPer).'%','totalJwMcRej'=>sprintf("%.2f",$totalJwMcPer).'%','totalFinalProcessRej'=>sprintf("%.2f",$totalJFinalPer).'%','totalIhRejPer'=>sprintf("%.2f",$totalIhRejPer).'%','totalFRejPer'=>sprintf("%.2f",$totalFRejPer).'%','top10IhForgeTbody'=>$top10IhForgeTbody,'top10JwForgeTbody'=>$top10JwForgeTbody,'top10IhMcTbody'=>$top10IhMcTbody,'top10JwMcTbody'=>$top10JwMcTbody,'salesQtyTbody'=>$salesQtyTbody,'salesValueTbody'=>$salesValueTbody,'custRejTbody'=>$custRejTbody,'copqTbody'=>$copqTbody,'copqTFoot'=>$copqTFoot,'copqAmount'=>sprintf("%.2f",$copqAmount)]);
        
    }

	//GST ITC-04 REPORT @09/09/25
    public function gstITC(){
        $this->data['headData']->pageTitle = 'GST ITC';
        $this->data['startDate'] = date('Y-m-01');
        $this->data['endDate'] = date('Y-m-d');
        $this->load->view('reports/production_report/gst_itc',$this->data);
    }
    
    public function getGSTITC4($jsonData=""){
        if(!empty($jsonData)){
            $data = (array) decodeURL($jsonData);
        }else{
            $data = $this->input->post();
        }

        $jobOutData = $this->productionReport->getVendorChallanOutDetail($data);
		$tbody = "";
        if($data['itc_type'] == 1){
            $thead= '<tr class="text-center"><th colspan="15" style="background:#B5B3B3;color:black">Details of inputs/capital goods sent for job-work</th></tr>
                        <tr style="background:#ffdb93;">
                            <th style="min-width:100px;">Job Worker</th>
                            <th style="min-width:80px;">Job Worker GSTIN</th>
                            <th style="min-width:50px;">State</th>
                            <th style="min-width:100px;">Job Worker\'s Type</th>
                            <th style="min-width:80px;">Challan Number</th>
                            <th style="min-width:100px;">Challan Date</th>
                            <th style="min-width:100px;">Types of Goods</th>
                            <th style="min-width:100px;">Description of Goods</th>
                            <th style="min-width:100px;">UQC</th>
                            <th style="min-width:100px;">QTY</th>
                            <th style="min-width:50px;">Taxable Value</th>
                            <th style="min-width:50px;">IGST Rate</th>
                            <th style="min-width:50px;">CGST Rate</th>
                            <th style="min-width:50px;">SGST Rate</th>
                            <th style="min-width:50px;">Cess</th>
                    </tr>';
            
            foreach ($jobOutData as $row) :
                $taxableAmt = round(($row->qty * $row->price),2);
                $cgstAmt = $sgstAmt = round((($taxableAmt * round(($row->gst_per/2),2))/100),2);
                $igstAmt = $cgstAmt + $sgstAmt;
                if($row->gst_statecode != 24){$cgstAmt = 0;$sgstAmt = 0;}else{$igstAmt = 0;}

                $tbody .= '<tr>
                    <td class="text-left">'.$row->party_name.'</td>
                    <td>'.$row->gstin.'</td>
                    <td class="text-left">'.$row->state_name.'</td>
                    <td>NON SEZ</td>
                    <td>'.$row->ch_number.'</td>
                    <td>'.(!empty($row->ch_date) ? date('d-m-Y',strtotime($row->ch_date)) : '').'</td>
                    <td>Inputs</td>
                    <td class="text-left">'.$row->item_name.'</td>
                    <td>'.$row->uom.'</td>
                    <td>'.$row->qty.'</td>
                    <td>'.$taxableAmt.'</td>
                    <td>'.$igstAmt.'</td>
                    <td>'.$cgstAmt.'</td>
                    <td>'.$sgstAmt.'</td>
                    <td></td>
                </tr>';
            endforeach;
        }else{
            $thead= '<tr class="text-center"><th colspan="13" style="background:#B5B3B3;color:black">5(A)Details of inputs/capital goods received from Job worker to whom such goods were sent for job work;losses & wastes</th></tr>
					<tr style="background:#ffdb93;">
						<th style="min-width:100px;">Job Worker</th>
						<th style="min-width:80px;">Job Worker GSTIN</th>
						<th style="min-width:50px;">State</th>
						<th style="min-width:50px;">Original Challan No.<br><small>(Issued By Principal)</small></th>
						<th style="min-width:50px;">Original Challan Date<br><small>(Issued By Principal)</small></th>
						<th style="min-width:50px;">Challan No.<br><small>(Issued By JW)</small></th>
						<th style="min-width:50px;">Challan Date<br><small>(Issued By JW)</small></th>
						<th style="min-width:100px;">Nature of Job Work Done</th>
						<th style="min-width:100px;">Description of Goods</th>
						<th style="min-width:100px;">UQC</th>
						<th style="min-width:100px;">QTY</th>
						<th style="min-width:100px;">UQC<br><small>(Losses & Wastes)</small></th>
						<th style="min-width:100px;">QTY<br><small>(Losses & Wastes)</small></th>
				</tr>';
		
            foreach ($jobOutData as $row) :
				$prcBom = $this->sop->getPrcBomData(['prc_id'=>$row->prc_id,'stock_data'=>1,'single_row'=>1]);
				$material_weight = 0;
				if($row->cutting_flow == 1){
					$material_weight = (!empty($prcBom->uom) && $prcBom->uom == 'KGS') ? $prcBom->ppc_qty : $prcBom->wt_pcs;
				}else{
					$cuttingBatch = $this->sop->getCuttingBatchDetail(['prc_number'=>$prcBom->batch_no,'heat_no'=>$prcBom->heat_no,'single_row'=>1]);
					$material_weight = $cuttingBatch->ppc_qty;
				}
			
				$beforWt = ((empty($row->process_from))?$material_weight:((!empty($row->prev_weight) && $row->prev_weight > 0)?$row->prev_weight:0)) ;
				$kgs = $beforWt * $row->qty;
				
				$prcKgs = round($kgs,2);
				$itemQty = round($row->qty,2);
				$kg_per_nos = (!empty($itemQty) ? ($prcKgs / $itemQty) : 0);
				$scrap_wt = round($kg_per_nos - $row->finish_wt,4);
				$process_loss = ($row->rej_qty * $kg_per_nos);
				$total_scrap = ($scrap_wt * $itemQty);
                
                $tbody .= '<tr>
                    <td class="text-left">'.$row->party_name.'</td>
                    <td>'.$row->gstin. '</td>
                    <td class="text-left">'.$row->state_name.'</td>
                    <td>'.$row->ch_number.'</td>
                    <td>'.(!empty($row->ch_date) ? date('d-m-Y',strtotime($row->ch_date)) : '').'</td>
                    <td>'.$row->in_challan_no.'</td>
                    <td>'.(!empty($row->trans_date) ? date('d-m-Y',strtotime($row->trans_date)) : '').'</td>
                    <td class="text-left">'.$row->process_name.'</td>
                    <td class="text-left">'.$row->item_name.'</td>
                    <td>'.$row->uom.'</td>
                    <td>'.$row->qty.'</td>
                    <td>KGS</td>
                    <td>'.floatval($total_scrap + $process_loss).'</td>
                </tr>';
            endforeach;
        }
            
        if($data['file_type'] == 1){
            if($data['itc_type'] == 1){
                $xls_filename = 'GST ITC-04_'.date('F',strtotime($data['from_date'])).'-'.date('Y',strtotime($data['from_date'])).'.xls';
            }else{
                $xls_filename = 'GST ITC-05_'.date('F',strtotime($data['from_date'])).'-'.date('Y',strtotime($data['from_date'])).'.xls';
            }
            
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename='.$xls_filename);
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $response = '<table class="table-bordered itemList" border="1" repeat_header="1">
                    <thead class="thead-info dataHead" id="theadData">'.$thead.'</thead>
                    <tbody>'.$tbody.'</tbody>
                </table>';
            echo $response;
        }else{
            $this->printJson(['status' => 1, 'thead' => $thead, 'tbody' => $tbody]);
        }
    } 

}
?>