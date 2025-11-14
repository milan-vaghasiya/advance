<form >
	<div class="col-md-12">
		<div class="row">
			<input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="item_type" id="item_type" value="" >
			<div class="col-md-2 form-group">
				<label for="ref_date">Issue Date</label>
				<input type="date" id="ref_date" name="ref_date" class="form-control req" value="<?=date("Y-m-d")?>" />
			</div>
			<div class="col-md-2 form-group">
				<label for="prc_id">PRC No.</label>
				<select name="prc_id" id="prc_id" class="form-control select2">
                    <option value="">Select PRC</option>
                    <?php
                    if(!empty($prcList)){
                        foreach($prcList as $row){
                            ?>  <option value="<?=$row->id?>" data-item_id="<?=$row->item_id?>"><?=$row->prc_number?></option> <?php
                        }
                    }
                    ?>
                </select>
			</div>
            <div class="col-md-3 form-group">
				<label for="bom_group">Group</label>
				<select name="bom_group" id="bom_group" class="form-control select2">
                    <option value="">Select Material Group</option>
                    <?php
                    if(!empty($groupList)){
                        foreach($groupList as $row){
                            ?>  <option value="<?=$row->group_name?>"><?=$row->group_name?></option> <?php
                        }
                    }
                    ?>
                </select>
			</div>
			<div class="col-md-5 form-group">
				<label for="item_id">Bom Item</label>
				<select name="item_id" id="item_id" class="form-control select2 req" autocomplete="off" class="form-control select2">
					<option value="">Select Item</option>
					
				</select>
			</div>

			<div class="col-md-12  form-group">
				<label for="remark">Remark</label>
				<input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>" />
			</div>
			<div class="col-md-12 form-group mt-3">
                <div class="error general_batch_no"></div>
                <table class="table jpExcelTable">
                    <thead  class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Location</th>
                            <th>Batch No</th>
                            <th>Stock Qty</th>
                            <th>Issue Qty</th>
                        </tr>
                    </thead>
                    <tbody id="stockTbody">
                    <?=!empty($batchHtml)?$batchHtml:''?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total</th>
                            <th id="totalQty"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
		</div>
	</div>
</form>
