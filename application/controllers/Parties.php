<?php
class Parties extends MY_Controller{
    private $index = "party/index";
    private $form = "party/form";
    private $ledgerForm = "party/ledger_form";
    private $gstFrom = "party/gst_form";
    private $contactFrom = "party/contact_form";
    private $opbal_index = "party/opbal_index";
    private $excel_upload_form = "party/excel_upload_form";
	private $party_contact = "party/party_contact";
	private $party_setting_form = "party/party_setting_form";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "Party Master";
		$this->data['headData']->controller = "parties";        
    }

    public function list($type="customer"){
        $this->data['headData']->pageUrl = "parties/list/".$type;
        $this->data['type'] = $type;
        $this->data['party_category'] = $party_category = array_search(ucwords($type),$this->partyCategory);
		$this->data['headData']->pageTitle = $this->partyCategory[$party_category];
        $this->data['tableHeader'] = getMasterDtHeader($type);
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($party_category,$party_type = 1){
        $data=$this->input->post();
		$data['party_category'] = $party_category;
		$data['party_type'] = $party_type;
        $result = $this->party->getDTRows($data);
        $sendData = array();
        $i = ($data['start']+1);
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->table_status = $party_category;
            $row->party_category_name = $this->partyCategory[$row->party_category];
            $sendData[] = getPartyData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addParty(){
        $data = $this->input->post();
        $this->data['party_category'] = $data['party_category'];
        $this->data['party_type'] = (!empty($data['party_type']) ? $data['party_type'] : '1');
        $this->data['ledgerList'] = $this->party->getPartyList(['tax_type'=>"'TDS'"]);
        $this->data['tdsClassList'] = $this->party->getTDSClassList();

        if($data['party_category'] != 4):            
            $this->data['currencyData'] = $this->party->getCurrencyList();
            $this->data['countryData'] = $this->party->getCountries();
            $this->data['salesExecutives'] = $this->employee->getEmployeeList();
            $this->data['groupList'] = $this->group->getGroupList(['group_code'=>(($data['party_category'] == 1)?"'SD'":"'SC'")]);
            $this->data['priceStructureList'] = $this->itemPriceStructure->getPriceStructureList();
            $code = $this->party->getPartyCode($this->data['party_category']);
            if($this->data['party_category'] == 2):
                $this->data['party_code'] = 'S'.sprintf("%04d",$code);
            elseif($this->data['party_category'] == 3):
                $this->data['party_code'] = 'V'.sprintf("%04d",$code);
            endif;
            $this->load->view($this->form, $this->data);
        else:
            $this->data['groupList'] = $this->group->getGroupList(['not_group_code'=>"'SD','SC'"]);
            $this->data['hsnList'] = $this->hsnModel->getHSNList();
            $this->load->view($this->ledgerForm,$this->data);
        endif;
    }

	/* UPDATED BY : AVT DATE : 13-12-2024 */
	public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(isset($data['form_type'])):
            if(empty($data['group_id'])):
                $errorMessage['group_id'] = "Group Name is required.";
            endif;
        else:
            if (empty($data['party_name']))
                $errorMessage['party_name'] = "Company name is required.";

            if (empty($data['party_category']))
                $errorMessage['party_category'] = "Party Category is required.";
			
			if ($data['party_type'] == 1 && empty($data['party_code']))
                $errorMessage['party_code'] = "Party code is required.";

            if($data['party_category'] != 4):
        
                if (empty($data['gstin']) && in_array($data['registration_type'],[1,2]))
                    $errorMessage['gstin'] = 'Gstin is required.';

                if (empty($data['country_id']))
                    $errorMessage['country_id'] = 'Country is required.';

                if (empty($data['state_id']))
                    $errorMessage['state_id'] = 'State is required.';

                if (empty($data['city_name']))
                    $errorMessage['city_name'] = 'City is required.';

                if (empty($data['party_address']))
                    $errorMessage['party_address'] = "Address is required.";

                if (empty($data['party_pincode']))
                    $errorMessage['party_pincode'] = "Pincode is required.";
                    
            endif;
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			
            if(!empty($_FILES['assesment_file'])):
                if($_FILES['assesment_file']['name'] != null || !empty($_FILES['assesment_file']['name'])):
                    $this->load->library('upload');
                    $_FILES['userfile']['name']     = $_FILES['assesment_file']['name'];
                    $_FILES['userfile']['type']     = $_FILES['assesment_file']['type'];
                    $_FILES['userfile']['tmp_name'] = $_FILES['assesment_file']['tmp_name'];
                    $_FILES['userfile']['error']    = $_FILES['assesment_file']['error'];
                    $_FILES['userfile']['size']     = $_FILES['assesment_file']['size'];
                    
                    $imagePath = realpath(APPPATH . '../assets/uploads/party_assesment/');
                    $config = ['file_name' => time()."_assesment",'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload()):
                        $errorMessage['assesment_file'] = $this->upload->display_errors();
                    else:
                        $uploadData = $this->upload->data();
                        $data['assesment_file'] = $uploadData['file_name'];
                    endif;
                endif;
            endif;
		
            if(!isset($data['form_type'])):
                $data['party_name'] = ucwords($data['party_name']);
                $data['gstin'] = (!empty($data['gstin']))?strtoupper($data['gstin']):"";
                $data['gst_reg_date'] = (!empty($data['gst_reg_date']))?$data['gst_reg_date']:NULL;
                $data['price_structure_id'] = (!empty($data['price_structure_id']))?$data['price_structure_id']:0;
            endif;

            $this->printJson($this->party->save($data));
        endif;
    }
	
    public function edit(){
        $data = $this->input->post();
        $result = $this->party->getParty($data);
        $this->data['dataRow'] = $result;

        $this->data['ledgerList'] = $this->party->getPartyList(['tax_type'=>"'TDS'"]);
        $this->data['tdsClassList'] = $this->party->getTDSClassList();

        if($result->party_category != 4):
            $this->data['currencyData'] = $this->party->getCurrencyList();
            $this->data['countryData'] = $this->party->getCountries();
            $this->data['salesExecutives'] = $this->employee->getEmployeeList();
            $this->data['groupList'] = $this->group->getGroupList(['group_code'=>(($result->party_category == 1)?"'SD'":"'SC'")]);      
            $this->data['priceStructureList'] = $this->itemPriceStructure->getPriceStructureList();
            $this->load->view($this->form, $this->data);
        else:
            $this->data['groupList'] = $this->group->getGroupList(['not_group_code'=>"'SD','SC'"]);
            $this->data['hsnList'] = $this->hsnModel->getHSNList();
            $this->load->view($this->ledgerForm,$this->data);
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->party->delete($id));
        endif;
    }

    public function gstDetail(){
        $data = $this->input->post();
        $this->data['party_id'] = $data['id'];
        $this->load->view($this->gstFrom,$this->data);
    }

    public function getPartyGSTDetailHtml(){
        $data = $this->input->post();
        $result = $this->party->getPartyGSTDetail($data);

        $tbodyData = "";$i = 1;        
        if (!empty($result)) :
            foreach ($result as $row) :
                $deleteParam = "{'postData':{'id' : ".$row->id.",'party_id':".$row->party_id."},'message' : 'GST Detail','fndelete':'deleteGstDetail','res_function':'resTrashPartyGstDetail'}";
                $tbodyData .= '<tr>
                    <td>' .  $i++ . '</td>
                    <td>' . $row->gstin . '</td>
                    <td>' . $row->party_address . '</td>
                    <td>' . $row->party_pincode . '</td>
                    <td>' . $row->delivery_address . '</td>
                    <td>' . $row->delivery_pincode . '</td>
                    <td class="text-center">
                        <button type="button" onclick="trash('.$deleteParam.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                    </td>
                </tr> ';
            endforeach;
        else :
            $tbodyData .= '<tr><td colspan="7" style="text-align:center;">No data available in table</td></tr>';
        endif;
        $this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
    }

    public function saveGstDetail(){
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['gstin']))
            $errorMessage['gstin'] = "GST is required.";
		if (empty($data['party_address']))
            $errorMessage['party_address'] = "Party Address is required.";
        if (empty($data['party_pincode']))
            $errorMessage['party_pincode'] = "Party Pincode is required.";
        if (empty($data['delivery_address']))
            $errorMessage['delivery_address'] = "Delivery Address is required.";
        if (empty($data['delivery_pincode']))
            $errorMessage['delivery_pincode'] = "Delivery Pincode is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $this->printJson($this->party->saveGstDetail($data));
        endif;
    }

    public function deleteGstDetail(){
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->party->deleteGstDetail($id));
        endif;
    }

    public function contactDetail(){
        $data = $this->input->post();
        $this->data['party_id'] = $data['id'];
        $this->load->view($this->contactFrom,$this->data);
    }

    public function getPartyContactDetailHtml(){
        $data = $this->input->post();
        $result = $this->party->getPartyContactDetail($data);

        $tbodyData = "";$i = 1;        
        if (!empty($result)) :
            foreach ($result as $row) :
                $deleteParam = "{'postData':{'id' : ".$row->id.",'party_id':".$row->party_id."},'message' : 'Contact Detail','fndelete':'deleteContactDetail','res_function':'resTrashPartyContactDetail'}";
                $tbodyData .= '<tr>
                    <td>' .  $i++ . '</td>
                    <td>' . $row->contact_person . '</td>
                    <td>' . $row->mobile_no . '</td>
                    <td>' . $row->contact_email . '</td>
                    <td class="text-center">
                        <button type="button" onclick="trash('.$deleteParam.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                    </td>
                </tr> ';
            endforeach;
        else :
            $tbodyData .= '<tr><td colspan="5" style="text-align:center;">No data available in table</td></tr>';
        endif;
        $this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
    }

    public function saveContactDetail(){
        $data = $this->input->post();
		$errorMessage = array();

		if(empty($data['person']))
			$errorMessage['person'] = "Contact Person is required.";
        if(empty($data['mobile']))
			$errorMessage['mobile'] = "Contact Mobile is required.";
        if(empty($data['email']))
			$errorMessage['email'] = "Contact Email is required.";
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $this->printJson($this->party->saveContactDetail($data));
		endif;
    }

    public function deleteContactDetail(){
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->party->deleteContactDetail($id));
        endif;
    }

    public function getPartyList(){
        $data = $this->input->post();
        $partyList = $this->party->getPartyList($data);
        $this->printJson(['status'=>1,'data'=>['partyList'=>$partyList]]);
    }

    /* Party Opening Balance Start */
    public function opBalIndex(){
        $this->data['grpData'] = $this->group->getGroupList();
        $this->load->view($this->opbal_index,$this->data);
    }

    public function getGroupWiseLedger(){
        $data = $this->input->post();
        $ledgerData = $this->party->getPartyOpBalance(['group_id'=>$data['group_id']]);

        $tbody="";$i=1;
        if(!empty($ledgerData)):
            foreach($ledgerData as $row):         
                $row->opb = $row->op_balance;       
                $crSelected = (!empty($row->op_balance_type) && $row->op_balance_type == "1")?"selected":"";
                $drSelected = (!empty($row->op_balance_type) && $row->op_balance_type == "-1")?"selected":"";

                $row->opBalanceInput = '<div class="input-group">
                    <select name="balance_type[]" id="balance_type_'.$row->id.'" class="form-control" style="width: 20%;">
                        <option value="1" '.$crSelected.'>CR</option>
                        <option value="-1" '.$drSelected.'>DR</option>
                    </select>
                    <input type="text" id="op_balance_'.$row->id.'" name="op_balance[]" class="form-control floatOnly" value="'.floatVal(abs($row->opb)).'" style="width: 40%;" />
                </div>
                <input type = "hidden"  id="party_id_'.$row->id.'" name="party_id[]" value="'.$row->id.'" >' ;                

                $tbody .= '<tr>
                    <td style="width: 5%;">'.$i++.'</td>
                    <td style="width: 25%;">'.$row->account_name.'</td>
                    <td class="text-right" style="width: 10%;" id="cur_op_'.$row->id.'">'.$row->opb.'</td>
                    <td style="width: 20%;">' .$row->opBalanceInput. '</td>
                    <td style="width: 5%;">
                        <button type="button" class="btn btn-success saveOp" datatip="Save" flow="left" data-id="'.$row->id.'"><i class="fa fa-check"></i></button>
                    </td>
                </tr>';
            endforeach;
        endif;
        $this->printJson(['status'=>1, 'count'=>$i, 'tbody'=>$tbody]);
    }

    public function saveOpeningBalance(){
        $data = $this->input->post();
        $this->printJson($this->party->saveOpeningBalance($data));
    }

    /* Party Opening Balance End */
    
    /* Party Excel Upload */ 
    public function addPartyExcel(){
        $data = $this->input->post();
        $this->data['party_category'] = $data['party_category'];
        $this->load->view($this->excel_upload_form,$this->data);
    }

    public function savePartyExcel(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['itemData']))
            $errorMessage['itemData'] = "Enter party detail";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            foreach($data['itemData'] as $row):
                $executiveData = $this->employee->getEmployee(['emp_name'=>$row['sales_executive']]); 
                $regType = array_search($row['registration_type'],$this->gstRegistrationTypes);

                $row['sales_executive'] = !empty($executiveData->id) ? $executiveData->id : 0;
                $row['registration_type'] = $regType;
                $code = $this->party->getPartyCode($row['party_category']);
                if($row['party_category'] == 2):
                    $row['party_code'] = 'S'.sprintf("%04d",$code);
                elseif($row['party_category'] == 3):
                    $row['party_code'] = 'V'.sprintf("%04d",$code);
                endif;
                $result = $this->party->save($row);
            endforeach;
            
            $this->printJson(['status'=>1,'message'=>'Party Saved Successfully.']);
        endif;
    }

	public function checkPartyDuplicate(){
        $data = $this->input->post();
        $customWhere = "party_name = '".$data['party_name']."' ";
        $customWhere .= (!empty($data['party_mobile']) ? " AND party_mobile = '".$data['party_mobile']."'" : '');
        $partyData = $this->party->getParty(['customWhere'=>$customWhere, 'is_delete'=>0]);
        $this->printJson(['status'=>1,'party_id'=>(!empty($partyData->id)?$partyData->id:"")]);
    }

	/* ACCOUNT SETTTING CREATED BY : AVT DATE:13-12-2024 */
	public function editPartySettings(){
        $data = $this->input->post();
        $result = $this->party->getParty($data);
        $this->data['dataRow'] = $result;
        $this->data['groupList'] = $this->group->getGroupList(['group_code'=>((in_array($result->party_category,[1]))?"'SD'":"'SC'")]);
        $this->data['tdsClassList'] = $this->party->getTDSClassList();
		$this->data['ledgerList'] = $this->party->getPartyList(['tax_type'=>"'TDS'"]);
        $this->load->view($this->party_setting_form,$this->data);
    }

	/* CREATED BY : AVT DATE:13-12-2024 */
    public function updatePartyContact(){
        $data = $this->input->post();
        $this->data['party_id'] = $data['party_id'];
        $this->load->view($this->party_contact,$this->data);
    }

	/* CREATED BY : AVT DATE:13-12-2024 */
    public function savePartyContact(){
        $data = $this->input->post();
        $errorMessage = array();
        
		if(empty($data['contact_person'])){
            $errorMessage['contact_person'] = "Contact Person is required.";
        }
		if(empty($data['designation'])){
            $errorMessage['designation'] = "Designation is required.";
        }
		if(empty($data['party_mobile'])){
            $errorMessage['party_mobile'] = "Mobile No is required.";
        }   
        if(empty($data['party_email'])){
            $errorMessage['party_email'] = "Email is required.";
        } 
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->party->savePartyContact($data));
        endif;
    }

	/* CREATED BY : AVT DATE:13-12-2024 */
    public function partyContactHtml(){
        $data = $this->input->post();
        $partyData = $this->party->getPartyContact(['party_id'=>$data['party_id']]);
        $tbodyData="";$i=1; 
        if(!empty($partyData)):
            $i=1;$deleteButton="";
            foreach($partyData as $row):

                if(empty($row->is_default)){
                    $deleteParam = "{'postData':{'id' : ".$row->id."},'res_function':'partyContactHtml','fndelete':'deletePartyContact'}";    
                    $deleteButton = '<button type="button" class="btn btn-outline-danger btn-sm waves-effect waves-light permission-remove" onclick="trash('.$deleteParam.');"><i class="mdi mdi-trash-can-outline"></i></button>';
                }
              
                $tbodyData.= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$row->contact_person.'</td>
                            <td>'.$row->designation.'</td>
                            <td>'.$row->party_mobile.'</td>
                            <td>'.$row->party_email.'</td>
                            <td class="text-center">'.$deleteButton.'</td>
                        </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
        endif;

        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

	/* CREATED BY : AVT DATE:13-12-2024 */
    public function deletePartyContact(){
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->party->deletePartyContact($data['id']));
        endif;
    }

	/* CREATED BY : AVT DATE:13-12-2024 */
    public function getCustomerList(){
		$data = $this->input->post();
		$result = $this->party->getPartyList($data);
		$this->printJson($result);
	}

	/* Create Vendor Store */ 
    public function createVendorStore(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->party->createVendorStore($data));
        endif;
    }

}
?>