<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            
            <div class="col-md-12 form-group">
                <label for="remark">Type</label>
                <select name="type" id="type" class="form-control select2">
                    <option value="1" <?=(!empty($dataRow->type) && $dataRow->type == 1)?"selected":""?>>Rejection Reason</option>
                    <option value="2" <?=(!empty($dataRow->type) && $dataRow->type == 2)?"selected":""?>>Idle Reason</option>
                    <option value="3" <?=(!empty($dataRow->type) && $dataRow->type == 3)?"selected":""?>>Rework Reason</option>                    
                    <option value="4" <?=(!empty($dataRow->type) && $dataRow->type == 4)?"selected":""?>>Feasibility Reason</option>                    
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="code"> Code</label>
                <textarea name="code" id="code" class="form-control req" placeholder="Rejection Reason" ><?=(!empty($dataRow->code))?$dataRow->code:"";?></textarea>
                <div class="error code"></div>
            </div>
           
            <div class="col-md-12 form-group">
                <label for="remark"> Reason</label>
                <textarea name="remark" id="remark" class="form-control req" placeholder="Rejection Reason" ><?=(!empty($dataRow->remark))?$dataRow->remark:"";?></textarea>
                <div class="error remark"></div>
            </div>
           
        </div>
    </div>
</form>