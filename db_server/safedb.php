<?php
header("Access-Control-Allow-Origin: *");
$location ="sqlite:".__DIR__."/safebrowsing.sqllite3";

if (isset($_GET['finger']))
{
    $dbh  = new PDO($location) or die("cannot open the database");

    $query =  "SELECT * FROM browsertab WHERE fingerprint='".$_GET['finger']."'";
    $count = 0;
    $res = $dbh->query($query);
    if($res) {
        foreach ($res as $row) {
            $name['name'] = $row[1];
            $name["status"]=0;
            $count = $count + 1;
            echo json_encode($name);
            $dbh = null;
            die();
        }
    }
    if ($count==0)
    {
        $query =  "INSERT INTO browsertab VALUES('".$_GET['finger']."','".$_GET['name']."')";
        $dbh->query($query);
        $res = $dbh->query($query);
        $ar["status"]=1;
        echo json_encode($ar);

    }
}

else
{
    echo "Not a website!";
}