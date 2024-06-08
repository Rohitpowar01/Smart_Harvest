<?php 
session_start();
ini_set('memory_limit', '-1');
$userlogin = $_SESSION['farmer_login_user'];

require('../sql.php'); // Includes Login Script

function getMrpKg($crop, $conn) {
    $sql = "SELECT mrp_kg FROM production_approx WHERE crop = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $crop);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['mrp_kg'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $trade_crop = $_POST['crops'];
    $crop_quantity = $_POST['trade_farmer_cropquantity'];
    $costingkg = $_POST['trade_farmer_cost'];
    $farmer_fkid = $para1; // Use the farmer's ID from session

    // Get the mrp_kg for the crop
    $mrp_kg = getMrpKg($trade_crop, $conn);

    // Check if costingkg is within the limit
    if ($costingkg <= $mrp_kg) {
        $msp = $costingkg * 0.5;

        // Insert the new trade into farmer_crops_trade
        $sql = "INSERT INTO farmer_crops_trade (farmer_fkid, Trade_crop, Crop_quantity, costperkg, msp) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isidd", $farmer_fkid, $trade_crop, $crop_quantity, $costingkg, $msp);

        if ($stmt->execute()) {
            // Insert data into farmer_history table with initial status "order pending"
            $status = "order pending";
            $sql_history = "INSERT INTO farmer_history (farmer_id, farmer_crop, farmer_quantity, farmer_price, date, Status) VALUES (?, ?, ?, ?, NOW(), ?)";
            $stmt_history = $conn->prepare($sql_history);
            $stmt_history->bind_param("isiss", $farmer_fkid, $trade_crop, $crop_quantity, $costingkg, $status);
            
            if ($stmt_history->execute()) {
                echo "<script>alert('Crop Details Added Successfully');</script>";
                header("Location: ftradecrops.php");
                exit(); // Redirect after successful submission
            } else {
                echo "<script>alert('Error: " . $stmt_history->error . "');</script>";
            }
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error: Cost per kg exceeds the market price');</script>";
    }
}
?>
