<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
			
            <div class="col-md-4 form-group">
                <label for="type">Type</label>
                <select name="type" id="type" class="form-control modal-select2">
                    <option value="">Select</option>
                    <?php
                    if(!empty($fieldList)){
                        foreach($fieldList as $row){
                            $selected = (!empty($dataRow->type) && $dataRow->type == $row->id)?'selected':'';
                            ?> <option value="<?=$row->id?>" <?= $selected?>><?=$row->field_name?></option> <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-8 form-group">
                <label for="title">Options</label>
                <input type="text" name="title" id="title" class="form-control req" value="<?=(!empty($dataRow->title) ? $dataRow->title : "")?>">
            </div>

        </div>        
    </div>
</form>