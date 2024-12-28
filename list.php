<?php
ob_start();
session_start();
if (isset($_SESSION["searchSession"])) {
   header("Location: SearchAccomodation.PHP");
}
?>


<?php
$mysqli = require_once "database.php";
$currentDateTime = date('Y-m-d H:i:s');
// Fetch properties for Premium
$queryPremium = "SELECT * FROM `property-info` WHERE `subscription`='premium' AND `expiry_date` >= '$currentDateTime'  ORDER BY RAND()";
$resultPremium = $mysqli->query($queryPremium);

$propertiesPremium = [];
if ($resultPremium) {
    while ($row = $resultPremium->fetch_assoc()) {
        $propertiesPremium[] = $row;
    }
}

// Fetch properties for basic
$queryBasic = "SELECT * FROM `property-info` WHERE subscription='basic'  AND `expiry_date` >= '$currentDateTime'  ORDER BY RAND()";
$resultBasic = $mysqli->query($queryBasic);

$propertiesBasic = [];
if ($resultBasic) {
    while ($row = $resultBasic->fetch_assoc()) {
        $propertiesBasic[] = $row;
    }
}

$mysqli->close();



// Determine the number of properties per page
$propertiesPerPage = 6;
// Calculate total pages
$totalPages = ceil(count($propertiesPremium) / $propertiesPerPage);
$totalPages_basic = ceil(count($propertiesBasic) / $propertiesPerPage);
?>

<?php


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Search"])) {
  $address = $_POST["address"] ?? '';
  $gender = $_POST["gender"] ?? '';
  $wifi = isset($_POST["wifi"]) ? 'Yes' : 'No';
  $water = isset($_POST["water"]) ? 'Yes' : 'No';
  $electricity = isset($_POST["electricity"]) ? 'Yes' : 'No';
  $minPrice = $_POST['minPrice'] ?? 0;
  $maxPrice = $_POST['maxPrice'] ?? 0;

  $mysqli = require_once "database.php"; // Ensure this path is correct and the file is accessible
  if ($mysqli->connect_error) {
      die("Connection failed: " . $mysqli->connect_error);
  }

  $likePattern = '%' . $mysqli->real_escape_string($address) . '%';
  $currentDateTime = date('Y-m-d H:i:s');
  $sql = "SELECT * FROM `property-info` WHERE (`PBrgy` LIKE ? OR `PStreet` LIKE ? OR `PCity` LIKE ? OR `PName` LIKE ?) 
          AND `PGender` = ? AND (`PWifi` = ? AND `PWater` = ? AND `PElectric` = ?) AND `Pprice` BETWEEN ? AND ? AND `expiry_date` >= ? 
          ORDER BY CASE WHEN `subscription` = 'premium' THEN 0 ELSE 1 END, `Pprice` DESC";

  $stmt = $mysqli->prepare($sql);
  if ($stmt === false) {
      die("MySQL prepare error: " . $mysqli->error); // Display SQL preparation error
  }

  $stmt->bind_param('sssssssiiis', $likePattern, $likePattern, $likePattern, $likePattern, $gender, $wifi, $water, $electricity, $minPrice, $maxPrice, $currentDateTime);
  $stmt->execute();
  $result = $stmt->get_result();
  
  echo "Query executed. Number of results: " . $result->num_rows; // Debug line to check the number of results

  while ($row = $result->fetch_assoc()) {
      $propertiesPremium[] = $row;
  }
  
  echo "Properties loaded: " . count($propertiesPremium); // Another debug line to confirm properties are loaded

  $stmt->close();
  $mysqli->close();

  // Optionally, redirect or handle the display logic here
  // header("Location: SearchAccomodation.PHP");
  // die();
}

ob_end_flush();
?>

<?php
                            // Including database connection
                            $mysqli = require "database.php";

                            // SQL to count registered owners
                            $query = "SELECT `gcash_num` FROM `admin-tb` WHERE `Admin_Id`= '1' ";
                            $Result = $mysqli->query($query);
                            if ($Result) {
                                if ($Row = $Result->fetch_assoc()) {
                                    $contactnum = $Row['gcash_num'];

                                }}
                            $mysqli->close();
                            ?>
                            
                            
<?php
require("PHPMailer/src/PHPMailer.php");
require("PHPMailer/src/SMTP.php");


if (isset($_POST["sent"])) {
    // Database connection
    $mysqli = require "database.php";
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $name = $_POST["name"];
    $userId = $_POST["userId"];
    $email = $_POST["email"];
    $msg = $_POST["msg"];

    // Fetch the contact email
    $query = "SELECT `username` FROM `user_account` WHERE `UID` = ?";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $stmt->bind_result($contactEmail);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Query preparation failed: " . $mysqli->error);
    }

    if (isset($contactEmail)) {
        // Email configuration
        $mailTo = $contactEmail;
        $htmlContent = $msg;

        $mail = new PHPMailer\PHPMailer\PHPMailer();

        $mail->SMTPDebug = 2; // Enable verbose debug output (use 0 in production)

        $mail->isSMTP();

        $mail->Host = "mail.smtp2go.com";

        $mail->SMTPAuth = true;

        $mail->Username = "dormna";
        $mail->Password = "dormnasmtp";

        $mail->SMTPSecure = "tls";

        $mail->Port = 2525;

        $mail->From = $email;

        $mail->FromName = $name;

        $mail->addAddress($mailTo, "INQUIRY");

        $mail->isHTML(true);

        $mail->Subject = "INQUIRY";
        $mail->Body = $htmlContent;
        $mail->AltBody = "PlainText";

        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "Message sent successfully";
            header("Location: list.php");
            exit();
        }
    } else {
        echo "User ID not found";
    }

    $mysqli->close();
}
?>
   
                            
                            
                            
                            



<style>

/* Popup container - can be anything you want */
.popup {
    
  position: relative;
  display: inline-block;
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* The actual popup */
.popup .popuptext {
   
  visibility: hidden;
  width: 300px;
  background-color: #555;
  color: #fff;
  text-align: left;
  border-radius: 6px;
  padding: 8px 8px;
  position: absolute;
  z-index: 1;
  bottom: 125%;
  left: 50%;
  margin-left: 10px;
    margin-bottom:60px;
}

/* Popup arrow */
.popup .popuptext::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;

  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #555 transparent transparent transparent;
}

/* Toggle this class - hide and show the popup */
.popup .show {
  visibility: visible;
  -webkit-animation: fadeIn 1s;
  animation: fadeIn 1s;
}

/* Add animation (fade in the popup) */
@-webkit-keyframes fadeIn {
  from {opacity: 0;} 
  to {opacity: 1;}
}

@keyframes fadeIn {
  from {opacity: 0;}
  to {opacity: 1;}
}

        
.input {
    width:280px;
    border: none;
    outline: none;
    background: white;
    padding: 10px; /* Add some padding for a more polished look */
    border-radius: 4px; /* Rounded corners */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    margin-bottom:10px;
}

.wrapper1{
margin-top:20px;
width: 220px;
background: #fff;
border-radius: 10px;
padding: 20px 25px 40px;
box-shadow: 0 12px 35px rgba(0,0,0,0.1);
}
header h2{
font-size: 24px;
font-weight: 600;
}
header p{
margin-top: 5px;
font-size: 16px;
}
.price-input{
width: 100%;
display: flex;
margin-top:5px;
margin-bottom:25px;

}
.price-input .field{
margin-right:15px;
display: flex;
width: 100%;
height: 45px;
align-items: center;
}
.field input{
width: 100%;
height: 100%;
outline: none;
font-size: 19px;
margin-left: 12px;
border-radius: 5px;
text-align: center;
border: 1px solid #999;

}
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
-webkit-appearance: none;
}
.price-input .separator{

width: 130px;
display: flex;
font-size: 19px;
align-items: center;
justify-content: center;
}
.slider{
height: 5px;
position: relative;
background: #ddd;
border-radius: 5px;
}
.slider .progress{
height: 100%;
left: 0%;
right: 0%;
position: absolute;
border-radius: 5px;
background: green;
}
.range-input{
position: relative;
}
.range-input input{

position: absolute;
width: 100%;
height: 5px;
top: -5px;
background: none;
pointer-events: none;
-webkit-appearance: none;
-moz-appearance: none;
}
input[type="range"]::-webkit-slider-thumb{

height: 17px;
width: 17px;
border-radius: 50%;
background:green;
pointer-events: auto;
-webkit-appearance: none;
box-shadow: 0 0 6px rgba(0,0,0,0.05);
}
input[type="range"]::-moz-range-thumb{
height: 17px;
width: 17px;
border: none;
border-radius: 50%;
background: #17A2B8;
pointer-events: auto;
-moz-appearance: none;
box-shadow: 0 0 6px rgba(0,0,0,0.05);
}
</style>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>List of Accomodations</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css"
      
      rel="stylesheet"
    />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">


    
</head>

<body>

<!--
<div class="search">
  <form  method="post" action="SearchAccomodation.PHP" >
    <label for="search" class="searchLabel">Search for location</label>
    <div class="searcBAR">
    
    <div class="custom-nav">
  <select name="type">
    <option value="#">Type</option>
    <option value="Dormitory">Dormitory</option> 
    <option value="BoardingHouse">Boarding House</option>
  </select>
</div>


<div class="custom-nav">
  <select name="gender">
    <option value="">Gender</option>
    <option value="Male">Male</option> 
    <option value="Female">Female</option>
    <option value="Mixed">Mixed</option>
  </select>
</div>

<div class="custom-nav">
  <select name="price">
    <option value="#">Price</option>
    <option value="">Below ₱1000</option>
    <option value="">₱1001 - ₱1500</option>
    <option value="">₱1501 - ₱2000</option>
    <option value="">₱2001 - ₱2500</option>
    <option value="">₱2501 - Above</option>
  </select>
</div>


 
          <input class="searchInput" id="search" type="search" name ="address" placeholder="Search for location/name of accomodation" autofocus required />
          </div>
         
     
    <button class="searchbtn" type="submit" name="Search">Search</button>
  </form>
</div>
-->
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
              <li><a href="list.php" class ="active">Search</a></li>
               <li><a href="index.php#about">About Us</a></li> 
       
            </ul>
      </div>
    </nav>
    
    <div class="main-wrapper">


    <!--FILTERS-->

<div class="sidebar">
            <div class="sidehead">
                <div class="dots">
                  
                </div>
                <hr style="margin: 15px 0; border: 1px solid #eee">
            </div>
            <div class="sidebody" style="height: 69vh;">

            <form  method="post" action="SearchAccomodation.PHP" > 
                <div class="searchBar">
                    <input placeholder="Search for Location/Name" id="searchBar" name="address" type="text">
               
                </div>
                    
      <div class="filters">
                <hr style="border: 1px solid green;">
                <h4 style="margin-bottom:5px;">Gender-Specific</h4>

                <div class="checkbox-wrapper-27">
                  <label class="checkbox">
                 
                    <input type="radio" name="gender" value="male">
                    <span class="checkbox__icon"></span>
                    Male
                  </label>
                </div>

                
                <div class="checkbox-wrapper-27">
                  <label class="checkbox">
                  
                    <input type="radio" name="gender" value="female">
                    <span class="checkbox__icon"></span>
                    Female
                  </label>
                </div>

                <div class="checkbox-wrapper-27">
                  <label class="checkbox">
                   
                    <input type="radio" name="gender" value="mixed">
                    <span class="checkbox__icon"></span>
                    Mixed Gender
                  </label>
                </div>
    </div>


    <div class="filters">
                <hr style="border: 1px solid green;">
                <h4 style="margin-bottom:5px;">Ameneties and Utilies Included</h4>

                <div class="checkbox-wrapper-27">
                  <label class="checkbox">
                 
                    <input type="checkbox" name="wifi" value="wifi">
                    <span class="checkbox__icon"></span>
                    Wifi
                  </label>
                </div>

                
                <div class="checkbox-wrapper-27">
                  <label class="checkbox">
                  
                    <input type="checkbox" name="water" value="water">
                    <span class="checkbox__icon"></span>
                    Water Supply
                  </label>
                </div>

                <div class="checkbox-wrapper-27">
                  <label class="checkbox">
                   
                    <input type="checkbox" name="electricity" value="electricity">
                    <span class="checkbox__icon"></span>
                    Electricity
                  </label>
                </div>
    </div>






    <div class="filters">
    
 
        <header>
            <hr style="border: 1px solid green;">
            <h4 style="margin-bottom:5px;">Price Range</h4>
        </header>
        <p class="minmax">Min - Max</p>
        <div class="price-input">
            <div class="field">
                <input type="number" class="input-min" name="minPrice" value="0">
            </div>
            <div class="separator">-</div>
            <div class="field">
                <input type="number" class="input-max" name="maxPrice" value="10000">
            </div>
        </div>
        <div class="slider">
            <div class="progress"></div>
        </div>
        <div class="range-input">
            <input type="range" class="range-min" name="rangeMin" min="0" max="10000" value="0" step="100">
            <input type="range" class="range-max" name="rangeMax" min="0" max="10000" value="10000" step="100">
        </div>
    

     </div>                

     <input type="submit" value="Search" name="Search" class="search_btn">


</form>

            </div>
            <div class="sidefoot">
             
            </div>

        </div>
        
        <section class="properties">
<!-- yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy-->
    <div class="container" id="premium">
        
        <h1 class="title_list">Suggested Accomodations</h1>
        <div class="properties-grid" id="propertiesGrid">
          
            <?php 
            $currentPage = 1;
            foreach ($propertiesPremium as $index => $property): 
                // Calculate current page based on property index
                if ($index % $propertiesPerPage === 0 && $index !== 0) {
                    $currentPage++;
                }
            ?>
            <div class="property-card-premium" 
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
                 data-page="<?= $currentPage ?>">
                 <div class="image-container-premium">

                 <?php 
                              $slot = htmlspecialchars($property['PNum-slot-avail' ]);
                              if ($slot > 0 ) {
                                $slot =  "assets/premium.png";

                              } elseif ($slot <= 0) {
                                $slot =  "assets/occupied.png";
                              }
                              ?>

                 <span class="iconPremium"> <img src= <?php echo $slot?> /></span>
        <img src="img/<?= htmlspecialchars($property['Pimage']) ?>" alt="Property Image" class="property-image">
       
    </div>
                <div class="property-details-initial">
                    
                <?php 
                              $accredited = htmlspecialchars($property['accredited' ]);
                              if ($accredited == 'yes' ) {
                                $accredited =  "fas fa-check-circle verified-badge";

                              } else {
                                $accredited ="";
                              }
                     ?>

                    <h2 class="property-address"><?= htmlspecialchars($property['Pname']) ?> <i class="<?php echo   $accredited ?>"> </i></h2>
                    <p class="property-address"><i class="fas fa-map-marker-alt"></i>&nbsp <?= htmlspecialchars($property['PBrgy']) ?>, <?= htmlspecialchars($property['PStreet']) ?></p>

                    <p class="property-type"><i class="fas fa-home"></i>&nbsp <?= htmlspecialchars($property['PAcc-type']) ?></p>
                    <p class="property-price"><i class="fas fa-peso-sign"></i>&nbsp <?= htmlspecialchars($property['Pprice']) ?>/Month  
                                  &nbsp&nbsp&nbsp&nbsp&nbsp
                        <?php
                        if (htmlspecialchars($property['PWifi']) == 'Yes') {
                            echo '<i class="fas fa-wifi"></i> Free WiFi'; // Displays icon with "Free WiFi"
                        } else {
                            // You can customize what to show when there's no WiFi
                        }
                        ?>
                   </p>

                  
                  </p><br>
                    <div class="property-icons" >
                        <p class="property-slots"><i class="fas fa-bed">&nbsp </i> <?= htmlspecialchars($property['PNum-slot-avail']) ?>&nbsp&nbsp&nbsp&nbsp&nbsp
                        <?php 
                              $gender = htmlspecialchars($property['PGender']);
                              if ($gender == 'Male') {
                                $gender =  "Male only";
                                echo '<i class="fas fa-mars"></i> ' . $gender;
                              } elseif ($gender == 'Female') {
                                $gender =  "Female only";
                                echo '<i class="fas fa-venus"></i> ' . $gender;
                              } elseif ($gender == 'Mixed') {
                                $gender =  "Mixed Gender";
                                echo '<i class="fas fa-mars"></i><i class="fas fa-venus"></i> ' . $gender;
                              } 
                              ?>
                       </p>    
                    </div>
                    
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <ul class="pagination">
            <li class="prev" onclick="changePagePremium('prev')"><a href="#">Prev</a></li>
            <?php for ($i = 1; $i <= 3; $i++): ?>
            <li onclick="changePagePremium(<?= $i ?>)"><a href="#"><?= $i ?></a></li>
            <?php endfor; ?>
            <li class="next" onclick="changePagePremium('next')"><a href="#">Next</a></li>
        </ul>
    </div>






<!-- BASIC SUBSCRIPTION -->

<div class="container-basic" id = "basic">
        <h1 class="title_list_basic">List of Available Accomodations</h1>
        <div class="properties-grid" id="propertiesGrid">
            <?php 
            $currentPage = 1;
            foreach ($propertiesBasic as $index => $property): 
                // Calculate current page based on property index
                if ($index % $propertiesPerPage === 0 && $index !== 0) {
                    $currentPage++;
                }
            ?>
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
                 data-page="<?= $currentPage ?>">
              
                        <?php
                              $slot = htmlspecialchars($property['PNum-slot-avail' ]);
                              if ($slot > 0 ) {
                                $slot =  "";

                              } elseif ($slot <= 0) {
                                $slot = "<img src= assets/occupied.png>";
                              }
                              ?>

                 <span class="iconPremium"> <?php echo $slot?></span>
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

                    <h2 class="property-address"><?= htmlspecialchars($property['Pname']) ?> <i class="<?php echo   $accredited ?>"> </i></h2>
                    <p class="property-address"><i class="fas fa-map-marker-alt"></i>&nbsp <?= htmlspecialchars($property['PBrgy']) ?>, <?= htmlspecialchars($property['PStreet']) ?></p>

                    <p class="property-type"><i class="fas fa-home"></i>&nbsp <?= htmlspecialchars($property['PAcc-type']) ?></p>
                    <p class="property-price"><i class="fas fa-peso-sign"></i>&nbsp <?= htmlspecialchars($property['Pprice']) ?>/Month  
                                  &nbsp&nbsp&nbsp&nbsp&nbsp
                        <?php
                        if (htmlspecialchars($property['PWifi']) == 'Yes') {
                            echo '<i class="fas fa-wifi"></i> Free WiFi'; // Displays icon with "Free WiFi"
                        } else {
                            
                        }
                        ?>
                   </p>

                  
                  </p><br>
                  <div class="property-icons" >
                        <p class="property-slots"><i class="fas fa-bed">&nbsp </i> <?= htmlspecialchars($property['PNum-slot-avail']) ?>&nbsp&nbsp&nbsp&nbsp&nbsp
                        <?php 
                              $gender = htmlspecialchars($property['PGender']);
                              if ($gender == 'Male') {
                                $gender =  "Male only";
                                echo '<i class="fas fa-mars"></i> ' . $gender;
                              } elseif ($gender == 'Female') {
                                $gender =  "Female only";
                                echo '<i class="fas fa-venus"></i> ' . $gender;
                              } elseif ($gender == 'Mixed') {
                                $gender =  "Mixed Gender";
                                echo '<i class="fas fa-mars"></i><i class="fas fa-venus"></i> ' . $gender;
                              } 
                              ?>
                       </p>    
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>


        

        <ul class="pagination">
            <li class="prev" onclick="changePageBasic('prev')"><a href="#basic">Prev</a></li>
            <?php for ($i = 1; $i <= 3; $i++): ?>
            <li onclick="changePageBasic(<?= $i ?>)"><a href="#basic"><?= $i ?></a></li>
            <?php endfor; ?>
            <li class="next" onclick="changePageBasic('next')"><a href="#basic">Next</a></li>
        </ul>
    </div>
    <div id="propertyDetails" class="property-details-container"></div>

</section>






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
              <a href="#"><i class="ri-youtube-fill"></i></a>
            </li>
            <li>
              <a href="#"><i class="ri-instagram-line"></i></a>
            </li>
            <li>
              <a href="#"><i class="ri-facebook-fill"></i></a>
            </li>
            <li>
              <a href="#"><i class="ri-linkedin-fill"></i></a>
            </li>
          </ul>
        </div>
        <div class="footer__col">
          <h4>Quicklinks</h4>
            
         <div class="footer__links">
            <li><a href="index.php#Dormitories">Dormitory Listings</a></li>
            <li><a href="index.php#Boarding_Houses">Boarding House Listings</a></li>
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
                <h5>Phone Number</h5>
                <p><?php echo $contactnum  ?></p>
              </div>
            </li>
            <li>
              <span><i class="ri-record-mail-line"></i></span>
              <div>
                <h5>Email</h5>
                <p>DormNa@gmail.com</p>
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
      </div>
      <div class="footer__bar">
        Copyright © 2024 DormNa. All rights reserved.
      </div>
    </footer>


<script>
  // JavaScript to handle pagination for premium properties
  let currentPagePremium = 1;
  const totalPagesPremium = <?= $totalPages; ?>; // get the total pages for premium
  
  function showPremiumPropertiesForPage(page) {
    document.querySelectorAll('.property-card-premium').forEach(card => {
      card.style.display = 'none';
    });
    
    document.querySelectorAll(`.property-card-premium[data-page="${page}"]`).forEach(card => {
      card.style.display = 'block';
    });
  }
  
  function changePagePremium(action) {
    if (action === 'prev' && currentPagePremium > 1) {
      currentPagePremium--;
    } else if (action === 'next' && currentPagePremium < totalPagesPremium) {
      currentPagePremium++;
    } else if (typeof action === 'number') {
      currentPagePremium = action;
    }
    showPremiumPropertiesForPage(currentPagePremium);
  }
  
  // JavaScript to handle pagination for basic properties
  let currentPageBasic = 1;
  const totalPagesBasic = <?= $totalPages_basic; ?>; // get the total pages for basic
  
  function showBasicPropertiesForPage(page) {
    document.querySelectorAll('.property-card').forEach(card => {
      card.style.display = 'none';
    });
    
    document.querySelectorAll(`.property-card[data-page="${page}"]`).forEach(card => {
      card.style.display = 'block';
    });
  }
  
  function changePageBasic(action) {
    if (action === 'prev' && currentPageBasic > 1) {
      currentPageBasic--;
    } else if (action === 'next' && currentPageBasic < totalPagesBasic) {
      currentPageBasic++;
    } else if (typeof action === 'number') {
      currentPageBasic = action;
    }
    showBasicPropertiesForPage(currentPageBasic);
  }
  
  // Initialize the first page for both premium and basic
  document.addEventListener('DOMContentLoaded', () => {
    showPremiumPropertiesForPage(1);
    showBasicPropertiesForPage(1);
  });
</script>





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


      // Build the details HTML
      const propertyDetails = `
      <div class="property-details">
          <img src="${propertyImage}" alt="Property Image" class="property-image-full">
          <div class="property-details_2">
          <div class="property-info">
         
            <h2>Name: ${propertyName}</h2>
            <p>Accredited by SLSU: <i class="${propertyAccredited === "yes" ? 'fas fa-check-circle' : 'fas fa-times-circle'}" style="color: ${propertyAccredited === "yes" ? 'green' : 'red'};"></i></p>
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
            
            
            
                        
   <button class="field_btn" name="sent" style="padding: .9em 5.4em;" onclick="myFunction()">
    Inquire! <i class="ri-exclamation-mark-line"></i>

</button> 
<div class="popup">
    <span class="popuptext" id="myPopup">
        Send Message to The Owner
        <form id="propertyForm" action="list.php" method="post" onsubmit="event.stopPropagation()">
           <br>
            <input value="${userId}" type="hidden" name="userId" id="hiddenPropertyId">
            <label>Name:</label>
            <input type="text" class="input" name="name" placeholder="ex.Juan Dela Cruz" required>
            <label>SlSU Email:</label>
            <input class="input" type="email" name="email" placeholder="ex.jdelacruz@slsu.edu.ph" required>
            <label>Message:</label>
            <input class="input" type="text" name="msg"  required>
            <input class="field_btn" name="sent" type="submit" style="padding: .9em 5.4em;" value="Send">
        </form>
    </span>
</div>
           
            
            <button class="field_btn" name="submit" onclick="location.href='#basic';">Back</button>
            
            

            
          </div>
          </div>
          
          
        </div>`;








      detailsContainer.innerHTML = propertyDetails;
      detailsContainer.style.display = 'block';
      detailsContainer.scrollIntoView({ behavior: 'smooth' });
    });
  });
});




</script>

<script>
// When the user clicks on div, open the popup
function myFunction() {
  var popup = document.getElementById("myPopup");
  popup.classList.toggle("show");
}
</script>


<script > 
document.addEventListener('DOMContentLoaded', function() {
  const propertyCards = document.querySelectorAll('.property-card-premium');
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
      // Build the details HTML
      const propertyDetails = `
      <div class="property-details">
          <img src="${propertyImage}" alt="Property Image" class="property-image-full">
          <div class="property-details_2">
          <div class="property-info">
         
            <h2>Name: ${propertyName}</h2>
            <p>Accredited by SLSU: <i class="${propertyAccredited === "yes" ? 'fas fa-check-circle' : 'fas fa-times-circle'}" style="color: ${propertyAccredited === "yes" ? 'green' : 'red'};"></i></p>
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
               <button class="field_btn" name="sent" style="padding: .9em 5.4em;" onclick="myFunction()">
    Inquire Here! <i class="ri-exclamation-mark-line"></i>
</button>
<div class="popup">
    <span class="popuptext" id="myPopup">
        Send Message to The Owner
        <form id="propertyForm" action="list.php" method="post" onsubmit="event.stopPropagation()">
            <br>
            <input value="${userId}" type="hidden" name="userId" id="hiddenPropertyId">
            <label>Name:</label>
            <input class="input" type="text" name="name" required>
            <label>SlSU Email:</label>
            <input class="input" type="email" name="email" required>
            <label>Message:</label>
            <input class="input" type="text" name="msg" required>
            <input class="field_btn" name="sent" type="submit" style="padding: .9em 5.4em;" value="Send">
        </form>
    </span>
</div>
  
            <button class="field_btn" name="submit" onclick="location.href='#premium';">Back</button>
          </div>
          </div>
        </div>`;

      detailsContainer.innerHTML = propertyDetails;
      detailsContainer.style.display = 'block';
      detailsContainer.scrollIntoView({ behavior: 'smooth' });
    });
  });
});


//slider price
const rangeInput = document.querySelectorAll(".range-input input"),
priceInput = document.querySelectorAll(".price-input input"),
range = document.querySelector(".slider .progress");
let priceGap = 1000;

priceInput.forEach(input =>{
    input.addEventListener("input", e =>{
        let minPrice = parseInt(priceInput[0].value),
        maxPrice = parseInt(priceInput[1].value);
        
        if((maxPrice - minPrice >= priceGap) && maxPrice <= rangeInput[1].max){
            if(e.target.className === "input-min"){
                rangeInput[0].value = minPrice;
                range.style.left = ((minPrice / rangeInput[0].max) * 100) + "%";
            }else{
                rangeInput[1].value = maxPrice;
                range.style.right = 100 - (maxPrice / rangeInput[1].max) * 100 + "%";
            }
        }
    });
});

rangeInput.forEach(input =>{
    input.addEventListener("input", e =>{
        let minVal = parseInt(rangeInput[0].value),
        maxVal = parseInt(rangeInput[1].value);

        if((maxVal - minVal) < priceGap){
            if(e.target.className === "range-min"){
                rangeInput[0].value = maxVal - priceGap
            }else{
                rangeInput[1].value = minVal + priceGap;
            }
        }else{
            priceInput[0].value = minVal;
            priceInput[1].value = maxVal;
            range.style.left = ((minVal / rangeInput[0].max) * 100) + "%";
            range.style.right = 100 - (maxVal / rangeInput[1].max) * 100 + "%";
        }
    });
});
</script>


</body>

<script src="https://unpkg.com/scrollreveal"></script>
<script src="main.js"></script>
  <script>
                  function adjustFontSize() {
                    var element = document.getElementById('slsu');
                    element.style.fontSize = window.innerWidth < 400 ? '.5rem' : '.7rem';
                  }
                
                  // Attach the function to both onload and onresize events
                  window.onload = adjustFontSize;
                  window.onresize = adjustFontSize;
</script>
    
</html>
