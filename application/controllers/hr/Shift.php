<?php
class Shift extends MY_Controller{
    private $indexPage = "hr/shift/index";
    private $shiftForm = "hr/shift/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Shift";
		$this->data['headData']->controller = "hr/shift";
	}

    public function index(){
        $this->data['tableHeader'] = getHrDtHeader('shift');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->shiftModel->getDTRows($data);	
        $sendData = array();$i=($data['start'] + 1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;         
            $sendData[] = getShiftData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addShift(){
        $this->load->view($this->shiftForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['shift_name']))
			$errorMessage['shift_name'] = "Shift Name is required.";
        if(empty($data['shift_start']))
			$errorMessage['shift_start'] = "Shift Start Time is required.";
        if(empty($data['shift_end']))
            $errorMessage['shift_end'] = "Shift End Time is required.";
        if(empty($data['lunch_start']))
            $errorMessage['lunch_start'] = "Lunch Start Time is required.";
        if(empty($data['lunch_end']))
            $errorMessage['lunch_end'] = "Lunch End Time is required.";

        $shiftStart = new DateTime(date('H:i:s',strtotime(date('Y-m-d').' '.$data['shift_start'])));
        $shiftEnd = new DateTime(date('H:i:s',strtotime(date('Y-m-d').' '.$data['shift_end'])));

        if($shiftEnd < $shiftStart){$shiftEnd->modify('+1 day');}

        $totalShift = $shiftStart->diff($shiftEnd);
        $data['total_shift_time'] = $totalShift->format('%H:%I');
        $totalSM = (intVal($totalShift->format('%H')) * 60) + intVal($totalShift->format('%I'));

        $lunchStart = new DateTime(date('H:i:s',strtotime($data['lunch_start'])));
        $lunchEnd = new DateTime(date('H:i:s',strtotime($data['lunch_end'])));
        $totalLunch = $lunchStart->diff($lunchEnd);
        $data['total_lunch_time'] = $totalLunch->format('%H:%I');
        $totalLM = (intVal($totalLunch->format('%H')) * 60) + intVal($totalLunch->format('%I'));
		$totalDM = $totalSM - $totalLM;
		
		$pHour = intVal($totalDM / 60);
		$pMinute = intVal($totalDM % 60);
        $data['production_hour'] = str_pad($pHour, 2, '0', STR_PAD_LEFT).':'.str_pad($pMinute, 2, '0', STR_PAD_LEFT);

        if($data['production_hour'] > 24)
            $errorMessage['general_error'] = "Invalid Shift Time.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->shiftModel->save($data));
        endif;
    }

    public function edit(){     
        $data = $this->input->post();
        $this->data['dataRow'] = $this->shiftModel->getShift($data);
        $this->load->view($this->shiftForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->shiftModel->delete($id));
        endif;
    }
}
?>