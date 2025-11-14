<div class="row">
    <div class="col-12">
        <?php if(!empty($header_footer)): ?>
        <table>
            <tr>
                <td>
                    <?php if(!empty($companyData->print_header)): ?>
                        <img src="<?=base_url($companyData->print_header)?>" class="img">
                    <?php endif;?>
                </td>
            </tr>
        </table>
        <?php endif; ?>
        
        <table class="table bg-light-grey">
            <tr class="" style="letter-spacing: 2px;font-weight:bold;padding:2px !important; border-bottom:1px solid #000000;">
                <th style="width:33%;" class="fs-18 text-left">
                </th>
                <th style="width:33%;" class="fs-18 text-center"><?=(($pvData->vou_name_s == "BCRct")?"Payment Receipt":"Payment Voucher")?></th>
                <th style="width:33%;" class="fs-18 text-right"><?=$printType?></th>
            </tr>
        </table>
        
        <table class="table item-list-bb fs-22" style="margin-top:5px;">
            <tr>
                <td>
                    <b>Voucher No. <?=$pvData->trans_number?></b>
                </td>
                <td>
                    <b>Voucher Date. <?=formatDate($pvData->trans_date)?></b>
                </td>
            </tr>
        </table>
        <?php 
        $title = ($pvData->payment_mode == 'CASH')?'Cash':'Bank';
        $invNo = (!empty($pvData->inv_prefix && $pvData->inv_no))? '<b><u>'.$pvData->inv_prefix.$pvData->inv_no.'</u></b>':'________';
		$invDate = (!empty($pvData->inv_date))? '<b><u>'.formatDate($pvData->inv_date).'</u></b>':'________';
		$refNo = (!empty($pvData->doc_no))?'<b><u>'.$pvData->doc_no.'</u></b>':'________';
		$refDate = (!empty($pvData->doc_date))?'<b><u>'.formatDate($pvData->doc_date).'</u></b>':'________';
		$rno = ($title != 'Cash' && !empty($refNo))?' Ref No.: '.$refNo.' Dt.: '.$refDate:'';
        ?>
        <table class="table item-list-bb fs-22" style="margin-top:5px;">
            <tr>
                <td style="width:100%; vertical-align:top;">
                    Received with thanks from M/s. <b><?=$pvData->party_name?></b><br><br>              
                    The sum of Ruppes : <b><?=numToWordEnglish($pvData->net_amount)?></b><br><br>
                    By <?= $pvData->payment_mode.$rno ?> against advance / full / part payment of our Bill No. <?= $invNo ?>  Dt. <?= $invDate ?><br><br>  
                    Ruppes : <b><?= $pvData->net_amount ?> /-</b><br><br><br>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom:30px;">
                    <b>Note: </b> <?= $pvData->remark ?>
                </td> 
            </tr>
            <tr>
                <td colspan="3" class="text-right">
                    <b>For, <?= $companyData->company_name ?></b> <br><br><br><br><br><br><br>
                    
                    <i>(Authorised Signatory)</i>
                </td>
            </tr>
        </table>
        
        <!-- <table class="table top-table" style="margin-top:5px;border-top:1px solid #545454;">
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
        </table> -->
    </div>
</div>