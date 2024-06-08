<?php
include('csession.php');
include('../sql.php');

ini_set('memory_limit', '-1');

if (!isset($_SESSION['customer_login_user'])) {
    header("location: ../index.php"); // Redirecting To Home Page
    exit(); // Stop script execution
}

$query4 = "SELECT * from custlogin where email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$para1 = $row4['cust_id'];
$para2 = $row4['cust_name'];
?>

<!DOCTYPE html>
<html>
<?php include('cheader.php');  ?>

<body class="bg-white" id="top">

    <?php include('cnav.php');  ?>

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
                    <span class="badge badge-danger badge-pill mb-3">Shopping</span>
                </div>
            </div>

            <div class="row row-content">
                <div class="col-md-12 mb-3">
                    <div class="card text-white bg-gradient-danger mb-3">
                        <div class="card-header">
                            <span class="text-danger display-4">Buy Crops</span>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered table-responsive-md btn-table">
                                <thead class="text-white text-center">
                                    <tr>
                                        <th>Crop Name</th>
                                        <th>Quantity (in KG)</th>
                                        <th>Price (in Rs)</th>
                                        <th>Add Item</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <form method="POST" action="cbuy_redirect.php">
                                            <td>
                                                <div class="form-group">
                                                    <?php
                                                    // query database table for crops with quantity greater than zero
                                                    $sql = "SELECT crop FROM production_approx where quantity > 0";
                                                    $result = $conn->query($sql);

                                                    // populate dropdown menu options with the crop names
                                                    echo "<select id='crops' name='crops' class='form-control text-dark'>";
                                                    echo "<option value=''>Select Crop</option>";
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<option value='" . $row["crop"] . "'>" . $row["crop"] . "</option>";
                                                    }
                                                    echo "</select>";
                                                    ?>
                                                </div>
                                            </td>

                                            <input hidden name="tradeid" id="tradeid" value="">
                                            <input type="hidden" name="cust_id" value="<?php echo $para1; ?>">

                                            <td>
                                                <div class="form-group">
                                                    <input id="quantity" type="number" placeholder="Available Quantity" max='50' name="quantity" required class="form-control text-dark">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group">
                                                    <input id="price" type="text" value="0" name="price" readonly class="form-control text-dark">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group">
                                                    <button class="btn btn-success form-control" name="add_to_cart" type="submit" disabled>Add To Cart</button>
                                                </div>
                                            </td>
                                        </form>
                                    </tr>
                                </tbody>
                            </table>

                            <h3 class="text-white">Order Details</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-responsive-md btn-table display" id="myTable">
                                    <tr>
                                        <th width="40%">Item Name</th>
                                        <th width="10%">Quantity (in KG)</th>
                                        <th width="20%">Price (in Rs.)</th>
                                        <th width="5%">Action</th>
                                    </tr>
                                    <?php
                                    $query = "SELECT * FROM `cart` WHERE cust_id=$para1";
                                    $result = mysqli_query($conn, $query);
                                    if (mysqli_num_rows($result) > 0) {
                                        $total = 0;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                            <tr class="bg-white">
                                                <td><?php echo ucfirst($row["cropname"]); ?></td>
                                                <td><?php echo $row["quantity"]; ?></td>
                                                <td>Rs. <?php echo $row["price"]; ?> </td>
                                                <td><a href="#" onclick="deleteCartItem(<?php echo $row['id']; ?>)" class="btn btn-warning btn-block">Remove</a></td>
                                            </tr>
                                    <?php
                                            $total += $row["price"];
                                        }
                                    ?>
                                        <tr class="text-dark">
                                            <td colspan="2" align="right">Total</td>
                                            <td align="right">Rs. <?php echo number_format($total, 2); ?></td>
                                            <td>
                                                <form method="POST" action="ccheck_out.php">
                                                    <button class="btn btn-info form-control" name="pay" type="submit" id="checkout-button">Place Order</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require("footer.php"); ?>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>

    <script>
        document.getElementById("crops").addEventListener("change", function() {
            var crops = jQuery('#crops').val();
            jQuery.ajax({
                url: 'ccheck_quantity.php',
                type: 'post',
                data: 'crops=' + crops,
                success: function(result) {
                    try {
                        var result = JSON.parse(result);
                        var cquantity = parseInt(result.quantityR);
                        var TradeId = parseInt(result.TradeIdR);
                        console.log(result);

                        if (cquantity > 0) {
                            document.getElementById("quantity").placeholder = cquantity;
                            document.getElementById("tradeid").value = TradeId;
                        } else {
                            document.getElementById("quantity").placeholder = "Select Crop";
                        }
                    } catch (error) {
                        console.log('Error:', error);
                    }
                }
            });
        });
    </script>

    <script>
        document.getElementById("quantity").addEventListener("change", function() {
            const addToCartBtn = document.querySelector('[name="add_to_cart"]');
            var quantity = jQuery('#quantity').val();
            var crops = jQuery('#crops').val();

            jQuery.ajax({
                url: 'ccheck_price.php',
                type: 'post',
                data: {
                    crops: crops,
                    quantity: quantity
                },
                success: function(result) {
                    var cprice = parseInt(result);
                    if (cprice > 0) {
                        document.getElementById("price").value = cprice;
                        addToCartBtn.removeAttribute('disabled');
                    } else {
                        document.getElementById("price").value = "0";
                    }
                }
            });
        });
    </script>

<script>
    const quantityInput = document.getElementById("quantity");

    quantityInput.addEventListener("change", () => {
        const max = parseFloat(document.getElementById("quantity").placeholder);
        const value = parseFloat(quantityInput.value);

        if (value > max) {
            alert(`Maximum quantity exceeded. Please enter a quantity less than or equal to ${max}.`);
            quantityInput.value = max;
        }
    });
</script>


    <script>
        function deleteCartItem(id) {
            if (confirm("Are you sure you want to remove this item?")) {
                window.location.href = "cbuy_crops.php?action=delete&id=" + id;
            }
        }
    </script>

</body>
</html>

<?php
if (isset($_GET["action"])) {
    if ($_GET["action"] == "delete") {
        $delete_id = $_GET["id"];

        // Find the item in the cart
        $query = "SELECT * FROM `cart` WHERE `id` = ? AND `cust_id` = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $delete_id, $para1);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $item_name = $row["cropname"];
            $item_quantity = $row["quantity"];

            // Increment the stock quantity in production_approx
            $update_query = "UPDATE production_approx SET quantity = quantity + ? WHERE crop = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("is", $item_quantity, $item_name);
            $update_stmt->execute();
            $update_stmt->close();

            // Delete the item from the cart table
            $delete_query = "DELETE FROM `cart` WHERE `id` = ? AND `cust_id` = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("ii", $delete_id, $para1);
            $delete_stmt->execute();
            $delete_stmt->close();

            // Remove the item from the shopping cart session
            foreach ($_SESSION["shopping_cart"] as $key => $values) {
                if ($values["item_id"] == $delete_id) {
                    unset($_SESSION["shopping_cart"][$key]);
                }
            }

            echo '<script>alert("Item Removed")</script>';
            echo '<script>window.location="cbuy_crops.php"</script>';
        }

        $stmt->close();
    }
}
?>
