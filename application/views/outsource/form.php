<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Outsource</u></h4>
                    </div>
                    <div class="card-body">
                        <form id="vendorChallanForm" data-res_function="challanResponse">
                            <input type="hidden" name="challan_id" id="challan_id" value="0" />
                            <div class="row">
                                <div class="col-md-2 form-group">
                                    <label for="ch_number">Challan Date</label>
                                    <input type="text" name="ch_number" id="ch_number" class="form-control req" value="<?=$ch_prefix.$ch_no?>" readonly>
                                </div>
								<div class="col-md-2 form-group">
                                    <label for="ch_date">Challan Date</label>
                                    <input type="date" name="ch_date" id="ch_date" class="form-control req" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-2 form-group">
                                    <label for="delivery_date">Delivery Date</label>
                                    <input type="date" name="delivery_date" id="delivery_date" class="form-control req" value="<?= date('Y-m-d') ?>">
                                </div>
								<div class="col-md-4 form-group">
                                    <label for="party_id">Vendor</label>
                                    <select name="party_id" id="party_id" class="form-control select2 req">
                                        <option value="">Select Vendor</option>
                                        <?php
                                        if(!empty($vendorList)){
                                            foreach($vendorList as $row){
                                                $selected = (!empty($vendor_id) && $vendor_id== $row->id)?'selected':'';
                                                ?>
                                                <option value="<?=$row->id?>" <?=$selected?>><?=$row->party_name?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label for="vehicle_no">Vehicle No.</label>
                                    <input type="text" name="vehicle_no" id="vehicle_no" class="form-control" value="<?=(!empty($dataRow->vehicle_no)) ? $dataRow->vehicle_no : '' ?>" />
                                </div>
                                <div class="col-md-2 form-group">
                                    <label for="transport_id">Transport</label>
                                    <select name="transport_id" id="transport_id" class="form-control select2">
                                        <option value="">Select Transport</option>
                                        <?php
                                        if(!empty($transportList)){
                                            foreach($transportList as $row){
                                                echo '<option value="'.$row->id.'">'.$row->transport_name.'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-10 form-group">
                                    <label for="remark">Remark</label>
                                    <input type="text" name="remark" id="remark" class="form-control" value="">
                                </div>
                               
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <div class="error general_error"></div><br>
                                        <table id='outsourceTransTable' class="table table-bordered jpDataTable colSearch">
                                            <thead class="thead-info">
                                                <tr class="text-center">
                                                    <th style="width:5%;">#</th>
                                                    <th style="width:10%;">PRC No.</th>
                                                    <th style="width:10%;">PRC Date</th>
                                                    <th style="width:20%;">Product</th>
                                                    <th style="width:15%;">Process</th>
                                                    <th style="width:10%;">Request Qty.</th>
                                                    <th style="width:10%;">Challan Qty.</th>
                                                    <th style="width:10%;">Process Cost</th>
                                                    <th style="width:10%;">Material Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (!empty($requestData)) {
                                                    $i=1;
                                                    foreach ($requestData as $row) {
                                                        ?>
                                                        <tr class="text-center">
                                                            <td>
                                                                <input type="checkbox" id="md_checkbox_<?= $row->id ?>" name="id[]" class="filled-in chk-col-success challanCheck" data-rowid="<?=$row->id ?>" value="<?= $row->id ?>"  ><label for="md_checkbox_<?= $row->id ?>" class="mr-3"></label>
                                                            </td>
                                                            <td><?=$row->prc_number?></td>
                                                            <td><?=formatDate($row->prc_date)?></td>
                                                            <td><?=$row->item_name?></td>
                                                            <td><?=$row->process_name?></td>
                                                            <td><?=$row->qty?></td>
                                                            <td>
                                                                <input type="text" id="ch_qty<?=$row->id ?>" name="ch_qty[]" data-req_qty="<?=$row->qty?>" data-rowid="<?= $row->id ?>" class="form-control challanQty floatOnly text-center challanInput checkRow<?=$row->id?>" value="<?=$row->qty?>" disabled>
                                                                <div class="error chQty<?=$row->id?>"></div>
                                                            </td>
                                                            <td>
                                                                <input type="text" id="price<?=$row->id ?>" name="price[]"  data-rowid="<?= $row->id ?>" class="form-control floatOnly text-center challanInput checkRow<?=$row->id?>" value="<?=$row->process_cost?>" disabled>
                                                            </td>
                                                            <td>
                                                                <input type="text" id="material_value<?=$row->id ?>" name="material_value[]"  data-rowid="<?= $row->id ?>" class="form-control floatOnly text-center challanInput checkRow<?=$row->id?>" value="<?=$row->material_value?>" disabled>
                                                                <input type="hidden" id="cost_per_pcs<?=$row->id ?>"  data-rowid="<?= $row->id ?>" class="form-control floatOnly text-center challanInput  checkRow<?=$row->id?>" value="<?=$row->material_value?>" disabled>
                                                                <input type="hidden" id="material_wt<?=$row->id ?>" name="material_wt[]"  data-rowid="<?= $row->id ?>" class="form-control floatOnly text-center challanInput  checkRow<?=$row->id?>" value="<?=$row->material_wt?>" disabled>
                                                                <input type="hidden" id="material_price<?=$row->id ?>" name="material_price[]"  data-rowid="<?= $row->id ?>" class="form-control floatOnly text-center challanInput  checkRow<?=$row->id?>" value="<?=$row->material_price?>" disabled>
                                                                <input type="hidden" id="pre_process_cost<?=$row->id ?>" name="pre_process_cost[]"  data-rowid="<?= $row->id ?>" class="form-control floatOnly text-center challanInput  checkRow<?=$row->id?>" value="<?=$row->pre_process_cost?>" disabled>
																<div class="error chMtWt<?=$row->id?>"></div>
                                                                <div class="error processError<?=$row->id?>"></div>
															</td>
                                                        </tr>
                                                        <?php
                                                        $i++;
                                                    }
                                                } else {
                                                ?>
                                                    <tr>
                                                        <td colspan="9" class="text-center">No data available in table</td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
						<div class="row">
							<div class="col-md-12">
                                <?php  $param = "{'formId':'vendorChallanForm','fnsave':'save','res_function':'challanResponse'}"; ?>
								<button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="customStore(<?=$param?>);"><i class="fa fa-check"></i> Save</button>
								<a href="<?= base_url('outsource/index') ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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
    $(document).ready(function() {
        
        $(document).on("click", ".challanCheck", function() {
            $('.challanCheck').not(this).prop('checked', false).attr('checked', 'disabled');
            $(".challanInput").attr('disabled', 'disabled');
            var rowid = $(this).data('rowid');
            $(".error").html("");
            if (this.checked) {
                var req_id = $(this).val();
                $.ajax({
                    url:base_url + controller + "/getMaterialValue",
                    type:'post',
                    data:{id:req_id}, 
                    dataType:'json',
                    success:function(response){
                       $("#cost_per_pcs"+rowid).val(response.cost_per_pcs);
                       $("#material_wt"+rowid).val(response.material_wt);
                       $("#material_price"+rowid).val(response.material_price);
                       $("#pre_process_cost"+rowid).val(response.pre_process_cost);
                        $(".processError"+rowid).html(response.processCostError);
                       $(".challanQty").trigger('keyup');
                    }
                });
                $(".checkRow" + rowid).removeAttr('disabled');
            } 
        });

        
        $(document).on("keyup", ".challanQty", function() {
            var id = $(this).data('rowid');
            var req_qty = $(this).data('req_qty');
            var ch_qty = $("#ch_qty" + id).val();
            var cost_per_pcs = $("#cost_per_pcs" + id).val();
            if (parseFloat(ch_qty) > parseFloat(req_qty)) {
                $("#ch_qty" + id).val('0');
            }else{
                var material_value = parseFloat(ch_qty) * parseFloat(cost_per_pcs);
                $("#material_value" + id).val(material_value.toFixed(3));
            }
        });

    });

    function challanResponse(data,formId="vendorChallanForm"){ 
        if(data.status==1){
            $('#'+formId)[0].reset();
            window.location.href = base_url +controller;
        }else{
            if(typeof data.message === "object"){
                $(".error").html("");
                $.each( data.message, function( key, value ) {$("."+key).html(value);});
            }else{
                Swal.fire({ icon: 'error', title: data.message });
            }			
        }
    }
</script>