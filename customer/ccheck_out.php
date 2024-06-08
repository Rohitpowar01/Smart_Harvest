<?php
include('csession.php'); // Include session file
include('../sql.php'); // Include SQL connection file

// Check if user is logged in
if (!isset($_SESSION['customer_login_user'])) {
    header("location: ../index.php"); // Redirect to home page
    exit(); // Stop execution after redirection
}

// Fetch customer details from the session
$email = $_SESSION['customer_login_user'];
$res = mysqli_query($conn, "SELECT * FROM custlogin WHERE email='$email'");
$row = mysqli_fetch_assoc($res);
$cust_id = $row['cust_id'];
$cust_name = $row['cust_name'];
$para2 = $row['cust_name'];

$total_price = 0;

// Assuming you have a session variable $_SESSION['shopping_cart'] containing the items in the cart
if (!empty($_SESSION['shopping_cart'])) {
    foreach ($_SESSION['shopping_cart'] as $item) {
        $crop_name = $item['item_name'];
        $quantity = $item['item_quantity'];
        $price = $item['item_price'];
        $total_price += $price;

        // Update the quantity of the crop in the farmer_crops_trade table
        $update_query = "UPDATE farmer_crops_trade SET Crop_quantity = Crop_quantity - ? WHERE Trade_crop = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("is", $quantity, $crop_name);
        $update_stmt->execute();
        $update_stmt->close();

        // Save the order details in the database
        $insert_query = "INSERT INTO orders (cust_id, crop_name, quantity, price, status) VALUES (?, ?, ?, ?, 'order pending')";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("isii", $cust_id, $crop_name, $quantity, $price);
        $insert_stmt->execute();
        $insert_stmt->close();

       }

    // Clear the shopping cart session
    unset($_SESSION['shopping_cart']);

    // Clear the cart for the specific customer from the database
    $clear_cart_query = "DELETE FROM cart WHERE cust_id = ?";
    $clear_cart_stmt = $conn->prepare($clear_cart_query);
    $clear_cart_stmt->bind_param("i", $cust_id);
    $clear_cart_stmt->execute();
    $clear_cart_stmt->close();

    $emailContent = "<h1>Order Confirmation</h1><br><h3>Thank you for placing your order, $cust_name !</h3? <br> <p>Total Price of your order = $total_price Rs</p><br><p> Item will be delivered at your doorstep within 7 days.</p>";

    // Send the email using PHPMailer
    if (smtp_mailer($email, 'Order Confirmation', $emailContent)) {
        // Display a confirmation message to the user
        ?>
        <!DOCTYPE html>
        <html>
        <?php include('cheader.php'); ?>
        <body class="bg-white" id="top">
            <?php include('cnav.php'); ?>
            <section class="section section-shaped section-lg">
                <div class="shape shape-style-1 shape-primary">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 mx-auto text-center">
                            <span class="badge badge-danger badge-pill mb-3">Order Processing</span>
                        </div>
                    </div>
                    <div class="row row-content">
                        <div class="col-md-12 mb-3">
                            <div class="card text-white bg-gradient-success mb-3">
                                <div class="card-header">
                                    <span class="text-success display-4">Order Placed Successfully</span>
                                </div>
                                <div class="card-body">
                                    <p>Thank you for placing your order, <?php echo $cust_name; ?>! Your order has been successfully received.</p>
                                    <p>An email confirmation will be sent to you shortly.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php require("footer.php"); ?>
        </body>
        </html>
        <?php
    } else {
        echo "Email could not be sent.";
    }
} else {
    // Handle empty shopping cart
    echo "Your shopping cart is empty. Please add items before checking out.";
}

// Function to send email using PHPMailer
function smtp_mailer($to, $subject, $msg) {
    require_once("../smtp/class.phpmailer.php");
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPDebug = 0;
    $mail->SMTPAuth = TRUE;
    $mail->SMTPSecure = 'ssl';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465;
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Username = "smartharvest.ind@gmail.com";
    $mail->Password = "aeghbruvjptysmsn";
    $mail->SetFrom("smartharvest.ind@gmail.com");
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    if (!$mail->Send()) {
        return 0;
    } else {
        return 1;
    }
}
?>
