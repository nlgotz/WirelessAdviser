<!--
NAME=Device Uptime Report
DESCRIPTION=Listing of all the devices and their latest polled uptime.
-->

<?php
include( '../model/dbconn.inc' );

$query = 'SELECT devices.device_id, display_name, ping_type, response_time, execution_time';
$query .= ' FROM wirelessadviser.status_polling_history sph, wirelessadviser.devices';
$query .= ' WHERE devices.device_id = sph.device_id';
$query .= ' AND execution_time = ';
$query .= ' (SELECT MAX( execution_time )';
$query .= ' FROM wirelessadviser.status_polling_history sph2';
$query .= ' WHERE sph.device_id = sph2.device_id )';
$query .= ' ORDER BY device_id';

$result = pg_query( $db, $query );
if ( !$result )
{
	echo "ERROR:Problem with SQL query";
	exit;
}
else
{
	echo <<<THEAD
<html>
<head>
	<title>Device Uptime</title>
	<link rel="stylesheet" type="text/css" media="screen" href="../cambium.css"/>
</head>
<body style="background-color:#FFFFFF;">
<h1>Device Uptime</h1>
<table id="myTable" class="tablesorter"> 
<thead> 
	<tr> 
		<th>Device Id</th> 	
		<th>Device Name</th>
		<th>Uptime</th>
		<th>Last Polled</th>
	</tr> 
</thead> 
<tbody>
THEAD;

	while( $row = pg_fetch_assoc( $result) )
	{
		$raw = $row[ 'response_time' ] / 100;
		$seconds = $raw % 60;
		$raw /= 60;
		$minutes = $raw % 60;
		$raw /= 60;
		$hours = $raw % 24;
		$raw /= 24;
		$days = round( $raw );
		
		echo '<tr>';
		echo '<td>' . $row[ 'device_id' ] . '</td>';		
		echo '<td nowrap="nowrap">' . $row[ 'display_name' ] . '</td>';
		echo "<td>$days days, $hours hours, $minutes minutes, $seconds seconds.</td>";	
		echo '<td>' . $row[ 'execution_time' ] . '</td>';		
		echo '</tr>';
	}

	echo "</tbody>";	
	echo "</table>";		
	echo "</body></html>";
}
pg_close( $db );
?>