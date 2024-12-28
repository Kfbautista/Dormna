<?php
session_start();
require("PHPMailer/src/PHPMailer.php");
require("PHPMailer/src/SMTP.php");


$mysqli = require "database.php";  // Ensure this file returns the $mysqli object

$userId = $_SESSION["userID"];
$propertyID = $_SESSION['propertyID'];



        if (isset($_POST["submit-btn2"])) {
            $email = $_POST["email"];
            $transaction_id = rand(1000000000, 9999999999);
            $gcash_name = $_POST["gcash_name"];
            $gcash_num = $_POST["gcash_num"];
            $ref_num = $_POST["ref_num"];
            $subs= "premium";

            if ($_FILES["image"]["error"] == 0) {
                $fileName = $_FILES["image"]["name"];
                $fileSize = $_FILES["image"]["size"];
                $tmpName = $_FILES["image"]["tmp_name"];
                $validImageExtension = ['jpg', 'jpeg', 'png'];
                $imageExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                if (in_array(strtolower($imageExtension), $validImageExtension) && $fileSize <= 1000000) {
                    $newImageName = uniqid() . '.' . $imageExtension;
                    if (move_uploaded_file($tmpName, 'img/' . $newImageName)) {

                        $stmt = $mysqli->prepare("INSERT INTO `payment-transaction-tb` (`transaction-id`, `client-gcash-name`, `client-gcash-num`,`transaction-num`, `transaction-img`,`subscription`, `UID`, `PID`) VALUES (?, ?, ?, ?, ?,?,?,?)");
                        $stmt->bind_param("ssssssii", $transaction_id, $gcash_name, $gcash_num,$ref_num, $newImageName, $subs, $userId, $propertyID);
                    
        

                        if ($stmt->execute()) {
                            
                            //mailer
                            $mailTo = $_POST["email"];
                            $htmlContent = "
                                <html>
                                <head>
                                    <title>Invoice</title>
                                </head>
                                <body>
                                    <h1>Invoice for Your Payment</h1>
                                    <p><strong>Transaction ID:</strong> {$transaction_id}</p>
                                    <p><strong>Subscription Type:</strong> {$subs}</p>
                                    <p><strong>Property ID:</strong> {$propertyID}</p>
                                    <p>Thank you for your payment.</p>
                                </body>
                                </html>";
                            
                            $mail = new PHPMailer\PHPMailer\PHPMailer();
                            
                            $mail->SMTPDebug = 0;
                            
                            $mail->isSMTP();
                            
                            $mail->Host = "mail.smtp2go.com";
                            
                            $mail->SMTPAuth = true;
                            
                            $mail->Username = "dormna";
                            $mail->Password = "dormnasmtp";
                            
                            $mail->SMTPSecure ="tls";
                            
                            $mail->Port = "2525";
                            
                            $mail->From = "kfdcbautista@slsu.edu.ph";
                            $mail->FromName = "Dormna Admin";
                            
                            $mail->addAddress($mailTo, "PAYMENT");
                            
                            $mail->isHTML(true);
                            
                            $mail->Subject= "PAYMENT";
                            $mail->Body = $htmlContent;
                            $mail->AltBody ="PlainText";
                         
                            header("Location: premium-payment.php?submitted=true"); 
                            exit();
                        }

                        if (!$stmt->execute()) {
                            echo "Error: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        echo "Failed to move uploaded file.";
                    }
                } else {
                    echo "Invalid file extension or file size too large.";
                }
            } else {
                echo "File upload error: " . $_FILES["image"]["error"];
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
  <title>Payment(Premium)</title>
  <link rel="stylesheet" href="payment_style.css">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">


</head>
<body>

    <div class="container bg-light d-md-flex align-items-center"> 
        <div class="card box1 shadow-sm p-md-5 p-md-5 p-4">
             <div class="fw-bolder mb-4"><span class="fas fa-dollar-sign">
             </span><span class="ps-1">For your subscription payment, either send the amount to the GCash number below or scan the QR code</span>
            </div> 
            
            <div class="d-flex flex-column">


                


                 <div class="d-flex align-items-center justify-content-between text"> 
                    <span class="">Subscription</span> 

                    <span class="fas fa-dollar-sign">
                        <span class="ps-1">Premium</span>
                    </span> 

                </div> 
                
                <div class="d-flex align-items-center justify-content-between text mb-4"> 
                        <span>Fee</span> <span class="fas fa-dollar-sign">
                            <span class="ps-1">1000.00</span></span> 
                </div>
                        
                <div class="border-bottom mb-4">
                  
                </div>
                
                
                
                <div class="d-flex flex-column mb-5">
                    <span class="far fa-calendar-alt text"><span class="ps-2">GCash Payment:</span></span>
                    <span class="ps-3">09502215011</span>
                    <span class="ps-3">Account Name: </span>
                    <span class="ps-3"> Kenneth Francis Bautista</span>
                </div>
                


                    <div class="d-flex align-items-center justify-content-between text mt-5">
                        <div class="d-flex flex-column text"> <span>Pay using QR code: </span> 
                       
                        </div>
                        
                        
                    
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <img src="assets/qr.jpg" alt="QR Code" style="width: 100%; height: 100%;" >
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  
                        
                    <div class="btn btn-primary rounded-circle" data-toggle="modal" data-target="#qrModal">
                        <img src="assets/qr.jpg" />
                        <span class="fas fa-comment-alt"></span></div> 
                    </div> 

            </div> 
        </div>

    <div class="card box2 shadow-sm">
         <div class="d-flex align-items-center justify-content-between p-md-5 p-4"> 
                            <span class="h5 fw-bold m-0">Payment Form</span>
                         <div class="btn btn-primary bar">
                            <span class="fas fa-bars"></span>
                        </div> 
        </div> 
        

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link active" id="paymentInfo-tab" data-toggle="tab" href="#paymentInfo">Payment Info</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="enterReference-tab" data-toggle="tab" href="#enterReference">Transaction Info</a>
            </li>


        </ul>
        


                 <div class="px-md-5 px-4 mb-4 d-flex align-items-center"> 
                
                    
                   
                </div> 


                    <!-- FORM 1 -->
                <div  id="form1">
                <form  class="payment-form" action="premium-payment.php" method="post" enctype="multipart/form-data" style="display:yes;">

                    <div class="row"> 
                        <div class="col-12"> 
                        <div class="d-flex flex-column px-md-5 px-4 mb-4"> 
                            <span>Email Address</span>
                            <div class="inputWithIcon"> 
                                <input class="form-control" name="email" type="text" placeholder="Email used for this account" required> 
                                
                                
                        </div> 
                        </div> 
                

                        <div class="d-flex flex-column px-md-5 px-4 mb-4"> 
                            <span>Gcash Account Name</span>
                            <div class="inputWithIcon"> 
                                <input class="form-control" name="gcash_name" type="text" placeholder="Gcash Name" required> 
                                
                                
                        </div> 
                        </div> 

                        <div class="d-flex flex-column px-md-5 px-4 mb-4"> 
                            <span>Gcash Account Number</span>
                            <div class="inputWithIcon"> 
                                <input class="form-control" name="gcash_num" type="text" placeholder="Gcash Number" required > 
                                
                                
                        </div> 
                        </div> 

                    </div>
                    <div class="d-flex flex-column px-md-5 px-4 mb-4"> 

                      
                        <input  value="Next"  class="field_btn" onclick="showForm(2)">
                        </div>
                  
            
                    </div> 
             
            </div>

<!--  form 2-->
<div  id="form2" style="display:none;">
        <div class="row"> 
            <div class="col-12"> 
            <div class="d-flex flex-column px-md-5 px-4 mb-4"> 
                <span>Enter Gcash Reference Number</span>
                <div class="inputWithIcon"> 
                    <input class="form-control" name="ref_num" type="text" placeholder="Transaction Number" required> 
                    
                    
            </div> 
            </div> 
            
            <div class="d-flex flex-column px-md-5 px-4 mb-4"> 
                <span>Upload Proof of Payment</span> 
            </div> 
            <div class="photo-upload" onclick="document.getElementById('file-upload').click()">
            <label for="images" class="drop-container" id="dropcontainer">
                <span class="drop-title">Drop files here</span>
                <input class = "uploadfile" type="file" id="file-upload" name="image"  accept=".jpg, .jpeg, .png" required>         
              </label>
            
              </div> 
              
            
            </div> 

      
            </div> 

            <div class="d-flex flex-column px-md-5 px-4 mb-4"> 
            <input  type="submit" value="Submit"  name="submit-btn2" class="field_btn " onclick="showForm(3)">
        </div>
    </div>
        
    </form> 
   
    

    <div id="form3" style="display:none;">
            <div class="row"> 
                <div class="col-12"> 
                <div class="d-flex flex-column px-md-5 px-4 mb-4"> 
                   
                    <div class = "successfully_sumbmitted_text">
                        <span><p  class = "successfully_sumbmitted_text">The payment form has been successfully submitted. Please wait while we verify your payment. We will inform you shortly.</p></span> 
                    </div>
                </div> 
    
            
                 <div class="d-flex flex-column px-md-5 px-4 mb-4">     
                <input id="payButtonForm3" type="submit" value="Back Home" onclick="location.href='index.php';"  class="field_btn" style="padding-left: 120px;">
            </div>
        </div>
            
            </div> 
    
            </div> 





</div>     
        
     </div>
                     
</body>
<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if the 'submitted' flag is set in the URL
    const urlParams = new URLSearchParams(window.location.search);
    const submitted = urlParams.get('submitted');

    if (submitted) {
        showForm(3); // Show form 3 if the submission was successful
    }
});

function showForm(formNumber) {
    // Hide all forms
    document.querySelectorAll('.payment-form').forEach(form => {
        form.style.display = 'none';
    });

    // Activate and show the corresponding tab and form
    if (formNumber === 2) {
        document.getElementById('paymentInfo-tab').classList.remove('active');
        document.getElementById('enterReference-tab').classList.add('active');
        document.getElementById('form2').style.display = 'block';
    } else if (formNumber === 3) {
        document.getElementById('form1').style.display = 'none';
        document.getElementById('form2').style.display = 'none';
        document.getElementById('form3').style.display = 'block';
    }
}
</script>





</html>