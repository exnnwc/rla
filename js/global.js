
$(document.body).ready(function () {
    $(document).on("click", "#login", function (event) {
        login($("#login_input").val(), $("#password_input").val());
    });
    $(document).on("click", "#logout", function (event) {
        logOut();
    });
    $(document).on("click", "#show_login", function (event) {
        $("#login_form").show();
        $("#show_login").hide();
    });
    $(document).on("click", "#hide_login", function (event) {
        $("#login_form").hide();
        $("#show_login").show();
    });
    $(document).on("keypress", "#password_input", function (event) {
            if(event.key==="Enter"){
                login($("#login_input").val(), $("#password_input").val());
            }
    });
});
