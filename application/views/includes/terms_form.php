<div class="modal modal-left fade" id="termModel" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Terms & Conditions</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <label for="conditions">Conditions</label>
                    <textarea name="conditions" id="conditions" class="form-control req" rows="10"><?= (!empty($termsConditions->condition)) ? $termsConditions->condition : (!empty($termsList->conditions) ? $termsList->conditions : "") ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success tc_save" data-bs-dismiss="modal"><i class="fa fa-check"></i> Save Terms</button>
            </div>
        </div>
    </div>
</div>