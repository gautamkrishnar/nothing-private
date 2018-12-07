<?php

header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/connection.php';
$finger = 'finger';

if (!isset($_GET[$finger])) {
    die('Not&nbsp;a&nbsp;website!');
}

$stmt = $mysqli->prepare('DELETE FROM browsertab WHERE fingerprint=?');
$stmt->bind_param('s', $_GET[$finger]);
$stmt->execute();

echo json_encode([
    'state' => 1,
]);
