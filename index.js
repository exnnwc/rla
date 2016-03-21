
$(document.body).ready(function () {
    $(document).on("click", ".vote_button", function (event) {
        html_id = event.target.id; 
        vote = html_id.substr(0,3);
        achievement_id = html_id.substr(3, html_id.length-3);
        createVote(achievement_id, vote);
    });
   $(".vote_div").ready(function(event){

    });
});
function startTimer(id){
   getNumOfSecondsUntilAuthorize(id,function(original_num_of_seconds){
        setInterval(function(){
            original_num_of_seconds--;
            voteTime =  formatVoteTime(original_num_of_seconds);
            $("#vote_timer"+id).html(voteTime);
        }, 1000);
    }); 
}


