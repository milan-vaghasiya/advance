<?php
class Terms extends MY_Controller
{
    private $indexPage = "terms/index";
    private $termsForm = "terms/form";
	public $termsTypeArray = ["Purchase","Sales"];
	//public $termsTypeArray = [8=>"Purchase Order",12=>"Sales Quotation",14=>"Sales Order",55=>"Sales Enquiry"];
    
	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Terms";
		$this->data['headData']->controller = "terms";
        $this->data['headData']->pageUrl = "terms";
		
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->terms->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getTermsData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addTerms(){
        $this->data['typeArray'] = $this->TERMS_TYPES;
        $this->load->view($this->termsForm, $this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['title']))
			$errorMessage['title'] = "Title is required.";
        if(empty($data['conditions']))
			$errorMessage['conditions'] = "Conditions is required.";
        if(empty($data['type'])):
			$errorMessage['type'] = "Type is required.";
		endif;
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$data['sub_menu_id'] = $data['type'];
			$data['type'] = $this->TERMS_TYPES[$data['type']];
            $this->printJson($this->terms->save($data));
        endif;
    }

    public function edit(){     
        $data = $this->input->post();
        $this->data['dataRow'] = $this->terms->getTerm($data);
        $this->data['typeArray'] = $this->termsTypeArray;
        $this->load->view($this->termsForm, $this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->terms->delete($id));
        endif;
    }
}
?>