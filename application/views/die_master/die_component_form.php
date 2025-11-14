
<form>
    <div class="col-md-12">        
        <div class="row">
            <input type="hidden" name="id" id="id" value="">

            <div class="col-md-12 form-group">
                <label for="item_id">Product</label>
                <select name="item_id" id="item_id" class="form-control select2 req">
                    <option value="">Select Product</option>
                    <?php
                    if (!empty($itemList)) :
                        foreach ($itemList as $row):
                            echo '<option value="'.$row->id.'">'.(!empty($row->item_code) ? '['.$row->item_code.'] ' : '').$row->item_name.'</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control select2 req">
                    <option value="">Select Category</option>
                </select>
            </div>

        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('change',"#item_id",function(){
		var item_id = $(this).val();

		if(item_id){
			$.ajax({
				url : base_url + controller + '/getDieCategoryList',
				type: 'post',
				data: { item_id:item_id },
				dataType : 'json',
			}).done(function(response){
				$("#category_id").html(response.options);
			});
		}
	});
});
</script>