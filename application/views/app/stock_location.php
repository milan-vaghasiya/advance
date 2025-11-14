<form data-res_function="getIssueResponse">
    <input type="hidden" name="id" value="<?=$id?>">
    <input type="hidden" name="location_id" id="location_id" value="">
    <a href="#" class="btn btn-sm btn-primary btn-save float-end"  id="openreader-single2" data-qrr-target="#single2" data-qrr-audio-feedback="true"  aria-label="QR outline">
        Add Location
    </a>
    <div class="mr-2" id="locationData"></div>
    <div class="error location_id"></div>

</form>

<script>
    $(".select2").select2();
	$(function(){
		// overriding path of JS script and audio 
		$.qrCodeReader.jsQRpath = "<?=base_url()?>/assets/qrcode/dist/js/jsQR/jsQR.min.js";
		$.qrCodeReader.beepPath = "<?=base_url()?>/assets/qrcode/dist/audio/beep.mp3";
		// read or follow qrcode depending on the content of the target input
		$("#openreader-single2").qrCodeReader({callback: function(code) {
		if (code) {
            //Check Aready scan or not
            var qrCodes = $("input[name='qrCode[]']").map(function(){return $(this).val();}).get();
            console.log(jQuery.inArray(code, qrCodes));
            $.ajax({
                url: base_url + controller +'/getLocationData',
                data: {code:code},
                type: "POST",
                dataType:"json",
                success:function(response){
                    if(response.status==0){
                        Swal.fire({ icon: 'error', title: response.message });
                    }else{
                        $("#location_id").val(response.location_id);
                        $("#locationData").html(response.html);
                    }
                }
            });
		}  
		}}).off("click.qrCodeReader").on("click", function(){
            var qrcode = $("#single2").val();
            if (qrcode) {
                window.location.href = qrcode;
            }else{
                $.qrCodeReader.instance.open.call(this);
            }
		});
	});

    $(document).ready(function(){
        $(document).on("change input",".batchQty", function(){
            var qty = parseFloat($(this).val());
            var qrCode = $(this).data('qr_code');
            var stock_qty = parseFloat($(this).data('stock_qty'));
            if(qty > stock_qty){
                $(this).val("");
                $(".batch_qty_"+qrCode).html('Stock Not Available');
            }
        
        });
    });
    function Remove(button) {
        //Determine the reference of the Row using the Button.
        var row = $(button).closest("TR");
        var table = $("#stockData")[0];
        table.deleteRow(row[0].rowIndex);
        var qrCodes = $("input[name='qrCode[]']").map(function(){return $(this).val();}).get();
        if(qrCodes.length == 0){
            $("#item_id").val();
            $("#item_name").html("");
        }
    };
    function getIssueResponse(data){
        if(data.status==1){
            Swal.fire({
                title: "Success",
                text: data.message,
                icon: "success",
                showCancelButton: false,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok!"
            }).then((result) => {
               window.location.reload() ;
            });
            
        }else{
            if(typeof data.message === "object"){
                $(".error").html("");
                $.each( data.message, function( key, value ) {$("."+key).html(value);});
            }else{
                Swal.fire({ icon: 'error', title: data.message });
            }			
        }
    }
</script>