<?php
include( 'dbconn.inc' );

echo "Database connection to $dbname succeeded.";

pg_close( $db );
?>