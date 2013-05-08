<?php
include( 'dbconn.inc' );

$query = "DELETE FROM wirelessadviser.events";
if ( array_key_exists( 'event_id', $_GET ) )
{
	$eventKeys = preg_split( "/__/", $_GET['event_id'] );
	$query .= " WHERE event_time = '" . $eventKeys[0] . "'";
	$query .= " AND device_id = '" . $eventKeys[1] . "'";
	
	$result = pg_query( $db, $query );
	if ( !$result )
	{
		echo "ERROR:Problem with SQL query " + $query;
	}
	
	// Check to see if any events exist now for the device.  
	$query = "SELECT device_id FROM wirelessadviser.events WHERE device_id = '" . $eventKeys[1] . "'";
	$result = pg_query( $db, $query );
	if ( !$result ) 
	{
		echo "ERROR: Problem retrieving device state for device id = '" . $eventKeys[1] . "'";
	}
	else if ( pg_num_rows( $result ) == 0 )
	{
		$query = "UPDATE wirelessadviser.devices SET device_state = 'unknown' WHERE device_id = '" . $eventKeys[1] . "'";
		$result = pg_query( $db, $query );
		if ( !$result ) 
		{
			echo "ERROR: Problem updating device state for device id " + $eventKeys[1];
		}
	}
}
else
{
	echo "ERROR:No event id received to perform the deletion.";
}

pg_close( $db );
?>