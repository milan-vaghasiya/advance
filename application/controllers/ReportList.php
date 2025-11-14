<?php
class ReportList extends MY_Controller{
    private $indexPage = "reports/index";
  
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Report List";
		$this->data['headData']->controller = "reportList";
	}
	
	public function index(){        
        $this->data['pageHeader'] ='Reports';
        $this->data['permission'] = $this->permission->getEmployeeReportMenus();
        $this->load->view($this->indexPage,$this->data);
    }
}
?>