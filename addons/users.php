<!--
NAME=User Accounts
DESCRIPTION=Listing of all the users and their account types that have been imported into Wireless Adviser
-->

<?php

include( '../model/dbconn.inc' );

$query =  "SELECT *";
$query .= " FROM wirelessadviser.users";
$query .= " ORDER BY user_id";

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
	<title>Users</title>
	<link rel="stylesheet" type="text/css" media="screen" href="../cambium.css"/>
</head>

<body style="background-color:#FFFFFF;">

<h1>User Accounts</h1>

<table id="myTable" class="tablesorter"> 
<thead> 
	<tr> 
		<th>User ID</th>
		<th>Account Type</th> 
	</tr> 
</thead> 
<tbody> 
THEAD;

	while( $row = pg_fetch_assoc( $result) )
	{
		echo "<tr>";
		$keylist = array( 'user_id', 'account_type' );
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