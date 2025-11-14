<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="ref_id" id="ref_id" value="<?=$ref_id?>">
            <input type="hidden" name="document_type" id="document_type" value="<?=(!empty($document_type))?$document_type:""?>">

            <div class="col-md-12 form-group">
                <label for="ewbNo">Eway Bill No.</label>
                <input type="text" name="ewbNo" id="ewbNo" class="form-control" value="<?=$ewbNo?>" readonly />
            </div>

            <div class="col-md-12 form-group">
                <label for="cancelRsnCode">Cancel Reason</label>
                <select name="cancelRsnCode" id="cancelRsnCode" class="form-control single-select req">
                    <option value="">Select Reason</option>
                    <?php
                        foreach($reasonList as $key => $value):
                            echo '<option value="'.$key.'">'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="cancelRmrk">Cancel Remark <strong class="text-danger">*</strong></label>
                <textarea name="cancelRmrk" id="cancelRmrk" class="form-control req"></textarea>
                <div class="error cancelRmrk"></div>
            </div>
        </div>
    </div>
</form>