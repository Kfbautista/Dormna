<?php
$mysqli = require_once "database.php";
$currentDateTime = date('Y-m-d H:i:s');

$queryPremium = "SELECT * FROM `property-info` WHERE `subscription`='premium'
AND `expiry_date` >= '$currentDateTime' AND `PAcc-type` = 'Dormitory' ORDER BY RAND()";
$resultPremium = $mysqli->query($queryPremium);

$propertiesPremium = [];
if ($resultPremium) {
    while ($row = $resultPremium->fetch_assoc()) {
        $propertiesPremium[] = $row;
    }
}


$queryPremiumBH = "SELECT * FROM `property-info` WHERE `subscription`='premium' AND `expiry_date` >= '$currentDateTime' AND `PAcc-type` = 'BoardingHouse' ORDER BY RAND()";
$resultPremiumBH = $mysqli->query($queryPremiumBH);

$propertiesPremiumBH = [];
if ($resultPremiumBH) {
    while ($row = $resultPremiumBH->fetch_assoc()) {
        $propertiesPremiumBH[] = $row;
    }
}

$mysqli->close();
?>


<?php
           
 $mysqli = require "database.php";

                         
$query = "SELECT `gcash_num` FROM `admin-tb` WHERE `Admin_Id`= '1' ";
 $Result = $mysqli->query($query);
if ($Result) {
  if ($Row = $Result->fetch_assoc()) {
   $contactnum = $Row['gcash_num'];

     }}
      $mysqli->close();
 ?>
<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css"
      
      rel="stylesheet"
    />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" 
    rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="styles.css" />
    <title>DormNa</title>
    
    
    
    <style>
        
        html {
  box-sizing: border-box;
}

      .column {
  float: left;
  width: 33.3%;
  margin-bottom: 16px;
  padding: 0 8px;
}

.card {
    padding:50px;
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
  margin: 8px;
}

.about-section {
  padding: 50px;
  text-align: center;
  background-color: #474e5d;
  color: white;
}

.container {
  padding: 0 16px;
}

.container::after, .row::after {
  content: "";
  clear: both;
  display: table;
}
.title {
  color: grey;
}

.button:hover {
  background-color: #555;
}

@media screen and (max-width: 650px) {
  .column {
    width: 100%;
    display: block;
  }
}
    </style>
  </head>
  <body>
    <nav_index>
      <div class="nav__bar">
        <div class="nav__header">
            
            <div class="logo nav__logo">
                      <img src="assets/SLSU-LOGO.png" style="width:60px; height:60px;" 
                      onload="this.style.width = this.style.height = window.innerWidth < 400 ? '40px' : '60px';"
                      onresize="this.style.width = this.style.height = window.innerWidth < 400 ? '40px' : '60px';" />
                        <span id="slsu" style="font-size: .7rem;">Southern Luzon State University<br /></span>
        
        
                    <hr width="1" size="70px" style="0 auto" />
             
                    <img src="assets/dormna_logo.png" style="width:160px;height:60px;" 
                    onload="this.style.height = window.innerWidth < 400 ? '40px' : '60px';" 
                    onresize="this.style.height = window.innerWidth < 400 ? '40px' : '60px';" />

           
          </div>
          
          
          <div class="nav__menu__btn" id="menu-btn">
            <i class="ri-menu-line"></i>
          </div>
        </div>

        <ul class="nav__links" id="nav-links">
              <li><a href="#home">Home</a></li>
              <li><a href="#Dormitories">Dormitory</a></li>
               <li><a href="#Boarding_Houses">Boarding House</a></li>
              <li><a href="list.php">Search</a></li>
              <li><a href="#about">About Us</a></li> 
       
            </ul>
       

        
        
      </div>
    </nav_index>

    
    <header class="header" id="home">
    
      <div class="login-container" id="">
        <div class="login-icon"></div>
        <div class="login-text">Are you an Accomodation Owner?</div>
        <button class="login-button" onclick="location.href='login.php';">Login Here</button>
      </div>

      
      <div class="section__container header__container">
        
        <p class="section__subheader">Where distance is not a barrier to your education</p>
        <h1>FIND YOUR HOME<br />AWAY FROM HOME</h1>
   
        <button class="btn" onclick="location.href='#about';">Explore</button>
     
 </div>   
    </section>
    </header>
    <?php
   $mysqli = require "database.php";

   $query = "SELECT COUNT(*) AS dorm_count FROM `property-info` WHERE `PAcc-type` = 'Dormitory' AND  `subscription` != ''";

   $result = $mysqli->query($query);
   
   if ($result) {
       $row = $result->fetch_assoc();
       $dorm_count = $row['dorm_count'];
   } else {
       echo "Error: " . $mysqli->error;
   }
   
   
   $mysqli->close();
   
    ?>


<br><br>
    <ul class="menu__banner">
      <li>
        <span><i class="ri-community-line"></i></span>
        <h4><?php echo $dorm_count?>+</h4>
        <p>Dormitories Listed</p>
      </li>
      <li>

      <?php
   $mysqli = require "database.php";

   $query = "SELECT COUNT(*) AS bh_count FROM `property-info` WHERE `PAcc-type` = 'BoardingHouse' AND  `subscription` != ''";

   $result = $mysqli->query($query);
   
   if ($result) {
       $row = $result->fetch_assoc();
       $bh_count = $row['bh_count'];
   } else {
       echo "Error: " . $mysqli->error;
   }
   
   $mysqli->close();
   
    ?>

        <span><i class="ri-home-2-line"></i></span>
        <h4><?php echo $bh_count?>+</h4>
        <p>Boarding Houses Listed</p>
      </li>
      <li>
        <span><i class="ri-map-pin-line"></i></span>
        <h4>Lucban</h4>
        <p>Exclusive Location</p>
      </li>

      <?php
   $mysqli = require "database.php";

   $query = "SELECT SUM(`PNum-slot-avail`) AS slots_count FROM `property-info` WHERE `subscription` != ''";

   $result = $mysqli->query($query);

   if ($result) {
       $row = $result->fetch_assoc();
       $totalAvailableSlots = $row['slots_count'];
    
   } else {
       echo "Error: " . $mysqli->error;
   }
   
   
   $mysqli->close();
   
    ?>

      <li>
        <span><i class="ri-group-line"></i></span>
        <h4><?php  echo $totalAvailableSlots?>+</h4>
        <p>Available Slots</p>
      </li>
    </ul>
    



    

    <section class="about" id="about">
        

        
        
        
        
        
      <div class="section__container about__container">
        <div class="about__grid">
          <div class="about__image">
            <img src="assets/dormitoryIMG.jpg" alt="about" />
          </div>
          <div class="about__card">
            <div class="iconDorm">
              <img src="assets/dormitory.png" alt="about" />
              </div>
            <h4>Dormitory</h4>
            <p>Dormitories provide shared living spaces with study areas, kitchens, and recreational
               facilities. They are located near educational institutions, reducing commute times 
               and ensuring a secure environment.
            </p>
          </div>
          <div class="about__image">
            <img src="assets/boardinghouse.jpg  " alt="about" />
          </div>
          <div class="about__card">
            <div class="iconBH">
              <img src="assets/boarding house.png" alt="about" />
              </div>
            <h4>Boarding House</h4>
            <p>Boarding houses offer affordable, independent living with individual
               or shared rooms and basic facilities near campuses, providing convenience,
                freedom, and privacy.</p>
          </div>
        </div>
        <div class="about__content">
          <p class="section__subheader">ABOUT US</p>
          <h2 class="section__header">Discover <br> Our Website</h2>
          <p class="section__description">
            Welcome to DormNa! DormNa is a tool designed to help SLSU students find dormitories
             and boarding houses through its website and Campus kiosk, offering customizable filters
              for a personalized search experience. Meanwhile, accommodation owners can highlight their 
              offerings by purchasing a subscription on the platform.
          </p>
          <button class="btn" onclick="location.href='list.php';">Find Now</button>
        </div>
        


        
        
        
        
      </div>
      
      
      
      
    </section>
  
    <section class="room__container" id="room">
      <div class="Acomodation__header">
        <div id="Dormitories">
          <p class="section__subheader">Dormitories</p>
          <h2 class="section__header">Suggested Dormitories</h2>
        </div>
        
      </div>

      <section class="properties">
    <div class="container" id="premium_dorm">
        <div class="properties-grid-featured" id="propertiesGrid">
            <?php
            $max_display = 4; 
            $count = 0;
            foreach ($propertiesPremium as $index => $property):
                if ($count >= $max_display) break; 
                ?>
                <div class="property-card-featured"
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
                                $slot =  "<img src=assets/premium.png />";

                              } elseif ($slot <= 0) {
                                $slot = "<img src= assets/occupied.png />";
                              }
                              ?>
        
        <span class="iconPremium"><?php echo $slot?></span>

                        <img src="img/<?= htmlspecialchars($property['Pimage']) ?>" 
                        alt="Property Image" class="property-image">
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

                    <h2 class="property-address"><?= htmlspecialchars($property['Pname']) ?> 
                    <i class="<?php echo   $accredited ?>"> </i></h2>
                    <p class="property-address"><i class="fas fa-map-marker-alt"></i>&nbsp 
                    <?= htmlspecialchars($property['PBrgy']) ?>,
                     <?= htmlspecialchars($property['PStreet']) ?></p>

                    <p class="property-type"><i class="fas fa-home"></i>&nbsp
                     <?= htmlspecialchars($property['PAcc-type']) ?></p>
                    <p class="property-price"><i class="fas fa-peso-sign"></i>&nbsp 
                    <?= htmlspecialchars($property['Pprice']) ?>/Month  
                                  &nbsp&nbsp&nbsp&nbsp&nbsp
                        <?php
                        if (htmlspecialchars($property['PWifi']) == 'Yes') {
                            echo '<i class="fas fa-wifi"></i> Free WiFi'; 
                        } else {
                     
                        }
                        ?>
                   </p>

                  
                  </p><br>
                  <div class="property-icons" >
                        <p class="property-slots"><i class="fas fa-bed">&nbsp
                           </i> <?= htmlspecialchars($property['PNum-slot-avail']) ?>
                           &nbsp&nbsp&nbsp&nbsp&nbsp
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
                <?php
                $count++; 
            endforeach;
            ?>
        </div>
    </div>
</section>
      <div class="section__nav">
        <button class="btn" onclick="location.href='list.php';">Show more</button>
      </div>

    </section>


    <section class="room__container" id="room">

      <div class="Acomodation__header">
      <div id="Boarding_Houses">
        <p class="section__subheader">Boarding House</p>
        <h2 class="section__header">Suggested Boarding Houses</h2>
      </div>
      </div>
      <section class="properties">
    <div class="container" id="premium_BH">
        <div class="properties-grid-featured" id="propertiesGrid">
            <?php
            $max_display = 4;
            $count = 0; 
            foreach ($propertiesPremiumBH as $index => $property):
                if ($count >= $max_display) break; 
                ?>
                <div class="property-card-featured-BH"
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
                                $slot =  "<img src=assets/premium.png />";

                              } elseif ($slot <= 0) {
                                $slot = "<img src= assets/occupied.png />";
                              }
                              ?>
   
        <span class="iconPremium"><?php echo $slot?></span>
                    
                        <img src="img/<?= htmlspecialchars($property['Pimage']) ?>" 
                        alt="Property Image" class="property-image">
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

                    <h2 class="property-address"><?= htmlspecialchars($property['Pname']) ?>
                     <i class="<?php echo   $accredited ?>"> </i></h2>
                    <p class="property-address"><i class="fas fa-map-marker-alt"></i>&nbsp 
                    <?= htmlspecialchars($property['PBrgy']) ?>, <?= htmlspecialchars($property['PStreet']) ?></p>

                    <p class="property-type"><i class="fas fa-home"></i>&nbsp 
                    <?= htmlspecialchars($property['PAcc-type']) ?></p>
                    <p class="property-price"><i class="fas fa-peso-sign"></i>&nbsp 
                    <?= htmlspecialchars($property['Pprice']) ?>/Month  
                                  &nbsp&nbsp&nbsp&nbsp&nbsp
                        <?php
                        if (htmlspecialchars($property['PWifi']) == 'Yes') {
                            echo '<i class="fas fa-wifi"></i> Free WiFi'; 
                        } else {
                            
                        }
                        ?>
                   </p>

                  
                  </p><br>
                  <div class="property-icons" >
                        <p class="property-slots"><i class="fas fa-bed">&nbsp </i>
                         <?= htmlspecialchars($property['PNum-slot-avail']) ?>&nbsp&nbsp&nbsp&nbsp&nbsp
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
                <?php
                $count++; 
            endforeach;
            ?>
        </div>
    </div>
</section>




<div class="section__nav">
        <button class="btn" onclick="location.href='list.php';">Show more</button>
      </div>

      <div id="propertyDetails" class="property-details-container"></div>
    </section>


    <section class="section__container feature__container" id="feature">
      <p class="section__subheader">FEATURES</p>
      <h2 class="section__header">DormNa Core Features</h2>
      <div class="feature__grid">
        <div class="feature__card">
          <span><i class="ri-search-line"></i></span>
          <h4>Search</h4>
          <p>
         SLSU Students can easily search for the name or location of
          thier preferred dormitories and boarding houses.
          </p>
        </div>
        <div class="feature__card">
          <span><i class="ri-filter-3-line"></i></span>
          <h4>Search Filters</h4>
          <p>
          Use search filters to refine search results based on specific 
          criteria such as price range, amenities, utilities included in the rent, 
          and gender-specific accommodation.
          </p>
        </div>
        <div class="feature__card">
          <span><i class="ri-building-line"></i></span>
          <h4>Exclusively for SLSU Students</h4>
          <p>
            Designed for SLSU students, offering accommodations that meet 
            the needs and preferences of students studying in SLSU(Main Campus).
          </p>
        </div>
        <div class="feature__card">
          <span><i class="ri-vip-crown-line"></i></span>
          <h4>Property Subscription Model</h4>
          <p>
            Accommodation owners can showcase their properties through a 
            subscription model, gaining access to a targeted audience of students.
          </p>
        </div>
        <div class="feature__card">
          <span><i class="ri-map-pin-2-line"></i></span>
          <h4>Lucban Location Focus</h4>
          <p>
            All listings are located in Lucban, ensuring that SLSU students 
            find convenient accommodations close to the SLSU(Main Campus).
          </p>
        </div>
        <div class="feature__card">
          <span><i class="ri-information-line"></i></span>
          <h4>Campus Kiosk Integration</h4>
          <p>
            A unique feature offering campus kiosks where students can 
             DormNa directly, making it easier to find accommodations.
          </p>
        </div>
      </div>
    </section>
    


<br><br><br><br>
    <footer class="footer">
      <div class="section__container footer__container">
        <div class="footer__col">
          <div class="logo footer__logo">
              <img src="assets/SLSU-LOGO.png" style="width:50px;height:50px;" />  <span style=" font-size: .5rem;">Southern Luzon State University<br /></span>
          <hr width="1" size="50px" style="0 auto" />
          <img src="assets/dormna_logo.png" style="width:150px;height:50px;" />
          
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
      </div>
      <div class="footer__bar">
        Copyright © 2024 DormNa. All rights reserved.
      </div>
    </footer>

    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="main.js"></script>



    <script > 
document.addEventListener('DOMContentLoaded', function() {
  const propertyCards = document.querySelectorAll('.property-card-featured');
  const detailsContainer = document.getElementById('propertyDetails');

  propertyCards.forEach(card => {
    card.addEventListener('click', function() {
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



      const propertyDetails = `
      <div class="property-details">
          <img src="${propertyImage}" alt="Property Image" class="property-image-full">
          <div class="property-details_2">
          <div class="property-info">
         
            <h2>Name: ${propertyName}</h2>
            <p>Accredited by SLSU: <i class="${propertyAccredited === "yes" ?
             'fas fa-check-circle' : 'fas fa-times-circle'}" 
             style="color: ${propertyAccredited === "yes" ? 'green' : 'red'};"></i></p>
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
              Wifi: <i class="${propertyWifi === "Yes" ? 'fas fa-check-circle'
               : 'fas fa-times-circle'}" style="color: ${propertyWifi === "Yes" ? 'green' : 'red'};"></i>
              Water: <i class="${propertyWater === "Yes" ? 'fas fa-check-circle'
               : 'fas fa-times-circle'}" style="color: ${propertyWater === "Yes" ? 'green' : 'red'};"></i>
              Electric: <i class="${propertyElectric === "Yes" ? 'fas fa-check-circle'
              : 'fas fa-times-circle'}" style="color: ${propertyElectric === "Yes" ? 'green' : 'red'};"></i>
            </p>
            <button class="field_btn" name="submit" onclick="location.href='#premium_dorm';">Back</button>
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


<script > 
document.addEventListener('DOMContentLoaded', function() {
  const propertyCards = document.querySelectorAll('.property-card-featured-BH');
  const detailsContainer = document.getElementById('propertyDetails');

  propertyCards.forEach(card => {
    card.addEventListener('click', function() {
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


      const propertyDetails = `
      <div class="property-details">
          <img src="${propertyImage}" alt="Property Image" class="property-image-full">
          <div class="property-details_2">
          <div class="property-info">
         
            <h2>Name: ${propertyName}</h2>
            <p>Accredited by SLSU: <i class="${propertyAccredited === "yes" ? 
            'fas fa-check-circle' : 'fas fa-times-circle'}" style="color: 
            ${propertyAccredited === "yes" ? 'green' : 'red'};"></i></p>
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
              Wifi: <i class="${propertyWifi === "Yes" ? 'fas fa-check-circle'
               : 'fas fa-times-circle'}" style="color: ${propertyWifi === "Yes" ? 'green' : 'red'};"></i>
              Water: <i class="${propertyWater === "Yes" ? 'fas fa-check-circle'
               : 'fas fa-times-circle'}" style="color: ${propertyWater === "Yes" ? 'green' : 'red'};"></i>
              Electric: <i class="${propertyElectric === "Yes" ? 'fas fa-check-circle'
               : 'fas fa-times-circle'}" style="color: ${propertyElectric === "Yes" ? 'green' : 'red'};"></i>
            </p>
            <button class="field_btn" name="submit" onclick="location.href='#premium_BH';">Back</button>
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
              function adjustFontSize() {
                var element = document.getElementById('slsu');
                element.style.fontSize = window.innerWidth < 400 ? '.5rem' : '.7rem';
              }
            
              
              window.onload = adjustFontSize;
              window.onresize = adjustFontSize;
              
              
              
              
        
const sections = document.querySelectorAll('div[id]');
const navLinks = document.querySelectorAll('.nav__links a');

function checkInView() {
  sections.forEach(section => {
    const top = section.offsetTop - 50;
    const height = section.offsetHeight;
    const id = section.getAttribute('id');
    
    if (window.scrollY >= top && window.scrollY < top + height) {
      navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href').slice(1) === id) {
          link.classList.add('active');
        }
      });
    }
  });
}
window.addEventListener('scroll', checkInView);
              
</script>

  </body>
</html>

