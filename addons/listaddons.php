<?php

echo <<<END
<table id="myTable" class="tablesorter"> 
<thead> 
	<tr> 
		<th>Name</th> 
		<th>Description</th> 
	</tr> 
</thead> 
<tbody> 
END;

$handle = opendir( 'addons' );
$addonList = array();
while ( ( $entry = readdir( $handle ) ) != false )
{
	if ( $entry != "." && $entry != ".." && $entry != "listaddons.php" )
	{
		// Read the file and parse out the interesting bit.  Pack into an array keyed by name
		// to support alphabetical sorts.  If the NAME and DESCRIPTION tags are not there, ignore it.
		$subject = file_get_contents( "addons/$entry" );
		if ( ( preg_match( '/(NAME=)(.*)/i', $subject ) == 1 ) && ( preg_match( '/(DESCRIPTION=)(.*)/i', $subject ) == 1 ) )
		{
			preg_match( '/(NAME=)(.*)/i', $subject, $name );
			preg_match( '/(DESCRIPTION=)(.*)/i', $subject, $description );
			$addonList[ $name[2] ] = array( $description[2], $entry );
		}
	}
}

uksort( $addonList, 'strcasecmp' );
foreach ($addonList as $key => $val) 
{
	echo '<tr><td><a href="addons/' . $val[1] . '" target="_blank">' . $key . '</a></td><td>' . $val[0]. '</td></tr>';
}
echo "</tbody>";
echo "</table>";

?>
