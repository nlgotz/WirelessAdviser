<?php 
/*
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
*/
$host = "localhost";
$port = "5432";
$dbname = "wirelessadviser";
$dbuser = "postgres";
$dbpassword = "postgres";
$connection = "host=" . $host . " port=" . $port . " dbname=" . $dbname . " user=" . $dbuser . " password=" . $dbpassword;
$db = pg_connect( $connection );

if ( !$db )
{
	echo "ERROR: Database connection problem (connection info: $connection).  Check the credentials, hostname, port, and database name in <application-root>/conf/database.xml.  " . pg_last_error($db);
	exit;
}

?>
