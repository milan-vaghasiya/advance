<form>
    <div class="row">
        <div class="col-md-12">
            <table class="table jpExcelTable">
                <tr class="text-center  bg-light">
                    <th style="width:20%">ECN No</th>
                    <th style="width:20%">ECN Date</th>
                    <th style="width:20%">Item Code</th>
                </tr>
                <tr class="text-center">
                    <td><?=$ecnData->ecn_no?></td>
                    <td><?=formatDate($ecnData->ecn_date)?></td>
                    <td style="width:15%;"><?=$ecnData->item_code?></td>
                </tr>
                <tr>
                    <th style="width:15%;" class="bg-light text-left">Part Description</th>
                    <td style="width:30%;" class="bg-light text-left" colspan="2"><?=$ecnData->item_name?></td>
                </tr>
            </table>

            <table class="table jpExcelTable" style="margin-top:5px;">
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

            <table class="table jpExcelTable" style="margin-top:5px;">
                <tr>
                    <td  class="bg-light"> 
                        <b>[A] Reason For Change :-</b>
                    </td>
                </tr>
                <tr>
                    <td >
                        <?=$ecnData->change_reason?>
                    </td>
                </tr>
            </table>

            <table class="table jpExcelTable" style="margin-top:5px;">
                <tr>
                    <td  class="bg-light"> 
                        <b>[B] Details Of Change :-</b>
                    </td>
                </tr>
                <tr>
                    <td >
                        <?=$ecnData->change_detail?>
                    </td>
                </tr>
            </table>

            <table class="table jpExcelTable" style="margin-top:5px;">
                <tr>
                    <td colspan="8" class="bg-light"><b>[C] Effect Of Changes & Action :-</b></td>
                </tr>
                <tr>
                    <th style="width:10%">Sr. No.</th>
                    <th style="width:30%">Description of Changes Required</th>
                    <th style="width:25%">Action</th>
                    <th style="width:15%">Changed By</th>
                    <th style="width:20%">Changed At</th>
                </tr>
                <?php
                if(!empty($checkList)){
                    $i=1;
                    foreach($checkList AS $row){
                        ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><?=$row->check_point?></td>
                            <td><?=$row->action_detail?></td>
                            <td><?=$row->emp_name?></td>
                            <td><?=formatDate($row->changed_at)?></td>
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
            <table class="table jpExcelTable text-left" style="margin-top:5px;">
                <thead >
                    <tr>
                        <td colspan="4" class="bg-light"><b>[D] Inventory Details :-</b></td>
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
        </div>
        <div class="col-md-4 form-group">
            <label for="eng_approve_date">Cust.Engineering Approval Date</label>
            <input type="date" name="eng_approve_date" id="eng_approve_date" class="form-control req">
            <input type="hidden" name="id" value="<?=$ecnData->id?>">
        </div>
        <div class="col-md-4 form-group">
            <label for="quality_approve_date">Cust.Quality Approval Date</label>
            <input type="date" name="quality_approve_date" id="quality_approve_date" class="form-control req">
            <input type="hidden" name="id" value="<?=$ecnData->id?>">
        </div>   
        <div class="col-md-4 form-group">
            <label for="other_approve_date">Other Approval Date</label>
            <input type="date" name="other_approve_date" id="other_approve_date" class="form-control req">
            <input type="hidden" name="id" value="<?=$ecnData->id?>">
        </div>
        <div class="col-md-4 form-group">
            <label for="effect_date">Effect Date</label>
            <input type="date" name="effect_date" id="effect_date" class="form-control req">
            <input type="hidden" name="id" value="<?=$ecnData->id?>">
        </div>
    </div>
</form>