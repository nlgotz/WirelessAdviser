<!--
NAME=Link Definitions Report
DESCRIPTION=Listing of all the link definitions that have been configured into Wireless Adviser
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
$query .= " FROM wirelessadviser.links";
$query .= " ORDER BY link_id";

$result = pg_query( $db, $query );
if ( !$result )
{
	echo "ERROR:Problem with Link SQL query.";
	exit;
}
else
{
	echo <<<THEAD
<html>
<head>
	<title>Link Definitions</title>
	<link rel="stylesheet" type="text/css" media="screen" href="../cambium.css"/>
</head>

<body style="background-color:#FFFFFF;">

<h1>Link Definitions</h1>

<table id="myTable" class="tablesorter"> 
<thead> 
	<tr> 
		<th>Link Id</th>
		<th>Link Name</th> 
		<th>Parent Device ID (Name)</th>
		<th>Child Device ID (Name)</th>
	</tr> 
</thead> 
<tbody> 
THEAD;

	while( $row = pg_fetch_assoc( $result) )
	{
		echo "<tr>";
		$keylist = array( 'link_id', 'display_name', 'parent_id', 'child_id' );
		foreach( $keylist as $key )
		{
			if ( strcmp( $key, 'parent_id' ) == 0 || strcmp( $key, 'child_id' ) == 0 ) 
			{
				$row[ $key ] = $row[ $key ] . '   (' . $deviceId2NameMap[ $row[ $key ] ] . ')';
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