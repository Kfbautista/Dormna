<?php
session_start();
if (isset($_SESSION["admin"])) {
    header("Location: dash-admin.php");
 }
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login </title>
  <link rel="stylesheet" href="styles.css">

</head>
<body>




  <div class="bod">

    <div class="wrapper">
      <div class="title-text">
        <div class="title login">Admin Login</div>
      </div>
      <div class="form-container-login">
        <div class="slide-controls" style= "display:none;">

       
          <div class="slider-tab"></div>
        </div>
        <div class="form-inner">


          <form  class="login" action="admin-login.php" method="post">
            <div class="field">
              <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="field">
              <input type="password" name="password" placeholder="Password" required>
            </div>



        <?php
        if (isset($_POST["login"])) {
           $username = $_POST["username"];
           $password = $_POST["password"];

         

            require_once "database.php";
    
            $sql = "SELECT * FROM `admin-tb` WHERE `username_admin` = '$username'";
            $result = mysqli_query($conn, $sql);
            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
            if ($user) {
                if (password_verify($password, $user["password_admin"])) {
                    session_start();
                    $_SESSION["admin"] = "yes";
                    header("Location: dash-admin.php");
                    die();
                }else{
                   echo "<div style='background-color: #f44336; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>Password does not match</div>";

                }
            }else{
            
              echo "<div style='background-color: #f44336; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>Email does not match</div>";

            }
        }
        ?>

            <button class="field_btn" name="login">Login</button>
          </form>

          
          


          </form>
        </div>
      </div>
    </div>
  </div>



<script src="https://unpkg.com/scrollreveal"></script>
<script src="main.js"></script>
</body>
</html>
