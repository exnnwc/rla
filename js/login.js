
$(document.body).ready(function () {
    $(document).on("click", "#login", function (event) {
        if (!$("#login_input").val() || !$("#password_input").val()){
            $("#login_status").html("Please input a login and password.");
            $("#login_status").css("color", "red");
            return;
        }
        login($("#login_input").val(), $("#password_input").val());
    });
});
