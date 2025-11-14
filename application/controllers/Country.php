<?php
class Country extends MY_Controller{
    private $indexPage = "country/index";
    private $form = "country/form";
    private $stateIndexPage = "state/index";
    private $stateForm = "state/form";
    private $citiesIndexPage = "cities/index";
    private $citiesForm = "cities/form";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Countries";
        $this->data['headData']->controller = "country";
    }

    public function index(){
        $this->data['headData']->pageUrl = "country";
        $this->data['tableHeader'] = getMasterDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->countries->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getCountriesData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
	public function addCountries(){
        $this->load->view($this->form, $this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['name'])){
            $errorMessage['name'] = "Countries Name is required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->countries->save($data));
        endif;
    }

    public function edit(){
          $data = $this->input->post();
        $this->data['dataRow'] = $this->countries->getContries($data);

        $this->load->view($this->form, $this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->countries->delete($id));
        endif;
    }

    //state
    public function states(){
        $this->data['headData']->pageTitle = "States";
        $this->data['headData']->pageUrl = "country";
        $this->data['tableHeader'] = getMasterDtHeader('states');
        $this->load->view($this->stateIndexPage,$this->data);
    }
    
	public function getStatesDTRows(){
        $data = $this->input->post();
        $result = $this->countries->getStatesDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getStatesData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addState(){
        $this->load->view($this->stateForm, $this->data);
    }

    public function saveState(){
        $data = $this->input->post();

        $errorMessage = array();
        if(empty($data['name'])){
            $errorMessage['name'] = "State Name is required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->countries->saveStae($data));
        endif;
    }

    public function editState(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->countries->getState($data);

        $this->load->view($this->stateForm, $this->data);
    }

    public function deleteState(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->countries->deleteState($id));
        endif;
    }

    //cities
    public function cities(){
        $this->data['headData']->pageTitle = "Cities";
        $this->data['headData']->pageUrl = "country/cities";
        $this->data['tableHeader'] = getMasterDtHeader('cities');
        $this->load->view($this->citiesIndexPage,$this->data);
    }
    
	public function getCitiesDTRows(){
        $data = $this->input->post();
        $result = $this->countries->getCitiesDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getCitiesData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
	public function addCities(){
		$this->data['countryData'] = $this->party->getCountries();
        $this->load->view($this->citiesForm, $this->data);
    }
    
	public function saveCities(){
        $data = $this->input->post();
        $errorMessage = array();
		
        if(empty($data['name'])){
            $errorMessage['name'] = "Cities Name is required.";
        }
		if (empty($data['country_id'])){
			$errorMessage['country_id'] = 'Country is required.';
		}
		if (empty($data['state_id'])){
			$errorMessage['state_id'] = 'State is required.';
		}
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->countries->saveCities($data));
        endif;
    }
    
	public function editCities(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->countries->editCities($data);
		$this->data['countryData'] = $this->party->getCountries();
        $this->load->view($this->citiesForm, $this->data);
    }
    
	public function deleteCities(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->countries->deleteCities($id));
        endif;
    }
}