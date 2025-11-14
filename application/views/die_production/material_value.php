<form>
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow['id']) ? $dataRow['id'] : '')?>">

            <div class="col-md-12">
                <label for="material_value">Material Value</label>
                <input type="text" name="material_value" id="material_value" class="form-control req" value="<?=(!empty($dataRow['material_value']) ? $dataRow['material_value'] : '')?>">
            </div>
        </div>
    </div>
</form>