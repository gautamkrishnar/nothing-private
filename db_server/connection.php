<?php
$host = getenv("MYSQL_HOST");
$user = getenv("MYSQL_USER");
$password = getenv("MYSQL_PASSWORD");
$db = getenv("MYSQL_DB");
$monitoring_url = getenv("MONITORING_URL");

if($monitoring_url) {
    require_once(__DIR__ . "/vendor/autoload.php");
    Sentry\init(['dsn' => $monitoring_url]);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
set_exception_handler(function ($e) {
    global $monitoring_url;
    if ($monitoring_url) {
        Sentry\captureException($e);
    }
    die('Error occurred!');
});

$mysqli = new mysqli($host, $user, $password, $db);
$mysqli->set_charset('utf8mb4');
