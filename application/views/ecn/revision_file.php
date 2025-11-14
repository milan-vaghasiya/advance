<form autocomplete="off" id="editRevison">
	<div class="col-md-12">
		<div class="row form-group">
			
			<input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : '')?>" />		
			<div class="col-md-12 form-group">
				<label for="customer_drg"> Customer drg. </label>
				<input type="file" name="customer_drg" id="customer_drg" class="form-control" />                
			</div>
			<div class="col-md-12
             form-group">
				<label for="company_drg">Company drg.</label>
				<input type="file" name="company_drg" id="company_drg" class="form-control" />                
			</div>	
		</div>
	</div>
</form>
