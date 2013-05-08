<!--
NAME=Policy Definitions Report
DESCRIPTION=Listing of all the policy definitions that have been imported into Wireless Adviser
-->

<?php

include( '../model/dbconn.inc' );

$query =  "SELECT *";
$query .= " FROM wirelessadviser.policies";
$query .= " ORDER BY policy_name";

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
	<title>Policy Definitions</title>
	<link rel="stylesheet" type="text/css" media="screen" href="../cambium.css"/>
</head>

<body style="background-color:#FFFFFF;">

<h1>Policy Definitions</h1>

<table id="myTable" class="tablesorter"> 
<thead> 
	<tr> 
		<th>Policy Name</th>
		<th>Policy Class</th> 
		<th>Last Execution</th> 
		<th>Next Execution</th>
		<th>Execution Interval</th>
	</tr> 
</thead> 
<tbody> 
THEAD;

	while( $row = pg_fetch_assoc( $result) )
	{
		echo "<tr>";
		$keylist = array( 'policy_name', 'policy_class', 'last_execution', 'next_execution', 'execution_interval' );
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