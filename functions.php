<?php

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');

include_once('class.phpmailer.php');

function checkEmail($str)
{
	return preg_match("/^[\.A-z0-9_\-\+]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $str);
}


function send_mail($from,$to,$subject,$body)
{
	include_once('include/class.phpmailer.php');
	include_once ('include/neoteric_general.inc');
	include_once('include/neoteric_globals.inc.php');
	
	$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
	$mail->IsSMTP(); // telling the class to use SendMail transport

		$mail->AddAddress($to);
		$mail->SetFrom($from);
		$mail->Subject = $subject;
		$mail->MsgHTML($body);
		$mail->Send();
		
	$headers = '';
	$headers .= "From: $from\n";
	$headers .= "Reply-to: $from\n";
	$headers .= "Return-Path: $from\n";
	$headers .= "Message-ID: <" . md5(uniqid(time())) . "@" . $_SERVER['SERVER_NAME'] . ">\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Date: " . date('r', time()) . "\n";

	mail($to,$subject,$body,$headers);
}
?>
