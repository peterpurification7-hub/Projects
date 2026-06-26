<?php
@$dbConnect = new mysqli('localhost', 'root', '', 'techforge');
$uid = $_POST['userid'];

// This will also delete their orders automatically due to our ON DELETE CASCADE rule!
$sql = "DELETE FROM tbluser WHERE userid = '$uid'";

if ($dbConnect->query($sql)) {
    header("Location: adminreview.php");
} else {
    echo "Error: " . $dbConnect->error;
}
$dbConnect->close();
?>