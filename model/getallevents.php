<?php
include( 'dbconn.inc' );

$query = "SELECT events.event_time, events.device_id, devices.display_name, devices.device_type, events.severity_id, events.description";
$query .= " FROM wirelessadviser.events LEFT OUTER JOIN wirelessadviser.devices";
$query .= " ON events.device_id = devices.device_id";

if ( array_key_exists( 'device_id', $_GET ) && array_key_exists( 'severity_id', $_GET ) )
{
	$query .= " WHERE device_id = '" . $_GET['device_id'] . "'";
	$query .= " AND severity_id = '" . $_GET['severity_id'] . "'";
}
else if ( array_key_exists( 'device_id', $_GET ) )
{
	$query .= " WHERE device_id = '" . $_GET['device_id'] . "'";
}
else if ( array_key_exists( 'severity_id', $_GET ) )
{
	$query .= " WHERE severity_id = '" . $_GET['severity_id'] . "'";
}
$query .=  " ORDER BY events.event_time DESC";


$result = pg_query( $db, $query );
if ( !$result )
{
	echo "ERROR:Problem with SQL query.";
	exit;
}
else
{
	$numberOfRows = pg_num_rows( $result );

	echo '{ "eventList": [ ';
	while( $row = pg_fetch_assoc( $result) )
	{
		echo "{";
		echo '"event_time":"' . $row['event_time'] . '",';
		echo '"device_id":"' . $row['device_id'] . '",';
		echo '"display_name":"' . $row['display_name'] . '",';
		echo '"device_type":"' . $row['device_type'] . '",';
		echo '"severity_id":"' . $row['severity_id'] . '",';
		echo '"description":"' . $row['description'] . '"';
		echo "}";
		
		if ( $numberOfRows > 1 )
		{
			echo ",";
			$numberOfRows--;
		}

	}
	echo ' ] }';	
}


pg_close( $db );
?>