<form action="" method="post" target="_blank">    
    <input type="hidden" name="inv_no" value="<?=$inv_no?>">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="table table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Size</th>
                                <th>Weight/Pcs.</th>
                                <th>Qty</th>
                                <th>Total Weight</th>
                                <th>Box</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $i=1;
                                foreach($itemList as $row):
                                    $row->box = (!empty($row->packing_qty))?ceil($row->qty/$row->packing_qty):0;
                                    $row->total_weight = round(($row->wkg * $row->qty),3);
                                    echo '<tr>
                                        <td>
                                            '.$row->size.'
                                            <input type="hidden" name="itemData['.$i.'][size]" value="'.$row->size.'">
                                        </td>
                                        <td>
                                            '.$row->wkg.'
                                            <input type="hidden" name="itemData['.$i.'][weight]" value="'.$row->wkg.'">
                                        </td>
                                        <td>
                                            '.$row->qty.'
                                            <input type="hidden" name="itemData['.$i.'][qty]" value="'.$row->qty.'">
                                        </td>
                                        <td>
                                            '.$row->total_weight.'
                                            <input type="hidden" name="itemData['.$i.'][total_weight]" value="'.$row->total_weight.'">
                                        </td>
                                        <td>
                                            '.$row->box.'
                                            <input type="hidden" name="itemData['.$i.'][box]" value="'.$row->box.'">
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
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="table table-responsive">
                    <table id="itemTable" class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th colspan="3" class="text-center">Other Item Details</th>
                            </tr>
                            <tr>
                                <th>Item Name</th>
                                <th>Weight</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="otherItem">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    <input type="text" name="otherItem[999][item_name]" id="item_name" class="form-control" placeholder="Item Name" value="">
                                </td>
                                <td>
                                    <input type="text" name="otherItem[999][weight]" id="weight" class="form-control floatOnly" placeholder="Weight" value="">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-primary addOtherItem"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="col-md-12 form-group">
                <input type="checkbox" id="cc_attached" name="cc_attached" class="filled-in chk-col-success" value="CC Attached">
                <label for="cc_attached">CC Attached</label>
            </div>
        </div>
    </div>

    <input type="submit" class="hidden" id="submit_btn">
</form>
<script>
var oiCount = 0;
$(document).ready(function(){
    $(document).on('click','.addOtherItem',function(e){
        e.stopImmediatePropagation();
        e.preventDefault();

        var html = '<tr>';

        html += '<td>';
        html += '<input type="text" name="otherItem['+oiCount+'][item_name]" id="item_name_'+oiCount+'" class="form-control" placeholder="Item Name" value="'+$("#item_name").val()+'">';
        html += '</td>';

        html += '<td>';
        html += '<input type="text" name="otherItem['+oiCount+'][weight]" id="weight_'+oiCount+'" class="form-control floatOnly" placeholder="Weight" value="'+$("#weight").val()+'">';
        html += '</td>';

        html += '<td class="text-center">';
        html += '<button type="button" class="btn btn-outline-danger" onclick="removeItem(this);"><i class="fa fa-trash"></i></button>';
        html += '</td>';

        html += '</tr>';

        $("#otherItem").append(html);
        $("#item_name").val("");
        $("#weight").val("");
        $("#item_name").focus();
        oiCount++;
    });
});

function removeItem(btn){
    var row = $(btn).closest("TR");
	var table = $("#itemTable")[0];
	table.deleteRow(row[0].rowIndex);
}
</script>