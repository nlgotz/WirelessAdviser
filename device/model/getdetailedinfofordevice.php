<?php 
include( 'dbconn.inc' );

// Recover device_id.
if ( array_key_exists( 'device_id', $_GET ) )
{
	$device_id = $_GET['device_id'];
}

//Build and execute query.
$query = 'SELECT poll_id, result_value';
$query .= ' FROM wirelessadviser.device_properties';
$query .= ' WHERE device_id = \'' . $device_id . '\'';

$result = pg_query( $db, $query );
if ( !$result )
{
	echo "ERROR:Problem with SQL query.";
	exit;
}

// Process results and return JSON.
echo '{ ';
$firstTime = true;
while( $row = pg_fetch_assoc( $result ) )
{
	if( $firstTime != true )
	{
		echo ', ';
	}
	else
	{
		$firstTime = false;
	}

	echo '"' . $row['poll_id'] . '":"' . $row['result_value'] . '"';	
}
echo ' }';

pg_close( $db );
?>