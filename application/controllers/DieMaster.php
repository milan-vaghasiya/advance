<?php
class DieMaster extends MY_Controller
{
    private $indexPage = "die_master/index";
    private $part_list_index = "die_master/part_list_index";
    private $reject_part = "die_master/reject_part";    
    private $acc_index = "die_master/acc_index";
	private $acc_form = "die_master/acc_form";
	private $die_set_form = "die_master/die_set_form";
	private $die_component_form = "die_master/die_component_form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Die Set"; 
		$this->data['headData']->controller = "dieMaster";
        $this->data['headData']->pageUrl = "dieMaster";
	}
	
	public function index(){
        $this->data['pageHeader'] = 'Die Set';
        $this->data['catData'] = $catData = $this->itemCategory->getCategoryList(['category_type'=>11,'final_category'=>1]);
        
        $dieRegisterData = $this->dieMaster->getDieRegister();

        $groupedResult = array_reduce($dieRegisterData, function($itemData, $row) {
            if(!isset($itemData[$row->fg_id][$row->set_no])):
                $row->{"category_".$row->category_id} = $row->cat_code;
				$row->{"die_id_".$row->category_id} = $row->id;
                unset($row->category_id,$row->cat_code,$row->id);
				
                $itemData[$row->fg_id][$row->set_no] = $row;
            else:
                $itemData[$row->fg_id][$row->set_no]->{"category_".$row->category_id} = $row->cat_code;
                $itemData[$row->fg_id][$row->set_no]->{"die_id_".$row->category_id} = $row->id;
            endif;
            
            return $itemData;
        }, []);
        
        $tbody=''; $i=1;
        foreach($groupedResult as $group):
            foreach ($group as $row):
                $dieTD=''; $setBtn='';
                foreach ($catData as $cat):
                    $catCode = (isset($row->{"category_".$cat->id})) ? $row->{"category_".$cat->id} : ''; 
					$die_id = (isset($row->{"die_id_".$cat->id})) ? $row->{"die_id_".$cat->id} : 0; 					
                    $dieHistoryParam = "{'postData':{'die_id' : ".$die_id."}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'viewDieHistory', 'call_function' : 'viewDieHistory', 'title' : '".$row->item_code.' ('.$row->category_name.")', 'button' : 'close'}";
                    $dieHistoryBtn = '<a href="javascript:void(0)" datatip="View Die History" flow="down" onclick="modalAction('.$dieHistoryParam.');">'.$catCode.'</a>';

                    if(!empty($catCode)){
						$dieTD .= '<td>'.$dieHistoryBtn.'</td>';
					}else{
						$dieList = $this->dieProduction->getDieMasterList(['category_id'=>$cat->id, 'fg_id'=>$row->fg_id, 'set_no'=>0, 'status'=>'0,1']);

                        if(!empty($dieList) && (!empty($row->status) && $row->status != 5)){
                            $dieTD .= '<td style="min-width:200px;">
                                <input type="hidden" name="set_no" id="set_no'.$row->fg_id.'_'.$cat->id.'" value="'.$row->set_no.'">
                                <input type="hidden" name="item_id" id="item_id'.$row->fg_id.'_'.$cat->id.'"  value="'.$row->fg_id.'">
                                <input type="hidden" name="category_id[]" id="category_id'.$row->fg_id.'_'.$cat->id.'" value="'.$cat->id.'">

                                <select name="die_id['.$cat->id.']" id="die_set_code'.$row->fg_id.'_'.$cat->id.'" class="form-control select2">
                                    <option value="">Select Die</option>';
                                    if(!empty($dieList)):
                                        foreach($dieList as $die):
                                            $dieTD .= '<option value="'.$die->id.'">'.$die->item_code.'</option>';
                                        endforeach;
                                    endif;
                                $dieTD .= '</select>
                                <div class="error die_id'.$cat->id.'"></div>
                            </td>';
                            
                            $setparam = "{'postData' : {'die_master_id' : '".$die->id."'}, 'formId' : 'dieSetForm', 'fnsave':'saveDieSet'}";
                            $setBtn = '<a class="btn btn-success permission-modify" href="javascript:void(0)" datatip="Save" flow="down" onclick="dieStore('.$setparam.');"><i class="fa fa-check"></i></a>';
                        }else{
                            $dieTD .= '<td></td>';
                        }
					}
                endforeach;
                
                if(empty($row->status)) { $status=' - '; }
                elseif($row->status == 1) { $status = 'Available'; }
                elseif($row->status == 2) { $status = 'Issued'; }
                elseif($row->status == 3) { $status = 'Hold'; }
                elseif($row->status == 4) { $status = 'Reject'; }

                $tbody .= '<tr>';
                $tbody .= '<td>'.getActionButton($setBtn).'</td>';
                $tbody .= '<td>'.$i++.'</td>';
                $tbody .= '<td>'.$row->item_name.'</td>';
                $tbody .= '<td></td>';
                $tbody .= '<td>'.$row->set_no.'</td>';
                $tbody .= $dieTD;
                $tbody .= '<td> - </td>';
                $tbody .= '<td>'.$status.'</td>';
                $tbody .= '<td> - </td>';
                $tbody .= '</tr>';
            endforeach;
        endforeach;

        $this->data['tbodyData'] = $tbody;
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function partListIndex(){
		$this->data['headData']->pageTitle = "Die Component"; 
        $this->data['headData']->pageUrl = "dieMaster/partListIndex";
        $this->data['tableHeader'] = getProductionDtHeader('partList');
        $this->load->view($this->part_list_index,$this->data);
    }

    public function getPartListDTRows($status = 0){
        $data = $this->input->post(); $data['status']=$status;
        $result = $this->dieMaster->getPartListDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPartListData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function rejectPart(){
        $this->data['id'] = $this->input->post('id');
        $this->load->view($this->reject_part, $this->data);
    }

    public function changePartStatus(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if($data['status'] == 4 && empty($data['rejection_reason'])){
            $errorMessage['rejection_reason'] = "Rejection Reason is required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dieMaster->changePartStatus($data));
        endif;
    }

	public function viewDieHistory(){
        $data = $this->input->post();
        $this->data['dieData'] = $this->dieMaster->getDieMasterData(['id'=>$data['die_id'], 'single_row'=>1]);
        $this->load->view('die_master/view_die_history',$this->data);
    }

    public function updateDieHistory(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['weight'])){
            $errorMessage['weight'] = "Weight is Required";
        }
        if(empty($data['height'])){
            $errorMessage['height'] = "Height is Required";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$this->printJson($this->dieMaster->updateDieHistory($data));
        endif;
    }

	public function dieHistoryHtml(){  
        $data = $this->input->post();
        $dieHistoryData = $this->dieMaster->getDieHistoryData($data);
        $logData = $this->sop->getPRCLog(['die_master_ids'=>$data['die_id'], 'item_id'=>$dieHistoryData[0]->fg_id]);
        $total_log = (!empty($logData->total_log) ? $logData->total_log : 0);

		$dieRecords = '';$total_volume = 0;
        if(!empty($dieHistoryData)):
            $i=1; $editBtn='';
            foreach($dieHistoryData as $row):
                $editBtn = "<button type='button' onclick='editDie(".json_encode($row).",this);' class='btn btn-sm btn-outline-success waves-effect waves-light permission-modify' datatip='Edit'><i class='far fa-edit'></i></button>";

                $dieRecords .='<tr class="text-center">
                    <td>'.$i.'</td>
                    <td>'.formatDate($row->recut_date).'</td>
                    <td>'.$row->recut_no.'</td>
                    <td>'.$row->pop_number. ' - ' .formatDate($row->pop_date).'</td>
                    <td>'.$row->capacity.'</td>
                    <td>'.floatval($row->volume).'</td>
                    <td>'.floatval($row->weight).'</td>
                    <td>'.floatval($row->height).'</td>
                    <td>'.floatval($row->total_value).'</td>
                    <td>'.floatval($row->length).'</td>
                    <td>'.floatval($row->width).'</td>
                    <td>'.floatval($row->material_value).'</td>
                    <td>
                        '.(!empty($row->attach_file) ? '<a href="'.base_url("assets/uploads/die_component/".$row->attach_file).'" class="btn btn-outline-primary" target="_blank"><i class="fa fa-download"></i></a>' : '').'
                    </td>
                    <td>'.$editBtn.'</td>
                </tr>';
                $total_volume += $row->volume;
                $i++;
            endforeach;
            $totalDieRun = $total_volume + $total_log;
        else:
            $dieRecords .= '<tr><td colspan="13" class="text-center">Data not available.</td></tr>';
        endif;
        $this->printJson(['status'=>1,'tbodyData'=>$dieRecords,'totalDieRun'=>$totalDieRun]);
	}
	
    public function recutDie(){
        $data = $this->input->post();
        $errorMessage = array();
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->dieProduction->recutDie($data));
        endif;
    }

	public function dieEntryIndex(){
        $this->data['headData']->pageUrl = "dieMaster";
        $this->data['headData']->pageTitle = "Die Entry";
        $this->data['tableHeader'] = getAccountingDtHeader('dieEntry');
        $this->load->view($this->acc_index,$this->data);
    }

    public function getDieEntryDTRows($trans_status=0){
        $data = $this->input->post(); $data['trans_status']=$trans_status;
        $result = $this->dieMaster->getDieEntryDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->trans_status = $trans_status;
            $sendData[] = getDieEntryData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addDieEntry(){
        $data = $this->input->post();
        $this->data['id'] = $data['id'];
        $this->data['status'] = $data['status'];
		$this->data['dieData'] = $this->dieMaster->getDieHistoryData(['id'=>$data['id'],'single_row'=>1]);
        $this->load->view($this->acc_form, $this->data);
    }

    public function saveAccVou(){
        $data = $this->input->post();
        $errorMessage = array();
        if(in_array($data['status'],[1,2,3])){
            if(empty($data['acc_vou'])){$errorMessage['acc_vou'] = "Acc. Voucher No. is required.";}
        }
        if(in_array($data['status'],[4])) {
            if(empty($data['rej_vou'])){$errorMessage['rej_vou'] = "Rej. Voucher No. is required.";}
        }
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			unset($data['status']);
            $this->printJson($this->dieMaster->saveAccVou($data));
        endif;
    }

	/* Add Die Set */
	public function addDieSet(){
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view($this->die_set_form,$this->data);
    }

    public function getCategoryWiseSet(){
        $data = $this->input->post();
        $dieSetData = $this->item->getDieSetData(['item_id'=>$data['item_id'],'group_by'=>'die_kit.ref_cat_id']);
           
		$i=1; $tbody=''; 
		if(!empty($dieSetData)):
			foreach($dieSetData as $row):

                $dieList = $this->dieProduction->getDieMasterList(['category_id'=>$row->ref_cat_id, 'fg_id'=>$data['item_id'], 'set_no'=>0, 'status'=>'1']);

				$tbody .= '<tr>
						<td class="text-center">'.$i++.'</td>
						<td>
                            '.$row->category_name.'
                            <input type="hidden" name="category_id[]" id="category_id'.$row->ref_cat_id.'" value="'.$row->ref_cat_id.'">
                        </td>
						<td>
                            <select name="die_id['.$row->ref_cat_id.']" id="die_id'.$row->ref_cat_id.'" class="form-control select2">
                                <option value="">Select Die</option>';
                                if(!empty($dieList)):
                                    foreach($dieList as $die):
                                        $tbody .= '<option value="'.$die->id.'">'.$die->item_code.'</option>';
                                    endforeach;
                                endif;
                            $tbody .= '</select>
                            <div class="error die_id'.$row->ref_cat_id.'"></div>
                        </td>
                    </tr>';
			endforeach;
        else:
            $tbody = '<tr><td colspan="3" class="text-center">No data found.</td></tr>';
		endif;
        $this->printJson(['tbody'=>$tbody]);
    }

    public function saveDieSet(){
        $data = $this->input->post();
		$errorMessage = array();

        if (empty($data['item_id'])) {
            $errorMessage['item_id'] = "Product is required.";
        }

        if (empty($data['category_id'])) {
            $errorMessage['general_error'] = "Category is required.";
        } else {
            foreach ($data['category_id'] as $key => $value) {
                if (empty($data['die_id'][$value])) {
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

    /* Add Die Component */
    public function addDieComponent(){
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view($this->die_component_form,$this->data);        
    }

    public function getDieCategoryList(){
        $data = $this->input->post();
        $catData = $this->item->getDieSetData(['item_id'=>$data['item_id'],'group_by'=>'die_kit.ref_cat_id']);

        $options = '<option value="">Select Category</option>';
        if (!empty($catData)):
            foreach($catData as $row):
                $options .= '<option value="'.$row->ref_cat_id.'">'.$row->category_name.'</option>';
            endforeach;
        endif;

        $this->printJson(['options'=>$options]);
    }

    public function saveDieComponent(){
        $data = $this->input->post();
		$errorMessage = array();

        if (empty($data['item_id'])) {
            $errorMessage['item_id'] = "Product is required.";
        }

        if (empty($data['category_id'])) {
            $errorMessage['category_id'] = "Category is required.";
        }

        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->dieMaster->saveDieComponent($data));
		endif;
    }

    public function deleteComponent(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->dieMaster->deleteComponent($id));
        endif;
    }
}
?>