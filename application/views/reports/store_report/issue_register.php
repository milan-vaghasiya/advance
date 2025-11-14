<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
			       <div class="input-group ">
                        <div class="input-group-append" style="width:20%;">
                            <select id="category_type" class="form-control select2">
                                <option value="">ALL Item Tpye</option>
                                <option value="1">Finish Goods</option>
                                <option value="2">Consumable</option>
                                <option value="3">Raw Material</option>
                            </select>
                        </div>
                        <div class="input-group-append" style="width:20%;">
                            <select id="item_type" class="form-control select2">
                                <option value="">ALL Category</option>
                                <?php
                                    foreach ($subCategoryData as $row) {
                                        echo "<option value='".$row->id."'>".$row->category_name."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="input-group-append" style="width:20%;">
                            <select id="item_id" class="form-control select2">
                                <option value="">ALL Item</option>
                                <?php
                                    if(!empty($itemData)){
                                        foreach ($itemData as $row) {
                                            echo "<option value='".$row->id."'>".$row->item_name."</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" />                                    
                        <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>" />
                        <div class="input-group-append">
                            <button type="button" class="btn waves-effect waves-light btn-success float-right refreshReportData loadData" title="Load Data">
                                <i class="fas fa-sync-alt"></i> Load
                            </button>
                        </div>
                    </div>
                    <div class="error fromDate"></div>
                    <div class="error toDate"></div>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body reportDiv" style="min-height:75vh">
                            <div class="table-responsive">
                                <table id='reportTable' class="table table-bordered">
                                    <thead id="theadData" class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Issue No.</th>
                                            <th>Issue Date</th>
                                            <th>Prc No.</th>
                                            <th>Item Name</th>
                                            <th>Issue Qty.</th>
                                            <th>Return Qty.</th>
                                            <th>Pending Return</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                    <tfoot id="tfootData" class="thead-dark">
                                        <tr>
                                            <th colspan="5">Total</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th>0</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	reportTable();
    setTimeout(function(){$(".loadData").trigger('click');},500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
        var item_type = $("#category_type").val();
        var category_id = $("#item_type").val();
        var item_id = $("#item_id").val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

		if(valid){
            $.ajax({
                url: base_url + controller + '/getIssueRegister',
                data: {item_type:item_type,category_id:category_id,item_id:item_id,from_date:from_date,to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#tbodyData").html(data.tbody);
                    $("#tfootData").html(data.tfoot);
					reportTable();
                }
            });
        }
    });

    $(document).on('change', '#category_type', function() {
        var category_type = $(this).val();
        
        $.ajax({
            type: "POST",
            url: base_url + controller + '/getSubCategory',
            data: {category_type:category_type},
            dataType:'json',
        }).done(function(response) {
            if(response.status == 1){
                $('#item_type').html('');
                $('#item_type').html(response.subCatOptions);
                $('#item_id').html('');
                $('#item_id').html(response.itemOptions);
                initSelect2();
            }
        });
    }); 

    $(document).on('change', '#item_type', function() {
        var item_type = $(this).val();
        
        $.ajax({
            type: "POST",
            url: base_url + controller + '/getItemList',
            data: {item_type:item_type},
            dataType:'json',
        }).done(function(response) {
            if(response.status == 1){
                $('#item_id').html('');
                $('#item_id').html(response.options);
                initSelect2();
            }
        });
    }); 
});
</script>