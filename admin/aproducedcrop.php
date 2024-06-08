<?php
session_start(); // Starting Session
require('../sql.php'); // Includes Login Script

// Storing Session
$user = $_SESSION['admin_login_user'];

if (!isset($_SESSION['admin_login_user'])) {
    header("location: ../index.php");
} // Redirecting To Home Page
$query4 = "SELECT * from admin where admin_name ='$user'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$para1 = $row4['admin_id'];
$para2 = $row4['admin_name'];
$para3 = $row4['admin_password'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $crop = $_POST['crop'];
    $quantity = $_POST['quantity'];
    $mrp_kg = $_POST['mrp_kg'];

    $update_query = "UPDATE production_approx SET quantity='$quantity', mrp_kg='$mrp_kg' WHERE crop='$crop'";
    mysqli_query($conn, $update_query);
}
?>

<!DOCTYPE html>
<html>
<?php require('aheader.php'); ?>

<body class="bg-white" id="top">

<?php require('anav.php'); ?>

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
    </div>
    <!-- ======================================================================================================================================== -->

    <div class="container">

        <div class="row">
            <div class="col-md-8 mx-auto text-center">
                <span class="badge badge-danger badge-pill mb-3">Crops</span>
            </div>
        </div>

        <div class="row row-content">
            <div class="col-md-12 mb-3">

                <div class="card text-white bg-gradient-warning mb-3">
                    <div class="card-header">
                        <span class="text-warning display-4">Produced Crops</span>
                    </div>

                    <div class="card-body text-white">
                        <table class="table table-striped table-hover table-bordered bg-gradient-white text-center display" id="myTable">
                            <thead>
                                <tr class="font-weight-bold text-default">
                                    <th><center>Crop Name</center></th>
                                    <th><center>Quantity (in KG)</center></th>
                                    <th><center>MRP/KG</center></th>
                                    <th><center>Action</center></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT crop, quantity, mrp_kg FROM production_approx WHERE quantity > 0";
                                $query = mysqli_query($conn, $sql);

                                while ($res = mysqli_fetch_array($query)) {
                                ?>
                                    <tr class="text-center">
                                        <form method="POST" action="">
                                            <td> <?php echo $res['crop']; ?> <input type="hidden" name="crop" value="<?php echo $res['crop']; ?>"></td>
                                            <td><input type="number" name="quantity" value="<?php echo $res['quantity']; ?>"></td>
                                            <td><input type="number" step="0.01" name="mrp_kg" value="<?php echo $res['mrp_kg']; ?>"></td>
                                            <td><button type="submit" class="btn btn-primary">Update</button></td>
                                        </form>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</section>

<?php require("footer.php"); ?>

<script>
    $(document).ready(function () {
        $('#myTable').DataTable();
    });
</script>
</body>
</html>
