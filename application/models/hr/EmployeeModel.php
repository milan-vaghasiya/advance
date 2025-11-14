<?php
class EmployeeModel extends MasterModel{
    private $empMaster = "employee_master";
    private $empDocuments = "emp_docs";
    private $empNom = "emp_nomination_detail";
    private $empEdu = "emp_education_detail";

    public function getDTRows($data){
        $data['tableName'] = $this->empMaster;

        $data['select'] = "employee_master.*,department_master.name as dept_name,emp_designation.title as emp_designation,emp_category.category as emp_category";

        $data['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $data['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $data['leftJoin']['emp_category'] = "employee_master.emp_category = emp_category.id";
        
        $data['where']['employee_master.emp_role !='] = "-1";

		if($data['status']==0):
            $data['where']['employee_master.is_active']=1;
        else:
            $data['where']['employee_master.is_active']=0;
        endif;
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "employee_master.emp_code";
        $data['searchCol'][] = "department_master.name";
        $data['searchCol'][] = "emp_designation.title";
        $data['searchCol'][] = "emp_category.category";
        $data['searchCol'][] = "employee_master.emp_contact";
        $data['searchCol'][] = "employee_master.pan_no";
        $data['searchCol'][] = "employee_master.aadhar_no";
        $data['searchCol'][] = "(CASE WHEN employee_master.unit_id = 1 THEN 'UNIT 1' WHEN employee_master.unit_id = 2 THEN 'UNIT 2' ELSE '' END)";
        
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function getNextEmpNo(){
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "ifnull((MAX(biomatric_id) + 1),1) as biomatric_id";
        $nextNo = $this->specificRow($queryData)->biomatric_id;
		return $nextNo;
    }

    public function getEmployeeList($data=array()){
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "employee_master.*,department_master.name as department_name,emp_designation.title as designation_name";
        $queryData['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $queryData['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
		
        if(!empty($data['emp_role'])):
            $queryData['where_in'] = $data['emp_role'];
        endif;

        if(!empty($data['emp_sys_desc_id'])):
            $queryData['where']['find_in_set("'.$data['emp_sys_desc_id'].'", emp_sys_desc_id) >'] = 0;
        endif;

        if(!empty($data['emp_designation'])):
            $queryData['where']['employee_master.emp_designation'] = $data['emp_designation'];
        endif;

        if(!empty($data['is_active'])):
            $queryData['where_in']['employee_master.is_active'] = $data['is_active'];
        endif;

        if(empty($data['all'])):
            $queryData['where']['employee_master.emp_role !='] = "-1";
        endif;
		
		if(!empty($data['emp_dept_id'])):
            $queryData['where']['employee_master.emp_dept_id'] = $data['emp_dept_id'];
        endif;

        return $this->rows($queryData);
    }

    //12-04-2024
    public function getEmployee($data){
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "employee_master.*,department_master.name as department_name,emp_designation.title as designation_name";
        $queryData['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $queryData['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        if(!empty($data['id'])){
            $queryData['where']['employee_master.id'] = $data['id'];
        }
        if(!empty($data['emp_name'])){
            $data['where']['emp_name'] = $data['emp_name'];
        }
        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
                $errorMessage['emp_contact'] = "Contact no. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;
			
			//Latest Emp. Code
			if(empty($data['id'])):
                $emp_no = $this->getNextEmpNo();
				$data['biomatric_id'] = $emp_no;
				$data['emp_code'] = sprintf("AE%03d",$emp_no);
            endif;

            if(empty($data['id'])):
                $data['emp_psc'] = $data['emp_password'];
                $data['emp_password'] = md5($data['emp_password']); 
            endif;

            $result =  $this->store($this->empMaster,$data,'Employee');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }        
    }

    public function checkDuplicate($data){
        $queryData['tableName'] = $this->empMaster;
        $queryData['where']['emp_contact'] = $data['emp_contact'];
        
        if(!empty($data['id']))
            $queryData['where']['id !='] = $data['id'];
        
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $checkData['columnName'] = ['created_by','updated_by'];
            $checkData['value'] = $id;
            $checkUsed = $this->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Shift is currently in use. you cannot delete it.'];
            endif;

            $this->trash($this->empDocuments,['emp_id'=>$id],'Employee');
            $this->trash($this->empNom,['emp_id'=>$id],'Employee');
            $this->trash($this->empEdu,['emp_id'=>$id],'Employee');

            $result = $this->trash($this->empMaster,['id'=>$id],'Employee');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function activeInactive($postData){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->empMaster,$postData,'');
            $result['message'] = "Employee ".(($postData['is_active'] == 1)?"Activated":"De-activated")." successfully.";
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function changePassword($data){
        try{
            $this->db->trans_begin();

            if(empty($data['id'])):
                return ['status'=>2,'message'=>'Somthing went wrong...Please try again.'];
            endif;

            $empData = $this->getEmployee(['id'=>$data['id']]);
            if(md5($data['old_password']) != $empData->emp_password):
                $result = ['status'=>0,'message'=>['old_password'=>"Old password not match."]];
            endif;

            $postData = ['id'=>$data['id'],'emp_password'=>md5($data['new_password']),'emp_psc'=>$data['new_password']];
            $result = $this->store($this->empMaster,$postData);
            $result['message'] = "Password changed successfully.";

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function resetPassword($id){
        try{
            $this->db->trans_begin();

            $data['id'] = $id;
            $data['emp_psc'] = '123456';
            $data['emp_password'] = md5($data['emp_psc']); 
            
            $result = $this->store($this->empMaster,$data);
            $result['message'] = 'Password Reset successfully.';

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}

    public function editProfile($data){
        try{
            $this->db->trans_begin();

            $form_type = $data['form_type']; unset($data['form_type'], $data['designationTitle']);
            $empData = $this->getEmployee(['id'=>$data['id']]);

            if(in_array($form_type,['updateProfilePic','personalDetail','workProfile'])):
                $result = $this->store($this->empMaster,$data,'Employee');
            endif;
            
            if($form_type == 'workProfile'):
                $relieveData = array();
                $relieveData['emp_id'] = (!empty($data['id'])) ? $data['id'] : $result['id'];
                $relieveData['emp_joining_date'] = $data['emp_joining_date'];
                $relieveData['emp_relieve_date'] = '';
                $relieveData['reason'] = '';
                $relieveData['is_delete'] = 0;
                if (empty($data['id'])):
                    $this->saveReliveDetailJson($relieveData);
                else:
                    if (!empty($empData->relieve_detail)):
                        $jsonData = json_decode($empData->relieve_detail);
                        $relieveArr = array();
        
                        $joiningDate = array();
                        foreach ($jsonData as $row):
                            $joiningDate[] = $row->emp_relieve_date;
                            if ($empData->emp_joining_date == $row->emp_joining_date):
                                $relieveArr[] = [
                                    'emp_joining_date' => $data['emp_joining_date'],
                                    'emp_relieve_date' => '',
                                    'reason' => ''
                                ];
                            else :
                                $relieveArr[] = $row;
                            endif;
                        endforeach;
                        $max = max(array_map('strtotime', $joiningDate));
                        if ($data['emp_joining_date'] > date('Y-m-d', $max)) :
                            $this->edit($this->empMaster, ['id' => $data['id']], ['relieve_detail' => json_encode($relieveArr), 'emp_joining_date' => $data['emp_joining_date']]);
                        else :
                            $errorMessage['emp_joining_date'] = "Sorry you can not edit joining date beacuse joining date is less then last relieve date";
                            return ['status' => 0, 'message' => $errorMessage];
                        endif;
                    else :
                        $this->saveReliveDetailJson($relieveData);
                    endif;
                endif;
            endif;

            if($form_type == "empDocs"):
                $result = $this->store($this->empDocuments,$data,'Employee Document');
            endif;

            if($form_type == "empNomination"):
                $result = $this->store($this->empNom,$data,'Employee Nomination');
            endif;

            if($form_type == "empEdu"):
                $result = $this->store($this->empEdu,$data,'Employee Education');
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

    public function saveReliveDetailJson($data){
        try{
            $this->db->trans_begin();

            $empQuery['tableName'] = $this->empMaster;
            $empQuery['where']['id'] = $data['emp_id'];
            $empQuery['where']['employee_master.is_delete'] = $data['is_delete'];
            $empData = $this->row($empQuery);

            if (!empty($empData->emp_relieve_date) && !empty($data['emp_joining_date']) && $empData->emp_relieve_date > $data['emp_joining_date']) :
                $errorMessage['emp_joining_date'] = "Your joining date is less then last relieve date";
                return ['status' => 0, 'message' => $errorMessage];
            endif;

            $relieveArr = array();
            if (!empty($empData->relieve_detail)):
                $relieveArr = json_decode($empData->relieve_detail);
            endif;

            $relieveArr[] = [
                'emp_joining_date' => $data['emp_joining_date'],
                'emp_relieve_date' => $data['emp_relieve_date'],
                'reason' => $data['reason']
            ];
            $joining_date = '';

            if (!empty($data['emp_joining_date'])):
                $joining_date = $data['emp_joining_date'];
            else:
                $joining_date = $empData->emp_joining_date;
            endif;

            $result = $this->edit($this->empMaster, ['id' => $data['emp_id']], ['relieve_detail' => json_encode($relieveArr), 'emp_joining_date' => $joining_date]);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function removeProfileDetails($data){
        try{
            $this->db->trans_begin();

            if($data['type'] == "empDocs"):
                if(empty($data['id'])):
                    return ['status'=>0,'message'=>'Somthing went wrong...Please try again.'];
                else:
                    $queryData = array();
                    $queryData['tableName'] = $this->empDocuments;
                    $queryData['where']['id'] = $data['id'];
                    $docDetail = $this->row($queryData);

                    $filePath = realpath(APPPATH . '../assets/uploads/emp_documents/');
                    if(!empty($docDetail->doc_file) && file_exists($filePath.'/'.$docDetail->doc_file)):
                        unlink($filePath.'/'.$docDetail->doc_file);
                    endif;

                    $result = $this->trash($this->empDocuments,['id'=>$data['id'],'emp_id'=>$data['emp_id']],"Employee Document");
                endif;
            endif;

            if($data['type'] == "empNomination"):
                $result = $this->trash($this->empNom,['id'=>$data['id']],"Employee Nomination");
            endif;

            if($data['type'] == "empEdu"):
                $result = $this->trash($this->empEdu,['id'=>$data['id']],"Employee Education");
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

    public function getEmpDocuments($data){
        $queryData['tableName'] = $this->empDocuments;
        $queryData['select'] = 'emp_docs.*,(CASE WHEN doc_type = 1 THEN "Extra Documents" WHEN doc_type = 2 THEN "Aadhar Card"  WHEN doc_type = 3 THEN "Basic Rules" ELSE "" END) as doc_type_name';
        $queryData['where']['emp_id']=$data['emp_id'];		
        return $this->rows($queryData);
    }

    public function getNominationData($data){
		$queryData['where']['emp_id'] = $data['emp_id'];
		$queryData['tableName'] = $this->empNom;
		return $this->rows($queryData);
	}

    public function getEducationData($data){
		$queryData['where']['emp_id'] = $data['emp_id'];
		$queryData['tableName'] = $this->empEdu;
		return $this->rows($queryData);
	}

}
?>