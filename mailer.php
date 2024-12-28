<?php
require("PHPMailer/src/PHPMailer.php");
require("PHPMailer/src/SMTP.php");




$mailTo = "johnbenedick93.abadilla@gmail.com";
$body = "12345";

$mail = new PHPMailer\PHPMailer\PHPMailer();

$mail->SMTPDebug = 3;

$mail->isSMTP();

$mail->Host = "mail.smtp2go.com";

$mail->SMTPAuth = true;

$mail->Username = "dormna";
$mail->Password = "dormnasmtp";

$mail->SMTPSecure ="tls";

$mail->Port = "2525";

$mail->From = "kfdcbautista@slsu.edu.ph";
$mail->FromName = "Dormna Admin";

$mail->addAddress($mailTo, "CODE");

$mail->isHTML(true);

$mail->Subject= "Verification Code";
$mail->Body = $body;
$mail->AltBody ="PlainText";


if(!$mail->send()){

    echo "Mailer Error: ". $mail->ErrorInfo;


}
else{

    echo "Message has been sent";
}




?>