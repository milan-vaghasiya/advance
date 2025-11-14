<style>
    .bg-red {
        background-color: rgba(239, 77, 86, 0.2) !important;
    }
    .bg-success {
        background-color: rgba(113, 218, 201, 0.5) !important;
    }
</style>
<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" id="id" value="">
            <input type="hidden" id="party_category" name="party_category" value="<?= $party_category?>">
            <div class="col-md-6 form-group">
                <label for="">Select File</label>
                <div class="input-group">
                    <a href="<?=base_url("assets/uploads/defualt/party_excel.xlsx")?>" class="btn btn-outline-info" title="Download Example File" download><i class="fa fa-download"></i></a>
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
        <p class="font-bold">
            <span class="float-left text-warning">You can enter upto 50 records only.</span>
            <span class="float-end text-primary">Can not save duplicate party. Duplicate parties are shown with red color.</span><br>
        </p>
        <div class="table-container" style="height: 40vh; overflow-y: auto; overflow-x: hidden;">
            <table id="partyDetails" class="table jpExcelTable">
                <thead class="thead-info" style="position: sticky; top: 0; z-index: 1;">
                    <tr class="text-center">
                        <th style="width:30px;">#</th>
                        <th>Company Name</th>
                        <!-- <th>Party Code</th> -->
                        <th>Sales Executive</th>
                        <th>Contact Person</th>
                        <th>Mobile No.</th>
                        <th>Whatsapp No.</th>
                        <th>Party Email</th>
                        <th>Credit Days</th>
                        <th>Registration Type</th>
                        <th>Party GSTIN</th>
                        <th>Party PAN</th>
                        <th>Currency</th>
                        <th>Distance (Km)</th>
                        <th>Country</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Pincode</th>
                        <th>Address</th>
                        <th>Delivery Address</th>
                        <th>Action</th>
                    </tr>                    
                </thead>
                <tbody>
                    <tr id="noData">
                        <td colspan="21" class="text-center">No data available in table</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</form>

<script src="<?php echo base_url(); ?>assets/js/xlsx.full.min.js?v=<?=time()?>"></script>
<script>
$(document).ready(function() {
    $(document).on("click",'#readButton',function(e) {
        e.stopImmediatePropagation();e.preventDefault();
        var inputArray = [
            "party_name",
            // "party_code",
            "sales_executive",
            "contact_person",
            "party_mobile",
            "whatsapp_no",
            "party_email",
            "credit_days",
            "registration_type",
            "gstin",
            "pan_no",
            "currency",
            "distance",
            "country_name",
            "state_name",
            "city_name",
            "party_pincode",
            "party_address",
            "delivery_address"
        ];

        var fileInput = document.getElementById('excelFile');
        var file = fileInput.files[0];
        $(".excel_file").html("");
        
        if(file){
            var columnCount = $('table#partyDetails thead tr').first().children().length;
            $("table#partyDetails > TBODY").html('<tr><td id="noData" colspan="'+columnCount+'" class="text-center">Loading...</td></tr>'); 

            var reader = new FileReader();
            reader.onload = function(e) {
                var data = new Uint8Array(e.target.result);
                var workbook = XLSX.read(data, { type: 'array' });

                var sheetName = workbook.SheetNames[0]; // Assuming the first sheet
                var worksheet = workbook.Sheets[sheetName];

                var jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1,raw:false,dateNF:'dd-mm-yyyy' });

                //Remove blank line.
                $('table#partyDetails > TBODY').html("");              

                var postData = [];
                if(jsonData.length > 51){
                    $(".excel_file").html("There are more than 50 records in your file.");
                }else{
                    $(".excel_file").html("");
                    $.each(jsonData,function(ind,row){ 
                        postData = [];

                        if(ind > 0){
                            var party_id = "";
                            if(row[0]){
                                $.each(inputArray,function(key,column){
                                    postData[column] = row[key]  || "";
                                });

                                // var party_codes = $("input[name='party_code[]']").map(function(){return $(this).val();}).get();
                                // || postData.party_code == "" || postData.party_code == null$.inArray(postData.party_code,party_codes) >= 0 ||
                                var party_names = $("input[name='party_name[]']").map(function(){return $(this).val();}).get();
                                var party_mobiles = $("input[name='party_mobile[]']").map(function(){return $(this).val();}).get();

                                if($.inArray(postData.party_name,party_names) >= 0 || $.inArray(postData.party_mobile,party_mobiles) >= 0  || postData.party_name == "" || postData.party_name == null || postData.party_mobile == "" || postData.party_mobile == null){
                                    party_id = -1;
                                }else{
                                    $.ajax({
                                        url : base_url + 'parties/checkPartyDuplicate',
                                        type : 'post',
                                        data : { party_name : postData['party_name'], party_mobile : postData['party_mobile']},
                                        global:false,
                                        async:false,
                                        dataType:'json'
                                    }).done(function(res){
                                        party_id = res.party_id;
                                    }); 
                                }
                                
                                postData['party_id'] = party_id;
                                postData = Object.assign({}, postData);
                                AddRow(postData);
                            } 
                        } 
                    });
                }
            };
            reader.readAsArrayBuffer(file); 
        }else{
            $(".excel_file").html("Please Select File.");
        }         
    });
});

function AddRow(data){

    var tblName = "partyDetails";

    //Remove blank line.
	$('table#'+tblName+' tr#noData').remove();

    //Get the reference of the Table's TBODY element.
	var tBody = $("#" + tblName + " > TBODY")[0];    
    var ind = -1 ;
	row = tBody.insertRow(ind);
    $(row).attr('class',((data.party_id == "")?"bg-success":"bg-red"));

    var disabled = ((data.party_id == "")?false:true);

    //Add index cell
	var countRow = ($('#' + tblName + ' tbody tr:last').index() + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:30px;");
    $(row).attr('id',countRow);

    var mainIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][id]", value: $("#id").val() ,disabled:disabled});
    var categoryInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][party_category]",  value: $("#party_category").val() ,disabled:disabled});
    var partyTypeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][party_type]",  value: 1 ,disabled:disabled});
    var partyNameInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][party_name]",  value: data.party_name ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.party_name);
    cell.append(partyNameInput);
    cell.append(mainIdInput);
    cell.append(categoryInput);
    cell.append(partyTypeInput);

    // var partyCodeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][party_code]",  value: data.party_code,disabled:disabled });
    // cell = $(row.insertCell(-1));
    // cell.html(data.party_code);
    // cell.append(partyCodeInput);

    var salesExecutiveInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][sales_executive]",  value: data.sales_executive,disabled:disabled });
    cell = $(row.insertCell(-1));
    cell.html(data.sales_executive);
    cell.append(salesExecutiveInput);

    var cPersonInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][contact_person]",  value: data.contact_person ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.contact_person);
    cell.append(cPersonInput);

    var cPhoneInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][party_mobile]",  value: data.party_mobile,disabled:disabled });
    cell = $(row.insertCell(-1));
    cell.html(data.party_mobile);
    cell.append(cPhoneInput);
    
    var wpInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][whatsapp_no]",  value: data.whatsapp_no,disabled:disabled });
    cell = $(row.insertCell(-1));
    cell.html(data.whatsapp_no);
    cell.append(wpInput);

    var pEmailInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][party_email]",  value: data.party_email ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.party_email);
    cell.append(pEmailInput);

    var creditInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][credit_days]",  value: data.credit_days ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.credit_days);
    cell.append(creditInput);

    var regTypeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][registration_type]",  value: data.registration_type ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.registration_type);
    cell.append(regTypeInput);
  
    var gstInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][gstin]",  value: data.gstin,disabled:disabled });
    cell = $(row.insertCell(-1));
    cell.html(data.gstin);
    cell.append(gstInput);

    var panNoInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][pan_no]",  value: data.pan_no ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.pan_no);
    cell.append(panNoInput);

    var currencyInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][currency]",  value: data.currency,disabled:disabled });
    cell = $(row.insertCell(-1));
    cell.html(data.currency);
    cell.append(currencyInput);

    var distanceInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][distance]",  value: data.distance,disabled:disabled });
    cell = $(row.insertCell(-1));
    cell.html(data.distance);
    cell.append(distanceInput);

    var countryIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][country_name]",  value: data.country_name,disabled:disabled });
    cell = $(row.insertCell(-1));
    cell.html(data.country_name);
    cell.append(countryIdInput);

    var stateInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][state_name]",  value: data.state_name ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.state_name);
    cell.append(stateInput);

    var cityInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][city_name]",  value: data.city_name ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.city_name);
    cell.append(cityInput);

    var pincodeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][party_pincode]",  value: data.party_pincode ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.party_pincode);
    cell.append(pincodeInput);  

    var addressInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][party_address]",  value: data.party_address ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.party_address);
    cell.append(addressInput);  

    var deliveryAddressInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][delivery_address]",  value: data.delivery_address ,disabled:disabled});
    cell = $(row.insertCell(-1));
    cell.html(data.delivery_address);
    cell.append(deliveryAddressInput);

    //Add Button cell.
    cell = $(row.insertCell(-1));
    var btnRemove = $('<button><i class="fas fa-trash"></i></button>');
    btnRemove.attr("type", "button");
    btnRemove.attr("onclick", "Remove(this);");
    btnRemove.attr("class", "btn btn-sm btn-outline-danger waves-effect waves-light");
    cell.append(btnRemove);
    cell.attr("class", "text-center");
    cell.attr("style", "width:30px;");
}

function Remove(button) {
    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#partyDetails")[0];
    table.deleteRow(row[0].rowIndex);
    var countTR = $('#partyDetails tbody tr:last').index() + 1;
    if (countTR == 0) {
        $("#tempItem").html('<tr id="noData"><td colspan="21" align="center">No data available in table</td></tr>');
    }
};
</script>