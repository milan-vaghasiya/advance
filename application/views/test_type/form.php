<form>
    <div class="col-md-12">
        <div class="row">            
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : "")?>" />

            <div class="col-md-12 form-group">
                <label for="test_name">Test Name</label>
                <input type="text" name="test_name" id="test_name" class="form-control req" value="<?=(!empty($dataRow->test_name) ? $dataRow->test_name : "")?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="head_name">Head Name</label>
                <input type="text" name="head_name" id="head_name" class="form-control req" value="<?=(!empty($dataRow->head_name) ? $dataRow->head_name : "")?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="doc_no"> IATF Doc No</label>
                <input type="text" name="doc_no" id="doc_no" class="form-control" value="<?=(!empty($dataRow->doc_no) ? $dataRow->doc_no : "")?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="rev_detail"> Revision </label>
                <input type="text" name="rev_detail" id="rev_detail" class="form-control" value="<?=(!empty($dataRow->rev_detail) ? $dataRow->rev_detail : "")?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="test_remark">Test Remark</label>
                <textarea name="test_remark" id="test_remark" class="form-control"><?=(!empty($dataRow->test_remark) ? $dataRow->test_remark : "")?></textarea>
            </div>
            <hr>
            <h6>Sample Detail : </h6>
            <div class="col-md-4 form-group">
                <label for="sample_1"> 0 to 10 tonne(Pcs)  </label>
                <input type="text" name="sample_1" id="sample_1" class="form-control numericOnly" value="<?=(!empty($dataRow->sample_1) ? $dataRow->sample_1 : "")?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="sample_2"> 10-25 tonne(Pcs) </label>
                <input type="text" name="sample_2" id="sample_2" class="form-control numericOnly" value="<?=(!empty($dataRow->sample_2) ? $dataRow->sample_2 : "")?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="sample_3"> 25 and above(Pcs) </label>
                <input type="text" name="sample_3" id="sample_3" class="form-control numericOnly" value="<?=(!empty($dataRow->sample_3) ? $dataRow->sample_3 : "")?>" />
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        $('#head_name').typeahead({
            source: function(query, result)
            {
                $.ajax({
                    url:base_url + 'testType/testHeadSearch',
                    method:"POST",
                    global:false,
                    data:{query:query},
                    dataType:"json",
                    success:function(data){
                        result($.map(data, function(item){return item;}));                    
                    }
                });
            }
        });
    });
</script>