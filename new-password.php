<?php
session_start();
if (!isset($_SESSION["changeOrResetPass"])) {
   header("Location: forgot-password.php");
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Password</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">


</head>
<body>




  <div class="bod">

    <div class="wrapper">

      <div class="title-text">
        <div class="title login">Reset Password</div>
      </div>

      <div class="password-reset-message">
      You're all set to create a new password. Please enter your new password below to complete the reset process
  </div>

      <div class="form-container-login">
        
        <div class="form-inner">
            
          <form  class="forgot-password"  method="post">
       
    
  
            <div class="field">
              <input type="password" name="password" placeholder="New Password" required>
            </div>

            <div class="field">
                <input type="password" name="repeat_password" placeholder="Confirm New Password" required>
              </div>


              <?php
if (isset($_POST["submit"])) {

  $email = $_SESSION["email"];

  $password = $_POST["password"];
  $passwordRepeat = $_POST["repeat_password"];
  
  $passwordHash = password_hash($password, PASSWORD_DEFAULT);
  $errors = array();


  if ($password!==$passwordRepeat) {
    array_push($errors,"");
    echo "<div style='background-color: #f44336; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>Password does not match</div>";
    }

else{
    $mysqli = require_once "database.php";

    $sql = "UPDATE user_account
            SET password= ?
            WHERE username = ?";
    
    $stmt = $mysqli ->prepare($sql);
    $stmt->bind_param("ss", $passwordHash, $email);
    $stmt->execute();


  
    echo "<div style='background-color: #006400; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>Password UPDATED</div>";
    // Clear the email from the session after it's no longer needed



session_destroy();
  

echo '<a href="login.php" style="color: #2a9d8f; text-decoration: none; font-weight: bold;">Go to Login</a>';




}




}
?>


<div class="signUp_btn">
            <button class="field_btn" name="submit" >Submit</button>
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
