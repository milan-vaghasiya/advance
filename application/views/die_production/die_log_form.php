<form data-res_function="getDieLogResponse">
    <div class="row">
        <input type="hidden" name="id" id="id" value="<?=!empty($dataRow->log_id)?$dataRow->log_id:''?>">
        <input type="hidden" name="die_id" id="die_id" value="<?=$die_id?>">
        <input type="hidden" name="process_by" id="process_by" value="<?=$process_by?>">
        <input type="hidden" name="ref_id" id="ref_id" value="<?=!empty($challan_id)?$challan_id:''?>">
        <input type="hidden" name="">
        <div class="col-md-4 form_group">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>">
        </div>
        <?php if($process_by == 1){ ?>
            <div class="col-md-8 form-group">
                <label for="process_description">Setup Description</label>
                <input type="text" name="process_description" class="form-control" >
            </div>
        <?php } ?>
		
        <div class="col-md-4 form-group">
            <label for="start_date_time">Start Date Time</label>
            <input type="datetime-local" name="start_date_time" id="start_date_time" class="form-control req countProductionTime">
        </div>
        <div class="col-md-4 form-group">
            <label for="end_date_time">End Date Time</label>
            <input type="datetime-local" name="end_date_time" id="end_date_time" class="form-control req countProductionTime">
        </div>
        <div class="col-md-4 form-group">
            <label for="production_time">Production Time(HH:MM)</label>
            <input type="text" name="production_time" id="production_time" class="form-control req" readOnly>
        </div>
        <div class="col-md-4 form-group">
            <label for="program_time">Program Time(HH:MM)</label>
            <input type="text" name="program_time" id="program_time" class="form-control">
        </div>
        <?php if(!empty($process_by) && $process_by == 2){ ?>
        <!-- IF CHALLAN RECEIVE FROM VENDOR -->
            <div class="col-md-4 form-group">
                <label for="in_challan_no">In challan No</label>
                <input type="text" name="in_challan_no" id="in_challan_no" class="form-control req">
            </div>
            <input type="hidden" name="processor_id" id="processor_id" value="<?=$processor_id?>">
            <div class="col-md-4 form-group" >
                <label for="attachment">Attachment</label>
                <input type="file" name="attachment" id="attachment" class="form-control" />
            </div>
        <?php }else{?>
        <!--- IF INHOUSE PRODUCTION -->
        
        <div class="col-md-4">
            <label for="processor_id">Machine</label>
            <select name="processor_id" id="processor_id" class="form-control select2 req">
                <option value="0">Select</option>
                <?php
                if(!empty($machineList)){
                    foreach($machineList as $row){
                        $selected = (!empty($dataRow->processor_id ) && $dataRow->processor_id  == $row->id)?'selected':'';
                        echo '<option value="'.$row->id.'" '.$selected.'>'.$row->item_code.'</option>';
                    }
                }
                ?>
            </select>
        </div>


        <div class="col-md-4">
            <label for="operator_id">Operator</label>
            <select name="operator_id" id="operator_id" class="form-control select2">
                <option value="0">Select</option>
                <?php
                if(!empty($operatorList)){
                    foreach($operatorList as $row){
                        ?><option value="<?=$row->id?>"><?=$row->emp_name?></option><?php
                    }
                }
                ?>
            </select>
        </div>
        <?php } ?>
        
        <div class="col-md-12 form-group">
            <label for="remark">Remark</label>
            <textarea type="text" name="remark" id="remark" class="form-control" value="" rows="2"></textarea>
        </div>

    </div>
</form>
<script>
    $(document).ready(function(){
        var overwriteMask = IMask(document.getElementById('production_time'),{
            mask: 'HH:MM',
            definitions: {
                H: {
                    mask: '0',
                    displayChar: '00',
                    placeholderChar: '00',
                },
                M: {
                    mask: '0',
                    displayChar: '00',
                    placeholderChar: '00',
                },
            },
            lazy: true,
            overwrite: 'shift'
        });
		
		var overwriteMask = IMask(document.getElementById('program_time'),{
            mask: 'HH:MM',
            definitions: {
                H: {
                    mask: '0',
                    displayChar: '00',
                    placeholderChar: '00',
                },
                M: {
                    mask: '0',
                    displayChar: '00',
                    placeholderChar: '00',
                },
            },
            lazy: true,
            overwrite: 'shift'
        });
		

		$(document).on("keyup change",".countProductionTime",function(){
			var startTime = $('#start_date_time').val(); 
			var endTime = $('#end_date_time').val();
			
			$('.error').html("");
			if(startTime > endTime){
				$('#end_date_time').val("");
				$('.error .end_date_time').html("Invalid Date.");
			}else{

				$("#production_time").val("00:00");
				if(startTime != "" && endTime != ""){
					var diff =  Math.abs(new Date(endTime) - new Date(startTime));
					var seconds = Math.floor(diff/1000); //ignore any left over units smaller than a second
					var minutes = Math.floor(seconds/60); 
					seconds = seconds % 60;
					var hours = Math.floor(minutes/60);
					minutes = minutes % 60;

					var timeDiff = ("0" + hours).slice(-2) + ":" + ("0" + minutes).slice(-2);
					$("#production_time").val(timeDiff);
				}
			}
		});
    });
</script>
