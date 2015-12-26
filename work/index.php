<?php
include ("../config.php");
?>

<html><body onload="ListAllWork();DisplayWorkHistory();">
            <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>

        <script src="work.js"></script>
        <a href="<?php echo SITE_ROOT; ?>">Back To Achievements List</a>
        <h1 style="text-align:center;">Queue</h1>
            
        <div>
            <h3>
        Daily
            </h3>
            <div id="daily"> </div>
        </div><div>
            <h3>
        Weekly
            </h3>
            <div id="weekly"> </div>
        </div><div>
            <h3>
        Monthly
            </h3>
            <div id="monthly"> </div>
        </div><div>
            <h3>
        Unassigned
            </h3>
            <div id="unassigned"> </div>
        <div>
        </div>
        </div>
        <h1 style="text-align:center;">History</h1>
        <div id="work_history">
            
        </div>
<?php
    


?>
    </body></html>
