<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />

            <div class="col-md-12 form-group">
                <label for="activities">Activities</label>
                <input name="activities" class="form-control req" value="<?=(!empty($dataRow->activities))?$dataRow->activities:"";?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="frequency">Frequency</label>
                <select name="frequency[]" id="frequency" class="form-control select2" multiple>
                    <?php
                    if (!empty($freqList)) {
                        foreach ($freqList as $freq) {
                            $selected = (!empty($dataRow->frequency)) && in_array($freq,explode(',',$dataRow->frequency)) ? 'selected' : '';
                            echo '<option value="'.$freq.'" '.$selected.'>'.$freq.'</option>';
                        }
                    }
                    ?>
                </select>
            </div>

        </div>
    </div>
</form>