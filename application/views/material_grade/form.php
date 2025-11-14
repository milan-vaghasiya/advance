<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <div class="col-md-12 form-group">
                <label for="material_grade">Material Grade</label>
                <input type="text" name="material_grade" id="material_grade" class="form-control req" value="<?= (!empty($dataRow->material_grade)) ? $dataRow->material_grade : "" ?>">
            </div>
           
            <div class="col-md-6 form-group">
                <label for="standard">Standard</label>
                <input type="text" name="standard" id="standard" class="form-control" value="<?= (!empty($dataRow->standard)) ? $dataRow->standard : "" ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="scrap_group">Scrap Group</label>
                <select name="scrap_group" id="scrap_group" class="form-control select2 req">
                    <option value="">Select Scrap Group</option>
                    <?php
                    foreach ($scrapData as $row) :
                        $selected = (!empty($dataRow->scrap_group) && (in_array($row->id,  explode(',', $dataRow->scrap_group)))) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->item_name . '</option>';
                    endforeach;
                    ?>
                </select>
                <div class="error scrap_group"></div>
            </div>
            <div class="col-md-6 form-group">
                <label for="color_code">Colour Code</label>
                <input type="text" name="color_code" id="color_code" class="form-control" value="<?= (!empty($dataRow->color_code)) ? $dataRow->color_code : "" ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="density">Density</label>
                <input type="text" name="density" id="density" class="form-control floatOnly" value="<?= (!empty($dataRow->density)) ? $dataRow->density : "" ?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="tc_head">TC Head</label>
                <select name="tc_head[]" id="tc_head" class="form-control select2" multiple>
                    <?php
                    if(!empty($tcHeadList)):
                        foreach ($tcHeadList as $row) :
                            $selected = (!empty($dataRow->tc_head) && (in_array($row->id,  explode(',', $dataRow->tc_head)))) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->test_name . '</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $('#standard').typeahead({
		source: function(query, result)
		{
			$.ajax({
				url:base_url + 'materialGrade/standardSearch',
				method:"POST",
				global:false,
				data:{query:query},
				dataType:"json",
				success:function(data){
                    result($.map(data, function(item){return item;}));                    
                }
			});
		}
	});	 

    $('#color_code').typeahead({
		source: function(query, result)
		{
			$.ajax({

				url:base_url + 'materialGrade/colorCodeSearch',
				method:"POST",
				global:false,
				data:{query:query},
				dataType:"json",
				success:function(data){
                    result($.map(data, function(item){return item;}));                    
                }
			});
		}
	});	
});
</script>