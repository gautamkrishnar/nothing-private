<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
set_exception_handler(function ($e) {
    die('Error occurred!');
});

$mysqli = new mysqli('', '', '', '');
$mysqli->set_charset('utf8mb4');
