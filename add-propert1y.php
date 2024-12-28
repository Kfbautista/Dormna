<?php
session_start();
if (!isset($_SESSION["user"])) {
   header("Location: login.php");
}
?>


<?php

if (isset($_POST["submit-btn"])) {
    // Retrieve post data
    $name = $_POST["name"];
    $address = $_POST["address"];
    $facebook = $_POST["facebook"];
    $slots = $_POST["slots"];
    $contact_person = $_POST["contact_person"];
    $contact_number = $_POST["contact_number"];
    $price = $_POST["price"];
    $accommodation_type = $_POST["accommodation_type"];
    $propertyGender = $_POST["propertyGender"];
    $description = $_POST["description"];
    $userId = $_SESSION["userID"];
    
    // File upload logic
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $fileName = $_FILES["image"]["name"];
        $fileSize = $_FILES["image"]["size"];
        $tmpName = $_FILES["image"]["tmp_name"];

        $validImageExtension = ['jpg', 'jpeg', 'png'];
        $imageExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($imageExtension), $validImageExtension) && $fileSize <= 1000000) {
            $newImageName = uniqid() . '.' . $imageExtension;
            move_uploaded_file($tmpName, 'img/' . $newImageName);

            $mysqli = require_once "database.php";
            // Database insert query
            $stmt = $mysqli->prepare("INSERT INTO `property-info` (`pname`, `paddress`, `pfbname`, `PNum-slot-avail`, `pcontact-name`, `pcontact-num`, `pprice`, `pacc-type`, `pgender`, `pdscrpt`, `pimage`, `user_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        


         
    
    
           $stmt->bind_param("sssississssi", $name, $address, $facebook, $slots, $contact_person, $contact_number, $price, $accommodation_type, $propertyGender, $description, $newImageName, $userId);
            $stmt->execute();

            echo "<script>alert('Successfully Added'); window.location.href = 'index.php';</script>";
        } else {
            echo "<script>alert('Invalid Image Extension or File Too Large');</script>";
        }
    } else {
        echo "<script>alert('No Image Uploaded or Error Uploading File');</script>";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Property Form</title>
  <link rel="stylesheet" href="styles.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link
  href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css"
  rel="stylesheet"
/>

</head>

<div class="addProperty">


    <div class="form-container">

    
        <div class="form-header">
          <h1>Add A New Property</h1>
          <p>Your Property With Us And Be Confident That Your Room Will Be Filled Out!</p>
        </div>
        <form action="add-property.php" method="post" enctype="multipart/form-data"> <!-- Update action attribute as needed -->
          <div class="input-group">

            <label for="name">Name *</label>
            <input type="text" id="name" name="name" required placeholder="Enter Name of the Property">
          </div>
          <div class="input-group">
            <label for="address">Address *</label>
            <input type="text" id="address" name="address" required placeholder="Enter Address">
          </div>
          <div class="input-group">
            <label for="facebook">Facebook *</label>
            <input type="text" id="facebook" name="facebook" required placeholder="Enter Facebook name">
          </div>
          <div class="input-group">
      <label for="slots">Number of Available Slots *</label>
      <input type="number" min="0" step="1" id="slots" name="slots" required placeholder="Enter number of slots available">
      <!-- Populate with actual options -->
      </select>
      </div>
      <div class="input-group">
      <label for="contact-person">Contact Person *</label>
      <input type="text" id="contact-person" name="contact_person" required placeholder="Enter Contact Person">
      </div>
      <div class="input-group">
      <label for="contact-number">Contact Number *</label>
      <input type="text" id="contact-number" name="contact_number" required placeholder="Enter Contact Number">
      </div>
      <div class="input-group">
      <label for="price">Price *</label>
      <input type="number" id="price" name="price" required placeholder="How much per month">
      </div>
      <div class="input-group">
        <label for="accommodation-type">Accommodation Type *</label>
        <select id="accommodation-type" name="accommodation_type" required>
            <option value="">Select Accommodation Type</option>
            <option value="Dormitory">Dormitory</option>
            <option value="BoardingHouse">Boarding House</option>
        </select>
    </div>
    
      <div class="input-group">
      <label for="gender">Gender *</label>

      <select id="propertyGender" name="propertyGender" required>
        <option value="">Select Property Type</option>
        <option value="Male">Male Only</option>
        <option value="Female">Female Only</option>
        <option value="Mixed">Mixed Gender</option>
    </select>


      </div>

      <div class="input-group">
      <label for="description">Description</label>
      <textarea class="description" id="description" name="description" required placeholder="Enter Description"></textarea>
      </div>
      <div class="photo-upload" onclick="document.getElementById('file-upload').click()">
      Upload Photos<br>
      <span>Drag your images here, or browse</span>
      <input type="file" id="file-upload" name="image"  accept=".jpg, .jpeg, .png">
      </div>
      <input type="submit" value="Add New Property" name="submit-btn" class="submit-btn">
      
        </form>
      </div>


    </div>
   
    </html>