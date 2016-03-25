
$(document.body).ready(function () {
    $(document).on("click", ".upvote", function (event) {
        html_id=event.target.id;
        achievement_id=html_id.substr(6, html_id.length-6);
        changePoints(achievement_id, true);
    });
    $(document).on("click", ".downvote", function (event) {
        html_id=event.target.id;
        achievement_id=html_id.substr(8, html_id.length-8);
        changePoints(achievement_id, false);
    });
});
