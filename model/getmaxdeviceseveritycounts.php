<?php
include( 'dbconn.inc' );

$query = 'SELECT devices.device_id, max( severity_id ) as max_severity_id';
$query .= ' FROM wirelessadviser.events, wirelessAdviser.devices';
$query .= ' WHERE devices.device_id = events.device_id';
$query .= ' GROUP BY devices.device_id';

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
		$maxCountAccumulator[ $row['max_severity_id'] ]++;
	}
	
	$numberOfRows = count( $maxCountAccumulator );
	echo '{ "severityCounts": [';
	foreach( $maxCountAccumulator as $key=>$value )
	{
		echo "{";
		echo '"severity_id":"' . $key . '",';
		echo '"count":"' . $value . '"';
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