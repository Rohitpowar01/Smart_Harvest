<?php
session_start();
require('../sql.php'); // Includes SQL connection script

if (isset($_POST['add_to_cart'])) {
    $crop = $_POST['crops'];
    $quantity = (int)$_POST['quantity'];
    $tradeID = (int)$_POST['tradeid'];
    $price = (float)$_POST['price'];
    $cust_id = (int)$_POST['cust_id'];

    if ($crop && $quantity > 0 && $tradeID && $price >= 0) {
        // Insert or update cart table using prepared statements
        $query = "INSERT INTO `cart` (`cust_id`, `cropname`, `quantity`, `price`) 
          VALUES (?, ?, ?, ?) 
          ON DUPLICATE KEY UPDATE `quantity` = `quantity` + VALUES(`quantity`)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isid", $cust_id, $crop, $quantity, $price);

        if ($stmt->execute()) {
            // Update stock quantity in production_approx table
            $update_query = "UPDATE production_approx SET quantity = quantity - ? WHERE crop = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("is", $quantity, $crop);
            $update_stmt->execute();
            $update_stmt->close();

            // Check if shopping cart session exists
            if (isset($_SESSION["shopping_cart"])) {
                // Check if item already exists in shopping cart
                $item_array_id = array_column($_SESSION["shopping_cart"], "item_id");
                if (!in_array($tradeID, $item_array_id)) {
                    // Add item to shopping cart session
                    $item_array = array(
                        'item_id' => $tradeID,
                        'item_name' => $crop,
                        'item_price' => $price,
                        'item_quantity' => $quantity
                    );
                    array_push($_SESSION['shopping_cart'], $item_array);
                } else {
                    echo '<script>alert("Item Already Added")</script>';
                }
            } else {
                // Create new shopping cart session
                $item_array = array(
                    'item_id' => $tradeID,
                    'item_name' => $crop,
                    'item_price' => $price,
                    'item_quantity' => $quantity
                );
                $_SESSION["shopping_cart"] = array($item_array);
            }
            // Redirect to cbuy_crops.php
            header("Location: cbuy_crops.php?action=add&id=$tradeID");
            exit(); // Ensure script execution stops after redirection
        } else {
            // Handle query execution error
            echo '<script>alert("Error adding item to cart")</script>';
        }
        $stmt->close();
    } else {
        // Handle invalid input
        echo '<script>alert("Invalid input")</script>';
    }
} else {
    // Handle case where add_to_cart is not set
    echo '<script>alert("No item to add")</script>';
}

$conn->close();
?>
