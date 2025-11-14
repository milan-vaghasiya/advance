<?php
class Dashboard extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Dashboard";
		$this->data['headData']->controller = "app/dashboard";
	}
	
	public function index(){
		$this->data['headData']->appMenu = "app/dashboard";
        $this->load->view('app/dashboard',$this->data);
    }

	
}
?>