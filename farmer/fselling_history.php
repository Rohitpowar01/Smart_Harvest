<?php
include ('fsession.php');
ini_set('memory_limit', '-1');

if(!isset($_SESSION['farmer_login_user'])){
    header("location: ../index.php"); // Redirecting To Home Page
    exit(); // Ensuring script termination after redirection
}

$query4 = "SELECT * FROM farmerlogin WHERE email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$para1 = $row4['farmer_id'];
$para2 = $row4['farmer_name'];

$sql = "SELECT farmer_crop, farmer_quantity, farmer_price, `date`, status, reason FROM farmer_history WHERE farmer_id='".$para1."'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<?php include ('fheader.php'); ?>

<body class="bg-white" id="top">
    <?php include ('fnav.php'); ?>

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
                    <span class="badge badge-danger badge-pill mb-3">Trade</span>
                </div>
            </div>

            <div class="row row-content">
                <div class="col-md-12 mb-3">
                    <div class="card text-white bg-gradient-warning mb-3">
                        <div class="card-header">
                            <span class=" text-warning display-4"> Selling History </span>
                        </div>

                        <div class="card-body text-dark">
                            <table class="table table-striped table-hover table-bordered bg-gradient-white text-center display" id="myTable">
                                <thead>
                                    <tr>
                                        <th>Crop</th>
                                        <th>Quantity (in KG)</th>
                                        <th>Total Amount received (in Rs)</th>
                                        <th>Date of Transaction</th>
                                        <th>Status</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    while($row = mysqli_fetch_assoc($result)) {
                                        $cropname = ucfirst($row["farmer_crop"]);
                                        $cropquantity = $row["farmer_quantity"];
                                        $cropprice = $row["farmer_price"];
                                        $currentdate = $row['date'];
                                        $status = $row['status'];
                                        $reason = $row['reason'];

                                        echo "<tr>";
                                        echo "<td>$cropname</td>";
                                        echo "<td>$cropquantity</td>";
                                        echo "<td>$cropprice</td>";
                                        echo "<td>$currentdate</td>";
                                        echo "<td>$status</td>";
                                        echo "<td>$reason</td>";
                                        echo "</tr>";
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
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>
</body>
</html>
