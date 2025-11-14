<?php
class PartyModel extends MasterModel{
    private $partyMaster = "party_master";
    private $groupMaster = "group_master";
    private $countries = "countries";
    private $states = "states";
	private $cities = "cities";
    private $transDetails = "trans_details";

    public function getPartyCode($category=1){
        $queryData['tableName'] = $this->partyMaster;
        $queryData['select'] = "ifnull((MAX(CAST(REGEXP_SUBSTR(party_code,'[0-9]+') AS UNSIGNED)) + 1),1) as code";
        $queryData['where']['party_category'] = $category;
        $result = $this->row($queryData)->code;
        return $result;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->partyMaster;
        
		if($data['party_category'] != 4):
            $data['where']['party_master.party_category'] = $data['party_category'];
			
			$data['where']['party_master.party_type'] = $data['party_type'];
			
			$data['order_by']['party_master.party_name'] = "ASC";
        endif;

        $data['searchCol'][] = "";
		$data['searchCol'][] = "";
        if($data['party_category'] == 1):
            $data['searchCol'][] = "party_master.party_name";
			$data['searchCol'][] = "party_master.contact_person";
			$data['searchCol'][] = "party_master.party_mobile";
			$data['searchCol'][] = "party_master.party_code";
			$data['searchCol'][] = "party_master.currency";
        elseif($data['party_category'] == 2):
            $data['searchCol'][] = "party_master.party_name";
			$data['searchCol'][] = "party_master.contact_person";
			$data['searchCol'][] = "party_master.party_mobile";
			$data['searchCol'][] = "party_master.party_code";
        elseif($data['party_category'] == 3):
            $data['searchCol'][] = "party_master.party_name";
			$data['searchCol'][] = "party_master.contact_person";
			$data['searchCol'][] = "party_master.party_mobile";
			$data['searchCol'][] = "party_master.party_address";
			$data['searchCol'][] = "party_master.party_code";
        elseif($data['party_category'] == 4):
            $data['select'] = "party_master.*,(CASE WHEN tl.op_balance > 0 THEN CONCAT(ABS(tl.op_balance), ' Cr.') WHEN tl.op_balance < 0 THEN CONCAT(ABS(tl.op_balance), ' Dr.') ELSE 0 END) as op_balance,(CASE WHEN tl.cl_balance > 0 THEN CONCAT(ABS(tl.cl_balance), ' Cr.') WHEN tl.cl_balance < 0 THEN CONCAT(ABS(tl.cl_balance), ' Dr.') ELSE 0 END) as cl_balance";

            $data['leftJoin']["(SELECT tl.vou_acc_id , (am.opening_balance + SUM( CASE WHEN tl.trans_date < '".$this->startYearDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance, (am.opening_balance  + SUM( CASE WHEN tl.trans_date <= '".$this->endYearDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance FROM party_master as am LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id WHERE am.is_delete = 0 AND tl.is_delete = 0 GROUP BY am.id) as tl"] = 'tl.vou_acc_id = party_master.id';

            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "party_master.group_name";
            $data['searchCol'][] = "tl.op_balance";
            $data['searchCol'][] = "tl.cl_balance";
        endif;

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function getPartyList($data=array()){
        $queryData = array();
        $queryData['tableName']  = $this->partyMaster;
        
        if(!empty($data['party_category'])):
            $queryData['where_in']['party_category'] = $data['party_category'];
        endif;

        if(!empty($data['group_id'])):
            $queryData['where_in']['group_id'] = $data['group_id'];
        endif;
		
		if(!empty($data['party_type'])):
            $queryData['where_in']['party_type'] = $data['party_type'];
        else:
            $queryData['where']['party_type'] = 1;
        endif;

        if(!empty($data['group_code'])):
            $queryData['where_in']['group_code'] = $data['group_code'];
        endif;

        if(!empty($data['system_code'])):
            $queryData['where_in']['system_code'] = $data['system_code'];
            $queryData['order_by_field']['system_code'] = $data['system_code'];
        else:
            $queryData['order_by']['party_name'] = "ASC";
        endif;
		
		if(!empty($data['id'])):
            $queryData['where']['id'] = $data['id'];
        endif;

        return $this->rows($queryData);
    }

    public function getParty($data){
        $queryData = array();
        $queryData['tableName']  = $this->partyMaster;
        $queryData['select'] = "party_master.*,IF(party_master.gstin > 0,SUBSTRING(party_master.gstin,1,2),96) as state_code,IF(currency.inrrate > 0,currency.inrrate,1) as inrrate, currency.arial_uni_ms as currency_code, (CASE WHEN tl.cl_balance > 0 THEN ABS(TRIM(tl.cl_balance)+0) WHEN tl.cl_balance < 0 THEN ABS(TRIM(tl.cl_balance)+0) ELSE 0 END) as closing_balance,(CASE WHEN tl.cl_balance > 0 THEN 'CR' WHEN tl.cl_balance < 0 THEN 'DR' ELSE '' END) as closing_type,tl.cl_balance";

        $queryData['select'] .= ",b_countries.name as country_name,b_states.name as state_name,IF(b_states.gst_statecode > 0, b_states.gst_statecode, 96) as state_code,d_countries.name as delivery_country_name,d_states.name as delivery_state_name,IF(d_states.gst_statecode > 0, d_states.gst_statecode, 96) as delivery_state_code";

        $queryData['leftJoin']['currency'] = "currency.currency = party_master.currency";

        $party_id = (!empty($data['id']))?" AND am.id = ".$data['id']:"";
        $queryData['leftJoin']["(
            SELECT am.id as vou_acc_id, (ifnull(am.opening_balance,0) + SUM(CASE WHEN tl.trans_date <= '".$this->endYearDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id AND tl.is_delete = 0
            WHERE am.is_delete = 0 ".$party_id." GROUP BY am.id
        ) as tl"] = 'tl.vou_acc_id = party_master.id';

        $queryData['leftJoin']['countries as b_countries'] = "party_master.country_id = b_countries.id";
        $queryData['leftJoin']['states as b_states'] = "party_master.state_id = b_states.id";

        $queryData['leftJoin']['countries as d_countries'] = "party_master.delivery_country_id = d_countries.id";
        $queryData['leftJoin']['states as d_states'] = "party_master.delivery_state_id = d_states.id";


        if(!empty($data['id'])):
            $queryData['where']['party_master.id'] = $data['id'];
        endif;

        if(!empty($data['party_category'])):
            $queryData['where_in']['party_master.party_category'] = $data['party_category'];
        endif;

        if(!empty($data['system_code'])):
            $queryData['where']['party_master.system_code'] = $data['system_code'];
        endif;

        if(!empty($data['party_name'])):
            $queryData['where']['party_master.party_name'] = $data['party_name'];
        endif;
		
        if(!empty($data['customWhere'])):
            $queryData['customWhere'][] = $data['customWhere'];
        endif;
		
		if(isset($data['is_delete'])):
            $queryData['where']['party_master.is_delete'] = $data['is_delete'];
        endif;

        return $this->row($queryData);
    }

    public function getCurrencyList(){
		$queryData['tableName'] = 'currency';
		return $this->rows($queryData);
	}

    public function getCountries(){
		$queryData['tableName'] = $this->countries;
		$queryData['order_by']['name'] = "ASC";
		return $this->rows($queryData);
	}

    public function getCountry($data){
		$queryData['tableName'] = $this->countries;
        if(!empty($data['id'])):
		    $queryData['where']['id'] = $data['id'];
        endif;  
        if(!empty($data['name'])):
            $queryData['where']['name'] = $data['name'];
        endif;
		return $this->row($queryData);
	}

    public function getStates($data=array()){
        $queryData['tableName'] = $this->states;
		$queryData['where']['country_id'] = $data['country_id'];
		$queryData['order_by']['name'] = "ASC";
		return $this->rows($queryData);
    }

    public function getState($data){
        $queryData['tableName'] = $this->states;
        $queryData['select'] = 'states.*,states.name as state_name,states.gst_statecode as state_code,countries.name as country_name';
        $queryData['leftJoin']['countries'] = "countries.id = states.country_id";
		$queryData['where']['states.id'] = $data['id'];
		return $this->row($queryData);
    }

    public function getCities($data=array()){
        $queryData['tableName'] = $this->cities;
		$queryData['where']['state_id'] = $data['state_id'];
		$queryData['order_by']['name'] = "ASC";
		return $this->rows($queryData);
    }

    public function getCity($data){
        $queryData['tableName'] = $this->cities;
        $queryData['select'] = 'cities.*,states.name as state_name,states.gst_statecode as state_code,countries.name as country_name';
        $queryData['leftJoin']['states'] = 'cities.state_id = states.id';
        $queryData['leftJoin']['countries'] = "countries.id = cities.country_id";
		$queryData['where']['cities.id'] = $data['id'];
		return $this->row($queryData);
    }

	/* UPDATED BY : AVT DATE:18-12-2024 */
	public function save($data){
		try {
			$this->db->trans_begin();

            if(!empty($data['party_category']) && $this->checkDuplicate(['party_category'=>$data['party_category'], 'party_name'=>$data['party_name'], 'party_mobile'=>$data['party_mobile'], 'id'=>$data['id']]) > 0) :
				$errorMessage['party_name'] = "Party name is duplicate.";
				return ['status' => 0, 'message' => $errorMessage];
            endif;
			
			if(!empty($data['party_category']) && $data['party_type'] == 1 && $this->checkDuplicate(['party_category'=>$data['party_category'], 'party_type'=>$data['party_type'], 'party_code'=>$data['party_code'], 'id'=>$data['id']]) > 0) :
				$errorMessage['party_code'] = "Party code is duplicate.";
				return ['status' => 0, 'message' => $errorMessage];
            endif;

            if(!empty($data['party_category']) && !in_array($data['party_category'],[4,5]))://Customer,Supplier & Vendor
                $groupData = $this->group->getGroup(['group_code'=>(($data['party_category'] == 1)?"'SD'":"'SC'"), 'is_default' => 1]);

                $data['group_id'] = $groupData->id;
                $data['group_name'] = $groupData->name;
                $data['group_code'] = $groupData->group_code;
            elseif(!empty($data['party_category']) && $data['party_category'] == 4)://Other Ledger
                $groupData = $this->group->getGroup(['id'=>$data['group_id']]);

                $data['group_id'] = $groupData->id;
                $data['group_name'] = $groupData->name;
                $data['group_code'] = $groupData->group_code;
            endif;
			unset($data['form_type']);
            $result = $this->store($this->partyMaster, $data, 'Party');

            if(!empty($data['party_category']) && $data['party_category'] != 4):
                $data['party_id'] = $result['id'];
                $this->saveGstDetail($data);
            endif;			
			
            if(!empty($result['id']) && !empty($data['contact_person']) ):
				
				//Save Party Contact
                $contactDetail = [
                    'party_id'=>$result['id'],
                    'contact_person'=>$data['contact_person'],
                    'designation'=>(!empty($data['designation']) ? $data['designation'] : ''),
                    'party_mobile'=>$data['party_mobile'],
                    'party_email'=>$data['party_email'],
                    'is_default'=>1
                ];

                if(!empty($contactDetail)):
                    if(empty($data['id'])):
                        $contactDetail['id'] = "";
                    else:
                        $contactDetail['id'] = "-1";
                    endif;
                    $this->savePartyContact($contactDetail);
                endif;
			endif;
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Throwable $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}
	
	public function checkDuplicate($data){
        $queryData['tableName'] = $this->partyMaster;

        if(!empty($data['party_name'])):
            $queryData['where']['party_name'] = $data['party_name'];        
        endif;

        if(!empty($data['party_code'])):
            $queryData['where']['party_code'] = $data['party_code'];        
        endif;
		
		/*
		if(!empty($data['party_category'])):
            $queryData['where']['party_category'] = $data['party_category']; 
        endif;
		*/
		
		if(!empty($data['party_mobile'])):
            $queryData['where']['party_mobile'] = $data['party_mobile']; 
        endif;

        if(!empty($data['party_type'])):
            $queryData['where']['party_type'] = $data['party_type']; 
        endif;
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
		try {
			$this->db->trans_begin();

            $checkBillWiseRef = $this->transMainModel->checkBillWiseRef(['id'=>0,'party_id'=>$id,'entry_type'=>0]);
            if($checkBillWiseRef == true):
                return ['status'=>2,'message'=>'Bill Wise Reference already adjusted. if you want to delete this account first unset all adjustment.'];
            endif;
            $this->remove("trans_billwise",['trans_main_id'=>0,'party_id'=>$id,'trans_number'=>"OpBal"]);

            $checkData['columnName'] = ['party_id','acc_id','opp_acc_id','vou_acc_id','sp_acc_id'];
			$checkData['ignoreTable'] = ['party_master','party_contact'];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Party is currently in use. you cannot delete it.'];
            endif;

            $this->trash($this->transDetails, ['main_ref_id' => $id,'table_name' =>  $this->partyMaster,'description' => 'PARTY GST DETAIL']);
			$this->trash('party_contact', ['party_id' => $id]); 
			$result = $this->trash($this->partyMaster, ['id' => $id], 'Party');

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Throwable $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function getPartyGSTDetail($data){
        $queryData = array();
        $queryData['tableName'] = $this->transDetails;
        $queryData['select'] = "id, main_ref_id as party_id, t_col_1 as gstin, t_col_2 as party_address, t_col_3 as party_pincode, t_col_4 as delivery_address, t_col_5 as delivery_pincode";
        $queryData['where']['main_ref_id'] = $data['party_id'];
        $queryData['where']['table_name'] = $this->partyMaster;
        $queryData['where']['description'] = "PARTY GST DETAIL";
        return $this->rows($queryData);
    }

    public function saveGstDetail($data){
        try {
			$this->db->trans_begin();

            $partyDetails = $this->getParty(['id'=>$data['party_id']]);

            $queryData['tableName'] = $this->transDetails;
            $queryData['where']['main_ref_id'] = $data['party_id'];
            $queryData['where']['table_name'] = $this->partyMaster;
            $queryData['where']['description'] = "PARTY GST DETAIL";
            $gstData = $this->row($queryData);

            $postData = [
                'id' => (!empty($gstData))?$gstData->id:"",
                'main_ref_id' =>  $data['party_id'],
                'table_name' => $this->partyMaster,
                'description' => "PARTY GST DETAIL",
                't_col_1' => $data['gstin'],
                't_col_2' => $data['party_address'],
			    't_col_3' => $data['party_pincode'],
                't_col_4' => $data['delivery_address']
            ];
            $result = $this->store($this->transDetails,$postData);

            if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Throwable $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function deleteGstDetail($id){
		try {
			$this->db->trans_begin();

			$result = $this->trash($this->transDetails, ['id' => $id], 'Party GST Detail');

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Throwable $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function getPartyContactDetail($data){
        $queryData = array();
        $queryData['tableName'] = $this->transDetails;
        $queryData['select'] = "id, main_ref_id as party_id, t_col_1 as contact_person, t_col_2 as mobile_no, t_col_3 as contact_email";
        $queryData['where']['main_ref_id'] = $data['party_id'];
        $queryData['where']['table_name'] = $this->partyMaster;
        $queryData['where']['description'] = "PARTY CONTACT DETAIL";
        return $this->rows($queryData);
    }

    public function saveContactDetail($data){
        try {
			$this->db->trans_begin();

            $postData = [
                'id' => "",
                'main_ref_id' => $data['party_id'],
                'table_name' => $this->partyMaster,
                'description' => "PARTY CONTACT DETAIL",
                't_col_1' => $data['person'],
                't_col_2' => $data['mobile'],
			    't_col_3' => $data['email']
            ];

            $result = $this->store($this->transDetails,$postData,'Contact Detail');

            if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Throwable $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
    }

    public function deleteContactDetail($id){
		try {
			$this->db->trans_begin();

			$result = $this->trash($this->transDetails, ['id' => $id], 'Contact Detail');

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Throwable $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

    public function getTDSClassList($data = array()){
        $queryData = array();
        $queryData['tableName'] = "tds_class";
        
        if(!empty($data['class_type']))
            $queryData['where_in']['class_type'] = $data['class_type'];

        $result = $this->rows($queryData);
        return $result;
    }
    
    /* Party Opening Balance Start */

    public function getPartyOpBalance($data){
		$group_id = (!empty($data['group_id']))? 'AND am.group_id = '.$data['group_id']:'';
		$group_code = (!empty($data['group_code']))? 'AND am.group_code = '.$data['group_code']:'';

        $ledgerSummary = $this->db->query("SELECT 
            am.id,
            am.party_name as account_name, 
            am.group_name, 

            (CASE WHEN am.opening_balance > 0 THEN CONCAT(abs(am.opening_balance),' CR.') WHEN am.opening_balance < 0 THEN CONCAT(abs(am.opening_balance),' DR.') ELSE am.opening_balance END) as op_balance,
            (CASE WHEN am.opening_balance > 0 THEN 1 WHEN am.opening_balance < 0 THEN -1 ELSE 1 END) as op_balance_type,

            (CASE WHEN am.other_op_balance > 0 THEN CONCAT(abs(am.other_op_balance),' CR.') WHEN am.other_op_balance < 0 THEN CONCAT(abs(am.other_op_balance),' DR.') ELSE am.other_op_balance END) as other_op_balance,
            (CASE WHEN am.other_op_balance > 0 THEN 1 WHEN am.other_op_balance < 0 THEN -1 ELSE 1 END) as other_op_balance_type

            FROM party_master as am
            WHERE am.is_delete = 0 ".$group_id." ".$group_code."
            ORDER BY am.party_name
        ")->result();

        return $ledgerSummary;
    }

    public function saveOpeningBalance($data){
        try{
            $this->db->trans_begin();
            
            if(!empty($data['id'])):

				$data['opening_balance'] = floatval(($data['opening_balance'] * $data['balance_type']));
                unset($data['balance_type']);

                $this->store("party_master",$data);
            else:
                return ['status'=>2,'message'=>'Somthing is wrong...Ledger not found.'];
            endif;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Ledger Opening Balance updated successfully.'];
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    /* Party Opening Balance End */ 
	
	/* CREATED BY : AVT DATE:13-12-2024 */
	/********** Start Party Contact Detail **********/
    public function getPartyContact($data){ 
		$queryData['tableName'] = "party_contact";
		$queryData['select'] = "party_contact.*";	
		$queryData['where']['party_contact.party_id'] = $data['party_id'];
        $result = $this->rows($queryData);
        return $result;
	}
	
    public function savePartyContact($data){  
        try {
            $this->db->trans_begin();

            if(empty($data['id'])):
                $data['id'] = "";
                $result = $this->store('party_contact', $data,'Party Contact');
            else:
				unset($data['id']);
                $result = $this->edit('party_contact', ['party_id'=>$data['party_id'],'is_default'=>1], $data,'Party Contact');
            endif;

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

	public function deletePartyContact($id){
		try{
			$this->db->trans_begin();

			$result = $this->trash('party_contact',['id'=>$id],"Record");
			
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}
    /********** End Party Contact Detail **********/

	/* Create Vendor Store */
    public function createVendorStore($data) {
        try{
            $this->db->trans_begin();

            $level = $this->storeLocation->getStoreLocationList(['ref_id'=>$this->VENDOR_STORE->id]);
            $count = count($level);
            $nextlevel = (($data['store_level'] != 0) ? $data['store_level'].'.'.($count+1) : ($count+1));
            $mainStore = $this->storeLocation->getStoreLocation(['store_level'=>$data['store_level']]);

            $data['main_store_id'] = (!empty($mainStore->id) ? $mainStore->id : 0);
            $data['store_level'] = $nextlevel;
            $data['store_name'] = 'Vendor Store';
            $data['location'] = trim($data['party_name']);

            $storeData = [
                'id' => '',
                'store_level' => $nextlevel,
                'store_name' => 'Vendor Store',
                'location' => $data['party_name'],
                'main_store_id' => (!empty($mainStore->id) ? $mainStore->id : 0),
                'ref_id' => $this->VENDOR_STORE->id,
                'final_location' => 1,
                'store_type' => 0,
                'party_id' => $data['id'],
                'created_by' => $this->loginId,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $storeSave = $this->store('location_master', $storeData);

            $this->edit($this->partyMaster, ['id'=>$data['id']], ['location_id'=>$storeSave['id']]);

            $result = ['status' => 1, 'message' => 'Vendor Store Created Successfully.'];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	    
    }

}
?>