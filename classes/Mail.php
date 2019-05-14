<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//Load Composer's autoloader
require 'vendor/autoload.php';

class Mail {
	public static function sendMail( $subject, $body, $address ) {
		$mail = new PHPMailer( true );                              // Passing `true` enables exceptions
		try {
			//Server settings
			//$mail->SMTPDebug = 2;                                 // Enable verbose debug output
			$mail->isSMTP();									  // Set mailer to use SMTP
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted

			$mail->Host = 'smtp.gmail.com';  					  // Specify main and backup SMTP servers
			$mail->Port = 465;                                    // TCP port to connect to
			$mail->isHTML(true);                                  // Set email format to HTML


			$mail->Username = 'socialenigma93@gmail.com';           // SMTP username
			$mail->Password = 'your_password';                           // SMTP password

			//Recipients
			$mail->setFrom('no-reply@socialproject.com', 'Mailer');
			$mail->addAddress( $address );   // Add a recipient
			//$mail->addAddress('ellen@example.com');               // Name is optional
			//$mail->addReplyTo('info@example.com', 'Information');
			//$mail->addCC('cc@example.com');
			//$mail->addBCC('bcc@example.com');

			//Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			//Content

			$mail->Subject = $subject;
			$mail->Body    = $body;
			//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			$mail->send();
		} catch (Exception $e) {
			echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
		}
	}
}