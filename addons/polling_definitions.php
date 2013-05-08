<!--
NAME=Polling Definitions Report
DESCRIPTION=Listing of all the polling definitions that have been imported into Wireless Adviser
-->

<?php

include( '../model/dbconn.inc' );

$query =  "SELECT *";
$query .= " FROM wirelessadviser.polling_definitions";
$query .= " ORDER BY device_type, poll_id";

$result = pg_query( $db, $query );
if ( !$result )
{
	echo "ERROR:Problem with SQL query.";
	exit;
}
else
{

	echo <<<THEAD
<html>
<head>
	<title>Polling Definitions Report</title>
	<link rel="stylesheet" type="text/css" media="screen" href="../cambium.css"/>
</head>

<body style="background-color:#FFFFFF;">

<h1>Polling Definitions</h1>

<table id="myTable" class="tablesorter"> 
<thead> 
	<tr> 
		<th>Device Type</th>
		<th>Poll Id</th> 
		<th>Data Type</th> 
		<th>OID</th>
		<th>Execution Interval</th>
	</tr> 
</thead> 
<tbody> 
THEAD;

	while( $row = pg_fetch_assoc( $result) )
	{
		echo "<tr>";
		$keylist = array( 'device_type', 'poll_id', 'data_type', 'oid', 'execution_interval' );
		foreach( $keylist as $key )
		{
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