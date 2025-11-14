<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" value="<?=(isset($dataRow->id) && !empty($dataRow->id) ? $dataRow->id : "") ?>">

            <div class="col-md-12 form-group">
                <label for="close_remark">Close Remark</label>
                <input type="text" name="close_remark" class="form-control" value="" />
            </div>

        </div>
    </div>
</form>