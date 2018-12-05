<?php

header('Access-Control-Allow-Origin: *');
$location = 'sqlite:' . __DIR__ . '/safebrowsing.sqllite3';
$finger = 'finger';
$status = 'status';
if (isset($_GET[$finger]))
{

    $dbh = new PDO($location) || die('cannot open the database');

    $stmt = $dbh->prepare('SELECT * FROM browsertab WHERE fingerprint=?') || trigger_error($dbh->error, E_USER_ERROR);
    $stmt->execute([$_GET[$finger]]) || trigger_error($stmt->error, E_USER_ERROR);
    $result = $stmt->fetch();

    $count = 0;
    if ($result) {
        $name['name'] = $result['name'];
        $name[$status] = 0;
	$count = 1;
        echo json_encode($name);
        $dbh = null;
        die();
    }

    if ($count == 0)
    {
        if (isset($_GET['check']))
        {
            $arr[$status] = 3;
            echo json_encode($arr);
            die();
        }

        $query = 'INSERT INTO browsertab VALUES (?,?)';
        $dbh->prepare($query)->execute([$_GET[$finger], $_GET['name']]);

        $ar[$status] = 1;
        echo json_encode($ar);
	$dbh=null;
	die();
    }
}
else
{
    echo 'Not&nbsp;a&nbsp;website!';
}
