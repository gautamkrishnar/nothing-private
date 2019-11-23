<?php
if (php_sapi_name() == "cli") {
    // In cli-mode
    require_once __DIR__ . '/connection.php';

    $stmt = $mysqli->prepare('SELECT count(*) cnt FROM browsertab');
    $stmt->execute();
    $stmt->bind_result($count_from_db);
    $stmt->fetch();
    $stmt->free_result();

    $visitors_file_name = __DIR__ . '/visitors.txt';
    $count_from_file = (int)@file_get_contents($visitors_file_name);
    file_put_contents($visitors_file_name, $count_from_db + $count_from_file);

    $stmt = $mysqli->prepare('TRUNCATE TABLE browsertab');
    $stmt->execute();
} else {
    // Web mode
    echo "Cant be run directly";
}
