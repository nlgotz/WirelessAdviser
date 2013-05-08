<?php 
include( 'dbconn.inc' );

$maxNumberOfPolls = 25;

// Recover device_id.
if ( array_key_exists( 'device_id', $_GET ) )
{
	$device_id = $_GET['device_id'];
}

//Build and execute query.
$query = 'SELECT execution_time, response_time';
$query .= ' FROM wirelessadviser.status_polling_history';
$query .= ' WHERE device_id = \'' . $device_id . '\'';
$query .= ' ORDER BY execution_time DESC';
$query .= ' LIMIT ' . $maxNumberOfPolls;

$result = pg_query( $db, $query );
if ( !$result )
{
	echo "ERROR:Problem with SQL query.";
	exit;
}

// Process results and return JSON.
// Need to reverse the order of the results due to the need to go in 
// increasing time.
$resultArray = array();
while( $row = pg_fetch_assoc( $result ) )
{
	$entry = '{ "timestamp":"' . $row['execution_time'] . '","value":';
	if ( $row['response_time'] != -1 )
	{
		$entry .= '"1"';
	}
	else
	{
		$entry .= '"0"';
	}
	$entry .=  '}';
	$resultArray[] = $entry;
}
$resultArray = array_reverse( $resultArray );


echo '{ "statusList": [ ';
$firstTime = true;
foreach( $resultArray as $entry )
{
	if( $firstTime != true )
	{
		echo ', ';
	}
	else
	{
		$firstTime = false;
	}
	echo $entry;
}
echo ' ] }';

pg_close( $db );
?>