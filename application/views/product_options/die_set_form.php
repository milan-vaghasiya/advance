
<h4 class="fs-15 text-primary border-bottom-sm">Create New Set</h4>
<form id="catForm">
    <div class="col-md-12">        
        <div class="row">
            <div class="table-responsive">
                <input type="hidden" name="id" id="id" value="">
                <input type="hidden" name="item_id" id="item_id" value="<?=$item_id?>" />
                <input type="hidden" name="qty" id="qty" value="1" />

                <table class="table table-bordered align-items-center">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:45%">Category</th>
                            <th style="width:50%">Die List</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?=(!empty($catBody) ? $catBody : '')?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <?php
                    $param = "{'formId':'catForm','fnsave':'saveDieSet','controller':'productOption'}";
                ?>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="dieStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</form>
<hr>
<h4 class="fs-15 text-primary border-bottom-sm">Die List</h4>
<form id="dieForm">
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <input type="hidden" name="id" id="id" value="">
                <input type="hidden" name="item_id" id="item_id" value="<?=$item_id?>" />
                
                <table class="table table-bordered align-items-center">
                    <thead class="thead-info">
                        <?=(!empty($dieHead) ? $dieHead : '')?>
                    </thead>
                    <tbody>
                        <?=(!empty($dieBody) ? $dieBody : '')?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
function dieStore(postData){
	setPlaceHolder();
	
	var formId = postData.formId;
	var fnsave = postData.fnsave || "save";
	var controllerName = postData.controller || controller;
	var formClose = postData.form_close || "";

	var form = $('#'+formId)[0];
	var fd = new FormData(form);	

	$.ajax({
		url: base_url + controllerName + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){		
		if(data.status==1){
            initTable(); $(".modal-select2").select2();
            Swal.fire({ icon: 'success', title: data.message}).
            then(function(result) {
                window.location.reload();
		    });            
        }else{
            if(typeof data.message === "object"){
                $(".error").html("");
                $.each( data.message, function( key, value ) {$('#'+formId+" "+"."+key).html(value);});
            }else{
                initTable();
                Swal.fire({ icon: 'error', title: data.message });
            }			
        }			
	});
}  

function dieSetStore(postData){
	setPlaceHolder();
	
	var formId = postData.formId;
	var fnsave = postData.fnsave || "save";
	var controllerName = postData.controller || controller;

	var form = $('#'+formId)[0];
    var fd = $('.dieSetItem_'+postData.postData.die_master_id).find('input,select').serializeArray();
    console.log(fd);
	
	$.ajax({
		url: base_url + controllerName + '/' + fnsave,
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
        if(data.status==1){
            initTable(); $(".modal-select2").select2();
            Swal.fire({ icon: 'success', title: data.message}).
            then(function(result) {
                window.location.reload();
		    });  
        }else{
            if(typeof data.message === "object"){
                $(".error").html("");
                $.each( data.message, function( key, value ) {$('.dieSetItem_'+postData.postData.die_master_id+" "+"."+key).html(value);});
            }else{
                initTable();
                Swal.fire({ icon: 'error', title: data.message });
            }			
        }			
	});
}    
</script>