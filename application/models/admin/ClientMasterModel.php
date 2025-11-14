<?php
class ClientMasterModel extends MasterModel{
    private $clientMaster = "company_info";
    private $countries = "countries";
	private $states = "states";
	private $cities = "cities";
    private $employeeMaster = "employee_master";
    private $defualtMenuMaster = "defualt_menu_master";
    private $menuMaster = "menu_master";
    private $menuPermission = "menu_permission";
    private $defualtSubMenuMaster = "defualt_sub_menu_master";
    private $subMenuMaster = "sub_menu_master";
    private $subMenuPermission = "sub_menu_permission";
    private $defualPartyMaster = "defualt_party_master";
    private $partyMaster = "party_master";
    private $defualtTaxMaster = "defualt_tax_master";
    private $taxMaster = "tax_master";
    private $defualtExpenseMaster = "defualt_expense_master";
    private $expenseMaster = "expense_master";
    private $defualtItemCategory = "defualt_item_category";
    private $itemCategory = "item_category";
    private $defualtLocationMaster = "defualt_location_master";
    private $locationMaster = "location_master";

    public function getDTRows($data){
        $data['tableName'] = $this->clientMaster;

        $data['select'] = "company_info.*,bcountry.name as company_country, bstate.name as company_state, bstate.gst_statecode as company_state_code, bcity.name as company_city";

        $data['leftJoin']['countries as bcountry'] = "company_info.company_country_id = bcountry.id";
        $data['leftJoin']['states as bstate'] = "company_info.company_state_id = bstate.id";
        $data['leftJoin']['cities as bcity'] = "company_info.company_city_id = bcity.id";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "company_info.company_name";
        $data['searchCol'][] = "company_info.company_contact_person";
        $data['searchCol'][] = "company_info.company_phone";
        $data['searchCol'][] = "company_info.company_email";
        $data['searchCol'][] = "company_info.company_gst_no";
        $data['searchCol'][] = "bcity.name";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
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
		$queryData['where']['id'] = $data['id'];
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
		$queryData['where']['id'] = $data['id'];
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

    public function checkDuplicate($data){
        $queryData['tableName'] = $this->employeeMaster;
        $queryData['where']['emp_contact'] = $data['company_phone'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['company_phone'] = "User already exists. Please use another mobile no. for new registration.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $result = $this->store($this->clientMaster,$data,'Client');
            $data['cm_id'] = $result['id'];

            $addUser = $this->addUser($data);
            if($addUser['status'] != 1):
                $this->db->trans_rollback();
                return $addUser;
            endif;
            $data['created_by'] = $addUser['id'];

            $defualMenu = $this->addDefualtMenu($data);
            if($defualMenu['status'] != 1):
                $this->db->trans_rollback();
                return $defualMenu;
            endif;

            $addLedger = $this->addDefualtLedger($data);
            if($addLedger['status'] != 1):
                $this->db->trans_rollback();
                return $addLedger;
            endif;

            $addTaxMaster = $this->addDefualtTaxMaster($data);
            if($addTaxMaster['status'] != 1):
                $this->db->trans_rollback();
                return $addTaxMaster;
            endif;

            $addExpenseMaster = $this->addDefualtExpenseMaster($data);
            if($addExpenseMaster['status'] != 1):
                $this->db->trans_rollback();
                return $addExpenseMaster;
            endif;

            $addItemCtegory = $this->addDefualtItemCategory($data);
            if($addItemCtegory['status'] != 1):
                $this->db->trans_rollback();
                return $addItemCtegory;
            endif;

            $addLocationMaster = $this->addDefualtLocationMaster($data);
            if($addLocationMaster['status'] != 1):
                $this->db->trans_rollback();
                return $addLocationMaster;
            endif;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function addUser($data){
        try{
            $this->db->trans_begin();

            $userData = [
                'id' => "",
                'emp_role' => -1,
                'emp_code' => "E001",
                'emp_name' => $data['company_contact_person'],
                'emp_contact' => $data['company_phone'],
                'emp_password' => md5('123456'),
                'emp_psc' => '123456',
                'cm_id' => $data['cm_id'],
                'created_by' => 0
            ];
            $result = $this->store($this->employeeMaster,$userData);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'User added successfully.','id'=>$result['id']];
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"Create User Error : somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function addDefualtMenu($data){
        try{
            $this->db->trans_begin();

            $queryData = [];
            $queryData['tableName'] = $this->defualtMenuMaster;
            $queryData['order_by']["menu_seq"] = "ASC";
            $defualtMenu = $this->rows($queryData);

            foreach($defualtMenu as $menu):
                $queryData = [];
                $queryData['tableName'] = $this->defualtSubMenuMaster;
                $queryData['where']['menu_id'] = $menu->id;
                $queryData['order_by']["sub_menu_seq"] = "ASC";
                $queryData['order_by']["is_report"] = "ASC";
                $defualtSubMenu = $this->rows($queryData);

                $menu = (array) $menu;
                $menu['id'] = "";
                $menu['cm_id'] = $data['cm_id'];
                $menu['created_by'] = $data['created_by'];

                $masterMenu = $this->store($this->menuMaster,$menu);

                $permission = [
                    'id' => '',
                    'is_master' => $menu['is_master'],
                    'emp_id' => $data['created_by'],
                    'menu_id' => $masterMenu['id'],
                    'is_read' => 1,
                    'is_write' => 1,
                    'is_modify' => 1,
                    'is_remove' => 1,
                    'created_by' => $data['created_by'],
                    'cm_id' => $data['cm_id']
                ];
                $this->store($this->menuPermission,$permission);
                

                foreach($defualtSubMenu as $subMenu):
                    $subMenu = (array) $subMenu;
                    $subMenu['id'] = "";
                    $subMenu['menu_id'] = $masterMenu['id'];
                    $subMenu['cm_id'] = $data['cm_id'];
                    $subMenu['created_by'] = $data['created_by'];

                    $subMenuMaster = $this->store($this->subMenuMaster,$subMenu);

                    $subPermission = [
                        'id' => '',
                        'emp_id' => $data['created_by'],
                        'menu_id' => $masterMenu['id'],
                        'sub_menu_id' => $subMenuMaster['id'],
                        'is_read' => 1,
                        'is_write' => 1,
                        'is_modify' => 1,
                        'is_remove' => 1,
                        'is_approve' => $subMenu['is_approve_req'],
                        'created_by' => $data['created_by'],
                        'cm_id' => $data['cm_id']
                    ];
                    $this->store($this->subMenuPermission,$subPermission);
                endforeach;
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Menu added successfully.'];
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"Menu Error : somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function addDefualtLedger($data){
        try{
            $this->db->trans_begin();

            $queryData = [];
            $queryData['tableName'] = $this->defualPartyMaster;
            $partyList = $this->rows($queryData);

            foreach($partyList as $row):
                $row = (array) $row;
                $row['id'] = "";
                $row['balance_type'] = "1";
                $row['opening_balance'] = 0;
                $row['created_by'] = $data['created_by'];
                $row['cm_id'] = $data['cm_id'];

                $this->store($this->partyMaster,$row);
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Ledger added successfully.'];
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"Ledger Error : somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function addDefualtTaxMaster($data){
        try{
            $this->db->trans_begin();

            $taxCodes = [
                'cgst1' => 'CGSTIPACC',
                'sgst1' => 'SGSTIPACC',
                'igst1' => 'IGSTIPACC',
                'utgst1' => 'UTGSTIPACC',
                'cess1' => 'CESSIP',
                'cess_qty1' => 'CESSIP',
                'tcs1' => 'TCSONPUR',
                'tds1' => 'TDSRECEIVABLE',
                'cgst2' => 'CGSTOPACC',
                'sgst2' => 'SGSTOPACC',
                'igst2' => 'IGSTOPACC',
                'utgst2' => 'UTGSTOPACC',
                'cess2' => 'CESSOP',
                'cess_qty2' => 'CESSOP',
                'tcs2' => 'TCSONSALE',
                'tds2' => 'TDSPAYABLE'
            ];

            $queryData = [];
            $queryData['tableName'] = $this->defualtTaxMaster;
            $taxList = $this->rows($queryData);

            foreach($taxList as $row):
                $queryData = [];
                $queryData['tableName'] = $this->partyMaster;
                $queryData['where']['system_code'] = $taxCodes[$row->map_code.$row->tax_type];
                $queryData['where']['cm_id'] = $data['cm_id'];
                $partyDetail = $this->row($queryData);

                $row = (array) $row;
                $row['id'] = "";
                $row['acc_id'] = $partyDetail->id;
                $row['acc_name'] = $partyDetail->party_name;
                $row['created_by'] = $data['created_by'];
                $row['cm_id'] = $data['cm_id'];

                $this->store($this->taxMaster,$row);
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Tax Master added successfully.'];
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"Tax Master Error : somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function addDefualtExpenseMaster($data){
        try{
            $this->db->trans_begin();

            $expenseCodes = [
                'roff' => 'ROFFACC',
            ];

            $queryData = [];
            $queryData['tableName'] = $this->defualtExpenseMaster;
            $expenseList = $this->rows($queryData);

            foreach($expenseList as $row):
                $queryData = [];
                $queryData['tableName'] = $this->partyMaster;
                $queryData['where']['system_code'] = $expenseCodes[$row->map_code];
                $queryData['where']['cm_id'] = $data['cm_id'];
                $partyDetail = $this->row($queryData);

                $row = (array) $row;
                $row['id'] = "";
                $row['acc_id'] = $partyDetail->id;
                $row['created_by'] = $data['created_by'];
                $row['cm_id'] = $data['cm_id'];

                $this->store($this->expenseMaster,$row);
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Expense Master added successfully.'];
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"Expense Master Error : somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function addDefualtItemCategory($data){
        try{
            $this->db->trans_begin();

            $queryData = [];
            $queryData['tableName'] = $this->defualtItemCategory;
            $categoryList = $this->rows($queryData);

            foreach($categoryList as $row):
                $categoryName = $row->auth_detail_po;

                $row = (array) $row;
                $row['id'] = "";
                $row['auth_detail_po'] = "";
                $row['created_by'] = $data['created_by'];
                $row['cm_id'] = $data['cm_id'];

                $result = $this->store($this->itemCategory,$row);

                if(!empty($row['ref_id'])):
                    $setData = [];
                    $setData['tableName'] = $this->itemCategory;
                    $setData['where']['id'] = $result['id'];
                    $setData['update']['ref_id'] = "(SELECT id FROM item_category WHERE category_name = '".$categoryName."' AND cm_id = ".$data['cm_id'].")";
                    $this->setValue($setData);
                endif;
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Item Category added successfully.'];
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"Item Category Error : somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function addDefualtLocationMaster($data){
        try{
            $this->db->trans_begin();

            $queryData = [];
            $queryData['tableName'] = $this->defualtLocationMaster;
            $locationList = $this->rows($queryData);

            foreach($locationList as $row):

                $row = (array) $row;
                $row['id'] = "";
                $row['created_by'] = $data['created_by'];
                $row['cm_id'] = $data['cm_id'];
                $result = $this->store($this->locationMaster,$row);

                
                $setData = [];
                $setData['tableName'] = $this->locationMaster;
                $setData['where']['id'] = $result['id'];                
                if(!empty($row['ref_id'])):
                    $setData['update']['ref_id'] = "(SELECT id FROM location_master WHERE store_type = 99 AND cm_id = ".$data['cm_id'].")";
                    $setData['update']['main_store_id'] = "(SELECT id FROM location_master WHERE store_type = 99 AND cm_id = ".$data['cm_id'].")";
                else:
                    $setData['update']['main_store_id'] = $result['id'];
                endif;
                $this->setValue($setData);                
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Location Master added successfully.'];
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"Location Master Error : somthing is wrong. Error : ".$e->getMessage()];
        }
    }
}
?>