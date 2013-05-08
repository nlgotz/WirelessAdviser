<!--
NAME=Device Status Report
DESCRIPTION=Listing of all the devices and their status poll history
-->

<?php
include( '../model/dbconn.inc' );

$query = 'SELECT devices.device_id, display_name, ping_type, response_time, execution_time';
$query .= ' FROM wirelessadviser.status_polling_history, wirelessadviser.devices';
$query .= ' WHERE devices.device_id = status_polling_history.device_id';
$query .= ' ORDER BY display_name, execution_time';

$result = pg_query( $db, $query );
if ( !$result )
{
	echo "ERROR:Problem with SQL query";
	exit;
}
else
{
	echo '<html><head><title>Device Status Chart</title><link rel="stylesheet" type="text/css" media="screen" href="../cambium.css"/></head><body style="background-color:#FFFFFF;">';

	echo "<h1>Device Status Chart</h1>";
	
	$deviceIdToNameMap = array();
	$deviceIdHistory = array();
	$deviceUpDownHits = array();
	
	while( $row = pg_fetch_assoc( $result) )
	{
		$state = 1;
		$deviceIdToNameMap[ $row['device_id' ] ] = $row['display_name'];
		if ( $row['response_time'] == -1 )
		{
			$state = 0;
		}
		if ( array_key_exists( $row['device_id'], $deviceIdHistory ) == false )
		{
			$deviceIdHistory[ $row['device_id' ] ] = array();
		}
		array_push( $deviceIdHistory[ $row['device_id' ] ], $state );
		
		if ( array_key_exists( $row['device_id'], $deviceUpDownHits ) == false )
		{
			$deviceUpDownHits[ $row['device_id' ] ] = array();
		}
		
		$deviceUpDownHits[ $row['device_id'] ][ $state ]++;
	}

	echo <<<THEAD
<table id="myTable" class="tablesorter"> 
<thead> 
	<tr> 
		<th>Device Id</th> 
		<th>Device Name</th>
		<th>Percentage Pass</th>
		<th>Percentage Fail</th>
	</tr> 
</thead> 
<tbody>
THEAD;
	
	foreach( $deviceIdHistory as $key=>$stateList )
	{
		echo '<tr><td>' . $key . '</td><td nowrap="nowrap">' . $deviceIdToNameMap[$key] . "</td>";
		$totalPass = $deviceUpDownHits[ $key ][1];
		$totalFail = $deviceUpDownHits[ $key ][0];		
		echo "<td>" . round( ($totalPass / ($totalPass + $totalFail))* 100, 2 ) . "</td>";
		echo "<td>" . round( ($totalFail / ($totalPass + $totalFail))* 100, 2 ) . "</td>";		
		
		foreach( $stateList as $time=>$state )
		{
			if ( $state == 0 )
			{		
				echo '<td style="background-color:#FF0000;"></td>';
			}
			else
			{
				echo '<td style="background-color:#00FF00;"></td>';
			}
		}
		echo "</tr>";
	}
}

	echo "</table>";	
	echo "</tbody>";	
	echo "</body></html>";
pg_close( $db );
?>