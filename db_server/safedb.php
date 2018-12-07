<?php

header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/connection.php';
$finger = 'finger';
$status = 'status';

if (!isset($_GET[$finger])) {
    die('Not&nbsp;a&nbsp;website!');
}

$stmt = $mysqli->prepare('SELECT name FROM browsertab WHERE fingerprint=?');
$stmt->bind_param('s', $_GET[$finger]);
$stmt->execute();
$stmt->bind_result($name);

if ($stmt->fetch()) {
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

$stmt = $mysqli->prepare('INSERT INTO browsertab VALUES (?,?)');
$stmt->bind_param('ss', $_GET[$finger], $_GET['name']);
$stmt->execute();

echo json_encode([
    $status => 1,
]);
