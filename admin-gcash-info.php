
<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin-login.php");
 }
?>











<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Information</title>
    <link rel="stylesheet" href="style--dashboard.css"> <!-- Ensure this path is correct -->
</head>
<body class="payment-info-page">
<div class="payment-info-container">
    <h2>GCash Payment</h2>
    <form action="admin-gcash-info.php" method="POST" enctype="multipart/form-data">
        <br>
         <div class="form-group">
            <label for="gcash-name">Email:</label>
            <input type="text" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="gcash-name">GCash Name:</label>
            <input type="text" id="gcash-name" name="gcash_name" required>
        </div>
        <div class="form-group">
            <label for="gcash-number">GCash Number:</label>
            <input type="text" id="gcash-number" name="gcash_num" required pattern="\d*" minlength="11" maxlength="11">
        </div>
        <div class="form-group">
            <label for="file-upload" class="drop-container" id="dropcontainer">
                GCash Qr Code Image:
                <input class="uploadfile" type="file" id="file-upload" name="gcash_qr" accept=".jpg, .jpeg, .png" required>
            </label>
        </div>



        <?php
$mysqli = include "database.php";
if (!$mysqli) {
    die('Database connection failed');
}

if (isset($_POST["submit"])) {
      $email = htmlspecialchars($_POST["email"]);
    $gcash_name = htmlspecialchars($_POST["gcash_name"]);
    $gcash_num = htmlspecialchars($_POST["gcash_num"]);

    // Check if the file input exists and if a file has been uploaded without errors
    if (isset($_FILES["gcash_qr"]) && $_FILES["gcash_qr"]["error"] == 0) {
        $fileName = $_FILES["gcash_qr"]["name"];
        $fileSize = $_FILES["gcash_qr"]["size"];
        $tmpName = $_FILES["gcash_qr"]["tmp_name"];
        $validImageExtension = ['jpg', 'jpeg', 'png'];
        $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($imageExtension, $validImageExtension) && $fileSize <= 10000000) {
            $newImageName = uniqid() . '.' . $imageExtension;
            if (move_uploaded_file($tmpName, 'img/' . $newImageName)) {
                $stmt = $mysqli->prepare("UPDATE `admin-tb` SET `email` = ?, `gcash_name` = ?, `gcash_num` = ?, `qr_image` = ? WHERE `username_admin` = 'admin'");
                $stmt->bind_param("ssss", $email, $gcash_name, $gcash_num, $newImageName);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success' style='background-color: #006400; color: white; padding: 14px 20px; margin: 10px 0; border: none; cursor: pointer; width: 100%; text-align: center;'>Successfully Updated!</div>";
                } else {
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
   
    }
}

$mysqli->close();
?>










        <button type="submit" name="submit">Submit Payment Info</button>
    </form>
</div>
</body>
</html>
