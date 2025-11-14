<?php
class StoreReport extends MY_Controller{
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Store Report";
		$this->data['headData']->controller = "reports/storeReport";
    }

    public function stockRegister(){
		$this->data['headData']->pageTitle = "STOCK REGISTER";
        $this->data['pageHeader'] = 'STOCK REGISTER';
        $this->data['headData']->pageUrl = "reports/storeReport/stockRegister";
        $this->load->view("reports/store_report/item_stock",$this->data);
    }

	public function getStockRegisterData(){
        $data = $this->input->post();
        $result = $this->storeReport->getStockRegisterData($data);

        $thead = '<tr class="text-center">
            <th colspan="4">Stock Register</th>
        </tr>
        <tr>
            <th class="text-center">#</th>
            <th class="text-left">Item Description</th>
            <th class="text-right">Balance Qty.</th>
        </tr>';

        if($data['item_type'] == 3){
            $thead = '<tr class="text-center">
                <th colspan="5">Stock Register</th>
            </tr>
            <tr>
                <th class="text-center">#</th>
                <th class="text-left">Item Description</th>
                <th class="text-center">Material Grade</th>
                <th class="text-right">Balance Qty.</th>
            </tr>';
        }

        $tbody = '';$i=1;
		foreach($result as $row):
			$batch_qty = floatVal($row->stock_qty);
            if(floatVal($row->stock_qty) > 0){                
                $locationId = (isset($data['item_type']) && $data['item_type'] == 0) ? "/".$this->CUT_STORE->id : '';
                $batch_qty = '<a href="'.base_url("reports/storeReport/batchStockHistory/".$row->item_id.$locationId).'" target="_blank" datatip="Ledger" flow="left">'.floatVal($row->stock_qty).'</a>';
            }
            $td = (($row->item_type == 3) ? '<td class="text-center">'.$row->material_grade.'</td>' : '');
            $locationId = (isset($data['item_type']) && $data['item_type'] == 0) ? "/0/".$this->CUT_STORE->id : '';
			
            $tbody .= '<tr>
                <td class="text-center">'.$i++.'</td>
                <td class="text-left">
					<a href="'.base_url("reports/storeReport/itemHistory/".encodeURL($row->item_id)).$locationId.'" target="_blank" datatip="History" flow="left">'.$row->item_name.'</a>
				</td>
                '.$td.'
                <td class="text-right">
                    '.$batch_qty.'
				</td>
            </tr>';
        endforeach;
		
        $this->printJson(['status'=>1,'tbody'=>$tbody,'thead'=>$thead]);
    }
	
	public function itemHistory($item_id="",$batch_no="",$location_id=""){
        $this->data['item_id'] = $item_id = decodeURL($item_id);
        $this->data['batch_no'] = decodeURL($batch_no);
        $this->data['location_id'] = $location_id;
		$this->data['headData']->pageTitle = "Item History";
        $this->data['itemData'] = $this->item->getItem(['id'=>$item_id]);
        $this->data['from_date'] = $this->startYearDate;
        $this->data['to_date'] = $this->endYearDate;
        $this->load->view('reports/store_report/item_history',$this->data);
    }
	
    public function getItemHistory(){
		$data = $this->input->post();
        $itemData = $this->item->getItem(['id'=>$data['item_id']]);
		
		$itemSummary = $this->storeReport->getItemSummary($data);
        $itemHistory = $this->storeReport->getItemHistory($data);

        $thead = '<tr class="text-center">
            <th colspan="5" class="text-left">'.((!empty($itemData))?$itemData->item_name:"Item History").'</th>
            <th colspan="2" class="text-right">Op. Stock : '.floatVal($itemSummary->op_stock_qty).'</th>
        </tr>
        <tr>
            <th style="min-width:25px;">#</th>
            <th style="min-width:100px;">Trans. Type</th>
            <th style="min-width:100px;">Trans. No.</th>
            <th style="min-width:50px;">Trans. Date</th>
            <th style="min-width:50px;">Inward</th>
            <th style="min-width:50px;">Outward</th>
            <th style="min-width:50px;">Balance</th>
        </tr>';
		
        $i=1; $tbody =""; $tfoot=""; $balanceQty = $itemSummary->op_stock_qty;
        foreach($itemHistory as $row):  
            $balanceQty += $row->qty * $row->p_or_m;     

			$trans_type = ($row->p_or_m == 1) ? 'Inward To' : 'Out from';
			$row->ref_no = (!empty($row->ref_no) ? $row->ref_no : ((!empty($row->sub_menu_name)) ? $row->sub_menu_name : 'Stock Verification'));
			$row->emp_name = (!empty($row->emp_name) ? '<br>'.$row->emp_name : '<br> Admin');
			
            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>'.$trans_type.' '.$row->location.'</td>
                <td>'.(empty($data['stock_type']) ? $row->ref_no : '-').'</td>
                <td>'.(empty($data['stock_type']) ? formatDate($row->trans_date).$row->emp_name : '-').'</td>
                <td>'.floatVal($row->in_qty).'</td>
                <td>'.floatVal($row->out_qty).'</td>
                <td>'.floatVal($balanceQty).'</td>
            </tr>';
        endforeach;

        $tfoot .= '<tr>
            <th colspan="4" class="text-right">Cl. Stock</th>
            <th>' .floatVal($itemSummary->in_stock_qty). '</th>
            <th>' .floatVal($itemSummary->out_stock_qty). '</th>
            <th>' .floatVal($itemSummary->cl_stock_qty). '</th>
        </tr>';

        $this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }
	
	public function stockTransactions(){    
		$this->data['item_id'] = $this->input->post('item_id');
		$data['item_id'] = $this->data['item_id'];
        $result = $this->storeReport->getStockTransaction($data);
        
        $tbody = ""; $i=1;
        foreach($result as $row):
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td class="text-left">'.$row->batch_no.'</td>
                <td  class="text-right">'.floatVal($row->stock_qty).'</td>
            </tr>';
        endforeach;
		$this->data['tbody'] = $tbody;
        $this->load->view("reports/store_report/brand_wise_trans",$this->data);
    }
    
	/* ISSUE REGISTER REPORT Created By Sagar : 13/05/2024 */
    public function issueRegister(){
        $this->data['headData']->pageUrl = "reports/storeReport/issueRegister";
        $this->data['headData']->pageTitle = "ISSUE REGISTER REPORT";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['subCategoryData'] = $this->itemCategory->getCategoryList(['category_type'=>'1,2,3','final_category'=>1]);
        $this->data['itemData'] = $this->item->getItemList();
        $this->load->view("reports/store_report/issue_register",$this->data);
    }

	/* Created By Sagar : 13/05/2024 */
    public function getSubCategory($data=array()) {
        $data = $this->input->post();
        $subCategoryData = $this->itemCategory->getCategoryList(['category_type'=>$data['category_type'],'final_category'=>1]);
        $subCatOptions = "<option value=''>ALL Category</option>";
        foreach ($subCategoryData as $row) {
            $subCatOptions .= "<option value='".$row->id."'>".$row->category_name."</option>";
        }

        $itemData = $this->item->getItemList(['item_type'=>$data['category_type']]);
        $itemOptions = "<option value=''>ALL Item</option>";
        foreach ($itemData as $row) {
            $itemOptions .= "<option value='".$row->id."'>".$row->item_name."</option>";
        }

        $this->printJson(['status'=>1, 'subCatOptions'=>$subCatOptions, 'itemOptions' => $itemOptions]);
    }

	/* Created By Sagar : 13/05/2024 */
    public function getItemList($data=array()) {
        $data = $this->input->post();
        $itemData = $this->item->getItemList(['category_id'=>$data['item_type']]);
        $options = "<option value=''>ALL Item</option>";
        foreach ($itemData as $row) {
            $options.="<option value='".$row->id."'>".$row->item_name."</option>";
        }
        $this->printJson(['status'=>1, 'options'=>$options]);
    }

	/* Created By Sagar : 13/05/2024 */
    public function getIssueRegister(){
        $data = $this->input->post();
        $errorMessage = array();
        if($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $issueData = $this->storeReport->getIssueRegister($data);
            $tbody=""; $i=1; $tfoot=""; $totalQty=0; $totalRetQty=0; $totalPenRetQty=0;

            foreach($issueData as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->issue_number.'</td>
                    <td>'.formatDate($row->issue_date).'</td>
                    <td>'.(!empty($row->prc_number) ? $row->prc_number : "-").'</td>
                    <td>'.(!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name.'</td>
                    <td>'.floatval($row->issue_qty).'</td>
                    <td>'.(($row->is_return == 1) ? floatval($row->return_qty) : "-").'</td>
                    <td>'.(($row->is_return == 1) ? floatval($row->issue_qty - $row->return_qty) : "-").'</td>
                </tr>';
                $totalQty+=floatval($row->issue_qty);
                $totalRetQty+=(($row->is_return == 1) ? floatval($row->return_qty) : 0);
                $totalPenRetQty+=(($row->is_return == 1) ? floatval($row->issue_qty - $row->return_qty) : 0);
            endforeach;
            $tfoot = '<tr>
                    <th colspan="5">Total</th>
                    <th>'.round($totalQty).'</th>
                    <th>'.round($totalRetQty).'</th>
                    <th>'.round($totalPenRetQty).'</th>
                </tr>';
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }

   /* INVENTORY MONITORING REPORT CREATE BY RASHMI 15/05/2024*/
    public function inventoryMonitor(){
        $this->data['pageHeader'] = 'INVENTORY MONITORING REPORT';
        $this->data['categoryList'] = $this->itemCategory->getCategoryList(['final_category'=>0,'ref_id'=>0]);
        $this->load->view('reports/store_report/inventory_monitor',$this->data);
    }

	/*CREATE BY RASHMI 15/05/2024*/
    public function getInventoryMonitor(){
        $data = $this->input->post();
        $errorMessage = array();
		if(empty($data['to_date']))
			$errorMessage['toDate'] = "Date is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData = $this->storeReport->getInventoryMonitor($data);
            $tbody="";$i=1;$opningStock=0;$closingStock=0;$totalClosing=0;$fyOpeningStock=0;$totalOpeningStock=0;$monthlyInward=0;$monthlyCons=0;$inventory=0;$amount=0;$total=0;$totalInventory=0;$totalValue=0;$totalUP=0;
            
            foreach($itemData as $row):                
                $data['item_id'] = $row->id;
                $fyOSData = Array();
                $opningStock = (!empty($row->opening_qty)) ? $row->opening_qty : 0;
                $monthlyInward = $row->rqty;
                $monthlyCons = abs($row->iqty);
                $totalOpeningStock = floatval($opningStock);
                $closingStock = ($totalOpeningStock + $monthlyInward - $monthlyCons);
                $total = round(($closingStock * $row->price), 2);
				
				$cl_qty = floatVal(round($closingStock,2));
				if(floatVal($cl_qty) > 0){                
					$cl_qty = '<a href="'.base_url("reports/storeReport/batchStockHistory/".$row->id).'" target="_blank" datatip="Ledger" flow="left">'.floatVal(round($closingStock,2)).'</a>';
				}
                
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.(!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name.'</td>
                    <td>'.$row->uom.'</td>
                    <td>'.floatVal($totalOpeningStock).'</td>
                    <td>'.floatVal(round($monthlyInward,2)).'</td>
                    <td>'.floatVal(round($monthlyCons,2)).'</td>
                    <td>'.$cl_qty.'</td>
                    <td>'.numberFormatIndia($row->price).'</td>
                    <td>'.numberFormatIndia($total).'</td>
                </tr>';
                //$totalInventory += round($row->price,2);
                $totalValue += $total;
                $totalClosing += $closingStock;
            endforeach;
            
            $totalAvgRate = (!empty($totalInventory)) ? round(($totalInventory / ($i-1)),2) : 0;
            $totalUP = (!empty($totalInventory)) ? round(($totalValue / $totalInventory),2) : 0;

            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'totalClosing'=>number_format($totalClosing,2), 'totalInventory'=>'', 'totalUP'=>numberFormatIndia($totalUP), 'totalValue'=>numberFormatIndia($totalValue)]);
        endif;
    }

    public function bomReport(){
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view("reports/store_report/bom_report",$this->data);
    }

    public function getBomData(){
        $data = $this->input->post();
        $this->data['item_name'] = $data['item_name'];
        if($data['report_type'] == 1){
            $this->data['bomData'] = $this->storeReport->getBomData(['item_id'=>$data['item_id']]);
            $html = $this->load->view("reports/store_report/bom_view",$this->data,true);
        }else{
            $this->data['bomData'] = $this->storeReport->getBomCostData(['item_id'=>$data['item_id']]);
            $html = $this->load->view("reports/store_report/bom_cost_view",$this->data,true);
        }
       
        
        $this->printJson(['status'=>1, 'html'=>$html]);
    }

	public function batchStockHistory($item_id="",$location_id=""){
        $this->data['headData']->pageTitle = "Stock Ledger";
        $this->data['pageHeader'] = 'STOCK LEDGER';
        $this->data['itemData'] = $this->item->getItem(['id'=>$item_id]);
        $this->data['location_id'] = $location_id;
        $this->load->view('reports/store_report/stock_history',$this->data);
    }

    public function getBatchStockHistory(){
        $data = $this->input->post();
        $data['stock_required'] = 1;
        $data['group_by'] = "stock_trans.location_id,stock_trans.batch_no";
        $data['supplier'] = 1;
        $stockHistory = $this->itemStock->getItemStockBatchWise($data);
        
        $i=1; $tbody =""; 
        foreach($stockHistory as $row):  
            $row->fg_item_name = (!empty($row->fg_item_code)) ? '['.$row->fg_item_code.'] '.$row->fg_item_name : $row->fg_item_name;
            $row->batch_no = ($row->location_id == $this->CUT_STORE->id)? '('.$row->ref_batch.') '.$row->batch_no : $row->batch_no;
            
			$tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>'.$row->store_name.' - '.$row->location.'</td>
                <td>'.$row->heat_no.'</td>
                <td>'.$row->batch_no.'</td>
                <td>'.$row->party_name.'<br>'.$row->fg_item_name.'</td>
                <td>'.$row->qty.'('.$row->uom.')</td>
            </tr>';
        endforeach;

        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }
}
?>