<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* Common Table Header */
function tableHeader($data){
	$c=0;$colsAlignment=array();$srno_position=1;$sortable=array();
    $html = '<thead class="thead-info"><tr>';
    foreach($data as $row):
        $name = $row['name'];
        $style = (isset($row['style']))?$row['style']:"";
		
        $orderable = (isset($row['orderable']))?$row['orderable']:"true";
        $class = (isset($row['class']))?$row['class']:"";
		$html .= '<th class="'.$class.'" style="'.$style.'" data-orderable="'.$orderable.'">'.$name.'</th>';
		
        if(isset($row['srnoPosition'])):
			$srno_position = $row['srnoPosition'];
        endif;

        if(isset($row['sortable'])):
            if($row['sortable'] == false || $row['sortable'] == "FALSE"):
                $sortable[] = $c;
            endif;
        endif;
		
		if(isset($row['textAlign']) and $row['textAlign']=="left"):
			$colsAlignment['left'][]= $c;
		elseif(isset($row['textAlign']) and $row['textAlign']=="right"):
			$colsAlignment['right'][]= $c;
		elseif(isset($row['textAlign']) and $row['textAlign']=="center"):
			$colsAlignment['center'][]= $c;
		endif;
        $c++;
    endforeach;
    $html .= '</tr></thead>';

    return [$html,json_encode($colsAlignment),$srno_position,json_encode($sortable)];
}

/* Create Action Button */
function getActionButton($buttons){
	$action = '<div class="actionWrapper" style="position:relative;">
					<div class="actionButtons actionButtonsRight">
						<a class="mainButton btn-dark" href="javascript:void(0)"><i class="fa fa-cog"></i></a>
						<div class="btnDiv">'.$buttons.'</div>
					</div>
				</div>';
	return $action;
}

function getVoucherNameLong($entryType){
    switch($entryType){
        case 1:
            return "Sales Enquiry";
        case 2:
            return "Sales Quotation";
        case 3:
            return "Quotation Revision";
        case 4:
            return "Sales Order";
        case 7:
            return "Delivery Challan";
        case 6: //Manufacturing (Domestics)
            return "Sales Invoice";
        case 7: //Jobwork (Domestics)
            return "Sales Invoice";
        case 8: //Manufacturing (Export)
            return "Sales Invoice";
        case 9:
            return "Proforma Invoice";
        case 10: //Commercial Invoice
            return "Sales Invoice";
        case 11: //Custom Invoice
            return "Sales Invoice";
        case 12:
            return "Purchase Invoice";
        case 13:
            return "Credit Note";
        case 14:
            return "Debit Note";
        case 15:
            return "Cash/Bank Received";
        case 16:
            return "Cash/Bank Paid";
        case 17:
            return "Journal Voucher";
        case 18:
            return "GST Expense";
        case 19:
            return "GST Income";
        case 20:
            return "JobWork Invoice";
        case 21:
            return "Advance Salary";
        case 22:
            return "Employee Loan";
        default:
			return "";
    }
}

function getVoucherNameShort($entryType){
    switch($entryType){
        case 1:
            return "SEnq";
        case 2:
            return "SQuo";
        case 3:
            return "QRev";
        case 4:
            return "SOrd";
        case 7:
            return "Chln";
        case 6: //Manufacturing (Domestics)
            return "Sale";
        case 7: //Jobwork (Domestics)
            return "Sale";
        case 8: //Manufacturing (Export)
            return "Sale";
        case 9:
            return "PrIn";
        case 10: //Commercial Invoice
            return "Sale";
        case 11: //Custom Invoice
            return "Sale";
        case 12:
            return "Purc";
        case 13:
            return "C.N.";
        case 14:
            return "D.N.";
        case 15:
            return "BCRct";
        case 16:
            return "BCPmt";
        case 17:
            return "Jrnl";
        case 18:
            return "GExp";
        case 19:
            return "GInc";
        case 20:
            return "JWInv";
        case 21:
            return "AdSal";
        case 22:
            return "EmpLoan";
        default:
			return "";
    }
}

function getSystemCode($type,$isChild,$gstType=0){
	$retVal = "";
	if($isChild == false){
		switch($type){	
			case "Purc": // Purchase Invoice
				$retVal = "PURACC";
				break;
			case "Sale": // Sales Invoice
				$retVal = "SALESACC";
				break;
			case "C.N.": // Credit Note
				$retVal = "SALESACC";
				break;	
			case "D.N.": // Debit Note
				$retVal = "PURACC";
				break;
            case "GExp": // GST Expense
                $retVal = "PURACC";
                break;
            case "GInc": // GST Income
                $retVal = "SALESACC";
                break;
		}
	}else{
		switch($type){	
			case "Purc": // Purchase Invoice
				$retVal = ($gstType == 3)?"PURTFACC":"PURGSTACC";
				break;
			case "Sale": // Sales Invoice
				$retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
				break;
			case "C.N.": // Credit Note
				$retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
				break;	
			case "D.N.": // Debit Note
				$retVal = ($gstType == 3)?"PURTFACC":"PURGSTACC";
				break;
            case "GExp": // GST Expense
                $retVal = ($gstType == 3)?"PURTFACC":"PURGSTACC";
                break;
            case "GInc": // GST Income
                $retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
                break;
		}
	}
	return $retVal;
}

function getSPAccCode($entryType,$gstType,$spType){
    switch($entryType){
        /* Purchase Invoice Case Start */
        case $entryType == "Purc" && $gstType == 1 && $spType == 1:
            $retVal = "PURGSTACC";    
            break;
        case $entryType == "Purc" && $gstType == 2 && $spType == 1:
            $retVal = "PURIGSTACC";    
            break;
        case $entryType == "Purc" && $gstType == 2 && $spType == 2:
            $retVal = "IMPORTGSTACC";    
            break;
        case $entryType == "Purc" && $gstType == 3 && $spType == 2:
            $retVal = "IMPORTTFACC";    
            break;
        case $entryType == "Purc" && $gstType == 1 && $spType == 3:
            $retVal = "PURJOBGSTACC";    
            break;
        case $entryType == "Purc" && $gstType == 2 && $spType == 3:
            $retVal = "PURJOBIGSTACC";    
            break;
        case $entryType == "Purc" && $gstType == 3:
            $retVal = "PURTFACC";    
            break;
        /* Purchase Invoice Case End */


        /* Debit Note Case Start */
        case $entryType == "D.N." && $gstType == 1 && $spType == 1:
            $retVal = "PURGSTACC";    
            break;
        case $entryType == "D.N." && $gstType == 2 && $spType == 1:
            $retVal = "PURIGSTACC";    
            break;
        case $entryType == "D.N." && $gstType == 2 && $spType == 2:
            $retVal = "IMPORTGSTACC";    
            break;
        case $entryType == "D.N." && $gstType == 3 && $spType == 2:
            $retVal = "IMPORTTFACC";    
            break;
        case $entryType == "D.N." && $gstType == 1 && $spType == 3:
            $retVal = "PURJOBGSTACC";    
            break;
        case $entryType == "D.N." && $gstType == 2 && $spType == 3:
            $retVal = "PURJOBIGSTACC";    
            break;
        case $entryType == "D.N." && $gstType == 3:
            $retVal = "PURTFACC";    
            break;
        /* Debit Note Case End */

        /* GST Expense Case Start */
        case $entryType == "GExp" && $gstType == 1 && $spType == 1:
            $retVal = "PURGSTACC";    
            break;
        case $entryType == "GExp" && $gstType == 2 && $spType == 1:
            $retVal = "PURIGSTACC";    
            break;
        case $entryType == "GExp" && $gstType == 2 && $spType == 2:
            $retVal = "IMPORTGSTACC";    
            break;
        case $entryType == "GExp" && $gstType == 3 && $spType == 2:
            $retVal = "IMPORTTFACC";    
            break;
        case $entryType == "GExp" && $gstType == 1 && $spType == 3:
            $retVal = "PURJOBGSTACC";    
            break;
        case $entryType == "GExp" && $gstType == 2 && $spType == 3:
            $retVal = "PURJOBIGSTACC";    
            break;
        case $entryType == "GExp" && $gstType == 3:
            $retVal = "PURTFACC";    
            break;
        /* GST Expense Case End */

        /* Sales Invoice Case Start */
        case $entryType == "Sale" && $gstType == 1 && $spType == 1:
            $retVal = "SALESGSTACC";    
            break;
        case $entryType == "Sale" && $gstType == 2 && $spType == 1:
            $retVal = "SALESIGSTACC";    
            break;
        case $entryType == "Sale" && $gstType == 2 && $spType == 2:
            $retVal = "EXPORTGSTACC";    
            break;
        case $entryType == "Sale" && $gstType == 3 && $spType == 2:
            $retVal = "EXPORTTFACC";    
            break;
        case $entryType == "Sale" && $gstType == 1 && $spType == 3:
            $retVal = "SALESJOBGSTACC";    
            break;
        case $entryType == "Sale" && $gstType == 2 && $spType == 3:
            $retVal = "SALESJOBIGSTACC";    
            break;
        case $entryType == "Sale" && $gstType == 3:
            $retVal = "SALESTFACC";    
            break;
        /* Sales Invoice Case End */

        /* Credit Note Case Start */
        case $entryType == "C.N." && $gstType == 1 && $spType == 1:
            $retVal = "SALESGSTACC";    
            break;
        case $entryType == "C.N." && $gstType == 2 && $spType == 1:
            $retVal = "SALESIGSTACC";    
            break;
        case $entryType == "C.N." && $gstType == 2 && $spType == 2:
            $retVal = "EXPORTGSTACC";    
            break;
        case $entryType == "C.N." && $gstType == 3 && $spType == 2:
            $retVal = "EXPORTTFACC";    
            break;
        case $entryType == "C.N." && $gstType == 1 && $spType == 3:
            $retVal = "SALESJOBGSTACC";    
            break;
        case $entryType == "C.N." && $gstType == 2 && $spType == 3:
            $retVal = "SALESJOBIGSTACC";    
            break;
        case $entryType == "C.N." && $gstType == 3:
            $retVal = "SALESTFACC";    
            break;
        /* Credit Note Case End */

        /* GST Income Case Start */
        case $entryType == "GInc" && $gstType == 1 && $spType == 1:
            $retVal = "SALESGSTACC";    
            break;
        case $entryType == "GInc" && $gstType == 2 && $spType == 1:
            $retVal = "SALESIGSTACC";    
            break;
        case $entryType == "GInc" && $gstType == 2 && $spType == 2:
            $retVal = "EXPORTGSTACC";    
            break;
        case $entryType == "GInc" && $gstType == 3 && $spType == 2:
            $retVal = "EXPORTTFACC";    
            break;
        case $entryType == "GInc" && $gstType == 1 && $spType == 3:
            $retVal = "SALESJOBGSTACC";    
            break;
        case $entryType == "GInc" && $gstType == 2 && $spType == 3:
            $retVal = "SALESJOBIGSTACC";    
            break;
        case $entryType == "GInc" && $gstType == 3:
            $retVal = "SALESTFACC";    
            break;
        /* GST Income Case End */

        default: 
            $retVal = "";
            break;
    }
    return $retVal;
}

function getCrDrEff($type){
	$result = array();
	switch($type){
		case "Purc": //Purchase Invoice
			$result['vou_type'] = "CR";
			$result['opp_type'] = "DR";		
			break;	

		case "Sale": //Sales Invoice
			$result['vou_type'] = "DR";
			$result['opp_type'] = "CR";	
			break;  
    
		case "C.N.": //Credit Note
			$result['vou_type'] = "CR";
			$result['opp_type'] = "DR";		
			break;
            
		case "D.N.": //Debit Note
			$result['vou_type'] = "DR";
			$result['opp_type'] = "CR";	
			break;

		case "BCRct": //Bank/Cash Receipt
			$result['vou_type'] = "DR";
			$result['opp_type'] = "CR";	
			break;

		case "BCPmt": //Bank/Cash Payment
			$result['vou_type'] = "CR";
			$result['opp_type'] = "DR";	
			break;

		case "GExp": //GST Expense
			$result['vou_type'] = "CR";
			$result['opp_type'] = "DR";	
			break;

        case "GInc": //GST Income
            $result['vou_type'] = "DR";
            $result['opp_type'] = "CR";	
            break;

        case "AdSal": //Advance Salary Payment
            $result['vou_type'] = "CR";
            $result['opp_type'] = "DR";	
            break;

        case "EmpLoan": //Employee Loan Payment
            $result['vou_type'] = "CR";
            $result['opp_type'] = "DR";	
            break;
	}
	return $result;
}

function getExpArrayMap($input){
    $result = array();
	$expAmount=0;
    if(!empty($input)):
        for($i=1; $i<=25 ; $i++):
            $result['exp'.$i.'_acc_id'] = (isset($input['exp'.$i.'_acc_id']))?$input['exp'.$i.'_acc_id']:0;
            $result['exp'.$i.'_per'] = (isset($input['exp'.$i.'_per']))?$input['exp'.$i.'_per']:0;
            $result['exp'.$i.'_amount'] = (isset($input['exp'.$i.'_amount']))?$input['exp'.$i.'_amount']:0;
            $expAmount += $result['exp'.$i.'_amount'];
        endfor;
    endif;
	$result['exp_amount'] = $expAmount;
	return $result;
}
?>