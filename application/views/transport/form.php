<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />

            <div class="col-md-8 form-group">
                <label for="transport_name">Tranport Name</label>
                <input type="text" name="transport_name" class="form-control req" value="<?=(!empty($dataRow->transport_name))?$dataRow->transport_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="transport_id">Tranport ID</label>
                <input type="text" name="transport_id" class="form-control req" value="<?=(!empty($dataRow->transport_id))?$dataRow->transport_id:""; ?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" class="form-control"><?=(!empty($dataRow->address))?$dataRow->address:""?></textarea>
            </div>
        </div>
    </div>
</form>