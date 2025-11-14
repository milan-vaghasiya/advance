<html>
    <head>
        <title>Journal Voucher</title>
        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">
    </head>
    <body>
        <div class="row">
            <div class="col-12">
				<table>
					<tr>
						<td>
							<?php if(!empty($letter_head)): ?>
                                <img src="<?=$letter_head?>" class="img">
                            <?php endif;?>
						</td>
					</tr>
				</table>

				<table class="table bg-light-grey">
					<tr class="" style="letter-spacing: 2px;font-weight:bold;padding:2px !important; border-bottom:1px solid #000000;">
						<td style="width:33%;" class="fs-16 text-left">
							Vou. No. : <?=$jvData->trans_number?>
						</td>
						<td style="width:33%;" class="fs-18 text-center">Journal Voucher</td>
						<td style="width:33%;" class="fs-16 text-right">
							Vou. Date. : <?=formatDate($jvData->trans_date)?>
						</td>
					</tr>
				</table> 
                
                <table class="table item-list-bb" style="margin-top:10px;">
					<thead>
						<tr>
							<th>No.</th>
							<th class="text-left">Ledger Name</th>
							<th class="text-right">Credit</th>
							<th class="text-right">Debit</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$i=1;$totalCredit = 0;$totalDebit = 0;
							if(!empty($jvData->ledgerData)):
								foreach($jvData->ledgerData as $row): 
									$debit =0;  $credit =0;
    								if($row->c_or_d == 'DR'){$debit = $row->amount;}else{$credit = $row->amount;}
									$row->ledger_name = (!empty($row->remark))?$row->ledger_name.'<br><small>Note : '.$row->remark.'</small>':$row->ledger_name;
									echo '<tr>';
										echo '<td class="text-center">'.$i++.'</td>';
										echo '<td class="text-left">'.$row->ledger_name.'</td>';
										echo '<td class="text-right">'.$credit.'</td>';
										echo '<td class="text-right">'.$debit.'</td>';
									echo '</tr>';
									$totalCredit += $credit;
									$totalDebit += $debit;
								endforeach;
							endif;
						?>
						<tr>
                            <th colspan="2" class="text-right">Total</th>
                            <th class="text-right"><?=sprintf('%.2f',$totalCredit)?></th>
                            <th class="text-right"><?=sprintf('%.2f',$totalDebit)?></th>                          
                        </tr>
						<tr>
							<th colspan="4" class="text-left">Note : <?=$jvData->remark?></th>
						</tr>
					</tbody>
				
                </table>
                
				<htmlpagefooter name="lastpage">
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:50%;" rowspan="4"></td>
							<th colspan="2">For, <?=$companyData->company_name?></th>
						</tr>
						<tr>
							<td style="width:50%;" class="text-center" height="50"></td>
						</tr>
						<tr>
							<td style="width:50%;" class="text-center"><b>Authorised Singnatory</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;">JV No. & Date : <?=$jvData->trans_number.' ['.formatDate($jvData->trans_date).']'?></td>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>
                </htmlpagefooter>
				<sethtmlpagefooter name="lastpage" value="on" />
            </div>
        </div>        
    </body>
</html>
