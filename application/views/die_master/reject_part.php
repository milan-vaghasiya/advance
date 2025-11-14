<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=$id?>" />
            <input type="hidden" name="status" id="status" value="4" />
            <input type="hidden" name="msg" id="msg" value="Rejected" />

            <div class="col-md-12 form-group">
                <label for="rejection_reason">Rejection Reason</label>
                <textarea name="rejection_reason" id="rejection_reason" class="form-control req"></textarea>
            </div>
        </div>
    </div>
</form>