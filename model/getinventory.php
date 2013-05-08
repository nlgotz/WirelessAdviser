<?php
include( 'dbconn.inc' );

$query =  "SELECT devices.device_id, display_name, ip_address, device_type, device_state, latitude, longitude, azimuth, height, max_severity as severity_id";
$query .= " FROM wirelessadviser.devices LEFT OUTER JOIN wirelessadviser.max_severity_for_device";
$query .= " ON devices.device_id = max_severity_for_device.device_id";
$query .= " ORDER BY devices.device_id";

$result = pg_query( $db, $query );
if ( !$result )
{
	echo "ERROR:Problem with SQL query.";
	exit;
}
else
{
	$numberOfRows = pg_num_rows( $result );
	echo '{ "inventoryList": [ ';
	while( $row = pg_fetch_assoc( $result) )
	{
		echo "{";
		$jsonData = "";
		foreach( $row as $key => $value )
		{
			$jsonData .= '"' . $key . '":"' . $value . '",';
		}
		$jsonData = substr( $jsonData, 0 , -1 );
		echo $jsonData;
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