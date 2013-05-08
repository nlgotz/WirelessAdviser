<?php 
include( 'dbconn.inc' );

$result = pg_query( $db, "SELECT * FROM wirelessadviser.links" );
if ( !$result )
{
	echo "ERROR:Problem with SQL query.";
	exit;
}
else
{
	$numberOfRows = pg_num_rows( $result );

	echo '{ "linkList": [ ';

	while( $row = pg_fetch_assoc( $result) )
	{
		echo "{";
		echo '"parent_id":"' . $row['parent_id'] . '",';
		echo '"child_id":"' . $row['child_id'] . '"';
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