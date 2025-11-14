<?php $this->load->view('includes/header'); ?>

<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="row">
						<div class="col-md-3"><h4><?=$pageHeader?></h4></div>
						<div class="col-md-9">
							<form enctype="multipart/form-data">
								<div class="input-group">
									<div class="custom-file">
										<input type="file" class="form-control custom-file-input" name="json_file" id="json_file" accept=".json" autocomplete="off">
									</div>
									<select name="match_status" id="match_status" class="form-control">
										<option value="0">ALL</option>
										<option value="1">Matched</option>
										<option value="2">Unmatched</option>
										<option value="3">NOT FOUND IN ERP</option>
										<option value="4">NOT FOUND IN JSON</option>
									</select>
									<input type="date" name="from_date" id="from_date" class="form-control" value="<?= $startDate ?>" />
									<div class="error fromDate"></div>
									<input type="date" name="to_date" id="to_date" class="form-control" value="<?= $endDate ?>" />
									<div class="input-group-append ml-2">
										<button type="button" class="btn waves-effect waves-light btn-success float-right " onclick="loadData(this.form,'VIEW')" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
								</div>
								<div class="error toDate"></div>
							</form>
						</div>
					</div>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
						<div class="card-body reportDiv" style="min-height:75vh">
							<div class="table-responsive">
								<table id='gstr2bTable' class="table table-bordered jpDataTable">
									<thead class="thead-dark">
										<tr>
											<th>Status</th>
											<th>Section</th>
											<th>GSTIN</th>
											<th>Party Name</th>
											<th>INV. No.</th>
											<th>INV Date</th>
											<th>Invoice Value</th>
											<th>Revarse Charge</th>
											<th>POS</th>
											<th>Invoice Type</th>
											<th>Taxable Value</th>
											<th>Rate</th>
											<th>CGST</th>
											<th>SGST</th>
											<th>IGST</th>
											<th>CESS</th>
										</tr>
									</thead>
									<tbody></tbody>
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
var tableOption = {
    responsive: true,
    "scrollY": '52vh',
    "scrollX": true,
    deferRender: true,
    scroller: true,
    destroy: true,
    "autoWidth" : false,
    order: [],
    "columnDefs": [
        {type: 'natural',targets: 0},
        {orderable: false,targets: "_all"},
        {className: "text-center",targets: [0, 1]},
        {className: "text-center","targets": "_all"}
    ],
    pageLength: -1,
    language: {search: ""},
    lengthMenu: [
        [ 10, 20, 25, 50, 75, 100, 250, 500, -1],
        [ '10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows', '250 rows', '500 rows', 'Show all rows' ]
    ],
    dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    buttons: {
        dom: {
            button: {
                className: "btn btn-outline-dark"
            }
        },
        buttons:[ 
            'pageLength', 
            {
                extend: 'excel',
                exportOptions: {
                    columns: "thead th:not(.noExport)"
                }
            }
        ]
    },
    "fnInitComplete":function(){  },
    "fnDrawCallback": function() { }
};

reportTable('gstr2bTable',tableOption);
function loadData(form,file_type = "VIEW") {
	$(".error").html("");
	var valid = 1;
	var fd = new FormData(form);
    $.ajax({
        url: base_url + controller + '/matchGstr2bData',
        data: fd,
        type: "POST",
        processData: false,
		contentType: false,
        dataType: 'json',
        success: function(data) {	
            if(data.status===0){
                $(".error").html("");
                $.each( data.message, function( key, value ) {$("."+key).html(value);});
		    }else{
                $("#gstr2bTable").DataTable().clear().destroy();

                $("#gstr2bTable").html("");
                $("#gstr2bTable").html(data.tableData);

                reportTable('gstr2bTable',tableOption);
            }
            
        }
    });		
}
</script>