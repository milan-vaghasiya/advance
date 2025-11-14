<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=$id?>" />
            <input type="hidden" name="status" id="status" value="<?=$status?>" />
			
			<div class="table-responsive">
				<table class="table jpExcelTable text-center">
					<tr class="thead-dark">
						<th colspan="2" style="width:25%;">Material</th>
						<th colspan="2" style="width:25%;">Process</th>
						<th colspan="2" style="width:25%;">Scrap</th>
						<th style="width:25%;">Total</th>
					</tr>
					<tr class="thead-dark">
						<th>Rate</th>
						<th>Weight</th>
						<th>Time(Hours)</th>
						<th>Rate(Hourly)</th>
						<th>Rate</th>
						<th>Weight</th>
						<th>Weight</th>
					</tr>
					<?php 
						$scrap_wt = (!empty($dieData->weight) && !empty($dieData->material_weight)) ? ($dieData->material_weight - $dieData->weight) : 0;
						$scrap_cost = $dieData->scrap_rate * $scrap_wt;
					?>
					<tr>
						<td><?=round($dieData->material_value,2)?></td>
						<td><?=round($dieData->weight,2)?></td>
						<td><?=round($dieData->production_time,2)?></td>
						<td><?=round($dieData->mhr,2)?></td>
						<td><?=round($dieData->scrap_rate,2)?></td>
						<td><?=round($scrap_wt,2)?></td>
						<td><?=round($dieData->weight,2)?></td>
					</tr>
					<tr class="thead-dark">
						<th colspan="2">Material Cost</th>
						<th colspan="2">Process Cost</th>
						<th colspan="2">Scrap Cost</th>
						<th>Total Value</th>
					</tr>
					<tr>
						<td colspan="2"><?=round($dieData->material_cost,2)?></td>
						<td colspan="2"><?=round($dieData->process_cost,2)?></td>
						<td colspan="2"><?=round($scrap_cost,2)?></td>
						<td><?=round($dieData->total_value,2)?></td>
					</tr>
				</table>
			</div>
			
			<hr>
			
			<?php if($status != 4): ?>
				<div class="col-md-12 form-group">
					<label for="acc_vou">Acc. Voucher No.</label>
					<input type="text" name="acc_vou" id="acc_vou" class="form-control req" value=""/>
				</div>
			<?php else: ?>
				<div class="col-md-12 form-group">
					<label for="rej_vou">Rej. Voucher No.</label>
					<input name="rej_vou" id="rej_vou" class="form-control req"/>
				</div>
			<?php endif; ?>
        </div>
    </div>
</form>