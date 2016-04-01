$(document.body).ready(function () {
    $(document).on("focusin", ".explanation_input", function (event) {
        $(".explanation_input").val("");
        $(".explanation_input").css("color", "black");
    });
    $(document).on("focusout", ".explanation_input", function (event) {
        if ($(".explanation_input").val().trim()==""){
            $(".explanation_input").val("Please explain why if nay.");
            $(".explanation_input").css("color", "grey");
        }
    });
    $(document).on("click", ".vote_button", function (event) {
        html_id = event.target.id; 
        vote = html_id.substr(0,3);
        if (vote==="yay"){
            vote=true;
        } else if (vote==="nay"){
            vote=false;
        }
        achievement_id = html_id.substr(3, html_id.length-3);
        explanation = $("#explanation_input"+achievement_id).val();
        if (explanation.trim() === "Please explain why if nay." || explanation.trim()==""){
            explanation="";
        }
        createVote(achievement_id, vote, explanation);
    
        window.reload();
    });
});


