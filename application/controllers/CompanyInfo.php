<?php
class CompanyInfo extends MY_Controller{
    private $indexPage = "company_info";
    private $settings = "general_setting";
    private $masterSetting = "master_setting";
	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Company Info";
		$this->data['headData']->controller = "companyInfo";
        $this->data['headData']->pageUrl = "companyInfo";
	}
	
	public function index(){
        $this->data['dataRow'] = $this->masterModel->getCompanyInfo();
        $this->data['countryData'] = $this->party->getCountries();
        $this->load->view($this->indexPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
    
        if(empty($data['company_name']))
            $errorMessage['company_name'] = "Company Name is required.";

        if(empty($data['company_email']))
            $errorMessage['company_email'] = "Company Email is required.";

        if(empty($data['company_contact_person']))
            $errorMessage['company_contact_person'] = "Contact Person is required.";

        // if(empty($data['company_city_id']))
        //     $errorMessage['company_city_id'] = "City Name is required.";

        // if(empty($data['company_state_id']))
        //     $errorMessage['company_state_id'] = "State Name is required.";

        // if(empty($data['company_country_id']))
        //     $errorMessage['company_country_id'] = "Country Name is required.";

        if(empty($data['company_address']))
            $errorMessage['company_address'] = "Address is required.";
            
        if(empty($data['company_pincode']))
            $errorMessage['company_pincode'] = "Pincode is required.";

        $this->load->library('upload');
        if(!empty($_FILES['company_logo']['name'])):
            $companyLogo = "";
            $_FILES['userfile']['name']     = $_FILES['company_logo']['name'];
            $_FILES['userfile']['type']     = $_FILES['company_logo']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['company_logo']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['company_logo']['error'];
            $_FILES['userfile']['size']     = $_FILES['company_logo']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/company_logo/');
            $ext = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
            $fileName = $this->cm_id."_company_logo.".$ext;
            $config = ['file_name' => $fileName, 'allowed_types' => 'jpg|jpeg|png|gif|JPG|JPEG|PNG', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

            $this->upload->initialize($config);

            if(!$this->upload->do_upload()):
                $errorMessage['company_logo'] .= $fileName . " => " . $this->upload->display_errors();
            else:
                $uploadData = $this->upload->data();
                $data['company_logo'] = $companyLogo = $uploadData['file_name'];
            endif;

            if(!empty($errorMessage['company_logo'])):
                if (file_exists($imagePath . '/' . $companyLogo)) : unlink($imagePath . '/' . $companyLogo); endif;
            endif;
        endif;

        if(!empty($_FILES['print_header']['name'])): 
            $printHeader = "";           
            $_FILES['userfile']['name']     = $_FILES['print_header']['name'];
            $_FILES['userfile']['type']     = $_FILES['print_header']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['print_header']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['print_header']['error'];
            $_FILES['userfile']['size']     = $_FILES['print_header']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/company_logo/');
            $ext = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
            $fileName = $this->cm_id."_print_header.".$ext;
            $config = ['file_name' => $fileName, 'allowed_types' => 'jpg|jpeg|png|gif|JPG|JPEG|PNG', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

            $this->upload->initialize($config);

            if(!$this->upload->do_upload()):
                $errorMessage['print_header'] .= $fileName . " => " . $this->upload->display_errors();
            else:
                $uploadData = $this->upload->data();
                $data['print_header'] = $printHeader = $uploadData['file_name'];
            endif;

            if(!empty($errorMessage['print_header'])):
                if (file_exists($imagePath . '/' . $printHeader)) : unlink($imagePath . '/' . $printHeader); endif;
            endif;
        endif;
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->masterModel->saveCompanyInfo($data));
        endif;
    }

    public function generalSetting(){
        $this->data['dataRow'] = $this->masterModel->getGeneralSettings();
        $this->data['accountSetting'] = $this->masterModel->getAccountSettings();
        $this->load->view($this->settings,$this->data);
    }

    public function saveSettings(){
        $data = $this->input->post();
        $this->printJson($this->masterModel->saveSettings($data));
    }
}
?>