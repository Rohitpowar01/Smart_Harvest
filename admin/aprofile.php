<?php
session_start(); // Starting Session
require('../sql.php'); // Includes Login Script

// Storing Session
$user = $_SESSION['admin_login_user'];

if (!isset($_SESSION['admin_login_user'])) {
    header("location: ../index.php");
    exit(); // Redirecting To Home Page
}

$query4 = "SELECT * from admin where admin_name ='$user'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$para1 = $row4['admin_id'];
$para2 = $row4['admin_name'];
$para3 = $row4['admin_password'];

// Update order status if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $update_query = "UPDATE orders SET status='$status' WHERE order_id='$order_id'";
    mysqli_query($conn, $update_query);
}

// Update trade status and reason if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['history_id'], $_POST['status'], $_POST['reason'])) {
    $history_id = $_POST['history_id'];
    $status = $_POST['status'];
    $reason = $_POST['reason'];
    $update_trade_query = "UPDATE farmer_history SET Status='$status', Reason='$reason' WHERE History_id='$history_id'";
    mysqli_query($conn, $update_trade_query);
}

// Fetch orders from the database
$order_query = "SELECT * FROM orders";
$order_result = mysqli_query($conn, $order_query);

// Fetch farmer trade history from the database
$trade_history_query = "SELECT * FROM farmer_history";
$trade_history_result = mysqli_query($conn, $trade_history_query);
?>

<!DOCTYPE html>
<html>
<?php require('aheader.php'); ?>

<body class="bg-white" id="top">

<?php require('anav.php'); ?>

<section class="section section-shaped section-lg">
    <div class="container ">
        <!-- Top Customer Orders Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h3 class="card-title">Order Management</h3>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer ID</th>
                                    <th>Crop Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = mysqli_fetch_assoc($order_result)) { ?>
                                    <tr>
                                        <td><?php echo $order['order_id']; ?></td>
                                        <td><?php echo $order['cust_id']; ?></td>
                                        <td><?php echo $order['crop_name']; ?></td>
                                        <td><?php echo $order['quantity']; ?></td>
                                        <td><?php echo $order['price']; ?></td>
                                        <td><?php echo $order['order_date']; ?></td>
                                        <td><?php echo ucfirst($order['status']); ?></td>
                                        <td>
                                            <form method="POST">
                                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                <select name="status" class="form-control">
                                                    <option value="order pending" <?php if ($order['status'] == 'order pending') echo 'selected'; ?>>Order Pending</option>
                                                    <option value="shipping" <?php if ($order['status'] == 'shipping') echo 'selected'; ?>>Shipping</option>
                                                    <option value="delivered" <?php if ($order['status'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary mt-2">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of Top Customer Orders Form -->

        <!-- Farmer Trade History Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h3 class="card-title">Farmer Trade History</h3>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Trade ID</th>
                                    <th>Farmer ID</th>
                                    <th>Crop</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($trade = mysqli_fetch_assoc($trade_history_result)) { ?>
                                    <tr>
                                        <td><?php echo $trade['History_id']; ?></td>
                                        <td><?php echo $trade['farmer_id']; ?></td>
                                        <td><?php echo $trade['farmer_crop']; ?></td>
                                        <td><?php echo $trade['farmer_quantity']; ?></td>
                                        <td><?php echo $trade['farmer_price']; ?></td>
                                        <td><?php echo $trade['date']; ?></td>
                                        <td><?php echo $trade['Status']; ?></td>
                                        <td><?php echo $trade['Reason']; ?></td>
                                        <td>
                                            <form method="POST">
                                                <input type="hidden" name="history_id" value="<?php echo $trade['History_id']; ?>">
                                                <select name="status" class="form-control">
                                                    <option value="order pending" <?php if ($trade['Status'] == 'order pending') echo 'selected'; ?>>Order Pending</option>
                                                    <option value="shipping" <?php if ($trade['Status'] == 'shipping') echo 'selected'; ?>>Shipping</option>
                                                    <option value="delivered" <?php if ($trade['Status'] == 'paid') echo 'selected'; ?>>Paid</option>
                                                    <option value="payment failed" <?php if ($trade['Status'] == 'payment failed') echo 'selected'; ?>>Payment Failed</option>
                                                </select>
                                                <input type="text" name="reason" class="form-control mt-2" placeholder="Reason">
                                                <button type="submit" class="btn btn-primary mt-2">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of Farmer Trade History Form -->
    </div>
</section>

<?php require("footer.php"); ?>

</body>
</html>
