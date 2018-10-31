<?php

header("Access-Control-Allow-Origin: *");
require_once('./connection.php');
$finger = "finger";
$status = "status";
if (isset($_GET[$finger]))
{
    $stmt = $mysqli->prepare('SELECT * FROM browsertab WHERE fingerprint=?');
    $stmt->bind_param("s", $_GET[$finger]);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->num_rows;
    while($row = $result->fetch_assoc()) {
        $name['name'] = $row['name'];
        $name[$status] = 0;
	    $count = 1;
        echo json_encode($name);
        $mysqli->close();
        die();
    }

    if ($count == 0)
    {
        if (isset($_GET['check']))
        {
            $arr[$status] = 3;
            echo json_encode($arr);
            $mysqli->close();
            die();
        }
        $stmt = $mysqli->prepare('INSERT INTO browsertab VALUES (?,?)');
        $stmt->bind_param("ss", $_GET[$finger], $_GET['name']);
        $stmt->execute();
        $ar[$status] = 1;
        echo json_encode($ar);
        $mysqli->close();
	    die();
    }
}
else
{
    echo "Not&nbsp;a&nbsp;website!";
}
?>


