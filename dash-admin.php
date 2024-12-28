
<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin-login.php");
 }
?>

<?php

if (isset( $adminSession )) {
    header("Location: dash-admin.php");
    exit();
 }
?>

<?php

$mysqli = require "database.php";

// Get the current date and time in MySQL format
$currentDateTime = date('Y-m-d H:i:s');

// Prepare a SELECT statement to fetch records that have expired
$sqlSelect = "SELECT * FROM `payment-transaction-tb` WHERE `expiry_date` <= ?" ;
$stmtSelect = $mysqli->prepare($sqlSelect);

if ($stmtSelect) {
    $stmtSelect->bind_param("s", $currentDateTime);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();

    if ($result->num_rows > 0) {
        // Prepare an UPDATE statement to set status to empty for expired records
        $sqlUpdate = "UPDATE `payment-transaction-tb` SET `status` = 'expired' WHERE `expiry_date` <= ?";
        $stmtUpdate = $mysqli->prepare($sqlUpdate);
        
        if ($stmtUpdate) {
            $stmtUpdate->bind_param("s", $currentDateTime);
            $stmtUpdate->execute();
           // echo "Updated " . $stmtUpdate->affected_rows . " records where the subscription has expired.";

            //updating the subscription to NULL
            $sqlUpdateSubs = "UPDATE `property-info` SET `subscription` = '' WHERE `expiry_date` <= ?";
            $stmtUpdateSubs = $mysqli->prepare($sqlUpdateSubs);
            $stmtUpdateSubs->bind_param("s", $currentDateTime);
            $stmtUpdateSubs->execute();

            $stmtUpdateSubs->close();
            $stmtUpdate->close();
        } else {
            echo "Error preparing update statement: " . $mysqli->error;
        }
    } else {
       
    }

    $stmtSelect->close();
} else {
    echo "Error preparing select statement: " . $mysqli->error;
}

$mysqli->close();

?>



<?php

$mysqli = require "database.php";





//premium
$query = "SELECT * FROM `payment-transaction-tb` WHERE `subscription`= 'premium' AND `status` = '' ";
$result = $mysqli->query($query);

$properties = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
}

//basic
$query_basic = "SELECT * FROM `payment-transaction-tb` WHERE `subscription`= 'basic' AND `status` = '' ";
$result_basic = $mysqli->query($query_basic);

$properties_basic = [];
if ($result_basic) {
    while ($row = $result_basic->fetch_assoc()) {
        $properties_basic[] = $row;
    }
}



//succesfull transaction
$query_successful = "SELECT * FROM `payment-transaction-tb` WHERE `status` = 'accepted' ";
$result_successful = $mysqli->query($query_successful);

$properties_successful = [];
if ($result_successful) {
    while ($row = $result_successful->fetch_assoc()) {
        $properties_successful[] = $row;
    }
}


//Rejected transaction
$query_rejected = "SELECT * FROM `payment-transaction-tb` WHERE `status` = 'rejected' ";
$result_rejected = $mysqli->query($query_rejected);

$properties_rejected = [];
if ($result_rejected ) {
    while ($row = $result_rejected ->fetch_assoc()) {
        $properties_rejected[] = $row;
    }
}



//expired payments
$query_expired = "SELECT * FROM `payment-transaction-tb` WHERE `status` = 'expired' ";
$result_expired = $mysqli->query($query_expired);

$properties_expired = [];
if ($result_expired ) {
    while ($row = $result_expired ->fetch_assoc()) {
        $properties_expired[] = $row;
    }
}


//accepted
if (isset($_POST["accept"])) {
    $property_id = $_POST['property_id'];
    $subs = $_POST['subs'];
    $userId = $_POST['user_id'];
    $sql = "UPDATE `property-info` SET subscription = ? WHERE PID = ?";

    $stmt = $mysqli ->prepare($sql);
    $stmt->bind_param("si",$subs, $property_id);
    $stmt->execute();

    if (isset($_POST["accept"])) {

       
        $expiryPremium = date('Y-m-d H:i:s', strtotime('+1 month'));//not fixed
        $expiryBasic = date('Y-m-d H:i:s', strtotime('+1 month'));//not fixed
        $expiryFREE = date("Y-m-d H:i:s", strtotime("+1 month"));//not fixed
        $payment_id = $_POST['payment_id'];
        $status = "accepted";
    
        //SUBSCRIPTION VALIDITY CONDITION
        if($subs == 'premium'){
        $sql = "UPDATE `payment-transaction-tb` SET `status` = ?, `expiry_date` = ? WHERE `payment-id` = ?" ;
    
        $stmt = $mysqli ->prepare($sql);
        $stmt->bind_param("ssi", $status,  $expiryPremium,  $payment_id  );
       if ($stmt->execute()){
        
                                    //notif
                $premSubsNotif= "Congratulations, your premium subscription payment is confirmed! Your accommodation property is now listed in the app. Check it out!";

                $notifSQL =  "INSERT INTO `notification` (`UID`, `notification_msg`) VALUES (?, ?)";
                $notifStmt = $mysqli->prepare($notifSQL);
                                            
                $notifStmt->bind_param("is", $userId ,  $premSubsNotif );
                $notifStmt->execute();
                
                 echo '<script>';
          echo 'alert("You Successfully Accepted the Payment");';
          echo 'window.location.href = "dash-admin.php";'; // Redirects to the dashboard page after the alert
          echo '</script>';
        }


       
        }

        else if ($subs == 'basic') {
            $sql = "UPDATE `payment-transaction-tb` SET `status` = ?, `expiry_date` = ? WHERE `payment-id` = ?" ;
    
            $stmt = $mysqli ->prepare($sql);
            $stmt->bind_param("ssi", $status,  $expiryBasic,  $payment_id  );
            if ($stmt->execute()){
        
                //notif
                $basicSubsNotif= "Congratulations, your basic subscription payment is confirmed! Your accommodation property is now listed in the app. Check it out!";

                $notifSQL =  "INSERT INTO `notification` (`UID`, `notification_msg`) VALUES (?, ?)";
                $notifStmt = $mysqli->prepare($notifSQL);
                                        
                $notifStmt->bind_param("is", $userId ,  $basicSubsNotif );
                $notifStmt->execute();
                
                
                  echo '<script>';
          echo 'alert("You Successfully Accepted the Payment");';
          echo 'window.location.href = "dash-admin.php";'; // Redirects to the dashboard page after the alert
          echo '</script>';
                
                
                }
        } 
        
        else {
            $sql = "UPDATE `payment-transaction-tb` SET `status` = ?, `expiry_date` = ? WHERE `payment-id` = ?" ;
    
            $stmt = $mysqli ->prepare($sql);
            $stmt->bind_param("ssi", $status,  $expiryFREE,  $payment_id  );
            $stmt->execute();
        }
        


    
    }
}


   if (isset($_POST["reject"])) {
        $property_id = $_POST['property_id'];
        $subs = $_POST['subs_reject'];
    
        $sql = "UPDATE `property-info` SET subscription = ? WHERE PID = ?";
    
        $stmt = $mysqli ->prepare($sql);
        $stmt->bind_param("si",$subs, $property_id);
        $stmt->execute();
    
        if (isset($_POST["reject"])) {
    
            $payment_id = $_POST['payment_id'];
            $status = "rejected";
        
            $sql = "UPDATE `payment-transaction-tb` SET `status` = ? WHERE `payment-id` = ?";
        
            $stmt = $mysqli ->prepare($sql);
            $stmt->bind_param("si", $status,   $payment_id  );
            if ($stmt->execute()){
        
                //notif
            $freeTrialNotif= "Sorry, your payment could not be verified. Please review your payment details and try again";

            $notifSQL =  "INSERT INTO `notification` (`UID`, `notification_msg`) VALUES (?, ?)";
            $notifStmt = $mysqli->prepare($notifSQL);
                                    
            $notifStmt->bind_param("is", $userId ,  $freeTrialNotif );
            $notifStmt->execute();
            
            
              echo '<script>';
          echo 'alert("You Successfully Rejected the Payment");';
          echo 'window.location.href = "dash-admin.php";'; // Redirects to the dashboard page after the alert
          echo '</script>';
}
           
        
        
        }

 


}

$adminSession = $_SESSION["admin"];

$mysqli->close();


?>




<!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <title>Admin Dashboard </title>
      <link rel="stylesheet" href="style--dashboard.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
      <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="sidebar">
            <div class="logo"></div>
            <ul class="menu">
                <li class="active">
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

                <li >
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

        <div class="main--content">
            <div class="header--wrapper">
                <div class="header--title">
                    <span>SLSU Admin</span>
                    <h2>Dashboard</h2>
                </div>
                <div class="user--info">
                    <img src="./slsu.jfif" alt=""/>
                </div> 
            </div>
            <div class="card--container">
                <h3 class="main--title">Today's Data</h3>
                <div class="card--wrapper">
<!-- Number of Registered Owners -->

    <?php
    // Including database connection
    $mysqli = require "database.php";

    // SQL to count registered owners
    $query = "SELECT COUNT(*) AS total_owners FROM `user_account`";
    $result = $mysqli->query($query);

    $total_owners = 0; // Default value in case the query fails

    if ($result) {
        $row = $result->fetch_assoc();
        $total_owners = $row['total_owners'];
    } else {
        echo "Error fetching data: " . $mysqli->error;
    }

    $mysqli->close();
    ?>


<div class="info-card light-blue">
    <div class="card--header">
        <div class="amount">
            <span class="title">Registered Owners</span>
            <span class="amount--value"><?php echo $total_owners; ?></span>
        </div>
        <i class="fas fa-users icon dark-blue"></i>
    </div>
    <span class="card-detail">Total Owners</span>
</div>



<!-- Number of Premium Subscriptions -->

<?php
   $mysqli = require "database.php";

   $query = "SELECT COUNT(*) AS premium_count FROM `property-info` WHERE subscription = 'premium'";

   $result = $mysqli->query($query);
   
   if ($result) {
       $row = $result->fetch_assoc();
       $premium_count = $row['premium_count'];
   } else {
       echo "Error: " . $mysqli->error;
   }
   
   $mysqli->close();
   
    ?>




<div class="info-card light-green">
    <div class="card--header">
        <div class="amount">
            <span class="title">Premium Subscriptions</span>
            <span class="amount--value"><?php echo $premium_count; ?></span>
        </div>
        <i class="fas fa-crown icon dark-green"></i>
    </div>
    <span class="card-detail">Total Premium</span>
</div>




<!-- Number of Basic Subscriptions -->



<?php
    // Including database connection
    $mysqli = require "database.php";

    // SQL to count registered owners
    $query = "SELECT COUNT(*) AS basic_count FROM `property-info` WHERE subscription = 'basic'; ";
    $result = $mysqli->query($query);

    $basic_count = 0; // Default value in case the query fails

    if ($result) {
        $row = $result->fetch_assoc();
        $basic_count = $row['basic_count'];
    } else {
        echo "Error fetching data: " . $mysqli->error;
    }

    $mysqli->close();
    ?>


<div class="info-card light-red">
    <div class="card--header">
        <div class="amount">
            <span class="title">Basic Subscriptions</span>
            <span class="amount--value"><?php echo $basic_count; ?></span>
        </div>
        <i class="fas fa-user icon dark-red"></i>
    </div>
    <span class="card-detail">Total Basic</span>
</div>
<!-- Number of Pending Payments -->
<?php
    // Including database connection
    $mysqli = require "database.php";

    // SQL to count registered owners
    $query = "SELECT COUNT(*) AS pending_count FROM `payment-transaction-tb` WHERE `status` = ''; ";
    $result = $mysqli->query($query);

    $pending_count = 0; // Default value in case the query fails

    if ($result) {
        $row = $result->fetch_assoc();
        $pending_count = $row['pending_count'];
    } else {
        echo "Error fetching data: " . $mysqli->error;
    }

    $mysqli->close();
    ?>




<div class="info-card light-purple">
    <div class="card--header">
        <div class="amount">
            <span class="title">Pending Payments</span>
            <span class="amount--value"><?php echo $pending_count; ?></span>
        </div>
        <i class="fas fa-exclamation-triangle icon dark-purple"></i>
    </div>
    <span class="card-detail">Total Pending</span>
</div>
</div>
</div>

<!-- Premium Subscription Table -->



<div class="subscription-table-container">
    <h3 class="premium-title">Pending Premium Payments</h3>
    <div class="scrollable-table">
    <?php if (!empty($properties)): ?>
        
        <table>
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>User ID</th>
                    <th>Property ID</th>
                    <th>Reference No.</th>
                    <th>Proof of Payment </th>
                    <th colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($properties as $property): ?>
                <tr>
                    <form method="post" action="dash-admin.php">
                        <td><?php echo htmlspecialchars($property['transaction-id']); ?></td>
                        <td><?php echo htmlspecialchars($property['UID']); ?></td>
                        <td><?php echo htmlspecialchars($property['PID']); ?></td>
                        <td><?php echo htmlspecialchars($property['transaction-num']); ?></td>
                        <td>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#imageModal<?php echo $property['transaction-id']; ?>">
                                View
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="imageModal<?php echo $property['transaction-id']; ?>" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel<?php echo $property['transaction-id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel<?php echo $property['transaction-id']; ?>">Proof Of Payment</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <img src="img/<?php echo htmlspecialchars($property['transaction-img']); ?>" style="width: 100%; height: auto;">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($property['UID']); ?>">
                            <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($property['PID']); ?>">
                            <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($property['payment-id']); ?>">
                            <input type="hidden" name="subs" value="premium">
                            <button type="submit" class="button" name="accept">Accept</button>
                        </td>
                        
                        
                        <td>
                        <input type="hidden" name="subs_reject" value="">
                        <button class="reject-button" name = "reject">Reject</button>
                    </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-results-message">
            <p>No data available.</p>
        </div>
    <?php endif; ?>
</div>

</div>


<!-- Basic Subscription Table -->
<div class="subscription-table-container">
    <h3 class="basic-title">Pending Basic Payments</h3>
    <div class="scrollable-table">
    <?php if (!empty($properties_basic)): ?>

    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>User ID</th>
                <th>Property ID</th>
                <th>Reference No.</th>
                <th>Proof of Payment </th>
                <th colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($properties_basic as $property): ?>
            <tr>
                <form method="post" action="dash-admin.php">
                    <td><?php echo htmlspecialchars($property['transaction-id']); ?></td>
                    <td><?php echo htmlspecialchars($property['UID']); ?></td>
                    <td><?php echo htmlspecialchars($property['PID']); ?></td>
                    <td><?php echo htmlspecialchars($property['transaction-num']); ?></td>
                    <td>
                   
                             <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#imageModal<?php echo $property['transaction-id']; ?>">
                                View
                            </button>

                            <!-- The Modal -->
                            <div class="modal fade" id="imageModal<?php echo $property['transaction-id']; ?>" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel<?php echo $property['transaction-id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel<?php echo $property['transaction-id']; ?>">Proof Of Payment</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Image displayed inside the modal -->
                                            <img src="img/<?php echo htmlspecialchars($property['transaction-img']); ?>" style="width: 100%; height: auto;">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    </td>

                    <td>
                        <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($property['PID']); ?>">
                        <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($property['payment-id']); ?>">
                        <input type="hidden" name="gcash_num" value="<?php echo htmlspecialchars($property['client-gcash-num']); ?>">
                        <input type="hidden" name="ref_num" value="<?php echo htmlspecialchars($property['transaction-num']); ?>">
                        <input type="hidden" name="subs" value="basic">
                        <button type="submit" class="accept-button" name="accept">Accept</button>
                    </td>
                        
                        <td><input type="hidden" name="subs_reject" value="">
                            <button class="reject-button" name = "reject">Reject</button>
                        </td>
                </form>
            </tr>
            <?php endforeach; ?>

            
        </tbody>
        
        <?php else: ?>
        <div class="no-results-message">
            <p>No data available.</p>
        </div>
    <?php endif; ?>
    </table>
</div></div>


<!-- Successful Transactions -->
<div class="subscription-table-container">
    <h3 class="successful-title">Successful Transactions</h3>
    <div class="scrollable-table">
    <?php if (!empty($properties_successful)): ?>
      
    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>User Id</th>
                <th>Property ID</th>
                <th>Gcash No.</th>
                <th>Reference No.</th>
                <th>Subscription</th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach ($properties_successful as $property): ?>
            <tr>
               
                    <td><?php echo htmlspecialchars($property['transaction-id']); ?></td>
                    <td><?php echo htmlspecialchars($property['UID']); ?></td>
                    <td><?php echo htmlspecialchars($property['PID']); ?></td>
                    <td><?php echo htmlspecialchars($property['client-gcash-num']); ?></td>
                    <td><?php echo htmlspecialchars($property['transaction-num']); ?></td>
                    <td><?php echo htmlspecialchars($property['subscription']); ?></td>
                    
               
            </tr>
            <?php endforeach; ?>
                 <?php else: ?>
        <div class="no-results-message">
            <p>No data available.</p>
        </div>
    <?php endif; ?>
        </tbody>
        
   
    </table>
</div></div>





<!-- Rejected Transactions Table -->
<div class="subscription-table-container">
    <h3 class="rejected-title">Rejected Transactions</h3>
    <div class="scrollable-table">
    <?php if (!empty($properties_rejected)): ?>

    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>User Id</th>
                <th>Property ID</th>>
                <th>Gcash No.</th>
                <th>Reference No.</th>
                <th>Subscription</th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach ($properties_rejected as $property): ?>
            <tr>
               
                    <td><?php echo htmlspecialchars($property['transaction-id']); ?></td>
                    <td><?php echo htmlspecialchars($property['UID']); ?></td>
                    <td><?php echo htmlspecialchars($property['PID']); ?></td>
                    <td><?php echo htmlspecialchars($property['client-gcash-num']); ?></td>
                    <td><?php echo htmlspecialchars($property['transaction-num']); ?></td>
                    <td><?php echo htmlspecialchars($property['subscription']); ?></td>
                    
               
            </tr>
            <?php endforeach; ?>
        </tbody>
        <?php else: ?>
        <div class="no-results-message">
            <p>No data available.</p>
        </div>
    <?php endif; ?>
    </table>
</div>
</div>



<!-- expired -->
<div class="subscription-table-container">
    <h3 class="expired-title">Expired Subscription Payments</h3>
    <div class="scrollable-table">
    <?php if (!empty($properties_expired)): ?>
 
    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>User Id</th>
                <th>Property ID</th>
                <th>Gcash No.</th>
                <th>Reference No.</th>
                <th>Subscription</th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach ($properties_expired as $property): ?>
            <tr>
               
                    <td><?php echo htmlspecialchars($property['transaction-id']); ?></td>
                    <td><?php echo htmlspecialchars($property['UID']); ?></td>
                    <td><?php echo htmlspecialchars($property['PID']); ?></td>
                    <td><?php echo htmlspecialchars($property['client-gcash-num']); ?></td>
                    <td><?php echo htmlspecialchars($property['transaction-num']); ?></td>
                    <td><?php echo htmlspecialchars($property['subscription']); ?></td>
                    
               
            </tr>
            <?php endforeach; ?>
        </tbody>
        <?php else: ?>
        <div class="no-results-message">
            <p>No data available.</p>
        </div>
    <?php endif; ?>
    </table>
    </div>
</div>











</div>
</body>

<script>
    document.getElementById('openPaymentInfo').addEventListener('click', function(e) {
        e.preventDefault(); // Prevent default anchor action
        window.open('payment--info.html', '_blank'); // Open the payment info page in a new tab
    });
    </script>
    <!-- Bootstrap CSS -->

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</html>