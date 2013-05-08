<?php

$returnString = "ERROR: Unknown error occurred...";

// Note: the following few lines of code are from dbconn.inc.  Need special handling here to check if the database actually exists first.

// Load database.xml conf file.
$dbconf = new SimpleXMLElement( '../../conf/database.xml', null, true );
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
	echo "ERROR: Database connection problem (connection info: $connection).  Check the credentials, hostname, port, and database name in <application-root>/conf/database.xml.  " . pg_last_error($db);
	exit;
}
else
{
	$db2 = pg_connect( $connection . " dbname=" . $dbname );
	if ( $db2 )
	{
		pg_close( $db );
		$db = $db2;
	}
	else
	{
		echo "ERROR: Database does not exist.";
		exit;
	}
}


if( array_key_exists( 'user_id', $_GET ) == true && array_key_exists( 'password', $_GET ) == true )
{
	$query = "SELECT *";
	$query .= " FROM wirelessadviser.users";
	$query .= " WHERE users.user_id = '" . $_GET['user_id'] . "'";
	$query .= " AND users.password = '" . $_GET['password'] . "'";
		
	$result = pg_query( $db, $query );
	if ( !$result )
	{
		$returnString = pg_last_error();
	}
	else
	{
		if ( pg_num_rows( $result ) == 1 )
		{
			$row = pg_fetch_assoc( $result );
			$returnString = $row[ 'account_type' ];
		}
		else
		{
			$returnString = "ERROR: Login or password incorrect.";
		}
	}
}
else
{
	$returnString = "ERROR: No login or password specified.";
}

echo $returnString;

pg_close( $db );