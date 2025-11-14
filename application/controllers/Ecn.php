<?php
class Ecn extends My_COntroller{

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "ECN";
		$this->data['headData']->controller = "ecn";
	}

    /** Check List Master */
        public function ecnCheckList(){
            $this->data['headData']->pageTitle = "ECN Check List";
            $this->data['headData']->pageUrl = "ecn/ecnCheckList";
            $this->data['tableHeader'] = getMasterDtHeader('ecnCheckList');
            $this->load->view('ecn/checklist_index',$this->data);
        }
        
        public function getCheckListDTRows(){
            $data = $this->input->post();
            $result = $this->ecn->getCheckListDTRows($data);
            $sendData = array();$i=($data['start']+1);
            foreach($result['data'] as $row):          
                $row->sr_no = $i++;
                $sendData[] = getEcnCheckListData($row);
            endforeach;
            $result['data'] = $sendData;
            $this->printJson($result);
        }
        
        public function addCheckList(){
            $this->load->view('ecn/checklist_form',$this->data);
        }
        
        public function savecheckList(){
            $data = $this->input->post();
            $errorMessage = array();
            if(empty($data['description']))
                $errorMessage['description'] = "Description Required.";

            if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
            else:
                $this->printJson($this->ecn->savecheckList($data));
            endif;
        }

        public function editCheckList(){
            $data = $this->input->post();
            $this->data['dataRow'] = $this->ecn->getCheckList(['id'=>$data['id'],'single_row'=>1]);
            $this->load->view('ecn/checklist_form',$this->data);
        }

        public function deleteCheckList(){
            $id = $this->input->post('id');
            if(empty($id)):
                $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
            else:
                $this->printJson($this->ecn->deleteCheckList($id));
            endif;
        }
    /** End Master */

    /** ECN */
        public function index(){
            $this->data['headData']->pageTitle = "ECN";
            $this->data['headData']->controller = "ecn";
            $this->data['headData']->pageUrl = "ecn";
            $this->data['tableHeader'] = getMasterDtHeader('ecn');
            $this->load->view('ecn/index',$this->data);
        }

        public function getDTRows($status=0){    
            $data = $this->input->post();$data['status'] = $status;
            $result = $this->ecn->getDTRows($data);
            $sendData = array();$i=($data['start']+1);
            foreach($result['data'] as $row):          
                $row->sr_no = $i++;
                $row->ecn_type_lbl = $this->ECN_TYPES[$row->ecn_type];
                $sendData[] = getEcnData($row);
            endforeach;
            $result['data'] = $sendData;
            $this->printJson($result);
        }

        public function addEcn(){
            $this->data['itemData'] = $this->item->getItemList(['item_type'=>1]);
            $this->data['empList'] = $this->employee->getEmployeeList();
            $this->load->view('ecn/form',$this->data);
        }

        public function saveEcn(){
            $data = $this->input->post();
            $errorMessage = array();
            if(empty($data['item_id'])){ $errorMessage['item_id'] = "Item Name is required."; }

            if($data['ecn_no'] == ""){ $errorMessage['ecn_no'] = "ECN No. is required."; }

            if(empty($data['ecn_date'])){ 
                $errorMessage['ecn_date'] = "ECN Date is required."; }
            else{
                if (($data['ecn_date'] < $this->startYearDate) OR ($data['ecn_date'] > $this->endYearDate)){
                    $errorMessage['ecn_date'] = "Invalid Date (Out of Financial Year).";
                }
            }

            if(empty($data['drw_no'])){ $errorMessage['drw_no'] = "Drawing No. is required."; }

            if(empty($data['cust_rev_no'])){ $errorMessage['cust_rev_no'] = "Cust. Rev. No. is required."; }

            if(empty($data['cust_rev_date'])){ $errorMessage['cust_rev_date'] = "Cust. Rev. Date is required."; }

            if($data['rev_no'] == ""){  $errorMessage['rev_no'] = "Revision No. is required."; }

            if(empty($data['rev_date'])){ $errorMessage['rev_date'] = "Revision Date is required.";  }
            
            if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
            else:
				   if(isset($_FILES['company_drg']['name'])):
                    if($_FILES['company_drg']['name'] != null || !empty($_FILES['company_drg']['name'])):
                        $this->load->library('upload');
                        $_FILES['userfile']['name']     = $_FILES['company_drg']['name'];
                        $_FILES['userfile']['type']     = $_FILES['company_drg']['type'];
                        $_FILES['userfile']['tmp_name'] = $_FILES['company_drg']['tmp_name'];
                        $_FILES['userfile']['error']    = $_FILES['company_drg']['error'];
                        $_FILES['userfile']['size']     = $_FILES['company_drg']['size'];
                        
                        $imagePath = realpath(APPPATH . '../assets/uploads/ecn/');
                        $config = ['file_name' => 'ecn-'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];
    
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload()):
                            $errorMessage['company_drg'] = $this->upload->display_errors();
                            $this->printJson(["status"=>0,"message"=>$errorMessage]);
                        else:
                            $uploadData = $this->upload->data();
                            $data['company_drg'] = $uploadData['file_name'];
                        endif;
                    endif;
                endif;

                if(isset($_FILES['customer_drg']['name'])):
                    if($_FILES['customer_drg']['name'] != null || !empty($_FILES['customer_drg']['name'])):
                        $this->load->library('upload');
                        $_FILES['userfile']['name']     = $_FILES['customer_drg']['name'];
                        $_FILES['userfile']['type']     = $_FILES['customer_drg']['type'];
                        $_FILES['userfile']['tmp_name'] = $_FILES['customer_drg']['tmp_name'];
                        $_FILES['userfile']['error']    = $_FILES['customer_drg']['error'];
                        $_FILES['userfile']['size']     = $_FILES['customer_drg']['size'];
                        
                        $imagePath = realpath(APPPATH . '../assets/uploads/ecn/');
                        $config = ['file_name' => 'ecn-'.time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];
    
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload()):
                            $errorMessage['customer_drg'] = $this->upload->display_errors();
                            $this->printJson(["status"=>0,"message"=>$errorMessage]);
                        else:
                            $uploadData = $this->upload->data();
                            $data['customer_drg'] = $uploadData['file_name'];
                        endif;
                    endif;
                endif;
                $data['core_team'] = (!empty($data['core_team'])?implode(",",$data['core_team']):'');
                $this->printJson($this->ecn->saveEcn($data));
            endif;
        }

        public function editEcn(){
            $data = $this->input->post();
            $this->data['dataRow'] = $this->ecn->getEcn(['id'=>$data['id'],'single_row'=>1]);
            $this->data['itemData'] = $this->item->getItemList(['item_type'=>1]);
            $this->data['empList'] = $this->employee->getEmployeeList();
            $this->load->view('ecn/form',$this->data);
        }
    
        public function deleteEcn(){
            $id = $this->input->post('id');
            if(empty($id)):
                $this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
            else:
                $this->printJson($this->ecn->deleteEcn($id));
            endif;
        }

        public function ecnEffect(){
            $ecn_id = $this->input->post('id');
            $this->data['ecn_id'] = $ecn_id;
            $this->data['dataRow'] = $this->ecn->getEcnEffectDetail(['ecn_id'=> $ecn_id]);
            $this->data['empData'] = $this->employee->getEmployeeList();
            $this->data['checkList'] = $this->ecn->getCheckList();
            
            $this->load->view('ecn/ecn_effect',$this->data);
        }

        public function saveEcnEffect(){
            $data = $this->input->post();
            $errorMessage = array();
            if(empty($data['ecn_checklist_id'])){ $errorMessage['general_error'] = "Action Detail required."; }
            else{
                foreach($data['ecn_checklist_id'] AS $key=>$ecn_checklist_id){
                    if(empty($data['action_detail'][$key])){ $errorMessage['action_detail'.$ecn_checklist_id] = "Action Detail required."; }
                    if(empty($data['changed_by'][$key])){ $errorMessage['changed_by'.$ecn_checklist_id] = "Changed By required."; }
                }
            }

            if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
            else:
                $this->printJson($this->ecn->saveEcnEffect($data));
            endif;
        }
    
        public function inventoryDetail(){
            $data = $this->input->post();
            $this->data['dataRow'] = $this->ecn->getEcn(['id'=>$data['id'],'single_row'=>1]);
            $this->data['stockData'] = $this->itemStock->getItemStock(['item_id'=>$this->data['dataRow']->item_id]);
            $this->load->view('ecn/inventory_detail',$this->data);
        }

        public function saveInventoryDetail(){
            $data = $this->input->post();
            $errorMessage = array();
            $postData = [
                'id'=>$data['id'],
                'fg_stock'=>$data['fg_stock'].'~'.$data['fg_action'],
                'wip_stock'=>$data['wip_stock'].'~'.$data['wip_action'],
                'rm_stock'=>$data['rm_stock'].'~'.$data['rm_action'],
            ];
            $this->printJson($this->ecn->saveInventoryDetail($postData));
        }

        public function approveEcn(){
            $data = $this->input->post();
            $this->data['ecnData']  =$ecnData= $this->ecn->getEcn(['id'=>$data['id'],'single_row'=>1]);
            $this->data['checkList'] = $this->ecn->getEcnEffectDetail(['ecn_id'=> $data['id']]);
            $this->data['oldRevData'] = $this->ecn->getPrevRevisionData(['item_id'=>$ecnData->item_id,'rev_date'=>$ecnData->rev_date,'ecn_id'=> $data['id']]);
            $this->load->view('ecn/approve_form',$this->data);
        }

        public function saveEcnApproval(){
            $data = $this->input->post();
            $errorMessage = array();
            if(empty($data['effect_date'])){ $errorMessage['effect_date'] = "Effective Date is required."; }

            if(empty($data['eng_approve_date'])){ $errorMessage['eng_approve_date'] = "Engineering Date is required."; }
            if(empty($data['quality_approve_date'])){ $errorMessage['quality_approve_date'] = "Quality Date is required."; }
            if(empty($data['other_approve_date'])){ $errorMessage['other_approve_date'] = "Other Date is required."; }           

            if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
            else:
                $data['approved_by'] = $this->loginId;
                $data['status'] = 1;
                $this->printJson($this->ecn->saveEcn($data));
            endif;
        }
        
		public function activeEcn(){
            $data = $this->input->post();
            $errorMessage = array();
            if(empty($data['id'])):
                $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
            else:
                $this->printJson($this->ecn->activeEcn($data));
            endif;
        }


        public function printEcn($id){
            $this->data['ecnData']  =$ecnData= $this->ecn->getEcn(['id'=>$id,'single_row'=>1]);
            $this->data['checkList'] = $this->ecn->getEcnEffectDetail(['ecn_id'=> $id]);
            $this->data['oldRevData'] = $this->ecn->getPrevRevisionData(['item_id'=>$ecnData->item_id,'rev_date'=>$ecnData->rev_date,'ecn_id'=> $id]);
            $this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
            
            $this->data['letter_head']=base_url('assets/images/letterhead_top.png');
            
        
    
            $pdfData = $this->load->view('ecn/ecn_print',$this->data,true);
            $htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                    <tr>
                        <td style="text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                    </tr>
                </table>';
            
            $mpdf = new \Mpdf\Mpdf();
            $pdfFileName='ecn-'.$id.'.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo,0.05,array(100,100));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('P', '', '', '', '', 5, 5, 30, 20, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName,'I');
        }
    /** END ECN */
	
}
?>