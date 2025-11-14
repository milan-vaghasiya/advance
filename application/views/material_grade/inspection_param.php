<form>
    <div class="col-md-12">
        <div class="error generalError"></div>
        <div class="row">
            <input type="hidden" name="grade_id" id="grade_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            
            <div class="col-md-4 form-group">
                <label for="micro_structure">Micro Structure</label>
                <input type="text" name="micro_structure" class="form-control " value="<?=(!empty($dataRow->micro_structure))?$dataRow->micro_structure:""; ?>" />
            </div>
            
            <div class="col-md-8 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" class="form-control " value="<?=(!empty($dataRow->remark))?$dataRow->remark:""; ?>" />
            </div>

            <!--S.no	Parameters		Specifiction	Method Of Inspn.-->

            <div class="col-md-12 form-group">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Parameters</th>
                            <th>Specifiction</th>
                            <th>Min</th>
                            <th>Max</th>
                            <th>Other</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(!empty($materialParam)): $i=1;

                                echo '<tr>
                                    <th>A</th>
                                    <th colspan="6">Chemical Composition</th>
                                </tr>';
                                foreach($materialParam as $row):
                                    if($row->type == 2):
                                        echo '<tr>
                                            <td>'.$i++.'</td>
                                            <td>'.$row->family_name.'</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>';
                                    endif;
                                endforeach;

                                echo '<tr>
                                    <th>B</th>
                                    <th colspan="6">Mechanical</th>
                                </tr>';
                                foreach($materialParam as $row):
                                    if($row->type == 3):
                                        echo '<tr>
                                            <td>'.$i++.'</td>
                                            <td>'.$row->family_name.'</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>';
                                    endif;
                                endforeach;

                                echo '<tr>
                                    <th>C</th>
                                    <th colspan="6">Heat treatment process</th>
                                </tr>';
                                foreach($materialParam as $row):
                                    if($row->type == 4):
                                        echo '<tr>
                                            <td>'.$i++.'</td>
                                            <td>'.$row->family_name.'</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>';
                                    endif;
                                endforeach;

                                echo '<tr>
                                    <th>D</th>
                                    <th colspan="6">Microstructure</th>
                                </tr>';
                                foreach($materialParam as $row):
                                    if($row->type == 5):
                                        
                                        echo'<tr>
                                            <td>'.$i++.'</td>
                                            <td>'.$row->family_name.'</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>';
                                    endif;
                                endforeach;
                            endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
