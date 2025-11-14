<?php
class AdvanceSalary extends MY_Controller
{
	private $indexpage = "hr/advance_salary/index";
    private $form = "hr/advance_salary/form";
    private $sanctionForm = "hr/advance_salary/sanction_form";
	private $indexPenalty = "hr/advance_salary/indexPenalty";
    private $penalty_form = "hr/advance_salary/penalty_form";
    private $indexFacility = "hr/advance_salary/indexFacility";
    private $facility_form = "hr/advance_salary/facility_form";
    private $bulk_advance_form = "hr/advance_salary/bulk_advance_form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Advance Salary";
		$this->data['headData']->controller = "hr/advanceSalary";
        $this->data['headData']->pageUrl = "hr/advanceSalary";
	}

	public function index(){    
        $this->data['tableHeader'] = getHrDtHeader('advanceSalary');
        $this->load->view($this->indexpage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post(); 
        $data['status'] = $status; $data['type'] = 1;
        $result = $this->advanceSalary->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getAdvanceSalaryData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addAdvance(){
        $this->data['empData'] = $this->employee->getEmployeeList();
        // print_r($this->data['empData']);exit;
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(!isset($data['form_type'])){
            if(empty($data['emp_id'])){$errorMessage['emp_id'] = "Employee is required.";}
            if(empty($data['amount'])){$errorMessage['amount'] = "Amount is required.";}
            if(empty($data['reason'])){$errorMessage['reason'] = "Reason is required.";}
        }else{
            unset($data['form_type']);
            if(empty($data['sanctioned_at']))
                $errorMessage['sanctioned_at'] = "Date and Time is required.";
            if(empty($data['sanctioned_amount']))
                $errorMessage['sanctioned_amount'] = "Sanction Amount is required.";
            $data['sanctioned_by'] = $this->loginId;
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            unset($data['empSelect']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->advanceSalary->save($data));
        endif;
    }
    
    public function edit(){
        $id = $this->input->post('id'); 
        $this->data['dataRow'] = $this->advanceSalary->getAdvanceSalary($id);
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->load->view($this->form,$this->data);
    }

    public function sanctionAdvance(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->advanceSalary->getAdvanceSalary($id);
        $this->load->view($this->sanctionForm,$this->data);
    }
    
    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->advanceSalary->delete($id));
        endif;
    }
  
    public function bulkAdvance(){
    $this->data['deptData'] = $this->department->getDepartmentList();
    $this->load->view($this->bulk_advance_form,$this->data);
    }

    public function getBulkAdvanceDetails(){
		$data = $this->input->post();
		$empData = $this->employee->getEmployeeList(['emp_dept_id'=>$data['dept_id']]);	
		$i=1; $tbody="";
		if(!empty($empData)):
			foreach($empData as $row): 
				$tbody .= '<tr>
					<td>'.$i.'</td>
					<td>'.$row->emp_code.'</td>
					<td>'.$row->emp_name.'</td>
					<td>'.$row->department_name.'</td>
					<td>'.$row->designation_name.'</td>
					<td>  
                        <select name="payment_mode['.($i-1).']" id="payment_mode" class="form-control select2">
                        <option value="CS">CASH</option>
                        <option value="BA">BANK</option>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="emp_id[]" value="'.$row->id.'">
                      
                        <input type="text" name="amount[]" id="amount'.$i.'" data-row_id="'.$i.'" class="form-control  floatOnly" value="">
                    </td>
                    <td><input type="text" name="reason[]" id="reason'.$i.'" data-row_id="'.$i.'" class="form-control" value=""></td>
				</tr>'; $i++;
			endforeach;
		endif;
		
		$this->printJson(['status'=>1, 'tbody'=>$tbody]);
	}

    public function saveBulkAdvance(){
        $data = $this->input->post();
        $errorMessage = array();
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            foreach ($data['emp_id'] as $key => $value) :
                if(!empty($data['amount'][$key])):
                    $bulkData = [
                        'id' => '',
                        'type' => 1,
                        'entry_date' => $data['entry_date'],
                        'payment_mode' => $data['payment_mode'][$key],
                        'emp_id' => $value,
                        'amount' => $data['amount'][$key],
                        'reason' => $data['reason'][$key],
                        'created_by' => $this->session->userdata('loginId')
                    ];
                    $this->advanceSalary->save($bulkData);
                endif; 
            endforeach;   
            $this->printJson(['status'=>1,'message'=>'Save Bulk Advance Salary Successfully']);         
        endif;
    }
}
?>