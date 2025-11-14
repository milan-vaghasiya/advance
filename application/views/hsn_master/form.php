<form enctype="multpart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />   
            <div class="col-md-8 form-group">
                <label for="hsn">HSN</label>
                <input type="text" name="hsn" class="form-control numericOnly req" value="<?= (!empty($dataRow->hsn)) ? $dataRow->hsn : "" ?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="gst_per">GST Per (%)</label>
                <select name="gst_per" id="gst_per" class="form-control select2">
                    <option value="">Select</option>
                    <?php
                        foreach($this->gstPer as $key => $value):
                            $selected = (!empty($dataRow->gst_per) && floatVal($dataRow->gst_per) == $key)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            
            <div class="col-md-12 form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control " rows="3"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>           
        </div>
    </div>
</form>
