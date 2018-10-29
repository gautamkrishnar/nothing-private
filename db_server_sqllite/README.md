# Nothing private Server files (Depreciated)
* [safedb.php](safedb.php) contains the backend code written in PHP to process the [safebrowsing.sqllite3](safebrowsing.sqllite3) database. Uses the PHP PDO extension for database access.
* [forgetme.php](forgetme.php) is used to delete the user's fingerprint data from the database
* The PHP script uses JSON to communicate with the app front end.

This code is now depreciated. Switched to MYSQL database for more performance.
