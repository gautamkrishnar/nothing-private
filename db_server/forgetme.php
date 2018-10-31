<?php

header("Access-Control-Allow-Origin: *");
require_once('./connection.php');
$finger = "finger";

if (isset($_GET[$finger]))
{
    $stmt = $mysqli->prepare('SELECT * FROM browsertab WHERE fingerprint=?');
    $stmt->bind_param("s", $_GET[$finger]);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $stmt = $mysqli->prepare('DELETE FROM browsertab WHERE fingerprint=?');
        $stmt->bind_param("s", $_GET[$finger]);
        $stmt->execute();
		$forgeted['state']=1;
        echo json_encode($forgeted);
        $mysqli->close();
        die();
    }
}
else
{
    echo "Not&nbsp;a&nbsp;website!";
}
?>
