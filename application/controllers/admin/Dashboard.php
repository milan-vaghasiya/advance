<?php
class Dashboard extends MY_AdminController{

	public function __construct()	{
		parent::__construct();
		$this->data['headData']->pageTitle = "Dashboard";
		$this->data['headData']->controller = "dashboard";
	}
	
	public function index(){	    
        $this->load->view('admin/dashboard',$this->data);
    }
}
?>