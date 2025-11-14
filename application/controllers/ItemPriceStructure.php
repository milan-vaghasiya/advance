<?php
class ItemPriceStructure extends MY_Controller{
    private $index = "item_price_structure/index";
    private $form = "item_price_structure/form";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Item Price Structure";
		$this->data['headData']->controller = "itemPriceStructure";
		$this->data['headData']->pageUrl = "itemPriceStructure";
    }

    public function index(){
        $this->data['tableHeader'] = getMasterDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows(){
		$data=$this->input->post();
		$result = $this->itemPriceStructure->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getItemPriceStructureData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPriceStructure(){
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['structure_name']))
            $errorMessage['structure_name'] = "Structure Name is required.";

        if(!empty($data['itemData'])):
            $mrpValues = array_sum(array_column($data['itemData'],'mrp')) + array_sum(array_column($data['itemData'],'dealer_mrp')) + array_sum(array_column($data['itemData'],'retail_mrp'));
            if($mrpValues <= 0):
                $errorMessage['item_price_error'] = "Please input at least one item price.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->itemPriceStructure->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->itemPriceStructure->getItemPriceStructure($data);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view($this->form,$this->data);
    }

    public function copyStructure(){
        $data = $this->input->post();
        $dataRow = $this->itemPriceStructure->getItemPriceStructure($data);
        $structureData  = array();
        foreach($dataRow as $row):
            unset($row->id,$row->structure_id,$row->is_defualt);
            $structureData[] = $row;
        endforeach;
        $this->data['dataRow'] = $structureData;
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->itemPriceStructure->delete($id));
        endif;
    }
}
?>