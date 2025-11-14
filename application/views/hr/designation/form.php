<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			
			<div class="col-md-12 form-group">
				<label for='title' class="control-label">Designation Name</label>
				<input type="text" id="title" name="title" class="form-control req" value="<?=(!empty($dataRow->title))?$dataRow->title:""?>">				
			</div>
			
            <div class="col-md-12 form-group">
                <label for='description' class="control-label">Remark</label>
                <textarea name="description" class="form-control" rows="1"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
		</div>
	</div>	
</form>
            
