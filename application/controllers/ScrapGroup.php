<?php
class ScrapGroup extends MY_Controller
{
    private $indexPage = "scrap_group/index";
    private $formPage = "scrap_group/form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Scrap Group";
		$this->data['headData']->controller = "scrapGroup";
		$this->data['headData']->pageUrl = "scrapGroup";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->data['item_type'] = 10;
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($item_type = 0){
        $data = $this->input->post();
        $data['item_type'] = $item_type;
        $result = $this->scrapGroup->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getScrapGroupData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addScrapGroup(){
        $data = $this->input->post();
        $this->data['item_type'] = $data['item_type'];
        $this->data['unitData'] = $this->item->itemUnits();
        $this->load->view($this->formPage,$this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['item_name']))
            $errorMessage['item_name'] = "Scrap Group Name is required.";

        if (empty($data['uom']))
            $errorMessage['uom'] = "Unit Name is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->scrapGroup->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->scrapGroup->getScrapGroup($data);
        $this->data['item_type'] = $this->data['dataRow']->item_type;
        $this->data['unitData'] = $this->item->itemUnits();
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->scrapGroup->delete($id));
        endif;
    }

  
    
}
?>