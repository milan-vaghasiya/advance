<?php
class ItemCategory extends MY_Controller{
    private $indexPage = "item_category/index";
    private $form = "item_category/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Item Category";
		$this->data['headData']->controller = "itemCategory";
        $this->data['headData']->pageUrl = "itemCategory/list";
	}

    public function list($parent_id = 0){
        $this->data['parent_id'] = $parent_id;
        $categoryData = $this->itemCategory->getCategory(['id'=>$parent_id]);
        $this->data['categoryName'] = (!empty($categoryData->category_name))?$categoryData->category_name:"Item Category";
        $this->data['ref_id'] = (!empty($categoryData))?$categoryData->ref_id:0;
        $this->data['tableHeader'] = getMasterDtHeader("itemCategory");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($parent_id = 0){
        $data = $this->input->post(); $data['parent_id'] = $parent_id;
        $result = $this->itemCategory->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row): 
            $row->sr_no = $i++;         
            $sendData[] = getItemCategoryData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addItemCategory(){
        $data = $this->input->post();
        $this->data['ref_id'] = $data['ref_id'];
        $this->data['mainCategory'] = $this->itemCategory->getCategoryList(['final_category'=>0]);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['category_name']))
            $errorMessage['category_name'] = "Category is required.";
        if(empty($data['ref_id'])):
            $errorMessage['ref_id'] = "Main Category is required.";    
        elseif($data['ref_id'] == 25):
            if(empty($data['category_code'])):
                $errorMessage['category_code'] = "Category Code is required.";
            endif;
        endif;

        $nextlevel='';
        if(!empty($data['category_level'])):
            $level = $this->itemCategory->getCategoryList(['ref_id'=>$data['ref_id']]);
            $count = count($level);
            $nextlevel = $data['category_level'].'.'.($count+1);
            $data['category_level'] = $nextlevel;
        endif;
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->itemCategory->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->itemCategory->getCategory($data);
        $this->data['mainCategory'] = $this->itemCategory->getCategoryList(['final_category'=>0]);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->itemCategory->delete($id));
        endif;
    }

}
?>