<?php 
include( 'dbconn.inc' );

$result = pg_query( $db, "SELECT * FROM wirelessadviser.devices ORDER BY device_id" );
$device_status = pg_query( $db, "SELECT * FROM wirelessadviser.max_severity_for_device ORDER BY device_id" );
if ( !$result or !$device_status )
{
	echo "ERROR:Problem with SQL query.";
	exit;
}
else
{
	// Build map of device to status.
	$deviceToStatusMap;
	while( $row = pg_fetch_assoc( $device_status ) )
	{
		$deviceToStatusMap[ $row[ 'device_id' ] ] = $row[ 'max_severity' ];
	}

	
	$numberOfRows = pg_num_rows( $result );
	echo '{ "deviceList": [ ';
	while( $row = pg_fetch_assoc( $result) )
	{
		echo "{";
		$jsonData = "";
		foreach( $row as $key => $value )
		{
			$jsonData .= '"' . $key . '":"' . $value . '",';
		}
		$jsonData .= '"severity_id":"' . $deviceToStatusMap[ $row['device_id'] ] . '"';
		echo $jsonData;
		echo "}";
		
		if ( $numberOfRows > 1 )
		{
			echo ",";
			$numberOfRows--;
		}

	}
	echo ' ] }';	
	
	/*
	// Process device table results.
	$numberOfRows = pg_num_rows( $result );
	echo '{ "deviceList": [ ';
	while( $row = pg_fetch_assoc( $result) )
	{
		echo "{";
		echo '"device_id":"' . $row['device_id'] . '",';
		echo '"display_name":"' . $row['display_name'] . '",';
		echo '"device_type":"' . $row['device_type'] . '",';
		echo '"device_status":"' . $row['device_status'] . '",';			
		echo '"severity_id":"' . $deviceToStatusMap[ $row['device_id'] ] . '",';
		echo '"latitude":"' . $row['latitude'] . '",';
		echo '"longitude":"' . $row['longitude'] . '"';
		echo "}";
		
		if ( $numberOfRows > 1 )
		{
			echo ",";
			$numberOfRows--;
		}

	}

	echo ' ] }';
*/
}

pg_close( $db );
?>