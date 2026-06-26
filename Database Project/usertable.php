<?php
$dbConnect = new mysqli('localhost', 'root', '', 'techforge');
$email = $_POST['email'];
$password = $_POST['password'];
$type = $_POST['type'];

$query = "SELECT * FROM tbluser WHERE email = '$email' AND password = '$password' AND type = '$type'";
$result = $dbConnect->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $uid = $user['userid'];
    
    if ($type == 'admin') {
        header("Location: adminreview.php");
    } else {
        // Traffic Control: Dashboard vs Configurator
        $order = $dbConnect->query("SELECT orderid FROM tblorder WHERE userid = '$uid'");
        if ($order->num_rows > 0) {
            header("Location: user_main.php?userid=" . $uid);
        } else {
            header("Location: tech_configurator.php?userid=$uid");
        }
    }
} else {
    echo "Login Failed. <a href='tech_register.html'>Back</a>";
}
?>