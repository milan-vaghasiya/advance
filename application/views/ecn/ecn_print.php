<div class="row">
        <div class="col-md-12">
            <table class="table" style="margin-top:5px;">
                <tr>
                    <th style="font-size:16px;">Engineering Change Note Review Report (ECN)</th>
                </tr>
            </table>
            <table class="table item-list-bb">
                <tr class="text-center  bg-light">
                    <th style="width:20%">ECN No</th>
                    <th style="width:20%">ECN Date</th>
                    <th style="width:20%">Effect Date</th>
                    <th style="width:20%">Item Code</th>
                </tr>
                <tr class="text-center">
                    <td><?=$ecnData->ecn_no?></td>
                    <td><?=formatDate($ecnData->ecn_date)?></td>
                    <td><?=formatDate($ecnData->effect_date)?></td>
                    <td style="width:15%;"><?=$ecnData->item_code?></td>
                </tr>
                <tr>
                    <th style="width:15%;" class="bg-light text-left">Part Description</th>
                    <td style="width:30%;" class="bg-light text-left" colspan="3"><?=$ecnData->item_name?></td>
                </tr>
            </table>

            <table class="table item-list-bb" style="margin-top:5px;">
                <tr class="text-center">
                    <th colspan="2" class="bg-light">OLD Revision</th>
                    <th colspan="2" class="bg-light">New Revision</th>
                </tr>
                <tr  class="text-left">
                    <th style="width:20%">Rev. No.</th>
                    <td style="width:30%"><?=(!empty($oldRevData->rev_no) ? $oldRevData->rev_no : '')?></td>
                    <th style="width:20%">Rev. No.</th>
                    <td style="width:30%"><?=$ecnData->rev_no?></td>
                </tr>
                <tr  class="text-left">
                    <th>Rev. Date</th>
                    <td><?=(!empty($oldRevData->rev_date) ? formatDate($oldRevData->rev_date) : '')?></td>
                    <th>Rev. Date</th>
                    <td><?=formatDate($ecnData->rev_date)?></td>
                </tr>
                <tr  class="text-left">
                    <th style="width:20%">Cust. Rev. No.</th>
                    <td style="width:30%"><?=(!empty($oldRevData->cust_rev_no) ? $oldRevData->cust_rev_no : '')?></td>
                    <th style="width:20%">Cust. Rev. No.</th>
                    <td style="width:30%"><?=$ecnData->cust_rev_no?></td>
                </tr>
                <tr  class="text-left">
                    <th>Cust. Rev. Date</th>
                    <td><?=(!empty($oldRevData->cust_rev_date) ? formatDate($oldRevData->cust_rev_date) : '')?></td>
                    <th>Cust. Rev. Date</th>
                    <td><?=formatDate($ecnData->cust_rev_date)?></td>
                </tr>
            </table>

            <table class="table item-list-bb" style="margin-top:5px;">
                <tr>
                    <td  class="bg-light"> 
                        <b>[A] Reason For Change :-</b>
                        <?=$ecnData->change_reason?>
                    </td>
                </tr>
            </table>

            <table class="table item-list-bb" style="margin-top:5px;">
                <tr>
                    <td  class="bg-light"> 
                        <b>[B] Details Of Change :-</b>
                        <?=$ecnData->change_detail?>
                    </td>
                </tr>
            </table>

            <table class="table item-list-bb" style="margin-top:5px;">
                <tr>
                    <td colspan="5" class="bg-light"><b>[C] Effect Of Changes & Action :-</b></td>
                </tr>
                <tr>
                    <th style="width:8%">Sr. No.</th>
                    <th style="width:30%">Description of Changes Required</th>
                    <th style="width:30%">Action</th>
                    <th style="width:20%">Changed By</th>
                    <th style="width:12%">Changed At</th>
                </tr>
                <?php
                if(!empty($checkList)){
                    $i=1;
                    foreach($checkList AS $row){
                        ?>
                        <tr>
                            <td class="text-center"><?=$i++?></td>
                            <td><?=$row->check_point?></td>
                            <td><?=$row->action_detail?></td>
                            <td><?=$row->emp_name?></td>
                            <td class="text-center"><?=formatDate($row->changed_at)?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                
            </table>
            <?php
                $fgData = explode("~",$ecnData->fg_stock);
                $wipData = explode("~",$ecnData->wip_stock);
                $rmData = explode("~",$ecnData->rm_stock);
                $fg_stock = ((!empty($fgData[0]))?$fgData[0]:''); $fg_action = ((!empty($fgData[1]))?$fgData[1]:'');

                $wip_stock = ((!empty($wipData[0]))?$wipData[0]:''); $wip_action = ((!empty($wipData[1]))?$wipData[1]:'');
                
                $rm_stock = ((!empty($rmData[0]))?$rmData[0]:''); $rm_action = ((!empty($rmData[1]))?$rmData[1]:'');
            ?>
            <table class="table item-list-bb text-left" style="margin-top:5px;">
                <thead >
                    <tr>
                        <td colspan="3" class="bg-light"><b>[D] Inventory Details :-</b></td>
                    </tr>
                    <tr>
                        <th style="width:15%;">Detail</th>
                        <th style="width:15%;">Stock To be Consider</th>
                        <th style="width:55%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Existing</th>
                        <td> <?=(!empty($fg_stock)?$fg_stock:'')?> </td>
                        <td> <?=$fg_action?> </td>
                    </tr>
                    <tr>
                        <th>Inprocess</th>
                        <td> <?=(!empty($wip_stock)?$wip_stock:'')?> </td>
                        <td> <?=$wip_action?> </td>
                    </tr>
                    <tr>
                        <th>Raw Material</th>
                        <td> <?=(!empty($rm_stock)?$rm_stock:'')?> </td>
                        <td> <?=$rm_action?> </td>
                    </tr>
                </tbody>
            </table>
            <table class="table item-list-bb" style="margin-top:5px;">
                <tr>
                    <td  class="bg-light"> 
                        <b>Note:-</b> <?=$ecnData->remark?>
                    </td>
                </tr>
               
            </table>

            <htmlpagefooter name="lastpage">
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                        <tr>
							<td style="width:50%;" rowspan="4"></td>
							<th colspan="2">For, <?=$companyData->company_name?></th>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"><?=$ecnData->prepareBy?></td>
							<td style="width:25%;" class="text-center"><?=$ecnData->approveBy?></td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
							<td style="width:25%;" class="text-center"><b>Authorised By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>
                </htmlpagefooter>
				<sethtmlpagefooter name="lastpage" value="on" />
        </div>
        
    </div