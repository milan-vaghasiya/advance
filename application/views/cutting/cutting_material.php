<form data-res_function="getBomResponse">
    <?php
    $cut_weight = ((!empty($cut_weight) && $cut_weight > 0)?$cut_weight:((!empty($kitData[0]->qty))?$kitData[0]->qty:0));
    ?>
    <input type="hidden" id="prc_id" name="prc_id" value="<?=$prc_id?>">
    <input type="hidden" id="fg_item_id" name="fg_item_id" value="<?=$fg_item_id?>">
    <input type="hidden" id="prc_qty" name="prc_qty" value="<?=$prc_qty?>">
    <input type="hidden" id="process_id" name="process_id" value="3">
    <input type="hidden" id="ppc_qty" name="ppc_qty" value="<?=$cut_weight?>">
    <input type="hidden" id="id" name="id" value="<?=!empty($prcBom->id)?$prcBom->id:''?>">
    <div class="col-md-6 form-group">
        <label for="item_id">Raw Material</label>
        <select name="item_id" id="item_id" class="form-control select2 itemChange">
            <?php
                if(!empty($kitData)){
                    foreach($kitData AS $row){
                        $selected = (!empty($prcBom->item_id) && $prcBom->item_id == $row->ref_item_id)?'selected':'';
                        $disabled = (!empty($prcBom->item_id) && $prcBom->item_id != $row->ref_item_id)?'disabled':'';

                        ?> <option value="<?=$row->ref_item_id?>" data-bom_qty="<?=$row->qty?>" <?=$selected?> <?=$disabled?>><?=$row->item_name?></option> <?php
                    }
                }
            ?>
        </select>
    </div>
    <hr>
    
    <div class="float-right">
        <h6 class="text-primary">Required Material : <?=($cut_weight * $prc_qty).' '.((!empty($kitData[0]->uom))?$kitData[0]->uom:'')?></h6>
    </div><div class="error table_err"></div>
    <table class="table table-bordered">
        <thead class="thead-info">
            <th>Location</th>
            <th>Batch No.</th>
            <th>Stock Qty.</th>
            <th>Issue Qty.</th>
        </thead>
        <tbody id="tbodyData">
            
        </tbody>
    </table>
    </div>
</form>

<script>
    $(document).ready(function(){
        
        setTimeout(function(){ $('.itemChange').trigger('change'); }, 10);

        $(document).on('change','.itemChange',function(e) {
            e.stopImmediatePropagation();e.preventDefault();
            var item_id = $(this).val();
            var fg_item_id = $("#fg_item_id").val();
            if(item_id){
                $.ajax({
                    url:base_url + "store/getBatchWiseStock",
                    type:'post',
                    data:{item_id:item_id,fg_item_id:fg_item_id},
                    dataType:'json',
                    success:function(data){
                        if(data.status == 1){
                            $('#tbodyData').html('');
                            $('#tbodyData').html(data.tbodyData);
                        }
                    }
                });
            }
        });
    });
</script>