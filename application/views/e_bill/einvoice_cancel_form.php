<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="ref_id" id="ref_id" value="<?=$ref_id?>">
            <input type="hidden" name="doc_type" id="doc_type" value="<?=$doc_type?>">

            <div class="col-md-12 form-group">
                <label for="ack_no">ACK No.</label>
                <input type="text" class="form-control" value="<?=$akc_no?>" readonly />
            </div>

            <div class="col-md-12 form-group">
                <label for="Irn">IRN</label>
                <input type="text" name="Irn" id="Irn" class="form-control" value="<?=$irn?>" readonly />
            </div>

            <div class="col-md-12 form-group">
                <label for="CnlRsn">Cancel Reason</label>
                <select name="CnlRsn" id="CnlRsn" class="form-control single-select req">
                    <option value="">Select Reason</option>
                    <?php
                        foreach($reasonList as $key => $value):
                            echo '<option value="'.$key.'">'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="CnlRem">Cancel Remark <strong class="text-danger">*</strong></label>
                <textarea name="CnlRem" id="CnlRem" class="form-control req"></textarea>
                <div class="error CnlRem"></div>
            </div>
        </div>
    </div>
</form>