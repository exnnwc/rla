
function formatVoteTime( num_of_seconds){
    if (num_of_seconds>3599){
        hours = Math.floor (num_of_seconds/3600);
        num_of_seconds = num_of_seconds-(hours*3600);
        minutes = Math.floor(num_of_seconds/60);
        num_of_seconds = num_of_seconds - (minutes*60);
        seconds = num_of_seconds;
    }else if (num_of_seconds>59){
        hours = 0;
        minutes = Math.floor(num_of_seconds/60);
        seconds = num_of_seconds - (minutes*60);
    } else if (num_of_seconds<60){
        hours =0;
        minutes = 0;
        seconds = num_of_seconds;
    }
    return hours + "h " + minutes + "m " + seconds + "s";
}
