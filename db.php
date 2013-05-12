<?php
include_once("db/class.db.php");

$host = "localhost";
$dbname = "wirelessadviser";
$user = "postgres";
$password = "postgres";

    //create the db connection
$db = new db("pgsql:host=$host;port=5432;dbname=$dbname;user=$user;password=$password",
	$user,
	$password);

$site="http://".$_SERVER['HTTP_HOST']."/wafe";

?>