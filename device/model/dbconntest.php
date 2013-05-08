<?php

echo '<html><body>';
include( 'dbconn.inc' );

echo '<h1>DB Connection successful</h1>';

$serverinfo = explode( ':', $dbconf->server["value"] );
echo "<li>" . $dbconf->server["value"] . "(server=$serverinfo[0], port=$serverinfo[1])" . "</ul>";
echo "<li>" . $dbconf->database["value"] . "</ul>";
echo "<li>" . $dbconf->username["value"] . "</ul>";
echo "<li>" . $dbconf->password["value"] . "</ul>";

echo '</body></html>';
pg_close( $db );