<?php

header('Access-Control-Allow-Origin: *');
$location = 'sqlite:' . __DIR__ . '/safebrowsing.sqllite3';
$finger   = 'finger';
$status   = 'status';

$_GET[$finger] = 'f';
$_GET['name']  = 'n';

if (!isset($_GET[$finger])) {
    die('Not&nbsp;a&nbsp;website!');
}

($dbh = new PDO($location)) || die('cannot open the database');

($stmt = $dbh->prepare('SELECT name FROM browsertab WHERE fingerprint=?')) || trigger_error($dbh->error, E_USER_ERROR);
$stmt->execute([$_GET[$finger]]) || trigger_error($stmt->error, E_USER_ERROR);
$stmt->bindColumn('name', $name);

if ($stmt->fetch(PDO::FETCH_BOUND)) {
    die(json_encode([
        'name'  => $name,
        $status => 0,
    ]));
}

if (isset($_GET['check'])) {
    die(json_encode([
        $status => 3,
    ]));
}

$dbh->prepare('INSERT INTO browsertab VALUES (?,?)')
    ->execute([$_GET[$finger], $_GET['name']]);

echo json_encode([
    $status => 1,
]);
