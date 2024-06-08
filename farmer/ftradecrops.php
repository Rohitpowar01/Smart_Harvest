<?php
include ('fsession.php');

ini_set('memory_limit', '-1');

if(!isset($_SESSION['farmer_login_user'])){
    header("location: ../index.php");
    exit(); // Redirecting To Home Page and stopping further execution
}

$query4 = "SELECT * from farmerlogin where email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$para1 = $row4['farmer_id'];
$para2 = $row4['farmer_name'];

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
    $msp = $costingkg +($costingkg* 0.5);
    if ($msp <= $mrp_kg) {
        // Insert the new trade into farmer_crops_trade
        $sql = "INSERT INTO farmer_crops_trade (farmer_fkid, Trade_crop, Crop_quantity, costperkg, msp) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isidd", $farmer_fkid, $trade_crop, $crop_quantity, $costingkg, $msp);

        if ($stmt->execute()) {
            // Update production_approx table with the new quantity
            $query4 = "UPDATE production_approx SET quantity = quantity + ? WHERE crop = ?";
            $stmt2 = $conn->prepare($query4);
            $stmt2->bind_param("is", $crop_quantity, $trade_crop);
            if ($stmt2->execute()) {
                // Insert data into farmer_history table with initial status "order pending"
                $status = "order pending";
                $sql_history = "INSERT INTO farmer_history (farmer_id, farmer_crop, farmer_quantity, farmer_price, date, Status) VALUES (?, ?, ?, ?, NOW(), ?)";
                $stmt_history = $conn->prepare($sql_history);
                $stmt_history->bind_param("isiss", $farmer_fkid, $trade_crop, $crop_quantity, $costingkg, $status);
                if ($stmt_history->execute()) {
                    echo "<script>alert('Crop Details Added Successfully');</script>";
                } else {
                    echo "<script>alert('Error: " . $stmt_history->error . "');</script>";
                }
            } else {
                echo "<script>alert('Error: " . $stmt2->error . "');</script>";
            }
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error: Cost per kg exceeds the market price');</script>";
    }
}
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
    </div>

<div class="container ">
    <div class="row">
        <div class="col-md-8 mx-auto text-center">
            <span class="badge badge-danger badge-pill mb-3">Trade</span>
        </div>
    </div>

    <div class="row row-content">
        <div class="col-md-12 mb-3">
            <div class="card text-white bg-gradient-success mb-3">
                <div class="card-header">
                    <span class="text-success display-4"> Update Crop Stock </span>
                </div>
                <div class="card-body text-dark">
                    <form role="form" onsubmit="return validateForm()" id="sellcrops" action="" method="POST">
                        <div class="alert alert-info alert-dismissible fade show text-center" style="display: none;" id="popup" role="alert">
                            <strong class="text-center text-dark">Current Market Avg Price for <span id="crop"></span> is: <span id="price"></span></strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="alert alert-info alert-dismissible fade show text-center" style="display: none;" id="details" role="alert">
                            <strong class="text-center text-dark">Crop Details Added Successfully</strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <table class="table table-striped table-hover table-bordered bg-gradient-white text-center display" id="myTable">
                            <thead>
                                <tr class="font-weight-bold text-default">
                                    <th><center>Crop Name</center></th>
                                    <th><center>Quantity (in KG)</center></th>
                                    <th><center>Cost borne by farmer per KG (in Rs)</center></th>
                                    <th><center>Upload CROP Details</center></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <td>
                                        <div class="form-group">
                                            <select id="crops" name="crops" class="form-control" required>
                                                <option value="">Select Crop</option>
                                                <option value="arhar">Arhar</option>
                                                <option value="bajra">Bajra</option>
                                                <option value="barley">Barley</option>
                                                <option value="cotton">Cotton</option>
                                                <option value="gram">Gram</option>
                                                <option value="jowar">Jowar</option>
                                                <option value="jute">Jute</option>
                                                <option value="lentil">Lentil</option>
                                                <option value="maize">Maize</option>
                                                <option value="moong">Moong</option>
                                                <option value="ragi">Ragi</option>
                                                <option value="rice">Rice</option>
                                                <option value="soyabean">Soyabean</option>
                                                <option value="urad">Urad</option>
                                                <option value="wheat">Wheat</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" name="trade_farmer_cropquantity" min="100" required class="form-control required">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" name="trade_farmer_cost" id="trade_farmer_cost" required class="form-control required">
                                        </div>
                                    </td>
                                    <td>
                                        <center>
                                            <button type="submit" name="Crop_submit" value="Crop_submit" class="btn btn-success">Submit</button>
                                        </center>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<?php require("footer.php");?>

<script>
document.getElementById("crops").addEventListener("change", function() {
    var crops = jQuery('#crops').val();
    document.getElementById("crop").innerHTML = crops;

    jQuery.ajax({
        url: 'fcheck_price.php',
        type: 'post',
        data: 'crops=' + crops,
        success: function(response){
            $('#price').text(response); // set the response to the HTML element
            $("#popup").css({'display':'block'});
        }
    });
});

function validateForm() {
    var quantity = document.forms["sellcrops"]["trade_farmer_cropquantity"].value;
    if (quantity < 100) {
        alert("Quantity must be at least 100.");
        return false;
    }
    return true;
}
</script>
</body>
</html>
