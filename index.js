
$(document.body).ready(function () {
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


