<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			
			<div class="col-md-12 form-group">
				<label for='description' class="control-label">Check Point</label>
				<input type="text" id="description" name="description" class="form-control req" value="<?=(!empty($dataRow->description))?$dataRow->description:""?>">
			</div>
			
		</div>
	</div>	
</form>
            
