<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="structure_id" id="structure_id" class="form-control" value="<?=(!empty($dataRow[0]->structure_id))?$dataRow[0]->structure_id:""?>">

            <div class="col-md-6 form-group">
                <label for="structure_name">Price Structure Name</label>
                <input type="text" name="structure_name" id="structure_name" class="form-control" value="<?=(!empty($dataRow[0]->structure_name))?$dataRow[0]->structure_name:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="is_defualt">Is Defualt ?</label>
                <select name="is_defualt" id="is_defualt" class="form-control">
                    <option value="0" <?=(!empty($dataRow[0]->is_defualt) && $dataRow[0]->is_defualt == 0)?"selected":""?>>No</option>
                    <option value="1" <?=(!empty($dataRow[0]->is_defualt) && $dataRow[0]->is_defualt == 1)?"selected":""?>>Yes</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="penalty_price">Penalty Amount</label>
                <input type="text" name="penalty_price" id="penalty_price" class="form-control floatOnly" value="<?=(!empty($dataRow[0]->penalty_price))?floatval($dataRow[0]->penalty_price):""?>">
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="error item_price_error"></div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Category Name</th>
                            <th>GST Per %</th>
                            <th style="width:15%;">MRP</th>
                            <th style="width:15%;">Dealer MRP</th>
                            <th style="width:15%;">Retail MRP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $itemData = array();
                            if(!empty($dataRow)):
                                foreach($dataRow as $row):
                                    $itemData[$row->item_id] = $row;
                                endforeach;
                            endif;

                            $i =1;
                            foreach($itemList as $row):
                                $id = (!empty($itemData[$row->id]->id))?$itemData[$row->id]->id:"";
                                $mrp = (!empty($itemData[$row->id]->mrp))?floatval($itemData[$row->id]->mrp):0;
                                $dealer_mrp = (!empty($itemData[$row->id]->dealer_mrp))?floatval($itemData[$row->id]->dealer_mrp):0;
                                $retail_mrp = (!empty($itemData[$row->id]->retail_mrp))?floatval($itemData[$row->id]->retail_mrp):0;

                                echo '<tr>
                                    <td>'.$i.'</td>
                                    <td>
                                        '.$row->item_name.'
                                        <input type="hidden" name="itemData['.$i.'][id]" id="id_'.$i.'" value="'.$id.'">
                                        <input type="hidden" name="itemData['.$i.'][item_id]" id="item_id_'.$i.'" value="'.$row->id.'">
                                        <input type="hidden" name="itemData['.$i.'][gst_per]" id="gst_per_'.$i.'" value="'.floatVal($row->gst_per).'">
                                    </td>
                                    <td>'.$row->category_name.'</td>
                                    <td>'.floatVal($row->gst_per).'</td>
                                    <td>
                                        <input type="text" name="itemData['.$i.'][mrp]" id="mrp_'.$i.'" class="form-control floatOnly" value="'.$mrp.'">
                                    </td>
                                    <td>
                                        <input type="text" name="itemData['.$i.'][dealer_mrp]" id="price_'.$i.'" class="form-control floatOnly" value="'.$dealer_mrp.'">
                                    </td>
                                    <td>
                                        <input type="text" name="itemData['.$i.'][retail_mrp]" id="price_'.$i.'" class="form-control floatOnly" value="'.$retail_mrp.'">
                                    </td>
                                </tr>';
                                $i++;
                            endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    /* $(document).on('change','#structure_id',function(){
        var structure_name = ($(this).val() != "")?$(this).find(':selected').text():"";
        $("#structure_name").val(structure_name);
    }); */

    /* $(document).on('change keyup','.calculatePrice',function(){
        var row_id = $(this).data('row_id');
        var gst_per = $("#gst_per_"+row_id).val() || 0;
        var price = $("#price_"+row_id).val() || 0;
        var mrp = $("#mrp_"+row_id).val() || 0;

        if(gst_per > 0){
            if($(this).attr('id') == "price_"+row_id){
                if(parseFloat(price) > 0){
                    var tax_amt = parseFloat( (parseFloat(price) * parseFloat(gst_per) ) / 100 ).toFixed(2);
                    var new_mrp = parseFloat( parseFloat(price) + parseFloat(tax_amt) ).toFixed(2);
                    $("#mrp_"+row_id).val(new_mrp);                    
                }else{
                    $("#mrp_"+row_id).val(0);
                }
                return true;
            }

            if($(this).attr('id') == "mrp_"+row_id){
                if(parseFloat(mrp) > 0){
                    var gstReverse = parseFloat(( ( parseFloat(gst_per) + 100 ) / 100 )).toFixed(2);
                    var new_price = parseFloat( parseFloat(mrp) / parseFloat(gstReverse) ).toFixed(2);
                    $("#price_"+row_id).val(new_price);                    
                }else{
                    $("#price_"+row_id).val(0);
                }
                return true;
            }
        }else{
            if($(this).attr('id') == "price_"+row_id && price > 0){
                $("#mrp_"+row_id).val(price);
                return true;
            }

            if(mrp > 0){
                $("#price_"+row_id).val(mrp);
                return true;
            }
        }
        
    }); */
});
</script>