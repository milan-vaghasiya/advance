<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-8 form-group">
                <label for="category">Category Name</label>
                <input name="category" class="form-control req" value="<?=(!empty($dataRow->category))?$dataRow->category:"";?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="overtime">Over Time</label>
                <select name="overtime" id="overtime" class="form-control select2">
                    <option value="No" <?=(!empty($dataRow->overtime) && $dataRow->overtime == "No")?"selected":""?>>No</option>
                    <option value="Yes" <?=(!empty($dataRow->overtime) && $dataRow->overtime == "Yes")?"selected":""?>>Yes</option>
                </select> 
            </div>
        </div>
    </div>
</form>