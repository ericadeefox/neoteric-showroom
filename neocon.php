<?php

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');


//$db_host		= '67.52.55.18';
//$db_user		= 'showroom';
//$db_pass		= 'neova17';
//$db_database	= 'tz_members'; 

// Database config 
$db_host		= 'neoteric.cx04raar3xwq.us-west-2.rds.amazonaws.com';
$db_user		= 'neo_user';
$db_pass		= 'e.72hIwD$J';
$db_database	= 'neoteric'; 

$link = mysql_connect($db_host,$db_user,$db_pass, true) or die('Unable to establish a DB connection');

mysql_select_db($db_database,$link);

?>
