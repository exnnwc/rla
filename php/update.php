<html><body>

        <?php
        include ("config.php");
        $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
        $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
        $connection->exec("update achievements set power_adj=power where active=1");
        //$update->execute();
        $statement = $connection->query("select * from achievements where parent=0");
        $statement->execute();
        while ($achievement = $statement->fetchObject()) {
            echo "<div>$achievement->id $achievement->name $achievement->power ";            
            $statement2 = $connection->query("select required_by from requirements where active=1 and required_for=$achievement->id order by required_by");
            $statement2->execute();
            while ($required_by = $statement2->fetchColumn()) {
                $requirement=fetch_achievement($required_by);
                echo "<div style='background-color:gray;margin-left:32px'>$requirement->id $requirement->name ->$requirement->power</div>";
                $connection->exec("update achievements set power_adj=power_adj+$requirement->power where id=$requirement->id");
            }
            $statement2 = $connection->query("select required_for from requirements where active=1 and required_by=$achievement->id order by required_for");
            $statement2->execute();
            while ($required_for = $statement2->fetchColumn()) {
                $requirement=fetch_achievement($required_for);
                echo "<div style='background-color:red;margin-left:32px'>$requirement->id $requirement->name ->$requirement->power</div>";
                $connection->exec("update achievements set power_adj=power_adj-$requirement->power_adj where id=$requirement->id");
            }
            echo "</div>";
        }
        
        
        
        function fetch_achievement($id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select * from achievements where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchObject();
}
        ?>
    </body></html>
