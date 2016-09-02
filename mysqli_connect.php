<?php # Script 8.2 - mysqli_connect.php
/*This file contains the database access information.
This file also establishes a connection to MySQL
and selects the database.*/
// Set the database access information as constants:
DEFINE ('DB_USER', 'root');
DEFINE ('DB_PASSWORD', '');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'ssd');
// Make the connection:
$dbc = @mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Ha ocurrido un error... no se pudo conectar: ' . mysqli_connect_error() );

// corrige problemas de acentos
mysqli_set_charset($dbc, 'utf8');
?>
