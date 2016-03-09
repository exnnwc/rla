
$(document.body).ready(function () {
    $(document).on("click", "#login", function (event) {
        login();
    });
    $(document).on("click", "#logout", function (event) {
        logOut();
    });
});
