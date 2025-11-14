<!--<link href="<?=base_url();?>assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link href="<?=base_url();?>assets/css/style.css?v=<?=time()?>" rel="stylesheet">-->
<div style="padding:0px 40px;text-align:justify;font-family:Times New Roman;font-size:15px;">
	<h2 class="text-center fs-20" style="font-family:Times New Roman;"><u>Loan Approval Confirmation</u></h2>
	<table style="width:100%;">
		<tr>
			<td style="width:70%;font-family:Times New Roman;font-size:15px;"><b>To,</b></td>
			<th class="text-right" style="font-family:Times New Roman;font-size:15px;">Loan No. : #<?=$loanData->trans_number?></th>
		</tr>
		<tr>
			<td colspan="2" style="font-family:Times New Roman;font-size:15px;">
				<b><?=$loanData->emp_code ?> - <?=$loanData->emp_name ?></b><br>
				<?=$loanData->emp_designation ?> (<?=$loanData->dept_name ?>)
				<?php echo ((!empty($loanData->emp_contact)) ? '<br>+91 '.$loanData->emp_contact : '') ;  ?>
			</td>
		</tr>
	</table>
	<div class="text-center fs-18" style="margin:15px 0px;font-family:Times New Roman;font-size:15px;">
		<strong><u>
			SUB : Loan approval confirmation (<small>Your application for Loan <?= date("d-m-Y", strtotime ( $loanData->entry_date)) ?></small>)
		</u></strong>
	</div>
	
	<p><b>Dear <?=$loanData->emp_name ?>,</b></p>
		
	<p>
		We refer to the above request as <b><?=$loanData->trans_number?></b> for <b>&#8377; <?= floatval($loanData->demand_amount)?></b> (<i><?=numToWordEnglish($loanData->demand_amount)?></i>)
	</p>
	<p>
		We are pleased to inform you that your loan request has been approved, and we will be releasing the amount of &#8377; <?= floatval($loanData->approved_amount)?>, to you.
	</p>
	<p>
		We are sure you will agree that your consistent performance, dependability and overall contribution have helped the immediate approval of your request.
	</p>
	<p>
		The loan recovery will start from <b><?= date("F, Y", strtotime ( '+1 month' , strtotime ( $loanData->entry_date ) ))?></b>. The last Installment for your loan will be on <b><?= date("F, Y", strtotime ( '+'.(intVal($loanData->total_emi)+1).' month' , strtotime ( $loanData->entry_date ) ))?></b>
	</p>
	<strong>Loan Amount : &#8377; <?= floatVal($loanData->approved_amount)?></strong><br>
	<strong>Amount Of EMI : &#8377; <?= intVal($loanData->emi_amount)?></strong>
	<table style="width:100%;margin-top:30px;">
		<tr>
			<td style="width:50%;"></td>
			<th style="width:50%;font-family:Times New Roman;font-size:17px;" class="text-center">For, <?=$companyData->company_name?></th>
		</tr>
		<tr><td colspan="2" height="50"></td></tr>
		<tr>
			<td style="width:60%;"></td>
			<th class="text-center" style="font-family:Times New Roman;font-size:17px;">(Authorized Signatory)</th>
		</tr>
	</table>
</div>