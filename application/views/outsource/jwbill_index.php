<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <h4 class="card-title pageHeader">Vendor Challan</h4>
					<div class="float-end" style="width:30%;">
					    <div class="input-group"> 
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>"/>                                    
                            <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>"/>
                            <div class="input-group-append">
                                <button type="button" class="btn waves-effect waves-light btn-success loadData" title="Load Data">
                                    <i class="fas fa-sync-alt"></i> Load
                                </button>
                            </div>
                            <div class="error fromDate"></div>
                            <div class="error toDate"></div>
                        </div> 
                    </div> 
				</div>
            </div>
		</div>
         <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id='jobWorkBillTable' class="table table-bordered ssTable ssTable-cf" data-url='/getjobWorkBillDTRows'></table>
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
    setTimeout(function(){$('.loadData').trigger('click');},50);
    
    $(document).on('click','.loadData',function(){
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        
        $("#jobWorkBillTable").attr("data-url",$("#jobWorkBillTable").data('url')+'/'+from_date+'/'+to_date);
        $("#jobWorkBillTable").data("hp_fn_name","");
        $("#jobWorkBillTable").data("page","");
        $("#jobWorkBillTable").data("hp_fn_name",'getProductionDtHeader');
        $("#jobWorkBillTable").data("page",'jwbill');
        ssTable.state.clear();
        initTable();
	});
});
</script>