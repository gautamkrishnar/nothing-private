# Nothing private Server files

* [cron.db](cron.php) truncates the database and updates the visitors.txt file
* [safedb.php](safedb.php) contains the backend code written in PHP to process the database.
* [db.sql](db.sql) contains database schema definition.
* [forgetme.php](forgetme.php) is used to delete the user's fingerprint data from the database.
* The PHP script uses JSON to communicate with the app front end.
