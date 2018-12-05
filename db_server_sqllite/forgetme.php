<?php

header('Access-Control-Allow-Origin: *');
$location = 'sqlite:' . __DIR__ . '/safebrowsing.sqllite3';
$finger   = 'finger';

if (!isset($_GET[$finger])) {
    die('Not&nbsp;a&nbsp;website!');
}

($dbh = new PDO($location)) || die('cannot open the database');

($stmt = $dbh->prepare('DELETE FROM browsertab WHERE fingerprint=?')) || trigger_error($dbh->error, E_USER_ERROR);
$stmt->execute([$_GET[$finger]]) || trigger_error($stmt->error, E_USER_ERROR);

echo json_encode([
    'state' => 1,
]);
