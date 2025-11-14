<form>
    <div class="col-md-12">
        <div class="row">
            <h6 style="color:#ff0000;font-size:1rem;"><i>Note : Cycle Time Per Piece In Seconds</i></h6>
            <table class="table excel_table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;text-align:center;">#</th>
                        <th style="width:20%;">Process Name</th>
                        <th style="width:10%;">Cycle Time</th>
                        <th style="width:10%;">Finished Weight</th>
                        <th style="width:10%;">Process Cost</th>
                        <th style="width:15%;">Cost Per Unit</th>
                        <th style="width:10%;">Output Qty</th>
                        <th style="width:20%;">Drawing File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
					$html = "";
                    if (!empty($processData)) :
                        $i = 1; $j=0;
                        foreach ($processData as $row) :
                            $pid = (!empty($row->id)) ? $row->id : "";
                            $ct = (!empty($row->cycle_time)) ? $row->cycle_time : "";
                            $fgwt = (!empty($row->finish_wt)) ? $row->finish_wt : "";
                            $conv_ratio = (!empty($row->conv_ratio)) ? $row->conv_ratio : "";
                            $process_cost = (!empty($row->process_cost)) ? $row->process_cost : "";
                            $output_qty = (!empty($row->output_qty)) ? $row->output_qty : "";
                            $html .= '<tr id="' . $row->id . '">
                                <td class="text-center">' . $i++ . '</td>
                                <td>' . $row->process_name . '</td>
                                <td class="text-center">
                                    <input type="text" name="ctData['.$j.'][cycle_time]" class="form-control numericOnly" step="1" value="' . $ct . '" />
                                    <input type="hidden" name="ctData['.$j.'][id]" value="' . $pid . '" />
                                </td>
                                <td class="text-center">
                                    <input type="text" name="ctData['.$j.'][finish_wt]" class="form-control floatOnly" step="1" value="' . $fgwt . '" />
                                </td> 
                                <td class="text-center" hidden>
                                    <input type="text" name="ctData['.$j.'][conv_ratio]" class="form-control floatOnly" step="1" value="' . $conv_ratio . '" />
                                </td> 
                                 <td class="text-center">
                                    <input type="text" name="ctData['.$j.'][process_cost]" class="form-control floatOnly" step="1" value="' . $process_cost . '" />
                                </td>
								<td>
									<select name="ctData['.$j.'][uom]" class="form-control select2">
										<option value="">Select Unit</option>';
										foreach($unitList as $unitRow):
											$selected = (!empty($row->uom) && $unitRow->unit_name == $row->uom) ? "selected" : "";
											$html .= '<option value="'.$unitRow->unit_name.'" '.$selected.'>'.$unitRow->unit_name.'</option>';
										endforeach;
							$html .= '</select>
								</td>
                                <td class="text-center">
                                    <input type="text" name="ctData['.$j.'][output_qty]" class="form-control numericOnly validateOutQty" min="1"  value="' . $output_qty	. '" />
                                </td>                                   
								<td class="d-flex align-items-center">
									<input type="file" name="drawing_file[]" class="form-control" />
									<input type="hidden" name="old_drawing_file[]" value="'.$row->drawing_file.'">';
									if(!empty($row->drawing_file)):
										$html .= '<a class="text-primary font-bold ml-5" id="supplier_file" href="'.base_url("assets/uploads/process_drg/".$row->drawing_file).'" download=""><i class="fa fa-download" aria-hidden="true"></i></a>';
									endif;
							$html .= '</td>
                              </tr>';
							$j++;
                        endforeach;
                    else :
                        $html .= '<tr><td colspan="8" class="text-center">No Data Found.</td></tr>';
                    endif;
					
					echo $html;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('keyup','.validateOutQty',function(){
        var output_qty = $(this).val() || "";
        if(parseFloat(output_qty) <= 0){
            $(this).val("1");
        }else{
            return true;
        }
    });
});
</script>