<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function getAccountingDtHeader($page){
    /** Final Packing For Invoice */
    $data['finalPacking'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['finalPacking'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['finalPacking'][] = ["name"=>"Pack No."];
	$data['finalPacking'][] = ["name"=>"Pack Date"];
	$data['finalPacking'][] = ["name"=>"Customer Name"];
	$data['finalPacking'][] = ["name"=>"Total Package"];
	$data['finalPacking'][] = ["name"=>"Total Box"];
    $data['finalPacking'][] = ["name"=>"Total Qty"];

    /* Sales Invoice Header */
    $data['salesInvoice'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['salesInvoice'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['salesInvoice'][] = ["name"=>"Inv No."];
	$data['salesInvoice'][] = ["name"=>"Inv Date"];
	$data['salesInvoice'][] = ["name"=>"Customer Name"];
	$data['salesInvoice'][] = ["name"=>"Taxable Amount"];
	$data['salesInvoice'][] = ["name"=>"GST Amount"];
    $data['salesInvoice'][] = ["name"=>"Net Amount"];

    /* Credit Note Header */
    $data['creditNote'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['creditNote'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['creditNote'][] = ["name"=>"CN Type."];
	$data['creditNote'][] = ["name"=>"CN No."];
	$data['creditNote'][] = ["name"=>"CN Date"];
	$data['creditNote'][] = ["name"=>"Party Name"];
	$data['creditNote'][] = ["name"=>"Taxable Amount"];
	$data['creditNote'][] = ["name"=>"GST Amount"];
    $data['creditNote'][] = ["name"=>"Net Amount"];

    /* Purchase Invoice Header */
    $data['purchaseInvoice'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['purchaseInvoice'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['purchaseInvoice'][] = ["name"=>"Inv No."];
	$data['purchaseInvoice'][] = ["name"=>"Inv Date"];
	$data['purchaseInvoice'][] = ["name"=>"Party Name"];
	$data['purchaseInvoice'][] = ["name"=>"Taxable Amount"];
	$data['purchaseInvoice'][] = ["name"=>"GST Amount"];
    $data['purchaseInvoice'][] = ["name"=>"Net Amount"];

    /* Debit Note Header */
    $data['debitNote'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['debitNote'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
	$data['debitNote'][] = ["name"=>"DN Type."];
	$data['debitNote'][] = ["name"=>"DN No."];
	$data['debitNote'][] = ["name"=>"DN Date"];
	$data['debitNote'][] = ["name"=>"Party Name"];
	$data['debitNote'][] = ["name"=>"Taxable Amount"];
	$data['debitNote'][] = ["name"=>"GST Amount"];
    $data['debitNote'][] = ["name"=>"Net Amount"];

    /* GST Expense Header */
    $data['gstExpense'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['gstExpense'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];  
	$data['gstExpense'][] = ["name"=>"Inv No."];
	$data['gstExpense'][] = ["name"=>"Inv Date"];
	$data['gstExpense'][] = ["name"=>"Party Name"];
	$data['gstExpense'][] = ["name"=>"Taxable Amount"];
	$data['gstExpense'][] = ["name"=>"GST Amount"];
    $data['gstExpense'][] = ["name"=>"Net Amount"];

    /* GST Income Header */
    $data['gstIncome'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['gstIncome'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];  
	$data['gstIncome'][] = ["name"=>"Inv No."];
	$data['gstIncome'][] = ["name"=>"Inv Date"];
	$data['gstIncome'][] = ["name"=>"Party Name"];
	$data['gstIncome'][] = ["name"=>"Taxable Amount"];
	$data['gstIncome'][] = ["name"=>"GST Amount"];
    $data['gstIncome'][] = ["name"=>"Net Amount"];

    /* Journal Entry Header */
    $data['journalEntry'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['journalEntry'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['journalEntry'][] = ["name" => "JV No."];
    $data['journalEntry'][] = ["name" => "JV Date."];
    $data['journalEntry'][] = ["name" => "Ledger Name"];
    $data['journalEntry'][] = ["name" => "Debit", "textAlign" => "right"];
    $data['journalEntry'][] = ["name" => "Credit", "textAlign" => "right"];
    $data['journalEntry'][] = ["name" => "Note"];

    /* Payment Voucher  */
    $data['paymentVoucher'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['paymentVoucher'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE]; 
    $data['paymentVoucher'][] = ["name" => "Voucher No."];
    $data['paymentVoucher'][] = ["name" => "Voucher Date"];
    $data['paymentVoucher'][] = ["name" => "Party Name"];
    $data['paymentVoucher'][] = ["name" => "Bank/Cash"];
    $data['paymentVoucher'][] = ["name" => "Amount", "textAlign" => "center"];
    $data['paymentVoucher'][] = ["name" => "Doc. No."];
    $data['paymentVoucher'][] = ["name" => "Doc. Date"];
    $data['paymentVoucher'][] = ["name" => "Note"];

    /* Tax Payment Voucher */
    $data['taxPayment'][] = ["name"=>"Action","class"=>"text-center no_filter noExport","sortable"=>FALSE];
	$data['taxPayment'][] = ["name"=>"#","class"=>"text-center no_filter","sortable"=>FALSE];
    $data['taxPayment'][] = ["name" => "Voucher No."];
    $data['taxPayment'][] = ["name" => "Voucher Date"];
    $data['taxPayment'][] = ["name" => "Ledger Name"];
    $data['taxPayment'][] = ["name" => "Bank/Cash"];
    $data['taxPayment'][] = ["name" => "Amount", "textAlign" => "center"];
    $data['taxPayment'][] = ["name" => "Doc. No."];
    $data['taxPayment'][] = ["name" => "Doc. Date"];
    $data['taxPayment'][] = ["name" => "Note"];

    return tableHeader($data[$page]);
}

/* Final Packing Data */
function getPendingFinalPackingData($data){
    $url = encodeURL(['packing_id'=>$data->id]); 
    $invBtn = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('salesInvoice/addInvoice/'.$url).'" datatip="Create Invoice" flow="down" ><i class="ti ti-file-invoice"></i></a>';
    $action = getActionButton($invBtn);
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->total_package,$data->total_box,$data->total_qty];
}

/* Sales Invoice Table Data */
function getSalesInvoiceData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('salesInvoice/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Sales Invoice'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $print = "";//'<a href="javascript:void(0)" class="btn btn-info btn-edit printDialog permission-approve1" datatip="Print Invoice" flow="down" data-id="'.$data->id.'" data-print_format="" data-fn_name="printInvoice"><i class="fa fa-print"></i></a>';
    $print_f2 = "";$invPrints = "";
    if($data->inv_item_count == $data->pdi_report_count):
        $print_f2 = '<a href="javascript:void(0)" class="btn btn-primary btn-edit printDialog permission-approve1" datatip="Print Invoice" flow="down" data-id="'.$data->id.'" data-print_format="f2" data-fn_name="printInvoice"><i class="fa fa-print"></i></a>';

		if(!empty($data->pdi_report_count)){
			$urlData = ['so_id'=>$data->id, 'pdi_type'=>$data->pdi_type,'party_logo'=>$data->party_logo];
			$invPrints = '<a href="'.base_url('salesInvoice/invPrints/'.encodeURL($urlData)).'" target = "_blank" class="btn btn-dribbble btn-edit permission-approve1" datatip="All Reports" flow="down" ><i class="fa fa-print"></i></a>';
		}
    endif;

    //Export Invoice Print
    $expInvPrint = '';
    if(in_array($data->tax_class,["EXPORTGSTACC","EXPORTTFACC"])):
        $expInvPrint = '<a href="'.base_url('salesInvoice/printExportInvoice/'.$data->id).'" target="_blank" datatip="Export Invoice PDF" flow="down" class="btn btn-info"><i class="fa fa-print"></i></a>';
    endif;
    
    $ewbPDF = '';$ewbDetailPDF = '';$generateEWB = '';  $cancelEwb = '';
    if(!empty($data->ewb_status)):
        $ewbPDF = '<a href="'.base_url('ebill/ewb_pdf/'.$data->eway_bill_no).'" target="_blank" datatip="EWB PDF" flow="down" class="btn btn-dark"><i class="fa fa-print"></i></a>';

        $ewbDetailPDF = '<a href="'.base_url('ebill/ewb_detail_pdf/'.$data->eway_bill_no).'" target="_blank" datatip="EWB DETAIL PDF" flow="down" class="btn btn-warning"><i class="fas fa-print"></i></a>';

        if($data->ewb_status == 3):
            $ewbParam = "{'postData':{'id' : ".$data->id.",'party_id' : ".$data->party_id.",'doc_type' : 'INV'}, 'modal_id' : 'modal-xl', 'form_id' : 'generateEwayBill', 'title' : 'E-way Bill For Invoice No. : ".($data->trans_number)."', 'fnedit' : 'addEwayBill', 'fnsave' : 'generateEwb', 'js_store_fn' : 'generateEwb','controller':'ebill','syncBtn':1}";

            $generateEWB = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-way Bill" flow="down" onclick="ebillFrom('.$ewbParam.');"><i class="fas fa-truck"></i></a>';
        else:
            $cancelEwbParam = "{'postData':{'id' : ".$data->id.",'doc_type' : 'INV'}, 'modal_id' : 'modal-md', 'form_id' : 'cancelEwb', 'title' : 'Cancel Eway Bill [ Invoice No. : ".($data->trans_number)." ]', 'fnedit' : 'loadCancelEwayBillForm', 'fnsave' : 'cancelEwayBill', 'js_store_fn' : 'cancelEwayBill','controller':'ebill','syncBtn':0,'save_btn_text':'Cancel EWB'}";
            $cancelEwb = '<a href="javascript:void(0)" class="btn btn-danger" datatip="Cancel Eway Bill" flow="down" onclick="ebillFrom('.$cancelEwbParam.');"><i class="fas fa-times"></i></a>';

            $editButton="";$deleteButton="";
        endif;
    else:
        if(empty($data->eway_bill_no)):
            $ewbParam = "{'postData':{'id' : ".$data->id.",'party_id' : ".$data->party_id.",'doc_type' : 'INV'}, 'modal_id' : 'modal-xl', 'form_id' : 'generateEwayBill', 'title' : 'E-way Bill For Invoice No. : ".($data->trans_number)."', 'fnedit' : 'addEwayBill', 'fnsave' : 'generateEwb', 'js_store_fn' : 'generateEwb','controller':'ebill','syncBtn':1}";

            $generateEWB = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-way Bill" flow="down" onclick="ebillFrom('.$ewbParam.');"><i class="fa fa-truck"></i></a>';
        endif;
    endif;

    $generateEinv = ""; $einvPdf = "";
    if(!empty($data->e_inv_status)):
        $einvPdf = '<a href="'.base_url('ebill/einv_pdf/'.$data->e_inv_no).'" target="_blank" datatip="E-Invoice PDF" flow="down" class="btn btn-dark"><i class="fa fa-print"></i></a>';

        $editButton="";$deleteButton="";
    else:
        if(empty($data->e_inv_no)):
            $einvParam = "{'postData':{'id' : ".$data->id.",'party_id' : ".$data->party_id.",'doc_type' : 'INV'}, 'modal_id' : 'modal-xl', 'form_id' : 'generateEinv', 'title' : 'E-Invoice For Invoice No. : ".$data->trans_number."', 'fnedit' : 'addEinvoice', 'fnsave' : 'generateEinvoice', 'js_store_fn' : 'generateEinvoice','controller':'ebill','syncBtn':1}";

            $generateEinv = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-Invoice" flow="down" onclick="ebillFrom('.$einvParam.');"><i class="mdi mdi-receipt"></i></a>';
        endif;
    endif;

    $cancelInv = "";
    if($data->trans_status != 3):
        $cancelInvParam = "{'postData':{'id' : ".$data->id.",'doc_type' : 'INV'}, 'modal_id' : 'modal-md', 'form_id' : 'cancelInv', 'title' : 'Cancel Invoice No. : ".$data->trans_number."', 'fnedit' : 'loadCancelInvForm', 'fnsave' : 'cancelEinvoice', 'js_store_fn' : 'cancelEinv','controller':'ebill','syncBtn':0,'save_btn_text':'Cancel Invoice'}";
        $cancelInv = '<a href="javascript:void(0)" class="btn btn-danger" datatip="Cancel Invoice" flow="down" onclick="ebillFrom('.$cancelInvParam.');"><i class="fa fa-times"></i></a>';
    else:
        $editButton="";$deleteButton="";$generateEinv = "";$generateEWB = '';$cancelEwb = '';
    endif;

	//$cancelInv = ""; $generateEinv = "";$generateEWB = '';$cancelEwb = '';
    $action = getActionButton($print_f2.$expInvPrint.$invPrints.$print.$ewbPDF.$ewbDetailPDF.$generateEWB.$cancelEwb.$einvPdf.$generateEinv.$cancelInv.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
}

/* Credit Note Table Data */
function getCreaditNoteData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('creditNote/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Credit Note'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $print = '<a href="javascript:void(0)" class="btn btn-warning btn-edit printDialog permission-approve1" datatip="Print Invoice" flow="down" data-id="'.$data->id.'" data-fn_name="printCreditNote"><i class="fa fa-print"></i></a>';

    $generateEinv = ""; $einvPdf = "";
    if(!empty($data->e_inv_status)):
        $einvPdf = '<a href="'.base_url('ebill/einv_pdf/'.$data->e_inv_no).'" target="_blank" datatip="E-Invoice PDF" flow="down" class="btn btn-dark"><i class="fa fa-print"></i></a>';

        $editButton="";$deleteButton="";
    else:
        if(empty($data->e_inv_no)):
            $einvParam = "{'postData':{'id' : ".$data->id.",'party_id' : ".$data->party_id.",'doc_type' : 'CRN'}, 'modal_id' : 'modal-xl', 'form_id' : 'generateEinv', 'title' : 'E-Invoice For Invoice No. : ".$data->trans_number."', 'fnedit' : 'addEinvoice', 'fnsave' : 'generateEinvoice', 'js_store_fn' : 'generateEinvoice','controller':'ebill','syncBtn':1}";

            $generateEinv = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-Invoice" flow="down" onclick="ebillFrom('.$einvParam.');"><i class="mdi mdi-receipt"></i></a>';
        endif;
    endif;

    $cancelInv = "";
    if($data->trans_status != 3):
        $cancelInvParam = "{'postData':{'id' : ".$data->id.",'doc_type' : 'CRN'}, 'modal_id' : 'modal-md', 'form_id' : 'cancelInv', 'title' : 'Cancel Credit Note No. : ".$data->trans_number."', 'fnedit' : 'loadCancelInvForm', 'fnsave' : 'cancelEinvoice', 'js_store_fn' : 'cancelEinv','controller':'ebill','syncBtn':0,'save_btn_text':'Cancel Credit Note'}";
        $cancelInv = '<a href="javascript:void(0)" class="btn btn-danger" datatip="Cancel Credit Note" flow="down" onclick="ebillFrom('.$cancelInvParam.');"><i class="fa fa-times"></i></a>';
    else:
        $editButton="";$deleteButton="";$generateEinv = "";$generateEWB = '';  $cancelEwb = '';
    endif;

    $action = getActionButton($print.$einvPdf.$generateEinv.$cancelInv.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->order_type,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
}

/* Purchase Invoice Table Data */
function getPurchaseInvoiceData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('purchaseInvoice/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Purchase Invoice'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
}

/* Debit Note Table Data */
function getDebitNoteData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('debitNote/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Debit Note'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $print = '<a href="javascript:void(0)" class="btn btn-warning btn-edit printDialog permission-approve1" datatip="Print Debit Note" flow="down" data-id="'.$data->id.'" data-fn_name="printDebitNote"><i class="fa fa-print"></i></a>';

    $generateEinv = ""; $einvPdf = "";
    if(!empty($data->e_inv_status)):
        $einvPdf = '<a href="'.base_url('ebill/einv_pdf/'.$data->e_inv_no).'" target="_blank" datatip="E-Invoice PDF" flow="down" class="btn btn-dark"><i class="fa fa-print"></i></a>';
        $editButton="";$deleteButton="";
    else:
        if(empty($data->e_inv_no)):
            $einvParam = "{'postData':{'id' : ".$data->id.",'party_id' : ".$data->party_id.",'doc_type' : 'DBN'}, 'modal_id' : 'modal-xl', 'form_id' : 'generateEinv', 'title' : 'E-Invoice For Invoice No. : ".$data->trans_number."', 'fnedit' : 'addEinvoice', 'fnsave' : 'generateEinvoice', 'js_store_fn' : 'generateEinvoice','controller':'ebill','syncBtn':1}";

            $generateEinv = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-Invoice" flow="down" onclick="ebillFrom('.$einvParam.');"><i class="mdi mdi-receipt"></i></a>';
        endif;
    endif;

    $cancelInv = "";
    if($data->trans_status != 3):
        $cancelInvParam = "{'postData':{'id' : ".$data->id.",'doc_type' : 'DBN'}, 'modal_id' : 'modal-md', 'form_id' : 'cancelInv', 'title' : 'Cancel Debit Note No. : ".$data->trans_number."', 'fnedit' : 'loadCancelInvForm', 'fnsave' : 'cancelEinvoice', 'js_store_fn' : 'cancelEinv','controller':'ebill','syncBtn':0,'save_btn_text':'Cancel Debit Note'}";
        $cancelInv = '<a href="javascript:void(0)" class="btn btn-danger" datatip="Cancel Debit Note" flow="down" onclick="ebillFrom('.$cancelInvParam.');"><i class="fa fa-times"></i></a>';
    else:
        $editButton="";$deleteButton="";$generateEinv = "";$generateEWB = '';  $cancelEwb = '';
    endif;

    $action = getActionButton($print.$einvPdf.$generateEinv.$cancelInv.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->order_type,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
}

/* GST Expense Table Data */
function getGstExpenseData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('gstExpense/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Expese'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
}

/* GST Income Table Data */
function getGstIncomeData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('gstIncome/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Income'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
}

/* Journal Entry Data */
function getJournalEntryData($data){
    //$editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('journalEntry/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="mdi mdi-square-edit-outline"></i></a>';

    $editParam = "{'postData':{'id' : ".$data->id."}, 'init_action' : 'editJournalEntry', 'call_function':'edit'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Journal Entry'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $printVoucher = '<a href="'.base_url('journalEntry/printJV/'.$data->id).'" type="button" class="btn btn-primary" datatip="Print JV" flow="down" target="_blank"><i class="fas fa-print"></i></a>';

    $action = getActionButton($printVoucher . $editButton . $deleteButton);
	$debit = $credit = "";
    if($data->c_or_d == 'DR'){$debit = $data->amount;}else{$credit = $data->amount;}

    return [$action, $data->sr_no, $data->trans_number, formatDate($data->trans_date), $data->acc_name, $debit, $credit, $data->remark];
}

/* Payment Voucher Data */
function getPaymentVoucher($data){
    $editButton = '';$deleteButton = '';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Voucher'}";
    $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-lg-modal', 'form_id' : 'editVoucher', 'title' : 'Update Voucher','call_function':'edit'}";
    
    if($data->trans_status == 0): 
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
        
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    endif;

    $printVoucher = '';
    /* if($data->vou_name_s == "BCRct"){
        $printVoucher = '<a href="javascript:void(0)" class="btn btn-info btn-edit printPaymentVoucher" datatip="Print Voucher" flow="down" data-id="'.$data->id.'" data-function="voucher_pdf"><i class="fa fa-print"></i></a>';
    } */
    $action = getActionButton($printVoucher.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->opp_acc_name,$data->vou_acc_name,$data->net_amount,$data->doc_no,$data->doc_date,$data->remark];
}

/* Tax Payment Data */
function getTaxPaymentData($data){
    $editButton = '';$deleteButton = '';

    $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'bs-right-xl-modal', 'form_id' : 'editVoucher', 'title' : 'Update Voucher','call_function':'edit'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="modalAction('.$editParam.');"><i class="mdi mdi-square-edit-outline"></i></a>';
    
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Voucher'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->opp_acc_name,$data->vou_acc_name,numberFormatIndia($data->net_amount),$data->doc_no,((!empty($data->doc_date))?formatDate($data->doc_date):""),$data->remark];
}

?>