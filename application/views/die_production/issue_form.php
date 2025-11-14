<form data-res_function="getBomResponse">
    <input type="hidden" id="id" name="die_id" value="<?=$id?>">
    <div class="error general_error"></div>
    <table id='bomTable' class="table table-bordered jpExcelTable mb-5">  
        <thead class="text-center">
            <tr>
                <th style="min-width:20px">#</th>
                <th style="min-width:100px">Category</th>
                <th style="min-width:100px">Item</th>
            </tr>
        </thead>
        <tbody id="bomTbodyData" class="text-center">
            <?php
            if(!empty($bomData)){
                $bomCategory = array_reduce($bomData, function($category, $kit) { $category[$kit->category_name][] = $kit;  return $category; }, []);
                $i=1;
                foreach ($bomCategory as $category => $bomArray){
                    $options = "";
                    foreach ($bomArray as $row){
                        $selected = ((!empty($dieData) && $row->ref_item_id == $dieData->item_id)?'selected':'');
                        $options .= '<option value="'.$row->ref_item_id.'" data-category_name="'.$row->category_name.'" data-bom_qty="'.$row->qty.'" data-row_id="'.$i.'" '. $selected.'>'.(!empty($row->item_code) ? "[".$row->item_code."]" : "").$row->item_name." [BOM Qty.".$row->qty.']</option>';
                    }
                    ?>
                        <tr>
                        <td><?=$i?></td>
                        <td><?=$category?></td>
                        <td>
                            <input type="hidden" name="id" id="id" value="<?=!empty($dieData->id)?$dieData->id:''?>">
                            <input type="hidden" name="category_name" id="category_name" value="<?=!empty($dieData->category_name)?$dieData->category_name:$bomArray[0]->category_name?>">              
                            <input type="hidden" name="qty" id="qty" value="<?=!empty($dieData->bom_qty)?$dieData->bom_qty:$bomArray[0]->qty?>">       
                            <select name="item_id" id="item_id" class="form-control select2 itemChange">
                                <?=$options?>
                            </select>
                        </td>
                    </tr>
                    <?php
                    
                    $i++;
                }
            }else{
                ?>
                <th colspan="3" class="text-center">No data available.</th>
                <?php
            }
            ?>
        </tbody>
    </table>
</form>

<script> 
    $(document).ready(function(){
        $(document).on('change','.itemChange',function() {
            var row_id = $(this).find(":selected").data('row_id');
            var bom_qty = $(this).find(":selected").data('bom_qty');
            var category_name = $(this).find(":selected").data('category_name');
            $("#qty").val(bom_qty);
            $("#category_name").val(category_name);
        });
    });   
    
</script>