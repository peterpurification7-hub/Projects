<?php
@$dbConnect = new mysqli('localhost', 'root', '', 'techforge');
$oid = $_POST['orderid'];

// Deleting the order will automatically clean up the tblorder_items too!
$sql = "DELETE FROM tblorder WHERE orderid = '$oid'";

if ($dbConnect->query($sql)) {
    header("Location: adminreview.php");
} else {
    echo "Error: " . $dbConnect->error;
}
$dbConnect->close();
?>