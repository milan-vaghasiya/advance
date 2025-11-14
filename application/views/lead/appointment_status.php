<form>
    <div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <th>Appointment Date</th>
                <td><?=formatDate($appointmentData->appointment_date,'d-m-Y ').formatDate($appointmentData->appointment_time,'H:i A')?></td>
            </tr>
            <tr>
                <th>Mode</th>
                <td><?=$appointmentMode[$appointmentData->mode]?></td>
            </tr>
            <tr>
                <th>Contact Person</th>
                <td><?=$appointmentData->contact_person?></td>
            </tr>
            <tr>
                <th>Purpose</th>
                <td><?=$appointmentData->purpose?></td>
            </tr>
        </table>
    </div>
    <hr>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="<?=$appointmentData->id?>">            
            
            <div class="col-md-12 form-group">
                <label for="note">Status</label>
                <select name="status" id="ststus" class="form-control">
                    <option value="2">Complete</option>
                    <option value="1">Cancel</option>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="notes">Note</label>
                <textarea name="notes" id="notes" class="form-control"></textarea>
                <div class="error note"></div>
            </div>

        </div>
    </div>    
</form>

    
