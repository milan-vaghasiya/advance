<div class="row">
	<div class="col-12">
		<table class="table item-list-bb">			
		<?php $sample_size= (!empty($firData->sampling_qty))?floatval($firData->sampling_qty):5 ?>
			<thead>
				<tr style="text-align:center;" class="bg-light">
					<th rowspan="2">SL. No.</th>
					<th rowspan="2">Parameter</th>
					<th rowspan="2">Specification</th>
					<th rowspan="2">CTQ</th>
					<th rowspan="2">Inspection Method</th>
					<th colspan="<?= ($sample_size + 1) ?>">Supplier Observation</th>
					<th colspan="2">PHC - SQA Observation</th>
					<th colspan="<?= ($sample_size + 1) ?>">PHPL Observation</th>
				</tr>

				<tr style="text-align:center;" class="bg-light">
					<?php for ($i = 1; $i <= $sample_size; $i++): ?>
						<th><?= $i;?></th>
					<?php endfor; ?>
					<th>Remarks</th>
 
					<th>Min</th>
					<th>Max</th>

					<?php for ($i = 1; $i <= $sample_size; $i++): ?>
						<th><?= $i;?></th>
					<?php endfor; ?>
					<th>Remarks</th>
				</tr>
			</thead>

			<tbody>
				<?php
					$tbodyData="";$i=1; 
					foreach ($paramData as $row):
						$obj = New StdClass;
						if(!empty($firData)):
							$obj = json_decode($firData->observation_sample); 
						endif;
					?>
						<tr>
							<td class="text-center"><?= $i ?></td>
							<td><?= $row->parameter ?></td>
							<td><?= $row->specification ?></td>
							<td ></td>
							<td><?= $row->instrument ?></td>

							<?php for ($c = 0; $c < $sample_size; $c++): ?>
								<td class="text-center" style="width: 5%;">
									<?= $obj->{$row->id}[$c] ?? '' ?>
								</td>
							<?php endfor; ?>

							<td style="text-align:center;" style="width: 8%;">
								<?= $obj->{$row->id}[$c] ?? '';?>
							</td>

							<td class="text-center" ><?= $row->min;?></td>
							<td class="text-center" ><?= $row->max;?></td>

							<?php for ($c = 0; $c < $sample_size; $c++): ?>
								<td class="text-center" style="width: 5%;"></td>
							<?php endfor; ?>

							<td></td> 
						</tr>
						<?php
						$i++;
					endforeach;
				?>
			</tbody>
		</table>
	</div>
</div>