<?php
class Employees extends MY_Controller{
    private $indexPage = "hr/employee/index";
    private $employeeForm = "hr/employee/form";
    private $profile = "hr/employee/emp_profile";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "User Master";
		$this->data['headData']->controller = "hr/employees";   
        $this->data['headData']->pageUrl = "hr/employees";
	}

    public function index(){        
        $this->data['tableHeader'] = getHrDtHeader('employees');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status']=$status;
        $result = $this->employee->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
		foreach($result['data'] as $row):
			$row->sr_no = $i++; 
			$row->emp_role = $this->empRole[$row->emp_role];
			$sendData[] = getEmployeeData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEmployee(){
        $this->data['departmentList'] = $this->department->getDepartmentList();
        $this->data['roleList'] = $this->empRole;
        $this->data['genderList'] = $this->gender;
        $this->data['designationList'] = $this->designation->getDesignations();
        //$this->data['systemDesignation'] = $this->systemDesignation;
        $this->data['empCategoryList'] = $this->employeeCategory->getEmployeeCategoryList(); 
        //$this->data['shiftList'] = $this->shiftModel->getShiftList();
        $this->data['emp_no'] = $this->employee->getNextEmpNo();
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->load->view($this->employeeForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['emp_name']))
            $errorMessage['emp_name'] = "Employee name is required.";
        if(empty($data['emp_code']))
            $errorMessage['emp_code'] = "Emp. Code is required.";
        /* if(empty($data['shift_id']))
            $errorMessage['shift_id'] = "Shift is required."; */
        if(empty($data['emp_role']))
            $errorMessage['emp_role'] = "Role is required.";
        if(empty($data['emp_contact']))
            $errorMessage['emp_contact'] = "Contact No. is required.";
        if(empty($data['emp_dept_id']))
            $errorMessage['emp_dept_id'] = "Department is required.";
        if(empty($data['emp_designation'])):
            if(empty($data['designationTitle'])):
                $errorMessage['emp_designation'] = "Designation is required.";
            else:
                $designation = $this->designation->save(['id'=>'','title'=>$data['designationTitle']]);
                if($designation['status'] != 1):
                    $errorMessage['emp_designation'] = "Please Select Valid Designation.";
                else:
                    $data['emp_designation'] = $designation['id'];
                endif;                
            endif;
        endif;
        unset($data['designationTitle']);
        if(empty($data['id'])):
            $data['emp_password'] = "123456";
        endif;

        if(!empty($_FILES['sign_image']['name'])):
            $attachment = "";
            $this->load->library('upload');
            
            $_FILES['userfile']['name']     = $_FILES['sign_image']['name'];
            $_FILES['userfile']['type']     = $_FILES['sign_image']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['sign_image']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['sign_image']['error'];
            $_FILES['userfile']['size']     = $_FILES['sign_image']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/signature/');

            $fileName = 'sign_'.$data['emp_code']."_".$this->cm_id;
            $config = ['file_name' => $fileName, 'allowed_types' => 'jpg|jpeg|png|gif|JPG|JPEG|PNG', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

            $this->upload->initialize($config);

            if(!$this->upload->do_upload()):
                $errorMessage['sign_image'] = $fileName . " => " . $this->upload->display_errors();
            else:
                $uploadData = $this->upload->data();
                $data['sign_image'] = $uploadData['file_name'];
            endif;

            if(!empty($errorMessage['sign_image'])):
                if (file_exists($imagePath . '/' . $attachment)) : unlink($imagePath . '/' . $attachment); endif;
            endif;            
        endif;
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:            
            $data['emp_name'] = ucwords($data['emp_name']);
            $this->printJson($this->employee->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['departmentList'] = $this->department->getDepartmentList();
        $this->data['roleList'] = $this->empRole;
        $this->data['genderList'] = $this->gender;
        $this->data['designationList'] = $this->designation->getDesignations();
        $this->data['systemDesignation'] = $this->systemDesignation;
        $this->data['empCategoryList'] = $this->employeeCategory->getEmployeeCategoryList(); 
        $this->data['shiftList'] = $this->shiftModel->getShiftList();
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->data['dataRow'] = $this->employee->getEmployee($data);
        $this->load->view($this->employeeForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->delete($id));
        endif;
    }

    public function activeInactive(){
        $postData = $this->input->post();
        if(empty($postData['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->activeInactive($postData));
        endif;
    }
    
    public function changePassword(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['old_password']))
            $errorMessage['old_password'] = "Old Password is required.";
        if(empty($data['new_password']))
            $errorMessage['new_password'] = "New Password is required.";
        if(empty($data['cpassword']))
            $errorMessage['cpassword'] = "Confirm Password is required.";
        if(!empty($data['new_password']) && !empty($data['cpassword'])):
            if($data['new_password'] != $data['cpassword'])
                $errorMessage['cpassword'] = "Confirm Password and New Password is Not match!.";
        endif;

        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['id'] = $this->loginId;
			$result =  $this->employee->changePassword($data);
			$this->printJson($result);
		endif;
    }

    public function resetPassword(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->resetPassword($data['id']));
        endif;
    }

    public function empProfile($emp_id){
        $this->data['empData'] = $this->employee->getEmployee(['id'=>$emp_id]);
        $this->data['departmentList'] = $this->department->getDepartmentList();
        $this->data['designationList'] = $this->designation->getDesignations();
        $this->data['empCategoryList'] = $this->employeeCategory->getEmployeeCategoryList();
        $this->data['shiftList'] = $this->shiftModel->getShiftList();
        //$this->data['empDocs'] = $this->employee->getEmpDocs($emp_id);

        $this->load->view($this->profile,$this->data);        
    }

    public function editProfile(){
        $data = $this->input->post();
        $errorMessage = array();
        if($data['form_type'] == 'personalDetail'):
            if(empty($data['emp_name']))
                $errorMessage['emp_name'] = "Employee name is required.";
            /* if(empty($data['emp_code']))
                $errorMessage['emp_code'] = "Employee Code is required."; */
            if(empty($data['emp_contact']))
                $errorMessage['emp_contact'] = "Contact No. is required.";

            $data['emp_name'] = ucwords($data['emp_name']);
        endif;    

        if($data['form_type'] == 'workProfile'):
            if(empty($data['emp_dept_id']))
                $errorMessage['emp_dept_id'] = "Department is required.";
            if(empty($data['emp_designation']))
                $errorMessage['emp_designation'] = "Designation is required.";
            if(empty($data['emp_type']))
                $errorMessage['emp_type'] = "Employee Type is required.";
            if($data['sal_pay_mode'] != "CASH"):
                if(empty($data['bank_name']))
                    $errorMessage['bank_name'] = "Bank is required.";
                if(empty($data['account_no']))
                    $errorMessage['account_no'] = "Acc. No. is required.";
                if(empty($data['ifsc_code']))
                    $errorMessage['ifsc_code'] = "IFSC Code is required.";
            endif;
        endif;  

        if($data['form_type'] == "updateProfilePic"):
            if($_FILES['emp_profile']['name'] != null || !empty($_FILES['emp_profile']['name'])):
                $this->load->library('upload');
                $_FILES['userfile']['name']     = $_FILES['emp_profile']['name'];
                $_FILES['userfile']['type']     = $_FILES['emp_profile']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['emp_profile']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['emp_profile']['error'];
                $_FILES['userfile']['size']     = $_FILES['emp_profile']['size'];
                
                $imagePath = realpath(APPPATH . '../assets/uploads/emp_profile/');
                $ext = pathinfo($_FILES['emp_profile']['name'], PATHINFO_EXTENSION);

                $config = ['file_name' => $data['id'].'.'.$ext,'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path' => $imagePath];

                if(file_exists($config['upload_path'].'/'.$config['file_name'])) unlink($config['upload_path'].'/'.$config['file_name']);

                $this->upload->initialize($config);
                if (!$this->upload->do_upload()):
                    $errorMessage['newProfilePhoto'] = $this->upload->display_errors();
                else:
                    $uploadData = $this->upload->data();
                    $data['emp_profile'] = $uploadData['file_name'];
                endif;
            else:
                $this->printJson(['status'=>0,'message'=>"Image not found."]);exit;
            endif;
        endif;

        if($data['form_type'] == "empDocs"):
            if(empty($data['doc_name']))
                $errorMessage['doc_name'] = "Document Name is required.";
            if(empty($data['doc_no']))
                $errorMessage['doc_no'] = "Document No is required.";
            if(empty($data['doc_type']))
                $errorMessage['doc_type'] = "Document Type is required.";

            if($_FILES['doc_file']['name'] != null || !empty($_FILES['doc_file']['name'])):
                $this->load->library('upload');
                $_FILES['userfile']['name']     = $_FILES['doc_file']['name'];
                $_FILES['userfile']['type']     = $_FILES['doc_file']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['doc_file']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['doc_file']['error'];
                $_FILES['userfile']['size']     = $_FILES['doc_file']['size'];
                
                $imagePath = realpath(APPPATH . '../assets/uploads/emp_documents/');
                $config = ['file_name' => time()."_doc_file_".$data['emp_id']."_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];
    
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()):
                    $errorMessage['doc_file'] = $this->upload->display_errors();
                    $this->printJson(["status"=>0,"message"=>$errorMessage]);
                else:
                    $uploadData = $this->upload->data();
                    $data['doc_file'] = $uploadData['file_name'];
                endif;
            else:
                unset($data['doc_file']);
            endif;
        endif;

        if($data['form_type'] == "empNomination"):
            if(empty($data['nom_name']))
                $errorMessage['nom_name'] = "Name is required.";
            if(empty($data['nom_relation']))
                $errorMessage['nom_relation'] = "Relation is required.";
            if(empty($data['nom_dob']))
                $errorMessage['nom_dob'] = "Date of birth is required.";
        endif;

        if($data['form_type'] == "empEdu"):
            if(empty($data['course']))
                $errorMessage['course'] = "Course is required.";
            if(empty($data['passing_year']))
                $errorMessage['passing_year'] = "Passing Year is required.";
            if(empty($data['grade']))
                $errorMessage['grade'] = "Grade is required.";
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->employee->editProfile($data));
        endif;
    }

    public function getEmpDocumentsHtml(){
        $data = $this->input->post();
        $docData = $this->employee->getEmpDocuments(['emp_id'=>$data['emp_id']]);

        $tbodyData="";$i=1; 
        if(!empty($docData)):
            $i=1;
            foreach($docData as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id.",'emp_id':".$row->emp_id.",'type':'empDocs'},'message' : 'Employee Document','fndelete':'removeProfileDetails','res_function':'resTrashEmpDocs'}";
                $tbodyData.= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td class="text-center">'.$row->doc_name.'</td>
                    <td class="text-center">'.$row->doc_no.'</td>
                    <td class="text-center">'.$row->doc_type_name.'</td>
                    <td class="text-center">
                        '.((!empty($row->doc_file))?'<a href="'.base_url('assets/uploads/emp_documents/'.$row->doc_file).'" target="_blank"><i class="fa fa-download"></i></a>':"") .'
                    </td>
                    <td class="text-center">
                        <button type="button" onclick="trash('.$deleteParam.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
                    </td>
                </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="7" class="text-center">No data available in table</td></tr>';
        endif;

        $this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
    }

    public function getEmpNominationsHtml(){
        $data = $this->input->post();
        $empNom = $this->employee->getNominationData(['emp_id'=>$data['emp_id']]);

        $tbodyData="";$i=1; 
        if(!empty($empNom)):
            $i=1;
            foreach($empNom as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id.",'emp_id':".$row->emp_id.",'type':'empNomination'},'message' : 'Employee Nomination','fndelete':'removeProfileDetails','res_function':'resTrashEmpNomination'}";
                $tbodyData.= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $row->nom_name . '</td>
                    <td>' . $row->nom_gender . '</td>
                    <td>' . $row->nom_relation . '</td>
                    <td>' . $row->nom_dob . '</td>
                    <td>' . $row->nom_proportion . ' </td>
                    <td class="text-center">
                        <button type="button" onclick="trash('.$deleteParam.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
                    </td>
                </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="7" class="text-center">No data available in table</td></tr>';
        endif;
        $this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
    }

    public function getEmpEducationsHtml(){
        $data = $this->input->post();
        $empEdu = $this->employee->getEducationData(['emp_id'=>$data['emp_id']]);

        $tbodyData="";$i=1; 
        if(!empty($empEdu)):
            $i=1;
            foreach($empEdu as $row):
                $deleteParam = "{'postData':{'id' : ".$row->id.",'emp_id':".$row->emp_id.",'type':'empEdu'},'message' : 'Employee Education','fndelete':'removeProfileDetails','res_function':'resTrashEmpEdu'}";
                $tbodyData.= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $row->course . '</td>
                    <td>' . $row->university . '</td>
                    <td>' . $row->passing_year . ' </td>
                    <td>' . $row->grade . '</td>
                    <td class="text-center">
                        <button type="button" onclick="trash('.$deleteParam.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>
                    </td>
                </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="6" class="text-center">No data available in table</td></tr>';
        endif;
        $this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
    }

    public function removeProfileDetails(){
        $data = $this->input->post();
        $this->printJson($this->employee->removeProfileDetails($data));
    }
}
?>