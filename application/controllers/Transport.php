<?php
class Transport extends MY_Controller{
    private $indexPage = "transport/index";
    private $formPage = "transport/form";
   
    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Transport";
		$this->data['headData']->controller = "transport";
		$this->data['headData']->pageUrl = "transport";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
		$data=$this->input->post();
		$result = $this->transport->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getTransportData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addTransport(){
        $this->load->view($this->formPage,$this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['transport_name']))
            $errorMessage['transport_name'] = "Transport Name is required.";
		if (empty($data['transport_id']))
            $errorMessage['transport_id'] = "Transport ID is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $this->printJson($this->transport->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->transport->getTransport($data);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->transport->delete($id));
        endif;
    }
    
}
?>