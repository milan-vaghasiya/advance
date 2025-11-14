<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                        </div>       
                        <div class="col-md-6 float-right">  
                            <div class="input-group">
                                <div class="input-group-append" style="width:40%;">
                                    <select id="party_id" class="form-control select2">
                                        <option value="ALL">ALL</option>
                                        <?php
                                            foreach($partyList as $row):
                                                echo '<option value="'.$row->id.'">'.$row->party_name.'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                </div>
                                <div class="input-group-append" style="width:40%;">
                                    <select id="item_id" class="form-control select2" >
                                        <option value="">Select Item</option>
                                        <?php
                                            $itemIds = array_unique(array_column($itemList, 'item_id'));
                                            $itemName = array_unique(array_column($itemList, 'item_name'));
                                            foreach($itemIds as $key => $row):
                                                echo '<option value="'.$row.'">'.$itemName[$key].'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                </div>
                                <div class="input-group-append">
                                    <button type="button" class="btn waves-effect waves-light btn-success refreshReportData loadData" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
                                </div>
                            </div>
                            <div class="error stock_type"></div>
                        </div>                  
                    </div>                                         
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card"> 
                            <div class="card-body reportDiv" style="min-height:75vh">
                                <div class="table-responsive">
                                    <table id='reportTable' class="table table-bordered">
                                        <thead class="thead-dark" id="theadData">
                                            <tr class="text-center">
                                                <th colspan="5">Pending Orders</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th class="text-left">Order No.</th>
                                                <th class="text-left">Order Date</th>
                                                <th class="text-left">Bom Item</th>
                                                <th class="text-right">Plan Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyData"></tbody>
                                        <tfoot class="thead-dark" id="tfootData">
                                            <tr>
                                                <th colspan="4">Total</th>
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
</div>


<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	reportTable();
    // setTimeout(function(){$(".loadData").trigger('click');},500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
		var party_id = $('#party_id').val();
		var item_id = $('#item_id').val();
		// if($("#party_id").val() == ""){$(".party_id").html("Customer is required.");valid=0;}
		if($("#item_id").val() == ""){$(".item_id").html("Item is required.");valid=0;}
		if(valid){
            $.ajax({
                url: base_url + controller + '/getMrpReport',
                data: {party_id:party_id,item_id:item_id},
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

	$(document).on('change', '#party_id', function() {
		var party_id = $(this).val();
		$.ajax({
			type: "POST",
			url: base_url + controller + '/getPendingPartyOrders',
			data: {party_id: party_id},
            dataType:'json',
		}).done(function(response) {
            console.log(response);
			$('#item_id').html('');
            $('#item_id').html(response.options);
		});
	});
});


</script>