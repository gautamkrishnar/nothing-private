<?php

header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/connection.php';
$finger = 'finger';

if (isset($_GET[$finger]))
{
   $stmt = $mysqli->prepare('DELETE FROM browsertab WHERE fingerprint=?');
   $stmt->bind_param('s', $_GET[$finger]);
   $stmt->execute();
   $forgeted['state']=1;
   echo json_encode($forgeted);
   $mysqli->close();
   die();
}
else
{
    echo 'Not&nbsp;a&nbsp;website!';
}
