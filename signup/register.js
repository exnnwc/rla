
$(document.body).ready(function () {
    $(document).on("click", "#register_user", function (event) {
        if (!$("#password1").val() || !$("#password2").val() || !$("#new_username").val()){
            $("#error_div").css("color", "red");
            $("#error_div").html("Please fill out all areas.");
        } else if ($("#new_email").val() && !is_it_an_email($("#new_email").val())){
            $("#error_div").css("color", "red");
            $("#error_div").html("Please input a valid email.");
        }
        if ($("#password1").val() && $("#password2").val() && $("#new_username").val()  
          && do_passwords_match($("#password1").val(), $("#password2").val()) 
          || ($("#new_email").val() && is_it_an_email($("#new_email").val()))){
                    
            doesUsernameAlreadyExist($("#new_username").val(), function (result){
                if (!JSON.parse(result)){
                    registerUser($("#new_username").val(), $("#password1").val(), $("#new_email").val());
                } else if (JSON.parse(result)){
                    //ERROR username already exists
                }
            });
        }
        
    });
    
    $(document).on("keyup", "#password2", function (event) {
        if ($("#password1").val().length == $("#password2").val().length){
            do_passwords_match($("#password1").val(), $("#password2").val());
        }
    });
    $(document).on("keyup", "#new_username", function (event) {
            doesUsernameAlreadyExist($("#new_username").val(), function (result){
                if (JSON.parse(result)){
                    $("#username_error").html("Username already exists!");
                } else if (!JSON.parse(result)){
                    $("#username_error").html("");
                }
            });
    });
});


function do_passwords_match(password1, password2){
        if (password1!=password2){
           $("#password_error").html("Passwords do not match!"); 
            $("#password_error").css("color", "red");
            $("#password_error").css("font-weight", "bold");
            return false;
        } 
        else if (password1==password2){
           $("#password_error").html("Ok!"); 
            $("#password_error").css("color", "green");
            return true;
        }
}

function is_it_an_email(email){
    return !(email.match(".+\@.+\.+.")===null && typeof email.match(".+\@.+\.+.") === "object");
}

