<?php

// Verify database.xml exist, and then load and process it.
if ( !file_exists( '../../conf/database.xml' ) )
{
	echo "ERROR: ../../conf/database.xml is missing or corrupted.  Restore the file or reinstall Wireless Adviser.";
	exit;
}

// Load database.xml conf file.
try
{
	$dbconf = new SimpleXMLElement( '../../conf/database.xml', null, true );
	if ( $dbconf == FALSE )
	{
		throw new Exception( '../../conf/database.xml is corrupted.' );
	}
}
catch( Exception $e )
{
	echo "ERROR: ../../conf/database.xml is corrupted.  Restore the file or reinstall Wireless Adviser.";
	exit;
}
$serverinfo = explode( ':', $dbconf->server["value"] );

$host = $serverinfo[0];
$port = $serverinfo[1];
$dbname = $dbconf->database["value"];
$dbuser = $dbconf->username["value"];
$dbpassword = $dbconf->password["value"];

$connection = "host=" . $host . " port=" . $port . " user=" . $dbuser . " password=" . $dbpassword;

$db = pg_connect( $connection );
if ( !$db )
{
	echo "ERROR:Problem with connecting to the database. ($connection) - " . pg_last_error();
	exit;
}
if ( !pg_query( $db, "DROP DATABASE $dbname" ) )
{
	echo "ERROR: Problem deleting $dbname - " . pg_last_error();
}
else
{
	echo "$dbname successfully deleted.";
}

pg_close( $db );
?>