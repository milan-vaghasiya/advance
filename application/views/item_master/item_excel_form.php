<style>
    .bg-red {
        background-color: rgba(239, 77, 86, 0.2) !important;
    }
    .bg-success {
        background-color: rgba(113, 218, 201, 0.5) !important;
    }
</style>
<form>
    <div class="row">
        <input type="hidden" name="item_type" id="item_type" value="<?=$item_type?>">
        
        <div class="col-md-3">
            <a href="<?= base_url($headData->controller . '/createItemMasterExcel/'.$item_type ) ?>" class="btn btn-block btn-info bg-info-dark mr-2" target="_blank">
                <i class="fa fa-download"></i>&nbsp;&nbsp;
                <span class="btn-label">Download Excel</span>
            </a>
        </div>
        <div class="col-md-6">
            <input type="file" name="item_excel" id="item_excel" class="form-control float-left" accept=".xlsx, .xls" />
            <h6 class="col-md-12 msg text-primary text-center mt-1"></h6>
        </div>
        <div class="col-md-3">
            <a href="javascript:void(0);" class="btn btn-block btn-success bg-success-dark ml-2" onclick="readExcel()" type="button">
                <i class="fas fa-file-excel"></i>&nbsp;
                <span class="btn-label">Read</span>
            </a>
        </div>
        <div class="error general_error"></div>
    </div>
    <hr>
    <p class="font-bold">
        <span class="float-left text-warning">You can enter upto 50 records only.</span>
        <span class="float-end text-primary">Can not save duplicate items. Duplicate items are shown with red color.</span><br>
    </p>
    <div class="table-responsive">
        <table class="table jpExcelTable" id="excelTable">
            <thead>
                <?php
                    $html = '<tr class="thead-info text-center">
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Category</th>                
                            <th>UOM</th>
                            <th>HSN Code</th>
                            <th>GST %</th>';

                    if (in_array($item_type, [1,3,7])) {
                        $html .= '<th>Material Grade</th>';
                    }

                    if ($item_type == 1) {
                        $html .= '<th>Weight (Kg)</th>';                    
                    }

                    if (in_array($item_type, [1,2,5,6,9])) {
                        $html .= '<th>Price</th>';                    
                    }

                    if (in_array($item_type,[1,2,7,9])) {
						$html .= '<th>Size</th>';
                    }

                    if (!in_array($item_type,[3,7])) {
                        $html .= '<th>Make/Brand</th>';
                    }

                    if (in_array($item_type,[2,9])) { 
                        $html .= '<th>Serial Number</th>';
                    }

                    if ($item_type == 2) {
                        $html .= '<th>No. of Corner</th>
                                <th>Dia</th>
                                <th>Length (mm)</th>
                                <th>Flute Length (mm)</th>';
                    }

                    if (in_array($item_type, [3,7])) {
                        $html .= '<th>Section (Dia)</th>';
                    }

                    if ($item_type == 6) {
                        $html .=  '<th>Size/Range</th>
                                <th>Permissible Error</th>
                                <th>Calibration Req?</th>
                                <th>Cali. Frequency (Month)</th>
                                <th>Cali.Reminder (Days Before)</th>';
                    }

                    if ($item_type == 5) {
                        $html .=  '<th>Serial Number</th>
								<th>Installed On</th>
                                <th>Pre. Maintenance?</th>
                                <th>Plan Days</th>
                                <th>Specification</th>';
                    }else{
                        $html .=  '<th>Description</th>';
                    }
                            
                    $html .= '<th>Action</th>
                            </tr>';

                    echo $html;
                ?>
            </thead>
            <tbody id="excelTbody">
                <tr>
                    <th colspan="20" class="text-center"> No Data Available. </th>
                </tr>
            </tbody>
        </table>
    </div>
</form>
<script src="<?php echo base_url(); ?>assets/js/xlsx.full.min.js?v=<?=time()?>"></script>
<script>
function readExcel() {   
    <?php if ($item_type == 1) { ?>
        var inputArray = [
            'item_code',
            'item_name',
            'category_name',                
            'uom',
            'hsn_code',
            'gst_per',
            'material_grade',
            'wt_pcs',
            'price',
            'size',
            'make_brand',
            'description'
        ];
    <?php } ?>

    <?php if ($item_type == 2) { ?>
        var inputArray = [
            'item_code',
            'item_name',
            'category_name',                
            'uom',
            'hsn_code',
            'gst_per',
            'price',
            'size',
            'make_brand',
            'part_no',
            'no_corner',
            'dia',
            'length_mm',
            'flute_length',
            'description'
        ];
    <?php } ?>

    <?php if ($item_type == 3) { ?>
        var inputArray = [
            'item_code',
            'item_name',
            'category_name',                
            'uom',
            'hsn_code',
            'gst_per',
            'material_grade',
            'dia',
            'description'
        ];
    <?php } ?>

    <?php if ($item_type == 5) { ?>
        var inputArray = [
            'item_code',
            'item_name',
            'category_name',                
            'uom',
            'hsn_code',
            'gst_per',
            'price',
            'make_brand',
            'part_no',
            'installation_year',
            'prev_maint_req',
            'plan_days',
            'description'
        ];
    <?php } ?>

    <?php if ($item_type == 6) { ?>
        var inputArray = [
            'item_code',
            'item_name',
            'category_name',                
            'uom',
            'hsn_code',
            'gst_per',
            'price',
            'make_brand',
            'size',
            'permissible_error',
            'cal_required',
            'cal_freq',
            'cal_reminder',
            'description'
        ];
    <?php } ?>

    <?php if ($item_type == 7) { ?>
        var inputArray = [
            'item_code',
            'item_name',
            'category_name',                
            'uom',
            'hsn_code',
            'gst_per',
            'material_grade',
            'size',
            'dia',
            'description'
        ];
    <?php } ?>

    <?php if ($item_type == 9) { ?>
        var inputArray = [
            'item_code',
            'item_name',
            'category_name',                
            'uom',
            'hsn_code',
            'gst_per',
            'price',
            'size',
            'make_brand',
            'part_no',
            'description'
        ];
    <?php } ?>

    var fileInput = document.getElementById('item_excel');
    var file = fileInput.files[0];
    $(".general_error").html("");
    
    if (file) {
        var columnCount = $('table#excelTable thead tr').first().children().length;
        $("table#excelTable > TBODY").html('<tr><td id="noData" colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');

        var reader = new FileReader();
        reader.onload = function(e) {
            var data = new Uint8Array(e.target.result);
            var workbook = XLSX.read(data, { type: 'array' });

            var sheetName = workbook.SheetNames[0]; // Assuming the first sheet
            var worksheet = workbook.Sheets[sheetName];

            var jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1,raw:false,dateNF:'dd-mm-yyyy' });

            //Remove blank line.
            $('table#excelTable > TBODY').html("");
            
            var postData = [];
            if(jsonData.length > 51){
                $(".general_error").html("There are more than 50 records in your file.");
            }else{
                $(".general_error").html("");
                $.each(jsonData,function(ind,row){ 
                    postData = [];
                    
                    if(ind > 0){     
                        var item_id = "";             
                        $.each(inputArray,function(key,column){  
                            postData[column] = row[key]  || "";
                        });

                        var item_codes = $("input[name='item_code[]']").map(function(){return $(this).val();}).get();
                        var item_names = $("input[name='item_name[]']").map(function(){return $(this).val();}).get();

                        if($.inArray(postData.item_code,item_codes) >= 0 && $.inArray(postData.item_name,item_names) >= 0 || postData.item_code == "" || postData.item_code == null || postData.item_name == "" || postData.item_name == null){
                            item_id = -1;
                        }else{
                            $.ajax({
                                url : base_url + 'items/checkDuplicateItems',
                                type : 'post',
                                data : { item_name : postData['item_name'], item_code : postData['item_code'], item_type : $("#item_type").val()},
                                global:false,
                                async:false,
                                dataType:'json'
                            }).done(function(res){
                                item_id = res.item_id;
                            });
                        }
                        
                        postData['item_id'] = item_id;
                        postData = Object.assign({}, postData);

                        AddRow(postData);
                    } 
                });
            }
        };
        reader.readAsArrayBuffer(file);    
    } else {
        $(".general_error").html("Please Select File.");
    } 
}

function AddRow(data) {
    var item_type = $('#item_type').val();

    var tblName = "excelTable";

    //Remove blank line.
	$('table#'+tblName+' tr#noData').remove();

    //Get the reference of the Table's TBODY element.
	var tBody = $("#" + tblName + " > TBODY")[0];    
    var ind = -1 ;
	row = tBody.insertRow(ind);
    $(row).attr('class',((data.item_id == "")?"bg-success":"bg-red"));
    
    var disabled = ((data.item_id == "")?false:true);

    //Add index cell
	var countRow = ($('#' + tblName + ' tbody tr:last').index() + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:30px;");
    $(row).attr('id',countRow);
	
    //Item Code
	var cell = $(row.insertCell(-1));
	cell.html(data.item_code);
    var itemCodeIp = $("<input/>", { type: "hidden", name: "item_code[]", value: data.item_code ,disabled:disabled});
    cell.append(itemCodeIp);
    cell.append('<div class="error item_code'+countRow+'"></div>');

    //Item Name
	var cell = $(row.insertCell(-1));
	cell.html(data.item_name);
    var itemNmIp = $("<input/>", { type: "hidden", name: "item_name[]", value: data.item_name ,disabled:disabled});
    cell.append(itemNmIp);
    cell.append('<div class="error item_name'+countRow+'"></div>');

    //Category
	var cell = $(row.insertCell(-1));
	cell.html(data.category_name);
    var catIp = $("<input/>", { type: "hidden", name: "category_name[]", value: data.category_name ,disabled:disabled});
    cell.append(catIp);
    
    //UOM
	var cell = $(row.insertCell(-1));
	cell.html(data.uom);
    var unitIp = $("<input/>", { type: "hidden", name: "uom[]", value: data.uom ,disabled:disabled});
    cell.append(unitIp);

    //HSN
	var cell = $(row.insertCell(-1));
	cell.html(data.hsn_code);
    var hsnIp = $("<input/>", { type: "hidden", name: "hsn_code[]", value: data.hsn_code ,disabled:disabled});
    cell.append(hsnIp);

    //GST
    var cell = $(row.insertCell(-1));
	cell.html(data.gst_per);
    var gstIp = $("<input/>", { type: "hidden", name: "gst_per[]", value: data.gst_per ,disabled:disabled});
    cell.append(gstIp);

    if ($.inArray(item_type, ['1','3','7']) >= 0) {
        //Material Grade
        var cell = $(row.insertCell(-1));
        cell.html(data.material_grade);
        var mtGrdIp = $("<input/>", { type: "hidden", name: "material_grade[]", value: data.material_grade ,disabled:disabled});
        cell.append(mtGrdIp);
    }

    if (item_type == 1) {
        //Weight (Kg)
        var cell = $(row.insertCell(-1));
        cell.html(data.wt_pcs);
        var wtIp = $("<input/>", { type: "hidden", name: "wt_pcs[]", value: data.wt_pcs ,disabled:disabled});
        cell.append(wtIp);
    }

    if ($.inArray(item_type, ['1','2','5','6','9']) >= 0) {
        //Price
        var cell = $(row.insertCell(-1));
        cell.html(data.price);
        var priceIp = $("<input/>", { type: "hidden", name: "price[]", value: data.price ,disabled:disabled});
        cell.append(priceIp);
    }

    if ($.inArray(item_type, ['1','2','7','9']) >= 0) {
        //Size
        var cell = $(row.insertCell(-1));
        cell.html(data.size);
        var sizeIp = $("<input/>", { type: "hidden", name: "size[]", value: data.size ,disabled:disabled});
        cell.append(sizeIp);
    }

    if ($.inArray(item_type, ['1','2','5','6','9']) >= 0) {
        //Make Brand
        var cell = $(row.insertCell(-1));
        cell.html(data.make_brand);
        var makeBrandIp = $("<input/>", { type: "hidden", name: "make_brand[]", value: data.make_brand ,disabled:disabled});
        cell.append(makeBrandIp);
    }

    if ($.inArray(item_type, ['2','9']) >= 0) {
        //Serial Number
		var cell = $(row.insertCell(-1));
		cell.html(data.part_no);
		var srNoIp = $("<input/>", { type: "hidden", name: "part_no[]", value: data.part_no ,disabled:disabled});
		cell.append(srNoIp);
    }

    if (item_type == 2) {
        //No Of Corner
        var cell = $(row.insertCell(-1));
        cell.html(data.no_corner);
        var noCornerlIp = $("<input/>", { type: "hidden", name: "no_corner[]", value: data.no_corner ,disabled:disabled});
        cell.append(noCornerlIp);

        //Dia
        var cell = $(row.insertCell(-1));
        cell.html(data.dia);
        var diaIp = $("<input/>", { type: "hidden", name: "dia[]", value: data.dia ,disabled:disabled});
        cell.append(diaIp);

        //Length
        var cell = $(row.insertCell(-1));
        cell.html(data.length_mm);
        var lengthIp = $("<input/>", { type: "hidden", name: "length_mm[]", value: data.length_mm ,disabled:disabled});
        cell.append(lengthIp);

        //Flute Length
        var cell = $(row.insertCell(-1));
        cell.html(data.flute_length);
        var fluteLengthIp = $("<input/>", { type: "hidden", name: "flute_length[]", value: data.flute_length ,disabled:disabled});
        cell.append(fluteLengthIp);
    }

    if ($.inArray(item_type, ['3','7']) >= 0) {
        //Section (Dia)
        var cell = $(row.insertCell(-1));
        cell.html(data.dia);
        var diaIp = $("<input/>", { type: "hidden", name: "dia[]", value: data.dia ,disabled:disabled});
        cell.append(diaIp);
    }

    if (item_type == 6) {
        //Size/Range
        var cell = $(row.insertCell(-1));
        cell.html(data.size);
        var sizeIp = $("<input/>", { type: "hidden", name: "size[]", value: data.size ,disabled:disabled});
        cell.append(sizeIp);

        //Permissible Error
        var cell = $(row.insertCell(-1));
        cell.html(data.permissible_error);
        var perErrorIp = $("<input/>", { type: "hidden", name: "permissible_error[]", value: data.permissible_error ,disabled:disabled});
        cell.append(perErrorIp);

        //Calibration Req?
        var cell = $(row.insertCell(-1));
        cell.html(data.cal_required);
        var calReqIp = $("<input/>", { type: "hidden", name: "cal_required[]", value: data.cal_required ,disabled:disabled});
        cell.append(calReqIp);

        //Cali. Frequency (Month)
        var cell = $(row.insertCell(-1));
        cell.html(data.cal_freq);
        var calFreqIp = $("<input/>", { type: "hidden", name: "cal_freq[]", value: data.cal_freq ,disabled:disabled});
        cell.append(calFreqIp);

        //Cali.Reminder (Days Before)
        var cell = $(row.insertCell(-1));
        cell.html(data.cal_reminder);
        var calReminderIp = $("<input/>", { type: "hidden", name: "cal_reminder[]", value: data.cal_reminder ,disabled:disabled});
        cell.append(calReminderIp);
    }

    if (item_type == 5) {
		//Serial Number
		var cell = $(row.insertCell(-1));
		cell.html(data.part_no);
		var srNoIp = $("<input/>", { type: "hidden", name: "part_no[]", value: data.part_no ,disabled:disabled});
		cell.append(srNoIp);
		
        //Installed On
        var cell = $(row.insertCell(-1));
        cell.html(data.installation_year);
        var insYearIp = $("<input/>", { type: "hidden", name: "installation_year[]", value: data.installation_year ,disabled:disabled});
        cell.append(insYearIp);

        //Pre. Maintenance?
        var cell = $(row.insertCell(-1));
        cell.html(data.prev_maint_req);
        var prevMaintIp = $("<input/>", { type: "hidden", name: "prev_maint_req[]", value: data.prev_maint_req ,disabled:disabled});
        cell.append(prevMaintIp);
    
		//Plan Days
        var cell = $(row.insertCell(-1));
        cell.html(data.plan_days);
        var planDaysIp = $("<input/>", { type: "hidden", name: "plan_days[]", value: data.plan_days ,disabled:disabled});
        cell.append(planDaysIp);
	}
    
    //Description
    var cell = $(row.insertCell(-1));
	cell.html(data.description);
    var descriptionIp = $("<input/>", { type: "hidden", name: "description[]", value: data.description ,disabled:disabled});
    cell.append(descriptionIp);

    //Add Button cell.
    cell = $(row.insertCell(-1));
    var btnRemove = $('<button><i class="fas fa-trash"></i></button>');
    btnRemove.attr("type", "button");
    btnRemove.attr("onclick", "Remove(this);");
    btnRemove.attr("class", "btn btn-sm btn-outline-danger waves-effect waves-light");
    cell.append(btnRemove);
    cell.attr("class", "text-center");
    cell.attr("style", "width:30px;");
};

function Remove(button) {
    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#excelTable")[0];
    table.deleteRow(row[0].rowIndex);
    var countTR = $('#excelTable tbody tr:last').index() + 1;
    if (countTR == 0) {
        $("#tempItem").html('<tr id="noData"><td colspan="20" align="center">No data available in table</td></tr>');
    }
};
</script>