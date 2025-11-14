<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<div class="row">
						<div class="col-md-7">
							<h4 class="card-title">Leave Authority</h4>
						</div>
						<div class="col-md-5">
							<div class="input-group">
								<div class="input-group-append" style="width:80%;">
									<select name="dept_id" id="dept_id" class="form-control select2 req" style="width:80%;margin-bottom:0px;">
										<option value="">All Department</option>
										<?php
											foreach($deptRows as $row):
												echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
											endforeach;
										?>
									</select>
								</div>
								<div class="input-group-append">
									<button type="button" class="btn waves-effect waves-light btn-success loaddata" title="Load Data" >
									<i class="fas fa-sync-alt"></i> Load
									</button>
								</div>
							</div>
						</div>	
					</div>
				</div>
			</div>
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="leaveAuthorityTable" class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>#</th>
										<th>Employee Code</th>
										<th>Employee Name</th>
										<th>Final Approval</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>

<div class="modal fade" id="leaveAuthModal" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel1" data-bs-backdrop="static" data-bs-keyboard="false">  
  <div class="modal-dialog" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1"></h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <input type="hidden" id="emp_id" value="">
                        <div class="col-md-12 form-group">
                            <label for="fla_id">Final Approval Authority</label>
                            <select name="fla_id" id="fla_id" class="form-control select2" multiple>
							
							</select>
						    <input type="hidden" id="pla_id" value="">
						    <div class="fla_id error"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary press-close-btn save-form" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success" onclick="saveAuthority()"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	leaveAuthorityTable();
    $(document).on('click','.loaddata',function(e){
        $(".error").html("");
		var valid = 1;
        var dept_id = $('#dept_id').val();
        if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getLeaveAuthority',
                data: {dept_id:dept_id},
                type: "POST",
                dataType:'json',
                success:function(data){
                    if(data.status===0){
                        $(".error").html("");
                        $.each( data.message, function( key, value ) {$("."+key).html(value);});
                    } else {
						$("#leaveAuthorityTable").dataTable().fnDestroy();
                        $("#tbodyData").html(data.tbodyData);
						leaveAuthorityTable();
                    }
                }
            });
        }
    });
});

function openLeaveAuthModal(emp_id,pla,fla,emp_name="Leave Authority Modal"){
    $("#leaveAuthModal").modal('show');
	$("#leaveAuthModal .modal-title").html(emp_name);
    $('#emp_id').val(emp_id);
    $.ajax({
        url: base_url + controller + '/getEmpLeaveAuthDetail',
        data: {emp_id:emp_id,pla:pla,fla:fla},
        type: "POST",
        dataType:'json',
        success:function(data){
            $('#pla').html(data.plaOptions);$('#pla_id').val(pla);
            $('#fla_id').html(data.flaOptions);$('#fla_id').val(fla);
            initSelect2('leaveAuthModal');
        }
    });
}

function saveAuthority(){
    var pla_id = $("#pla_id").val();
	var fla_id = $("#fla_id").val();
	var emp_id = $("#emp_id").val();
	
	if(emp_id)
	{
		$.ajax({
			url: base_url + controller + '/saveAuthority',
			data:{id:emp_id,pla_id:pla_id,fla_id:fla_id},
			type: "POST",
			dataType:"json",
		}).done(function(data){
		    if(data.status===0){
				$(".error").html("");
				$.each( data.message, function( key, value ) {$("."+key).html(value);});
			}else if(data.status==1){
		        $("#leaveAuthModal").modal('hide');
				Swal.fire({ icon: 'success', title: data.message});
				//toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });			
			    $('.loaddata').trigger('click');
			}
		});
	}
	else
	{
		toastr.error("ID NOT FOUND", 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
	}
}

function leaveAuthorityTable(tableId = 'leaveAuthorityTable') {
	var leaveAuthorityTable = $('#'+tableId).DataTable({
		responsive: true,
		"paging":   false,
		"autoWidth" : false,
		order: [],
		"columnDefs": [
		    {type: 'natural',targets: 0},
			{orderable: false,targets: "_all"},
			{className: "text-center",targets: [0, 1]},
			{className: "text-center","targets": "_all"}
		],
		language: {search: "" },
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: ['excel'],
	});
	leaveAuthorityTable.buttons().container().appendTo('#'+tableId+'_wrapper toolbar');
	$('.dataTables_filter .form-control-sm').css("width", "97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
	$('.dataTables_filter').css("text-align", "left");
	$('.dataTables_filter label').css("display", "block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
	return leaveAuthorityTable;
}
</script>