<?php 
include( 'dbconn.inc' );

$device_id = 0;

// Recover device_id.
if ( array_key_exists( 'device_id', $_GET ) )
{
	$device_id = $_GET['device_id'];
}

//Build and execute query.
$query = 'SELECT devices.device_id, ip_address, display_name, device_type, max_severity as severity_id';
$query .= ' FROM wirelessadviser.devices LEFT OUTER JOIN wirelessadviser.max_severity_for_device';
$query .= ' ON devices.device_id = max_severity_for_device.device_id';
$query .= ' WHERE devices.device_id = \'' . $device_id . '\'';

$result = pg_query( $db, $query );
if ( !$result )
{
	echo "ERROR:Problem with SQL query: " . $query;
	exit;
}

// Process results and return JSON.
$jsonText = '{';
while( $row = pg_fetch_assoc( $result ) )
{
	foreach ( $row as $key=>$value )
	{
		$jsonText .= '"' . $key . '":"' . $value . '",';
	}
	$jsonText = substr( $jsonText, 0, -1 );
	$jsonText .= '}';
	echo $jsonText;
}


pg_close( $db );
?>