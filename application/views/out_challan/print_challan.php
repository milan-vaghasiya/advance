<div class="row">
	<div class="col-12">	
        <table class="table top-table-border">
			<tr>
				<td rowspan="2" style="width:55%;vertical-align:top;">
					<b>M/S. </b><?=(!empty($partyData->party_name) ? $partyData->party_name : '')?><br>
					<?=(!empty($partyData->party_address) ? $partyData->party_address : '')?><br>
					<b>Kind. Atte. : </b><?=(!empty($partyData->contact_person) ? $partyData->contact_person : '')?><br>
					<b>Mo.: </b><?=(!empty($partyData->party_mobile) ? $partyData->party_mobile : '')?><br>
					<b>E-mail: </b><?=(!empty($partyData->party_email) ? $partyData->party_email : '')?><br><br>
					<b>GSTIN: </b><?=(!empty($partyData->gstin) ? $partyData->gstin : '')?>
				</td>
				<th style="width:20%;vertical-align:top;">Ch. No.</th>
				<td style="width:25%;vertical-align:top;"><?=(!empty($dataRow->trans_number) ? $dataRow->trans_number : '')?></td>
			</tr>
			<tr>
				<th style="width:20%;vertical-align:top;">Ch. Date</th>
                <td style="width:20%;vertical-align:top;">
                    <?=(!empty($dataRow->trans_date) ? date('d/m/Y', strtotime($dataRow->trans_date)) : '')?>
                </td>
			</tr>
		</table>

		<table class="table item-list-bb" style="margin-top:10px;">
            <thead>
                <tr class="text-center">
                    <th style="width:10%;">Sr.No.</th>
                    <th style="width:70%;">Item Name</th>
                    <th style="width:20%;">Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $i=1; $total_qty=0;
                    if(!empty($dataRow->itemList)):
                        foreach($dataRow->itemList as $row):
                            $rowspan = (!empty($row->item_remark) ? '2': '1');
                            echo '<tr>
                                <td class="text-center" rowspan='.$rowspan.'>'.$i.'</td>
                                <td>'.(!empty($row->item_code) ? '['.$row->item_code.'] ' : '').$row->item_name.'</td>
                                <td class="text-center" rowspan='.$rowspan.'>'.(!empty($row->qty) ? sprintf('%0.2f', $row->qty) : 0).'</td>
                            </tr>';
                            echo (!empty($row->item_remark) ? '<tr><td><b>Remark : </b>'.$row->item_remark.'</td></tr>' : '');

                            $total_qty += $row->qty;
                            $i++;
                        endforeach;

                        echo '<tr>
                            <th class="text-right" colspan="2">Total Qty</th>
                            <th>'.sprintf('%0.2f', $total_qty).'</th>
                        </tr>';

                        echo '<tr>
                            <td colspan="3"><b>Notes : </b>'.(!empty($dataRow->remark) ? $dataRow->remark : '').'</td>
                        </tr>';
                    endif;
                ?>
			</tbody>
		</table>

        <htmlpagefooter name="lastpage">
            <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:40%;" rowspan="4"></td>
                    <th colspan="2">For, <?=$companyData->company_name?></th>
                </tr>
                <tr>
                    <td style="width:30%;" class="text-center"><?=(!empty($dataRow->prepared_by) ? $dataRow->prepared_by : '')?></td>
                    <td style="width:30%;" class="text-center"></td>
                </tr>
                <tr>
                    <td style="width:30%;" class="text-center"><b>Prepared By</b></td>
                    <td style="width:30%;" class="text-center"><b>Authorised By</b></td>
                </tr>
            </table>
            <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:50%;">Ch. No. & Date : <?=$dataRow->trans_number.' ['.formatDate($dataRow->trans_date).']'?></td>
                    <td style="width:25%;"></td>
                    <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>
        </htmlpagefooter>
        <sethtmlpagefooter name="lastpage" value="on" />
	</div>
</div>