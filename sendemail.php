<div id="content">
<?php
	/********************* included files ****************************/
	include_once('include/class.phpmailer.php');
	include_once ('include/neoteric_general.inc');
	include_once('include/neoteric_globals.inc.php');

	$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
	$mail->IsSMTP(); // telling the class to use SendMail transport

	$url =  urldecode(getValue('pageToPrint') . '&offset=5&NEOPASSWD=' . MD5(NEOPASSWD));
	try {
		$mail->AddAddress(getValue('to'));
		$mail->SetFrom(getValue('from'));
		$mail->Subject = getValue('subject');
		$mail->MsgHTML(getValue('message'));

		if(!file_exists(EMAIL_TMP)) mkdir(EMAIL_TMP);

		$basefile = $file = 'srfax_html2pdf';
		while(file_exists(EMAIL_TMP . '/' . $file . '.html')) $file = $basefile . ($index = rand(0,10000));
		//while(file_exists(EMAIL_TMP . '/' . $file . '.html')) $file = $basefile . ($index = 2261);

		$htmlfile = EMAIL_TMP . '/' . $file . '.html';
		$pdffile = EMAIL_TMP . '/' . $file . '.pdf';
		$fh = fopen($url, "rb");
		$html = '';
		$html = implode('', file($url));
		fclose($fh);

		$fh = fopen($htmlfile, 'w');
		fwrite($fh, $html);
		fclose($fh);

		$cmd = "convert -antialias -contrast -density 300 $htmlfile $pdffile";
		if(file_exists($htmlfile))
			exec($cmd);
		else
			throw new Exception('HTML file not created');

		$mail->AddAttachment($pdffile);      // attachment
		$mail->Send();
		echo "The email was sent successfully";
	} catch (phpmailerException $e) {
		echo "The email was not sent successfully<hr/>";
		echo $e->errorMessage(); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
		echo "The email was not sent successfully<hr/>";
		echo $e->getMessage(); //Boring error messages from anything else!
	}
?>
</div>
