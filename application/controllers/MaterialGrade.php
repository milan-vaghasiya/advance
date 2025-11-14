<?php 
class MaterialGrade extends MY_Controller
{
    private $indexPage = "material_grade/index";
    private $materialForm = "material_grade/form";
    private $inspection_param = "material_grade/inspection";
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Material Grade";
		$this->data['headData']->controller = "materialGrade";
        $this->data['headData']->pageUrl = "materialGrade";
	}
	 
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->materialGrade->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getMaterialData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMaterialGrade(){
        $this->data['scrapData'] = $this->item->getItemList(['item_type'=>10]);
        $this->data['tcHeadList'] = $this->testType->getTypeList();
        $this->load->view($this->materialForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['material_grade']))
			$errorMessage['material_grade'] = "Material Grade is required.";
        if(empty($data['scrap_group']))
			$errorMessage['scrap_group'] = "Scrap Group is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['tc_head'] = (!empty($data['tc_head']))?implode(",",$data['tc_head']):'';
            // $data['scrap_group'] = implode(",",$data['scrap_group']); 
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->materialGrade->save($data));
        endif;
    }

    public function edit(){     
        $data = $this->input->post();
        $this->data['dataRow'] = $this->materialGrade->getMaterial($data);
        $this->data['scrapData'] = $this->item->getItemList(['item_type'=>10]);
        $this->data['tcHeadList'] = $this->testType->getTypeList();
        $this->load->view($this->materialForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->materialGrade->delete($id));
        endif;
    }

    public function standardSearch(){
		$this->printJson($this->materialGrade->standardSearch());
	}

	public function colorCodeSearch(){
		$this->printJson($this->materialGrade->colorCodeSearch());
	}

     /*TC Parameter Details*/
     public function getInspectionParam(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->materialGrade->getMaterial($data);
        $this->data['tcHeadList'] = (!empty($this->data['dataRow']->tc_head))?$this->testType->getTestParameter(['test_type'=>$this->data['dataRow']->tc_head]):[];
        $this->data['tcData'] = $this->materialGrade->getTcMasterData(['grade_id'=>$data['id']]);
		$this->data['approve_by'] = (!empty($data['approve_by'])) ? $this->loginId : 0;
        $this->load->view($this->inspection_param,$this->data);
    }

    public function saveInspectionParam(){
        $data = $this->input->post(); 
        $errorMessage = array();

        if(empty($data['test_type']))
			$errorMessage['generalError'] = "Material Specification is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $headArray = [] ;
            foreach($data['test_type'] as $k=>$test_type){
                    $json=[];
                    foreach($data['param'][$test_type] as $key=>$value){
                        $json[str_replace(" ","",$data['param'][$test_type][$key]['param'])]=$data['param'][$test_type][$key];
                    }
                    $headArray[]=[
                        'id'=>$data['id'][$k],
                        'ins_type'=>$data['ins_type'][$k],
                        'test_type'=>$test_type,
                        'parameter'=>json_encode($json),
                    ];
            }
            $tcData = [
                'grade_id'=>$data['grade_id'],
				'item_id'=>(!empty($data['item_id']) ? $data['item_id'] : 0),
				'approve_by'=>(!empty($data['approve_by']) ? $data['approve_by'] : 0),
                'headData'=>$headArray,
            ];
            $this->printJson($this->materialGrade->saveInspectionParam($tcData));
        endif;
    }

    public function reOpenTcParam(){
		$data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->materialGrade->reOpenTcParam($data));
		endif;
	}
}
?>