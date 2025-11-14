<?php
class BillWise extends MY_Controller{
    private $index = "bill_wise/index";
    private $form = "bill_wise/form";
    private $view = "bill_wise/view";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageUrl = "reports/accountingReport/accountLedger";
        $this->data['headData']->pageTitle = "Bill Wise";
        $this->data['pageHeader'] = 'Bill Wise';
        $this->data['headData']->controller = "billWise";
    }

    public function index($party_id = ""){
        $this->data['party_id'] = (!empty($party_id))?decodeURL($party_id):"";
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3"]);
        $this->load->view($this->index,$this->data);
    }

    public function getUnsettledTransactions(){
        $data = $this->input->post();
        $result = $this->billWise->getUnsettledTransactions($data);

        $tbody = '';$i=1;$balance = 0;
        foreach($result as $row):
            $billWiseParam = "{'postData':{'id' : ".$row->id.",'party_id':".$row->party_id.",'c_or_d':'".(($row->c_or_d == "CR")?"DR":"CR")."'},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'billWiseForm', 'title' : 'Bill Wise Settlement','call_function':'loadReferenceTransactions','js_store_fn':'customStore'}";
            $billWiseButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Settlement" flow="down" onclick="modalAction('.$billWiseParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

            $viewButton = '';
            if(empty(floatval($row->pending_amount))):
                $billWiseButton = "";
            endif;

            if(floatval($row->net_amount - $row->pending_amount) > 0):
                $viewParam = "{'postData':{'id' : ".$row->id."},'modal_id' : 'bs-right-lg-modal', 'form_id' : 'billWiseForm', 'title' : 'View Bill Wise Settlement','call_function':'viewReferenceTransactions','button':'close'}";
                $viewButton = '<a class="btn btn-primary btn-edit" href="javascript:void(0)" datatip="View Settlement" flow="down" onclick="modalAction('.$viewParam.');"><i class="fas fa-eye"></i></a>';
            endif;

            $action = getActionButton($viewButton.$billWiseButton);

            $balance += ($row->pending_amount * $row->p_or_m);
            $tbody .= '<tr>
                <td>'.$action.'</td>
                <td class="text-left">'.$row->vou_name_s.'</td>
                <td class="text-left">'.$row->trans_number.'</td>
                <td class="text-left">'.formatDate($row->trans_date).'</td>
                <td class="text-right">'.numberFormatIndia(floatval($row->net_amount)).'</td>
                <td class="text-right">'.numberFormatIndia(floatval($row->net_amount - $row->pending_amount)).'</td>
                <td class="text-right">'.numberFormatIndia(floatval($row->pending_amount)).'</td>
                <td class="text-right">'.numberFormatIndia(floatval($balance)).'</td>
                <td class="text-center">'.$row->settled_vou_no.'</td>
            </tr>';
            $i++;
        endforeach;

        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }

    public function loadReferenceTransactions(){
        $data = $this->input->post();
        $data['status'] = 0;
        $this->data['dataRow'] = $this->billWise->getUnsettledTransactions(['id'=>$data['id'],'status'=>0]);
        unset($data['id']);
        $this->data['unsettledTrans'] = $this->billWise->getUnsettledTransactions($data);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty(array_sum(array_column($data['billWise'],'amount')))):
            $errorMessage['settlement_error'] = "Please enter settlement amount.";
        else:
            foreach($data['billWise'] as $key=>$row):
                if(!empty($row['amount']) && floatval($row['amount']) > 0):
                    $refData = $this->billWise->getBillWiseTransaction(['id'=>$row['ag_ref_id']]);
                    if(empty($refData)):
                        $errorMessage['settlement_error'] = "Referece Detail mismatch. Please reopen settlement form.";
                        break;
                    else:
                        if(floatval($row['amount']) > floatval($refData->pending_amount)):
                            $errorMessage['amount_'.$key] = "Invalid Amount.";
                        endif;
                    endif;
                endif;
            endforeach;

            $transData = $this->billWise->getBillWiseTransaction(['id'=>$data['id']]);//print_r($transData);exit;
            if(array_sum(array_column($data['billWise'],'amount')) > floatval($transData->pending_amount)):
                $errorMessage['settlement_error'] = "Voucher Unsettled Amount and Reference	Settlement Amount mismatch.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->billWise->save($data));
        endif;
    }

    public function viewReferenceTransactions(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->billWise->getBillWiseTransaction($data);
        $this->load->view($this->view,$this->data);
    }

    public function getSettledTransaction(){
        $data = $this->input->post();
        $settledTrans =  $this->billWise->getBillWiseTransaction(['ag_ref_id'=>$data['ag_ref_id']]);

        $i=1;$tbody = '';
        foreach($settledTrans as $row):
            $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Settlement','res_function':'resTrashBillWise','fndelete':'removeSettlement'}";
            $deleteButton = '<a class="btn btn-outline-danger btn-delete btn-sm" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="left"><i class="mdi mdi-trash-can-outline"></i></a>';

            $tbody .= '<tr>
                <td>'.$row->trans_number.'</td>
                <td>'.formatDate($row->trans_date).'</td>
                <td>'.numberFormatIndia(floatval($row->net_amount)).'</td>
                <td>'.numberFormatIndia(floatval($row->amount)).'</td>
                <td class="text-center">
                    '.$deleteButton.'
                </td>
            </tr>';
            $i++;
        endforeach;

        if(empty($tbody)):
            $tbody = '<tr>
                <td colspan="5" class="text-center">No data available in table</td>
            </tr>';
        endif;

        $this->printJson(['status'=>1,'tbodyData'=>$tbody]);
    }

    public function removeSettlement(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->billWise->removeSettlement($id));
        endif;
    }
}
?>