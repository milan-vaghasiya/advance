<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="">

            <div class="col-md-12 form-group">
                <label for="item_id">Item</label>
                <select name="item_id" id="item_id" class="form-control select2 req">
                    <option value="">Select Item</option>
                    <?php
                    if(!empty($itemList)){
                        foreach($itemList as $row){
                            echo '<option value="'.$row->id.'">'.$row->item_name.'</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" class="form-control"></textarea>
            </div>

            <hr>
            <div class="col-md-12 form-group">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-info">
                            <th>Location</th>
                            <th>Batch No.</th>
                            <th>Stock Qty.</th>
                            <th>Rej. Qty.</th>
                        </thead>
                        <tbody id="tbodyData"></tbody>
                    </table>
                    <div class="error table_err"></div>
                </div>
            </div>

        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('change', '#item_id', function () {
        var item_id = $(this).val();

        if(item_id) {
            $.ajax({
                url:base_url + controller + "/getBatchWiseStock",
                type:'post',
                data:{ item_id:item_id },
                dataType:'json',
                success:function(data){
                    if(data.status == 1){
                        $('#tbodyData').html('');
                        $('#tbodyData').html(data.tbodyData);
                    }
                }
            });
        }
    });
});
</script>