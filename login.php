
<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: dash-owner.php");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
 <link
      href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css"
      
      rel="stylesheet"
    />



  <link rel="stylesheet" href="styles.css">




  <style>
   

  
  </style>
  
  
</head>
<body>



          <nav>
      <div class="nav__bar">
        <div class="nav__header">
            
            
          <div class="logo nav__logo">
              <img src="assets/SLSU-LOGO.png" style="width:60px; height:60px;" onload="this.style.width = this.style.height = window.innerWidth < 400 ? '40px' : '60px';" onresize="this.style.width = this.style.height = window.innerWidth < 400 ? '40px' : '60px';" />
                <span id="slsu" style="font-size: .7rem;">Southern Luzon State University<br /></span>

           

            <hr width="1" size="70px" style="0 auto" />
     
            <img src="assets/dormna_logo.png" style="width:160px;height:60px;" onload="this.style.height = window.innerWidth < 400 ? '40px' : '60px';" onresize="this.style.height = window.innerWidth < 400 ? '40px' : '60px';" />

           
          </div>
          
          <div class="nav__menu__btn" id="menu-btn">
            <i class="ri-menu-line"></i>
          </div>
        </div>
               <ul class="nav__links" id="nav-links">
              <li><a href="index.php#home">Home</a></li>
              
              <li><a href="index.php#Dormitories">Dormitory</a></li>
               <li><a href="index.php#Boarding_Houses">Boarding House</a></li>
              <li><a href="list.php">Search</a></li>
              <li><a href="index.php#about">About Us</a></li> 
       
            </ul>
        
        
      </div>
    </nav>
  <div class="bod">
      



    <div class="wrapper">
      <div class="title-text">
        <div class="title login">Login Form</div>
        <div class="title signup">Signup Form</div>
      </div>
      <div class="form-container-login">
        <div class="slide-controls">
          <input type="radio" name="slide" id="login" checked>
          <input type="radio" name="slide" id="signup">
          <label for="login" class="slide login">Login</label>
          <label for="signup" class="slide signup">Signup</label>
          <div class="slider-tab"></div>
        </div>
        <div class="form-inner">


          <form  class="login" action="login.php" method="post">
            <div class="field">
              <input type="text" name="email" placeholder="Email Address" required>
            </div>
            <div class="field">
              <input type="password" name="password" placeholder="Password" required>
            </div>



        <?php
        if (isset($_POST["login"])) {
           $email = $_POST["email"];
           $password = $_POST["password"];

         

            require_once "database.php";
    
            $sql = "SELECT * FROM user_account WHERE username = '$email'";
            $result = mysqli_query($conn, $sql);
            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
            if ($user) {
                if (password_verify($password, $user["password"])) {
                    session_start();
                    $_SESSION["user"] = "yes";
                    $_SESSION["userID"] = $user['UID'];
                    header("Location: dash-owner.php");
                    die();
                }else{
                   echo "<div style='background-color: #f44336; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>Password does not match</div>";

                }
            }else{
            
              echo "<div style='background-color: #f44336; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>Email does not match</div>";

            }
        }
        ?>

   

            <div class="pass-link"><a href="forgot-password.php">Forgot password?</a></div>
            <button class="field_btn" name="login">Login</button>
            <div class="signup-link">Not a member? <a href="#">Signup now</a></div>
          </form>




          <form action="login.php#signup" method="post" class="signup" id="signup">
            <div class="field">
              <input type="text" name="email" placeholder="Email Address" required>
            </div>
            <div class="field">
              <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="field">
              <input type="password" name="repeat_password" placeholder="Confirm password" required>
         </div>

         
<?php
  if (isset($_POST["submit"])) {
  $email = $_POST["email"];
  $password = $_POST["password"];
  $passwordRepeat = $_POST["repeat_password"];
  $passwordHash = password_hash($password, PASSWORD_DEFAULT);
  $errors = array();
  $mysqli = require_once "database.php";

  // Checking if the email is already registered
  $query = "SELECT * FROM `user_account` WHERE `username` = ?";
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $rowCount = $result->num_rows;

  if ($rowCount > 0) {
      echo "<div style='background-color: #f44336; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>Email already exists!</div>";
  } else {
      if (empty($email) || empty($password) || empty($passwordRepeat)) {
          array_push($errors, "All fields are required");
      } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          echo "<div style='background-color: #f44336; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>Email is not valid</div>";
      } else if (strlen($password) < 8) {
          echo "<div style='background-color: #f44336; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>Password must be at least 8 characters long</div>";
      } else if ($password !== $passwordRepeat) {
          echo "<div style='background-color: #f44336; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>Password does not match</div>";
      } else {
          $sql = "INSERT INTO `user_account` (`username`, `password`) VALUES (?, ?)";
          $stmt = $mysqli->prepare($sql);
          if ($stmt) {
              $stmt->bind_param("ss", $email, $passwordHash);
              $stmt->execute();
              echo "<div class='alert alert-success' style='background-color: #006400; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>You are registered successfully.</div>";
          } else {
              die("Something went wrong with preparing the statement");
          }
      }
  }
  $stmt->close();
  $mysqli->close();
}
?>



<div class="signUp_btn">
            <button class="field_btn" name="submit" >Signup</button>
      </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  
     <footer class="footer">
      <div class="section__container footer__container">
        <div class="footer__col">
          <div class="logo footer__logo">
              <img src="assets/SLSU-LOGO.png" style="width:50px;height:50px;" />  <span style=" font-size: .5rem;">Southern Luzon State University<br /></span>
          <hr width="1" size="50px" style="0 auto" />
          <img src="assets/dormna_logo.png" style="width:150px;height:50px;" />
           <!-- <span>DormNa</span> -->
          </div>
          <p class="section__description">
           
          </p>
          <ul class="footer__socials">
            
         
            <li>
              <a href="#"><i class="ri-facebook-fill"></i></a>
            </li>
            
          </ul>
        </div>
        <div class="footer__col">
          <h4>Quicklinks</h4>
            
          <div class="footer__links">
            <li><a href="#Dormitories">Dormitory Listings</a></li>
            <li><a href="#Boarding_Houses">Boarding House Listings</a></li>
            <li><a href="list.php">Search for Accommodations</a></li>
            <li><a href="login.php">Login for Accomodation Owners</a></li>
         
          </div>
        </div>
        <div class="footer__col">
          <h4>Contact Us</h4>
          <div class="footer__links">
            <li>
              <span><i class="ri-phone-fill"></i></span>
              <div>

<?php
                            
                            $mysqli = require "database.php";

                
                            $query = "SELECT `email` FROM `admin-tb` WHERE `Admin_Id`= '1' ";
                            $Result = $mysqli->query($query);
                            if ($Result) {
                                if ($Row = $Result->fetch_assoc()) {
                                    $email = $Row['email'];

                                }}
                            $mysqli->close();
                            ?>
  
                <h5>Phone Number</h5>
                <p><?php echo $contactnum  ?></p>
              </div>
            </li>
            <li>
              <span><i class="ri-record-mail-line"></i></span>
              <div>
                <h5>Email</h5>
                <p><?php echo $email ?> </p>
              </div>
            </li>
            <li>
              <span><i class="ri-map-pin-2-fill"></i></span>
              <div>
                <h5>Location</h5>
                <p>Lucban Quezon, Philippines</p>
              </div>
            </li>
          </div>
        </div>
      </div
          <br><br><br><br><br><br><br><br><br>
      <div class="footer__bar">
        Copyright © 2024 DormNa. All rights reserved.
      </div>
      
  
    </footer>
  <script>


   // JavaScript to handle form slide

  // JavaScript to handle form slide
  const loginText = document.querySelector(".title-text .login");
  const loginForm = document.querySelector("form.login");
  const loginBtn = document.querySelector("label.login");
  const signupBtn = document.querySelector("label.signup");
  const signupLink = document.querySelector("form .signup-link a");
  const signUpButton = document.querySelector('.signUp_btn button');

  // Check for hash in the URL on load to keep the form state
  window.addEventListener('load', () => {
    if (window.location.hash === '#signup') {
      showSignupForm();
    }
  });

  // Show signup form without changing the page state
  function showSignupForm() {
    loginForm.style.marginLeft = "-50%";
    loginText.style.marginLeft = "-50%";
  }

  // Set up click event for signup button
  signupBtn.onclick = () => {
    window.location.hash = 'signup';
    showSignupForm();
  };

  // Set up click event for login button
  loginBtn.onclick = () => {
    window.location.hash = 'login';
    loginForm.style.marginLeft = "0%";
    loginText.style.marginLeft = "0%";
  };

  // Set up click event for the link to the signup form
  signupLink.onclick = () => {
    signupBtn.click();
    return false;
  };

  // Set up the form submission using AJAX
  document.querySelector('.signup').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent traditional form submission

    let formData = new FormData(this);

    fetch('login.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(data => {
      console.log(data);
      // Optionally do something with the response
    })
    .catch(error => {
      console.error('Error:', error);
    });

    // Keep the signup form visible
    showSignupForm();
  });

  // Ensure that the signup form stays visible after clicking the signup button
  signUpButton.addEventListener('click', function() {
    window.location.hash = 'signup';
    showSignupForm();
  });

</script>


  </script>


<script src="https://unpkg.com/scrollreveal"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.getElementById('menu-btn');
    const navLinks = document.getElementById('nav-links');

    menuBtn.addEventListener('click', function() {
      navLinks.classList.toggle('open');
    });
  });
</script>
 <script>
              function adjustFontSize() {
                var element = document.getElementById('slsu');
                element.style.fontSize = window.innerWidth < 400 ? '.5rem' : '.7rem';
              }
            
              // Attach the function to both onload and onresize events
              window.onload = adjustFontSize;
              window.onresize = adjustFontSize;
 </script>
</body>
</html>
