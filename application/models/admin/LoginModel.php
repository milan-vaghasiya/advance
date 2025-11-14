<?php
class LoginModel extends CI_Model{

	private $adminMaster = "admin";
    private $empRole = ["-1"=>"Super Admin","1"=>"Admin","2"=>"Employee"];

	public function checkAuth($data){
		$result = $this->db->where('user_name',$data['user_name'])->where('password',md5($data['password']))->where('is_delete',0)->get($this->adminMaster);
		
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
					$this->session->set_userdata('role',$resData->user_role);
					$this->session->set_userdata('roleName',$this->empRole[$resData->user_role]);
					$this->session->set_userdata('emp_name',$resData->user_name);					
					
					return ['status'=>1,'message'=>'Login Success.'];
				endif;
			endif;
		else:
			return ['status'=>0,'message'=>"Invalid Username or Password."];
		endif;
	}

}
?>