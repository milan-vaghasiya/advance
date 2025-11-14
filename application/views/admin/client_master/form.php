<?php $this->load->view('admin/includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <form id="addCompanyInfo" data-res_function="companyInfoRes">
                                <div class="col-md-12">
                                    <div class="row">
                                        <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />

                                        <div class="col-md-4 form-group">
                                        <label for="company_name">Company Name</label>
                                        <input type="text" name="company_name" id="company_name" class="form-control req" value="<?= (!empty($dataRow->company_name)) ? $dataRow->company_name : "" ?>">
                                        </div> 

                                        <div class="col-md-4 form-group">
                                        <label for="company_email">Company Email</label>
                                        <input type="text" name="company_email" id="company_email" class="form-control req" value="<?= (!empty($dataRow->company_email)) ? $dataRow->company_email : "" ?>">
                                        </div> 

                                        <div class="col-md-4 form-group">
                                            <label for="company_slogan">Company Slogan</label>
                                            <input name="company_slogan" id="company_slogan" class="form-control" value="<?= (!empty($dataRow->company_slogan)) ? $dataRow->company_slogan : "" ?>">
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="company_contact_person">Company Contact Person</label>
                                            <input name="company_contact_person" id="company_contact_person" class="form-control req" value="<?= (!empty($dataRow->company_contact_person)) ? $dataRow->company_contact_person : "" ?>">
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="company_phone">Mobile No. (Login ID)</label>
                                            <input name="company_phone" id="company_phone" class="form-control req" value="<?= (!empty($dataRow->company_phone)) ? $dataRow->company_phone : "" ?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="company_country_id">Company Country</label>
                                            <select name="company_country_id" id="company_country_id" class="form-control country_list select2 req"  data-state_id="company_state_id" data-selected_state_id="<?=(!empty($dataRow->company_state_id))?$dataRow->company_state_id:""?>">
                                                <option value="">Select Country</option>
                                                <?php foreach($countryData as $row):
                                                    $selected = (!empty($dataRow->company_country_id) && $dataRow->company_country_id == $row->id)?"selected":((empty($dataRow->company_country_id) && $row->id == 101)?"selected":"");
                                                ?>
                                                    <option value="<?=$row->id?>" <?=$selected?>><?=$row->name?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="company_state_id">Company State</label>
                                            <select name="company_state_id" id="company_state_id" class="form-control state_list select2 req" data-city_id="company_city_id" data-selected_city_id="<?=(!empty($dataRow->company_city_id))?$dataRow->company_city_id:""?>" >
                                                <option value="">Select State</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="company_city_id">Company City</label>
                                            <select name="company_city_id" id="company_city_id" class="form-control select2 req" >
                                                <option value="">Select City</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="company_pincode">Company Pincode</label>
                                            <input name="company_pincode" id="company_pincode" class="form-control req" value="<?= (!empty($dataRow->company_pincode)) ? $dataRow->company_pincode : "" ?>">
                                        </div>

                                        <div class="col-md-12 form-group">
                                            <label for="company_address">Company Address</label>
                                            <input name="company_address" id="company_address" class="form-control req" value="<?= (!empty($dataRow->company_address)) ? $dataRow->company_address : "" ?>">
                                        </div>

                                        <!-- <div class="col-md-3 form-group">
                                            <label for="delivery_country_id">Delivery Country</label>
                                            <select name="delivery_country_id" id="delivery_country_id" class="form-control country_list select2"  data-state_id="delivery_state_id" data-selected_state_id="<?=(!empty($dataRow->delivery_state_id))?$dataRow->delivery_state_id:""?>">
                                                <option value="">Select Country</option>
                                                <?php foreach($countryData as $row):
                                                    //$selected = (!empty($dataRow->delivery_country_id) && $dataRow->delivery_country_id == $row->id)?"selected":"";
                                                ?>
                                                    <option value="<?=$row->id?>" <?=$selected?>><?=$row->name?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="delivery_state_id">Delivery State</label>
                                            <select name="delivery_state_id" id="delivery_state_id" class="form-control state_list select2" data-city_id="delivery_city_id" data-selected_city_id="<?=(!empty($dataRow->delivery_city_id))?$dataRow->delivery_city_id:""?>" >
                                                <option value="">Select State</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="delivery_city_id">Delivery City</label>
                                            <select name="delivery_city_id" id="delivery_city_id" class="form-control select2" >
                                                <option value="">Select City</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="delivery_pincode">Delivery Pincode</label>
                                            <input name="delivery_pincode" id="delivery_pincode" class="form-control" value="<?= (!empty($dataRow->delivery_pincode)) ? $dataRow->delivery_pincode : "" ?>">
                                        </div>

                                        <div class="col-md-12 form-group">
                                            <label for="delivery_address">Delivery Address</label>
                                            <input name="delivery_address" id="delivery_address" class="form-control" value="<?= (!empty($dataRow->delivery_address)) ? $dataRow->delivery_address : "" ?>">
                                        </div> -->

                                        <div class="col-md-4 form-group">
                                            <label for="company_reg_no">MSME Reg. No.</label>
                                            <input name="company_reg_no" id="company_reg_no" class="form-control" value="<?= (!empty($dataRow->company_reg_no)) ? $dataRow->company_reg_no : "" ?>">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="company_gst_no">Company GST No.</label>
                                            <input name="company_gst_no" id="company_gst_no" class="form-control" value="<?= (!empty($dataRow->company_gst_no)) ? $dataRow->company_gst_no : "" ?>">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="company_pan_no">Company Pan No.</label>
                                            <input name="company_pan_no" id="company_pan_no" class="form-control" value="<?= (!empty($dataRow->company_pan_no)) ? $dataRow->company_pan_no : "" ?>">
                                        </div>

                                        <!-- <div class="col-md-4 form-group">
                                            <label for="company_bank_name">Company Bank Name</label>
                                            <input name="company_bank_name" id="company_bank_name" class="form-control" value="<?= (!empty($dataRow->company_bank_name)) ? $dataRow->company_bank_name : "" ?>">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="company_bank_branch">Company Bank Branch</label>
                                            <input name="company_bank_branch" id="company_bank_branch" class="form-control" value="<?= (!empty($dataRow->company_bank_branch)) ? $dataRow->company_bank_branch : "" ?>">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="company_acc_name">Company Account Name</label>
                                            <input name="company_acc_name" id="company_acc_name" class="form-control" value="<?= (!empty($dataRow->company_acc_name)) ? $dataRow->company_acc_name : "" ?>">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="company_acc_no">Company Account No.</label>
                                            <input name="company_acc_no" id="company_acc_no" class="form-control" value="<?= (!empty($dataRow->company_acc_no)) ? $dataRow->company_acc_no : "" ?>">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="company_ifsc_code">Company IFSC Code</label>
                                            <input name="company_ifsc_code" id="company_ifsc_code" class="form-control" value="<?= (!empty($dataRow->company_ifsc_code)) ? $dataRow->company_ifsc_code : "" ?>">
                                        </div> 

                                        <div class="col-md-4 form-group">
                                            <label for="swift_code">Swift Code</label>
                                            <input name="swift_code" id="swift_code" class="form-control" value="<?= (!empty($dataRow->swift_code)) ? $dataRow->swift_code : "" ?>">
                                        </div> -->
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer bg-facebook">
                            <div class="col-md-12"> 
                                <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'addCompanyInfo','fnsave':'save'});" ><i class="fa fa-check"></i> Save </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/includes/footer'); ?>
<script>
$(document).ready(function(){
    $("#company_country_id").trigger('change');
    $("#delivery_country_id").trigger('change');
});

function companyInfoRes(data,formId){
    if(data.status==1){
        Swal.fire({ icon: 'success', title: data.message});

        window.location.href = base_url + controller;
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