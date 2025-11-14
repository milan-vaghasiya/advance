<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">

            <div class="col-md-12 form-group">
                <label for="name">Group Name</label>
                <input type="text" name="name" id="name" class="form-control req" value="<?=(!empty($dataRow->name))?$dataRow->name:""?>">
            </div>

            <div class="col-md-9 form-group">
                <label for="under_group_id">Perent Group</label>
                <select name="under_group_id" id="under_group_id" class="form-control select2 req">
                    <option value="">Select Group</option>
                    <?php
                        foreach($masterGroupList as $row):
                            $selected = (!empty($dataRow->under_group_id) && $row->id == $dataRow->under_group_id)?"selected":"";
                            echo "<option value='".$row->id."' ".$selected.">".$row->name."</option>";
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="seq">Seq.</label>
                <input type="text" name="seq" id="seq" class="form-control" value="<?=(!empty($dataRow->seq))?$dataRow->seq:""?>">
            </div>
        </div>
    </div>
</form>