<form data-res_function="prcResponse">
    <input type="hidden" id="prc_id" name="prc_id" value="<?=$dataRow->prc_id?>">
    <input type="hidden" id="prc_process_id" name="prc_process_id" value="<?=$dataRow->id?>">
    <input type="hidden" id="process_id" name="process_id" value="<?=$dataRow->current_process_id?>">
    <input type="hidden" id="next_process_id" name="next_process_id" value="<?=$dataRow->next_process_id?>">
    <div class="error general_error"></div>
    <table id='movementTransTable' class="table table-bordered mb-5">  
        <thead class="text-center">
            <tr>
                <th style="min-width:20px">#</th>
                <th style="min-width:100px">Date</th>
                <th>Store</th>
                <th>Qty.</th>
                <th>Move To Next</th>
            </tr>
        </thead>
        <tbody id="movementTbodyData">
            <?php
            if(!empty($movementList)){
                $i = 1;
                foreach($movementList as $row){
                    if($row->qty > 0){?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=formatDate($row->trans_date)?></td>
                            <td><?=$row->store_name?></td>
                            <td><?=$row->qty?></td>
                            <td>
                                <input type="text" name="qty[]" id="qty_<?=$row->id?>" class="form-control numericonly" >
                                <input type="hidden" name="trans_id[]" id="trans_id_<?=$row->id?>" value="<?=$row->id?>">
                                <div class="error qty_<?=$row->id?>"></div>
                            </td>
                        </tr>
                        <?php
                    }
                    
                }
            }
            ?>
        </tbody>
    </table>
</form>

<script>

</script>