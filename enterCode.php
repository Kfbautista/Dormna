<?php
session_start();
if (!isset($_SESSION["enterEmail"])) {
   header("Location: forgot-password.php");
}


if (isset($_SESSION["changeOrResetPass"])) {
   header("Location: new-password.php");
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
        <div class="title login">Verification</div>
      </div>

      <div class="password-reset-message">
      Please check your email for the verification code we've sent. Enter this code in the field provided on our website to submit your request and proceed with resetting your password
  </div>
      <div class="form-container-login">
        
        <div class="form-inner">
            
          <form  class="enterCode"  method="post">
       
    
  
            <div class="field">
                <input type="text" name="code" placeholder="Verification Code" required>
              </div>

              <?php

if (isset($_POST['verify'])) {
    $inputCode = $_POST['code']; // The code user inputs for verification
    $email = $_SESSION["email"];
    $mysqli = require_once "database.php";
    // Fetch the stored token and expiry from the database
    $stmt = $mysqli->prepare("SELECT reset_token, reset_token_expires_at FROM user_account WHERE username = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($inputCode == $row['reset_token'] && new DateTime() < new DateTime($row['reset_token_expires_at'])) {
          $_SESSION["changeOrResetPass"] = "yes";
          header("Location: new-password.php"); // Redirect to new password page
            exit();
        } else {
            $error = "";
            echo "<div style='background-color: #f44336; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>Invalid or expired verification code</div>";
           
        }
    }
}

?>















<div class="signUp_btn">
 <button class="field_btn" name="verify">Verify</button>
      </div>
          </form>
      </div>
      </div>
    </div>


    <br><br><br><br><br>
  </div>


  
<script src="https://unpkg.com/scrollreveal"></script>
<script src="main.js"></script>
</body>
</html>
