<?php
//  OpenEMR
//  MySQL Config

global $disable_utf8_flag;
$disable_utf8_flag = false;

//$host: Servicio de MySQL configurado en el arhcivo docker-compose.yml (ops-openemr-mysql) 
$host	= 'ops-openemr-mysql';
$port	= '3306';
$login	= 'openemr';
$pass	= 'openemr';
$dbase	= 'openemr';
$db_encoding	= 'utf8mb4';

$sqlconf = array();
global $sqlconf;
$sqlconf["host"]= $host;
$sqlconf["port"] = $port;
$sqlconf["login"] = $login;
$sqlconf["pass"] = $pass;
$sqlconf["dbase"] = $dbase;
$sqlconf["db_encoding"] = $db_encoding;

//$config = 1: OpenEMR ya está configurado.
//$config = 0: Si desea volver a configurar el servidor SQL, cambie la variable 'config' a ​​0 and re-run this script.

//////////////////////////
//////////////////////////
//////////////////////////
//////DO NOT TOUCH THIS///
$config = 0; /////////////
//////////////////////////
//////////////////////////
//////////////////////////
?>
