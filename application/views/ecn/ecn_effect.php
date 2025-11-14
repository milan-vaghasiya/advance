<form>
    <div class="col-md-12">
        <div class="row form-group">
            <input type="hidden" id="ecn_id" name="ecn_id" value="<?=$ecn_id?>" />

            <div class="error general_error"></div>
            <div class="table-responsive">
                <table id="changesTable" class="table jpExcelTable">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:20%;">Description Of Changes</th>
                            <th style="width:20%;">Action</th>
                            <th style="width:20%;">Changed By </th>
                            <th style="width:20%;">Changed At</th>
                        </tr>
                    </thead>
                    <tbody id="tempItem" class="temp_item">
                        <?php 		
                            $actionDetail = array();
                            if(!empty($dataRow)){
                                $actionDetail = array_reduce($dataRow, function($actionDetail, $action) { $actionDetail[$action->ecn_checklist_id] = $action; return $actionDetail; }, []);
                            }								
                            if(!empty($checkList)): 
                            $i=1;
                            foreach($checkList as $row):
                                $disabled  = ((!empty($actionDetail[$row->id]))?'':'disabled');
                                $checked  = (!empty($actionDetail[$row->id]))?'checked':'';
                            ?>
                            <tr>
                                <td style="width:5%;">
                                <input type="checkbox" id="md_checkbox_<?= $row->id ?>" name="ecn_checklist_id[]" class="filled-in chk-col-success checkEffect" data-rowid="<?=$row->id ?>" value="<?= $row->id ?>"  <?=$checked?>><label for="md_checkbox_<?= $row->id ?>" class="mr-3"></label>
                                </td>
                                <td>
                                    <?=$row->description?>
                                    <input type="hidden" id="id<?=$i?>" name="id[]" value="<?=((!empty($actionDetail[$row->id]->id))?$actionDetail[$row->id]->id:'')?>" class="checkRow<?=$row->id?>" <?=$disabled?>>
                                </td>
                               
                                <td>
                                    <input type="text" id="action_detail<?=$row->id?>" name="action_detail[]" class="form-control checkRow<?=$row->id?>" value="<?=((!empty($actionDetail[$row->id]->action_detail))?$actionDetail[$row->id]->action_detail:'')?>" <?=$disabled?>>

                                    <div class="error action_detail<?=$row->id?>"></div>
                                </td>
                                <td>
                                    <select name="changed_by[]" id="changed_by<?=$row->id?>" class="form-control select2 checkRow<?=$row->id?>" <?=$disabled?>>
                                        <option value="">Select Employee</option>
                                        <?php
                                            foreach ($empData as $row1) :
                                                $selected = (!empty($actionDetail[$row->id]->changed_by) && ($row1->id == $actionDetail[$row->id]->changed_by)) ? "selected" : "";
                                                echo '<option value="' . $row1->id . '" '.$selected.'>' . $row1->emp_name . '</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                    <div class="error changed_by<?=$row->id?>"></div>
                                </td>
                                <td>
                                    <input type="date" id="changed_at<?=$row->id?>" name="changed_at[]" class="form-control checkRow<?=$row->id?>" value="<?=(!empty($actionDetail[$row->id]->changed_at) ? $actionDetail[$row->id]->changed_at : date("Y-m-d"))?>" <?=$disabled?>>
                                    <div class="error changed_at<?=$row->id?>"></div>
                                </td>
                            </tr>
                        <?php $i++; endforeach; else: ?>
                        <tr id="noData">
                            <td colspan="5" class="text-center">No data available in table</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</form>
<script>
  $(document).ready(function() {
        
        $(document).on("click", ".checkEffect", function() {
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