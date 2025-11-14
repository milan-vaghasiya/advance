<form id="cpPrint">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="item_id" id="item_id" value="<?=$item_id?>">

            <div class="col-md-9 form-group">
                <label for="rev_no">Revision No.</label>
                <select name="rev_no" id="rev_no" class="form-control select2 req">
                    <option value="">Select Revision No.</option>
                    <?php
                        foreach($revisionList as $row):
                            echo '<option value="'.$row->rev_no.'">'.$row->rev_no.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error rev_no"></div>
            </div>
            <div class="col-md-3 form-group">
                <button type="button" class="btn waves-effect waves-light btn-outline-success mt-4 printControlPlan" target="_blank"><i class="fas fa-print"></i> Print</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    $(document).on('click','.printControlPlan',function(e){
        e.stopImmediatePropagation();e.preventDefault();
        $(".error").html("");
		var valid = 1;
        var item_id = $('#item_id').val();
        var rev_no = $('#rev_no').val() || 0;
        if($('#rev_no').val() == ""){$(".rev_no").html("Revision No. is required."); valid=0;}
		if(valid)
		{
            window.open(base_url + controller + '/getControlPlanPrint/'+item_id+'/'+rev_no);
        }
      
    });  
});
</script>
