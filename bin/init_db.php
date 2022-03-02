#!/usr/bin/env_php
<?php
$dsn = "pgsql:host=localhost;port=5432;dbname=book_library_db";
$dbConn = new PDO($dsn, 'postgres', 'qwerty');

$dbConnect = pg_connect('host=localhost dbname=contact_list_db user=postgres password=qwerty');



$dbConn->query('DELETE FROM users');
