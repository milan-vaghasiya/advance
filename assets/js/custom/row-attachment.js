$(document).ready(function(){
    $(document).on('change','.file-upload',function(e){
        var row_id = $(this).data('id');
        var file = e.target.files[0];
        if (file) {
            getFilePath(file,e,row_id);
			$('#view-attechment-'+row_id).removeClass('hidden');
            $('#upload-container-'+row_id).addClass('hidden');
			$('#remove-attechment-'+row_id).removeClass('hidden');
			$('#attachment-status-'+row_id).val(1);
        }else{
            $("#attachment_"+row_id).val("");
            $('#view-attechment-'+row_id).attr('src','');
            $('#view-attechment-'+row_id).addClass('hidden');
            $('#upload-container-'+row_id).removeClass('hidden');
            $('#remove-attechment-'+row_id).addClass('hidden');
            $('#attachment-status-'+row_id).val(0);
        }
    });

    $(document).on('click','.remove-attechment',function(){
		var row_id = $(this).data('row_id');
        $("#attachment_"+row_id).val("");
        $('#view-attechment-'+row_id).attr('src','');
        $('#view-attechment-'+row_id).addClass('hidden');
        $('#upload-container-'+row_id).removeClass('hidden');
        $('#remove-attechment-'+row_id).addClass('hidden');
        $('#download-attechment-'+row_id).addClass('hidden');
        $('#attachment-status-'+row_id).val(2);
	});
});

function getFilePath(file,e="",row_id = ""){
    if(file){
        // Check if the file extension is an image
        var extension = (e == "")?file.split('.').pop().toLowerCase():file.name.split('.').pop().toLowerCase();
        
        var imgExtension = ["jpg","jpeg","png","gif"];

        var fileExtension = {
            "pdf":"assets/uploads/defualt/pdf_image.png",
            "xlsx":"assets/uploads/defualt/excel_image.png",
            "docx":"assets/uploads/defualt/wordfile_image.png",
            "txt":"assets/uploads/defualt/textfile_image.png"
        };

        if ($.inArray(extension, imgExtension) !== -1) {
            if(e){
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#view-attechment-'+row_id).attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }else{
                return file;
            }       
        }else if(fileExtension.hasOwnProperty(extension)){
            // Handle other file types (e.g., PDF, Excel, Word, Text)
            if(e){
                $('#view-attechment-'+row_id).attr('src', base_url + fileExtension[extension]);
            }else{
                return base_url + fileExtension[extension];
            }           
        }else{
            // Handle unknown file types
            if(e){
                $('#view-attechment-'+row_id).attr('src', base_url + 'assets/uploads/defualt/otherfile_image.png');
            }else{
                return base_url + 'assets/uploads/defualt/otherfile_image.png';
            }              
        }
    }else{
        return "";
    }    
}

function attachmentInput(postData = ""){
    var ind = postData.index || 0;
    var inputName = postData.inputName || "";
    var inputStatus = postData.inputStatus || "";
    var file = postData.file || "";
    var fileName = postData.fileName || "";

    var html = '';

    //file upload button 
    html += '<div id="upload-container-'+ind+'" class="upload-container '+((file != '')?"hidden":"")+'"><label for="attachment_'+ind+'" class="btn btn-outline-info  waves-effect waves-light"><i class="fa fa-paperclip"></i></label><input id="attachment_'+ind+'" data-id="'+ind+'" class="file-upload" name="'+inputName+'" type="file"></div>';

    //file status [0 = no file selected, 1 = new file selected, 2 = old file removed]
    html += '<input id="attachment-status-'+ind+'" data-id="'+ind+'" class="file-upload" name="'+inputStatus+'" type="hidden" value="0">';
    
    //view image
    html += '<img class="item-attechment '+((file == '')?"hidden":"")+'" id="view-attechment-'+ind+'" src="'+getFilePath(file)+'" alt="Selected File" title="'+fileName+'">';  

    // file download and remove button
    html += '<div class="text-center">'; 
        
        //download button
        html += '<a href="'+file+'" id="download-attechment-'+ind+'" class="btn btn-sm btn-outline-primary m-t-5 '+((file == '')?"hidden":"")+'" title="Download File" download><i class="fa fa-arrow-down" ></i></a>'; 
        
        //remove button
        html += '<button type="button" class="btn btn-sm btn-outline-danger remove-attechment m-t-5 '+((file == '')?"hidden":"")+'" id="remove-attechment-'+ind+'"  title="Remove File" data-row_id="'+ind+'"><i class="ti-trash"></i></button>';

    html += '</div>';
    return html;
}