<form>
<div class="col-md-12">
		<div class="row">
			<input type="hidden" name="id" value="<?= (!empty($prcData->id)) ? $prcData->id : ""; ?>" />
			<input type="hidden" name="production_type" value="<?= (!empty($prcData->production_type)) ? $prcData->production_type : ""; ?>" />
			<div class="col-md-12 form-group" <?=($prcData->status != 1 OR $prcData->production_type == 2)?'hidden':''?>>
				<label for="first_process">Initial Stage</label>
				<select name="first_process" id="first_process" class="form-control select2 req" autocomplete="off">
					<?php
					if(!empty($processList)):
                        foreach($processList as $row):
                            $selected = ((!empty($prcData->first_process) && $prcData->first_process == $row->process_id) ?'selected' : '');
                            ?><option value="<?=$row->process_id?>" <?=$selected?>> <?=$row->process_name?></option><?php
                        endforeach;
                    endif;
					?>
				</select>
			</div>
           
			<div class="col-md-12 form-group">
                <div class="error process_error"></div>
				<?php
                $i =1;
                $acceptArray = [];
                if(!empty($acceptData)){
                    $acceptArray = array_reduce($acceptData, function($acceptArray, $process) { $acceptArray[$process->process_id]= $process; return $acceptArray; }, []);
                }
                $tbody = '';$fixRow="";
                if(!empty($processList)):
                    foreach($processList as $row):
                        $checked = (!empty($prcData->process_ids) ? ((in_array($row->process_id,explode(",",$prcData->process_ids))) ? "checked" : "") : (empty($prcData->process_ids)?'checked':''));
                        $readOnly = (!empty($acceptData) && !empty($acceptArray[$row->process_id]) && ($acceptArray[$row->process_id]->in_qty + $acceptArray[$row->process_id]->pending_accept) > 0)?'onclick="return false"':'';

                        $html = ' <tr id="'.$row->id.'" class="'.(!empty($readOnly)?'fixed':'').'">
                                    <td>'.$i.'</td>
                                    <td class="text-left">
                                        <input type="checkbox" id="md_checkbox_'.$i.'" name="process[]" class="filled-in chk-col-success" value="'.$row->process_id.'" '.$checked.'  '.$readOnly.' ><label for="md_checkbox_'.$i.'" class="mr-10" >'.$row->process_name.'</label>
                                    </td>
                                </tr>';
                        if(!empty($acceptData) && !empty($acceptArray[$row->process_id]) && ($acceptArray[$row->process_id]->in_qty + $acceptArray[$row->process_id]->pending_accept) > 0){
                            $fixRow.=$html;
                        }else{
                            $tbody.=$html;
                        }
                        $i++;
                    endforeach;
                endif;
                ?>
                 <table class="table jpExcelTable mt-3 ">
                    <thead>
                        <tr class="bg-light">
                            <th class="text-center" style="width:5%;">Sr.No.</th>
                            <th class="text-center">In Progress Process</th>
                        </tr>
                    </thead>
                    <tbody >
                    <?=$fixRow?>
                    </tbody>
                </table>
                <table class="table jpExcelTable mt-3 " id="itemProcessData">
                    <thead>
                        <tr class="bg-light">
                            <th class="text-center" style="width:5%;">Sr.No.</th>
                            <th class="text-center">Process Detail</th>
                        </tr>
                    </thead>
                    <tbody id="sortable">
                    <?=$tbody?>
                    </tbody>
                </table>
			</div>
			<div class="error general_error"></div>
			
		</div>
	</div>
</form>
<script>
    $(document).ready(function(){
        $("#sortable").sortable({
            axis: "y",                // Restrict dragging to the vertical axis
            cancel: ".fixed",         // Prevent the first two rows from being dragged
            placeholder: "ui-state-highlight",  // Placeholder for dragging items
            start: function(event, ui) {
                ui.helper.css("z-index", 9999);  // Make dragged item appear on top
            },
            update: function(event, ui) {
                var fixedRows = $(".fixed");  // Get all fixed rows
                var firstFixedRow = fixedRows.first();  // The first fixed row
                var secondFixedRow = fixedRows.last();  // The Last fixed row
                var draggedItem = ui.item;  // The dragged item
                
                // Prevent the dragged item from being placed above or between the fixed rows
                if (draggedItem.index() < firstFixedRow.index()) {
                    // If it's moved above the first fixed row, cancel the move
                    ui.item.closest('tbody').sortable('cancel');
                }

                // Prevent the dragged item from being placed between the fixed rows
                if (draggedItem.index() > firstFixedRow.index() && draggedItem.index() < secondFixedRow.index()) {
                    // If the dragged item is placed between the two fixed rows, cancel the move
                    ui.item.closest('tbody').sortable('cancel');
                }
            }
        });
    }); 
    function fixWidthHelper(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    }
</script>