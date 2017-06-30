<?php

header("Access-Control-Allow-Origin: *");
$location ="sqlite:".__DIR__."/safebrowsing.sqllite3";

if (isset($_GET['finger']))
{
    $finger = $_GET['finger'];
    $dbh  = new PDO($location) or die("cannot open the database");
    # $query =  "SELECT * FROM browsertab WHERE fingerprint='".$_GET['finger']."'";
    $stmt = $dbh->prepare("SELECT * FROM browsertab WHERE fingerprint=?");
    $stmt->execute($finger);
    $result = $stmt->fetch(); 
    $count = 0;
    # $res = $dbh->query($query);
    if($result) {
        foreach ($result as $row) {
            $name["name"] = $row[1];
            $name["status"]=0;
            $count = $count + 1;
            echo json_encode($name);
            $dbh = null;
            die();
        }
    }
    $stmt->close();
    
    if ($count==0)
    {
        if (isset($_GET['check']))
        {
            $arr["status"]=3;
            echo json_encode($arr);
            die();
        }
        $username = $_GET['name'];
        # $query =  "INSERT INTO browsertab VALUES('".$_GET['finger']."','".$_GET['name']."')";
        #$dbh->query($query);
        #$res = $dbh->query($query);
        $query = "INSERT INTO browsertab VALUES (?,?)"
        $dbh->prepare($query)->execute([$finger, $username]);
        
        $ar["status"]=1;
        echo json_encode($ar);
    }
}
else
{
    echo "Not a website!";
}
?>


