$(document.body).ready(function () {
    if ($(document.body).attr('id') === "user_profile") {
        userID = 0;
    } else if ($(document.body).attr('id').length>11) {
        userID = $(document.body).attr('id').substr(12, $(document.body).attr('id').length-12);
    }
    displayUserProfile(userID);
});


function displayUserProfile(userID){
    $.ajax({
        method:"POST",
        url:"/rla/php/user-profile.php",
        data:{ id:userID }
    })
        .done(function (result){
            console.log(result);
        });
    
}
