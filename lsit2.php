<?php
session_start();


if (isset($_POST['submit'])) {
    $_SESSION['propertyID'] = $_POST['propertyId'];
    header("Location: subscription.php");
    exit();
}


?>







<?php

$userId = $_SESSION["userID"];
$mysqli = require_once "database.php";


// Fetch properties for female gender

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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <title>User Dashboard</title>

    <style>
    body{
    padding:50px;
}
.container1{
    max-width: 600px;
    margin:0 auto;
    padding:50px;
    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
}
.form-group{
    margin-bottom:30px;
}
  </style>



</head>
<body>
    <div class="container1">
        <h1>Login Successfully!</h1>
        <a href="logout.php" class="btn btn-warning">Logout</a>
        <a href="form.php" class="btn btn-warning">add property</a>
    </div>


   
<section class="properties">


<div class="container" id="ResultSearch">
    <h1 class="title">YOUR ACCOMODATION PROPERTY</h1>
    <div class="properties-grid" id="propertiesGrid">


        <?php foreach ($properties as $index => $property): ?>
            <div class="property-card"
                 data-property-id="<?= htmlspecialchars($property['PID']) ?>"
                 data-property-name="<?= htmlspecialchars($property['Pname']) ?>"
                 data-property-address="<?= htmlspecialchars($property['PAddress']) ?>"
                 data-property-type="<?= htmlspecialchars($property['PAcc-type']) ?>"
                 data-property-price="<?= htmlspecialchars($property['Pprice']) ?>"
                 data-property-fb="<?= htmlspecialchars($property['PFBname']) ?>"
                 data-property-contact-name="<?= htmlspecialchars($property['Pcontact-name']) ?>"
                 data-property-contact-num="<?= htmlspecialchars($property['Pcontact-num']) ?>"
                 data-property-slots="<?= htmlspecialchars($property['PNum-slot-avail']) ?>"
                 data-property-image="img/<?= htmlspecialchars($property['Pimage']) ?>"
                 data-property-gender="<?= htmlspecialchars($property['PGender']) ?>"
                 data-property-dscrption="<?= htmlspecialchars($property['PDscrpt']) ?>"
                 
                 >


    
    <img src="img/<?= htmlspecialchars($property['Pimage']) ?>" alt="Property Image" class="property-image">
    <div class="property-details-initial">
                    <h2 class="property-address"><?= htmlspecialchars($property['Pname']) ?></h2>
                    <p class="property-address"><?= htmlspecialchars($property['PAddress']) ?></p>
                    <p class="property-type"><?= htmlspecialchars($property['PAcc-type']) ?></p>
                    <p class="property-price">₱<?= htmlspecialchars($property['Pprice']) ?>/Month</p>
                    <div class="property-icons">
                        <p class="property-slots">🛏 <?= htmlspecialchars($property['PNum-slot-avail']) ?></p>
                    </div>
                </div>
  </div>
<?php endforeach; ?>

          
        </div>

       
</section>



<div id="propertyDetails" class="property-details-container"></div>





<script > 
document.addEventListener('DOMContentLoaded', function() {
  const propertyCards = document.querySelectorAll('.property-card');
  const detailsContainer = document.getElementById('propertyDetails');

  propertyCards.forEach(card => {
    card.addEventListener('click', function() {
     
    const propertyId = this.dataset.propertyId;
      const propertyName = this.dataset.propertyName;
      const propertyAddress = this.dataset.propertyAddress;
      const propertyType = this.dataset.propertyType;
      const propertyPrice = this.dataset.propertyPrice;
      const propertyImage = this.dataset.propertyImage;
      const propertyFb = this.dataset.propertyFb;
      const propertyContactName = this.dataset.propertyContactName;
      const propertyContactNum = this.dataset.propertyContactNum;
      const propertySlots = this.dataset.propertySlots;
      const propertyGender = this.dataset.propertyGender;
      const propertyDescription = this.dataset.propertyDscrption;

      const propertyDetails = `
        <div class="property-details">
          <img src="${propertyImage}" alt="Property Image" class="property-image-full">
          <div class="property-info">
            <h2>${propertyId}</h2>
            <h2>${propertyName}</h2>
            <p>Address: ${propertyAddress}</p>
            <p>Type: <span>${propertyType}</span></p>
            <p>Price: <span class="price">₱${propertyPrice}/Month</span></p>
            <p>Facebook: ${propertyFb}</p>
            <p>Contact: ${propertyContactName}, ${propertyContactNum}</p>
            <p>Slots Available: ${propertySlots}</p>
            <p>Gender: ${propertyGender}</p>
            <p>Description: ${propertyDescription}</p>
            <button class="field_btn" name="submit" onclick="location.href='#ResultSearch';">Back</button>


            <form id="propertyForm" action="index.php" method="post" style="display:yes;">
            <input value = "${propertyId}" type="hidden" name="propertyId" id="hiddenPropertyId">
 

          <button class="field_btn" name="submit" onclick="location.href='subscription.php';">Buy Subsctiption</button>


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








</script>












</body>
</html>