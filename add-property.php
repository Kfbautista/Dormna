
<?php
session_start();
if (!isset($_SESSION["user"])) {
   header("Location: login.php");
   exit;
}

if (isset($_POST["submit-btn"])) {
    // Retrieve post data
    $name = $_POST["name"];
    $slots = $_POST["slots"];
    $price = $_POST["price"];
    $accommodation_type = $_POST["accommodation_type"];
    $propertyGender = $_POST["propertyGender"];

    $street = $_POST["street"];
    $brgy = $_POST["brgy"];
    $city = $_POST["city"];
    
    $facebook = $_POST["facebook"];
   
    $contact_person = $_POST["contact_person"];
    $contact_number = $_POST["contact_number"];
   
    // Check if each checkbox is checked
    $wifi = isset($_POST["wifi"]) ? 'Yes' : 'No';
    $water = isset($_POST["water"]) ? 'Yes' : 'No';
    $electricity = isset($_POST["electricity"]) ? 'Yes' : 'No';
    
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
            $stmt = $mysqli->prepare("INSERT INTO `property-info` (`pname`, `PStreet`,`PBrgy`, `PCity`,`pfbname`, `PNum-slot-avail`, `pcontact-name`, `pcontact-num`, `pprice`, `pacc-type`, `pgender`, `pdscrpt`, `pimage`, `user_id`,`PWifi`,`PWater`,`PElectric`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?,?)");
            $stmt->bind_param("sssssississssisss", $name, $street, $brgy, $city, $facebook, $slots, $contact_person, $contact_number, $price, $accommodation_type, $propertyGender, $description, $newImageName, $userId, $wifi, $water, $electricity);
            if ($stmt->execute()) {
                echo "<script>alert('Successfully Added'); window.location.href = 'dash-owner.php';</script>";
            } else {
                echo "<script>alert('Error Adding Property: " . $mysqli->error . "');</script>";
            }
        } else {
            echo "<script>alert('Invalid Image Extension or File Too Large');</script>";
        }
    } else {
        echo "<script>alert('No Image Uploaded or Error Uploading File');</script>";
    }
    $mysqli->close();
}
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
      <title>Add Accomodation Property </title>
      <link rel="stylesheet" href="style--dash-owner.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
    </head>
    <body>
        <div class="sidebar">
            <div class="logo"></div>
            <ul class="menu">
                <li >
                    <a href="dash-owner.php">
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

        <div class="main--content">
            <div class="header--wrapper">
                <div class="header--title">
                    <span>Add Accomodation </span>
                    <h2>Property</h2>
                </div>
                

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
           
                <h3 class="main--title"></h3>      
<div class="card--wrapper">


  <div style="position:center;">


            <div class="form-container">


                <div class="form-header">
                <h1>Add A New Accomodation Property</h1>
                <br>  <br>
                </div>
                <form action="add-property.php" method="post" enctype="multipart/form-data"> <!-- Update action attribute as needed -->
                
                <!-- Accomodation identity -->
                <div class="input-group">

                    <label for="name">Name *</label>
                    <input type="text" id="name" name="name" required placeholder="Enter Name of the Property">
                </div>

                <div class="input-group">
                <label for="slots">Number of Available Slots *</label>
                    <input type="number" min="0" step="1" id="slots" name="slots" required placeholder="Enter number of slots available">
                    <!-- Populate with actual options -->
                    </select>
                </div>


                <div class="input-group">
                    <label for="price">Rent per Month *</label>
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
                    <label for="gender">Gender Specific *</label>

                    <select id="propertyGender" name="propertyGender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male Only</option>
                        <option value="Female">Female Only</option>
                        <option value="Mixed">Mixed Gender</option>
                    </select>


                 </div>

              


                <div class="input-group">
                </div>

                <h3 style="color:#52a669;" >Address:</h3>

                <div class="input-group">
                </div>
                <div class="input-group">
                </div>
                

                <div class="input-group">
                    <label for="address">Street/Purok/Sitio*</label>
                    <input type="text" id="address" name="street" required placeholder="Enter Street/Purok/Sitio">
                </div>

                <div class="input-group">
                    <label for="address">Barangay*</label>
                    <input type="text" id="address" name="brgy" required placeholder="Enter Barangay">
                </div>

                <div class="input-group">
                    <label for="address">City*</label>
                    <input type="text" id="address" name="city" required placeholder="Enter City">
                </div>

                <h3 style="color:#52a669;">Contact:</h3>

                <div class="input-group">
                </div>
                <div class="input-group">
                </div>

                
                <div class="input-group">
                    <label for="facebook">Facebook*</label>
                    <input type="text" id="facebook" name="facebook" required placeholder="Enter Facebook name/link">
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

            <h3 style="color:#52a669;">Amenities and Utilities</h3>

            </div>     <div class="input-group">  </div>   <div class="input-group">  </div>
            
                 
                 <div class="input-group"> 

            <h5 style="color:#52a669;">Included in Monthly Rental Cost:</h5>

            </div>  
             <div class="input-group">  </div>   <div class="input-group">  </div>

            <div class="input-group">
            <!--Checkbox-->
            <div class="checkbox-wrapper-29">
                <label class="checkbox">
                    <input type="checkbox" class="checkbox__input" name="wifi" value="wifi"/>  
                    <span class="checkbox__label"></span>
                    Wifi/Internet Connection
                </label>
                </div>
                  
                
                
        
            </div>

                
                <div class="input-group"> 
                
                
                                 <div class="checkbox-wrapper-29">
                                            <label class="checkbox">
                                                <input type="checkbox" class="checkbox__input" name="water" value="water"/>  
                                                <span class="checkbox__label"></span>
                                                Water Supply
                                            </label>
                                            </div>
                
                
                
                </div>



                    <div class="input-group"> 
                    
                          <div class="checkbox-wrapper-29">
                                    <label class="checkbox">
                                        <input type="checkbox" class="checkbox__input" name="electricity" value="electricity"/>  
                                        <span class="checkbox__label"></span>
                                        Electricity
                                    </label>
                                    </div>
                    
                    
                    </div>
    


        
       
             <div class="input-group"> </div>  <div class="input-group"> </div>  <div class="input-group"> </div>
   

          

            <div class="input-group">
            <label for="description">Description*</label>
            <textarea class="description" id="description" name="description" required placeholder="Enter Description of your Property, Other Amenities and Utilities included to monthly rental cost and also the not included, and other description"></textarea>
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
            </div>
         


</div>
</div>
</div>
</body>





</html>