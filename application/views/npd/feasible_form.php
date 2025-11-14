<form id="feasibilityForm">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=$dataRow->id?>">
            <div class="col-md-12 form-group">
                <label for="feasible_status">Feasible Status</label>
                <select id="feasible_status" name="feasible_status" class="form-control">
                    <option value="1">Yes</option>
                    <option value="2">No</option>
                </select>
           </div>
           <div class="col-md-12 form-group feasibleReasonDiv" style="display:none">
                <label for="feasible_reason">Feasible Reason</label>
                <select id="feasible_reason" name="feasible_reason" class="form-control select2 req">
                    <option value="">Select</option>
                    <?php
                        if(!empty($reasonList)){
                            foreach($reasonList AS $row){
                                ?><option value="<?=$row->remark?>"><?=$row->remark?></option><?php
                            }
                        }
                    ?>
                </select>
           </div>
            <div class="col-md-12 form-group">
                <label for="feasible_remark">Remark</label>
                <textarea  class="form-control"></textarea>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        $(document).on('change', '#feasible_status', function () {
            var feasible_status = $(this).val();
            if(feasible_status == 2){
                $(".feasibleReasonDiv").show();
            }else{
                $(".feasibleReasonDiv").hide();
            }
        });
    });
</script>