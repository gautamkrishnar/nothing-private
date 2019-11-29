<?php
require_once("vendor/autoload.php");
Sentry\init(['dsn' => 'https://a3b0f1cebac341c19ecd87ccc7eab5a7@sentry.io/1823897' ]);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
set_exception_handler(function ($e) {
    Sentry\captureException($e);
    die('Error occurred!');
});

$mysqli = new mysqli('', '', '', '');
$mysqli->set_charset('utf8mb4');
