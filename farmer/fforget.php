<?php
session_start();
require('../sql.php'); // Includes Database Connection

if (isset($_POST['send_otp'])) {
    $email = $_POST['farmer_email'];
    $res = mysqli_query($conn, "SELECT * FROM farmerlogin WHERE email='$email'");
    $count = mysqli_num_rows($res);
    if ($count > 0) {
        $otp = rand(11111, 99999);
        mysqli_query($conn, "UPDATE farmerlogin SET otp='$otp' WHERE email='$email'");
        $html = "Your OTP verification code for Smart Harvest is " . $otp;
        smtp_mailer($email, 'OTP Verification', $html);
        $_SESSION['farmer_email'] = $email;
        $_SESSION['otp_sent'] = true;
    } else {
        $error = "Email does not exist!";
    }
}

if (isset($_POST['verify_otp'])) {
    $otp = $_POST['otp'];
    $email = $_SESSION['farmer_email'];
    $res = mysqli_query($conn, "SELECT * FROM farmerlogin WHERE email='$email' AND otp='$otp'");
    $count = mysqli_num_rows($res);
    if ($count > 0) {
        $_SESSION['otp_verified'] = true;
    } else {
        $error = "Invalid OTP!";
    }
}

if (isset($_POST['reset_password'])) {
    $new_password = $_POST['new_password'];
    $email = $_SESSION['farmer_email'];
    mysqli_query($conn, "UPDATE farmerlogin SET password='$new_password', otp=NULL WHERE email='$email'");
    $success = "Password reset successfully!";
    unset($_SESSION['farmer_email']);
    unset($_SESSION['otp_sent']);
    unset($_SESSION['otp_verified']);
}

function smtp_mailer($to, $subject, $msg){
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
        alert("Message could not be sent. Mailer Error: {$e}");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="../assets/img/logo.png" />
  <title>Smart Harvest - Forgot Password</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
  integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
  integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
  <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/bootstrap-social/5.1.1/bootstrap-social.min.css "/>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
  <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/creativetim.min.css" type="text/css">
</head>

<body class="bg-white" id="top">
  <!-- Navbar -->
  <nav id="navbar-main" class="navbar navbar-main navbar-expand-lg bg-default navbar-light position-sticky top-0 shadow py-0">
    <div class="container">
      <ul class="navbar-nav navbar-nav-hover align-items-lg-center">
        <li class="nav-item dropdown">
          <a href="../index.php" class="navbar-brand mr-lg-5 text-white">
            <img src="../assets/img/nav.png" />
          </a>
        </li>
      </ul>
      <button class="navbar-toggler bg-white" type="button" data-toggle="collapse" data-target="#navbar_global" aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon text-white"></span>
      </button>
      <div class="navbar-collapse collapse bg-default" id="navbar_global">
        <div class="navbar-collapse-header">
          <div class="row">
            <div class="col-10 collapse-brand">
              <a href="../index.html">
                <img src="../assets/img/nav.png" />
              </a>
            </div>
            <div class="col-2 collapse-close bg-danger">
              <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar_global" aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation">
                <span></span>
                <span></span>
              </button>
            </div>
          </div>
        </div>
        <ul class="navbar-nav align-items-lg-center ml-auto">
          <li class="nav-item">
            <a href="../contact.php" class="nav-link">
              <span class="text-white nav-link-inner--text"><i class="text-white fas fa-address-card"></i> Contact</span>
            </a>
          </li>
          <li class="nav-item">
            <div class="dropdown show">
              <a class="nav-link dropdown-toggle text-white" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="text-white nav-link-inner--text"><i class="text-white fas fa-user-plus"></i> Sign Up</span>
              </a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                <a class="dropdown-item" href="../farmer/fregister.php">Farmer</a>
                <a class="dropdown-item" href="../customer/cregister.php">Customer</a>
              </div>
            </div>
          </li>
          <li class="nav-item">
            <div class="dropdown show">
              <a class="nav-link dropdown-toggle text-success" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="text-success nav-link-inner--text"><i class="text-success fas fa-sign-in-alt"></i> Login</span>
              </a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                <a class="dropdown-item" href="flogin.php">Farmer</a>
                <a class="dropdown-item" href="../customer/clogin.php">Customer</a>
                <a class="dropdown-item" href="../admin/alogin.php">Admin</a>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- End Navbar -->

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
          <span class="badge badge-info badge-pill mb-3">Forgot Password</span>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12 mb-3">
          <div class="card card-body bg-gradient-warning">
            <?php if (!isset($_SESSION['otp_sent']) && !isset($_SESSION['otp_verified'])) { ?>
              <form method="POST" action="">
                <div class="form-group row">
                  <label for

="email" class="col-md-3 col-form-label">
                    <h6 class="text-white font-weight-bold">Email Id</h6>
                  </label>
                  <div class="col-md-9">
                    <input type="text" class="form-control" required name="farmer_email" placeholder="Enter Email ID" />
                  </div>
                </div>
                <div class="form-group row">
                  <div class="offset-md-3 col-md-2">
                    <button type="submit" class="btn btn-info text-dark" name="send_otp" id="send_otp">
                      Send OTP
                    </button>
                  </div>
                </div>
                <span><?php echo isset($error) ? $error : ''; ?></span>
              </form>
            <?php } elseif (isset($_SESSION['otp_sent']) && !isset($_SESSION['otp_verified'])) { ?>
              <form method="POST" action="">
                <div class="form-group row">
                  <label for="otp" class="col-md-3 col-form-label">
                    <h6 class="text-white font-weight-bold">Enter OTP</h6>
                  </label>
                  <div class="col-md-9">
                    <input type="text" class="form-control" required name="otp" placeholder="Enter OTP" />
                  </div>
                </div>
                <div class="form-group row">
                  <div class="offset-md-3 col-md-2">
                    <button type="submit" class="btn btn-info text-dark" name="verify_otp" id="verify_otp">
                      Verify OTP
                    </button>
                  </div>
                </div>
                <span><?php echo isset($error) ? $error : ''; ?></span>
              </form>
            <?php } elseif (isset($_SESSION['otp_verified'])) { ?>
              <form method="POST" action="">
                <div class="form-group row">
                  <label for="new_password" class="col-md-3 col-form-label">
                    <h6 class="text-white font-weight-bold">New Password</h6>
                  </label>
                  <div class="col-md-9">
                    <input type="password" class="form-control" required name="new_password" placeholder="Enter New Password" />
                  </div>
                </div>
                <div class="form-group row">
                  <div class="offset-md-3 col-md-2">
                    <button type="submit" class="btn btn-info text-dark" name="reset_password" id="reset_password">
                      Reset Password
                    </button>
                  </div>
                </div>
                <span><?php echo isset($success) ? $success : ''; ?></span>
              </form>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php require("footer.php");?>
</body>

</html>