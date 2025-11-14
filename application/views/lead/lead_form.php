<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=!empty($dataRow->id)?$dataRow->id:''?>">
            <input type="hidden" name="entry_type" id="entry_type" value="1">
            <input type="hidden" name="status" id="status" value="<?=(!empty($dataRow->status))?$dataRow->status:0?>">

            <div class="col-md-3 form-group">
                <label for="lead_date">Approch Date</label>
                <input type="date" name="lead_date" id="lead_date" max="<?=date("Y-m-d")?>" class="form-control req" value="<?=(!empty($dataRow->lead_date))?$dataRow->lead_date:date("Y-m-d")?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="lead_from">Lead From</label>
                <select name="lead_from" id="lead_from" class="form-control select2">
                    <option value="">Select</option>
                    <?php
                        foreach($this->leadFrom as $row):
                            $selected = (!empty($dataRow->lead_from) && $dataRow->lead_from == $row)?"selected":"";
                            echo '<option value="'.$row.'" '.$selected.'>'.$row.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="mode">Mode</label>
                <select name="mode" id="mode" class="form-control req select2">
                    <?php
                        foreach($this->appointmentMode as $key=>$row):
                            $selected = (!empty($dataRow->mode) && $dataRow->mode == $key)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected .'>'.$row.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

			<div class="col-md-3 form-group">
                <label for="sales_executive">Sales Executives</label>
                <select class="form-control select2" name="sales_executive" id="sales_executive">
                    <option value="">Select Sales Executive</option>
                    <?php
                    if(!empty($salesExecutives)){
                        foreach($salesExecutives as $row){
                            $selected = (!empty($dataRow->sales_executive) && $dataRow->sales_executive == $row->id)?'selected':(($this->loginId == $row->id)?'selected':'');
                            $disabled = (in_array($this->userRole,[-1,1]) || $row->id == $this->loginId)?:'disabled';
                            echo '<option value="'.$row->id.'" '.$selected.' '.$disabled.'>'.$row->emp_name.' </option>';
                        }
                    }
                    ?>
                </select>
            </div>            

            <div class="col-md-3 form-group">
                <label for="party_name">Customer</label>
                <input type="text" name="party_name" id="party_name" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>" class="form-control req" />
                <input type="hidden" name="party_id" id="party_id" value="<?=(!empty($dataRow->party_id))?$dataRow->party_id:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" id="contact_person" class="form-control" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="contact_no">Contact No.</label>
                <input type="text" name="contact_no" id="contact_no" class="form-control" value="<?=(!empty($dataRow->contact_no))?$dataRow->contact_no:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="next_fup_date">Next Follow UP Date</label>
                <input type="date" name="next_fup_date" id="appointment_next_fup_datedate" class="form-control" value="<?=(!empty($dataRow->next_fup_date))?$dataRow->next_fup_date:getFyDate()?>" min="<?=getFyDate()?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="notes">Notes</label>
                <div class="input-group">
                    <textarea name="notes" id="notes" class="form-control"><?=(!empty($dataRow->notes))?$dataRow->notes:""?></textarea>
                </div>
            </div>

        </div>
    </div>    
</form>
<script>
    $(document).ready(function(){

        $('#party_name').typeahead({
            source: function(query, result)
            {
                $.ajax({
                    url:base_url + controller + '/partySearch',
                    method:"POST",
                    global:false,
                    data:{query:query},
                    dataType:"json",
                    success:function(data){
                        result($.map(data, function(party){ return party; }));
                    }
                });
            }
        });
        
        $(document).on('change','#party_name',function(){
            var party_name = $(this).val();
            $.ajax({
                    url:base_url + controller + '/getPartyData',
                    data:{party_name:party_name},
                    method:"POST",
                    dataType:"json",
                    success:function(data){
                        $("#sales_executive").html(data.sales_executive);
                        $("#sales_executive").select2();
                        $("#contact_person").val(data.contact_person);
                        $("#contact_no").val(data.contact_no);
                    }
                });
        });
	});
</script>