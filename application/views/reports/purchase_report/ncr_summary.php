<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
				<div class="page-title-box">
					<div class="float-end" style="width:70%;">
					    <div class="input-group">
                            <div class="input-group-append" style="width:30%;">
                                <select id="party_id" class="form-control select2">
                                    <option value="">ALL Customer</option>
                                    <?=getPartyListOption($partyList)?>
                                </select>
                            </div>
                            <div class="input-group-append" style="width:30%;">
                                <select id="item_id" class="form-control select2">
                                    <option value="">ALL Item</option>
                                    <?=getItemListOption($itemList)?>
                                </select>
                            </div>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>"/>                                    
                            <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>"/>
                            <div class="input-group-append">
                                <button type="button" class="btn waves-effect waves-light btn-success float-right loadData" title="Load Data">
                                    <i class="fas fa-sync-alt"></i> Load
                                </button>
                            </div>
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
                        <div class="card-body reportDiv" style="min-height:75vh">
                            <div class="table-responsive">
                                <table id='complaintTable' class="table table-bordered">
                                    <thead id="theadData" class="thead-dark">
                                        <tr>
                                            <th colspan="13" class="text-center">Supplier NCR Summary</th>
                                            <th colspan="2">PUR-F-12 (Rev.01 dtd. 01.01.2025)</th>
                                        </tr>
                                        <tr>
                                            <th>#</th>
                                            <th>NCR No.</th>
                                            <th>NCR Date</th>
                                            <th>Customer Name</th>
                                            <th>Challan No.</th>
                                            <th>Batch No.</th>
                                            <th>Part No.</th>
                                            <th>Ref. of Complaint</th>
                                            <th>Details of Complaint</th>
                                            <th>Lot Qty.</th>
                                            <th>Rej. Qty.</th>
                                            <th>CAPA Request</th>
                                            <th>CAPA Report No.</th>
                                            <th>Effectiveness</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyData"></tbody>
                                    <tfoot class="thead-dark" id="tfootData">
                                        <tr>
                                            <th colspan="9">Total</th>
                                            <th>0</th>
                                            <th>0</th>
                                            <th colspan="4"></th>
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
    pageLength: 25,
    language: {search: ""},
    lengthMenu: [
        [ 10, 20, 25, 50, 75, 100, 250,500 ],
        [ '10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows','250 rows','500 rows' ]
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
            },
            {
                text: 'Pdf',
                action: function ( e, dt, node, config ) {
                    loadData('pdf');
                }
            },
            {
                text: 'Refresh',
                action: function (){ 
                    $(".refreshReportData").trigger('click');
                } 
            }
        ]
    },
    "fnInitComplete":function(){ },
    "fnDrawCallback": function() {  }
};

$(document).ready(function(){
    reportTable('complaintTable',tableOption);
	loadData();
    $(document).on('click','.loadData',function(){
		loadData();
	}); 
}); 

function loadData(pdf=""){
    $(".error").html("");
    var valid = 1;
    var party_id = $("#party_id").val();
    var item_id = $("#item_id").val();
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

    var postData = {party_id:party_id,item_id:item_id,from_date:from_date,to_date:to_date,pdf:pdf};
    if(valid){
        if(pdf == "") {
            $.ajax({
                url: base_url + controller + '/getSupplierNCRData',
                data: postData,
                type: "POST",
                dataType:'json',
                success:function(data){
                    $("#complaintTable").DataTable().clear().destroy();
                    $("#tbodyData").html(data.tbody);
                    $("#tfootData").html(data.tfoot);
                    reportTable('complaintTable',tableOption);
                }
            });
        }else
        {
            var url = base_url + controller + '/getSupplierNCRData/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
            window.open(url);
        }
    }
} 
</script>
