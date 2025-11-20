<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<?php $class=''; if($item_type == 1){ $class='text-center'; ?>
						<div class="float-start">
							<ul class="nav nav-pills">
								<li class="nav-item"> 
									<button onclick="statusTab('itemTable',1);" class="nav-tab btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Existing</button> 
								</li>
								<li class="nav-item"> 
									<button onclick="statusTab('itemTable',2);" class="nav-tab btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">New Enquiry</button> 
								</li>
							</ul>
						</div>
                    <?php } ?>
					<div class="float-end">
                        <?php
                            $addParam = "{'postData':{'item_type':".$item_type."},'modal_id' : 'bs-right-lg-modal', 'call_function':'addItem', 'form_id' : 'addItem', 'title' : 'Add ".$this->itemTypes[$item_type]."'}";
                        ?>
                        <button type="button" class="btn waves-effect waves-light btn-outline-dark float-right permission-write press-add-btn" onclick="modalAction(<?=$addParam?>);"><i class="fa fa-plus"></i> Add <?=$this->itemTypes[$item_type]?></button>
						<?php
							if($item_type != 4):
								$excelParam = "{'postData':{'item_type':".$item_type."},'modal_id' : 'bs-right-xl-modal', 'call_function':'uploadItemExcel','fnsave':'saveUploadedExcel', 'form_id' : 'uploadItem', 'title' : 'Add ".$this->itemTypes[$item_type]."'}";
                        ?>
							<button type="button" class="btn waves-effect waves-light btn-outline-info float-right permission-write press-add-btn" onclick="modalAction(<?=$excelParam?>);"><i class="fas fa-upload"></i> Upload <?=$this->itemTypes[$item_type]?></button>
						<?php endif; ?>
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
                                <table id='itemTable' class="table table-bordered ssTable ssTable-cf" data-url='/getDTRows/<?=$item_type?>'></table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/product.js?v=<?=time()?>"></script>
<script>
function getProcessTransHtml(data){
    var postData = data.postData || {};
	var fnget = data.fnget || "";
	var controllerName = data.controller || controller;
	var resFunctionName = data.res_function || "";

	var table_id = data.table_id || "";
	var thead_id = data.thead_id || "";
	var tbody_id = data.tbody_id || "";
	var tfoot_id = data.tfoot_id || "";	

	if(thead_id != ""){
		$("#"+table_id+" #"+thead_id).html(data.thead);
	}
	
	$.ajax({
		url: base_url + controllerName + '/' + fnget,
		data:postData,
		type: "POST",
		dataType:"json",
		beforeSend: function() {
			if(table_id != ""){
				var columnCount = $('#'+table_id+' thead tr').first().children().length;
				$("#"+table_id+" #"+tbody_id).html('<tr><td colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');  
			}
		},
	}).done(function(res){
		if(resFunctionName != ""){
			window[resFunctionName](response);
		}else{
			$("#"+table_id+" #"+tbody_id).html('');
			$("#"+table_id+" #"+tbody_id).html(res.tbodyData);
            $("#process_id").html(res.processOptions);
            $("#process_id").select2();
			if(tfoot_id != ""){
				$("#"+table_id+" #"+tfoot_id).html('');
				$("#"+table_id+" #"+tfoot_id).html(res.tfootData);
			}
		}
	});
}

function fixWidthHelper(e, ui) {
	ui.children().each(function () {
		$(this).width($(this).width());
	});
	return ui;
}
</script>