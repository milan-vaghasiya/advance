<form id="vendorChallanForm" data-res_function="challanResponse">
    <div class="row">
        <div class="col-md-2 form-group">
            <label for="ch_number">Challan Date</label>
            <input type="text" name="ch_number" id="ch_number" class="form-control req" value="<?=$ch_prefix.str_pad($ch_no,2,0,STR_PAD_LEFT)?>" readonly>
        </div>
        <div class="col-md-2 form-group">
            <label for="ch_date">Challan Date</label>
            <input type="date" name="ch_date" id="ch_date" class="form-control req" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-md-8 form-group">
            <label for="party_id">Vendor</label>
            <select name="party_id" id="party_id" class="form-control select2 req">
                <option value="">Select Vendor</option>
                <?php
                if(!empty($vendorList)){
                    foreach($vendorList as $row){
                        $selected = (!empty($vendor_id) && $vendor_id== $row->id)?'selected':'';
                        ?>
                        <option value="<?=$row->id?>" <?=$selected?> data-mhr="<?=$row->mhr?>"><?=$row->party_name?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        
        <div class="col-md-12">
            <div class="table-responsive">
                <div class="error general_error"></div><br>
                <table id='outsourceTransTable' class="table table-bordered jpDataTable colSearch">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <th class="text-center" style="width:5%;">#</th>
                            <th class="text-center" style="width:10%;">Job No.</th>
                            <th class="text-center" style="width:10%;">Job Date</th>
                            <th class="text-center" style="width:15%;">Die Type</th>
                            <th class="text-center" style="width:15%;">Product</th>
                            <th class="text-center" style="width:15%;">Material Price</th>
                            <th class="text-center" style="width:15%;">MHR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($dieProdList)) {
                            $i=1;
                            foreach ($dieProdList as $row) {
                                $material_value = 0;$material_cost=0;$material_weight=0;$process_cost=0;
                                if($row->trans_type == 1){
                                    $material_value = $row->rm_price;
                                    $material_cost=$row->rm_price*$row->issue_qty;
                                    $material_weight = $row->issue_qty;
                                }else{
                                    $material_value = $row->material_rate;
                                    $material_cost=$row->total_value;
                                    $material_weight = $row->material_weight;
                                }
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" id="md_checkbox_<?= $i ?>" name="dp_id[]" class="filled-in chk-col-success challanCheck" data-rowid="<?= $i ?>" value="<?= $row->id ?>"  checked><label for="md_checkbox_<?= $i ?>" class="mr-3"></label>
                                        <input type="hidden" name="item_id[]"  value="<?=$row->item_id?>" class="checkRow<?= $i ?>" >
                                    </td>
                                    <td><?=$row->trans_number?></td>
                                    <td><?=formatDate($row->trans_date)?></td>
                                    <td><?=$row->category_name?></td>
                                    <td><?=(($row->fg_item_code) ? "[".$row->fg_item_code."] " : "").$row->fg_item_name?></td>
                                    <td>
                                        <input type="text" name="material_cost[]" value="<?=$material_cost?>" class="form-control checkRow<?= $i ?>" >
                                        <input type="hidden" name="material_value[]" value="<?=$material_value?>" class="form-control checkRow<?= $i ?>" >
                                        <input type="hidden" name="material_weight[]" value="<?=$material_weight?>" class="checkRow<?= $i ?>" >
                                    </td>
                                    <td>
                                        <input type="text" name="mhr[]" value="" class="form-control mhr checkRow<?= $i ?>" >
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="5" class="text-center">No data available in table</td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $(document).on("click", ".challanCheck", function() {
            var id = $(this).data('rowid');
            $(".error").html("");
            if (this.checked) {
                $(".checkRow" + id).removeAttr('disabled');
            } else {
                $(".checkRow" + id).attr('disabled', 'disabled');
            }
        });
    });
</script>