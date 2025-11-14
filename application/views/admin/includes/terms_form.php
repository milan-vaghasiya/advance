<div class="modal modal-left fade" id="termModel" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Terms & Conditions</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 mb-10">
                    <table id="terms_condition" class="table table-bordered dataTable no-footer">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th style="width:20%;">Title</th>
                                <th style="width:75%;">Condition</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($termsList)) :
                                $termaData = $termsConditions;
                                $i = 1;$j = 0;
                                foreach ($termsList as $row) :
                                    $checked = ($row->is_default == 1 && empty($termaData))?"checked":"";
                                    $disabled = ($row->is_default != 1 && empty($termaData))?"disabled":"";
                                    
                                    if(!empty($termaData)):
                                        if(in_array($row->id, array_column($termaData, 'term_id'))) :
                                            $checked = "checked";
                                            $disabled = "";
                                            $row->conditions = $termaData[$j]->condition;
                                            $j++;
                                        else:
                                            $checked = "";
                                            $disabled = "disabled";
                                        endif;
                                    endif;
                            ?>
                                    <tr>
                                        <td  class="text-center">
                                            <input type="checkbox" id="md_checkbox<?= $i ?>" class="filled-in chk-col-success termCheck" data-rowid="<?= $i ?>" check="<?= $checked ?>" <?= $checked ?> />
                                            <label for="md_checkbox<?= $i ?>"><?= $i ?></label>
                                        </td>
                                        <td>
                                            <?= $row->title ?>
                                            <input type="hidden" name="termsData[<?= $i ?>][i_col_1]" id="term_id<?= $i ?>" value="<?= $row->id ?>" <?= $disabled ?> />
                                            <input type="hidden" name="termsData[<?= $i ?>][t_col_1]" id="term_title<?= $i ?>" value="<?= $row->title ?>" <?= $disabled ?> />
                                        </td>
                                        <td>
                                            <input type="text" name="termsData[<?= $i ?>][t_col_2]" id="condition<?= $i ?>" class="form-control" value="<?= $row->conditions ?>" <?= $disabled ?> />
                                        </td>
                                    </tr>
                                <?php
                                    $i++;
                                endforeach;
                            else :
                                ?>
                                <tr>
                                    <td class="text-center" colspan="3">No data available in table</td>
                                </tr>
                            <?php
                            endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary press-close-btn btn-close-modal" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn btn-success btn-terms-save" data-bs-dismiss="modal"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>
