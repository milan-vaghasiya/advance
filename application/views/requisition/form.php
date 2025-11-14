<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : "") ?>">
            <input type="hidden" name="item_name" id="item_name" value="">

            <div class="col-md-3 form-group">
                <label for="challan_no">Req No.</label>
                <div class="input-group">
                    <input type="text" name="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number) ? $dataRow->trans_number : $trans_number) ?>" readOnly />
                    <input type="hidden" name="trans_no" value="<?=(!empty($dataRow->trans_no) ? $dataRow->trans_no : $trans_no) ?>" readOnly />
                </div>
            </div>

            <div class="col-md-3 form-group">
                <label for="trans_date">Req Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date("Y-m-d")?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="item_id">Items</label>
                <select name="item_id" id="item_id" class="form-control reqItemId modal-select2 select2 req">
                    <option value="">Select Item</option>
                    <?php
                        if(!empty($itemData)){
                            foreach ($itemData as $row) {
								$selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id) ? "selected" : (!empty($md_item_id && $md_item_id == $row->id) ? "selected" : "");
								$item_name = (!empty($row->item_code)) ? "[".$row->item_code."] ".$row->item_name : $row->item_name;
								echo "<option value='".$row->id."' data-item_name='".$item_name."' ".$selected.">".$item_name."</option>";
                            }
                        }
                    ?>
                </select>
                <div class="error item_err"></div>
            </div>

			<div class="col-md-3 form-group">
                <label for="prc_id">PRC No.</label>
                <select name="prc_id" id="prc_id" class="form-control reqItemId modal-select2 select2">
                    <option value="">Select Prc No</option>
                    <?php
                        if(!empty($prcData)){
                            foreach ($prcData as $row) {
                                $selected = (!empty($dataRow->prc_id) && $dataRow->prc_id == $row->id) ? "selected" : (!empty($md_prc_id && $md_prc_id == $row->id) ? "selected" : "");
                                echo "<option value='".$row->id."' data-prc_number='".$row->prc_number."' ".$selected.">".$row->prc_number."</option>";
                            }
                        }
                    ?>
                </select>
                <input type="hidden" name="prc_number" id="prc_number" value="">
            </div>

            <div class="col-md-3 form-group">
                <label for="req_qty">Req. Qty</label>
                <input type="text" name="req_qty" id="req_qty" class="form-control req" value="<?=(!empty($dataRow->req_qty) ? floatval($dataRow->req_qty) : (!empty($md_req_qty) ? floatval($md_req_qty) : "") ) ?>" />
                <div class="error qty_err"></div>
            </div>

            <div class="col-md-3 form-group">
                <label for="urgency">Urgency</label>
                <select name="urgency" id="urgency" class="form-control single-select select2">
                    <option value="1" <?=(!empty($dataRow->urgency) && $dataRow->urgency == 1 ? "selected" : "") ?>>Low</option>
                    <option value="2" <?=(!empty($dataRow->urgency) && $dataRow->urgency == 2 ? "selected" : "") ?>>Medium</option>
                    <option value="3" <?=(!empty($dataRow->urgency) && $dataRow->urgency == 3 ? "selected" : "") ?>>High</option>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark) ? $dataRow->remark : "") ?>" />
            </div>

            <?php if(empty($dataRow) && empty($md_prc_id)){ ?>
				<div class="col-md-12 form-group">
					<button type="button" class="btn btn-info float-right" onclick="AddRow()">Add</button>
				</div>
            <?php } ?>
        </div>

        <?php if(empty($dataRow) && empty($md_prc_id)){ ?>
        <hr style="width:100%;">
        <div class="row">
            <div class="error genral_error"></div>
            <div class="table-responsive">
                <table id="addReqTbl" class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Prc No</th>
                            <th>Qty</th>
                            <th>Urgency</th>
                            <th>Remark</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="addReqBody"></tbody>
                </table>
            </div>
        </div>
        <?php } ?>
    </div>
</form>
<script>
    $(document).ready(function(){
    });

    $(document).on('change keyup','.reqItemId',function(){

        var item_val = $('option:selected', this).data('item_name')
        if(item_val != undefined && item_val != null){
            $("#item_name").val(item_val);
        }

		var prc_number_val = $('option:selected', this).data('prc_number')
        if(prc_number_val != undefined && prc_number_val != null){
            $("#prc_number").val(prc_number_val);
        }
    });

    function AddRow() {
        $(".error").html("");
        var isValid = 1;

        if ($("#item_id").val() == "") {
            $(".item_id").html("Item is required.");
            isValid = 0;
        }
        if ($("#trans_date").val() == "") {
            $(".trans_date").html("Date is required.");
            isValid = 0;
        }
        if ($("#req_qty").val() == "") {
            $(".req_qty").html("Qty is required.");
            isValid = 0;
        }

        if (isValid) {
            var trans_number = $("#trans_number").val();
            var trans_no = $("#trans_no").val();
            var trans_date = $("#trans_date").val();
            var item_id = $("#item_id").val();
            var item_name = $("#item_name").val();
			var prc_id = $("#prc_id").val();
            var prc_number = $("#prc_number").val();
            var req_qty = $("#req_qty").val();
            var urgency = $("#urgency").val();
            var remark = $("#remark").val();

            //Get the reference of the Table's TBODY element.
            $("#addReqTbl").dataTable().fnDestroy();
            var tblName = "addReqTbl";
            var tBody = $("#" + tblName + " > TBODY")[0];

            //Add Row.
            row = tBody.insertRow(-1);

            //Add index cell
            var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
            var cell = $(row.insertCell(-1));
            cell.html(countRow);

            cell = $(row.insertCell(-1));
            cell.html(trans_date + '<input type="hidden" name="trans_date[]" value="' + trans_date + '">');

            cell = $(row.insertCell(-1));
            cell.html(item_name + '<input type="hidden" name="item_id[]" value="' + item_id + '">');

            cell = $(row.insertCell(-1)); 
			cell.html(prc_number + '<input type="hidden" name="prc_id[]" value="' + prc_id + '">');

            cell = $(row.insertCell(-1));
            cell.html(req_qty + '<input type="hidden" name="req_qty[]" value="' + req_qty + '">');

            var urgencyVal;
            if(urgency != ''){
                if(urgency == 1)
                    urgencyVal = 'Low'
                if(urgency == 2)
                    urgencyVal = 'Medium'
                if(urgency == 3)
                    urgencyVal = 'High'
            }
            cell = $(row.insertCell(-1));
            cell.html(urgencyVal + '<input type="hidden" name="urgency[]" value="' + urgency + '">');

            cell = $(row.insertCell(-1));
            cell.html(remark + '<input type="hidden" name="remark[]" value="' + remark + '">');

            //Add Button cell.
            cell = $(row.insertCell(-1));
            var btnRemove = $('<button><i class="mdi mdi-trash-can-outline"></i></button>');
            btnRemove.attr("type", "button");
            btnRemove.attr("onclick", "Remove(this);");
            btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light btn-sm");
            cell.append(btnRemove);
            cell.attr("class", "text-center");
            
            $("#trans_date").val("");
            $("#item_id").val("");
            $("#item_id").val("");
            $("#req_qty").val("");
            $("#urgency").val("");
            $("#remark").val("");
            initSelect2();
        }
    };

    function Remove(button) {
        //Determine the reference of the Row using the Button.
        var row = $(button).closest("TR");
        var table = $("#addReqTbl")[0];
        table.deleteRow(row[0].rowIndex);
        $('#addReqTbl tbody tr td:nth-child(1)').each(function(idx, ele) {
            ele.textContent = idx + 1;
        });
        var countTR = $('#addReqTbl tbody tr:last').index() + 1;
        if(countTR == 0){
            $("#tempItem").html('<tr id="noData"><td colspan="8" align="center">No data available in table</td></tr>');
        }	
    };
</script>