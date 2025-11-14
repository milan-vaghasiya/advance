<style>
    #finaltbl th,#finaltbl td,#finaltbl select{font-size:0.750rem;}
</style>
<form autocomplete="off" id="saveBulkAdvance">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />

            <div class="col-md-4">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?=(!empty($dataRow->entry_date))?$dataRow->entry_date:date("Y-m-d")?>" max="<?=(!empty($dataRow->entry_date))?$dataRow->entry_date:date("Y-m-d")?>" />
            </div>
           
            <div class="col-md-6 form-group">
                <label for="dept_id">Department</label>
                <select name="dept_id" id="dept_id" class="form-control select2">
                    <option value="">ALL Department</option>
                    <?php
                        foreach($deptData as $row):
                            $selected = (!empty($dataRow->dept_id) && $row->id == $dataRow->dept_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'> '.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata mt-25" title="Load Data"><i class="fas fa-sync-alt"></i> Load</button>	
            </div>
        </div>
    </div>
    <hr>
    <div class="col-md-12">
        <div class="error general"></div>
    </div>
    <div class="col-md-12 mt-3">
        <div class="row form-group">
            <div class="table-responsive">
                <table id="finaltbl" class="table table-bordered generalTable">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:8%;">Code</th>
                            <th style="width:15%;">Employee Name</th>
                            <th style="width:10%;">Department</th>
                            <th style="width:10%;">Designation</th>
                            <th style="width:10%;">Payment Mode</th>
                            <th style="width:10%;">Amount </th>
                            <th>Demand Reason</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyData"></tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
	$(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var dept_id = $('#dept_id').val();
	
		if(valid){
            $.ajax({
                url: base_url + controller + '/getBulkAdvanceDetails',
                data: {dept_id:dept_id},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#tbodyData").html("");
					$("#tbodyData").html(data.tbody);
                }
            });
        }
    });  
});
</script>