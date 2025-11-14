<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			
			<div class="col-md-12 form-group">
				<label for='name' class="control-label">Department Name</label>
				<input type="text" id="name" name="name" class="form-control req" value="<?=(!empty($dataRow->name))?$dataRow->name:""?>">
			</div>
			<div class="col-md-12 form-group">
                <label for='description' class="control-label">Remark</label>
                <textarea name="description" class="form-control" rows="1"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
		</div>
	</div>	
</form>
            
