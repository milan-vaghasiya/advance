<?php
class Lead extends MY_Controller{
	private $indexPage = "lead/index";
	private $leadForm = "lead/lead_form";
    private $followupFrom = "lead/followup_form";
	private $leadStatusForm = "lead/lead_status_form";
	private $appointmentForm = 'lead/appointment_form';
	private $appointmentStatusForm = "lead/appointment_status";

    public function __construct()
	{
		parent::__construct();
		$this->data['headData']->pageTitle = "CRM DASHBOARD";
		$this->data['headData']->controller = "lead";
		$this->data['headData']->pageUrl = "lead";
        //$this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'lead']);
	}

    public function index(){
		$this->data['tableHeader'] = getSalesDtHeader("lead");
		$this->load->view($this->indexPage, $this->data);
	}
	
    public function getDTRows($lead_status = 0)	{
		$data = $this->input->post();
		$data['lead_status'] = $lead_status;
		$result = $this->leads->getDTRows($data);
		$sendData = array();
		$i = ($data['start'] + 1);
		foreach ($result['data'] as $row):
			$row->sr_no = $i++;
			$row->controller = $this->data['headData']->controller;

			$row->appointments = '';
			$row->followupDate = '';
			$row->followupNote = '';
			$followupData = $this->leads->getFollowupData(['entry_type' => 1, 'lead_id' => $row->id]);
			if(!empty($followupData)):
				$row->followupDate = formatDate($followupData->appointment_date);
				$row->next_fup_date = formatDate($followupData->next_fup_date);
				$row->followupNote =$followupData->notes;
			endif;

			if ($row->lead_status == 0 || $row->lead_status == 1):
				$appointsData = $this->leads->getAppointments(['entry_type' => 2, 'lead_id' => $row->id, 'status' => 0]);
				if (!empty($appointsData)):
					$apArray = [];
					foreach ($appointsData as $ap):
						$style = "";
						if (date('Y-m-d H:i:s') >= date("Y-m-d H:i:s", strtotime($ap->appointment_date . ' ' . $ap->appointment_time . ' -24 Hours'))):
							$style = 'text-danger';
						endif;

						$appoParam = "{'postData' : {'id' : ".$ap->id."}, 'modal_id' : 'modal-md', 'form_id' : 'appointmentStatus', 'title' : 'Appointment Status','fnsave':'saveAppointmentStatus','fnedit':'appointmentStatus'}";

						$apArray[] = '<a href="javascript:void(0)" class="' . $style . '" onclick="edit('.$appoParam.');">' . formatDate($ap->appointment_date, 'd-m-Y ') . formatDate($ap->appointment_time, 'H:i A') . '</a>';
					endforeach;

					$row->appointments = implode("<hr style='margin-top:0px;margin-bottom:0px'>", $apArray);
				endif;
			endif;
			$sendData[] = getLeadData($row);
		endforeach;
		$result['data'] = $sendData;
		$this->printJson($result);
	}

    public function addLead(){
        $this->data['entry_type'] = $this->data['entryData']->id;
		$this->data['categoryList'] = $this->itemCategory->getCategoryList(['ref_id'=>0,'final_category'=>1]);
        $this->data['salesExecutives'] = $this->employee->getEmployeeList();
		$this->load->view($this->leadForm, $this->data);
	}
	
    public function save(){
        $data = $this->input->post();
		$errorMessage = array();
		if (empty($data['lead_date']))
			$errorMessage['lead_date'] = "Date is required.";
		if (empty($data['mode']))
			$errorMessage['mode'] = "Mode is required.";
		if (empty($data['party_name']))
			$errorMessage['party_name'] = "Customer is required."; // Updated By :- Sweta @05-09-2023
		if (empty($data['sales_executive']))
			$errorMessage['sales_executive'] = "Sales Executive is required.";

		if (!empty($errorMessage)):
			$this->printJson(['status' => 0, 'message' => $errorMessage]);
		else:
			$result = $this->leads->saveLead($data);
			$this->printJson($result);
		endif;
    }

    public function edit(){
		$id = $this->input->post('id');
		$leadData = $this->leads->getLead($id);
        $this->data['salesExecutives'] = $this->employee->getEmployeeList();		
		$this->data['dataRow'] = $leadData;
		$this->load->view($this->leadForm, $this->data);
	}

    public function addFollowup(){
        $data = $this->input->post();
		$this->data['lead_id'] = $data['id'];
		$this->data['entry_type'] = $data['entry_type'];
		$this->data['party_id'] = $data['party_id'];
		$this->data['sales_executive'] = $data['sales_executive'];
		$this->data['salesExecutives'] = $this->employee->getEmployeeList();
		
		$this->load->view($this->followupFrom, $this->data);
    }

    public function followupListHtml($data=array())	{
        $data = $this->input->post();
		$appintmentData = $this->leads->getAppointments($data);
		$html = '';
		if (!empty($appintmentData)):
			$i = 1;
			foreach ($appintmentData as $row):
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Followup','fndelete':'deleteApproachTans','res_function':'resTrashFollowup','controller':'lead'}";

				$deleteBtn = '<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-sm btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="mdi mdi-trash-can-outline"></i></button>';
				$html.='<tr>
					<td clas="text-center">'.$i++.'</td>
					<td clas="text-center">'.formatDate($row->appointment_date,'d-m-Y ').'</td>
					<td>'.$this->appointmentMode[$row->mode].'</td>
					<td>'.$row->executive_name.'</td>
					<!-- <td>'.$row->contact_person.'</td> -->
					<td>'.$row->notes.'</td>
					<td >'.$deleteBtn.'</td>
				</tr>';
			endforeach;
		else:
			$html = '<tr><th colspan="7" class="text-center">No data available.</th></tr>';
		endif;

		$this->printJson(['status'=>1,"tbodyData"=>$html]);
	}

    public function saveFollowup()
	{
		$data = $this->input->post();
		$errorMessage = array();
		if (empty($data['appointment_date'])) {
			$errorMessage['appointment_date'] = "Date is required.";
		}
		if (empty($data['mode'])) {
			$errorMessage['mode'] = "Mode is required.";
		}
		/*if (empty($data['sales_executive'])) {
			$errorMessage['sales_executive'] = "Sales Executive is required.";
		}*/
		if (!empty($errorMessage)):
			$this->printJson(['status' => 0, 'message' => $errorMessage]);
		else:
			$result = $this->leads->saveFollowup($data);
			$this->printJson($result);
		endif;
	}

    public function addAppointment(){
        $data = $this->input->post();
		$data['entry_type'] = 2;
		$this->data['lead_id'] = $data['id'];
		$this->data['appointmentMode'] = $this->appointmentMode;
		$this->load->view($this->appointmentForm, $this->data);
    }

    public function saveAppointment()	{
		$data = $this->input->post();
		$errorMessage = array();
		if (empty($data['appointment_date']))
			$errorMessage['appointment_date'] = "Date is required.";
		if (empty($data['appointment_time']))
			$errorMessage['appointment_time'] = "Time is required.";
		if (empty($data['contact_person']))
			$errorMessage['contact_person'] = "Contact Person is required.";
		if (empty($data['mode']))
			$errorMessage['mode'] = "Mode is required.";

		if (!empty($errorMessage)):
			$this->printJson(['status' => 0, 'message' => $errorMessage]);
		else:
			$leadData = $this->leads->getLead($data['lead_id']); 
			$data['sales_executive'] = $leadData->sales_executive;
			$data['contact_person'] = ucwords($data['contact_person']);
			$data['appointment_date'] = formatDate($data['appointment_date'], 'Y-m-d');
			$data['appointment_time'] = formatDate($data['appointment_time'], 'H:i:s');
			$result = $this->leads->setAppointment($data);
			$this->printJson($result);
		endif;
	}

	public function deleteApproachTans(){
		$data = $this->input->post();
		if (empty($data['id'])):
			$this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
		else:
			$result = $this->leads->deleteApproachTans($data['id']);
			$this->printJson($result);
		endif;
	}

    public function appointmentListHtml($data=array())	{
		$data = $this->input->post();
        $this->leads->getAppointments($data);
		$appointmentData = $this->leads->getAppointments($data);
		$html = '';
		if (!empty($appointmentData)):
			$i = 1;
			foreach ($appointmentData as $row):
				$deleteBtn = '';
				if (empty($row->status)):
					$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Appointment','fndelete':'deleteApproachTans','res_function':'resTrashAppointment'}";

					$deleteBtn = '<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>';
				endif;

				$html .= '<tr>
					<td clas="text-center">' . $i++ . '</td>
					<td clas="text-center">' . formatDate($row->appointment_date, 'd-m-Y ') . formatDate($row->appointment_time, 'H:i A') . '</td>
					<td>' . $this->appointmentMode[$row->mode] . '</td>
					<td>' . $row->contact_person . '</td>
					<td>' . $row->purpose . '</td>
					<td clas="text-center">' . $deleteBtn . '</td>
				</tr>';
			endforeach;
		else:
			$html = '<tr><th colspan="6" class="text-center">No data available.</th></tr>';
		endif;
		$this->printJson(['status'=>1,"tbodyData"=>$html]);
	}

	public function appointmentStatus(){
		$data = $this->input->post();
		$this->data['appointmentMode'] = $this->appointmentMode;
		$this->data['appointmentData'] = $this->leads->getAppointmentDetail($data['id']);
		$this->load->view($this->appointmentStatusForm, $this->data);
	}

	public function saveAppointmentStatus(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['notes']))
			$errorMessage['notes'] = "Reason is required.";


		if (!empty($errorMessage)):
			$this->printJson(['status' => 0, 'message' => $errorMessage]);
		else:
			$this->printJson($this->leads->setAppointment($data));
		endif;
	}
	
	public function approachStatus(){
		$data = $this->input->post();
		$this->data['lead_id'] = $data['id'];
		$this->data['entry_type'] = (!empty($data['entry_type']))?$data['entry_type']:0;
		$this->load->view($this->leadStatusForm,$this->data);
	}

	public function saveApproachStatus(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['lead_status'])):
			$errorMessage['lead_status'] = "Status is required.";
		else:
			if(empty($data['reason']) && $data['lead_status'] == 4):
				$errorMessage['reason'] = "Reason is required.";
			endif;
		endif;

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->leads->saveApproachStatus($data));
		endif;
	}

	/* Created By :- Sweta @05-09-2023 */
	public function partySearch(){
		$this->printJson($this->party->partySearch());
	}

	/* Created By :- Sweta @05-09-2023 */
	public function getPartyData(){
        $data = $this->input->post();
        $partyData = $this->party->getParty(['party_name'=>$data['party_name']]);
        $options = "Select Sales Executive";
		if(!empty($partyData->sales_executive)){
			$salesExecutives = $this->employee->getEmployeeList();
			
			foreach($salesExecutives as $row){
				$selected = (!empty($partyData->sales_executive) && $partyData->sales_executive == $row->id)?'selected':(($this->loginId == $row->id)?'selected':'');
				$options .= '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.' </option>';
			}
		}		
		$contact_person = (!empty($partyData->contact_person)?$partyData->contact_person:'');
		$contact_no = (!empty($partyData->contact_no)?$partyData->contact_no:'');
		$this->printJson(['status'=>1,'sales_executive'=>$options,'contact_person'=>$contact_person,'contact_no'=>$contact_no]);
    }
}
?>