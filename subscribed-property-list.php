<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin-login.php");
 }
?>


<?php
$mysqli = require_once "database.php";

if (isset($_POST['search']) && !empty(trim($_POST['propertyName']))) {
    // Clean up input
    $propertyName = trim($_POST['propertyName']);

    // Use prepared statement to query the database
    $stmt = $mysqli->prepare("SELECT * FROM `property-info` WHERE `Pname` LIKE ?");
    $likeQuery = '%' . $propertyName . '%';
    $stmt->bind_param("s", $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    $properties = [];
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }

    $stmt->close();
} else {
    // Fetch all properties with a non-empty subscription
    $result = $mysqli->query("SELECT * FROM `property-info` WHERE `subscription` IS NOT NULL AND `subscription` != ''");

    $properties = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $properties[] = $row;
        }
    }

    $result->close(); // Make sure to close result if not using prepared statements
}

$mysqli->close();
?>



<?php
$mysqli = require "database.php"; // Ensure your database connection file path is correct.



if (isset($_POST['accredit'])) {
    // Retrieve all form data or set to a default value using null coalescing operator ??
    $propertyId = $_POST['propertyId'] ;
      $user_id = $_POST['user_id'] ;
    $accredited = "yes"; 

    $stmtAccredit = $mysqli->prepare("SELECT * FROM `property-info` WHERE `PID` = ? AND `accredited` = 'yes'");
    $stmtAccredit->bind_param("s", $propertyId);  // 's' specifies the variable type => 'string'
    
 
    $stmtAccredit->execute();
    

    $result = $stmtAccredit->get_result();
    
    if ($result->num_rows > 0) {
        
        echo "<script>alert('This property has already been marked as accredited!'); window.location.href='subscribed-property-list.php';</script>";
    } else {
  
    $query = "UPDATE `property-info` SET 
        `accredited` = ?
        WHERE `PID` = ?";

    $stmt = $mysqli->prepare($query);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->error);
    }

    $stmt->bind_param('si', 
    $accredited, $propertyId);

    if ($stmt->execute()) {
        
         $accredittedNotif= "Congratulations, Your Property ID: $propertyId  successfully marked as accredited ";

                $notifSQL =  "INSERT INTO `notification` (`UID`, `notification_msg`) VALUES (?, ?)";
                $notifStmt = $mysqli->prepare($notifSQL);
                                            
                $notifStmt->bind_param("is", $user_id ,  $accredittedNotif );
                $notifStmt->execute();
        
        
        
        echo "<script>alert('Successfully marked this property as accredited'); window.location.href='subscribed-property-list.php';</script>";
        //notif
               
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();



}
 
    $mysqli->close();
}
?>



<?php
$mysqli = require "database.php"; // Ensure your database connection file path is correct.



if (isset($_POST['cancel'])) {
    // Retrieve all form data or set to a default value using null coalescing operator ??
     $user_id = $_POST['user_id'] ;
    $propertyId = $_POST['propertyId'] ;
    $accredited = ""; 


  
    $query = "UPDATE `property-info` SET 
        `accredited` = ?
        WHERE `PID` = ?";

    $stmt = $mysqli->prepare($query);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->error);
    }

    $stmt->bind_param('si', 
    $accredited, $propertyId);

    if ($stmt->execute()) {
        
        
         //notif
                $CancelAccredittedNotif= "Your Accreditation was cancelled! ";

                $notifSQL =  "INSERT INTO `notification` (`UID`, `notification_msg`) VALUES (?, ?)";
                $notifStmt = $mysqli->prepare($notifSQL);
                                            
                $notifStmt->bind_param("is", $user_id ,  $CancelAccredittedNotif );
                $notifStmt->execute();
                
        echo "<script>alert('Successfully unmarked this property as accredited'); window.location.href='subscribed-property-list.php';</script>";
        
       
        
        
        
        
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();




 
    $mysqli->close();
}
?>









<!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <title>Subscribed Property List </title>
      <link rel="stylesheet" href="style--dash-owner.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"/>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    </head>
    <body>
 <div class="sidebar">
            <div class="logo"></div>
            <ul class="menu">
                <li >
                    <a href="dash-admin.php">
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

                <li class="active">
                    <a href="subscribed-property-list.php" id="">
                       <i class="fas fa-house-user"></i>  <!-- Classic home icon -->

                        <span>Property List</span>
                    </a>
                </li>



                <li>
                    <a href="admin-gcash-info.php" id="">
                        <i class="fas fa-chart-bar"></i>
                        <span>Update Payment Info</span>
                    </a>
                </li>

            

                
                <li>
                    <a href="change-pass-admin.php">
                        <i class="fas fa-star"></i>
                        <span>Change Password</span>
                    </a>
                </li>
                <li class="logout">
                    <a href="admin-logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="main--content" id="main">
            <div class="header--wrapper">
                <div class="header--title">
                    <span>List of</span>
                    <h2>Subscribed Properties</h2>
                </div>

                <div class="search-container" >
                <form method="post" action="subscribed-property-list.php" enctype="multipart/form-data">
                  <input type="text" placeholder="Search Property Name" name="propertyName">
                  <button type="submit" name="search"><i class="fa fa-search"></i></button>
                </form>
            </div>

















            </div>
            <div class="card--container">
                <h3 class="main--title">You can verify the properties if it accreditted or not</h3>      
        <div class="card--wrapper">


        <section class="properties">


        <div class="container" id="ResultSearch">
            <div class="properties-grid" id="propertiesGrid">

            <?php if (!empty($properties)): ?>
                <?php foreach ($properties as $index => $property): ?>
                    <div class="property-card"
                         data-user-id="<?= htmlspecialchars($property['user_id']) ?>"
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

                    <h2 class="property-address"> <?= htmlspecialchars($property['Pname']) ?>  <i class="<?php echo   $accredited ?>"> </i></h2>
                        
        
                            <p class="property-price">₱<?= htmlspecialchars($property['Pprice']) ?>/Month</p>
                            
                        </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="no-results-message" style=" margin-left:0px;">
            <p>No Property Subscribed Yet  </p>

       

            </p>
            
        </div>
    <?php endif; ?>
                
                </div>

            
    </section>





    </div>
    
</div>
<div id="propertyDetails" class="property-details-container"></div>
</div>
</body>


<script > 
document.addEventListener('DOMContentLoaded', function() {
  const propertyCards = document.querySelectorAll('.property-card');
  const detailsContainer = document.getElementById('propertyDetails');

  propertyCards.forEach(card => {
    card.addEventListener('click', function() {
        const userId = this.dataset.userId;
      const propertyId = this.dataset.propertyId;
      const propertyName = this.dataset.propertyName;
      const propertyAddress = `${this.dataset.propertyStreet}, ${this.dataset.propertyBrgy}`;
      const propertyType = this.dataset.propertyType;
      const propertyPrice = this.dataset.propertyPrice;
      const propertyImage = this.dataset.propertyImage;
      const propertyFb = this.dataset.propertyFb;
      const propertyContactName = this.dataset.propertyContactName;
      const propertyContactNum = this.dataset.propertyContactNum;
      const propertySlots = this.dataset.propertySlots;
      const propertyGender = this.dataset.propertyGender;
      const propertyDescription = this.dataset.propertyDscrption;
      const propertyWifi = this.dataset.propertyWifi;
      const propertyWater = this.dataset.propertyWater;
      const propertyElectric = this.dataset.propertyElectric;
      const propertyAccredited = this.dataset.propertyAccredited;

      let formHtml = '';

      if (propertyAccredited === "yes") {
        formHtml = `<form id="propertyForm" action="subscribed-property-list.php" method="post">
                     <input value="${userId}" type="hidden" name="user_id" id="hiddenPropertyId">
                      <input value="${propertyId}" type="hidden" name="propertyId" id="hiddenPropertyId">
                      <button class="field_btn" name="cancel">Cancel Accreditation?</button>
                    </form>`;
      } else {
        formHtml = `<form id="propertyForm" action="subscribed-property-list.php" method="post">
                      <input value="${userId}" type="hidden" name="user_id" id="hiddenPropertyId">
                      <input value="${propertyId}" type="hidden" name="propertyId" id="hiddenPropertyId">
                      <button class="field_btn" name="accredit">Accredit this Property?</button>
                    </form>`;
      }

      const propertyDetails = `
        <div class="property-details" style="display:flex;">
          <img src="${propertyImage}" alt="Property Image" class="property-image-admin">
          <div class="property-details" style="display:flex; width:60%;">
          <div class="property-info">
            <h2>ID: ${propertyId}</h2>
            <h2>Name: ${propertyName}</h2>
            <p>Accredited: <i class="${propertyAccredited === "yes" ? 'fas fa-check-circle' : 'fas fa-times-circle'}" style="color: ${propertyAccredited === "yes" ? 'green' : 'red'};"></i></p>
            <p>Address: ${propertyAddress}</p>
            <p>Type: ${propertyType}</p>
            <p>Price: ₱${propertyPrice}/Month</p>
            <p>Facebook: ${propertyFb}</p>
            <p>Contact: ${propertyContactName}, ${propertyContactNum}</p>
            <p>Slots Available: ${propertySlots}</p>
            <p>Gender: ${propertyGender}</p>
            <p>Description: ${propertyDescription}</p>
            <h4>Utilities and Amenities Included:</h4>
            <p>
              Wifi: <i class="${propertyWifi === "Yes" ? 'fas fa-check-circle' : 'fas fa-times-circle'}" style="color: ${propertyWifi === "Yes" ? 'green' : 'red'};"></i>
              Water: <i class="${propertyWater === "Yes" ? 'fas fa-check-circle' : 'fas fa-times-circle'}" style="color: ${propertyWater === "Yes" ? 'green' : 'red'};"></i>
              Electric: <i class="${propertyElectric === "Yes" ? 'fas fa-check-circle' : 'fas fa-times-circle'}" style="color: ${propertyElectric === "Yes" ? 'green' : 'red'};"></i>
            </p>
            ${formHtml}
            <button class="field_btn" onclick="location.href='#ResultSearch';">Back</button>
          </div>
          </div>
        </div>
      `;

      detailsContainer.innerHTML = propertyDetails;
      detailsContainer.style.display = 'block';
      detailsContainer.scrollIntoView({ behavior: 'smooth' });
    });
  });
});





 



/*
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
    <div class="image-upload-container">
        <label for="file-upload">
            <img src="${propertyImage}" alt="Property Image" class="property-image-full">
            <i class="fas fa-edit edit-icon"></i> <!-- FontAwesome edit icon -->
        </label>
        <input value="${propertyId}" type="hidden" name="propertyId" id="hiddenPropertyId">
        <input id="file-upload" type="file" name="image" style="display: none;" accept=".jpg, .jpeg, .png" onchange="previewImage(event)">
        <input type="submit" value="Update Image" name="update" class="submit-btn">
    </div>
</form>


          <div class="property-details" >
        
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

         
      <button class="field_btn"  name="submit" onclick="location.href='subscription.php';"  >BuySubscription</button>
      </form>
      </div>
            
          </div>

          <div id="propertyForm">
          <div class="property-details">
          

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


       <h3 style="color:#52a669;">Amenities and Utilities Included in Monthly Rental Cost:</h3>

            <div class="input-group">
            </div>
        
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
    <div style="display:flex; margin-left:90px;"  >
      <button type="submit" name="save_changes" class="submit-btn">Save</button > 
      <button type="button" onclick="location.href='#main';" class="submit-btn" >Back</button > 
    </div>

  </form>
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
  
*/
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