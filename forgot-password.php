

<?php
ob_start();
session_start();
if (isset($_SESSION["enterEmail"])) {
   header("Location: enterCode.php");
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">


</head>
<body>




  <div class="bod">
    <br><br><br><br>
    <div class="wrapper">
        <br>
      <div class="title-text">
        <div class="title login">Reset Password</div>
      </div>

      <div class="password-reset-message">
    Please enter your email address, and we will send you a code to verify your identity and proceed with password reset
  </div>


      <div class="form-container-login">
        
        <div class="form-inner">
            
          <form  class="forgot-password"  method="post">
       
    
  
            <div class="field">
                <input type="text" name="email" placeholder=" Email Address " required>

              </div>


<?php
require("PHPMailer/src/PHPMailer.php");
require("PHPMailer/src/SMTP.php");


if (isset($_POST["login"])) {
  
    $email = $_POST["email"];
    $_SESSION["email"] = $_POST["email"];
    $token = "";

  for ($i = 0; $i < 5; $i++) {
    $token .= random_int(0, 9); // Generate a random digit and concatenate it to the string
    }




    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);
    
    $mysqli = require_once "database.php"; // This should now correctly assign the MySQLi object to $mysqli

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo "<div style='background-color: #f44336; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>Email is not valid</div>";
      
     }


 else{        //checking if the email already registered
    $sql = "SELECT * FROM user_account WHERE username = '$email'";
    $result = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($result);
    if ($rowCount>0) {
           


    $sql = "UPDATE user_account
            SET reset_token= ?,
                reset_token_expires_at = ?
            WHERE username = ?";
    
    $stmt = $mysqli ->prepare($sql);
    $stmt->bind_param("sss", $token, $expiry, $email);
    $stmt->execute();

    


    $queryEmail = "SELECT `email` FROM `admin-tb` WHERE `Admin_Id`= '1' ";
    $emailResult = $mysqli->query($queryEmail);
    if ($emailResult) {
        if ($emailRow = $emailResult->fetch_assoc()) {
            $emailFrom = $emailRow['email'];
//mailer
    $mailTo = $_POST["email"];
    $body = $token;
    
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    
    $mail->SMTPDebug = 3;
    
    $mail->isSMTP();
    
    $mail->Host = "mail.smtp2go.com";
    
    $mail->SMTPAuth = true;
    
    $mail->Username = "dormna";
    $mail->Password = "dormnasmtp";
    
    $mail->SMTPSecure ="tls";
    
    $mail->Port = "8025";
    
    $mail->From = $emailFrom;
    $mail->FromName = "Dormna Admin";
    
    $mail->addAddress($mailTo, "CODE");
    
    $mail->isHTML(true);
    
    $mail->Subject= "Verification Code";
    $mail->Body = $body;
    $mail->AltBody ="PlainText";
    $mail->send();
    if(!$mail->send()){

      echo "Mailer Error:" . $mail->ErrorInfo;
    }

    else
    {
      echo "msg sent";
      $_SESSION["enterEmail"] = "yes";
      header("Location: enterCode.php");
      die();
    }

    

  }}



  }

  else{

    echo "<div style='background-color: #f44336; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>Email not registerd!</div>";
  }
      


}
}
ob_end_flush();
?>













<div class="signUp_btn">
            <button class="field_btn" name="login" href="forgot-password.php" >Send</button>
      </div>
          </form>
      </div>
      </div>
    </div>

  </div>
  
<script src="https://unpkg.com/scrollreveal"></script>
<script src="main.js"></script>
</body>
</html>
