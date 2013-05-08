<?php
include( 'dbconn.inc' );

$result = pg_query( $db, "SELECT * FROM wirelessadviser.events_by_device_type ORDER BY device_type" );
if ( !$result )
{
	echo "ERROR:Problem with SQL query.";
	exit;
}
else
{
	$numberOfRows = pg_num_rows( $result );

	echo '{ "severityCountsByType": [';
	
	while( $row = pg_fetch_assoc( $result) )
	{
		echo "{";
		echo '"type":"' . $row['device_type'] . '",';
		echo '"severity_id":"' . $row['severity_id'] . '",';
		echo '"count":"' . $row['count'] . '"';
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