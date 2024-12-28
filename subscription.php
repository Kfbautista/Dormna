<?php
session_start();
if (!isset($_SESSION["user"])) {
   header("Location: login.php");
}
?>





<?php

$userId = $_SESSION["userID"];
$propertyID = $_SESSION['propertyID'];
$mysqli = require "database.php"; // Make sure this returns the mysqli object


if (isset($_POST["free"])) {
 

  // Check if a free trial has already been activated
  $query = "SELECT * FROM `property-info` WHERE `PID` = ? AND `user_id` = ? AND `free_trial` = 'activated'";
  $stmt = $mysqli->prepare($query);
  if ($stmt === false) {
      die("MySQL prepare error: " . $mysqli->error);
  }
  $stmt->bind_param("ii", $propertyID, $userId);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
      echo '<script>';
      echo 'alert("You have already activated a free trial. Please consider subscribing to one of our premium plans for continued benefits.");';
      echo 'window.location.href = "dash-owner.php";';
      echo '</script>';
  } else {
      $expiry = date('Y-m-d H:i:s', strtotime('+7 days'));
      $freeSubs = 'basic'; // Assuming 'basic' is the subscription type for a free trial
      $freeTrial = 'activated';

      $updateSql = "UPDATE `property-info` SET `subscription` = ?, `expiry_date` = ?, `free_trial` = ? WHERE `user_id` = ? AND `PID` = ?";
      $updateStmt = $mysqli->prepare($updateSql);
      if ($updateStmt === false) {
          die("MySQL prepare error: " . $mysqli->error); // Proper error handling for prepared statement
      }

      $updateStmt->bind_param("sssii", $freeSubs, $expiry, $freeTrial, $userId, $propertyID);

      if ($updateStmt->execute()) {

        $freeTrialNotif= "Congratulations! You have successfully activated your 7-day free trial!";

         $notifSQL =  "INSERT INTO `notification` (`UID`, `notification_msg`) VALUES (?, ?)";
         $notifStmt = $mysqli->prepare($notifSQL);

         $notifStmt->bind_param("is", $userId ,  $freeTrialNotif );
         $notifStmt->execute();
         








          echo '<script>';
          echo 'alert("Congratulations! You have successfully activated your 7-day free trial!");';
          echo 'window.location.href = "dash-owner.php";'; // Redirects to the dashboard page after the alert
          echo '</script>';
      } else {
          echo "Error updating record: " . $updateStmt->error;
      }

      $updateStmt->close();
  }

  $stmt->close();
}



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




<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Subscription Plans</title>
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
<link rel="stylesheet" href="style1.css">
</head>
<body>

<h1>Choose the best subscription plan for you!</h1>
    <p>
  
    </p>

    <div class="pricing">
      <div class="plan">
      <h2>Free Trial</h2>
        <div class="price">FREE</div>
        <ul class="features">
          <li><i class="fas fa-check-circle"></i> Standard Listing</li>
          <li><i class="fas fa-check-circle"></i> 7 Days Validity</li>      
          <li><i class="fas fa-times-circle"></i> No priority support</li>
        </ul>
        <form action="subscription.php" method="POST" enctype="multipart/form-data">
        <button class = "field_btn" name="free">Get Started</button>
      </form>
      </div>
      <div class="plan popular">
        <span>Best Value</span>
        <h2>Premium</h2>
        <div class="price">₱200/month</div>
        <ul class="features">
          <li><i class="fas fa-check-circle"></i> Featured Listing</li>
          <li><i class="fas fa-check-circle"></i> Top of the List</li>
          <li><i class="fas fa-check-circle"></i> Top Rated Badge</li>
          <li><i class="fas fa-check-circle"></i> 1 Month Validity</li> 
        </ul>
        <button class = "field_btn" onclick="location.href='premium-payment.php';">Buy Now</button>
      </div>
      <div class="plan">
        <h2>Basic</h2>
        <div class="price">₱100/month</div>
        <ul class="features">
         <li><i class="fas fa-check-circle"></i> Standard Listing</li>
         <li><i class="fas fa-check-circle"></i> 1 Month Validity</li> 
         <li><i class="fas fa-times-circle"></i> No priority support</li>
     
        </ul>
        <button class = "field_btn" onclick="location.href='basic-payment.php';">Buy Now</button>
      </div>
    </div>
</body>
</html>
