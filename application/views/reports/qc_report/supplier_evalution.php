<?php 
	$this->load->view('includes/header'); 
	$start_year = $this->session->userdata('startYear');
	$end_year = $this->session->userdata('endYear');
	$monthArr = ['Apr-'.$start_year=>'01-04-'.$start_year,'May-'.$start_year=>'01-05-'.$start_year,'Jun-'.$start_year=>'01-06-'.$start_year,'Jul-'.$start_year=>'01-07-'.$start_year,'Aug-'.$start_year=>'01-08-'.$start_year,'Sep-'.$start_year=>'01-09-'.$start_year,'Oct-'.$start_year=>'01-10-'.$start_year,'Nov-'.$start_year=>'01-11-'.$start_year,'Dec-'.$start_year=>'01-12-'.$start_year,'Jan-'.$end_year=>'01-01-'.$end_year,'Feb-'.$end_year=>'01-02-'.$end_year,'Mar-'.$end_year=>'01-03-'.$end_year];
?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="card-header">
						<div class="row">
							<div class="col-md-2">
								<select name="type" id="type" class="form-control select2 req">
									<option value="1">Purchase</option>
									<option value="2">JobWork</option>
								</select>
							</div>
                            <div class="col-md-2">
								<select name="party_id" id="party_id" class="form-control select2">
                                    <option value="">Select Supplier</option>
                                    <?php
										foreach($supplierData as $row):
											echo '<option value="'.$row->id.'">'.$row->party_name.'</option>';
										endforeach;  
                                    ?>
                                </select>
							</div>
							<div class="col-md-2">
								<select name="from_month" id="from_month" class="form-control select2 req">
									<option value="">Select Month</option>
									<?php
										foreach($monthArr as $key=>$value):
											$selected = (date('m',strtotime($this->session->userdata('startDate'))) == date('m',strtotime($value))) ? "selected" : "";
											echo '<option value="'.date('m',strtotime($value)).'" '.$selected.'>'.date('F',strtotime($value)).'</option>';
										endforeach;
									?>
								</select>
								<div class="text-danger from_month"></div>
								<div class="error date_range_error"></div>
							</div>
							<div class="col-md-1 form-group">
								<select name="from_year" id="from_year" class="form-control select2 req">
									<?php
										$yearList = $this->db->get('financial_year')->result();
										$cyKey = array_search(1,array_column($yearList,'is_active'));
										foreach($yearList as $key=>$row):
											// if($cyKey >= $key):
												$selected = ($this->session->userdata('financialYear') == $row->financial_year)?"selected":"";
												echo "<option value='".$row->start_year."' ".$selected.">".$row->start_year."</option>";
											// endif;
										endforeach;
									?>
								</select>
								<div class="text-danger from_year"></div>
							</div>
							<div class="col-md-2">
								<select name="to_month" id="to_month" class="form-control select2 req">
									<option value="">Select Month</option>
									<?php
										foreach($monthArr as $key=>$value):
											$selected = (date('m',strtotime($this->session->userdata('endDate'))) == date('m',strtotime($value))) ? "selected" : "";
											echo '<option value="'.date('m',strtotime($value)).'" '.$selected.'>'.date('F',strtotime($value)).'</option>';
										endforeach;
									?>
								</select>
								<div class="text-danger to_month"></div>
							</div>
							<div class="col-md-1 form-group">
								<select name="to_year" id="to_year" class="form-control select2 req">
									<?php
										$yearList = $this->db->get('financial_year')->result();
										$cyKey = array_search(1,array_column($yearList,'is_active'));
										foreach($yearList as $key=>$row):
											$selected = ($this->session->userdata('endYear') == $row->start_year)?"selected":"";
											echo "<option value='".$row->start_year."' ".$selected.">".$row->start_year."</option>";
										endforeach;
									?>
								</select>
								<div class="text-danger to_year"></div>
							</div>
                            <div class="col-md-2">  
                                <div class="input-group justify-content-around"">
                                    <div class="input-group-append ml-2">
										<div class="input-group-append">
											<button type="button" class="btn waves-effect waves-light btn-success loadData" data-pdf="0" title="Load Data">
												<i class="fas fa-sync-alt"></i> Load
											</button>
											<button type="button" class="btn waves-effect waves-light btn-warning float-right loadData" data-pdf="1" title="PDF">
												<i class="fas fa-print"></i> PDF
											</button>
										</div>
                                    </div>
                                </div>
                                <div class="error toDate"></div>
                            </div>                 
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
                                <table id='reportTable' class="table table-bordered">
                                    <thead id="theadData" class="thead-dark">
									<tr class="text-center">
										<th colspan="3"><?= (!empty($pageHeader) ? $pageHeader : "");?></th>
									</tr>
									<tr class="text-center">
										<th style="min-width:50px;">Sr No.</th>
										<th style="min-width:50px;">Supplier Name</th>
										<th>Total</th>
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
</div>


<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	reportTable();
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
        var party_id = $("#party_id").val();
	    var type = $('#type').val();
		var is_pdf = $(this).data('pdf');
		var from_month = $('#from_month').val();
		var from_year = $('#from_year').val();
		var to_month = $('#to_month').val();
		var to_year = $('#to_year').val();
		
		if($("#from_month").val() == ""){$(".from_month").html("From Month is required.");valid=0;}
		if($("#from_year").val() == ""){$(".from_year").html("From Year is required.");valid=0;}
		if($("#to_month").val() == ""){$(".to_month").html("To Month is required.");valid=0;}
		if($("#to_year").val() == ""){$(".to_year").html("To Year is required.");valid=0;}

        var postData = {party_id:party_id,is_pdf:is_pdf,type:type,from_month:from_month,from_year:from_year,to_month:to_month, to_year:to_year};

		if(valid){
            if(is_pdf == 0){
                $.ajax({
					url: base_url + controller + '/getSupplierEvalution',
					data: postData,
					type: "POST",
					dataType:'json',
					success:function(data){
						$(".error").html("");
						$.each( data.message, function( key, value ) {
							$("."+key).html(value);
						});
						$("#reportTable").DataTable().clear().destroy();
						$("#theadData").html(data.theadData);
						$("#tbodyData").html(data.tbodyData);
						reportTable();
					}
				});
            }else{
                var url = base_url + controller + '/getSupplierEvalution/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
                window.open(url);
            } 
        }
    });   
});
</script>