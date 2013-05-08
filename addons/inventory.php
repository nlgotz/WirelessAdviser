<!--
NAME=Device Inventory Report
DESCRIPTION=Listing of all the devices and related data that have been imported into Wireless Adviser
-->

<?php

include( '../model/dbconn.inc' );

$query =  "SELECT *";
$query .= " FROM wirelessadviser.devices";
$query .= " ORDER BY devices.device_id";

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
	<title>Device Inventory Report</title>
	<link rel="stylesheet" type="text/css" media="screen" href="../cambium.css"/>
</head>

<body style="background-color:#FFFFFF;">

<h1>Device Inventory</h1>

<table id="myTable" class="tablesorter"> 
<thead> 
	<tr> 
		<th>Device ID</th>
		<th>Display Name</th> 
		<th>IP Address</th> 
		<th>Read Community</th>
		<th>Write Community</th>
		<th>Device Type</th> 
		<th>Device State</th>
		<th>Latitude</th>
		<th>Longitude</th>
		<th>Azimuth</th>
		<th>Height</th>
		<th>SNMP Version</th>
		<th>SNMP Port</th>
		<th>SNMP Ping (minutes)</th>
	</tr> 
</thead> 
<tbody> 
THEAD;

	while( $row = pg_fetch_assoc( $result) )
	{
		echo "<tr>";
		$keylist = array( 'device_id', 'display_name', 'ip_address', 'read_community', 'write_community', 'device_type', 'device_state', 'latitude', 'longitude', 'azimuth', 'height', 'snmp_version', 'snmp_port', 'snmp_ping' );
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