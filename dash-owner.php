
<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
 }
 

if (isset($_POST['submit'])) {
    $_SESSION['propertyID'] = $_POST['propertyId'];
    header("Location: subscription.php");
    exit();
}




?>







<?php

$userId = $_SESSION["userID"];
$mysqli = require_once "database.php";




$query = "SELECT * FROM `property-info` WHERE `user_id`='$userId'";
$result = $mysqli->query($query);

$properties = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
}




$mysqli->close();




?>

<?php
$userId = $_SESSION["userID"];
$mysqli = require "database.php";
//premium
$query = "SELECT * FROM `notification`WHERE `UID`='$userId'  ORDER BY `notification`.`notif_id` DESC";
$result = $mysqli->query($query);

$notification = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notification[] = $row;
    }
}








$mysqli->close();

?>

<?php
// Expired subscription notification
$mysqli = require "database.php";
$userId = $_SESSION["userID"];

$sqlSelect = "SELECT * FROM `payment-transaction-tb` WHERE `status` = 'expired' AND `UID` = ? AND `expired_notified` = ''" ;
$stmt = $mysqli->prepare($sqlSelect);
if ($stmt === false) {
    echo "Error preparing statement: " . $mysqli->error;
    exit;
}
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Prepare an UPDATE statement to set status to empty for expired records
    $sqlUpdate = "UPDATE `payment-transaction-tb` SET `expired_notified` = 'yes' WHERE `status` = 'expired' AND `UID` = '$userId' AND `expired_notified` = ''";
    $stmtUpdate = $mysqli->prepare($sqlUpdate);
    
    if ($stmtUpdate) {
        $stmtUpdate->execute();}
    
    $expiredSubsNotif = "Your subscription has expired! You can renew or choose a new subscription plan.";
    $sqlInsert = "INSERT INTO `notification` (`UID`, `notification_msg`) VALUES (?, ?)";
    $stmtInsert = $mysqli->prepare($sqlInsert);
    if ($stmtInsert === false) {
        echo "Error preparing insert statement: " . $mysqli->error;
        exit;
    }
    $stmtInsert->bind_param("is", $userId, $expiredSubsNotif);
    $stmtInsert->execute();
    $stmtInsert->close();
} else {
  
}

$stmt->close();
$mysqli->close();
?>

<?php

if (isset($_POST["update"]) ) {

    $propertyId = $_POST["propertyId"];
    $fileName = $_FILES["image"]["name"];
    $fileSize = $_FILES["image"]["size"];
    $tmpName = $_FILES["image"]["tmp_name"];
    $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $validImageExtension = ['jpg', 'jpeg', 'png'];
    
    if (in_array($imageExtension, $validImageExtension) && $fileSize <= 10000000) {
        $newImageName = uniqid() . '.' . $imageExtension;
        move_uploaded_file($tmpName, 'img/' . $newImageName);

        $mysqli = require "database.php";
     
        $stmt = $mysqli->prepare("UPDATE `property-info` SET `Pimage`=? WHERE `PID`=?");
        $stmt->bind_param("si", $newImageName, $propertyId);
        if ($stmt->execute()) {
            echo "<script>alert('Successfully Updated'); window.location.href = 'dash-owner.php';</script>";
        } else {
            echo "<script>alert('Error Updating Property: " . $mysqli->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Invalid Image Extension or File Too Large'); window.location.href = 'dash-owner.php';</script>";
    }
    $mysqli->close();
}
?>

<?php
$mysqli = require "database.php"; // Ensure your database connection file path is correct.

if (isset($_POST['save_changes'])) {
    // Retrieve all form data or set to a default value using null coalescing operator ??
    $propertyId = $_POST['propertyId'] ;
    $propertyName = $_POST['propertyName'] ;
    $propertyStreet = $_POST['propertyStreet'] ;
    $propertyBrgy = $_POST['propertyBrgy'] ;
    $propertyType = $_POST['propertyType'] ;
    $propertyPrice = $_POST['propertyPrice'] ;
    $propertyFb = $_POST['propertyFb'] ;
    $propertyContactName = $_POST['propertyContactName'] ;
    $propertyContactNum = $_POST['propertyContactNum'];
    $propertySlots = $_POST['propertySlots'] ;
    $propertyGender = $_POST['propertyGender'] ;
    $propertyDescription = $_POST['propertyDescription'] ;
    $propertyWifi = isset($_POST['propertyWifi']) && $_POST['propertyWifi'] === 'on' ? 'Yes' : 'No';
    $propertyWater = isset($_POST['propertyWater']) && $_POST['propertyWater'] === 'on' ? 'Yes' : 'No';
    $propertyElectric = isset($_POST['propertyElectric']) && $_POST['propertyElectric'] === 'on' ? 'Yes' : 'No';

    if (!$propertyId) {
        die("Property ID is required.");
    }

    $query = "UPDATE `property-info` SET 
        `PName` = ?, 
        `PStreet` = ?, 
        `PBrgy` = ?, 
        `PAcc-type` = ?, 
        `Pprice` = ?, 
        `PFBname` = ?, 
        `Pcontact-name` = ?, 
        `Pcontact-num` = ?, 
        `PNum-slot-avail` = ?, 
        `PGender` = ?, 
        `PDscrpt` = ?, 
        `PWifi` = ?, 
        `PWater` = ?, 
        `PElectric` = ? 
        WHERE `PID` = ?";

    $stmt = $mysqli->prepare($query);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->error);
    }

    $stmt->bind_param('ssssssssssssssi', 
        $propertyName, $propertyStreet, $propertyBrgy, $propertyType, $propertyPrice, 
        $propertyFb, $propertyContactName, $propertyContactNum, $propertySlots, 
        $propertyGender, $propertyDescription, $propertyWifi, $propertyWater, 
        $propertyElectric, $propertyId);

    if ($stmt->execute()) {
        echo "<script>alert('Property details updated successfully!'); window.location.href='dash-owner.php';</script>";
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
    $mysqli->close();
}
?>






<style>
@import 'https://fonts.googleapis.com/css?family=Material+Icons';

.notification-icon {
    position: relative;
    margin-right: 1em;
    border-radius: 5px;
    background: #ecf0f1;
    cursor: pointer; /* Indicates it's clickable */
    display: inline-block; /* Ensures the label behaves like a block for clicking */
}

.notification-icon i {
    margin: .5rem;
}

.num-count {
    position: absolute;
    user-select: none;
    cursor: default;
    font-size: 0.6rem;
    background: #e74c3c;
    width: 1.2rem;
    height: 1.2rem;
    color: #ecf0f1;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    top: -0.33rem;
    right: -0.66rem;
    box-sizing: border-box;
}

.notification-container {
   
    display: none; /* Initially hidden */
    position: absolute;
    z-index: 100; /* Adjust based on your layout needs */
    top: 50px; /* Adjust as necessary to fit under the icon */
    right: 20px; /* Adjust as necessary */
    width: 400px; /* Adjust as necessary */
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    padding: 20px;
}

/* Show the notification container when the checkbox is checked */
#toggleNotifications:checked + .notification-icon + .notification-container {
    display: block;
}

.notification-container h3 {
    text-transform: uppercase;
    font-size: 0.75rem;
    font-weight: 700;
    color: #84929f;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table {
    margin:10px;
    width: 100%;
    border-collapse: collapse; /* Ensures that the borders between cells are merged */
}

.table th, .table td {
    font-size: 0.80rem;
    border: Opx solid #ccc; /* Applies a border to each table cell */
    padding: 8px; /* Adds padding inside each cell */
    text-align: left; /* Aligns text to the left */
}

.table th {
 
    background-color: #f8f9fa; /* Gives a background color to the table headers */
}

.table tr {
    
    background-color: #f2f2f2; /* Adds zebra striping to rows */
}

.hidden {
            display: none;
        }


        .edit-field {
            width: 50%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; /* Makes padding not affect width */
            margin-top: 4px;
        }

        textarea.edit-field {
            height: 100px;
            resize: vertical; /* Allows user to resize vertically */
        }

    </style>




<!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <title>Owner Dashboard </title>
      <link rel="stylesheet" href="style--dash-owner.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"/>

    </head>
    <body>
        <div class="sidebar">
            <div class="logo"></div>
            <ul class="menu">
                <li class="active">
                    <a href="#">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                 <li>
                    <a href="index.php">
                   <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                
                <li>
                    <a href="add-property.php">
                    <i class="fas fa-plus"></i>
                        <span>Add Property</span>
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

        <div class="main--content" id="main">
            <div class="header--wrapper">
                <div class="header--title">
                    <span>Property Owner</span>
                    <h2>Dashboard</h2>
                </div>
      
                <!-- -->
                <!-- Hidden Checkbox -->
    <input type="checkbox" id="toggleNotifications" style="display: none;">

<!-- Label acts as the toggle button -->
<label for="toggleNotifications" class="notification-icon right">
    <i class="material-icons dp48">notifications</i>

    <?php
   $mysqli = require "database.php";
   $userId = $_SESSION["userID"];
   $query = "SELECT COUNT(*) AS notif_count FROM `notification` WHERE `UID` = ' $userId'";

   $result = $mysqli->query($query);
   
   if ($result) {
       $row = $result->fetch_assoc();
       $notif_count = $row['notif_count'];
   } else {
       echo "Error: " . $mysqli->error;
   }
   
   $mysqli->close();
   
    ?>






<?php

   $mysqli = require "database.php";
  

   if (isset($_POST["close"])) {
    $notif_id = $_POST["notif_id"]; 

   
    $stmt = $mysqli->prepare("DELETE FROM `notification` WHERE `notif_id` = ?");

        $stmt->bind_param('i', $notif_id); 
        $stmt->execute();
        $stmt->close();
        
          echo '<script>';
          echo 'alert("You Successfully Deleted the Notification");';
          echo 'window.location.href = "dash-owner.php";'; // Redirects to the dashboard page after the alert
          echo '</script>';


    $mysqli->close();
}

    ?>



    <span class="num-count"><?php echo $notif_count?></span>
</label>

<!-- Notification Container, visibility controlled by the checkbox -->
<div class="notification-container">
    <h3>Notifications
        <label for="toggleNotifications" >close</label>
    </h3>
    <table class="table">
        <tr class="notification new">
        <tbody>
                <?php foreach ($notification as $notif): ?>
                <tr>
                <form method="post" action="dash-owner.php">
                        <td><?php echo htmlspecialchars($notif['notification_msg']); ?></td>
                        <td>
                            <input type="hidden" name="notif_id" value="<?php echo htmlspecialchars($notif['notif_id']); ?>">
                            <button  class="material-icons dp48 right" name="close" >close</button>
                            
                        </td>
                </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </tr>
        <!-- Additional rows -->
    </table>
</div>





            </div>
            <div class="card--container">
                <h3 class="main--title">Your Accomodation Property</h3>      
        <div class="card--wrapper">


        <section class="properties">


        <div class="container" id="ResultSearch">
            <div class="properties-grid" id="propertiesGrid">

            <?php if (!empty($properties)): ?>
                <?php foreach ($properties as $index => $property): ?>
                    <div class="property-card"
                        data-property-id="<?= htmlspecialchars($property['PID']) ?>"
                        data-property-name="<?= htmlspecialchars($property['Pname']) ?>"
                        data-property-street="<?= htmlspecialchars($property['PStreet']) ?>"
                        data-property-brgy="<?= htmlspecialchars($property['PBrgy']) ?>"
                        data-property-type="<?= htmlspecialchars($property['PAcc-type']) ?>"
                        data-property-price="<?= htmlspecialchars($property['Pprice']) ?>"
                        data-property-fb="<?= htmlspecialchars($property['PFBname']) ?>"
                        data-property-contact-name="<?= htmlspecialchars($property['Pcontact-name']) ?>"
                        data-property-contact-num="<?= htmlspecialchars($property['Pcontact-num']) ?>"
                        data-property-slots="<?= htmlspecialchars($property['PNum-slot-avail']) ?>"
                        data-property-image="img/<?= htmlspecialchars($property['Pimage']) ?>"
                        data-property-gender="<?= htmlspecialchars($property['PGender']) ?>"
                        data-property-dscrption="<?= htmlspecialchars($property['PDscrpt']) ?>"
                        data-property-wifi="<?= htmlspecialchars($property['PWifi']) ?>"
                        data-property-water="<?= htmlspecialchars($property['PWater']) ?>"
                        data-property-electric="<?= htmlspecialchars($property['PElectric']) ?>"
                         data-property-accredited="<?= htmlspecialchars($property['accredited']) ?>"
                           data-property-subscription="<?= htmlspecialchars($property['subscription']) ?>"
                        >





             <?php 
                              $subs = htmlspecialchars($property['subscription' ]);
                              if ($subs == 'premium') {
                                $subs =  "assets/premium.png";

                              } elseif ($subs == 'basic') {
                                $subs =  "assets/basic.png";
                              }
                              
                              
                              else{
                                   $subs =  "assets/transparent.png";
                                  
                                  
                              }
                ?>
          

                 <span class="iconPremium"> <img src= <?php echo $subs?> /></span>
            
            <img src="img/<?= htmlspecialchars($property['Pimage']) ?>" alt="Property Image" class="property-image">
        
            <div class="property-details-initial">
                
                            
                            
                <?php 
                              $accredited = htmlspecialchars($property['accredited' ]);
                              if ($accredited == 'yes' ) {
                                $accredited =  "fas fa-check-circle verified-badge";

                              } else {
                                $accredited ="";
                              }
                     ?>

                    <h2 class="property-address">  <?= htmlspecialchars($property['Pname']) ?> <i class="<?php echo   $accredited ?>"> </i></h2>
                            <p class="property-price">₱<?= htmlspecialchars($property['Pprice']) ?>/Month</p>
                            
                        </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="no-results-message" style=" margin-left:0px;">
            <p>It looks like you haven't uploaded any accommodation property yet. You can upload your property. You can upload your property here: <a href="add-property.php" style= "text-decoration: underline;" class="alert-link">Add Property</a> </p>

       

            </p>
            
        </div>
    <?php endif; ?>
                
                </div>

            
    </section>





    </div>
    
</div>
<div id="propertyDetails" class="property-details-container"></div>
</div>

</div>

</body>


<script > 
/*document.addEventListener('DOMContentLoaded', function() {
  const propertyCards = document.querySelectorAll('.property-card');
  const detailsContainer = document.getElementById('propertyDetails');

  propertyCards.forEach(card => {
    card.addEventListener('click', function() {
    const propertyId = this.dataset.propertyId;
      const propertyName = this.dataset.propertyName;
      const propertyStreet = this.dataset.propertyStreet;
      const propertyBrgy = this.dataset.propertyBrgy;
      const propertyType = this.dataset.propertyType;
      const propertyPrice = this.dataset.propertyPrice;
      const propertyImage = this.dataset.propertyImage;
      const propertyFb = this.dataset.propertyFb;
      const propertyContactName = this.dataset.propertyContactName;
      const propertyContactNum = this.dataset.propertyContactNum;
      const propertySlots = this.dataset.propertySlots;
      const propertyGender = this.dataset.propertyGender;
      const propertyDescription = this.dataset.propertyDescription;
      const propertyWifi = this.dataset.propertyWifi;
      const propertyWater = this.dataset.propertyWater;
      const propertyElectric = this.dataset.propertyElectric;


      // Build the details HTML
      const propertyDetails = `
        <div class="property-details">
          <img src="${propertyImage}" alt="Property Image" class="property-image-full">
          <div class="property-info">
            <h2>Property ID: ${propertyId}</h2>
            <h2>Name: ${propertyName}</h2>
            <p>Street: ${propertyStreet}</p>
            <p>Brgy: ${propertyBrgy}</p>
            <p>Type: ${propertyType}</p>
            <p>Price: ₱${propertyPrice}/Month</p>
            <p>Facebook: ${propertyFb}</p>
            <p>Contact: ${propertyContactName}, ${propertyContactNum}</p>
            <p>Slots Available: ${propertySlots}</p>
            <p>Gender: ${propertyGender}</p>
            <p>Description: ${propertyDescription}</p>
            <h4>Utilities and Amenities Included:</h4>
            <p>
                Wifi:
                <i class="${propertyWifi === "Yes" ? 'fas fa-check-circle' : 'fas fa-times-circle'}" style="color: ${propertyWifi === "Yes" ? 'green' : 'red'};"></i>
                Water:
                <i class="${propertyWater === "Yes" ? 'fas fa-check-circle' : 'fas fa-times-circle'}" style="color: ${propertyWater === "Yes" ? 'green' : 'red'};"></i>
                Electric:
                <i class="${propertyElectric === "Yes" ? 'fas fa-check-circle' : 'fas fa-times-circle'}" style="color: ${propertyElectric === "Yes" ? 'green' : 'red'};"></i>
            </p>


     

            <button class="field_btn" name="submit" onclick="location.href='#main';">Back</button>


            <form id="propertyForm" action="dash-owner.php" method="post" style="display:yes;">
         
            <input value="${propertyId}" type="hidden" name="propertyId" id="hiddenPropertyId">
  

 

          <button class="field_btn" name="submit" onclick="location.href='subscription.php';">Buy Subsctiption</button>


             </form>

<form method="post" action="dash-owner.php">
 <input value="${propertyId}" type="hidden" name="propertyId" id="hiddenPropertyId">
    <input value="${propertyName}" type="hidden" name="propertyName" id="propertyName">
    <input value="${propertyStreet}" type="hidden" name="propertyStreet" id="propertyStreet">
    <input value="${propertyBrgy}" type="hidden" name="propertyBrgy" id="propertyBrgy">
    <input value="${propertyType}" type="hidden" name="propertyType" id="propertyType">
    <input value="${propertyPrice}" type="hidden" name="propertyPrice" id="propertyPrice">
    <input value="${propertyFb}" type="hidden" name="propertyFb" id="propertyFb">
    <input value="${propertyContactName}" type="hiddentext" name="propertyContactName" id="propertyContactName">
    <input value="${propertyContactNum}" type="hidden" name="propertyContactNum" id="propertyContactNum">
    <input value="${propertySlots}" type="hidden" name="propertySlots" id="propertySlots">
    <input value="${propertyGender}" type="hidden" name="propertyGender" id="propertyGender">
    <textarea name="propertyDescription" type="hidden" id="propertyDescription">${propertyDescription}</textarea>
    <input type="checkbox"  type="hidden" name="propertyWifi" ${propertyWifi === 'Yes' ? 'checked' : ''}>
    <input type="checkbox"  type="hidden" name="propertyWater" ${propertyWater === 'Yes' ? 'checked' : ''}>
    <input type="checkbox" type="hidden" name="propertyElectric" ${propertyElectric === 'Yes' ? 'checked' : ''}>
   <button type="button" onclick="location.href='#main';">Back</button>
<button  class="field_btn" name="save_changes">Save Changes</button>

  </form>

          </div>
        </div>`
          


        ;

      detailsContainer.innerHTML = propertyDetails;
      detailsContainer.style.display = 'block';
      detailsContainer.scrollIntoView({ behavior: 'smooth' });
    });
  });
});



*/

 // File upload logic




document.addEventListener('DOMContentLoaded', function() {
  const propertyCards = document.querySelectorAll('.property-card');
  const detailsContainer = document.getElementById('propertyDetails');

  propertyCards.forEach(card => {
    card.addEventListener('click', function() {
      const propertyId = this.dataset.propertyId;
      const propertyName = this.dataset.propertyName;
      const propertyStreet = this.dataset.propertyStreet;
      const propertyBrgy = this.dataset.propertyBrgy;
      const propertyType = this.dataset.propertyType;
      const propertyPrice = this.dataset.propertyPrice;
      const propertyImage = this.dataset.propertyImage;
      const propertyFb = this.dataset.propertyFb;
      const propertyContactName = this.dataset.propertyContactName;
      const propertyContactNum = this.dataset.propertyContactNum;
      const propertySlots = this.dataset.propertySlots;
      const propertyGender = this.dataset.propertyGender;
      const propertyDscrption = this.dataset.propertyDscrption;
      const propertyWifi = this.dataset.propertyWifi;
      const propertyWater = this.dataset.propertyWater;
      const propertyElectric = this.dataset.propertyElectric;

      const propertyDetails = `
        <div class="property-details">
        
        <div class="front" >



        <form id="propertyForm" action="dash-owner.php" method="post" enctype="multipart/form-data">
    <div class="image-upload-container" >
        <label for="file-upload">
            <img src="${propertyImage}" alt="Property Image" class="property-image-full">
            <i class="fas fa-edit edit-icon"></i> <!-- FontAwesome edit icon -->
        </label>
        <input value="${propertyId}" type="hidden" name="propertyId" id="hiddenPropertyId">
        <input id="file-upload" type="file" name="image" style="display: none;" accept=".jpg, .jpeg, .png" onchange="previewImage(event)">
        <input type="submit" value="Update Image" name="update" class="submit-btn">
    </div>
</form>


<div class="property-details">
        
          <h2>Property ID: ${propertyId}</h2>
            <h2>Name: ${propertyName}</h2>
            <p>Street: ${propertyStreet}</p>
            <p>Brgy: ${propertyBrgy}</p>
            <p>Type: ${propertyType}</p>
            <p>Price: ₱${propertyPrice}/Month</p>
            <p>Facebook: ${propertyFb}</p>
            <p>Contact: ${propertyContactName}, ${propertyContactNum}</p>
            <p>Slots Available: ${propertySlots}</p>
            <p>Gender: ${propertyGender}</p>
            <p>Description: ${propertyDscrption}</p>
            <h4>Utilities and Amenities Included:</h4>
            <p>
                Wifi:
                <i class="${propertyWifi === "Yes" ? 'fas fa-check-circle' : 'fas fa-times-circle'}" style="color: ${propertyWifi === "Yes" ? 'green' : 'red'};"></i>
                Water:
                <i class="${propertyWater === "Yes" ? 'fas fa-check-circle' : 'fas fa-times-circle'}" style="color: ${propertyWater === "Yes" ? 'green' : 'red'};"></i>
                Electric:
                <i class="${propertyElectric === "Yes" ? 'fas fa-check-circle' : 'fas fa-times-circle'}" style="color: ${propertyElectric === "Yes" ? 'green' : 'red'};"></i>
            </p>


            <form id="propertyForm" action="dash-owner.php" method="post" style="display:yes;">
         
         <input value="${propertyId}" type="hidden" name="propertyId" id="hiddenPropertyId">

         
      <button class="field_btn"  name="submit" onclick="location.href='subscription.php';"  style="  padding: .8em 5.4em;">Buy Subscription</button>
      </form>
      </div>
            
          </div>

          <div id="propertyForm">
           <div class="property-details "  style=" width:95%;">
          

    <div class="property-info">
    <div class="form-header">
     <h1>Update your property details below:</h1>
       <br>  <br>
    <form method="post" action="dash-owner.php">
    <!-- Hidden input for Property ID -->
    <input type="hidden" name="propertyId" id="hiddenPropertyId" value="${propertyId}">

    <!-- Property information section -->
    <div class="input-group">
      <label for="propertyName">Name *</label>
      <input type="text" id="propertyName" name="propertyName" required value="${propertyName}">
    </div>
    <div class="input-group">
      <label for="propertyStreet">Street/Purok/Sitio *</label>
      <input type="text" id="propertyStreet" name="propertyStreet" required value="${propertyStreet}">
    </div>
    <div class="input-group">
      <label for="propertyBrgy">Barangay *</label>
      <input type="text" id="propertyBrgy" name="propertyBrgy" required value="${propertyBrgy}">
    </div>
    <div class="input-group">
  <label for="propertyType">Accommodation Type *</label>
  <select id="propertyType" name="propertyType" required>
    <option value="">Select Accommodation Type</option>
    <option value="Dormitory" ${propertyType === 'Dormitory' ? 'selected' : ''}>Dormitory</option>
    <option value="BoardingHouse" ${propertyType === 'BoardingHouse' ? 'selected' : ''}>Boarding House</option>
  </select>


</div>

    <div class="input-group">
      <label for="propertyPrice">Rent per Month *</label>
      <input type="text" id="propertyPrice" name="propertyPrice" required value="${propertyPrice}">
    </div>
    <div class="input-group">
      <label for="propertySlots">Number of Available Slots *</label>
      <input type="text" id="propertySlots" name="propertySlots" required value="${propertySlots}">
    </div>

        <div class="input-group">
        <label for="propertyGender">Gender Specific *</label>
        <select id="propertyGender" name="propertyGender" required>
            <option value="">Select Gender</option>
            <option value="Male" ${propertyGender === 'Male' ? 'selected' : ''}>Male Only</option>
            <option value="Female" ${propertyGender === 'Female' ? 'selected' : ''}>Female Only</option>
            <option value="Mixed" ${propertyGender === 'Mixed' ? 'selected' : ''}>Mixed Gender</option>
        </select>
        </div>

    
    <!-- Contact information section -->
    <div class="input-group">
      <label for="propertyFb">Facebook *</label>
      <input type="text" id="propertyFb" name="propertyFb" required value="${propertyFb}">
    </div>
    <div class="input-group">
      <label for="propertyContactName">Contact Person *</label>
      <input type="text" id="propertyContactName" name="propertyContactName" required value="${propertyContactName}">
    </div>
    <div class="input-group">
      <label for="propertyContactNum">Contact Number *</label>
      <input type="text" id="propertyContactNum" name="propertyContactNum" required value="${propertyContactNum}">
    </div>

    <!-- Description and utilities section -->
    <div class="input-group">
      <label for="propertyDescription">Description *</label>
      <input  type="text" id="propertyDescription" name="propertyDescription" required value="${propertyDscrption}" style="width:207%;"></input>
    </div>

<div class="input-group">  </div>
                 <div class="input-group"> 

            <h3 style="color:#52a669;">Amenities and Utilities</h3>

            </div>     <div class="input-group">  </div>   <div class="input-group">  </div>
            
                 
                 <div class="input-group"> 

            <h5 style="color:#52a669;">Included in Monthly Rental Cost:</h5>

            </div>  
             <div class="input-group">  </div>   <div class="input-group">  </div>
        
    <div class="input-group">
      <label class="checkbox">
        Wifi/Internet Connection
        <input type="checkbox" class="checkbox__input" name="propertyWifi"  ${propertyWifi === 'Yes' ? 'checked' : ''}>
      </label>
    </div>
    <div class="input-group">
      <label class="checkbox">
        Water Supply
        <input type="checkbox" class="checkbox__input" name="propertyWater" ${propertyWater === 'Yes' ? 'checked' : ''}>
      </label>
    </div>
    <div class="input-group">
      <label class="checkbox">
        Electricity
        <input type="checkbox" class="checkbox__input" name="propertyElectric" ${propertyElectric === 'Yes' ? 'checked' : ''}>
      </label>
    </div>

    
    <!-- Submission button -->
  <div style="display:flex; margin-left:0px;   "  >
      <button type="submit" name="save_changes" class="submit-btn" style="width: 7Z0%;">Save</button > 
      <button type="button" onclick="location.href='#main';" class="submit-btn" >Back</button > 
    </div>

  </form>
          </div>

       </div>
        </div>
        </div>
        </div>`;

      detailsContainer.innerHTML = propertyDetails;
      detailsContainer.style.display = 'block';
      detailsContainer.scrollIntoView({ behavior: 'smooth' });
    });
  });
});

function saveChanges() {
  // Code to save changes to the backend, potentially using AJAX or form submission
  alert('Changes would be saved with implementation specific backend call.');
}



document.getElementById('editButton').addEventListener('click', function() {
            var form = document.getElementById('propertyForm');
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        });
  

</script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.querySelector('.property-image-full');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

  


</html>