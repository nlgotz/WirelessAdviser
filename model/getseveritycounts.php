<?php
include( 'dbconn.inc' );

$result = pg_query( $db, "SELECT * FROM wirelessadviser.events_by_severity" );
if ( !$result )
{
	echo "ERROR:Problem with SQL query.";
	exit;
}
else
{
	$numberOfRows = pg_num_rows( $result );

	echo '{ "severityCounts": [';
	
	while( $row = pg_fetch_assoc( $result) )
	{
		echo "{";
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