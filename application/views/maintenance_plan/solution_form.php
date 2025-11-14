<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="machine_id" id="machine_id" value="<?=(!empty($machine_id) ? $machine_id : '')?>">
            <input type="hidden" name="activity_list" id="activity_list" value="<?=(!empty($activity_list) ? $activity_list : '')?>">

            <div class="col-md-3 form-group">
                <label for="solution_date">Solution Date</label>
                <input type="datetime-local" name="solution_date" id="solution_date" class="form-control req" min="<?=date('Y-m-d')?>" max="<? date('Y-m-d')?>" value="<?=date('Y-m-d H:i:s')?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="agency">Maint. Through</label>
                <select name="agency" id="agency" class="form-control select2">
                    <option value="1">In House</option>
                    <option value="2">Third Party</option>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="sol_by">Maint. Agency</label> <span class="text-danger">*</span>
                <div class="input-group">
                    <div class="input-group-append inHouse" style="width:100%">
                        <input type="text" name="solution_by" id="solution_by" class="form-control" value="">
                    </div>
                    <div class="input-group-append thirdParty" style="width:100%">
                        <select name="vendor_id" id="vendor_id" class="form-control select2">
                            <option value="">Select Third Party</option>
                            <?php
                            foreach ($partyData as $row) :
                                echo "<option value='".$row->id."'>".$row->party_name."</option>";
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-12 form-group">  
                <hr>              
                <div class="error general_error"></div>
                <div class="table-responsive" style="min-height:60vh">
                    <table class="table table-bordered">
                        <thead class="thead-info">
                            <tr class="text-center">
                                <th style="width:5%">#</th>
                                <th style="width:30%">Activities</th>
                                <th style="width:15%">Status</th>
                                <th style="width:50%">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(!empty($dataRow)){
                                $i=1;
                                foreach($dataRow as $row){
                                    echo '<tr>
                                        <td class="text-center">'.$i++.'</td>
                                        <td>
                                            '.$row->activities.'
                                            <input type="hidden" name="id[]" id="id" value="'.$row->id.'">
                                        </td>
                                        <td>
                                            <select name="solution_status[]" id="solution_status" class="form-control select2">
                                                <option value="Ok">Ok</option>
                                                <option value="Not Ok">Not Ok</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="remark[]" id="remark" class="form-control" value="">                                        
                                        </td>
                                    </tr>';
                                }
                            }else{
                                echo '<tr><td class="text-center" colspan="4">Data not available.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function() {
    $(".thirdParty").hide();
    
    $(document).on("change", "#agency", function() {
        var agency = $(this).val();
        if (agency == 2) {
            $(".thirdParty").show();
            $(".inHouse").hide();
        } else {
            $(".thirdParty").hide();
            $(".inHouse").show();
        }
    });
});
</script>