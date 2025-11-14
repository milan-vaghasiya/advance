<?php
class EmpLoan extends MY_Controller
{
	private $indexpage = "hr/emp_loan/index";
    private $form = "hr/emp_loan/form";
    private $loan_senction = "hr/emp_loan/loan_senction";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "User Loan";
		$this->data['headData']->controller = "hr/empLoan";
        $this->data['headData']->pageUrl = "hr/empLoan";
	}

    //view table
	public function index(){    
        $this->data['tableHeader'] = getHrDtHeader('empLoan');
        $this->load->view($this->indexpage,$this->data);
    }

    public function getDTRows($trans_status =0){
        $data = $this->input->post(); $data['trans_status'] = $trans_status;
        $result = $this->empLoan->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getEmpLoanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addLoan()
    {
        $this->data['empData'] = $this->empLoan->getEmployeeList();
        // $this->data['ledgerData'] = $this->party->getPartyListOnGroupCode(['"BA"','"CS"']);
        $this->load->view($this->form,$this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee is required.";
        //if(empty($data['vou_acc_id']))
            //$errorMessage['vou_acc_id'] = "Ledger is required.";
        if(empty($data['payment_mode']))
            $errorMessage['payment_mode'] = "Payment Mode is required.";
        if(empty($data['demand_amount']))
            $errorMessage['demand_amount'] = "Amount is required.";
        if(empty($data['reason']))
            $errorMessage['reason'] = "Reason is required.";
        if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $trans_no = $this->transMainModel->nextTransNo(22);
            // $trans_prefix = $this->transMainModel->getTransPrefix(22);
            unset($data['empSelect']);
            $masterData = [ 
				'id' => $data['id'],
				'entry_date' => date('Y-m-d',strtotime($data['entry_date'])),
                'payment_mode'=>$data['payment_mode'],
                'vou_acc_id'=>$data['vou_acc_id'],
				'emp_id' => $data['emp_id'],
				'demand_amount' => $data['demand_amount'],
				'total_emi' => $data['total_emi'],
				'emi_amount' => $data['emi_amount'],
				'reason' => $data['reason'],
                'created_by' => $this->session->userdata('loginId'),
			];
            $this->printJson($this->empLoan->save($masterData));
        endif;
    }
    
    public function edit()
    {
        $id = $this->input->post('id'); 
        $this->data['approve_type'] = $this->input->post('approve_type'); 
        $this->data['dataRow'] = $this->empLoan->getEmpLoan($id);
        $this->data['empData'] = $this->empLoan->getEmployeeList();
        // $this->data['ledgerData'] = $this->party->getPartyListOnGroupCode(['"BA"','"CS"']);
        $this->load->view($this->form,$this->data);
    }
    
    public function delete()
    {
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->empLoan->delete($id));
        endif;
    }
    function printLoan($id){
		$this->data['loanData'] = $loanData = $this->empLoan->getEmpLoan($id);
		$this->data['companyData'] = $this->empLoan->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		$pdfData = $this->load->view('hr/emp_loan/printLoan',$this->data,true);
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;" rowspan="3"></td>
							<th colspan="2">For, '.$this->data['companyData']->company_name.'</th>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"></td>
							<td style="width:25%;" class="text-center">Authorised By</td>
						</tr>
					</table>';
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='Loan'.$loanData->trans_number.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->AddPage('P','','','','',5,5,35,10,3,3,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

    public function saveLoanApproval(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee is required.";
        //if(empty($data['vou_acc_id']))
            //$errorMessage['vou_acc_id'] = "Ledger is required.";
        if(empty($data['payment_mode']))
            $errorMessage['payment_mode'] = "Payment Mode is required.";
        if(empty($data['approved_amount']))
            $errorMessage['approved_amount'] = "Amount is required.";
        if(empty($data['reason']))
            $errorMessage['reason'] = "Reason is required.";
        if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
           $data['approved_by'] = $this->loginId;
           $data['approved_at'] = date("Y-m-d h:i:s");
           $data['trans_status'] = 1;
        $this->printJson($this->empLoan->save($data));
        endif;
    }

    public function loanSenction(){
        $id = $this->input->post('id'); 
        $this->data['dataRow'] = $this->empLoan->getEmpLoan($id);
        $this->load->view($this->loan_senction,$this->data);
    }

    public function saveLoanSenction(){
        $data = $this->input->post();
        
        $data['sanctioned_by'] = $this->loginId;
        $data['sanctioned_at'] = date("Y-m-d h:i:s");
        $data['trans_status'] = 2;
        $this->printJson($this->empLoan->save($data));
       
    }
}
?>