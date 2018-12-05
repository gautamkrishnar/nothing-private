<?php

header('Access-Control-Allow-Origin: *');
$location = 'sqlite:' . __DIR__ . '/safebrowsing.sqllite3';
$finger = 'finger';

if (isset($_GET[$finger]))
{
	
    $dbh = new PDO($location) || die('cannot open the database');

    $stmt = $dbh->prepare('SELECT * FROM browsertab WHERE fingerprint=?') || trigger_error($dbh->error, E_USER_ERROR);
    $stmt->execute([$_GET[$finger]]) || trigger_error($stmt->error, E_USER_ERROR);
    $result = $stmt->fetch(); 
	
    if ($result) {
        $stmt = $dbh->prepare('DELETE FROM browsertab WHERE fingerprint=?') || trigger_error($dbh->error, E_USER_ERROR);
    	$stmt->execute([$_GET[$finger]]) || trigger_error($stmt->error, E_USER_ERROR);
		
		$forgeted['state']=1;
		echo json_encode($forgeted);
    }
    $dbh=null;
	die();
}
else
{
    echo 'Not&nbsp;a&nbsp;website!';
}
