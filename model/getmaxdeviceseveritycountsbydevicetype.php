<?php
include( 'dbconn.inc' );

$query = 'SELECT devices.device_id, device_type, max( severity_id ) as max_severity_id';
$query .= ' FROM wirelessadviser.events, wirelessAdviser.devices';
$query .= ' WHERE devices.device_id = events.device_id';
$query .= ' GROUP BY devices.device_id ORDER BY device_type';

$result = pg_query( $db, $query );
if ( !$result )
{
	echo "ERROR:Problem with SQL query.";
	exit;
}
else
{
	$maxCountAccumulator;
	
	// Accumulate devices.
	while( $row = pg_fetch_assoc( $result) )
	{
		$maxCountAccumulator[ $row['device_type'] ][ $row['max_severity_id'] ]++;
	}
	
	$numberOfRows = count( $maxCountAccumulator );
	echo '{ "severityCountsByType": [';
	foreach( $maxCountAccumulator as $key=>$severityAccumulator )
	{
		$numberOfRows2 = count( $severityAccumulator );
		foreach( $severityAccumulator as $key2=>$value )
		{
			echo "{";
			echo '"type":"' . $key . '",';
			echo '"severity_id":"' . $key2 . '",';
			echo '"count":"' . $value . '"';
			echo "}";
			
			if ( $numberOfRows2 > 1 )
			{
				echo ",";
				$numberOfRows2--;
			}
		}
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