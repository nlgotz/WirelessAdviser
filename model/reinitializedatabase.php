<?php
include( 'dbconn.inc' );

function runSqlFile( $dbHandle, $fileName  )
{
	$fileQuery = file_get_contents( $fileName );

	if ( !pg_query( $dbHandle, $fileQuery ) )
	{
		return "ERROR: Problem loading $fileName into " . pg_dbname( $dbHandle ) . " - " . pg_last_error();
	}
	else
	{
		return "Successfully loaded data from $fileName into " . pg_dbname( $dbHandle );
	}

}
$returnString = runSqlFile( $db, "../../wabe/scripts/init_db.sql" );
$returnString .= "\n\n" .  runSqlFile( $db, "../../wabe/scripts/rrs/rrs.sql" );
$returnString .= "\n\n" . runSqlFile( $db, "../../wabe/scripts/rrs/util_functions.sql" );
$returnString .= "\n\n" . runSqlFile( $db, "../../wabe/scripts/rrs/rrs_functions.sql" );
$returnString .= "\n\n" . runSqlFile( $db, "../../wabe/scripts/rrs/rrs_views.sql" );
echo $returnString;

pg_close( $db );
?>