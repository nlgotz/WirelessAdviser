<?php 

/*
$host = "localhost";
$port = "5432";
$dbname = "wirelessadviser";
$dbuser = "postgres";
$dbpassword = "postgres";
*/

// Load database.xml conf file.
$dbconf = new SimpleXMLElement( '../../../conf/database.xml', null, true );
$serverinfo = explode( ':', $dbconf->server["value"] );

$host = $serverinfo[0];
$port = $serverinfo[1];
$dbname = $dbconf->database["value"];
$dbuser = $dbconf->username["value"];
$dbpassword = $dbconf->password["value"];

$connection = "host=" . $host . " port=" . $port . " dbname=" . $dbname . " user=" . $dbuser . " password=" . $dbpassword;
$db = pg_connect( $connection );
if ( !$db )
{
	echo "ERROR:Problem with connecting to the database. ($connection)";
	exit;
}

?>