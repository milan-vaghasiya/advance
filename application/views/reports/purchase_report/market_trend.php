<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="row">    
                        <div class="col-md-3 offset-md-9">  
                            <div class="input-group">
                                <input type="month" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m')?>" />
                                <div class="input-group-append ml-2">
                                    <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>
                                </div>
                            </div>
                            <div class="error toDate"></div>
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
                                                <th class="text-center">No.</th>
                                                <th class="text-center">Items</th>
                                                <th class="text-center">April</th>
                                                <th class="text-center">May</th>
                                                <th class="text-center">June</th>
                                                <th class="text-center">July</th>
                                                <th class="text-center">August</th>
                                                <th class="text-center">September</th>
                                                <th class="text-center">October</th>
                                                <th class="text-center">November</th>
                                                <th class="text-center">December</th>
                                                <th class="text-center">January</th>
                                                <th class="text-center">February</th>
                                                <th class="text-center">March</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyData"> </tbody>
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
    initModalSelect();
	reportTable();
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var to_date = $('#to_date').val();
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getTrendMarket',
                data: {to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    console.log(data);
                    $("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tbody);
					reportTable();
                }
            });
        }
    });   
});
</script>