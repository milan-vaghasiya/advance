<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
                    <div class="float-start">
                        <ul class="nav nav-pills">
                            <li class="nav-item"> 
                                <a href="<?=base_url("companyInfo")?>" class="nav-tab btn waves-effect waves-light btn-outline-primary">Company Info</a>
                            </li>
                            <li class="nav-item"> 
                                <a href="<?=base_url("companyInfo/generalSetting")?>" class="nav-tab btn waves-effect waves-light btn-outline-primary active">General Settings</a>
                            </li>
                        </ul>
                    </div>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <form id="gerenalSetting" data-res_function="resSaveSettings">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table table-responsive">
                                                <table class="table table-bordered">
                                                    <?php
                                                        $groupedMenu = array_reduce($dataRow, function($itemData, $row) {
                                                            $itemData[$row->menu_name][] = $row;
                                                            return $itemData;
                                                        }, []);

                                                        $j = 1;
                                                        foreach($groupedMenu as $menu => $subMenu):
                                                            echo '<tr class="thead-dark">
                                                                <th colspan="4" class="text-center">'.$menu.'</th>
                                                            </tr>';

                                                            echo '<tr class="thead-dark">
                                                                <th>#</th>
                                                                <th>Menu Name</th>
                                                                <th style="width:20%;">Vou. Prefix</th>
                                                                <th style="width:20%;">Vou. Strat No.</th>
                                                            </tr>';

                                                            $i=1;
                                                            foreach($subMenu as $row):
                                                                echo '<tr>
                                                                    <td>'.$i.'</td>
                                                                    <td>'.$row->vou_name_long.'</td>
                                                                    <td>
                                                                        <input type="hidden" name="settings['.$j.'][id]" value="'.$row->id.'">

                                                                        <input type="text" name="settings['.$j.'][vou_prefix]" class="form-control" value="'.$row->vou_prefix.'">
                                                                    </td> 
                                                                    <td>
                                                                        <input type="text" name="settings['.$j.'][auto_start_no]" class="form-control numericOnly" value="'.$row->auto_start_no.'">
                                                                    </td>
                                                                </tr>';
                                                                $i++;$j++;
                                                            endforeach;
                                                        endforeach;
                                                    ?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 class="card-title">Payment Reminder Settings</h4>
                                            <input type="hidden" name="account_setting[id]" id="id" value="<?=(!empty($accountSetting->id))?$accountSetting->id:""?>">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="rrb_days">Receivable Reminder Before Days</label>
                                            <input type="text" name="account_setting[rrb_days]" id="rrb_days" class="form-control numricOnly" value="<?=(!empty($accountSetting->rrb_days))?$accountSetting->rrb_days:""?>">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="prb_days">Payable Reminder Before Days</label>
                                            <input type="text" name="account_setting[prb_days]" id="prb_days" class="form-control numricOnly" value="<?=(!empty($accountSetting->rrb_days))?$accountSetting->rrb_days:""?>">
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="is_description">Item Description</label>
                                            <select name="account_setting[is_description]" id="is_description" class="form-control">
                                                <option value="0" <?=(!empty($accountSetting) && $accountSetting->is_description == 0) ? "selected" : "";?>>No</option>
                                                <option value="1" <?=(!empty($accountSetting) && $accountSetting->is_description == 1) ? "selected" : "";?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 class="card-title">Accounting Settings</h4>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="tcs_limit">TCS Limit</label>
                                            <input type="text" name="account_setting[tcs_limit]" id="tcs_limit" class="form-control floatOnly" value="<?=(!empty($accountSetting->tcs_limit))?floatval($accountSetting->tcs_limit):""?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="tds_limit">TDS Limit</label>
                                            <input type="text" name="account_setting[tds_limit]" id="tds_limit" class="form-control floatOnly" value="<?=(!empty($accountSetting->tds_limit))?floatval($accountSetting->tds_limit):""?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="tcs_with_pan_per">TCS With PAN Per(%)</label>
                                            <input type="text" name="account_setting[tcs_with_pan_per]" id="tcs_with_pan_per" class="form-control floatOnly" value="<?=(!empty($accountSetting->tcs_with_pan_per))?$accountSetting->tcs_with_pan_per:""?>">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="tcs_without_pan_per">TCS Without PAN Per(%)</label>
                                            <input type="text" name="account_setting[tcs_without_pan_per]" id="tcs_without_pan_per" class="form-control floatOnly" value="<?=(!empty($accountSetting->tcs_without_pan_per))?$accountSetting->tcs_without_pan_per:""?>">
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <h4 class="card-title">Production </h4>
                                        <div class="col-md-3">
                                            <label for="prod_kgpcs_conversion">Kgs to pcs conversion</label>
                                            <select name="account_setting[prod_kgpcs_conversion]" id="prod_kgpcs_conversion" value="<?=(!empty($accountSetting->prod_kgpcs_conversion))?$accountSetting->prod_kgpcs_conversion:""?>" class="form-control">
                                                <option value="0" <?=(!empty($accountSetting) && $accountSetting->prod_kgpcs_conversion == 0) ? "selected" : "";?>>No</option>
                                                <option value="1" <?=(!empty($accountSetting) && $accountSetting->prod_kgpcs_conversion == 1) ? "selected" : "";?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer bg-facebook">
                            <div class="col-md-12"> 
                                <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="customStore({'formId':'gerenalSetting','fnsave':'saveSettings'});" ><i class="fa fa-check"></i> Save </button>
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
function resSaveSettings(data,formId){
    if(data.status==1){
        Swal.fire({ icon: 'success', title: data.message});

        window.location.reload();
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