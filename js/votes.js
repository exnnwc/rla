function createVote(achievement_id, vote, explanation){
    data = {function_to_be_called:"create_vote", achievement_id:achievement_id, vote:vote, explanation:explanation};
    AJAXOnly(data, function (result){
        console.log(result);
    });
}


