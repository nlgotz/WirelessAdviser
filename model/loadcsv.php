<?php

/**
 * Loads data from the csv file to the database table.  The file must be at ../../wabe/conf, 
 * and the name of the file must match the name of the database table.
 */
function loadTableFromCsv( $dbHandle, $table, $fileName )
{
	$dbTable = "wirelessadviser.$table";
	$fileRows = file( $fileName );
	$fileRowCount = count( $fileRows );

	if ( pg_query( $dbHandle, 'DELETE FROM ' . $dbTable ) !== FALSE )
	{
		if ( pg_copy_from( $dbHandle, $dbTable, $fileRows, "," ) == true )
		{
			$query = 'SELECT * FROM ' . $dbTable;
			$result = pg_query( $dbHandle, $query );
			$dbRowCount = pg_num_rows( $result );
			if ( $dbRowCount == $fileRowCount )
			{
				return "Success: Loaded data from $fileName to $dbTable";
			}
			else
			{
				return "ERROR: Problem loading data from $fileName ($fileRowCount entries) to $dbTable ($dbRowCount entries).";
			}
		}
		else
		{
			return "ERROR: Problem copying from $fileName to $dbTable. " . pg_last_error();
		}
	}
	else
	{
		return "ERROR: Problems deleting data from $dbTable. " . pg_last_error();
	}	
}


//
// Main script - connect to the database, recover the type, and execute.
include( 'dbconn.inc' );

$returnString = "";
if ( array_key_exists( 'type', $_GET ) )
{
	// Filename format will always be type.csv.  It's what all the scripts expect.
	$filename = "../../wabe/conf/" . $_GET["type"] . ".csv";
	
	// If we're not reloading we need to have a file uploaded - look for it and move it to the right spot.
	if ( array_key_exists( 'reload', $_GET ) == false )
	{
		if ( count( $_FILES ) == 0 )
		{
			$returnString = "ERROR: No file was specified.";
		}
		else if ( $_FILES["csvFile"]["error"] > 0 )
		{
			$returnString = "ERROR: Trouble uploading file - " . $FILES["csvFile"]["error"];
		}
		else
		{
			//$filename = "../../wabe/conf/" . $_FILES["csvFile"]["name"];  // Old version - keep around for posterity.
			// Make a backup.
			copy( $filename, $filename . ".bak" );
			if ( move_uploaded_file( $_FILES["csvFile"]["tmp_name"], $filename ) == false )
			{
				$returnString = "ERROR: Trouble moving " . $_FILES["csvFile"]["name"] . " into the <application-root>/wabe/conf folder.";
			}
		}
	}
	
	// Now load (or reload) the file if there are not errors at this point.
	if ( strncmp( $returnString, "ERROR", 4 ) != 0 )
	{
		$returnString = loadTableFromCsv( $db, $_GET['type'], $filename );
		if ( strncmp( $returnString, "ERROR", 4 ) == 0 )
		{
			$returnString .= "\n\n\nAttempting to reload the original file.";
			// Restore the original file and try to reload.
			copy( $filename . ".bak", $filename );
			$secondTry = loadTableFromCsv( $db, $_GET['type'], $filename );
			$returnString .= "\n\n" . $secondTry;
		}
	}
}
else
{
	$returnString = "ERROR: Type not specified.";
}

echo $returnString;

pg_close( $db );
?>
