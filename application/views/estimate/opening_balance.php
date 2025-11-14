<?php $this->load->view('includes/header'); ?>
<style> 
	.typeahead.dropdown-menu{width:95.5% !important;padding:0px;border: 1px solid #999999;box-shadow: 0 2px 5px 0 rgb(0 0 0 / 26%);}
	.typeahead.dropdown-menu li{border-bottom: 1px solid #999999;}
	.typeahead.dropdown-menu li .dropdown-item{padding: 8px 1em;margin:0;}
</style>

<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">                    
					<div class="float-end">
                        <div class="input-group">
                            <div class="input-group-append">
                                <button type="button" class="btn waves-effect waves-light btn-success float-right loadData" title="Load Data">
                                    <i class="fas fa-sync-alt"></i> Load
                                </button> 
                            </div>
                        </div>
					</div>  
                    <h4 class="card-title text-left">Update Ledger Opening</h4>                  
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <form autocomplete="off" id="saveLedgerOp">					
                                <div class="col-md-12 mt-3">
                                    <div class="error op_data_error"></div>
                                    <div class="row form-group">
                                        <div class="table-responsive ">
                                            <table id="ledgerOpening" class="table table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th style="width:5%;">#</th>
                                                        <th>Party Name</th>
                                                        <th>Op. Balance</th>
                                                        <th>New Op. Balance</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="ledgerOpeningData">
                                                    <tr>
                                                        <td class="text-center" colspan="5">No data available in table</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
    setTimeout(function(){ $(".loadData").trigger('click'); }, 100);

    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		//var group_id = $('#group_id').val();	
        $.ajax({
            url: base_url + controller + '/getGroupWiseLedger',
            data: {},
            type: "POST",
            dataType:'json',
            success:function(data){
                $("#ledgerOpeningData").html(data.tbody);
            }
        });
    }); 

    $(document).on('click','.saveOp',function(){
        var party_id = $(this).data('id');
        var balance_type = $("#balance_type_"+party_id).val();
        var other_op_balance = $("#other_op_balance_"+party_id).val();

        var fd = {id:party_id,balance_type:balance_type,other_op_balance:other_op_balance};

        Swal.fire({
            title: 'Confirm!',
            text: 'Are you sure to update ledger opening balance?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Do it!',
        }).then(function(result) {
            if (result.isConfirmed){
                $.ajax({
                    url: base_url + controller + '/saveOpeningBalance',
                    data:fd,
                    type: "POST",
                    dataType:"json",
                }).done(function(data){
                    if(data.status===0){
                        $(".error").html("");
                        $.each( data.message, function( key, value ) {$("."+key).html(value);});
                    }else if(data.status==1){

                        var cur_op = parseFloat(parseFloat(other_op_balance) * parseFloat(balance_type)).toFixed(2);
                        var cur_op_text = '';
                        if(parseFloat(cur_op) > 0){
                            cur_op_text = '<span class="text-success">'+cur_op+' CR</span>';
                        }else if(parseFloat(cur_op) < 0){
                            cur_op_text = '<span class="text-danger">'+Math.abs(cur_op)+' DR</span>';
                        }else{
                            cur_op_text = cur_op;
                        }
                        $("#cur_op_"+party_id).html(cur_op_text);
                        
                        Swal.fire( 'Success', data.message, 'success' );
                    }else{
                        Swal.fire( 'Sorry...!', data.message, 'error' );
                    }
                            
                });
            }
	    });
    });
});
</script>