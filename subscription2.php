
<?php
session_start();
if (!isset($_SESSION["user"])) {
   header("Location: login.php");
}
?>










<!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <title>Add Accomodation Property </title>
      <link rel="stylesheet" href="style--dash-owner.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
    </head>
    <body>
        <div class="sidebar">
            <div class="logo"></div>
            <ul class="menu">
                <li class="active">
                    <a href="dash-owner.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="add-property.php">
                    <i class="fas fa-plus"></i>
                        <span>Add Property</span>
                    </a>
                </li>
                <li class="FAQ">
                    <a href="#">
                        <i class="fas fa-question-circle"></i>
                        <span>FAQ</span>
                    </a>
                </li>
                <li>
                    <a href="forgot-password.php">
                    <i class="fas fa-user-lock"></i>
                        <span>Change Password</span>
                    </a>
                </li>
                <li class="logout">
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="main--content-subs">
            <div class="header--wrapper">
                <div class="header--title">
                    <span>Add Accomodation </span>
                    <h2>Property</h2>
                </div>
                <div class="user--info">
                    <img src="./home-owner-icon.png" alt=""/>
                </div> 
            </div>
            <div class="card--container-subs">
                <h3 class="main--title"></h3>      
<div class="card--wrapper-subs">


<?php

$userId = $_SESSION["userID"];
$propertyID = $_SESSION['propertyID'];
$mysqli = require "database.php"; // Make sure this returns the mysqli object

// Check for pending payments
if ($checkQuery = $mysqli->prepare("SELECT * FROM `payment-transaction-tb` WHERE `PID` = ? AND `status` = ''")) {
    $checkQuery->bind_param("i", $propertyID);
    $checkQuery->execute();
    $result = $checkQuery->get_result();
    if ($result->num_rows > 0) {
        echo '<script>';
        echo 'alert("You have a pending payment for this property.");';
        echo 'window.location.href = "dash-owner.php";'; // Redirects to the dashboard page after the alert
        echo '</script>';
        $checkQuery->close();
        $mysqli->close();
        exit(); // Stop script execution after redirect
    }
    $checkQuery->close();
}

// Check for accepted payments
if ($checkQuery = $mysqli->prepare("SELECT * FROM `payment-transaction-tb` WHERE `PID` = ? AND `status` = 'accepted'")) {
    $checkQuery->bind_param("i", $propertyID);
    $checkQuery->execute();
    $result = $checkQuery->get_result();
    if ($result->num_rows > 0) {
        echo '<script>';
        echo 'alert("You have already purchased a subscription for this property.");';
        echo 'window.location.href = "dash-owner.php";'; // Redirects to the dashboard page after the alert
        echo '</script>';
        $checkQuery->close();
        $mysqli->close();
        exit(); // Stop script execution after redirect
    }
    $checkQuery->close();
}

$mysqli->close();
?>




<div class="pricing-table">
   
  <div class="plan-container-basic">
    <div class="plan">
      <div class="plan-header">BASIC</div>
      <div class="price">₱500/6Mos </div>
      <ul class="plan-features">
        <li><i class="fas fa-check"></i>Standard Listing</li>
        <li><i class="fas fa-check"></i>6 Months Validity</li>
        <li></i> </li>
        <li></i> </li>
        <li></i> </li>
  
     
    
      </ul>
      
    </div>


    <button class="signup" onclick="location.href='basic-payment.php';">SIGN UP</button>
  </div>
  
  <div class="plan-container">
    <div class="plan">
      <div class="plan-header">PREMIUM</div>
      <div class="price">₱1000/Yr</div>
      <ul class="plan-features">
        <li><i class="fas fa-check"></i>Featured Listing</li>
        <li><i class="fas fa-check"></i>Top of the List</li>
        <li><i class="fas fa-check"></i>Top Rated Badge</li>
        <li><i class="fas fa-check"></i>1 Year Validity</li>
      </ul>
    </div>
    <button class="signup" onclick="location.href='premium-payment.php';" >SIGN UP</button>
  </div>

</div>


      



</div>
</div>
</div>
</body>


<script>
  document.querySelectorAll('.signup').forEach(button => {
    button.addEventListener('mouseover', () => {
      button.closest('.plan-container').querySelector('.plan').style.boxShadow = '0 0 20px rgba(0, 0, 0, 0.2)';
    });
    button.addEventListener('mouseout', () => {
      button.closest('.plan-container').querySelector('.plan').style.boxShadow = '';
    });
  });
  </script>


</html>