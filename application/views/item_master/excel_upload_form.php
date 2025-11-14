<form>
    <div class="col-md-12">
        <!-- Excel Config Section Start -->
        <!-- <div class="row form-group">
            <div class="col-md-2">
                <button type="button" class="btn btn-secondary btn-block" title="Click Me" data-bs-toggle="collapse" href="#import_excel_section" role="button" aria-expanded="false" aria-controls="import_excel"> Excel Config.</button>
            </div>
            <div class="col-md-10">
                <hr>
            </div>
        </div> -->

        <!-- <section class="collapse multi-collapse" id="import_excel_section"> -->
            <div class="row" id="input_excel_column">
                <div class="table-responsive">
                    <table class="table jpExcelTable">
                        <thead class="thead-info">
                            <tr class="text-center">
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Item Category</th>
                                <th>Hsn Code</th>
                                <th>Unit</th>
                                <th>Gst Per.</th>
                                <th>Price(Exc.)</th>
                                <th>MRP(Inc.)</th>
                                <th>Min Stock Qty</th>
                                <th>Packing Standard</th>
                                <th>Product Description</th>
                                <th>Start Row</th>
                            </tr>                    
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" id="item_code_column" class="form-control text-center" value="A"></td>
                                <td><input type="text" id="item_name_column" class="form-control text-center" value="B"></td>
                                <td><input type="text" id="category_id_column" class="form-control text-center" value="C"></td>
                                <td><input type="text" id="hsn_code_column" class="form-control text-center" value="D"></td>
                                <td><input type="text" id="unit_id_column" class="form-control text-center" value="E"></td>
                                <td><input type="text" id="gst_per_column" class="form-control text-center" value="F"></td>
                                <td><input type="text" id="price_column" class="form-control text-center" value="G"></td>
                                <td><input type="text" id="mrp_column" class="form-control text-center" value="H"></td>
                                <td><input type="text" id="min_qty_column" class="form-control text-center" value="I"></td>
                                <td><input type="text" id="packing_standard_column" class="form-control text-center" value="J"></td>
                                <td><input type="text" id="description_column" class="form-control text-center" value="K"></td>
                                <td><input type="text" id="start_row" class="form-control text-center numericOnly" value="2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <!-- </section> -->
       
        <!-- Excel Config Section End -->
        <hr>

        <div class="row">
            <input type="hidden" id="id" value="">
            <div class="col-md-6 form-group">
                <label for="">Select File</label>
                <div class="input-group">
                    <a href="<?=base_url("assets/uploads/defualt/product_excel.xlsx")?>" class="btn btn-outline-info" title="Download Example File" download><i class="fa fa-download"></i></a>
                    <div class="input-group-append">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input form-control" id="excelFile" accept=".xlsx, .xls">
                        </div>
                    </div>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" id="readButton" type="button">Read Excel</button>
                    </div>
                </div>
                <div class="error excel_file"></div>
            </div>
        </div>
    </div>
    
    <hr>

    <div class="col-md-12">
        <div class="error itemData"></div>
        <p class="text-primary font-bold text-right">Can not save duplicate item. duplicate items are shown with red color.</p>
        <div class="table-responsive">
            <table id="productDetails" class="table jpExcelTable">
                <thead class="thead-info">
                    <tr class="text-center">
                        <th>#</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Item Category</th>
                        <th>Hsn Code</th>
                        <th>Unit</th>
                        <th>Gst Per.</th>
                        <th>Price(Exc.)</th>
                        <th>MRP(Inc.)</th>
                        <th>Min Stock Qty</th>
                        <th>Packing Standard</th>
                        <th>Product Description</th>
                    </tr>                    
                </thead>
                <tbody>
                    <tr id="noData">
                        <td colspan="12" class="text-center">No data available in table</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</form>

<script src="<?php echo base_url(); ?>assets/js/xlsx.full.min.js?v=<?=time()?>"></script>
<script>
var clickedTr = 0;
$(document).ready(function() {
    $(document).on("click",'#readButton',function() {
        var inputArray = [
            "item_code",
            "item_name",
            "category_id",
            "hsn_code",
            "unit_id",
            "gst_per",
            "price",
            "mrp",
            "min_qty",
            "packing_standard",
            "description"
        ];
        const alphaVal = (s) => s.toLowerCase().charCodeAt(0) - 97 + 1;

        var start_row = $("#input_excel_column #start_row").val();

        $("#input_excel_column .error").html("");

        $.each(inputArray,function(key,column){
            var alpha_val = $("#"+column+"_column").val();
            var input_val = alphaVal(alpha_val);

            if(input_val == ""){ $("#input_excel_column ."+column+"_column").html("Please input column no."); }

            if(input_val == 0){ $("#input_excel_column ."+column+"_column").html("Please input column no."); }
        });


        if(start_row == ""){ $("#input_excel_column .start_row").html("Please input row no."); }
        if(start_row < 2){ $("#input_excel_column .start_row").html("Please input minimum row no. 2"); }

        var fileInput = document.getElementById('excelFile');
        var file = fileInput.files[0];
        $(".excel_file").html("");
        
        if(file){
            var errorCount = $('#input_excel_column .error:not(:empty)').length;

            if(errorCount == 0){
                var columnCount = $('table#productDetails thead tr').first().children().length;
                $("table#productDetails > TBODY").html('<tr><td id="noData" colspan="'+columnCount+'" class="text-center">Loading...</td></tr>'); 

                setTimeout(function(){
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var data = new Uint8Array(e.target.result);
                        var workbook = XLSX.read(data, { type: 'array' });

                        var sheetName = workbook.SheetNames[0]; // Assuming the first sheet
                        var worksheet = workbook.Sheets[sheetName];

                        var jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                        var fileData = [];
                        // Process the data or display it in the table

                        //Remove blank line.
                        $('table#productDetails > TBODY').html("");              

                        var postData = [];
                        $.each(jsonData,function(ind,row){ 
                            postData = [];
                            if(ind >= (start_row - 1)){
                                var item_id = "";
                                if(row[1]){
                                    row[1] = row[1] || -1;

                                    $.each(inputArray,function(key,column){
                                        var alpha_val = $("#"+column+"_column").val();
                                        var input_val = alphaVal(alpha_val);

                                        if(input_val != ""){ 
                                            postData[column] = row[input_val]  || "";
                                        }
                                    });
                                    $.ajax({
                                        url : base_url + 'items/checkItemDuplicate',
                                        type : 'post',
                                        data : { item_name : postData['item_name']},
                                        global:false,
                                        async:false,
                                        dataType:'json'
                                    }).done(function(res){
                                        item_id = res.item_id;
                                    }); 
                                    postData['item_id'] = item_id;
                                    postData = Object.assign({}, postData);
                                    
                                    AddRow(postData);
                                } 
                            } 
                        });
                    };

                    reader.readAsArrayBuffer(file); 
                },200);
            }
        }else{
            $(".excel_file").html("Please Select File.");
        }         
    });
});

function AddRow(data){ 

    var tblName = "productDetails";

    //Remove blank line.
	$('table#'+tblName+' tr#noData').remove();

    //Get the reference of the Table's TBODY element.
	var tBody = $("#" + tblName + " > TBODY")[0];    
    var ind = -1 ;
	row = tBody.insertRow(ind);
    $(row).attr('style',((data.item_id == "")?"background:#8ce1d3;":"background:#F88379;"));

    var disabled = ((data.item_id == "")?false:true); //console.log(disabled);

    //Add index cell
	var countRow = ($('#' + tblName + ' tbody tr:last').index() + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

    $(row).attr('id',countRow);

    var mainIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][id]",  value: $("#id").val()  ,disabled:disabled});
    var itemCodeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][item_code]",  value: data.item_code ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.item_code);
    cell.append(itemCodeInput);
    cell.append(mainIdInput);

    var itemNameInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][item_name]",  value: data.item_name ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.item_name);       
    cell.append(itemNameInput);

    var categoryInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][category_id]",  value: data.category_id ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.category_id);
    cell.append(categoryInput);

    var hsnCodeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][hsn_code]",  value: data.hsn_code ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.hsn_code);
    cell.append(hsnCodeInput);

    var unitInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][unit_id]",  value: data.unit_id ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.unit_id);
    cell.append(unitInput);

    var gstInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][gst_per]",  value: data.gst_per ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.gst_per);
    cell.append(gstInput);

    var priceInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][price]",  value: data.price ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.price);
    cell.append(priceInput);

    var mrpInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][mrp]",  value: data.mrp ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.mrp);
    cell.append(mrpInput);

    var minQtyInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][min_qty]",  value: data.min_qty ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.min_qty);
    cell.append(minQtyInput);

    var packingInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][packing_standard]",  value: data.packing_standard ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.packing_standard);
    cell.append(packingInput);

    var descriptionInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][description]",  value: data.description ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.description);
    cell.append(descriptionInput);
}
</script>