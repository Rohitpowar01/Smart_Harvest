<?php
session_start();
date_default_timezone_set("Asia/Calcutta"); 
$userlogin=$_SESSION['customer_login_user'];
$servername="localhost";
$username="root";
$password="";
$dbname="smart_harvest";

//Create Connection 
$conn =mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Update Details</title>
</head>
<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $Name = $_POST['name'];
        $Number = $_POST['number'];
        $Address = $_POST['address'];
        $ID = $_POST['id'];
        $sql = "UPDATE custlogin SET cust_name='$Name', contact_no='$Number', address='$Address' WHERE cust_id='$ID'";

        if (mysqli_query($conn, $sql)) {
            echo "Record updated successfully";
            $_SESSION['customer_login_user'] = $Name;
            header("Location: ../customer/cprofile.php");
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
        mysqli_close($conn);
    } else {
        $user_check = $_SESSION['customer_login_user'];
        $sql = "SELECT * FROM custlogin WHERE cust_name='$user_check'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
        } else {
            echo "0 results";
        }
    }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <input type="hidden" name="id" value="<?php echo $row['cust_id']; ?>">
        Name: <input type="text" name="name" value="<?php echo $row['cust_name']; ?>">
        <br><br>
        Contact Number: <input type="text" name="number" value="<?php echo $row['contact_no']; ?>">
        <br><br>
        Address: <input type="text" name="address" value="<?php echo $row['address']; ?>">
        <br><br>
        <input type="submit" name="submit" value="Update">
    </form>
</body>
</html>
