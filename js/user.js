
function doesUsernameAlreadyExist(username, callback){
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:{function_to_be_called:"does_username_already_exist", username:username}
    })
        .done (function (result){
            callback(result);
        });
}
function login(login, password){
    if (!login || !password){
        //BAD username and password not filled out
        return;
    }
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:{function_to_be_called:"login", login:login, password:password}
    })
        .done(function (result){
            console.log(result);
            data=JSON.parse(result);
            loginSuccessful=data[0];
            loginSuccessful
              ? window.location.reload()
              : $("#login_status").css("color", "red");
            $("#login_status").html(data[1]);
        });

}
function logOut(){
    $.ajax({
        method:"POST",
        url:"/rla/php/logout.php"
    })
        .done(function(result){
            window.location.reload();
        });
}
function registerUser(username, password, email){
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:{function_to_be_called:"register_user", username:username, password:password, email:email}
    })
        .done(function(result){
            console.log(result);        
            data=(JSON.parse(result));
            successfulRegistration = data[0];
            successfulRegistration
              ? $("#error_div").css("color", "green")
              : $("#error_div").css("color", "red");
            $("#error_div").html(data[1]);
        });
}
