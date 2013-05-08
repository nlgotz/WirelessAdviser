<?php
include( 'dbconn.inc' );

$device_id = '0';
$rrs_id = 2;		
/*
** rrs_id mappings
** id	time			interval
** 1	last hour		1 minute
** 2	last 4 hours	4 minutes
** 3	last 12 hours	12 minutes
** 4	last day		30 minutes
** 5	last week		3 hours
** 6	last month		4 hours
** 7	last year		1 day
*/

// Recover device_id.
if ( array_key_exists( 'device_id', $_GET ) )
{
	$device_id = $_GET['device_id'];
}

// Recover bucket id.
if ( array_key_exists( 'bucket_id', $_GET ) )
{
	$bucket_id = $_GET['bucket_id'];
}


//Build and execute the gauge query.
$query = 'SELECT poll_id, data_value, execution_time';
$query .= ' FROM rrs.aggregated_gauge_results, rrs.bucket';
$query .= ' WHERE aggregated_gauge_results.device_id = \'' . $device_id . '\'';
$query .= ' AND bucket.bucket_id = aggregated_gauge_results.bucket_id';
$query .= ' AND bucket.rrs_id = ' . $rrs_id;
$query .= ' ORDER BY poll_id, execution_time';

$result = pg_query( $db, $query );
if ( !$result )
{
	echo "ERROR: Problem with SQL query.";
	exit;
}

// Pack and consolidate the results.
$probeQueryResults = array();
while( $row = pg_fetch_assoc( $result ) )
{
	$probeQueryResults[ $row['poll_id'] ][ $row[ 'execution_time' ] ] = $row[ 'data_value' ];
}


// Build and execute the counter query.
$query = 'SELECT poll_id, data_value, execution_time';
$query .= ' FROM rrs.aggregated_counter_results, rrs.bucket';
$query .= ' WHERE aggregated_counter_results.device_id = \'' . $device_id . '\'';
$query .= ' AND bucket.bucket_id = aggregated_counter_results.bucket_id';
$query .= ' AND bucket.rrs_id = ' . $rrs_id;
$query .= ' ORDER BY poll_id, execution_time';

$result = pg_query( $db, $query );
if ( !$result )
{
	echo "ERROR: Problem with SQL query.";
	exit;
}

// Pack and consolidate the results.
while( $row = pg_fetch_assoc( $result ) )
{
	$probeQueryResults[ $row['poll_id'] ][ $row[ 'execution_time' ] ] = $row[ 'data_value' ];
}


// Print out JSON.
echo '{ "stats": [ ';

$firstPollId = true;
foreach( $probeQueryResults as $probe_id => $probe_data )
{
	if( $firstPollId != true )
	{
		echo ',';
	}
	else
	{
		$firstPollId = false;
	}
	echo '{ "pollId":"' . $probe_id . '","pollData":[ ';

	$firstPollData = true;
	foreach( $probe_data as $timestamp => $value )
	{
		if( $firstPollData != true )
		{
			echo ',';
			$firstPollData = false;
		}
		else
		{
			$firstPollData = false;
		}		
		echo '{ "timestamp":"' . strtotime( $timestamp ) . '","value":"' . $value . '" }';
	}
	echo ' ]}';

}

echo ' ]}';	

pg_close( $db );
?>