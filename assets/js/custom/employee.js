$(document).ready(function(){
	
	// Check For Employee is under child act or not
    $(document).on('change','#emp_birthdate',function(){
        var dob = new Date($(this).val());
        var today = new Date();
        var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
        $('#age').html(age+' years old');
        //console.log(dob + " & " + today + " = " + age);
        if (age < 18) {
            $(".emp_birthdate").html("Under Child Labour Act");
        }
        else{$(".emp_birthdate").html("");}        
    });

});
