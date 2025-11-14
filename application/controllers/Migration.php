<?php
class Migration extends MY_Controller{
    public function __construct(){
        parent::__construct();
    }

    public function addColumnInTable(){
        $result = $this->db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'tradeez' AND TABLE_NAME NOT IN ( SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'cm_id' AND TABLE_SCHEMA = 'tradeez' )")->result();

        foreach($result as $row):
            $this->db->query("ALTER TABLE ".$row->TABLE_NAME." ADD `cm_id` INT NOT NULL DEFAULT '0' AFTER `is_delete`;");
        endforeach;

        echo "success";exit;
    }

    public function deleteColumnInTable(){
        try{
            $this->db->trans_begin();
            $result = $this->db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'u399487905_jsk'   AND TABLE_NAME  IN ( SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'cm_id' AND TABLE_SCHEMA = 'u399487905_jsk' )")->result();
            foreach($result as $row):
                    $this->db->query("ALTER TABLE ".$row->TABLE_NAME." DROP COLUMN cm_id;");
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Success.";
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function defualtLedger(){
        $accounts = [
            ['name' => 'Sales Account', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESACC'],
            
            ['name' => 'Sales Account GST', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESGSTACC'],

            ['name' => 'Sales Account IGST', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESIGSTACC'],

            ['name' => 'Sales Account Tax Free', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESTFACC'],

            ['name' => 'Exempted Sales (Nill Rated)', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESEXEMPTEDTFACC'],

            ['name' => 'Sales Account GST JOBWORK', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESJOBGSTACC'],

            ['name' => 'Sales Account IGST JOBWORK', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESJOBIGSTACC'],

            ['name' => 'Export With Payment', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'EXPORTGSTACC'],

            ['name' => 'Export Without Payment', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'EXPORTTFACC'],

            ['name' => 'SEZ Supplies With Payment', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SEZSGSTACC'],

            ['name' => 'SEZ Supplies Without Payment', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SEZSTFACC'],

            ['name' => 'Deemed Export', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'DEEMEDEXP'],
            
            ['name' => 'CGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'CGSTOPACC'],
            
            ['name' => 'SGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'SGSTOPACC'],
            
            ['name' => 'IGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'IGSTOPACC'],
            
            ['name' => 'UTGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'UTGSTOPACC'],
            
            ['name' => 'CESS (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TCS ON SALES', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'Purchase Account', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURACC'],
            
            ['name' => 'Purchase Account GST', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURGSTACC'],

            ['name' => 'Purchase Account IGST', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURIGSTACC'],

            ['name' => 'Purchase Account URD GST', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURURDGSTACC'],

            ['name' => 'Purchase Account URD IGST', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURURDIGSTACC'],

            ['name' => 'Purchase Account Tax Free', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURTFACC'],

            ['name' => 'Exempted Purchase (Nill Rated)', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PUREXEMPTEDTFACC'],

            ['name' => 'Purchase Account GST JOBWORK', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURJOBGSTACC'],

            ['name' => 'Purchase Account IGST JOBWORK', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURJOBIGSTACC'],

            ['name' => 'Import', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'IMPORTACC'],

            ['name' => 'Import of Services', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'IMPORTSACC'],

            ['name' => 'Received from SEZ', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'SEZRACC'],
            
            ['name' => 'CGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'CGSTIPACC'],
            
            ['name' => 'SGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'SGSTIPACC'],
            
            ['name' => 'IGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'IGSTIPACC'],
            
            ['name' => 'UTGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'UTGSTIPACC'],
            
            ['name' => 'CESS (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TCS ON PURCHASE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TDS PAYABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TDS RECEIVABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'GST PAYABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'GST RECEIVABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'ROUNDED OFF', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => 'ROFFACC'],
            
            ['name' => 'CASH ACCOUNT', 'group_name' => 'Cash-In-Hand', 'group_code' => 'CS', 'system_code' => 'CASHACC'],
            
            ['name' => 'ELECTRICITY EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'OFFICE RENT EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'GODOWN RENT EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'TELEPHONE AND INTERNET CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'PETROL EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SALES INCENTIVE', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'INTEREST PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'INTEREST RECEIVED', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'SAVING BANK INTEREST', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'DISCOUNT RECEIVED', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'DISCOUNT PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SUSPENSE A/C', 'group_name' => 'Suspense A/C', 'group_code' => 'AS', 'system_code' => ''],
            
            ['name' => 'PROFESSIONAL FEES PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'AUDIT FEE', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'ACCOUNTING CHARGES PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'LEGAL FEE', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SALARY', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'WAGES', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'FREIGHT CHARGES', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'PACKING AND FORWARDING CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'REMUNERATION TO PARTNERS', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'TRANSPORTATION CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'DEPRICIATION', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'PLANT AND MACHINERY', 'group_name' => 'Fixed Assets', 'group_code' => 'FA', 'system_code' => ''],
            
            ['name' => 'FURNITURE AND FIXTURES', 'group_name' => 'Fixed Assets', 'group_code' => 'FA', 'system_code' => ''],
            
            ['name' => 'FIXED DEPOSITS', 'group_name' => 'Deposits (Assets)', 'group_code' => 'DA', 'system_code' => ''],
            
            ['name' => 'RENT DEPOSITS', 'group_name' => 'Deposits (Assets)', 'group_code' => 'DA', 'system_code' => '']	            
        ];
        try{
            $this->db->trans_begin();
            $accounts = (object) $accounts;
            foreach($accounts as $row):
                $row = (object) $row;

                $groupData = $this->db->where('group_code',$row->group_code)->get('group_master')->row();

                $ledgerData = [
                    'party_category' => 4,
                    'group_name' => $groupData->name,
                    'group_code' => $groupData->group_code,
                    'group_id' => $groupData->id,
                    'party_name' => $row->name,                    
                    'system_code' => $row->system_code
                ];

                $this->db->where('party_name',$row->name);
                $this->db->where('is_delete',0);
                $this->db->where('party_category',4);
                $checkLedger = $this->db->get('party_master');

                if($checkLedger->num_rows() > 0):
                    $id = $checkLedger->row()->id;
                    $this->db->where('id',$id);
                    $this->db->update('party_master',$ledgerData);
                else:
                    $this->db->insert('party_master',$ledgerData);
                endif;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Defualt Ledger Migration Success.";
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function updateLedgerClosingBalance(){
        try{
            $this->db->trans_begin();

            $partyData = $this->db->where('is_delete',0)->get("party_master")->result();
            foreach($partyData as $row):
                //Set oprning balance as closing balance
                $this->db->where('id',$row->id);
                $this->db->update('party_master',['cl_balance'=>'opening_balance']);

                //get ledger trans amount total
                $this->db->select("SUM(amount * p_or_m) as ledger_amount");
                $this->db->where('vou_acc_id',$row->id);
                $this->db->where('is_delete',0);
                $ledgerTrans = $this->db->get('trans_ledger')->row();
                $ledgerAmount = (!empty($ledgerTrans->ledger_amount))?$ledgerTrans->ledger_amount:0;

                //update colsing balance
                $this->db->set("cl_balance","`cl_balance` + ".$ledgerAmount,FALSE);
                $this->db->where('id',$row->id);
                $this->db->update('party_master');
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Closing Balance Migration Success.";
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    /* Miracel Data Import Start */
    public function createParty(){
        try{
            $this->db->trans_begin();

            $this->db->select("id,vou_type,party_name,gst_no,city_name");
            $this->db->where('party_name !=',"");
            $this->db->where('party_id',0);
            $result = $this->db->get('miracel_data')->result();
            
            foreach($result as $row):
                $this->db->reset_query();
                $this->db->select('id,city_id,state_id,country_id');
                $this->db->where('REPLACE(LOWER(party_name)," ","") = ',str_replace(" ","",strtolower($row->party_name)));
                $partyData = $this->db->get('party_master')->row();
                if(!empty($partyData)):
                    $this->db->reset_query();
                    $this->db->where('id',$row->id);
                    $this->db->update('miracel_data',['party_id'=>$partyData->id,'city_id'=>$partyData->city_id,'state_id'=>$partyData->state_id,'country_id'=>$partyData->country_id]);
                else:
                    $partyCategory = ($row->vou_type == "SALES")?1:2;
                    $code = $this->party->getPartyCode($partyCategory);
                    $prefix = "AE";
                    if($partyCategory == 1):
                        $prefix = "C";
                    elseif($partyCategory == 2):
                        $prefix = "S";
                    elseif($partyCategory == 3):
                        $prefix = "V";
                    endif;

                    $party_code = $prefix.sprintf("%03d",$code);

                    $this->db->reset_query();
                    $this->db->select('id as city_id,state_id,country_id');
                    $this->db->where('REPLACE(LOWER(cities.name)," ","") = ',str_replace(" ","",strtolower($row->city_name)));
                    $cityData = $this->db->get('cities')->row();

                    $postData = [
                        'party_category' => $partyCategory,
                        'party_type' => 1,
                        'party_code' => $party_code,
                        'party_name' => $row->party_name,
                        'registration_type' => (!empty($row->gst_no))?1:4,
                        'gstin' => $row->gst_no,
                        'currency' => "INR",
                        'country_id' => (!empty($cityData))?$cityData->country_id:0,
                        'state_id' => (!empty($cityData))?$cityData->state_id:0,
                        'city_id' => (!empty($cityData))?$cityData->city_id:0,
                        'supplied_types' => 1
                    ];

                    $groupCode = ($partyCategory == 1) ? "SD" : "SC";
                    $groupData = $this->party->getGroupOnGroupCode($groupCode, true);
                    $postData['group_id'] = $groupData->id;
                    $postData['group_name'] = $groupData->name;
                    $postData['group_code'] = $groupData->group_code;

                    //print_r($postData);print_r("<hr>");
                    $this->db->reset_query();
                    $this->db->insert('party_master',$postData);

                    $this->db->reset_query();
                    $this->db->where('id',$row->id);
                    $this->db->update('miracel_data',['party_id'=>$this->db->insert_id(),'city_id'=>$postData['city_id'],'state_id'=>$postData['state_id'],'country_id'=>$postData['country_id']]);
                endif;
            endforeach;
            

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Party Migration Success.";
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function updatePartyGstinDetails(){
        try{
            $this->db->trans_begin();

            $this->db->select("id,gstin,party_address,party_pincode,delivery_address,delivery_pincode");
            $this->db->where_in('party_category',[1,2,3]);
            $this->db->where('is_delete',0);
            $result = $this->db->get('party_master')->result();

            foreach($result as $row):
                $this->db->where('main_ref_id',$row->id);
                $this->db->where('table_name','party_master');
                $this->db->where('description','PARTY GST DETAIL');
                $this->db->where('t_col_1','');
                $this->db->update('trans_details',['is_delete'=>1]);

                $this->db->select('id');
                $this->db->where('main_ref_id',$row->id);
                $this->db->where('table_name','party_master');
                $this->db->where('description','PARTY GST DETAIL');
                $this->db->where('t_col_1',$row->gstin);
                $gstDetails = $this->db->get('trans_details')->row();

                $postData = [
                    'main_ref_id' =>  $row->id,
                    'table_name' => 'party_master',
                    'description' => "PARTY GST DETAIL",
                    't_col_1' => $row->gstin,
                    't_col_2' => $row->party_address,
                    't_col_3' => $row->party_pincode,
                    't_col_4' => $row->delivery_address,
                    't_col_5' => $row->delivery_pincode
                ];

                if(!empty($gstDetails)):
                    $this->db->where('id',$gstDetails->id);
                    $this->db->update('trans_details',$postData);
                else:
                    $this->db->insert('trans_details',$postData);
                endif;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Party GST Details Migration Success.";
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function createItem(){
        try{
            $this->db->trans_begin();

            $this->db->select("id,vou_type,item_name,gst_per,price");
            $this->db->where('item_name !=',"");
            $this->db->where('item_id',0);
            $result = $this->db->get('miracel_data')->result();
            
            foreach($result as $row):
                $this->db->reset_query();
                $this->db->select('id,item_name,gst_per');
                $this->db->where('REPLACE(LOWER(item_name)," ","") = ',str_replace(" ","",strtolower($row->item_name)));
                $itemData = $this->db->get('item_master')->row();

                if(!empty($itemData)):
                    $this->db->reset_query();
                    $this->db->where('id',$row->id);
                    $this->db->update('miracel_data',['item_id'=>$itemData->id]);

                    $this->db->where('id',$itemData->id);
                    $this->db->update('item_master',['price' => $row->price,'gst_per' => $row->gst_per]);
                else:
                    $item_type = ($row->vou_type == "SALES")?1:3;

                    $postData = [
                        'item_type' => $item_type,
                        'item_code' => "",
                        'item_name' => $row->item_name,
                        'category_id' => ($row->vou_type == "SALES")?12:37,
                        'unit_id' => 27,
                        'price' => $row->price,
                        'gst_per' => $row->gst_per,
                        'order_category' => "IMPORT"
                    ];

                    //print_r($postData);print_r("<hr>");
                    $this->db->reset_query();
                    $this->db->insert('item_master',$postData);

                    $this->db->reset_query();
                    $this->db->where('id',$row->id);
                    $this->db->update('miracel_data',['item_id'=>$this->db->insert_id()]);
                endif;
            endforeach;
            

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Item Migration Success.";
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function createSalesInvoice(){
        try{
            $this->db->trans_begin();

            $this->db->select("*");
            $this->db->where('vou_type',"SALES");
            $this->db->where('status',0);
            $this->db->order_by('id','ASC');
            $result = $this->db->get('miracel_data')->result();

            $i=0;$j=0;$postData = array();
            foreach($result as $row):
                if(!empty($row->bill_no)):
                    ++$i;

                    $trans_no = explode("/",$row->bill_no)[1];
                    if(empty($row->gst_no)):
                        $gst_type = 1;
                        $tax_class = "SALESGSTACC";
                        $sp_acc_id = 2;
                        $state_code = 24;
                    else:
                        if(substr($row->gst_no,0,2) == 24):
                            $gst_type = 1;
                            $tax_class = "SALESGSTACC";
                            $sp_acc_id = 2;
                            $state_code = substr($row->gst_no,0,2);
                        else:
                            $gst_type = 2;
                            $tax_class = "SALESIGSTACC";
                            $sp_acc_id = 3;
                            $state_code = substr($row->gst_no,0,2);
                        endif;
                    endif;

                    $postData[$i]['entry_type'] = 32;
                    $postData[$i]['trans_prefix'] = "GT/2023/";
                    $postData[$i]['trans_no'] = $trans_no;
                    $postData[$i]['doc_no'] = "";
                    $postData[$i]['doc_date'] = null;
                    $postData[$i]['party_name'] = $row->party_name;
                    $postData[$i]['gst_type'] = $gst_type;
                    $postData[$i]['party_state_code'] = $state_code;
                    $postData[$i]['tax_class'] = $tax_class;
                    $postData[$i]['trans_number'] = "GT/2023/".$trans_no;
                    $postData[$i]['trans_date'] = date("Y-m-d",strtotime(str_replace("/","-",$row->bill_date)));
                    $postData[$i]['party_id'] = $row->party_id;
                    $postData[$i]['gstin'] = $row->gst_no;
                    $postData[$i]['memo_type'] = strtoupper($row->memo_type);
                    $postData[$i]['sp_acc_id'] = $sp_acc_id;
                    $postData[$i]['apply_round'] = 1;
                    $postData[$i]['masterDetails'] = [
                        't_col_1' => "",
                        't_col_2' => "",
                        't_col_3' => "",
                        'i_col_1' => 100
                    ];
                    $postData[$i]['remark'] = "IMPORT ".$row->bill_no;
                    $postData[$i]['vou_name_l'] = "Sales Invoice";
                    $postData[$i]['vou_name_s'] = "Sale";
                endif;

                $this->db->select('item_master.*,unit_master.unit_name');
                $this->db->join('unit_master','unit_master.id = item_master.unit_id','left');
                $this->db->where('item_master.id',$row->item_id);
                $itemData = $this->db->get('item_master')->row();

                $postData[$i]['itemData'][$j]['md_id'] = $row->id;
                $postData[$i]['itemData'][$j]['item_id'] = $row->item_id;
                $postData[$i]['itemData'][$j]['item_name'] = $row->item_name;
                $postData[$i]['itemData'][$j]['item_type'] = $itemData->item_type;
                $postData[$i]['itemData'][$j]['stock_eff'] = 0;
                $postData[$i]['itemData'][$j]['p_or_m'] = -1;
                $postData[$i]['itemData'][$j]['hsn_code'] = "";
                $postData[$i]['itemData'][$j]['qty'] = $row->qty;
                $postData[$i]['itemData'][$j]['packing_qty'] = 1;
                $postData[$i]['itemData'][$j]['unit_id'] = $itemData->unit_id;
                $postData[$i]['itemData'][$j]['unit_name'] = $itemData->unit_name;
                $postData[$i]['itemData'][$j]['price'] = $row->price;
                $postData[$i]['itemData'][$j]['org_price'] = $row->price;
                $postData[$i]['itemData'][$j]['disc_per'] = 0;
                $postData[$i]['itemData'][$j]['disc_amount'] = 0;
                $postData[$i]['itemData'][$j]['cgst_per'] = round(($row->gst_per/2),2);
                $postData[$i]['itemData'][$j]['cgst_amount'] = round(($row->gst_amount/2),2);
                $postData[$i]['itemData'][$j]['sgst_per'] = round(($row->gst_per/2),2);
                $postData[$i]['itemData'][$j]['sgst_amount'] = round(($row->gst_amount/2),2);
                $postData[$i]['itemData'][$j]['gst_per'] = $row->gst_per;
                $postData[$i]['itemData'][$j]['gst_amount'] = $row->gst_amount;
                $postData[$i]['itemData'][$j]['igst_per'] = $row->gst_per;
                $postData[$i]['itemData'][$j]['igst_amount'] = $row->gst_amount;
                $postData[$i]['itemData'][$j]['amount'] = $row->amount;
                $postData[$i]['itemData'][$j]['taxable_amount'] = $row->amount;
                $postData[$i]['itemData'][$j]['net_amount'] = $row->amount + $row->gst_amount;
                $postData[$i]['itemData'][$j]['item_remark'] = "IMPORT";

                $j++;
            endforeach;

            /* foreach($postData as $row):
                //print_r($row);print_r("<hr>");
                $row['id'] = "";
                $itemData = $row['itemData']; 
                $masterDetails = $row['masterDetails']; unset($row['itemData'],$row['masterDetails']);

                $row['opp_acc_id'] = $row['party_id'];
                $row['ledger_eff'] = 1;
                $row['gstin'] = (!empty($row['gstin']))?$row['gstin']:"URP";
                
                $accType = getSystemCode($row['vou_name_s'],false);
                if(!empty($accType)):
                    $spAcc = $this->party->getParty(['system_code'=>$accType]);
                    $row['vou_acc_id'] = (!empty($spAcc))?$spAcc->id:0;
                else:
                    $row['vou_acc_id'] = 0;
                endif;

                $row['total_amount'] = array_sum(array_column($itemData,'amount'));
                $row['taxable_amount'] = array_sum(array_column($itemData,'taxable_amount'));
                $row['cgst_acc_id'] = 13;
                $row['cgst_per'] = 0;
                $row['cgst_amount'] = ($row['gst_type'] == 1)?array_sum(array_column($itemData,'cgst_amount')):0;
                $row['sgst_acc_id'] = 14;
                $row['sgst_per'] = 0;
                $row['sgst_amount'] = ($row['gst_type'] == 1)?array_sum(array_column($itemData,'sgst_amount')):0;
                $row['igst_acc_id'] = 15;
                $row['igst_per'] = 0;
                $row['igst_amount'] = ($row['gst_type'] == 2)?array_sum(array_column($itemData,'igst_amount')):0;
                $row['gst_amount'] = $row['igst_amount'] + $row['cgst_amount'] + $row['sgst_amount'];
                $row['net_amount'] = array_sum(array_column($itemData,'net_amount'));
                $row['round_off_acc_id'] = 41;
                $row['round_off_amount'] = round((round($row['net_amount'],0,PHP_ROUND_HALF_UP) - $row['net_amount']),2);
                $row['net_amount'] = $row['net_amount'] + $row['round_off_amount'];

                $result = $this->masterModel->store("trans_main",$row,'Sales Invoice');

                if(!empty($masterDetails)):
                    $masterDetails['id'] = "";
                    $masterDetails['main_ref_id'] = $result['id'];
                    $masterDetails['table_name'] = "trans_main";
                    $masterDetails['description'] = "SI MASTER DETAILS";
                    $this->masterModel->store("trans_details",$masterDetails);
                endif;

                
                foreach($itemData as $item):
                    $md_id = $item['md_id']; unset($item['md_id']);
                    $item['id'] = "";
                    $item['entry_type'] = $row['entry_type'];
                    $item['trans_main_id'] = $result['id'];
                    $item['is_delete'] = 0;

                    $itemTrans = $this->masterModel->store("trans_child",$item);

                    $setData = array();
                    $setData['tableName'] = "miracel_data";
                    $setData['where']['id'] = $md_id;
                    $setData['update']['status'] = $itemTrans['id'];
                    $this->masterModel->setValue($setData);
                endforeach;

                $row['id'] = $result['id'];
                $this->transMainModel->ledgerEffects($row,array());
            endforeach; */
            //exit;

            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                $this->db->trans_rollback();
                echo "Sales Invoice Migration Success.";
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }

    }

    public function createPurchaseInvoice(){
        try{
            $this->db->trans_begin();

            $this->db->select("*");
            $this->db->where('vou_type',"PURCHASE");
            $this->db->where('status',0);
            $this->db->order_by('id','ASC');
            $result = $this->db->get('miracel_data')->result();

            $i=0;$j=0;$postData = array();
            foreach($result as $row):
                if(!empty($row->bill_no)):
                    ++$i;

                    if(empty($row->gst_no)):
                        $gst_type = 1;
                        $tax_class = "PURGSTACC";
                        $sp_acc_id = 20;
                        $state_code = 24;
                    else:
                        if(substr($row->gst_no,0,2) == 24):
                            $gst_type = 1;
                            $tax_class = "PURGSTACC";
                            $sp_acc_id = 20;
                            $state_code = substr($row->gst_no,0,2);
                        else:
                            $gst_type = 2;
                            $tax_class = "PURIGSTACC";
                            $sp_acc_id = 21;
                            $state_code = substr($row->gst_no,0,2);
                        endif;
                    endif;

                    $postData[$i]['entry_type'] = 33;
                    $postData[$i]['trans_prefix'] = "PUR/2023/";
                    $postData[$i]['trans_no'] = $i;
                    $postData[$i]['doc_no'] = "PUR/2023/".$i;
                    $postData[$i]['doc_date'] = date("Y-m-d");
                    $postData[$i]['party_name'] = $row->party_name;
                    $postData[$i]['gst_type'] = $gst_type;
                    $postData[$i]['party_state_code'] = $state_code;
                    $postData[$i]['tax_class'] = $tax_class;
                    $postData[$i]['trans_number'] = $row->bill_no;
                    $postData[$i]['trans_date'] = date("Y-m-d",strtotime(str_replace("/","-",$row->bill_date)));
                    $postData[$i]['party_id'] = $row->party_id;
                    $postData[$i]['gstin'] = $row->gst_no;
                    $postData[$i]['memo_type'] = strtoupper($row->memo_type);
                    $postData[$i]['sp_acc_id'] = $sp_acc_id;
                    $postData[$i]['itc'] = "Inputs";
                    $postData[$i]['apply_round'] = 1;
                    $postData[$i]['remark'] = "IMPORT ".$row->bill_no;
                    $postData[$i]['vou_name_l'] = "Purchase Invoice";
                    $postData[$i]['vou_name_s'] = "Purc";
                endif;

                $this->db->select('item_master.*,unit_master.unit_name');
                $this->db->join('unit_master','unit_master.id = item_master.unit_id','left');
                $this->db->where('item_master.id',$row->item_id);
                $itemData = $this->db->get('item_master')->row();

                $postData[$i]['itemData'][$j]['md_id'] = $row->id;
                $postData[$i]['itemData'][$j]['item_id'] = $row->item_id;
                $postData[$i]['itemData'][$j]['item_name'] = $row->item_name;
                $postData[$i]['itemData'][$j]['item_type'] = $itemData->item_type;
                $postData[$i]['itemData'][$j]['stock_eff'] = 0;
                $postData[$i]['itemData'][$j]['p_or_m'] = 1;
                $postData[$i]['itemData'][$j]['hsn_code'] = "";
                $postData[$i]['itemData'][$j]['qty'] = $row->qty;
                $postData[$i]['itemData'][$j]['unit_id'] = $itemData->unit_id;
                $postData[$i]['itemData'][$j]['unit_name'] = $itemData->unit_name;
                $postData[$i]['itemData'][$j]['price'] = $row->price;
                $postData[$i]['itemData'][$j]['disc_per'] = 0;
                $postData[$i]['itemData'][$j]['disc_amount'] = 0;
                $postData[$i]['itemData'][$j]['cgst_per'] = round(($row->gst_per/2),2);
                $postData[$i]['itemData'][$j]['cgst_amount'] = round(($row->gst_amount/2),2);
                $postData[$i]['itemData'][$j]['sgst_per'] = round(($row->gst_per/2),2);
                $postData[$i]['itemData'][$j]['sgst_amount'] = round(($row->gst_amount/2),2);
                $postData[$i]['itemData'][$j]['gst_per'] = $row->gst_per;
                $postData[$i]['itemData'][$j]['gst_amount'] = $row->gst_amount;
                $postData[$i]['itemData'][$j]['igst_per'] = $row->gst_per;
                $postData[$i]['itemData'][$j]['igst_amount'] = $row->gst_amount;
                $postData[$i]['itemData'][$j]['amount'] = $row->amount;
                $postData[$i]['itemData'][$j]['taxable_amount'] = $row->amount;
                $postData[$i]['itemData'][$j]['net_amount'] = $row->amount + $row->gst_amount;
                $postData[$i]['itemData'][$j]['item_remark'] = "IMPORT";

                $j++;
            endforeach;

            /* foreach($postData as $row):
                //print_r($row);print_r("<hr>");
                $row['id'] = "";
                $itemData = $row['itemData']; 
                $masterDetails = (isset($row['masterDetails']))?$row['masterDetails']:array(); 
                unset($row['itemData'],$row['masterDetails']);

                $row['opp_acc_id'] = $row['party_id'];
                $row['ledger_eff'] = 1;
                $row['gstin'] = (!empty($row['gstin']))?$row['gstin']:"URP";
                
                $accType = getSystemCode($row['vou_name_s'],false);
                if(!empty($accType)):
                    $spAcc = $this->party->getParty(['system_code'=>$accType]);
                    $row['vou_acc_id'] = (!empty($spAcc))?$spAcc->id:0;
                else:
                    $row['vou_acc_id'] = 0;
                endif;

                $row['total_amount'] = array_sum(array_column($itemData,'amount'));
                $row['taxable_amount'] = array_sum(array_column($itemData,'taxable_amount'));
                $row['cgst_acc_id'] = 13;
                $row['cgst_per'] = 0;
                $row['cgst_amount'] = ($row['gst_type'] == 1)?array_sum(array_column($itemData,'cgst_amount')):0;
                $row['sgst_acc_id'] = 14;
                $row['sgst_per'] = 0;
                $row['sgst_amount'] = ($row['gst_type'] == 1)?array_sum(array_column($itemData,'sgst_amount')):0;
                $row['igst_acc_id'] = 15;
                $row['igst_per'] = 0;
                $row['igst_amount'] = ($row['gst_type'] == 2)?array_sum(array_column($itemData,'igst_amount')):0;
                $row['gst_amount'] = $row['igst_amount'] + $row['cgst_amount'] + $row['sgst_amount'];
                $row['net_amount'] = array_sum(array_column($itemData,'net_amount'));
                $row['round_off_acc_id'] = 41;
                $row['round_off_amount'] = round((round($row['net_amount'],0,PHP_ROUND_HALF_UP) - $row['net_amount']),2);
                $row['net_amount'] = $row['net_amount'] + $row['round_off_amount'];

                $result = $this->masterModel->store("trans_main",$row,'Sales Invoice');

                if(!empty($masterDetails)):
                    $masterDetails['id'] = "";
                    $masterDetails['main_ref_id'] = $result['id'];
                    $masterDetails['table_name'] = "trans_main";
                    $masterDetails['description'] = "PURINV MASTER DETAILS";
                    $this->masterModel->store("trans_details",$masterDetails);
                endif;

                
                foreach($itemData as $item):
                    $md_id = $item['md_id']; unset($item['md_id']);
                    $item['id'] = "";
                    $item['entry_type'] = $row['entry_type'];
                    $item['trans_main_id'] = $result['id'];
                    $item['is_delete'] = 0;

                    $itemTrans = $this->masterModel->store("trans_child",$item);

                    $setData = array();
                    $setData['tableName'] = "miracel_data";
                    $setData['where']['id'] = $md_id;
                    $setData['update']['status'] = $itemTrans['id'];
                    $this->masterModel->setValue($setData);
                endforeach;

                $row['id'] = $result['id'];
                $this->transMainModel->ledgerEffects($row,array());
            endforeach; */
            exit;

            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                $this->db->trans_rollback();
                echo "Purchase Invoice Migration Success.";
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    /* Miracel Data Import End */

    public function executeQuery(){
        $result = $this->db->query("");
        return $result;
    }

	/* Migration/qcInstrument */
	public function qcInstrument(){
        try{
            $this->db->trans_begin();
            
			$this->db->reset_query();
			$this->db->select("qc.*,GROUP_CONCAT(id) as ids");
            $this->db->where('item_id',0);
            $this->db->group_by('item_name,make_brand,size,permissible_error');
            $result = $this->db->get('qc')->result();
			
            foreach($result as $row):
                $qcData = [
                    'item_code' => 'AT2-IN-',
                    'item_name' => $row->item_name,
                    'item_type' => 6,
					'category_id' => 24,
                    'uom' => 'NOS',
                    'gst_per' => 18,                    
                    'size' => $row->size,
                    'make_brand' => $row->make_brand,
                    'permissible_error' => $row->permissible_error,
                    'cal_required' => $row->cal_req,
                    'cal_freq' => $row->cal_freq,
					'cal_reminder' => $row->cal_rem_day
                ];

                //$this->db->insert('item_master',$qcData);
				
				$this->db->where_in('id',$row->ids);
				//$this->db->update('qc',['item_id'=>$this->db->insert_id()]);
            endforeach;
			exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "QC ITEM Migration Success.";
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
 
 	/* Migration/qcInstrumentStock */
	public function qcInstrumentStock(){
        try{
            $this->db->trans_begin();
            
			$this->db->reset_query();
            $this->db->where('item_id !=',0);
            $result = $this->db->get('qc')->result();
			
            foreach($result as $row):
                $qcData = [
					'item_id' => $row->item_id,
                    'item_code' => $row->item_code,
                    'item_name' => $row->item_code.' '.$row->item_name.' '.$row->size,
					'category_id' => 24,
                    'uom' => 'NOS',
                    'gst_per' => $row->gst_per,
					'hsn_code' => $row->hsn_code,
                    'size' => $row->size,
					'mfg_sr' => $row->sr_no,
                    'make_brand' => $row->make_brand,
                    'permissible_error' => $row->permissible_error,
                    'cal_required' => $row->cal_req,
                    'cal_freq' => $row->cal_freq,
					'cal_reminder' => $row->cal_rem_day,
					'description' => $row->description
                ];

                //$this->db->insert('qc_instruments',$qcData);
				
				//$this->db->where_in('id',$row->ids);
				//$this->db->update('qc',['item_id'=>$this->db->insert_id()]);
            endforeach;
			exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "QC ITEM Migration Success.";
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function migrateJobRevNo(){
        try{
            $this->db->trans_begin();
            
			$this->db->reset_query();
            $this->db->select("prc_master.id,prc_master.item_id");
            $this->db->where('(rev_no IS NULL OR rev_no = "")');
            $this->db->where('prc_master.is_delete',0);
            $result = $this->db->get('prc_master')->result();
			
            foreach($result as $row):
                $revData = $this->item->getLatestRev(['item_id'=>$row->item_id]);
                $rev_no = (!empty($revData->rev_no))?$revData->rev_no:null;

			    $this->db->reset_query();
				$this->db->where('id',$row->id);
				$this->db->update('prc_master',['rev_no'=>$rev_no]);
            endforeach;
			// exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Rev Migration Success.";
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    //migrate cutting_flow in prc_master table

    public function migrateCuttingFlow(){
        try{
            $this->db->trans_begin();
            
			$this->db->reset_query();
            $this->db->where('is_delete',0);            
            $this->db->where('item_type',1);
            $result = $this->db->get('item_master')->result();
			
            foreach($result as $row):
				$this->db->where('item_id',$row->id);
				$this->db->update('prc_master',['cutting_flow'=>$row->cutting_flow]);
            endforeach;
			// exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "ITEM Cutting Migration Success.";
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
}
?>