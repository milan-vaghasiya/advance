<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            
            <div class="col-md-12 form-group">
                <label for="country_id">Select Country</label>
                <select name="country_id" id="country_id" class="form-control country_list select2 req" data-state_id="state_id" data-selected_state_id="<?=(!empty($dataRow->state_id))?$dataRow->state_id:4030?>">
                    <option value="">Select Country</option>
                    <?php foreach($countryData as $row):
                        $selected = (!empty($dataRow->country_id) && $dataRow->country_id == $row->id)?"selected":((empty($dataRow) && $row->id == 101)?"selected":"");

                    ?>
                        <option value="<?=$row->id?>" <?=$selected?>><?=$row->name?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="state_id">Select State</label>
                <select name="state_id" id="state_id" class="form-control state_list select2 req">
                    <option value="">Select State</option>
                </select>
            </div>
            
            <div class="col-md-12 form-group">
                <label for="name">Cities Name</label>
                <input type="text" name="name" id="name" class="form-control req" value="<?=(!empty($dataRow->name))?$dataRow->name:""?>">
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){    
    $("#country_id").trigger('change');
});
</script>