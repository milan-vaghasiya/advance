<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="item_type" value="<?=(!empty($dataRow->item_type))?$dataRow->item_type:$item_type; ?>" />
            <input type="hidden" name="category_id" value="<?=(!empty($dataRow->category_id))?$dataRow->category_id:180; ?>" />

            <div class="col-md-12 form-group">
                <label for="item_name">Scrap Group Name</label>
                <input type="text" name="item_name" class="form-control req" value="<?=(!empty($dataRow->item_name))?$dataRow->item_name:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="uom">Unit</label>
                <select name="uom" id="uom" class="form-control select2 req">
                    <option value="">--</option>
                    <?php
						foreach ($unitData as $row) :
							$selected = (!empty($dataRow->uom) && $dataRow->uom == $row->unit_name) ? "selected" : "";
							echo '<option value="' . $row->unit_name . '" ' . $selected . '>[' . $row->unit_name . '] ' . $row->description . '</option>';
						endforeach;
                    ?>
                </select>
            </div>
        </div>
    </div>
</form>