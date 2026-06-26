<?php
$dbConnect = new mysqli('localhost', 'root', '', 'techforge');

$fname = addslashes($_POST['firstname']);
$lname = addslashes($_POST['lastname']);
$email = addslashes($_POST['email']);
$phone = addslashes($_POST['phone']);
$pass  = addslashes($_POST['password']);

// Logic: Check for existing email OR full name match
$check = $dbConnect->query("SELECT email FROM tbluser WHERE email='$email' OR (firstname='$fname' AND lastname='$lname')");

if ($check->num_rows > 0) {
    echo "<h2>Registration Error</h2><p>An account with this email or name already exists.</p>";
    echo "<a href='tech_register.html'>Try Again</a>";
} else {
    $sql = "INSERT INTO tbluser (firstname, lastname, email, phone, password, type) 
            VALUES ('$fname', '$lname', '$email', '$phone', '$pass', 'user')";
    if ($dbConnect->query($sql)) {
        header("Location: tech_configurator.php?userid=" . $dbConnect->insert_id);
    }
}
?>