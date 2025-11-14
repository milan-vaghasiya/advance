<form data-res_function="resAppointments">
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="lead_id" id="lead_id" value="<?=$lead_id?>">
            <input type="hidden" name="entry_type" id="entry_type" value="2">

            <div class="col-md-3 form-group">
                <label for="appointment_date">Appintment Date</label>
                <input type="date" name="appointment_date" id="appointment_date" min="<?=date("Y-m-d")?>" class="form-control req" value="<?=(!empty($dataRow->appointment_date))?$dataRow->appointment_date:date("Y-m-d")?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="appointment_time">Appintment Time</label>
                <input type="time" name="appointment_time" id="appointment_time" min="<?=date("Y-m-d H:i:s")?>" class="form-control req" value="<?=(!empty($dataRow->appointment_time))?date("h:i:s",strtotime($dataRow->appointment_time)):date("h:i:s")?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="mode">Mode</label>
                <select name="mode" id="mode" class="form-control req select2">
                    <?php
                       foreach($appointmentMode as $key=>$row):
                           $selected = (!empty($dataRow->mode) and $dataRow->mode == $row)?"selected":"";
                           echo '<option value="'.$key.'" '.$selected .'>'.$row.'</option>';
                       endforeach;
                   ?>
                    ?>
                </select>
            </div>
			<div class="col-md-3 form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" id="contact_person" class="form-control text-capitalize req" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="purpose">Purpose</label>
                <div class="input-group">
                    <textarea name="purpose" id="purpose" class="form-control"><?=(!empty($dataRow->purpose))?$dataRow->purpose:""?></textarea>
                </div>
                <div class="error purpose"></div>
            </div>
            <div class="col-md-12 form-group">
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right mt-30" onclick="customStore({'formId':'appointment','fnsave':'saveAppointment'},);"> <i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>    
</form>
<hr>
<style>#appointmentTable td,#appointmentTable th{font-size:0.8rem;}</style>
<div class="col-md-12">
    <div class="row">
        <label for="">Appointments : </label>
        <div class="table-responsive">
            <table id='appointmentTable' class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Appointment Schedule</th>
                        <th>Mode</th>
                        <th>Appointment With</th>
                        <th>Notes</th>
                        <th style="width:10%;">Action</th>
                    </tr>                            
                </thead>
                <tbody id="appointmentData">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    var appointmentTrans = {'postData':{'lead_id':$("#lead_id").val(),'entry_type':$("#entry_type").val()},'table_id':"appointmentTable",'tbody_id':'appointmentData','tfoot_id':'','fnget':'appointmentListHtml'};
    getTransHtml(appointmentTrans);
});

function resAppointments(data){
    if(data.status==1){
        toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

        $("#contact_person").val("");
        $("#purpose").val("");

        var appointmentTrans = {'postData':{'lead_id':$("#lead_id").val(),'entry_type':$("#entry_type").val()},'table_id':"appointmentTable",'tbody_id':'appointmentData','tfoot_id':'','fnget':'appointmentListHtml'};
        getTransHtml(appointmentTrans);
        initTable(); 
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }			
    }
}

function resTrashAppointment(data){
    if(data.status==1){
        toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

        var appointmentTrans = {'postData':{'lead_id':$("#lead_id").val(),'entry_type':$("#entry_type").val()},'table_id':"appointmentTable",'tbody_id':'appointmentData','tfoot_id':'','fnget':'appointmentListHtml'};
        getTransHtml(appointmentTrans);
        initTable(); 
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }			
    }
}
</script>