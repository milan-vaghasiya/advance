<?php
class CurrencyMaster extends MY_Controller{
    private $indexPage = "currency_master/index";

	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Currency Master";
		$this->data['headData']->controller = "currencyMaster";
        $this->data['headData']->pageUrl = "currencyMaster";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $data = $this->input->post(); 
        $result = $this->currencyModel->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row): 
            $row->sr_no = $i++; 

            $row->inrinput = '<input type="text" id="inrrate_'.$row->id.'" name="inrrate[]" class="form-control floatOnly" value="'.$row->inrrate.'" /><input type = "hidden"  id="id_'.$row->id.'" name=id[] value="'.$row->id.'" >' ;   
            
            $sendData[] = getCurrencyMasterData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function save(){
        $data = $this->input->post();
        $this->printJson($this->currencyModel->save($data));
    }
}
?>