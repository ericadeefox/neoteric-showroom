<?php
	/****h* manager/index.php
	* NAME
	*  index.php
	* FUNCTION
	*  This page redirects the user to the proper page if only the directory was found.
	* AUTHOR 
	*  Klynt Gerde
	* CREATION DATE
	*  June, 2004
	******
	*/

	/*************************** included files ****************************/
	include_once('neoteric_general.inc');
	/*************************** code			****************************/
	//redirect the user to manager.php
	softRedirect('/');	//quit after redirecting
	exit();
?>
