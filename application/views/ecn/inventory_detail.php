<form>
    <div class="col-md-12">
        <div class="row form-group">
            <input type="hidden" id="id" name="id" value="<?=$dataRow->id?>" />
            <?php
            $fgData = explode("~",$dataRow->fg_stock);
            $wipData = explode("~",$dataRow->wip_stock);
            $rmData = explode("~",$dataRow->rm_stock);
            $fg_stock = ((!empty($fgData[0]))?$fgData[0]:''); $fg_action = ((!empty($fgData[1]))?$fgData[1]:'');

            $wip_stock = ((!empty($wipData[0]))?$wipData[0]:''); $wip_action = ((!empty($wipData[1]))?$wipData[1]:'');
            
            $rm_stock = ((!empty($rmData[0]))?$rmData[0]:''); $rm_action = ((!empty($rmData[1]))?$rmData[1]:'');
            ?>
            <div class="error general_error"></div>
            <div class="table-responsive">
                <table id="inventoryTable" class="table jpExcelTable">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:15%;">Detail</th>
                            <th style="width:15%;">System Stock</th>
                            <th style="width:15%;">Stock To be Consider</th>
                            <th style="width:55%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Existing</th>
                            <td><?=((!empty($stockData->fg_stock))?floatval($stockData->fg_stock):0)?></td>
                            <td>
                                <input type="text" name="fg_stock" id="fg_stock" value="<?=(!empty($fg_stock)?$fg_stock:((!empty($stockData->fg_stock))?floatval($stockData->fg_stock):0))?>" class="form-control floatOnly">
                            </td>
                            <td>
                                <input type="text" name="fg_action" id="fg_action" value="<?=$fg_action?>" class="form-control">
                            </td>
                        </tr>
                        <tr>
                            <th>Inprocess</th>
                            <td><?=((!empty($stockData->wip_stock))?floatval($stockData->wip_stock):0)?></td>
                            <td>
                                <input type="text" name="wip_stock" id="wip_stock" value="<?=((!empty($wip_stock))?$wip_stock:((!empty($stockData->wip_stock))?floatval($stockData->wip_stock):0))?>" class="form-control floatOnly">
                            </td>
                            <td>
                                <input type="text" name="wip_action" id="wip_action" value="<?= $wip_action?>" class="form-control">
                            </td>
                        </tr>
                        <tr>
                            <th>Raw Material</th>
                            <td><?=((!empty($stockData->rm_stock))?floatval($stockData->rm_stock):0)?></td>
                            <td>
                                <input type="text" name="rm_stock" id="rm_stock" value="<?=((!empty($rm_stock))?$rm_stock:((!empty($stockData->rm_stock))?floatval($stockData->rm_stock):0))?>" class="form-control floatOnly">
                            </td>
                            <td>
                                <input type="text" name="rm_action" id="rm_action" value="<?=$rm_action?>" class="form-control">
                            </td>
                        </tr>
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</form>
