<?php
class TdmStore extends MY_Controller{

	public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "ATI Desk";
		$this->data['headData']->controller = "tdmStore";
	}
	
	public function index(){
		
        $this->load->view('ati/ati_desk',$this->data);
    }
	
}
?>