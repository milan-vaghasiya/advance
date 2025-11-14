<?php
class MaintenanceReport extends MY_Controller
{
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Maintenance Report";
		$this->data['headData']->controller = "reports/maintenanceReport";
	}

    /* Machines Report */
	public function machineReport(){
        $this->data['headData']->pageUrl = "reports/maintenanceReport/machineReport";
		$this->data['headData']->pageTitle = "MACHINES REPORT";
        $this->data['pageHeader'] = 'MACHINES REPORT';
        $this->data['machineData'] = $this->item->getItemList(['item_type'=>5]);
        $this->load->view("reports/maintenance_report/machine_report",$this->data);
    }
    
    /* Maintenance Log Book */
    public function maintenanceLog(){
        $this->data['headData']->pageUrl = "reports/maintenanceReport/maintenanceLog";
		$this->data['headData']->pageTitle = "MAINTENANCE LOG BOOK REPORT";
        $this->data['pageHeader'] = 'MAINTENANCE LOG BOOK REPORT';
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->load->view('reports/maintenance_report/maintenance_log',$this->data);
    }

    public function getMaintenanceLogData(){
        $data = $this->input->post();
        $errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['multi_row'] = 1;
            $mlogData = $this->machineBreakdown->getMachineBreakdown($data);
            $tbody = '';
            if(!empty($mlogData)):
                $i=1; 
                foreach($mlogData as $row):
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td>'.(!empty($row->prc_number) ? $row->prc_number : '').'</td>
                        <td>'.$row->item_code.'</td>
                        <td>'.date("d-m-Y H:i:s",strtotime($row->trans_date)).'</td>
                        <td>'.date("d-m-Y H:i:s",strtotime($row->end_date)).'</td>
                        <td>'.$row->idle_reason.'</td>
                        <td>'.$row->solution.'</td>
                    </tr>';
                endforeach;
            endif;            
            $this->printJson(['status'=>1,'tbody'=>$tbody]);
        endif;
    }

    public function printMaintenanceLog($pdate){
        $data['from_date'] = explode('~',$pdate)[0];
        $data['to_date'] = explode('~',$pdate)[1];
        $data['multi_row'] = 1;
        $mlogData = $this->machineBreakdown->getMachineBreakdown($data); 

        $tbody = '';
        if(!empty($mlogData)):
            $i=1; 
            foreach($mlogData as $row):
                $tbody .= '<tr class="text-center">
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.(!empty($row->prc_number) ? $row->prc_number : '').'</td>
                    <td>'.$row->item_code.'</td>
                    <td>'.date("d-m-Y H:i:s",strtotime($row->trans_date)).'</td>
                    <td>'.date("d-m-Y H:i:s",strtotime($row->end_date)).'</td>
                    <td>'.$row->idle_reason.'</td>
                    <td>'.$row->solution.'</td>
                </tr>';
            endforeach;
        endif;   
        
        $logo = base_url('assets/images/logo.png');
		
		$topSectionO ='<table class="table">
						<tr>
							<td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-center" style="font-size:1rem;width:60%">Maintenance Log Book Data</td>
                            <td style="width:20%;"></td>
						</tr>
					</table>';
        $itemList = '<table id="reportTable" class="table table-bordered align-items-center itemList">
                        <thead class="thead-info" id="theadData">
                            <tr class="text-center">
                                <th rowspan="2">#</th>
                                <th rowspan="2">Date</th>
                                <th rowspan="2">PRC No.</th>
                                <th rowspan="2">Machine No.</th>
                                <th colspan="2">Time</th>
                                <th rowspan="2">Idle Reason</th>
                                <th rowspan="2">Solution</th>
                            </tr>
                            <tr class="text-center">
                                <th>From</th>
                                <th>To</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyData">
                            '.$tbody.'
                        </tbody>
                    </table>';

	    $originalCopy = '<div style="">'.$topSectionO.$itemList.'</div>';
		
		$pdfData = $originalCopy;
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='maintenance_log_book.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('L','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }
    
    /* Preventive Maintenance Plan Print */
    public function printPrevMaintenance(){
        $logo=base_url('assets/images/logo.png');
		$topSectionO ='<table class="table">
						<tr>
							<td style="width:20%;"><img src="'.$logo.'" style="height:50px;"></td>
							<td class="org_title text-center" style="font-size:1.2rem;width:60%;text-align:center;">PREVENTIVE MAINTENANCE PLAN</td>
                            <td style="width:20%;"></td>
                        </tr>
					</table>';
        $itemList='';
        $machineData = $this->item->getItemList(['item_type'=>5]); 
        foreach($machineData as $machine):    
            $prevData = $this->activities->getmaintenanceData(['machine_id'=>$machine->id]); $i=1;

            if(!empty($prevData)){
                $itemList.='<table class="table table-bordered align-items-center itemList">
                    <thead class="thead-info">
                        <tr class="text-left" style="font-size:1rem;"><th colspan="7">Machine: '.(!empty($machine->item_code)? '['.$machine->item_code.'] '.$machine->item_name:$machine->item_name).'</th></tr>
                        <tr class="text-center">
                            <th rowspan="2" style="width:5%">#</th>
                            <th rowspan="2" style="width:60%">Activities to be carried out</th>
                            <th colspan="5">Checking Frequency</th>
                        </tr>
                        <tr class="text-center">
                            <th style="width:7%">Yearly</th>
                            <th style="width:7%">Half Yearly</th>
                            <th style="width:7%">Quarterly</th>
                            <th style="width:7%">Monthly</th>
                            <th style="width:7%">Daily</th>
                        </tr>
                    </thead>
                <tbody>';
                foreach($prevData as $row):
                    $daily=''; $yearly=''; $halfyearly=''; $quarterly=''; $monthly='';
                    if(!empty($row->checking_frequancy) && $row->checking_frequancy == 'Yearly'){
                        $yearly = '<img src="'.base_url('assets/uploads/check/check.jpg').'" width="30" height="30" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                    }
                    if(!empty($row->checking_frequancy) && $row->checking_frequancy == 'Half Yearly'){
                        $halfyearly = '<img src="'.base_url('assets/uploads/check/check.jpg').'" width="30" height="30" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                    }
                    if(!empty($row->checking_frequancy) && $row->checking_frequancy == 'Quarterly'){
                        $quarterly = '<img src="'.base_url('assets/uploads/check/check.jpg').'" width="30" height="30" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                    }
                    if(!empty($row->checking_frequancy) && $row->checking_frequancy == 'Monthly'){
                        $monthly = '<img src="'.base_url('assets/uploads/check/check.jpg').'" width="30" height="30" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                    }
                    if(!empty($row->checking_frequancy) && $row->checking_frequancy == 'Daily'){
                        $daily = '<img src="'.base_url('assets/uploads/check/check.jpg').'" width="30" height="30" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                    }

                    $itemList.='<tr>
                        <td class="text-center">'.$i++.'</td>
                        <td class="text-left">'.$row->activities.'</td>
                        <td class="text-center">'.$yearly.'</td>
                        <td class="text-center">'.$halfyearly.'</td>
                        <td class="text-center">'.$quarterly.'</td>
                        <td class="text-center">'.$monthly.'</td>
                        <td class="text-center">'.$daily.'</td>
                    </tr>';
                endforeach;
                $itemList.= '</tbody></table>';
            }
        endforeach;

	    $originalCopy = '<div style="">'.$topSectionO.$itemList.'</div>';
		
		$pdfData = $originalCopy;
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='prev_maintenance_plan.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('L','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }

	/* Breakdown Maintenance Register */
    public function maintenanceBreakdown() {        
        $this->data['headData']->pageUrl = "reports/maintenanceReport/maintenanceBreakdown";
		$this->data['headData']->pageTitle = "BREAKDOWN MAINTENANCE REGISTER";
        $this->data['pageHeader'] = 'BREAKDOWN MAINTENANCE REGISTER';
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->load->view('reports/maintenance_report/maintenance_breakdown',$this->data);
    }

    public function getMaintenanceBreakdownData() {
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['multi_row'] = 1;
            $ticketData = $this->machineBreakdown->getMachineBreakdown($data); 
            $tbody=""; $i=1;
            if(!empty($ticketData)){
                foreach($ticketData as $row){

                    $startTime = new DateTime($row->trans_date);
                    $endTime = new DateTime($row->end_date);
                    $downTime = $startTime->diff($endTime);

                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->trans_number.'</td>
                        <td>'.formatDate($row->trans_date,'d-m-Y H:i:s').'</td>
                        <td>'.(!empty($row->end_date)?formatDate($row->end_date,'d-m-Y H:i:s'):'').'</td>
                        <td>'.$row->prc_number.'</td>
                        <td>'.$row->part_code.'</td>
                        <td>'.$row->item_code.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->idle_reason.'</td>
                        <td>'.$row->solution.'</td>
                        <td>'.($downTime->d).'</td>
                        <td>'.($downTime->h).'</td>
                        <td>'.(($downTime->d * 11) + $downTime->h).'</td>
                        <td>'.($downTime->m).'</td>
                    </tr>';
                }
            }
            $this->printJson(['status'=>1,'tbody'=>$tbody]);
        endif;
    }
}
?>