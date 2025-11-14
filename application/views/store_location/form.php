<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="store_level" id="store_level" value="" />
            
            <div class="col-md-8 form-group">
                <label for="location">Rack</label>
                <input type="text" name="location" class="form-control req" value="<?=(!empty($dataRow->location))?$dataRow->location:""; ?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="final_location">Final Store</label>
                <select name="final_location" id="final_location" class="form-control">
                    <option value="0" <?=(!empty($dataRow) && $dataRow->final_location == 0) ? "selected" : "";?>>No</option>
                    <option value="1" <?=(!empty($dataRow) && $dataRow->final_location == 1) ? "selected" : "";?>>Yes</option>
                </select>
            </div>

            <div class="col-md-12 form-group" id="selectStoreName">
                <label for="ref_id">Store Name</label>
                <span class="float-right">
                    <a href="javascript:void(0)" id="addNewStore"><i class="fa fa-plus"></i> Add New</a>
                </span>
                <select name="ref_id" id="ref_id" class="form-control select2 req">
                <option value="">Select Store</option>
                    <?php
                        if(!empty($storeList)):
                            foreach($storeList as $row):
                                $selected = (!empty($dataRow->ref_id) && $dataRow->ref_id == $row->id)?"selected":((!empty($ref_id) && $ref_id == $row->id)?"selected":"");
                                echo '<option value="' . $row->id . '" data-level="'.$row->store_level.'" data-store_name="'.$row->location.'"' . $selected . '>' . $row->location . '</option>';
                            endforeach;
                        endif;
                    ?>
                </select>                
            </div>

            <div class="col-md-12 form-group" id="inputStoreName">
                <label for="ref_id">Store Name</label>
                <span class="float-right">
                    <a href="javascript:void(0)" id="selectStore"><i class="fa fa-plus"></i> Select Store</a>
                </span>
                <input type="text" id="store_name" name="store_name" class="form-control req" value="<?=(!empty($dataRow->store_name))?$dataRow->store_name:((!empty($location))?$location:"") ?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" rows="2" class="form-control"></textarea>
            </div>

        </div>
    </div>
</form>
<!-- Create By : Karmi  -->
<script type="text/javascript">
$(document).ready(function(){
    $("#inputStoreName").hide();
    setTimeout(function(){
        $('#ref_id').trigger('change');
    }, 1000);
    
    $(document).on('change','#ref_id',function(){
		var ref_id = $(this).val();
		var level = $(this).find(":selected").data('level'); 
		var store_name = $(this).find(":selected").data('store_name');
        $('#store_level').val(level);
        $('#store_name').val(store_name);
	}); 

    $(document).on('click',"#selectStore",function(){ 
        $("#selectStoreName").show(); $("#inputStoreName").hide(); 
        $('#store_name').val(""); $("#ref_id").val(""); $("#ref_id").select2(); $('#store_level').val("");
    });
    $(document).on('click',"#addNewStore",function(){ 
        $("#selectStoreName").hide(); $("#inputStoreName").show(); 
        $('#store_name').val(""); $("#ref_id").val(""); $("#ref_id").select2(); $('#store_level').val("");
    }); 
});
</script>