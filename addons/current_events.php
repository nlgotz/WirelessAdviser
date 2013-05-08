<!--
NAME=Current Events Report
DESCRIPTION=Listing of all events in the event database of Wireless Adviser
-->

<?php

include( '../model/dbconn.inc' );
include( '../libs/wafe_utils.inc' );

$deviceId2NameMap = array();

$query =  "SELECT device_id, display_name";
$query .= " FROM wirelessadviser.devices";
$query .= " ORDER BY device_id";

$result = pg_query( $db, $query );
if ( !$result )
{
	echo "ERROR:Problem with Device SQL query.";
	exit;
}
else
{
	while( $row = pg_fetch_assoc( $result) )
	{
		$deviceId2NameMap[ $row[ 'device_id' ] ] = $row[ 'display_name' ];
	}
}

$query =  "SELECT *";
$query .= " FROM wirelessadviser.events";
$query .= " ORDER BY event_time DESC, device_id";

$result = pg_query( $db, $query );
if ( !$result )
{
	echo "ERROR:Problem with SQL query.";
	exit;
}
else
{
	$numRows = pg_num_rows( $result );
	echo <<<THEAD
<html>
<head>
	<title>Events Report</title>
	<link rel="stylesheet" type="text/css" media="screen" href="../cambium.css"/>
</head>

<body style="background-color:#FFFFFF;">

<h1>Events</h1>
<i>Total number of events: 
THEAD;

	echo $numRows;

	echo <<<THEAD2
</i>
<p>
<table id="myTable" class="tablesorter"> 
<thead> 
	<tr> 
		<th>Event Time</th>
		<th>Device Name</th> 
		<th>Severity</th> 
		<th>Description</th>
	</tr> 
</thead> 
<tbody> 
THEAD2;
	while( $row = pg_fetch_assoc( $result ) )
	{
		echo "<tr>";
		$keylist = array( 'event_time', 'device_id', 'severity_id', 'description' );
		foreach( $keylist as $key )
		{
			if ( strcmp( $key, 'device_id' ) == 0 ) 
			{
				$row[ $key ] = $deviceId2NameMap[ $row[ $key ] ];
			}
			else if ( strcmp( $key, 'severity_id' ) == 0 ) 
			{
				$row[ $key ] = getNameForSeverityId( $row[ $key ] );
			}
			echo "<td>" . $row[ $key ] . "</td>";
		}
		echo "</tr>";
	}
	
	echo "</table>";	
	echo "</tbody>";	
	echo "</body></html>";
}

pg_close( $db );
?>