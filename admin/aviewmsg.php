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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_mail'])) {
    $email = $_POST['farmer_email'];
    $message = $_POST['farmer_message'];
    $res = mysqli_query($conn, "SELECT * FROM farmerlogin WHERE email='$email'");
    $count = mysqli_num_rows($res);
    if ($count > 0) {
        smtp_mailer($email, 'Message from Smart Harvest', $message);
        echo "Mail sent successfully";
    } else {
        echo "Email does not exist";
    }
}

function smtp_mailer($to, $subject, $msg) {
    require_once("../smtp/class.phpmailer.php");
    try {
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
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$e}";
    }
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

        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto text-center">
                    <span class="badge badge-danger badge-pill mb-3">Contact</span>
                </div>
            </div>

            <div class="row row-content">
                <div class="col-md-12 mb-3">
                    <div class="card text-white bg-gradient-info mb-3">
                        <div class="card-header">
                            <span class="text-primary display-4">Contact Queries</span>
                        </div>

                        <div class="card-body text-white">
                            <form method="post" action="">
                                <div class="form-group">
                                    <label for="farmer_email">Farmer Email:</label>
                                    <input type="email" class="form-control" id="farmer_email" name="farmer_email" required>
                                </div>
                                <div class="form-group">
                                    <label for="farmer_message">Message:</label>
                                    <textarea class="form-control" id="farmer_message" name="farmer_message" rows="4" required></textarea>
                                </div>
                                <button type="submit" name="send_mail" class="btn btn-primary">Send Message</button>
                            </form>
                            <table class="table table-striped table-hover table-bordered bg-gradient-white text-center display" id="myTable">
                                <thead>
                                    <tr class="font-weight-bold text-default text-center">
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Mobile No.</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Address</th>
                                        <th class="text-center">Message</th>
                                        <th class="text-center">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $q = "SELECT * FROM contactus";
                                    $query = mysqli_query($conn, $q);

                                    while ($res = mysqli_fetch_array($query)) {
                                    ?>
                                        <tr class="text-center">
                                            <td><?php echo $res['c_id']; ?></td>
                                            <td><?php echo $res['c_name']; ?></td>
                                            <td><?php echo $res['c_mobile']; ?></td>
                                            <td><?php echo $res['c_email']; ?></td>
                                            <td><?php echo $res['c_address']; ?></td>
                                            <td><?php echo $res['c_message']; ?></td>
                                            <td><button class="btn btn-sm btn-danger"><a href="amsgdelete.php?id=<?php echo $res['c_id']; ?>" class="nav-link text-white">Delete</a></button></td>
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
