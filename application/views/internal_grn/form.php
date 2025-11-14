<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($gateInwardData->id))?$gateInwardData->id:""?>">
            <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?=(!empty($gateInwardData->trans_prefix))?$gateInwardData->trans_prefix:$trans_prefix?>">
            <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($gateInwardData->trans_no))?$gateInwardData->trans_no:$trans_no?>"> 
            <input type="hidden" name="grn_type" id="grn_type" value="3"> 

            <div class="col-md-3 form-group">
                <label for="trans_no">GRN No.</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($gateInwardData->trans_number))?$gateInwardData->trans_number:$trans_number?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="trans_date">GRN Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=(!empty($gateInwardData->trans_date))?$gateInwardData->trans_date:getFyDate("Y-m-d")?>">
            </div>            

            <div class="col-md-6 form-group">
                <label for="item_id">Consuption Item Name</label>
                <select id="item_id" name="item_id" class="form-control select2 req">
                    <option value="">Select Item Name</option>
                    <?php 
                    foreach($itemList as $row):
                        echo '<option value="'.$row->id.'" data-item_type="'.$row->item_type.'">'.(!empty($row->item_code) ? '[ '.$row->item_code.' ] ' : '').$row->item_name.(!empty($row->material_grade) ? ' '.$row->material_grade : '').'</option>';

                    endforeach;
                    ?>
                </select>
            </div>
            
            <div class="col-md-4 form-group">
                <label for="to_item">Convert Item Name</label>
                <select id="to_item" name="to_item" class="form-control select2 req">
                    <option value="">Select Item</option>
                    <?php 
                    foreach($itemList as $row):
                        echo '<option value="'.$row->id.'" data-item_type="'.$row->item_type.'">'.(!empty($row->item_code) ? '[ '.$row->item_code.' ] ' : '').$row->item_name.(!empty($row->material_grade) ? ' '.$row->material_grade : '').'</option>';

                    endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="batch_no">Batch No</label>
                <select id="batch_no" name="batch_no" class="form-control select2 req">
                    <option value="">Select Batch</option>
                    <?php echo (!empty($batchNo)? $batchNo :'')?>
                </select>
            </div>  

            <div class="col-md-4 form-group">
                <label for="fg_item_id">Finish Goods</label>
                <select id="fg_item_id" name="fg_item_id" class="form-control select2 req">
                    <option value="">Select Finish Goods</option>
                    <?php echo (!empty($fgoption)? $fgoption :'')?>
                </select>
            </div>  
            <div class="col-md-12 form-group">  
                <label for="item_remark">Remark</label>
                <input type="text" id="item_remark" name="item_remark" class="form-control" value="">
            </div> 
        </div>

        <div class="col-md-12 form-group mt-4">
            <div class="error general_batch_no"></div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <th>Location</th>
                        <th>Batch No.</th>
                        <th>Stock Qty.</th>
                        <th>Issue Qty.</th>
                    </thead>
                    <tbody id="tbodyData"> 
                    </tbody> 
                </table>
                <div class="error table_err"></div>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function() {
    $(document).on('change','#item_id',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		var item_id = $(this).val();
        var item_type = $("#item_id :selected").data('item_type');
		
		$('#to_item').val(item_id);
		
		$.ajax({
			url: base_url + 'purchaseIndent/getItemWiseFgList',
			type:'post',
			data:{ item_id:item_id ,batch_details:1 ,item_type:item_type},
			dataType:'json',
			success:function(data){
				$("#fg_item_id").html(data.fgoption);
				$("#batch_no").html(data.batchNo);
				initSelect2();
			}
		});
	});
	
	$(document).on('change','#to_item',function(e){
        e.stopImmediatePropagation();e.preventDefault();
		var item_id = $(this).val();
        var item_type = $("#to_item :selected").data('item_type');
		
		$.ajax({
			url: base_url + 'purchaseIndent/getItemWiseFgList',
			type:'post',
			data:{ item_id:item_id , item_type:item_type},
			dataType:'json',
			success:function(data){
				$("#fg_item_id").html(data.fgoption);
				initSelect2();
			}
		});
	});

    $(document).on('change', '#batch_no', function () {
        var batch_no = $(this).val();
        var item_id = $('#item_id').val();

        if(batch_no){
            $.ajax({
                url:base_url + controller + "/getBatchWiseStock",
                type:'post',
                data:{batch_no:batch_no,item_id:item_id},
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