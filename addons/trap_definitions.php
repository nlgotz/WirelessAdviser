<!--
NAME=Trap Definitions Report
DESCRIPTION=Listing of all the trap definitions that have been imported into Wireless Adviser
-->

<?php

include( '../model/dbconn.inc' );

$query =  "SELECT *";
$query .= " FROM wirelessadviser.trap_definitions";
$query .= " ORDER BY device_type, name";

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
	<title>Trap Definitions Report</title>
	<link rel="stylesheet" type="text/css" media="screen" href="../cambium.css"/>
</head>

<body style="background-color:#FFFFFF;">

<h1>Trap Definitions</h1>

<table id="myTable" class="tablesorter"> 
<thead> 
	<tr> 
		<th>Device Type</th>
		<th>Name</th> 
		<th>Snmp Version</th> 
		<th>Description</th>
		<th>Severity</th>
		<th>Enterprise</th>
		<th>Generic Trap String</th>
		<th>Generic Trap</th>
		<th>Specific Trap</th>
		<th>SNMP Trap OID</th>
		<th>Varbinds</th>	
	</tr> 
</thead> 
<tbody> 
THEAD;

	while( $row = pg_fetch_assoc( $result) )
	{
		echo "<tr>";
		$keylist = array( 'device_type', 'name', 'snmp_version', 'description', 'severity', 'enterprise', 'generic_trap_string', 'generic_trap', 'specific_trap', 'snmp_trap_oid', 'varbinds' );
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